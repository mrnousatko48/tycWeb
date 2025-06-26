<?php

declare(strict_types=1);

namespace App\UI\Front\Profile;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use Nette\Security\User;

final class ProfilePresenter extends Nette\Application\UI\Presenter
{
    private Explorer $database;
    private User $user;

    public function __construct(Explorer $database, User $user)
    {
        parent::__construct();
        $this->database = $database;
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
        $userRow = $this->database->table('users')->get($userId);

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

        $form->addText('address', 'Adresa:')
            ->setNullable();

        $form->addText('city', 'Město:')
            ->setNullable();

        $form->addText('psc', 'PSČ:') // Přidáno pole PSČ
            ->setNullable()
            ->addRule($form::PATTERN, 'Zadejte platné PSČ (např. 12345 nebo 123 45)', '^\d{3}\s?\d{2}$');

        $form->addSubmit('save', 'Uložit změny');

        $form->onSuccess[] = [$this, 'editProfileFormSucceeded'];

        $userId = $this->user->getId();
        $userRow = $this->database->table('users')->get($userId);
        if ($userRow) {
            $form->setDefaults($userRow->toArray());
        }

        return $form;
    }

    public function editProfileFormSucceeded(Form $form, \stdClass $values): void
    {
        $userId = $this->user->getId();

        $this->database->table('users')
            ->where('id', $userId)
            ->update([
                'username' => $values->username,
                'firstname' => $values->firstname,
                'lastname' => $values->lastname,
                'email' => $values->email,
                'address' => $values->address,
                'city' => $values->city,
                'psc' => $values->psc, // Uložení PSČ
            ]);

        $this->flashMessage('Profil byl úspěšně aktualizován.', 'success');
        $this->redirect('default');
    }
}
