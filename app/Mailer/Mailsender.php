<?php
declare(strict_types=1);

namespace App\MailSender;

use Nette\Mail\Message;
use Nette\Mail\Mailer;
use Latte\Engine;
use Mpdf\Mpdf;
use App\Model\OrderFacade;
use Nette\Database\Explorer;

class MailSender
{
    public function __construct(
        private Mailer $mailer,
        private OrderFacade $orderFacade,
        private Explorer $database  // přidáno pro načtení bank. účtu
    ){
    }

    public function createRegistrationEmail(string $email, string $username): Message
    {
        $latte = new Engine();
        $mail = new Message;

        $params = [
            'email' => $email,
            'username' => $username,
        ];

        $html = $latte->renderToString(__DIR__ . '/registration.latte', $params);

        $mail->setFrom('okurkyvmalinovce@seznam.cz')
            ->addTo($email)
            ->setSubject('Vítejte! Registrace byla úspěšná')
            ->setHtmlBody($html);

        return $mail;
    }

    public function createNewUserEmail(string $email, string $username): Message
    {
        $latte = new Engine();
        $mail = new Message;

        $params = [
            'email' => $email,
            'username' => $username,
        ];

        $html = $latte->renderToString(__DIR__ . '/newUser.latte', $params);

        $mail->setFrom('okurkyvmalinovce@seznam.cz')
            ->addTo('okurkyvmalinovce@seznam.cz')
            ->setSubject('Nová registrace uživatele')
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

        $itemsSubtotal = 0;
        foreach ($orderItems as $item) {
            $itemsSubtotal += $item->total_price * $item->quantity;
        }

        $shippingInfo = $this->orderFacade->getShippingInfo($order->shipping);
        $shippingCost = $shippingInfo ? $shippingInfo['cost'] : 0.0;

        $paymentCost = $order->payment === 'DOBIRKA' ? 40.0 : 0.0;
        $total = $itemsSubtotal + $shippingCost + $paymentCost;

        // Načtení bankovního účtu z tabulky contact_info
        $contactInfo = $this->database->table('contact_info')->fetch();
        $bankAccount = $contactInfo ? $contactInfo->bank_account : 'není zadán';

        $htmlInvoice = $latte->renderToString(__DIR__ . '/invoice.latte', [
            'order' => $order,
            'items' => $orderItems,
            'recipient' => $recipientName,
            'itemsSubtotal' => $itemsSubtotal,
            'shippingCost' => $shippingCost,
            'paymentCost' => $paymentCost,
            'total' => $total,
            'bankAccount' => $bankAccount,  // přidáno
        ]);

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($htmlInvoice);
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        $htmlBody = $latte->renderToString(__DIR__ . '/invoiceEmail.latte', [
            'recipient' => $recipientName,
            'orderId' => $order->id,
        ]);

        $mail = new Message;
        $mail->setFrom('okurkyvmalinovce@seznam.cz')
            ->addTo($recipientEmail)
            ->setSubject('Faktura za vaši objednávku č. ' . $order->id)
            ->setHtmlBody($htmlBody)
            ->addAttachment("faktura-{$order->id}.pdf", $pdfContent, 'application/pdf');

        $this->mailer->send($mail);
    }

    public function sendPasswordResetEmail(string $email, string $resetCode): void
    {
        $latte = new Engine();
        $mail = new Message;

        $params = [
            'email' => $email,
            'resetCode' => $resetCode,
        ];

        $html = $latte->renderToString(__DIR__ . '/passreset.latte', $params);

        $mail->setFrom('okurkyvmalinovce@seznam.cz')
            ->addTo($email)
            ->setSubject('Reset hesla')
            ->setHtmlBody($html);

        $this->mailer->send($mail);
    }

