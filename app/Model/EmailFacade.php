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

    public function getTemplateByName(string $name, string $lang = 'cs'): array
    {
        $template = $this->database->table('email_templates')
            ->where('name', $name)
            ->where('lang', $lang)
            ->fetch();

        if (!$template) {
            error_log("Template '$name' for language '$lang' not found in database.");
            throw new \Exception("Template $name for language $lang was not found.");
        }

        $data = $template->toArray();
        $data['pdf_paths'] = json_decode($data['pdf_paths'] ?? '[]', true);
        return $data;
    }

    public function getAllTemplates(): array
    {
        return $this->database->table('email_templates')
            ->order('id ASC')
            ->fetchAll();
    }

    public function getTemplateById(int $id): ?array
    {
        $row = $this->database->table('email_templates')
            ->where('id', $id)
            ->fetch();
        return $row ? $row->toArray() : null;
    }

    public function updateTemplate(int $id, array $values): void
    {
        $this->database->table('email_templates')
            ->where('id', $id)
            ->update([
                'subject' => $values['subject'],
                'body' => $values['body'],
                'recipient_email' => $values['recipient_email'],
                'admin_phone' => $values['admin_phone'],
                'lang' => $values['lang'] ?? 'cs',
                'updated_at' => new \DateTime(),
            ]);
    }
}