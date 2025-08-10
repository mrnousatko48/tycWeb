<?php
declare(strict_types=1);

namespace App\UI\Front\Sign;

use App\UI\Front\BaseFrontPresenter;
use Nette;
use App\UI\Accessory\FormFactory;
use Nette\Application\UI\Form;
use App\Model\DuplicateNameException;
use App\MailSender\MailSender;

final class SignPresenter extends BaseFrontPresenter
{
    public string $backlink = '';

    public function __construct(
        private FormFactory $formFactory,
        private MailSender $mailSender,
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

        $lang = $this->getParameter('lang') ?? $this->getSession('order')->lang ?? 'cs';


        $form->addText('username', 'form.username')
            ->setRequired($lang === 'en' ? 'Username is required.' : 'Uživatelské jméno je povinné.');

        $form->addText('firstname', 'form.firstname');
        
        $form->addText('lastname', 'form.lastname');

        $form->addEmail('email', 'form.email')
            ->setRequired($lang === 'en' ? 'Email is required.' : 'Email je povinný.');

        $form->addPassword('password', 'form.password')
            ->setRequired($lang === 'en' ? 'Password is required.' : 'Heslo je povinné.')
            ->addRule($form::MinLength, $lang === 'en' ? 'Password must be at least %d characters long.' : 'Heslo musí mít alespoň %d znaků.', $this->userFacade::PasswordMinLength);

        $form->addPassword('confirmPassword', 'form.confirm_password')
            ->setRequired($lang === 'en' ? 'Confirm password is required.' : 'Potvrzení hesla je povinné.')
            ->addRule($form::EQUAL, $lang === 'en' ? 'Passwords do not match.' : 'Hesla se neshodují.', $form['password']);

        $form->addReCaptcha('recaptcha', 'form.recaptcha', true, $lang === 'en' ? 'Please verify you are not a robot.' : 'Prosím, ověřte, že nejste robot.');

        $form->addHidden('lang', $lang);

        $form->addSubmit('send', $lang === 'en' ? 'Sign Up' : 'Registrovat se');

        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            $session = $this->getSession('order');
             $lang = $data->lang ?? $session->lang ?? 'cs';
            try {
                $this->userFacade->add(
                    username: $data->username,
                    firstname: $data->firstname,
                    lastname: $data->lastname,
                    email: $data->email,
                    password: $data->password,
                    role: 'UZIVATEL'
                );

                $this->mailSender->sendRegistrationEmail($data->email, $data->username, $lang);
                 $this->mailSender->sendNewUserEmail($data->email, $data->username, $lang);

                $this->flashMessage($lang === 'en' ? 'Registration successful. Please sign in.' : 'Registrace proběhla úspěšně. Přihlaste se.', 'success');
                $this->redirect('Sign:in', ['lang' => $lang]);

            } catch (DuplicateNameException $e) {
                $message = $e->getMessage();

                /** @var \Nette\Forms\Controls\BaseControl $email */
                $email = $form['email'];
                /** @var \Nette\Forms\Controls\BaseControl $username */
                $username = $form['username'];

                if (str_contains($message, 'Uživatelské jméno') || str_contains($message, 'Username')) {
                    $username->addError($lang === 'en' ? 'Username already exists.' : 'Uživatelské jméno již existuje.');
                } elseif (str_contains($message, 'Email')) {
                    $email->addError($lang === 'en' ? 'Email already exists.' : 'Email již existuje.');
                } else {
                    $form->addError($lang === 'en' ? 'Registration failed.' : 'Registrace selhala.');
                }

            } catch (\Exception $e) {
                if ($e instanceof \Nette\Application\AbortException) {
                    throw $e;
                }

                $this->flashMessage($lang === 'en' ? 'Registration failed.' : 'Registrace selhala.', 'danger');
            }
        };

