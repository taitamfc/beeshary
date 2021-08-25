<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpCommission extends ObjectModel
{
    public $commision_rate;
    public $seller_customer_id;

    public static $definition = array(
        'table' => 'wk_mp_commision',
        'primary' => 'id_wk_mp_commision',
        'fields' => array(
            'commision_rate' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'seller_customer_id' => array('type' => self::TYPE_INT, 'required' => true),
        ),
    );

    /**
     * Get Commission Rate by using Customer ID, if customer id is false then current customer id will be used
     *
     * @return float
     */
    public function getCommissionRate($idCustomer = false)
    {
        if (!$idCustomer) { // customer id is false we will take current customer's id
            $idCustomer = Context::getContext()->customer->id;
        }

        return Db::getInstance()->getValue('SELECT `commision_rate` FROM `'._DB_PREFIX_.'wk_mp_commision`
            WHERE `seller_customer_id` = '.(int) $idCustomer);
    }

    /**
     * Get all those sellers who has not set commission yet
     *
     * @return array/boolean
     */
    public function getSellerWithoutCommission()
    {
        $mpSellerInfo = Db::getInstance()->executeS(
            'SELECT
                c.`id_customer` as `seller_customer_id`,
                c.`email`,
                mpsi.`business_email` FROM `'._DB_PREFIX_.'customer` c
                JOIN `'._DB_PREFIX_.'wk_mp_seller` mpsi ON (mpsi.seller_customer_id = c.id_customer)
                WHERE mpsi.`active` = 1 AND mpsi.`seller_customer_id` NOT IN
                (SELECT `seller_customer_id` FROM `'._DB_PREFIX_.'wk_mp_commision`)'
        );

        if (empty($mpSellerInfo)) {
            return false;
        }

        return $mpSellerInfo;
    }

    /**
     * get mp commission according to seller or global commission
     *
     * @param int $sellerCustomerId seller customer id
     *
     * @return bool
     */
    public static function getCommissionBySellerCustomerId($sellerCustomerId)
    {
        $objMpCommission = new WkMpCommission();
        if ($commission = $objMpCommission->getCommissionRate($sellerCustomerId)) {
            return $commission;
        } else {
            return Configuration::get('WK_MP_GLOBAL_COMMISSION');
        }

        return false;
    }
}
