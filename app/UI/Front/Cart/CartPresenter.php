<?php

declare(strict_types=1);

namespace App\UI\Front\Cart;

use Nette;
use App\Model\OrderFacade;

final class CartPresenter extends Nette\Application\UI\Presenter
{
    private OrderFacade $orderFacade;

    public function __construct(OrderFacade $orderFacade)
    {
        parent::__construct();
        $this->orderFacade = $orderFacade;
    }

    protected function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage('Pro zobrazení košíku se musíte přihlásit.', 'warning');
            $this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
        }
    }

    public function renderDefault(): void
    {
        $userId = (int) $this->getUser()->getId();
        $orders = $this->orderFacade->getOrdersByUserId($userId);
        $this->template->orders = $orders;
    }

    public function renderDetail(): void
    {
    }
}
