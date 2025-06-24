<?php

namespace App\MailSender;

use Nette\Mail\Message;
use Nette\Mail\Mailer;
use Latte\Engine;

class MailSender
{
    public function __construct(
        private Mailer $mailer
    ) {
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
    
}
