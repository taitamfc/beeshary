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

class MpSellerPaymentChangeStatusModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function initContent()
    {
        $id_seller = Tools::getValue('id_seller');
        $id_currency = Tools::getValue('id_currency');
        $id_transaction = Tools::getValue('id_transaction');
        $status = Tools::getValue('check_status');

        $obj_sellerpayment = new MarketplaceSellerPayment();
        $seller_payment_data = $obj_sellerpayment->getSellerPaymentBySellerId($id_seller, $id_currency);
        if ($seller_payment_data) {
            $obj_sellertransaction = new MpSellerPaymentTransactions();
            $seller_transaction_data = $obj_sellertransaction->getSellerTransactionById($id_transaction);
            if ($seller_transaction_data) {
                $amount = $seller_transaction_data['amount'];

                if ($status == '1') {
                    $total_paid = $seller_payment_data['total_paid'] - $amount;
                    $total_due = $seller_payment_data['total_due'] + $amount;
                    $update_payment = $obj_sellerpayment->updateSellerPayment($total_paid, $total_due, $id_seller, $id_currency);
                    if ($update_payment) {
                        $change_status = 0;
                        $update_status = $obj_sellertransaction->updateTransactionStatus($id_transaction, $change_status);
                        if ($update_status) {
                            echo 1;
                        } else {
                            echo 0;
                        }
                    } else {
                        echo 0;
                    }
                } else {

                    if ($seller_payment_data['total_due'] < $amount) {
                        echo -1;
                    } else {
                        $total_paid = $seller_payment_data['total_paid'] + $amount;
                        $total_due = $seller_payment_data['total_due'] - $amount;
                        $update_payment = $obj_sellerpayment->updateSellerPayment($total_paid, $total_due, $id_seller, $id_currency);
                        if ($update_payment) {
                            $change_status = 1;
                            $update_status = $obj_sellertransaction->updateTransactionStatus($id_transaction, $change_status);
                            if ($update_status) {
                                echo 1;
                            } else {
                                echo 0;
                            }
                        } else {
                            echo 0;
                        }
                    }
                }

                die; //Close ajax
            }
        }
    }
}
