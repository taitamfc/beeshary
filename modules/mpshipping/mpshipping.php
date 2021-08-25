<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once 'classes/MpShippingInclude.php';
include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
class MpShipping extends CarrierModule
{
    public $id_carrier;
    public function __construct()
    {
        $this->name = 'mpshipping';
        $this->tab = 'front_office_features';
        $this->version = '5.2.1';
        $this->author = 'Webkul';
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->dependencies = array('marketplace');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        parent::__construct();
        $this->displayName = $this->l('Marketplace Seller Shipping');
        $this->description = $this->l('Provide seller to create their own shipping method');
    }

    public function getOrderShippingCost($cart, $shippingCost)
    {
        return $shippingCost;
    }

    public function getOrderShippingCostExternal($params)
    {
        $this->getOrderShippingCost($params, 0);
    }

    /**
     * [getOrderShippingCost To send shipping cost of carriers made by the sellers after adding impact price.
     *
     * @param [Objec]t $cart          [Object of the current cart]
     * @param [Float]  $shippingCost [previous shipping cost calculated in the Cart.php]
     *
     * @return [Float|false] [Shipping cost of the carrier after adding impact price]
     */
    public function getPackageShippingCost($cart, $shippingCost, $products)
    {
        $objMpshipProd = new MpShippingProductMap();
        $sellerProdSum = array();
        $adminCurrDef = Configuration::get('PS_CURRENCY_DEFAULT');
        foreach ($products as $valProd) {
            $mpProdId = $objMpshipProd->checkMpProduct($valProd['id_product']);
            $mpSellerProd = (new WkMpSellerProduct($mpProdId))->id_seller;
            if (!isset($sellerProdSum[$mpSellerProd]['price'])) {
                if (isset($valProd['price_with_reduction'])) {
                    $sellerProdSum[$mpSellerProd]['price'] = $valProd['price_with_reduction'] * $valProd['quantity'];
                }
                if (isset($valProd['weight'])) {
                    $sellerProdSum[$mpSellerProd]['weight'] = $valProd['weight'] * $valProd['quantity'];
                }
            } else {
                if (isset($valProd['price_with_reduction'])) {
                    $sellerProdSum[$mpSellerProd]['price'] += $valProd['price_with_reduction'] * $valProd['quantity'];
                }
                if (isset($valProd['weight'])) {
                    $sellerProdSum[$mpSellerProd]['weight'] += $valProd['weight'] * $valProd['quantity'];
                }
            }
        }
        $impactPrice = 0;
        $psCarrierRef = (new Carrier($this->id_carrier))->id_reference;
        $objMpShipMthod = new MpShippingMethod();
        $mpShippingId = $objMpShipMthod->getMpShippingId($psCarrierRef);
        if ($mpShippingId) {
            $deliveryAddObj = new Address($cart->id_address_delivery);
            if (isset($deliveryAddObj->id_country) && $deliveryAddObj->id_country) {
                $objMpShipMthod = new MpShippingMethod($mpShippingId);
                $shippingIdSeller = $objMpShipMthod->mp_id_seller;
                if (isset($sellerProdSum[$shippingIdSeller]['price'])) {
                    $sellerProductTotal = Tools::convertPriceFull($sellerProdSum[$shippingIdSeller]['price'], Currency::getCurrencyInstance((int) $this->context->cart->id_currency), Currency::getCurrencyInstance((int) $adminCurrDef));
                }

                if (isset($sellerProductTotal)) {
                    $shipMthod = $objMpShipMthod->shipping_method;

                    $byPrice = 0;
                    if ($shipMthod == 2) {
                        $byPrice = 1;
                    }

                    if ($byPrice) {
                        $totlProdCart = $sellerProductTotal;
                    } else {
                        $totlProdCart = $sellerProdSum[$shippingIdSeller]['weight'];
                    }
                    $chkIdState = $deliveryAddObj->id_state;
                    $chkIdCountry = $deliveryAddObj->id_country;
                    $chkIdZone = Country::getIdZone($chkIdCountry);
                    $objImpactCrr = new MpShippingImpact();
                    if (!$chkIdState) {
                        $impactPrice = $objImpactCrr->getImpactPriceWithRange($totlProdCart, $mpShippingId, $chkIdZone, $chkIdCountry, $chkIdState, $byPrice, 1);
                    } else {
                        $impactPrice = $objImpactCrr->getImpactPriceWithRange($totlProdCart, $mpShippingId, $chkIdZone, $chkIdCountry, $chkIdState, $byPrice, 0);
                    }
                }
            }
        }
        if ($impactPrice) {
            $idCarrier = $this->id_carrier;
            $idCart = $cart->id;
            $objMpCart = new MpShippingCart();
            $isAvailable = $objMpCart->isAvailable($idCarrier, $idCart);

            if (!$isAvailable) {
                $objMpCart->id_ps_carrier = $idCarrier;
                $objMpCart->id_ps_cart = $idCart;
                $objMpCart->extra_cost = $impactPrice;
                $objMpCart->save();
            } else {
                if ($impactPrice != $isAvailable['extra_cost']) {
                    $objMpCart = new MpShippingCart($isAvailable['id']);
                    $objMpCart->id_ps_carrier = $idCarrier;
                    $objMpCart->id_ps_cart = $idCart;
                    $objMpCart->extra_cost = $impactPrice;
                    $objMpCart->save();
                }
            }
        }
        $impactPrice = Tools::convertPrice($impactPrice, Currency::getCurrencyInstance((int) $this->context->cart->id_currency));
        $totalShipping = $shippingCost + $impactPrice;
        if ($totalShipping > 0) {
            return $totalShipping;
        } else {
            return 0;
        }
    }

    public function checkMarketplaceVersion()
    {
        if (Module::getInstanceByName('marketplace')->version < "5.3.0") {
            return true;
        }

        return false;
    }

