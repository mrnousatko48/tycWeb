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

    // Fetch content for a specific section (banner, durability, customization)
    public function getSectionContent(string $section): array
    {
        return $this->database->table('content_sections')
            ->where('section_name', $section)
            ->order('ordering ASC')
            ->fetchAll();
    }

    // Update content for a section
    public function updateSectionContent(string $section, string $contentType, ?string $contentText = null, ?string $imagePath = null): void
    {
        $this->database->table('content_sections')
            ->where('section_name = ? AND content_type = ?', $section, $contentType)
            ->update([
                'content_text' => $contentText,
                'image_path' => $imagePath
            ]);
    }

    // Fetch gallery images
    public function getGalleryImages(): array
    {
        return $this->database->table('gallery')
            ->order('ordering ASC')
            ->fetchAll();
    }

    // Add a gallery image
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

    // Delete a gallery image
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

    // Update gallery image ordering
    public function updateGalleryOrder(array $order): void
    {
        foreach ($order as $index => $id) {
            $this->database->table('gallery')
                ->where('id', $id)
                ->update(['ordering' => $index + 1]);
        }
    }

    // Fetch contact information
    public function getContactInfo()
    {
        return $this->database->table('contact_info')->fetch();
    }

    // Update contact information
    public function updateContactInfo(int $id, array $values): void
    {
        $this->database->table('contact_info')->get($id)->update($values);
    }

    // Existing methods for form options (unchanged)
    public function getFormOptions(string $type): array
    {
        return $this->database->table($type . '_options')->fetchAll();
    }
}