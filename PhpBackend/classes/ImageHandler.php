<?php
class ImageHandler
{
    const UPLOAD_DIRECTORY = "../uploads/";
    const MAX_WIDTH = 500;
    const MAX_HEIGHT = 500;

    public function __construct(private $image) {}

    /**
     * Process the upload of the image
     * @return string|null The path to the uploaded image or null if there was an error
     */
    public function processUpload()
    {
        if (!$this->image || $this->image['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $tmpName = $this->image['tmp_name'];
        $name = uniqid() . "_" . basename($this->image['name']);
        $targetPath = self::UPLOAD_DIRECTORY . $name;

        $imageInfo = getimagesize($tmpName);

        if (!$imageInfo) {
            return null;
        }

        [$width, $height, $type] = $imageInfo;

        if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
            $ratio = min(self::MAX_WIDTH / $width, self::MAX_HEIGHT / $height);
            $newWidth = round($width * $ratio);
            $newHeight = round($height * $ratio);

            $src = imagecreatefromstring(file_get_contents($tmpName));
            $dst = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Save compressed version
            imagejpeg($dst, $targetPath);
            imagedestroy($src);
            imagedestroy($dst);
        } else {
            move_uploaded_file($tmpName, $targetPath);
        }

        return "uploads/" . $name;
    }
}
