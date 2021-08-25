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

class AdminMangopaySellerPayOutController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        if (Tools::getValue('action') == 'getMangopayBankDetails') {
            return $this->ajaxProcessGetMangopayBankDetails();
        } else {
            $this->show_toolbar = false;
            $this->display = 'add';
            parent::initContent();
        }
    }

    /**
     * Generate the render form
     * @return void
     */
    public function renderForm()
    {
        $mgp_client_id = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        if (isset($mgp_client_id) && $mgp_client_id) {
            $sellers_info = WkMpSeller::getAllSeller();
            if ($sellers_info['0']['id_seller']) {
                $id_seller = $sellers_info['0']['id_seller'];
                $obj_mgp_seller = new MangopayMpSeller();
                $seller_mgp_dtls = $obj_mgp_seller->checkSellerMangopayDetailsAvailable(
                    Configuration::get('WK_MP_MANGOPAY_CLIENTID'),
                    $id_seller,
                    Configuration::get('WK_MP_MANGOPAY_CURRENCY')
                );
                if ($seller_mgp_dtls && $seller_mgp_dtls['mgp_userid']) {
                    $obj_mgp_mpservice = new MangopayMpService();
                    // Validate if details can be registered to mangopay form mangopay API
                    $mgp_registered_bank_acc_ids = $obj_mgp_mpservice->getMangopayBankAccounts(
                        $seller_mgp_dtls['mgp_userid']
                    );
                    if ($mgp_registered_bank_acc_ids) {
                        foreach ($mgp_registered_bank_acc_ids as $key => $bankAccount) {
                            if (!$bankAccount->Active) {
                                unset($mgp_registered_bank_acc_ids[$key]);
                            }
                        }
                    }
                    $this->context->smarty->assign('mgp_registered_bank_acc_ids', $mgp_registered_bank_acc_ids);
                }
            }
            if ($sellers_info) {
                $this->context->smarty->assign('sellers_info', $sellers_info);
            }
            $currency_mgp = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $this->context->smarty->assign('wallet_currency_sign', $currency_mgp);
        }
        $this->context->smarty->assign('mgp_client_id', $mgp_client_id);
        $this->fields_form = array(
            'submit' => array(
            'title' => $this->l('Save'),
            'class' => 'button',
            ),
        );

        return parent::renderForm();
    }

    /**
     * Create seller payout.
     * @return void
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submit_seller_payout')) {
            $this->validateSellerPayOutFields();
            if (!count($this->errors)) {
                $id_seller = Tools::getValue('seller_id');
                $payout_amount = Tools::getValue('payout_debit_amt_seller');
                $payout_amount = Tools::ps_round($payout_amount, 2);
                $payout_account = Tools::getValue('payout_seller_bank_acc_id');
                $payout_fee = Tools::getValue('payout_fees_seller');
                $payout_fee = Tools::ps_round($payout_fee, 2);
                $obj_mgp_mpservice = new MangopayMpService();
                $obj_mgp_seller = new MangopayMpSeller();
                $currency_mgp = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
                $seller_mgp_dtls = $obj_mgp_seller->sellerMangopayDetailsByCurrency($id_seller, $currency_mgp);
                if ($seller_mgp_dtls) {
                    $seller_mgp_usr_id = $seller_mgp_dtls['mgp_userid'];
                    $seller_mgp_wallet_id = $seller_mgp_dtls['mgp_walletid'];
                    if (!$seller_mgp_usr_id) {
                        $this->errors[] = $this->l('Seller Mangopay User Id is missing.');
                    } else {
                        $mgp_pay_out = $obj_mgp_mpservice->createSellerpayOut(
                            $seller_mgp_usr_id,
                            $seller_mgp_wallet_id,
                            $payout_account,
                            $payout_amount,
                            $payout_fee
                        );
                        if (isset($mgp_pay_out['Status']) && $mgp_pay_out['Status'] != 'FAILED') {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                        } else {
                            $this->errors[] = $mgp_pay_out['ResultMessage'];
                        }
                    }
                } else {
                    $this->errors[] = $this->l('Seller Mangopay User Id is missing for current configuration.
                    Seller need to register his country first. Perhaps For the current mangopay currency, Wallet id
                    has not been generated. To get a Wallet Id in the current mangopay currency seller has to save
                    country once more.');
                }
            }
        }
    }

    /**
     * Validate the payout details
     * @return void
     */
    private function validateSellerPayOutFields()
    {
        $id_seller = Tools::getValue('seller_id');
        $payout_amount = Tools::getValue('payout_debit_amt_seller');
        $payout_amount = Tools::ps_round($payout_amount, 2);
        $payout_account = Tools::getValue('payout_seller_bank_acc_id');
        $seller_wallet_currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
        if ($id_seller) {
            $obj_mgp_seller = new MangopayMpSeller();
            $seller_mgp_dtls = $obj_mgp_seller->sellerMangopayDetailsByCurrency($id_seller, $seller_wallet_currency);
            if ($seller_mgp_dtls) {
                $seller_mgp_usr_id = $seller_mgp_dtls['mgp_userid'];
                $seller_mgp_wallet_id = $seller_mgp_dtls['mgp_walletid'];
                if (!$seller_mgp_usr_id) {
                    $this->errors[] = $this->l('Seller Mangopay User Id is missing.');
                }
                if ($seller_mgp_wallet_id) {
                    $obj_mgp_mpservice = new MangopayMpService();
                    $mgp_wallet_dtls = $obj_mgp_mpservice->getWalletDetails($seller_mgp_wallet_id);
                    if ($mgp_wallet_dtls) {
                        $total_wallet_balance = $mgp_wallet_dtls['Balance']->Amount;
                        if (isset($total_wallet_balance)) {
                            if ($payout_amount > $total_wallet_balance) {
                                $this->errors[] = $this->l('Not sufficient Balance in the wallet.');
                            }
                        } else {
                            $this->errors[] = $this->l('Mangopay Wallet balance not found.');
                        }
                    } else {
                        $this->errors[] = $this->l('Mangopay Wallet details are missing.');
                    }
                } else {
                    $this->errors[] = $this->l('Seller Mangopay Wallet Id is missing.');
                }
            } else {
                $this->errors[] = $this->l('Seller Mangopay User Id is missing for current configuration. Seller need
                to register his country first. Perhaps For the current mangopay currency, Wallet id has not been
                generated. To get a Wallet Id in the current mangopay currency seller has to save country once more.');
            }
        } else {
            $this->errors[] = $this->l('Seller not found.');
        }

        if (!$seller_wallet_currency) {
            $this->errors[] = $this->l('Seller Wallet Currency Not found.');
        }

        if (!$payout_amount) {
            $this->errors[] = $this->l('Please Enter amount greater than 0.');
        }

        if (!$payout_account) {
            $this->errors[] = $this->l('Please Select the account.');
        }
    }

    /**
     * add js variables and js file.
     * @return void
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef(
            array(
                'ajaxurlBankDetails' => $this->context->link->getAdminlink('AdminMangopaySellerPayOut'),
                'noAccountMsg' => $this->l('No Account Available'),
            )
        );
        $this -> addJS(_MODULE_DIR_.'mpmangopaypayment/views/js/mangopay_bank_details.js');
    }

    /**
     * Get mangopay bank details by id seller
     *
     * @return void
     */
    public function ajaxProcessGetMangopayBankDetails()
    {
        $id_seller = Tools::getValue('idSeller');
        $obj_mgp_seller = new MangopayMpSeller();
        $seller_mgp_dtls = $obj_mgp_seller->checkSellerMangopayDetailsAvailable(Configuration::get('WK_MP_MANGOPAY_CLIENTID'), $id_seller, Configuration::get('WK_MP_MANGOPAY_CURRENCY'));
        //Validate is seller has saved his country before registering his bank details to the mangopay
        if ($seller_mgp_dtls && $seller_mgp_dtls['mgp_userid']) {
            $obj_mgp_mpservice = new MangopayMpService();
            // Validate if details can be registered to mangopay form mangopay API
            $mgp_registered_bank_acc_ids = $obj_mgp_mpservice->getMangopayBankAccounts($seller_mgp_dtls['mgp_userid']);
            if ($mgp_registered_bank_acc_ids) {
                foreach ($mgp_registered_bank_acc_ids as $key => $bankAccount) {
                    if (!$bankAccount->Active) {
                        unset($mgp_registered_bank_acc_ids[$key]);
                    }
                }
            }
        }
        if (isset($mgp_registered_bank_acc_ids) && $mgp_registered_bank_acc_ids) {
            die(Tools::jsonEncode($mgp_registered_bank_acc_ids)); //ajax close
        } else {
            die('fail');
        }
    }
}
