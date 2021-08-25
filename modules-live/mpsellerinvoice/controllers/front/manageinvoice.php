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

class MpSellerInvoiceManageInvoiceModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)) {
            $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($seller && $seller['active']) {
                // Seller Invoice Configuration
                $objInvoiceConfig = new MpSellerInvoiceConfig();
                if ($invoiceConfig = $objInvoiceConfig->isSellerInvoiceConfigExist($seller['id_seller'])) {
                    $objInvoiceConfig = new MpSellerInvoiceConfig($invoiceConfig['id']);
                    if (Validate::isLoadedObject($objInvoiceConfig)) {
                        $this->context->smarty->assign(
                            array(
                                'sellerInvoiceConfig' => (array) $objInvoiceConfig,
                            )
                        );
                    }
                }
                // End of code

                // Seller order invoice
                $objMpOrder = new WkMpSellerOrder();
                $objOrderStatus = new WkMpSellerOrderStatus();
                if ($mporders = $objMpOrder->getSellerOrders(
                    $this->context->language->id,
                    $this->context->customer->id
                )) {
                    $objSellerInvoice = new MpSellerOrderInvoiceRecord();
                    foreach ($mporders as $key => &$order) {
                        $objOrder = new Order($order['id_order']);
                        $order['pdf_download'] = 0;
                        $isCommission = $objSellerInvoice->getOrderInvoiceNumber($order['id_order'], $seller['id_seller']);
                        if ($isCommission) {
                            $order['pdf_download'] = 1;
                        }
                        if (!$objMpOrder->checkSellerOrder($objOrder, $seller['id_seller'])) {
                            $idOrderState = $objOrderStatus->getCurrentOrderState(
                                $order['id_order'],
                                $seller['id_seller']
                            );
                            if ($idOrderState) {
                                $state = new OrderState($idOrderState, $this->context->language->id);
                                $order['order_status'] = $state->name;
                            }
                        }
                        if ($sellerOrderStatus = Tools::jsonDecode(Configuration::get('WK_MP_SELLER_INVOICE_ORDER_STATUS'))) {
                            if (!in_array($objOrder->current_state, $sellerOrderStatus)) {
                                unset($mporders[$key]);
                            }
                        }
                        $order['buyer_info'] = new Customer($order['buyer_id_customer']);
                        if ($sellerOrderTotal = $objMpOrder->getTotalOrder(
                            $order['id_order'],
                            $this->context->customer->id
                        )) {
                            $order['total_paid'] = Tools::displayPrice(
                                $sellerOrderTotal,
                                (int) $order['id_currency']
                            );
                        }
                    }
                    $this->context->smarty->assign(
                        array(
                            'mpsellerorders' => $mporders,
                        )
                    );
                }
                // End of code

                // Admin commission invoice code
                $allCommissionInvoice = MpCommissionInvoiceHistory::getSellerInvoiceHistory($seller['id_seller']);
                $this->context->smarty->assign(
                    array(
                        'mporders' => $allCommissionInvoice,
                        )
                );
                // End of code

                $objInvoiceRecord = new MpSellerOrderInvoiceRecord();
                $lastInsertRow = $objInvoiceRecord->getLastRowByIdSeller($seller['id_seller']);
                if (!$lastInsertRow) {
                    $lastInsertRow = 1;
                }
                $this->context->smarty->assign(array(
                    'logic' => 'manage_sellerinvoice',
                    'self' => dirname(__FILE__),
                    'seller' => $seller,
                    'lastInsertRow' => $lastInsertRow,
                    'is_seller' => $seller['active'],
                    'static_token' => Tools::getToken(false),
                    'defaultConfig' => Configuration::get('PS_INVOICE_PREFIX', $this->context->language->id),
                ));
                $this->defineJSVars();
                WkMpHelper::assignGlobalVariables();
                WkMpHelper::assignDefaultLang($seller['id_seller']);
                $this->setTemplate('module:'.$this->module->name.'/views/templates/front/managesellerinvoice.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('mpsellerinvoice', 'manageinvoice')));
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitInvoiceConfig')) {
            if (isset($this->context->customer->id)) {
                $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
                if ($seller && $seller['active']) {
                    $sellerDefaultLanguage = Tools::getValue('default_lang');
                    $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);
                    $invoiceNumber = trim(Tools::getValue('invoice_number'));
                    $invoiceVAT = trim(Tools::getValue('invoice_vat'));

                    if (!trim(Tools::getValue('invoice_prefix_'.$defaultLang))) {
                        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                            $sellerLang = Language::getLanguage((int) $defaultLang);
                            $this->errors[] = sprintf($this->module->l('Invoice prefix is required in %s', 'manageinvoice'), $sellerLang['name']);
                        } else {
                            $this->errors[] = $this->module->l('Invoice prefix is required', 'manageinvoice');
                        }
                    } else {
                        $languages = Language::getLanguages();
                        $className = 'MpSellerInvoiceConfig';
                        $rules = call_user_func(array($className, 'getValidationRules'), $className);
                        foreach ($languages as $language) {
                            $languageName = '';
                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                $languageName = '('.$language['name'].')';
                            }
                            if (!Validate::isCleanHtml(trim(Tools::getValue('invoice_prefix_'.$language['id_lang'])))) {
                                $this->errors[] = sprintf($this->module->l('Invoice prefix field %s is invalid', 'manageinvoice'), $languageName);
                            } elseif (Tools::strlen(trim(Tools::getValue('invoice_prefix_'.$language['id_lang']))) > $rules['sizeLang']['invoice_prefix']) {
                                $this->errors[] = sprintf($this->module->l('Invoice prefix field is too long (%2$d chars max).', 'manageinvoice'), call_user_func(array($className, 'displayFieldName'), $className), $rules['sizeLang']['invoice_prefix']);
                            }

                            if (trim(Tools::getValue('invoice_legal_text_'.$language['id_lang']))) {
                                $legalText = trim(Tools::getValue('invoice_legal_text_'.$language['id_lang']));
                                $limit = (int) Configuration::get('MP_SELLER_LEGAL_TEXT_LIMIT');
                                if ($limit <= 0) {
                                    $limit = 1000;
                                }
                                if (!Validate::isCleanHtml($legalText)) {
                                    $this->errors[] = sprintf($this->module->l('Legal text field %s is invalid', 'manageinvoice'), $languageName);
                                } elseif (Tools::strlen(strip_tags($legalText)) > $limit) {
                                    $this->errors[] = sprintf($this->module->l('Legal text field %s is too long: (%d chars max).', 'manageinvoice'), $languageName, $limit);
                                }
                            }

                            if (trim(Tools::getValue('invoice_footer_text_'.$language['id_lang']))) {
                                $footerText = trim(Tools::getValue('invoice_footer_text_'.$language['id_lang']));
                                $limit = (int) Configuration::get('MP_SELLER_FOOTER_TEXT_LIMIT');
                                if ($limit <= 0) {
                                    $limit = 1000;
                                }
                                if (!Validate::isCleanHtml($footerText)) {
                                    $this->errors[] = sprintf($this->module->l('Footer text field %s is invalid', 'manageinvoice'), $languageName);
                                } elseif (Tools::strlen(strip_tags($footerText)) > $limit) {
                                    $this->errors[] = sprintf(
                                        $this->module->l('Footer text field %s is too long: (%d chars max).', 'manageinvoice'),
                                        $languageName,
                                        $limit
                                    );
                                }
                            }
                        }
                    }

                    $objInvoiceRecord = new MpSellerOrderInvoiceRecord();
                    $lastInsertRow = $objInvoiceRecord->getLastRowByIdSeller($seller['id_seller']);

                    if ($lastInsertRow) {
                        if ($invoiceNumber == 0) {
                        } elseif ($invoiceNumber < $lastInsertRow) {
                            $this->errors[] = sprintf(
                                $this->module->l(
                                    'Invoice number must be greater than or equal to %d',
                                    'manageinvoice'
                                ),
                                $lastInsertRow
                            );
                        }
                    }
                    if ($invoiceNumber) {
                        if (!Validate::isInt($invoiceNumber)) {
                            $this->errors[] = $this->module->l(
                                'Invoice number can only be numeric value',
                                'manageinvoice'
                            );
                        } elseif (strlen($invoiceNumber) > 20) {
                            $this->errors[] = $this->module->l(
                                'Invoice number can not be more than 10 digit',
                                'manageinvoice'
                            );
                        }
                    }

                    if ($invoiceVAT) {
                        if (!Validate::isGenericName($invoiceNumber)) {
                            $this->errors[] = $this->module->l(
                                'Invoice vat number is not valid',
                                'manageinvoice'
                            );
                        }
                    }
                    if (empty($this->errors)) {
                        $objInvoiceConfig = new MpSellerInvoiceConfig();
                        if ($isExist = $objInvoiceConfig->isSellerInvoiceConfigExist($seller['id_seller'])) {
                            $objInvoiceConfig = new MpSellerInvoiceConfig($isExist['id']);
                        }
                        $objInvoiceConfig->id_seller = (int) $seller['id_seller'];
                        $objInvoiceConfig->invoice_number = $invoiceNumber;
                        $objInvoiceConfig->invoice_vat = $invoiceVAT;
                        foreach (Language::getLanguages(false) as $language) {
                            $prefixIdLang = $language['id_lang'];
                            $legalIdLang = $language['id_lang'];
                            $footerIdLang = $language['id_lang'];

                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                if (!Tools::getValue('invoice_prefix_'.$language['id_lang'])) {
                                    $prefixIdLang = $defaultLang;
                                }
                                if (!Tools::getValue('invoice_legal_text_'.$language['id_lang'])) {
                                    $legalIdLang = $defaultLang;
                                }
                                if (!Tools::getValue('invoice_footer_text_'.$language['id_lang'])) {
                                    $footerIdLang = $defaultLang;
                                }
                            } else {
                                //if multilang is OFF then all fields will be filled as default lang content
                                $prefixIdLang = $defaultLang;
                                $legalIdLang = $defaultLang;
                                $footerIdLang = $defaultLang;
                            }

                            $objInvoiceConfig->invoice_prefix[$language['id_lang']] = trim(Tools::getValue('invoice_prefix_'.$prefixIdLang));
                            $objInvoiceConfig->invoice_legal_text[$language['id_lang']] = trim(Tools::getValue('invoice_legal_text_'.$legalIdLang));
                            $objInvoiceConfig->invoice_footer_text[$language['id_lang']] = trim(Tools::getValue('invoice_footer_text_'.$footerIdLang));
                        }
                        //dump($objInvoiceConfig);die;
                        $objInvoiceConfig->save();
                        Tools::redirect(
                            $this->context->link->getModuleLink(
                                $this->module->name,
                                'manageinvoice',
                                array(
                                    'config' => 1,
                                )
                            )
                        );
                    }
                }
            } else {
                Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('mpsellerinvoice', 'manageinvoice')));
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerStylesheet(
            'marketplace_account_css',
            'modules/marketplace/views/css/marketplace_account.css'
        );
        $this->registerStylesheet(
            'mp_global_style_css',
            'modules/marketplace/views/css/mp_global_style.css'
        );

        $this->registerStylesheet(
            'datatable_bootstrap_css',
            'modules/marketplace/views/css/datatable_bootstrap.css'
        );

        $this->registerJavascript(
            'mp-change_multilang',
            'modules/marketplace/views/js/change_multilang.js'
        );

        $this->registerJavascript(
            'mp-seller-invoice',
            'modules/mpsellerinvoice/views/js/sellerinvoice.js'
        );

        $this->registerJavascript(
            'jquery-dataTables-min',
            'modules/marketplace/views/js/jquery.dataTables.min.js'
        );

        $this->registerJavascript(
            'mp-dataTables-bootstrap',
            'modules/marketplace/views/js/dataTables.bootstrap.js'
        );

        $this->registerJavascript(
            'mp-mporder',
            'modules/marketplace/views/js/mporder.js'
        );
    }

    public function defineJSVars()
    {
        $jsVars = array(
            'manageinvoice_link' => $this->context->link->getModuleLink($this->module->name, 'manageinvoice'),
            'mporderdetails_link' => $this->context->link->getModuleLink('marketplace', 'mporderdetails'),
            'display_name' => $this->module->l('Display', 'manageinvoice'),
            'records_name' => $this->module->l('Records per page', 'manageinvoice'),
            'no_product' => $this->module->l('No order found', 'manageinvoice'),
            'show_page' => $this->module->l('Showing page', 'manageinvoice'),
            'show_of' => $this->module->l('of', 'manageinvoice'),
            'no_record' => $this->module->l('No records available', 'manageinvoice'),
            'filter_from' => $this->module->l('filtered from', 'manageinvoice'),
            't_record' => $this->module->l('Total records', 'manageinvoice'),
            'search_item' => $this->module->l('Search', 'manageinvoice'),
            'p_page' => $this->module->l('Previous', 'manageinvoice'),
            'n_page' => $this->module->l('Next', 'manageinvoice'),
        );
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $jsVars['friendly_url'] = 1;
        } else {
            $jsVars['friendly_url'] = 0;
        }

        Media::addJsDef($jsVars);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'manageinvoice'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Invoice', 'manageinvoice'),
            'url' => '',
        );

        return $breadcrumb;
    }
}
