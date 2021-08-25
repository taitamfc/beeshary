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

class MangopayConfig extends ObjectModel
{
    public $id;
    public $id_employee;
    public $mgp_clientid;
    public $mgp_passphrase;
    public $mgp_userid;
    public $mgp_walletid;
    public $user_type;
    public $user_email;
    public $currency_iso;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_mangopay_config',
        'primary' => 'id',
        'fields' => array(
            'id_employee' => array('type' => self::TYPE_INT),
            'mgp_clientid' => array('type' => self::TYPE_STRING),
            'mgp_passphrase' => array('type' => self::TYPE_STRING),
            'mgp_userid' => array('type' => self::TYPE_INT),
            'mgp_walletid' => array('type' => self::TYPE_INT),
            'user_type' => array('type' => self::TYPE_STRING),
            'currency_iso' => array('type' => self::TYPE_STRING),
            'user_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Save the configuration data of the mangopay.
     * @param [type] $params
     * @return void
     */
    public static function saveMangopayConfigurationData($params)
    {
        if (is_array($params)) {
            $objMgpConfig = new self();
            $objMgpConfig->mgp_clientid = $params['mgp_clientid'];
            $objMgpConfig->id_employee = $params['id_employee'];
            $objMgpConfig->mgp_passphrase = $params['mgp_passphrase'];
            $objMgpConfig->mgp_userid = $params['mgp_userid'];
            $objMgpConfig->mgp_walletid = $params['mgp_walletid'];
            $objMgpConfig->currency_iso = $params['currency_iso'];
            $objMgpConfig->user_type = $params['user_type'];
            $objMgpConfig->user_email = $params['user_email'];

            return $objMgpConfig->save();
        } else {
            return false;
        }
    }

    /**
     * Get the admin configurtion details.
     * @param [type] $client_id
     * @param [type] $pass_phrase
     * @return void
     */
    public function getAdminConfigData($client_id, $pass_phrase)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_config`
            WHERE `mgp_clientid` = \''.pSQL($client_id).'\'
            AND `mgp_passphrase` = \''.pSQL($pass_phrase).'\''
        );
    }

    /**
     * Get the admin configuration details by currence.
     * @param [type] $client_id
     * @param [type] $pass_phrase
     * @param [type] $currency_iso
     * @return void
     */
    public function getAdminConfigDataByCurrency($client_id, $pass_phrase, $currency_iso)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_config`
            WHERE `mgp_clientid` = \''.pSQL($client_id).'\'
            AND `mgp_passphrase` = \''.pSQL($pass_phrase).'\'
            AND `currency_iso` = \''.pSQL($currency_iso).'\''
        );
    }

    /**
     * Get the super admin employee id.
     * @return int
     */
    public static function getSupperAdmin()
    {
        if ($data = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'employee` ORDER BY `id_employee`')) {
            foreach ($data as $emp) {
                $employee = new Employee($emp['id_employee']);
                if ($employee->isSuperAdmin()) {
                    return $emp['id_employee'];
                }
            }
        }
        return false;
    }

    // create new order status with name for mangopay bankwire payments
    public function insertMangopayBankwireOrderStatus()
    {
        $moduleInstance = Module::getInstanceByName('mpmangopaypayment');
        $objOrderState = new OrderState();
        $objOrderState->invoice = 0;
        $objOrderState->send_email = 1;
        $objOrderState->module_name = 'mpmangopaypayment';
        $objOrderState->color = '#4169E1';
        $objOrderState->unremovable = 1;
        foreach (Language::getLanguages(false) as $language) {
            $objOrderState->name[$language['id_lang']] = $moduleInstance->l(
                'Awaiting mangopay bank wire payment',
                'MangopayConfig'
            );
            $objOrderState->template[$language['id_lang']] = 'bankwire';
        }
        if ($objOrderState->save()) {
            return Configuration::updateValue('PS_OS_MANGOPAY_BANKWIRE', $objOrderState->id);
        }
        return false;
    }

    public static function encryptString($value)
    {
        $characters = '0123456789';
        $rand = '';
        for ($i = 0; $i < 8; ++$i) {
            $rand = $rand.$characters[mt_rand(0, Tools::strlen($characters) - 1)];
        }
        return str_rot13($rand.$value.str_shuffle($rand));
    }

    public static function decryptString($encodedValue)
    {
        $decodeString = str_rot13($encodedValue);
        return Tools::substr($decodeString, 8, Tools::strlen($decodeString)-16);
    }

    public function getOrderIdsByOrderReference($orderReference)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_order` FROM `'._DB_PREFIX_.'orders` WHERE `reference` = \''.pSql($orderReference).'\''
        );
    }

    // Gdpr function to  delete customer Mangopay data
    public function deleteGdprCustomerMangopayDetails($idCustomer)
    {
        Db::getInstance()->delete('wk_mp_mangopay_buyer', 'id_customer = '.(int)$idCustomer);
        if ($sellerInfo = WkMpSeller::getSellerByCustomerId($idCustomer)) {
            Db::getInstance()->delete('wk_mp_mangopay_seller', 'id_seller = '.(int)$sellerInfo['id_seller']);
            Db::getInstance()->delete('wk_mp_mangopay_seller_country', 'id_seller = '.(int)$sellerInfo['id_seller']);
        }
        return true;
    }
}
