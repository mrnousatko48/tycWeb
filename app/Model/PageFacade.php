<?php
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

    public function getLogos(): array
    {
        $logos = $this->database->table('logos')->fetchAll();
        $result = [
            'light' => '',
            'dark' => ''
        ];
        foreach ($logos as $logo) {
            $result[$logo->theme] = $logo->image_path;
        }
        return $result;
    }

    /**
     * Update logo for a specific theme.
     */
    public function updateLogo(string $theme, ?FileUpload $image = null): void
    {
        if ($image instanceof FileUpload && $image->isOk()) {
            $currentLogo = $this->database->table('logos')->where('theme', $theme)->fetch();
            $imagePath = ImageUploader::uploadImage($image, 'uploads/logo', $currentLogo ? $currentLogo->image_path : null);
            $data = ['image_path' => $imagePath];

            $existing = $this->database->table('logos')->where('theme', $theme)->fetch();
            if ($existing) {
                $this->database->table('logos')->where('theme', $theme)->update($data);
            } else {
                $this->database->table('logos')->insert(array_merge($data, ['theme' => $theme]));
            }
        }
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
        // Handle image upload
        if (!empty($values['image']) && $values['image'] instanceof FileUpload && $values['image']->isOk()) {
            $currentImage = $this->getBannerSection()->image ?? null;
            $imagePath = ImageUploader::uploadImage($values['image'], 'uploads/home', $currentImage);
            if ($imagePath) {
                $this->updateSectionContent('banner', 'image', null, $imagePath);
            }
        }
        // Update other fields
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
            $imagePath = ImageUploader::uploadImage($image, 'uploads/home', $currentImage);
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
     * Fetch all customizations.
     */
    public function getCustomizations(): array
    {
        return $this->database->table('customization')
            ->order('ordering')
            ->fetchAll();
    }

    /**
     * Add a new customization with title, description, and image.
     */
    public function addCustomization(string $title, string $description, FileUpload $image): void
    {
        if (!$image->isOk()) {
            throw new \Exception('Musíte nahrát platný obrázek.');
        }
        $imagePath = ImageUploader::uploadImage($image, 'uploads/home', null);

        $maxOrdering = $this->database->table('customization')
            ->select('MAX(ordering) AS max_ordering')
            ->fetch()
            ->max_ordering ?? 0;

        $this->database->table('customization')->insert([
            'title' => $title,
            'description' => $description,
            'image_path' => $imagePath,
            'ordering' => $maxOrdering + 1
        ]);
    }

    /**
     * Delete a customization by ID.
     */
    public function deleteCustomization(int $id): void
    {
        $customization = $this->database->table('customization')->get($id);
        if ($customization) {
            if ($customization->image_path && file_exists(__DIR__ . '/../../www' . $customization->image_path)) {
                unlink(__DIR__ . '/../../www' . $customization->image_path);
            }
            $customization->delete();
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
     * Add a new gallery image with form values, including image upload.
     */
    public function addGalleryImage(array $values): void
    {
        $image = $values['image'] ?? null;
        if (!$image instanceof FileUpload || !$image->isOk()) {
            throw new \Exception('Valid image file is required.');
        }
        $imagePath = ImageUploader::uploadImage($image, 'uploads/gallery', null);
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
            if ($image->image && file_exists(__DIR__ . '/../../' . $image->image)) {
                unlink(__DIR__ . '/../../' . $image->image);
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

    public function getLegalPage(string $sectionName): ?object
    {
        return $this->database->table('legal_pages')
            ->where('section_name', $sectionName)
            ->fetch();
    }

    /**
     * Fetch all legal pages.
     */
    public function getLegalPages(): array
    {
        return $this->database->table('legal_pages')
            ->order('title ASC')
            ->fetchAll();
    }

    /**
     * Update or insert legal page content.
     */
    public function updateLegalPage(string $sectionName, string $title, string $content): void
    {
        $data = [
            'section_name' => $sectionName,
            'title' => $title,
            'content' => $content
        ];

        $existing = $this->database->table('legal_pages')
            ->where('section_name', $sectionName)
            ->fetch();

        if ($existing) {
            $this->database->table('legal_pages')
                ->where('section_name', $sectionName)
                ->update($data);
        } else {
            $this->database->table('legal_pages')->insert($data);
        }
    }
}