<?php
namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Http\FileUpload;
use App\Utils\ImageUploader;
use App\Utils\FileUploader;

class ModelFacade
{
    private Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    // ---- MANUFACTURERS ----
    public function getManufacturers(): \Nette\Database\Table\Selection
    {
        return $this->database->table('manufacturers')->order('name');
    }

    public function getManufacturer(int $id): ?ActiveRow
    {
        return $this->database->table('manufacturers')->get($id);
    }

    public function addManufacturer(string $name): ActiveRow
    {
        try {
            return $this->database->table('manufacturers')->insert([
                'name' => trim($name),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            throw new \Exception("Manufacturer '$name' already exists.");
        }
    }

    public function updateManufacturer(int $id, string $name): void
    {
        try {
            $this->database->table('manufacturers')->get($id)?->update([
                'name' => trim($name),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            throw new \Exception("Manufacturer '$name' already exists.");
        }
    }

    public function deleteManufacturer(int $id): void
    {
        $this->database->table('manufacturers')->get($id)?->delete();
    }

    public function getManufacturerNameById(int $id): string
    {
        $manufacturer = $this->database->table('manufacturers')->get($id);
        return $manufacturer ? $manufacturer->name : '';
    }

    // ---- MODELS ----
    public function getModels(?int $manufacturerId = null): \Nette\Database\Table\Selection
    {
        $query = $this->database->table('models')->order('name');
        if ($manufacturerId) {
            $query->where('manufacturer_id', $manufacturerId);
        }
        return $query;
    }

    public function getModelsByManufacturer(int $manufacturerId): array
    {
        $query = $this->database->table('models')
            ->where('manufacturer_id', $manufacturerId)
            ->order('name ASC');
        $models = [];
        foreach ($query->fetchAll() as $model) {
            $models[$model->id] = $model->name;
        }
        return $models;
    }

    public function getModelById(int $id): ?ActiveRow
    {
        return $this->database->table('models')->get($id);
    }

    public function getModelNameById(int $id): string
    {
        $model = $this->database->table('models')->get($id);
        return $model ? $model->name : '';
    }

    public function addModel(array $data): ActiveRow
{
    $this->database->beginTransaction();
    try {
        $colorIds = $data['color_ids'] ?? [];
        $featureOptions = $data['feature_options'] ?? [];
        unset($data['color_ids'], $data['feature_options']);

        $model = $this->database->table('models')->insert($data);

        foreach ($colorIds as $colorId) {
            $this->database->table('model_colors')->insert([
                'model_id' => $model->id,
                'color_id' => $colorId,
            ]);
        }

        foreach ($featureOptions as $featureId => $optionId) {
            $this->database->table('model_features')->insert([
                'model_id' => $model->id,
                'feature_id' => $featureId,
                'feature_option_id' => $optionId,
            ]);
        }

        $this->database->commit();
        return $model;
    } catch (UniqueConstraintViolationException $e) {
        $this->database->rollBack();
        throw new \Exception("Model '{$data['name']}' already exists for this manufacturer.");
    } catch (\Exception $e) {
        $this->database->rollBack();
        throw $e;
    }
}

    public function updateModel(int $id, array $data): void
{
    $this->database->beginTransaction();
    try {
        $colorIds = $data['color_ids'] ?? [];
        $featureOptions = $data['feature_options'] ?? [];
        unset($data['color_ids'], $data['feature_options']);

        $this->database->table('models')->get($id)?->update($data);

        $this->database->table('model_colors')->where('model_id', $id)->delete();
        foreach ($colorIds as $colorId) {
            $this->database->table('model_colors')->insert([
                'model_id' => $id,
                'color_id' => $colorId,
            ]);
        }

        $this->database->table('model_features')->where('model_id', $id)->delete();
        foreach ($featureOptions as $featureId => $optionId) {
            $this->database->table('model_features')->insert([
                'model_id' => $id,
                'feature_id' => $featureId,
                'feature_option_id' => $optionId,
            ]);
        }

        $this->database->commit();
    } catch (UniqueConstraintViolationException $e) {
        $this->database->rollBack();
        throw new \Exception("Model '{$data['name']}' already exists for this manufacturer.");
    } catch (\Exception $e) {
        $this->database->rollBack();
        throw $e;
    }
}

    public function deleteModel(int $id): void
    {
        $this->database->table('models')->get($id)?->delete();
    }

    public function getColorsByModel(int $modelId, string $lang): array
    {
        $modelColors = $this->database->table('model_colors')
            ->where('model_id', $modelId)
            ->fetchPairs('color_id', null);

        if (!$modelColors) {
            return [];
        }

        $colors = $this->database->table('colors')
            ->select('id, name_' . $lang . ' AS name, hex_code')
            ->where('id', array_keys($modelColors))
            ->fetchAll();

        $result = [];
        foreach ($colors as $color) {
            $result[] = [
                'name' => $color->name,
                'hex_code' => $color->hex_code
            ];
        }

        return $result;
    }

public function getFeaturesByModel(int $modelId, string $lang): array
    {
        $features = $this->database->table('model_features')
            ->where('model_id', $modelId)
            ->fetchPairs('feature_id', null);

        if (!$features) {
            return [];
        }

        $featureData = $this->database->table('features')
            ->select('id, name_' . $lang . ' AS name')
            ->where('id', array_keys($features))
            ->fetchPairs('id', 'name');

        $options = $this->database->table('feature_options')
            ->select('id, feature_id, name_' . $lang . ' AS name, price, price_eur, image_path, allow_user_upload')
            ->where('feature_id', array_keys($features))
            ->order('feature_id')
            ->fetchAll();

        $result = [];
        foreach ($options as $option) {
            $featureName = $featureData[$option->feature_id] ?? 'Unknown Feature';
            $result[$featureName][] = [
                'name' => $option->name,
                'price' => (float)$option->price,
                'price_eur' => (float)$option->price_eur,
                'allow_user_upload' => (bool)$option->allow_user_upload
            ];
        }

        return $result;
    }

    public function getImagesByModel(int $modelId): array
    {
        $images = $this->database->table('model_images')
            ->where('model_id', $modelId)
            ->fetchAll();

        $result = [];
        foreach ($images as $image) {
            $result[] = [
                'image_path' => $image->image_path,
                'alt_text' => 'Image for model ID ' . $modelId
            ];
        }

        return $result;
    }

    // ---- COLORS ----
    public function getColors(): \Nette\Database\Table\Selection
    {
        return $this->database->table('colors')->order('name');
    }

    public function getColor(int $id): ?ActiveRow
    {
        return $this->database->table('colors')->get($id);
    }

    public function addColor(string $name, ?string $hexCode = null, ?string $name_cs = null, ?string $name_en = null): ActiveRow
    {
        try {
            return $this->database->table('colors')->insert([
                'name' => trim($name),
                'name_cs' => $name_cs ? trim($name_cs) : trim($name),
                'name_en' => $name_en ? trim($name_en) : trim($name),
                'hex_code' => $hexCode,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            throw new \Exception("Color '$name' already exists.");
        }
    }

public function updateColor(int $id, string $name, ?string $hexCode = null, ?string $name_cs = null, ?string $name_en = null): void
{
    try {
        $this->database->table('colors')->get($id)?->update([
            'name' => trim($name),
            'name_cs' => $name_cs ? trim($name_cs) : trim($name),
            'name_en' => $name_en ? trim($name_en) : trim($name),
            'hex_code' => $hexCode,
        ]);
    } catch (UniqueConstraintViolationException $e) {
        throw new \Exception("Color '$name' already exists.");
    }
}

    public function deleteColor(int $id): void
    {
        $this->database->table('colors')->get($id)?->delete();
    }

    public function getModelColors(int $modelId): array
    {
        return $this->database->table('model_colors')
            ->where('model_id', $modelId)
            ->fetchPairs('color_id', 'color_id');
    }

    // ---- FEATURES ----
    public function getFeatures(): \Nette\Database\Table\Selection
    {
        return $this->database->table('features')->order('name');
    }

    public function getFeature(int $id): ?ActiveRow
    {
        return $this->database->table('features')->get($id);
    }

   public function addFeature(string $name, ?string $name_en = null): ActiveRow
    {
        try {
            return $this->database->table('features')->insert([
                'name' => trim($name),
                'name_en' => $name_en ? trim($name_en) : null,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            throw new \Exception("Feature '$name' already exists.");
        }
    }

    public function updateFeature(int $id, string $name, ?string $name_en = null): void
    {
        try {
            $this->database->table('features')->get($id)?->update([
                'name' => trim($name),
                'name_en' => $name_en ? trim($name_en) : null,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            throw new \Exception("Feature '$name' already exists.");
        }
        }

    public function deleteFeature(int $id): void
    {
        $this->database->table('features')->get($id)?->delete();
    }

    public function getFeatureOptions(int $featureId): array
    {
        return $this->database->table('feature_options')
            ->where('feature_id', $featureId)
            ->fetchAll();
    }

    public function getAllFeatureOptions(): \Nette\Database\Table\Selection
    {
        return $this->database->table('feature_options')->order('feature_id, name');
    }

    public function addFeatureOption(int $featureId, string $name, float $price = 0.00, float $price_eur = 0.00, bool $allowUserUpload = false, string $name_cs = null, string $name_en = null): ActiveRow
    {
        $this->database->beginTransaction();
        try {
            $option = $this->database->table('feature_options')->insert([
                'feature_id' => $featureId,
                'name' => trim($name),
                'name_cs' => $name_cs ? trim($name_cs) : trim($name),
                'name_en' => $name_en ? trim($name_en) : trim($name),
                'price' => $price,
                'price_eur' => $price_eur,
                'allow_user_upload' => $allowUserUpload,
            ]);
            $this->database->commit();
            return $option;
        } catch (UniqueConstraintViolationException $e) {
            $this->database->rollBack();
            throw new \Exception("Option '$name' already exists for this feature.");
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

public function updateFeatureOption(int $id, string $name, float $price, float $price_eur, bool $allowUserUpload, string $name_cs = null, string $name_en = null): void
{
    $this->database->beginTransaction();
    try {
        $this->database->table('feature_options')->get($id)->update([
            'name' => trim($name),
            'name_cs' => $name_cs ? trim($name_cs) : trim($name),
            'name_en' => $name_en ? trim($name_en) : trim($name),
            'price' => $price,
            'price_eur' => $price_eur,
            'allow_user_upload' => $allowUserUpload,
        ]);
        $this->database->commit();
    } catch (UniqueConstraintViolationException $e) {
        $this->database->rollBack();
        throw new \Exception("Option '$name' already exists for this feature.");
    } catch (\Exception $e) {
        $this->database->rollBack();
        throw $e;
    }
}

    public function deleteFeatureOption(int $id): void
    {
        $this->database->table('feature_options')->get($id)?->delete();
    }

    public function getModelFeatures(int $modelId): array
    {
        return $this->database->table('model_features')
            ->where('model_id', $modelId)
            ->fetchAll();
    }

    public function addUserUpload(FileUpload $file, string $originalFilename): ?ActiveRow
    {
        $uploadDir = 'uploads/user_uploads';
        $filePath = FileUploader::uploadFile($file, $uploadDir);
        if ($filePath) {
            error_log("Attempting to insert into user_uploads: file_path=$filePath, original_filename=$originalFilename");
            try {
                $upload = $this->database->table('user_uploads')->insert([
                    'file_path' => $filePath,
                    'original_filename' => $originalFilename,
                ]);
                error_log("Insert succeeded, upload ID: " . $upload->id);
                return $upload;
            } catch (\Exception $e) {
                error_log("Database insertion failed: " . $e->getMessage() . " | Stack trace: " . $e->getTraceAsString());
                throw new \Exception("Failed to save upload to database: " . $e->getMessage());
            }
        } else {
            error_log("File upload failed, filePath is empty");
        }
        return null;
    }

    public function getUserUploads(int $modelId): Selection
    {
        return $this->database->table('user_uploads')
            ->where('model_id', $modelId)
            ->order('created_at DESC');
    }

    public function deleteUserUpload(int $uploadId): void
    {
        $upload = $this->database->table('user_uploads')->get($uploadId);
        if ($upload) {
            $filePath = __DIR__ . '/../../' . $upload->file_path;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $upload->delete();
        }
    }

    // ---- IMAGES ----
    public function getModelImages(int $modelId): \Nette\Database\Table\Selection
    {
        return $this->database->table('model_images')
            ->where('model_id', $modelId)
            ->order('created_at DESC');
    }

    public function addModelImage(int $modelId, FileUpload $file): ?ActiveRow
    {
        $uploadDir = 'uploads/models/' . $modelId;
        $imagePath = ImageUploader::uploadImage($file, $uploadDir);
        if ($imagePath) {
            return $this->database->table('model_images')->insert([
                'model_id' => $modelId,
                'image_path' => $imagePath,
            ]);
        }
        return null;
    }

    public function deleteModelImage(int $imageId): void
    {
        $image = $this->database->table('model_images')->get($imageId);
        if ($image) {
            $filePath = __DIR__ . '/../../' . $image->image_path;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $image->delete();
        }
    }

    public function getDefaultImages(): \Nette\Database\Table\Selection
    {
        return $this->database->table('default_images')->order('created_at DESC');
    }

    public function addDefaultImage(FileUpload $file): ?ActiveRow
    {
        $uploadDir = '/uploads/default';
        $imagePath = ImageUploader::uploadImage($file, $uploadDir);
        if ($imagePath) {
            return $this->database->table('default_images')->insert([
                'image_path' => $imagePath,
            ]);
        }
        return null;
    }

    public function deleteDefaultImage(int $imageId): void
    {
        $image = $this->database->table('default_images')->get($imageId);
        if ($image) {
            $filePath = __DIR__ . '/../../' . $image->image_path;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $image->delete();
        }
    }
}