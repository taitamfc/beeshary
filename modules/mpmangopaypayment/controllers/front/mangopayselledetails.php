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

class MpMangopayPaymentMangopaySelleDetailsModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Mangopay Payment ', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('mpmangopaypayment', 'mangopayselledetails')
        );

        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Add Details', array(), 'Breadcrumb'),
            'url' => ''
        );
        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)) {
            $smartyVars = array();
            $idCustomer = $this->context->customer->id;
            $sellerDetail = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($sellerDetail && $sellerDetail['active']) {
                $clientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
                if (isset($clientId) && $clientId) {
                    $idSeller = $sellerDetail['id_seller'];
                    if ($countries = Country::getCountries($this->context->language->id, false, false, false)) {
                        $smartyVars['countries'] = $countries;
                    }
                    if ($sellerIdCountry = MangopaySellerCountry::sellerCountryID($idSeller)) {
                        $smartyVars['id_country'] = $sellerIdCountry;
                    }
                    $smartyVars['is_seller'] = 1;
                    $smartyVars['logic'] = 'mgp_seller_details';
                    $this->context->smarty->assign($smartyVars);
                    $this->setTemplate('module:mpmangopaypayment/views/templates/front/mangopaysellerdetails.tpl');
                }
            } else {
                Tools::redirect(
                    'index.php?controller=my-account&back='.
                    urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopayselledetails'))
                );
            }
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back='.
                urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopayselledetails'))
            );
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submit_mgp_details')) {
            if (isset($this->context->customer->id)) {
                $idCustomer = $this->context->customer->id;
                $sellerDetail = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
                if ($sellerDetail && $sellerDetail['active']) {
                    $idSellerCountry = Tools::getValue('seller_id_country');
                    if ($idSellerCountry == '') {
                        $this->errors[] = $this->module->l('Country is required', 'mangopayselledetails');
                    }
                    if (!$this->errors) {
                        $idSeller = $sellerDetail['id_seller'];
                        $id_country = MangopaySellerCountry::sellerCountryID($idSeller);
                        if (!$id_country) {
                            $this->createSellerCountry($idCustomer, $idSeller, $idSellerCountry);
                        } else {
                            if ($id_country != $idSellerCountry) {
                                // if country changed later
                                $country_details = MangopaySellerCountry::sellerCountryDetails($idSeller);
                                if ($country_details) {
                                    $this->createSellerCountry(
                                        $idCustomer,
                                        $idSeller,
                                        $idSellerCountry,
                                        $country_details['id']
                                    );
                                }
                            } else {
                                $this->createSellerOtherCurrencyWallets($idSeller);
                            }
                        }
                        if (!count($this->errors)) {
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'mpmangopaypayment',
                                    'mangopayselledetails',
                                    array(
                                        'mangopay_details_saved' => 1
                                    )
                                )
                            );
                        }
                    }
                } else {
                    Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
                }
            } else {
                Tools::redirect(
                    'index.php?controller=authentication&back='.
                    urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopayselledetails'))
                );
            }
        }
    }

    public function createSellerCountry($idCustomer, $idSeller, $idSellerCountry, $id = false)
    {
        if ($id) {
            $objMgpSellerCountry = new MangopaySellerCountry($id);
        } else {
            $objMgpSellerCountry = new MangopaySellerCountry();
        }
        $objMgpSellerCountry->id_customer = $idCustomer;
        $objMgpSellerCountry->id_seller = $idSeller;
        $objMgpSellerCountry->id_country = $idSellerCountry;
        $objMgpSellerCountry->save();
        if ($objMgpSellerCountry->id) {
            $mgpUser = MangopayMpSeller::sellerMangopayDetails($idSeller); //if wallet already created
            if (!$mgpUser) {
                $this->createSellerMangopayWallet($idSeller);
            } else {
                if (MangopayMpSeller::sellerMangopayDetails($idSeller)) {
                    $objMgpSseller = new MangopayMpSeller();
                    if ($objMgpSseller->deleteSellerMangopaydetailsByMangopayUserId($mgpUser['mgp_userid'])) {
                        $this->createSellerMangopayWallet($idSeller);
                    }
                }
            }
        }
    }

    public function createSellerMangopayWallet($idSeller)
    {
        if ($sellerIdCountry = MangopaySellerCountry::sellerCountryID($idSeller)) {
            $objMgpService = new MangopayMpService();
            if ($mangopayUserId = $objMgpService->createMangopayUserLegal($sellerIdCountry, $idSeller, 'Seller')) {
                $objMgpConfig = new MangopayConfig();
                $clientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
                if ($mangopayConfigs = $objMgpConfig->getAdminConfigData(
                    $clientId,
                    Configuration::get('WK_MP_MANGOPAY_PASSPHRASE')
                )) {
                    foreach ($mangopayConfigs as $config) {
                        try {
                            $mangopayWalletId = $objMgpService->createMangopayWallet(
                                $mangopayUserId,
                                $config['currency_iso']
                            );
                            if ($mangopayWalletId) {
                                $objMgpSseller = new MangopayMpSeller();
                                $objMgpSseller->mgp_clientid = $clientId;
                                $objMgpSseller->mgp_userid = $mangopayUserId;
                                $objMgpSseller->mgp_walletid = $mangopayWalletId;
                                $objMgpSseller->currency_iso = $config['currency_iso'];
                                $objMgpSseller->id_seller = $idSeller;
                                $objMgpSseller->save();
                            }
                        } catch (Exception $e) {
                            $this->errors[] = $e->GetMessage();
                            return false;
                        }
                    }
                }
                return $mangopayUserId;
            }
        }
        return false;
    }

    public function createSellerOtherCurrencyWallets($idSeller)
    {
        $mgpUser = MangopayMpSeller::sellerMangopayDetails($idSeller);
        $mangopayUserId = $mgpUser['mgp_userid'];
        if ($mangopayUserId) {
            $objMgpConfig = new MangopayConfig();
            $clientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
            $mangopayConfigs = $objMgpConfig->getAdminConfigData(
                $clientId,
                Configuration::get('WK_MP_MANGOPAY_PASSPHRASE')
            );
            if ($mangopayConfigs) {
                $objMgpService = new MangopayMpService();
                foreach ($mangopayConfigs as $config) {
                    $walletAvail = MangopayMpSeller::checkSellerMangopayDetailsAvailable(
                        $clientId,
                        $idSeller,
                        $config['currency_iso']
                    );
                    if (!$walletAvail) {
                        try {
                            $mangopayWalletId = $objMgpService->createMangopayWallet(
                                $mangopayUserId,
                                $config['currency_iso']
                            );
                            if ($mangopayWalletId) {
                                $objMgpSseller = new MangopayMpSeller();
                                $objMgpSseller->mgp_clientid = $clientId;
                                $objMgpSseller->mgp_userid = $mangopayUserId;
                                $objMgpSseller->mgp_walletid = $mangopayWalletId;
                                $objMgpSseller->currency_iso = $config['currency_iso'];
                                $objMgpSseller->id_seller = $idSeller;
                                return $objMgpSseller->save();
                            }
                        } catch (Exception $e) {
                            $this->errors[] = $e->GetMessage();
                        }
                    }
                }
            }
        }
        return false;
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerStylesheet('marketplace_accountcss', 'modules/marketplace/views/css/marketplace_account.css');
    }
}
