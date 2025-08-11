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

        // Determine current language (query -> session -> fallback)
        $lang = $this->getHttpRequest()->getQuery('lang')
            ?? $this->getParameter('lang')
            ?? $this->getSession('order')->lang
            ?? 'cs';

        // Preserve language across the POST
        $form->addHidden('lang', $lang);

        $form->addText('username', 'form.username')
            ->setRequired('form.username.required');

        $form->addPassword('password', 'form.password')
            ->setRequired('form.password.required');

        $form->addSubmit('send', 'form.signin.submit');

        $form->onSuccess[] = function (Form $form, \stdClass $data) use ($lang): void {
            // prefer posted lang value (if present), otherwise fallback
            $postedLang = $data->lang ?? $lang;

            try {
                $this->getUser()->login($data->username, $data->password);
                $this->restoreRequest($this->backlink);
                $this->flashMessage('sign.in.success', 'success');

                // Redirect to home while preserving language
                $this->redirect('Home:default', ['lang' => $postedLang]);
            } catch (Nette\Security\AuthenticationException) {
                // Keep the correct language and show the error flash
                $this->flashMessage('sign.in.invalid_credentials', 'danger');

                // Redirect back to the same page with lang preserved.
                // This makes sure the presenter chooses the right translator/locale.
                $this->redirect('this', ['lang' => $postedLang]);
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

        $form->addHidden('lang', $lang);

        $form->addSubmit('send', 'form.signup.submit');

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

                $this->flashMessage('sign.up.success', 'success');
                $this->redirect('Sign:in', ['lang' => $lang]);

            } catch (DuplicateNameException $e) {
                $message = $e->getMessage();

                /** @var \Nette\Forms\Controls\BaseControl $email */
                $email = $form['email'];
                /** @var \Nette\Forms\Controls\BaseControl $username */
                $username = $form['username'];

                if (str_contains($message, 'Uživatelské jméno') || str_contains($message, 'Username')) {
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
        $lang = $this->getSession('order')->lang ?? 'cs';
        $this->getUser()->logout();
        $this->getHttpResponse()->deleteCookie(session_name());
        $this->flashMessage('sign.out.success', 'success');
        $this->redirect('Home:default', ['lang' => $lang]);
    }

protected function createComponentForgotPasswordForm(): Form
{
    $form = $this->formFactory->create();
    $form->setTranslator($this->translator);
    $form->elementPrototype->class[] = 'custom-form';

    // Prefer POST -> query -> session -> fallback
    $post = $this->getHttpRequest()->getPost();
    $lang = $post['lang']
        ?? $this->getHttpRequest()->getQuery('lang')
        ?? $this->getSession('order')->lang
        ?? 'cs';

    $form->addEmail('email', 'form.email')
        ->setRequired('form.email.required');

    // preserve lang in POST
    $form->addHidden('lang', $lang);


    $form->getElementPrototype()->action = $this->link('this', ['lang' => $lang]);

    $form->addSubmit('send', 'form.forgot_password.submit');

    $form->onSuccess[] = function (Form $form, \stdClass $data): void {
        $lang = $data->lang ?? 'cs';

        $user = $this->userFacade->findByEmail($data->email);
        if (!$user) {
            // show flash or form error — either is fine; we keep language thanks to action
            $this->flashMessage('forgot_password.email_not_found', 'danger');
            return;
        }

        $resetCode = $this->generateRandomCode();
        $this->userFacade->saveResetCode($user->id, $resetCode);
        $this->mailSender->sendPasswordResetEmail($data->email, $resetCode, $lang);

        $this->flashMessage('forgot_password.code_sent', 'success');
        $this->redirect('Sign:resetPassword', ['lang' => $lang]);
    };

    return $form;
}


   protected function createComponentResetPasswordForm(): Form
{
    $form = $this->formFactory->create();
    $form->setTranslator($this->translator);
    $form->elementPrototype->class[] = 'custom-form';

    $post = $this->getHttpRequest()->getPost();
    $lang = $post['lang']
        ?? $this->getHttpRequest()->getQuery('lang')
        ?? $this->getSession('order')->lang
        ?? 'cs';

    $form->addText('resetCode', 'form.reset_code')
        ->setRequired('form.reset_code.required')
        ->addRule(Form::MAX_LENGTH, 'form.reset_code.max_length', 6);

    $form->addPassword('newPassword', 'form.new_password')
        ->setRequired('form.new_password.required')
        ->addRule(Form::MIN_LENGTH, 'form.new_password.min_length', 6);

    $form->addPassword('confirmPassword', 'form.confirm_password')
        ->setRequired('form.confirm_password.required')
        ->addRule($form::EQUAL, 'form.passwords_not_matching', $form['newPassword']);

    $form->addHidden('lang', $lang);
    $form->getElementPrototype()->action = $this->link('this', ['lang' => $lang]);


    $form->addSubmit('send', 'form.reset_password.submit');

    $form->onSuccess[] = function (Form $form, \stdClass $data): void {
        $lang = $data->lang ?? 'cs';

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
        $this->redirect('Sign:in', ['lang' => $lang]);
    };

    return $form;
}


    private function generateRandomCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}