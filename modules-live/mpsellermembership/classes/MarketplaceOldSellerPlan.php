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

class MarketplaceOldSellerPlan extends ObjectModel
{
    public $id;
    public $id_seller;
    public $active_from;
    public $expire_on;

    public static $definition = array(
        'table' => 'wk_mp_old_seller_plan',
        'primary' => 'id',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'active_from' => array('type' => self::TYPE_DATE),
            'expire_on' => array('type' => self::TYPE_DATE),
        ),
    );

    public function getAllData()
    {
        return Db::getInstance()->executeS(
            'SELECT mposp.*, mpsi.`business_email`, mpsi.`seller_name`
        	FROM `'._DB_PREFIX_.'wk_mp_old_seller_plan` mposp
        	JOIN `'._DB_PREFIX_.'marketplace_seller_info` mpsi
        	ON ( mpsi.`id` = mposp.`id_seller`)'
        );
    }

    public static function getInfoByIdSeller($id_seller)
    {
        if ($id_seller) {
            return DB::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_old_seller_plan`
                WHERE `id_seller` = '.(int) $id_seller
            );
        }

        return false;
    }
}
