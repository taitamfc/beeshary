<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpSellerPayment extends ObjectModel
{
    public $id_seller;
    public $id_currency;
    public $total_earning;
    public $total_paid;
    public $total_due;

    public static $definition = array(
        'table' => 'wk_mp_seller_payment',
        'primary' => 'id_seller_payment',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'required' => true),
            'total_earning' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'total_paid' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'total_due' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'id_currency' => array('type' => self::TYPE_INT, 'required' => true),
        ),
    );

    /**
     * Settleing seller total earning total due or total paid amount in seller payment table
     * 
     * @param  int $idCurrency Prestashop Currency ID
     * @param  int $mpProduct  Array of product of currenct seller
     * @return bool
     */
    public function processSellerPaymentTransaction($idCurrency, $mpProduct)
    {
        if ($mpProduct['product_list']) {
            $totalSellerAmt = 0;
            foreach ($mpProduct['product_list'] as $productAttrArr) {
                foreach ($productAttrArr as $productInfo) {
                    $sellerAmt = $productInfo['seller_amount'] + $productInfo['seller_tax'];
                    $totalSellerAmt += $sellerAmt;
                    $idSeller = $productInfo['id_seller'];
                }
            }

            $objSellerPayment = new self();
            $check_seller = $objSellerPayment->getDetailsByIdSellerAndIdCurrency($idSeller, $idCurrency);
            if (!$check_seller) {
                $objSellerPayment->id_seller = $idSeller;
                $objSellerPayment->total_earning = $totalSellerAmt;
                $objSellerPayment->total_paid = 0;
                $objSellerPayment->total_due = $totalSellerAmt;
                $objSellerPayment->id_currency = $idCurrency;
                $objSellerPayment->save();
            } else {
                $totalEarning = $check_seller['total_earning'] + $totalSellerAmt;
                $totalDue = $check_seller['total_due'] + $totalSellerAmt;

                $this->updateEarningAndDueByIdSellerAndIdCurrency($totalEarning, $totalDue, $idSeller, $idCurrency);
            }
        }
    }

    /**
     * Get seller payment details using seller id & currency id
     *
     * @param int $id_seller Seller ID
     * @param int $id_currency Prestashop Currency ID
     * @return bool/array
     */
    public function getDetailsByIdSellerAndIdCurrency($id_seller, $id_currency)
    {
        if ($id_seller && $id_currency) {
            $details = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_payment` WHERE `id_seller`= '.(int) $id_seller.' AND `id_currency` = '.(int) $id_currency);
            if ($details) {
                return $details;
            }
            return false;
        }

        return false;
    }

    /**
     * Get Seller detail from seller payment using id seller and id currency
     * 
     * @param  int $idSeller   Seller ID
     * @param  int $idCurrency Prestashop Currency ID
     * @return bool
     */
    public static function getSellerTotalByIdSeller($idSeller, $idCurrency)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_payment` WHERE `id_seller`='.(int) $idSeller.' AND `id_currency` ='.(int) $idCurrency);
    }
    
    /**
     * Get All Seller detail with sum of their earning due and paid based on currency id
     * 
     * @param  int $idCurrency Prestashop Currency ID
     * @return array
     */
    public static function getSellerTotal($idCurrency)
    {
        return Db::getInstance()->getRow('SELECT 
            SUM(`total_earning`) as total_earning,
            SUM(`total_pending`) as total_pending,
            SUM(`total_paid`) as total_paid,
            SUM(`total_due`) as total_due FROM `'._DB_PREFIX_.'wk_mp_seller_payment` WHERE `id_currency` ='.(int) $idCurrency);
    }

    /**
     * Get seller payment details according to seller id
     *
     * @param int $id_seller
     * @return array
     */
    public static function getDetailsByIdSeller($id_seller)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_payment` WHERE `id_seller`='.(int) $id_seller);
    }

    /**
     * Update total earning and total due of any seller
     *
     * @param float $earning
     * @param float $due
     * @param int $id_seller
     * @param int $id_currency
     * @return array
     */
    public function updateEarningAndDueByIdSellerAndIdCurrency($earning, $due, $id_seller, $id_currency)
    {
        $result = false;
        if (isset($earning) && isset($due) && isset($id_seller) && isset($id_currency)) {
            $is_updated = Db::getInstance()->update('wk_mp_seller_payment', array('total_earning' => (float) $earning, 'total_due' => (float) $due), '`id_seller` = '.(int) $id_seller.' AND `id_currency` = '.(int) $id_currency);
            if ($is_updated) {
                $result = $is_updated;
            }
        }

        return $result;
    }

    /**
     * Get customer payment details according to customer id
     *
     * @param int $id_customer
     * @return bool/array
     */
    public function getCustomerPaymentDetails($id_customer)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_customer_payment_detail` WHERE `seller_customer_id` = '.(int) $id_customer);
    }

    public function getPaymentModeById($payment_mode_id)
    {
        return Db::getInstance()->getValue('SELECT `payment_mode` FROM `'._DB_PREFIX_.'wk_mp_payment_mode` WHERE `id_mp_payment` = '.(int) $payment_mode_id);
    }
}
