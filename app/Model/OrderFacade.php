<?php

namespace App\Model;

use Nette\Database\Explorer;

final class OrderFacade
{
    private Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    public function createOrder(array $data): void
    {
        $this->database->table('orders')->insert($data);
        
    }

    /** @return \Nette\Database\Table\ActiveRow[] */
    public function getAllOrders(): iterable
    {
        return $this->database->table('orders')->order('created_at DESC')->fetchAll();
    }

    public function getOrdersByUserId(int $userId)
    {
        return $this->database->table('orders')
            ->where('user_id', $userId)
            ->order('created_at DESC');
    }

}
