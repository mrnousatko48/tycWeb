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

    public function createRegistrationEmail(string $email, string $username): Message
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('registration');
        if (!$template) {
            throw new \Exception('Šablona registration nebyla nalezena v databázi.');
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

    public function createNewUserEmail(string $email, string $username): Message
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('new_user');
        if (!$template) {
            throw new \Exception('Šablona new_user nebyla nalezena v databázi.');
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

    public function sendRegistrationEmail(string $email, string $username): void
    {
        $mail = $this->createRegistrationEmail($email, $username);
        $this->mailer->send($mail);
    }

    public function sendNewUserEmail(string $email, string $username): void
    {
        $mail = $this->createNewUserEmail($email, $username);
        $this->mailer->send($mail);
    }

    public function sendInvoiceEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order, array $orderItems): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('invoice');
        if (!$template) {
            error_log('Template "invoice" not found in database.');
            throw new \Exception('Šablona invoice nebyla nalezena v databázi.');
        }

        $itemsSubtotal = 0;
        foreach ($orderItems as $item) {
            $itemsSubtotal += $item->total_price * $item->quantity;
        }

        $shippingInfo = $this->orderFacade->getShippingInfo($order->shipping);
        $shippingCost = $shippingInfo ? $shippingInfo['cost'] : 0.0;
        $vendorName = $this->orderFacade->getVendorNameByShippingOptionId($order->shipping);

        $paymentCost = $order->payment === 'DOBIRKA' ? 40.0 : 0.0;
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
            throw new \Exception('Chyba při renderování šablony invoice: ' . $e->getMessage());
        }

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($htmlInvoice);
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        $emailTemplate = $this->emailFacade->getTemplateByName('invoice_email');
        if (!$emailTemplate) {
            error_log('Template "invoice_email" not found in database.');
            throw new \Exception('Šablona invoice_email nebyla nalezena v databázi.');
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
            throw new \Exception('Chyba při renderování šablony invoice_email: ' . $e->getMessage());
        }

        $mail = new Message;
        $mail->setFrom('opnx3d@seznam.cz')
            ->addTo($recipientEmail)
            ->setSubject($subject)
            ->setHtmlBody($htmlBody)
            ->addAttachment("faktura-{$order->id}.pdf", $pdfContent, 'application/pdf');

        $this->mailer->send($mail);
    }

    public function sendPasswordResetEmail(string $email, string $resetCode): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('password_reset');
        if (!$template) {
            throw new \Exception('Šablona password_reset nebyla nalezena v databázi.');
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

    public function sendPaymentConfirmationEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('payment_confirmation');
        if (!$template) {
            throw new \Exception('Šablona payment_confirmation nebyla nalezena v databázi.');
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

    public function sendShippedEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('shipped');
        if (!$template) {
            throw new \Exception('Šablona shipped nebyla nalezena v databázi.');
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

    public function sendReadyForPickupEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('ready_for_pickup');
        if (!$template) {
            throw new \Exception('Šablona ready_for_pickup nebyla nalezena v databázi.');
        }

        $params = [
            'recipient' => $recipientName,
            'orderId' => $order->id,
            'deliveryPoint' => $order->delivery_point ?? 'Není uvedeno dodací místo',
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

    public function sendPickedUpEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('picked_up');
        if (!$template) {
            throw new \Exception('Šablona picked_up nebyla nalezena v databázi.');
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

    public function sendNewOrderEmail(string $recipientName, \Nette\Database\Table\ActiveRow $order, array $orderItems): void
    {
        $latte = new Engine();
        $template = $this->emailFacade->getTemplateByName('new_order');
        if (!$template) {
            error_log('Template "new_order" not found in database.');
            throw new \Exception('Šablona new_order nebyla nalezena v databázi.');
        }

        $itemsSubtotal = 0;
        foreach ($orderItems as $item) {
            $itemsSubtotal += $item->total_price * $item->quantity;
        }

        $shippingInfo = $this->orderFacade->getShippingInfo($order->shipping);
        $shippingCost = $shippingInfo ? $shippingInfo['cost'] : 0.0;
        $vendorName = $this->orderFacade->getVendorNameByShippingOptionId($order->shipping);

        $paymentCost = $order->payment === 'DOBIRKA' ? 40.0 : 0.0;
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
            throw new \Exception('Chyba při renderování šablony new_order: ' . $e->getMessage());
        }

        $mail = new Message;
        $mail->setFrom('opnx3d@seznam.cz')
            ->addTo($template['recipient_email'] ?? 'opnx3d@seznam.cz')
            ->setSubject($subject)
            ->setHtmlBody($html);

        $this->mailer->send($mail);
    }
}