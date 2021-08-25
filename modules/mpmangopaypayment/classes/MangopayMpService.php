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

class MangopayMpService extends ObjectModel
{
    private $mangoPayApi;

    public function __construct()
    {
        $this->mangoPayApi = new MangoPay\MangoPayApi();
        $this->mangoPayApi->Config->ClientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        $this->mangoPayApi->Config->ClientPassword = Configuration::get('WK_MP_MANGOPAY_PASSPHRASE');
        $this->mangoPayApi->Config->TemporaryFolder = _PS_MODULE_DIR_.'mpmangopaypayment/temp/';
        if (Configuration::get('WK_MP_MANGOPAY_MODE') == 'sandbox') {
            $this->mangoPayApi->Config->BaseUrl = 'https://api.sandbox.mangopay.com';
        } else {
            $this->mangoPayApi->Config->BaseUrl = 'https://api.mangopay.com';
        }
    }

    /**
     * create the mangopay legal user
     * @param [type] $id_country
     * @param int $userId
     * @param string $userType
     * @return int
     */
    public function createMangopayUserLegal($id_country, $userId, $userType)
    {
        // if (!$id_employee) {
        //     $id_employee = 1;
        // } //1 for Superadmin by default
        try {
            $obj_legaluser = new MangoPay\UserLegal();
            if ($userType == 'Seller') {
                $objUser = new WkMpSeller($userId);
                $firstName = $objUser->seller_firstname;
                $lastName = $objUser->seller_lastname;
                $email = $objUser->business_email;
            } elseif ($userType == 'Customer') {
                $objUser = new Customer($userId);
                $firstName = $objUser->firstname;
                $lastName = $objUser->lastname;
                $email = $objUser->email;
            } else {
                $objUser = new Employee($userId);
                $firstName = $objUser->firstname;
                $lastName = $objUser->lastname;
                $email = $objUser->email;
            }
            $name = $firstName.' '.$lastName;
            $obj_legaluser->Name = $name;
            $obj_legaluser->PersonType = 'LEGAL';
            $obj_legaluser->LegalPersonType = 'BUSINESS';
            $obj_legaluser->LegalRepresentativeFirstName = $firstName;
            $obj_legaluser->LegalRepresentativeLastName = $lastName;
            $obj_legaluser->LegalRepresentativeEmail = $email;
            $obj_legaluser->LegalRepresentativeNationality = Country::getIsoById($id_country);
            $obj_legaluser->LegalRepresentativeCountryOfResidence = Country::getIsoById($id_country);
            $obj_legaluser->LegalRepresentativeBirthday = 1404111618;  //Date:2014-06-30 Prestashop does not provide employee birthday
            if ($userType == 'Admin') {
                $obj_legaluser->Tag = 'Admin';
            } elseif ($userType == 'Seller') {
                $obj_legaluser->Tag = 'Seller';
            } else {
                $obj_legaluser->Tag = 'Customer';
            }
            $obj_legaluser->Email = $email;
            $result_user = $this->mangoPayApi->Users->Create($obj_legaluser);
        } catch (\MangoPay\ResponseException $e) {
            error_log(date('[Y-m-d H:i e] ').'Mangopay Payment error:  Error while creating mangopay user legal and Data: Id Country: '.$id_country.PHP_EOL.'Error Message : '.$e->getMessage().PHP_EOL.PHP_EOL, 3, _PS_MODULE_DIR_.'mpmangopaypayment/error.log');
        }
        if ($mgp_legel_userid = $result_user->Id) {
            return $mgp_legel_userid;
        }

        return false;
    }

