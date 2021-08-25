<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'marketplace/classes/WkMpRequiredClasses.php');
require_once(dirname(__FILE__).'/classes/WkMpSellerPaymentRequest.php');

class MpSellerPaymentRequest extends Module
{
    const _INSTALL_SQL_FILE_ = 'install.sql';

    public function __construct()
    {
        $this->name = 'mpsellerpaymentrequest';
        $this->tab = 'front_office_features';
        $this->version = '5.0.1';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->need_instance = 1;
        $this->dependencies = array('marketplace');
        parent::__construct();
        $this->displayName = $this->l('Seller Payment Request');
        $this->description = $this->l('Add seller payment request option in your marketplace module.');
        $this->confirmUninstall = $this->l('Are you sure to uninstall this module? Please confirm');
    }

    public function install()
    {
        if (!parent::install()
            || !WkMpSellerPaymentRequest::createTable()
            || !$this->callInstallTab()
            || !$this->addConfiguration()
            || !$this->registerHook('displayMPMenuBottom')
            || !$this->registerHook('displayBackOfficeFooter')
            || !$this->registerHook('displayMPMyAccountMenu')
            || !$this->registerHook('actionAfterCancelSellerTransaction')
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !WkMpSellerPaymentRequest::deleteTable()
            || !$this->uninstallTab()
            || !$this->removeConfiguration()
        ) {
            return false;
        }
        return true;
    }

    public function removeConfiguration()
    {
        if (!Configuration::deleteByName('WK_MP_SPR_LOCK_IN_PERIOD')
            || !Configuration::deleteByName('WK_MP_SPR_LOCK_IN_AMOUNT')
            || !Configuration::deleteByName('WK_MP_SPR_MAX_WITHDRAWAL')
        ) {
            return false;
        }
        return true;
    }

    public function addConfiguration()
    {
        if (!Configuration::updateValue('WK_MP_SPR_LOCK_IN_PERIOD', 0)
            || !Configuration::updateValue('WK_MP_SPR_LOCK_IN_AMOUNT', 0)
            || !Configuration::updateValue('WK_MP_SPR_MAX_WITHDRAWAL', 0)
        ) {
            return false;
        }
        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminSellerPaymentRequest', 'Seller Payment Request', 'AdminMarketplaceManagement');
        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }
        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tabParentId = 0;
        if ($tabParentName) {
            $this->createModuleTab($className, $tabName, $tabParentId, $tabParentName);
        } else {
            $this->createModuleTab($className, $tabName, $tabParentId);
        }
    }

    public function createModuleTab($className, $tabName, $tabParentId, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = $tabParentId;
        }
        $tab->module = $this->name;
        return $tab->add();
    }

    public function hookDisplayMPMenuBottom()
    {
        $idCustomer = $this->context->customer->id;
        $seller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
        if ($this->context->controller->module->name == $this->name) {
            $logic = 'mp_seller_payment_request';
        } else {
            $logic = '';
        }
        if ($seller && $seller['active']) {
            $this->context->smarty->assign(array('link', $this->context->link, 'logic' => $logic));
            return $this->fetch('module:'.$this->name.'/views/templates/hook/paymentrequestmenu.tpl');
        }
    }

    public function hookDisplayMPMyAccountMenu()
    {
        $idCustomer = $this->context->customer->id;
        $seller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
        if ($seller && $seller['active']) {
            return $this->fetch('module:'.$this->name.'/views/templates/hook/mpmyaccountmenu.tpl');
        }
    }

    public function hookActionAfterCancelSellerTransaction($params)
    {
        if (isset($params['id_seller_transaction_history'])
            && $idMpTransaction = (int)$params['id_seller_transaction_history']
        ) {
            WkMpSellerPaymentRequest::cancelByTransactionId($idMpTransaction);
        }
        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminSellerPaymentRequest'));
    }
}
