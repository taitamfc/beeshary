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

class MpBadge extends ObjectModel
{
    public $id;
    public $badge_name;
    public $badge_desc;
    public $badge_link;
    public $badge_banner;
    public $badge_watermark;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_badges',
        'primary' => 'id',
        'fields' => array(
            'badge_name' => array('type' => self::TYPE_STRING,'required' => true),
            'badge_desc' => array('type' => self::TYPE_STRING,'required' => true),
            'badge_link' => array('type' => self::TYPE_STRING,'required' => false),
            'badge_banner' => array('type' => self::TYPE_STRING,'required' => false),
            'badge_watermark' => array('type' => self::TYPE_STRING,'required' => false),
            'badge_is_partner' => array('type' => self::TYPE_STRING,'required' => false),
            'badge_color' => array('type' => self::TYPE_STRING,'required' => false),
            'badge_banner_category' => array('type' => self::TYPE_STRING,'required' => false),
            'badge_watermark_product_list' => array('type' => self::TYPE_STRING,'required' => false),
            'active' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );
    public function getAllBadges()
    {
        $badges = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_badges` WHERE active = 1');
        if (!empty($badges)) {
            return $badges;
        } else {
            return false;
        }
    }

    public function getBadgeInfo($badge_id)
    {
        $badge_info = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_badges` WHERE id='.(int) $badge_id);
        if (!empty($badge_info)) {
            return $badge_info;
        } else {
            return false;
        }
    }
}