    /**
     * Create the mangopay natural user.
     *
     * @param int $id_customer
     * @param int $id_country
     * @return int
     */
    public function createMangopayUserNatural($id_customer, $id_country)
    {
        try {
            $obj_customer = new Customer($id_customer);
            $obj_naturaluser = new MangoPay\UserNatural();
            $obj_naturaluser->PersonType = 'NATURAL';
            $obj_naturaluser->Email = $obj_customer->email;
            $obj_naturaluser->FirstName = $obj_customer->firstname;
            $obj_naturaluser->LastName = $obj_customer->lastname;
            $obj_naturaluser->Birthday = ($obj_customer->birthday == '0000-00-00' ? 1404111618 : strtotime($obj_customer->birthday));
            $obj_naturaluser->Nationality = Country::getIsoById($id_country);
            $obj_naturaluser->CountryOfResidence = Country::getIsoById($id_country);
            $obj_naturaluser->Tag = 'Buyer';
            $userid_result = $this->mangoPayApi->Users->Create($obj_naturaluser);
        } catch (\MangoPay\ResponseException $e) {
            error_log(date('[Y-m-d H:i e] ').'Mangopay Payment error:  Error while creating mangopay user natural and Data: Id Country: '.$id_country.' Id Customer:'.$id_customer.PHP_EOL.'Error Message : '.$e->getMessage().PHP_EOL.PHP_EOL, 3, _PS_MODULE_DIR_.'mpmangopaypayment/error.log');
        }
        if ($mgp_natural_userid = $userid_result->Id) {
            return $mgp_natural_userid;
        }

        return false;
    }

    /**
     * create the mangopay wallet.
     *
     * @param int $mgp_userid
     * @param boolean $id_currency
     * @return itn
     */
    public function createMangopayWallet($mgp_userid, $id_currency = false)
    {
        if (!$id_currency) {
            $id_currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
        }
        try {
            $obj_wallet = new MangoPay\Wallet();
            $obj_wallet->Owners = array($mgp_userid);
            $obj_wallet->Description = 'Wallet';
            $obj_wallet->Currency = $id_currency;
            $wallet_result = $this->mangoPayApi->Wallets->Create($obj_wallet);
        } catch (\MangoPay\ResponseException $e) {
            error_log(date('[Y-m-d H:i e] ').'Mangopay Payment error:  Error while creating mangopay wallet and Data: mgp userId: '.$mgp_userid.' Id Currency:'.$id_currency.PHP_EOL.'Error Message : '.$e->getMessage().PHP_EOL.PHP_EOL, 3, _PS_MODULE_DIR_.'mpmangopaypayment/error.log');
        }
        if ($mgp_walletid = $wallet_result->Id) {
            return $mgp_walletid;
        }

        return false;
    }

