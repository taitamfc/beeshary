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

include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
include_once 'classes/sellervacationdetail.php';

class MpSellerVacation extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'mpsellervacation';
        $this->tab = 'front_office_features';
        $this->version = '5.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->dependencies = array('marketplace');
        parent::__construct();
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->displayName = $this->l('MarketPlace Seller Vacation');
        $this->description = $this->l('Seller can add vacation request.');
    }

    public function hookDisplayMPMyAccountMenu()
    {
        if ($this->context->customer->id) {
            $customer_id = $this->context->customer->id;
            $obj_marketplace_seller = new WkMpSeller();
            $already_request = $obj_marketplace_seller->getSellerDetailByCustomerId($customer_id);

            if ($already_request) {
                $is_seller = $already_request['active'];
            }

            if (isset($is_seller)) {
                if ($is_seller) {
                    $this->context->smarty->assign('show_tpl', 1);

                    return $this->fetch('module:mpsellervacation/views/templates/hook/mpsellervacationmenu.tpl');
                }
            }
        }
    }

    public function hookDisplayMPMenuBottom()
    {
        return $this->fetch('module:mpsellervacation/views/templates/hook/mpsellervacationmenu.tpl');
    }

    public function hookDisplayMpCollectionFooter()
    {
        $shop_link_rewrite = Tools::getValue('mp_shop_name');
        if ($shop_link_rewrite) {
            $obj_marketplace_seller = new WkMpSeller();
            $seller_info = $obj_marketplace_seller->getSellerByLinkRewrite($shop_link_rewrite);
            if ($seller_info) {
                $id_seller = $seller_info['id_seller'];
                $showMessage = $this->vacationMessage($id_seller);

                return $showMessage;
            }
        }
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if ('after_price' === $params['type']) {
            $id_product = Tools::getValue('id_product');
            if ($id_product) {
                $obj_seller_vacation_detail = new SellerVacationDetail();
                $seller_info = $obj_seller_vacation_detail->getMpSellerProductDetailByIdProduct($id_product);
                if ($seller_info) {
                    $seller_id = $seller_info['id_seller'];
                    $seller_detail = $obj_seller_vacation_detail->mpSellerValidVacation($seller_id, $this->context->language->id);
                    if ($seller_detail) {
                        if ($seller_detail['active'] == 1) {
                            $this->context->smarty->assign('from_date', $seller_detail['from']);
                            $this->context->smarty->assign('to_date', $seller_detail['to']);
                            $this->context->smarty->assign('description', $seller_detail['description']);
                            return $this->fetch('module:mpsellervacation/views/templates/hook/seller_vacation_msg_without_heading.tpl');
                        }
                    }
                }
            }
        }
    }

    public function hookDisplaySellerProfileDetailBottom()
    {
        return $this->hookDisplayMpCollectionFooter();
    }

    public function hookActionAfterAddMPProduct($params)
    {
        $marketplace_product_id = $params['id_mp_product'];
        if ($marketplace_product_id) {
            $obj_seller_vacation_detail = new SellerVacationDetail();
            $mps_product_detail = $obj_seller_vacation_detail->getMpSellerProductDetailByProductId($marketplace_product_id);
            $product_info = $obj_seller_vacation_detail->getPrestaShopProductId($marketplace_product_id);
            if ($product_info && $product_info['id_ps_product']) {
                $id_product = $product_info['id_ps_product'];

                if ($mps_product_detail) {
                    $id_seller = $mps_product_detail['id_seller'];
                    $seller_detail = $obj_seller_vacation_detail->mpSellerValidVacation($id_seller, $this->context->language->id);
                    if ($seller_detail) {
                        $obj_seller_vacation_detail->enableDisableAddToCartForParticularProduct($seller_detail['active'], $seller_detail['addtocart'], $id_product);
                    }
                }
            }
        }
    }

    public function hookActionAfterToggleMPProductStatus($params)
    {
        $id_product = $params['id_product'];
        if ($id_product) {
            $obj_seller_vacation_detail = new SellerVacationDetail();
            $seller_info = $obj_seller_vacation_detail->getMpSellerProductDetailByIdProduct($id_product);
            if ($seller_info) {
                $seller_id = $seller_info['id_seller'];
                $seller_detail = $obj_seller_vacation_detail->mpSellerValidVacation($seller_id, $this->context->language->id);
                if ($seller_detail) {
                    $obj_seller_vacation_detail->enableDisableAddToCartForParticularProduct($seller_detail['active'], $seller_detail['addtocart'], $id_product);
                }
            }
        }
    }

    /**
     * showing vacation message on seller profile and collection page.
     *
     * @param [type] $seller_id [description]
     *
     * @return [type] [description]
     */
    public function vacationMessage($seller_id)
    {
        $id_seller = $seller_id;
        $obj_seller_vacation_detail = new SellerVacationDetail();
        $seller_detail = $obj_seller_vacation_detail->mpSellerValidVacation($id_seller, $this->context->language->id);
        if ($seller_detail) {
            if ($seller_detail['active'] == 1) {
                $this->context->smarty->assign('from_date', $seller_detail['from']);
                $this->context->smarty->assign('to_date', $seller_detail['to']);
                $this->context->smarty->assign('description', $seller_detail['description']);

                return $this->fetch('module:mpsellervacation/views/templates/hook/seller_vacation_msg_with_heading.tpl');
            }
        }
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($params['object']->id) {
            $new_lang_id = $params['object']->id;

            //Assign all lang's main table in an ARRAY
            $lang_tables = array('marketplace_seller_vacation');

            //If Admin create any new language when we do entry in module all lang tables.
            WkMpHelper::updateIdLangInLangTables($new_lang_id, $lang_tables);
        }
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        }
        $sql = str_replace(array(
            'PREFIX_',
            'ENGINE_TYPE',
        ), array(
            _DB_PREFIX_,
            _MYSQL_ENGINE_,
        ), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        if (!parent::install()
            || !$this->callInstallTab()
            || !$this->registerHook('displayMPMenuBottom')
            || !$this->registerHook('displayProductPriceBlock')
            || !$this->registerHook('displaySellerProfileDetailBottom')
            || !$this->registerHook('displayMPMyAccountMenu')
            || !$this->registerHook('actionAfterAddMPProduct')
            || !$this->registerHook('actionObjectLanguageAddAfter')
            || !$this->registerHook('actionAfterToggleMPProductStatus')
            || !$this->registerHook('displayMpCollectionFooter')
            ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->dropTable()
            || !$this->callUninstallTab()) {
            return false;
        }

        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminSellerVacation', 'Manage Seller Vacation', 'AdminMarketplaceManagement');

        return true;
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

    public function uninstallTab($class_name)
    {
        $id_tab = (int) Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
        } else {
            return false;
        }
    }

    public function callUninstallTab()
    {
        $this->uninstallTab('AdminSellerVacation');

        return true;
    }

    public function dropTable()
    {
        $obj_seller_vacation_detail = new SellerVacationDetail();
        $obj_seller_vacation_detail->resetAddToCartButton();

        Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'marketplace_seller_vacation');
        Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'marketplace_seller_vacation_lang');

        return true;
    }
}
