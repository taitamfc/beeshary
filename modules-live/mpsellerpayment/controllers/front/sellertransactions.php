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

class MpSellerPaymentSellerTransactionsModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Seller Payment', [], 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('mpsellerpayment', 'sellertransactions')
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Seller Transactions', [], 'Breadcrumb'),
            'url' => ''
        ];

        return $breadcrumb;
    }
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $id_customer = $this->context->customer->id;
        if (isset($id_customer)) {
            $obj_marketplace_seller = new SellerInfoDetail();
            $mp_seller = $obj_marketplace_seller->getSellerDetailsByCustomerId($id_customer);
            if ($mp_seller && $mp_seller['active']) {
                $id_seller = $mp_seller['id'];
                $login = 1;
                $obj_mpsellerpayment = new MarketplaceSellerPayment();
                $payment = MarketplaceSellerPayment::getDetailsByIdSeller($id_seller);
                $payment_currency = array();
                if ($payment) {
                    foreach ($payment as $key => $data) {
                        $currency_dtls = new Currency($data['id_currency']);
                        $payment_currency[$key]['iso_code'] = $currency_dtls->iso_code;
                        $payment_currency[$key]['sign'] = $currency_dtls->sign;
                        $payment_currency[$key]['id_currency'] = $currency_dtls->id;
                        $payment_currency[$key]['id_seller'] = $data['id_seller'];
                        $payment_currency[$key]['id'] = $data['id'];
                        $payment_currency[$key]['total_earning'] = $data['total_earning'];
                        $payment_currency[$key]['total_paid'] = $data['total_paid'];
                        $payment_currency[$key]['total_due'] = $data['total_due'];
                    }
                }
                if (!empty($payment_currency)) {
                    $this->context->smarty->assign('payment_currency', $payment_currency);
                }

                $seller_payment_transaction = MpSellerPaymentTransactions::getDetailsByIdSeller($id_seller);

                if ($seller_payment_transaction) {
                    $i = 0;
                    $payment_transactions_details = array();
                    foreach ($seller_payment_transaction as $payment_data) {
                        $currency_data = new Currency($payment_data['id_currency']);
                        $payment_transactions_details[$i]['currency'] = $currency_data->iso_code;
                        $payment_transactions_details[$i]['sign'] = $currency_data->sign;
                        $payment_transactions_details[$i]['id_seller'] = $payment_data['id_seller'];
                        $payment_transactions_details[$i]['id'] = $payment_data['id'];
                        $payment_transactions_details[$i]['amount'] = $payment_data['amount'];
                        $payment_transactions_details[$i]['date'] = $payment_data['date_add'];
                        $payment_transactions_details[$i]['type'] = $payment_data['type'];
                        $payment_transactions_details[$i]['status'] = $payment_data['status'];
                        ++$i;
                    }
                    $this->context->smarty->assign('payment_transactions', $payment_transactions_details);
                }

                $this->context->smarty->assign('login', $login);
                $this->context->smarty->assign('is_seller', 1);
                $this->context->smarty->assign('id_customer', $id_customer);
                $this->context->smarty->assign('logic', 'seller_trans');
                $this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
                $this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
                $this->setTemplate('module:mpsellerpayment/views/templates/front/seller_payment_transactions.tpl');
            }
        } else {
            Tools::redirect($link->getPageLink('my-account'));
        }
    }
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
    }
}
