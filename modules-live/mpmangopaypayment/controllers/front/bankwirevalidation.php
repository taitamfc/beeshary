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

class MpMangopayPaymentBankwireValidationModuleFrontController extends ModuleFrontController
{
    private $objApi;
    private $adminMangopayWalletId;
    private $buyerMangopayUserId;
    private $buyerMangopayWalletId;

    public function __construct()
    {
        parent::__construct();
        $this->paymentErrors = array();

        $this->objApi = new MangoPay\MangoPayApi();
        if (Configuration::get('WK_MP_MANGOPAY_MODE') == 'sandbox') {
            $this->objApi->Config->BaseUrl = 'https://api.sandbox.mangopay.com';
        } else {
            $this->objApi->Config->BaseUrl = 'https://api.mangopay.com';
        }

        $this->objApi->Config->ClientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        $this->objApi->Config->ClientPassword = Configuration::get('WK_MP_MANGOPAY_PASSPHRASE');
        $this->objApi->Config->TemporaryFolder = _PS_MODULE_DIR_.'mpmangopaypayment/temp/';
        $this->adminMangopayWalletId = Configuration::get('WK_MP_MANGOPAY_WALLETID');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Mangopay Payment', array(), 'Breadcrumb'),
            'url' => '',
        );

        return $breadcrumb;
    }

    public function initContent()
    {
        if ($this->module->active) {
            try {
                parent::initContent();
                $idCustomer = $this->context->customer->id;
                $cart = $this->context->cart;
                $addressDetails = Address::getCountryAndState($cart->id_address_invoice);
                $customerCountryIso = Country::getIsoById($addressDetails['id_country']);
                //Validate Cart
                if (!Validate::isLoadedObject($cart) && Order::getOrderByCartId((int) $this->context->cart->id)) {
                    Tools::redirect($this->context->link->getPageLink('order'));
                } else {
                    $this->createByuerMangopayWallet($idCustomer, $customerCountryIso);
                    if ($this->buyerMangopayWalletId) {
                    } else {
                        $this->paymentErrors[] = $this->module->l(
                            'Some error occurred while creating buyer\' wallet',
                            'bankwirevalidation'
                        );
                    }
                    //Validate currency
                    $currency = new Currency(
                        (int) Currency::getIdByIsoCode(Configuration::get('WK_MP_MANGOPAY_CURRENCY'))
                    );
                    if (!Validate::isLoadedObject($currency)) {
                        $this->paymentErrors[] = $this->module->l('Invalid Currency', 'bankwirevalidation');
                    } else {
                        //Creating buyer mangopay natural userid and wallet
                        $totalAmount = $cart->getOrderTotal(true);
                        $fees = 0; //Convert into cent,DebitedFunds – Fees = CreditedFunds (amount received on wallet)
                        // amount conversion to the amount for wich currency mangopay account is configured
                        if ($currency->id != $cart->id_currency) {
                            $totalAmount = Tools::convertPriceFull(
                                $totalAmount,
                                new Currency($cart->id_currency),
                                new Currency($currency->id)
                            );
                        }
                        $bankwirePayment = $this->mangopayPayIn(Tools::ps_round($totalAmount, 6), $fees);

                        if ($bankwirePayment->Status == 'CREATED' || $bankwirePayment->Status == 'SUCCEEDED') {
                            $orderStatus = (int) Configuration::get('PS_OS_MANGOPAY_BANKWIRE');
                            if ($bankwirePayment->Status == 'SUCCEEDED') {
                                $orderStatus = (int) Configuration::get('PS_OS_PAYMENT');
                            }

                            if ($this->context->cart->OrderExists()) {
                                //If the order already exists, we need to update the order status
                                $objOrder = new Order((int) Order::getOrderByCartId($this->context->cart->id));
                                $objOrderHistory = new OrderHistory();
                                $objOrderHistory->id_order = (int) $objOrder->id;
                                $objOrderHistory->changeIdOrderState($orderStatus, $objOrder, true);
                                $objOrderHistory->addWithemail(true);
                            } else {
                                // send mail to the customer for awaiting bankwire payment of mangopay
                                $mailVars = array(
                                    '{bankwire_owner}' => $bankwirePayment->PaymentDetails->BankAccount->OwnerName,
                                    '{bankwire_reference}' => $bankwirePayment->PaymentDetails->WireReference,
                                    '{bankwire_details}' => "<br />".
                                    $this->module->l("Account IBAN: ", 'bankwirevalidation').
                                    $bankwirePayment->PaymentDetails->BankAccount->Details->IBAN."<br />".
                                    $this->module->l("Account BIC: ", 'bankwirevalidation')
                                    .$bankwirePayment->PaymentDetails->BankAccount->Details->BIC."<br />".
                                    $this->module->l("Bankwire Reference: ", 'bankwirevalidation')
                                    .$bankwirePayment->PaymentDetails->WireReference,
                                    '{bankwire_address}' => ''
                                );
                                if ($this->module->validateOrder(
                                    (int) $this->context->cart->id,
                                    $orderStatus,
                                    (float) $this->context->cart->getOrderTotal(true),
                                    $this->module->displayName,
                                    null,
                                    $mailVars,
                                    null,
                                    false,
                                    $this->context->cart->secure_key
                                )) {
                                    //Saving bankwire details
                                    $objMangopayTransaction = new MangopayTransaction();
                                    if ($idTransaction = $objMangopayTransaction->saveTransactionDetails(
                                        $bankwirePayment,
                                        $this->module->currentOrderReference
                                    )) {
                                        $paymentDtl = $bankwirePayment->PaymentDetails;
                                        $objWireDtl = new MangopayMpBankwireDetails();
                                        $objWireDtl->id_mangopay_transaction = $idTransaction;
                                        $objWireDtl->mgp_wire_reference = $paymentDtl->WireReference;
                                        $objWireDtl->mgp_account_type = $paymentDtl->BankAccount->Type;
                                        $objWireDtl->mgp_account_owner_name = $paymentDtl->BankAccount->OwnerName;
                                        $objWireDtl->mgp_account_iban = $paymentDtl->BankAccount->Details->IBAN;
                                        $objWireDtl->mgp_account_bic = $paymentDtl->BankAccount->Details->BIC;
                                        $objWireDtl->declared_amount = $paymentDtl->DeclaredDebitedFunds->Amount / 100;
                                        $objWireDtl->save();
                                    }
                                }
                            }
                            Tools::redirect(
                                'index.php?controller=order-confirmation&id_cart='.(int) $this->context->cart->id.
                                '&id_module='.(int) $this->module->id.'&id_order='.$this->module->currentOrder.'&key='.
                                $this->context->cart->secure_key
                            );
                        } else {
                            $this->paymentErrors[] = $this->module->l(
                                'Some error has been occurred. Please try again.',
                                'bankwirevalidation'
                            );
                        }
                    }
                }
            } catch (Exception $e) {
                $this->paymentErrors[] = $e->getMessage();
            }
        } else {
            $this->paymentErrors[] = $this->module->l(
                'Currently Mangopay Bankwire Payment is not available. Please try again later.',
                'bankwirevalidation'
            );
        }

        if (count($this->paymentErrors)) {
            $this->context->smarty->assign('mangopay_error_messages', $this->paymentErrors);
            $this->setTemplate('module:mpmangopaypayment/views/templates/front/error_messages.tpl');
        }
    }

    private function mangopayPayIn($totalMmount, $fees)
    {
        try {
            $objPayin = new MangoPay\PayIn();
            $objPayin->CreditedWalletId = $this->buyerMangopayWalletId;
            $objPayin->AuthorId = $this->buyerMangopayUserId;
            $objPayin->PaymentType = "BankWire";
            $objPayin->PaymentDetails = new \MangoPay\PayInPaymentDetailsBankWire();
            $objPayin->DeclaredDebitedFunds = new \MangoPay\Money();
            $objPayin->DeclaredDebitedFunds->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $objPayin->DeclaredDebitedFunds->Amount = $totalMmount * 100;
            $objPayin->DeclaredFees = new \MangoPay\Money();
            $objPayin->DeclaredFees->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $objPayin->DeclaredFees->Amount = $fees * 100;
            $objPayin->ExecutionType = 'DIRECT';
            $objPayin->ExecutionDetails = new \MangoPay\PayInExecutionDetailsDirect();
            $objPayin->ExecutionDetails->SecureMode = 'DEFAULT';
            $objPayin->ExecutionDetails->SecureModeReturnURL = $this->context->link->getModuleLink(
                'mpmangopaypayment',
                'success'
            );
            return $this->objApi->PayIns->Create($objPayin);  // create Pay-In
        } catch (\MangoPay\ResponseException $e) {
            $this->paymentErrors[] = $this->module->l('Message: ', 'bankwirevalidation').$e->getMessage();
        }
    }

    private function createByuerMangopayWallet($idCustomer, $customerCountryIso)
    {
        $objMgpBuyer = new MangopayBuyer();
        $mgpData = $objMgpBuyer->getBuyerMangopayData($idCustomer, Configuration::get('WK_MP_MANGOPAY_CURRENCY'));
        if (!$mgpData) {    //If not exist
            $existMgpData = $objMgpBuyer->getExistingBuyerMangopayData($idCustomer);
            if (!$existMgpData) {
                //Create mangopay userid
                $objCustomer = new Customer($idCustomer);
                $objNaturalUser = new MangoPay\UserNatural();
                $objNaturalUser->PersonType = 'NATURAL';
                $objNaturalUser->Email = $objCustomer->email;
                $objNaturalUser->FirstName = $objCustomer->firstname;
                $objNaturalUser->LastName = $objCustomer->lastname;
                $birthday = ($objCustomer->birthday == '0000-00-00' ? 1404111618 : strtotime($objCustomer->birthday));
                $objNaturalUser->Birthday = $birthday;
                $objNaturalUser->Nationality = $customerCountryIso;
                $objNaturalUser->CountryOfResidence = $customerCountryIso;
                $objNaturalUser->Tag = $this->module->l('Buyer', 'payment');
                $userResult = $this->objApi->Users->Create($objNaturalUser);

                if ($userResult->Id) {
                    $this->buyerMangopayUserId = $userResult->Id;
                } else {
                    $this->paymentErrors[] = $this->module->l(
                        'Some error occurred while creating customer as mangopay user. Please try again.',
                        'bankwirevalidation'
                    );
                }
                //Create buyer walletid
                $objWallet = new MangoPay\Wallet();
                $objWallet->Owners = array($this->buyerMangopayUserId);
                $objWallet->Description = $this->module->l('Buyer\'s wallet', 'bankwirevalidation');
                $objWallet->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
                $walletResult = $this->objApi->Wallets->Create($objWallet);

                if ($walletResult->Id) {
                    $this->buyerMangopayWalletId = $walletResult->Id;
                } else {
                    $this->paymentErrors[] = $this->module->l(
                        'Some error occurred while creating customer as mangopay user. Please try again.',
                        'bankwirevalidation'
                    );
                }
                //save Mangopay Buyer Info
                if (!$this->saveMangopayBuyerInfo($idCustomer)) {
                    $this->paymentErrors[] = $this->module->l(
                        'Some error occurred while Buyer wallet information.',
                        'bankwirevalidation'
                    );
                }
            } else {
                //if currecy updated have to create new wallet for customer
                $objMgpService = new MangopayMpService();
                $mgpBuyerWalletId = $objMgpService->createMangopayWallet(
                    $existMgpData['mgp_userid'],
                    Configuration::get('WK_MP_MANGOPAY_CURRENCY')
                );
                if ($mgpBuyerWalletId) {
                    $this->buyerMangopayUserId = $existMgpData['mgp_userid'];
                    $this->buyerMangopayWalletId = $mgpBuyerWalletId;
                    if (!$this->saveMangopayBuyerInfo($idCustomer)) {
                        $this->paymentErrors[] = $this->module->l(
                            'Some error occurred while Buyer wallet information.',
                            'bankwirevalidation'
                        );
                    }
                } else {
                    $this->paymentErrors[] = $this->module->l(
                        'Some error occurred while creating customer wallet. Please try again.',
                        'bankwirevalidation'
                    );
                }
            }
        } else {
            $this->buyerMangopayUserId = $mgpData['mgp_userid'];
            $this->buyerMangopayWalletId = $mgpData['mgp_walletid'];
        }
    }

    private function saveMangopayBuyerInfo($idCustomer)
    {
        if ($idCustomer) {
            //Saving data in our table
            $objCustomer = new Customer($idCustomer);
            $objMgpBuyer = new MangopayBuyer($idCustomer);
            $objMgpBuyer->id_customer = $idCustomer;
            $objMgpBuyer->mgp_clientid = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
            $objMgpBuyer->mgp_userid = $this->buyerMangopayUserId;
            $objMgpBuyer->mgp_walletid = $this->buyerMangopayWalletId;
            $objMgpBuyer->currency_iso = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            //Problem : guest after login
            $objMgpBuyer->user_type = ($this->context->cookie->id_guest ? 'guest' : 'buyer');
            $objMgpBuyer->user_email = $objCustomer->email;

            return $objMgpBuyer->save();
        }
        return false;
    }
}
