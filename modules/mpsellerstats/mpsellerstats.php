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

if (Module::isInstalled('mpsellerstats')) {
    include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
}
require_once dirname(__FILE__).'/classes/MpStatsRequiredClasses.php';
class MpSellerStats extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    private $html = '';
    private $postErrors = array();
    public function __construct()
    {
        $this->name = 'mpsellerstats';
        $this->tab = 'front_office_features';
        $this->version = '5.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->dependencies = array('marketplace');
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Marketplace Seller Statistics ');
        $this->description = $this->l('Display Seller wise statistics ');
    }

    public function hookDisplayMPMenuTop($params)
    {
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpSeller && $mpSeller['active']) {
                $this->context->smarty->assign('mpmenu', '1');
                return $this->fetch('module:mpsellerstats/views/templates/hook/stats_link.tpl');
            }
        }
    }

    public function hookDisplayMPMyAccountMenu()
    {
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpSeller && $mpSeller['active']) {
                $this->context->smarty->assign('mpmenu', '0');
                return $this->fetch('module:mpsellerstats/views/templates/hook/stats_link.tpl');
            }
        }
    }

    public function hookDisplayMPMenuBottom()
    {
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpSeller && $mpSeller['active']) {
                $this->context->smarty->assign('mpmenu', '1');
                return $this->fetch('module:mpsellerstats/views/templates/hook/stats_link.tpl');
            }
        }
    }

    public function hookActionSearch($params)
    {
        if ($params['total'] > 0 && strlen($params['searched_query']) >= 3) {
            $idSellers = array();
            foreach ($params['products'] as $product) {
                $sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($product['id_product']);
                if ($sellerProduct && !isset($idSellers[$sellerProduct['id_seller']])) {
                    $idSellers[$sellerProduct['id_seller']] = 1;
                } elseif ($sellerProduct) {
                    $idSellers[$sellerProduct['id_seller']]++;
                }
            }

            if (!empty($idSellers)) {
                MpPage::insertSearchKeyword($params, $idSellers);
            }
        }
    }

    public function hookDisplayBeforeBodyClosingTag($params)
    {
        $idPage = MpPage::getCurrentId();
        if ($idPage) {
            MpConnectionsSource::logHttpReferer(null, $idPage);
            MpPage::setPageViewed($idPage);
        }
    }

    /**
     * Register all tthe hooks needed in the module.
     *
     * @return bool
     */
    public function registerPsHooks()
    {
        $hooks = array(
            'displayBeforeBodyClosingTag',
            'displayMPMyAccountMenu',
            'displayMPMenuTop',
            'actionMpSellerDelete',
            'actionSearch',
            'displayMPMenuBottom'
        );

        return $this->registerHook($hooks);
    }

    public function createTables()
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
        return true;
    }

    public function install()
    {
        if (!parent::install()
            || !$this->createTables()
            || !$this->registerPsHooks()
            ) {
            return false;
        }

        $defineFile = dirname(__FILE__).'/../../src/Adapter/Search/SearchProductSearchProvider.php';
        $findValue = 'Hook::exec(\'actionSearch\', array(
                \'searched_query\' => $queryString,
                \'total\' => $count,

                // deprecated since 1.7.x
                \'expr\' => $queryString,
            ))';


        $replaceIncValue = 'Hook::exec(\'actionSearch\', array(
                \'searched_query\' => $queryString,
                \'total\' => $count,
                \'products\' => $products,
                // deprecated since 1.7.x
                \'expr\' => $queryString,
            ))';

        $str=file_get_contents($defineFile);
        $str=str_replace($findValue, $replaceIncValue, $str);
        file_put_contents($defineFile, $str);
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->deleteTables()
            ) {
            return false;
        }

        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'mp_page`,
            `'._DB_PREFIX_.'mp_page_viewed`,
            `'._DB_PREFIX_.'mp_connections_source`,
            `'._DB_PREFIX_.'mp_connections_ipaddress`,
            `'._DB_PREFIX_.'mp_statssearch`
        ');
    }
}
