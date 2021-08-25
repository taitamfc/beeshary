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

class AdminMangopayPayOutController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        $this->show_toolbar = false;
        $this->display = 'view';
        parent::initContent();
    }

    /**
     * Generate the render view
     *
     * @return void
     */
    public function renderView()
    {
        //If admin submit for registering his bank details to the mangopay account
        $mgp_client_id = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        if ($mgp_client_id) {
            $admin_mgp_usr_id = Configuration::get('WK_MP_MANGOPAY_USERID');
            $admin_mgp_wallet_id = Configuration::get('WK_MP_MANGOPAY_WALLETID');
            $obj_mgp_mpservice = new MangopayMpService();
            $mgp_wallet_dtls = $obj_mgp_mpservice->getWalletDetails($admin_mgp_wallet_id);
            $admin_mgp_account_dtls = $obj_mgp_mpservice->getMangopayBankAccounts($admin_mgp_usr_id);
            if ($admin_mgp_account_dtls) {
                foreach ($admin_mgp_account_dtls as $key => $adminBankAccount) {
                    if (!$adminBankAccount->Active) {
                        unset($admin_mgp_account_dtls[$key]);
                    }
                }
                $this->context->smarty->assign('admin_mgp_account_dtls', $admin_mgp_account_dtls);
            }
            if ($mgp_wallet_dtls) {
                $this->context->smarty->assign('total_wallet_balance', ($mgp_wallet_dtls['Balance']->Amount) / 100);
                $this->context->smarty->assign('seller_wallet_currency', $mgp_wallet_dtls['Balance']->Currency);
            }
            if ($admin_mgp_usr_id && $admin_mgp_wallet_id) {
                $this->context->smarty->assign('admin_mgp_usr_id', $admin_mgp_usr_id);
                $this->context->smarty->assign('admin_mgp_wallet_id', $admin_mgp_wallet_id);
            }
            $currency_mgp = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $this->context->smarty->assign('wallet_currency_sign', $currency_mgp);
        }
        $this->context->smarty->assign('mgp_client_id', $mgp_client_id);

        return parent::renderView();
    }

    /**
     * Create the payout.
     *
     * @return void
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submit_admin_payout')) {
            $this->validatePayOutFields();
            if (!count($this->errors)) {
                $admin_mgp_usr_id = Configuration::get('WK_MP_MANGOPAY_USERID');
                $admin_mgp_wallet_id = Configuration::get('WK_MP_MANGOPAY_WALLETID');
                $payout_amount = Tools::getValue('payout_debit_amt_seller');
                $payout_amount = Tools::ps_round($payout_amount, 2);
                $payout_account = Tools::getValue('payout_admin_bank_acc_id');
                $payout_fee = Tools::getValue('payout_fees_seller');
                $payout_fee = Tools::ps_round($payout_fee, 2);
                $obj_mgp_mpservice = new MangopayMpService();
                $mgp_pay_out = $obj_mgp_mpservice->createSellerpayOut($admin_mgp_usr_id, $admin_mgp_wallet_id, $payout_account, $payout_amount, $payout_fee);
                if ($mgp_pay_out['Status'] != 'FAILED') {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                } else {
                    if (isset($mgp_pay_out['ResultMessage']) && $mgp_pay_out['ResultMessage']) {
                        $this->errors[] = $mgp_pay_out['ResultMessage'];
                    } else {
                        $this->errors[] = $this->l('Some error occured while PayOut. Please try again.');
                    }
                }
            }
        }
        parent::postProcess();
    }

    /**
     * validate the payout fields.
     *
     * @return void
     */
    private function validatePayOutFields()
    {
        $admin_mgp_usr_id = Configuration::get('WK_MP_MANGOPAY_USERID');
        $admin_mgp_wallet_id = Configuration::get('WK_MP_MANGOPAY_WALLETID');
        $payout_amount = Tools::getValue('payout_debit_amt_seller');
        $payout_amount = Tools::ps_round($payout_amount, 2);
        $payout_fee = Tools::getValue('payout_fees_seller');
        $payout_fee = Tools::ps_round($payout_fee, 2);
        $payout_account = Tools::getValue('payout_admin_bank_acc_id');
        $wallet_currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
        if (!$admin_mgp_usr_id) {
            $this->errors[] = $this->l('Mangopay User Id is missing.');
        }
        if (!$admin_mgp_wallet_id) {
            $this->errors[] = $this->l('Mangopay Wallet Id is missing.');
        }
        $obj_mgp_mpservice = new MangopayMpService();
        $mgp_wallet_dtls = $obj_mgp_mpservice->getWalletDetails($admin_mgp_wallet_id);
        if ($mgp_wallet_dtls) {
            $total_wallet_balance = ($mgp_wallet_dtls['Balance']->Amount) / 100;
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
        if (!$wallet_currency) {
            $this->errors[] = $this->l('Seller Wallet Currency Not found.');
        }
        if (!$payout_amount) {
            $this->errors[] = $this->l('Please Enter amount greater than 0.');
        }
        if (!$payout_fee && $payout_fee != 0) {
            $this->errors[] = $this->l('Please Enter fee.');
        }
        if ($payout_fee && $payout_fee < 0) {
            $this->errors[] = $this->l('Please Enter fee equal or greater than 0.');
        }
        if (!$payout_account) {
            $this->errors[] = $this->l('Please Select the account.');
        }
    }
}
