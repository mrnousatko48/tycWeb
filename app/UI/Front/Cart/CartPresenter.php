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
            $this->flashMessage($lang === 'en' ? 'Cart is empty.' : 'Košík je prázdný.', 'warning');
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
            $this->flashMessage($lang === 'en' ? 'Cart is empty.' : 'Košík je prázdný.', 'warning');
            $this->redirect('Cart:default', ['lang' => $lang]);
        }

        if (!isset($session->vendor) || !isset($session->shippingOption) || !isset($session->paymentMethod)) {
            $this->flashMessage($lang === 'en' ? 'Please select a vendor, shipping method, and payment method.' : 'Prosím, vyberte dopravce, způsob dopravy a platby.', 'warning');
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
        $shippingName = $shippingInfo ? $shippingInfo['name'] : ($lang === 'en' ? 'Not selected' : 'Není vybráno');
        $paymentInfo = $this->orderFacade->getPaymentInfo((int)$session->paymentMethod, $lang);
        $paymentCost = $paymentInfo ? (float)$paymentInfo['price'] : 0.0;
        $paymentName = $paymentInfo ? $paymentInfo['name'] : ($lang === 'en' ? 'Not selected' : 'Není vybráno');
        $totalCartValue = $itemsSubtotal + $shippingCost + $paymentCost;

        $this->template->cases = $decodedCases;
        $this->template->quantities = $quantities;
        $this->template->itemsSubtotal = $itemsSubtotal;
        $this->template->shippingCost = $shippingCost;
        $this->template->paymentCost = $paymentCost;
        $this->template->totalCartValue = $totalCartValue;
        $this->template->shipping = $shippingName;
        $this->template->payment = $paymentName;
        $this->template->delivery_point = $session->delivery_point ?? ($lang === 'en' ? 'Not specified' : 'Není zadáno');
        $this->template->currency = $lang === 'en' ? '€' : 'Kč';
        $this->template->lang = $lang;
    }

    protected function createComponentOrderForm(): Form
    {
        $form = new Form;
        $form->setTranslator($this->translator);

        $lang = $this->getHttpRequest()->getQuery('lang') ?? $this->getSession('order')->lang ?? 'cs';
        $vendor = $form->addSelect('vendor', 'cart.vendor', $this->orderFacade->getVendors($lang))
            ->setPrompt($lang === 'en' ? '---- Select Vendor ----' : '---- Vyberte dopravce ----')
            ->setRequired($lang === 'en' ? 'Please select a vendor.' : 'Zvolte dopravce.');

        $shippingOption = $form->addSelect('shippingOption', 'cart.shipping_option')
            ->setHtmlAttribute('data-depends', $vendor->getHtmlName())
            ->setHtmlAttribute('data-url', $this->link('Endpoint:shippingOptions', ['vendor' => '#', 'lang' => $lang]))
            ->setRequired($lang === 'en' ? 'Please select a shipping method.' : 'Zvolte způsob dopravy.');

        $paymentMethod = $form->addSelect('paymentMethod', 'cart.payment_method')
            ->setHtmlAttribute('data-depends', $vendor->getHtmlName())
            ->setHtmlAttribute('data-url', $this->link('Endpoint:paymentMethods', ['vendor' => '#', 'lang' => $lang]))
            ->setPrompt($lang === 'en' ? '---- Select Payment Method ----' : '---- Vyberte způsob platby ----')
            ->setRequired($lang === 'en' ? 'Please select a payment method.' : 'Zvolte způsob platby.');

        $deliveryPoint = $form->addText('delivery_point', 'cart.delivery_point')
            ->setHtmlAttribute('id', 'delivery-point-input')
            ->setHtmlAttribute('placeholder', $lang === 'en' ? 'Enter Z-Box or branch name, or address' : 'Zadejte název Z-Boxu nebo pobočky, nebo adresa')
            ->setRequired($lang === 'en' ? 'Delivery point is required.' : 'Místo doručení je povinné.');

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

        $form->addSubmit('submit', $lang === 'en' ? 'Proceed to Personal Details' : 'Pokračovat k osobním údajům')
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
            $this->flashMessage($values->lang === 'en' ? 'Cart is empty.' : 'Košík je prázdný.', 'warning');
            $this->redirect('Cart:default', ['lang' => $values->lang]);
            return;
        }

        // Validate vendor and shipping option
        if (!$this->orderFacade->isValidVendor($values->vendor)) {
            $this->flashMessage($values->lang === 'en' ? 'Invalid vendor.' : 'Neplatný dopravce.', 'danger');
            $this->redirect('this', ['lang' => $values->lang]);
            return;
        }
        if (!$this->orderFacade->isValidShippingOption($values->shippingOption)) {
            $this->flashMessage($values->lang === 'en' ? 'Invalid shipping method.' : 'Neplatný způsob dopravy.', 'danger');
            $this->redirect('this', ['lang' => $values->lang]);
            return;
        }
        if (!$this->orderFacade->isValidPaymentMethod($values->paymentMethod)) {
            $this->flashMessage($values->lang === 'en' ? 'Invalid payment method.' : 'Neplatný způsob platby.', 'danger');
            $this->redirect('this', ['lang' => $values->lang]);
            return;
        }

        $session->vendor = $values->vendor;
        $session->shippingOption = $values->shippingOption;
        $session->paymentMethod = $values->paymentMethod;
        $session->lang = $values->lang ?? $session->lang ?? 'cs';
        $session->delivery_point = $values->delivery_point ?: null;

        $additionalCost = $this->orderFacade->calculateAdditionalCost($values->shippingOption, $values->paymentMethod, $values->lang);
        $session->additionalCost = $additionalCost;
        $this->redirect('Cart:info', ['lang' => $session->lang]);
    }

    protected function createComponentSendOrderForm(): Form
    {
        $form = new Form;
        $form->setTranslator($this->translator); // Set the translator for form labels and messages

        $lang = $this->getSession('order')->lang ?? 'cs';
        $user = $this->getUser()->isLoggedIn() ? $this->getUser()->getIdentity() : null;
        if ($user) {
            $userData = $this->userFacade->getUserById($user->getId());
        } else {
            $userData = null;
        }

        $form->addText('firstname', 'cart.firstname')
            ->setRequired($lang === 'en' ? 'First name is required.' : 'Jméno je povinné.')
            ->setHtmlAttribute('id', 'firstname-field')
            ->setDefaultValue($userData ? $userData->firstname : '');

        $form->addText('lastname', 'cart.lastname')
            ->setRequired($lang === 'en' ? 'Last name is required.' : 'Příjmení je povinné.')
            ->setHtmlAttribute('id', 'lastname-field')
            ->setDefaultValue($userData ? $userData->lastname : '');

        $form->addEmail('email', 'cart.email')
            ->setRequired($lang === 'en' ? 'Email is required.' : 'Email je povinný.')
            ->setHtmlAttribute('id', 'email-field')
            ->setHtmlAttribute('placeholder', $lang === 'en' ? 'Enter your email' : 'Zadejte váš email')
            ->setDefaultValue($userData ? $userData->email : '');

        $form->addText('phone', 'cart.phone')
            ->setRequired($lang === 'en' ? 'Phone number is required.' : 'Telefonní číslo je povinné.')
            ->setHtmlAttribute('id', 'phone-field')
            ->setHtmlAttribute('placeholder', $lang === 'en' ? 'Enter your phone number' : 'Zadejte vaše telefonní číslo')
            ->setDefaultValue($userData && isset($userData->phone) ? $userData->phone : '');

        $form->addText('address', 'cart.address')
            ->setRequired($lang === 'en' ? 'Address is required.' : 'Adresa je povinná.')
            ->setHtmlAttribute('id', 'address-field')
            ->setHtmlAttribute('placeholder', $lang === 'en' ? 'Enter your address' : 'Zadejte vaši adresu')
            ->setDefaultValue($userData && isset($userData->address) ? $userData->address : '');

        $form->addText('city', 'cart.city')
            ->setRequired($lang === 'en' ? 'City is required.' : 'Město je povinné.')
            ->setHtmlAttribute('id', 'city-field')
            ->setHtmlAttribute('placeholder', $lang === 'en' ? 'Enter your city' : 'Zadejte vaše město')
            ->setDefaultValue($userData && isset($userData->city) ? $userData->city : '');

        $form->addText('psc', 'cart.psc')
            ->setRequired($lang === 'en' ? 'Postal code is required.' : 'PSČ je povinné.')
            ->setHtmlAttribute('id', 'psc-field')
            ->setHtmlAttribute('placeholder', $lang === 'en' ? 'Enter your postal code' : 'Zadejte vaše PSČ')
            ->setDefaultValue($userData && isset($userData->psc) ? $userData->psc : '');

        $form->addHidden('order_token', bin2hex(random_bytes(16)));
        $form->addHidden('lang', $lang);

        $form->addProtection($lang === 'en' ? 'The form has expired. Please try again.' : 'Formulář vypršel. Zkuste to znovu.');

        $form->addSubmit('submit', $lang === 'en' ? 'Submit Order' : 'Odeslat objednávku')
            ->setHtmlAttribute('class', 'btn px-6 py-3 rounded-xl text-base font-semibold transition transform hover:scale-105')
            ->setHtmlAttribute('style', 'background-color: var(--color-primary); color: var(--button-text);');

        $form->onSuccess[] = [$this, 'sendOrderFormSucceeded'];
        return $form;
    }

    public function sendOrderFormSucceeded(Form $form, \stdClass $values): void
    {
        $session = $this->getSession('order');
        $quantities = $session->quantities ?? [];
        $lang = $values->lang ?? $session->lang ?? 'cs';

        if (empty($quantities)) {
            $this->flashMessage($lang === 'en' ? 'Cannot complete an empty order.' : 'Nelze dokončit prázdnou objednávku.', 'danger');
            $this->redirect('Cart:default', ['lang' => $lang]);
            return;
        }

        if (!isset($session->vendor) || !isset($session->shippingOption) || !isset($session->paymentMethod)) {
            $this->flashMessage($lang === 'en' ? 'Please select a vendor, shipping method, and payment method.' : 'Prosím, vyberte dopravce, způsob dopravy a platby.', 'warning');
            $this->redirect('Cart:order', ['lang' => $lang]);
            return;
        }

        $orderToken = $values->order_token;
        $sessionToken = $session->order_token ?? null;
        if ($sessionToken && $sessionToken === $orderToken) {
            $this->flashMessage($lang === 'en' ? 'Order has already been processed.' : 'Objednávka již byla zpracována.', 'warning');
            $this->redirect('Home:default', ['lang' => $lang]);
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
            $this->mailSender->sendInvoiceEmail($values->email, $recipientName, $order, $items, $lang);
            $this->mailSender->sendNewOrderEmail($recipientName, $order, $items, $lang);

            unset($session->quantities, $session->vendor, $session->shippingOption, $session->paymentMethod, $session->additionalCost, $session->delivery_point, $session->order_token, $session->lang);
            $this->flashMessage($lang === 'en' ? 'Order successfully completed and invoice sent.' : 'Objednávka byla úspěšně dokončena a faktura odeslána.', 'success');
            $this->redirect('Home:default', ['lang' => $lang]);
        } catch (\InvalidArgumentException $e) {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect('Cart:default', ['lang' => $lang]);
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
            $this->flashMessage($lang === 'en' ? 'Cart is empty or no quantities specified.' : 'Košík je prázdný nebo nebylo zadáno žádné množství.', 'warning');
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
            $this->flashMessage($this->getSession('order')->lang === 'en' ? 'Quantity must be at least 1.' : 'Množství musí být alespoň 1.', 'warning');
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

        $lang = $this->getSession('order')->lang ?? 'cs';
        $this->flashMessage($lang === 'en' ? 'Case removed from cart.' : 'Kryt byl odebrán z košíku.', 'info');
        $this->redirect('this', ['lang' => $lang]);
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

        $lang = $this->getSession('order')->lang ?? 'cs';
        $this->flashMessage($lang === 'en' ? 'Case added to cart.' : 'Kryt byl přidán do košíku.', 'success');
        $this->redirect('this', ['lang' => $lang]);
    }
}