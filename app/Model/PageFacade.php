<?php
declare(strict_types=1);

namespace App\Model;

use Nette\Database\Context;
use Nette\Http\FileUpload;
use App\Utils\ImageUploader;

/**
 * PageFacade handles all data operations for page content, including database interactions and image uploads.
 */
class PageFacade
{
    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * Fetch all content for a given section, ordered by ordering.
     */
    public function getSectionContent(string $sectionName): array
    {
        return $this->database->table($sectionName)
            ->order('ordering')
            ->fetchAll();
    }

    /**
     * Update or insert content for a specific section and content type.
     */
    public function updateSectionContent(string $section, string $contentType, ?string $contentText = null, ?string $imagePath = null, ?int $ordering = null): void
    {
        $data = array_filter([
            'content_text' => $contentText,
            'image_path' => $imagePath,
            'ordering' => $ordering,
        ]);

        $existing = $this->database->table($section)
            ->where('content_type', $contentType)
            ->fetch();

        if ($existing) {
            $this->database->table($section)
                ->where('content_type', $contentType)
                ->update($data);
        } else {
            $this->database->table($section)->insert(array_merge($data, [
                'content_type' => $contentType,
            ]));
        }
    }

    /**
     * Fetch banner section data.
     */
    public function getBannerSection(): object
    {
        $banner = $this->getSectionContent('banner');
        $result = [
            'title' => '',
            'description' => '',
            'button_text' => '',
            'button_link' => '',
            'image' => null
        ];
        foreach ($banner as $item) {
            $result[$item->content_type] = $item->content_text ?? $item->image_path;
        }
        return (object)$result;
    }

    /**
     * Update banner section with form values, including image upload.
     */
    public function updateBannerSection(array $values): void
    {
        $image = $values['image'] ?? null;
        if ($image instanceof FileUpload && $image->isOk()) {
            $currentImage = $this->getBannerSection()->image ?? null;
            $imagePath = ImageUploader::uploadImage($image, 'Uploads/home', $currentImage);
            $this->updateSectionContent('banner', 'image', null, $imagePath);
        }
        $fields = ['title', 'description', 'button_text', 'button_link'];
        foreach ($fields as $field) {
            if (isset($values[$field])) {
                $this->updateSectionContent('banner', $field, $values[$field]);
            }
        }
    }

    /**
     * Fetch durability section data.
     */
    public function getDurabilitySection(): object
    {
        $durability = $this->getSectionContent('durability');
        $result = [
            'title' => '',
            'description1' => '',
            'description2' => '',
            'image' => null
        ];
        foreach ($durability as $item) {
            $result[$item->content_type] = $item->content_text ?? $item->image_path;
        }
        return (object)$result;
    }

    /**
     * Update durability section with form values, including image upload.
     */
    public function updateDurabilitySection(array $values): void
    {
        $image = $values['image'] ?? null;
        if ($image instanceof FileUpload && $image->isOk()) {
            $currentImage = $this->getDurabilitySection()->image ?? null;
            $imagePath = ImageUploader::uploadImage($image, 'Uploads/home', $currentImage);
            $this->updateSectionContent('durability', 'image', null, $imagePath);
        }
        $fields = ['title', 'description1', 'description2'];
        foreach ($fields as $field) {
            if (isset($values[$field])) {
                $this->updateSectionContent('durability', $field, $values[$field]);
            }
        }
    }

    /**
     * Fetch customization section data.
     */
    public function getCustomizationSection(): object
    {
        $rows = $this->database->table('customization')
            ->order('ordering')
            ->fetchAll();

        $section = [
            'title' => '',
            'button_text' => '',
            'button_link' => '',
            'features' => []
        ];

        $features = [];
        foreach ($rows as $row) {
            if ($row->content_type === 'title') {
                $section['title'] = $row->content_text;
            } elseif ($row->content_type === 'button_text') {
                $section['button_text'] = $row->content_text;
            } elseif ($row->content_type === 'button_link') {
                $section['button_link'] = $row->content_text;
            } elseif (preg_match('/feature(\d+)_title/', $row->content_type, $matches)) {
                $featureId = $matches[1];
                $features[$featureId]['id'] = $row->id;
                $features[$featureId]['title'] = $row->content_text;
            } elseif (preg_match('/feature(\d+)_description/', $row->content_type, $matches)) {
                $featureId = $matches[1];
                $features[$featureId]['id'] = $row->id;
                $features[$featureId]['description'] = $row->content_text;
            } elseif (preg_match('/feature(\d+)_image/', $row->content_type, $matches)) {
                $featureId = $matches[1];
                $features[$featureId]['id'] = $row->id;
                $features[$featureId]['image_path'] = $row->image_path;
            }
        }

        $section['features'] = array_values(array_map(function ($feature) {
            return (object)[
                'id' => $feature['id'] ?? null,
                'title' => $feature['title'] ?? '',
                'description' => $feature['description'] ?? '',
                'image_path' => $feature['image_path'] ?? null
            ];
        }, $features));

        return (object)$section;
    }

