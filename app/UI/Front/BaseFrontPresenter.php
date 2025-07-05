<?php

namespace App\UI\Front;

use Nette\Application\UI\Presenter;

abstract class BaseFrontPresenter extends Presenter
{
    protected \App\Model\OrderFacade $orderFacade;

    public function injectOrderFacade(\App\Model\OrderFacade $orderFacade): void
    {
        $this->orderFacade = $orderFacade;
    }

    protected function startup(): void
    {
        parent::startup();

        // Výpočet cartCount pro všechny front presentery
        if ($this->getUser()->isLoggedIn()) {
            $userId = (int) $this->getUser()->getId();
            $cartItems = $this->orderFacade->getCartCasesByUserId($userId);
            $cartCount = count($cartItems);
        } else {
            $session = $this->getSession('order');
            $quantities = $session->quantities ?? [];
            $cartCount = array_sum($quantities);
        }

        $this->template->cartCount = $cartCount;
    }
}
