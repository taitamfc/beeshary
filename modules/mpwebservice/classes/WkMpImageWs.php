<?php
/**
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpImageWs extends WkMpWebservice
{
    /**
     * @var string The extension of the image to display
     */
    public $imgExtension;
    /**
     * @var array The list of supported mime types
     */
    public $acceptedImgMimeTypes = array(
        'image/gif',
        'image/jpg',
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
    );
    /**
     * @var string The image type (product, sellerlogo, shoplogo)
     */
    public $imageType = null;
    public $sellerImage = array(
        'sellerlogo',
        'sellerbanner',
        'shoplogo',
        'shopbanner',
        'products',
    );
    /**
     * @var string The file path of the image to display.
     *             If not null, the image will be displayed,
     *             even if the XML output was not empty
     */
    public $imgToDisplay = null;

    /**
     * Management of images URL segment.
     * @return array
     */
    public function manageMpImages()
    {
        $this->imageType = $this->wsObject->urlSegment[2];
        switch ($this->wsObject->urlSegment[2]) {
            case 'sellerlogo':
                return $this->manageSellerImage();
            case 'sellerbanner':
                return $this->manageSellerImage();
            case 'shoplogo':
                return $this->manageSellerImage();
            case 'shopbanner':
                return $this->manageSellerImage();
            case 'products':
                return $this->manageMpProductImages();
            case '':
                foreach ($this->sellerImage as $image) {
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader(
                        'mpimages',
                        array(),
                        array('id' => $image, 'xlink_resource' => $this->wsObject->wsUrl.'seller/mpimages/'.$image),
                        false
                    );
                }
                return $this->output;
            default:
                $exception = new WebserviceException(
                    sprintf('Image of type "%s" does not exisat', $this->wsObject->urlSegment[2]),
                    array(48, 400)
                );
                throw $exception->setDidYouMean($this->wsObject->urlSegment[2], $this->sellerImage);
        }
    }


    public function manageMpProductImages()
    {
        $idMpProduct = $this->wsObject->urlSegment[3];
        switch ($idMpProduct) {
            case '':
                return $this->manageListMpProductImages();
            default:
                return $this->manageEntityMpProductImages($idMpProduct);
        }
    }

    /**
     * Display list of the Seller Product Images
     * @return xml
     */
    public function manageListMpProductImages()
    {
        if ($this->wsObject->method != 'GET') {
            $this->output['success'] = false;
            $this->output['message'] = 'This method is not allowed for listing marketplace product images.';
            return $this->output;
        }

        $mpImages = WkMpHelperWs::getAllMpImages($this->idSeller);
        $ids = $this->getSortedIdArray($mpImages, 'seller_product_id', true);
        $this->output['success'] = true;
        $this->output['productIds'] = array_values($ids);
        return $this->output;
    }

    public function manageEntityMpProductImages($idMpProduct)
    {
        $directory = _PS_MODULE_DIR_.'marketplace/views/img/product_img/';
        $objMpProduct = new WkMpSellerProduct($idMpProduct);
        if (!Validate::isLoadedObject($objMpProduct) || ($objMpProduct->id_seller != $this->idSeller)) {
            $this->output['success'] = false;
            $this->output['message'] = 'Invalid product';
            return false;
        }

        $mpImages = WkMpHelperWs::getImagesForProduct($idMpProduct);
        $availableImageIds = $this->getSortedIdArray($mpImages, 'id_mp_product_image');

        // If an image id is specified
        if (isset($this->wsObject->urlSegment[4])) {
            $idImage = $this->wsObject->urlSegment[4];
            if (!in_array($idImage, $availableImageIds)) {
                $this->output['success'] = false;
                $this->output['message'] = 'This image id does not exist';
                return false;
            } else {
                $imageName = WkMpHelperWs::getImageName($idImage);
                if (file_exists($directory.$imageName)) {
                    $imagePath = $directory.$imageName;
                }
            }
        } elseif ($this->wsObject->method == 'GET' || $this->wsObject->method == 'HEAD') {
            if ($availableImageIds) {
                $this->output['success'] = true;
                $this->output['mp_id_product'] = $idMpProduct;
                $this->output['imageIds'] = $availableImageIds;
                return $this->output;
            } else {
                $this->objOutput->setStatus(404);
                $this->wsObject->setOutputEnabled(false);
            }
        }

        if ($this->output != '') {
            return $this->output;
        } elseif (isset($imagePath)) {
            return $this->manageMpProductImagesCRUD(file_exists($imagePath), $imagePath, $directory);
        } else {
            return $this->manageMpProductImagesCRUD(false, '', $directory);
        }
    }

    public function manageMpProductImagesCRUD($originalImageExists, $imagePath, $directory)
    {
        switch ($this->wsObject->method) {
            case 'GET':
            case 'HEAD':
                if ($originalImageExists) {
                    $this->imgToDisplay = $imagePath;
                } else {
                    $this->output['success'] = false;
                    $this->output['message'] = 'This image does not exist on disk';
                    return $this->output;
                }
                break;
            case 'PUT':
                //@todo::Not updating, its Adding, I think PUT should not be for product image, only POST
                if ($originalImageExists) {
                    if ($this->writeMpPostedProductImageOnDisk($directory)) {
                        $this->imgToDisplay = $imagePath;
                        return $this->output;
                    } else {
                        $this->output['success'] = false;
                        $this->output['message'] = 'Unable to save this image.';
                        return $this->output;
                    }
                } else {
                    $this->output['success'] = false;
                    $this->output['message'] = 'This image does not exist on disk';
                    return $this->output;
                }
                break;
            case 'DELETE':
                $mpImage = new WkMpSellerProductImage($this->wsObject->urlSegment[4]);
                @unlink($directory.$mpImage->seller_product_image_name);
                if ($mpImage->delete()) {
                    $this->output['success'] = true;
                    $this->output['message'] = 'Deleted Successfully';
                    return $this->output;
                } else {
                    $this->output['success'] = false;
                    $this->output['message'] = 'This image does not exist on disk';
                    return $this->output;
                }

            case 'POST':
                if ($originalImageExists) {
                    $this->output['success'] = false;
                    $this->output['message'] = 'This image already exists.
                    To modify it, please use the PUT method';
                    return $this->output;
                } else {
                    if ($this->writeMpPostedProductImageOnDisk($directory)) {
                        return $this->output;
                    } else {
                        $this->output['success'] = false;
                        $this->output['message'] = 'Unable to save this image';
                        return $this->output;
                    }
                }
                break;
            default:
                $this->output['success'] = false;
                $this->output['message'] = 'This method is not allowed';
                return $this->output;
        }
    }

    public function writeMpPostedProductImageOnDisk($directory)
    {
        if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name']) {
            $file = $_FILES['image'];
            require_once _PS_CORE_DIR_.'/images.inc.php';
            if ($error = ImageManager::validateUpload($file)) {
                $this->output['success'] = false;
                $this->output['message'] = $error;
                return $this->output;
            }

            if (isset($file['tmp_name']) && $file['tmp_name'] != null) {
                if (!isset($file['tmp_name'])) {
                    return false;
                }

                if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
                || !move_uploaded_file($file['tmp_name'], $tmp_name)) {
                    $this->output['success'] = false;
                    $this->output['message'] = 'An error occurred during the image upload';
                    return $this->output;
                } else {
                    $productImage = WkMpHelperWs::productImageUploadWs(
                        $_FILES,
                        $this->wsObject->urlSegment[3],
                        $directory,
                        $tmp_name
                    );

                    if (!$productImage) {
                        $this->output['success'] = false;
                        $this->output['message'] = 'An error occurred while copying image';
                        return $this->output;
                    }
                }

                @unlink($tmp_name);
                $this->output['success'] = true;
                $this->output['message'] = 'Image uploaded successfully';
                $this->output['id_mp_product_image'] = $productImage['object']->id;
                $this->output['mp_product_image_url'] = $this->getMpProductImageURL($productImage['object']->id);
                return true;

                // @unlink($tmp_name);
                // $this->imgToDisplay = $directory.$productImage['name'];
                // $this->objOutput->setFieldsToDisplay('full');
                // $this->output = $this->objOutput->renderEntity($productImage['object'], 1);
                // $image_content = array(
                //     'sqlId' => 'content',
                //     'value' => base64_encode(Tools::file_get_contents($this->imgToDisplay)),
                //     'encode' => 'base64'
                // );
                // $this->output .= $this->objOutput->objectRender->renderField($image_content);
                // return true;
            }
        }
    }

    public function getMpProductImageURL($idMpProductImage)
    {
        $shopURL = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        $mpImage = new WkMpSellerProductImage($idMpProductImage);
        if ($mpImage->seller_product_image_name) {
            return $shopURL.'modules/marketplace/views/img/product_img/'.$mpImage->seller_product_image_name;
        } else {
            return '';
        }
    }

    public function getSortedIdArray($data, $key, $aSort = false)
    {
        $ids = array();
        foreach ($data as $value) {
            $ids[] = $value[$key];
        }
        $ids = array_unique($ids, SORT_NUMERIC);
        if ($aSort) {
            asort($ids);
        }
        return $ids;
    }

    public function manageSellerImage()
    {
        $mpSeller = new WkMpSeller($this->idSeller);
        if (!Validate::isLoadedObject($mpSeller)) {
            $this->output['success'] = false;
            $this->output['message'] = 'Seller object cannot be loaded.';
            return $this->output;
        }
        $imgDir = _PS_MODULE_DIR_.'marketplace/views/img/';
        if ($this->imageType == 'sellerlogo') {
            $directory = $imgDir.'seller_img/';
            $imageName = $mpSeller->profile_image;
        } elseif ($this->imageType == 'sellerbanner') {
            $directory = $imgDir.'seller_banner/';
            $imageName = $mpSeller->profile_banner;
        } elseif ($this->imageType == 'shoplogo') {
            $directory = $imgDir.'shop_img/';
            $imageName = $mpSeller->shop_image;
        } elseif ($this->imageType == 'shopbanner') {
            $directory = $imgDir.'shop_banner/';
            $imageName = $mpSeller->shop_banner;
        } else {
            $exception = new WebserviceException(
                sprintf('Image of type "%s" does not exist', $this->wsObject->urlSegment[1]),
                array(48, 400)
            );
            throw $exception->setDidYouMean($this->wsObject->urlSegment[1], array_keys($this->allowedMethods));
        }
        $sellerImagePath = $directory.$imageName;
        return $this->manageSellerImageCRUD($sellerImagePath, $directory, $imageName);
    }

    public function manageSellerImageCRUD($sellerImagePath, $directory, $imageName)
    {
        $originalImageExist = file_exists($sellerImagePath);
        switch ($this->wsObject->method) {
            case 'GET':
                if ($this->wsObject->urlSegment[3] == 'default') {
                    $sellerImagePath = $directory.'defaultimage.jpg';
                }
                if (file_exists($sellerImagePath)) {
                    $this->imgToDisplay = $sellerImagePath;
                } else {
                    $this->output['success'] = false;
                    $this->output['message'] = 'This image does not exist on disk';
                    return $this->output;
                }
                break;
            case 'HEAD':
                if ($originalImageExist) {
                    $this->imgToDisplay = $sellerImagePath;
                } else {
                    $this->output['success'] = false;
                    $this->output['message'] = 'This image does not exist on disk';
                    return $this->output;
                }
                break;
            case 'PUT':
                if ($this->writePostedSellerImageOnDisk($directory)) {
                    return $this->output;
                } else {
                    $this->output['success'] = false;
                    $this->output['message'] = 'Unable to save this image';
                    return $this->output;
                }
                break;
            case 'DELETE':
                $delete = WkMpHelperWs::deleteSellerImageWs($this->idSeller, $directory, $this->imageType);
                if (!$delete) {
                    $this->output['success'] = false;
                    $this->output['message'] = 'Unable to delete this image';
                    return $this->output;
                }

                $this->output['success'] = true;
                $this->output['message'] = 'Successfully deleted';
                return $this->output;
            case 'POST':
                if (!empty($imageName)) {
                    $this->output['success'] = false;
                    $this->output['message'] = 'This image already exists.
                    To modify it, please use the PUT method';
                    return $this->output;
                } else {
                    if ($this->writePostedSellerImageOnDisk($directory)) {
                        return $this->output;
                    } else {
                        $this->output['success'] = false;
                        $this->output['message'] = 'Unable to save this image';
                        return $this->output;
                    }
                }
                break;
            default:
                $this->output['success'] = false;
                $this->output['message'] = 'This method is not allowed';
                return $this->output;
        }
    }

    public function writePostedSellerImageOnDisk($directory)
    {
        if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name']) {
            $file = $_FILES['image'];
            require_once _PS_CORE_DIR_.'/images.inc.php';
            if ($error = ImageManager::validateUpload($file)) {
                $this->output['success'] = false;
                $this->output['message'] = $error;
                return $this->output;
            }

            if (isset($file['tmp_name']) && $file['tmp_name'] != null) {
                if (!isset($file['tmp_name'])) {
                    return false;
                }
                if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
                    || !move_uploaded_file($file['tmp_name'], $tmp_name)) {
                        $this->output['success'] = false;
                        $this->output['message'] = 'An error occurred during the image upload';
                        return $this->output;
                } else {
                    $imageName = WkMpHelperWs::sellerImageUploadWs(
                        $_FILES,
                        $tmp_name,
                        $this->idSeller,
                        $directory,
                        $this->imageType
                    );
                    if (!$imageName) {
                        $this->output['success'] = false;
                        $this->output['message'] = 'An error occurred while copying image';
                        return $this->output;
                    }
                }
                @unlink($tmp_name);
                $this->imgToDisplay = $directory.$imageName;
                $this->objOutput->setFieldsToDisplay('full');
                $image_content = array(
                        'sqlId' => 'content',
                        'value' => base64_encode(Tools::file_get_contents($this->imgToDisplay)), 'encode' => 'base64'
                    );
                $this->output .= $this->objOutput->objectRender->renderField($image_content);
                return true;
            }
        }
    }
}
