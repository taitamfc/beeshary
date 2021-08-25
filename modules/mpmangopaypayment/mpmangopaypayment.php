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
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

include_once dirname(__FILE__).'/lib/MangoPaySDK/autoload.php';
include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
include_once dirname(__FILE__).'/classes/MangopayClassIncluded.php';
class MpMangopayPayment extends PaymentModule
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'mpmangopaypayment';
        $this->tab = 'payments_gateways';
        $this->version = '5.2.0';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->dependencies = array('marketplace');
        parent::__construct();

        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
        $this->displayName = $this->l('Marketplace Mangopay Payment');
        $this->description = $this->l('Accept payments using Mangopay in marketplace');

        // set bankwire variables details for the mail template
        if (Tools::getValue('id_order') > 0) {
            if (Validate::isLoadedObject($objOrder = new Order(Tools::getValue('id_order')))) {
                $objMgpTransac = new MangopayTransaction();
                if ($mgpBankWireDetails = $objMgpTransac->getBankWirePayinByOrderReference($objOrder->reference)) {
                    $this->extra_mail_vars = array(
                        '{bankwire_owner}' => $mgpBankWireDetails['mgp_account_owner_name'],
                        '{bankwire_reference}' => $mgpBankWireDetails['mgp_wire_reference'],
                        '{bankwire_details}' => "<br />".$this->l("Account IBAN: ").
                        $mgpBankWireDetails['mgp_account_iban']."<br />".$this->l("Account BIC: ").
                        $mgpBankWireDetails['mgp_account_bic']."<br />".
                        $this->l("Bankwire Reference: ").$mgpBankWireDetails['mgp_wire_reference'],
                        '{bankwire_address}' => ''
                    );
                }
            }
        }
    }

    //Install the data and set the configuration default value.
    public function install()
    {
        if (function_exists('curl_version')) {
            $objMangopayConfig = new MangopayConfig();
            if (!parent::install()
                || !$this->createTables()
                || !$this->callInstallTab()
                || !$objMangopayConfig->insertMangopayBankwireOrderStatus()
                || !$this->registerModuleHooks()
                || !$this->insertModuleDefaultConfigValues()
            ) {
                return false;
            }
            return true;
        } else {
            $this->_errors[] = $this->l('Curl must be installed before using this module. Please install first.');
            return false;
        }
    }

    // create tables
    private function createTables()
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

    // Register the hooks used in this module.
    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'actionFrontControllerSetMedia',
                'displayBackOfficeHeader',
                'actionOrderStatusPostUpdate',
                'paymentOptions',
                'paymentReturn',
                'displayMPMenuBottom',
                'displayMpOrderDetailProductBottom',
                'displayAdminSellerDetailViewRightColumn',
                'actionAdminMangopaySellerBankDetailsListingResultsModifier',
                'actionAdminMangopayBankDetailsListingResultsModifier',
                'displayMPMyAccountMenu',
                'registerGDPRConsent',
                'actionDeleteGDPRCustomer',
            )
        );
    }

    // set configuration values for seller for functionalities cash out refund and bank details
    public function insertModuleDefaultConfigValues()
    {
        Configuration::updateValue('WK_MP_MANGOPAY_SELLER_REFUND', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_TRANSFER_TO', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_SELLER_CASHOUT', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_SELLER_BANK_DTL', 1);
        Configuration::updateValue(
            'WK_MP_MANGOPAY_TRANSFER_STATUS',
            Configuration::get('PS_OS_PAYMENT')
        );
        Configuration::updateValue('WK_MP_MANGOPAY_PAYIN_TYPE', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_MAIL_ADMIN_REFUND', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_MAIL_SELLER_REFUND', 1);
        Configuration::updateValue('WK_MP_PREVIOUS_DATA_DELETE', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_DIRECT_DEBIT_ENABLE', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_CARD_PAY_ENABLE', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_BANKWIRE_ENABLE', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_SAVE_CARD_ENABLE', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_SAVE_BANK_ACCOUNT_ENABLE', 1);
        Configuration::updateValue('WK_MP_MANGOPAY_PAY_SUCCEED_HOOK', '');
        return true;
    }

    // Install the tabs related to module
    public function callInstallTab()
    {
        $this->installTab('AdminMarketplaceMangopayConfiguration', 'Marketplace Mangopay Payment', 'AdminMarketplace');
        $this->installTab('AdminMangopayBankDetails', 'Manage Bank Details', 'AdminMarketplaceMangopayConfiguration');
        $this->installTab(
            'AdminMangopaySellerBankDetails',
            'Mangopay Seller Bank Details',
            'AdminMarketplaceMangopayConfiguration'
        );
        $this->installTab('AdminMangopayPayOut', 'Manage Pay out', 'AdminMarketplaceMangopayConfiguration');
        $this->installTab('AdminMangopaySellerPayOut', 'Mangopay Seller Payout', false, false);
        $this->installTab('AdminMangopayBankWire', 'Manage BankWire Payments', 'AdminMarketplaceMangopayConfiguration');
        $this->installTab('AdminMangopayRefund', 'Manage Card Payments', 'AdminMarketplaceMangopayConfiguration');
        $this->installTab(
            'AdminMangopayDirectDebit',
            'Manage Direct Debit payments',
            'AdminMarketplaceMangopayConfiguration'
        );

        return true;
    }

    // Install the tab
    public function installTab($class_name, $tab_name, $tab_parent_name = false, $need_tab = true)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }
        if ($class_name == 'AdminMarketplaceMangopayConfiguration') { //Tab name for which you want to add icon
            $tab->icon = 'credit_card'; //Material Icon name
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } elseif (!$need_tab) {
            $tab->id_parent = -1;
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->name;

        return $tab->add();
    }

    public function getContent()
    {
        $displayForm = true;
        //After submitting data from tpl form
        try {
            if (Tools::isSubmit('mgp_api_configuration')
                && Tools::getValue('clientid')
                && Tools::getValue('passphrase')
            ) {
                $idClient = Tools::getValue('clientid');
                $passPhrase = Tools::getValue('passphrase');
                $currencyIso = Tools::getValue('currency');
                $paymentMode = Tools::getValue('mode');
                $creditcard = Tools::getValue('creditcard');
                if (!$idClient) {
                    $this->context->controller->errors[] = $this->l('Please enter Client Id first.');
                }
                if (!$passPhrase) {
                    $this->context->controller->errors[] = $this->l('Please enter Pass Phrase first.');
                }
                if (!$currencyIso) {
                    $this->context->controller->errors[] = $this->l('Please select currency first.');
                } elseif (!Currency::getIdByIsoCode($currencyIso)) {
                    $this->context->controller->errors[] = $this->l('Please import the selected currency first from
                    International->Localization tab first.');
                }
                if (!$paymentMode) {
                    $this->context->controller->errors[] = $this->l('Please select mode of payment.');
                }
                if (!$creditcard) {
                    $this->context->controller->errors[] = $this->l('Please select at least one credit card type.');
                }
                if (!count($this->context->controller->errors)) {
                    $objMangopayConfig = new MangopayConfig();
                    if ($mangopayConfigData = $objMangopayConfig->getAdminConfigData($idClient, $passPhrase)) {
                        if ($currencyIso != Configuration::get('WK_MP_MANGOPAY_CURRENCY')) {
                            $configCurrencydata = $objMangopayConfig->getAdminConfigDataByCurrency(
                                $idClient,
                                $passPhrase,
                                $currencyIso
                            );
                            if (!$configCurrencydata) {
                                //if currecy updated have to create new wallet
                                $objMangopayService = new MangopayMpService();
                                if ($mgpWalletId = $objMangopayService->createMangopayWallet(
                                    $mangopayConfigData[0]['mgp_userid'],
                                    $currencyIso
                                )) {
                                    Configuration::updateValue('WK_MP_MANGOPAY_WALLETID', $mgpWalletId);
                                    $id_employee = MangopayConfig::getSupperAdmin();
                                    $obj_customer = new Employee($id_employee);
                                    $params = array(
                                        'mgp_clientid' => $idClient,
                                        'id_employee' => $this->context->cookie->id_employee,
                                        'mgp_passphrase' => $passPhrase,
                                        'mgp_userid' => $mangopayConfigData[0]['mgp_userid'],
                                        'mgp_walletid' => $mgpWalletId,
                                        'currency_iso' => $currencyIso,
                                        'user_type' => 'admin',
                                        'user_email' => $obj_customer->email,
                                    );
                                    $saveMangopayConfiguration = MangopayConfig::saveMangopayConfigurationData($params);
                                    if (!$saveMangopayConfiguration) {
                                        $this->context->controller->errors[] = Tools::displayError(
                                            $this->l('Error occurred while saving information in MangopayConfig.')
                                        );
                                    }
                                }
                            } else {
                                Configuration::updateValue(
                                    'WK_MP_MANGOPAY_WALLETID',
                                    $configCurrencydata['mgp_walletid']
                                );
                                Configuration::updateValue(
                                    'WK_MP_MANGOPAY_CURRENCY',
                                    $configCurrencydata['currency_iso']
                                );
                            }
                        }
                        // if client id changed again i.e. already generated update it
                        Configuration::updateValue('WK_MP_MANGOPAY_USERID', $mangopayConfigData[0]['mgp_userid']);
                    } else {
                        $exception = $this->createMangoPayUser($idClient, $passPhrase, $currencyIso, $paymentMode);
                    }
                    if (isset($exception)) {
                        $this->context->smarty->assign('unauthorization', $exception);
                    } else {
                        if ($this->saveMangopayAccountConfiguration()) {
                            $this->context->controller->confirmations[] = $this->l('Settings updated.');
                            $objMgpService = new MangopayMpService();
                            $returnUrl = $this->context->link->getModuleLink('mpmangopaypayment', 'success');
                            if ($hookId = Configuration::get('WK_MP_MANGOPAY_PAY_SUCCEED_HOOK')) {
                                $regHook = $objMgpService->registerMangopayEventHook(
                                    'PAYIN_NORMAL_SUCCEEDED',
                                    $returnUrl,
                                    $hookId
                                );
                            } else {
                                $regHook = $objMgpService->registerMangopayEventHook(
                                    'PAYIN_NORMAL_SUCCEEDED',
                                    $returnUrl
                                );
                            }
                            if ($regHook['Status'] == 'FAILED') {
                                $this->context->controller->errors[] = $this->l('Some error has been occurred while
                                registering webhooks for the bankwire payment');
                            } elseif (isset($regHook['Id'])) {
                                Configuration::updateValue('WK_MP_MANGOPAY_PAY_SUCCEED_HOOK', $regHook['Id']);
                            }
                        } else {
                            $this->context->controller->errors[] = $this->l('Some error has been occurred while saving
                            mangopay acount configuration.');
                        }
                    }
                }
                if (count($this->context->controller->errors)) {
                    $this->context->smarty->assign('errors', $this->context->controller->errors);
                }
            }
            // save seller related configuration
            if (Tools::isSubmit('submit_seller_mgp_conf')) {
                $this->saveSellerMangopayConfiguration();
            }
            // save mangopay general configuration
            if (Tools::isSubmit('submit_general_mgp_conf')) {
                if ($this->saveMangopayPaymentConfiguration()) {
                    $this->context->controller->confirmations[] = $this->l('Settings updated.');
                } else {
                    $this->context->controller->errors[] = $this->l('Some error has been occurred while saving
                    payment configuration.');
                }
            }
            // save mangopay mail configuration
            if (Tools::isSubmit('submit_mail_mgp_conf')) {
                $this->saveMailMangopayConfiguration();
            }
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->GetMessage();
        }
        $supportedCards = array(
            'Visa' => 'CB_VISA_MASTERCARD',
            'Mastercard' => 'CB_VISA_MASTERCARD',
            'Carte Bleue' => 'CB_VISA_MASTERCARD',
            'Maestro' => 'MAESTRO',
            'Diners' => 'DINERS',
            'Przelewy24' => 'P24',
            'iDeal' => 'IDEAL',
            // 'Bancontact/Mister Cash' => 'BCMC',
            'PayLib' => 'PAYLIB',
        );
        $this->context->smarty->assign(
            array(
                'this_path' => $this->_path,
                'displayForm' => $displayForm,
                'moduleInstalled' => true,
                'clientid' => Configuration::get('WK_MP_MANGOPAY_CLIENTID'),
                'passphrase' => Configuration::get('WK_MP_MANGOPAY_PASSPHRASE'),
                'creditcard' => unserialize(Configuration::get('WK_MP_MANGOPAY_CREDITCARD')),
                'currency' => Configuration::get('WK_MP_MANGOPAY_CURRENCY'),
                'mode' => Configuration::get('WK_MP_MANGOPAY_MODE'),
                'userid' => Configuration::get('WK_MP_MANGOPAY_USERID'),
                'walletid' => Configuration::get('WK_MP_MANGOPAY_WALLETID'),
                'title' => Configuration::get('WK_MP_MANGOPAY_TITLE'),
                'is_seller_refund' => Configuration::get('WK_MP_MANGOPAY_SELLER_REFUND'),
                'mgp_transfer_in' => Configuration::get('WK_MP_MANGOPAY_TRANSFER_TO'),
                'is_seller_cashout' => Configuration::get('WK_MP_MANGOPAY_SELLER_CASHOUT'),
                'is_seller_bank_dtl' => Configuration::get('WK_MP_MANGOPAY_SELLER_BANK_DTL'),
                'mangopay_payment_data_delete' => Configuration::get('WK_MP_PREVIOUS_DATA_DELETE'),
                'wallet_transf_status' => Configuration::get('WK_MP_MANGOPAY_TRANSFER_STATUS'),
                'mgp_payin_type' => Configuration::get('WK_MP_MANGOPAY_PAYIN_TYPE'),
                'is_mail_refundby_admin' => Configuration::get('WK_MP_MANGOPAY_MAIL_ADMIN_REFUND'),
                'is_mail_refundby_seller' => Configuration::get('WK_MP_MANGOPAY_MAIL_SELLER_REFUND'),
                'direct_debit_supp_cards' => $supportedCards,
                'wk_mangopay_card_pay_enable' => Configuration::get('WK_MP_MANGOPAY_CARD_PAY_ENABLE'),
                'wk_mangopay_direct_debit_enable' => Configuration::get('WK_MP_MANGOPAY_DIRECT_DEBIT_ENABLE'),
                'wk_mangopay_bankwire_enable' => Configuration::get('WK_MP_MANGOPAY_BANKWIRE_ENABLE'),
                'wk_mangopay_save_cards_enable' => Configuration::get('WK_MP_MANGOPAY_SAVE_CARD_ENABLE'),
                'wk_mangopay_save_bank_account_enable' => Configuration::get('WK_MP_MANGOPAY_SAVE_BANK_ACCOUNT_ENABLE'),
            )
        );
        $this->context->controller->addJS(
            _MODULE_DIR_.$this->name.'/views/js/wk_mangopay_configuration.js'
        );
        return $this->display(__FILE__, './views/templates/admin/configuration.tpl');
    }

    // If customer/seller is getting delete then we will delete this seller's mangopay info data
    public function hookActionDeleteGDPRCustomer($customer)
    {
        if ($customer && isset($customer['id'])) {
            $objMangopayConf = new MangopayConfig();
            if (!$objMangopayConf->deleteGdprCustomerMangopayDetails($customer['id'])) {
                return json_encode(
                    $this->l('Seller DHL Information : Unable to delete seller DHL Information.')
                );
            }
        }
    }

    //To trnasfer money to the wallets of sellers and admin if admin set to transfer money after delivered status
    public function hookActionOrderStatusPostUpdate($params)
    {
        $order = new Order($params['id_order']);
        if (isset($order->module) && $order->module == 'mpmangopaypayment') {
            if (Configuration::get('WK_MP_MANGOPAY_TRANSFER_STATUS')) {
                if (isset($params['newOrderStatus']->id)
                    && $params['newOrderStatus']->id == Configuration::get('WK_MP_MANGOPAY_TRANSFER_STATUS')
                ) {
                    $objMgpTransaction = new MangopayTransaction();
                    if ($transDetail = $objMgpTransaction->getTransactionsDetailsByOrderReference($order->reference)) {
                        if ($transDetail['payment_type'] == MangopayTransaction::WK_PAYMENT_TYPE_CARD) {
                            $objTransfer = new MangopayTransferDetails();
                            if (!$objTransfer->getTransferDetailsByOrderReference($order->reference)) {
                                $order = new Order((int) $params['id_order']);
                                $cart = new Cart((int) $order->id_cart);

                                $cartRules = $cart->getCartRules();
                                $cartProducts = $cart->getProducts();
                                $objSplitPayment = new WkMpSellerPaymentSplit();

                                foreach ($cartProducts as $key => $product) {
                                    $orderProductData = $objTransfer->getOrderTimeProductData(
                                        $cart->id,
                                        $product['id_product'],
                                        $product['id_product_attribute']
                                    );
                                    $cartProducts[$key]['price'] = $orderProductData['unit_price_tax_excl'];
                                    $cartProducts[$key]['price_wt'] = $orderProductData['unit_price_tax_incl'];
                                    $cartProducts[$key]['total'] = $orderProductData['total_price_tax_excl'];
                                    $cartProducts[$key]['total_wt'] = $orderProductData['total_price_tax_incl'];
                                }
                                $custComm = $objSplitPayment->paymentGatewaySplitedAmount($cartRules, $cartProducts);
                                $objMgpTransaction = new MangopayTransaction();
                                $newCustComm = $objMgpTransaction->getCustomizedCustomerCommissionArray($custComm);
                                $objMgpTransaction->transferAmountsToWallets(
                                    $newCustComm,
                                    $cart->id,
                                    (int) $cart->id_customer
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    //To show seller display to refund his transfers].
    public function hookDisplayMpOrderDetailProductBottom()
    {
        if (Configuration::get('WK_MP_MANGOPAY_SELLER_REFUND')) {
            $client_id = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
            if ($client_id) {
                $id_order = Tools::getValue('id_order');
                $id_customer = $this->context->customer->id;
                $obj_mgp_mpservice = new MangopayMpService();
                $seller_dtl = WkMpSeller::getSellerDetailByCustomerId($id_customer);
                if ($seller_dtl && $seller_dtl['active']) {
                    $objOrder = new Order($id_order);
                    $orderReference = $objOrder->reference;
                    $objMgpTransaction = new MangopayTransaction();
                    if ($transactionsDetails = $objMgpTransaction->getTransactionsDetailsByOrderReference(
                        $orderReference
                    )) {
                        if ($transactionsDetails['status'] == 'SUCCEEDED') {
                            $id_seller = $seller_dtl['id_seller'];
                            $mgp_transfer_obj = new MangopayTransferDetails();
                            $seller_mgp_transfers = $mgp_transfer_obj->getAllTransferDetailsBySellerByOrderReference(
                                $client_id,
                                $id_seller,
                                $orderReference
                            );
                            $all_transfers_details = array();
                            if ($seller_mgp_transfers) {
                                foreach ($seller_mgp_transfers as $key => $transfer) {
                                    $transfer_details = $obj_mgp_mpservice->getMangopayTransferDetails(
                                        $transfer['transfer_id']
                                    );
                                    if ($transfer_details['Status'] == 'SUCCEEDED') {
                                        $mgpTransferDetails = $mgp_transfer_obj->getTransferDetailByTransferId(
                                            $transfer['transfer_id']
                                        );
                                        $transfer_details['is_refunded'] = $transfer['is_refunded'];
                                        $transfer_details['refunded_by'] = $mgpTransferDetails['refunded_by'];
                                        $transfer_details['send_to_card'] = $mgpTransferDetails['send_to_card'];
                                        $all_transfers_details[$key] = (array) $transfer_details;
                                    }
                                }
                            }
                            $this->context->smarty->assign('all_mangopay_transfers_details', $all_transfers_details);
                        }
                    }
                }
            }
            $this->context->smarty->assign('mgp_client_id', $client_id);
            $objOrder = new Order($id_order);
            if (Validate::isLoadedObject($objOrder)) {
                if (Configuration::get('WK_MP_MANGOPAY_SELLER_REFUND')) {
                    //action when submitting refund buttun by seller
                    if (Tools::isSubmit('partial_mgp_transfer_Refund')) {
                        $mgp_transfer_id = Tools::getValue('mgp_transfer_id');
                        $idOrder = Tools::getValue('id_order');
                        $orderReference = $objOrder->reference;
                        $mgp_user_id = Tools::getValue('mgp_transfer_author_id');
                        $mgp_currency = Tools::getValue('mgp_currency');
                        $amount = $seller_mgp_transfers['0']['amount'];
                        $objMangopayTransaction = new MangopayTransaction();
                        $mgpTransaction = $objMangopayTransaction->getTransactionsDetailsByOrderReference(
                            $orderReference
                        );
                        $mgp_payin_id = $mgpTransaction['transaction_id'];
                        if (!$mgp_transfer_id) {
                            $this->context->controller->errors[] = $this->l('Mangopay Transfer Id is missing for this
                            transaction.');
                        }
                        if (!count($this->context->controller->errors)) {
                            $obj_mgp_mpservice = new MangopayMpService();
                            // Validate if details can be registered to mangopay form mangopay API
                            $params = array('transfer_id' => $mgp_transfer_id, 'author_id' => $mgp_user_id);
                            $mgp_transfer_ref = $obj_mgp_mpservice->createMangopayTransferRefund($params);
                            if ($mgp_transfer_ref['Status'] != 'FAILED') {
                                $refundedBy = 'Seller';
                                $objTransferDetails = new MangopayTransferDetails();
                                $update_transfer_details = $objTransferDetails->updateRefundDetailsByTransferId(
                                    $mgp_transfer_ref['InitialTransactionId'],
                                    $mgp_transfer_ref['Id'],
                                    $refundedBy
                                );

                                if ($update_transfer_details) {
                                    if (Configuration::get('WK_MP_MANGOPAY_TRANSFER_TO') == 2
                                        && $mgpTransaction['payment_type'] != MangopayTransaction::WK_PAYMENT_TYPE_BANKWIRE
                                    ) {
                                        $this->sellerTransferToBuyerCard(
                                            $mgp_payin_id,
                                            $mgp_user_id,
                                            $mgp_currency,
                                            $idOrder,
                                            $orderReference,
                                            $amount,
                                            $id_seller
                                        );
                                    }
                                    if (Configuration::get('WK_MP_MANGOPAY_MAIL_SELLER_REFUND') == 1) {
                                        MangopayTransferDetails::sendMailToAdminOnSellerRefund(
                                            $id_seller,
                                            $orderReference,
                                            1
                                        );
                                    }
                                    Tools::redirect(
                                        $this->context->link->getModuleLink(
                                            'marketplace',
                                            'mporderdetails',
                                            array('transfer_refunded' => 1, 'id_order' => $idOrder)
                                        )
                                    );
                                } else {
                                    Tools::redirect(
                                        $this->context->link->getModuleLink(
                                            'marketplace',
                                            'mporderdetails',
                                            array('mgpErr' => 1, 'id_order' => $idOrder)
                                        )
                                    );
                                }
                            } else {
                                $this->context->smarty->assign('mgpRefundErr', $mgp_transfer_ref['ResultMessage']);
                            }
                        }
                    }
                }
            }
            return $this->fetch('module:mpmangopaypayment/views/templates/hook/seller_mangopay_transfers.tpl');
        }
    }

    // add the tab on my account page.
    public function hookDisplayMPMyAccountMenu()
    {
        if (Configuration::get('WK_MP_MANGOPAY_CLIENTID')) {
            $seller_dtl = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($seller_dtl && $seller_dtl['active']) {
                $this->context->smarty->assign(
                    array(
                        'mpmenu' => '0',
                        'seller_bank_details_enable' => Configuration::get('WK_MP_MANGOPAY_SELLER_BANK_DTL'),
                        'seller_cashout_enable' => Configuration::get('WK_MP_MANGOPAY_SELLER_CASHOUT')
                    )
                );
                return $this->fetch('module:mpmangopaypayment/views/templates/hook/mangopay_menu_link.tpl');
            }
        }
    }

    // add the tab on the bottom of marketplace tab on my account page.
    public function hookDisplayMPMenuBottom()
    {
        $clientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        if (isset($clientId) && $clientId) {
            $id_customer = $this->context->customer->id;
            $seller_dtl = WkMpSeller::getSellerDetailByCustomerId($id_customer);
            if ($seller_dtl && $seller_dtl['active']) {
                $this->context->smarty->assign(
                    array(
                        'mpmenu' => '1',
                        'seller_bank_details_enable' => Configuration::get('WK_MP_MANGOPAY_SELLER_BANK_DTL'),
                        'seller_cashout_enable' => Configuration::get('WK_MP_MANGOPAY_SELLER_CASHOUT')
                    )
                );
                return $this->fetch('module:mpmangopaypayment/views/templates/hook/mangopay_menu_link.tpl');
            }
        }
    }

    // assign the tpl error on order.
    public function hookActionFrontControllerSetMedia($params)
    {
        $controller = Tools::getValue('controller');
        if ('order' == $controller) {
            $jsDef = array(
                'mgp_payment_url' => $this->context->link->getModuleLink('mpmangopaypayment', 'payment'),
                'card_error' => $this->l('Invalid card number.'),
                'exp_error' => $this->l('Invalid expiration date.'),
                'cvv_error' => $this->l('Invalid CVV.'),
                'no_saved_cards_msg' => $this->l('No saved card. Please enter details of the card for payment.'),
                'no_saved_acc_msg' => $this->l('No saved accounts. Please enter details of the account for payment.'),
                'paymentType' => Configuration::get('WK_MP_MANGOPAY_PAYIN_TYPE'),

                'some_error_occurred_txt' => $this->l('Some Error has been occurred. Please try again.'),
                'owner_name_txt' => $this->l('Owner Name'),
                'type_txt' => $this->l('Account Type'),
                'details_txt' => $this->l('Account Details'),
                'confirm_account_deactivate_msg' => $this->l('Are you sure?'),
                'mgp_owner_name_err' => $this->l('Please enter bank account owner name.'),
                'mgp_owner_addrline1_err' => $this->l('Please enter bank account owner required address line.'),
                'mgp_owner_addr_city_err' => $this->l('Please enter bank account owner city.'),
                'mgp_owner_addr_zipcode_err' => $this->l('Please enter bank account owner postal code.'),
                'mgp_owner_addr_region_err' => $this->l('Please enter bank account owner region.'),
                'mgp_owner_addr_country_err' => $this->l('Please select bank account owner country.'),
                'mgp_iban_err' => $this->l('Field IBAN is mandatory.'),
                'mgp_bic_err' => $this->l('Field BIC is mandatory.'),
                'mgp_account_number_err' => $this->l('Field Account Number is mandatory.'),
                'mgp_sort_code_err' => $this->l('Field Sort Code is mandatory.'),
                'mgp_country_err' => $this->l('Field Country is mandatory.'),
            );
            Media::addJsDef($jsDef);
            $this->context->controller->registerJavascript(
                'module-wk-jsmangopay',
                'modules/mpmangopaypayment/views/js/mangopay.js'
            );
            $this->context->controller->registerStylesheet(
                'module-wk-cssmangopay',
                'modules/mpmangopaypayment/views/css/mangopay.css'
            );
            $this->context->controller->addjQueryPlugin('growl', null, false);
        }
    }

    public function hookPaymentOptions($params)
    {
        if (Configuration::get('WK_MP_MANGOPAY_CLIENTID')
            && Configuration::get('WK_MP_MANGOPAY_USERID')
            && Configuration::get('WK_MP_MANGOPAY_WALLETID')
        ) {
            $paymentsToShow = array();
            $smartyVars = array();
            if (Configuration::get('WK_MP_MANGOPAY_TITLE')) {
                $smartyVars['title'] = Configuration::get('WK_MP_MANGOPAY_TITLE');
            }
            if ($countries = Country::getCountries($this->context->language->id, false, false, false)) {
                $smartyVars['countries'] = $countries;
            }
            $smartyVars['payment_type'] = Configuration::get('WK_MP_MANGOPAY_PAYIN_TYPE');
            $smartyVars['card_type'] = unserialize(Configuration::get('WK_MP_MANGOPAY_CREDITCARD'));
            //if payment type is direct card payment from wesite then remove cards does not support for this payIn
            if ($smartyVars['payment_type'] == 1) {
                $onlyWebCards = array('Przelewy24', 'iDeal', 'PayLib');
                $cardKeys = array_keys($smartyVars['card_type']);
                foreach ($cardKeys as $cardType) {
                    if (in_array($cardType, $onlyWebCards)) {
                        unset($smartyVars['card_type'][$cardType]);
                    }
                }
            }
            $smartyVars['payment_controller_link'] = $this->context->link->getModuleLink(
                'mpmangopaypayment',
                'payment'
            );
            $smartyVars['this_path'] = $this->_path;
            $smartyVars['module_dir'] = _MODULE_DIR_;
            $smartyVars['img_ps_dir'] = _PS_IMG_;
            $smartyVars['this_path_ssl'] = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.
            $this->name.'/';
            $smartyVars['save_card_enable'] =  Configuration::get('WK_MP_MANGOPAY_SAVE_CARD_ENABLE');
            $smartyVars['save_bank_account_enable'] =  Configuration::get('WK_MP_MANGOPAY_SAVE_BANK_ACCOUNT_ENABLE');

            $objMangopayBuyer = new MangopayBuyer();
            if ($userMangopayData = $objMangopayBuyer->getExistingBuyerMangopayData($this->context->customer->id)) {
                $objMangopayService = new MangopayMpService();
                if (isset($userMangopayData['mgp_userid']) && $userMangopayData['mgp_userid']) {
                    // get customers regietered cards id saved cards payments enabled
                    // Get customer registered bank details.. If direct debit payment is enable
                    if (Configuration::get('WK_MP_MANGOPAY_CARD_PAY_ENABLE')
                        && Configuration::get('WK_MP_MANGOPAY_SAVE_CARD_ENABLE')
                    ) {
                        $userCards = $objMangopayService->getCustomerMangopayRegisteredCardsDetails(
                            $userMangopayData['mgp_userid']
                        );
                        $cards = unserialize(Configuration::get('WK_MP_MANGOPAY_CREDITCARD'));
                        if (count($userCards) && (!isset($userCards['Status']) || $userCards['Status'] != 'FAILED')) {
                            foreach ($userCards as $key => $card) {
                                if (!$card->Active || !in_array($card->CardType, $cards)) {
                                    unset($userCards[$key]);
                                } else {
                                    $userCards[$key]->Id = MangopayConfig::encryptString(
                                        $card->Id
                                    );
                                }
                            }
                            $smartyVars['userCards'] = $userCards;
                        }
                    }
                    // Get customer registered bank details.. If direct debit payment is enable
                    if (Configuration::get('WK_MP_MANGOPAY_DIRECT_DEBIT_ENABLE')
                        && Configuration::get('WK_MP_MANGOPAY_SAVE_BANK_ACCOUNT_ENABLE')
                    ) {
                        $userBankAccounts = $objMangopayService->getMangopayBankAccounts(
                            $userMangopayData['mgp_userid'],
                            1
                        );
                        if ($userBankAccounts && count($userBankAccounts)) {
                            foreach ($userBankAccounts as $key => $account) {
                                if (empty($account->Active) || !$account->Active) {
                                    unset($userBankAccounts[$key]);
                                } else {
                                    $userBankAccounts[$key]->Id = MangopayConfig::encryptString(
                                        $account->Id
                                    );
                                }
                            }
                            $smartyVars['userBankAccounts'] = $userBankAccounts;
                        }
                    }
                }
            }
            $this->smarty->assign($smartyVars);
            if (Configuration::get('WK_MP_MANGOPAY_CARD_PAY_ENABLE')) {
                $payByCard = new PaymentOption();
                // insert mangopay card direct payment method
                $paymentForm = $this->fetch('module:mpmangopaypayment/views/templates/hook/payment.tpl');
                $payByCard->setCallToActionText(
                    $this->trans('Mangopay Card Payment', array(), 'Modules.MpMangopayPayment.Shop')
                )
                    ->setModuleName($this->name.'_card_type')
                    ->setForm($paymentForm)
                    ->setInputs(array('type_payment' => 'mgp_card' ))
                    ->setAction($this->context->link->getModuleLink($this->name, 'payment'));

                $paymentsToShow[] = $payByCard;
            }

            // insert mangopay bankwire payment method
            if (Configuration::get('WK_MP_MANGOPAY_BANKWIRE_ENABLE')) {
                $payByBankwire = new PaymentOption();
                $paymentForm = $this->fetch('module:mpmangopaypayment/views/templates/hook/prebankwire.tpl');
                $payByBankwire->setCallToActionText(
                    $this->trans('Mangopay Bankwire Payment', array(), 'Modules.MpMangopayPayment.Shop')
                )
                    ->setModuleName($this->name.'_bankwire_type')
                    ->setAdditionalInformation($paymentForm)
                    ->setAction($this->context->link->getModuleLink($this->name, 'bankwirevalidation'));

                $paymentsToShow[] = $payByBankwire;
            }
            // insert direct debit payment method
            if (Configuration::get('WK_MP_MANGOPAY_DIRECT_DEBIT_ENABLE')) {
                $payByDirectDebit = new PaymentOption();
                $paymentForm = $this->fetch('module:mpmangopaypayment/views/templates/hook/direct_debit_account.tpl');
                $payByDirectDebit->setCallToActionText(
                    $this->trans('Mangopay Direct Debit Payment', array(), 'Modules.MpMangopayPayment.Shop')
                )
                    ->setModuleName($this->name.'_direct_debit_type')
                    ->setForm($paymentForm)
                    ->setAction($this->context->link->getModuleLink($this->name, 'payment'));

                $paymentsToShow[] = $payByDirectDebit;
            }
            return $paymentsToShow;
        }
        return array();
    }

    // Display the payment return.
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }
        $objOrder = $params['order'];
        if (!isset($objOrder) || ($objOrder->module != $this->name)) {
            return false;
        }
        $smartyVars = array();
        if (isset($objOrder)
            && Validate::isLoadedObject($objOrder)
            && isset($objOrder->valid)
        ) {
            $smartyVars['id_order'] = $objOrder->id;
            $smartyVars['valid'] = $objOrder->valid;
        }
        if (isset($objOrder->reference) && !empty($objOrder->reference)) {
            $objMgpTransac = new MangopayTransaction();
            if ($mgpBankWireDetails = $objMgpTransac->getBankWirePayinByOrderReference($objOrder->reference)) {
                $smartyVars['valid'] = 1;
                $smartyVars['bankwire'] = $mgpBankWireDetails;
            }
            $smartyVars['reference'] = $objOrder->reference;
        }
        if (isset($objOrder->reference) && !empty($objOrder->reference)) {
            $this->smarty->assign('reference', $objOrder->reference);
        }
        $smartyVars['total_paid'] = Tools::displayPrice($objOrder->total_paid);
        $smartyVars['shop_name'] = $this->context->shop->name;
        $smartyVars['contact_url'] = $this->context->link->getPageLink('contact', true);

        $this->smarty->assign($smartyVars);
        return $this->fetch('module:mpmangopaypayment/views/templates/hook/payment_return.tpl');
    }

    // dispaly the bank account details.
    public function hookDisplayAdminSellerDetailViewRightColumn()
    {
        $id_seller = Tools::getValue('id_seller');
        $obj_mgp_seller = new MangopayMpSeller();
        $obj_mgp_mpservice = new MangopayMpService();

        if (Tools::isSubmit('mgp_bank_account_deactivate')) {
            $mgp_bank_account_deactivate = (array) $obj_mgp_mpservice->deactivateBankAccount(
                Tools::getValue('mgp_bank_author_id'),
                Tools::getValue('mgp_bank_account_id')
            );
            if (isset($mgp_bank_account_deactivate['Id']) && $mgp_bank_account_deactivate['Id']) {
                $idSeller = Tools::getValue('id_seller');
                $link = $this->context->link->getAdminLink('AdminSellerInfoDetail');
                Tools::redirectAdmin($link.'&id_seller='.$idSeller.'&viewwk_mp_seller&conf=3');
            } else {
                $this->errors[] = $mgp_bank_account_deactivate['ResultMessage'];
            }
        }

        $seller_mgp_dtls = $obj_mgp_seller->sellerMangopayDetails($id_seller);
        if ($seller_mgp_dtls && $seller_mgp_dtls['mgp_userid']) {
            $mgp_registered_bank_acc_ids = $obj_mgp_mpservice->getMangopayBankAccounts(
                $seller_mgp_dtls['mgp_userid']
            );
            if ($mgp_registered_bank_acc_ids) {
                $this->context->smarty->assign('mgp_registered_bank_acc_ids', $mgp_registered_bank_acc_ids);
            }
        }
        $this->context->controller->addJS(_MODULE_DIR_.'mpmangopaypayment/views/js/mangopay_bank_details.js');
        return $this->display(__FILE__, 'seller_mangopay_details.tpl');
    }

    // If admin submit for registering his bank details to the mangopay account
    public function hookActionAdminMangopayBankDetailsListingResultsModifier($params)
    {
        //If admin submit for registering his bank details to the mangopay account
        $params['list'] = array();
        $admin_mgp_usr_id = Configuration::get('WK_MP_MANGOPAY_USERID');
        if ($admin_mgp_usr_id) {
            $obj_mgp_mpservice = new MangopayMpService();
            // Validate if details can be registered to mangopay form mangopay API
            $mgp_registered_bank_acc_ids = $obj_mgp_mpservice->getMangopayBankAccounts($admin_mgp_usr_id);
            if ($mgp_registered_bank_acc_ids) {
                foreach ($mgp_registered_bank_acc_ids as $key => $value) {
                    $mgp_registered_bank_acc_ids[$key] = (array) $value;
                }
                $params['list'] = $mgp_registered_bank_acc_ids;
            }
        }
        $params['list_total'] = count($params['list']);
    }

    //To add some custom fields to the list].
    public function hookActionAdminMangopaySellerBankDetailsListingResultsModifier($params)
    {
        if ($params['list']) {
            $obj_mgp_mpservice = new MangopayMpService();
            $obj_mgp_seller = new MangopayMpSeller();
            foreach ($params['list'] as $key => $row) {
                $seller_mgp_dtls = $obj_mgp_seller->sellerMangopayDetails($row['id_seller']);
                if ($seller_mgp_dtls && $seller_mgp_dtls['mgp_userid']) {
                    $mgp_registered_bank_acc_ids = $obj_mgp_mpservice->getMangopayBankAccounts(
                        $seller_mgp_dtls['mgp_userid']
                    );
                    if ($mgp_registered_bank_acc_ids) {
                        $params['list'][$key]['num_accounts'] = count($mgp_registered_bank_acc_ids);
                    } else {
                        $params['list'][$key]['num_accounts'] = 0;
                    }
                } else {
                    $params['list'][$key]['num_accounts'] = 0;
                }
                $isMgpSellerExist = MangopayMpSeller::sellerMangopayDetails($row['id_seller']);
                if ($isMgpSellerExist) {
                    $params['list'][$key]['mgp_user'] = $this->l('Yes');
                } else {
                    $params['list'][$key]['mgp_user'] = $this->l('No');
                }
            }
        }
    }

    // Uninstall the module and tab
    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$this->dropTable()
            || !$this->unLinkFiles()
            || !$this->deleteConfigurationVariable()
        ) {
            return false;
        }
        return true;
    }

    // Delete the tables.
    public function dropTable()
    {
        if (Configuration::get('WK_MP_PREVIOUS_DATA_DELETE')) {
            return Db::getInstance()->execute(
                'DROP TABLE IF EXISTS
                `'._DB_PREFIX_.'wk_mp_mangopay_config`,
                `'._DB_PREFIX_.'wk_mp_mangopay_seller`,
                `'._DB_PREFIX_.'wk_mp_mangopay_buyer`,
                `'._DB_PREFIX_.'wk_mp_mangopay_seller_country`,
                `'._DB_PREFIX_.'wk_mp_mangopay_payin_refund`,
                `'._DB_PREFIX_.'wk_mp_mangopay_transfer_details`,
                `'._DB_PREFIX_.'wk_mp_mangopay_bankwire_details`,
                `'._DB_PREFIX_.'wk_mp_mangopay_transaction`'
            );
        } else {
            return true;
        }
    }

    // delete the configuration variable
    public function deleteConfigurationVariable()
    {
        $keys = array(
            'WK_MP_MANGOPAY_USERID',
            'WK_MP_MANGOPAY_WALLETID',
            'WK_MP_MANGOPAY_CLIENTID',
            'WK_MP_MANGOPAY_PASSPHRASE',
            'WK_MP_MANGOPAY_CREDITCARD',
            'WK_MP_MANGOPAY_CURRENCY',
            'WK_MP_MANGOPAY_MODE',
            'WK_MP_MANGOPAY_TITLE',
            'WK_MP_MANGOPAY_SELLER_REFUND',
            'WK_MP_MANGOPAY_TRANSFER_TO',
            'WK_MP_MANGOPAY_SELLER_CASHOUT',
            'WK_MP_MANGOPAY_SELLER_BANK_DTL',
            'WK_MP_MANGOPAY_TRANSFER_STATUS',
            'WK_MP_MANGOPAY_PAYIN_TYPE',
            'WK_MP_MANGOPAY_MAIL_SELLER_REFUND',
            'WK_MP_MANGOPAY_MAIL_ADMIN_REFUND',
            'WK_MP_PREVIOUS_DATA_DELETE',
            'WK_MP_MANGOPAY_CARD_PAY_ENABLE',
            'WK_MP_MANGOPAY_DIRECT_DEBIT_ENABLE',
            'WK_MP_MANGOPAY_BANKWIRE_ENABLE',
            'WK_MP_MANGOPAY_SAVE_CARD_ENABLE',
            'WK_MP_MANGOPAY_SAVE_BANK_ACCOUNT_ENABLE',
            'WK_MP_MANGOPAY_PAY_SUCCEED_HOOK',
        );
        foreach ($keys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }
        return true;
    }

    // Uninstall the tab.
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

    // Delete the configuartion file.
    public function unLinkFiles()
    {
        if (file_exists(_PS_MODULE_DIR_.'mpmangopaypayment/temp/MangoPaySdkStorage.tmp.php')) {
            return @unlink(_PS_MODULE_DIR_.'mpmangopaypayment/temp/MangoPaySdkStorage.tmp.php');
        } else {
            return true;
        }
    }

    // Save the seller mangopay configuration
    public function saveMailMangopayConfiguration()
    {
        Configuration::updateValue('WK_MP_MANGOPAY_MAIL_ADMIN_REFUND', Tools::getValue('is_mail_refundby_admin'));
        Configuration::updateValue('WK_MP_MANGOPAY_MAIL_SELLER_REFUND', Tools::getValue('is_mail_refundby_seller'));
        return true;
    }

    // Save the account configuration details.
    private function saveMangopayAccountConfiguration()
    {
        $credit_cards = Tools::getValue('creditcard');
        $supportedCards = array();
        foreach ($credit_cards as $value) {
            if ($value == 'Visa') {
                $supportedCards[$value] = 'CB_VISA_MASTERCARD';
            } elseif ($value == 'Mastercard') {
                $supportedCards[$value] = 'CB_VISA_MASTERCARD';
            } elseif ($value == 'Carte Bleue') {
                $supportedCards[$value] = 'CB_VISA_MASTERCARD';
            } elseif ($value == 'Maestro') {
                $supportedCards[$value] = 'MAESTRO';
            } elseif ($value == 'Diners') {
                $supportedCards[$value] = 'DINERS';
            } elseif ($value == 'Przelewy24') {
                $supportedCards[$value] = 'P24';
            } elseif ($value == 'iDeal') {
                $supportedCards[$value] = 'IDEAL';
            // } elseif ($value == 'Bancontact/Mister Cash') {
            //     $supportedCards[$value] = 'BCMC';
            } elseif ($value == 'PayLib') {
                $supportedCards[$value] = 'PAYLIB';
            }
        }
        Configuration::updateValue('WK_MP_MANGOPAY_CLIENTID', Tools::getValue('clientid'));
        Configuration::updateValue('WK_MP_MANGOPAY_PASSPHRASE', Tools::getValue('passphrase'));
        Configuration::updateValue('WK_MP_MANGOPAY_CREDITCARD', serialize($supportedCards));
        Configuration::updateValue('WK_MP_MANGOPAY_CURRENCY', Tools::getValue('currency'));
        Configuration::updateValue('WK_MP_MANGOPAY_MODE', Tools::getValue('mode'));
        Configuration::updateValue('WK_MP_MANGOPAY_TITLE', Tools::getValue('title'));
        return true;
    }

    // Save the seller mangopay configuration
    public function saveSellerMangopayConfiguration()
    {
        Configuration::updateValue('WK_MP_MANGOPAY_SELLER_REFUND', Tools::getValue('is_seller_refund'));
        Configuration::updateValue('WK_MP_MANGOPAY_SELLER_CASHOUT', Tools::getValue('is_seller_cashout'));
        Configuration::updateValue('WK_MP_MANGOPAY_SELLER_BANK_DTL', Tools::getValue('is_seller_bank_dtl'));
        return true;
    }

    // Save the payment configuration details
    private function saveMangopayPaymentConfiguration()
    {
        Configuration::updateValue('WK_MP_MANGOPAY_TRANSFER_TO', Tools::getValue('mgp_transfer_in'));
        Configuration::updateValue(
            'WK_MP_MANGOPAY_TRANSFER_STATUS',
            Tools::getValue('mgp_transfer_order_status')
        );
        Configuration::updateValue('WK_MP_MANGOPAY_PAYIN_TYPE', Tools::getValue('mgp_payin_type'));
        Configuration::updateValue('WK_MP_PREVIOUS_DATA_DELETE', Tools::getValue('mangopay_payment_data_delete'));
        Configuration::updateValue('WK_MP_MANGOPAY_BANKWIRE_ENABLE', Tools::getValue('wk_mangopay_bankwire_enable'));
        Configuration::updateValue('WK_MP_MANGOPAY_SAVE_CARD_ENABLE', Tools::getValue('wk_mangopay_save_cards_enable'));
        Configuration::updateValue(
            'WK_MP_MANGOPAY_SAVE_BANK_ACCOUNT_ENABLE',
            Tools::getValue('wk_mangopay_save_bank_account_enable')
        );
        Configuration::updateValue(
            'WK_MP_MANGOPAY_CARD_PAY_ENABLE',
            Tools::getValue('wk_mangopay_card_pay_enable')
        );
        Configuration::updateValue(
            'WK_MP_MANGOPAY_DIRECT_DEBIT_ENABLE',
            Tools::getValue('wk_mangopay_direct_debit_enable')
        );
        return true;
    }

    // Create the mangopay user.
    private function createMangoPayUser($idClient, $passPhrase, $currencyIso, $paymentMode)
    {
        try {
            $objMangopayApi = new MangoPay\MangoPayApi();
            if ($paymentMode == 'sandbox') {
                $objMangopayApi->Config->BaseUrl = 'https://api.sandbox.mangopay.com';
            } else {
                $objMangopayApi->Config->BaseUrl = 'https://api.mangopay.com';
            }
            $objMangopayApi->Config->ClientId = $idClient;
            $objMangopayApi->Config->ClientPassword = $passPhrase;
            $objMangopayApi->Config->TemporaryFolder = _PS_MODULE_DIR_.'mpmangopaypayment/temp/';

            $this->unLinkFiles();
            $idEmployee = MangopayConfig::getSupperAdmin();
            $objLegalUser = new MangoPay\UserLegal();
            if (Validate::isLoadedObject($objEmployee = new Employee($idEmployee))) {
                $objLegalUser->Name = $objEmployee->firstname.' '.$objEmployee->lastname;
                $objLegalUser->PersonType = 'LEGAL';
                $objLegalUser->LegalPersonType = 'BUSINESS';
                $objLegalUser->LegalRepresentativeFirstName = $objEmployee->firstname;
                $objLegalUser->LegalRepresentativeLastName = $objEmployee->lastname;
                $objLegalUser->LegalRepresentativeEmail = $objEmployee->email;
                $objLegalUser->LegalRepresentativeNationality = Country::getIsoById(
                    Configuration::get('PS_COUNTRY_DEFAULT')
                );
                $objLegalUser->LegalRepresentativeCountryOfResidence = Country::getIsoById(
                    Configuration::get('PS_COUNTRY_DEFAULT')
                );
                $objLegalUser->LegalRepresentativeBirthday = 1404111618;
                //Date:2014-06-30 Prestashop does not provide employee birthday
                $objLegalUser->Email = $objEmployee->email;
                $objLegalUser->Tag = 'Admin';
                $resultUser = $objMangopayApi->Users->Create($objLegalUser);
                if ($mangopayUserId = $resultUser->Id) {
                    $objWallet = new MangoPay\Wallet();
                    $objWallet->Owners = array($mangopayUserId);
                    $objWallet->Description = 'Wallet';
                    $objWallet->Currency = $currencyIso;
                    $resultWallet = $objMangopayApi->Wallets->Create($objWallet);
                    $mangopayWalletId = $resultWallet->Id;
                    if ($mangopayWalletId) {
                        Configuration::updateValue('WK_MP_MANGOPAY_CURRENCY', $currencyIso);
                        Configuration::updateValue('WK_MP_MANGOPAY_USERID', $mangopayUserId);
                        Configuration::updateValue('WK_MP_MANGOPAY_WALLETID', $mangopayWalletId);
                        $params = array(
                            'mgp_clientid' => $idClient,
                            'id_employee' => $this->context->cookie->id_employee,
                            'mgp_passphrase' => $passPhrase,
                            'mgp_userid' => $mangopayUserId,
                            'mgp_walletid' => $mangopayWalletId,
                            'currency_iso' => $currencyIso,
                            'user_type' => 'admin',
                            'user_email' => $objEmployee->email,
                        );
                        $saveMangopayConfiguration = MangopayConfig::saveMangopayConfigurationData($params);
                        if (!$saveMangopayConfiguration) {
                            error_log(
                                date('[Y-m-d H:i e] ').
                                'Mangopay Payment Error: error while saving data in MangopayConfig.'.PHP_EOL,
                                3,
                                _PS_MODULE_DIR_.'mpmangopaypayment/error.log'
                            );
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return $e->GetMessage();
        }
    }

    // Transfer the amount from seller account to buyer account.
    public function sellerTransferToBuyerCard(
        $mgp_payin_id,
        $mgp_user_id,
        $mgp_currency,
        $idOrder,
        $orderReference,
        $amount,
        $id_seller
    ) {
        if (!$mgp_payin_id) {
            $this->context->controller->errors[] = $this->l('Mangopay Pay In Id is missing for this transaction.');
        }
        if (!$mgp_currency) {
            $this->context->controller->errors[] = $this->l('Mangopay Pay In currency is missing for this transaction.');
        }
        if (!$mgp_user_id) {
            $this->context->controller->errors[] = $this->l('Mangopay Author Id is missing for this transaction.');
        }
        if (!count($this->context->controller->errors)) {
            $obj_mgp_mpservice = new MangopayMpService();
            $params = array(
                'payin_id' => $mgp_payin_id,
                'author_id' => $mgp_user_id,
                'amount' => $amount,
                'currency' => $mgp_currency
            );
            $mgp_payin_ref = $obj_mgp_mpservice->createMangopayPayInRefund($params);
            if ($mgp_payin_ref['Status'] != 'FAILED') {
                $obj_payin_refund = new MangopayPayInRefund();
                $obj_payin_refund->payin_id = $mgp_payin_id;
                $obj_payin_refund->amount = $amount;
                $obj_payin_refund->refund_id = $mgp_payin_ref['Id'];
                $obj_payin_refund->save();
                $objTransferDetails = new MangopayTransferDetails();
                $objTransferDetails->updatePayInRefundDetailsBySellerIdOrderReference(
                    $id_seller,
                    $orderReference
                );
                if (Configuration::get('WK_MP_MANGOPAY_MAIL_SELLER_REFUND') == 1) {
                    MangopayTransferDetails::sendMailToAdminOnSellerRefund($id_seller, $orderReference, 2);
                }
                Tools::redirect(
                    $this->context->link->getModuleLink(
                        'marketplace',
                        'mporderdetails',
                        array('payin_refunded' => 1, 'id_order' => $idOrder)
                    )
                );
            } else {
                if (isset($mgp_payin_ref['ResultMessage']) && $mgp_payin_ref['ResultMessage']) {
                    $this->context->smarty->assign('mgpRefundErr', $mgp_payin_ref['ResultMessage']);
                } else {
                    $this->context->smarty->assign(
                        'mgpRefundErr',
                        $this->l('Some error occured while PayIn refund. Please try again.')
                    );
                }
            }
        }
    }
}