    public function getContent()
    {

        $this->_html = '';

        if (Tools::isSubmit('submit_admin_default_shipping')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } elseif (Tools::isSubmit('submit_shipping_approval')) {
            $shippingAdmin = Tools::getValue('MP_SHIPPING_ADMIN_SELLER');
            Configuration::updateValue('MP_SHIPPING_ADMIN_SELLER', $shippingAdmin);
            $mpShippingApprove = Tools::getValue('MP_SHIPPING_ADMIN_APPROVE');
            Configuration::updateValue('MP_SHIPPING_ADMIN_APPROVE', $mpShippingApprove);

            if ($this->checkMarketplaceVersion()) {
                Configuration::updateValue(
                    'MP_SHIPPING_DISTRIBUTION_ALLOW',
                    Tools::getValue('MP_SHIPPING_DISTRIBUTION_ALLOW')
                );
                Configuration::updateValue(
                    'MP_SHIPPING_ADMIN_DISTRIBUTION',
                    Tools::getValue('MP_SHIPPING_ADMIN_DISTRIBUTION')
                );
            }

            $moduleConfig = $this->context->link->getAdminLink('AdminModules');
            Tools::redirectAdmin($moduleConfig.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4');
        } elseif (Tools::isSubmit('submit_email_setting')) {
            $shippingAdded = Tools::getValue('MP_MAIL_ADMIN_SHIPPING_ADDED');
            Configuration::updateValue('MP_MAIL_ADMIN_SHIPPING_ADDED', $shippingAdded);

            $shippingApproval = Tools::getValue('MP_MAIL_SELLER_SHIPPING_APPROVAL');
            Configuration::updateValue('MP_MAIL_SELLER_SHIPPING_APPROVAL', $shippingApproval);

            $moduleConfig = $this->context->link->getAdminLink('AdminModules');
            Tools::redirectAdmin($moduleConfig.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4');
        } else {
            $this->_html .= '<br />';
        }

        $link = new Link();
        $idLang = $this->context->language->id;
        $allPsCarriersArr = MpShippingMethod::getOnlyPrestaCarriers($idLang);

        $adminDefShipping = array();
        if (Configuration::get('MP_SHIPPING_ADMIN_DEFAULT')) {
            $adminDefShipping = unserialize(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));
        }

        $this->context->smarty->assign(array(
            'this_path' => $this->_path,
            'all_ps_carriers_arr' => $allPsCarriersArr,
            'admin_def_shipping' => $adminDefShipping,
            'MP_SHIPPING_ADMIN_SELLER' => Configuration::get('MP_SHIPPING_ADMIN_SELLER'),
            'MP_SHIPPING_ADMIN_APPROVE' => Configuration::get('MP_SHIPPING_ADMIN_APPROVE'),
            'MP_MAIL_ADMIN_SHIPPING_ADDED' => Configuration::get('MP_MAIL_ADMIN_SHIPPING_ADDED'),
            'MP_MAIL_SELLER_SHIPPING_APPROVAL' => Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL'),
        ));

        if ($this->checkMarketplaceVersion()) {
            $this->context->smarty->assign(array(
                'MP_SHIPPING_DISTRIBUTION_ALLOW' => Configuration::get('MP_SHIPPING_DISTRIBUTION_ALLOW'),
                'MP_SHIPPING_ADMIN_DISTRIBUTION' => Configuration::get('MP_SHIPPING_ADMIN_DISTRIBUTION'),
            ));
        }

        Media::addJsDef(array('ajaxurl_admin_mpshipping_url' => $link->getAdminLink('AdminMpSellerShipping')));

        $this->context->controller->addCSS($this->_path.'views/css/adminconfig.css');
        $this->context->controller->addJS($this->_path.'views/js/adminconfig.js');

        $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/admin.tpl');
        return $this->_html;
    }

    private function _postValidation()
    {
        if (Tools::isSubmit('submit_admin_default_shipping')) {
            if (!Tools::getValue('default_shipping')) {
                $this->_postErrors[] = $this->l('Choose atleast one shipping method');
            }
        }
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('submit_admin_default_shipping')) {
            $adminDefShipping = serialize(Tools::getValue('default_shipping'));
            Configuration::updateValue('MP_SHIPPING_ADMIN_DEFAULT', $adminDefShipping);

            /*Assign new selected shipping methods to the seller produccts which have no seller shipping methods*/
            $objMpShippingMet = new MpShippingMethod();
            $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
            /*END*/

            $moduleConfig = $this->context->link->getAdminLink('AdminModules');
            Tools::redirectAdmin($moduleConfig.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4');
        }
    }

    public function hookActionAdminCarriersListingFieldsModifier($list)
    {
        if ($this->checkMarketplaceVersion()) {
            //Display shipping distribution section in carriers page
            if (Configuration::get('MP_SHIPPING_DISTRIBUTION_ALLOW')) {
                $optionsDistributeType = array(
                'admin' => $this->l('Admin'),
                'seller' => $this->l('Seller'),
                'both' => $this->l('Both (on the basis of commission rate)'),
            );

                if (isset($list['select'])) {
                    //By default Admin will selected
                    $list['select'] .= ', IF(msd.`type` != "", msd.`type`, "admin") AS `distribute_type`';
                }
                if (isset($list['join'])) {
                    $list['join'] .= ' LEFT JOIN `'._DB_PREFIX_.'mp_shipping_distribution` msd ON (msd.`id_ps_reference` = a.`id_reference`)';
                }

                $list['fields']['distribute_type'] = array(
                    'title' => 'Shipping Distribute To',
                    'align' => 'text-center',
                    'orderby' => false,
                    'remove_onclick' => true,
                    'type' => 'select',
                    'hint' => $this->l('Distribution will applicable only on Marketplace Seller Product'),
                    'list' => $optionsDistributeType,
                    'filter_key' => 'msd!type',
                    'callback' => 'callCarrierDistribution',
                    'callback_object' => Module::getInstanceByName($this->name)
                );
            }
        }
    }

    // customization By Amit Webkul uv_265493
    public function hookDisplayMpEditProfileTab($params)
    {
        if ('AdminSellerInfoDetail' == Tools::getValue('controller')) {
            $admin = 1;
        } else {
            $admin = 0;
        }
        $this->context->smarty->assign('is_admin', $admin);
        return $this->fetch('module:mpshipping/views/templates/hook/free_shipping_tab.tpl');
    }

    // customization By Amit Webkul uv_265493
    public function hookDisplayMpEditProfileTabContent($params)
    {
        $idSeller = Tools::getValue('id_seller');
        if ('AdminSellerInfoDetail' == Tools::getValue('controller')) {
            $admin = 1;
        } else {
            $admin = 0;
            $sellerInfo = WkMpSeller::getSellerByCustomerId($this->context->customer->id);
            if ($sellerInfo) {
                $idSeller = $sellerInfo['id_seller'];
            }
        }
        if ($idSeller) {
            $objFreeShipping = new MpShippingFreeShipping();
            $info = $objFreeShipping->getFreeShippingInfoByIdSeller($idSeller, true);
            if (!$info) {
                $info = array();
            }
            $objCurrency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
            $this->context->smarty->assign(
                array(
                    'id_seller' => $idSeller,
                    'currency_sign' => $objCurrency->symbol,
                    'weight_unit' => Configuration::get('PS_WEIGHT_UNIT'),
                    'shipping_free_info' => $info,
                    'is_admin' => $admin
                )
            );
        }
        return $this->fetch('module:mpshipping/views/templates/hook/free_shipping_tab_content.tpl');
    }

    // customization By Amit Kumar Tiwari uv_265493
    public function hookActionBeforeUpdateSeller($params)
    {
        if ($params['id_seller']) {
            $freeShippingPrice = Tools::getValue('free_shipping_start_price');
            $freeShippingWeight = Tools::getValue('free_shipping_start_weight');
            if (!isset($freeShippingPrice)) {
                $this->context->controller->errors[] = $this->l('Free shipping start at (price) is required field');
            } elseif (!Validate::isPrice($freeShippingPrice)) {
                $this->context->controller->errors[] = $this->l('Free shipping start at (price) is invalid');
            }
            if (!isset($freeShippingWeight)) {
                $this->context->controller->errors[] = $this->l('Free shipping start at (weight) is required field');
            } elseif (!Validate::isUnsignedFloat($freeShippingWeight)) {
                $this->context->controller->errors[] = $this->l('Free shipping start at (weight) is invalid');
            }
        }
    }

    // customization By Amit Kumar Tiwari uv_265493
    public function hookActionAfterUpdateSeller($params)
    {
        if ($params['id_seller']) {
            $freeShippingPrice = Tools::getValue('free_shipping_start_price');
            $freeShippingWeight = Tools::getValue('free_shipping_start_weight');
            if (!Validate::isPrice($freeShippingPrice)) {
                $freeShippingPrice = '0';
            }
            if (!Validate::isUnsignedFloat($freeShippingWeight)) {
                $freeShippingWeight = '0';
            }
            $objFreeShipping = new MpShippingFreeShipping();
            $info = $objFreeShipping->getFreeShippingInfoByIdSeller($params['id_seller'], false);
            if ($info) {
                $objFreeShipping = new MpShippingFreeShipping($info['id_mp_shipping_free_shipping']);
            }
            $objFreeShipping->free_shipping_start_price = $freeShippingPrice;
            $objFreeShipping->free_shipping_start_weight = $freeShippingWeight;
            $objFreeShipping->id_seller = $params['id_seller'];
            $objFreeShipping->save();
        }
    }

    // customization By Amit Kumar Tiwari uv_265493
    public function hookDisplayAfterCarrierMpSplit($params)
    {
        if ($idSeller = $params['id_seller']) {
            if ($idSeller) {
                $defaultCurrency = Configuration::get('PS_CURRENCY_DEFAULT');
                $objFreeShipping = new MpShippingFreeShipping();
                $shippinCostInfo = $objFreeShipping->getFreeShippingInfoByIdSeller($idSeller, true);
                $remainingAmountPrice = 0;
                $remainingAmountWeight = 0;
                $remainingAmountPriceDisplay = 0;
                if ($shippinCostInfo) {
                    $productList = $this->getProductsBySeller($idSeller);
                    if ($shippinCostInfo['free_shipping_start_price'] > 0) {
                        $remainingAmountPrice = $shippinCostInfo['free_shipping_start_price'];
                        $priceSellerProducts = $this->context->cart->getOrderTotal(
                            true,
                            Cart::BOTH_WITHOUT_SHIPPING,
                            $productList,
                            null,
                            false
                        );
                        $remainingAmountPrice = $remainingAmountPrice - $priceSellerProducts;
                        if ($remainingAmountPrice < 0) {
                            $remainingAmountPrice = 0;
                        }
                        $remainingAmountPrice = Tools::ConvertPriceFull(
                            $remainingAmountPrice,
                            new Currency($defaultCurrency),
                            $this->context->currency
                        );
                    }
                    $remainingAmountPriceDisplay = Tools::displayPrice($remainingAmountPrice, $this->context->currency);
                    $remainingAmountWeight = $shippinCostInfo['free_shipping_start_weight'];
                    $totalWeight = $this->context->cart->getTotalWeight($productList);
                    $remainingAmountWeight = $remainingAmountWeight - $totalWeight;
                    if ($remainingAmountWeight < 0) {
                        $remainingAmountWeight = 0;
                    }
                }
                $this->context->smarty->assign(
                    array(
                        'remaining_amount' => $remainingAmountPrice,
                        'remaining_amount_d' => $remainingAmountPriceDisplay,
                        'remaining_amount_weight' => $remainingAmountWeight,
                        'weight_unit' => Configuration::get('PS_WEIGHT_UNIT'),
                        'id_seller' => $idSeller
                    )
                );
                return $this->fetch('module:mpshipping/views/templates/hook/free_shipping_info.tpl');
            }
        }
    }

    // customization by Amit Kumar Tiwari uv_265493
    public function checkFreeShippingAllowed($idSeller, $type = 'price')
    {
        if ($idSeller) {
            $defaultCurrency = Configuration::get('PS_CURRENCY_DEFAULT');
            $objFreeShipping = new MpShippingFreeShipping();
            $shippinCostInfo = $objFreeShipping->getFreeShippingInfoByIdSeller($idSeller, true);
            $remainingAmountPrice = 0;
            $remainingAmountWeight = 0;
            if ($shippinCostInfo) {
                if ($type == 'price') {
                    if ($shippinCostInfo['free_shipping_start_price'] > 0) {
                        $remainingAmountPrice = $shippinCostInfo['free_shipping_start_price'];
                        $remainingAmountPrice = Tools::ConvertPriceFull(
                            $remainingAmountPrice,
                            new Currency($defaultCurrency),
                            $this->context->currency
                        );
                    } else {
                        $remainingAmountPrice == -1;
                    }
                    return $remainingAmountPrice;
                } elseif ($type == 'weight') {
                    $remainingAmountWeight = $shippinCostInfo['free_shipping_start_weight'];
                    if ($remainingAmountWeight > 0) {
                        $remainingAmountWeight = $remainingAmountWeight;
                    } else {
                        $remainingAmountWeight = -1;
                    }
                    return $remainingAmountWeight;
                }
            }
        }
        return 0;
    }

    // customization By Amit Kumar Tiwari uv_265493
    public function getProductsBySeller($idSeller)
    {
        if (isset($this->context->cart->id)) {
            $sellerWiseProdList = array();
            $products = $this->context->cart->getProducts();
            foreach ($products as $product) {
                $id_seller = MpShippingFreeShipping::getSellerIdByIdProd($product['id_product']);
                $id_seller = $id_seller ? $id_seller : 0;
                if ($id_seller == $idSeller) {
                    $sellerWiseProdList[] = $product;
                }
            }
            return $sellerWiseProdList;
        }
        return array();
    }

    public function hookDisplayMPMyAccountMenu()
    {
        $link = new Link();
        $idCustomer = $this->context->cookie->id_customer;
        if ($idCustomer) {
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            $this->context->smarty->assign('mpmenu', 0);

            if ($mpCustomerInfo && $mpCustomerInfo['active']) {
                $mpIdSeller = $mpCustomerInfo['id_seller'];
                if ($mpIdSeller) {
                    $mpShippingList = $link->getModuleLink('mpshipping', 'mpshippinglist');
                    $this->context->smarty->assign('mpshippinglist', $mpShippingList);

                    return $this->fetch('module:mpshipping/views/templates/hook/mpshipping_link.tpl');
                }
            }
        }
    }

    /**
     * [hookactionObjectCarrierUpdateAfter::  Runs when a prestashop carrier is updated changes status of mp shipping method as status changes form prestashop carries toggle and deactivetes the mp carrier when the carrier is deleted form the prestashop end.].
     *
     * @param [type] $params [description]
     *
     * @return [type] [description]
     */
    public function hookactionObjectCarrierUpdateAfter($params)
    {
        $statusCrr = Tools::getValue('statuscarrier');
        if (isset($statusCrr)) {
            $mpShippingId = MpShippingMethod::getMpShippingId($params['object']->id_reference);
            if ($mpShippingId) {
                $objMpShippingMet = new MpShippingMethod($mpShippingId);
                if ($params['object']->active == 0) {
                    $objMpShippingMet->active = 0;
                    $objMpShippingMet->save();
                    $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
                } else {
                    $objMpShippingMet->active = 1;
                    $objMpShippingMet->save();
                }
            } else {
                //if deactive any carrier from carrier list and if that carrier is exist in admin default shipping then remove this carrier from default list
                if (!$params['object']->active) {
                    //Only for admin carriers if deactivated
                    $adminDefaultShipping = unserialize(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));

                    if (($key = array_search($params['object']->id_reference, $adminDefaultShipping)) !== false) {
                        //remove deactivated carrier from admin default shipping
                        unset($adminDefaultShipping[$key]);
                    }

                    if ($adminDefaultShipping) {
                        //if any other admin exist then just remove deleted carrier from default carrier
                        Configuration::updateValue('MP_SHIPPING_ADMIN_DEFAULT', serialize($adminDefaultShipping));
                    } else {
                        //if no more carrier exist
                        //$objCarrier = new Carrier(Configuration::get('PS_CARRIER_DEFAULT'));
                        //$adminDefaultShipping = array($objCarrier->id_reference);
                        $adminDefaultShipping = array();
                        Configuration::updateValue('MP_SHIPPING_ADMIN_DEFAULT', serialize($adminDefaultShipping));
                    }

                    $objMpShippingMet = new MpShippingMethod();
                    $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
                }
            }
        }
        if (($params['object']->id_reference && $params['object']->deleted)) {
            $mpShippingId = MpShippingMethod::getMpShippingId($params['object']->id_reference);
            if ($mpShippingId) {
                $objMpShippingMet = new MpShippingMethod($mpShippingId);
                $objMpShippingMet->active = 0;
                $objMpShippingMet->id_ps_reference = 0;
                $objMpShippingMet->save();
                $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
            }
        }
    }

