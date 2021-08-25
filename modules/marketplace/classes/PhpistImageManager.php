<?php
/**
* 2017 - 2018 PHPIST
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future.
*
*  @author    PHPIST <yassine.belkaid87@gmail.com>
*  @copyright 2017 - 2018 PHPIST
*  @license   Do not distribute this module without permission from the author
*/

/**
* ImageManager Model
*/
class PhpistImageManager
{
    
    public function __construct()
    {
        
    }

    public static function crop($source_path, $dest_img, $desired_width, $desired_height, $restrictions)
    {
        list($source_width, $source_height, $source_type) = getimagesize($source_path);

        switch ($source_type) {
            case IMAGETYPE_GIF:
                $source_gdim = imagecreatefromgif($source_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gdim = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $source_gdim = imagecreatefrompng($source_path);
                break;
        }

        $source_aspect_ratio = $source_width / $source_height;
        $desired_aspect_ratio = $desired_width / $desired_height;

        if ($source_aspect_ratio > $desired_aspect_ratio) {
            /*
             * Triggered when source image is wider
             */
            $temp_height = $desired_height;
            $temp_width = ( int ) ($desired_height * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = $desired_width;
            $temp_height = ( int ) ($desired_width / $source_aspect_ratio);
        }

        /*
         * Resize the image into a temporary GD image
         */
        $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled(
            $temp_gdim,
            $source_gdim,
            0, 0,
            0, 0,
            $temp_width, $temp_height,
            $source_width, $source_height
        );

        /*
         * Copy cropped region from temporary image into the desired GD image
         */
        $x0 = ($temp_width - $desired_width) / 2;
        $y0 = ($temp_height - $desired_height) / 2;
        $desired_gdim = imagecreatetruecolor($desired_width, $desired_height);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            $desired_width, $desired_height
        );

        $ps_png_quality = 7;
        $ps_jpeg_quality = 90;

        switch ($source_type) {
            case IMAGETYPE_GIF:
                $success = imagegif($desired_gdim, $dest_img);
            break;

            case IMAGETYPE_PNG:
                $quality = ($ps_png_quality === false ? 7 : $ps_png_quality);
                $success = imagepng($desired_gdim, $dest_img, (int)$quality);
            break;

            case IMAGETYPE_JPEG:
            default:
                $quality = ($ps_jpeg_quality === false ? 90 : $ps_jpeg_quality);
                imageinterlace($desired_gdim, 1); /// make it PROGRESSIVE
                $success = imagejpeg($desired_gdim, $dest_img, (int)$quality);
            break;
        }
        imagedestroy($desired_gdim);
        @chmod($dest_img, 0664);
        return $success;
    }

    public static function uploadCroppedImageBlb($dist, $file, $width, $height)
    {
        $sanitizeImg = str_replace('data:image/png;base64,', '', $file); 
        $base64      = str_replace(' ', '+', $sanitizeImg); 
        $imageBody   = base64_decode($base64);
        $data = imagecreatefromstring($imageBody);

        ImageManager::createWhiteImage($width, $height);
        $imgSuccess = ImageManager::write('png', $data, $dist);

        return $imgSuccess;
    }
}
