<?php

declare(strict_types=1);

namespace App\UI\Front\Cart;

use App\UI\Front\BaseFrontPresenter;
use Nette\Application\UI\Form;
use App\MailSender\MailSender;

final class CartPresenter extends BaseFrontPresenter
{
    private MailSender $mailSender;

    public function __construct(MailSender $mailSender)
    {
        parent::__construct();
        $this->mailSender = $mailSender;
    }

    private function cleanFeatureKey(string $key): string
    {
        $key = str_replace('_', ' ', $key);
        $key = mb_strtolower($key);
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
            if (isset($features['features']) && is_string($features['features'])) {
                $features = json_decode($features['features'], true) ?: $features;
            }

            $cleanFeatures = [];
            foreach ($features as $key => $value) {
                $cleanKey = $this->cleanFeatureKey($key);
                $cleanFeatures[$cleanKey] = $value;
            }

            $caseArray['features'] = $cleanFeatures;
            $decodedCases[] = (object) $caseArray;

            $quantity = $this->template->quantities[$case->id] ?? 1;
            $totalCartValue += $case->total_price * $quantity;
        }

        $this->template->cases = $decodedCases;
        $this->template->totalCartValue = $totalCartValue;
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

        $decodedCases = [];
        $totalCartValue = 0;
        foreach ($cases as $case) {
            $caseArray = $case->toArray();
            $features = $case->features ? json_decode($case->features, true) : [];
            if (isset($features['features']) && is_string($features['features'])) {
                $features = json_decode($features['features'], true) ?: $features;
            }

            $cleanFeatures = [];
            foreach ($features as $key => $value) {
                $cleanKey = $this->cleanFeatureKey($key);
                $cleanFeatures[$cleanKey] = $value;
            }

            $caseArray['features'] = $cleanFeatures;
            $decodedCases[] = (object) $caseArray;

            $quantity = $quantities[$case->id] ?? 1;
            $totalCartValue += $case->total_price * $quantity;
        }

