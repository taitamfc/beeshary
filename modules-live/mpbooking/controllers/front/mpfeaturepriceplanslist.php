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

class MpBookingMpFeaturePricePlansListModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Booking Price Rules List', array(), 'Breadcrumb'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->isLogged(true)) {
            $idCustomer = $this->context->customer->id;
            $mpSellerDetails = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpSellerDetails && $mpSellerDetails['active']) {
                $idSeller = $mpSellerDetails['id_seller'];
                $dateFrom = date('d-m-Y');
                $dateTo = date('d-m-Y', strtotime("+1 day", strtotime($dateFrom)));
                $idTable = 0;
                $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

                // Get Seller's feature price plans List
                $objFeaturePricing = new WkMpBookingProductFeaturePricing();
                if ($featurePricePlansList = $objFeaturePricing->getBookingPriceRules(
                    $idSeller,
                    $this->context->language->id
                )) {
                    foreach ($featurePricePlansList as &$pricePlan) {
                        if ($pricePlan['impact_type'] == 1) {
                            $pricePlan['impact_value'] = Tools::ps_round($pricePlan['impact_value'], 2).'%';
                        } else {
                            $pricePlan['impact_value'] = Tools::displayPrice(
                                Tools::convertPrice($pricePlan['impact_value'])
                            );
                        }
                    }
                }
                if (Tools::getIsset('mp_plan_status')) {
                    $this->changeBookkingPriceRuleStatus();
                }
                //delete selected checkbox process
                if ($selectedPlans = Tools::getValue('mp_plans_selected')) {
                    $this->deleteSelectedFeaturePricePlans($selectedPlans, $idSeller);
                }

                // delete individual feature price plan
                if (Tools::getValue('deleteplan')) {
                    if ($idFeaturePlan = Tools::getValue('id_feature_price_rule')) {
                        if (Validate::isLoadedObject(
                            $objPricePlan = new WkMpBookingProductFeaturePricing($idFeaturePlan)
                        )) {
                            if (Validate::isLoadedObject(
                                $objBookingProd = new WkMpBookingProductInformation($objPricePlan->id_booking_product_info)
                            )) {
                                if ($objBookingProd->id_seller == $idSeller) {
                                    if ($objPricePlan->delete()) {
                                        Tools::redirect(
                                            $this->context->link->getModuleLink(
                                                'mpbooking',
                                                'mpfeaturepriceplanslist',
                                                array('deleted' => 1)
                                            )
                                        );
                                    } else {
                                        $this->errors[] = $this->module->l('Some error occurred while deleting the plan.', 'mpfeaturepriceplanslist');
                                    }
                                } else {
                                    Tools::redirect($this->context->link->getModuleLink('marketplace', 'dashboard'));
                                }
                            }
                        } else {
                            $this->errors[] = $this->module->l('Object not loaded.', 'mpfeaturepriceplanslist');
                        }
                    }
                }

                //if product deleted completed by seller
                if (Tools::getIsset('deleted')) {
                    $smartyVars['deleted'] = 1;
                }

                //if product status updated by seller
                if (Tools::getIsset('status_updated')) {
                    $smartyVars['status_updated'] = 1;
                }
                $smartyVars['module_dir'] = _MODULE_DIR_;
                $smartyVars['featurePricePlansList'] = $featurePricePlansList;
                $smartyVars['default_lang'] = $mpSellerDetails['default_lang'];
                $smartyVars['defaultCurrencySign'] = $objDefaultCurrency->sign;
                $smartyVars['logic'] = 'mpfeaturepriceplans';
                $smartyVars['logged'] = $this->context->customer->isLogged();

                //assign default variables
                WkMpHelper::assignGlobalVariables();
                WkMpBookingHelper::assignDataTableVariables();
                Media::addJsDef(
                    array(
                        'confirm_delete_msg' => $this->module->l('Are you sure ?', 'mpfeaturepriceplanslist'),
                    )
                );
                $this->context->smarty->assign($smartyVars);
                $this->setTemplate('module:mpbooking/views/templates/front/mpfeaturepriceplanslist.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    private function deleteSelectedFeaturePricePlans($planIds, $idSeller)
    {
        $mpPlanDelete = true;
        if ($planIds) {
            foreach ($planIds as $idPlan) {
                if (Validate::isLoadedObject(
                    $objPricePlan = new WkMpBookingProductFeaturePricing($idPlan)
                )) {
                    if (Validate::isLoadedObject(
                        $objBookingProd = new WkMpBookingProductInformation($objPricePlan->id_booking_product_info)
                    )) {
                        if ($objBookingProd->id_seller == $idSeller) {
                            if (!$objPricePlan->delete()) {
                                $this->errors[] = $this->module->l(
                                    'Some error occurred while deleting the feature price Plans.',
                                    'mpfeaturepriceplanslist'
                                );
                                $mpPlanDelete = false;
                            }
                        }
                    }
                }
            }
        }
        if ($mpPlanDelete) {
            Tools::redirect(
                $this->context->link->getModuleLink('mpbooking', 'mpfeaturepriceplanslist', array('deleted' => 1))
            );
        }
    }

    private function changeBookkingPriceRuleStatus()
    {
        $idFeaturePricePlan = Tools::getValue('id_feature_price_rule');
        $objFeaturePricePlan = new WkMpBookingProductFeaturePricing($idFeaturePricePlan);
        if (Validate::isLoadedObject($objFeaturePricePlan)) {
            $statusToChange = Tools::getValue('mp_plan_status');
            if ($statusToChange) {
                $objFeaturePricePlan->active = 0;
            } else {
                $objFeaturePricePlan->active = 1;
            }
            if ($objFeaturePricePlan->save()) {
                Tools::redirect(
                    $this->context->link->getModuleLink(
                        'mpbooking',
                        'mpfeaturepriceplanslist',
                        array('status_updated' => 1)
                    )
                );
            }
        } else {
            $this->errors[] = $this->module->l('Object not loaded.', 'mpfeaturepriceplanslist');
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        //data table file included
        $this->registerStylesheet('datatable_bootstrap', 'modules/marketplace/views/css/datatable_bootstrap.css');
        $this->registerJavascript('mp-jquery-dataTables', 'modules/marketplace/views/js/jquery.dataTables.min.js');
        $this->registerJavascript('mp-dataTables.bootstrap', 'modules/marketplace/views/js/dataTables.bootstrap.js');
        // marketplace css
        $this->registerStylesheet('marketplace-account-css', 'modules/marketplace/views/css/marketplace_account.css');

        $this->registerJavascript(
            'mpbooking-wk-feature-price-plans-list',
            'modules/mpbooking/views/js/wk-mpbooking-global.js'
        );

        $this->registerJavascript(
            'mpbooking-wk-feature-price-plans',
            'modules/mpbooking/views/js/front/wk-mp-feature-price-plans.js'
        );
    }
}
