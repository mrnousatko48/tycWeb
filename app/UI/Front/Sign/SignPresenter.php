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
        $form->setTranslator($this->translator); // Enable translation for form

        $form->addText('username', 'form.username')
            ->setRequired('form.username.required');

        $form->addPassword('password', 'form.password')
            ->setRequired('form.password.required');

        $form->addSubmit('send', 'form.signin.submit');

        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            try {
                $this->getUser()->login($data->username, $data->password);
                $this->restoreRequest($this->backlink);
                $this->flashMessage('sign.in.success', 'success');
                $this->redirect('Home:default');
            } catch (Nette\Security\AuthenticationException) {
                $this->flashMessage('sign.in.invalid_credentials', 'danger');
            }
        };

        return $form;
    }

    protected function createComponentSignUpForm(): Form
    {
        $form = $this->formFactory->create();
        $form->setTranslator($this->translator); // Enable translation for form

        $form->addText('username', 'form.username')
            ->setRequired('form.username.required');

        $form->addText('firstname', 'form.firstname');
        
        $form->addText('lastname', 'form.lastname');

        $form->addEmail('email', 'form.email')
            ->setRequired('form.email.required');

        $form->addPassword('password', 'form.password')
            ->setRequired('form.password.required')
            ->addRule($form::MinLength, 'form.password.min_length', $this->userFacade::PasswordMinLength);

        $form->addPassword('confirmPassword', 'form.confirm_password')
            ->setRequired('form.confirm_password.required')
            ->addRule($form::EQUAL, 'form.passwords_not_matching', $form['password']);

        $form->addReCaptcha('recaptcha', 'form.recaptcha', true, 'form.recaptcha.error');

        $form->addSubmit('send', 'form.signup.submit');

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

                $this->flashMessage('sign.up.success', 'success');
                $this->redirect('Sign:in');

            } catch (DuplicateNameException $e) {
                $message = $e->getMessage();

                /** @var \Nette\Forms\Controls\BaseControl $email */
                $email = $form['email'];
                /** @var \Nette\Forms\Controls\BaseControl $username */
                $username = $form['username'];

                if (str_contains($message, 'Uživatelské jméno')) {
                    $username->addError('sign.up.username_exists');
                } elseif (str_contains($message, 'Email')) {
                    $email->addError('sign.up.email_exists');
                } else {
                    $form->addError('sign.up.failed');
                }

            } catch (\Exception $e) {
                if ($e instanceof \Nette\Application\AbortException) {
                    throw $e;
                }

                $this->flashMessage('sign.up.failed', 'danger');
            }
        };

        return $form;
    }

    public function actionOut(): void
    {
        $this->getUser()->logout();
        $this->getHttpResponse()->deleteCookie(session_name());
        $this->flashMessage('sign.out.success', 'success');
        $this->redirect('Home:default');
    }

    protected function createComponentForgotPasswordForm(): Form
    {
        $form = $this->formFactory->create();
        $form->setTranslator($this->translator); // Enable translation for form
        $form->elementPrototype->class[] = 'custom-form';

        $form->addEmail('email', 'form.email')
            ->setRequired('form.email.required');

        $form->addSubmit('send', 'form.forgot_password.submit');

        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            $user = $this->userFacade->findByEmail($data->email);
            if (!$user) {
                $this->flashMessage('forgot_password.email_not_found', 'danger');
                return;
            }

            $resetCode = $this->generateRandomCode();
            $this->userFacade->saveResetCode($user->id, $resetCode);
            $this->mailSender->sendPasswordResetEmail($data->email, $resetCode);

            $this->flashMessage('forgot_password.code_sent', 'success');
            $this->redirect('Sign:resetPassword');
        };

        return $form;
    }

    protected function createComponentResetPasswordForm(): Form
    {
        $form = $this->formFactory->create();
        $form->setTranslator($this->translator); // Enable translation for form
        $form->elementPrototype->class[] = 'custom-form';

        $form->addText('resetCode', 'form.reset_code')
            ->setRequired('form.reset_code.required')
            ->addRule(Form::MAX_LENGTH, 'form.reset_code.max_length', 6);

        $form->addPassword('newPassword', 'form.new_password')
            ->setRequired('form.new_password.required')
            ->addRule(Form::MIN_LENGTH, 'form.new_password.min_length', 6);

        $form->addPassword('confirmPassword', 'form.confirm_password')
            ->setRequired('form.confirm_password.required');

        $form->addSubmit('send', 'form.reset_password.submit');

        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            $user = $this->userFacade->findByResetCode($data->resetCode);
            if (!$user) {
                $form->addError('reset_password.invalid_code');
                return;
            }

            if ($data->newPassword !== $data->confirmPassword) {
                $form->addError('form.passwords_not_matching');
                return;
            }

            $this->userFacade->updatePassword($user->id, $data->newPassword);
            $this->flashMessage('reset_password.success', 'success');
            $this->redirect('Sign:in');
        };

        return $form;
    }

    private function generateRandomCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}