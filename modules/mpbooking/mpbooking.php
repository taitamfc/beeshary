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

if (!defined('_PS_VERSION_')) {
    exit;
}
use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\Order\OrderPresenter;

if (Module::isEnabled('marketplace')) {
    include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
}
require_once dirname(__FILE__).'/../mpbooking/classes/WkMpBookingRequiredClasses.php';

class MpBooking extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct()
    {
        $this->name = 'mpbooking';
        $this->tab = 'front_office_features';
        $this->version = '4.0.1';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->dependencies = array('marketplace');

        parent::__construct();

        $this->displayName = $this->l('Prestashop Marketplace Booking And Reservation System');
        $this->description = $this->l('Online Marketplace Booking And Reservation System');
    }

    public function getContent()
    {
        $this->html = '';
        if (Tools::isSubmit('btnSubmit')) {
            if (Tools::isSubmit('btnSubmit')) {
                $WK_MP_CONSIDER_DATE_TO = Configuration::get('WK_MP_CONSIDER_DATE_TO');
                if (isset($WK_MP_CONSIDER_DATE_TO)
                    && (Tools::getValue('WK_MP_CONSIDER_DATE_TO') != $WK_MP_CONSIDER_DATE_TO)
                ) {
                    $objBookingCart = new WkMpBookingCart();
                    if (!$objBookingCart->deleteCurrentCustomerCarts()) {
                        $this->context->controller->errors[] = $this->l(
                            'Some issue has been occurred while deleting current customer catrs.'
                        );
                    }
                }
            }
            if (!count($this->context->controller->errors)) {
                Configuration::updateValue('WK_MP_CONSIDER_DATE_TO', Tools::getValue('WK_MP_CONSIDER_DATE_TO'));
                Configuration::updateValue(
                    'WK_MP_FEATURE_PRICE_RULES_SHOW',
                    Tools::getValue('WK_MP_FEATURE_PRICE_RULES_SHOW')
                );

                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.
                    $this->tab.'&module_name='.$this->name.'&conf=4'
                );
            }
        } elseif (Tools::isSubmit('submitAddFeaturePricePriority')) {
            $priority = Tools::getValue('featurePricePriority');
            $uniquePriorities = array_unique($priority);
            if (count($priority) == count($uniquePriorities)) {
                $priorityConfig = implode(';', $priority);

                if (Configuration::updateValue('WK_MP_PRODUCT_FEATURE_PRICING_PRIORITY', $priorityConfig)) {
                    Tools::redirectAdmin(
                        $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.
                        $this->tab.'&module_name='.$this->name.'&conf=4'
                    );
                } else {
                    $this->context->controller->errors[] = $this->l(
                        'Some error occurred while updating booking price rules priorities.'
                    );
                }
            } else {
                $this->context->controller->errors[] = $this->l(
                    'Duplicate values selected for booking price rules priorities.'
                );
            }
        } else {
            $this->html .= '<br />';
        }
        $this->html .= $this->renderForm();
        return $this->html;
    }

    public function renderForm()
    {
        //Get default language
        $html = '';
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
                    'label' => $this->l('Consider Price For \'Date To\''),
                    'name' => 'WK_MP_CONSIDER_DATE_TO',
                    'required' => false,
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
                    'hint' => $this->l('If yes, Last date price will be added for the booking product. Otherwise
                    booking will not be considered for the last date. For example- Hotel room booking in which last
                    date is considered as checkout date and last date is not considered in price calculations.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show booking price rules to customers'),
                    'name' => 'WK_MP_FEATURE_PRICE_RULES_SHOW',
                    'required' => false,
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
                    'hint' => $this->l('If disabled, created booking price rules will not be shown to the customers.'),
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
        //$this->fields_form = array();
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $html = $helper->generateForm($fields_form);
        // Add booking price rules priority settings configuration
        $priorities = Configuration::get('WK_MP_PRODUCT_FEATURE_PRICING_PRIORITY');
        $this->context->smarty->assign('featurePricePriority', explode(';', $priorities));
        $html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/bookingPriceRulesPriority.tpl');
        return $html;
    }

    public function getConfigFieldsValues()
    {
        $config_vars = array(
            'WK_MP_CONSIDER_DATE_TO' => Tools::getValue('WK_MP_CONSIDER_DATE_TO', Configuration::get('WK_MP_CONSIDER_DATE_TO')),
            'WK_MP_FEATURE_PRICE_RULES_SHOW' => Tools::getValue(
                'WK_MP_FEATURE_PRICE_RULES_SHOW',
                Configuration::get('WK_MP_FEATURE_PRICE_RULES_SHOW')
            ),
        );
        return $config_vars;
    }

    protected function createTables()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        }
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }
        return true;
    }

    public function hookDisplayHeader()
    {
        if (isset($this->context->cart->id) && $this->context->cart->id) {
            $objBookingCart = new WkMpBookingCart();
            $cartBookingData = $objBookingCart->getCartInfo($this->context->cart->id);

            if ($cartBookingData) {
                foreach ($cartBookingData as $booking) {
                    /*To remove room from cart before today's date*/
                    $objBookingCart = new WkMpBookingCart($booking['id_booking_cart']);
                    $idProduct = $booking['id_product'];
                    $bookingType = $objBookingCart->booking_type;
                    if (strtotime($booking['date_from']) < strtotime(date('Y-m-d'))) {
                        if ($bookingType == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                            $daysCount = (int) WkMpBookingHelper::getNumberOfDays(
                                $objBookingCart->date_from,
                                $objBookingCart->date_to
                            );
                        } else {
                            $daysCount = 1;
                        }
                        $quantityToReduce = ($daysCount * (int)$objBookingCart->quantity);
                        if ($this->context->cart->updateQty(
                            (int)$quantityToReduce,
                            (int)$idProduct,
                            null,
                            false,
                            'down',
                            0,
                            null,
                            true
                        )) {
                            if (!$objBookingCart->delete()) {
                                $this->context->controller->errors[] = $this->l(
                                    'Error while deleting booking from cart booking table.'
                                );
                            }
                        } else {
                            $this->context->controller->errors[] = $this->l('Error while updating cart quantity.');
                        }
                    }
                    /*To remove bookings from cart if product is deleted*/
                    $product = new Product($idProduct);
                    if (!Validate::isLoadedObject($product)) {
                        if (!$objBookingCart->deleteBookingProductCartByIdProductIdCart(
                            $idProduct,
                            $this->context->cart->id
                        )) {
                            $this->context->controller->errors[] = $this->l(
                                'Error while deleting bookings of deleted product from cart booking table.'
                            );
                        }
                    } else {
                        /*To remove bookings from cart if time slots are deleted*/
                        if ($bookingType == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                            $objBookingProductInfo = new WkMpBookingProductInformation();
                            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                                $objTimeSlotPrices = new WkMpBookingProductTimeSlotPrices();
                                if (!$objTimeSlotPrices->getProductTimeSlotDetails(
                                    $bookingProductInfo['id_booking_product_info'],
                                    $objBookingCart->date_from,
                                    $objBookingCart->time_from,
                                    $objBookingCart->time_to
                                )) {
                                    if ($this->context->cart->updateQty(
                                        (int) $objBookingCart->quantity,
                                        (int)$idProduct,
                                        null,
                                        false,
                                        'down',
                                        0,
                                        null,
                                        true
                                    )) {
                                        if (!$objBookingCart->delete()) {
                                            $this->context->controller->errors[] = $this->l(
                                                'Error while deleting booking from cart booking table.'
                                            );
                                        }
                                    } else {
                                        $this->context->controller->errors[] = $this->l(
                                            'Error while updating cart quantity.'
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        // disable the feature price plans which date range has been expired
        if ('product' == Tools::getValue('controller')) {
            $idPsProduct = Tools::getValue('id_product');
            if ($bookingProductInfo = WkMpBookingProductInformation::getBookingProductInfo(0, $idPsProduct)) {
                $objFeaturePrice = new WkMpBookingProductFeaturePricing();
                if ($productBookingPlans = $objFeaturePrice->getProductFeaturePriceRules(
                    $bookingProductInfo['id_booking_product_info'],
                    false,
                    1
                )) {
                    $currentDateTime = strtotime(date('Y-m-d'));
                    foreach ($productBookingPlans as $plan) {
                        $planDateFrom = strtotime($plan['date_from']);
                        $planDateTo = strtotime($plan['date_to']);
                        if ($plan['date_selection_type'] == 2) {
                            $condition = ($currentDateTime > $planDateFrom);
                        } else {
                            $condition = ($currentDateTime > $planDateFrom && $currentDateTime > $planDateTo);
                        }
                        if ($condition) {
                            $objFeaturePrice = new WkMpBookingProductFeaturePricing($plan['id_feature_price_rule']);
                            $objFeaturePrice->active = 0;
                            $objFeaturePrice->save();
                        }
                    }
                }
            }
        }
        if ('updateproduct' == Tools::getValue('controller')) {
            if ($idMpProduct = Tools::getValue('id_mp_product')) {
                $objBookingProductInfo = new WkMpBookingProductInformation();
                if ($objBookingProductInfo->getBookingProductInfo($idMpProduct, 0)) {
                    Tools::redirect(
                        $this->context->link->getModuleLink(
                            'mpbooking',
                            'mpbookingproduct',
                            array('id_mp_product' => $idMpProduct)
                        )
                    );
                }
            }
        }
    }

    public function hookDisplayOverrideTemplate($params)
    {
        if ('customer/history' == $params['template_file']) {
            $orders = array();
            $customer_orders = Order::getCustomerOrders($this->context->customer->id);
            $order_presenter = new OrderPresenter();
            foreach ($customer_orders as $customer_order) {
                $order = new Order((int) $customer_order['id_order']);
                $orders[$customer_order['id_order']] = $order_presenter->present($order);
            }
            if ($orders) {
                foreach ($orders as &$order) {
                    if ($orderProducts = $order['products']) {
                        $bookingProductInfo = new WkMpBookingProductInformation();
                        foreach ($orderProducts as $product) {
                            if ($bookingProductInfo->getBookingProductInfo(0, $product['id_product'])) {
                                $order['bookingProductExists'] = 1;
                            }
                        }
                    }
                }
                $this->context->smarty->assign('orders', $orders);
                return 'module:mpbooking/views/templates/hook/historyOverrided.tpl';
            }
        }
        if ('checkout/cart' == $params['template_file']) {
            $presenter = new CartPresenter();
            $isBookingProductAvailable = 0;
            $presentedCart = $presenter->present($this->context->cart, true);
            if ($presentedCart) {
                $objBookingProductInfo = new WkMpBookingProductInformation();
                $objBookingCart = new WkMpBookingCart();
                foreach ($presentedCart['products'] as $key => $product) {
                    $idProduct = $product['id_product'];
                    if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                        $isBookingProductAvailable = 1;
                        $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                        if ($bookingProductCartInfo = $objBookingCart->getBookingProductCartInfo(
                            $idProduct,
                            $this->context->cart->id
                        )) {
                            foreach ($bookingProductCartInfo as $keyProduct => $cartBooking) {
                                if ($cartBooking['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                                    $numDays = WkMpBookingHelper::getNumberOfDays(
                                        $cartBooking['date_from'],
                                        $cartBooking['date_to']
                                    );
                                    $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'] * $numDays;
                                    $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                        $idBookingProductInfo,
                                        $cartBooking['date_from'],
                                        $cartBooking['date_to'],
                                        false,
                                        $this->context->currency->id
                                    );
                                    $bookingProductCartInfo[$keyProduct]['totalPriceTE'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_excl']));
                                    $bookingProductCartInfo[$keyProduct]['totalPriceTI'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_incl']));
                                } elseif ($cartBooking['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                                    $bookingTimeSlotPrice = false;
                                    $objTimeSlot = new WkMpBookingProductTimeSlotPrices();
                                    $slotDetails = $objTimeSlot->getProductTimeSlotDetails(
                                        $idBookingProductInfo,
                                        $cartBooking['date_from'],
                                        $cartBooking['time_from'],
                                        $cartBooking['time_to']
                                    );
                                    if ($slotDetails) {
                                        $bookingTimeSlotPrice['price_tax_excl'] = $slotDetails['price'];
                                        $taxRate = (float) WkMpBookingProductInformation::getAppliedProductTaxRate(
                                            $idProduct
                                        );
                                        $bookingTimeSlotPrice['price_tax_incl'] = $bookingTimeSlotPrice['price_tax_excl'] * ((100 + $taxRate) / 100);
                                        $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'];
                                        $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                            $idBookingProductInfo,
                                            $cartBooking['date_from'],
                                            $cartBooking['date_from'],
                                            $bookingTimeSlotPrice,
                                            $this->context->currency->id
                                        );
                                    }
                                }
                                $bookingProductCartInfo[$keyProduct]['totalPriceTE'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_excl']));
                                $bookingProductCartInfo[$keyProduct]['totalPriceTI'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_incl']));
                                $bookingProductCartInfo[$keyProduct]['unit_feature_price_tax_excl_formated'] = Tools::displayPrice((float)$totalPriceBookingProduct['total_price_tax_excl']);
                                $bookingProductCartInfo[$keyProduct]['unit_feature_price_tax_incl_formated'] = Tools::displayPrice((float)$totalPriceBookingProduct['total_price_tax_incl']);
                            }
                            $presentedCart['products'][$key]['isBookingProduct'] = 1;
                            $presentedCart['products'][$key]['booking_product_data'] = $bookingProductCartInfo;
                        }
                    }
                }
            }
            if ($isBookingProductAvailable) {
                $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                $this->context->smarty->assign(
                    array(
                        'priceDisplay' => $priceDisplay,
                        'presentedCart' => $presentedCart,
                        'cart_template_file' => _PS_THEME_DIR_.'templates/checkout/cart.tpl',
                        'booking_type_time_slot' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT,
                        'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
                    )
                );
                return 'module:mpbooking/views/templates/hook/cartCheckoutOverrided.tpl';
            }
        }
		/* disable this because mpcartsplit use it */
        if ('checkout/order-confirmation' == $params['template_file'] && false) {
            $idOrder = $params['controller']->id_order;
            $order = new Order($idOrder);
            $order_presenter = new OrderPresenter();
            $presentedOrder = $order_presenter->present($order);
            if ($presentedOrder) {
                $orderProducts = $presentedOrder['products'];
                $objBookingOrders = new WkMpBookingOrder();
                $objBookingProductInfo = new WkMpBookingProductInformation();
                foreach ($orderProducts as $key => $product) {
                    if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(
                        0,
                        $product['id_product']
                    )) {
                        $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                        if ($bookingProductOrderInfo = $objBookingOrders->getBookingProductOrderInfo(
                            $product['id_product'],
                            $idOrder
                        )) {
                            foreach ($bookingProductOrderInfo as $keyProduct => $cartBooking) {
                                $bookingProductOrderInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'] * (WkMpBookingHelper::getNumberOfDays($cartBooking['date_from'], $cartBooking['date_to']));
                                $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $idBookingProductInfo,
                                    $cartBooking['date_from'],
                                    $cartBooking['date_to']
                                );
                                $bookingProductOrderInfo[$keyProduct]['totalPriceTE'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_excl']));
                                $bookingProductOrderInfo[$keyProduct]['totalPriceTI'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_incl']));
                                $bookingProductOrderInfo[$keyProduct]['product_real_price_tax_excl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['product_real_price_tax_excl']));
                                $bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_excl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_excl']));
                                $bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_incl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_incl']));
                                $bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_excl_formated'] = Tools::displayPrice((float) $cartBooking['range_feature_price_tax_excl']);
                                $bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_incl_formated'] = Tools::displayPrice((float) $cartBooking['range_feature_price_tax_incl']);
                            }
                            $orderProducts[$key]['isBookingProduct'] = 1;
                            $orderProducts[$key]['booking_product_data'] = $bookingProductOrderInfo;
                        }
                    }
                }
                $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                $this->context->smarty->assign(
                    array(
                        'orderProducts' => $orderProducts,
                        'subtotals' => $presentedOrder['subtotals'],
                        'totals' => $presentedOrder['totals'],
                        'labels' => $presentedOrder['labels'],
                        'add_product_link' => false,
                        'order_confirmation_template_file' =>_PS_THEME_DIR_.'templates/checkout/order-confirmation.tpl',
                        'priceDisplay' => $priceDisplay,
                        'booking_type_time_slot' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT,
                        'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
                    )
                );
                return 'module:mpbooking/views/templates/hook/checkoutOrderConfirmationOverrided.tpl';
            }
        }
        if ('customer/order-detail' == $params['template_file']) {
            $idOrder = Tools::getValue('id_order');
            $order = new Order($idOrder);
            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                $orderDetails = (new OrderPresenter())->present($order);
                if ($orderDetails) {
                    $orderProducts = $orderDetails['products'];
                    if ($orderProducts) {
                        $objBookingOrders = new WkMpBookingOrder();
                        $objOrderCurrency = new Currency($order->id_currency);
                        $bookingProductExists = 0;
                        $objBookingProductInfo = new WkMpBookingProductInformation();
                        foreach ($orderProducts as $key => $product) {
                            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(
                                0,
                                $product['id_product']
                            )) {
                                $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                                if ($bookingProductOrderInfo = $objBookingOrders->getBookingProductOrderInfo(
                                    $product['id_product'],
                                    $idOrder
                                )) {
                                    foreach ($bookingProductOrderInfo as $keyProduct => $cartBooking) {
                                        $bookingProductOrderInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'] * (WkMpBookingHelper::getNumberOfDays($cartBooking['date_from'], $cartBooking['date_to']));
                                        $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                            $idBookingProductInfo,
                                            $cartBooking['date_from'],
                                            $cartBooking['date_to'],
                                            false,
                                            $order->id_currency
                                        );
                                        $bookingProductOrderInfo[$keyProduct]['totalPriceTE'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_excl']), $objOrderCurrency);
                                        $bookingProductOrderInfo[$keyProduct]['totalPriceTI'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_incl']), $objOrderCurrency);
                                        $bookingProductOrderInfo[$keyProduct]['product_real_price_tax_excl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['product_real_price_tax_excl']), $objOrderCurrency);
                                        $bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_excl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_excl']), $objOrderCurrency);
                                        $bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_excl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_incl']), $objOrderCurrency);

                                        $bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_excl_formated'] = Tools::displayPrice((float) $cartBooking['range_feature_price_tax_excl'], $objOrderCurrency);
                                        $bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_incl_formated'] = Tools::displayPrice((float) $cartBooking['range_feature_price_tax_incl'], $objOrderCurrency);
                                    }
                                    $orderProducts[$key]['isBookingProduct'] = 1;
                                    $orderProducts[$key]['booking_product_data'] = $bookingProductOrderInfo;
                                    $bookingProductExists = 1;
                                }
                            }
                        }
                        if ($bookingProductExists) {
                            $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                            $this->context->smarty->assign(
                                array(
                                    'bookingProductExists' => $bookingProductExists,
                                    'priceDisplay' => $priceDisplay,
                                    'orderProducts' => $orderProducts,
                                    'order_details_template_file' => _PS_THEME_DIR_.
                                    'templates/customer/order-detail.tpl',
                                    'booking_type_time_slot' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT,
                                    'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
                                )
                            );
                            return 'module:mpbooking/views/templates/hook/frontOrderDetailsOverrided.tpl';
                        }
                    }
                }
            }
        }
        if ('catalog/_partials/quickview' == $params['template_file']) {
            $idProduct = Tools::getValue('id_product');
            $controller = Tools::getValue('controller');
            if ('product' == $controller || 'index' == $controller || 'category' == $controller) {
                if (isset($idProduct) && $idProduct) {
                    $objBookingProductInfo = new WkMpBookingProductInformation();
                    if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                        $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                        // Data to show Disables dates (Disable dates/slots tab)
                        $objBookingDisableDates = new WkMpBookingProductDisabledDates();
                        // get booking product disable dates
                        $disableDatesInfo = $objBookingDisableDates->getBookingProductDisableDatesInfoFormatted(
                            $idBookingProductInfo
                        );
                        $disabledDays = 0;
                        $disabledDates = 0;
                        if ($disableDatesInfo) {
                            if (isset($disableDatesInfo['disabledDays']) && $disableDatesInfo['disabledDays']) {
                                $disabledDays = $disableDatesInfo['disabledDays'];
                            }
                            if (isset($disableDatesInfo['disabledDates']) && $disableDatesInfo['disabledDates']) {
                                $disabledDates = $disableDatesInfo['disabledDates'];
                            }
                        }
                        // Data to show Disables dates (Disable dates/slots tab)
                        $objTimeSlots = new WkMpBookingProductTimeSlotPrices();
                        $selectedDates = $objTimeSlots->getProductTimeSlotsSelectedDates($idBookingProductInfo);
                        $this->context->smarty->assign(
                            array(
                                'disabledDays' => $disabledDays,
                                'disabledDates' => $disabledDates,
                                'selectedDates' => json_encode($selectedDates),
                                'isBookingProduct' => 1,
                                'moduleDir' => _PS_MODULE_DIR_.'mpbooking',
                            )
                        );
                        return 'module:mpbooking/views/templates/hook/productQuickReviewOverrided.tpl';
                    }
                }
            }
        }
        if ('checkout/checkout' == $params['template_file']) {
            $presenter = new CartPresenter();
            $isBookingProductAvailable = 0;
            $presentedCart = $presenter->present($this->context->cart, true);
            if ($presentedCart) {
                $objBookingCart = new WkMpBookingCart();
                $objBookingProductInfo = new WkMpBookingProductInformation();
                foreach ($presentedCart['products'] as $key => $product) {
                    $idProduct = $product['id_product'];
                    if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                        $isBookingProductAvailable = 1;
                        if ($bookingProductCartInfo = $objBookingCart->getBookingProductCartInfo(
                            $idProduct,
                            $this->context->cart->id
                        )) {
                            $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                            $totalPriceTE = 0;
                            foreach ($bookingProductCartInfo as $keyProduct => $cartBooking) {
                                if ($cartBooking['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                                    $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'] * (WkMpBookingHelper::getNumberOfDays($cartBooking['date_from'], $cartBooking['date_to']));
                                    $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                        $idBookingProductInfo,
                                        $cartBooking['date_from'],
                                        $cartBooking['date_to'],
                                        false,
                                        $this->context->currency->id
                                    );
                                    $totalPriceTE += ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_excl']);
                                } elseif ($cartBooking['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                                    $bookingTimeSlotPrice = false;
                                    $objTimeSlot = new WkMpBookingProductTimeSlotPrices();
                                    $slotDetails = $objTimeSlot->getProductTimeSlotDetails(
                                        $idBookingProductInfo,
                                        $cartBooking['date_from'],
                                        $cartBooking['time_from'],
                                        $cartBooking['time_to']
                                    );
                                    if ($slotDetails) {
                                        $bookingTimeSlotPrice['price_tax_excl'] = $slotDetails['price'];
                                        $taxRate = (float) WkMpBookingProductInformation::getAppliedProductTaxRate(
                                            $idProduct
                                        );
                                        $bookingTimeSlotPrice['price_tax_incl'] = $bookingTimeSlotPrice['price_tax_excl'] * ((100 + $taxRate) / 100);
                                        $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'];
                                        $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                            $idBookingProductInfo,
                                            $cartBooking['date_from'],
                                            $cartBooking['date_from'],
                                            $bookingTimeSlotPrice,
                                            $this->context->currency->id
                                        );
                                        $totalPriceTE += ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_excl']);
                                    }
                                }
                            }
                            $presentedCart['products'][$key]['isBookingProduct'] = 1;
                            $presentedCart['products'][$key]['total_price_tax_excl'] = $totalPriceTE;
                            $presentedCart['products'][$key]['total_price_tax_excl_formatted'] = Tools::displayPrice(
                                $totalPriceTE
                            );
                        }
                    }
                }
            }
            if ($isBookingProductAvailable) {
                $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                $this->context->smarty->assign(
                    array(
                        'booking_type_time_slot' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT,
                        'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
                        'priceDisplay' => $priceDisplay,
                        'cart' => $presentedCart,
                        'checkout_template_file' => _PS_THEME_DIR_.'templates/checkout/checkout.tpl',
                    )
                );
                return 'module:mpbooking/views/templates/hook/checkoutCheckoutOverrided.tpl';
            }
        }
    }

    // DisplayProductButtons is changed in hookDisplayProductAdditionalInfo in new versions.
    public function hookDisplayProductAdditionalInfo()
    {
        
        return $this->hookDisplayProductButtons();
    }

    public function hookDisplayProductButtons()
    {
        $objBookingProductInfo = new WkMpBookingProductInformation();
        $idProduct = Tools::getValue('id_product');
		$this->context->smarty->assign('idProduct', $idProduct);
        if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
            $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
            $dateFrom = date('Y-m-d');
            if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                $dateTo = date('Y-m-d', strtotime($dateFrom));
            } else {
                $dateTo = date('Y-m-d', strtotime("+1 day", strtotime($dateFrom)));
            }
            $this->context->smarty->assign(
                array(
                    'date_from' => date('d-m-Y', strtotime($dateFrom)),
                    'date_to' => date('d-m-Y', strtotime($dateTo)),
                )
            );
            $objBookingOrders = new WkMpBookingOrder();
            $bookingTimeSlotPriceToday = false;
            $bookingTimeSlotPrice = false;
            $objBookingDisableDates = new WkMpBookingProductDisabledDates();
            if ($bookingProductInfo['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                $objTimeSlots = new WkMpBookingProductTimeSlotPrices();
                $bookingTimeSlots = $objTimeSlots->getBookingProductTimeSlotsOnDate(
                    $idBookingProductInfo,
                    $dateFrom,
                    true,
                    1
                );
                if ($bookingTimeSlots) {
                    $flag = 0;
                    $totalSlotsQty = 0;
                    foreach ($bookingTimeSlots as $key => $timeSlot) {
                        $bookedSlotQuantity = $objBookingOrders->getProductTimeSlotOrderedQuantity(
                            $idProduct,
                            $dateFrom,
                            $timeSlot['time_slot_from'],
                            $timeSlot['time_slot_to'],
                            1
                        );
                        $availQty = $bookingProductInfo['quantity'] - $bookedSlotQuantity;
                        $bookingTimeSlots[$key]['available_qty'] = ($availQty < 0) ? 0 : $availQty;
                        $bookingTimeSlots[$key]['price_tax_excl'] = $timeSlot['price'];
                        $totalSlotsQty += $bookingProductInfo['quantity'] - $bookedSlotQuantity;
                        $taxRate = (float) WkMpBookingProductInformation::getAppliedProductTaxRate($idProduct);
                        $bookingTimeSlots[$key]['price_tax_incl'] = $timeSlot['price'] * ((100 + $taxRate) / 100);
                        $bookingTimeSlotPrice['price_tax_excl'] = $bookingTimeSlots[$key]['price_tax_excl'];
                        $bookingTimeSlotPrice['price_tax_incl'] = $bookingTimeSlots[$key]['price_tax_incl'];

                        if ($flag == 0 && $bookingTimeSlots[$key]['available_qty']) {
                            $bookingTimeSlots[$key]['checked'] = 1;
                            $bookingTimeSlotPriceToday['price_tax_excl'] = $bookingTimeSlots[$key]['price_tax_excl'];
                            $bookingTimeSlotPriceToday['price_tax_incl'] = $bookingTimeSlots[$key]['price_tax_incl'];
                            $flag = 1;
                        } else {
                            $bookingTimeSlots[$key]['checked'] = 0;
                        }
                        $totalFeaturePrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                            $idBookingProductInfo,
                            $dateFrom,
                            $dateFrom,
                            $bookingTimeSlotPrice,
                            $this->context->currency->id
                        );
                        if ($totalFeaturePrice) {
                            $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                            if (!$priceDisplay || $priceDisplay == 2) {
                                $bookingTimeSlots[$key]['formated_slot_price'] = Tools::displayPrice(
                                    $totalFeaturePrice['total_price_tax_incl']
                                );
                            } elseif ($priceDisplay == 1) {
                                $bookingTimeSlots[$key]['formated_slot_price'] = Tools::displayPrice(
                                    $totalFeaturePrice['total_price_tax_excl']
                                );
                            }
                        }
                    }
                    if ($flag == 0 && !$bookingTimeSlotPriceToday) {
                        $bookingTimeSlotPriceToday['price_tax_excl'] = 0;
                        $bookingTimeSlotPriceToday['price_tax_incl'] = 0;
                    }
                    $this->context->smarty->assign('totalSlotsQty', $totalSlotsQty);
                    $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                        $idBookingProductInfo,
                        $dateFrom,
                        $dateFrom,
                        $bookingTimeSlotPriceToday,
                        $this->context->currency->id
                    );
                    if ($totalPrice) {
                        $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                        if (!$priceDisplay || $priceDisplay == 2) {
                            $productFeaturePrice = $totalPrice['total_price_tax_incl'];
                        } elseif ($priceDisplay == 1) {
                            $productFeaturePrice = $totalPrice['total_price_tax_excl'];
                        }
                    }
                } else {
                    $productFeaturePrice = 0;
                }
                // get disable dates info for current selected dates
                $selectedDatesDisableInfo = $objBookingDisableDates->getBookingProductDisableDatesInDateRange(
                    $idBookingProductInfo,
                    $dateFrom,
                    $dateFrom
                );
                $this->context->smarty->assign('bookingTimeSlots', $bookingTimeSlots);
                
            } else {
                $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                    $idBookingProductInfo,
                    $dateFrom,
                    $dateTo,
                    $bookingTimeSlotPriceToday,
                    $this->context->currency->id
                );
                if ($totalPrice) {
                    $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                    if (!$priceDisplay || $priceDisplay == 2) {
                        $productFeaturePrice = $totalPrice['total_price_tax_incl'];
                    } elseif ($priceDisplay == 1) {
                        $productFeaturePrice = $totalPrice['total_price_tax_excl'];
                    }
                }
                // get disable dates info for current selected dates
                $selectedDatesDisableInfo = $objBookingDisableDates->getBookingProductDisableDatesInDateRange(
                    $idBookingProductInfo,
                    $dateFrom,
                    $dateTo
                );
            }
            $bookedQuantity = $objBookingOrders->getProductOrderedQuantityInDateRange($idProduct, $dateFrom, $dateTo, 1);
            $maxAvailableQuantity = $bookingProductInfo['quantity'] - $bookedQuantity;

            $objFeaturePrice = new WkMpBookingProductFeaturePricing();
            if ($bookingPricePlans = $objFeaturePrice->getProductFeaturePriceRules(
                $bookingProductInfo['id_booking_product_info'],
                false,
                1
            )) {
                foreach ($bookingPricePlans as &$plan) {
                    $plan['impact_value_formated'] = Tools::displayPrice(Tools::convertPrice($plan['impact_value']));
                }
            }
            //Get featurePrice priority
            $featurePricePriority = Configuration::get('WK_MP_PRODUCT_FEATURE_PRICING_PRIORITY');
            $featurePricePriority = explode(';', $featurePricePriority);
            foreach ($featurePricePriority as $key => $priority) {
                if ($priority == 'date_range') {
                    $featurePricePriority[$key] = $this->l('For Date Range');
                } elseif ($priority == 'specific_date') {
                    $featurePricePriority[$key] = $this->l('For Specific Date');
                } elseif ($priority == 'special_day') {
                    $featurePricePriority[$key] = $this->l('For Special Days');
                }
            }
            $this->context->smarty->assign(
                array(
                    'booking_type_time_slot' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT,
                    'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
                    'selectedDatesDisabled' => $selectedDatesDisableInfo ? 1 : 0,
                    'featurePricePriority' => $featurePricePriority,
                    'maxAvailableQuantity' => $maxAvailableQuantity,
                    'bookingPricePlans' => $bookingPricePlans,
                    'bookingProductInformation' => $bookingProductInfo,
                    'productFeaturePrice' => Tools::displayPrice($productFeaturePrice),
                    'module_dir' => _MODULE_DIR_.$this->name,
                    'show_feature_price_rules' => Configuration::get('WK_MP_FEATURE_PRICE_RULES_SHOW')
                )
            );
            return $this->fetch('module:mpbooking/views/templates/hook/customerBookingInterface.tpl');
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        // echo "hookDisplayProductAdditionalInfo";die;
        $idPsProduct = Tools::getValue('id_product');
        $controller = Tools::getValue('controller');
        if ('product' == $controller
            || 'index' == $controller
            || 'category' == $controller
            || 'cart' == $controller
            || 'order' == $controller
        ) {
            $jsDef = array();
            if ($idPsProduct) {
                $objBookingProductInfo = new WkMpBookingProductInformation();
                if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idPsProduct)) {
                    // Data to show Disables dates (Disable dates/slots tab)
                    $objBookingDisableDates = new WkMpBookingProductDisabledDates();
                    // get booking product disable dates
                    $bookingDisableDatesInfo = $objBookingDisableDates->getBookingProductDisableDatesInfoFormatted(
                        $bookingProductInfo['id_booking_product_info']
                    );
                    if ($bookingDisableDatesInfo) {
                        if (isset($bookingDisableDatesInfo['disabledDays'])) {
                            $jsDef['disabledDays'] = $bookingDisableDatesInfo['disabledDays'];
                        }
                        if (isset($bookingDisableDatesInfo['disabledDates'])) {
                            $jsDef['disabledDates'] = $bookingDisableDatesInfo['disabledDates'];
                        }
                    }
                    // Data to show Disables dates (Disable dates/slots tab)
                    $objTimeSlots = new WkMpBookingProductTimeSlotPrices();
                    $selectedDates = $objTimeSlots->getProductTimeSlotsSelectedDates(
                        $bookingProductInfo['id_booking_product_info']
                    );
                    Media::addJsDefL('selectedDatesJson', json_encode($selectedDates));
                }
            }
            Media::addJsDefL('disable_date_title', $this->l('Bookings are unavailable on this date'));
            Media::addJsDefL(
                'bookings_in_select_range_label',
                $this->l('Following bookings will be created for selected date range')
            );
            Media::addJsDefL('no_slots_available_text', $this->l('No slots available'));
            Media::addJsDefL('total_price_text', $this->l('Total Price'));
            Media::addJsDefL('dateText', $this->l('Date Selected'));
            Media::addJsDefL('dateRangeText', $this->l('Date Range'));
            Media::addJsDefL('priceText', $this->l('Price'));
            Media::addJsDefL('To_txt', $this->l('To'));
            Media::addJsDefL('qtyText', $this->l('quantity'));
            Media::addJsDefL('invalidQtyErr', $this->l('Invalid Quantity.'));
            Media::addJsDefL('slot_booked_text', $this->l('Slot Booked!'));

            $jsDef['wkBookingCartLink'] = $this->context->link->getModuleLink('mpbooking', 'bookingproductcartactions');
            $jsDef['considerDateToConfiguration'] = Configuration::get('WK_MP_CONSIDER_DATE_TO');
            $jsDef['booking_type_date_range'] = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE;
            $jsDef['booking_type_time_slot'] = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT;
            Media::addJsDef($jsDef);
            $this->context->controller->registerJavascript(
                'booking-BookingInterface-js',
                'modules/'.$this->name.'/views/js/front/wk-customer-booking-Interface.js'
            );
            $this->context->controller->registerStylesheet(
                'booking-global-css',
                'modules/'.$this->name.'/views/css/wk-booking-global-style.css'
            );

            $this->context->controller->addJqueryUI(array('ui.slider', 'ui.datepicker'));
        }
    }

    public function hookActionValidateOrder($data)
    {
        $cart = $data['cart'];
        $order = $data['order'];
        $idOrder = $order->id;
        $cartProducts = $cart->getProducts();
        $objBookingProductInfo = new WkMpBookingProductInformation();
        $objBookingCart = new WkMpBookingCart();
        $paidProductPrices = array();
        foreach ($cartProducts as $product) {
            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $product['id_product'])) {
                if ($bookingProductCartInfo = $objBookingCart->getBookingProductCartInfo(
                    $product['id_product'],
                    $cart->id
                )) {
                    $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                    foreach ($bookingProductCartInfo as $keyProduct => $cartBookingProduct) {
                        $idProduct = $cartBookingProduct['id_product'];
                        $productPriceTI = Product::getPriceStatic((int) $idProduct, true);
                        $productPriceTE = Product::getPriceStatic((int) $idProduct, false);
                        if ($cartBookingProduct['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                            $bookingTimeSlotPrice = false;
                            $objTimeSlot = new WkMpBookingProductTimeSlotPrices();
                            $slotDetails = $objTimeSlot->getProductTimeSlotDetails(
                                $idBookingProductInfo,
                                $cartBookingProduct['date_from'],
                                $cartBookingProduct['time_from'],
                                $cartBookingProduct['time_to']
                            );
                            if ($slotDetails) {
                                $bookingTimeSlotPrice['price_tax_excl'] = $slotDetails['price'];
                                $taxRate = (float) WkMpBookingProductInformation::getAppliedProductTaxRate($idProduct);
                                $bookingTimeSlotPrice['price_tax_incl'] = $bookingTimeSlotPrice['price_tax_excl'] * ((100 + $taxRate) / 100);
                                $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartBookingProduct['quantity'];
                                $totalFeaturePrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $idBookingProductInfo,
                                    $cartBookingProduct['date_from'],
                                    $cartBookingProduct['date_from'],
                                    $bookingTimeSlotPrice
                                );
                            }
                        } elseif ($cartBookingProduct['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                            $totalFeaturePrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                $idBookingProductInfo,
                                $cartBookingProduct['date_from'],
                                $cartBookingProduct['date_to'],
                                false
                            );
                        }

                        // create array of product price differences for creating specific prices
                        if (isset($paidProductPrices[$cartBookingProduct['id_product']])) {
                            $paidProductPrices[$cartBookingProduct['id_product']]['paid_total_product_price_ti'] += $totalFeaturePrice['total_price_tax_incl'] * $cartBookingProduct['quantity'];
                            $paidProductPrices[$cartBookingProduct['id_product']]['paid_total_product_price_te'] += $totalFeaturePrice['total_price_tax_excl'] * $cartBookingProduct['quantity'];
                        } else {
                            $paidProductPrices[$cartBookingProduct['id_product']]['paid_total_product_price_ti'] = $totalFeaturePrice['total_price_tax_incl'] * $cartBookingProduct['quantity'];
                            $paidProductPrices[$cartBookingProduct['id_product']]['paid_total_product_price_te'] = $totalFeaturePrice['total_price_tax_excl'] * $cartBookingProduct['quantity'];
                        }

                        // enter the bookings ptoducts order information in our booking order table
                        $objBookingOrders = new WkMpBookingOrder();
                        $objBookingOrders->id_cart = $cartBookingProduct['id_cart'];
                        $objBookingOrders->id_order = $order->id;
                        $objBookingOrders->id_product = $cartBookingProduct['id_product'];
                        $objBookingOrders->id_mp_product = $bookingProductInfo['id_mp_product'];
                        $objBookingOrders->quantity = $cartBookingProduct['quantity'];
                        $objBookingOrders->booking_type = $cartBookingProduct['booking_type'];
                        $objBookingOrders->date_from = $cartBookingProduct['date_from'];
                        $objBookingOrders->date_to = $cartBookingProduct['date_to'];
                        $objBookingOrders->time_from = $cartBookingProduct['time_from'];
                        $objBookingOrders->time_to = $cartBookingProduct['time_to'];
                        $objBookingOrders->consider_last_date = $cartBookingProduct['consider_last_date'];
                        $objBookingOrders->product_real_price_tax_excl = $productPriceTE;
                        $objBookingOrders->product_real_price_tax_incl = $productPriceTI;
                        $objBookingOrders->range_feature_price_tax_incl = Tools::ps_round(
                            $totalFeaturePrice['total_price_tax_incl'],
                            6
                        );
                        $objBookingOrders->range_feature_price_tax_excl = Tools::ps_round(
                            $totalFeaturePrice['total_price_tax_excl'],
                            6
                        );
                        $objBookingOrders->total_order_tax_excl = $order->total_paid_tax_excl;
                        $objBookingOrders->total_order_tax_incl = $order->total_paid_tax_incl;
                        if (!$objBookingOrders->save()) {
                            error_log(
                                date('[Y-m-d H:i e] ').'WkMpBookingOrder save Error : Error occured while making entry
                                with the details :: cartBookingProduct = '.$cartBookingProduct.PHP_EOL.
                                'totalFeaturePriceArray = '.$totalFeaturePrice.PHP_EOL,
                                3,
                                _PS_MODULE_DIR_.'mpbooking/error.log'
                            );
                        }
                    }
                }
            }
        }
        // change the order details product price info as paid by cusstomer after applying feature prices
        if (count($paidProductPrices)) {
            $objBookingOrders = new WkMpBookingOrder();
            foreach ($paidProductPrices as $id_product => $productPrice) {
                $orderProductDetails = $objBookingOrders->getOrderDetailsProductInfo($idOrder, $id_product);
                if ($orderProductDetails) {
                    if ($orderProductDetails['total_price_tax_incl'] != $productPrice['paid_total_product_price_ti']) {
                        $fieldsToUpdate = array();

                        $fieldsToUpdate['total_price_tax_incl'] = $productPrice['paid_total_product_price_ti'];
                        $fieldsToUpdate['total_price_tax_excl'] = $productPrice['paid_total_product_price_te'];
                        $productQty = $orderProductDetails['product_quantity'];

                        $fieldsToUpdate['unit_price_tax_incl'] = Tools::ps_round(
                            ($productPrice['paid_total_product_price_ti'] / $productQty),
                            6
                        );
                        $fieldsToUpdate['unit_price_tax_excl'] = Tools::ps_round(
                            ($productPrice['paid_total_product_price_te'] / $productQty),
                            6
                        );
                        if (!$objBookingOrders->updatePsOrderDetailsColumns($idOrder, $id_product, $fieldsToUpdate)) {
                            error_log(
                                date('[Y-m-d H:i e] ').'actionValidateOrder : Error occured while updating product
                                prices in order_detail (feature prices) for id_product : '.$id_product.PHP_EOL.
                                'id_order = '.$idOrder.PHP_EOL.'fieldsToUpdate = '.$fieldsToUpdate,
                                3,
                                _PS_MODULE_DIR_.'mpbooking/error.log'
                            );
                        }
                    }
                }
            }
        }
    }

    // * admin display booking product orders details.
    public function hookDisplayAdminOrder()
    {
        $idOrder = Tools::getValue('id_order');
        $order = new Order($idOrder);
        $orderProducts = $order->getProducts();
        $objBookingProductInfo = new WkMpBookingProductInformation();
        $objBookingOrders = new WkMpBookingOrder();
        foreach ($orderProducts as $key => &$product) {
            if ($product['image'] != null) {
                $imageName = 'product_mini_'.(int) $product['product_id'].
                (isset($product['product_attribute_id']) ? '_'.(int) $product['product_attribute_id'] : '').'.jpg';

                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(
                    _PS_IMG_DIR_.'p/'.$product['image']->getExistingImgPath().'.jpg',
                    $imageName,
                    45,
                    'jpg'
                );
                if (file_exists(_PS_TMP_IMG_DIR_.$imageName)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$imageName);
                } else {
                    $product['image_size'] = false;
                }
            }
            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $product['product_id'])) {
                if ($bookingProductOrderInfo = $objBookingOrders->getBookingProductOrderInfo(
                    $product['product_id'],
                    $idOrder
                )) {
                    $objOrderCurrency = new Currency($order->id_currency);
                    if ($sellerProductDetails = $objBookingOrders->getSellerProductDetailsByIdOrder(
                        $idOrder,
                        $product['id_product']
                    )) {
                        $product['sellerName'] = $sellerProductDetails['seller_name'];
                        $sellerDetails = WkMpSeller::getSellerDetailByCustomerId(
                            $sellerProductDetails['seller_customer_id']
                        );
                        $product['id_seller'] = $sellerDetails['id_seller'];
                    } else {
                        $product['sellerName'] = $this->l('Admin');
                    }
                    foreach ($bookingProductOrderInfo as $keyProduct => $cartBooking) {
                        $bookingProductOrderInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'] * (WkMpBookingHelper::getNumberOfDays($cartBooking['date_from'], $cartBooking['date_to']));
                        $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                            $bookingProductInfo['id_booking_product_info'],
                            $cartBooking['date_from'],
                            $cartBooking['date_to']
                        );
                        $bookingProductOrderInfo[$keyProduct]['totalPriceTE'] = Tools::displayPrice(
                            (float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_excl']),
                            $objOrderCurrency
                        );
                        $bookingProductOrderInfo[$keyProduct]['totalPriceTI'] = Tools::displayPrice(
                            (float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_incl']),
                            $objOrderCurrency
                        );
                        $bookingProductOrderInfo[$keyProduct]['product_real_price_tax_excl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['product_real_price_tax_excl']), $objOrderCurrency);
                        $bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_excl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_excl']), $objOrderCurrency);
                        $bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_incl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_incl']), $objOrderCurrency);
                        $bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_excl_formated'] = Tools::displayPrice((float) $cartBooking['range_feature_price_tax_excl'], $objOrderCurrency);
                        $bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_incl_formated'] = Tools::displayPrice((float) $cartBooking['range_feature_price_tax_incl'], $objOrderCurrency);
                    }
                    $orderProducts[$key]['booking_product_data'] = $bookingProductOrderInfo;
                } else {
                    unset($orderProducts[$key]);
                }
            } else {
                unset($orderProducts[$key]);
            }
        }
        if (count($orderProducts)) {
            $this->context->smarty->assign(
                array(
                    'orderBookingProducts' => $orderProducts,
                    'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
                )
            );
            return $this->display(__FILE__, 'adminBookingProductOrderDetails.tpl');
        }
    }

    public function hookDisplayMpOrderDetailProductBottom()
    {
        $idOrder = Tools::getValue('id_order');
        if (Validate::isLoadedObject($objOrder = new Order($idOrder))) {
            $sellerDetail = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($sellerDetail && $sellerDetail['active']) {
                $orderProducts = $objOrder->getProducts();
                $objBookingProductInfo = new WkMpBookingProductInformation();
                $objBookingOrders = new WkMpBookingOrder();
                foreach ($orderProducts as $key => &$product) {
                    if ($product['image'] != null) {
                        $imageName = 'product_mini_'.(int) $product['product_id'].
                        (isset($product['product_attribute_id']) ? '_'.(int) $product['product_attribute_id'] : '').
                        '.jpg';

                        // generate image cache, only for back office
                        $product['image_tag'] = ImageManager::thumbnail(
                            _PS_IMG_DIR_.'p/'.$product['image']->getExistingImgPath().'.jpg',
                            $imageName,
                            45,
                            'jpg'
                        );
                        if (file_exists(_PS_TMP_IMG_DIR_.$imageName)) {
                            $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$imageName);
                        } else {
                            $product['image_size'] = false;
                        }
                    }
                    if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $product['product_id'])) {
                        if ($bookingProductOrderInfo = $objBookingOrders->getBookingProductOrderInfo(
                            $product['product_id'],
                            $idOrder
                        )) {
                            $objOrderCurrency = new Currency($objOrder->id_currency);
                            if ($sellerProductDetails = $objBookingOrders->getSellerProductDetailsByIdOrder(
                                $idOrder,
                                $product['id_product'],
                                $this->context->customer->id
                            )) {
                                $product['mpBookingProductLink'] = $this->context->link->getModuleLink(
                                    'mpbooking',
                                    'mpbookingproduct',
                                    array('id_mp_product' => $bookingProductInfo['id_mp_product'])
                                );
                                foreach ($bookingProductOrderInfo as $keyProduct => $cartBooking) {
                                    $bookingProductOrderInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'] * (WkMpBookingHelper::getNumberOfDays($cartBooking['date_from'], $cartBooking['date_to']));
                                    $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                        $bookingProductInfo['id_booking_product_info'],
                                        $cartBooking['date_from'],
                                        $cartBooking['date_to']
                                    );
                                    $bookingProductOrderInfo[$keyProduct]['totalPriceTE'] = Tools::displayPrice(
                                        (float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_excl']),
                                        $objOrderCurrency
                                    );
                                    $bookingProductOrderInfo[$keyProduct]['totalPriceTI'] = Tools::displayPrice(
                                        (float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_incl']),
                                        $objOrderCurrency
                                    );
                                    $bookingProductOrderInfo[$keyProduct]['product_real_price_tax_excl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['product_real_price_tax_excl']), $objOrderCurrency);
                                    $bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_excl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_excl']), $objOrderCurrency);
                                    $bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_incl_formated'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_incl']), $objOrderCurrency);
                                    $bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_excl_formated'] = Tools::displayPrice((float) $cartBooking['range_feature_price_tax_excl'], $objOrderCurrency);
                                    $bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_incl_formated'] = Tools::displayPrice((float) $cartBooking['range_feature_price_tax_incl'], $objOrderCurrency);
                                }
                                $orderProducts[$key]['booking_product_data'] = $bookingProductOrderInfo;
                            } else {
                                unset($orderProducts[$key]);
                            }
                        } else {
                            unset($orderProducts[$key]);
                        }
                    } else {
                        unset($orderProducts[$key]);
                    }
                }
                if (count($orderProducts)) {
                    $this->context->smarty->assign(
                        array(
                            'orderBookingProducts' => $orderProducts,
                            'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
                        )
                    );
                    return $this->fetch('module:mpbooking/views/templates/hook/sellerBookingProductDetails.tpl');
                }
            }
        }
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if ($params['smarty']->template_resource == 'module:ps_shoppingcart/modal.tpl') {
            $lastProductAdded = $this->context->cart->getLastProduct();
            $idProduct = $lastProductAdded['id_product'];
            $objBookingProductInfo = new WkMpBookingProductInformation();
            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                $objBookingCart = new WkMpBookingCart();
                $bookingProductCartInfo = $objBookingCart->getCartInfoByProduct($idProduct, $this->context->cart->id);
                if ($bookingProductCartInfo) {
                    foreach ($bookingProductCartInfo as $key => $product) {
                        if ($product['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                            $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                $bookingProductInfo['id_booking_product_info'],
                                $bookingProductCartInfo[$key]['date_from'],
                                $product['date_to']
                            );
                            $bookingProductCartInfo[$key]['totalPriceTE'] = Tools::displayPrice(
                                (float) ($product['quantity'] * $totalPriceBookingProduct['total_price_tax_excl'])
                            );
                            $bookingProductCartInfo[$key]['totalPriceTI'] = Tools::displayPrice(
                                (float) ($product['quantity'] * $totalPriceBookingProduct['total_price_tax_incl'])
                            );
                        } elseif ($product['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                            $bookingTimeSlotPrice = false;
                            $objTimeSlot = new WkMpBookingProductTimeSlotPrices();
                            $slotDetails = $objTimeSlot->getProductTimeSlotDetails(
                                $bookingProductInfo['id_booking_product_info'],
                                $product['date_from'],
                                $product['time_from'],
                                $product['time_to']
                            );
                            if ($slotDetails) {
                                $bookingTimeSlotPrice['price_tax_excl'] = $slotDetails['price'];

                                $taxRate = (float) WkMpBookingProductInformation::getAppliedProductTaxRate($idProduct);
                                $bookingTimeSlotPrice['price_tax_incl'] = $bookingTimeSlotPrice['price_tax_excl'] * ((100 + $taxRate) / 100);
                                $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $bookingProductInfo['id_booking_product_info'],
                                    $product['date_from'],
                                    $product['date_from'],
                                    $bookingTimeSlotPrice
                                );
                            }
                        }
                        $bookingProductCartInfo[$key]['totalPriceTE'] = Tools::displayPrice(
                            (float) ($product['quantity'] * $totalPriceBookingProduct['total_price_tax_excl'])
                        );
                        $bookingProductCartInfo[$key]['totalPriceTI'] = Tools::displayPrice(
                            (float) ($product['quantity'] * $totalPriceBookingProduct['total_price_tax_incl'])
                        );
                    }
                    $this->context->smarty->assign(
                        array(
                            'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
                            'bookingProductCartInfo'=> $bookingProductCartInfo
                        )
                    );
                    return $this->fetch('module:mpbooking/views/templates/hook/cartPopUpBookingInfo.tpl');
                }
            }
        }
    }

    public function hookActionProductUpdate($params)
    {
        if (isset($params['id_product']) && $idProduct = $params['id_product']) {
            $objBookingProductInfo = new WkMpBookingProductInformation();
            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                if ($bookingProductInfo['id_booking_product_info']) {
                    $objProduct = new Product($idProduct);
                    if (!$objProduct->is_virtual) {
                        $objProduct->is_virtual = 1;
                        $objProduct->save();
                    }
                    $objBookingProductInfo = new WkMpBookingProductInformation(
                        $bookingProductInfo['id_booking_product_info']
                    );
                    $objBookingProductInfo->active = $objProduct->active;
                    $objBookingProductInfo->save();
                }
            }
        }
    }

    //Action after UPDATE MP products
    public function hookActionAfterUpdateMPProduct($params)
    {
        if (isset($params['id_mp_product']) && $idMpProduct = $params['id_mp_product']) {
            $objBookingProductInfo = new WkMpBookingProductInformation();
            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idMpProduct)) {
                if ($bookingProductInfo['id_booking_product_info']) {
                    // $objMpProduct = new WkMpSellerProduct($idMpProduct);
                    // $objMpProduct->save();
                    $objBookingProductInfo = new WkMpBookingProductInformation(
                        $bookingProductInfo['id_booking_product_info']
                    );
                    $objBookingProductInfo->active = $objProduct->active;
                    $objBookingProductInfo->save();
                }
            }
        }
    }

    public function hookActionMpSellerDelete($params)
    {
        if (isset($params['id_seller']) && ($idSeller = $params['id_seller'])) {
            $objBookingProduct = new WkMpBookingProductInformation();
            if ($sellerBookingProducts = $objBookingProduct->getSellerBookingProductsInfo($idSeller)) {
                foreach ($sellerBookingProducts as $bookingProduct) {
                    if ($bookingProduct['id_mp_product']) {
                        $objSellerProduct = new WkMpSellerProduct($bookingProduct['id_mp_product']);
                        $objSellerProduct->delete();
                    }
                }
            }
        }
    }

    public function hookActionBeforeToggleMPProductStatus($params)
    {
        if ($idMpProduct = $params['id_mp_product']) {
            $objSellerProduct = new WkMpSellerProduct($idMpProduct);
            $objBookingProductInfo = new WkMpBookingProductInformation();
            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(
                $idMpProduct,
                $objSellerProduct->id_ps_product
            )) {
                if (Validate::isLoadedObject($objMpProduct = new WkMpSellerProduct($idMpProduct))) {
                    $objBookingProductInfo = new WkMpBookingProductInformation($bookingProductInfo['id_mp_product']);
                    $objBookingProductInfo->active = !$objMpProduct->active;
                    if (!$objBookingProductInfo->save()) {
                        $this->context->controller->errors[] = $this->l('Some error has been occurred changin status in
                        the booking product information');
                    }
                }
            }
        }
    }

    // To remove booking products from the list of marketplace admin seller products list
    public function hookActionAdminSellerProductDetailListingResultsModifier($params)
    {
        if ($params['list']) {
            $objBookingProduct = new WkMpBookingProductInformation();
            foreach ($params['list'] as $key => $row) {
                if ($objBookingProduct->getBookingProductInfo(
                    $row['id_mp_product'],
                    $row['id_ps_product']
                )) {
                    unset($params['list'][$key]);
                }
            }
        }
    }

    public function hookActionAdminProductsListingResultsModifier($params)
    {
        if (isset($params['products']) && $params['products']) {
            $objBookingProductInfo = new WkMpBookingProductInformation();
            foreach ($params['products'] as &$product) {
                if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $product['id_product'])) {
                    $product['sav_quantity'] = $bookingProductInfo['quantity'];
                }
            }
        }
    }

    public function hookDisplayMPMyAccountMenu($params)
    {
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $mpSelerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpSelerInfo && $mpSelerInfo['active']) {
                $this->context->smarty->assign(
                    array(
                        'mpmenu' => 0,
                        'mp_bookingproductlist_link' => $this->context->link->getModuleLink(
                            'mpbooking',
                            'mpbookingproductslist'
                        ),
                        'mp_featurepriceplans_link' => $this->context->link->getModuleLink(
                            'mpbooking',
                            'mpfeaturepriceplanslist'
                        ),
                    )
                );
                return $this->fetch('module:mpbooking/views/templates/hook/mpBookingProductLink.tpl');
            }
        }
    }

    //Action after delete MP product
    public function hookActionMpProductDelete($params)
    {
        if (isset($params['id_mp_product'])) {
            $objBookingProductInfo = new WkMpBookingProductInformation();
            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo($params['id_mp_product'])) {
                $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                $objProductFeaturePricing = new WkMpBookingProductFeaturePricing();
                if (!$objProductFeaturePricing->deleteFeaturePricePlansByIdBookingProductInfo($idBookingProductInfo)) {
                    $this->context->controller->errors[] = $this->l('Some error has been occurred while deleting booking
                    price rules of this product.');
                }
                $objTimeSlotPrices = new WkMpBookingProductTimeSlotPrices();
                if (!$objTimeSlotPrices->deleteTimeSlotsByIdBookingProductInfo($idBookingProductInfo)) {
                    $this->context->controller->errors[] = $this->l('Some error has been occurred while deleting time
                    slots info of this product.');
                }
                $objDisableDates = new WkMpBookingProductDisabledDates();
                if (!$objDisableDates->deleteDisableDatesByIdBookingProductInfo($idBookingProductInfo)) {
                    $this->context->controller->errors[] = $this->l('Some error has been occurred while deleting disable
                    dates info of this product.');
                }
                $objBookingProductInfo = new WkMpBookingProductInformation($idBookingProductInfo);
                if (!$objBookingProductInfo->delete()) {
                    $this->context->controller->errors[] = $this->l('Some error has been occurred while deleting booking
                    product infor of this product.');
                }
            }
        }
    }

    public function hookDisplayMPMenuBottom($params)
    {
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $mpSelerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpSelerInfo && $mpSelerInfo['active']) {
                $objBookingProduct = new WkMpBookingProductInformation();
                $countSellerBookingProducts = 0;
                $sellerBookingProducts = $objBookingProduct->getSellerBookingProductsInfo($mpSelerInfo['id_seller']);
                $this->context->smarty->assign(
                    array(
                        'mpmenu' => 1,
                        'mp_bookingproductlist_link' => $this->context->link->getModuleLink('mpbooking', 'mpbookingproductslist'),
                        'mp_featurepriceplans_link' => $this->context->link->getModuleLink('mpbooking', 'mpfeaturepriceplanslist'),
                        'countBookingProducts' => $sellerBookingProducts ? count($sellerBookingProducts) : 0,
                    )
                );
                return $this->fetch('module:mpbooking/views/templates/hook/mpBookingProductLink.tpl');
            }
        }
    }

    public function hookActionSellerProductsListResultModifier($params)
    {
        if (isset($params['seller_product_list']) && $params['seller_product_list']) {
            $objBookingProduct = new WkMpBookingProductInformation();
            foreach ($params['seller_product_list'] as $key => $product) {
                if ($objBookingProduct->getBookingProductInfo($product['id_mp_product'])) {
                    unset($params['seller_product_list'][$key]);
                }
            }
        }
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($params['object']->id) {
            $newIdLang = $params['object']->id;
            //Assign all lang's main table in an ARRAY
            $langTables = array('wk_mp_booking_product_feature_pricing');

            //If Admin update new language when we do entry in module all lang tables.
            WkMpHelper::updateIdLangInLangTables($newIdLang, $langTables, 'id_feature_price_rule');
            $this->createMailLangDirectoryWithFiles($newIdLang);
        }
    }

    public function callInstallTab()
    {
        $this->installTab(
            'AdminManageSellerBookingProduct',
            'Manage Seller Booking Product',
            'AdminMarketplaceManagement'
        );
        $this->installTab(
            'AdminSellerBookingProductDetail',
            'Manage Seller Booking Product',
            'AdminManageSellerBookingProduct'
        );
        $this->installTab(
            'AdminSellerBookingProductPricePlansSettings',
            'Manage Seller Booking Produts Price Rules',
            'AdminManageSellerBookingProduct'
        );

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

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'displayProductButtons',
                'actionFrontControllerSetMedia',
                'displayOverrideTemplate',
                'actionValidateOrder',
                'displayAdminOrder',
                'displayProductPriceBlock',
                'displayHeader',
                'displayProductAdditionalInfo',
                'actionProductUpdate',
                'actionAdminProductsListingResultsModifier',
                'actionAdminSellerProductDetailListingResultsModifier',
                'displayMPMyAccountMenu',
                'displayMPMenuBottom',
                'actionBeforeToggleMPProductStatus',
                'displayMpOrderDetailProductBottom',
                'actionSellerProductsListResultModifier',
                'actionMpProductDelete',
                'actionAfterUpdateMPProduct',
                'actionObjectLanguageAddAfter',
                'actionMpSellerDelete'
            )
        );
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerModuleHooks()
            || !$this->createTables()
            || !$this->callInstallTab()
            || !Configuration::updateValue('WK_MP_CONSIDER_DATE_TO', 0)
            || !Configuration::updateValue('WK_MP_FEATURE_PRICE_RULES_SHOW', 1)
            || !(Configuration::updateValue(
                'WK_MP_PRODUCT_FEATURE_PRICING_PRIORITY',
                'specific_date;special_day;date_range'
            ))
            || !$this->createMailLangDirectoryWithFiles()
        ) {
            return false;
        }

        return true;
    }

    public function dropTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing`,
            `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing_lang`,
            `'._DB_PREFIX_.'wk_mp_booking_time_slots_prices`,
            `'._DB_PREFIX_.'wk_mp_booking_cart`,
            `'._DB_PREFIX_.'wk_mp_booking_order`,
            `'._DB_PREFIX_.'wk_mp_booking_product_disabled_dates`,
            `'._DB_PREFIX_.'wk_mp_booking_product_info`'
        );
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                if (!$moduleTab->delete()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function deleteConfigVars()
    {
        $config_keys = array(
            'WK_MP_CONSIDER_DATE_TO',
            'WK_MP_PRODUCT_FEATURE_PRICING_PRIORITY',
            'WK_MP_FEATURE_PRICE_RULES_SHOW',
        );
        foreach ($config_keys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->dropTables()
            || !$this->deleteConfigVars()
            || !$this->uninstallTab()
        ) {
            return false;
        }
        return true;
    }

    // Ps all imported language's Mail directory will be created with all files in module's mails folder
    private function createMailLangDirectoryWithFiles($idLang = 0)
    {
        if ($idLang) {
            if ($language = new Language($idLang)) {
                $langISO = $language->iso_code;
                //Ignore 'en' directory because we already have this in our module folder
                if ($langISO != 'en') {
                    $this->createModuleMailDir($langISO);
                }
            }
        } else {
            if ($allLanguages = Language::getLanguages(false, $this->context->shop->id)) {
                foreach ($allLanguages as $language) {
                    $langISO = $language['iso_code'];
                    //Ignore 'en' directory because we already have this in our module folder
                    if ($langISO != 'en') {
                        $this->createModuleMailDir($langISO);
                    }
                }
            }
        }
        return true;
    }

    private function createModuleMailDir($langIso)
    {
        $moduleMailDir = _PS_MODULE_DIR_.$this->name.'/mails/';
        //create lang dir if not exist in module mails directory
        if (!file_exists($moduleMailDir.$langIso)) {
            @mkdir($moduleMailDir.$langIso, 0777, true);
        }
        //Now if lang dir is exist or created by above code
        if (is_dir($moduleMailDir.$langIso)) {
            $mailEnDir = _PS_MODULE_DIR_.$this->name.'/mails/en/';
            if (is_dir($mailEnDir)) {
                if ($allFiles = scandir($mailEnDir)) {
                    foreach ($allFiles as $fileName) {
                        if ($fileName != '.' && $fileName != '..') {
                            $source = $mailEnDir.$fileName;
                            $destination = $moduleMailDir.$langIso.'/'.$fileName;
                            //if file not exist in desti directory then create that file
                            if (!file_exists($destination) && file_exists($source)) {
                                Tools::copy($source, $destination);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
}
