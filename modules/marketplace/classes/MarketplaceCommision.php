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

class MarketplaceCommision extends ObjectModel
{
    public $id;
    public $commision;
    public $seller_customer_id;
    public $seller_customer_name;

    public static $definition = array(
        'table' => 'marketplace_commision',
        'primary' => 'id',
        'fields' => array(
            'commision' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'seller_customer_id' => array('type' => self::TYPE_INT, 'required' => true),
            'seller_customer_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
        ),
    );

    /**
     * [getSellerCommission get applied commission by particular seller].
     *
     * @return [type] [description]
     */
    public function getCommissionRateBySeller($customer_id = false)
    {
        $sql = 'SELECT `commision` FROM `'._DB_PREFIX_.'marketplace_commision`';
        if ($customer_id) {
            $sql .= ' WHERE seller_customer_id = '.(int) $customer_id;
        } else {
            $sql .= ' WHERE seller_customer_id = '.(int) $this->customer_id;
        }

        return Db::getInstance()->getValue($sql);
    }

    public function findAllCustomerInfo()
    {
        $customer_info = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'customer`
            WHERE `id_customer` = '.(int) $this->customer_id
        );
        if (empty($customer_info)) {
            return false;
        }

        return $customer_info;
    }

    /**
     * get the customer for whose commission is not set.
     *
     * @return [array or false] [customer array]
     */
    public function getSellerNotHaveCommissionSet()
    {
        $mp_seller_info = Db::getInstance()->executeS(
            'SELECT c.`id_customer` as `seller_customer_id`, c.`email`, mpsi.`business_email` FROM `'._DB_PREFIX_.'customer` c
			JOIN `'._DB_PREFIX_.'marketplace_seller_info` mpsi ON (mpsi.seller_customer_id = c.id_customer)
			WHERE mpsi.active = 1 
            AND mpsi.seller_customer_id NOT IN (SELECT `seller_customer_id` FROM `'._DB_PREFIX_.'marketplace_commision`)'
        );
        if (empty($mp_seller_info)) {
            return false;
        }

        return $mp_seller_info;
    }

    public function getCommissionById($id)
    {
        $commission = Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_commision`
            WHERE id = '.(int) $id
        );
        if ($commission) {
            return $commission;
        }

        return false;
    }

    /**
     * Could not fine any function in prestashop
     * [getTaxByIdOrderDetail get tax amout by id_order_details].
     *
     * @param [int] $id_order_detail
     *
     * @return [float]
     */
    public function getTaxByIdOrderDetail($id_order_detail)
    {
        $tax_amt = Db::getInstance()->getValue(
            'SELECT `total_amount` FROM `'._DB_PREFIX_.'order_detail_tax`
            WHERE `id_order_detail` = '.(int) $id_order_detail
        );
        if ($tax_amt) {
            return $tax_amt;
        }

        return 0;
    }

    public static function updateSellerNameByIdCustomer($seller_customer_id, $seller_name)
    {
        return Db::getInstance()->update('marketplace_commision', array('seller_customer_name' => $seller_name), 'seller_customer_id = '.(int) $seller_customer_id);
    }
}
