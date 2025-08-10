<?php
declare(strict_types=1);

namespace App\MailSender;

use Nette\Mail\Message;
use Nette\Mail\Mailer;
use Latte\Engine;
use Mpdf\Mpdf;
use App\Model\OrderFacade;
use App\Model\EmailFacade;

class MailSender
{
    public function __construct(
        private Mailer $mailer,
        private OrderFacade $orderFacade,
        private EmailFacade $emailFacade,
    ) {
    }

    public function createRegistrationEmail(string $email, string $username, string $lang = 'cs'): Message
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('registration', $lang);
        if (!$template) {
            throw new \Exception("Template registration for language $lang was not found.");
        }

        $params = [
            'email' => $email,
            'username' => $username,
        ];

        $latte->setLoader(new \Latte\Loaders\StringLoader());
        $subject = $latte->renderToString($template['subject'], $params);
        $html = $latte->renderToString($template['body'], $params);

        $mail = new Message;
        $mail->setFrom('opnx3d@seznam.cz')
            ->addTo($email)
            ->setSubject($subject)
            ->setHtmlBody($html);

        return $mail;
    }

    public function createNewUserEmail(string $email, string $username, string $lang = 'cs'): Message
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('new_user', $lang);
        if (!$template) {
            throw new \Exception("Template new_user for language $lang was not found.");
        }

        $params = [
            'email' => $email,
            'username' => $username,
        ];

        $latte->setLoader(new \Latte\Loaders\StringLoader());
        $subject = $latte->renderToString($template['subject'], $params);
        $html = $latte->renderToString($template['body'], $params);

        $mail = new Message;
        $mail->setFrom('opnx3d@seznam.cz')
            ->addTo($template['recipient_email'] ?? 'opnx3d@seznam.cz')
            ->setSubject($subject)
            ->setHtmlBody($html);

        return $mail;
    }

    public function sendRegistrationEmail(string $email, string $username, string $lang = 'cs'): void
    {
        $mail = $this->createRegistrationEmail($email, $username, $lang);
        $this->mailer->send($mail);
    }

    public function sendNewUserEmail(string $email, string $username, string $lang = 'cs'): void
    {
        $mail = $this->createNewUserEmail($email, $username, $lang);
        $this->mailer->send($mail);
    }

    public function sendInvoiceEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order, array $orderItems, string $lang = 'cs'): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('invoice', $lang);
        if (!$template) {
            error_log("Template 'invoice' for language $lang not found in database.");
            throw new \Exception("Template invoice for language $lang was not found.");
        }

        $itemsSubtotal = 0;
        foreach ($orderItems as $item) {
            $price = $lang === 'en' ? (float)$item->total_price_eur : (float)$item->total_price;
            $itemsSubtotal += $price * $item->quantity;
        }

        $shippingInfo = $this->orderFacade->getShippingInfo($order->shipping, $lang);
        $shippingCost = $shippingInfo ? (float)$shippingInfo['cost'] : 0.0;
        $vendorName = $this->orderFacade->getVendorNameByShippingOptionId($order->shipping);

        $paymentInfo = $this->orderFacade->getPaymentInfo($order->payment, $lang);
        $paymentCost = $paymentInfo ? (float)$paymentInfo['price'] : 0.0;
        $total = $itemsSubtotal + $shippingCost + $paymentCost;

        $params = [
            'order' => $order,
            'items' => $orderItems,
            'recipient' => $recipientName,
            'itemsSubtotal' => $itemsSubtotal,
            'shippingCost' => $shippingCost,
            'paymentCost' => $paymentCost,
            'total' => $total,
            'vendorName' => $vendorName,
        ];

        $latte->setLoader(new \Latte\Loaders\StringLoader());
        try {
            $htmlInvoice = $latte->renderToString($template['body'], $params);
        } catch (\Exception $e) {
            error_log('Error rendering invoice template: ' . $e->getMessage());
            throw new \Exception("Error rendering invoice template for language $lang: " . $e->getMessage());
        }

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($htmlInvoice);
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        $emailTemplate = $this->emailFacade->getTemplateByName('invoice_email', $lang);
        if (!$emailTemplate) {
            error_log("Template 'invoice_email' for language $lang not found in database.");
            throw new \Exception("Template invoice_email for language $lang was not found.");
        }

        $emailParams = [
            'recipient' => $recipientName,
            'orderId' => $order->id,
        ];

        try {
            $subject = $latte->renderToString($emailTemplate['subject'], $emailParams);
            $htmlBody = $latte->renderToString($emailTemplate['body'], $emailParams);
        } catch (\Exception $e) {
            error_log('Error rendering invoice_email template: ' . $e->getMessage());
            throw new \Exception("Error rendering invoice_email template for language $lang: " . $e->getMessage());
        }

        $mail = new Message;
        $mail->setFrom('opnx3d@seznam.cz')
            ->addTo($recipientEmail)
            ->setSubject($subject)
            ->setHtmlBody($htmlBody)
            ->addAttachment("invoice-{$order->id}.pdf", $pdfContent, 'application/pdf');

        $this->mailer->send($mail);
    }

    public function sendPasswordResetEmail(string $email, string $resetCode, string $lang = 'cs'): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('password_reset', $lang);
        if (!$template) {
            throw new \Exception("Template password_reset for language $lang was not found.");
        }

        $params = [
            'email' => $email,
            'resetCode' => $resetCode,
        ];

        $latte->setLoader(new \Latte\Loaders\StringLoader());
        $subject = $latte->renderToString($template['subject'], $params);
        $html = $latte->renderToString($template['body'], $params);

        $mail = new Message;
        $mail->setFrom('opnx3d@seznam.cz')
            ->addTo($email)
            ->setSubject($subject)
            ->setHtmlBody($html);

        $this->mailer->send($mail);
    }

