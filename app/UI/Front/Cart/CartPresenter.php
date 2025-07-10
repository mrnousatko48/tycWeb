<?php

declare(strict_types=1);

namespace App\UI\Front\Cart;

use App\UI\Front\BaseFrontPresenter;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use App\MailSender\MailSender;

final class CartPresenter extends BaseFrontPresenter
{
    private Explorer $database;
    private MailSender $mailSender;

    public function __construct(Explorer $database, MailSender $mailSender)
    {
        parent::__construct();
        $this->database = $database;
        $this->mailSender = $mailSender;
    }

    private function cleanFeatureKey(string $key): string
    {
        // Replace underscores with spaces
        $key = str_replace('_', ' ', $key);

        // Lowercase everything
        $key = mb_strtolower($key);

        // Capitalize first letter
        $key = ucfirst($key);

        return $key;
    }

    public function renderDefault(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $userId = (int)$this->getUser()->getId();
            $cases = $this->orderFacade->getCartCasesByUserId($userId);
        } else {
            $session = $this->getSession('order');
            $quantities = $session->quantities ?? [];

            if (empty($quantities)) {
                $this->template->cases = [];
                return;
            }

            $caseIds = array_keys($quantities);
            $cases = $this->orderFacade->getCasesByIds($caseIds);
            $this->template->quantities = $quantities;
        }

        $decodedCases = [];
        $totalCartValue = 0;
        foreach ($cases as $case) {
            $caseArray = $case->toArray();
            $features = $case->features ? json_decode($case->features, true) : [];

            // Check if there's an outer 'features' key and decode the inner JSON
            $cleanFeatures = [];
            if (!empty($features) && isset($features['features'])) {
                $innerFeatures = json_decode($features['features'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    foreach ($innerFeatures as $key => $value) {
                        $cleanKey = $this->cleanFeatureKey($key);
                        $cleanFeatures[$cleanKey] = $value;
                    }
                }
            }

            $caseArray['features'] = $cleanFeatures;
            $decodedCases[] = (object) $caseArray;

            // Calculate total value for this case based on quantity
            $quantity = $this->template->quantities[$case->id] ?? 1;
            $totalCartValue += $case->total_price * $quantity;
        }

        $this->template->cases = $decodedCases;
        $this->template->totalCartValue = $totalCartValue;
    }

    protected function createComponentSendOrderForm(): Form
    {
        $form = new Form;

        $userId = (int) $this->getUser()->getId();
        $user = $this->database->table('users')->get($userId);

        $form->addText('firstname', 'Jméno:')
            ->setRequired('Zadejte své jméno');
        $form->addText('lastname', 'Příjmení:')
            ->setRequired('Zadejte své příjmení');
        $form->addText('email', 'E-mail:')
            ->setRequired('Zadejte e-mail')
            ->addRule(Form::EMAIL, 'Zadejte platný e-mail');
        $form->addText('address', 'Adresa:')
            ->setRequired('Zadejte svou adresu');
        $form->addText('city', 'Město:')
            ->setRequired('Zadejte město');
        $form->addText('psc', 'PSČ:')
            ->setRequired('Zadejte PSČ');
        $form->addSelect('payment', 'Způsob platby:', [
                'PREVOD' => 'Převodem',
                'DOBIRKA' => 'Dobírka',
            ])
            ->setRequired('Zvolte způsob platby');
            
        $form->addSubmit('submit', 'Dokončit objednávku');

        if ($user) {
            $form->setDefaults([
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
                'email' => $user->email ?? '',
                'address' => $user->address ?? '',
                'city' => $user->city ?? '',
                'psc' => $user->psc ?? '',
            ]);
        }

        $form->onSuccess[] = [$this, 'sendOrderFormSucceeded'];
        return $form;
    }

    public function sendOrderFormSucceeded(Form $form, \stdClass $values): void
    {
        $session = $this->getSession('order');
        $quantities = $session->quantities ?? [];

        if (empty($quantities)) {
            $this->flashMessage('Nelze dokončit prázdnou objednávku.', 'danger');
            $this->redirect('Cart:default');
            return;
        }

        $userId = $this->getUser()->getId();

        if ($this->getUser()->isLoggedIn()) {
            $order = $this->orderFacade->createOrder(
                (int)$userId,
                $values->firstname,
                $values->lastname,
                $values->email,
                $values->address,
                $values->city,
                $values->psc,
                $values->payment,
                $quantities
            );
        } else {
            $order = $this->orderFacade->createGuestOrder(
                $values->firstname,
                $values->lastname,
                $values->email,
                $values->address,
                $values->city,
                $values->psc,
                $values->payment,
                $quantities
            );
        }
        $items = $this->orderFacade->getOrderItems($order->id);

        $recipientName = $values->firstname . ' ' . $values->lastname;
        $this->mailSender->sendInvoiceEmail($values->email, $recipientName, $order, $items);

        unset($session->quantities);
        $this->flashMessage('Objednávka byla úspěšně dokončena a faktura odeslána.', 'success');
        $this->redirect('Home:default');
    }

    public function renderOrder(): void
    {
        $session = $this->getSession('order');
        $quantities = $session->quantities ?? [];

        if (empty($quantities)) {
            $this->flashMessage('Košík je prázdný.', 'warning');
            $this->redirect('Cart:default');
        }

        $caseIds = array_keys($quantities);
        $cases = $this->orderFacade->getCasesByIds($caseIds);

        $this->template->cases = $cases;
        $this->template->quantities = $quantities;
    }

    public function createOrder(int $userId, string $address, string $city, array $caseIds)
    {
        $this->database->beginTransaction();

        try {
            $order = $this->database->table('orders')->insert([
                'user_id' => $userId,
                'address' => $address,
                'city' => $city,
                'state' => 'OBJEDNANO',
                'created_at' => new \DateTime(),
            ]);

            foreach ($caseIds as $caseId) {
                $this->database->table('order_case')->insert([
                    'order_id' => $order->id,
                    'case_id' => $caseId,
                ]);
            }

            $this->database->commit();
            return $order;
        } catch (\Throwable $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

    public function actionCreateOrder(): void
    {
        $quantities = $this->getHttpRequest()->getPost('quantities') ?? [];
        $selected = [];

        foreach ($quantities as $caseId => $data) {
            $amount = (int)($data['amount'] ?? 0);
            if ($amount > 0) {
                $selected[(int) $caseId] = $amount;
            }
        }

        if (empty($selected)) {
            $this->flashMessage('Košík je prázdný nebo nebylo zadáno žádné množství.', 'warning');
            $this->redirect('Cart:default');
        }

        $this->getSession('order')->quantities = $selected;
        $this->redirect('Cart:order');
    }

    public function handleRemoveCase(int $caseId): void
    {
        $userId = (int) $this->getUser()->getId();
        $this->orderFacade->removeCaseFromCartByUser($userId, $caseId);

        $session = $this->getSession();
        $this->orderFacade->removeCaseFromCart($session, $caseId);

        $this->flashMessage("Kryt byl odebrán z košíku.", 'info');
        $this->redirect('this');
    }

    public function handleAddCase($caseData): void
    {
        $case = $this->orderFacade->createCase($caseData, null); // null == guest

        if ($this->getUser()->isLoggedIn() === false) {
            $session = $this->getSession('order');
            $quantities = $session->quantities ?? [];
            $quantities[$case->id] = ($quantities[$case->id] ?? 0) + 1;
            $session->quantities = $quantities;
        }

        $this->flashMessage('Kryt byl přidán do košíku.', 'success');
        $this->redirect('this');
    }
}