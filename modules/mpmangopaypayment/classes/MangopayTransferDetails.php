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

class MangopayTransferDetails extends ObjectModel
{
    public $id;
    public $order_reference;
    public $mgp_clientid;
    public $buyer_id_customer;
    public $id_seller;
    public $currency;
    public $amount;
    public $fees;
    public $transfer_id;
    public $refund_transfer_id;
    public $is_refunded;
    public $refunded_by;
    public $send_to_card;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_mangopay_transfer_details',
        'primary' => 'id',
        'fields' => array(
            'order_reference' => array('type' => self::TYPE_STRING),
            'mgp_clientid' => array('type' => self::TYPE_STRING),
            'buyer_id_customer' => array('type' => self::TYPE_INT),
            'id_seller' => array('type' => self::TYPE_INT),
            'currency' => array('type' => self::TYPE_STRING),
            'amount' => array('type' => self::TYPE_FLOAT),
            'fees' => array('type' => self::TYPE_FLOAT),
            'transfer_id' => array('type' => self::TYPE_INT),
            'is_refunded' => array('type' => self::TYPE_INT),
            'refunded_by' => array('type' => self::TYPE_STRING),
            'send_to_card' => array('type' => self::TYPE_INT),
            'refund_transfer_id' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Get the transfer detail by client.
     * @param int $id_client
     * @return array
     */
    public function getAllTransferDetailsByClientid($id_client)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_transfer_details`
            WHERE `mgp_clientid` = \''.pSQL($id_client).'\''
        );
    }

    /**
     * Get all transfer details by seller
     * @param int $id_client
     * @param int $id_seller
     * @param string $orderReference
     * @return array
     */
    public function getAllTransferDetailsBySellerByOrderReference($id_client, $id_seller, $orderReference)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_transfer_details`
            WHERE `mgp_clientid` = \''.pSQL($id_client).'\''.'
            AND `id_seller`='.(int) $id_seller.'
            AND `order_reference`= \''.pSQL($orderReference).'\''
        );
    }

    /**
     * Update the refund details in transfer detail table.
     * @param int $id_transfer
     * @param int $refund_transfer_id
     * @param string $refundedBy
     * @return bool
     */
    public function updateRefundDetailsByTransferId($id_transfer, $refund_transfer_id, $refundedBy)
    {
        $table = 'wk_mp_mangopay_transfer_details';
        $data = array(
            'is_refunded' => 1,
            'refund_transfer_id' => (int) $refund_transfer_id,
            'refunded_by' => pSQL($refundedBy)
        );
        $where = 'transfer_id = '.(int)$id_transfer;
        $result = Db::getInstance()->update($table, $data, $where);

        return $result;
    }

    /**
     * get the transfewr detail according to particular order reference pass in the argument.
     * @param string $orderReference
     * @return array
     */
    public function getTransferDetailsByOrderReference($orderReference)
    {
        return Db::getInstance()->executeS(
            'SELECT mtd.*, mt.`buyer_mgp_userid` FROM `'._DB_PREFIX_.'wk_mp_mangopay_transfer_details` mtd
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_mangopay_transaction` mt ON (mt.`order_reference` = mtd.`order_reference`)
            WHERE mtd.`order_reference` = \''.pSQL($orderReference).'\''
        );
    }

    /**
     * Get order details by cart.
     * @param int $id_cart
     * @param int $id_product
     * @param int $idProductAttribute
     * @return array
     */
    public function getOrderTimeProductData($id_cart, $id_product, $idProductAttribute)
    {
        return  Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'order_detail
            where id_order IN (SELECT id_order FROM '._DB_PREFIX_.'orders where id_cart='.(int) $id_cart.')
            AND product_id ='.(int) $id_product.' AND product_attribute_id = '.(int) $idProductAttribute
        );
    }

    /**
     * Update the payin refunds by id seller.
     * @param int $id_seller
     * @param string $orderReference
     * @return bool
     */
    public function updatePayInRefundDetailsBySellerIdOrderReference($id_seller, $orderReference)
    {
        $table = 'wk_mp_mangopay_transfer_details';
        $data = array('send_to_card' => 1);
        $where = 'id_seller = '.(int)$id_seller.' AND order_reference = \''.pSQL($orderReference).'\'';
        return Db::getInstance()->update($table, $data, $where);
    }

    /**
     * send mail to admin form seller on refund
     * @param int $idSeller
     * @param string $orderRefrence
     * @param int $refundType
     * @return void
     */
    public static function sendMailToAdminOnSellerRefund($idSeller, $orderReference, $refundType)
    {
        $objMpSeller = new WkMpSeller($idSeller);
        $objMgpTransfer = new self();
        $orderId = $objMgpTransfer->getOrderIdByOrderReference($orderReference);
        $link = new Link();
        $templateVars = array(
            '{seller_firstname}' => $objMpSeller->seller_firstname,
            '{seller_lastname}' => $objMpSeller->seller_lastname,
            '{seller_email}' => $objMpSeller->business_email,
            '{order_reference}' => $orderReference,
            '{order_id}' => $orderId,
            '{order_link}' => $link->getAdminLink('AdminOrders').'&id_order='.$orderId.'&vieworder',
        );
        if (Configuration::get('WK_MP_SUPERADMIN_EMAIL')) {
            $adminEmail = Configuration::get('WK_MP_SUPERADMIN_EMAIL');
        } else {
            $id_employee = MangopayConfig::getSupperAdmin();
            $obj_customer = new Employee($id_employee);
            $adminEmail = $obj_customer->email;
        }
        $businessEmail = $objMpSeller->business_email;
        $sellerName = $objMpSeller->seller_firstname .' '.$objMpSeller->seller_lastname;
        $tempPath = _PS_MODULE_DIR_.'mpmangopaypayment/mails/';
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        if ($refundType == 1) {
            $templateVars['{refunded_to}'] = 'Wallet';
        } else {
            $templateVars['{refunded_to}'] = 'Card';
        }
        return Mail::Send(
            $defaultLangId,
            'refund_by_seller',
            Mail::l('Refunded By Seller', $defaultLangId),
            $templateVars,
            $adminEmail,
            null,
            $businessEmail,
            $sellerName,
            null,
            null,
            $tempPath,
            false,
            null,
            null
        );
    }

    /**
     *  send mail to seller form admin on refund
     * @param int $idSeller
     * @param string $orderReference
     * @param int $refunded_type
     * @return void
     */
    public static function sendMailToSellerOnAdminRefund($idSeller, $orderReference, $refunded_type)
    {
        $objModule = new MpMangopayPayment();
        $objMpSeller = new WkMpSeller($idSeller);
        $objMgpTransfer = new self();
        $orderId = $objMgpTransfer->getOrderIdByOrderReference($orderReference);
        $templateVars = array(
            '{seller_firstname}' => $objMpSeller->seller_firstname,
            '{seller_lastname}' => $objMpSeller->seller_lastname,
            '{order_reference}' => $orderReference,
            '{order_id}' => $orderId,
        );
        $lang = $objMpSeller->default_lang;
        if ($refunded_type == 1) {//for transfer refund from seller wallet
            $templateVars['{refund_type}'] = $objModule->l('Transfer refund from your wallet');
        } else {
            $templateVars['{refund_type}'] = $objModule->l('PayIn amount has been refunded from admin wallet');
        }
        if (Configuration::get('WK_MP_SUPERADMIN_EMAIL')) {
            $adminEmail = Configuration::get('WK_MP_SUPERADMIN_EMAIL');
        } else {
            $id_employee = MangopayConfig::getSupperAdmin();
            $obj_customer = new Employee($id_employee);
            $adminEmail = $obj_customer->email;
        }
        $businessEmail = $objMpSeller->business_email;
        $sellerName = $objMpSeller->seller_firstname . ' ' .$objMpSeller->seller_lastname;
        $tempPath = _PS_MODULE_DIR_.'mpmangopaypayment/mails/';
        return Mail::Send(
            $lang,
            'refund_by_admin',
            Mail::l('Refunded By Admin', $lang),
            $templateVars,
            $businessEmail,
            $sellerName,
            $adminEmail,
            null,
            null,
            null,
            $tempPath,
            false,
            null,
            null
        );
    }

    /**
     * Get the seller id by transfer id.
     * @param int $transferId
     * @param string $orderReference
     * @return int
     */
    public function getSellerIdByTransferIdAndOrderReference($transferId, $orderReference)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_seller` FROM `'._DB_PREFIX_.'wk_mp_mangopay_transfer_details`
            WHERE `transfer_id` = '.(int) $transferId.' AND `order_reference` = \''.pSQL($orderReference).'\''
        );
    }

    /**
     * Get the transfer detail by transfer id.
     * @param int $transferId
     * @return array
     */
    public function getTransferDetailByTransferId($transferId)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_transfer_details`
            WHERE `transfer_id` = '.(int) $transferId
        );
    }

    /**
     * get orderId by order reference
     * @param [type] $orderReference
     * @return void
     */
    public function getOrderIdByOrderReference($orderReference)
    {
        return (int)Db::getInstance()->getValue(
            'SELECT `id_order`
            FROM `'._DB_PREFIX_.'orders`
            WHERE `reference` = \''.pSQL($orderReference).'\''
        );
    }
}
