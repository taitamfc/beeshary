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

class MarketplaceMpOrderDetailsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $objMpOrder = new WkMpSellerOrder();
        $objMpOrderDetail = new WkMpSellerOrderDetail();

        if (isset($this->context->customer->id)) {
            $editOrderPermission = 1;
            $idCustomer = $this->context->customer->id;
            //Override customer id if any staff of seller want to use this controller
            if (Module::isEnabled('mpsellerstaff')) {
                $staffDetails = WkMpSellerStaff::getStaffInfoByIdCustomer($idCustomer);
                if ($staffDetails
                    && $staffDetails['active']
                    && $staffDetails['id_seller']
                    && $staffDetails['seller_status']
                ) {
                    $staffTabDetails = WkMpTabList::getStaffPermissionWithTabName(
                        $staffDetails['id_staff'],
                        $this->context->language->id,
                        WkMpTabList::MP_ORDER_TAB
                    );
                    if ($staffTabDetails) {
                        //For edit order permission
                        $editOrderPermission = $staffTabDetails['edit'];
                    }
                }

                $getCustomerId = WkMpSellerStaff::overrideMpSellerCustomerId($idCustomer);
                if ($getCustomerId) {
                    $idCustomer = $getCustomerId;
                }
            }

            $seller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($seller && $seller['active']) {
                if ($idOrder = Tools::getValue('id_order')) {
                    $order = new Order($idOrder);
                    $idCurrency = (int) $order->id_currency;

                    $mpOrderDetails = $objMpOrderDetail->getSellerOrderDetail($idOrder, $this->context->language->id);
                    if ($mpOrderDetails) {
                        $orderProduct = $objMpOrderDetail->getSellerProductFromOrder($idOrder, $idCustomer);
                        if ($orderProduct) {
                            // Set voucher details
                            $this->setVoucherDetails($idOrder, $seller['id_seller'], $idCurrency);

                            $productTotal = 0;
                            $sellerTotal = 0;
                            $adminTotal = 0;

                            $taxBreakDown = array();
                            foreach ($orderProduct as &$product) {
                                $productTotal += $product['total_price_tax_incl'];
                                $sellerAmount = $product['seller_amount'] + $product['seller_tax'];
                                $sellerTotal += $sellerAmount;

                                $adminAmount = $product['admin_commission'] + $product['admin_tax'];
                                $adminTotal += $adminAmount;

                                $product['admin_commission_formatted'] = Tools::displayPrice(
                                    $product['admin_commission'],
                                    $idCurrency
                                );
                                $product['seller_amount_formatted'] = Tools::displayPrice(
                                    $product['seller_amount'],
                                    $idCurrency
                                );

                                $product['seller_total_amount'] = Tools::displayPrice($sellerAmount, $idCurrency);
                                $product['admin_total_commission'] = Tools::displayPrice($adminAmount, $idCurrency);

                                $product['unit_price_tax_excl'] = Tools::displayPrice(
                                    $product['unit_price_tax_excl'],
                                    $idCurrency
                                );
                                $product['unit_price_tax_incl'] = Tools::displayPrice(
                                    $product['unit_price_tax_incl'],
                                    $idCurrency
                                );

                                $product['total_price_tax_incl_formatted'] = Tools::displayPrice(
                                    $product['total_price_tax_incl'],
                                    $idCurrency
                                );

                                $product['price_ti'] = Tools::displayPrice($product['price_ti'], $idCurrency);
                                $product['price_te'] = Tools::displayPrice($product['price_te'], $idCurrency);

                                $product['total_tax'] = Tools::displayPrice(
                                    $product['seller_tax']+$product['admin_tax'],
                                    $idCurrency
                                );

                                $product['rate'] = Tools::ps_round(
                                    $objMpOrderDetail->getTaxRateByIdOrderDetail($product['id_order_detail']),
                                    2
                                );
                                $product['seller_tax'] = Tools::displayPrice($product['seller_tax'], $idCurrency);
                                $product['admin_tax'] = Tools::displayPrice($product['admin_tax'], $idCurrency);

                                $taxBreakDown[$product['id_order_detail']]['rate'] = Tools::ps_round(
                                    $objMpOrderDetail->getTaxRateByIdOrderDetail($product['id_order_detail']),
                                    2
                                );
                                $taxBreakDown[$product['id_order_detail']]['seller_tax'] = Tools::displayPrice(
                                    $product['seller_tax'],
                                    $idCurrency
                                );
                                $taxBreakDown[$product['id_order_detail']]['admin_tax'] = Tools::displayPrice(
                                    $product['admin_tax'],
                                    $idCurrency
                                );

                                $product['commission_rate'] = Tools::ps_round($product['commission_rate'], 2);
                            }

                            // get addresses
                            $this->mpOrderAddressDetails($idOrder);

                            // get order status
                            $this->shippingProcess($seller);

                            // get order reference
                            $order = new Order($idOrder);
                            if (Validate::isLoadedObject($order)) {
                                $this->context->smarty->assign('reference', $order->reference);
                            }

                            //Get shipping name of this order
                            if ($order->id_carrier) {
                                $objCarrier = new Carrier($order->id_carrier);
                                $this->context->smarty->assign('order_shipping_name', $objCarrier->name);
                            }

                            $sellerOrderTotal = $objMpOrder->getTotalOrder($idOrder, $idCustomer);
                            if ($sellerOrderTotal) {
                                //Add shipping amount in total orders
                                if ($sellerShippingEarning = WkMpAdminShipping::getSellerShippingByIdOrder($idOrder, $idCustomer)) {
                                    $this->context->smarty->assign(
                                        'seller_shipping_earning',
                                        Tools::displayPrice($sellerShippingEarning, $idCurrency)
                                    );

                                    $sellerOrderTotal += $sellerShippingEarning;
                                }
                            }

                            $this->context->smarty->assign(array(
                                'editOrderPermission' => $editOrderPermission,
                                'order_products' => $orderProduct,
                                'mp_total_order' => Tools::displayPrice($sellerOrderTotal, $idCurrency),
                                'mp_order_details' => $mpOrderDetails,
                                'is_seller' => '1',
                                'logic' => 4,
                                'wkself' => dirname(__FILE__),
                                'admin_commission_total' => Tools::displayPrice($adminTotal, $idCurrency),
                                'seller_total' => Tools::displayPrice($sellerTotal, $idCurrency),
                                'product_total' => Tools::displayPrice($productTotal, $idCurrency),
                                'taxBreakDown' => $taxBreakDown,
                                'id_order' => $idOrder,
                            ));

                            $this->setTemplate('module:marketplace/views/templates/front/order/mporderdetails.tpl');
                        } else {
                            Tools::redirect(__PS_BASE_URI__.'pagenotfound');
                        }
                    } else {
                        Tools::redirect(__PS_BASE_URI__.'pagenotfound');
                    }
                } else {
                    Tools::redirect(__PS_BASE_URI__.'pagenotfound');
                }
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function postProcess()
    {
        if ($this->context->customer->id) {
            $idCustomer = $this->context->customer->id;
            //Override customer id if any staff of seller want to use this controller
            if (Module::isEnabled('mpsellerstaff')) {
                $getCustomerId = WkMpSellerStaff::overrideMpSellerCustomerId($idCustomer);
                if ($getCustomerId) {
                    $idCustomer = $getCustomerId;
                }
            }

            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            $objOrderStatus = new WkMpSellerOrderStatus();
            $idOrder = Tools::getValue('id_order');
            $order = new Order($idOrder);
            if ($mpSeller && $mpSeller['active']) {
                if (Tools::isSubmit('submitState')) {
                    $idOrderState = Tools::getValue('id_order_state');
                    if (!$idOrderState) {   // seller just update the status without selecting other
                        $idOrderState = Tools::getValue('id_order_state_checked');
                    }

                    $products = $order->getProducts();
                    if ($products) {
                        $flag = true;
                        foreach ($products as $prod) {
                            $isProductSeller = WkMpSellerProduct::checkPsProduct(
                                $prod['product_id'],
                                $mpSeller['id_seller']
                            );
                            if (!$isProductSeller) {
                                $flag = false;
                                break;
                            }
                        }
                    }
                    $oldOs = $objOrderStatus->getCurrentOrderState($idOrder, $mpSeller['id_seller']);
                    if ($oldOs == $idOrderState) {
                        $this->errors[] = $this->module->l('The new order status is invalid.', 'mporderdetails');
                    }
                    if (empty($this->errors)) {
                        $isUpdated = true;
                        $objOrderStatus->processSellerOrderStatus($idOrder, $mpSeller['id_seller'], $idOrderState);
                        if ($flag) {    // this order is belong to only current seller
                            $isUpdated = $objOrderStatus->updateOrderByIdOrderAndIdOrderState($idOrder, $idOrderState);
                        }

                        // If sellers change their order status as cancelled - This feature will run through hookActionOrderStatusPostUpdate on marketplace class

                        if ($isUpdated) {
                            Hook::exec('actionAfterSellerOrderStatusUpdate', array(
                                'id_seller' => $mpSeller['id_seller'],
                                'id_order' => $idOrder,
                                'id_order_state' => $idOrderState
                            ));

                            //To manage staff log (changes add/update/delete)
                            WkMpHelper::setStaffHook(
                                $this->context->customer->id,
                                Tools::getValue('controller'),
                                $idOrder,
                                2
                            ); // 2 for Add action

                            Tools::redirect($this->context->link->getModuleLink('marketplace', 'mporderdetails', array('id_order' => $idOrder, 'is_order_state_updated' => 1)));
                        } else {
                            Tools::redirect($this->context->link->getModuleLink('marketplace', 'mporderdetails', array('id_order' => $idOrder)));
                        }
                    }
                }
            } else {
                Tools::redirect($this->context->link->getPageLink('my-account'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function setVoucherDetails($idOrder, $seller, $idCurrency)
    {
        WkMpSellerOrderDetail::setVoucherDetails($idOrder, $seller, $idCurrency, true);
    }

    public function shippingProcess($seller)
    {
        $idOrder = Tools::getValue('id_order');
        $objOrderStatus = new WkMpSellerOrderStatus();
        $order = new Order($idOrder);
        $history = $objOrderStatus->getHistory($this->context->language->id, $seller['id_seller'], $idOrder);
        if (!$history) {
            $history = $order->getHistory($this->context->language->id);
        }
        foreach ($history as &$orderState) {
            $orderState['text-color'] = Tools::getBrightness($orderState['color']) < 128 ? 'white !important' : 'black !important';
        }

        // process only those order status which allowed by admin
        $sellerOrderStatus = Configuration::get('WK_MP_SELLER_ORDER_STATUS_ACCESS');
        $status = '';
        if ($sellerOrderStatus) {
            $sellerOrderStatus = Tools::jsonDecode($sellerOrderStatus);
            if ($status = OrderState::getOrderStates($this->context->language->id)) {
                foreach ($status as $key => $state) {
                    if (!in_array($state['id_order_state'], $sellerOrderStatus)) {
                        unset($status[$key]);
                    }
                }
            }
        }

        $this->context->smarty->assign(array(
                'update_url_link' => $this->context->link->getModuleLink('marketplace', 'mporderdetails', array('id_order' => $idOrder)),
                'states' => $status,
                'current_id_lang' => $this->context->language->id,
                'order' => $order,
                'history' => $history,
                'currentState' => $objOrderStatus->getCurrentOrderState($idOrder, $seller['id_seller']),
                'img_url' => _PS_IMG_,
                'is_order_state_updated' => Tools::getValue('is_order_state_updated'),
            ));
    }

    public function mpOrderAddressDetails($idOrder)
    {
        $idLang = Context::getContext()->language->id;
        $order = new Order($idOrder);
        $customer = new Customer($order->id_customer);
        $addressInvoice = new Address($order->id_address_invoice, $idLang);
        if (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state) {
            $invoiceState = new State((int) $addressInvoice->id_state);
        }
        $invoiceFormat = AddressFormat::generateAddress($addressInvoice, array(), '<br />');

        if ($order->id_address_invoice == $order->id_address_delivery) {
            $addressDelivery = $addressInvoice;
            if (isset($invoiceState)) {
                $deliveryState = $invoiceState;
            }
        } else {
            $addressDelivery = new Address($order->id_address_delivery, $idLang);
            if (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state) {
                $deliveryState = new State((int) ($addressDelivery->id_state));
            }
        }
        $deliveryFormat = AddressFormat::generateAddress($addressDelivery, array(), '<br />');

        $this->context->smarty->assign(array(
            'customer_addresses' => $customer->getAddresses($idLang),
            'addresses' => array(
                'delivery' => $addressDelivery,
                'deliveryFormat' => $deliveryFormat,
                'deliveryState' => isset($deliveryState) ? $deliveryState : null,
                'invoice' => $addressInvoice,
                'invoiceFormat' => $invoiceFormat,
                'invoiceState' => isset($invoiceState) ? $invoiceState : null,
                ),
            ));
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUI('ui.datepicker');
        $this->registerStylesheet('marketplace_account', 'modules/'.$this->module->name.'/views/css/marketplace_account.css');
    }
}
