<?php
declare(strict_types=1);

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

    /**
     * Generate a unique variable symbol
     */
    private function generateVariableSymbol(): string
    {
        return date('Ymd') . str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate additional cost based on shipping and payment
     */
    public function calculateAdditionalCost(string $shipping, string $payment): float
    {
        $shippingRow = $this->database->table('shipping')
            ->where('code', $shipping)
            ->fetch();

        $shippingCost = $shippingRow ? (float)$shippingRow->cost : 0.0;
        $paymentCost = $payment === 'DOBIRKA' ? 40.0 : 0.0;
        return $shippingCost + $paymentCost;
    }

    /**
     * Fetch orders with details, optionally filtered by status
     */
    public function getOrdersWithDetails(?string $status = null): array
    {
        $query = $this->database->table('orders')
            ->order('created_at DESC');

        if ($status !== null) {
            $query->where('state', $status);
        }

        $orders = $query->fetchAll();
        $orderData = [];

        foreach ($orders as $order) {
            $orderCases = $this->database->table('order_case')
                ->where('order_id', $order->id)
                ->fetchAll();

            $caseIds = array_column(iterator_to_array($orderCases), 'case_id');
            $cases = $this->database->table('cases')
                ->where('id', $caseIds)
                ->fetchAll();

            $user = $order->user_id ? $this->database->table('users')->get($order->user_id) : null;

            $orderData[] = [
                'order' => $order,
                'cases' => $this->processCases($orderCases, $cases),
                'user' => $user,
            ];
        }

        return $orderData;
    }

    /**
     * Fetch details for a specific order
     */
    public function getOrderDetails(int $orderId): ?array
    {
        $order = $this->database->table('orders')->get($orderId);
        if (!$order) {
            return null;
        }

        $orderCases = $this->database->table('order_case')
            ->where('order_id', $orderId)
            ->fetchAll();

        $caseIds = array_column(iterator_to_array($orderCases), 'case_id');
        $cases = $this->database->table('cases')
            ->where('id', $caseIds)
            ->fetchAll();

        $user = $order->user_id ? $this->database->table('users')->get($order->user_id) : null;

        return [
            'order' => $order,
            'cases' => $this->processCases($orderCases, $cases),
            'user' => $user,
        ];
    }

    /**
     * Process cases to handle JSON features and include quantities
     */
    private function processCases(iterable $orderCases, iterable $cases): array
    {
        $processedCases = [];
        $caseMap = [];
        foreach ($cases as $case) {
            $caseMap[$case->id] = $case;
        }

        foreach ($orderCases as $orderCase) {
            $case = $caseMap[$orderCase->case_id] ?? null;
            if ($case) {
                $features = json_decode($case->features, true) ?? [];
                // Handle nested 'features' key
                if (isset($features['features']) && is_string($features['features'])) {
                    $features = json_decode($features['features'], true) ?? $features;
                }
                // Clean feature keys
                $cleanFeatures = [];
                foreach ($features as $key => $value) {
                    $cleanKey = str_replace('_', ' ', $key);
                    $cleanKey = mb_convert_case($cleanKey, MB_CASE_TITLE, 'UTF-8');
                    $cleanFeatures[$cleanKey] = $value;
                }
                $processedCases[] = (object)[
                    'id' => $case->id,
                    'manufacturer' => $case->manufacturer,
                    'model' => $case->model,
                    'color' => $case->color,
                    'total_price' => $case->total_price,
                    'features' => $cleanFeatures,
                    'state' => $case->state,
                    'user_id' => $case->user_id,
                    'created_at' => $case->created_at,
                    'quantity' => $orderCase->quantity,
                ];
            }
        }
        return $processedCases;
    }

    /**
     * Update order state and synchronize case states
     */
    public function updateOrderState(int $orderId, string $newState): void
    {
        $validStates = ['OBJEDNANO', 'ZAPLACENO', 'ODESLANO', 'DORUCENO', 'VYZVEDNUTO'];
        if (!in_array($newState, $validStates)) {
            throw new \InvalidArgumentException("Invalid state: $newState");
        }

        $order = $this->database->table('orders')->get($orderId);
        if (!$order) {
            throw new \InvalidArgumentException("Order $orderId not found");
        }

        $currentStateIndex = array_search($order->state, $validStates);
        $newStateIndex = array_search($newState, $validStates);

        if ($currentStateIndex === false || $newStateIndex !== $currentStateIndex + 1) {
            throw new \InvalidArgumentException("Cannot transition from {$order->state} to $newState");
        }

        $this->database->beginTransaction();
        try {
            $this->database->table('orders')
                ->where('id', $orderId)
                ->update(['state' => $newState]);

            $caseIds = $this->database->table('order_case')
                ->where('order_id', $orderId)
                ->fetchPairs(null, 'case_id');

            if (!empty($caseIds)) {
                $this->database->table('cases')
                    ->where('id', $caseIds)
                    ->update(['state' => $newState]);
            }

            $this->database->commit();
        } catch (\Throwable $e) {
            $this->database->rollBack();
            error_log("Error updating order state for order $orderId: " . $e->getMessage());
            throw $e;
        }
    }

    public function createOrder(int $userId, string $firstname, string $lastname, string $email, string $phone, string $address, string $city, string $psc, string $payment, array $caseQuantities, string $shipping, ?string $deliveryPoint = null): ActiveRow
    {
        if (empty($caseQuantities)) {
            throw new \InvalidArgumentException('Cart cannot be empty.');
        }

        $this->database->beginTransaction();

        try {
            $additionalCost = $this->calculateAdditionalCost($shipping, $payment);
            $variableSymbol = $this->generateVariableSymbol();
            $order = $this->database->table('orders')->insert([
                'user_id' => $userId,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'psc' => $psc,
                'payment' => $payment,
                'shipping' => $shipping,
                'delivery_point' => $deliveryPoint,
                'additional_cost' => $additionalCost,
                'state' => 'OBJEDNANO',
                'created_at' => new \DateTime(),
                'variable_symbol' => $variableSymbol,
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
            error_log("Error creating order: " . $e->getMessage());
            throw $e;
        }
    }

    public function createGuestOrder(string $firstname, string $lastname, string $email, string $phone, string $address, string $city, string $psc, string $payment, array $caseQuantities, string $shipping, ?string $deliveryPoint = null): ActiveRow
    {
        if (empty($caseQuantities)) {
            throw new \InvalidArgumentException('Cart cannot be empty.');
        }

        $this->database->beginTransaction();

        try {
            $additionalCost = $this->calculateAdditionalCost($shipping, $payment);
            $variableSymbol = $this->generateVariableSymbol();
            $order = $this->database->table('orders')->insert([
                'user_id' => null,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'psc' => $psc,
                'payment' => $payment,
                'shipping' => $shipping,
                'delivery_point' => $deliveryPoint,
                'additional_cost' => $additionalCost,
                'state' => 'OBJEDNANO',
                'created_at' => new \DateTime(),
                'variable_symbol' => $variableSymbol,
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
            error_log("Error creating guest order: " . $e->getMessage());
            throw $e;
        }
    }

    public function getShippingInfo(string $shippingCode): ?array
    {
        $shippingRow = $this->database->table('shipping')
            ->where('code', $shippingCode)
            ->fetch();

        return $shippingRow ? ['cost' => (float)$shippingRow->cost, 'name' => $shippingRow->name] : null;
    }

    public function isValidShippingCode(string $shippingCode): bool
    {
        return $this->database->table('shipping')
            ->where('code', $shippingCode)
            ->count('*') > 0;
    }

    public function getShippingOptions(): array
    {
        $options = [];
        $shippingRows = $this->database->table('shipping')->fetchAll();
        foreach ($shippingRows as $row) {
            $options[$row->code] = sprintf('%s (%s Kč)', $row->name, number_format($row->cost, 2, ',', ' '));
        }
        return $options;
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

        $features = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, ['manufacturer', 'model', 'color', 'total_price'])) {
                $features[$key] = $value;
            }
        }

        if (!empty($features)) {
            $coreData['features'] = json_encode($features);
        }

        try {
            $case = $this->database->table('cases')->insert($coreData);
            error_log('Case created with ID: ' . $case->id . ', Data: ' . print_r($coreData, true));
        } catch (\Exception $e) {
            error_log('Error inserting case: ' . $e->getMessage());
            throw $e;
        }

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
                $features = json_decode($case->features, true) ?? [];
                if (isset($features['features']) && is_string($features['features'])) {
                    $features = json_decode($features['features'], true) ?? $features;
                }
                $cleanFeatures = [];
                foreach ($features as $key => $value) {
                    $cleanKey = str_replace('_', ' ', $key);
                    $cleanKey = mb_convert_case($cleanKey, MB_CASE_TITLE, 'UTF-8');
                    $cleanFeatures[$cleanKey] = $value;
                }

                $result[] = (object)[
                    'id' => $case->id,
                    'manufacturer' => $case->manufacturer,
                    'model' => $case->model,
                    'color' => $case->color,
                    'total_price' => $case->total_price,
                    'features' => $cleanFeatures,
                    'quantity' => $orderCase->quantity,
                ];
            }
        }

        return $result;
    }
}