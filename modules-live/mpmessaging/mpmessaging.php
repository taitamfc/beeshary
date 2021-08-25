<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

require_once dirname(__FILE__).'/libs/plivo/vendor/autoload.php';  // Loads plivo the library
require_once dirname(__FILE__).'/libs/Twilio/autoload.php'; // Loads twilio the library
require_once dirname(__FILE__).'/libs/clicksend/vendor/autoload.php'; // Loads clicksend the library

class MpMessaging extends Module
{
    protected $_html;
    protected $_postErrors = array();
    public $smsAPIList = array(
        'plivo' => 'Plivo',
        'twilio' => 'Twilio',
        'clicksend' => 'ClickSend',
    );

    public function __construct()
    {
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->version = '5.0.0';
        $this->name = 'mpmessaging';
        $this->author = $this->l('Webkul');
        $this->tab = 'front_office_features';
        parent::__construct();

        $this->displayName = $this->l('Marketplace Messaging(SMS Notification)');
        $this->description = $this->l('Provide Messaging(SMS Notification) for marketplace sellers and prestashop customers');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->smsAPIList['plivo'] = $this->l('Plivo');
        $this->smsAPIList['twilio'] = $this->l('Twilio');
        $this->smsAPIList['clicksend'] = $this->l('ClickSend');
    }

    private function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
            $defaultLang = Language::getLanguage((int) Configuration::get('PS_LANG_DEFAULT'));
            if (Tools::getValue('WK_MPSMS_ORDER_SMS')) {
                if (!Tools::getValue('WK_MPSMS_ORDER_TEMPLATE_'.$idLang)) {
                    $this->_postErrors[] = $this->l('Order confirmation template is required in '.$defaultLang['name']);
                }
            }

            if (Tools::getValue('WK_MPSMS_STATUS_SMS')) {
                if (!Tools::getValue('WK_MPSMS_STATUS_TEMPLATE_'.$idLang)) {
                    $this->_postErrors[] = $this->l('Order status template is required in '.$defaultLang['name']);
                }
            }

            if (Tools::getValue('WK_MPSMS_TRACKING_SMS')) {
                if (!Tools::getValue('WK_MPSMS_TRACKING_TEMPLATE_'.$idLang)) {
                    $this->_postErrors[] = $this->l('Order tracking template is required in '.$defaultLang['name']);
                }
            }

            if (Tools::getValue('WK_MPSMS_SELLER_ORDER_SMS')) {
                if (!Tools::getValue('WK_MPSMS_SELLER_ORDER_TEMPLATE_'.$idLang)) {
                    $this->_postErrors[] = $this->l('Seller Order template is required in '.$defaultLang['name']);
                }
            }

            if (Tools::getValue('WK_MPSMS_SELLER_PRODUCT_SMS')) {
                if (!Tools::getValue('WK_MPSMS_SELLER_PRODUCT_TEMPLATE_'.$idLang)) {
                    $this->_postErrors[] = $this->l('Seller Product activation/de-activation template is required in '.$defaultLang['name']);
                }
            }

            if (Tools::getValue('WK_MPSMS_SELLER_PROFILE_SMS')) {
                if (!Tools::getValue('WK_MPSMS_SELLER_PROFILE_TEMPLATE_'.$idLang)) {
                    $this->_postErrors[] = $this->l('Seller Profile activation/de-activation template is required in '.$defaultLang['name']);
                }
            }

            if (Tools::getValue('WK_MPSMS_MOBILE_PREFIX')) {
                if (!Tools::getValue('WK_MPSMS_MOBILE_PREFIX_NO')) {
                    $this->_postErrors[] = $this->l('Enter prefix value or disable prefix.');
                }
            }