    public function hookDisplayMPMenuBottom()
    {
        $link = new Link();
        $idCustomer = $this->context->cookie->id_customer;
        if ($idCustomer) {
            /*$obj_seller_info = new WkMpSeller();*/
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            $this->context->smarty->assign('mpmenu', 1);

            if ($mpCustomerInfo) {
                $mpIdSeller = $mpCustomerInfo['id_seller'];
                if ($mpIdSeller) {
                    $mpShippingList = $link->getModuleLink('mpshipping', 'mpshippinglist');
                    $this->context->smarty->assign('mpshippinglist', $mpShippingList);

                    return $this->fetch('module:mpshipping/views/templates/hook/mpshipping_link.tpl');
                }
            }
        }
    }

    public function displayShippingMethods($mpIdSeller = false)
    {
        $idLang = $this->context->language->id;
        $allPsCarriersArr = MpShippingMethod::getOnlyPrestaCarriers($idLang);
        if ($mpIdSeller) {
            $objMpShippingMethod = new MpShippingMethod();
            $mpShippingData = $objMpShippingMethod->getMpShippingMethods($mpIdSeller);

            if ($mpShippingData) {
                foreach ($mpShippingData as $key => $value) {
                    $mpShippingData[$key]['id_carrier'] = $value['id_ps_reference'];
                }
            }

            if (Configuration::get('MP_SHIPPING_ADMIN_SELLER')) {
                if ($allPsCarriersArr) {
                    foreach ($allPsCarriersArr as $key => $value) {
                        $allPsCarriersArr[$key]['id'] = 0;
                        $allPsCarriersArr[$key]['mp_shipping_name'] = $value['name'];
                    }
                }

                if (!$mpShippingData) {
                    $mpShippingData = $allPsCarriersArr;
                } else {
                    $mpShippingData = array_merge($mpShippingData, $allPsCarriersArr);
                }
            }
            if ($mpShippingData) {
                $this->context->smarty->assign('mp_shipping_data', $mpShippingData);
            }
        }

        $this->context->smarty->assign('mp_module_dir', _MODULE_DIR_);

        $mpProductId = Tools::getValue('id_mp_product');
        if ($mpProductId) {
            $objMpShippingProductMap = new MpShippingProductMap();
            $mpShippingProdMapDetails = $objMpShippingProductMap->getMpShippingProductMapDetails($mpProductId);
            $mpShippingIdMap = array();
            $sellerObj = (array) new WkMpSellerProduct($mpProductId);
            $selectedCarriers = $sellerObj['ps_id_carrier_reference'];
            if ($selectedCarriers) {
                $selectedCarriers = unserialize($selectedCarriers);
            }

            $this->context->smarty->assign('mp_shipping_id_map', (array)$selectedCarriers);

            $this->context->smarty->assign('mp_product_id', $mpProductId);

            //check is mpvirtualproduct module install or not
            //if it install then check is product is virtual product or any simple product
            //if product is virtual product then we can not shown any shipping method
            $isMpInstall = Module::isInstalled('mpvirtualproduct');
            if ($isMpInstall) {
                include_once dirname(__FILE__).'/../mpvirtualproduct/classes/MarketplaceVirtualProduct.php';
                $objMvp = new MarketplaceVirtualProduct();
                $isVirtualProduct = $objMvp->isMpProductIsVirtualProduct($mpProductId);
                if (empty($isVirtualProduct)) {
                    return $this->display(__FILE__, 'addproduct_shipping.tpl');
                    //return $this->fetch('module:mpshipping/views/templates/hook/addproduct_shipping.tpl');
                }
            } else {
                return $this->display(__FILE__, 'addproduct_shipping.tpl');
                //return $this->fetch('module:mpshipping/views/templates/hook/addproduct_shipping.tpl');
            }
        } else {
            return $this->display(__FILE__, 'addproduct_shipping.tpl');
            //return $this->fetch('module:mpshipping/views/templates/hook/addproduct_shipping.tpl');
        }
    }

