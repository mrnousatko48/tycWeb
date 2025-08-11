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
            $this->error($this->translator->translate('profile.user_not_found'));
        }

        $this->template->profileUser = $userRow;
    }

    protected function createComponentEditProfileForm(): Form
    {
        $form = new Form;
        $form->setTranslator($this->translator); // Enable translation for form labels and errors

        $form->addText('username', 'form.username')
            ->setRequired('form.username.required');

        $form->addText('firstname', 'form.firstname')
            ->setRequired('form.firstname.required');

        $form->addText('lastname', 'form.lastname')
            ->setRequired('form.lastname.required');

        $form->addEmail('email', 'form.email')
            ->setRequired('form.email.required');

        $form->addText('phone', 'form.phone')
            ->setNullable();

        $form->addText('address', 'form.address')
            ->setNullable();

        $form->addText('city', 'form.city')
            ->setNullable();

        $form->addText('psc', 'form.psc')
            ->setNullable()
            ->addRule($form::PATTERN, 'form.psc.pattern', '^\d{3}\s?\d{2}$');

        $form->addSubmit('save', 'form.save');

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

        $this->flashMessage($this->translator->translate('flash.success.profile_updated'), 'success');
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
        $form->setTranslator($this->translator); // Enable translation for form labels and errors

        $form->addPassword('currentPassword', 'form.current_password')
            ->setRequired('form.current_password.required');

        $form->addPassword('newPassword', 'form.new_password')
            ->setRequired('form.new_password.required')
            ->addRule($form::MIN_LENGTH, 'form.new_password.min_length', 6);

        $form->addPassword('newPasswordConfirm', 'form.confirm_password')
            ->setRequired('form.confirm_password.required')
            ->addRule($form::EQUAL, 'form.passwords_not_matching', $form['newPassword']);

        $form->addSubmit('save', 'form.change_password');

        $form->onSuccess[] = [$this, 'changePasswordFormSucceeded'];

        return $form;
    }

     public function changePasswordFormSucceeded(Form $form, \stdClass $values): void
    {
        $userId = $this->user->getId();

        $userRow = $this->userFacade->getUserById($userId);
        if (!$userRow) {
            $this->error($this->translator->translate('profile.user_not_found'));
        }

        if (!$this->userFacade->verifyPassword($userId, $values->currentPassword)) {
            $form->addError($this->translator->translate('form.current_password.invalid'));
            return;
        }

        $this->userFacade->updatePassword($userId, $values->newPassword);

        $this->flashMessage($this->translator->translate('flash.success.reset_password.success'), 'success');
        $this->redirect('this', ['lang' => $this->lang]);
    }
}