    /**
     * create the mangopay seller payout.
     *
     * @param int $mangopay_user_id
     * @param int $mangopay_wallet_id
     * @param int $mangopay_acc_id
     * @param int $amt
     * @param integer $fee
     * @return array
     */
    public function createSellerpayOut($mangopay_user_id, $mangopay_wallet_id, $mangopay_acc_id, $amt, $fee = 0)
    {
        $PayOut = new \MangoPay\PayOut();
        $PayOut->AuthorId = $mangopay_user_id;
        $PayOut->DebitedWalletID = $mangopay_wallet_id;
        $PayOut->DebitedFunds = new \MangoPay\Money();
        $PayOut->DebitedFunds->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
        $PayOut->DebitedFunds->Amount = $amt * 100;
        $PayOut->Fees = new \MangoPay\Money();
        $PayOut->Fees->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
        $PayOut->Fees->Amount = $fee * 100;
        $PayOut->PaymentType = 'BANK_WIRE';
        $PayOut->MeanOfPaymentDetails = new \MangoPay\PayOutPaymentDetailsBankWire();
        $PayOut->MeanOfPaymentDetails->BankAccountId = $mangopay_acc_id;
        $PayOut->Tag = 'Mangopay Seller Wallet Payout.';

        try {
            $result = $this->mangoPayApi->PayOuts->Create($PayOut);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    /**
     * Register the seller mangopay bank account.
     *
     * @param int $user_id
     * @param int $bank_details_params
     * @return array
     */
    public function registerMangopayBankAccount($user_id, $bank_details_params)
    {
        $BankAccount = new \MangoPay\BankAccount();
        $BankAccount->Type = $bank_details_params['mgp_bank_type'];
        if ($bank_details_params['mgp_bank_type'] == 'IBAN') {
            $BankAccount->Details = new MangoPay\BankAccountDetailsIBAN();
            $BankAccount->Tag = 'Seller Mangopay bank Account';
            $BankAccount->Details->IBAN = $bank_details_params['mgp_iban'];
            $BankAccount->Details->BIC = $bank_details_params['mgp_bic'];
            $BankAccount->OwnerName = $bank_details_params['mgp_owner_name'];
        } elseif ($bank_details_params['mgp_bank_type'] == 'GB') {
            $BankAccount->Details = new MangoPay\BankAccountDetailsGB();
            $BankAccount->Tag = 'Seller Mangopay bank Account';
            $BankAccount->Details->AccountNumber = $bank_details_params['mgp_account_number'];
            $BankAccount->Details->SortCode = $bank_details_params['mgp_sort_code'];
            $BankAccount->OwnerName = $bank_details_params['mgp_owner_name'];
        } elseif ($bank_details_params['mgp_bank_type'] == 'US') {
            $BankAccount->Details = new MangoPay\BankAccountDetailsUS();
            $BankAccount->Tag = 'Seller Mangopay bank Account';
            $BankAccount->Details->AccountNumber = $bank_details_params['mgp_account_number'];
            $BankAccount->Details->ABA = $bank_details_params['mgp_aba'];
            $BankAccount->OwnerName = $bank_details_params['mgp_owner_name'];
        } elseif ($bank_details_params['mgp_bank_type'] == 'CA') {
            $BankAccount->Details = new MangoPay\BankAccountDetailsCA();
            $BankAccount->Tag = 'Seller Mangopay bank Account';
            $BankAccount->Details->AccountNumber = $bank_details_params['mgp_account_number'];
            $BankAccount->Details->InstitutionNumber = $bank_details_params['mgp_institution_number'];
            $BankAccount->Details->BranchCode = $bank_details_params['mgp_branch_code'];
            $BankAccount->Details->BankName = $bank_details_params['mgp_bank_name'];
            $BankAccount->OwnerName = $bank_details_params['mgp_owner_name'];
        } elseif ($bank_details_params['mgp_bank_type'] == 'OTHER') {
            $BankAccount->Details = new MangoPay\BankAccountDetailsOTHER();
            $BankAccount->Tag = 'Seller Mangopay bank Account';
            $BankAccount->Details->AccountNumber = $bank_details_params['mgp_account_number'];
            $BankAccount->Details->Country = $bank_details_params['mgp_owner_country'];
            $BankAccount->Details->BIC = $bank_details_params['mgp_bic'];
            $BankAccount->OwnerName = $bank_details_params['mgp_owner_name'];
        }
        $BankAccount->OwnerAddress = new \MangoPay\Address();
        $BankAccount->OwnerAddress->AddressLine1 = $bank_details_params['mgp_owner_addressline1'];
        $BankAccount->OwnerAddress->AddressLine2 = $bank_details_params['mgp_owner_addressline2'];
        $BankAccount->OwnerAddress->City = $bank_details_params['mgp_owner_city'];
        $BankAccount->OwnerAddress->Country = $bank_details_params['mgp_owner_country'];
        $BankAccount->OwnerAddress->PostalCode = $bank_details_params['mgp_owner_postcode'];
        $BankAccount->OwnerAddress->Region = $bank_details_params['mgp_owner_region'];
        try {
            $result = $this->mangoPayApi->Users->CreateBankAccount($user_id, $BankAccount);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    /**
     * Deactivate the mangopay  bank account.
     *
     * @param int $user_id
     * @param int $bank_account_id
     * @return array
     */
    public function deactivateBankAccount($user_id, $bank_account_id)
    {
        try {
            $bankAccount = $this->mangoPayApi->Users->GetBankAccount($user_id, $bank_account_id);
            if (isset($bankAccount->Id) && $bankAccount->Id) {
                $bankAccount->Active = false;
                try {
                    $result = $this->mangoPayApi->Users->UpdateBankAccount($user_id, $bankAccount);
                } catch (Exception $e) {
                    $result['Status'] = 'FAILED';
                    $result['ResultMessage'] = $e->getMessage();
                }
            }
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }


        return (array) $result;
    }

    /**
     * Get the mangopay account of the seller.
     * @param int $user_id
     * @return array
     */
    public function getMangopayBankAccounts($user_id, $active = 2)
    {
        //Build the parameters for the request
        try {
            $Pagination = new \MangoPay\Pagination();
            $Pagination->ItemsPerPage = 50;
            if ($active == 1 || $active == 0) {
                $Pagination->Active = true;
            } elseif ($active == 0) {
                $Pagination->Active = false;
            }
            //Send the request
            $result = $this->mangoPayApi->Users->GetBankAccounts($user_id, $Pagination);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    /**
     * Get the mangopay wallet details.
     *
     * @param int $WalletID
     * @return array
     */
    public function getWalletDetails($WalletID)
    {
        try {
            //Send the request
            $result = $this->mangoPayApi->Wallets->Get($WalletID);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    /**
     * Get the mangopay transfer details
     *
     * @param int $id_transfer
     * @return array
     */
    public function getMangopayTransferDetails($id_transfer)
    {
        try {
            //Send the request
            $result = $this->mangoPayApi->Transfers->Get($id_transfer);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    /**
     * Get the mangopay payin detail.
     *
     * @param int $id_payin
     * @return array
     */
    public function getMangopayPayInDetails($id_payin)
    {
        try {
            //Send the request
            $result = $this->mangoPayApi->PayIns->Get($id_payin);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    /**
     * Create mangopay payin refund.
     *
     * @param int $params
     * @return array
     */
    public function createMangopayPayInRefund($params)
    {
        //Build the parameters for the request
        $Refund = new \MangoPay\Refund();
        $Refund->AuthorId = $params['author_id'];
        $Refund->DebitedFunds = new \MangoPay\Money();
        $Refund->DebitedFunds->Currency = $params['currency'];
        $Refund->DebitedFunds->Amount = $params['amount'] * 100;
        $Refund->Fees = new \MangoPay\Money();
        $Refund->Fees->Currency = $params['currency'];
        $Refund->Fees->Amount = 0;
        try {
            //Send the request
            $result = $this->mangoPayApi->PayIns->CreateRefund($params['payin_id'], $Refund);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    /**
     * Create the mangopay transfer refund.
     *
     * @param int $params
     * @return array
     */
    public function createMangopayTransferRefund($params)
    {
        try {
            //Build the parameters for the request
            $Refund = new \MangoPay\Refund();
            $Refund->AuthorId = $params['author_id'];
            //Send the request
            $result = $this->mangoPayApi->Transfers->CreateRefund($params['transfer_id'], $Refund);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }


    /**
     * Get the transactions by wallet id.
     *
     * @param int $id_wallet
     * @return array
     */
    public function getTranctionsByWalletId($id_wallet)
    {
        //Build the parameters for the request
        $Pagination = new \MangoPay\Pagination();
        $Filter = new \MangoPay\FilterBase();
        $Filter->Type = 'TRANSFER';
        $Filter->Nature = 'REGULAR';
        $Filter->Direction = 'DEBIT';
        try {
            //Send the request
            $result = $this->mangoPayApi->Wallets->GetTransactions($id_wallet, $Pagination, $Filter);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    /**
     * Get Mangopay transfer refund detail.
     *
     * @param int $id_transfer
     * @return array
     */
    public function getMangopayTransferRefundDetails($id_transfer)
    {
        try {
            //Send the request
            $result = $this->mangoPayApi->Transfers->GetRefund($id_transfer);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    public function deactivateUserCard($idCard)
    {
        try {
            //Send the request
            $objCard = new MangoPay\Card();
            $objCard->Id = $idCard;
            $objCard->Active = 0;
            $objCard->Validity = \MangoPay\CardValidity::Invalid;
            $result = $this->mangoPayApi->Cards->Update($objCard);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }
        return (array) $result;
    }

    //Get the customer mangopay register card details.
    public function getCustomerMangopayRegisteredCardsDetails($user_id, $active = 2)
    {
        try {
            $Pagination = new \MangoPay\Pagination();
            //Send the request
            $Pagination->ItemsPerPage = 50;
            if ($active == 1 || $active == 0) {
                $Pagination->Active = true;
            } elseif ($active == 0) {
                $Pagination->Active = false;
            }
            $result = $this->mangoPayApi->Users->GetCards($user_id, $Pagination);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }

        return (array) $result;
    }

    // Create Direct Debit Direct payment
    public function createDirectDebitDirectpayment($userId, $walletId, $mandateId, $amount, $fees)
    {
        try {
            $PayIn = new \MangoPay\PayIn();
            $PayIn->Tag = 'Direct Debit Direct By Mandate';
            $PayIn->AuthorId = $userId;
            $PayIn->CreditedUserId = $userId;
            $PayIn->CreditedWalletId = $walletId;
            $PayIn->PaymentType = "DirectDebit";
            $PayIn->PaymentDetails = new \MangoPay\PayInPaymentDetailsDirectDebit();
            $PayIn->DebitedFunds = new \MangoPay\Money();
            $PayIn->DebitedFunds->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $PayIn->DebitedFunds->Amount = $amount * 100;
            $PayIn->Fees = new \MangoPay\Money();
            $PayIn->Fees->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $PayIn->Fees->Amount = $fees * 100;
            $PayIn->MandateId = $mandateId;
            $PayIn->ExecutionType = 'DIRECT';
            $PayIn->ExecutionDetails = new \MangoPay\PayInExecutionDetailsDirect();
            $PayIn->ExecutionDetails->SecureMode = 'DEFAULT';
            $PayIn->ExecutionDetails->SecureModeReturnURL = Context::getContext()->link->getModuleLink(
                'mpmangopaypayment',
                'success'
            );
            $result = $this->mangoPayApi->PayIns->Create($PayIn);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }
        return (array) $result;
    }

    // Create Mandate Debit Direct payment
    public function createMandate($bankAccountId)
    {
        $context = Context::getContext();
        try {
            $Mandate = new \MangoPay\Mandate();
            $Mandate->Tag = "Mangopay Mandate";
            $Mandate->BankAccountId = $bankAccountId;
            $Mandate->Culture = $context->language->iso_code;

            $Mandate->ReturnURL = $context->link->getModuleLink(
                'mpmangopaypayment',
                'success',
                array('id_cart' => $context->cart->id)
            );
            $result = $this->mangoPayApi->Mandates->Create($Mandate);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }
        return (array) $result;
    }

    // get Mandate details
    public function getMandate($mandateId)
    {
        try {
            $result = $this->mangoPayApi->Mandates->get($mandateId);
        } catch (Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }
        return (array) $result;
    }

    public function registerMangopayEventHook($eventType, $returnUrl, $idHook = 0)
    {
        try {
            if ($idHook) {
                $Hook = new \MangoPay\Hook();
                $Hook->Url = $returnUrl;
                $Hook->Id = $idHook;
                $result = $this->mangoPayApi->Hooks->update($Hook);
            } else {
                $Hook = new \MangoPay\Hook();
                $Hook->Tag = "Event Hook";
                $Hook->EventType = $eventType;
                $Hook->Url = $returnUrl;
                $result = $this->mangoPayApi->Hooks->Create($Hook);
            }
        } catch (MangoPay\Libraries\Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }
        return (array) $result;
    }

    public function viewAllHooks($active = 2)
    {
        try {
            $Pagination = new \MangoPay\Pagination();
            //Send the request
            $Pagination->ItemsPerPage = 50;
            if ($active == 1 || $active == 0) {
                $Pagination->Active = true;
            } elseif ($active == 0) {
                $Pagination->Active = false;
            }
            $result = $this->mangoPayApi->Hooks->GetAll($Pagination);
        } catch (MangoPay\Libraries\Exception $e) {
            $result['Status'] = 'FAILED';
            $result['ResultMessage'] = $e->getMessage();
        }
        return (array) $result;
    }
}
