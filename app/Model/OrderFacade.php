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

    public function createOrder(int $userId, string $firstname, string $lastname, string $address, string $city, string $psc, array $caseQuantities): ActiveRow
    {
        $this->database->beginTransaction();
    
        try {
            $order = $this->database->table('orders')->insert([
                'user_id' => $userId,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'address' => $address,
                'city' => $city,
                'psc' => $psc,
                'state' => 'OBJEDNANO',
                'created_at' => new \DateTime(),
            ]);
    
            foreach ($caseQuantities as $caseId => $quantity) {
                $this->database->table('order_case')->insert([
                    'order_id' => $order->id,
                    'case_id' => $caseId,
                    'quantity' => $quantity,
                ]);
    
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
    
    public function createGuestOrder(string $firstname, string $lastname, string $address, string $city, string $psc, array $caseQuantities): ActiveRow
    {
        $this->database->beginTransaction();
    
        try {
            $order = $this->database->table('orders')->insert([
                'user_id' => null,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'address' => $address,
                'city' => $city,
                'psc' => $psc,
                'state' => 'OBJEDNANO',
                'created_at' => new \DateTime(),
            ]);
    
            foreach ($caseQuantities as $caseId => $quantity) {
                $this->database->table('order_case')->insert([
                    'order_id' => $order->id,
                    'case_id' => $caseId,
                    'quantity' => $quantity,
                ]);
            }
    
            $this->database->commit();
            return $order;
        } catch (\Throwable $e) {
            $this->database->rollBack();
            throw $e;
        }
    }
    

    public function getAllOrders(): iterable
    {
        return $this->database->table('orders')
            ->order('created_at DESC')
            ->fetchAll();
    }

    public function getAllCases(): iterable
    {
        return $this->database->table('cases')
            ->order('created_at DESC')
            ->fetchAll();
    }

    public function getOrderCases(int $orderId): iterable
    {
        return $this->database->table('order_case')
            ->where('order_id', $orderId)
            ->select('cases.*')
            ->fetchAll();
    }

    public function getOrdersByUserId(int $userId): \Nette\Database\Table\Selection
    {
        return $this->database->table('orders')
            ->where('user_id', $userId)
            ->order('created_at DESC');
    }

    public function getCasesByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $this->database->table('cases')
            ->where('id', $ids)
            ->where('state', 'KOSIK')
            ->fetchAll();
    }


    public function getCasesByUserId(int $userId): \Nette\Database\Table\Selection
    {
        return $this->database->table('cases')
            ->where('user_id', $userId)
            ->order('created_at DESC');
    }

    public function getCartCasesByUserId(int $userId): \Nette\Database\Table\Selection
    {
        return $this->database->table('cases')
            ->where('user_id', $userId)
            ->where('state', 'KOSIK')
            ->order('created_at DESC');
    }

    public function createCase(array $data, ?int $userId = null, \Nette\Http\Session $session = null): ActiveRow
    {
        $data['user_id'] = $userId;
        $data['state'] = 'KOSIK';
    
        $case = $this->database->table('cases')->insert($data);
    
        if ($userId === null && $session !== null) {
            $orderSection = $session->getSection('order');
            $quantities = $orderSection->quantities ?? [];
            $quantities[$case->id] = 1;
            $orderSection->quantities = $quantities;
        }
    
        return $case;
    }
    

    public function removeCaseFromCart(\Nette\Http\Session $session, int $caseId): void
    {
        $orderSection = $session->getSection('order');
        $quantities = $orderSection->quantities ?? [];

        if (isset($quantities[$caseId])) {
            unset($quantities[$caseId]);
            $orderSection->quantities = $quantities;
        }
    }

    public function removeCaseFromCartByUser(int $userId, int $caseId): void
    {
        $this->database->table('cases')
            ->where('id', $caseId)
            ->where('user_id', $userId)
            ->where('state', 'KOSIK')
            ->delete();
    }

}
