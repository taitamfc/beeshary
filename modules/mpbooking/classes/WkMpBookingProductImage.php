<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpBookingProductImage
{
    public function __construct()
    {
        $this->moduleInstance = Module::getInstanceByName('mpbooking');
        $this->errors = array();
    }

    public function uploadImage($imageFile, $actionIdForUpload)
    {
        $finalData = array();
        if ($actionIdForUpload) {
            if (isset($imageFile['productimages']) && $imageFile['productimages']) {
                $imageFile = $imageFile['productimages'];
                if (isset($imageFile['name'][0]) && $imageFile['name'][0]) {
                    if ($this->validateProductImage($imageFile['name'][0])) {
                        if ($this->updatePsProductImage($actionIdForUpload, $imageFile['tmp_name'][0])) {
                            $finalData['status'] = 'success';
                            $finalData['file_name'] = '';
                            $finalData['error_message'] = '';
                        } else {
                            $finalData['status'] = 'fail';
                            $finalData['file_name'] = '';
                            $finalData['error_message'] = $this->errors[0];
                        }
                    } else {
                        $finalData['status'] = 'fail';
                        $finalData['file_name'] = '';
                        $finalData['error_message'] = $this->errors[0];
                    }
                } else {
                    $finalData['status'] = 'fail';
                    $finalData['file_name'] = '';
                    $finalData['error_message'] = $this->moduleInstance->l('image not found', 'WkMpBookingProductImage');
                }
            } else {
                $finalData['status'] = 'fail';
                $finalData['file_name'] = '';
                $finalData['error_message'] = $this->moduleInstance->l('image not found', 'WkMpBookingProductImage');
            }
        } else {
            $finalData['status'] = 'fail';
            $finalData['file_name'] = '';
            $finalData['error_message'] = $this->moduleInstance->l('product not found', 'WkMpBookingProductImage');
        }

        return $finalData;
    }

    public function updatePsProductImage($idProduct, $oldPath)
    {
        $haveCover = 0;
        // if one of the other image is already have cover
        $images = Image::getImages(Context::getContext()->language->id, $idProduct);
        if ($images) {
            foreach ($images as $img) {
                if ($img['cover'] == 1) {
                    $haveCover = 1;
                }
            }
        }
        $objImage = new Image();
        $objImage->id_product = $idProduct;
        $objImage->position = Image::getHighestPosition($idProduct) + 1;

        if ($haveCover == 0) {
            $objImage->cover = 1;
        } else {
            $objImage->cover = 0;
        }
        if ($objImage->add()) {
            $imageId = $objImage->id;
            $newPath = $objImage->getPathForCreation();
            $imagesTypes = ImageType::getImagesTypes('products');

            if ($imagesTypes) {
                foreach ($imagesTypes as $imageType) {
                    if (!ImageManager::resize(
                        $oldPath,
                        $newPath.'-'.Tools::stripslashes($imageType['name']).'.'.$objImage->image_format,
                        $imageType['width'],
                        $imageType['height'],
                        $objImage->image_format
                    )) {
                        $this->errors[] = $this->moduleInstance->l('some error occurred', 'WkMpBookingProductImage');
                        return false;
                    }
                }
                ImageManager::resize($oldPath, $newPath.'.'.$objImage->image_format);
            } else {
                $this->errors[] = $this->moduleInstance->l('Image types not found', 'WkMpBookingProductImage');
                return false;
            }
        } else {
            $this->errors[] = $this->moduleInstance->l('some error occurred', 'WkMpBookingProductImage');
            return false;
        }
        return true;
    }

    public function validateProductImage($imageName)
    {
        if (!$imageName) {
            $this->errors[] = $this->moduleInstance->l('image not found', 'WkMpBookingProductImage');
            return false;
        } elseif (!ImageManager::isCorrectImageFileExt($imageName)) {
            $this->errors[] = '<strong>'.$imageName.'</strong> : '.
            $this->moduleInstance->l('Image format not recognized ', 'WkMpBookingProductImage').
            $this->l(' Allowed formats are: .gif, .jpg, .png');
            return false;
        }
        return true;
    }
}
