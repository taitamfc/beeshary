<?php
/**
* 2010-2017 Webkul
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

class MpSellerBadges extends ObjectModel
{
    public $id;
    public $badge_id;
    public $mp_seller_id;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_seller_badges',
        'primary' => 'id',
        'fields' => array(
            'badge_id' => array('type' => self::TYPE_INT),
            'mp_seller_id' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );

    public function getSellerBadges($mp_seller_id)
    {
        $badge_info = Db::getInstance()->executeS('SELECT msb.*, mb.badge_name,mb.badge_desc
			FROM `'._DB_PREFIX_.'mp_seller_badges` msb
			LEFT JOIN `'._DB_PREFIX_.'mp_badges` mb ON msb.badge_id = mb.id
			WHERE msb.mp_seller_id = '.(int) $mp_seller_id.' AND mb.active = 1');
        if (!empty($badge_info)) {
            return $badge_info;
        }

        return false;
    }

    public function deletePrevSellerBadges($mp_seller_id)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mp_seller_badges` WHERE `mp_seller_id` = '.(int) $mp_seller_id.'');
    }

    public function deleteSellerBadge($mp_seller_id, $badge_id)
    {
        $result = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mp_seller_badges` WHERE `mp_seller_id` = '.(int) $mp_seller_id.' and `badge_id` = '.(int) $badge_id.'');
        return $result;
    }
}
