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

class WkGdprAnonymousCustomer extends ObjectModel
{
    public $id_gdpr_anonymous_customer;
    public $id_customer;
    public $date_add;

    public static $definition = array(
        'table' => 'wk_gdpr_anonymous_customer',
        'primary' => 'id_gdpr_anonymous_customer',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public static function customerDataErased($idCustomer)
    {
        if (!$idCustomer) {
            return false;
        }

        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_gdpr_anonymous_customer` WHERE `id_customer` ='.(int)$idCustomer;
        return Db::getInstance()->getRow($sql);
    }
}
