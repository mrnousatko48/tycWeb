<?php
declare(strict_types=1);

namespace App\UI\Admin;

use Nette\Application\UI\Presenter;

abstract class BaseAdminPresenter extends Presenter
{
    protected function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage('Pro přístup je nutné se přihlásit.', 'warning');
            $this->redirect(':Front:Sign:in', ['backlink' => $this->storeRequest()]);
        }

        if (!$this->getUser()->isInRole('ADMIN')) {
            $this->flashMessage('Nemáte administrátorská oprávnění.', 'warning');
            $this->redirect(':Front:Sign:in');
        }
    }
}