<?php
namespace App\Model;

use Nette\Database\Explorer; 
use Nette\Http\FileUpload;
use App\Utils\ImageUploader;

class PageFacade
{
    private Explorer $database; 
    private string $lang;

    public function __construct(Explorer $database, string $lang = 'cs')
    {
        $this->database = $database;
        $this->lang = $lang;
    }

    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }

    public function getSectionContent(string $sectionName): array
    {
        $contentColumn = $this->lang === 'en' ? 'content_text_en' : 'content_text';
        return $this->database->table($sectionName)
            ->select("id, content_type, COALESCE($contentColumn, content_text) AS content_text, content_text_en, image_path, ordering")
            ->order('ordering')
            ->fetchAll();
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

    public function updateLogo(string $theme, ?FileUpload $image = null): void
    {
        if ($image instanceof FileUpload && $image->isOk()) {
            $currentLogo = $this->database->table('logos')->where('theme', $theme)->fetch();
            $imagePath = ImageUploader::uploadImage($image, 'Uploads/logo', $currentLogo ? $currentLogo->image_path : null);
            $data = ['image_path' => $imagePath];

            $existing = $this->database->table('logos')->where('theme', $theme)->fetch();
            if ($existing) {
                $this->database->table('logos')->where('theme', $theme)->update($data);
            } else {
                $this->database->table('logos')->insert(array_merge($data, ['theme' => $theme]));
            }
        }
    }
    
    public function updateSectionContent(string $section, string $contentType, ?string $contentText = null, ?string $contentTextEn = null, ?string $imagePath = null, ?int $ordering = null): void
    {
        $data = array_filter([
            'content_text' => $contentText,
            'content_text_en' => $contentTextEn,
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

    public function getBannerSection(): object
    {
        $banner = $this->getSectionContent('banner');
        $result = [
            'title' => '',
            'title_en' => '',
            'description' => '',
            'description_en' => '',
            'button_text' => '',
            'button_text_en' => '',
            'button_link' => '',
            'button_link_en' => '',
            'image' => null
        ];
        foreach ($banner as $item) {
            $result[$item->content_type] = $item->content_text ?? $item->image_path;
            $result[$item->content_type . '_en'] = $item->content_text_en ?? '';
        }
        return (object)$result;
    }

    public function updateBannerSection(array $values): void
    {
        if (!empty($values['image']) && $values['image'] instanceof FileUpload && $values['image']->isOk()) {
            $currentImage = $this->getBannerSection()->image ?? null;
            $imagePath = ImageUploader::uploadImage($values['image'], 'Uploads/home', $currentImage);
            if ($imagePath) {
                $this->updateSectionContent('banner', 'image', null, null, $imagePath);
            }
        }
        $fields = [
            'title' => 'title_en',
            'description' => 'description_en',
            'button_text' => 'button_text_en',
            'button_link' => 'button_link_en'
        ];
        foreach ($fields as $field => $fieldEn) {
            if (isset($values[$field]) || isset($values[$fieldEn])) {
                $this->updateSectionContent('banner', $field, $values[$field] ?? null, $values[$fieldEn] ?? null);
            }
        }
    }

    public function getDurabilitySection(): object
    {
        $durability = $this->getSectionContent('durability');
        $result = [
            'title' => '',
            'title_en' => '',
            'description1' => '',
            'description1_en' => '',
            'description2' => '',
            'description2_en' => '',
            'image' => null
        ];
        foreach ($durability as $item) {
            $result[$item->content_type] = $item->content_text ?? $item->image_path;
            $result[$item->content_type . '_en'] = $item->content_text_en ?? '';
        }
        return (object)$result;
    }

    public function updateDurabilitySection(array $values): void
    {
        $image = $values['image'] ?? null;
        if ($image instanceof FileUpload && $image->isOk()) {
            $currentImage = $this->getDurabilitySection()->image ?? null;
            $imagePath = ImageUploader::uploadImage($image, 'Uploads/home', $currentImage);
            $this->updateSectionContent('durability', 'image', null, null, $imagePath);
        }
        $fields = [
            'title' => 'title_en',
            'description1' => 'description1_en',
            'description2' => 'description2_en'
        ];
        foreach ($fields as $field => $fieldEn) {
            if (isset($values[$field]) || isset($values[$fieldEn])) {
                $this->updateSectionContent('durability', $field, $values[$field] ?? null, $values[$fieldEn] ?? null);
            }
        }
    }

    public function getCustomizations(): array
    {
        $titleColumn = $this->lang === 'en' ? 'title_en' : 'title';
        $descColumn = $this->lang === 'en' ? 'description_en' : 'description';
        return $this->database->table('customization')
            ->select("id, COALESCE($titleColumn, title) AS title, COALESCE($descColumn, description) AS description, image_path, ordering")
            ->order('ordering')
            ->fetchAll();
    }

    public function addCustomization(string $title, string $description, FileUpload $image, ?string $titleEn = null, ?string $descriptionEn = null): void
    {
        if (!$image->isOk()) {
            throw new \Exception('Musíte nahrát platný obrázek.');
        }
        $imagePath = ImageUploader::uploadImage($image, 'Uploads/home', null);

        $maxOrdering = $this->database->table('customization')
            ->select('MAX(ordering) AS max_ordering')
            ->fetch()
            ->max_ordering ?? 0;

        $this->database->table('customization')->insert([
            'title' => $title,
            'title_en' => $titleEn,
            'description' => $description,
            'description_en' => $descriptionEn,
            'image_path' => $imagePath,
            'ordering' => $maxOrdering + 1
        ]);
    }

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

    public function getGalleryImages(): array
    {
        $altTextColumn = $this->lang === 'en' ? 'alt_text_en' : 'alt_text';
        return $this->database->table('gallery')
            ->select("id, image, COALESCE($altTextColumn, alt_text) AS alt_text, alt_text_en, ordering")
            ->order('ordering ASC')
            ->fetchAll();
    }

    public function addGalleryImage(array $values): void
    {
        $image = $values['image'] ?? null;
        if (!$image instanceof FileUpload || !$image->isOk()) {
            throw new \Exception('Valid image file is required.');
        }
        $imagePath = ImageUploader::uploadImage($image, 'Uploads/gallery', null);
        $altText = $values['alt_text'] ?? null;
        $altTextEn = $values['alt_text_en'] ?? null;
        $ordering = (int)($values['ordering'] ?? 0);

        $this->database->table('gallery')
            ->where('ordering >= ?', $ordering)
            ->update(['ordering' => $this->database->literal('ordering + 1')]);

        $this->database->table('gallery')->insert([
            'image' => $imagePath,
            'alt_text' => $altText,
            'alt_text_en' => $altTextEn,
            'ordering' => $ordering,
        ]);
    }

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

    public function updateGalleryOrder(array $order): void
    {
        foreach ($order as $index => $id) {
            $this->database->table('gallery')
                ->where('id', $id)
                ->update(['ordering' => $index + 1]);
        }
    }

    public function getContactInfo(): ?object
    {
        $nameColumn = $this->lang === 'en' ? 'name_en' : 'name';
        $addressColumn = $this->lang === 'en' ? 'address_en' : 'address';
        return $this->database->table('contact_info')
            ->select("id, COALESCE($nameColumn, name) AS name, name_en, COALESCE($addressColumn, address) AS address, address_en, ico, phone, email, map_embed")
            ->fetch();
    }

    public function updateContactInfo(int $id, array $values): void
    {
        $this->database->table('contact_info')->get($id)->update($values);
    }

    public function getLegalPage(string $sectionName): ?object
    {
        $titleColumn = $this->lang === 'en' ? 'title_en' : 'title';
        $contentColumn = $this->lang === 'en' ? 'content_en' : 'content';
        return $this->database->table('legal_pages')
            ->select("id, section_name, COALESCE($titleColumn, title) AS title, title_en, COALESCE($contentColumn, content) AS content, content_en, updated_at")
            ->where('section_name', $sectionName)
            ->fetch();
    }

    public function getLegalPages(): array
    {
        $titleColumn = $this->lang === 'en' ? 'title_en' : 'title';
        $contentColumn = $this->lang === 'en' ? 'content_en' : 'content';
        return $this->database->table('legal_pages')
            ->select("id, section_name, COALESCE($titleColumn, title) AS title, title_en, COALESCE($contentColumn, content) AS content, content_en, updated_at")
            ->order('title ASC')
            ->fetchAll();
    }

    public function updateLegalPage(string $sectionName, string $title, string $content, ?string $titleEn = null, ?string $contentEn = null): void
    {
        $data = [
            'section_name' => $sectionName,
            'title' => $title,
            'content' => $content,
            'title_en' => $titleEn,
            'content_en' => $contentEn
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