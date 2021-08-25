<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__).'/classes/define.php';
require_once(_PS_MODULE_DIR_.'mpbooking/classes/WkMpBookingProductInformation.php');
use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\Order\OrderPresenter;

class MpCartOrderSplit extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    private $_html = '';
    private $_postErrors = array();
    public function __construct()
    {
        $this->name = 'mpcartordersplit';
        $this->tab = 'front_office_features';
        $this->version = '5.1.0';
        $this->author = 'Webkul';
        $this->dependencies = array('marketplace');
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '1.7');

        parent::__construct();

        $this->displayName = $this->l('Marketplace Cart And Order Split');
        $this->description = $this->l('This module split cart and order according to seller');
        $this->confirmUninstall = $this->l('Are you sure? All module data will be lost after uninstalling the module');
    }

    public function hookActionFrontControllerSetMedia()
    {
        if ((Tools::getValue('controller') == 'cart' && Configuration::get('MP_ENABLE_CART_SPLIT'))
        || (Tools::getValue('controller') == 'order' && Configuration::get('MP_ENABLE_SELLER_WISE_PRODUCT'))) {
            $this->context->controller->registerStylesheet(
                'front-cart-order-split-css',
                'modules/'.$this->name.'/views/css/frontcartordersplit.css'
            );
        }

        if (Configuration::get('MP_ENABLE_CART_SPLIT') && Tools::getValue('controller') == 'order') {
            $this->context->controller->registerJavascript(
                'front-cart-order-split-js',
                'modules/'.$this->name.'/views/js/frontcartordersplit.js'
            );

            // Order Final Summary Enable Case
            if (Configuration::get('PS_FINAL_SUMMARY_ENABLED')) {
                $idLang = $this->context->language->id;
                $carriers = array();
                $delivery_option_list = $this->context->cart->getDeliveryOptionList();
                $deliveryOption = $this->context->cart->getDeliveryOption();
                foreach ($deliveryOption as $idAddressDelivery => $selectedCarriers) {
                    foreach ($delivery_option_list[$idAddressDelivery][$selectedCarriers]['carrier_list'] as $idCarrier => $carrierDetail) {
                        $carrier = new Carrier((int) $idCarrier, $idLang);
                        $carriers[$idCarrier] = array(
                            'id_carrier' => $idCarrier,
                            'logo' => $carrierDetail['logo'],
                            'name' => $carrier->name,
                            'delay' => $carrier->delay,
                            'price' => Tools::displayPrice($carrierDetail['price_with_tax']),
                        );
                    }
                }

                Media::addJsDef(array(
                    'carriers' => json_encode($carriers),
                ));
            }
        }
    }
	
    public function hookDisplayOverrideTemplate($params)
    {

		if ($params['template_file'] == 'checkout/order-confirmation') {
            $order = new Order((int)Tools::getValue('id_order'), $this->context->language->id);
            $orderRefrence = $order->reference;

            $orderPresenter = new OrderPresenter();
            $customerOrders = array();
            $orders = Order::getByReference($orderRefrence)->getResults();
            if ($orders) {
                foreach ($orders as $orderDetail) {
                    $order = new Order((int)$orderDetail->id, $this->context->language->id);
                    $presentedOrder = $orderPresenter->present($order);

                    $this->context->smarty->assign(array(
                        'products' => $presentedOrder['products'],
                        'subtotals' => $presentedOrder['subtotals'],
                        'totals' => $presentedOrder['totals'],
                        'labels' => $presentedOrder['labels'],
                        'add_product_link' => false,
                    ));
                    $presentedOrder['order_confirmation_table_html'] = $this->fetch('checkout/_partials/order-confirmation-table.tpl');

                    $customerOrders[] = $presentedOrder;
                }
            }

            $this->context->smarty->assign('customerOrders', $customerOrders);
			return 'module:'.$this->name.'/views/templates/hook/customer_order_confirmation.tpl';
			
			
        }
		if ($params['template_file'] == 'checkout/_partials/cart-detailed' || $params['template_file'] == 'checkout/cart') {
            if (Configuration::get('MP_ENABLE_SELLER_WISE_PRODUCT')) {
                $presenter = new CartPresenter();
                $presented_cart = $presenter->present($this->context->cart, $shouldSeparateGifts = true);
                if ((int)$presented_cart['products_count']) {
                    $sellerWiseProducts = array();
                    $objCarrierProductMap = new CarrierProductMap();
                    foreach ($presented_cart['products'] as $product) {
                        $sellerDetail = $objCarrierProductMap->getSellerDetailByIdProd(
                            $product['id_product'],
                            $this->context->language->id
                        );
                        if ($sellerDetail) {
                            if (!isset($sellerWiseProducts[$sellerDetail['id_seller']])) {
                                $sellerWiseProducts[$sellerDetail['id_seller']] = array(
                                    'seller' => array(
                                        'id_seller' => $sellerDetail['id_seller'],
                                        'shop_name' => $sellerDetail['shop_name'],
                                        'shop_link' => $this->context->link->getModuleLink(
                                            'marketplace',
                                            'shopstore',
                                            array('mp_shop_name' => $sellerDetail['shop_link_rewrite'])
                                        ),
                                    ),
                                    'products' => array()
                                );
                            }
                            $sellerWiseProducts[$sellerDetail['id_seller']]['products'][] = $product;
                        } else {
                            $idSeller = 0;
                            if (!isset($sellerWiseProducts[$idSeller])) {
                                $sellerWiseProducts[$idSeller] = array(
                                    'seller' => array(
                                        'id_seller' => $idSeller,
                                        'shop_name' => Configuration::get('PS_SHOP_NAME'),
                                        'shop_link' => $this->context->shop->getBaseURL(true, true)
                                    ),
                                    'products' => array()
                                );
                            }
                            $sellerWiseProducts[$idSeller]['products'][] = $product;
                        }
                    }

                    $extendFilePath = 'checkout/cart.tpl';
                    if ($params['template_file'] == 'checkout/_partials/cart-detailed') {
                        $extendFilePath = 'checkout/_partials/cart-detailed.tpl';
                    }

                    $this->context->smarty->assign(array(
                        'sellerWiseProducts'=> $sellerWiseProducts,
                        'extendFilePath'=> $extendFilePath
                    ));

					return $this->fetch('module:'.$this->name.'/views/templates/hook/seller_wise_cart.tpl');
                }
            }
        }
		return false;
    }
	

    public function hookDisplayOrderCarrierList($data)
    {
        if (Configuration::get('MP_ENABLE_CART_SPLIT')) {
            $id_address = $data['id_address'];
            $PS_SHOP_NAME = Configuration::get('PS_SHOP_NAME');

            $obj_cp_map = new CarrierProductMap();
            $selected_delivery_option = $obj_cp_map->getCarrierDetailByIdCart($this->context->cart->id);

            $delivery_option_list = $this->context->cart->getDeliveryOptionList();
            $deliveryOption = $this->context->cart->getDeliveryOption();

            // Create Seller Wise Delivery Option List
            $delivery_option_list = $obj_cp_map->displayDataOfCartSplit($delivery_option_list);
			
			$lastProductAdded = $this->context->cart->getLastProduct();
			$idProduct = $lastProductAdded['id_product'];
			$objBookingProductInfo = new WkMpBookingProductInformation();
			$bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct);
			
	
            $this->context->smarty->assign(array(
                'selected_delivery_option' => $selected_delivery_option,
				'bookingProductInfo' => $bookingProductInfo,
                'PS_SHOP_NAME' => $PS_SHOP_NAME,
                'use_taxes' => (int)Configuration::get('PS_TAX'),
                'priceDisplay' => Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer),
                'display_tax_label' => $this->context->country->display_tax_label,
                'id_address' => $id_address,
                'delivery_option' => $deliveryOption,
                'option_list' => $delivery_option_list[$id_address],
            ));
            return $this->fetch('module:'.$this->name.'/views/templates/hook/order_carrier.tpl');
        }
    }

    public function hookActionCartUpdateQuantityBefore()
    {
        $obj_cp_map = new CarrierProductMap();
        $obj_cp_map->deleteDataBycartId(Context::getContext()->cart->id);
    }

    public function hookActionObjectProductInCartDeleteAfter()
    {
        $obj_cp_map = new CarrierProductMap();
        $obj_cp_map->deleteDataBycartId(Context::getContext()->cart->id);
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        }

        $sql = str_replace(array('PREFIX_',  'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        if (!parent::install()
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('actionCartUpdateQuantityBefore')
            || !$this->registerHook('actionObjectProductInCartDeleteAfter')
            || !$this->registerHook('displayOrderCarrierList')
            || !$this->registerHook('displayOverrideTemplate')
            ) {
            return false;
        }

        // Set default config variable
        Configuration::updateValue('MP_ENABLE_CART_SPLIT', 1);
        Configuration::updateValue('MP_ENABLE_SELLER_WISE_PRODUCT', 1);

        return true;
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()
            || ($keep && !$this->deleteTables())
            || ($keep && !$this->deleteConfigKeys())) {
            return false;
        }

        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'mp_carrierproduct_map`');
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('MP_ENABLE_CART_SPLIT', Tools::getValue('MP_ENABLE_CART_SPLIT'));
            Configuration::updateValue('MP_ENABLE_SELLER_WISE_PRODUCT', Tools::getValue('MP_ENABLE_SELLER_WISE_PRODUCT'));
        }

        $module_config = $this->context->link->getAdminLink('AdminModules');
        Tools::redirectAdmin($module_config.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4');
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postProcess();
        } else {
            $this->_html .= '<br />';
        }

        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderForm()
    {
        //Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Configuration'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display Seller Wise Shipping'),
                    'name' => 'MP_ENABLE_CART_SPLIT',
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'desc' => $this->l('If yes, Seller wise shippings will be displayed in checkout page'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display Seller Wise Products'),
                    'name' => 'MP_ENABLE_SELLER_WISE_PRODUCT',
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'desc' => $this->l('If yes, Seller wise products will be displayed in cart page'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->submit_action = 'btnSubmit';
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        //Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        //$this->fields_form = array();
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues()
    {
        $config_vars = array(
            'MP_ENABLE_CART_SPLIT' => Tools::getValue(
                'MP_ENABLE_CART_SPLIT',
                Configuration::get('MP_ENABLE_CART_SPLIT')
            ),
            'MP_ENABLE_SELLER_WISE_PRODUCT' => Tools::getValue(
                'MP_ENABLE_SELLER_WISE_PRODUCT',
                Configuration::get('MP_ENABLE_SELLER_WISE_PRODUCT')
            ),
        );

        return $config_vars;
    }

    public function deleteConfigKeys()
    {
        $var = array(
            'MP_ENABLE_CART_SPLIT',
            'MP_ENABLE_SELLER_WISE_PRODUCT',
        );
        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }
}
