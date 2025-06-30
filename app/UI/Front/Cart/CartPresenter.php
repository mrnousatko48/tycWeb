<?php

declare(strict_types=1);

namespace App\UI\Front\Cart;

use Nette;
use App\Model\OrderFacade;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;

final class CartPresenter extends Nette\Application\UI\Presenter
{
    private OrderFacade $orderFacade;
    private Explorer $database;

    public function __construct(OrderFacade $orderFacade, Explorer $database)
    {
        parent::__construct();
        $this->orderFacade = $orderFacade;
        $this->database = $database;
    }

    public function renderDefault(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $userId = (int) $this->getUser()->getId();
            $cases = $this->orderFacade->getCartCasesByUserId($userId);
            $this->template->cases = $cases;
        } else {
            $session = $this->getSession('order');
            $quantities = $session->quantities ?? [];

            if (empty($quantities)) {
                $this->template->cases = [];
                return;
            }

            $caseIds = array_keys($quantities);
            $cases = $this->orderFacade->getCasesByIds($caseIds);
            $this->template->cases = $cases;
            $this->template->quantities = $quantities;
        }
    }



    protected function createComponentSendOrderForm(): Form
    {
        $form = new Form;

        $userId = (int) $this->getUser()->getId();
        $user = $this->database->table('users')->get($userId);

        $form->addText('firstname', 'Jméno:');
        $form->addText('lastname', 'Příjmení:');

        if (!$this->getUser()->isLoggedIn()) {
            $form['firstname']->setRequired('Zadejte své jméno');
            $form['lastname']->setRequired('Zadejte své příjmení');
        }

        $form->addText('address', 'Adresa:')
            ->setRequired('Zadejte svou adresu');

        $form->addText('city', 'Město:')
            ->setRequired('Zadejte město');

        $form->addText('psc', 'PSČ:')
            ->setRequired('Zadejte PSČ');

        $form->addSubmit('submit', 'Dokončit objednávku');

        if ($user) {
            $form->setDefaults([
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
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
                $values->address,
                $values->city,
                $values->psc,
                $quantities
            );
        } else {
            $order = $this->orderFacade->createGuestOrder(
                $values->firstname,
                $values->lastname,
                $values->address,
                $values->city,
                $values->psc,
                $quantities
            );
        }

        unset($session->quantities);

        $this->flashMessage('Objednávka byla úspěšně dokončena.', 'success');
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
}