    public function hookActionAfterAssignProduct($params)
    {
        $objMpSellerProduct = new MpShippingMethod();
        $mpSellerShippingDetails = $objMpSellerProduct->getDefaultShippingBySellerId($params['id_seller']);
        if ($mpSellerShippingDetails) {
            $carriers = array();
            foreach ($mpSellerShippingDetails as $mpSellerProductDetail) {
                $objMpShippingProductMap = new MpShippingProductMap();
                $objMpShippingProductMap->mp_product_id = $params['mp_id_product'];
                $objMpShippingProductMap->mp_shipping_id = $mpSellerProductDetail['id'];
                $objMpShippingProductMap->id_ps_reference = $mpSellerProductDetail['id_ps_reference'];
                $objMpShippingProductMap->save();

                $carriers[] = $mpSellerProductDetail['id_ps_reference'];
            }
            $objProduct = new Product($params['id_product']);
            $objProduct->setCarriers($carriers);
        } else {
            //Remove shipping from seller product in prestashop catalog
            MpShippingProduct::deletePsProductCarrier($params['id_product']);

            //set carrier using carrier reference
            $objMpShippingProductMap = new MpShippingProductMap();
            $objMpShippingProductMap->assignAdminDefaultCarriersToSellerProduct($params['id_product']);
        }
    }

