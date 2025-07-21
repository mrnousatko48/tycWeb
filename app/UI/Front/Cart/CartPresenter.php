<?php
declare(strict_types=1);

namespace App\UI\Front\Cart;

use App\UI\Front\BaseFrontPresenter;
use Nette\Application\UI\Form;
use App\MailSender\MailSender;
use App\Model\EmailFacade;

final class CartPresenter extends BaseFrontPresenter
{
    private MailSender $mailSender;
    private EmailFacade $emailFacade;

    public function __construct(MailSender $mailSender, EmailFacade $emailFacade)
    {
        parent::__construct();
        $this->mailSender = $mailSender;
        $this->emailFacade = $emailFacade;
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

        if (!isset($session->vendor) || !isset($session->shippingOption) || !isset($session->paymentMethod)) {
            $this->flashMessage('Prosím, vyberte dopravce, způsob dopravy a platby.', 'warning');
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

        $shippingInfo = $this->orderFacade->getShippingInfo((int)$session->shippingOption);
        \Tracy\Debugger::barDump($shippingInfo, 'Shipping Info');
        $shippingCost = $shippingInfo ? (float)$shippingInfo['cost'] : 0.0;
        $shippingName = $shippingInfo ? $shippingInfo['name'] : 'Není vybráno';
        $paymentInfo = $this->orderFacade->getPaymentInfo((int)$session->paymentMethod);
        \Tracy\Debugger::barDump($paymentInfo, 'Payment Info');
        $paymentCost = $paymentInfo ? (float)$paymentInfo['price'] : 0.0;
        $paymentName = $paymentInfo ? $paymentInfo['name'] : 'Není vybráno';
        $totalCartValue = $itemsSubtotal + $shippingCost + $paymentCost;

        $this->template->cases = $decodedCases;
        $this->template->quantities = $quantities;
        $this->template->itemsSubtotal = $itemsSubtotal;
        $this->template->shippingCost = $shippingCost;
        $this->template->paymentCost = $paymentCost;
        $this->template->totalCartValue = $totalCartValue;
        $this->template->shipping = $shippingName;
        $this->template->payment = $paymentName;
        $this->template->delivery_point = $session->delivery_point ?? 'Není zadáno';
    }

    protected function createComponentOrderForm(): Form
    {
        $form = new Form;
        $vendor = $form->addSelect('vendor', 'Dopravce:', $this->orderFacade->getVendors())
            ->setPrompt('----')
            ->setRequired('Zvolte dopravce');

        $shippingOption = $form->addSelect('shippingOption', 'Způsob dopravy:')
            ->setHtmlAttribute('data-depends', $vendor->getHtmlName())
            ->setHtmlAttribute('data-url', $this->link('Endpoint:shippingOptions', '#'))
            ->setRequired('Zvolte způsob dopravy');

        $paymentMethod = $form->addSelect('paymentMethod', 'Způsob platby:')
            ->setHtmlAttribute('data-depends', $vendor->getHtmlName())
            ->setHtmlAttribute('data-url', $this->link('Endpoint:paymentMethods', '#'))
            ->setPrompt('----')
            ->setRequired('Zvolte způsob platby');

        $deliveryPoint = $form->addText('delivery_point', 'Místo doručení (např. název Z-Boxu nebo pobočky):')
            ->setHtmlAttribute('id', 'delivery-point-input')
            ->setHtmlAttribute('placeholder', 'Zadejte název Z-Boxu nebo pobočky, nebo adresa')
            ->setRequired();
            

        $form->onAnchor[] = function () use ($vendor, $shippingOption, $paymentMethod) {
            $vendorId = $vendor->getValue() ? (int)$vendor->getValue() : null;
            \Tracy\Debugger::barDump($vendorId, 'Selected Vendor ID');

            // Populate shippingOption
            $shippingItems = $vendorId
                ? $this->orderFacade->getShippingOptionsByVendor($vendorId)
                : [];
            \Tracy\Debugger::barDump($shippingItems, 'Shipping Options on Anchor');
            $shippingOption->setItems($shippingItems);

            // Populate paymentMethod
            $paymentItems = $vendorId
                ? $this->orderFacade->getPaymentMethodsByVendor($vendorId)
                : [];
            \Tracy\Debugger::barDump($paymentItems, 'Payment Methods on Anchor');
            $paymentMethod->setItems($paymentItems);
        };

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

        // Validate vendor and shipping option
        if (!$this->orderFacade->isValidVendor($values->vendor)) {
            $this->flashMessage('Neplatný dopravce.', 'danger');
            $this->redirect('this');
            return;
        }
        if (!$this->orderFacade->isValidShippingOption($values->shippingOption)) {
            $this->flashMessage('Neplatný způsob dopravy.', 'danger');
            $this->redirect('this');
            return;
        }
        if (!$this->orderFacade->isValidPaymentMethod($values->paymentMethod)) {
            $this->flashMessage('Neplatný způsob platby.', 'danger');
            $this->redirect('this');
            return;
        }

        $session->vendor = $values->vendor;
        $session->shippingOption = $values->shippingOption;
        $session->paymentMethod = $values->paymentMethod;
        $session->delivery_point = $values->delivery_point ?: null;

        $additionalCost = $this->orderFacade->calculateAdditionalCost($values->shippingOption, $values->paymentMethod);
        $session->additionalCost = $additionalCost;

        $this->redirect('Cart:info');
    }

    protected function createComponentSendOrderForm(): Form
    {
        $form = new Form;

        $user = $this->getUser()->isLoggedIn() ? $this->getUser()->getIdentity() : null;
        if ($user) {
            $userData = $this->userFacade->getUserById($user->getId());
        } else {
            $userData = null;
        }

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

        $form->addHidden('order_token', bin2hex(random_bytes(16)));

        $form->addProtection('Formulář expiroval, prosím odešlete znovu.');

        $form->addSubmit('submit', 'Dokončit objednávku')
            ->setHtmlAttribute('class', 'btn px-6 py-3 rounded-xl text-base font-semibold transition transform hover:scale-105')
            ->setHtmlAttribute('style', 'background-color: var(--color-primary); color: var(--button-text);');

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

        if (!isset($session->vendor) || !isset($session->shippingOption) || !isset($session->paymentMethod)) {
            $this->flashMessage('Prosím, vyberte dopravce, způsob dopravy a platby.', 'warning');
            $this->redirect('Cart:order');
            return;
        }

        $orderToken = $values->order_token;
        $sessionToken = $session->order_token ?? null;
        if ($sessionToken && $sessionToken === $orderToken) {
            $this->flashMessage('Objednávka již byla zpracována.', 'warning');
            $this->redirect('Home:default');
            return;
        }
        $session->order_token = $orderToken;

        $userId = $this->getUser()->isLoggedIn() ? (int)$this->getUser()->getId() : null;

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
                    $session->paymentMethod,
                    $quantities,
                    $session->shippingOption,
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
                    $session->paymentMethod,
                    $quantities,
                    $session->shippingOption,
                    $session->delivery_point
                );
            }

            $items = $this->orderFacade->getOrderItems($order->id);
            $recipientName = $values->firstname . ' ' . $values->lastname;
            $this->mailSender->sendInvoiceEmail($values->email, $recipientName, $order, $items);
            $this->mailSender->sendNewOrderEmail($recipientName, $order, $items);

            unset($session->quantities, $session->vendor, $session->shippingOption, $session->paymentMethod, $session->additionalCost, $session->delivery_point, $session->order_token);
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
}