        return $form;
    }

    public function actionOut(): void
    {
        $lang = $this->getSession('order')->lang ?? 'cs';
        $this->getUser()->logout();
        $this->getHttpResponse()->deleteCookie(session_name());
        $this->flashMessage($lang === 'en' ? 'Successfully signed out.' : 'Úspěšně odhlášeno.', 'success');
        $this->redirect('Home:default', ['lang' => $lang]);
    }

    protected function createComponentForgotPasswordForm(): Form
    {
        $form = $this->formFactory->create();
        $form->setTranslator($this->translator); // Enable translation for form
        $form->elementPrototype->class[] = 'custom-form';

        $lang = $this->getHttpRequest()->getQuery('lang') ?? $this->getSession('order')->lang ?? 'cs';

        $form->addEmail('email', 'form.email')
            ->setRequired($lang === 'en' ? 'Email is required.' : 'Email je povinný.');

        $form->addHidden('lang', $lang);

        $form->addSubmit('send', $lang === 'en' ? 'Send Reset Code' : 'Odeslat kód pro obnovení');

        $form->onSuccess[] = function (Form $form, \stdClass $data) use ($lang): void {
            $user = $this->userFacade->findByEmail($data->email);
            if (!$user) {
                $this->flashMessage($lang === 'en' ? 'Email not found.' : 'Email nenalezen.', 'danger');
                return;
            }

            $resetCode = $this->generateRandomCode();
            $this->userFacade->saveResetCode($user->id, $resetCode);
            $this->mailSender->sendPasswordResetEmail($data->email, $resetCode, $lang);

            $this->flashMessage($lang === 'en' ? 'Reset code sent to your email.' : 'Kód pro obnovení byl odeslán na váš email.', 'success');
            $this->redirect('Sign:resetPassword', ['lang' => $lang]);
        };

        return $form;
    }

    protected function createComponentResetPasswordForm(): Form
    {
        $form = $this->formFactory->create();
        $form->setTranslator($this->translator); // Enable translation for form
        $form->elementPrototype->class[] = 'custom-form';

        $lang = $this->getHttpRequest()->getQuery('lang') ?? $this->getSession('order')->lang ?? 'cs';

        $form->addText('resetCode', 'form.reset_code')
            ->setRequired($lang === 'en' ? 'Reset code is required.' : 'Kód pro obnovení je povinný.')
            ->addRule(Form::MAX_LENGTH, $lang === 'en' ? 'Reset code must be %d characters long.' : 'Kód pro obnovení musí mít %d znaků.', 6);

        $form->addPassword('newPassword', 'form.new_password')
            ->setRequired($lang === 'en' ? 'New password is required.' : 'Nové heslo je povinné.')
            ->addRule(Form::MIN_LENGTH, $lang === 'en' ? 'Password must be at least %d characters long.' : 'Heslo musí mít alespoň %d znaků.', 6);

        $form->addPassword('confirmPassword', 'form.confirm_password')
            ->setRequired($lang === 'en' ? 'Confirm password is required.' : 'Potvrzení hesla je povinné.');

        $form->addHidden('lang', $lang);

        $form->addSubmit('send', $lang === 'en' ? 'Reset Password' : 'Obnovit heslo');

        $form->onSuccess[] = function (Form $form, \stdClass $data) use ($lang): void {
            $user = $this->userFacade->findByResetCode($data->resetCode);
            if (!$user) {
                $form->addError($lang === 'en' ? 'Invalid reset code.' : 'Neplatný kód pro obnovení.');
                return;
            }

            if ($data->newPassword !== $data->confirmPassword) {
                $form->addError($lang === 'en' ? 'Passwords do not match.' : 'Hesla se neshodují.');
                return;
            }

            $this->userFacade->updatePassword($user->id, $data->newPassword);
            $this->flashMessage($lang === 'en' ? 'Password successfully reset.' : 'Heslo bylo úspěšně obnoveno.', 'success');
            $this->redirect('Sign:in', ['lang' => $lang]);
        };

        return $form;
    }

    private function generateRandomCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}