<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;

final class OrderFacade
{
    private Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    /**
     * Create a new order and assign all user's cart cases to it
     */
    /**
     * Create a new order and assign selected cases to it via order_case table
     */
    /**
     * Create a new order and assign selected cases to it via order_case table
     */
    public function createOrder(int $userId, string $address, string $city, array $caseQuantities): ActiveRow
    {
        $this->database->beginTransaction();

        try {
            // Create the order
            $order = $this->database->table('orders')->insert([
                'user_id' => $userId,
                'address' => $address,
                'city' => $city,
                'state' => 'OBJEDNANO',
                'created_at' => new \DateTime(),
            ]);

            foreach ($caseQuantities as $caseId => $quantity) {
                $this->database->table('order_case')->insert([
                    'order_id' => $order->id,
                    'case_id' => $caseId,
                    'quantity' => $quantity,
                ]);

                // Update case state to prevent reuse in cart
                $this->database->table('cases')
                    ->where('id', $caseId)
                    ->where('user_id', $userId)
                    ->where('state', 'KOSIK')
                    ->update([
                        'state' => 'OBJEDNANO',
                    ]);
            }

            $this->database->commit();
            return $order;
        } catch (\Throwable $e) {
            $this->database->rollBack();
            throw $e;
        }
    }



    /**
     * Return all orders, newest first
     */
    public function getAllOrders(): iterable
    {
        return $this->database->table('orders')
            ->order('created_at DESC')
            ->fetchAll();
    }

    /**
     * Return all cases, newest first
     */
    public function getAllCases(): iterable
    {
        return $this->database->table('cases')
            ->order('created_at DESC')
            ->fetchAll();
    }

    /**
     * Get user's orders (used in cart history or admin)
     */
    public function getOrdersByUserId(int $userId): \Nette\Database\Table\Selection
    {
        return $this->database->table('orders')
            ->where('user_id', $userId)
            ->order('created_at DESC');
    }

    /**
     * Get user's cases (cart or full order items)
     */
    public function getCasesByUserId(int $userId): \Nette\Database\Table\Selection
    {
        return $this->database->table('cases')
            ->where('user_id', $userId)
            ->order('created_at DESC');
    }

    /**
     * Get user's current cart cases (not yet in an order)
     */
    public function getCartCasesByUserId(int $userId): \Nette\Database\Table\Selection
    {
        return $this->database->table('cases')
            ->where('user_id', $userId)
            ->where('state', 'KOSIK')
            ->order('created_at DESC');
    }

    /**
     * Add a new case (to cart or manually)
     */
    public function createCase(array $data): ActiveRow
    {
        return $this->database->table('cases')->insert($data);
    }

    public function getCasesByIds(array $ids)
    {
        return $this->database->table('cases')
            ->where('id', $ids)
            ->fetchAll();
    }
}
