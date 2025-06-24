<?php

declare(strict_types=1);

namespace App\UI\Admin\Dashboard;

use Nette;
use App\Model\OrderFacade;
use Nette\Database\Explorer;

final class DashboardPresenter extends Nette\Application\UI\Presenter
{
    private OrderFacade $orderFacade;
    private Explorer $database;

    public function __construct(OrderFacade $orderFacade, Explorer $database)
    {
        parent::__construct();
        $this->orderFacade = $orderFacade;
        $this->database = $database;
    }

    public function renderDefault(): void
    {
        $orders = $this->orderFacade->getAllOrders();

        $orderData = [];
        foreach ($orders as $order) {
            $caseIds = $this->database->table('order_case')
                ->where('order_id', $order->id)
                ->fetchPairs(null, 'case_id');
            
            $cases = $this->database->table('cases')
                ->where('id', $caseIds)
                ->fetchAll();
        
            $user = $this->database->table('users')->get($order->user_id);

            $orderData[] = [
                'order' => $order,
                'cases' => $cases,
                'user' => $user,
            ];
        }

        $this->template->orders = $orderData;
    }

    public function renderDetail(): void
    {

    }
}