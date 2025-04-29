<?php

class ImageHandler
{
    const UPLOAD_DIRECTORY = "../uploads/";
    const MAX_WIDTH = 500;
    const MAX_HEIGHT = 500;

    private $image;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function processUpload(): ?string
    {
        if (!$this->image || $this->image['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = self::UPLOAD_DIRECTORY;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalExtension = strtolower(pathinfo($this->image['name'], PATHINFO_EXTENSION));
        $fileName = uniqid('img_', true) . '.' . $originalExtension;
        $targetPath = $uploadDir . $fileName;

        $imageInfo = getimagesize($this->image['tmp_name']);
        if (!$imageInfo) {
            return null;
        }

        [$width, $height, $type] = $imageInfo;

        if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
            $this->resizeImage($width, $height, $type, $this->image['tmp_name'], $targetPath);
        } else {
            move_uploaded_file($this->image['tmp_name'], $targetPath);
        }

        return "uploads/" . $fileName;
    }

    private function resizeImage(int $width, int $height, int $type, string $tmpName, string $targetPath)
    {
        $ratio = min(self::MAX_WIDTH / $width, self::MAX_HEIGHT / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);

        $src = $this->createImageFromType($tmpName, $type);
        if ($src === false) {
            return;
        }

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        if ($type == IMAGETYPE_PNG) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $this->saveImageFromType($dst, $targetPath, $type);

        imagedestroy($src);
        imagedestroy($dst);
    }

    private function createImageFromType(string $tmpName, int $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($tmpName);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($tmpName);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($tmpName);
            default:
                return false;
        }
    }

    private function saveImageFromType($dst, string $targetPath, int $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($dst, $targetPath);
                break;
            case IMAGETYPE_PNG:
                imagepng($dst, $targetPath);
                break;
            case IMAGETYPE_GIF:
                imagegif($dst, $targetPath);
                break;
        }
    }
}
