<?php
namespace App\Utils;

use Nette\Http\FileUpload;

class ImageUploader
{
    /**
     * Uploads and converts an image to WebP format, correcting orientation for JPEG images.
     * Deletes the old image if provided.
     *
     * @param FileUpload $file The uploaded file.
     * @param string $uploadDir The target directory (e.g., 'uploads/home').
     * @param string|null $oldImagePath Path to the old image to delete.
     * @return string|null The path of the uploaded (converted) image, or null on failure.
     */
    public static function uploadImage(FileUpload $file, string $uploadDir, ?string $oldImagePath = null): ?string
    {
        // Validate the file type
        if (!$file->isOk() || !$file->isImage() || !in_array($file->getContentType(), ['image/jpeg', 'image/png', 'image/gif'])) {
            return null;
        }

        // Ensure upload directory exists
        $absoluteUploadDir = __DIR__ . '/../../www/' . ltrim($uploadDir, '/');
        if (!is_dir($absoluteUploadDir)) {
            mkdir($absoluteUploadDir, 0777, true);
        }

        // Generate a unique name (without extension, as we'll convert to webp)
        $uniqueName = uniqid() . '_' . pathinfo($file->getSanitizedName(), PATHINFO_FILENAME);
        // Temporary destination: preserve original file extension for now
        $tempPath = $absoluteUploadDir . '/' . $uniqueName . '.' . pathinfo($file->getSanitizedName(), PATHINFO_EXTENSION);
        // Move the uploaded file to the temporary path
        $file->move($tempPath);

        // Load the image using GD based on its type
        $imageInfo = getimagesize($tempPath);
        if ($imageInfo === false) {
            unlink($tempPath);
            return null;
        }
        $type = $imageInfo[2];
        switch ($type) {
            case IMAGETYPE_JPEG:
                $imageResource = imagecreatefromjpeg($tempPath);
                break;
            case IMAGETYPE_PNG:
                $imageResource = imagecreatefrompng($tempPath);
                break;
            case IMAGETYPE_GIF:
                $imageResource = imagecreatefromgif($tempPath);
                break;
            default:
                unlink($tempPath);
                return null;
        }

        if ($imageResource === false) {
            unlink($tempPath);
            return null;
        }

        // Correct orientation for JPEG images
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($tempPath);
            if ($exif && isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $imageResource = imagerotate($imageResource, 180, 0);
                        break;
                    case 6:
                        $imageResource = imagerotate($imageResource, -90, 0);
                        break;
                    case 8:
                        $imageResource = imagerotate($imageResource, 90, 0);
                        break;
                }
            }
        }

        // Define new filename with .webp extension
        $newFileName = $uniqueName . '.webp';
        $newRelativePath = $absoluteUploadDir . '/' . $newFileName;
        $newDbPath = '/www/' . ltrim($uploadDir, '/') . '/' . $newFileName;

        // Convert and save image as WebP (quality set to 80)
        if (!imagewebp($imageResource, $newRelativePath, 80)) {
            imagedestroy($imageResource);
            unlink($tempPath);
            return null;
        }
        imagedestroy($imageResource);

        // Remove the original file
        unlink($tempPath);

        // Delete the old image if it exists
        if ($oldImagePath && file_exists(__DIR__ . '/../../www' . $oldImagePath)) {
            unlink(__DIR__ . '/../../www' . $oldImagePath);
        }

        return $newDbPath;
    }
}