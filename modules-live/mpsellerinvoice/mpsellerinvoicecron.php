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
include_once '../../config/config.inc.php';
include_once 'mpsellerinvoice.php';

class MpSellerInvoiceCron extends mpsellerinvoice
{
    public function __construct()
    {
        parent::__construct();
        $this->updateSellerInvoice();
    }

    public function updateSellerInvoice()
    {

        $objMpSellerInvoice = new MpSellerInvoice();
        if (Tools::getValue('token') != $objMpSellerInvoice->secure_key) {
            if ($errorLog = fopen(_PS_MODULE_DIR_.'/mpsellerinvoice/error_log', 'a+')) {
                $now = new DateTime();
                $txt = '['.$now->format('Y-m-d H:i:s').'] : ';
                $txt .= 'Failed to create admin commission:  Token Invalid';
                fwrite($errorLog, $txt."\n");
            }
            fclose($errorLog);
            die('Something went wrong.');
        }
        $objRecord = new MpSellerInvoiceConfig();
        $invoiceHistory = new MpCommissionInvoiceHistory();
        $allSeller = WkMpSeller::getAllSeller(false, false, $this->context->language->id);
        if ($allSeller) {
            foreach ($allSeller as $seller) {
                $idSeller = $seller['id_seller'];
                $result = $objRecord->isSellerInvoiceConfigExist($idSeller);
                if ($result) {
                    if ($result['invoice_based'] == 2) { // Time Interval
                        $timeInterval = $result['last_generated'];
                        if ($timeInterval == '0000-00-00 00:00:00') {
                            $lastGenerated = '1970-01-01';
                        } else {
                            $lastGenerated = date('Y-m-d', strtotime($timeInterval));
                        }
                        $generateDate = date('Y-m-d', strtotime(' - '.$result['value'].' '.$result['time_interval']));
                        $currentDate = date('Y-m-d');
                        if ($lastGenerated <= $generateDate) {
                            $result = MpCommissionInvoiceHistory::getSellerOrders(
                                $seller['seller_customer_id'],
                                $lastGenerated,
                                $currentDate
                            );
                            $existing = array();
                            $idOrders = array();

                            if ($result) {
                                $existingOrders = MpCommissionInvoiceHistory::getSellerExistingOrder(
                                    $idSeller,
                                    $lastGenerated,
                                    $currentDate
                                );
                                if ($existingOrders) {
                                    $existing = explode(',', implode(',', array_column($existingOrders, 'orders')));
                                }

                                foreach ($result as $data) {
                                    if ($existing) {
                                        if (!in_array($data['id_order'], $existing)) {
                                            $idOrders[] = $data['id_order'];
                                        }
                                    } else {
                                        $idOrders[] = $data['id_order'];
                                    }
                                }
                            }

                            if (!empty($idOrders)) {
                                $orders = implode(',', $idOrders);
                                if (!$invoiceHistory->checkRecord($idSeller, $lastGenerated, $currentDate)) {
                                    $invoiceHistory->createInvoiceHistory(
                                        $idSeller,
                                        $orders,
                                        $lastGenerated,
                                        $currentDate
                                    );
                                }
                            }
                        }
                    } elseif ($result['invoice_based'] == 3) { // invoice on Threshold amount
                        $timeInterval = $result['last_generated'];
                        if ($timeInterval == '0000-00-00 00:00:00') {
                            $lastGenerated = '1970-01-01';
                        } else {
                            $lastGenerated = date('Y-m-d', strtotime($timeInterval));
                        }
                        $lastGenerated = date('Y-m-d H:i:s', strtotime($timeInterval));
                        $currentDate = date('Y-m-d H:i:s');
                        $sellerOrders = MpCommissionInvoiceHistory::getSellerOrders(
                            $seller['seller_customer_id'],
                            $lastGenerated,
                            $currentDate
                        );
                        $existing = array();
                        if ($sellerOrders) {
                            $existingOrders = MpCommissionInvoiceHistory::getSellerExistingOrder(
                                $idSeller,
                                $lastGenerated,
                                $currentDate
                            );
                            if ($existingOrders) {
                                $existing = explode(',', implode(',', array_column($existingOrders, 'orders')));
                            }
                            $commissionValue = 0;
                            $idOrders = array();
                            foreach ($sellerOrders as $data) {
                                if ($existing) {
                                    if (!in_array($data['id_order'], $existing)) {
                                        $idOrders[] = $data['id_order'];
                                        $commissionValue += $data['admin_commission_earned'];
                                    }
                                } else {
                                    $idOrders[] = $data['id_order'];
                                    $commissionValue += $data['admin_commission_earned'];
                                }
                            }
                            if ($commissionValue > $result['value']) {
                                if ($idOrders) {
                                    $orders = implode(',', $idOrders);
                                    if (!$invoiceHistory->checkRecord($idSeller, $lastGenerated, $currentDate)) {
                                        $invoiceHistory->createInvoiceHistory(
                                            $idSeller,
                                            $orders,
                                            $lastGenerated,
                                            $currentDate
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            die('DONE');
        }
    }
}

new MpSellerInvoiceCron();
