<?php

namespace App\Model;

use Nette\Database\Context;

class PageFacade
{
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function getSectionContent(string $section): array
    {
        return $this->database->table('content_sections')
            ->where('section_name', $section)
            ->order('ordering ASC')
            ->fetchAll();
    }

    public function updateSectionContent(string $section, string $contentType, ?string $contentText = null, ?string $imagePath = null): void
    {
        $this->database->table('content_sections')
            ->where('section_name = ? AND content_type = ?', $section, $contentType)
            ->update([
                'content_text' => $contentText,
                'image_path' => $imagePath
            ]);
    }

    public function getGalleryImages(): array
    {
        return $this->database->table('gallery')
            ->order('ordering ASC')
            ->fetchAll();
    }

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

    public function updateGalleryOrder(array $order): void
    {
        foreach ($order as $index => $id) {
            $this->database->table('gallery')
                ->where('id', $id)
                ->update(['ordering' => $index + 1]);
        }
    }

    public function getContactInfo()
    {
        return $this->database->table('contact_info')->fetch();
    }

    public function updateContactInfo(int $id, array $values): void
    {
        $this->database->table('contact_info')->get($id)->update($values);
    }

    public function getFormOptions(string $type): array
    {
        if ($type === 'color') {
            return []; // Handled by models table
        }
        return $this->database->table($type . '_options')->fetchAll();
    }

    public function getManufacturers(): array
    {
        return $this->database->table('manufacturers')
            ->order('name ASC')
            ->fetchAll();
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
    
public function getColorsByModel(int $modelId): array
{
    $model = $this->database->table('models')->get($modelId);
    return $model && $model->colors ? explode(',', $model->colors) : [];
}

    public function getManufacturerNameById(int $id): string
    {
        $manufacturer = $this->database->table('manufacturers')->get($id);
        return $manufacturer ? $manufacturer->name : '';
    }

    public function getModelNameById(int $id): string
    {
        $model = $this->database->table('models')->get($id);
        return $model ? $model->name : '';
    }

}