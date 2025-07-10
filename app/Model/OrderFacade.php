<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Http\Session;

final class OrderFacade
{
    private Explorer $database;
    private Session $session; 

    public function __construct(Explorer $database, Session $session)
    {
        $this->database = $database;
        $this->session = $session; 
    }



    public function createOrder(int $userId, string $firstname, string $lastname, string $email, string $address, string $city, string $psc, string $payment, array $caseQuantities): ActiveRow
    {
        $this->database->beginTransaction();

        try {
            $order = $this->database->table('orders')->insert([
                'user_id' => $userId,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'address' => $address,
                'city' => $city,
                'psc' => $psc,
                'payment' => $payment,
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

    public function createGuestOrder(string $firstname, string $lastname, string $address, string $city, string $psc, string $payment, array $caseQuantities): ActiveRow
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
                'payment' => $payment,
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

      public function createCase(array $data, ?int $userId = null): ActiveRow
    {
        $coreData = [
            'user_id' => $userId,
            'manufacturer' => $data['manufacturer'] ?? null,
            'model' => $data['model'] ?? null,
            'color' => $data['color'] ?? null,
            'total_price' => $data['total_price'] ?? 0.0,
            'state' => 'KOSIK',
            'created_at' => new \DateTime()
        ];

        // Extract features (all fields except core ones)
        $features = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, ['manufacturer', 'model', 'color', 'total_price'])) {
                $features[$key] = $value;
            }
        }

        // Add features to core data if using JSON column
        if (!empty($features)) {
            $coreData['features'] = json_encode($features);
        }

        try {
            // Insert case into the database
            $case = $this->database->table('cases')->insert($coreData);
            error_log('Case created with ID: ' . $case->id . ', Data: ' . print_r($coreData, true));
        } catch (\Exception $e) {
            error_log('Error inserting case: ' . $e->getMessage());
            throw $e; // Re-throw to be caught by processForm
        }

        // For guest users, store case in session
        if ($userId === null) {
            $orderSection = $this->session->getSection('order');
            $quantities = $orderSection->quantities ?? [];
            $quantities[$case->id] = ($quantities[$case->id] ?? 0) + 1;
            $orderSection->quantities = $quantities;
            $this->session->setExpiration('30 minutes');
            error_log('Guest cart updated: ' . print_r($quantities, true));
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

    public function getOrderItems(int $orderId): array
    {
        $orderCases = $this->database->table('order_case')
            ->where('order_id', $orderId)
            ->fetchAll();

        $result = [];
        foreach ($orderCases as $orderCase) {
            $case = $this->database->table('cases')->get($orderCase->case_id);
            if ($case) {
                $features = $case->features ? json_decode($case->features, true) : [];
                $result[] = (object)[
                    'id' => $case->id,
                    'manufacturer' => $case->manufacturer,
                    'model' => $case->model,
                    'color' => $case->color,
                    'features' => $features, // Decoded features
                    'quantity' => $orderCase->quantity,
                ];
            }
        }

        return $result;
    }
}
