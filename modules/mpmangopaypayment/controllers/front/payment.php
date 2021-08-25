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

class MpMangopayPaymentPaymentModuleFrontController extends ModuleFrontController
{
    private $objApi;
    private $adminMangopayWalletId;
    private $buyerMangopayUserId;
    private $buyerMangopayWalletId;
    private $buyerCardId;

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
                $idCustomer = $this->context->cart->id_customer;
                $cart = $this->context->cart;
                $addrDetails = Address::getCountryAndState($cart->id_address_invoice);
                $customerCountryIso = Country::getIsoById($addrDetails['id_country']);
                //Validate Cart
                if (!Validate::isLoadedObject($cart) && Order::getOrderByCartId((int) Tools::getValue('cart'))) {
                    $this->paymentErrors[] = $this->module->l('Invalid Cart ID', 'payment');
                } else {
                    //Creating buyer mangopay natural userid and wallet
                    $this->createByuerMangopayWallet($idCustomer, $customerCountryIso);
                    if ($this->buyerMangopayWalletId) {
                        $objMgpCurrency = new Currency(
                            (int) Currency::getIdByIsoCode(Configuration::get('WK_MP_MANGOPAY_CURRENCY'))
                        );
                        if (!Validate::isLoadedObject($objMgpCurrency)) {
                            $this->paymentErrors[] = $this->module->l('Invalid Currency ID', 'payment').' '.
                            ($objMgpCurrency->id.'|'.$cart->id_currency);
                        } else {
                            $payinType = Configuration::get('WK_MP_MANGOPAY_PAYIN_TYPE');
                            if ($payinType == 2) {
                                $this->card_type = Tools::getValue('card_type');
                                $cardValueValidate = true;
                            } else {
                                if (Tools::getValue('pay_with_new_card') == 0) {
                                    $this->card_type = Tools::getValue('saved_customer_card_type');
                                    $this->buyerCardId = MangopayConfig::decryptString(
                                        Tools::getValue('saved_customer_card')
                                    );
                                    $cardValueValidate = 1;
                                } else {
                                    $this->card_type = Tools::getValue('card_type');
                                    $cardValidateArr = $this->userCardValidationAndRegistration();
                                    $cardValueValidate = $cardValidateArr['validate'];
                                }
                            }
                            if ($cardValueValidate) {
                                if ($totalAmount = $cart->getOrderTotal(true)) {
                                    if ($objMgpCurrency->id != $cart->id_currency) {
                                        // amount conversion to the amount for wich currency mangopay account is configured
                                        $totalAmount = Tools::convertPriceFull(
                                            $totalAmount,
                                            new Currency($cart->id_currency),
                                            new Currency($objMgpCurrency->id)
                                        );
                                    }
                                    $fees = 0;
                                    //Convert into cent,DebitedFunds – Fees = CreditedFunds (amount received on wallet)
                                    $payment = $this->mangopayPayIn($totalAmount, $fees, $payinType);
                                    if (isset($payment)) {
                                        //When payment more than 100Euro
                                        if ($payinType == 2) {
                                            $secureUrl = $payment->ExecutionDetails->RedirectURL;
                                        } else {
                                            $secureUrl = $payment->ExecutionDetails->SecureModeRedirectURL;
                                        }
                                        if ($secureUrl != '') { //When payment more than 100Euro
                                            Tools::redirect($secureUrl);
                                        } else {
                                            if ($payment->Status == 'SUCCEEDED') {
                                                $objMgpTransaction = new MangopayTransaction();
                                                $objMgpTransaction->mangopayOrderCreation(
                                                    $this,
                                                    $payment,
                                                    Configuration::get('PS_OS_PAYMENT'),
                                                    $cart
                                                );
                                            } else {
                                                $this->paymentErrors[] = $this->module->l(
                                                    'PayIn has been created with status : ',
                                                    'payment'
                                                ).$payment->Status.' '.$this->module->l('Result Code', 'payment').
                                                ' : '.$payment->ResultCode.', '.$payment->ResultMessage;
                                            }
                                        }
                                    }
                                } else {
                                    $this->paymentErrors[] = $this->module->l(
                                        'Amount can not be zero for the payment. Please try again.
                                        Sorry for inconvenience.',
                                        'payment'
                                    );
                                }
                            } else {
                                $this->paymentErrors[] = $this->module->l(
                                    'Cannot create card. Payment has not been created.',
                                    'payment'
                                );
                            }
                        }
                    } else {
                        $this->paymentErrors[] = $this->module->l(
                            'Some error occurred while creating buyer\'s wallet',
                            'payment'
                        );
                    }
                }
            } catch (Exception $e) {
                $this->paymentErrors[] = $e->getMessage();
            }
        } else {
            $this->paymentErrors[] = $this->module->l(
                'Currently Mangopay Payment is not available. Please try again later.',
                'payment'
            );
        }
        if (count($this->paymentErrors)) {
            $this->context->smarty->assign('mangopay_error_messages', $this->paymentErrors);
            $this->setTemplate('module:mpmangopaypayment/views/templates/front/error_messages.tpl');
        }
    }

    // Validate the card details on the payment.
    private function userCardValidationAndRegistration()
    {
        $cardValueValidate = false;
        $cardNumber = Tools::getValue('x_card_num');
        $cardExpMonth = Tools::getValue('ExpMonth');
        $cardExpYear = Tools::getValue('ExpYear');
        $cardExpYear = Tools::substr($cardExpYear, -2);
        $CardCvv = Tools::getValue('mangopay_card_code');
        $cardExpDate = $cardExpMonth.$cardExpYear;
        //Registering this cart to mangopay
        if ($cardNumber && $this->card_type && $CardCvv && $cardExpDate) {
            if (Tools::getValue('pay_with_new_card') == 1
            && (Configuration::get('WK_MP_MANGOPAY_SAVE_CARD_ENABLE') && Tools::getValue('save_trans_card'))
            ) {
                $objMangopayService = new MangopayMpService();
                if ($userCards = $objMangopayService->getCustomerMangopayRegisteredCardsDetails(
                    $this->buyerMangopayUserId
                )) {
                    $cardSubstrLength = ($this->card_type == "DINERS") ? 4 : 6;
                    $cardFirstNumber = Tools::substr($cardNumber, 0, $cardSubstrLength);
                    $cardLastNumber = Tools::substr($cardNumber, (Tools::strlen($cardNumber)-4), 4);
                    foreach ($userCards as $cardObj) {
                        $savedCardFirstNumber = Tools::substr($cardObj->Alias, 0, $cardSubstrLength);
                        $savedCardLastNumber = Tools::substr($cardObj->Alias, (Tools::strlen($cardObj->Alias)-4), 4);

                        if ($cardObj->Active
                        && $cardFirstNumber == $savedCardFirstNumber
                        && $cardLastNumber == $savedCardLastNumber
                        && $this->card_type == $cardObj->CardType
                        && $cardExpDate == $cardObj->ExpirationDate) {
                            $this->buyerCardId = $cardObj->Id;
                            $cardValueValidate = true;
                            break;
                        }
                    }
                }
            }

            if (!$cardValueValidate) {
                $result = $this->mangopayCardRegistration();
                if ($result) {
                    $url = $result->CardRegistrationURL;
                    $fields = array(
                        'accessKeyRef' => $result->AccessKey,
                        'cardNumber' => $cardNumber,
                        'cardExpirationDate' => $cardExpDate,
                        'cardCvx' => $CardCvv,
                        'data' => $result->PreregistrationData,
                    );
                    //post data to this url using curl
                    $cardRegResponse = $this->getCardRegistrationData($url, $fields);
                    //Validate input card information
                    if ($cardRegResponse == 'errorCode=02625') {
                        $this->paymentErrors[] = $this->module->l('Invalid card number.', 'payment');
                    } elseif ($cardRegResponse == 'errorCode=02626') {
                        $this->paymentErrors[] = $this->module->l('Invalid date.', 'payment');
                    } elseif ($cardRegResponse == 'errorCode=02627') {
                        $this->paymentErrors[] = $this->module->l('Invalid CVV number.', 'payment');
                    } elseif ($cardRegResponse == 'errorCode=02628') {
                        $this->paymentErrors[] = $this->module->l('Transaction refused.', 'payment');
                    }
                    if (!count($this->paymentErrors)) {
                        //Validating mangopay card
                        $validateCard = $this->mangopayValidateCard($cardRegResponse, $result->Id);
                        if ($validateCard && $validateCard->Status == 'VALIDATED') {
                            $cardValueValidate = true;
                        } else {
                            $cardValueValidate = false;
                        }
                    } else {
                        $cardValueValidate = false;
                    }
                } else {
                    $cardValueValidate = false;
                }
            }
        } else {
            $cardValueValidate = false;
        }
        return array('validate' => $cardValueValidate);
    }

    // Create the mangopay payin.
    private function mangopayPayIn($totalAmount, $fees, $payinType)
    {
        try {
            $objMgpPayin = new MangoPay\PayIn();
            $totalAmount = $totalAmount * 100; //To convert in cent for mangopay;
            $objMgpPayin->CreditedWalletId = $this->buyerMangopayWalletId;
            $objMgpPayin->CreditedUserId = $this->buyerMangopayUserId;
            $objMgpPayin->AuthorId = $this->buyerMangopayUserId;
            $objMgpPayin->DebitedFunds = new MangoPay\Money();
            $objMgpPayin->PaymentType = 'CARD';
            $objMgpPayin->DebitedFunds->Amount = $totalAmount;
            $objMgpPayin->DebitedFunds->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $objMgpPayin->Fees = new MangoPay\Money();
            $objMgpPayin->Fees->Amount = $fees * 100;
            $objMgpPayin->Fees->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $this->setPayInObjectParameters($objMgpPayin, $payinType);
            $createdPayin = $this->objApi->PayIns->Create($objMgpPayin);  // create Pay-In
            // if save card feature is not active then deactive this card after pay IN
            if (Tools::getValue('pay_with_new_card') == 1
                && (!Configuration::get('WK_MP_MANGOPAY_SAVE_CARD_ENABLE') || !Tools::getValue('save_trans_card'))
            ) {
                if ($payinType == 1) {
                    $objMgpService = new MangopayMpService();
                    $objMgpService->deactivateUserCard($this->buyerCardId);
                }
            }
            return $createdPayin;
        } catch (\MangoPay\ResponseException $e) {
            $this->paymentErrors[] = $e->getMessage();
            error_log(
                date('[Y-m-d H:i e] : ').'Error while Creating mangopay PayIn. Error Message :'.
                json_encode($e->getMessage()).PHP_EOL,
                3,
                _PS_MODULE_DIR_.'mpmangopaypayment/error.log'
            );
        }
    }

    // Set the payin object parameters.
    private function setPayInObjectParameters(&$objMgpPayin, $payinType)
    {
        try {
            $objMgpPayin->PaymentDetails = new MangoPay\PayInPaymentDetailsCard();
            $objMgpPayin->PaymentDetails->CardType = $this->card_type;
            if ($payinType == 2) {   // execution type as Web
                $objMgpPayin->ExecutionDetails = new MangoPay\PayInExecutionDetailsWeb();
                $objMgpPayin->ExecutionType = 'WEB';
                $objMgpPayin->ExecutionDetails->SecureMode = 'DEFAULT';
                $objMgpPayin->ExecutionDetails->TemplateURLOptions = array(
                    'PAYLINE' => 'https://www.secure-site.com/pay-template.php'
                );
                $objMgpPayin->ExecutionDetails->ReturnURL = $this->context->link->getModuleLink(
                    'mpmangopaypayment',
                    'success',
                    array('id_cart' => $this->context->cart->id)
                );
                $objMgpPayin->ExecutionDetails->Culture = $this->context->language->iso_code;
            } else { // execution type as Direct
                $objMgpPayin->ExecutionDetails = new MangoPay\PayInExecutionDetailsDirect();
                $objMgpPayin->ExecutionType = 'DIRECT';
                $objMgpPayin->ExecutionDetails->SecureMode = 'DEFAULT';
                $objMgpPayin->PaymentDetails->CardId = $this->buyerCardId;
                $saveCard = 1;
                if (Tools::getValue('pay_with_new_card') == 1
                    && (!Configuration::get('WK_MP_MANGOPAY_SAVE_CARD_ENABLE') || !Tools::getValue('save_trans_card'))
                ) {
                    $saveCard = 0;
                }
                $objMgpPayin->ExecutionDetails->SecureModeReturnURL = $this->context->link->getModuleLink(
                    'mpmangopaypayment',
                    'success',
                    array('sv_cd' => $saveCard, 'id_cart' => $this->context->cart->id)
                );
            }
        } catch (\MangoPay\ResponseException $e) {
            $this->paymentErrors[] = $e->getMessage();
        }
    }

    // Create the wallet of the mangopay for the buyer.
    private function createByuerMangopayWallet($idCustomer, $customerCountryIso)
    {
        try {
            $objMgpBuyer = new MangopayBuyer();
            //If not exist
            if (!$mgpData = $objMgpBuyer->getBuyerMangopayData(
                $idCustomer,
                Configuration::get('WK_MP_MANGOPAY_CURRENCY')
            )) {
                if (!$existMgpData = $objMgpBuyer->getExistingBuyerMangopayData($idCustomer)) {
                    //Create mangopay userid
                    $objCustomer = new Customer($idCustomer);
                    $objNaturalUser = new MangoPay\UserNatural();
                    $objNaturalUser->PersonType = 'NATURAL';
                    $objNaturalUser->Email = $objCustomer->email;
                    $objNaturalUser->FirstName = $objCustomer->firstname;
                    $objNaturalUser->LastName = $objCustomer->lastname;
                    $objNaturalUser->Birthday = (
                        $objCustomer->birthday == '0000-00-00' ? 1404111618 : strtotime($objCustomer->birthday)
                    );
                    $objNaturalUser->Nationality = $customerCountryIso;
                    $objNaturalUser->CountryOfResidence = $customerCountryIso;
                    $objNaturalUser->Tag = $this->module->l('Buyer', 'payment');
                    $userResult = $this->objApi->Users->Create($objNaturalUser);

                    if ($userResult->Id) {
                        $this->buyerMangopayUserId = $userResult->Id;
                        $this->buyerMangopayUserId = $userResult->Id;
                        //Create buyer walletid
                        $objMgpWallet = new MangoPay\Wallet();
                        $objMgpWallet->Owners = array($this->buyerMangopayUserId);
                        $objMgpWallet->Description = $this->module->l('Buyer\'s wallet', 'payment');
                        $objMgpWallet->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
                        $walletResult = $this->objApi->Wallets->Create($objMgpWallet);

                        if ($walletResult->Id) {
                            $this->buyerMangopayWalletId = $walletResult->Id;
                            //save Mangopay Buyer Info
                            if (!$this->saveMangopayBuyerInfo($idCustomer)) {
                                $this->paymentErrors[] = $this->module->l(
                                    'Some error occurred while Buyer wallet information.',
                                    'payment'
                                );
                            }
                        } else {
                            $this->paymentErrors[] = $this->module->l(
                                'Some error occurred while creating mangopay wallet of customer. Please try again.',
                                'payment'
                            );
                        }
                    } else {
                        $this->paymentErrors[] = $this->module->l(
                            'Some error occurred while creating customer as mangopay user. Please try again.',
                            'payment'
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
                                'payment'
                            );
                        }
                    } else {
                        $this->paymentErrors[] = $this->module->l(
                            'Some error occurred while creating customer wallet. Please try again.',
                            'payment'
                        );
                    }
                }
            } else {
                $this->buyerMangopayUserId = $mgpData['mgp_userid'];
                $this->buyerMangopayWalletId = $mgpData['mgp_walletid'];
            }
        } catch (Exception $e) {
            $this->paymentErrors[] = $e->getMessage();
        }
    }

    // Save the details of the buyer.
    private function saveMangopayBuyerInfo($idCustomer)
    {
        //Saving data in our table
        $objCustomer = new Customer($idCustomer);
        $objMgpBuyer = new MangopayBuyer($idCustomer);
        $objMgpBuyer->id_customer = $idCustomer;
        $objMgpBuyer->mgp_clientid = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        $objMgpBuyer->mgp_userid = $this->buyerMangopayUserId;
        $objMgpBuyer->mgp_walletid = $this->buyerMangopayWalletId;
        $objMgpBuyer->currency_iso = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
        //Problem going guest after login
        $objMgpBuyer->user_type = ($this->context->cookie->id_guest ? 'guest' : 'buyer');
        $objMgpBuyer->user_email = $objCustomer->email;
        return $objMgpBuyer->save();
    }

    // Register the card for the transaction.
    private function mangopayCardRegistration()
    {
        try {
            $objCardRegistration = new MangoPay\CardRegistration();
            $objCardRegistration->UserId = $this->buyerMangopayUserId;  //required
            $objCardRegistration->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY'); //required
            $objCardRegistration->CardType = $this->card_type;
            return $this->objApi->CardRegistrations->Create($objCardRegistration);
        } catch (Exception $e) {
            $this->paymentErrors[] = $e->getMessage();
        }
    }

    // Get the registered card data.
    private function getCardRegistrationData($url, $fields)
    {
        $fieldsString = '';
        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');
        $curl = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, count($fields));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fieldsString);
        //execute post
        $response = curl_exec($curl);
        //close connection
        curl_close($curl);

        return $response;
    }

    // Validate the card details while trnasaction on mangopay.
    private function mangopayValidateCard($cardRegResponse, $regId)
    {
        try {
            $cardRegistration = $this->objApi->CardRegistrations->Get($regId);
            $cardRegistration->RegistrationData = $cardRegResponse;
            $updatedCardRegister = $this->objApi->CardRegistrations->Update($cardRegistration);
            if (!isset($updatedCardRegister->CardId)) {
                return false;
            } else {
                $cardDetails = $this->objApi->Cards->Get($updatedCardRegister->CardId);
                $this->buyerCardId = $cardDetails->Id;

                // send mail to the customer for his card registration as manopay says below line -
                //It is imperative to inform your users if you are registering their cards
                if (Validate::isLoadedObject($this->context->customer)) {
                    $mailParams = array();
                    $mailParams['{customer_name}'] = $this->context->customer->firstname.
                    ' '.$this->context->customer->lastname;
                    $mailParams['{card_alias}'] = $cardDetails->Alias;
                    $mailParams['{card_provider}'] = $cardDetails->CardProvider;

                    // Mail::Send(
                    //     $this->context->language->id,
                    //     'user_card_validation', //Specify the template file name
                    //     Mail::l('Validation du Paiement par Carte Bancaire', $this->context->language->id),
                    //     $mailParams,
                    //     $this->context->customer->email,
                    //     null,
                    //     null,
                    //     null,
                    //     null,
                    //     null,
                    //     _PS_MODULE_DIR_.'mpmangopaypayment/mails/',
                    //     false,
                    //     null,
                    //     null
                    // );
                }
                return $updatedCardRegister;
            }
        } catch (Exception $e) {
            $this->paymentErrors[] = $e->getMessage();
        }
    }

    // For direct debit payments create customers bank accounts on mangopay
    public function displayAjaxCreateCustomerBankDetails()
    {
        try {
            $result = array();
            $result['status'] = 'ko';
            $result['msg'] = $this->module->l('Some error occurred. Please try again', 'payment');
            parse_str(Tools::getValue('customer_bank_details_fields'), $customerBankDetails);
            if ($customerBankDetails['pay_with_new_account'] == 1) {
                $cart = $this->context->cart;
                $idCustomer = $this->context->cookie->id_customer;
                $addressDetails = Address::getCountryAndState($cart->id_address_invoice);
                $customerCountryIso = Country::getIsoById($addressDetails['id_country']);
                //validate customer filled bank details
                $this->paymentErrors = MangopayBuyer::validateMangopayBankDetailsFields($customerBankDetails);
                if (!count($this->paymentErrors)) {
                    // first create customer as mangopay user
                    $this->createByuerMangopayWallet($idCustomer, $customerCountryIso);
                    if (!count($this->paymentErrors)
                        && isset($this->buyerMangopayUserId)
                        && $this->buyerMangopayUserId
                    ) {
                        $objMangopayService = new MangopayMpService();
                        $isAccountAlreadySaved = false;
                        if ($userBankAccounts = $objMangopayService->getMangopayBankAccounts(
                            $this->buyerMangopayUserId,
                            1
                        )) {
                            if (isset($customerBankDetails['save_trans_account'])) {
                                foreach ($userBankAccounts as $userAccount) {
                                    if (($userAccount->Details->IBAN == $customerBankDetails['mgp_iban'])
                                    && ($userAccount->Details->BIC == $customerBankDetails['mgp_bic'])
                                    && ($userAccount->OwnerName == $customerBankDetails['mgp_owner_name'])
                                    && ($userAccount->Type == $customerBankDetails['mgp_bank_type'])
                                    && $userAccount->Active) {
                                        $bankAccountId = $customerBankDetails['saved_customer_account'];
                                        //decrypt card id
                                        $bankAccountId = $userAccount->Id;
                                        $isAccountAlreadySaved = true;
                                        break;
                                    }
                                }
                            }
                        }
                        //OwnerName(mgp_owner_name), Type(mgp_bank_type)
                        if (!$isAccountAlreadySaved) {
                            // register user bank details as mangopay bank account
                            $mgpRegisterBankAccResult = (array) $objMangopayService->registerMangopayBankAccount(
                                $this->buyerMangopayUserId,
                                $customerBankDetails
                            );
                            if (isset($mgpRegisterBankAccResult['Id']) && $mgpRegisterBankAccResult['Id']) {
                                $bankAccountId = $mgpRegisterBankAccResult['Id'];
                            } else {
                                $this->paymentErrors[] = $mgpRegisterBankAccResult['ResultMessage'];
                            }
                        }
                    } else {
                        $this->paymentErrors[] = $this->module->l(
                            'Some error has been occurred. Please try again.',
                            'payment'
                        );
                    }
                }
            } else {
                if ($bankAccountId = $customerBankDetails['saved_customer_account']) {
                    //decrypt card id
                    $bankAccountId = MangopayConfig::decryptString($bankAccountId);
                }
            }
            if (isset($bankAccountId) && $bankAccountId) {
                $objMangopayService = new MangopayMpService();
                // Create mandate with created mangopay bank account id.
                $mandateResult = $objMangopayService->createMandate($bankAccountId);
                if (isset($mandateResult['Id']) && $mandateResult['Id']) {
                    $result['status'] = 'ok';
                    $result['mandate_redirect'] = $mandateResult['RedirectURL'];
                    //deactivate bank account if not to save
                    if ($customerBankDetails['pay_with_new_account'] == 1
                        && (!Configuration::get('WK_MP_MANGOPAY_SAVE_BANK_ACCOUNT_ENABLE')
                        || !$customerBankDetails['save_trans_account'])
                    ) {
                        // user id check because deactivate only if bank account imediatly created
                        if (isset($this->buyerMangopayUserId) && $this->buyerMangopayUserId) {
                            $objMangopayService->deactivateBankAccount($this->buyerMangopayUserId, $bankAccountId);
                        }
                    }
                } else {
                    $this->paymentErrors[] = $mandateResult['ResultMessage'];
                }
            }
        } catch (Exception $e) {
            $this->paymentErrors[] = $e->getMessage();
        }
        if (count($this->paymentErrors)) {
            error_log(
                date('[Y-m-d H:i e] : ').'Error while creating account by customer. Error Message :'.
                json_encode($this->paymentErrors).PHP_EOL,
                3,
                _PS_MODULE_DIR_.'mpmangopaypayment/error.log'
            );
        }
        die(Tools::jsonEncode($result));
    }

    public function displayAjaxRemoveUserMangopayCard()
    {
        $result = array();
        $result['status'] = 'ko';
        $result['msg'] = $this->module->l('Some error occurred. Please try again', 'payment');
        if ($buyerCardId = Tools::getValue('id_user_card')) {
            try {
                //decrypt card id
                $buyerCardId = MangopayConfig::decryptString($buyerCardId);
                $objMgpService = new MangopayMpService();
                $resUpdate = $objMgpService->deactivateUserCard($buyerCardId);
                if (isset($resUpdate['Id']) && !$resUpdate['Active']) {
                    $result['status'] = 'ok';
                    $result['msg'] = $this->module->l('Card successfully removed.', 'payment');
                }
            } catch (Exception $e) {
                error_log(
                    date('[Y-m-d H:i e] : ').'Error while deactivating card by customer. Error Message :'.
                    json_encode($e->getMessage()).PHP_EOL,
                    3,
                    _PS_MODULE_DIR_.'mpmangopaypayment/error.log'
                );
            }
        }
        die(json_encode($result));
    }

    public function displayAjaxRemoveUserMangopayAccount()
    {
        $result = array();
        $result['status'] = 'ko';
        $result['msg'] = $this->module->l('Some error occurred. Please try again', 'payment');
        try {
            if ($buyerAccountId = Tools::getValue('id_user_account')) {
                $objMgpBuyer = new MangopayBuyer();
                if ($mgpBuyerData = $objMgpBuyer->getExistingBuyerMangopayData($this->context->customer->id)) {
                    //decrypt account id
                    $buyerAccountId = MangopayConfig::decryptString($buyerAccountId);
                    $objMgpService = new MangopayMpService();
                    $resUpdate = $objMgpService->deactivateBankAccount(
                        $mgpBuyerData['mgp_userid'],
                        $buyerAccountId
                    );
                    if (isset($resUpdate['Id']) && !$resUpdate['Active']) {
                        $result['status'] = 'ok';
                        $result['msg'] = $this->module->l('Account successfully removed.', 'payment');
                    }
                }
            }
        } catch (Exception $e) {
            error_log(
                date('[Y-m-d H:i e] : ').'Error while deactivating bank account by customer. Error Message :'.
                json_encode($e->getMessage()).PHP_EOL,
                3,
                _PS_MODULE_DIR_.'mpmangopaypayment/error.log'
            );
        }
        die(json_encode($result));
    }
}
