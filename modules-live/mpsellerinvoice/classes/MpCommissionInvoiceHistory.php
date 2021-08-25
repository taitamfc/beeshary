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

class MpCommissionInvoiceHistory extends ObjectModel
{
    public $id_seller;
    public $invoice_number;
    public $invoice_based;
    public $from;
    public $to;
    public $orders;
    public $last_notification;
    public $is_send_to_seller;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_admin_commission_invoice_history',
        'primary' => 'id',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'invoice_number' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'invoice_based' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'from' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'to' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'orders' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'last_notification' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'is_send_to_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            ),
        );

    public function getOrderInvoiceNumber($id_order, $id_seller)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'mp_admin_commission_invoice_history
                where
                    `orders` ='.(int) $id_order.' AND
                    `id_seller` ='.(int) $id_seller
        );
    }

    public function getLastRowByIdSeller($idSeller)
    {
        return Db::getInstance()->getValue(
            'SELECT `invoice_number` FROM `'._DB_PREFIX_.'mp_admin_commission_invoice_history`
                WHERE `id_seller` = '.(int) $idSeller.' ORDER BY `invoice_number` DESC'
        );
    }

    public function getLastRowId()
    {
        return Db::getInstance()->getValue(
            'SELECT `invoice_number` FROM `'._DB_PREFIX_.'mp_admin_commission_invoice_history`
                WHERE 1 ORDER BY `invoice_number` DESC'
        );
    }

    public function createInvoiceHistory($idSeller, $idOrders = 0, $from = null, $to = null)
    {
        $generateLog = 0;
        if ($idSeller) {
            $invoiceNumber = $this->getLastRowId();
            if ($invoiceNumber) {
                ++$invoiceNumber;
            } else {
                $invoiceNumber = 1;
            }
            $objInvoiceConfig = new MpSellerInvoiceConfig();
            $result = $objInvoiceConfig->isSellerInvoiceConfigExist($idSeller);
            if ($result) {
                if (!$from) {
                    $from = date('Y-m-d H:i:m');
                }

                if (!$to) {
                    $to = date('Y-m-d H:i:m');
                }
                $commissionInvoiceHistory = array();
                if (!$this->getOrderInvoiceNumber($idOrders, $idSeller)) {
                    $invoiceHistory = new self();
                    $invoiceHistory->id_seller = (int) $idSeller;
                    $invoiceHistory->invoice_number = (int) $invoiceNumber;
                    $invoiceHistory->invoice_based = (int) $result['invoice_based'];
                    $invoiceHistory->from = $from;
                    $invoiceHistory->to = $to;
                    $invoiceHistory->orders = $idOrders;
                    //To check if enable then commission invoice is send to seller
                    if (Configuration::get('MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC')) {
                        $invoiceHistory->is_send_to_seller = 1;
                    }
                    $invoiceHistory->save();
                    if ($invoiceHistory->save()) {
                        $generateLog = 1;
                        $commissionInvoiceHistory['invoice_number'] = (int) $invoiceNumber;
                        $commissionInvoiceHistory['to'] = $to;
                        $commissionInvoiceHistory['from'] = $from;
                    }
                } else {
                    $generateLog = 1;
                }
                if ($generateLog) {
                    if (Configuration::get('MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC')) {
                        if ($result['invoice_based'] == 1) {
                            $order = new Order((int) $idOrders);
                            $seller = new WkMpSeller($result['id_seller']);
                            $sellerCustomerId = $seller->seller_customer_id;
                            $create_commission_invoice = new CreateCommissionInvoice();
                            $adminAttachmentPDF = $create_commission_invoice->createInvoice(
                                $idOrders,
                                $sellerCustomerId,
                                false,
                                true,
                                $commissionInvoiceHistory,
                                true
                                // $result
                            );
                            $create_commission_invoice->sendCommissionInvoice(
                                $seller->business_email,
                                $adminAttachmentPDF,
                                $seller
                            );
                        } elseif ($result['invoice_based'] == 2 || $result['invoice_based'] == 3) {
                            $seller = new WkMpSeller($result['id_seller']);
                            $sellerCustomerId = $seller->seller_customer_id;
                            $create_commission_invoice = new CreateCommissionInvoice();
                            $adminAttachmentPDF = $create_commission_invoice->createInvoice(
                                $idOrders,
                                $sellerCustomerId,
                                false,
                                true,
                                $commissionInvoiceHistory
                                // $result
                            );
                            $create_commission_invoice->sendCommissionInvoice(
                                $seller->business_email,
                                $adminAttachmentPDF,
                                $seller
                            );
                        }
                    }
                    if ($generateLog) {
                        $objInvoiceConfig = new MpSellerInvoiceConfig($result['id']);
                        $objInvoiceConfig->id_seller = (int) $idSeller;
                        $objInvoiceConfig->last_generated = $to;
                        $objInvoiceConfig->update();
                    }
                }
            }
        }
    }

    public function updateSellerInvoiceBased(
        $idSeller,
        $type,
        $seller_invoice_value,
        $seller_invoice_interval
    ) {
        if ($result = $this->isSellerInvoiceExist($idSeller)) {
            Db::getInstance()->update(
                'mp_seller_order_invoice_based',
                array(
                    'id_seller' => (int) $idSeller,
                    'invoice_based' => (int) $type,
                    'value' => $seller_invoice_value,
                    'time_interval' => $seller_invoice_interval,
                    'last_generated' => $result['last_generated'],
                ),
                '`id_seller` = '.(int) $idSeller
            );
        } else {
            Db::getInstance()->insert(
                'mp_seller_order_invoice_based',
                array(
                    'id_seller' => (int) $idSeller,
                    'invoice_based' => (int) $type,
                    'value' => $seller_invoice_value,
                    'time_interval' => $seller_invoice_interval,
                    'last_generated' => date('Y-m-d H:i:s'),
                )
            );
        }
    }

    public function updateLastGeneratedInvoice(
        $idSeller,
        $type,
        $value,
        $interval,
        $last_generated
    ) {
        Db::getInstance()->update(
            'mp_seller_order_invoice_based',
            array(
                'id_seller' => (int) $idSeller,
                'invoice_based' => (int) $type,
                'value' => $value,
                'time_interval' => $interval,
                'last_generated' => $last_generated,
            ),
            '`id_seller` = '.(int) $idSeller
        );
    }

    public static function getSellerInvoiceHistory($idSeller)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'mp_admin_commission_invoice_history` WHERE `id_seller` = '.(int) $idSeller
        );
    }

    public static function getSellerInvoiceHistoryByInvoiceNumber($invoiceNumber)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'mp_admin_commission_invoice_history`
                WHERE `invoice_number` = '.(int) $invoiceNumber
        );
    }

    public static function getSellerExistingOrder($idSeller, $dateFrom, $dateTo)
    {
        $dateFrom = '1990-01-01 00:00:00';
        $sql = 'SELECT `orders` FROM `'._DB_PREFIX_.'mp_admin_commission_invoice_history` AS sih
            WHERE
                sih.`id_seller` = '.(int) $idSeller.' AND (sih.`to` >= \''.pSQL($dateFrom).'\' OR sih.`from`  <= \''.pSQL($dateTo).'\' OR (sih.`from` >= \''.pSQL($dateFrom).'\' AND sih.`to`<= \''.pSQL($dateTo).'\'))';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getSellerOrders($sellerCustomerId, $dateFrom, $dateTo)
    {
        $sellerOrderStatus = array('0');
        if ($sellerOrderStatus = Tools::jsonDecode(Configuration::get('MP_SELLER_INVOICE_ORDER_STATUS'))) {
            $sellerOrderStatus = implode(',', $sellerOrderStatus);
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT o.`id_order`, SUM(sod.`admin_commission`) + SUM(sod.`admin_tax`) admin_commission_earned
                FROM `'._DB_PREFIX_.'wk_mp_seller_order_detail` sod
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON (sod.`id_order` = o.`id_order`)
                LEFT JOIN `'._DB_PREFIX_.'order_detail` ordd ON (ordd.`product_id` = sod.`product_id` AND ordd.`id_order` = sod.`id_order`)
                WHERE
                    o.`current_state` IN ('.pSQL($sellerOrderStatus).') AND
                    sod.`seller_customer_id` = '.(int) $sellerCustomerId.' AND
                    sod.`date_add` BETWEEN \''.pSQL($dateFrom).'\' AND \''.pSQL($dateTo).'\'
                    GROUP BY sod.`date_add`'
        );

        return $result;
    }

    public function checkRecord($idSeller, $startDate, $endDate)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'mp_admin_commission_invoice_history
                WHERE
                    `id_seller` ='.(int) $idSeller.' AND
                    `from` = \''.pSQL($startDate).'\' AND
                    `to` = \''.pSQL($endDate).'\''
        );
    }
}
