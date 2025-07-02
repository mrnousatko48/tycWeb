<?php
declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;

final class PageFacade
{
    private Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    public function getSectionContent(string $section): array
    {
        return $this->database->table('page_content')
            ->where('section', $section)
            ->fetchAll();
    }

    public function getFormOptions(string $field_name): array
    {
        return $this->database->table('form_options')
            ->where('field_name', $field_name)
            ->fetchAll();
    }


}