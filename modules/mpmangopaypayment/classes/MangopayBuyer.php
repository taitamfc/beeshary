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

class MangopayBuyer extends ObjectModel
{
    public $id;
    public $id_customer;
    public $mgp_clientid;
    public $mgp_userid;
    public $mgp_walletid;
    public $user_type;
    public $user_email;
    public $currency_iso;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_mangopay_buyer',
        'primary' => 'id',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'mgp_clientid' => array('type' => self::TYPE_STRING),
            'mgp_userid' => array('type' => self::TYPE_INT),
            'mgp_walletid' => array('type' => self::TYPE_INT),
            'user_type' => array('type' => self::TYPE_STRING),
            'currency_iso' => array('type' => self::TYPE_STRING),
            'user_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
            'user_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * get the data mangopay data of the buyer
     * @param int $id_customer
     * @param int $currency_iso
     * @return array
     */
    public function getBuyerMangopayData($id_customer, $currency_iso)
    {
        $client_id = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_buyer`
            WHERE `id_customer`='.(int)$id_customer.'
            AND `mgp_clientid` = \''.pSQL($client_id).'\'
            AND `currency_iso` = \''.pSQL($currency_iso).'\''
        );
    }

     /**
     * get the mangopay detatil of the exesting buyer
     * @param int $id_customer
     * @return array
     */
    public function getExistingBuyerMangopayData($id_customer)
    {
        $client_id = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_buyer`
            WHERE `id_customer`='.(int)$id_customer.' AND `mgp_clientid` = \''.pSQL($client_id).'\''
        );
    }

    /**
     * Validate the bank account details.
     * @return void
     */
    public static function validateMangopayBankDetailsFields($bank_params)
    {
        $moduleInstance = Module::getInstanceByName('mpmangopaypayment');
        $errors = array();
        if (!$bank_params['mgp_bank_type']) {
            $errors[] = $moduleInstance->l('Please select a valid bank type.', 'MangopayBuyer');
        }
        if (!$bank_params['mgp_owner_name']) {
            $errors[] = $moduleInstance->l('Please enter account owner name.', 'MangopayBuyer');
        }
        if (!$bank_params['mgp_owner_addressline1']) {
            $errors[] = $moduleInstance->l('Please enter account owner required address line.', 'MangopayBuyer');
        }
        if (!$bank_params['mgp_owner_city']) {
            $errors[] = $moduleInstance->l('Please enter account owner city.', 'MangopayBuyer');
        }
        if (!$bank_params['mgp_owner_postcode']) {
            $errors[] = $moduleInstance->l('Please enter account owner postal code.', 'MangopayBuyer');
        }
        if (!$bank_params['mgp_owner_region']) {
            $errors[] = $moduleInstance->l('Please enter account owner region.', 'MangopayBuyer');
        }
        if (!$bank_params['mgp_owner_country']) {
            $errors[] = $moduleInstance->l('Please enter account owner country.', 'MangopayBuyer');
        }
        if ($bank_params['mgp_bank_type'] == 'IBAN') {
            if (!$bank_params['mgp_iban']) {
                $errors[] = $moduleInstance->l('IBAN is required field for type IBAN/BIC.', 'MangopayBuyer');
            }
            if (!$bank_params['mgp_bic']) {
                $errors[] = $moduleInstance->l('BIC is required field for type IBAN/BIC.', 'MangopayBuyer');
            }
        } elseif ($bank_params['mgp_bank_type'] == 'GB') {
            if (!$bank_params['mgp_account_number']) {
                $errors[] = $moduleInstance->l('Account Number is required field for type GB.', 'MangopayBuyer');
            }
            if (!$bank_params['mgp_sort_code']) {
                $errors[] = $moduleInstance->l('Sort Code is required field for type GB.', 'MangopayBuyer');
            }
        } elseif ($bank_params['mgp_bank_type'] == 'US') {
            if (!$bank_params['mgp_account_number']) {
                $errors[] = $moduleInstance->l('Account Number is required field for type US.', 'MangopayBuyer');
            }
            if (!$bank_params['mgp_aba']) {
                $errors[] = $moduleInstance->l('aba is required field for type US.', 'MangopayBuyer');
            }
        } elseif ($bank_params['mgp_bank_type'] == 'CA') {
            if (!$bank_params['mgp_account_number']) {
                $errors[] = $moduleInstance->l('Account Number is required field for type CA.', 'MangopayBuyer');
            }
            if (!$bank_params['mgp_bank_name']) {
                $errors[] = $moduleInstance->l('Bank Name is required field for type CA.', 'MangopayBuyer');
            }
            if (!$bank_params['mgp_institution_number']) {
                $errors[] = $moduleInstance->l('Institution Number is required field for type CA.', 'MangopayBuyer');
            }
            if (!$bank_params['mgp_branch_code']) {
                $errors[] = $moduleInstance->l('Branch Code is required field for type CA.', 'MangopayBuyer');
            }
        } elseif ($bank_params['mgp_bank_type'] == 'OTHER') {
            if (!$bank_params['mgp_account_number']) {
                $errors[] = $moduleInstance->l('Account Number is required field for type OTHER.', 'MangopayBuyer');
            }
            if (!$bank_params['mgp_bic']) {
                $errors[] = $moduleInstance->l('BIC is required field for type OTHER.', 'MangopayBuyer');
            }
        }
        return $errors;
    }
}
