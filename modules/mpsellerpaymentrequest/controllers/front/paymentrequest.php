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

class MpSellerPaymentRequestPaymentRequestModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        if (Tools::getValue('conf') == 1) {
            $this->success[] = $this->module->l('Request submitted successfully.', 'paymentrequest');
        }
    }
    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)) {
            $idCustomer = $this->context->customer->id;
            if (Module::isEnabled('mpsellerstaff')) {
                $getCustomerId = WkMpSellerStaff::overrideMpSellerCustomerId($idCustomer);
                if ($getCustomerId) {
                    $idCustomer = $getCustomerId;
                }
            }
            $seller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($seller && $seller['active']) {
                $requests = WkMpSellerPaymentRequest::getRequests($seller['id_seller']);
                $this->context->smarty->assign(array(
                    'logic' => 'mp_seller_payment_request',
                    'requests' => $requests
                ));
                $this->defineJSVars();
                $this->setTemplate('module:'.$this->module->name.
                '/views/templates/front/sellerpaymentrequest.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink($this->module->name, 'paymentrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.
            urlencode($this->context->link->getModuleLink($this->module->name, 'paymentrequest')));
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'mptransaction'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        );
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Request Payment', 'paymentrequest'),
            'url' => '',
        );
        return $breadcrumb;
    }

    public function defineJSVars()
    {
        $jsVars = array(
            'display_name' => $this->module->l('Display', 'paymentrequest'),
            'records_name' => $this->module->l('records per page', 'paymentrequest'),
            'no_product' => $this->module->l('No request found', 'paymentrequest'),
            'show_page' => $this->module->l('Showing page', 'paymentrequest'),
            'show_of' => $this->module->l('of', 'paymentrequest'),
            'no_record' => $this->module->l('No records available', 'paymentrequest'),
            'filter_from' => $this->module->l('filtered from', 'paymentrequest'),
            't_record' => $this->module->l('total records', 'paymentrequest'),
            'search_item' => $this->module->l('Search', 'paymentrequest'),
            'p_page' => $this->module->l('Previous', 'paymentrequest'),
            'n_page' => $this->module->l('Next', 'paymentrequest')
        );
        Media::addJsDef($jsVars);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('mp_global_style-css', 'modules/marketplace/views/css/mp_global_style.css');
        $this->registerStylesheet('datatable_bootstrap', 'modules/marketplace/views/css/datatable_bootstrap.css');
        $this->registerJavascript('mp-jquery-dataTables', 'modules/marketplace/views/js/jquery.dataTables.min.js');
        $this->registerJavascript('mp-dataTables.bootstrap', 'modules/marketplace/views/js/dataTables.bootstrap.js');
        $this->registerJavascript('mp-sellertransaction', 'modules/'.$this->module->name.'/views/js/paymentrequest.js');
    }

    public function displayAjaxGetPaymentRequestForm()
    {
        $output = false;
        $idCustomer = $this->context->customer->id;

        if (Module::isEnabled('mpsellerstaff')) {
            $getCustomerId = WkMpSellerStaff::overrideMpSellerCustomerId($idCustomer);
            if ($getCustomerId) {
                $idCustomer = $getCustomerId;
            }
        }
        $results = WkMpSellerPaymentRequest::sellerTransactionTotal($idCustomer);
        $currencies = array();
        foreach ($results as $result) {
            $currency = new Currency($result['id_currency']);
            $currencies[] = array(
                'id_currency' => $currency->id,
                'name' => $currency->name,
                'due_amount' => $currency->sign.' '.$result['no_prefix_seller_due']
            );
        }
        $this->context->smarty->assign('currencies', $currencies);
        $output = $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->module->name.'/views/templates/front/payment_request_popup.tpl'
        );
        die($output);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitPaymentRequest') && $idCurrency = (int)Tools::getValue('id_currency')) {
            if (0 >= (float)Tools::getValue('request_amount')
            || !Validate::isUnsignedFloat(Tools::getValue('request_amount'))
            ) {
                $this->errors[] = $this->module->l('Invalid requested amount.', 'paymentrequest');
            }
            $idCustomer = $this->context->customer->id;
            if (Module::isEnabled('mpsellerstaff')) {
                $getCustomerId = WkMpSellerStaff::overrideMpSellerCustomerId($idCustomer);
                if ($getCustomerId) {
                    $idCustomer = $getCustomerId;
                }
            }
            $seller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            $requestAmount = (float)Tools::getValue('request_amount');
            if ($lockInPeriod = Configuration::get('WK_MP_SPR_LOCK_IN_PERIOD')) {
                $lastRequest = WkMpSellerPaymentRequest::getSellerLastRequest($seller['id_seller'], $idCurrency);
                if ($lockInPeriod > round((time() - strtotime($lastRequest['date_add'])) / (60 * 60 * 24))) {
                    $this->errors[] = sprintf(
                        $this->module->l('Payment request can be submitted only after %s %s from the previous request.', 'paymentrequest'),
                        $lockInPeriod,
                        ' '.($lockInPeriod > 1 ?
                            $this->module->l('days', 'paymentrequest') :
                            $this->module->l('day', 'paymentrequest'))
                    );
                }
            }
            if ($maxWithdrawal = (int)Configuration::get('WK_MP_SPR_MAX_WITHDRAWAL')) {
                $maxWithdrawal = (float)Tools::convertPrice($maxWithdrawal, $idCurrency);
                if ($requestAmount > $maxWithdrawal) {
                    $this->errors[] = sprintf(
                        $this->module->l('Requested amount can not be higher than %s in a single request.', 'paymentrequest'),
                        Tools::displayPrice(
                            $maxWithdrawal,
                            new Currency($idCurrency)
                        )
                    );
                }
            }
            $orderTotals = WkMpSellerPaymentRequest::sellerTransactionTotal(
                $idCustomer,
                $idCurrency
            );
            $lockInAmount = (float)Tools::convertPrice(
                (int)Configuration::get('WK_MP_SPR_LOCK_IN_AMOUNT'),
                $idCurrency
            );
            if (isset($orderTotals[0]['no_prefix_seller_due']) && $lockInAmount) {
                $orderTotals[0]['no_prefix_seller_due'] -= $lockInAmount;
                if ($orderTotals[0]['no_prefix_seller_due'] < 0) {
                    $orderTotals[0]['no_prefix_seller_due'] = 0;
                }
                if ($requestAmount > $orderTotals[0]['no_prefix_seller_due']) {
                    $this->errors[] = sprintf(
                        $this->module->l('You have to reserve %s in your account.', 'paymentrequest'),
                        Tools::displayPrice(
                            $lockInAmount,
                            new Currency($idCurrency)
                        )
                    );
                }
            }
            if (!isset($orderTotals[0]['no_prefix_seller_due'])
                || $requestAmount > $orderTotals[0]['no_prefix_seller_due']
            ) {
                $currency = new Currency($idCurrency);
                $currentDue = $currency->sign .
                    ($orderTotals[0]['no_prefix_seller_due'] > 0 ? $orderTotals[0]['no_prefix_seller_due'] : 0);
                $this->errors[] = sprintf(
                    $this->module->l("Requested amount can not be higher than due currency amount. Your current payable due is %s in your account%s", 'paymentrequest'),
                    $currentDue,
                    ($lockInAmount ? ' '.$this->module->l('after deducting the reserved amount.', 'paymentrequest') : '.')
                );
            }
            if (!$this->errors) {
                $objWkMpSellerPaymentRequest = new WkMpSellerPaymentRequest();
                $objWkMpSellerPaymentRequest->id_seller = $seller['id_seller'];
                $objWkMpSellerPaymentRequest->id_currency = $idCurrency;
                $objWkMpSellerPaymentRequest->request_amount = $requestAmount;
                $objWkMpSellerPaymentRequest->add();
                Tools::redirect(
                    $this->context->link->getModuleLink($this->module->name, 'paymentrequest', array('conf' => 1))
                );
            }
        }
    }
}
