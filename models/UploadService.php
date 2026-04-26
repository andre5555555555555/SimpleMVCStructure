<?php 
class UploadService {
    private const MAX_FILE_SIZE = 5242880;

    function upload($file){
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Image upload failed.");
        }

        if (!is_uploaded_file($file["tmp_name"])) {
            throw new RuntimeException("Invalid upload request.");
        }

        if (($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            throw new RuntimeException("Image file is too large.");
        }

        if(!in_array($ext, $allowed, true)){
            throw new RuntimeException("Invalid file type.");
        }

        if ($ext === 'webp') {
            $newName = uniqid("img_", true) . ".webp";
            $path = "images/" . $newName;

            if (!move_uploaded_file($file["tmp_name"], $path)) {
                throw new RuntimeException("Failed to save uploaded image.");
            }

            return $path;
        }

        if (function_exists('imagewebp')) {
            $image = $this->createImageResource($file["tmp_name"], $ext);
            if ($image !== false) {
                $newName = uniqid("img_", true) . ".webp";
                $path = "images/" . $newName;

                if (imagewebp($image, $path, 82)) {
                    imagedestroy($image);
                    return $path;
                }

                imagedestroy($image);
            }
        }

        $newName = uniqid("img_", true) . "." . $ext;
        $path = "images/" . $newName;

        if (!move_uploaded_file($file["tmp_name"], $path)) {
            throw new RuntimeException("Failed to save uploaded image.");
        }

        return $path;
    }

    private function createImageResource($tmpPath, $extension){
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($tmpPath) : false;
            case 'png':
                if (!function_exists('imagecreatefrompng')) {
                    return false;
                }

                $image = @imagecreatefrompng($tmpPath);
                if ($image !== false) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                return $image;
            default:
                return false;
        }
    }
}