    public function sendPaymentConfirmationEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order): void
    {
        $latte = new Engine();
        $mail = new Message;

        $params = [
            'recipient' => $recipientName,
            'orderId' => $order->id,
            'variableSymbol' => $order->variable_symbol,
        ];

        $html = $latte->renderToString(__DIR__ . '/paymentConfirmation.latte', $params);

        $mail->setFrom('okurkyvmalinovce@seznam.cz')
            ->addTo($recipientEmail)
            ->setSubject('Potvrzení přijetí platby za objednávku č. ' . $order->id)
            ->setHtmlBody($html);

        $this->mailer->send($mail);
    }

    public function sendShippedEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order): void
    {
        $latte = new Engine();
        $mail = new Message;

        $params = [
            'recipient' => $recipientName,
            'orderId' => $order->id,
        ];

        $html = $latte->renderToString(__DIR__ . '/shipped.latte', $params);

        $mail->setFrom('okurkyvmalinovce@seznam.cz')
            ->addTo($recipientEmail)
            ->setSubject('Vaše objednávka č. ' . $order->id . ' byla odeslána')
            ->setHtmlBody($html);

        $this->mailer->send($mail);
    }

    public function sendReadyForPickupEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order): void
    {
        $latte = new Engine();
        $mail = new Message;

        $params = [
            'recipient' => $recipientName,
            'orderId' => $order->id,
            'deliveryPoint' => $order->delivery_point ?? 'Není uvedeno dodací místo',
        ];

        $html = $latte->renderToString(__DIR__ . '/readyForPickup.latte', $params);

        $mail->setFrom('okurkyvmalinovce@seznam.cz')
            ->addTo($recipientEmail)
            ->setSubject('Vaše objednávka č. ' . $order->id . ' je připravena k vyzvednutí')
            ->setHtmlBody($html);

        $this->mailer->send($mail);
    }

    public function sendPickedUpEmail(string $recipientEmail, string $recipientName, \Nette\Database\Table\ActiveRow $order): void
    {
        $latte = new Engine();
        $mail = new Message;

        $params = [
            'recipient' => $recipientName,
            'orderId' => $order->id,
        ];

        $html = $latte->renderToString(__DIR__ . '/pickedUp.latte', $params);

        $mail->setFrom('okurkyvmalinovce@seznam.cz')
            ->addTo($recipientEmail)
            ->setSubject('Vaše objednávka č. ' . $order->id . ' byla vyzvednuta')
            ->setHtmlBody($html);

        $this->mailer->send($mail);
    }

    public function sendNewOrderEmail(string $recipientName, \Nette\Database\Table\ActiveRow $order, array $orderItems): void
    {
        $latte = new Engine();
    
        $itemsSubtotal = 0;
        foreach ($orderItems as $item) {
            $itemsSubtotal += $item->total_price * $item->quantity;
        }
    
        $shippingInfo = $this->orderFacade->getShippingInfo($order->shipping);
        $shippingCost = $shippingInfo ? $shippingInfo['cost'] : 0.0;
        
        $paymentCost = $order->payment === 'DOBIRKA' ? 40.0 : 0.0;
        $total = $itemsSubtotal + $shippingCost + $paymentCost;
    
        // Načtení bankovního účtu z tabulky contact_info
        $contactInfo = $this->database->table('contact_info')->fetch();
        $bankAccount = $contactInfo ? $contactInfo->bank_account : 'není zadán';
    
        $html = $latte->renderToString(__DIR__ . '/newOrder.latte', [
            'order' => $order,
            'items' => $orderItems,
            'recipient' => $recipientName,
            'itemsSubtotal' => $itemsSubtotal,
            'shippingCost' => $shippingCost,
            'paymentCost' => $paymentCost,
            'total' => $total,
            'bankAccount' => $bankAccount, // přidáno
        ]);
    
        $mail = new Message;
        $mail->setFrom('okurkyvmalinovce@seznam.cz')
            ->addTo('okurkyvmalinovce@seznam.cz') // Admin email
            ->setSubject('Nová objednávka č. ' . $order->id)
            ->setHtmlBody($html);
    
        $this->mailer->send($mail);
    }
    
}