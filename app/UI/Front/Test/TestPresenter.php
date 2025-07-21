<?php
declare(strict_types=1);

namespace App\UI\Front\Test;

use Nette\Application\UI\Form;
use App\UI\Front\BaseFrontPresenter;

final class TestPresenter extends BaseFrontPresenter
{
        public function renderDefault(): void
    {
    }

protected function createComponentForm(): Form
{
    $form = new Form;
    $vendor = $form->addSelect('vendor', 'Vendor:', $this->orderFacade->getVendors())
        ->setPrompt('----');

    $shippingOption = $form->addSelect('shippingOption', 'Shipping Option:')
        ->setHtmlAttribute('data-depends', $vendor->getHtmlName())
        ->setHtmlAttribute('data-url', $this->link('Endpoint:shippingOptions', '#'));

    $paymentMethod = $form->addSelect('paymentMethod', 'Payment Method:')
        ->setHtmlAttribute('data-depends', $vendor->getHtmlName())
        ->setHtmlAttribute('data-url', $this->link('Endpoint:paymentMethods', '#'))
        ->setPrompt('----');

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

    $form->onSuccess[] = function (Form $form, \stdClass $values) {
        \Tracy\Debugger::barDump($values, 'Form Values');
        $this->flashMessage('Order submitted: Vendor ID ' . $values->vendor . ', Shipping Option ID ' . $values->shippingOption . ', Payment Method ID ' . $values->paymentMethod, 'success');
        $this->redirect('this');
    };

    return $form;
}

}