public function sendPaymentConfirmationEmail(string $recipientEmail, string $recipientName, \stdClass $order, string $lang = 'cs'): void
{
    $latte = new Engine();
    $template = $this->emailFacade->getTemplateByName('payment_confirmation', $lang);
    if (!$template) {
        throw new \Exception("Template payment_confirmation for language $lang was not found.");
    }

    $params = [
        'recipient' => $recipientName,
        'orderId' => $order->id,
        'variableSymbol' => $order->variable_symbol,
    ];

    $latte->setLoader(new \Latte\Loaders\StringLoader());
    $subject = $latte->renderToString($template['subject'], $params);
    $html = $latte->renderToString($template['body'], $params);

    $mail = new Message;
    $mail->setFrom('opnx3d@seznam.cz')
        ->addTo($recipientEmail)
        ->setSubject($subject)
        ->setHtmlBody($html);

    $this->mailer->send($mail);
}

public function sendShippedEmail(string $recipientEmail, string $recipientName, \stdClass $order, string $lang = 'cs'): void
{
    $latte = new Engine();
    $template = $this->emailFacade->getTemplateByName('shipped', $lang);
    if (!$template) {
        throw new \Exception("Template shipped for language $lang was not found.");
    }

    $params = [
        'recipient' => $recipientName,
        'orderId' => $order->id,
    ];

    $latte->setLoader(new \Latte\Loaders\StringLoader());
    $subject = $latte->renderToString($template['subject'], $params);
    $html = $latte->renderToString($template['body'], $params);

    $mail = new Message;
    $mail->setFrom('opnx3d@seznam.cz')
        ->addTo($recipientEmail)
        ->setSubject($subject)
        ->setHtmlBody($html);

    $this->mailer->send($mail);
}

public function sendReadyForPickupEmail(string $recipientEmail, string $recipientName, \stdClass $order, string $lang = 'cs'): void
{
    $latte = new Engine();
    $template = $this->emailFacade->getTemplateByName('ready_for_pickup', $lang);
    if (!$template) {
        throw new \Exception("Template ready_for_pickup for language $lang was not found.");
    }

    $params = [
        'recipient' => $recipientName,
        'orderId' => $order->id,
        'deliveryPoint' => $order->delivery_point ?? ($lang === 'en' ? 'Not specified' : 'Není uvedeno'),
    ];

    $latte->setLoader(new \Latte\Loaders\StringLoader());
    $subject = $latte->renderToString($template['subject'], $params);
    $html = $latte->renderToString($template['body'], $params);

    $mail = new Message;
    $mail->setFrom('opnx3d@seznam.cz')
        ->addTo($recipientEmail)
        ->setSubject($subject)
        ->setHtmlBody($html);

    $this->mailer->send($mail);
}

public function sendPickedUpEmail(string $recipientEmail, string $recipientName, \stdClass $order, string $lang = 'cs'): void
{
    $latte = new Engine();
    $template = $this->emailFacade->getTemplateByName('picked_up', $lang);
    if (!$template) {
        throw new \Exception("Template picked_up for language $lang was not found.");
    }

    $params = [
        'recipient' => $recipientName,
        'orderId' => $order->id,
    ];

    $latte->setLoader(new \Latte\Loaders\StringLoader());
    $subject = $latte->renderToString($template['subject'], $params);
    $html = $latte->renderToString($template['body'], $params);

    $mail = new Message;
    $mail->setFrom('opnx3d@seznam.cz')
        ->addTo($recipientEmail)
        ->setSubject($subject)
        ->setHtmlBody($html);

    $this->mailer->send($mail);
}

    public function sendNewOrderEmail(string $recipientName, \Nette\Database\Table\ActiveRow $order, array $orderItems, string $lang = 'cs'): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('new_order', $lang);
        if (!$template) {
            error_log("Template 'new_order' for language $lang not found in database.");
            throw new \Exception("Template new_order for language $lang was not found.");
        }

        $itemsSubtotal = 0;
        foreach ($orderItems as $item) {
            $price = $lang === 'en' ? (float)$item->total_price_eur : (float)$item->total_price;
            $itemsSubtotal += $price * $item->quantity;
        }

        $shippingInfo = $this->orderFacade->getShippingInfo($order->shipping, $lang);
        $shippingCost = $shippingInfo ? (float)$shippingInfo['cost'] : 0.0;
        $vendorName = $this->orderFacade->getVendorNameByShippingOptionId($order->shipping);

        $paymentInfo = $this->orderFacade->getPaymentInfo($order->payment, $lang);
        $paymentCost = $paymentInfo ? (float)$paymentInfo['price'] : 0.0;
        $total = $itemsSubtotal + $shippingCost + $paymentCost;

        $params = [
            'order' => $order,
            'items' => $orderItems,
            'recipient' => $recipientName,
            'itemsSubtotal' => $itemsSubtotal,
            'shippingCost' => $shippingCost,
            'paymentCost' => $paymentCost,
            'total' => $total,
            'vendorName' => $vendorName,
        ];

        $latte->setLoader(new \Latte\Loaders\StringLoader());
        try {
            $subject = $latte->renderToString($template['subject'], $params);
            $html = $latte->renderToString($template['body'], $params);
        } catch (\Exception $e) {
            error_log('Error rendering new_order template: ' . $e->getMessage());
            throw new \Exception("Error rendering new_order template for language $lang: " . $e->getMessage());
        }

        $mail = new Message;
        $mail->setFrom('opnx3d@seznam.cz')
            ->addTo($template['recipient_email'] ?? 'opnx3d@seznam.cz')
            ->setSubject($subject)
            ->setHtmlBody($html);

        $this->mailer->send($mail);
    }
}