    /**
     * Fetch a single customization feature by ID.
     */
    public function getCustomizationFeature(int $id): ?object
    {
        $row = $this->database->table('customization')->get($id);
        if (!$row || !preg_match('/feature(\d+)_/', $row->content_type, $matches)) {
            return null;
        }

        $featureId = $matches[1];
        $featureRows = $this->database->table('customization')
            ->where('content_type LIKE ?', "feature{$featureId}%")
            ->fetchAll();

        $feature = [
            'id' => $id,
            'title' => '',
            'description' => '',
            'image_path' => null
        ];

        foreach ($featureRows as $featureRow) {
            if ($featureRow->content_type === "feature{$featureId}_title") {
                $feature['title'] = $featureRow->content_text;
            } elseif ($featureRow->content_type === "feature{$featureId}_description") {
                $feature['description'] = $featureRow->content_text;
            } elseif ($featureRow->content_type === "feature{$featureId}_image") {
                $feature['image_path'] = $featureRow->image_path;
            }
        }

        return (object)$feature;
    }

    /**
     * Update a customization feature with form values, including image upload.
     */
    public function updateCustomizationFeature(int $id, array $values): void
    {
        $row = $this->database->table('customization')->get($id);
        if (!$row || !preg_match('/feature(\d+)_/', $row->content_type, $matches)) {
            throw new \Exception('Feature not found');
        }

        $featureId = $matches[1];
        $image = $values['image'] ?? null;
        $imagePath = null;
        if ($image instanceof FileUpload && $image->isOk()) {
            $currentFeature = $this->getCustomizationFeature($id);
            $imagePath = ImageUploader::uploadImage($image, 'Uploads/home', $currentFeature->image_path ?? null);
        }

        $this->database->table('customization')
            ->where('content_type LIKE ?', "feature{$featureId}%")
            ->delete();

        $this->database->table('customization')->insert([
            ['content_type' => "feature{$featureId}_title", 'content_text' => $values['title'], 'ordering' => $featureId * 3 - 1],
            ['content_type' => "feature{$featureId}_description", 'content_text' => $values['description'], 'ordering' => $featureId * 3],
            ['content_type' => "feature{$featureId}_image", 'image_path' => $imagePath ?? ($this->getCustomizationFeature($id)->image_path ?? null), 'ordering' => $featureId * 3 + 1]
        ]);
    }

    /**
     * Add a new customization feature with form values, including image upload.
     */
    public function addCustomizationFeature(array $values): void
    {
        $image = $values['image'] ?? null;
        if (!$image instanceof FileUpload || !$image->isOk()) {
            throw new \Exception('Valid image file is required.');
        }
        $imagePath = ImageUploader::uploadImage($image, 'Uploads/home', null);

        $maxFeature = $this->database->table('customization')
            ->where('content_type LIKE ?', 'feature%_title')
            ->select('MAX(SUBSTRING(content_type, 8, 1)) AS max_feature')
            ->fetchField('max_feature');

        $featureId = ($maxFeature ? (int)$maxFeature + 1 : 1);

        $this->database->table('customization')->insert([
            ['content_type' => "feature{$featureId}_title", 'content_text' => $values['title'], 'ordering' => $featureId * 3 - 1],
            ['content_type' => "feature{$featureId}_description", 'content_text' => $values['description'], 'ordering' => $featureId * 3],
            ['content_type' => "feature{$featureId}_image", 'image_path' => $imagePath, 'ordering' => $featureId * 3 + 1]
        ]);
    }

    /**
     * Delete a customization feature by ID.
     */
    public function deleteCustomizationFeature(int $id): void
    {
        $row = $this->database->table('customization')->get($id);
        if ($row && preg_match('/feature(\d+)_/', $row->content_type, $matches)) {
            $featureId = $matches[1];
            $this->database->table('customization')
                ->where('content_type LIKE ?', "feature{$featureId}%")
                ->delete();
        }
    }

