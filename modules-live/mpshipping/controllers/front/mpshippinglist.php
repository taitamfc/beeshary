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

class MpShippingMpShippingListModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpCustomerInfo && $mpCustomerInfo['active']) {
                $idSeller = $mpCustomerInfo['id_seller'];
                $objMpShipping = new MpShippingMethod();
                $objShippingProd = new MpShippingProductMap();

                //Get other shipping method before deleting
                $deleteAction = Tools::getValue('delete_action');
                if ($deleteAction) {
                    $mpShippingId = Tools::getValue('mpshipping_id');
                    die($mpShippingId);
                }

                //if shipping is assigned on product and going to delete
                if (Tools::isSubmit('submit_extra_shipping')) {
                    $oldShippingId = Tools::getValue('delete_shipping_id');
                    $objMpShippingNew = new MpShippingMethod($oldShippingId);
                    if ($objMpShippingNew->mp_id_seller == $idSeller) {
                        $newShippingId = Tools::getValue('extra_shipping');
                        $mpProdMap = $objShippingProd->getMpShippingForProducts($oldShippingId);
                        $carrierArr = array();
                        if (isset($newShippingId) && $newShippingId) {
                            $carrierArr[] = MpShippingMethod::getReferenceByMpShippingId($newShippingId);
                            if ($mpProdMap) {
                                foreach ($mpProdMap as $mpProd) {
                                    $objSellerProd = new WkMpSellerProduct($mpProd['mp_product_id']);
                                    $psProductId = $objSellerProd->id_ps_product;
                                    if ($psProductId) {
                                        $objProduct = new Product($psProductId);
                                        $objProduct->setCarriers($carrierArr);
                                    }
                                }
                                //update shipping product with new shipping id
                                $objShippingProd->updateNewShippingWithOldShippingOnProducts(
                                    $newShippingId,
                                    $oldShippingId
                                );
                            }
                            //delete shipping all data
                            $objMpShippingNew->deleteMpShipping($oldShippingId);
                        } else {
                            //If no carrier available then admin first carrier will assign
                            //delete shipping all data
                            $objMpShippingNew->deleteMpShipping($oldShippingId);
                            if ($mpProdMap) {
                                $adminDefShipping = unserialize(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));
                                if ($adminDefShipping) {
                                    $carrierArr[] = $adminDefShipping[0]; //first admin shipping
                                    foreach ($mpProdMap as $mpProd) {
                                        $objSellerProd = new WkMpSellerProduct($mpProd['mp_product_id']);
                                        $psProductId = $objSellerProd->id_ps_product;
                                        if ($psProductId) {
                                            $objProduct = new Product($psProductId);
                                            $objProduct->setCarriers($carrierArr);
                                        }
                                    }
                                }
                            }
                        }
                        Tools::redirect(
                            $this->context->link->getModuleLink(
                                'mpshipping',
                                'mpshippinglist',
                                array('delete_success' => 1)
                            )
                        );
                    } else {
                        Tools::redirect($this->context->link->getModuleLink('mpshipping', 'mpshippinglist'));
                    }
                }

                //Delete Shipping method from front
                $deleteShipping = Tools::getValue('delete_shipping');
                if ($deleteShipping) {
                    if ($mpShippingId = Tools::getValue('mpshipping_id')) {
                        $objMpShippingNew = new MpShippingMethod($mpShippingId);
                        if ($objMpShippingNew->mp_id_seller == $idSeller) {
                            $objMpShippingNew->deleteMpShipping($mpShippingId);
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'mpshipping',
                                    'mpshippinglist',
                                    array('delete_success' => 1)
                                )
                            );
                        } else {
                            Tools::redirect($this->context->link->getModuleLink('mpshipping', 'mpshippinglist'));
                        }
                    }
                }

                $newObjMpShipping = new MpShipping();
                if ($newObjMpShipping->checkMarketplaceVersion()) {
                    //Update tracking number from mp order details page
                    if (Tools::isSubmit('submit_mp_tracking_number')) {
                        if ($idOrder = Tools::getValue('id_order')) {
                            $objMpOrderDetail = new WkMpSellerOrderDetail();
                            $orderProduct = $objMpOrderDetail->getSellerProductFromOrder($idOrder, $idCustomer);
                            if ($orderProduct) {
                                $params = array();
                                $params['id_order'] = $idOrder;

                                $trackingNumber = Tools::getValue('mp_tracking_number');
                                $idOrderCarrier = Tools::getValue('id_order_carrier');

                                if ($trackingNumber && $idOrderCarrier) {
                                    $objOrderCarrier = new OrderCarrier($idOrderCarrier);
                                    if (!Validate::isLoadedObject($objOrderCarrier)) {
                                        $params['invalid_tracking_number'] = 1;
                                    } elseif (!Validate::isTrackingNumber($trackingNumber)) {
                                        $params['invalid_tracking_number'] = 1;
                                    } else {
                                        $objOrder = new Order($idOrder);
                                        $objOrder->shipping_number = $trackingNumber;
                                        if ($objOrder->update()) {
                                            $objOrderCarrier->tracking_number = $trackingNumber;
                                            if ($objOrderCarrier->update()) {
                                                $params['update_tracking_success'] = 1;
                                            }
                                        }
                                    }
                                } else {
                                    $params['invalid_tracking_number'] = 1;
                                }

                                Tools::redirect($this->context->link->getModuleLink(
                                    'marketplace',
                                    'mporderdetails',
                                    $params
                                ).'#mp_tracking_div');
                            }
                        }
                    }
                }


                //Only active shipping methods
                $mpShippingActive = $objMpShipping->getMpShippingMethods($idSeller);
                if ($mpShippingActive) {
                    $this->context->smarty->assign('mp_shipping_active', $mpShippingActive);
                }

                //Only default shipping methods
                $mpShippingDefault = $objMpShipping->getDefaultMpShippingMethods($idSeller);
                if ($mpShippingDefault) {
                    $defaultShippingName = '';
                    foreach ($mpShippingDefault as $defkey => $mpShippingDef) {
                        $shippingName = $mpShippingDef['mp_shipping_name'].' ('.$mpShippingDef['id'].')';
                        if ($defkey == 0) {
                            $defaultShippingName = $shippingName;
                        } else {
                            $defaultShippingName = $shippingName.', '.$defaultShippingName;
                        }
                    }

                    $this->context->smarty->assign('default_shipping_name', $defaultShippingName);
                }

                //show all shipping method which was not deleted in shipping list
                $mpShippingDetail = $objMpShipping->getAllShippingMethodNotDelete($idSeller, 0);
                if ($mpShippingDetail) {
                    $k = 0;
                    foreach ($mpShippingDetail as $mpShipping) {
                        if (file_exists(_PS_MODULE_DIR_.'mpshipping/views/img/logo/'.$mpShipping['id'].'.jpg')) {
                            $mpShippingDetail[$k]['image_exist'] = 1;
                        } else {
                            $mpShippingDetail[$k]['image_exist'] = 0;
                        }

                        $mpShippingProdMap = $objShippingProd->getMpShippingForProducts($mpShipping['id']);
                        if ($mpShippingProdMap) {
                            $mpShippingDetail[$k]['shipping_on_product'] = 1;
                        } else {
                            $mpShippingDetail[$k]['shipping_on_product'] = 0;
                        }

                        ++$k;
                    }

                    $this->context->smarty->assign('mp_shipping_detail', $mpShippingDetail);
                }

                $this->context->smarty->assign('updatempshipping_success', Tools::getValue('updatempshipping_success'));
                $this->context->smarty->assign('default_shipping_link', $link->getModuleLink('mpshipping', 'updatedefaultShipping'));
                $this->context->smarty->assign('logic', 'mp_carriers');
                $this->context->smarty->assign('title_text_color', Configuration::get('WK_MP_TITLE_TEXT_COLOR'));
                $this->context->smarty->assign('title_bg_color', Configuration::get('WK_MP_TITLE_BG_COLOR'));

                $this->defineJSVars();
                $this->setTemplate('module:mpshipping/views/templates/front/mpshippinglist.tpl');
            } else {
                Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('mpshipping', 'mpshippinglist')));
        }
    }

    public function defineJSVars()
    {
        $jsVars = array(
                'ajaxurl_shipping_extra' => $this->context->link->getModuleLink('mpshipping', 'mpshippinglist'),
                'wk_dataTables' => 1,
                'confirm_msg' => $this->module->l('Are you sure?', 'mpshippinglist'),
                'display_name' => $this->module->l('Display', 'mpshippinglist'),
                'records_name' => $this->module->l('records per page', 'mpshippinglist'),
                'no_product' => $this->module->l('No order found', 'mpshippinglist'),
                'show_page' => $this->module->l('Showing page', 'mpshippinglist'),
                'show_of' => $this->module->l('of', 'mpshippinglist'),
                'no_record' => $this->module->l('No records', 'mpshippinglist'),
                'filter_from' => $this->module->l('filtered from', 'mpshippinglist'),
                't_record' => $this->module->l('total records', 'mpshippinglist'),
                'search_item' => $this->module->l('Search', 'mpshippinglist'),
                'p_page' => $this->module->l('Previous', 'mpshippinglist'),
                'n_page' => $this->module->l('Next', 'mpshippinglist'),
            );

        Media::addJsDef($jsVars);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'mpshippinglist'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Carriers', 'mpshippinglist'),
            'url' => '',
        );

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('fancybox');
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('mpshippinglistcss', 'modules/'.$this->module->name.'/views/css/mpshippinglist.css');
        $this->registerJavascript('mpshippinglistjs', 'modules/'.$this->module->name.'/views/js/mpshippinglist.js');

        //data table file included
        $this->registerStylesheet('datatable_bootstrap', 'modules/marketplace/views/css/datatable_bootstrap.css');
        $this->registerJavascript('mp-jquery-dataTables', 'modules/marketplace/views/js/jquery.dataTables.min.js');
        $this->registerJavascript('mp-dataTables.bootstrap', 'modules/marketplace/views/js/dataTables.bootstrap.js');
    }
}
