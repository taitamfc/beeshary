<?php
/**
* 2010-2016 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpHelper extends ObjectModel
{
    public static function randomImageName($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $rand = '';

        for ($i = 0; $i < $length; ++$i) {
            $rand = $rand.$characters[mt_rand(0, Tools::strlen($characters) - 1)];
        }

        return $rand;
    }

    /**
     * [uploadMpImages uploar image for marketplace].
     *
     * @param [string] $dir    [path to upload]
     * @param bool     $width  [image width]
     * @param bool     $height [image height]
     *
     * @return [type] [error/success image id]
     */
    public static function uploadMpImages($image, $dirAbsPath, $width = false, $height = false)
    {
        if (!$image) {
            return false;
        }

        if ($image['error']) {
            return $image['error'];
        }

        if (!$width) {
            $width = 200;
        }

        if (!$height) {
            $height = 200;
        }

        if (!ImageManager::isCorrectImageFileExt($image['name'])) {
            return 2;
        }

        return ImageManager::resize($image['tmp_name'], $dirAbsPath, $width, $height);
    }

    /**
     * [insertLangIdinAllTables - Create a new row with default lang value when admin add new language]
     * @param  [type] $newIdLang [description]
     * @param  [type] $lang_tables [description]
     * @return [type]              [description]
     */
    public static function insertLangIdinAllTables($newIdLang, $langTables)
    {
        if ($langTables) {
            foreach ($langTables as $tables) {
                $tableIds = Db::getInstance()->executeS('SELECT `id` FROM `'._DB_PREFIX_.$tables.'`');
                if ($tableIds) {
                    foreach ($tableIds as $tableId) {
                        $tableLangs = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$tables.'_lang`
                            WHERE `id` = '.$tableId['id'].'
                            AND `id_lang` = '.(int) Configuration::get('PS_LANG_DEFAULT'));

                        if ($tableLangs) {
                            $tableValue = '';
                            foreach ($tableLangs as $key => $value) {
                                if ($key == 'id') {
                                    $tableValue = "'".$value."'";
                                } elseif ($key == 'id_lang') {
                                    $tableValue = $tableValue.', '."'".$newIdLang."'";
                                } else {
                                    $content = str_replace("'", "\'", $value);
                                    $tableValue = $tableValue.', '."'".$content."'";
                                }
                            }
                        }

                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.$tables.'_lang` VALUES ('.$tableValue.')');
                    }
                }
            }
        }
    }

    /**
     * [assignDefaultLang - Set default lang at every form of module according to configuration multi-lang]
     * @param  [type] $idSeller [description]
     * @return [type]               [description]
     */
    public static function assignDefaultLang($idSeller)
    {
        if (Configuration::get('MP_MULTILANG_ADMIN_APPROVE')) {
            Context::getContext()->smarty->assign('allow_multilang', 1);
            $curruntLang = SellerInfoDetail::findSellerDefaultLang($idSeller);
        } else {
            Context::getContext()->smarty->assign('allow_multilang', 0);

            if (Configuration::get('MP_MULTILANG_DEFAULT_LANG') == '1') {
                $curruntLang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('MP_MULTILANG_DEFAULT_LANG') == '2') {
                $curruntLang = SellerInfoDetail::findSellerDefaultLang($idSeller);
            }
        }

        // assign image max size limit
        MpHelper::assignPsFileMaxSize();

        Context::getContext()->smarty->assign('languages', Language::getLanguages());
        Context::getContext()->smarty->assign('total_languages', count(Language::getLanguages()));
        Context::getContext()->smarty->assign('current_lang', Language::getLanguage((int) $curruntLang));
        Context::getContext()->smarty->assign('multi_lang', Configuration::get('MP_MULTILANG_ADMIN_APPROVE'));
        Context::getContext()->smarty->assign('multi_def_lang_off', Configuration::get('MP_MULTILANG_DEFAULT_LANG'));
    }

    public static function assignPsFileMaxSize()
    {
        $objUploader = new Uploader();
        $psUploaderSize = $objUploader->getPostMaxSizeBytes();

        Context::getContext()->smarty->assign('psUploaderSize', $psUploaderSize);
        Context::getContext()->smarty->assign('post_max_size', ini_get('post_max_size'));
    }

    public static function uploadCropImages($sTempFileName)
    {
        //$iWidth = $iHeight = 500; // desired image result dimensions
        $iJpgQuality = 100;

        $aSize = getimagesize($sTempFileName); // try to obtain image info
        if (!$aSize) {
            @unlink($sTempFileName);

            return;
        }
        $iWidth = $aSize[0];
        $iHeight = $aSize[1];
        // check for image type
        switch ($aSize[2]) {
            case IMAGETYPE_JPEG:
                $sExt = '.jpg';

                // create a new image from file
                $vImg = @imagecreatefromjpeg($sTempFileName);
                break;
            case IMAGETYPE_PNG:
                $sExt = '.png';

                // create a new image from file
                $vImg = @imagecreatefrompng($sTempFileName);
                break;
            default:
                @unlink($sTempFileName);

                return;
        }

        // create a new true color image
        $vDstImg = @imagecreatetruecolor($iWidth, $iHeight);

        // copy and resize part of an image with resampling
        imagecopyresampled($vDstImg, $vImg, 0, 0, (int) Tools::getValue('x1'), (int) Tools::getValue('y1'), $iWidth, $iHeight, (int) Tools::getValue('w'), (int) Tools::getValue('h'));

        // define a result image filename
        $sResultFileName = $sTempFileName.$sExt;

        // output image to file
        imagejpeg($vDstImg, $sResultFileName, $iJpgQuality);
        @unlink($sTempFileName);

        $imagename = preg_replace('/^.+[\\\\\\/]/', '', $sResultFileName);

        if ($imagename) {
            return $imagename;
        }
    }

    public static function getSupperAdmin()
    {
        $data = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'employee` ORDER BY `id_employee`');
        if ($data) {
            foreach ($data as $emp) {
                $employee = new Employee($emp['id_employee']);
                if ($employee->isSuperAdmin()) {
                    return $emp['id_employee'];
                }
            }
        }

        return false;
    }

    /**
     * To avoid caching of image
     * @return [int] [timestamp]
     */
    public static function getTimestamp()
    {
        $date = new DateTime();
        return $date->getTimestamp();
    }
}
