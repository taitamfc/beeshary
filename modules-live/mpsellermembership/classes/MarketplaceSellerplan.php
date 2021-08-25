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

class MarketplaceSellerplan extends ObjectModel
{
    public $id;
    public $id_product;
    public $plan_price;
    public $plan_duration;
    public $num_products_allow;
    public $sequence_number;
    public $active;
    public $date_add;
    public $date_upd;

    public $plan_name;

    public static $definition = array(
        'table' => 'wk_mp_seller_plan',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'plan_price' => array('type' => self::TYPE_FLOAT ,'validate' => 'isUnsignedFloat', 'required' => true),
            'plan_duration' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'num_products_allow' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'sequence_number' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'plan_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
        ),
    );

    public function delete()
    {
        $obj_product = new Product($this->id_product);
        $obj_product->delete();

        return parent::delete();
    }

    public static function getPlanByIdProduct($id_product)
    {
        if ($id_product) {
            return Db::getInstance()->getValue(
                'SELECT `id` FROM `'._DB_PREFIX_.'wk_mp_seller_plan`
                WHERE `id_product` = '.(int) $id_product
            );
        }

        return false;
    }

    public static function getPlanInfoByIdProduct($id_product, $id_lang = false)
    {
        if (empty($id_lang)) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        if ($id_product) {
            return Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan` as msp 
				JOIN `'._DB_PREFIX_.'wk_mp_seller_plan_lang` as mspl
                ON (msp.id = mspl.id)
                WHERE msp.`id_product` = '.(int) $id_product.'
                AND mspl.`id_lang` = '.(int) $id_lang
            );
        }

        return false;
    }

    public function getPlanInfoById($id)
    {
        if ($id) {
            return DB::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan`
                WHERE `id` = '.(int) $id
            );
        }

        return false;
    }

    public function getPlanLangInfoById($id)
    {
        if ($id) {
            return DB::getInstance()->executeS(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan_lang`
                WHERE `id` = '.(int) $id
            );
        }

        return false;
    }

    public static function getPlanLangInfoByIdAndLangId($id, $id_lang)
    {
        if ($id && $id_lang) {
            return DB::getInstance()->getValue(
                'SELECT `plan_name` FROM `'._DB_PREFIX_.'wk_mp_seller_plan_lang`
                WHERE `id` = '.(int) $id.'
                AND `id_lang` = '.(int) $id_lang
            );
        }

        return false;
    }

    public static function uploadMpImage($files, $id_plan)
    {
        if (!empty($files['name']) && $files['size'] > 0 && $id_plan) {
            //plan image upload
            $upload_path = _PS_MODULE_DIR_.'mpsellermembership/views/img/';
            $image_name = $id_plan.'.jpg';
            ImageManager::resize($files['tmp_name'], $upload_path.$image_name, 150, 150);
        }
    }

    public static function uploadPsImage($logo_src, $id_product, $shop_id)
    {
        if (file_exists(_PS_TMP_IMG_DIR_.'product_'.$id_product.'.jpg')) {
            unlink(_PS_TMP_IMG_DIR_.'product_'.$id_product.'.jpg');
        }

        DB::getInstance()->delete('image', '`id_product` = '.(int) $id_product);
        Db::getInstance()->delete('image_shop', '`id_product` = '.(int) $id_product);

        if (file_exists(_PS_TMP_IMG_DIR_.'product_'.$id_product.'.jpg')) {
            unlink(_PS_TMP_IMG_DIR_.'product_'.$id_product.'.jpg');
        }

        if (file_exists(_PS_TMP_IMG_DIR_.'product_mini_'.$id_product.'_'.$shop_id.'.jpg')) {
            unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$id_product.'_'.$shop_id.'.jpg');
        }

        $image_obj = new Image();
        $image_obj->id_product = $id_product;
        $image_obj->position = 0;
        $image_obj->cover = 1;
        $image_obj->save();
        $image_id = $image_obj->id;
        $new_path = $image_obj->getPathForCreation();

        $imagesTypes = ImageType::getImagesTypes('products');
        foreach ($imagesTypes as $image_type) {
            ImageManager::resize($logo_src, $new_path.'-'.$image_type['name'].'.jpg', $image_type['width'], $image_type['height']);
        }

        ImageManager::resize($logo_src, $new_path.'.jpg');
        Hook::exec('actionWatermark', array('id_image' => $image_id, 'id_product' => $id_product));
    }

    public function toggleStatus()
    {
        $obj_product = new Product($this->id_product);
        $obj_product->toggleStatus();

        return parent::toggleStatus();
    }

    public function getAllPlan()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan`');
    }

    public function getAllActivePlanOrderBySequenceNumber($id_lang = false)
    {
        if (empty($id_lang)) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan` as msp
            JOIN `'._DB_PREFIX_.'wk_mp_seller_plan_lang` as mspl
            ON (msp.id = mspl.id) 
			WHERE mspl.`id_lang` = '.(int) $id_lang.'
            AND msp.`active` = 1
            ORDER BY `sequence_number` ASC'
        );
    }

    public function updateCustomerGroupByIdCustomerAndIdGroup($id_customer, $id_group)
    {
        if ($id_customer && $id_group) {
            $obj_customer = new Customer($id_customer);
            $group_id = array();
            $group_id[] = $id_group;
            $obj_customer->addGroups($group_id);
            unset($obj_customer);
        }
    }

    public function getAllModules()
    {
        $modules = Db::getInstance()->executeS('SELECT `id_module` FROM `'._DB_PREFIX_.'module`');
        $arr = array();
        foreach ($modules as $data) {
            $arr[] = $data['id_module'];
        }

        return $arr;
    }

    public function getAllShops()
    {
        $shops = Db::getInstance()->executeS('SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`');
        $arr = array();
        foreach ($shops as $data) {
            $arr[] = $data['id_shop'];
        }

        return $arr;
    }

    public function getFreePlanBySellerId($seller_id)
    {
        if ($seller_id) {
            $is_free_plan_taken = DB::getInstance()->getValue(
                'SELECT `is_free_plan_taken` FROM `'._DB_PREFIX_.'wk_mp_seller`
                WHERE `id_seller` = '.(int) $seller_id
                );
            if ($is_free_plan_taken) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    public function getPlanDetailsByPriceAndDurationAndAllowProducts($plan_price, $plan_duration, $num_products_allow)
    {
        return DB::getInstance()->getRow(
            'SELECT `id` FROM `'._DB_PREFIX_.'wk_mp_seller_plan`
            WHERE `plan_price` = '.(int) $plan_price.'
            AND `plan_duration` = '.(int) $plan_duration.'
            AND `num_products_allow` = '.(int) $num_products_allow
        );
    }
}
