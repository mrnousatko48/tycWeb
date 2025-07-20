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
                'updated_at' => new \DateTime(),
            ]);
    }
}