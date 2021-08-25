<?php
/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once 'classes/MarketplaceSellerPayment.php';
include_once 'classes/MpSellerPaymentTransactions.php';
class MpSellerPayment extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'mpsellerpayment';
        $this->tab = 'front_office_features';
        $this->version = '4.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        parent::__construct();
        $this->dependencies = array('marketplace');
        $this->displayName = $this->l('Marketplace Seller Payment');
        $this->description = $this->l('Manage marketplace seller payment and commission');
    }

    public function callInstallTab()
    {
        $this->installTab('AdminSellerPayment', 'Manage Seller Payment', 'AdminMarketplaceManagement');

        return true;
    }

    public function hookDisplayMpmyaccountmenuhook($params)
    {
        $customer_id = $this->context->customer->id;
        if ($customer_id) {
            $obj_marketplace_seller = new SellerInfoDetail();
            $mp_seller = $obj_marketplace_seller->getSellerDetailsByCustomerId($customer_id);
            if ($mp_seller && $mp_seller['active']) {
                $this->context->smarty->assign('mpmenu', '0');
                $this->context->smarty->assign('sellertransactions_link', $this->context->link->getModuleLink('mpsellerpayment', 'sellertransactions'));
                return $this->fetch('module:mpsellerpayment/views/templates/hook/seller_transaction.tpl');
            }
        }
    }

    public function hookDisplayMpmenuhookext($params)
    {
        $customer_id = $this->context->customer->id;
        if ($customer_id) {
            $obj_marketplace_seller = new SellerInfoDetail();
            $mp_seller = $obj_marketplace_seller->getSellerDetailsByCustomerId($customer_id);
            if ($mp_seller && $mp_seller['active']) {
                $this->context->smarty->assign('mpmenu', '1');
                $this->context->smarty->assign('sellertransactions_link', $this->context->link->getModuleLink('mpsellerpayment', 'sellertransactions'));
                return $this->fetch('module:mpsellerpayment/views/templates/hook/seller_transaction.tpl');
            }
        }
    }

    /**
     * [hookActionSellerPaymentTransaction - Action perform when customer order any product of seller]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function hookActionSellerPaymentTransaction($params)
    {
        if ($params['seller_cart_product_data']['product_list']) {

            $total_seller_amt = 0;
            foreach ($params['seller_cart_product_data']['product_list'] as $product_attr_arr) {
                foreach ($product_attr_arr as $product_info) {
                    $seller_amt = $product_info['seller_amount'] + $product_info['seller_tax'];

                    $total_seller_amt += $seller_amt;

                    $id_seller = $product_info['id_seller'];
                }
            }

            //$total_seller_amt_withshipping = $total_seller_amt + $params['seller_cart_product_data']['total_seller_shipping'];

            $id_currency = $params['id_currency'];

            $obj_seller_payment = new MarketplaceSellerPayment();

            $check_seller = $obj_seller_payment->getDetailsByIdSellerAndIdCurrency($id_seller, $id_currency);

            if (!$check_seller) {
                $obj_seller_payment->id_seller = $id_seller;
                $obj_seller_payment->total_earning = $total_seller_amt;
                $obj_seller_payment->total_paid = 0;
                $obj_seller_payment->total_due = $total_seller_amt;
                $obj_seller_payment->id_currency = $id_currency;
                $obj_seller_payment->save();
            } else {
                $total_earning = $check_seller['total_earning'] + $total_seller_amt;
                $total_due = $check_seller['total_due'] + $total_seller_amt;

                $obj_seller_payment->updateEarningAndDueByIdSellerAndIdCurrency($total_earning, $total_due, $id_seller, $id_currency);
            }

        }
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    protected function createTables()
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

    public function registerModuleHooks()
    {
        return $this->registerHook(
            ['displayMpmyaccountmenuhook', 'actionSellerPaymentTransaction', 'displayMpmenuhookext']
        );
    }

    public function install()
    {
        if (!parent::install()
            || !$this->createTables()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
            ) {
            return false;
        }

        return true;
    }

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

    public function drop()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'marketplace_seller_payment`,
            `'._DB_PREFIX_.'marketplace_seller_payment_transactions`');

        return true;
    }
    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallTab()
            ||  !$this->drop()
            ) {
            return false;
        }

        return true;
    }
}
