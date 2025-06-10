<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\UniqueConstraintViolationException;

class RepairFacade
{
    private Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    // ========================== Repairs Retrieval ========================== //

    /**
     * Retrieves all repairs from the database.
     */
    public function getAllRepairs(): array
    {
        return $this->database->table('repairs')->fetchAll();
    }

    /**
     * Retrieves a specific repair by its ID.
     */
    public function getRepairById(int $repairId): ?\Nette\Database\Table\ActiveRow
    {
        return $this->database->table('repairs')->get($repairId);
    }
    
    /**
     * Retrieves a repair along with its associated technician information.
     */
    public function getRepairWithTechnician(int $id): ?array
    {
        $repair = $this->database->table('repairs')->get($id);

        if (!$repair) {
            return null;
        }

        $technician = $repair->technitian_id 
            ? $repair->ref('users', 'technitian_id') 
            : null;

        return ['repair' => $repair, 'technician' => $technician];
    }
    
    // ========================== Device Types & Manufacturers ========================== //

    /**
     * Retrieves all device types.
     */
    public function getTypes(): array
    {
        return $this->database->table('device_types')->fetchPairs('id', 'name');
    }

    /**
     * Retrieves all manufacturers.
     */
    public function getManufacturers(): array
    {
        return $this->database->table('manufacturers')->fetchPairs('id', 'name');
    }

    /**
     * Retrieves manufacturers that produce devices of a specific type.
     */
    public function getManufacturersByType(int $typeId): array
    {
        return $this->database->table('manufacturers')
            ->where('id IN', $this->database->table('models')
                ->where('type_id', $typeId)
                ->select('manufacturer_id')
            )
            ->fetchPairs('id', 'name');
    }

    /**
     * Retrieves models associated with a specific manufacturer.
     */
    public function getModelsByManufacturer(int $manufacturerId): array
    {
        return $this->database->table('models')
            ->where('manufacturer_id', $manufacturerId)
            ->fetchPairs('id', 'name');
    }

    /**
     * Retrieves all fault types.
     */
    public function getFaults(): array 
    {
        return $this->database->table('faults')->fetchPairs('id', 'name');
    }

    // ========================== Adding Records ========================== //

    /**
     * Adds a new repair record to the database.
     */
    public function addRepair(int $manufacturerId, int $modelId, int $faultId, string $name, string $surname, string $email, string $phone): bool
    {
        try {
            $manufacturer = $this->database->table('manufacturers')->get($manufacturerId)->name ?? 'Unknown Manufacturer';
            $model = $this->database->table('models')->get($modelId)->name ?? 'Unknown Model';
            $fault = $this->database->table('faults')->get($faultId)->name ?? 'Unknown Fault';

            $this->database->table('repairs')->insert([
                'device' => $manufacturer . ' ' . $model,
                'issue' => $fault,
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'phone' => $phone,
                'created_at' => new \DateTime(),
            ]);
            return true;
        } catch (\Exception $e) {
            bdump('Error during repair insertion: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Adds a new device type.
     */
    public function addDeviceType(string $name): bool
    {
        try {
            $this->database->table('device_types')->insert([
                'name' => $name,
            ]);
            return true;
        } catch (\Exception $e) {
            bdump('Error adding device type: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Adds a new manufacturer.
     */
    public function addManufacturer(string $name): bool
    {
        try {
            $this->database->table('manufacturers')->insert([
                'name' => $name,
            ]);
            return true;
        } catch (\Exception $e) {
            bdump('Error adding manufacturer: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Adds a new device model.
     */
    public function addModel(string $name, int $manufacturerId, int $typeId, ?int $releaseYear = null): bool
    {
        try {
            $this->database->table('models')->insert([
                'name' => $name,
                'manufacturer_id' => $manufacturerId,
                'type_id' => $typeId,
                'release_year' => $releaseYear,
            ]);
            return true;
        } catch (\Exception $e) {
            bdump('Error adding model: ' . $e->getMessage());
            return false;
        }
    }
    
    // ========================== Updating Records ========================== //

    /**
     * Updates the status of a repair.
     */
    public function updateRepairStatus(int $id, string $status, string $statusDescription): void
    {
        $repair = $this->database->table('repairs')->get($id);
    
        if (!$repair) {
            throw new \Exception('Oprava nenalezena.');
        }
    
        $repair->update([
            'status' => $status,
            'status_description' => $statusDescription, // Save the description
        ]);
    }
    

}
