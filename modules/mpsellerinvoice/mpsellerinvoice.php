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

if (!defined('_PS_VERSION_')) {
    exit;
}
if (Module::isInstalled('marketplace')) {
    include_once _PS_MODULE_DIR_.'marketplace/classes/WkMpRequiredClasses.php';
}
include_once _PS_MODULE_DIR_.'mpsellerinvoice/classes/InvoiceClasses.php';

class MpSellerInvoice extends Module
{
    public function __construct()
    {
        $this->name = 'mpsellerinvoice';
        $this->tab = 'front_office_features';
        $this->author = 'Webkul';
        $this->version = '6.1.0';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->dependencies = array('marketplace');
        parent::__construct();
        $this->displayName = $this->l('Marketplace Seller Invoice');
        $this->description = $this->l('Invoice on behalf of seller will generated automatically');

    }

    // If customer is getting delete then we are updating seller with anonymous information
    public function hookActionDeleteGDPRCustomer($customer)
    {
    }

    // Showing seller information based on customer ID
    public function hookActionExportGDPRData($customer)
    {
    }

    public function hookDisplayMPMyAccountMenu()
    {
        return $this->displayMpSellerInvoiceTab(0);
    }

    public function hookDisplayMPMenuBottom()
    {
        return $this->displayMpSellerInvoiceTab(1);
    }