    /**
     * [hookActionAfterAddMPProduct - add shipping method on seller product.].
     *
     * @param [type] $params [description]
     *
     * @return [type] [description]
     */
    public function hookActionAfterAddMPProduct($params)
    {
        $this->assignShippingOnProduct($params, 0);
    }

    /**
     * [hookActionAfterUpdateMPProduct - run at the time when seller update product].
     *
     * @param [type] $params [description]
     *
     * @return [type] [description]
     */
    public function hookActionAfterUpdateMPProduct($params)
    {
        $this->assignShippingOnProduct($params, 1);
    }

    public function assignShippingOnProduct($params, $updateProduct)
    {
        $marketplaceProductId = $params['id_mp_product'];
        if ($marketplaceProductId) {
            $objMpSellerProductDetail = new WkMpSellerProduct($marketplaceProductId);
            $mpIdSeller = $objMpSellerProductDetail->id_seller;
            $psProductId = $objMpSellerProductDetail->id_ps_product;
            $productStatus = $objMpSellerProductDetail->active;

            //check if product choose as virtual product
            $isVirtualProduct = Tools::getValue('mp_is_virtual');
            if (!$isVirtualProduct) {
                $objMpShippingProductMap = new MpShippingProductMap();
                if ($updateProduct) {
                    //on update product hook
                    $objMpShippingProductMap->deleteMpShippingProductMapDetails($marketplaceProductId);
                }

                $psIDCarrierReference = 0;  // No Shipping Selected By Admin
                $carriers = array();
                $carrierRefs = array();
                $mpShippingCarrier = Tools::getValue('carriers');

                if (isset($mpShippingCarrier) && !empty($mpShippingCarrier)) {
                    //if seller select any carrier from list
                    foreach ($mpShippingCarrier as $mpShippingId) {
                        $mpShippingIdNew = (int) MpShippingMethod::getMpShippingId($mpShippingId);
                        $shippingSellerId = MpShippingMethod::getSellerIdByMpShippingId($mpShippingIdNew);
                        if ($mpShippingIdNew) {
                            if ($shippingSellerId == $mpIdSeller) {
                                $idPsReference = MpShippingMethod::getReferenceByMpShippingId($mpShippingIdNew);

                                //add shipping method to product and save a map in to table
                                $objMpShippingProductMap->mp_shipping_id = $mpShippingIdNew;
                                $objMpShippingProductMap->id_ps_reference = $idPsReference;
                                $objMpShippingProductMap->mp_product_id = $marketplaceProductId;
                                $objMpShippingProductMap->add();

                                $carriers[] = $idPsReference;
                                $carrierRefs[] = $idPsReference;
                            }
                        } else {
                            $carriers[] = $mpShippingId;
                            $objCarr = new Carrier($mpShippingId);
                            $carrierRefs[] = $objCarr->id_reference;
                        }
                    }

                    //check if product active then we need to update main product too
                    if ($productStatus == 1) {
                        if ($psProductId) {
                            $objProduct = new Product($psProductId);
                            $objProduct->setCarriers($carrierRefs);
                            $objProduct->save();
                        }
                    }

                    if ($carriers) {
                        $psIDCarrierReference = serialize($carriers);
                    }
                } else {
                    //if seller does not select any carrier from list then admin default shipping will apply
                    if ($updateProduct) {
                        if ($psProductId) {
                            //Remove shipping from seller product in prestashop catalog
                            Mpshippingproduct::deletePsProductCarrier($psProductId);
                        }
                    }

                    if ($productStatus == 1) {
                        if ($psProductId) {
                            //set carrier using carrier reference
                            $objMpShippingProductMap->assignAdminDefaultCarriersToSellerProduct($psProductId);
                        }
                    }

                    if (Configuration::get('MP_SHIPPING_ADMIN_DEFAULT')) {
                        $psIDCarrierReference = Configuration::get('MP_SHIPPING_ADMIN_DEFAULT');
                    }
                }


                $objMpSellerProductDetail->ps_id_carrier_reference = $psIDCarrierReference;
                $objMpSellerProductDetail->update();
            }
        }
    }

