<?php
namespace App\Utils;

use Nette\Http\FileUpload;

class FileUploader
{
    /**
     * Uploads a 3D file to the specified directory.
     *
     * @param FileUpload $file The uploaded file.
     * @param string $uploadDir The target directory (e.g., 'uploads/user_uploads/1').
     * @return string|null The path of the uploaded file, or null on failure.
     * @throws \Exception If the file type is invalid.
     */
    public static function uploadFile(FileUpload $file, string $uploadDir): ?string
    {
        if (!$file->isOk()) {
            return null;
        }

        $basePath = __DIR__ . '/../../www/' . ltrim($uploadDir, '/');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }

        $allowedExtensions = ['stl', 'obj', '3mf', 'ply', 'fbx', 'gltf', 'glb'];
        $ext = strtolower(pathinfo($file->getSanitizedName(), PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            throw new \Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedExtensions));
        }

        $filename = uniqid() . '.' . $ext;
        $filePath = $uploadDir . '/' . $filename;
        $fullPath = __DIR__ . '/../../www/' . ltrim($filePath, '/');

        $file->move($fullPath);
        return '/www/' . ltrim($filePath, '/');
    }
}