    /**
     * Update customization section with form values.
     */
    public function updateCustomizationSection(array $values): void
    {
        $this->database->table('customization')
            ->where('content_type', ['title', 'button_text', 'button_link'])
            ->delete();

        $this->database->table('customization')->insert([
            ['content_type' => 'title', 'content_text' => $values['title'], 'ordering' => 1],
            ['content_type' => 'button_text', 'content_text' => $values['button_text'], 'ordering' => 11],
            ['content_type' => 'button_link', 'content_text' => $values['button_link'], 'ordering' => 12]
        ]);
    }

    /**
     * Fetch gallery images.
     */
    public function getGalleryImages(): array
    {
        return $this->database->table('gallery')
            ->order('ordering ASC')
            ->fetchAll();
    }

    /**
     * Add a new gallery image with form values, including image upload.
     */
    public function addGalleryImage(array $values): void
    {
        $image = $values['image'] ?? null;
        if (!$image instanceof FileUpload || !$image->isOk()) {
            throw new \Exception('Valid image file is required.');
        }
        $imagePath = ImageUploader::uploadImage($image, 'Uploads/gallery', null);
        $altText = $values['alt_text'] ?? null;
        $ordering = (int)($values['ordering'] ?? 0);

        $this->database->table('gallery')
            ->where('ordering >= ?', $ordering)
            ->update(['ordering' => $this->database->literal('ordering + 1')]);

        $this->database->table('gallery')->insert([
            'image' => $imagePath,
            'alt_text' => $altText,
            'ordering' => $ordering,
        ]);
    }

    /**
     * Delete a gallery image.
     */
    public function deleteGalleryImage(int $id): void
    {
        $image = $this->database->table('gallery')->get($id);
        if ($image) {
            if ($image->image && file_exists(__DIR__ . '/../../web' . $image->image)) {
                unlink(__DIR__ . '/../../web' . $image->image);
            }
            $image->delete();
        }
    }

    /**
     * Update gallery image order.
     */
    public function updateGalleryOrder(array $order): void
    {
        foreach ($order as $index => $id) {
            $this->database->table('gallery')
                ->where('id', $id)
                ->update(['ordering' => $index + 1]);
        }
    }

    /**
     * Fetch contact information.
     */
    public function getContactInfo(): ?object
    {
        return $this->database->table('contact_info')->fetch();
    }

    /**
     * Update contact information.
     */
    public function updateContactInfo(int $id, array $values): void
    {
        $this->database->table('contact_info')->get($id)->update($values);
    }

    /**
     * Fetch form options (for colors, etc.).
     */
    public function getFormOptions(string $type): array
    {
        return $this->database->table($type . '_options')->fetchAll();
    }

    /**
     * Fetch manufacturers.
     */
    public function getManufacturers(): array
    {
        return $this->database->table('manufacturers')
            ->order('name ASC')
            ->fetchAll();
    }

    /**
     * Fetch models by manufacturer ID.
     */
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

    /**
     * Fetch model by ID.
     */
    public function getModelById(int $id): ?object
    {
        return $this->database->table('models')->get($id);
    }

    /**
     * Fetch colors by model ID.
     */
    public function getColorsByModel(int $modelId): array
    {
        $modelColors = $this->database->table('model_colors')
            ->where('model_id', $modelId)
            ->fetchPairs('color_id', null);

        if (!$modelColors) {
            return [];
        }

        $colors = $this->database->table('colors')
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

    /**
     * Fetch features by model ID, including prices.
     */
    public function getFeaturesByModel(int $modelId): array
    {
        $features = $this->database->table('model_features')
            ->where('model_id', $modelId)
            ->fetchPairs('feature_id', null);

        if (!$features) {
            return [];
        }

        $featureData = $this->database->table('features')
            ->where('id', array_keys($features))
            ->fetchPairs('id', 'name');

        $options = $this->database->table('feature_options')
            ->where('feature_id', array_keys($features))
            ->order('feature_id')
            ->fetchAll();

        $result = [];
        foreach ($options as $option) {
            $featureName = $featureData[$option->feature_id];
            $result[$featureName][] = [
                'name' => $option->name,
                'price' => (float)$option->price
            ];
        }

        return $result;
    }

    /**
     * Fetch manufacturer name by ID.
     */
    public function getManufacturerNameById(int $id): string
    {
        $manufacturer = $this->database->table('manufacturers')->get($id);
        return $manufacturer ? $manufacturer->name : '';
    }

    /**
     * Fetch model name by ID.
     */
    public function getModelNameById(int $id): string
    {
        $model = $this->database->table('models')->get($id);
        return $model ? $model->name : '';
    }
}