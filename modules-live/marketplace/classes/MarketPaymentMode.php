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

class MarketPaymentMode extends ObjectModel
{
    public $id;
    public $payment_mode;

    public static $definition = array(
        'table' => 'marketplace_payment_mode',
        'primary' => 'id',
        'fields' => array(
            'payment_mode' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
        ),
    );

    public function delete()
    {
    	$deleteMpPayment = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'marketplace_customer_payment_detail` WHERE `payment_mode_id` = '.(int) $this->id);

        if (!$deleteMpPayment || !parent::delete()) {
            return false;
        }

        return true;
    }
}
