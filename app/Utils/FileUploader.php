<?php
namespace App\Utils;

use Nette\Http\FileUpload;

class FileUploader
{
    public static function uploadFile(FileUpload $file, string $uploadDir): ?string
    {
        if (!$file->isOk()) {
            error_log('File upload failed: ' . $file->getError());
            return null;
        }

        $basePath = __DIR__ . '/../../www/' . ltrim($uploadDir, '/');
        if (!is_dir($basePath) && !mkdir($basePath, 0777, true)) {
            error_log('Failed to create directory: ' . $basePath);
            throw new \Exception('Failed to create upload directory.');
        }

        $allowedExtensions = ['stl', 'obj', '3mf', 'ply', 'fbx', 'gltf', 'glb'];
        $ext = strtolower(pathinfo($file->getSanitizedName(), PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            throw new \Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedExtensions));
        }

        $filename = uniqid() . '.' . $ext;
        $filePath = $uploadDir . '/' . $filename;
        $fullPath = __DIR__ . '/../../www/' . ltrim($filePath, '/');

        try {
            $file->move($fullPath);
            if (!file_exists($fullPath)) {
                error_log('File move failed: ' . $fullPath);
                throw new \Exception('Failed to move file to destination.');
            }
            error_log('File moved successfully to: ' . $fullPath);
            return '/www/' . ltrim($filePath, '/');
        } catch (\Exception $e) {
            error_log('Error moving file: ' . $e->getMessage());
            throw $e;
        }
    }
}