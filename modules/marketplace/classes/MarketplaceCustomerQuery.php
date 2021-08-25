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

class MarketplaceCustomerQuery extends ObjectModel
{
    public $id;
    public $id_product;
    public $id_customer;
    public $id_seller;
    public $subject;
    public $description;
    public $customer_email;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'marketplace_customer_query',
        'primary' => 'id',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'subject' => array('type' => self::TYPE_STRING),
            'description' => array('type' => self::TYPE_STRING),
            'customer_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
        ),
    );
}
