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
     * Calculate additional cost based on shipping option and payment method IDs
     */
    public function calculateAdditionalCost(int $shippingOptionId, int $paymentMethodId): float
    {
        $shippingOption = $this->database->table('shipping_options')->get($shippingOptionId);
        $paymentMethod = $this->database->table('vendor_payment_methods')->get($paymentMethodId);

        if (!$shippingOption) {
            \Tracy\Debugger::barDump($shippingOptionId, 'Invalid Shipping Option ID');
            throw new \InvalidArgumentException("Shipping option ID $shippingOptionId not found.");
        }

        $shippingCost = (float)$shippingOption->cost;
        $paymentCost = $paymentMethod ? (float)$paymentMethod->price : 0.0;

        \Tracy\Debugger::barDump([
            'shippingOptionId' => $shippingOptionId,
            'shippingCost' => $shippingCost,
            'paymentMethodId' => $paymentMethodId,
            'paymentCost' => $paymentCost,
        ], 'Additional Cost Calculation');

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

            $shippingInfo = $this->getShippingInfo((int)$order->shipping);
            $paymentInfo = $this->getPaymentInfo((int)$order->payment);

            $orderData[] = [
                'order' => (object)[
                    'id' => $order->id,
                    'user_id' => $order->user_id,
                    'firstname' => $order->firstname,
                    'lastname' => $order->lastname,
                    'email' => $order->email,
                    'phone' => $order->phone,
                    'address' => $order->address,
                    'city' => $order->city,
                    'psc' => $order->psc,
                    'payment' => $paymentInfo ? $paymentInfo['name'] : 'Unknown',
                    'payment_id' => $order->payment,
                    'shipping' => $shippingInfo ? $shippingInfo['name'] : 'Unknown',
                    'shipping_id' => $order->shipping,
                    'delivery_point' => $order->delivery_point,
                    'additional_cost' => $order->additional_cost,
                    'state' => $order->state,
                    'created_at' => $order->created_at,
                    'variable_symbol' => $order->variable_symbol,
                ],
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

        $shippingInfo = $this->getShippingInfo((int)$order->shipping);
        $paymentInfo = $this->getPaymentInfo((int)$order->payment);

        return [
            'order' => (object)[
                'id' => $order->id,
                'user_id' => $order->user_id,
                'firstname' => $order->firstname,
                'lastname' => $order->lastname,
                'email' => $order->email,
                'phone' => $order->phone,
                'address' => $order->address,
                'city' => $order->city,
                'psc' => $order->psc,
                'payment' => $paymentInfo ? $paymentInfo['name'] : 'Unknown',
                'payment_id' => $order->payment,
                'shipping' => $shippingInfo ? $shippingInfo['name'] : 'Unknown',
                'shipping_id' => $order->shipping,
                'delivery_point' => $order->delivery_point,
                'additional_cost' => $order->additional_cost,
                'state' => $order->state,
                'created_at' => $order->created_at,
                'variable_symbol' => $order->variable_symbol,
            ],
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
                if (isset($features['features']) && is_string($features['features'])) {
                    $features = json_decode($features['features'], true) ?? $features;
                }
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

    public function createOrder(int $userId, string $firstname, string $lastname, string $email, string $phone, string $address, string $city, string $psc, int $paymentMethodId, array $caseQuantities, int $shippingOptionId, ?string $deliveryPoint = null): ActiveRow
    {
        if (empty($caseQuantities)) {
            throw new \InvalidArgumentException('Cart cannot be empty.');
        }

        if (!$this->isValidShippingOption($shippingOptionId)) {
            throw new \InvalidArgumentException('Invalid shipping option.');
        }

        if (!$this->isValidPaymentMethod($paymentMethodId)) {
            throw new \InvalidArgumentException('Invalid payment method.');
        }

        $this->database->beginTransaction();

        try {
            $additionalCost = $this->calculateAdditionalCost($shippingOptionId, $paymentMethodId);
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
                'payment' => $paymentMethodId,
                'shipping' => $shippingOptionId,
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

    public function createGuestOrder(string $firstname, string $lastname, string $email, string $phone, string $address, string $city, string $psc, int $paymentMethodId, array $caseQuantities, int $shippingOptionId, ?string $deliveryPoint = null): ActiveRow
    {
        if (empty($caseQuantities)) {
            throw new \InvalidArgumentException('Cart cannot be empty.');
        }

        if (!$this->isValidShippingOption($shippingOptionId)) {
            throw new \InvalidArgumentException('Invalid shipping option.');
        }

        if (!$this->isValidPaymentMethod($paymentMethodId)) {
            throw new \InvalidArgumentException('Invalid payment method.');
        }

        $this->database->beginTransaction();

        try {
            $additionalCost = $this->calculateAdditionalCost($shippingOptionId, $paymentMethodId);
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
                'payment' => $paymentMethodId,
                'shipping' => $shippingOptionId,
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

        public function getShippingInfo(int $shippingOptionId): ?array
    {
        $shippingOption = $this->database->table('shipping_options')->get($shippingOptionId);
        \Tracy\Debugger::barDump($shippingOption ? (array)$shippingOption : null, "Shipping Option Data for ID: $shippingOptionId");
        return $shippingOption ? ['cost' => (float)$shippingOption->cost, 'name' => $shippingOption->name] : null;
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

    public function getOrdersByUserId(int $userId): array
    {
        $query = $this->database->table('orders')
            ->where('user_id', $userId)
            ->order('created_at DESC');

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

    public function getVendors(): array
    {
        $vendors = $this->database->table('vendors')
            ->order('name')
            ->fetchAll();

        $result = [];
        foreach ($vendors as $vendor) {
            $result[$vendor->id] = $vendor->name;
        }

        return $result;
    }

    public function getShippingOptionsByVendor(int $vendorId): array
    {
        $options = $this->database->table('shipping_options')
            ->where('vendor_id', $vendorId)
            ->order('name')
            ->fetchAll();

        $result = [];
        foreach ($options as $option) {
            $result[$option->id] = $option->name;
        }

        \Tracy\Debugger::barDump($result, "Shipping Options for Vendor ID: $vendorId");
        return $result;
    }

    public function getPaymentMethodsByVendor(int $vendorId): array
    {
        $methods = $this->database->table('vendor_payment_methods')
            ->where('vendor_id', $vendorId)
            ->order('name')
            ->fetchAll();

        $result = [];
        foreach ($methods as $method) {
            $result[$method->id] = sprintf('%s (%s CZK)', $method->name, number_format($method->price, 2));
        }

        return $result;
    }

    public function isValidVendor(int $vendorId): bool
    {
        return $this->database->table('vendors')->get($vendorId) !== null;
    }

    public function isValidShippingOption(int $shippingOptionId): bool
    {
        return $this->database->table('shipping_options')->get($shippingOptionId) !== null;
    }

    public function isValidPaymentMethod(int $paymentMethodId): bool
    {
        return $this->database->table('vendor_payment_methods')->get($paymentMethodId) !== null;
    }

    public function getPaymentInfo(int $paymentMethodId): ?array
    {
        $method = $this->database->table('vendor_payment_methods')->get($paymentMethodId);
        return $method ? ['name' => $method->name, 'price' => (float)$method->price] : null;
    }
}