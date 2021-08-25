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

class MpMangopayPaymentMangopaySellerCashOutModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Mangopay Payment ', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellercashout')
        );

        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Mangopay Cash Out', array(), 'Breadcrumb'),
            'url' => ''
        );
        return $breadcrumb;
    }

    // Display the seller payout details
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
                    if (Configuration::get('WK_MP_MANGOPAY_SELLER_CASHOUT')) {
                        $idSeller = $sellerDetail['id_seller'];
                        $smartyVars['is_seller'] = 1;
                        $smartyVars['logic'] = 'mgp_seller_cash_out';
                        $objMgpSeller = new MangopayMpSeller();
                        $sellerMgpDetail = $objMgpSeller->checkSellerMangopayDetailsAvailable(
                            Configuration::get('WK_MP_MANGOPAY_CLIENTID'),
                            $idSeller,
                            Configuration::get('WK_MP_MANGOPAY_CURRENCY')
                        );

                        //Validate is seller has saved his country before registering his bank details to the mangopay
                        if ($sellerMgpDetail && $sellerMgpDetail['mgp_userid']) {
                            $objMgpService = new MangopayMpService();

                            // Validate if details can be registered to mangopay form mangopay API
                            $mgpRegBankAccIds = $objMgpService->getMangopayBankAccounts(
                                $sellerMgpDetail['mgp_userid']
                            );
                            if ($mgpSellerWalletDetail = $objMgpService->getWalletDetails(
                                $sellerMgpDetail['mgp_walletid']
                            )) {
                                $smartyVars['total_wallet_balance'] = ($mgpSellerWalletDetail['Balance']->Amount) / 100;
                                $smartyVars['seller_wallet_currency'] = $mgpSellerWalletDetail['Balance']->Currency;
                            }
                            if ($mgpRegBankAccIds) {
                                foreach ($mgpRegBankAccIds as $key => $bankAccount) {
                                    if (!$bankAccount->Active) {
                                        unset($mgpRegBankAccIds[$key]);
                                    }
                                }
                                $smartyVars['mgp_registered_bank_acc_ids'] = $mgpRegBankAccIds;
                            }
                            $smartyVars['seller_not_registered'] = 1;
                        } else {
                            if (MangopayMpSeller::sellerMangopayDetails($idSeller)) {
                                $smartyVars['seller_mgp_user'] = 1;
                            }
                        }
                        $smartyVars['wallet_currency_sign'] = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
                        $smartyVars['seller_cashout_enable'] = Configuration::get('WK_MP_MANGOPAY_SELLER_CASHOUT');
                        $smartyVars['logged'] = $this->context->customer->isLogged();

                        $jsVars = array(
                            'payout_amt_err' => $this->module->l(
                                'Please enter a valid amount to cash out.',
                                'mangopaysellercashout'
                            ),
                            'bank_id_err' => $this->module->l(
                                'Please Select a bank account to cash out.',
                                'mangopaysellercashout'
                            ),
                        );

                        Media::addJsDef($jsVars);

                        $this->context->smarty->assign($smartyVars);
                        $this->setTemplate('module:mpmangopaypayment/views/templates/front/mangopaysellercashout.tpl');
                    } else {
                        Tools::redirect(
                            'index.php?controller=my-account&back='.
                            urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellercashout'))
                        );
                    }
                } else {
                    Tools::redirect(
                        'index.php?controller=my-account&back='.
                        urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellercashout'))
                    );
                }
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back='.
                urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellercashout'))
            );
        }
    }

    // create the seller payout
    public function postProcess()
    {
        if (isset($this->context->customer->id)) {
            if (Tools::isSubmit('submit_payout_amount')) {
                $idCustomer = $this->context->customer->id;
                $sellerDetail = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
                if ($sellerDetail && $sellerDetail['active']) {
                    $idSeller = $sellerDetail['id_seller'];
                    //If seller submit for registering his bank details to the mangopay account
                    $this->validateSellerPayOutFields($idSeller);
                    if (!count($this->errors)) {
                        $payoutAmount = Tools::getValue('payout_amount');
                        $payoutAmount = Tools::ps_round($payoutAmount, 2);
                        $payoutAccount = Tools::getValue('seller_mgp_account');
                        $objMgpSeller = new MangopayMpSeller();
                        $sellerMgpDetail = $objMgpSeller->checkSellerMangopayDetailsAvailable(
                            Configuration::get('WK_MP_MANGOPAY_CLIENTID'),
                            $idSeller,
                            Configuration::get('WK_MP_MANGOPAY_CURRENCY')
                        );
                        $objMgpService = new MangopayMpService();
                        // Validate if details can be registered to mangopay form mangopay API
                        $mgpPayout = $objMgpService->createSellerpayOut(
                            $sellerMgpDetail['mgp_userid'],
                            $sellerMgpDetail['mgp_walletid'],
                            $payoutAccount,
                            $payoutAmount
                        );
                        if ($mgpPayout['Status'] != 'FAILED') {
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'mpmangopaypayment',
                                    'mangopaysellercashout',
                                    array('payout_success' => 1)
                                )
                            );
                        } else {
                            $this->errors[] = $this->module->l($mgpPayout['ResultMessage'], 'mangopaysellercashout');
                        }
                    }
                } else {
                    Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
                }
            }
        } else {
            // in case no customer id found(customer not login)
            Tools::redirect(
                'index.php?controller=authentication&back='.
                urlencode($this->context->link->getModuleLink('mpmangopaypayment', 'mangopaysellercashout'))
            );
        }
    }

    // Validate the seller payout fields
    private function validateSellerPayOutFields($idSeller)
    {
        $payoutAmount = Tools::getValue('payout_amount');
        $payoutAmount = Tools::ps_round($payoutAmount, 2);
        $payoutAccount = Tools::getValue('seller_mgp_account');
        $sellerWalletTotalAmount = (int) Tools::getValue('seller_wallet_amount');
        $sellerWalletCurrency = Tools::getValue('seller_wallet_currency');
        $objMgpSeller = new MangopayMpSeller();

        $currencyMgp = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
        $sellerMgpDetail = $objMgpSeller->sellerMangopayDetailsByCurrency($idSeller, $currencyMgp);

        if (isset($sellerMgpDetail['mgp_userid']) && $sellerMgpDetail['mgp_userid']) {
            if (!Validate::isPrice($payoutAmount)) {
                $this->errors[] = $this->module->l(
                    'Please enter a valid amount to cash out.',
                    'mangopaysellercashout'
                );
            }
            if ($payoutAmount > $sellerWalletTotalAmount) {
                $this->errors[] = $this->module->l(
                    'Not sufficient Balance in the wallet.',
                    'mangopaysellercashout'
                );
            }
            if (!$sellerWalletCurrency) {
                $this->errors[] = $this->module->l(
                    'Seller Wallet Currency Not found.',
                    'mangopaysellercashout'
                );
            }
            if (!$payoutAmount) {
                $this->errors[] = $this->module->l(
                    'Please Enter amount greater than 0.',
                    'mangopaysellercashout'
                );
            }
            if (!$payoutAccount) {
                $this->errors[] = $this->module->l(
                    'Please Select the account(where to transfer).',
                    'mangopaysellercashout'
                );
            }
            if (!$sellerMgpDetail['mgp_walletid']) {
                $this->errors[] = $this->module->l(
                    'No mangopay wallet found for the seller. Please create a mangopay Wallet first.',
                    'mangopaysellercashout'
                );
            }
        } else {
            $this->errors[] = $this->module->l(
                'Mangopay User Id is missing for current configuration. You need to register your country first.
                Perhaps For the current mangopay currency, Wallet id has not been generated. To get a Wallet Id in the
                current mangopay currency please save your country once more.',
                'mangopaysellercashout'
            );
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerJavascript(
            'module-mangopay_bank_detailsjs',
            'modules/mpmangopaypayment/views/js/mangopay_bank_details.js'
        );
        $this->registerStylesheet('marketplace_accountcss', 'modules/marketplace/views/css/marketplace_account.css');
    }
}
