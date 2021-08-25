<?php
/*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*/

class MpSellerOrderInvoiceRecord extends ObjectModel
{
    public $id_order_invoice;
    public $invoice_number;
    public $id_order;
    public $id_seller;

    public static $definition = array(
        'table' => 'mp_seller_order_invoice',
        'primary' => 'id_order_invoice',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'invoice_number' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            ),
        );

    public function getOrderInvoiceNumber($id_order, $id_seller)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'mp_seller_order_invoice
                where
                    `id_order` ='.(int) $id_order.' AND
                    `id_seller` ='.(int) $id_seller
        );
    }


    public function getLastRowByIdSeller($idSeller)
    {
        return Db::getInstance()->getValue(
            'SELECT `invoice_number` FROM `'._DB_PREFIX_.'mp_seller_order_invoice`
                WHERE `id_seller` = '.(int) $idSeller.' ORDER BY `invoice_number` DESC'
        );
    }

    public function isSellerInvoiceExist($idSeller)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'mp_seller_order_invoice` WHERE `id_seller` = '.(int) $idSeller
        );
    }

    /**
     * Get Commission details from the individual order.
     *
     * @param int $id_order Order ID
     *
     * @return Float calculated comimission
     */
    public function getSellersCustomerIdByOrderId($idOrder)
    {
        $details = Db::getInstance()->executeS(
            'SELECT DISTINCT `seller_customer_id` FROM `'._DB_PREFIX_.'wk_mp_seller_order_detail`
            WHERE `id_order` = '.(int) $idOrder
        );
        if ($details) {
            return $details;
        }

        return false;
    }
}
