<?php

declare(strict_types=1);

namespace App\UI\Front\Profile;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use Nette\Security\User;
use App\Model\OrderFacade;

final class ProfilePresenter extends Nette\Application\UI\Presenter
{
    private Explorer $database;
    private User $user;
    private OrderFacade $orderFacade;

    public function __construct(Explorer $database, User $user, OrderFacade $orderFacade)
    {
        parent::__construct();
        $this->database = $database;
        $this->user = $user;
        $this->orderFacade = $orderFacade;
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
                'psc' => $values->psc,
            ]);

        $this->flashMessage('Profil byl úspěšně aktualizován.', 'success');
        $this->redirect('default');
    }

    public function renderHistory(): void
    {
        $userId = $this->user->getId();

        $orders = $this->orderFacade->getOrdersByUserId($userId);

        $orderData = [];
        foreach ($orders as $order) {
            $caseIds = $this->database->table('order_case')
                ->where('order_id', $order->id)
                ->fetchPairs(null, 'case_id');

            $cases = $this->database->table('cases')
                ->where('id', $caseIds)
                ->fetchAll();

            $orderData[] = [
                'order' => $order,
                'cases' => $cases,
            ];
        }

        $this->template->orders = $orderData;
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

        // Načti uživatele z DB
        $userRow = $this->database->table('users')->get($userId);
        if (!$userRow) {
            $this->error('Uživatel nebyl nalezen.');
        }

        // Ověření aktuálního hesla
        $passwords = $this->user->getAuthenticator();
        $identity = $this->user->getIdentity();

        // Pokud používáte Nette\Security\Passwords pro hashování:
        $passwords = new \Nette\Security\Passwords();

        if (!$passwords->verify($values->currentPassword, $userRow->password)) {
            $form->addError('Aktuální heslo je nesprávné.');
            return;
        }

        // Pokud je hash starší, rehashuj
        if ($passwords->needsRehash($userRow->password)) {
            $newHash = $passwords->hash($values->newPassword);
        } else {
            $newHash = $passwords->hash($values->newPassword);
        }

        // Ulož nové heslo do DB
        $this->database->table('users')
            ->where('id', $userId)
            ->update([
                'password' => $newHash,
            ]);

        $this->flashMessage('Heslo bylo úspěšně změněno.', 'success');
        $this->redirect('this');
    }
}
