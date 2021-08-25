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

class AdminSellerPaymentController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'MarketplaceSellerPayment';
        $this->table = 'marketplace_seller_payment';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info` cust ON (cust.`id` = a.`id_seller`)';
        $this->_select = 'seller_name as name';
        $this->_group = 'GROUP BY a.`id_seller`';
        $this->bootstrap = true;
        $this->identifier = 'id_seller';
        parent::__construct();
        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('Id'),
                'align' => 'center',
                'search' => false,
            ),
            'name' => array(
                'title' => $this->l('Seller Name'),
                'align' => 'center',
                'search' => false,
            ),
            'total_earning' => array(
                'title' => $this->l('Total Earning'),
                'align' => 'center',
                'type' => 'price',
                'callback' => 'CallTotalEarning',
                'search' => false,
            ),
            'total_paid' => array(
                'title' => $this->l('Total Paid'),
                'align' => 'center',
                'type' => 'price',
                'callback' => 'CallTotalPaid',
                'search' => false,
            ),
            'total_due' => array(
                'title' => $this->l('Total Due'),
                'align' => 'center',
                'type' => 'price',
                'callback' => 'CallTotalDue',
                'search' => false,
            ),
        );

    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->addRowAction('edit');
        // Remove "Add" button from toolbar
        unset($this->toolbar_btn['new']);
    }

    public function CallTotalEarning($value, $rowarr)
    {
        $default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $total = 0;
        $data_arr = MarketplaceSellerPayment::getDetailsByIdSeller($rowarr['id_seller']);
        if ($data_arr) {
            foreach ($data_arr as $data) {
                $total += Tools::convertPriceFull($data['total_earning'], new Currency($data['id_currency']), new Currency($default_currency));
            }
        }

        return Tools::displayPrice($total);
    }

    public function CallTotalPaid($value, $rowarr)
    {
        $default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $total = 0;
        $data_arr = MarketplaceSellerPayment::getDetailsByIdSeller($rowarr['id_seller']);
        if ($data_arr) {
            foreach ($data_arr as $data) {
                $total += Tools::convertPriceFull($data['total_paid'], new Currency($data['id_currency']), new Currency($default_currency));
            }
        }

        return Tools::displayPrice($total);
    }

    public function CallTotalDue($value, $rowarr)
    {
        $default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $total = 0;
        $data_arr = MarketplaceSellerPayment::getDetailsByIdSeller($rowarr['id_seller']);
        if ($data_arr) {
            foreach ($data_arr as $data) {
                $total += Tools::convertPriceFull($data['total_due'], new Currency($data['id_currency']), new Currency($default_currency));
            }
        }

        return Tools::displayPrice($total);
    }

    public function renderForm()
    {
        $id_seller = Tools::getValue('id_seller');
        if ($id_seller) {
            $seller_info = new SellerInfoDetail($id_seller);

            $id_customer = $seller_info->seller_customer_id;
            $seller = array('name' => $seller_info->seller_name, 'email' => $seller_info->business_email);
            $this->context->smarty->assign('seller', $seller);

            $obj_mpsellerpayment = new MarketplaceSellerPayment();

            $payment_mode_details = $obj_mpsellerpayment->getCustomerPaymentDetails($id_customer);
            if ($payment_mode_details) {
                $payment_mode = $obj_mpsellerpayment->getPaymentModeById($payment_mode_details['payment_mode_id']);
                $this->context->smarty->assign('payment_mode', $payment_mode);
                $this->context->smarty->assign('payment_mode_details', $payment_mode_details['payment_detail']);
            } else {
                $this->context->smarty->assign('payment_mode', 'N/A');
                $this->context->smarty->assign('payment_mode_details', 'N/A');
            }

            $payment = MarketplaceSellerPayment::getDetailsByIdSeller($id_seller);
            $k = 0;
            if ($payment) {
                $payment_currency = array();
                foreach ($payment as $data) {
                    $payment_currency[$k]['id_seller'] = $data['id_seller'];
                    $payment_currency[$k]['id'] = $data['id'];
                    $payment_currency[$k]['total_earning'] = $data['total_earning'];
                    $payment_currency[$k]['total_paid'] = $data['total_paid'];
                    $payment_currency[$k]['total_due'] = $data['total_due'];

                    $currency_dtls = new Currency($data['id_currency']);
                    $payment_currency[$k]['iso_code'] = $currency_dtls->iso_code;
                    $payment_currency[$k]['sign'] = $currency_dtls->sign;
                    $payment_currency[$k]['id_currency'] = $currency_dtls->id;

                    ++$k;
                }
                $this->context->smarty->assign('payment_currency', $payment_currency);
            }

            $payment_transactions_details = array();
            $payment_transactions = MpSellerPaymentTransactions::getDeatilsByIdSellerAndOrderBy($id_seller, 'date_add desc');
            if (Module::isInstalled('mpsellerwallet') && Module::isEnabled('mpsellerwallet')) {
				$this->context->smarty->assign('wallet_not_exists', 0);
            } else {
                $this->context->smarty->assign('wallet_not_exists', 1);
            }
            if ($payment_transactions) {
                $i = 0;
                foreach ($payment_transactions as $payment_data) {
                    //Code for working on voucher refund and create when cancel the payment or pay again
                    if (Module::isInstalled('mpsellerwallet') && Module::isEnabled('mpsellerwallet')) {
                        $wallet_info_obj = new MarketplaceSellerWallet();
                        $wallet_info = $wallet_info_obj->getWalletInfoByTransactionId($payment_data['id']);
                        if ($wallet_info) {
                            $payment_transactions_details[$i]['wallet_info_id'] = $wallet_info['id'];
                            $payment_transactions_details[$i]['voucher_amt'] = $wallet_info['wallet_amt'];
                            $payment_transactions_details[$i]['voucher_code'] = $wallet_info['voucher_code'];
                            $payment_transactions_details[$i]['seller_payment_id'] = $wallet_info['seller_payment_table_id'];
                        }
                    }
                    //END
                    $currency_data = new Currency($payment_data['id_currency']);
                    $payment_transactions_details[$i]['id'] = $payment_data['id'];
                    $payment_transactions_details[$i]['amount'] = Tools::convertPrice($payment_data['amount']);
                    $payment_transactions_details[$i]['date'] = $payment_data['date_add'];
                    $payment_transactions_details[$i]['type'] = $payment_data['type'];
                    $payment_transactions_details[$i]['status'] = $payment_data['status'];
                    $payment_transactions_details[$i]['currency'] = $currency_data->iso_code;
                    $payment_transactions_details[$i]['sign'] = $currency_data->sign;
                    $payment_transactions_details[$i]['id_currency'] = $payment_data['id_currency'];
                    $obj_sellerpayment = new MarketplaceSellerPayment();
                    $seller_payment_data = $obj_sellerpayment->getSellerPaymentBySellerId($id_seller, $payment_data['id_currency']);
                    $payment_transactions_details[$i]['transaction_seller_payment_id'] = $seller_payment_data['id'];
                    ++$i;
                }
            }
            $this->context->smarty->assign('payment_transactions_details', $payment_transactions_details);
            $this->context->smarty->assign('id_seller', $id_seller);
        }

        $this->fields_form = array(
                    'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button',
                    ),
                );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submit_btn')) {
            $amount = Tools::getValue('amount');
            $id_seller = Tools::getValue('id_seller');
            $transaction_id = Tools::getValue('transaction_id');
            $id_currency = Tools::getValue('id_currency');

            $obj_mp_seller_payment_trans = new MpSellerPaymentTransactions();
            $obj_mp_seller_payment_trans->id_seller = $id_seller;
            $obj_mp_seller_payment_trans->id_currency = $id_currency;
            $obj_mp_seller_payment_trans->amount = $amount;
            $obj_mp_seller_payment_trans->type = 'Payment';
            $obj_mp_seller_payment_trans->status = 1;
            $insert_transaction = $obj_mp_seller_payment_trans->save();

            if ($insert_transaction) {
                $obj_mp_seller_payment = new MarketplaceSellerPayment($transaction_id);
                $total_paid = $obj_mp_seller_payment->total_paid + $amount;
                $total_due = $obj_mp_seller_payment->total_due - $amount;

                $obj_mp_seller_payment->total_paid = $total_paid;
                $obj_mp_seller_payment->total_due = $total_due;
                $update_transaction = $obj_mp_seller_payment->save();

                if ($update_transaction) {
                    $this->context->smarty->assign('check_transaction', 1);
                } else {
                    $this->context->smarty->assign('check_transaction', 2);
                }
            } else {
                $this->context->smarty->assign('check_transaction', 2);
            }

            Tools::redirectAdmin(self::$currentIndex.'&conf=30&token='.$this->token.'&id_seller='.$id_seller.'&updatemarketplace_seller_payment');
        }

        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS(_MODULE_DIR_.'mpsellerpayment/views/css/seller_payment.css');
        $this->addJS(_MODULE_DIR_.'mpsellerpayment/views/js/sellerpayment.js');
    }
}
