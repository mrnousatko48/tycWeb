<?php
namespace App\Utils;

use Nette\Localization\Translator;

class ArrayTranslator implements Translator
{
    private array $translations;

    public function __construct(string $lang)
    {
        $file = __DIR__ . '/../lang/' . $lang . '.php';
        $this->translations = file_exists($file) ? include $file : [];
    }

    public function translate($message, ...$parameters): string
    {
        return $this->translations[$message] ?? $message;
    }
}