        $this->template->cases = $decodedCases;
        $this->template->quantities = $quantities;
        $this->template->totalCartValue = $totalCartValue;
    }

    public function renderInfo(): void
    {
        $session = $this->getSession('order');
        $quantities = $session->quantities ?? [];

        if (empty($quantities)) {
            $this->flashMessage('Košík je prázdný.', 'warning');
            $this->redirect('Cart:default');
        }

        if (!isset($session->shipping) || !isset($session->payment)) {
            $this->flashMessage('Prosím, vyberte způsob dopravy a platby.', 'warning');
            $this->redirect('Cart:order');
        }

        $caseIds = array_keys($quantities);
        $cases = $this->orderFacade->getCasesByIds($caseIds);

        $decodedCases = [];
        $itemsSubtotal = 0;
        foreach ($cases as $case) {
            $caseArray = $case->toArray();
            $features = $case->features ? json_decode($case->features, true) : [];
            if (isset($features['features']) && is_string($features['features'])) {
                $features = json_decode($features['features'], true) ?: $features;
            }

            $cleanFeatures = [];
            foreach ($features as $key => $value) {
                $cleanKey = $this->cleanFeatureKey($key);
                $cleanFeatures[$cleanKey] = $value;
            }

            $caseArray['features'] = $cleanFeatures;
            $decodedCases[] = (object) $caseArray;

            $quantity = $quantities[$case->id] ?? 1;
            $itemsSubtotal += $case->total_price * $quantity;
        }

        $shippingInfo = $this->orderFacade->getShippingInfo($session->shipping);
        $shippingCost = $shippingInfo ? (float)$shippingInfo['cost'] : 0.0;
        $shippingName = $shippingInfo ? $shippingInfo['name'] : 'Není vybráno';
        $paymentCost = $session->payment === 'DOBIRKA' ? 40.0 : 0.0;
        $totalCartValue = $itemsSubtotal + $shippingCost + $paymentCost;

        $this->template->cases = $decodedCases;
        $this->template->quantities = $quantities;
        $this->template->itemsSubtotal = $itemsSubtotal;
        $this->template->shippingCost = $shippingCost;
        $this->template->paymentCost = $paymentCost;
        $this->template->totalCartValue = $totalCartValue;
        $this->template->shipping = $shippingName;
        $this->template->payment = $session->payment === 'DOBIRKA' ? 'Dobírka' : 'Bankovní převod';
        $this->template->delivery_point = $session->delivery_point ?? 'Není zadáno';
    }

    protected function createComponentSendOrderForm(): Form
    {
        $form = new Form;

        // Get the current user session object
        $user = $this->getUser()->isLoggedIn() ? $this->getUser()->getIdentity() : null;

        // If user is logged in, fetch the latest user data using UserFacade
        if ($user) {
            $userData = $this->userFacade->getUserById($user->getId());
        } else {
            $userData = null;
        }

        // Set up form fields with default values from user session or database
        $form->addText('firstname', 'Jméno:')
            ->setRequired('Zadejte své jméno')
            ->setHtmlAttribute('id', 'firstname-field')
            ->setDefaultValue($userData ? $userData->firstname : '');

        $form->addText('lastname', 'Příjmení:')
            ->setRequired('Zadejte své příjmení')
            ->setHtmlAttribute('id', 'lastname-field')
            ->setDefaultValue($userData ? $userData->lastname : '');

        $form->addEmail('email', 'E-mail:')
            ->setRequired('Zadejte svůj e-mail')
            ->setHtmlAttribute('id', 'email-field')
            ->setHtmlAttribute('placeholder', 'Zadejte svůj e-mail')
            ->setDefaultValue($userData ? $userData->email : '');

        $form->addText('phone', 'Telefon:')
            ->setRequired('Zadejte své telefonní číslo')
            ->setHtmlAttribute('id', 'phone-field')
            ->setHtmlAttribute('placeholder', 'Zadejte své telefonní číslo')
            ->setDefaultValue($userData && isset($userData->phone) ? $userData->phone : '');

        $form->addText('address', 'Adresa:')
            ->setRequired('Zadejte svou adresu')
            ->setHtmlAttribute('id', 'address-field')
            ->setHtmlAttribute('placeholder', 'Zadejte svou adresu')
            ->setDefaultValue($userData && isset($userData->address) ? $userData->address : '');

        $form->addText('city', 'Město:')
            ->setRequired('Zadejte své město')
            ->setHtmlAttribute('id', 'city-field')
            ->setHtmlAttribute('placeholder', 'Zadejte své město')
            ->setDefaultValue($userData && isset($userData->city) ? $userData->city : '');

        $form->addText('psc', 'PSČ:')
            ->setRequired('Zadejte své PSČ')
            ->setHtmlAttribute('id', 'psc-field')
            ->setHtmlAttribute('placeholder', 'Zadejte své PSČ')
            ->setDefaultValue($userData && isset($userData->psc) ? $userData->psc : '');

        $form->addHidden('order_token', bin2hex(random_bytes(16))); // Unique token to prevent duplicate submissions

        $form->addProtection('Formulář expiroval, prosím odešlete znovu.'); // CSRF protection

        $form->addSubmit('submit', 'Dokončit objednávku')
            ->setHtmlAttribute('class', 'btn px-6 py-3 rounded-xl text-base font-semibold transition transform hover:scale-105')
            ->setHtmlAttribute('style', 'background-color: var(--color-primary); color: var(--button-text);');

        // If the form is successfully submitted, handle the form data
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

        if (!isset($session->shipping) || !isset($session->payment)) {
            $this->flashMessage('Prosím, vyberte způsob dopravy a platby.', 'warning');
            $this->redirect('Cart:order');
            return;
        }

        // Check for duplicate submission using order_token
        $orderToken = $values->order_token;
        $sessionToken = $session->order_token ?? null;
        if ($sessionToken && $sessionToken === $orderToken) {
            $this->flashMessage('Objednávka již byla zpracována.', 'warning');
            $this->redirect('Home:default');
            return;
        }
        $session->order_token = $orderToken;

        $userId = $this->getUser()->isLoggedIn() ? (int)$this->getUser()->getId() : null;
        $payment = $session->payment;
        $shipping = $session->shipping;

        try {
            if ($userId) {
                $order = $this->orderFacade->createOrder(
                    $userId,
                    $values->firstname,
                    $values->lastname,
                    $values->email,
                    $values->phone,
                    $values->address,
                    $values->city,
                    $values->psc,
                    $payment,
                    $quantities,
                    $shipping,
                    $session->delivery_point
                );
            } else {
                $order = $this->orderFacade->createGuestOrder(
                    $values->firstname,
                    $values->lastname,
                    $values->email,
                    $values->phone,
                    $values->address,
                    $values->city,
                    $values->psc,
                    $payment,
                    $quantities,
                    $shipping,
                    $session->delivery_point
                );
            }

            $items = $this->orderFacade->getOrderItems($order->id);
            $recipientName = $values->firstname . ' ' . $values->lastname;
            $this->mailSender->sendInvoiceEmail($values->email, $recipientName, $order, $items);
            $this->mailSender->sendNewOrderEmail($recipientName, $order, $items);

            // Clear session to prevent resubmission
            unset($session->quantities, $session->shipping, $session->payment, $session->additionalCost, $session->delivery_point, $session->order_token);
            $this->flashMessage('Objednávka byla úspěšně dokončena a faktura odeslána.', 'success');
            $this->redirect('Home:default');
        } catch (\InvalidArgumentException $e) {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect('Cart:default');
        }
    }

    public function actionCreateOrder(): void
    {
        $quantities = $this->getHttpRequest()->getPost('quantities') ?? [];
        $selected = [];

        foreach ($quantities as $caseId => $data) {
            $amount = (int)($data['amount'] ?? 0);
            if ($amount > 0) {
                $selected[(int)$caseId] = $amount;
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
        $case = $this->orderFacade->createCase($caseData, null);

        if ($this->getUser()->isLoggedIn() === false) {
            $session = $this->getSession('order');
            $quantities = $session->quantities ?? [];
            $quantities[$case->id] = ($quantities[$case->id] ?? 0) + 1;
            $session->quantities = $quantities;
        }

        $this->flashMessage('Kryt byl přidán do košíku.', 'success');
        $this->redirect('this');
    }

    protected function createComponentOrderForm(): Form
    {
        $form = new Form;

        $form->addSelect('shipping', 'Způsob dopravy:', $this->orderFacade->getShippingOptions())
            ->setRequired('Zvolte způsob dopravy')
            ->setHtmlAttribute('id', 'shipping-field');

        $form->addText('delivery_point', 'Místo doručení (např. název Z-Boxu nebo pobočky):')
            ->setHtmlAttribute('id', 'delivery-point-input')
            ->setHtmlAttribute('placeholder', 'Zadejte název Z-Boxu nebo pobočky')
            ->setRequired(false)
            ->addCondition(Form::EQUAL, $form['shipping'], 'ZASILKOVNA')
                ->toggle('delivery-point-field')
            ->addCondition(Form::EQUAL, $form['shipping'], 'BALIKOVNA')
                ->toggle('delivery-point-field');

        $form->addSelect('payment', 'Způsob platby:', [
            'PREVOD' => 'Bankovní převod',
            'DOBIRKA' => 'Dobírka (+40 Kč)',
        ])
            ->setRequired('Zvolte způsob platby')
            ->setHtmlAttribute('id', 'payment-field');

        $form->addSubmit('submit', 'Pokračovat k osobním údajům')
            ->setHtmlAttribute('class', 'btn px-8 py-4 rounded-2xl text-lg font-bold transition transform hover:scale-105')
            ->setHtmlAttribute('style', 'background-color: var(--color-primary); color: var(--button-text);');

        $form->onSuccess[] = [$this, 'orderFormSucceeded'];
        return $form;
    }

    public function orderFormSucceeded(Form $form, \stdClass $values): void
    {
        $session = $this->getSession('order');
        $quantities = $session->quantities ?? [];

        if (empty($quantities)) {
            $this->flashMessage('Košík je prázdný.', 'warning');
            $this->redirect('Cart:default');
            return;
        }

        // Validate shipping code via OrderFacade
        if (!$this->orderFacade->isValidShippingCode($values->shipping)) {
            $this->flashMessage('Neplatný způsob dopravy.', 'danger');
            $this->redirect('this');
            return;
        }

        $session->shipping = $values->shipping;
        $session->payment = $values->payment;
        $session->delivery_point = $values->delivery_point ?: null;

        $additionalCost = $this->orderFacade->calculateAdditionalCost($values->shipping, $values->payment);
        $session->additionalCost = $additionalCost;

        $this->redirect('Cart:info');
    }
}