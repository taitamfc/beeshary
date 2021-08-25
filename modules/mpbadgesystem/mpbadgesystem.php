<?php
/**
* 2010-2017 Webkul
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

include_once 'classes/MpBadge.php';
include_once 'classes/MpSellerBadges.php';
include_once 'classes/MpSellerBadgesConfiguration.php';
include_once dirname(__FILE__).'/../marketplace/classes/WkMpSeller.php';
include_once dirname(__FILE__).'/../marketplace/classes/WkMpSellerProduct.php';

class MpBadgeSystem extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'mpbadgesystem';
        $this->tab = 'front_office_features';
        $this->version = '5.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->dependencies = array('marketplace');
        parent::__construct();
        $this->displayName = $this->l('Marketplace Badge System');
        $this->description = $this->l('Add badges for markeplace sellers.');
    }

    public function hookActionAfterUpdateSeller($params)
    {
        if ($params['id_seller']) {
            $idSeller = $params['id_seller'];
            $badgeInfo = MpSellerBadgesConfiguration::getBadgeConnfigurationByIdSeller($idSeller);
            if ($badgeInfo) {
                $objSellerBadgeConfiguration = new MpSellerBadgesConfiguration($badgeInfo['id']);
            } else {
                $objSellerBadgeConfiguration = new MpSellerBadgesConfiguration();
            }
    
            $objSellerBadgeConfiguration->id_seller = $idSeller;
            $objSellerBadgeConfiguration->active = Tools::getValue('badge_configuration');
            $objSellerBadgeConfiguration->save();
        }
    }

    public function hookDisplayProductButtons($params)
    {
        $id_product = Tools::getValue('id_product');
        $seller_badge_info = $this->displayMpSellerBadge($id_product);
        if ($seller_badge_info) {
            $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
            $this->context->smarty->assign('seller_badges', $seller_badge_info);
            return $this->fetch('module:mpbadgesystem/views/templates/hook/display_seller_badge.tpl');
        }
    }

    public function hookDisplayMpSellerDetailsBottom()
    {
        $sellerInfo = WkMpSeller::getSellerByLinkRewrite(Tools::getValue('mp_shop_name'));
        if ($sellerInfo) {
            $badgeConfiguration = MpSellerBadgesConfiguration::getBadgeConnfigurationByIdSeller($sellerInfo['id_seller']);
            if ($badgeConfiguration && $badgeConfiguration['active']) {
                $obj_mp_seller_badges = new MpSellerBadges();
                $seller_badge_info = $obj_mp_seller_badges->getSellerBadges($sellerInfo['id_seller']);
                if ($seller_badge_info) {
                    $this->context->smarty->assign('seller_badges', $seller_badge_info);
                    $this->context->smarty->assign('display_badge_on_seller_profile', 1);
                    $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
                    return $this->fetch('module:mpbadgesystem/views/templates/hook/display_seller_badge.tpl');
                }
            }
        }
    }

    public function displayMpSellerBadge($id_product)
    {
        $seller_product_obj = new WkMpSellerProduct();
        $mp_product_details = $seller_product_obj->getSellerProductByPsIdProduct($id_product);
        if ($mp_product_details) {
            $badgeConfiguration = MpSellerBadgesConfiguration::getBadgeConnfigurationByIdSeller($mp_product_details['id_seller']);
            if ($badgeConfiguration && $badgeConfiguration['active']) {
                $obj_mp_seller_badges = new MpSellerBadges();
                $seller_badge_info = $obj_mp_seller_badges->getSellerBadges($mp_product_details['id_seller']);
                if ($seller_badge_info) {
                        return $seller_badge_info;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function hookDisplayMpEditProfileTab()
    {
        if (Tools::getValue('controller') == 'AdminSellerInfoDetail') {
            $this->context->smarty->assign('seller_bagde_tab', 1);
            return $this->display(__FILE__, 'add_seller_badge.tpl');
        }
        
    }
    public function hookDisplayMpEditProfileTabContent()
    {
        if (Tools::getValue('controller') == 'AdminSellerInfoDetail') {
            $link = new Link();
            $mp_seller_id = Tools::getValue('id_seller');
            $badgeInfo = MpSellerBadgesConfiguration::getBadgeConnfigurationByIdSeller($mp_seller_id);
            if (!$badgeInfo) {
                $objSellerBadgeConfiguration = new MpSellerBadgesConfiguration();
                $objSellerBadgeConfiguration->id_seller = $mp_seller_id;
                $objSellerBadgeConfiguration->active = 0;
                $objSellerBadgeConfiguration->save();
            }
            $badgeConfiguration = MpSellerBadgesConfiguration::getBadgeConnfigurationByIdSeller($mp_seller_id);
            if ($badgeConfiguration) {
                $this->context->smarty->assign('badgeConfiguration', $badgeConfiguration);
            }
            $obj_mp_seller_badges = new MpSellerBadges();
            $seller_badge_info = $obj_mp_seller_badges->getSellerBadges($mp_seller_id);
            if (!empty($seller_badge_info)) {
                $seller_badges = array();
                foreach ($seller_badge_info as $data) {
                    $seller_badges[] = $data['badge_id'];
                }
    
                $this->context->smarty->assign('seller_badges', $seller_badges);
                $this->context->smarty->assign('seller_badge_info', $seller_badge_info);
            }
            $obj_mp_badges = new MpBadge();
            $badges = $obj_mp_badges->getAllBadges();
            $this->context->smarty->assign('badges', $badges);
            $this->context->smarty->assign('mp_id_seller', $mp_seller_id);
            $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
            $this->context->smarty->assign('seller_bagde_tab', 0);
            $ajax_link = $link->getAdminLink('AdminMpAddNewBadge');
            $this->context->smarty->assign('ajax_link', $ajax_link);
    
            return $this->display(__FILE__, 'add_seller_badge.tpl');
        }
    }

    public function hookDisplayAdminSellerDetailViewBottom()
    {
        $mp_seller_id = Tools::getValue('id_seller');
        $obj_mp_seller_badges = new MpSellerBadges();
        $seller_badge_info = $obj_mp_seller_badges->getSellerBadges($mp_seller_id);
        if (!empty($seller_badge_info)) {
            $this->context->smarty->assign('seller_badge_info', $seller_badge_info);
            $this->context->smarty->assign('modules_dir', _MODULE_DIR_);

            return $this->display(__FILE__, 'view_seller_badges.tpl');
        }
    }

    public function install()
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
        if (!parent::install()
            || !$this->callInstallTab()
            || !$this->registerHook('displayMpEditProfileTab')
            || !$this->registerHook('displayMpEditProfileTabContent')
            || !$this->registerHook('displayAdminSellerDetailViewBottom')
            || !$this->registerHook('displayProductButtons')
            || !$this->registerHook('actionAfterUpdateSeller')
            || !$this->registerHook('displayMpSellerDetailsBottom')) {
            return false;
        }

        return true;
    }

    public function dropTable()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'mp_badges`,
            `'._DB_PREFIX_.'mp_seller_badges`,
            `'._DB_PREFIX_.'mp_seller_badges_configuration`');
    }

    public function callInstallTab()
    {
        $this->installTab('AdminMpAddNewBadge', 'Manage Seller Badges', 'AdminMarketplaceManagement');

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

    public function callUninstallTab()
    {
        $this->uninstallTab('AdminMpAddNewBadge');

        return true;
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

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->dropTable()
            || !$this->callUninstallTab()) {
            return false;
        }

        return true;
    }
}
