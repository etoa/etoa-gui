<?php

declare(strict_types=1);

namespace EtoA\Support;

class ImageUtils
{
    /**
     * @return resource|false
     */
    private static function imageCreateFromFile(string $path, bool $user_functions = false)
    {
        $info = @getimagesize($path);

        if (!$info) {
            return false;
        }

        $functions = array(
            IMAGETYPE_GIF => 'imagecreatefromgif',
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG => 'imagecreatefrompng',
            IMAGETYPE_WBMP => 'imagecreatefromwbmp',
            IMAGETYPE_XBM => 'imagecreatefromwxbm',
        );

        if ($user_functions) {
            $functions[IMAGETYPE_BMP] = 'imagecreatefrombmp';
        }

        if (!isset($functions[$info[2]])) {
            return false;
        }

        if (!function_exists($functions[$info[2]])) {
            return false;
        }

        return $functions[$info[2]]($path);
    }

    /**
     * Resizes a image and save it to a given filename
     */
    public static function resizeImage(
        string $fileFrom,
        string $fileTo,
        int $newMaxWidth = 0,
        int $newMaxHeight = 0,
        string $type = "jpeg"
    ): bool {
        if (!in_array($type, ['png', 'gif', 'jpeg', 'jpg'], true)) {
            return false;
        }

        if ($img = self::imageCreateFromFile($fileFrom)) {
            $width = imagesx($img);
            $height = imagesy($img);
            $resize = false;

            $newWidth = $newMaxWidth;
            $newHeight = $newMaxHeight;
            if ($width > $newMaxWidth) {
                $newWidth = $newMaxWidth;
                $newHeight = (int) ($height * ($newWidth / $width));
                if ($newHeight > $newMaxHeight) {
                    $newHeight = $newMaxHeight;
                    $newWidth = (int) ($width * ($newHeight / $height));
                }
                $resize = true;
            } elseif ($height > $newMaxHeight) {
                $newHeight = $newMaxHeight;
                $newWidth = (int) ($width * ($newHeight / $height));
                $resize = true;
            }

            if ($resize) {
                // resize using appropriate function
                if (GD_VERSION == 2) {
                    $imageId = imagecreatetruecolor($newWidth, $newHeight);

                    imagealphablending($imageId, false);
                    imagesavealpha($imageId, true);
                    $transparent = imagecolorallocatealpha($imageId, 255, 255, 255, 127);
                    imagefilledrectangle($imageId, 0, 0, $newWidth, $newHeight, $transparent);

                    imagecopyresampled($imageId, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                } else {
                    $imageId = imagecreate($newWidth, $newHeight);
                    imagecopyresized($imageId, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                }
                $handle = $imageId;
                // free original image
                imagedestroy($img);
            } else {
                $handle = $img;
            }

            switch ($type) {
                case 'png':
                    imagepng($handle, $fileTo);

                    break;
                case 'gif':
                    imagegif($handle, $fileTo);

                    break;
                case 'jpg':
                case 'jpeg':
                    imagejpeg($handle, $fileTo, 100);

                    break;
                default:
                    throw new \InvalidArgumentException('Unknown image type: ' . $type);
            }

            imagedestroy($handle);

            return true;
        }

        return false;
    }
}
