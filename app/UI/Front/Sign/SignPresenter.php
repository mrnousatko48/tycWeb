<?php

declare(strict_types=1);

namespace App\UI\Front\Sign;

use Nette;
use App\Model\UserFacade;
use App\UI\Accessory\FormFactory;
use Nette\Application\UI\Form;
use App\Model\DuplicateNameException;
use App\MailSender\MailSender;

final class SignPresenter extends Nette\Application\UI\Presenter
{
	public string $backlink = '';

	public function __construct(
		private UserFacade $userFacade,
		private FormFactory $formFactory,
		private MailSender $mailSender,
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
}