    /**
     * Assign all shipping on presashop product when admin activate seller product
     *
     * @param [type] $params [description]
     *
     * @return [type] [description]
     */
    public function hookActionToogleMPProductActive($params)
    {
        if ($mpProductId = $params['id_mp_product']) {
            $sellerProduct = WkMpSellerProduct::getSellerProductByIdProduct($mpProductId);
            if ($sellerProduct && $sellerProduct['id_ps_product']) {
                $psProductId = $sellerProduct['id_ps_product'];
                $carriers = array();

                $objMpShippingProductMap = new MpShippingProductMap();
                $mpShippingProductMapDetails = $objMpShippingProductMap->getMpShippingProductMapDetails($mpProductId);
                if ($mpShippingProductMapDetails) {
                    foreach ($mpShippingProductMapDetails as $mpShippingProduct) {
                        $carriers[] = $mpShippingProduct['id_ps_reference'];
                    }

                    $objProduct = new Product($psProductId);
                    $objProduct->setCarriers($carriers);
                } else {
                    //set carrier using carrier reference
                    $objMpShippingProductMap->assignAdminDefaultCarriersToSellerProduct($psProductId);
                }
            }
        }
    }

    public function hookDisplayMpProductListTop($params)
    {
        $idCustomer = $this->context->cookie->id_customer;
        if ($idCustomer) {
            $link = new Link();
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            $mpIdSeller = $mpCustomerInfo['id_seller'];
            if ($mpIdSeller) {
                $objMpShippingMethod = new MpShippingMethod();
                $mpShippingData = $objMpShippingMethod->getMpShippingMethods($mpIdSeller);
                if ($mpShippingData) {
                    foreach ($mpShippingData as $key => $value) {
                        $mpShippingData[$key]['id_carrier'] = $value['id_ps_reference'];
                    }
                }
                $idLang = $this->context->language->id;
                $allPsCarriersArr = MpShippingMethod::getOnlyPrestaCarriers($idLang);
                if (Configuration::get('MP_SHIPPING_ADMIN_SELLER')) {
                    if ($allPsCarriersArr) {
                        foreach ($allPsCarriersArr as $key => $value) {
                            $allPsCarriersArr[$key]['id'] = 0;
                            $allPsCarriersArr[$key]['mp_shipping_name'] = $value['name'];
                        }
                    }
                    if (!$mpShippingData) {
                        $mpShippingData = $allPsCarriersArr;
                    } else {
                        $mpShippingData = array_merge($mpShippingData, $allPsCarriersArr);
                    }
                }
                if ($mpShippingData) {
                    //Assign shipping button will display only if atleast one shipping will be active
                    $this->context->smarty->assign('shipping_method', $mpShippingData);
                    $this->context->smarty->assign('ajax_link', $link->getModuleLink('mpshipping', 'assignshippingforall'));
                    $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
                    $this->context->smarty->assign('mp_id_seller', $mpIdSeller);

                    return $this->fetch('module:mpshipping/views/templates/hook/assign_shipping_method.tpl');
                }
            }
        }
    }

    /**
     * Add tracking number on seller order which has seller shipping (Display Tab heading)
     *
     * @param array $params
     * @return void
     */
    public function hookdisplayOrderDetailsExtraTab($params)
    {
        if ($this->checkMarketplaceVersion()) {
            if ($idOrder = $params['id_order']) {
                $objOrder = new Order($idOrder);
                if (Validate::isLoadedObject($objOrder)) {
                    return $this->fetch('module:mpshipping/views/templates/hook/mporder_tracking_tab.tpl');
                }
            }
        }
    }

    /**
     * Add tracking number on seller order which has seller shipping (Display Tab content)
     *
     * @param array $params
     * @return void
     */
    public function hookDisplayOrderDetailsExtraTabContent($params)
    {
        if ($this->checkMarketplaceVersion()) {
            if ($idOrder = $params['id_order']) {
                $objOrder = new Order($idOrder);
                if (Validate::isLoadedObject($objOrder)) {
                    $objCarrier = new Carrier($objOrder->id_carrier);
                    $objMpShipping = new MpShippingMethod();
                    if ($objMpShipping->isSellerShippingByIdReference($objCarrier->id_reference)) {
                        $this->context->smarty->assign(
                            array(
                                'update_trackingNumber_action' => $this->context->link->getModuleLink(
                                    'mpshipping',
                                    'mpshippinglist',
                                    array(
                                        'id_order' => $idOrder,
                                        'id_order_carrier' => $objMpShipping->getCarrierOrderInfoByIdOrder($idOrder)
                                    )
                                ),
                            )
                        );

                        if ($objOrder->shipping_number) {
                            $this->context->smarty->assign('shipping_number', $objOrder->shipping_number);
                        }
                    }
                    return $this->fetch('module:mpshipping/views/templates/hook/mporder_tracking_content.tpl');
                }
            }
        }
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        if ('productlist' === Tools::getValue('controller')) {
            $this->context->controller->registerStylesheet(
                'assignshipping-css',
                'modules/'.$this->name.'/views/css/assignshipping.css'
            );

            $jsDef = array(
                'check_msg' => $this->l('Please select any shipping method first'),
                'success_msg' => $this->l('Shipping assigned successfully to all the products'),
                'error_msg' => $this->l('Some error occurs, try again later'),
            );

            Media::addJsDef($jsDef);

            $this->context->controller->registerJavascript(
                'assignshipping-js',
                'modules/'.$this->name.'/views/js/assignshipping.js'
            );
        } elseif ('mporderdetails' === Tools::getValue('controller')) {
            if (Tools::getValue('update_tracking_success') || Tools::getValue('invalid_tracking_number')) {
                $this->context->controller->registerJavascript(
                    'mporder-tracking-js',
                    'modules/'.$this->name.'/views/js/mporder-tracking.js'
                );
            }
        }
    }