            switch (Tools::getValue('WK_MPSMS_API')) {
                case 'plivo':
                    if (!Tools::getValue('WK_MPSMS_PLIVO_AUTH_ID')) {
                        $this->_postErrors[] = $this->l('Plivo Auth ID is required filed.');
                    }
                    if (!Tools::getValue('WK_MPSMS_PLIVO_TOKEN')) {
                        $this->_postErrors[] = $this->l('Plivo Token is required filed.');
                    }
                    if (!Tools::getValue('WK_MPSMS_PLIVO_NUMBER')) {
                        $this->_postErrors[] = $this->l('Plivo Number is required filed.');
                    }
                    break;
                case 'twilio':
                    if (!Tools::getValue('WK_MPSMS_TWILIO_AC_ID')) {
                        $this->_postErrors[] = $this->l('Twilio Account ID is required filed.');
                    }
                    if (!Tools::getValue('WK_MPSMS_TWILIO_PASSWORD')) {
                        $this->_postErrors[] = $this->l('Twilio Password is required filed.');
                    }
                    if (!Tools::getValue('WK_MPSMS_TWILIO_NUMBER')) {
                        $this->_postErrors[] = $this->l('Twilio Number is required filed.');
                    }
                    break;
                case 'clicksend':
                    if (!Tools::getValue('WK_MPSMS_CLICKSEND_USER_NAME')) {
                        $this->_postErrors[] = $this->l('ClickSend User Name is required filed.');
                    }
                    if (!Tools::getValue('WK_MPSMS_CLICKSEND_API_KEY')) {
                        $this->_postErrors[] = $this->l('ClickSend API Key is required filed.');
                    }
                    if (!Tools::getValue('WK_MPSMS_CLICKSEND_NUMBER')) {
                        $this->_postErrors[] = $this->l('ClickSend number is required filed.');
                    }
                    break;
            }
        }
    }

    public function getContent()
    {
        $this->context->controller->addJs($this->_path.'views/js/wk_config.js');

        if (Tools::isSubmit('btnSubmit')) {
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

        $this->context->smarty->assign(
            array(
                'this_path' => $this->_path,
                'smsAPIList' => $this->smsAPIList,
                'languages' => Language::getLanguages(),
                'totalLanguages' => count(Language::getLanguages()),
                'WK_MPSMS_API' => Configuration::get('WK_MPSMS_API'),
                'action_url' => Tools::safeOutput($_SERVER['REQUEST_URI']),
                'WK_MPSMS_ORDER_SMS' => Configuration::get('WK_MPSMS_ORDER_SMS'),
                'WK_MPSMS_SELLER_PROFILE_SMS' => Configuration::get('WK_MPSMS_SELLER_PROFILE_SMS'),
                'WK_MPSMS_SELLER_PRODUCT_SMS' => Configuration::get('WK_MPSMS_SELLER_PRODUCT_SMS'),
                'WK_MPSMS_SELLER_ORDER_SMS' => Configuration::get('WK_MPSMS_SELLER_ORDER_SMS'),
                'WK_MPSMS_MOBILE_PREFIX_NO' => Configuration::get('WK_MPSMS_MOBILE_PREFIX_NO'),
                'WK_MPSMS_MOBILE_PREFIX' => Configuration::get('WK_MPSMS_MOBILE_PREFIX'),
                'WK_MPSMS_STATUS_SMS' => Configuration::get('WK_MPSMS_STATUS_SMS'),
                'WK_MPSMS_PLIVO_TOKEN' => Configuration::get('WK_MPSMS_PLIVO_TOKEN'),
                'WK_MPSMS_PLIVO_NUMBER' => Configuration::get('WK_MPSMS_PLIVO_NUMBER'),
                'WK_MPSMS_ADDRESS_TYPE' => Configuration::get('WK_MPSMS_ADDRESS_TYPE'),
                'WK_MPSMS_TRACKING_SMS' => Configuration::get('WK_MPSMS_TRACKING_SMS'),
                'WK_MPSMS_TWILIO_AC_ID' => Configuration::get('WK_MPSMS_TWILIO_AC_ID'),
                'WK_MPSMS_TWILIO_NUMBER' => Configuration::get('WK_MPSMS_TWILIO_NUMBER'),
                'WK_MPSMS_PLIVO_AUTH_ID' => Configuration::get('WK_MPSMS_PLIVO_AUTH_ID'),
                'WK_MPSMS_TWILIO_PASSWORD' => Configuration::get('WK_MPSMS_TWILIO_PASSWORD'),
                'WK_MPSMS_CLICKSEND_NUMBER' => Configuration::get('WK_MPSMS_CLICKSEND_NUMBER'),
                'currentLang' => Language::getLanguage((int) $this->context->employee->id_lang),
                'WK_MPSMS_CLICKSEND_API_KEY' => Configuration::get('WK_MPSMS_CLICKSEND_API_KEY'),
                'orderStatusList' => OrderState::getOrderStates($this->context->employee->id_lang),
                'WK_MPSMS_CLICKSEND_USER_NAME' => Configuration::get('WK_MPSMS_CLICKSEND_USER_NAME'),
                'WK_MPSMS_ORDER_STATUS' => Tools::jsonDecode(Configuration::get('WK_MPSMS_ORDER_STATUS'), true),
                'WK_MPSMS_ORDER_TEMPLATE' => Tools::jsonDecode(Configuration::get('WK_MPSMS_ORDER_TEMPLATE'), true),
                'WK_MPSMS_STATUS_TEMPLATE' => Tools::jsonDecode(Configuration::get('WK_MPSMS_STATUS_TEMPLATE'), true),
                'WK_MPSMS_TRACKING_TEMPLATE' => Tools::jsonDecode(Configuration::get('WK_MPSMS_TRACKING_TEMPLATE'), true),
                'WK_MPSMS_SELLER_PROFILE_TEMPLATE' => Tools::jsonDecode(Configuration::get('WK_MPSMS_SELLER_PROFILE_TEMPLATE'), true),
                'WK_MPSMS_SELLER_PRODUCT_TEMPLATE' => Tools::jsonDecode(Configuration::get('WK_MPSMS_SELLER_PRODUCT_TEMPLATE'), true),
                'WK_MPSMS_SELLER_ORDER_TEMPLATE' => Tools::jsonDecode(Configuration::get('WK_MPSMS_SELLER_ORDER_TEMPLATE'), true),
            )
        );

        $this->_html .= $this->display(__FILE__, './views/templates/admin/mp_config.tpl');

        return $this->_html;
    }

    private function _postProcess()
    {
        $status = array();
        if (Tools::getValue('orderStatus')) {
            $status = Tools::getValue('orderStatus');
        }
        $WK_MPSMS_ORDER_TEMPLATE = array();
        $WK_MPSMS_STATUS_TEMPLATE = array();
        $WK_MPSMS_TRACKING_TEMPLATE = array();
        $WK_MPSMS_SELLER_ORDER_TEMPLATE = array();
        $WK_MPSMS_SELLER_PROFILE_TEMPLATE = array();
        $WK_MPSMS_SELLER_PRODUCT_TEMPLATE = array();
        $defaultLang = Configuration::get('PS_LANG_DEFAULT');

        Configuration::updateValue('WK_MPSMS_ADDRESS_TYPE', Tools::getValue('WK_MPSMS_ADDRESS_TYPE'));
        Configuration::updateValue('WK_MPSMS_MOBILE_PREFIX', Tools::getValue('WK_MPSMS_MOBILE_PREFIX'));
        Configuration::updateValue('WK_MPSMS_MOBILE_PREFIX_NO', Tools::getValue('WK_MPSMS_MOBILE_PREFIX_NO'));

        Configuration::updateValue('WK_MPSMS_ORDER_SMS', Tools::getValue('WK_MPSMS_ORDER_SMS'));
        Configuration::updateValue('WK_MPSMS_STATUS_SMS', Tools::getValue('WK_MPSMS_STATUS_SMS'));
        Configuration::updateValue('WK_MPSMS_TRACKING_SMS', Tools::getValue('WK_MPSMS_TRACKING_SMS'));

        Configuration::updateValue('WK_MPSMS_SELLER_ORDER_SMS', Tools::getValue('WK_MPSMS_SELLER_ORDER_SMS'));
        Configuration::updateValue('WK_MPSMS_SELLER_PRODUCT_SMS', Tools::getValue('WK_MPSMS_SELLER_PRODUCT_SMS'));
        Configuration::updateValue('WK_MPSMS_SELLER_PROFILE_SMS', Tools::getValue('WK_MPSMS_SELLER_PROFILE_SMS'));


        Configuration::updateValue('WK_MPSMS_API', Tools::getValue('WK_MPSMS_API'));
        Configuration::updateValue('WK_MPSMS_ORDER_STATUS', Tools::jsonEncode($status));
        Configuration::updateValue('WK_MPSMS_PLIVO_TOKEN', Tools::getValue('WK_MPSMS_PLIVO_TOKEN'));
        Configuration::updateValue('WK_MPSMS_PLIVO_NUMBER', Tools::getValue('WK_MPSMS_PLIVO_NUMBER'));
        Configuration::updateValue('WK_MPSMS_TWILIO_AC_ID', Tools::getValue('WK_MPSMS_TWILIO_AC_ID'));
        Configuration::updateValue('WK_MPSMS_PLIVO_AUTH_ID', Tools::getValue('WK_MPSMS_PLIVO_AUTH_ID'));
        Configuration::updateValue('WK_MPSMS_TWILIO_NUMBER', Tools::getValue('WK_MPSMS_TWILIO_NUMBER'));
        Configuration::updateValue('WK_MPSMS_TWILIO_PASSWORD', Tools::getValue('WK_MPSMS_TWILIO_PASSWORD'));
        Configuration::updateValue('WK_MPSMS_CLICKSEND_NUMBER', Tools::getValue('WK_MPSMS_CLICKSEND_NUMBER'));
        Configuration::updateValue('WK_MPSMS_CLICKSEND_API_KEY', Tools::getValue('WK_MPSMS_CLICKSEND_API_KEY'));
        Configuration::updateValue('WK_MPSMS_CLICKSEND_USER_NAME', Tools::getValue('WK_MPSMS_CLICKSEND_USER_NAME'));

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::getValue('WK_MPSMS_ORDER_TEMPLATE_'.$language['id_lang'])) {
                $WK_MPSMS_ORDER_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_ORDER_TEMPLATE_'.$language['id_lang']);
            } else {
                $WK_MPSMS_ORDER_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_ORDER_TEMPLATE_'.$defaultLang);
            }

            if (Tools::getValue('WK_MPSMS_STATUS_TEMPLATE_'.$language['id_lang'])) {
                $WK_MPSMS_STATUS_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_STATUS_TEMPLATE_'.$language['id_lang']);
            } else {
                $WK_MPSMS_STATUS_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_STATUS_TEMPLATE_'.$defaultLang);
            }

            if (Tools::getValue('WK_MPSMS_TRACKING_TEMPLATE_'.$language['id_lang'])) {
                $WK_MPSMS_TRACKING_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_TRACKING_TEMPLATE_'.$language['id_lang']);
            } else {
                $WK_MPSMS_TRACKING_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_TRACKING_TEMPLATE_'.$defaultLang);
            }

            if (Tools::getValue('WK_MPSMS_SELLER_ORDER_TEMPLATE_'.$language['id_lang'])) {
                $WK_MPSMS_SELLER_ORDER_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_SELLER_ORDER_TEMPLATE_'.$language['id_lang']);
            } else {
                $WK_MPSMS_SELLER_ORDER_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_SELLER_ORDER_TEMPLATE_'.$defaultLang);
            }

            if (Tools::getValue('WK_MPSMS_SELLER_PROFILE_TEMPLATE_'.$language['id_lang'])) {
                $WK_MPSMS_SELLER_PROFILE_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_SELLER_PROFILE_TEMPLATE_'.$language['id_lang']);
            } else {
                $WK_MPSMS_SELLER_PROFILE_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_SELLER_PROFILE_TEMPLATE_'.$defaultLang);
            }

            if (Tools::getValue('WK_MPSMS_SELLER_PRODUCT_TEMPLATE_'.$language['id_lang'])) {
                $WK_MPSMS_SELLER_PRODUCT_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_SELLER_PRODUCT_TEMPLATE_'.$language['id_lang']);
            } else {
                $WK_MPSMS_SELLER_PRODUCT_TEMPLATE[$language['id_lang']] = Tools::getValue('WK_MPSMS_SELLER_PRODUCT_TEMPLATE_'.$defaultLang);
            }
        }
        Configuration::updateValue('WK_MPSMS_ORDER_TEMPLATE', Tools::jsonEncode($WK_MPSMS_ORDER_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_STATUS_TEMPLATE', Tools::jsonEncode($WK_MPSMS_STATUS_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_TRACKING_TEMPLATE', Tools::jsonEncode($WK_MPSMS_TRACKING_TEMPLATE));

        Configuration::updateValue('WK_MPSMS_SELLER_ORDER_TEMPLATE', Tools::jsonEncode($WK_MPSMS_SELLER_ORDER_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_SELLER_PROFILE_TEMPLATE', Tools::jsonEncode($WK_MPSMS_SELLER_PROFILE_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_SELLER_PRODUCT_TEMPLATE', Tools::jsonEncode($WK_MPSMS_SELLER_PRODUCT_TEMPLATE));

        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    public function getMobileNumber($order)
    {
        $idAddress = $order->id_address_invoice;
        if (Configuration::get('WK_MPSMS_ADDRESS_TYPE') == 'delivery') {
            $idAddress = $order->id_address_delivery;
        }

        $address = new Address($idAddress);
        return $address->phone;
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        
        if (Tools::getIsset('controller') && Configuration::get('WK_MPSMS_STATUS_SMS')) {
            $controller = Tools::getValue('controller');
            if ($controller != 'order'
                && $controller != 'payment'
                && $controller != 'validation'
                && $controller != 'order-confirmation') {
                $order = new Order($params['id_order']);
                $idLang = $order->id_lang;
                $mobileNumber = $this->getMobileNumber($order);
                $statusMap = Tools::jsonDecode(Configuration::get('WK_MPSMS_ORDER_STATUS'), true);
                $statusAllTemplates = Tools::jsonDecode(Configuration::get('WK_MPSMS_STATUS_TEMPLATE'), true);

                if (in_array($params['newOrderStatus']->id, $statusMap)
                    && array_key_exists($idLang, $statusAllTemplates)
                    && $mobileNumber) {
                    $customer = new Customer($order->id_customer);
                    $currency = new Currency($order->id_currency);

                    $from = array(
                        '{customerFirstName}',
                        '{customerLastName}',
                        '{customerOrderHistory}',
                        '{referenceNo}',
                        '{orderStatus}',
                        '{trackingNumber}',
                        '{orderAmount}',
                    );

                    $to = array(
                        $customer->firstname,
                        $customer->lastname,
                        $this->context->link->getPageLink('history'),
                        $order->reference,
                        $params['newOrderStatus']->name,
                        $order->shipping_number,
                        Tools::displayPrice($order->total_paid_tax_incl, $currency),
                    );
                    $template = str_replace($from, $to, $statusAllTemplates[$idLang]);

                    // send message
                    $this->sendMessage($mobileNumber, $template);
                }
            }
        }
    }

    public function hookActionAdminOrdersTrackingNumberUpdate($params)
    {
        if (Configuration::get('WK_MPSMS_TRACKING_SMS')) {
            $idLang = $params['order']->id_lang;
            $mobileNumber = $this->getMobileNumber($params['order']);
            $orderStatus = new OrderState($params['order']->current_state, $idLang);
            $trackingAllTemplates = Tools::jsonDecode(Configuration::get('WK_MPSMS_TRACKING_TEMPLATE'), true);

            if (array_key_exists($idLang, $trackingAllTemplates) && $mobileNumber) {
                $currency = new Currency($params['order']->id_currency);

                $from = array(
                    '{customerFirstName}',
                    '{customerLastName}',
                    '{customerOrderHistory}',
                    '{referenceNo}',
                    '{orderStatus}',
                    '{trackingNumber}',
                    '{orderAmount}',
                );

                $to = array(
                    $params['customer']->firstname,
                    $params['customer']->lastname,
                    $this->context->link->getPageLink('history'),
                    $params['order']->reference,
                    $orderStatus->name,
                    $params['order']->shipping_number,
                    Tools::displayPrice($params['order']->total_paid_tax_incl, $currency),
                );
                $template = str_replace($from, $to, $trackingAllTemplates[$idLang]);

                // send message
                $this->sendMessage($mobileNumber, $template);
            }
        }
    }

    public function hookActionValidateOrder($params)
    {
        if (Configuration::get('WK_MPSMS_ORDER_SMS')) {
            $idLang = $params['order']->id_lang;
            $mobileNumber = $this->getMobileNumber($params['order']);
            $orderAllTemplates = Tools::jsonDecode(Configuration::get('WK_MPSMS_ORDER_TEMPLATE'), true);

            if (array_key_exists($idLang, $orderAllTemplates) && $mobileNumber) {
                $currency = new Currency($params['order']->id_currency);

                $from = array(
                    '{customerFirstName}',
                    '{customerLastName}',
                    '{customerOrderHistory}',
                    '{referenceNo}',
                    '{orderAmount}',
                );

                $to = array(
                    $params['customer']->firstname,
                    $params['customer']->lastname,
                    $this->context->link->getPageLink('history'),
                    $params['order']->reference,
                    Tools::displayPrice($params['order']->total_paid_tax_incl, $currency),
                );
                $template = str_replace($from, $to, $orderAllTemplates[$idLang]);

                // send message
                $this->sendMessage($mobileNumber, $template);
            }
        }

        if (Configuration::get('WK_MPSMS_SELLER_ORDER_SMS')) {
            $orderProducts = $params['order']->getProducts();
            $sellerProducts = array();
            foreach ($orderProducts as $product) {
                $mpProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($product['product_id']);
                if ($mpProduct) {
                    $sellerProducts[$mpProduct['id_seller']]['productList'][$product['product_id']] = $product;

                    if (array_key_exists('noOfProducts', $sellerProducts[$mpProduct['id_seller']])) {
                        $sellerProducts[$mpProduct['id_seller']]['noOfProducts'] += 1;
                        $sellerProducts[$mpProduct['id_seller']]['qtyOfProducts'] += $product['product_quantity'];
                        $sellerProducts[$mpProduct['id_seller']]['totalPrice'] += $product['total_price_tax_incl'];
                    } else {
                        $sellerProducts[$mpProduct['id_seller']]['noOfProducts'] = 1;
                        $sellerProducts[$mpProduct['id_seller']]['qtyOfProducts'] = $product['product_quantity'];
                        $sellerProducts[$mpProduct['id_seller']]['totalPrice'] = $product['total_price_tax_incl'];
                    }
                }
            }

            if ($sellerProducts) {
                $objSeller = new WkMpSeller();
                $sellerOrderAllTemplates = Tools::jsonDecode(Configuration::get('WK_MPSMS_SELLER_ORDER_TEMPLATE'), true);
                foreach ($sellerProducts as $key => $product) {
                    $mpSeller = $objSeller->getSellerWithLangBySellerId($key);
                    if ($mpSeller) {
                        if (array_key_exists($mpSeller['default_lang'], $sellerOrderAllTemplates) && $mpSeller['phone']) {
                            $currency = new Currency($params['order']->id_currency);

                            $from = array(
                                '{customerFirstName}',
                                '{customerLastName}',
                                '{referenceNo}',
                                '{orderAmount}',
                                '{sellerFirstName}',
                                '{sellerLastName}',
                                '{totalNumberOfProducts}',
                                '{totalQuantityOfAllProducts}',
                            );

                            $to = array(
                                $params['customer']->firstname,
                                $params['customer']->lastname,
                                $params['order']->reference,
                                Tools::displayPrice($params['order']->total_paid_tax_incl, $currency),
                                $mpSeller['seller_firstname'],
                                $mpSeller['seller_lastname'],
                                $product['noOfProducts'],
                                $product['qtyOfProducts'],
                            );
                            $template = str_replace($from, $to, $sellerOrderAllTemplates[$mpSeller['default_lang']]);

                            // send message
                            $this->sendMessage($mpSeller['phone'], $template);
                        }
                    }
                }
            }
        }
    }

    public function hookActionToogleSellerStatus($params)
    {
        if (Configuration::get('WK_MPSMS_SELLER_PROFILE_SMS')) {
            $objSeller = new WkMpSeller();
            $mpSeller = $objSeller->getSellerWithLangBySellerId($params['id_seller']);
            if ($mpSeller) {
                $idLang = $mpSeller['default_lang'];
                $mobileNumber = $mpSeller['phone'];
                if ($params['status']) {
                    $status = $this->l('activated');
                } else {
                    $status = $this->l('de-activated');
                }
                $profileAllTemplates = Tools::jsonDecode(Configuration::get('WK_MPSMS_SELLER_PROFILE_TEMPLATE'), true);

                if (array_key_exists($idLang, $profileAllTemplates) && $mobileNumber) {
                    $from = array(
                        '{sellerFirstName}',
                        '{sellerLastName}',
                        '{status}',
                        '{businessEmail}',
                        '{shopNameUnique}'
                    );

                    $to = array(
                        $mpSeller['seller_firstname'],
                        $mpSeller['seller_lastname'],
                        $status,
                        $mpSeller['business_email'],
                        $mpSeller['shop_name_unique'],
                    );
                    $template = str_replace($from, $to, $profileAllTemplates[$idLang]);

                    // send message
                    $this->sendMessage($mobileNumber, $template);
                }
            }
        }
    }

    public function hookActionAfterToggleMPProductStatus($params)
    {
        if (Configuration::get('WK_MPSMS_SELLER_PRODUCT_SMS')) {
            $mpProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($params['id_product']);
            // check is mp product product
            if ($mpProduct) {
                $objSeller = new WkMpSeller();
                $mpSeller = $objSeller->getSellerWithLangBySellerId($mpProduct['id_seller']);
                $idLang = $mpSeller['default_lang'];

                $mpProduct = WkMpSellerProduct::getSellerProductByIdProduct($mpProduct['id_mp_product'], $idLang);
                // check is mp product information in seller's default language
                if ($mpProduct) {
                    $mobileNumber = $mpSeller['phone'];
                    if ($params['active']) {
                        $status = $this->l('activated');
                    } else {
                        $status = $this->l('de-activated');
                    }
                    $productAllTemplates = Tools::jsonDecode(Configuration::get('WK_MPSMS_SELLER_PRODUCT_TEMPLATE'), true);

                    if (array_key_exists($idLang, $productAllTemplates) && $mobileNumber) {
                        $from = array(
                            '{sellerFirstName}',
                            '{sellerLastName}',
                            '{status}',
                            '{businessEmail}',
                            '{shopNameUnique}',
                            '{productName}'
                        );

                        $to = array(
                            $mpSeller['seller_firstname'],
                            $mpSeller['seller_lastname'],
                            $status,
                            $mpSeller['business_email'],
                            $mpSeller['shop_name_unique'],
                            $mpProduct['product_name'],
                        );
                        $template = str_replace($from, $to, $productAllTemplates[$idLang]);

                        // send message
                        $this->sendMessage($mobileNumber, $template);
                    }
                }
            }
        }
    }

    public function sendMessage($to, $body)
    {
        if (Configuration::get('WK_MPSMS_MOBILE_PREFIX')) {
            $to = Configuration::get('WK_MPSMS_MOBILE_PREFIX_NO').$to;
        }

        try {
            switch (Configuration::get('WK_MPSMS_API')) {
                case 'plivo':
                    $client = new \Plivo\RestAPI(
                        Configuration::get('WK_MPSMS_PLIVO_AUTH_ID'),
                        Configuration::get('WK_MPSMS_PLIVO_TOKEN')
                    );
                    $params = array(
                        'src' => Configuration::get('WK_MPSMS_PLIVO_NUMBER'), // Sender's phone number with country code
                        'dst' => $to, // Receiver's phone number with country code
                        'text' => $body, // Your SMS text message
                    );
                    // Send message
                    $response = $client->send_message($params);
                    if (is_array($response)) {
                        if ($response['status'] == 400) {
                            $this->createErrorLog($to, $response['response']['error']);
                        } else {
                            if ($response['status'] == 401) {
                                $this->createErrorLog($to, 'Credentials are not valid.');
                            }
                        }
                    }
                    break;
                case 'twilio':
                    $client = new \Twilio\Rest\Client(
                        Configuration::get('WK_MPSMS_TWILIO_AC_ID'),
                        Configuration::get('WK_MPSMS_TWILIO_PASSWORD')
                    );
                    $client->messages->create(
                        $to,
                        array(
                            'from' => Configuration::get('WK_MPSMS_TWILIO_NUMBER'),
                            'body' => $body,
                        )
                    );
                    break;
                case 'clicksend':
                    // Prepare ClickSend client.
                    $client = new \ClickSendLib\ClickSendClient(
                        Configuration::get('WK_MPSMS_CLICKSEND_USER_NAME'),
                        Configuration::get('WK_MPSMS_CLICKSEND_API_KEY')
                    );

                    // Get SMS instance.
                    $sms = $client->getSMS();
                    $messages = array(
                        array(
                            'source' => 'php',
                            'from' => Configuration::get('WK_MPSMS_CLICKSEND_NUMBER'),
                            'body' => $body,
                            'to' => $to,
                            'schedule' => strtotime(date('m/d/Y')),
                            'custom_string' => 'this is a test',
                        ),
                    );

                    // Send SMS.
                    $sms->sendSms(array('messages' => $messages));
                    break;
            }
        } catch (Exception $e) {
            $this->createErrorLog($to, $e->getMessage());
        }
    }

    public function createErrorLog($to, $errorMsg)
    {
        $error_file = dirname(__FILE__)."/error_log.txt";
        $now = date('Y:m:d:H:i:s')."\t".$to."\t".Configuration::get('WK_MPSMS_API')."\t".$errorMsg."\n";
        file_put_contents($error_file, $now, FILE_APPEND | LOCK_EX);
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($params['object']->id) {
            $newIdLang = $params['object']->id;
            $defaultLang = Configuration::get('PS_LANG_DEFAULT');
            $WK_MPSMS_ORDER_TEMPLATE = Tools::jsonDecode(Configuration::get('WK_MPSMS_ORDER_TEMPLATE'), true);
            $WK_MPSMS_STATUS_TEMPLATE = Tools::jsonDecode(Configuration::get('WK_MPSMS_STATUS_TEMPLATE'), true);
            $WK_MPSMS_TRACKING_TEMPLATE = Tools::jsonDecode(Configuration::get('WK_MPSMS_TRACKING_TEMPLATE'), true);

            $WK_MPSMS_SELLER_ORDER_TEMPLATE = Tools::jsonDecode(Configuration::get('WK_MPSMS_SELLER_ORDER_TEMPLATE'), true);
            $WK_MPSMS_SELLER_PROFILE_TEMPLATE = Tools::jsonDecode(Configuration::get('WK_MPSMS_SELLER_PROFILE_TEMPLATE'), true);
            $WK_MPSMS_SELLER_PRODUCT_TEMPLATE = Tools::jsonDecode(Configuration::get('WK_MPSMS_SELLER_PRODUCT_TEMPLATE'), true);

            $WK_MPSMS_ORDER_TEMPLATE[$newIdLang] = $WK_MPSMS_ORDER_TEMPLATE[$defaultLang];
            $WK_MPSMS_STATUS_TEMPLATE[$newIdLang] = $WK_MPSMS_STATUS_TEMPLATE[$defaultLang];
            $WK_MPSMS_TRACKING_TEMPLATE[$newIdLang] = $WK_MPSMS_TRACKING_TEMPLATE[$defaultLang];

            $WK_MPSMS_SELLER_ORDER_TEMPLATE[$newIdLang] = $WK_MPSMS_SELLER_ORDER_TEMPLATE[$defaultLang];
            $WK_MPSMS_SELLER_PROFILE_TEMPLATE[$newIdLang] = $WK_MPSMS_SELLER_PROFILE_TEMPLATE[$defaultLang];
            $WK_MPSMS_SELLER_PRODUCT_TEMPLATE[$newIdLang] = $WK_MPSMS_SELLER_PRODUCT_TEMPLATE[$defaultLang];
        }
        Configuration::updateValue('WK_MPSMS_ORDER_TEMPLATE', Tools::jsonEncode($WK_MPSMS_ORDER_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_STATUS_TEMPLATE', Tools::jsonEncode($WK_MPSMS_STATUS_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_TRACKING_TEMPLATE', Tools::jsonEncode($WK_MPSMS_TRACKING_TEMPLATE));

        Configuration::updateValue('WK_MPSMS_SELLER_ORDER_TEMPLATE', Tools::jsonEncode($WK_MPSMS_SELLER_ORDER_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_SELLER_PROFILE_TEMPLATE', Tools::jsonEncode($WK_MPSMS_SELLER_PROFILE_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_SELLER_PRODUCT_TEMPLATE', Tools::jsonEncode($WK_MPSMS_SELLER_PRODUCT_TEMPLATE));
    }

    public function registerPsAndMpHook()
    {
        return $this->registerHook(
            array(
                'actionValidateOrder',
                'actionToogleSellerStatus',
                'actionOrderStatusPostUpdate',
                'actionObjectLanguageAddAfter',
                'actionAfterToggleMPProductStatus',
                'actionAdminOrdersTrackingNumberUpdate',
            )
        );
    }

    public function insertConfigData()
    {
        $WK_MPSMS_ORDER_TEMPLATE = array();
        $WK_MPSMS_STATUS_TEMPLATE = array();
        $WK_MPSMS_TRACKING_TEMPLATE = array();

        $WK_MPSMS_SELLER_ORDER_TEMPLATE = array();
        $WK_MPSMS_SELLER_PROFILE_TEMPLATE = array();
        $WK_MPSMS_SELLER_PRODUCT_TEMPLATE = array();

        foreach (Language::getLanguages(false) as $language) {
            $WK_MPSMS_ORDER_TEMPLATE[$language['id_lang']] = 'Hello {customerFirstName}, your order is successful created with reference no {referenceNo} and total amount {orderAmount}.';

            $WK_MPSMS_STATUS_TEMPLATE[$language['id_lang']] = 'Hello {customerFirstName}, your updated order status is {orderStatus} of order reference {referenceNo}.';

            $WK_MPSMS_TRACKING_TEMPLATE[$language['id_lang']] = 'Hello {customerFirstName}, your tracking number is {trackingNumber} of order reference {referenceNo}.';
            
            $WK_MPSMS_SELLER_ORDER_TEMPLATE[$language['id_lang']] = 'Hello {sellerFirstName}, your {totalNumberOfProducts} products has been sold.';

            $WK_MPSMS_SELLER_PROFILE_TEMPLATE[$language['id_lang']] = 'Hello {sellerFirstName}, your marketplace seller profile has been {status}.';

            $WK_MPSMS_SELLER_PRODUCT_TEMPLATE[$language['id_lang']] = 'Hello {sellerFirstName}, your {productName} product has been {status}.';
        }

        Configuration::updateValue('WK_MPSMS_ORDER_SMS', 1);
        Configuration::updateValue('WK_MPSMS_API', 'twilio');
        Configuration::updateValue('WK_MPSMS_STATUS_SMS', 1);
        Configuration::updateValue('WK_MPSMS_TRACKING_SMS', 1);
        Configuration::updateValue('WK_MPSMS_TWILIO_AC_ID', '');
        Configuration::updateValue('WK_MPSMS_TWILIO_NUMBER', '');
        Configuration::updateValue('WK_MPSMS_SELLER_ORDER_SMS', 1);
        Configuration::updateValue('WK_MPSMS_TWILIO_PASSWORD', '');
        Configuration::updateValue('WK_MPSMS_SELLER_PRODUCT_SMS', 1);
        Configuration::updateValue('WK_MPSMS_SELLER_PROFILE_SMS', 1);
        Configuration::updateValue('WK_MPSMS_ADDRESS_TYPE', 'delivery');
        Configuration::updateValue('WK_MPSMS_ORDER_STATUS', Tools::jsonEncode(array(2, 4, 5, 6, 7, 8)));
        Configuration::updateValue('WK_MPSMS_ORDER_TEMPLATE', Tools::jsonEncode($WK_MPSMS_ORDER_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_STATUS_TEMPLATE', Tools::jsonEncode($WK_MPSMS_STATUS_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_TRACKING_TEMPLATE', Tools::jsonEncode($WK_MPSMS_TRACKING_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_SELLER_ORDER_TEMPLATE', Tools::jsonEncode($WK_MPSMS_SELLER_ORDER_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_SELLER_PROFILE_TEMPLATE', Tools::jsonEncode($WK_MPSMS_SELLER_PROFILE_TEMPLATE));
        Configuration::updateValue('WK_MPSMS_SELLER_PRODUCT_TEMPLATE', Tools::jsonEncode($WK_MPSMS_SELLER_PRODUCT_TEMPLATE));

        return true;
    }

    public function install()
    {
        if (function_exists('curl_init') == false || !Module::isInstalled('marketplace')) {
            $this->_errors[] = $this->l('To be able to use this module, please activate cURL (PHP extension) and install Marketplace module.');

            return false;
        } else {
            if (!parent::install()
                || !$this->registerPsAndMpHook()
                || !$this->insertConfigData()) {
                return false;
            }
        }

        return true;
    }

    public function deleteConfig()
    {
        $configKeys = array(
            'WK_MPSMS_CLICKSEND_USER_NAME',
            'WK_MPSMS_CLICKSEND_API_KEY',
            'WK_MPSMS_CLICKSEND_NUMBER',
            'WK_MPSMS_PLIVO_AUTH_ID',
            'WK_MPSMS_PLIVO_NUMBER',
            'WK_MPSMS_PLIVO_TOKEN',
            'WK_MPSMS_API',
            'WK_MPSMS_ORDER_SMS',
            'WK_MPSMS_STATUS_SMS',
            'WK_MPSMS_ADDRESS_TYPE',
            'WK_MPSMS_TRACKING_SMS',
            'WK_MPSMS_TWILIO_AC_ID',
            'WK_MPSMS_ORDER_STATUS',
            'WK_MPSMS_TWILIO_NUMBER',
            'WK_MPSMS_ORDER_TEMPLATE',
            'WK_MPSMS_TWILIO_PASSWORD',
            'WK_MPSMS_STATUS_TEMPLATE',
            'WK_MPSMS_TRACKING_TEMPLATE',
            'WK_MPSMS_SELLER_ORDER_SMS',
            'WK_MPSMS_SELLER_PRODUCT_SMS',
            'WK_MPSMS_SELLER_PROFILE_SMS',
            'WK_MPSMS_SELLER_ORDER_TEMPLATE',
            'WK_MPSMS_SELLER_PROFILE_TEMPLATE',
            'WK_MPSMS_SELLER_PRODUCT_TEMPLATE',
        );

        foreach ($configKeys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->deleteConfig()
            ) {
            return false;
        }

        return true;
    }
}
