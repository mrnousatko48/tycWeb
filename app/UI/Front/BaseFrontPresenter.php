<?php

namespace App\UI\Front;

use Nette\Application\UI\Presenter;
use App\Model\PageFacade;
use App\Model\OrderFacade;

abstract class BaseFrontPresenter extends Presenter
{
    protected OrderFacade $orderFacade;
    protected PageFacade $pageFacade;

    public function injectDependencies(OrderFacade $orderFacade, PageFacade $pageFacade): void
    {
        $this->orderFacade = $orderFacade;
        $this->pageFacade = $pageFacade;
    }

    protected function startup(): void
    {
        parent::startup();

        // Calculate cartCount for all front presenters
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

        // Fetch logo paths for light and dark themes
        $this->template->logos = $this->pageFacade->getLogos();
    }
}