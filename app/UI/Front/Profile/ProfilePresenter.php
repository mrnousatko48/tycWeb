<?php

declare(strict_types=1);

namespace App\UI\Front\Profile;

use App\UI\Front\BaseFrontPresenter;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

final class ProfilePresenter extends BaseFrontPresenter
{
    private User $user;


    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    protected function startup(): void
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            $this->flashMessage('Musíte být přihlášen.', 'warning');
            $this->redirect(':Front:Sign:in', ['backlink' => $this->storeRequest()]);
        }
    }

    public function renderDefault(): void
    {
        $userId = $this->user->getId();
        $userRow = $this->userFacade->getUserById($userId);

        if (!$userRow) {
            $this->error('Uživatel nebyl nalezen.');
        }

        $this->template->profileUser = $userRow;
    }

    protected function createComponentEditProfileForm(): Form
    {
        $form = new Form;

        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired();

        $form->addText('firstname', 'Jméno:')
            ->setRequired();

        $form->addText('lastname', 'Příjmení:')
            ->setRequired();

        $form->addEmail('email', 'Email:')
            ->setRequired();
        
        $form->addText('phone', 'Telefon:')
            ->setNullable();

        $form->addText('address', 'Adresa:')
            ->setNullable();

        $form->addText('city', 'Město:')
            ->setNullable();

        $form->addText('psc', 'PSČ:')
            ->setNullable()
            ->addRule($form::PATTERN, 'Zadejte platné PSČ (např. 12345 nebo 123 45)', '^\d{3}\s?\d{2}$');

        $form->addSubmit('save', 'Uložit změny');

        $form->onSuccess[] = [$this, 'editProfileFormSucceeded'];

        $userId = $this->user->getId();
        $userRow = $this->userFacade->getUserById($userId);
        if ($userRow) {
            $form->setDefaults($userRow->toArray());
        }

        return $form;
    }

    public function editProfileFormSucceeded(Form $form, \stdClass $values): void
    {
        $userId = $this->user->getId();

        $this->userFacade->updateUser($userId, \Nette\Utils\ArrayHash::from([
            'username' => $values->username,
            'firstname' => $values->firstname,
            'lastname' => $values->lastname,
            'email' => $values->email,
            'phone' => $values->phone,
            'address' => $values->address,
            'city' => $values->city,
            'psc' => $values->psc,
        ]));

        $this->flashMessage('Profil byl úspěšně aktualizován.', 'success');
        $this->redirect('default');
    }

    public function renderOrders(): void
    {
        $userId = $this->user->getId();
        $orders = $this->orderFacade->getOrdersByUserId($userId);
        $this->template->orders = $orders;
    }

    protected function createComponentChangePasswordForm(): Form
    {
        $form = new Form;

        $form->addPassword('currentPassword', 'Aktuální heslo:')
            ->setRequired('Zadejte aktuální heslo.');

        $form->addPassword('newPassword', 'Nové heslo:')
            ->setRequired('Zadejte nové heslo.')
            ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků.', 6);

        $form->addPassword('newPasswordConfirm', 'Potvrďte nové heslo:')
            ->setRequired('Potvrďte nové heslo.')
            ->addRule($form::EQUAL, 'Hesla se musí shodovat.', $form['newPassword']);

        $form->addSubmit('save', 'Změnit heslo');

        $form->onSuccess[] = [$this, 'changePasswordFormSucceeded'];

        return $form;
    }

    public function changePasswordFormSucceeded(Form $form, \stdClass $values): void
    {
        $userId = $this->user->getId();

        $userRow = $this->userFacade->getUserById($userId);
        if (!$userRow) {
            $this->error('Uživatel nebyl nalezen.');
        }

        if (!$this->userFacade->verifyPassword($userId, $values->currentPassword)) {
            $form->addError('Aktuální heslo je nesprávné.');
            return;
        }

        $this->userFacade->updatePassword($userId, $values->newPassword);

        $this->flashMessage('Heslo bylo úspěšně změněno.', 'success');
        $this->redirect('this');
    }
}