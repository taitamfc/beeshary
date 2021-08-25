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

class MarketplaceUploadCropImageModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->display_header = false;
        $this->display_footer = false;
        
        $objUploader = new Uploader();
        $psUploaderSize = $objUploader->getPostMaxSizeBytes();
        
        // Upload image through Drag & drop
        if (isset($_FILES['dropImage'])) {
            if ($_FILES['dropImage']['size'] < $psUploaderSize) {
                if (!ImageManager::isCorrectImageFileExt($_FILES['dropImage']['name'])) {
                    echo '1';
                    die;
                } else {
                    $productUpload = Tools::getValue('prod_upload');
                    if ($productUpload) {
                        $validImage = 1; //no validation of size if upload any product
                    } else {
                        $validImage = $this->validateShopLogoSize($_FILES['dropImage']);
                    }

                    if ($validImage) {
                        //Store temporarily images at dropimage directory
                        $sourcePath = $_FILES['dropImage']['tmp_name'];
                        $photoName = MpHelper::randomImageName();
                        $targetPath = _PS_MODULE_DIR_.'marketplace/views/img/dropimage/'.$photoName.'.jpg';
                        ImageManager::resize($sourcePath, $targetPath);

                        $_FILES['dropImage']['photo_name'] = $photoName;
                        $dropimage = Tools::jsonEncode($_FILES['dropImage']);
                        echo $dropimage;
                        die;
                    } else {
                        echo '2';
                        die;
                    }
                }
            }
        }

        $imgfile = Tools::jsonDecode(Tools::getValue('dropImage_file'), true);
        if (isset($imgfile)) {
            if (!$imgfile['error'] && $imgfile['size'] < $psUploaderSize) {
                $dropimgPath = _PS_MODULE_DIR_.'marketplace/views/img/dropimage/'.$imgfile['photo_name'].'.jpg';
                $sTempFileName = _PS_MODULE_DIR_.'marketplace/views/img/uploadimage/'.$imgfile['photo_name'];

                ImageManager::resize($dropimgPath, $sTempFileName);

                // change file permission to 644
                @chmod($sTempFileName, 0644);

                if (file_exists($sTempFileName) && filesize($sTempFileName) > 0) {
                    //Crop images upload
                    $imagename = MpHelper::uploadCropImages($sTempFileName);

                    //Delete image from dropimage directory
                    @unlink($dropimgPath);

                    echo $imagename; //return image name
                    die;
                }
            }
        }

        if (isset($_FILES['image_file'])) {
            if (!$_FILES['image_file']['error'] && $_FILES['image_file']['size'] < $psUploaderSize) {
                if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
                    $uniqueImgName = MpHelper::randomImageName();
                    $sTempFileName = _PS_MODULE_DIR_.'marketplace/views/img/uploadimage/'.$uniqueImgName;

                    if (!ImageManager::isCorrectImageFileExt($_FILES['image_file']['name'])) {
                        echo '1';
                        die;
                    } else {
                        $productUpload = Tools::getValue('prod_upload');
                        if ($productUpload) {
                            $validImage = 1; //no validation of size if upload any product
                        } else {
                            $validImage = $this->validateShopLogoSize($_FILES['image_file']);
                        }

                        if ($validImage) {
                            ImageManager::resize($_FILES['image_file']['tmp_name'], $sTempFileName);

                            // change file permission to 644
                            @chmod($sTempFileName, 0644);

                            if (file_exists($sTempFileName) && filesize($sTempFileName) > 0) {
                                $imagename = MpHelper::uploadCropImages($sTempFileName); //Crop images upload
                                echo $imagename; //return image name
                                die;
                            }
                        } else {
                            echo '2';
                            die;
                        }
                    }
                }
            }
        }

        $ajaxaction = Tools::getValue('field');
        if ($ajaxaction) {
            if ($ajaxaction == 'deleteimg') {
                $imagename = Tools::getValue('imagename');
                $uploadimgPath = _PS_MODULE_DIR_.'marketplace/views/img/uploadimage/'.$imagename;
                //Delete image from uploadimage directory
                @unlink($uploadimgPath);
                echo '1';
                die;
            }
        }
    }

    public function validateShopLogoSize($uploadLogo)
    {
        if (!empty($uploadLogo)) {
            list($width, $height) = getimagesize($uploadLogo['tmp_name']);
            if ($width == 0 || $height == 0) {
                $this->errors[] = $this->module->l('Invalid image size. Minimum image size must be 200X200.', 'uploadcropimage');

                return false;
            } elseif ($width < 200 || $height < 200) {
                $this->errors[] = $this->module->l('Invalid image size. Minimum image size must be 200X200.', 'uploadcropimage');

                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}