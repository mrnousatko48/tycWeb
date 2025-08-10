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
        $session = $this->getSession('order');
        $lang = $this->getParameter('lang') ?? $session->lang ?? 'cs';
        $this->template->lang = $lang; // Pass it to the template

        $quantities = $session->quantities ?? [];

        if ($this->getUser()->isLoggedIn()) {
            $userId = (int)$this->getUser()->getId();
            $cases = $this->orderFacade->getCartCasesByUserId($userId);
            $this->template->quantities = $quantities;
        } else {
            if (empty($quantities)) {
                $this->template->cases = [];
                $this->template->totalCartValue = 0;
                $this->template->quantities = [];
                $this->template->lang = $lang;
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
            $upload = $case->user_upload_id ? $this->orderFacade->getUserUploadById($case->user_upload_id) : null;
            $caseArray['user_upload_filename'] = $upload ? $upload->original_filename : null;
            $decodedCases[] = (object) $caseArray;

            $quantity = $quantities[$case->id] ?? 1;
            $price = $lang === 'en' ? (float)$case->total_price_eur : (float)$case->total_price;
            $totalCartValue += $price * $quantity;
        }

        $this->template->cases = $decodedCases;
        $this->template->totalCartValue = $totalCartValue;
        $this->template->lang = $lang;
    }

public function renderOrder(): void
{
    $session = $this->getSession('order');
    $lang = $this->getHttpRequest()->getQuery('lang') ?? $session->lang ?? 'cs';
    $session->lang = $lang; // Update session language
    $quantities = $session->quantities ?? [];

    if (empty($quantities)) {
        $this->flashMessage('Košík je prázdný.', 'warning');
        $this->redirect('Cart:default', ['lang' => $lang]);
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
        $upload = $case->user_upload_id ? $this->orderFacade->getUserUploadById($case->user_upload_id) : null;
        $caseArray['user_upload_filename'] = $upload ? $upload->original_filename : null;
        $decodedCases[] = (object) $caseArray;

        $quantity = $quantities[$case->id] ?? 1;
        $price = $lang === 'en' ? (float)$case->total_price_eur : (float)$case->total_price;
        $totalCartValue += $price * $quantity;
    }

    $this->template->cases = $decodedCases;
    $this->template->quantities = $quantities;
    $this->template->totalCartValue = $totalCartValue;
    $this->template->currency = $lang === 'en' ? '€' : 'Kč';
    $this->template->lang = $lang;
}

