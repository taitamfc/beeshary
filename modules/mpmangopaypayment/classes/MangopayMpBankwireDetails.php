<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MangopayMpBankwireDetails extends ObjectModel
{
    public $id_mangopay_transaction;
    public $mgp_wire_reference;
    public $mgp_account_type;
    public $mgp_account_owner_name;
    public $mgp_account_iban;
    public $mgp_account_bic;
    public $declared_amount;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_mangopay_bankwire_details',
        'primary' => 'id',
        'fields' => array(
            'id_mangopay_transaction' => array('type' => self::TYPE_STRING, 'size' => 64),
            'mgp_wire_reference' => array('type' => self::TYPE_STRING, 'size' => 32),
            'mgp_account_type' => array('type' => self::TYPE_STRING, 'size' => 32),
            'mgp_account_owner_name' => array('type' => self::TYPE_STRING, 'size' => 32),
            'mgp_account_iban' => array('type' => self::TYPE_STRING, 'size' => 255),
            'mgp_account_bic' => array('type' => self::TYPE_STRING, 'size' => 255),
            'declared_amount' => array('type' => self::TYPE_FLOAT),
        ),
    );
}
