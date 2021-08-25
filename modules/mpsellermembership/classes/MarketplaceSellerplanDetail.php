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

class MarketplaceSellerplanDetail extends ObjectModel
{
    public $id;
    public $id_plan;
    public $id_order;
    public $mp_id_seller;
    public $num_products_allow;
    public $plan_duration;
    public $active_from;
    public $expire_on;
    public $is_this_current_plan;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_seller_plan_detail',
        'primary' => 'id',
        'fields' => array(
            'id_plan' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'mp_id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'num_products_allow' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'plan_duration' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'active_from' => array('type' => self::TYPE_DATE),
            'expire_on' => array('type' => self::TYPE_DATE),
            'is_this_current_plan' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE,'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE,'validate' => 'isDateFormat'),
        ),
    );

    public function delete()
    {
        if ($this->is_this_current_plan && $this->active && $this->mp_id_seller) {
            $sellers_all_active_products = MarketplaceSellerplanDetail::getSellerProduct($this->mp_id_seller, true);
            if ($sellers_all_active_products) {
                foreach ($sellers_all_active_products as $product) {
                    $obj_mp_product = new WkMpSellerProduct($product['id_seller']);
                    if ($obj_mp_product->active) {
                        $obj_mp_product->active = 0;
                        $obj_mp_product->save();

                        $product = new Product($product['id_ps_product']);
                        $product->active = 0;
                        $product->save();
                    }
                }
            }
        }

        return parent::delete();
    }

    public function deleteByIdSeller($id_seller)
    {
        return Db::getInstance()->delete('wk_mp_seller_plan_detail', '`mp_id_seller` = '.(int) $id_seller);
    }

    public function getCurrentActivePlanBySellerId($mp_id_seller)
    {
        if ($mp_id_seller) {
            return DB::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan_detail`
                WHERE `mp_id_seller` = '.(int) $mp_id_seller.'
                AND `is_this_current_plan` = 1
                AND `active` = 1 ORDER BY `id` DESC'
            );
        }

        return false;
    }

    public function getAllCurrentActivePlans()
    {
        return DB::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan_detail`
            WHERE `is_this_current_plan` = 1'
        );
    }

    public function getLastPlanBySellerId($mp_id_seller)
    {
        if ($mp_id_seller) {
            return DB::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan_detail`
                WHERE `mp_id_seller` = '.(int) $mp_id_seller.'
                ORDER BY `id` DESC'
            );
        }

        return false;
    }

    public function getLastRequestedPlanBySellerId($mp_id_seller)
    {
        if ($mp_id_seller) {
            return DB::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan_detail`
                WHERE `mp_id_seller` = '.(int) $mp_id_seller.'
                AND `is_this_current_plan` = 0
                AND `active` = 0
                ORDER BY `id` DESC'
            );
        }

        return false;
    }

    public function getLastDeactivePlanBySellerId($mp_id_seller)
    {
        if ($mp_id_seller) {
            return DB::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan_detail`
                WHERE `mp_id_seller` = '.(int) $mp_id_seller.'
                AND `is_this_current_plan` = 0
                AND `active` = 1
                ORDER BY `id` DESC'
            );
        }

        return false;
    }

    public function updateCurrentPlanBySellerId($mp_id_seller, $current_plan)
    {
        if ($mp_id_seller) {
            DB::getInstance()->update(
                'wk_mp_seller_plan_detail',
                array('is_this_current_plan' => $current_plan),
                '`mp_id_seller` = '.(int) $mp_id_seller
            );
        }
    }

    public function getAllPlansBySellerId($mp_id_seller, $id_lang = false)
    {
        if (empty($id_lang)) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        if ($mp_id_seller) {
            return Db::getInstance()->executeS('SELECT mspl.`plan_name`, mpsp.`plan_price`, mpsp.`id_product`, mpsp.`active` as plan_status, mpspd.`num_products_allow`, mpspd.`date_add`,mpspd.`active_from`, mpspd.`expire_on`, mpspd.`is_this_current_plan`, mpspd.`active` 
				FROM `'._DB_PREFIX_.'wk_mp_seller_plan_detail` AS `mpspd` 
				JOIN `'._DB_PREFIX_.'wk_mp_seller_plan` AS mpsp
                ON (mpspd.`id_plan` = mpsp.`id`) 
				JOIN `'._DB_PREFIX_.'wk_mp_seller_plan_lang` as mspl
                ON (mpsp.id = mspl.id)
				WHERE mpspd.`mp_id_seller` = '.(int) $mp_id_seller.'
                AND mspl.`id_lang` = '.(int) $id_lang.'
                ORDER BY mpspd.`id` ASC'
            );
        }

        return false;
    }

    public function getFreePlanDetailsBySellerId($seller_id)
    {
        if ($seller_id) {
            return DB::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_plan_detail`
                WHERE `id_order` = 0
                AND `id_plan` = 0
                AND `mp_id_seller` = '.(int) $seller_id
            );
        }

        return false;
    }

    public static function getSellerProduct($idSeller, $active = true)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_product` msp
                LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product_lang` mspl ON (mspl.id_mp_product = msp.id_mp_product)
                WHERE msp.`id_seller` = '.(int) $idSeller.' AND mspl.`id_lang` = '.(int) Configuration::get('PS_LANG_DEFAULT');

        if ($active) {
            $sql .= ' AND msp.`active` = 1 ';
        } elseif (!$active) {
            $sql .= ' AND msp.`active` = 0 ';
        }

        $mpProducts = Db::getInstance()->executeS($sql);
        if ($mpProducts && !empty($mpProducts)) {
            return $mpProducts;
        }

        return false;
    }
}
