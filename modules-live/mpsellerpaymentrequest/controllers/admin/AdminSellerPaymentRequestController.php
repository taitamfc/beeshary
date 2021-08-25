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

class AdminSellerPaymentRequestController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'wk_mp_seller_payment_request';
        $this->className = 'WkMpSellerPaymentRequest';
        $this->identifier = 'id_seller_payment_request';
        parent::__construct();
        $this->modals[] = $this->getModal();
        $this->list_no_link = true;
        if (!Tools::isSubmit('view'.$this->table)) {
            $this->toolbar_title = $this->l('Manage Payment Request');
            $this->_select .= 'CONCAT(b.seller_firstname, " ", b.seller_lastname) as name, b.shop_name_unique,
            SUM(IF(a.status = 0, 1, 0)) as pending_request';
            $this->_join .= 'INNER JOIN '._DB_PREFIX_.'wk_mp_seller b ON(a.id_seller = b.id_seller)';
            $this->_group = 'GROUP BY a.id_seller';
            $this->_orderBy = 'a.id_seller_payment_request';
            $this->_orderWay = 'DESC';
            $this->fields_list = array(
                'name' => array(
                    'title' => $this->l('Seller Name'),
                    'align' => 'center',
                    'havingFilter' => true,
                ),
                'shop_name_unique' => array(
                    'title' => $this->l('Shop Unique Name'),
                    'align' => 'center',
                ),
                'pending_request' => array(
                    'title' => $this->l('Pending Request'),
                    'align' => 'center',
                    'havingFilter' => true,
                    'callback' => 'setBadge'
                )
            );
        }
        $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->fields_options = array(
            'Configuration' => array(
                'title' => $this->l('General Configuration'),
                'fields' => array(
                    'WK_MP_SPR_LOCK_IN_PERIOD' => array(
                        'title' => $this->l('Lock-in Period'),
                        'hint' => $this->l('Numeric value in days'),
                        'desc' => $this->l('Seller can make next payment request only after the above specified day(s) from previous request. Set 0 to disable'),
                        'type' => 'text',
                        'suffix' => $this->l('days'),
                        'class' => 'fixed-width-xxl',
                    ),
                    'WK_MP_SPR_LOCK_IN_AMOUNT' => array(
                        'title' => $this->l('Lock-in Amount'),
                        'desc' => $this->l('Minimum amount which seller has to reserve and can\'t withdraw from their account. Set 0 to disable'),
                        'type' => 'text',
                        'suffix' => $currency->sign,
                        'class' => 'fixed-width-xxl',
                    ),
                    'WK_MP_SPR_MAX_WITHDRAWAL' => array(
                        'title' => $this->l('Maximum withdrawal limit per request'),
                        'desc' => $this->l('Maximum amount for which the seller can make the payment request from admin. Set 0 to disable'),
                        'type' => 'text',
                        'suffix' => $currency->sign,
                        'class' => 'fixed-width-xxl',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'btnSubmit'
                ),
            ),
        );
    }

    public function renderView()
    {
        if (Tools::getValue('id_seller')) {
            $this->initRenderList();
            return $this->getSellerDetail().$this->renderList();
        }
    }

    public function initRenderList()
    {
        $idSeller = Tools::getValue('id_seller');
        $objWkMpSeller = WkMpSeller::getSeller((int)$idSeller);
        $this->toolbar_title = $objWkMpSeller['shop_name_unique'].' -- '.$this->l('Payment Requests');
        $this->_where .= ' AND id_seller = '. (int)$idSeller;
        $this->_orderBy = 'a.id_seller_payment_request';
        $this->_orderWay = 'DESC';
        $this->fields_list = array(
            'id_seller_payment_request' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'request_amount' => array(
                'title' => $this->l('Request Amount'),
                'align' => 'center',
                'callback' => 'getAmount',
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'callback' => 'getStatus',
                'type' => 'select',
                'filter_key' => 'a!status',
                'list' => array(
                    '0' => $this->l('Pending'),
                    '1' => $this->l('Approved'),
                    '2' => $this->l('Declined'),
                )
            ),
            'date_add' => array(
                'title' => $this->l('Request Date'),
                'align' => 'center',
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        self::$currentIndex =  self::$currentIndex.'&id_seller='.(int)$idSeller.'&view'.
        $this->table;
        $this->context->smarty->assign(array(
            'current' => self::$currentIndex,
        ));
    }

    public function filterToField($key, $filter)
    {
        if (Tools::getValue('id_seller')) {
            $this->initRenderList();
        }
        return parent::filterToField($key, $filter);
    }

    public function renderList()
    {
        if (!Tools::getValue('id_seller')) {
            $this->addRowAction('view');
        } else {
            $this->addRowAction('settle');
        }
        return parent::renderList();
    }

    public function getSellerDetail()
    {
        if ($idSeller = Tools::getValue('id_seller')) {
            $objWkMpSeller = WkMpSeller::getSeller((int)$idSeller);
            $fields_list = array(
                'total_earning' => array(
                    'title' => $this->l('Total earning'),
                    'width' => 'auto',
                    'type' => 'text',
                ),
                'admin_commission' => array(
                    'title' => $this->l('Admin Commission'),
                    'width' => 'auto',
                    'type' => 'text',
                ),
                'admin_tax' => array(
                    'title' => $this->l('Admin Tax'),
                    'width' => 'auto',
                    'type' => 'text',
                ),
                'admin_shipping' => array(
                    'title' => $this->l('Admin Shipping'),
                    'width' => 'auto',
                    'type' => 'text',
                ),
                'seller_total' => array(
                    'title' => $this->l('Seller Earnings'),
                    'width' => 'auto',
                    'type' => 'text',
                ),
                'seller_tax' => array(
                    'title' => $this->l('Seller Tax'),
                    'width' => 'auto',
                    'type' => 'text',
                ),
                'seller_shipping' => array(
                    'title' => $this->l('Seller Shipping'),
                    'width' => 'auto',
                    'type' => 'text',
                ),
                'seller_recieve' => array(
                    'title' => $this->l('Seller Received'),
                    'width' => 'auto',
                    'type' => 'text',
                ),
                'seller_due' => array(
                    'title' => $this->l('Seller Due'),
                    'width' => 'auto',
                    'type' => 'text',
                    'callback' => 'setBadge',
                ),
            );
            $result = WkMpSellerPaymentRequest::sellerTransactionTotal($objWkMpSeller['seller_customer_id']);
            $helper = new HelperList();
            $helper->shopLinkType = '';
            $helper->no_link = true;
            $helper->simple_header = true;
            $helper->identifier = 'id_seller';
            $helper->list_id = 'seller_earnings';
            $helper->show_toolbar = false;
            $helper->title = $objWkMpSeller['seller_firstname'].' '.
            $objWkMpSeller['seller_lastname'].' -- '.$this->l('Earning');
            $helper->table = $this->table;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->currentIndex = self::$currentIndex;
            return $helper->generateList($result, $fields_list);
        }
    }

    public function postProcess()
    {
        if (empty(Tools::getValue($this->table.'Orderby'))) {
            $this->context->cookie->{'sellerpaymentrequest'.$this->table.'Orderby'} = '';
        }
        if (empty(Tools::getValue($this->table.'Orderway'))) {
            $this->context->cookie->{'sellerpaymentrequest'.$this->table.'Orderway'} = '';
        }
        if (Tools::isSubmit('submitReset'.$this->table)) {
            $this->processResetFilters();
        }
        if (Tools::isSubmit('submitFilter')) {
            if (Tools::getValue($this->table.'Filter_id_seller_payment_request') == ''
               && Tools::getValue($this->table.'Filter_request_amount') == ''
               && Tools::getValue($this->table.'Filter_a!status') == ''
               && Tools::getValue($this->table.'Filter_name') == ''
               && Tools::getValue($this->table.'Filter_shop_name_unique') == ''
               && Tools::getValue($this->table.'Filter_pending_request') == ''
               && Tools::getValue($this->table.'Filter_date_add') == ''
            ) {
                $this->processResetFilters();
            } else {
                $this->processFilter();
            }
        }
        if (Tools::isSubmit('is_accepted')
            && $idSellerPaymentRequest = Tools::getValue('id_seller_payment_request')
        ) {
            $objWkMpSellerPaymentRequest = new WkMpSellerPaymentRequest($idSellerPaymentRequest);
            if (Tools::getValue('is_accepted')) {
                $amount = $objWkMpSellerPaymentRequest->request_amount;
                $objWkMpSeller = WkMpSeller::getSeller((int)$objWkMpSellerPaymentRequest->id_seller);
                $idCustomerSeller = $objWkMpSeller['seller_customer_id'];
                $idCurrency = $objWkMpSellerPaymentRequest->id_currency;
                $sellerDue = 0;
                $wkMpPaymentMethod = trim(Tools::getValue('wk_mp_payment_method'));
                $wkMpTransactionID = trim(Tools::getValue('wk_mp_transaction_id'));
                $wkMpRemark = trim(Tools::getValue('wk_mp_remark'));
                $sellerTotal = WkMpSellerTransactionHistory::getSellerOrderTotalByIdCustomer(
                    $idCustomerSeller,
                    $idCurrency
                );
                $sellerShippingInfo = WkMpAdminShipping::getTotalSellerShipping($idCustomerSeller, $idCurrency);
                if (!$sellerShippingInfo) {
                    $sellerShippingInfo = array(
                        'seller_shipping' => '0',
                        'id_currency' => $idCurrency
                    );
                }
                if (!empty($sellerTotal)) {
                    $sellerDue = $sellerTotal[0]['seller_total_earned'] - $sellerTotal[0]['seller_receive'];
                }
                if (isset($sellerShippingInfo['seller_shipping'])) {
                    $sellerDue += $sellerShippingInfo['seller_shipping'];
                }
                if ($sellerDue) {
                    if (!$amount) {
                        $this->errors[] = $this->l('Amount can not be empty');
                    } elseif ($amount <= 0) {
                        $this->errors[] = $this->l('Amount must be greater than zero');
                    } elseif (!Validate::isFloat($amount)) {
                        $this->errors[] = $this->l('Amount is not valid');
                    } elseif ($amount > $sellerDue) {
                        $this->errors[] = $this->l('Amount can not be greater than total due');
                    }
                    $objSellerPayment = new WkMpCustomerPayment();
                    $paymentModeDetail = $objSellerPayment->getPaymentDetailByIdCustomer($idCustomerSeller);
                    if ($wkMpPaymentMethod) {
                        if (!Validate::isGenericName($wkMpPaymentMethod)) {
                            $this->errors[] = $this->l('Payment method is not valid.');
                        }
                    } elseif ($paymentModeDetail) {
                        $wkMpPaymentMethod = $paymentModeDetail['payment_mode'];
                    }
                    if ($wkMpTransactionID) {
                        if (!Validate::isAnything($wkMpTransactionID)) {
                            $this->errors[] = $this->l('Transaction is not valid.');
                        }
                    } else {
                        $wkMpTransactionID = $this->l('N/A');
                    }
                    if ($wkMpRemark) {
                        if (!Validate::isGenericName($wkMpRemark)) {
                            $this->errors[] = $this->l('Remark is not valid.');
                        }
                    }
                    $lastRowValue = WkMpSellerTransactionHistory::getlastRowValue();
                    if (!$lastRowValue) {
                        $lastRowValue = '1#';
                    } else {
                        $lastRowValue++;
                    }
                    $wkMpTransactionID = $lastRowValue.'#'.$wkMpTransactionID;
                    if (empty($this->errors)) {
                        $sellerSplit = new WkMpSellerPaymentSplit();
                        if ($idMpTransaction = $sellerSplit->settleSellerAmount(
                            $objWkMpSellerPaymentRequest->id_seller,
                            $amount,
                            $idCurrency,
                            true,
                            $wkMpPaymentMethod,
                            'settlement',
                            $wkMpRemark,
                            $wkMpTransactionID
                        )) {
                            $objWkMpSellerPaymentRequest->status = 1;
                            $objWkMpSellerPaymentRequest->remark = $wkMpRemark;
                            $objWkMpSellerPaymentRequest->id_mp_transaction = $idMpTransaction;
                            $objWkMpSellerPaymentRequest->update();
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token.'&view'.
                            $this->table.'&id_seller='.(int)$objWkMpSellerPaymentRequest->id_seller);
                        } else {
                            $this->errors[] = $this->l('Something went wrong!');
                        }
                    }
                } else {
                    $this->errors[] = $this->l('Currently No due amount running for this seller');
                }
            } else {
                $objWkMpSellerPaymentRequest->remark = Tools::getValue('remark');
                $objWkMpSellerPaymentRequest->status = 2;
                $objWkMpSellerPaymentRequest->update();
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token.'&view'.
                $this->table.'&id_seller='.(int)$objWkMpSellerPaymentRequest->id_seller);
            }
        }
        if (Tools::isSubmit('btnSubmit')) {
            if (Tools::getValue('WK_MP_SPR_LOCK_IN_PERIOD')
                && !Validate::isUnsignedInt(Tools::getValue('WK_MP_SPR_LOCK_IN_PERIOD'))) {
                $this->errors[] = $this->l('Invalid lock in period, it should be positive numeric value.');
            } else {
                Configuration::updateValue(
                    'WK_MP_SPR_LOCK_IN_PERIOD',
                    (int)Tools::getValue('WK_MP_SPR_LOCK_IN_PERIOD', 0)
                );
            }
            if (Tools::getValue('WK_MP_SPR_LOCK_IN_AMOUNT')
                && !Validate::isUnsignedFloat(Tools::getValue('WK_MP_SPR_LOCK_IN_AMOUNT'))) {
                $this->errors[] = $this->l('Invalid lock in amount, it should be positive numeric value.');
            } else {
                Configuration::updateValue(
                    'WK_MP_SPR_LOCK_IN_AMOUNT',
                    (float)Tools::getValue('WK_MP_SPR_LOCK_IN_AMOUNT', 0)
                );
            }
            if (Tools::getValue('WK_MP_SPR_MAX_WITHDRAWAL')
                && !Validate::isUnsignedFloat(Tools::getValue('WK_MP_SPR_MAX_WITHDRAWAL'))) {
                $this->errors[] = $this->l('Invalid maximum withdrawal limit value, it should be positive numeric value.');
            } else {
                Configuration::updateValue(
                    'WK_MP_SPR_MAX_WITHDRAWAL',
                    (float)Tools::getValue('WK_MP_SPR_MAX_WITHDRAWAL', 0)
                );
            }
            if (!$this->errors) {
                $this->confirmations[] = $this->l('The configuration have been successfully updated.');
            }
        }
        if (Tools::isSubmit('submitBulkdeletewk_mp_seller_payment_request')) {
            if ($ids_wk_mp_seller_payment_request = Tools::getValue('wk_mp_seller_payment_requestBox')) {
                foreach ($ids_wk_mp_seller_payment_request as $id_wk_mp_seller_payment_request) {
                    if ($WkMpSellerPaymentRequest = new WkMpSellerPaymentRequest(
                        (int)$id_wk_mp_seller_payment_request
                    )) {
                        $WkMpSellerPaymentRequest->delete();
                    }
                }
                Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$this->token.'&view'.
                $this->table.'&id_seller='.(int)Tools::getValue('id_seller'));
            } else {
                $this->errors[] = $this->l('Please select at least one entry to delete.');
            }
        }
        parent::postProcess();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function getSeller($idSeller)
    {
        if (empty($idSeller)) {
            return;
        }
        if (!$seller = WkMpSeller::getSeller((int)$idSeller)) {
            return;
        }
        return ($seller['seller_firstname'] .' '. $seller['seller_lastname']);
    }

    public function getAmount($requestAmount, $obj)
    {
        if (empty($requestAmount)) {
            return;
        }
        return Tools::displayPrice($requestAmount, (new Currency((int)$obj['id_currency'])), false);
    }

    public function getDueAmount($isSeller, $obj)
    {
        if (empty($isSeller)) {
            return;
        }
        $requestAmount = Tools::convertPriceFull(
            $requestAmount,
            (new Currency((int)$obj['id_currency'])),
            $this->context->currency
        );
        return Tools::displayPrice($requestAmount, $this->context->currency, false);
    }

    public function setBadge($count)
    {
        return $this->getElementExtraHtml('badge_count', array('count' => $count));
    }

    public function getStatus($status)
    {
        return $this->getElementExtraHtml('badge_status', array('status' => $status));
    }

    public function displayViewLink($token, $id, $name = null)
    {
        $objWkMpSellerPaymentRequest = new WkMpSellerPaymentRequest($id);
        $tpl = $this->createTemplate('helpers/list/list_action_view.tpl');
        if (!array_key_exists('view', self::$cache_lang)) {
            self::$cache_lang['view'] = $this->l('View', 'Helper');
        }
        $tpl->assign(array(
        'href' => self::$currentIndex.'&id_seller='.$objWkMpSellerPaymentRequest->id_seller.'&view'.
        $this->table.'&token='.($token != null ? $token : $this->token),
        'action' => self::$cache_lang['view'],
        'id' => $objWkMpSellerPaymentRequest->id_seller
        ));
        return $tpl->fetch();
    }

    public function displaySettleLink($token, $id, $name = null)
    {
        $objWkMpSellerPaymentRequest = new WkMpSellerPaymentRequest($id);
        if ($objWkMpSellerPaymentRequest->status != 0) {
            return;
        }
        $this->context->smarty->assign(array(
            'id_seller_payment_request' => $objWkMpSellerPaymentRequest->id_seller_payment_request
        ));
         return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.
         '/views/templates/admin/settle_button.tpl');
    }

    public function getModal()
    {
        return array(
            'modal_id' => $this->module->name.'_modal',
            'modal_class' => 'modal-md',
            'modal_title' => $this->getElementExtraHtml('icon_money'),
            'modal_actions' => array(
                array(
                    'type' => 'button',
                    'label' => $this->l('Approve'),
                    'value' => 1,
                    'class' => 'btn-primary approve'
                ),
                array(
                    'type' => 'button',
                    'label' => $this->l('Decline'),
                    'value' => 1,
                    'class' => 'btn-danger decline'
                ),
            ),
            'modal_content' => $this->getElementExtraHtml('modal_body')
        );
    }

    public function getElementExtraHtml($type, $data = array())
    {
        $this->context->smarty->assign(array(
            'element_type' => $type,
            'data' => $data
        ));
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->module->name.
            '/views/templates/hook/form_element.tpl'
        );
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme = false);
        $this->addJS(_PS_MODULE_DIR_.$this->module->name.'/views/js/mpsellerpaymentrequest.js');
    }

    public function ajaxProcessGetModalForm()
    {
        if (Tools::getValue('action') == 'GetModalForm'
        && $idSellerPaymentRequest = Tools::getValue('id_seller_payment_request')) {
            $objWkMpSellerPaymentRequest = new WkMpSellerPaymentRequest($idSellerPaymentRequest);
            $objWkMpSeller = WkMpSeller::getSeller((int)$objWkMpSellerPaymentRequest->id_seller);
            $orderTotals = WkMpSellerPaymentRequest::sellerTransactionTotal(
                $objWkMpSeller['seller_customer_id'],
                $objWkMpSellerPaymentRequest->id_currency
            );
            if ($orderTotals) {
                $objSellerPayment = new WkMpCustomerPayment();
                $paymentModeDetail = $objSellerPayment->getPaymentDetailByIdCustomer(
                    $objWkMpSeller['seller_customer_id']
                );
                $this->context->smarty->assign(array(
                    'id_seller_payment_request' => $idSellerPaymentRequest,
                    'payment_mode_details' => $paymentModeDetail ? $paymentModeDetail['payment_detail'] :
                     $this->l('N/A'),
                    'payment_mode' => $paymentModeDetail ? $paymentModeDetail['payment_mode'] :
                    $this->l('N/A'),
                    'no_prefix_seller_due' => $orderTotals[0]['no_prefix_seller_due'],
                    'seller_due' => ($orderTotals[0]['no_prefix_seller_due'] > 0) ?
                    $orderTotals[0]['seller_due'] : Tools::displayPrice(
                        0,
                        new Currency($objWkMpSellerPaymentRequest->id_currency)
                    ),
                   'no_prefix_request_amount' => $objWkMpSellerPaymentRequest->request_amount,
                   'request_amount' => Tools::displayPrice(
                       $objWkMpSellerPaymentRequest->request_amount,
                       new Currency($objWkMpSellerPaymentRequest->id_currency)
                   ),
                    'mode_error_message' => $this->l('Invalid payment method.'),
                    'transaction_error_message' => $this->l('Invalid transaction Id.'),
                    'remark_error_message' => $this->l('Invalid remark.'),
                    'decline_error_message' => $this->l('Please mention reason to decline.')
                ));
                echo $this->context->smarty->fetch(
                    _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/settle_popup.tpl'
                );
            }
        }
        die;
    }
}
