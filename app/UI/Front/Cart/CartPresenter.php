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
