<?php
declare(strict_types=1);

namespace App\Model;

use Nette\Database\Context;

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
    public function getSectionContent(string $section): array
    {
        return $this->database->table('content_sections')
            ->where('section_name', $section)
            ->order('ordering ASC')
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

        $existing = $this->database->table('content_sections')
            ->where('section_name = ? AND content_type = ?', $section, $contentType)
            ->fetch();

        if ($existing) {
            $this->database->table('content_sections')
                ->where('section_name = ? AND content_type = ?', $section, $contentType)
                ->update($data);
        } else {
            $this->database->table('content_sections')->insert(array_merge($data, [
                'section_name' => $section,
                'content_type' => $contentType,
            ]));
        }
    }

    /**
     * Fetch banner section data.
     */
    public function getBannerSection(): array
    {
        $banner = $this->getSectionContent('banner');
        $result = [];
        foreach ($banner as $item) {
            $result[$item->content_type] = $item->content_text ?? $item->image_path;
        }
        return $result;
    }

    /**
     * Update banner section.
     */
    public function updateBannerSection(?int $id, array $values): void
    {
        $fields = ['title', 'description', 'button_text', 'button_link'];
        foreach ($fields as $field) {
            if (isset($values[$field])) {
                $this->updateSectionContent('banner', $field, $values[$field]);
            }
        }
        if (isset($values['image_path'])) {
            $this->updateSectionContent('banner', 'image', null, $values['image_path']);
        }
    }

    /**
     * Fetch durability section data.
     */
    public function getDurabilitySection(): array
    {
        $durability = $this->getSectionContent('durability');
        $result = [];
        $descriptionCount = 1;
        foreach ($durability as $item) {
            if (strpos($item->content_type, 'description') === 0) {
                $result['description' . $descriptionCount] = $item->content_text;
                $descriptionCount++;
            } else {
                $result[$item->content_type] = $item->content_text ?? $item->image_path;
            }
        }
        return $result;
    }

    /**
     * Update durability section.
     */
    public function updateDurabilitySection(?int $id, array $values): void
    {
        $fields = ['title', 'description1', 'description2'];
        foreach ($fields as $field) {
            if (isset($values[$field])) {
                $contentType = $field === 'description1' ? 'description1' : ($field === 'description2' ? 'description2' : $field);
                $this->updateSectionContent('durability', $contentType, $values[$field]);
            }
        }
        if (isset($values['image_path'])) {
            $this->updateSectionContent('durability', 'image', null, $values['image_path']);
        }
    }

    /**
     * Fetch customization section data.
     */
    public function getCustomizationSection(): array
    {
        $customization = $this->getSectionContent('customization');
        $result = ['features' => [], 'button_text' => '', 'button_link' => ''];
        $featureIndex = 1;
        foreach ($customization as $item) {
            if (strpos($item->content_type, 'feature') === 0) {
                $typeParts = explode('_', $item->content_type);
                $index = $typeParts[1] ?? $featureIndex;
                $type = $typeParts[2] ?? '';
                $result['features'][$index][$type] = $item->content_text ?? $item->image_path;
                if ($type === 'title') {
                    $featureIndex++;
                }
            } else {
                $result[$item->content_type] = $item->content_text ?? $item->image_path;
            }
        }
        return $result;
    }

    /**
     * Fetch a single customization feature by ID.
     */
    public function getCustomizationFeature(int $id): ?object
    {
        $feature = $this->database->table('content_sections')
            ->where('section_name = ? AND id = ?', 'customization', $id)
            ->fetch();
        if (!$feature) {
            return null;
        }
        $index = explode('_', $feature->content_type)[1];
        $related = $this->database->table('content_sections')
            ->where('section_name = ? AND content_type LIKE ?', 'customization', "feature_{$index}%")
            ->fetchAll();
        
        $result = (object)[
            'id' => $feature->id,
            'title' => '',
            'description' => '',
            'image_path' => '',
        ];
        foreach ($related as $item) {
            $type = explode('_', $item->content_type)[2];
            if ($type === 'title') {
                $result->title = $item->content_text;
            } elseif ($type === 'description') {
                $result->description = $item->content_text;
            } elseif ($type === 'image') {
                $result->image_path = $item->image_path;
            }
        }
        return $result;
    }

    /**
     * Update a customization feature.
     */
    public function updateCustomizationFeature(int $id, array $values): void
    {
        $feature = $this->database->table('content_sections')->get($id);
        if (!$feature) {
            return;
        }
        $index = explode('_', $feature->content_type)[1];
        if (isset($values['title'])) {
            $this->updateSectionContent('customization', "feature_{$index}_title", $values['title']);
        }
        if (isset($values['description'])) {
            $this->updateSectionContent('customization', "feature_{$index}_description", $values['description']);
        }
        if (isset($values['image_path'])) {
            $this->updateSectionContent('customization', "feature_{$index}_image", null, $values['image_path']);
        }
    }

    /**
     * Add a new customization feature.
     */
    public function addCustomizationFeature(array $values): void
    {
        $maxIndex = $this->database->table('content_sections')
            ->where('section_name = ? AND content_type LIKE ?', 'customization', 'feature%')
            ->select('MAX(CAST(SUBSTRING(content_type, 8, LOCATE("_", content_type, 8) - 8) AS UNSIGNED)) AS max_index')
            ->fetchField('max_index') ?? 0;
        $newIndex = $maxIndex + 1;

        $this->updateSectionContent('customization', "feature_{$newIndex}_title", $values['title'], null, $newIndex * 10);
        $this->updateSectionContent('customization', "feature_{$newIndex}_description", $values['description'], null, $newIndex * 10 + 1);
        if (isset($values['image_path'])) {
            $this->updateSectionContent('customization', "feature_{$newIndex}_image", null, $values['image_path'], $newIndex * 10 + 2);
        }
        if (isset($values['button_text'])) {
            $this->updateSectionContent('customization', 'button_text', $values['button_text']);
        }
        if (isset($values['button_link'])) {
            $this->updateSectionContent('customization', 'button_link', $values['button_link']);
        }
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
     * Add a new gallery image.
     */
    public function addGalleryImage(string $imagePath, ?string $altText, int $ordering): void
    {
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
        if ($type === 'color') {
            return [];
        }
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
     * Fetch colors by model ID.
     */
    public function getColorsByModel(int $modelId): array
    {
        $model = $this->database->table('models')->get($modelId);
        return $model && $model->colors ? explode(',', $model->colors) : [];
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