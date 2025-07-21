<?php
declare(strict_types=1);

namespace App\UI\Front\Sign;

use App\UI\Front\BaseFrontPresenter;
use Nette;
use App\UI\Accessory\FormFactory;
use Nette\Application\UI\Form;
use App\Model\DuplicateNameException;
use App\MailSender\MailSender;
use App\Model\EmailFacade;

final class SignPresenter extends BaseFrontPresenter
{
    public string $backlink = '';

    public function __construct(
        private FormFactory $formFactory,
        private MailSender $mailSender,
        private EmailFacade $emailFacade,
    ) {
    }

    protected function createComponentSignInForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Zadejte své uživatelské jméno');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadejte své heslo');

        $form->addSubmit('send', 'Přihlásit se');

        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            try {
                $this->getUser()->login($data->username, $data->password);
                $this->restoreRequest($this->backlink);
                $this->flashMessage('Přihlášení bylo úspěšné.', 'success');
                $this->redirect('Home:default');
            } catch (Nette\Security\AuthenticationException) {
                $this->flashMessage('Neplatné přihlašovací údaje.', 'danger');
            }
        };

        return $form;
    }

    protected function createComponentSignUpForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addText('username', '*Uživatelské jméno:')
            ->setRequired('Zadejte uživatelské jméno');

        $form->addText('firstname', 'Křestní jméno:');
        
        $form->addText('lastname', 'Příjmení:');

        $form->addEmail('email', '*Email:')
            ->setRequired('Zadejte e-mailovou adresu');

        $form->addPassword('password', '*Heslo:')
            ->setRequired('Zadejte heslo')
            ->addRule($form::MinLength, 'Heslo musí mít alespoň %d znaků.', $this->userFacade::PasswordMinLength);

        $form->addPassword('confirmPassword', '*Potvrzení hesla:')
            ->setRequired('Zadejte heslo znovu')
            ->addRule($form::EQUAL, 'Hesla se neshodují', $form['password']);

        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            try {
                $this->userFacade->add(
                    username: $data->username,
                    firstname: $data->firstname,
                    lastname: $data->lastname,
                    email: $data->email,
                    password: $data->password,
                    role: 'UZIVATEL'
                );
                
                $this->mailSender->sendRegistrationEmail($data->email, $data->username);
                $this->mailSender->sendNewUserEmail($data->email, $data->username);

                $this->flashMessage('Registrace byla úspěšná. Nyní se můžete přihlásit.', 'success');
                $this->redirect('Sign:in');
            } catch (DuplicateNameException $e) {
                $message = $e->getMessage();
                if (str_contains($message, 'Uživatelské jméno')) {
                    $form['username']->addError('Uživatelské jméno již existuje.');
                } elseif (str_contains($message, 'Email')) {
                    $form['email']->addError('Email již existuje.');
                } else {
                    $form->addError('Registrace se nezdařila. Zkuste to prosím znovu.');
                }
            } catch (\Exception $e) {
                if ($e instanceof \Nette\Application\AbortException) {
                    throw $e;
                }
                $this->flashMessage('Registrace se nepodařila. Zkuste to prosím znovu.', 'danger');
            }
        };

        return $form;
    }

    public function actionOut(): void
    {
        $this->getUser()->logout();
        $this->getHttpResponse()->deleteCookie(session_name());
        $this->flashMessage('Byli jste odhlášeni.', 'success');
        $this->redirect('Home:default');
    }

    protected function createComponentForgotPasswordForm(): Form
    {
        $form = $this->formFactory->create();
        $form->elementPrototype->class[] = 'custom-form';
        $form->addEmail('email', 'Váš email:')
            ->setRequired('Zadejte váš email');

        $form->addSubmit('send', 'Odeslat resetovací kód');

        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            $user = $this->userFacade->findByEmail($data->email);
            if (!$user) {
                $this->flashMessage('Tento email nebyl nalezen', 'danger');
                return;
            }

            $resetCode = $this->generateRandomCode();
            $this->userFacade->saveResetCode($user->id, $resetCode);
            $this->mailSender->sendPasswordResetEmail($data->email, $resetCode);

            $this->flashMessage('Na váš email byl odeslán resetovací kód', 'success');
            $this->redirect('Sign:resetPassword');
        };

        return $form;
    }

    protected function createComponentResetPasswordForm(): Form
    {
        $form = $this->formFactory->create();
        $form->elementPrototype->class[] = 'custom-form';

        $form->addText('resetCode', 'Resetovací kód:')
            ->setRequired('Zadejte resetovací kód')
            ->addRule(Form::MAX_LENGTH, 'Resetovací kód může mít maximálně %d znaků', 6);

        $form->addPassword('newPassword', 'Nové heslo:')
            ->setRequired('Zadejte nové heslo')
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň 6 znaků', 6);

        $form->addPassword('confirmPassword', 'Potvrďte nové heslo:')
            ->setRequired('Potvrďte nové heslo');

        $form->addSubmit('send', 'Obnovit heslo');

        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            $user = $this->userFacade->findByResetCode($data->resetCode);
            if (!$user) {
                $form->addError('Neplatný resetovací kód');
                return;
            }

            if ($data->newPassword !== $data->confirmPassword) {
                $form->addError('Hesla se neshodují');
                return;
            }

            $this->userFacade->updatePassword($user->id, $data->newPassword);
            $this->flashMessage('Heslo bylo úspěšně změněno', 'success');
            $this->redirect('Sign:in');
        };

        return $form;
    }

    private function generateRandomCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}