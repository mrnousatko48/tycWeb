<?php

declare(strict_types=1);

namespace App\UI\Admin\User;

use App\Model\UserFacade;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;

final class UserPresenter extends Presenter
{
    public function __construct(
        private UserFacade $userFacade
    ) {
    }

    public function renderDefault(): void
    {
        // Optional: add breadcrumbs, title, etc.
    }

    protected function createComponentUsersGrid(): DataGrid
    {
        $grid = new DataGrid();

        $grid->setPrimaryKey('id');
        $grid->setDataSource($this->userFacade->getUsers());

        $grid->addColumnText('username', 'Uživatelské jméno')->setSortable()->setFilterText();
        $grid->addColumnText('firstname', 'Jméno')->setSortable()->setFilterText();
        $grid->addColumnText('lastname', 'Příjmení')->setSortable()->setFilterText();
        $grid->addColumnText('email', 'Email')->setSortable()->setFilterText();
        $grid->addColumnText('address', 'Adresa')->setFilterText();
        $grid->addColumnText('city', 'Město')->setFilterText();
        $grid->addColumnText('role', 'Role')->setFilterMultiSelect([
            'ADMIN' => 'Admin',
            'UZIVATEL' => 'Uživatel',
        ]);
        $grid->addColumnDateTime('created_at', 'Registrován dne')->setFormat('j. n. Y H:i');
    
    // Make entire row clickable
    $grid->setRowCallback(function ($item, $tr) {
        $tr->addAttributes([
            'class' => 'clickable-row',
            'data-href' => $this->link('edit', ['id' => $item->id]),
            'style' => 'cursor: pointer;',
        ]);
        return $tr;
    });
    

        return $grid;
    }

    public function actionEdit(int $id): void
    {
        $user = $this->userFacade->getUserById($id);
        if (!$user) {
            $this->error('Uživatel nenalezen.');
        }

        $this['editUserForm']->setDefaults($user->toArray());
    }

    public function renderEdit(int $id): void
    {
        $this->template->editedUser = $this->userFacade->getUserById($id);
    }

    protected function createComponentEditUserForm(): \Nette\Application\UI\Form
    {
        $form = new \Nette\Application\UI\Form;

        $form->addText('username', 'Uživatelské jméno')->setRequired();
        $form->addText('firstname', 'Jméno');
        $form->addText('lastname', 'Příjmení');
        $form->addEmail('email', 'Email')->setRequired();
        $form->addText('address', 'Adresa');
        $form->addText('city', 'Město');
        $form->addSelect('role', 'Role', [
            'UZIVATEL' => 'Uživatel',
            'ADMIN' => 'Admin',
        ])->setRequired();

        $form->addSubmit('submit', 'Uložit změny');

        $form->onSuccess[] = function ($form, $values): void {
            $id = $this->getParameter('id');
            $this->userFacade->updateUser((int) $id, $values);
            $this->flashMessage('Uživatel upraven.', 'success');
            $this->redirect('default');
        };

        return $form;
    }
}
