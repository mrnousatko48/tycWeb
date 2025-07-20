<?php
declare(strict_types=1);

namespace App\Model;

use Nette\Database\Context;

class EmailFacade
{
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function getTemplateByName(string $name): array
    {
        $template = $this->database->table('email_templates')->where('name', $name)->fetch();
        if (!$template) {
            throw new \Exception("Šablona $name nebyla nalezena.");
        }
        $data = $template->toArray();
        $data['pdf_paths'] = json_decode($data['pdf_paths'] ?? '[]', true);
        return $data;
    }

    public function updateTemplate(string $name, array $data): void
    {
        $this->database->table('email_templates')->where('name', $name)->update($data);
    }
}