    public function displayMpSellerInvoiceTab($var)
    {
        if ($mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id)) {
            if ($mpCustomerInfo['active']) {
                $this->context->smarty->assign('mpmenu', $var);

                return $this->display(__FILE__, 'seller_invoice_tab.tpl');
            }
        }
    }

    // Show Commission Invoice based on tab in backoffice on seller controller page
    public function hookDisplayMpEditProfileTab()
    {
        if (Tools::getValue('controller') == 'AdminSellerInfoDetail') {
            return $this->display(__FILE__, 'admin_seller_invoice_based_tab.tpl');
        }
    }

    // Show Commission Invoice based on tab content in backoffice on seller controller page
    public function hookDisplayMpEditProfileTabContent()
    {
        if (Tools::getValue('controller') == 'AdminSellerInfoDetail') {
            if ($idSeller = Tools::getValue('id_seller')) {
                $objInvoiceConfig = new MpSellerInvoiceConfig();
                $result = $objInvoiceConfig->isSellerInvoiceConfigExist($idSeller);
                if ($result) {
                    $this->context->smarty->assign(
                        array(
                            'result' => $result,
                        )
                    );
                }
            }
            $this->context->smarty->assign(array(
                'wkcurrency' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
            ));

            return $this->display(__FILE__, 'admin_seller_invoice_based_tab_content.tpl');
        }
    }

    // Validate Commission Invoice based configuration on backoffice seller controller page
    public function hookActionBeforeAddSeller($params)
    {
        $this->validateSellerInvoice();
    }

    // Validate Commission Invoice based configuration on backoffice seller controller page
    public function hookActionBeforeUpdateSeller($params)
    {
        $this->validateSellerInvoice();
    }

    // Validate Commission Invoice based configuration on backoffice seller controller page
    public function validateSellerInvoice()
    {
        if (Tools::getValue('controller') == 'AdminSellerInfoDetail') {
            $seller_invoice_based = Tools::getValue('seller_invoice_based');
            if ($seller_invoice_based == 2) {
                $seller_invoice_value = Tools::getValue('seller_invoice_value');
                if ($seller_invoice_value != '') {
                    if (!Validate::isInt($seller_invoice_value)) {
                        $this->context->controller->errors[] = $this->l('Value are not valid');
                    } elseif ($seller_invoice_value <= 0) {
                        $this->context->controller->errors[] = $this->l('Value are not valid');
                    }
                } else {
                    $this->context->controller->errors[] = $this->l('Value can not be empty');
                }
            } elseif ($seller_invoice_based == 3) {
                $seller_threshold = Tools::getValue('seller_threshold');
                if ($seller_threshold) {
                    if (!Validate::isPrice($seller_threshold)) {
                        $this->context->controller->errors[] = $this->l('Threshold value is not valid');
                    }
                } else {
                    $this->context->controller->errors[] = $this->l('Threshold value can not be empty');
                }
            }
        }
    }

    // Save Commission Invoice based configuration on backoffice seller controller page
    public function hookActionAfterUpdateSeller($params)
    {
        $this->saveSellerInvoiceData($params);
    }

    // Save Commission Invoice based configuration on backoffice seller controller page
    public function hookActionAfterAddSeller($params)
    {
        $this->saveSellerInvoiceData($params);
    }

    //Save default commission invoice based configuration on install module
    public function saveDefaultSellerInvoiceConfig()
    {
        $allSeller = WkMpSeller::getAllSeller(false, false, $this->context->language->id);
        foreach ($allSeller as $seller) {
            $objInvoiceConfig = new MpSellerInvoiceConfig();
            $isExist = $objInvoiceConfig->isSellerInvoiceConfigExist($seller['id_seller']);
            if ($isExist) {
                $objInvoiceConfig = new MpSellerInvoiceConfig($isExist['id']);
            }
            $objInvoiceConfig->id_seller = $seller['id_seller'];
            $objInvoiceConfig->invoice_prefix = Configuration::get(
                'PS_INVOICE_PREFIX',
                $this->context->language->id
            );
            $objInvoiceConfig->invoice_based = 1;
            $objInvoiceConfig->value = 0;
            $objInvoiceConfig->time_interval = 'day';
            $objInvoiceConfig->save();
        }
        return true;
    }

    // Save Commission Invoice based configuration on backoffice seller controller page
    public function saveSellerInvoiceData($params)
    {


        if (Tools::getValue('controller') == 'AdminSellerInfoDetail') {
            if ($params['id_seller']) {
                $seller_invoice_based = Tools::getValue('seller_invoice_based');
                $seller_invoice_value = Tools::getValue('seller_invoice_value');
                $seller_invoice_interval = Tools::getValue('seller_invoice_interval');

                if ($seller_invoice_based == 3) {
                    $seller_invoice_value = Tools::getValue('seller_threshold');
                }

                $objInvoiceConfig = new MpSellerInvoiceConfig();
                $isExist = $objInvoiceConfig->isSellerInvoiceConfigExist($params['id_seller']);
                if ($isExist) {
                    $objInvoiceConfig = new MpSellerInvoiceConfig($isExist['id']);
                }
                $objInvoiceConfig->id_seller = $params['id_seller'];
                $objInvoiceConfig->invoice_prefix = Configuration::get(
                    'PS_INVOICE_PREFIX',
                    $this->context->language->id
                );
                $objInvoiceConfig->invoice_based = $seller_invoice_based;
                $objInvoiceConfig->value = $seller_invoice_value;
                $objInvoiceConfig->time_interval = $seller_invoice_interval;
                $objInvoiceConfig->save();
            }
        }
        //Save Default Commission Invoice based configuration when seller regester from frontend
        if (Dispatcher::getInstance()->getController() == 'sellerrequest') {
            if ($params['id_seller']) {
                $objInvoiceConfig = new MpSellerInvoiceConfig();
                $isExist = $objInvoiceConfig->isSellerInvoiceConfigExist($params['id_seller']);
                if ($isExist) {
                    $objInvoiceConfig = new MpSellerInvoiceConfig($isExist['id']);
                }
                $objInvoiceConfig->id_seller = $params['id_seller'];
                $objInvoiceConfig->invoice_prefix = Configuration::get(
                    'PS_INVOICE_PREFIX',
                    $this->context->language->id
                );
                $objInvoiceConfig->invoice_based = 1;
                $objInvoiceConfig->value = 0;
                $objInvoiceConfig->time_interval = 'day';
                $objInvoiceConfig->save();
            }
        }
    }

    /**
     * When order status change, this hook will call to send invoice pdf to customer ].
     *
     * @param array $params [new order state and order id]
     *
     * @return
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        //check this hook call when only admin change status because when seller change hookActionAfterSellerOrderStatusUpdate call and send mail (To stop email duplicacy)
        $idOrder = $params['id_order'];
        $currentState = $params['newOrderStatus']->id;
        if ($this->context->employee) {
            $objOrderState = new OrderState($currentState);
            $idCustomer = $params['cart']->id_customer;
            $this->onOrderStatusUpdateMailProcess($currentState, $idCustomer, $idOrder);
        }
        // Save order ID to generate commission invoice if commission invoice based on each order
        $objInvoiceRecord = new MpSellerOrderInvoiceRecord();
        $sellerOrderDetail = $objInvoiceRecord->getSellersCustomerIdByOrderId($idOrder);
        if ($sellerOrderDetail) {
            foreach ($sellerOrderDetail as $sellerOrder) {
                $objInvoiceConfig = new MpSellerInvoiceConfig();
                $objCommissionInvoiceHistory = new MpCommissionInvoiceHistory();
                $sellerDetail = WkMpSeller::getSellerDetailByCustomerId($sellerOrder['seller_customer_id']);
                if ($sellerDetail) {
                    $result = $objInvoiceConfig->isSellerInvoiceConfigExist($sellerDetail['id_seller']);
                    if ($result && $result['invoice_based'] == 1) {
                        if ($sellerOrderStatus = Tools::jsonDecode(Configuration::get('MP_SELLER_INVOICE_ORDER_STATUS'))) {
                            if (in_array($currentState, $sellerOrderStatus)) {
                                $objCommissionInvoiceHistory->createInvoiceHistory(
                                    $sellerDetail['id_seller'],
                                    $idOrder
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    public function onOrderStatusUpdateMailProcess($currentState, $idCustomer, $idOrder, $idSeller = false)
    {
        $objOrderState = new OrderState($currentState);
        if (!$this->context->cookie->seller_new_order) {
            if (Validate::isLoadedObject($objOrderState)) {
                $order = new Order((int) $idOrder);
                if ($idOrder && $idCustomer) {
                    foreach ($order->getProducts() as $prod_detail) {
                        $isBelongToSeller = WkMpSellerProduct::getSellerProductByPsIdProduct(
                            $prod_detail['product_id']
                        );
                        if ($isBelongToSeller) {
                            if ($idSeller) {
                                if ($isBelongToSeller['id_seller'] == $idSeller) {
                                    $seller_info = new WkMpSeller($isBelongToSeller['id_seller']);
                                    $orderDetails[$isBelongToSeller['id_seller']] =
                                    $seller_info->seller_customer_id;
                                }
                            } else {
                                $seller_info = new WkMpSeller($isBelongToSeller['id_seller']);
                                $orderDetails[$isBelongToSeller['id_seller']] = $seller_info->seller_customer_id;
                            }
                        } else {
                            if ($idSeller) {
                                if ($order->invoice_number) {
                                    $orderDetails['admin'] = 'admin';
                                }
                            } else {
                                $orderDetails['admin'] = 'admin';
                            }
                        }
                    }
                    $objCreateInvoice = new CreateInvoice();
                    foreach ($orderDetails as $key => $detail) {
                        $pdf = $objCreateInvoice->createInvoice($order, $detail, false, false, $objOrderState->invoice);
                        $attachmentPDF[$detail] = $pdf;
                        if (Configuration::get('MP_SELLER_INVOICE_TO_ADMIN') &&
                            Configuration::get('MP_SELLER_INVOICE_TO_ADMIN_AUTOMATIC') &&
                            $detail != 'admin'
                        ) {
                            if ($objOrderState->pdf_invoice) {
                                $objCreateInvoice->sendPaymentEmailToCustomer($order, $pdf, true, $objOrderState);
                            }

                            unset($adminAttachmentPDF);
                        }
                    }
                    if ($objOrderState->send_email && $objOrderState->invoice
                    ) {
                        if (Configuration::get('MP_SELLER_INVOICE_ACTIVE') == 1) {
                            $objCreateInvoice->sendPaymentEmailToCustomer($order, $attachmentPDF, false, $objOrderState);
                        }
                        if (Configuration::get('MP_SELLER_INVOICE_ACTIVE') == 0) {
                            //If seller invoice is disable then send admin ps default invoice to customer
                            foreach ($orderDetails as $key => $detail) {
                                $pdf = $objCreateInvoice->createInvoice($order, 'admin', false, false, $objOrderState->invoice);
                                $attachmentPDF[$detail] = $pdf;
                            }
                            $objCreateInvoice->sendPaymentEmailToCustomer($order, $attachmentPDF, false, $objOrderState);
                        }
                    }
                }
            }
        }
        unset($this->context->cookie->seller_new_order);
    }

    /**
     * When order status change, this hook will call to send invoice pdf to customer ].
     *
     * @param array $params [new order state and order id]
     *
     * @return
     */
    public function hookActionAfterSellerOrderStatusUpdate($params)
    {
        $idSeller = $params['id_seller'];
        $currentState = $params['id_order_state'];
        $idOrder = $params['id_order'];
        $objCart = Cart::getCartByOrderId($idOrder);
        $idCustomer = $objCart->id_customer;
        $this->onOrderStatusUpdateMailProcess($currentState, $idCustomer, $idOrder, $idSeller);
    }

    // Display view invoice button in order detail page
    public function hookDisplayMpOrderDetialShippingBottom()
    {
        if (isset($this->context->customer->id)) {
            $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            $id_order = Tools::getValue('id_order');
            $objSellerInvoice = new MpSellerOrderInvoiceRecord();
            $isInvoiceSent = $objSellerInvoice->getOrderInvoiceNumber($id_order, $seller['id_seller']);
            if ($seller && $seller['active'] && $id_order && $isInvoiceSent) {
                $invoice_sent = Tools::getValue('invoice_sent');
                $this->context->smarty->assign(array(
                    'id_order' => $id_order,
                    'id_seller' => $seller['id_seller'],
                    'invoice_sent' => $invoice_sent,
                ));

                return $this->display(__FILE__, 'sellerinvoice_block.tpl');
            }
        }
    }

    /**
     * [hookActionValidateOrder -> if order status is payment accepted then send mail to customer with invoice pdf].
     *
     * @param [type] $params [order information]
     *
     * @return [type] [description]
     */
    public function hookActionValidateOrder($params)
    {
        $id_order = $params['order']->id;
        $params['id_order'] = $id_order;
        $id_order_state = $params['orderStatus']->id;
        $order = new Order($id_order);
        $obj_split_payment = new WkMpSellerPaymentSplit();
        $seller_product = $obj_split_payment->sellerWiseSplitedAmount($params);
        unset($seller_product['admin']['total_price_tax_incl']);
        unset($seller_product['admin']['total_product_weight']);
        // adding admin's product into array
        foreach ($order->getProducts() as $prod_detail) {
            $isBelongToSeller = WkMpSellerProduct::getSellerProductByPsIdProduct($prod_detail['product_id']);
            if (!$isBelongToSeller) {
                $seller_product['admin'][$prod_detail['product_id']] = $prod_detail;
            }
        }
    }

    public function sendSellerInvoiceAttachment($order, $attachmentPDF, $id_order_state)
    {
        $create_invoice = new CreateInvoice();
        $data = $create_invoice->orderDetailsInformation($order, $id_order_state);
        $template_path = _PS_MODULE_DIR_.'mpsellerinvoice/mails/';
        if (Validate::isEmail($this->context->customer->email)) {
            Mail::Send(
                (int) $order->id_lang,
                'seller_invoice',
                Mail::l('Order confirmation', (int) $order->id_lang),
                $data,
                $this->context->customer->email,
                $this->context->customer->firstname.' '.$this->context->customer->lastname,
                null,
                null,
                $attachmentPDF,
                null,
                $template_path,
                false,
                (int) $order->id_shop
            );
            $create_invoice->sendPaymentEmailToCustomer($order, $attachmentPDF, false);
        }
    }

    // Show view/send commission invoice button on prestashop order detail page
    public function hookDisplayAdminPsSellerOrderViewHead($params)
    {
        $idOrder = Tools::getValue('id_order');
        $order = new Order($idOrder);
        $statusToCommission = Tools::jsonDecode(Configuration::get('MP_SELLER_INVOICE_ORDER_STATUS'));
        if (Configuration::get('MP_SELLER_INVOICE_TO_SELLER')) {
            return $this->display(__FILE__, 'admin_seller_invoice_order_head.tpl');
        }
    }

    // Show view/send commission invoice button on prestashop order detail page
    public function hookDisplayAdminPsSellerOrderViewBody($params)
    {
        $idOrder = Tools::getValue('id_order');
        $order = new Order($idOrder);
        $statusToCommission = Tools::jsonDecode(Configuration::get('MP_SELLER_INVOICE_ORDER_STATUS'));
        //Get Seller by seller customer id
        $seller = WkMpSeller::getSellerByCustomerId($params['idSellerCustomer']);
        //Get Seller order Status
        $sellerOrderStatusObj = new WkMpSellerOrderStatus();
        $currentSellerOrderStatus = $sellerOrderStatusObj->getCurrentOrderState($idOrder, $seller['id_seller']);
        if (Configuration::get('MP_SELLER_INVOICE_TO_SELLER') && in_array($currentSellerOrderStatus, $statusToCommission)) {
            $this->context->smarty->assign(array(
                'idSellerCustomer' => $params['idSellerCustomer'],
                'id_order' => Tools::getValue('id_order'),
                'show_view_send' => 1
            ));
        } else {
            $this->context->smarty->assign(array(
                'show_view_send' => 0
            ));
        }
        return $this->display(__FILE__, 'admin_seller_invoice_order_body.tpl');
    }

    public function getContent()
    {
        $this->_html = '';
        if (!Configuration::get('MP_SELLER_INVOICE_ACTIVE')) {
            $this->context->controller->addCSS($this->_path.'views/css/sellerinvoice.css');
        }
        $this->context->controller->addJs($this->_path.'views/js/sellerinvoice.js');
        if (Tools::isSubmit('btnSubmit') || Tools::isSubmit('btnCommissionSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } else {
            $this->_html .= '<br />';
        }

        $commissionCron = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        $commissionCron .= 'modules/mpsellerinvoice/mpsellerinvoicecron.php?';
        $commissionCron .= 'token='.$this->secure_key;
        $this->context->smarty->assign('commissionCron', $commissionCron);
        $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/cron_settings.tpl');
        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderForm()
    {
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        asort($statuses);
        foreach ($statuses as $key => $status) {
            $this->statuses_array[$key]['id_group'] = $status['id_order_state'];
            $this->statuses_array[$key]['name'] = $status['name'];
        }

        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fields_form = array();
        $fields_form[0]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Seller Invoice'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send seller invoice to customers'),
                        'name' => 'MP_SELLER_INVOICE_ACTIVE',

                        'is_bool' => true,
                        'desc' => $this->l('If enabled, customers will receive an invoice on behalf of sellers on purchase of seller product'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Character limit for legal text'),
                        'name' => 'MP_SELLER_LEGAL_TEXT_LIMIT',
                        'form_group_class' => 'mpsellerprefix',
                        'hint' => $this->l('Set limit of characters for having legal text note in invoice for sellers'),
                        'required' => true,
                        'col' => 1,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Character limit for footer text'),
                        'name' => 'MP_SELLER_FOOTER_TEXT_LIMIT',
                        'form_group_class' => 'mpsellerprefix',
                        'hint' => $this->l('Set limit of characters for having footer text note in invoice for sellers'),
                        'required' => true,
                        'col' => 1,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send seller invoice to admin'),
                        'name' => 'MP_SELLER_INVOICE_TO_ADMIN',
                        'form_group_class' => '',
                        'is_bool' => true,
                        'desc' => $this->l('If enabled, send invoice button will display at order detail page at seller end'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send seller invoice to admin automatically'),
                        'name' => 'MP_SELLER_INVOICE_TO_ADMIN_AUTOMATIC',

                        'form_group_class' => 'mpsellerinvoiceadmin',
                        'is_bool' => true,
                        'desc' => $this->l('If enabled, admin will receive an invoice mail from sellers automatically when customer place an order including seller\'s products.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
        );

        $fields_form[1]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Admin Commission Invoice'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send commission invoice to seller'),
                        'name' => 'MP_SELLER_INVOICE_TO_SELLER',

                        'is_bool' => true,
                        'desc' => $this->l('If enabled, Admin can send an invoice mail to seller for their commission'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send commission invoice to seller automatically'),
                        'name' => 'MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC',

                        'is_bool' => true,
                        'desc' => $this->l('If enabled, Commission invoice will be sent to seller as soon as it generates'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'group',
                        'label' => $this->l('Status to generate commission invoice'),
                        'name' => 'groupBoxStatus',
                        'required' => true,
                        'values' => $this->statuses_array,
                        'col' => '9',
                        'form_group_class' => 'wk_mp_seller_order_status',
                        'hint' => $this->l('Choose status on which commission invoice will be generated'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'btnCommissionSubmit',
                ),
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&tab_module='.
        $this->tab.'&module_name='.$this->name;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->submit_action = 'btnSubmit';
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;

        //Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues()
    {
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        asort($statuses);
        foreach ($statuses as $key => $status) {
            $this->statuses_array[$key]['id_group'] = $status['id_order_state'];
            $this->statuses_array[$key]['name'] = $status['name'];
        }

        $this->fields_value = array(
            'MP_SELLER_INVOICE_ACTIVE' => Tools::getValue(
                'MP_SELLER_INVOICE_ACTIVE',
                Configuration::get('MP_SELLER_INVOICE_ACTIVE')
            ),
            'MP_SELLER_INVOICE_TO_ADMIN' => Tools::getValue(
                'MP_SELLER_INVOICE_TO_ADMIN',
                Configuration::get('MP_SELLER_INVOICE_TO_ADMIN')
            ),
            'MP_SELLER_LEGAL_TEXT_LIMIT' => Tools::getValue(
                'MP_SELLER_LEGAL_TEXT_LIMIT',
                Configuration::get('MP_SELLER_LEGAL_TEXT_LIMIT')
            ),
            'MP_SELLER_FOOTER_TEXT_LIMIT' => Tools::getValue(
                'MP_SELLER_FOOTER_TEXT_LIMIT',
                Configuration::get('MP_SELLER_FOOTER_TEXT_LIMIT')
            ),
            'MP_SELLER_INVOICE_TO_SELLER' => Tools::getValue(
                'MP_SELLER_INVOICE_TO_SELLER',
                Configuration::get('MP_SELLER_INVOICE_TO_SELLER')
            ),
            'MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC' => Tools::getValue(
                'MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC',
                Configuration::get('MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC')
            ),
            'MP_SELLER_INVOICE_TO_ADMIN_AUTOMATIC' => Tools::getValue(
                'MP_SELLER_INVOICE_TO_ADMIN_AUTOMATIC',
                Configuration::get('MP_SELLER_INVOICE_TO_ADMIN_AUTOMATIC')
            ),
        );
        if ($this->statuses_array) {
            $sellerOrderStatus = Tools::jsonDecode(Configuration::get('MP_SELLER_INVOICE_ORDER_STATUS'));
            foreach ($this->statuses_array as $sellerOrderStatusVal) {
                if ($sellerOrderStatus && in_array($sellerOrderStatusVal['id_group'], $sellerOrderStatus)) {
                    $groupVal = 1;
                } else {
                    $groupVal = '';
                }

                $this->fields_value['groupBox_'.$sellerOrderStatusVal['id_group']] = $groupVal;
            }
        }

        return $this->fields_value;
    }

    private function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $limitFooter = trim(Tools::getValue('MP_SELLER_FOOTER_TEXT_LIMIT'));
            $limitLegal = trim(Tools::getValue('MP_SELLER_LEGAL_TEXT_LIMIT'));
            if ($limitLegal != '') {
                if (!Validate::isInt($limitLegal)) {
                    $this->_postErrors[] = $this->l('Value of limit for legal note is not valid');
                } elseif ($limitLegal <= 0) {
                    $this->_postErrors[] = $this->l('Invalid character limit for legal text.');
                }
            } else {
                $this->_postErrors[] = $this->l('Limit for legal note can not be empty');
            }

            if ($limitFooter != '') {
                if (!Validate::isInt($limitFooter)) {
                    $this->_postErrors[] = $this->l('Value of footer note is not valid');
                } elseif ($limitFooter <= 0) {
                    $this->_postErrors[] = $this->l('Invalid character limit for footer text.');
                }
            } else {
                $this->_postErrors[] = $this->l('Limit for footer note can not be empty');
            }
        }

        if (Tools::isSubmit('btnCommissionSubmit')) {
            if (!Tools::getValue('groupBox')) {
                $this->_postErrors[] = $this->l('Please select order status');
            }
        }
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('MP_SELLER_INVOICE_ACTIVE', Tools::getValue('MP_SELLER_INVOICE_ACTIVE'));

            Configuration::updateValue('MP_SELLER_INVOICE_TO_ADMIN', Tools::getValue('MP_SELLER_INVOICE_TO_ADMIN'));
            Configuration::updateValue('MP_SELLER_INVOICE_TO_ADMIN_AUTOMATIC', Tools::getValue('MP_SELLER_INVOICE_TO_ADMIN_AUTOMATIC'));

            Configuration::updateValue('MP_SELLER_LEGAL_TEXT_LIMIT', Tools::getValue('MP_SELLER_LEGAL_TEXT_LIMIT'));
            Configuration::updateValue('MP_SELLER_FOOTER_TEXT_LIMIT', Tools::getValue('MP_SELLER_FOOTER_TEXT_LIMIT'));
        }

        if (Tools::isSubmit('btnCommissionSubmit')) {
            Configuration::updateValue('MP_SELLER_INVOICE_TO_SELLER', Tools::getValue('MP_SELLER_INVOICE_TO_SELLER'));
            Configuration::updateValue('MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC', Tools::getValue('MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC'));
            $sellerOrderStatus = Tools::getValue('groupBox');
            if ($sellerOrderStatus) {
                Configuration::updateValue('MP_SELLER_INVOICE_ORDER_STATUS', Tools::jsonEncode($sellerOrderStatus));
            } else {
                Configuration::updateValue('MP_SELLER_INVOICE_ORDER_STATUS', '');
            }
            Configuration::updateValue('MP_SELLER_INVOICE_TO_SELLER', Tools::getValue('MP_SELLER_INVOICE_TO_SELLER'));
        }

        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
        $module_config = $this->context->link->getAdminLink('AdminModules');
        Tools::redirectAdmin($module_config.'&configure='.$this->name.'&tab_module='.$this->tab.'&conf=4&module_name='.$this->name);
    }

    public function deleteConfigKeys()
    {
        $var = array(
            'MP_SELLER_INVOICE_ACTIVE', 'MP_SELLER_INVOICE_TO_SELLER',
            'MP_SELLER_INVOICE_TO_ADMIN', 'MP_SELLER_INVOICE_TO_ADMIN_AUTOMATIC',
            'MP_SELLER_LEGAL_TEXT_LIMIT', 'MP_SELLER_FOOTER_TEXT_LIMIT',
            'MP_SELLER_INVOICE_ORDER_STATUS', 'MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC',
        );
        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminSellerInvoicePanel', 'Manage Seller Invoice', 'AdminMarketplaceManagement');
        $this->installTab('AdminSellerOrderInvoice', 'Seller Order Invoice', 'AdminSellerInvoicePanel');
        $this->installTab('AdminCommissionInvoice', 'Admin Commission Invoice', 'AdminSellerInvoicePanel');

        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function wkRegisterHook()
    {
        $hooks = array(
            'actionValidateOrder', 'displayOrderConfirmation',
            'actionOrderStatusPostUpdate', 'displayMpOrderDetialShippingBottom',
            'displayAdminPsSellerOrderViewHead', 'displayMPMenuBottom',
            'displayAdminPsSellerOrderViewBody', 'displayMpEditProfileTab',
            'displayMpEditProfileTabContent', 'actionAfterUpdateSeller',
            'actionAfterAddSeller', 'displayMPMyAccountMenu',
            'actionBeforeUpdateSeller', 'actionBeforeAddSeller',
            'actionSellerPaymentTransaction',
            'registerGDPRConsent', 'actionDeleteGDPRCustomer', 'actionExportGDPRData',
            'actionAfterSellerOrderStatusUpdate',
        );

        foreach ($hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    public function install()
    {
        $status = array(2, 3, 4, 5);
        Configuration::updateValue('MP_SELLER_LEGAL_TEXT_LIMIT', '1000');
        Configuration::updateValue('MP_SELLER_FOOTER_TEXT_LIMIT', '1000');
        Configuration::updateValue('MP_SELLER_INVOICE_ACTIVE', 1);
        Configuration::updateValue('MP_SELLER_INVOICE_TO_ADMIN', 1);
        Configuration::updateValue('MP_SELLER_INVOICE_TO_ADMIN_AUTOMATIC', 1);
        Configuration::updateValue('MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC', 1);
        Configuration::updateValue('MP_SELLER_INVOICE_TO_SELLER', 1);
        Configuration::updateValue('MP_SELLER_INVOICE_ORDER_STATUS', Tools::jsonEncode($status));
        $mpSellerInvoiceDb = new WkMpSellerInvoiceDb();
        if (!parent::install()
            || !$mpSellerInvoiceDb->createTable()
            || !$this->callInstallTab()
            || !$this->wkRegisterHook()
            || !$this->saveDefaultSellerInvoiceConfig()
            ) {
            return false;
        }

        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }

            return true;
        }

        return false;
    }

    public function uninstall()
    {
        $mpSellerInvoiceDb = new WkMpSellerInvoiceDb();
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$mpSellerInvoiceDb->deleteTables()
            || !$this->deleteConfigKeys()
            ) {
            return false;
        }

        return true;
    }
}
