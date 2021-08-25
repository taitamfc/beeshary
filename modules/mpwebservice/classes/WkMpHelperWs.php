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

class WkMpHelperWs extends ObjectModel
{
    public static function getImageName($id_mp_image)
    {
        return Db::getInstance()->getValue('SELECT `seller_product_image_name`
                            FROM '._DB_PREFIX_.'wk_mp_seller_product_image
                            WHERE id_mp_product_image = '.$id_mp_image);
    }

    public static function getAllMpImages($idSeller)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_product_image` wmspi
                            JOIN `'._DB_PREFIX_.'wk_mp_seller_product` wmsp
                            ON (wmspi.`seller_product_id` = wmsp.`id_mp_product`)
                            WHERE wmsp.`id_seller` = '. (int) $idSeller);
    }

    public static function getImagesForProduct($id_mp_product)
    {
        return Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'wk_mp_seller_product_image
            WHERE seller_product_id = '.(int) $id_mp_product);
    }

    public static function productImageUploadWs($image, $idMpProduct, $directory, $tmpName)
    {
        $name = $image['image']['name'];
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $imageName = WkMpHelper::randomImageName().'.'.$ext;
        //$imageName = WkMpHelper::randomImageName().'.'.end((explode(".", $name)));
        $objMpImage = self::uploadProductImage($idMpProduct, $imageName);
        if ($objMpImage) {
            if (ImageManager::resize($tmpName, $directory.$imageName)) {
                return array('object' => $objMpImage, 'name' => $imageName);
            }
        }
        return false;
    }

    /**
     * Copied from Marketplace -> WkMpSellerProductImage modified
     *
     * @param int    $mpIdProduct  Seller Product ID
     * @param string $imageNewName Image name
     * @return bool
     */
    public static function uploadProductImage($mpIdProduct, $imageNewName)
    {
        $objMpImage = new WkMpSellerProductImage();
        $objMpImage->seller_product_id = $mpIdProduct;
        $objMpImage->seller_product_image_name = $imageNewName;
        if ($objMpImage->save()) {
            //if product is active then check admin configure value
            //that product after update need to approved by admin or not
            WkMpSellerProduct::deactivateProductAfterUpdate($mpIdProduct);
            $mpProductDetails = WkMpSellerProduct::getSellerProductByIdProduct($mpIdProduct);
            if ($mpProductDetails && $mpProductDetails['active'] && $mpProductDetails['id_ps_product']) {
                //Upload images in ps product
                $objSellerProduct = new WkMpSellerProduct($mpIdProduct);
                $objSellerProduct->updatePsProductImage($mpIdProduct, $mpProductDetails['id_ps_product']);
            }

            return $objMpImage;
        }

        return false;
    }

    /**
     * Delete previous and add the new seller image
     * @param  array $image     $_FILES
     * @param  string $tmpName   temp_name created by Prestashop
     * @param  int $idSeller  id_seller
     * @param  string $directory image directory
     * @param  string $type      seller image type [sellerlogo, sellerbanner, shoplogo, shopbanner]
     * @return bool
     */
    public static function sellerImageUploadWs($image, $tmpName, $idSeller, $directory, $type)
    {
        $name = $image['image']['name'];
        $rand = Tools::passwdGen(6);
        $ext = end((explode(".", $name)));
        $imageName = $rand.'.'.$ext;
        $objWkSeller = new WkMpSeller($idSeller);
        if ($type == 'sellerlogo') {
            $oldImageName = $objWkSeller->profile_image;
            $objWkSeller->profile_image = $imageName;
        } elseif ($type == 'sellerbanner') {
            $oldImageName = $objWkSeller->profile_banner;
            $objWkSeller->profile_banner = $imageName;
        } elseif ($type == 'shoplogo') {
            $oldImageName = $objWkSeller->shop_image;
            $objWkSeller->shop_image = $imageName;
        } elseif ($type == 'shopbanner') {
            $oldImageName = $objWkSeller->shop_banner;
            $objWkSeller->shop_banner = $imageName;
        }
        if ($objWkSeller->save()) {
            @unlink($directory.$oldImageName);
            if ($type == 'sellerlogo' || $type == 'shoplogo') {
                if (ImageManager::resize($tmpName, $directory.$imageName, 200, 200)) {
                    return $imageName;
                }
            } else {
                if (ImageManager::resize($tmpName, $directory.$imageName)) {
                    return $imageName;
                }
            }
        }
        return false;
    }

    public static function deleteSellerImageWs($idSeller, $directory, $type)
    {
        $objWkSeller = new WkMpSeller($idSeller);
        if ($type == 'sellerlogo') {
            $imageName = $objWkSeller->profile_image;
            $objWkSeller->profile_image = '';
        } elseif ($type == 'sellerbanner') {
            $imageName = $objWkSeller->profile_banner;
            $objWkSeller->profile_banner = '';
        } elseif ($type == 'shoplogo') {
            $imageName = $objWkSeller->shop_image;
            $objWkSeller->shop_image = '';
        } elseif ($type == 'shopbanner') {
            $imageName = $objWkSeller->shop_banner;
            $objWkSeller->shop_banner = '';
        }
        @unlink($directory.$imageName);
        if (!$objWkSeller->save()) {
            return false;
        }
        return true;
    }
}