    public function hookDisplayAdminPsSellerOrderViewHead($params)
    {
        return $this->fetch('module:mpshipping/views/templates/hook/mporder_admin_tracking_head.tpl');
    }

    public function hookDisplayAdminPsSellerOrderViewBody($params)
    {
        if ($idOrder = Tools::getValue('id_order')) {
            $objOrder = new Order($idOrder);
            if (Validate::isLoadedObject($objOrder)) {
                $objCarrier = new Carrier($objOrder->id_carrier);
                $objMpShipping = new MpShippingMethod();
                if ($objMpShipping->isSellerShippingByIdReference($objCarrier->id_reference)) {
                    if ($objOrder->shipping_number) {
                        $this->context->smarty->assign('shipping_number', $objOrder->shipping_number);
                    }
                }
            }
        }
        return $this->fetch('module:mpshipping/views/templates/hook/mporder_admin_tracking_body.tpl');
    }

    public function hookActionBeforeAddMPProduct($params)
    {
        return $this->restrictProductToAddUpdate($params);
    }

    public function hookActionBeforeUpdateMPProduct($params)
    {
        return $this->restrictProductToAddUpdate($params);
    }

    public function restrictProductToAddUpdate($params)
    {
        if (!Tools::getValue('carriers') && empty(unserialize(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT')))) {
            //Seller doesn't select any shipping and admin doesn't set any default shipping
            $this->context->controller->errors[] = $this->l('Admin default shipping is not available so you can not save this product');
        }
    }

    public function hookDisplaySellerShipping()
    {
        $mpIdSeller = false;
        $mpProductId = Tools::getValue('id_mp_product');
        if (!$mpProductId) {
            //Add new product
            if (Tools::getValue('controller') != 'AdminSellerProductDetail') {
                if ($this->context->customer && $this->context->customer->id) {
                    $idCustomer = $this->context->customer->id;
                    $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
                    if ($mpCustomerInfo) {
                        $mpIdSeller = $mpCustomerInfo['id_seller'];
                    }
                }
            } else {
                $this->context->smarty->assign('mp_module_dir', _MODULE_DIR_);
                $this->context->smarty->assign('is_admin_controller', 1);
            }
        } else {
            //Update product
            $idSeller = WkMpSellerProduct::getSellerProductByIdProduct($mpProductId);
            $mpIdSeller = $idSeller['id_seller'];
            $this->context->smarty->assign('is_admin_controller', 0);
        }

        //Get admin default carriers name
        if (Configuration::get('MP_SHIPPING_ADMIN_DEFAULT')) {
            $allCarrierNames = array();
            $adminDefShipping = unserialize(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));
            if ($adminDefShipping) {
                foreach ($adminDefShipping as $idCarrier) {
                    if ($carrierData = Carrier::getCarrierByReference($idCarrier)) {
                        $allCarrierNames[] = $carrierData->name;
                    }
                }
            }

            $this->context->smarty->assign('allCarrierNames', $allCarrierNames);
        }

        return $this->displayShippingMethods($mpIdSeller);
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($params['object']->id) {
            $newLangId = $params['object']->id;

            //Assign all lang's main table in an ARRAY
            $langTables = array('mp_shipping_method');

            //If Admin create any new language when we do entry in module all lang tables.
            WkMpHelper::updateIdLangInLangTables($newLangId, $langTables);
        }
    }

    public function callCarrierDistribution($distributeType, $list)
    {
        $optionsDistributeType = array(
            'admin' => $this->l('Admin'),
            'seller' => $this->l('Seller'),
            'both' => $this->l('Both (on the basis of commission rate)'),
        );

        $html = '';
        $html .= '<select name="distribute_type" class="distribute_type" data-id-ps-reference="'.$list['id_reference'].'">';
        foreach ($optionsDistributeType as $typeKey => $typeVal) {
            $html .= '<option value="'.$typeKey.'"';
            if ($distributeType == $typeKey) {
                $html .= 'selected = "selected"';
            }
            $html .= '>'.$typeVal.'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if ($this->checkMarketplaceVersion()) {
            if ('AdminCarriers' === Tools::getValue('controller')) {
                //Change shipping distribution type of admin carriers
                $jsDef = array(
                    'path_admin_mp_shipping' => $this->context->link->getAdminLink('AdminMpSellerShipping'),
                    'success_msg' => $this->l('Updated Successfully'),
                    'error_msg' => $this->l('Some error occured...'),
                );

                Media::addJsDef($jsDef);

                $this->context->controller->addJs($this->_path.'views/js/change-shipping-distribution.js');
            }
        }
    }

    /**
    * If admin delete seller then seller's all shipping will be deleted from mpshipping table and ps carriers
    *
    * @param $params [Seller Id]
    *
    * @return bool
    */
    public function hookActionMpSellerDelete($params)
    {
        if ($idSeller = $params['id_seller']) {
            if ($sellerShipping = MpShippingMethod::getSellerAllShippingMethod($idSeller)) {
                foreach ($sellerShipping as $shipping) {
                    $objMpShipping = new MpShippingMethod();
                    $objMpShipping->deleteMpShipping($shipping['id']);
                }
            }
            // customization By Amit Kumar Tiwari Webkul uv_265493
            $objFreeShipp = new MpShippingFreeShipping();
            $objFreeShipp->deleteFreeShippingInfoByIdSeller($idSeller);
        }
    }

    public function callInstallTab()
    {
        $this->installTab('AdminMpSellerShipping', 'Manage Seller Shipping', 'AdminMarketplaceManagement');

        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        //creating tab in admin within marketplace tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function install()
    {
        $objModuleDb = new MpShippingDb();

        if (!parent::install()
            || !$objModuleDb->createTables()
            || !$this->callInstallTab()
            || !$this->setMpConfigurationVariables()
            || !$this->registerMpModuleHook()
            || !$this->createMailLangDirectoryWithFiles()
            ) {
            return false;
        }

        return true;
    }

    public function registerMpModuleHook()
    {
        return $this->registerHook(array(
                'displayMpaddproducttabhook', 'displayMpupdateproducttabhook', 'displayMPMyAccountMenu',
                'displayMPMenuBottom', 'actionAfterAddMPProduct', 'actionAfterUpdateMPProduct',
                'actionToogleMPProductActive', 'actionObjectLanguageAddAfter', 'displayMpProductListTop',
                'actionObjectCarrierUpdateAfter', 'displaySellerShipping', 'actionAfterAssignProduct',
                'actionBeforeAddproduct', 'actionBeforeUpdateproduct', 'actionFrontControllerSetMedia',
                'actionAdminCarriersListingFieldsModifier', 'actionAdminControllerSetMedia', 'actionMpSellerDelete',
                'displayOrderDetailsExtraTab', 'displayOrderDetailsExtraTabContent', 'displayAdminPsSellerOrderViewHead',
                'displayAdminPsSellerOrderViewBody', 'actionBeforeAddMPProduct', 'actionBeforeUpdateMPProduct',
                'actionBeforeAddMPProduct', 'actionBeforeUpdateMPProduct',
                'actionBeforeAddMPProduct', 'actionBeforeUpdateMPProduct', 'displayMpEditProfileTab', 'displayMpEditProfileTabContent', 'actionMpSellerDelete', 'actionBeforeUpdateSeller', 'actionAfterUpdateSeller', 'displayAfterCarrierMpSplit'
            ));
    }

    /**
     * Ps all imported language's Mail directory will be created with all files in module's mails folder
     *
     * @return tpl
     */
    public function createMailLangDirectoryWithFiles()
    {
        $mailEnDir = _PS_MODULE_DIR_.'mpshipping/mails/en/';
        if (is_dir($mailEnDir)) {
            $allFiles = scandir($mailEnDir);
            $allLanguages = Language::getLanguages(false, $this->context->shop->id);
            if ($allLanguages) {
                $moduleMailDir = _PS_MODULE_DIR_.'mpshipping/mails/';
                foreach ($allLanguages as $language) {
                    $langISO = $language['iso_code'];
                    //Ignore 'en' and 'fr' directory because we already have this in our module folder
                    if ($langISO != 'en' && $langISO != 'fr') {
                        //create lang dir if not exist in module mails directory
                        if (!file_exists($moduleMailDir.$langISO)) {
                            @mkdir($moduleMailDir.$langISO, 0777, true);
                        }

                        //Now if lang dir is exist or created by above code
                        if (is_dir($moduleMailDir.$langISO)) {
                            foreach ($allFiles as $fileName) {
                                if ($fileName != '.' && $fileName != '..') {
                                    $source = $mailEnDir.$fileName;
                                    $destination = $moduleMailDir.$langISO.'/'.$fileName;
                                    //if file not exist in desti directory then create that file
                                    if (!file_exists($destination) && file_exists($source)) {
                                        Tools::copy($source, $destination);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public function setMpConfigurationVariables()
    {
        //Assign Admin first shipping as Default shipping of Admin
        $idLang = $this->context->language->id;
        $allPsCarriersArr = MpShippingMethod::getOnlyPrestaCarriers($idLang);
        $carrRef = array();
        if ($allPsCarriersArr) {
            foreach ($allPsCarriersArr as $carrier) {
                //first element of array for first shipping
                $carrRef[] = $carrier['id_reference'];
            }
        }
        if (!empty($carrRef)) {
            $adminDefShipping = serialize($carrRef);
        } else {
            $adminDefShipping = 0;
        }

        if (!Configuration::updateValue('MP_SHIPPING_ADMIN_DEFAULT', $adminDefShipping)
            || !Configuration::updateValue('MP_SHIPPING_ADMIN_APPROVE', 1)
            || !Configuration::updateValue('MP_MAIL_ADMIN_SHIPPING_ADDED', 1)
            || !Configuration::updateValue('MP_MAIL_SELLER_SHIPPING_APPROVAL', 1)
            || !Configuration::updateValue('MP_SHIPPING_ADMIN_SELLER', 1)
            || !Configuration::updateValue('MP_SHIPPING_DISTRIBUTION_ALLOW', 1)
            || !Configuration::updateValue('MP_SHIPPING_ADMIN_DISTRIBUTION', 1)
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
                if (!$moduleTab->delete()) {
                    return false;
                }
            }
        }

        return true;
    }

    public function deleteConfigKeys()
    {
        $var = array(
                'MP_SHIPPING_ADMIN_DEFAULT', 'MP_SHIPPING_DISTRIBUTION_ALLOW', 'MP_SHIPPING_ADMIN_DISTRIBUTION',
                'MP_SHIPPING_ADMIN_APPROVE', 'MP_MAIL_ADMIN_SHIPPING_ADDED', 'MP_MAIL_SELLER_SHIPPING_APPROVAL',
                'MP_SHIPPING_ADMIN_SELLER'
            );

        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function deleteCarriersFromPS()
    {
        $objMpShipping = new MpShippingMethod();
        $idPsReference = $objMpShipping->getAllReferenceId();
        if ($idPsReference) {
            foreach ($idPsReference as $carrRef) {
                $idPsCarrier = MpShippingMethod::getCarrierIdByReference($carrRef['id_ps_reference']);
                $objCarrier = new Carrier($idPsCarrier);
                $isDeleted = $objCarrier->delete();
                if (!$isDeleted) {
                    return false;
                }
            }
        }

        return true;
    }

    public function uninstall()
    {
        $objModuleDb = new MpShippingDb();

        if (!parent::uninstall()
            || !$this->deleteCarriersFromPS()
            || !$objModuleDb->deleteTables()
            || !$this->deleteConfigKeys()
            || !$this->uninstallTab()) {
            return false;
        }

        return true;
    }
}
