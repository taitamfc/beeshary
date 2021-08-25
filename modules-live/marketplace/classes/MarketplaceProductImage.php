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

class MarketplaceProductImage extends ObjectModel
{
    public $id;
    public $seller_product_id;
    public $seller_product_image_id;
    public $id_ps_image;
    public $active;

    public static $definition = array(
        'table' => 'marketplace_product_image',
        'primary' => 'id',
        'fields' => array(
            'seller_product_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'seller_product_image_id' => array('type' => self::TYPE_STRING),
            'id_ps_image' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool'),
        ),
    );

    public function findProductImageByMpProId($id)
    {
        $productImage = Db::getInstance()->executeS(
            'SELECT  * FROM '._DB_PREFIX_.'marketplace_product_image
            WHERE `seller_product_id` = '.(int) $id
        );
        if (!empty($productImage)) {
            return $productImage;
        }

        return false;
    }

    public function getProductImageByPsIdImage($idPSImage)
    {
        return Db::getInstance()->getRow('SELECT  * FROM '._DB_PREFIX_.'marketplace_product_image
                WHERE `id_ps_image` = '.(int) $idPSImage);
    }

    public function uploadProductImage($images, $idMpProduct)
    {
        if (!empty($images)) {
            foreach ($images as $image) {
                if (!empty($image)) {
                    $explodeImg = explode('.', $image);
                    $uniqueName = $explodeImg[0];

                    Db::getInstance()->insert(
                        'marketplace_product_image',
                        array(
                            'seller_product_id' => (int) $idMpProduct,
                            'seller_product_image_id' => pSQL($uniqueName),
                            'id_ps_image' => 0
                        )
                    );

                    $uploadimgPath = _PS_MODULE_DIR_.'marketplace/views/img/uploadimage/'.$image;
                    $productimgnewPath = _PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$uniqueName.'.jpg';
                    ImageManager::resize($uploadimgPath, $productimgnewPath);

                    //Delete image from uploadimage directory
                    @unlink($uploadimgPath);
                }
            }
        }
    }

    public static function updateStatusAndPsIdImageByMpIdProduct($idMpProduct, $status, $idPSImage)
    {
        return Db::getInstance()->update(
                'marketplace_product_image',
                array(
                    'active' =>(int) $status,
                    'id_ps_image' =>(int) $idPSImage
                ),
                '`seller_product_id` = '.(int) $idMpProduct);
    }

    public static function updateStatusAndPsIdImageById($id, $status, $idPSImage)
    {
        return Db::getInstance()->update(
                'marketplace_product_image',
                array(
                    'active' =>(int) $status,
                    'id_ps_image' =>(int) $idPSImage
                ),
                '`id` = '.(int) $id);
    }
}
