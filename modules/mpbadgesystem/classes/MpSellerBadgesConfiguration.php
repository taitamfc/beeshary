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

class MpSellerBadgesConfiguration extends ObjectModel
{
    public $id;
    public $id_seller;
    public $active;

    public static $definition = array(
        'table' => 'mp_seller_badges_configuration',
        'primary' => 'id',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_INT),
        ),
    );

    public static function getBadgeConnfigurationByIdSeller($id_seller)
    {
        $badge_info = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_seller_badges_configuration` WHERE id_seller='.(int) $id_seller);
        if (!empty($badge_info)) {
            return $badge_info;
        } else {
            return false;
        }
    }
}