public function renderInfo(): void
{
    $session = $this->getSession('order');
    $lang = $this->getParameter('lang') ?? $session->lang ?? 'cs';
    $quantities = $session->quantities ?? [];

    if (empty($quantities)) {
        $this->flashMessage('Košík je prázdný.', 'warning');
        $this->redirect('Cart:default', ['lang' => $lang]);
    }

    if (!isset($session->vendor) || !isset($session->shippingOption) || !isset($session->paymentMethod)) {
        $this->flashMessage('Prosím, vyberte dopravce, způsob dopravy a platby.', 'warning');
        $this->redirect('Cart:order', ['lang' => $lang]);
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
        $upload = $case->user_upload_id ? $this->orderFacade->getUserUploadById($case->user_upload_id) : null;
        $caseArray['user_upload_filename'] = $upload ? $upload->original_filename : null;
        $decodedCases[] = (object) $caseArray;

        $quantity = $quantities[$case->id] ?? 1;
        $price = $lang === 'en' ? (float)$case->total_price_eur : (float)$case->total_price;
        $itemsSubtotal += $price * $quantity;
    }

    $shippingInfo = $this->orderFacade->getShippingInfo((int)$session->shippingOption, $lang);
    $shippingCost = $shippingInfo ? (float)$shippingInfo['cost'] : 0.0;
    $shippingName = $shippingInfo ? $shippingInfo['name'] : 'Není vybráno';
    $paymentInfo = $this->orderFacade->getPaymentInfo((int)$session->paymentMethod, $lang);
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
    $this->template->currency = $lang === 'en' ? '€' : 'Kč';
    $this->template->lang = $lang;
}

   protected function createComponentOrderForm(): Form
{
    $form = new Form;
    $form->setTranslator($this->translator);

    $lang = $this->getHttpRequest()->getQuery('lang') ?? $this->getSession('order')->lang ?? 'cs';
    $vendor = $form->addSelect('vendor', 'cart.vendor', $this->orderFacade->getVendors($lang))
        ->setPrompt('----')
        ->setRequired('Zvolte dopravce');

    $shippingOption = $form->addSelect('shippingOption', 'cart.shipping_option')
        ->setHtmlAttribute('data-depends', $vendor->getHtmlName())
        ->setHtmlAttribute('data-url', $this->link('Endpoint:shippingOptions', ['vendor' => '#', 'lang' => $lang]))
        ->setRequired('Zvolte způsob dopravy');

    $paymentMethod = $form->addSelect('paymentMethod', 'cart.payment_method')
        ->setHtmlAttribute('data-depends', $vendor->getHtmlName())
        ->setHtmlAttribute('data-url', $this->link('Endpoint:paymentMethods', ['vendor' => '#', 'lang' => $lang]))
        ->setPrompt('----')
        ->setRequired('Zvolte způsob platby');

    $deliveryPoint = $form->addText('delivery_point', 'cart.delivery_point')
        ->setHtmlAttribute('id', 'delivery-point-input')
        ->setHtmlAttribute('placeholder', 'Zadejte název Z-Boxu nebo pobočky, nebo adresa')
        ->setRequired();

    $form->addHidden('lang', $lang);

    $form->onAnchor[] = function () use ($vendor, $shippingOption, $paymentMethod, $lang) {
        $vendorId = $vendor->getValue() ? (int)$vendor->getValue() : null;

        // Populate shippingOption
        $shippingItems = $vendorId
            ? $this->orderFacade->getShippingOptionsByVendor($vendorId, $lang)
            : [];
        $shippingOption->setItems($shippingItems);

        // Populate paymentMethod
        $paymentItems = $vendorId
            ? $this->orderFacade->getPaymentMethodsByVendor($vendorId, $lang)
            : [];
        $paymentMethod->setItems($paymentItems);
    };

    $form->addSubmit('submit', 'cart.submit_to_personal_details')
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
        $session->lang = $values->lang ?? $session->lang ?? 'cs';
        $session->delivery_point = $values->delivery_point ?: null;

        $additionalCost = $this->orderFacade->calculateAdditionalCost($values->shippingOption, $values->paymentMethod);
        $session->additionalCost = $additionalCost;
        $this->redirect('Cart:info', ['lang' => $session->lang]);
    }

    protected function createComponentSendOrderForm(): Form
{
    $form = new Form;
    $form->setTranslator($this->translator); // Set the translator for form labels and messages

    $user = $this->getUser()->isLoggedIn() ? $this->getUser()->getIdentity() : null;
    if ($user) {
        $userData = $this->userFacade->getUserById($user->getId());
    } else {
        $userData = null;
    }

    $form->addText('firstname', 'cart.firstname')
        ->setRequired('cart.firstname_required')
        ->setHtmlAttribute('id', 'firstname-field')
        ->setDefaultValue($userData ? $userData->firstname : '');

    $form->addText('lastname', 'cart.lastname')
        ->setRequired('cart.lastname_required')
        ->setHtmlAttribute('id', 'lastname-field')
        ->setDefaultValue($userData ? $userData->lastname : '');

    $form->addEmail('email', 'cart.email')
        ->setRequired('cart.email_required')
        ->setHtmlAttribute('id', 'email-field')
        ->setHtmlAttribute('placeholder', 'cart.email_placeholder')
        ->setDefaultValue($userData ? $userData->email : '');

    $form->addText('phone', 'cart.phone')
        ->setRequired('cart.phone_required')
        ->setHtmlAttribute('id', 'phone-field')
        ->setHtmlAttribute('placeholder', 'cart.phone_placeholder')
        ->setDefaultValue($userData && isset($userData->phone) ? $userData->phone : '');

    $form->addText('address', 'cart.address')
        ->setRequired('cart.address_required')
        ->setHtmlAttribute('id', 'address-field')
        ->setHtmlAttribute('placeholder', 'cart.address_placeholder')
        ->setDefaultValue($userData && isset($userData->address) ? $userData->address : '');

    $form->addText('city', 'cart.city')
        ->setRequired('cart.city_required')
        ->setHtmlAttribute('id', 'city-field')
        ->setHtmlAttribute('placeholder', 'cart.city_placeholder')
        ->setDefaultValue($userData && isset($userData->city) ? $userData->city : '');

    $form->addText('psc', 'cart.psc')
        ->setRequired('cart.psc_required')
        ->setHtmlAttribute('id', 'psc-field')
        ->setHtmlAttribute('placeholder', 'cart.psc_placeholder')
        ->setDefaultValue($userData && isset($userData->psc) ? $userData->psc : '');

    $form->addHidden('order_token', bin2hex(random_bytes(16)));

    $form->addProtection('cart.form_expired');

    $form->addSubmit('submit', 'cart.submit_order')
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
        $lang = $this->getParameter('lang') ?? $this->getHttpRequest()->getPost('lang') ?? 'cs';
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
            $this->redirect('Cart:default', ['lang' => $lang]);
        }

        $session = $this->getSession('order');
        $session->quantities = $selected;
        $session->lang = $lang;

        if ($this->getUser()->isLoggedIn()) {
            $session->quantities = $selected;
        }

        $this->redirect('Cart:order', ['lang' => $lang]);
    }

    public function handleUpdateQuantity(int $caseId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->flashMessage('Množství musí být alespoň 1.', 'warning');
            $this->redirect('this');
        }

        $session = $this->getSession('order');
        $quantities = $session->quantities ?? [];

        // Update quantity in session
        $quantities[$caseId] = $quantity;
        $session->quantities = $quantities;

        if ($this->isAjax()) {
            // Send updated total and quantities back to the client
            $lang = $session->lang ?? 'cs';
            $cases = $this->getUser()->isLoggedIn()
                ? $this->orderFacade->getCartCasesByUserId((int)$this->getUser()->getId())
                : $this->orderFacade->getCasesByIds(array_keys($quantities));

            $totalCartValue = 0;
            foreach ($cases as $case) {
                $quantity = $quantities[$case->id] ?? 1;
                $price = $lang === 'en' ? (float)$case->total_price_eur : (float)$case->total_price;
                $totalCartValue += $price * $quantity;
            }

            $this->payload->totalCartValue = number_format($totalCartValue, 2, $lang === 'en' ? '.' : ',', ' ');
            $this->payload->currency = $lang === 'en' ? '€' : 'Kč';
            $this->payload->quantities = $quantities;
            $this->sendJson($this->payload);
        }
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