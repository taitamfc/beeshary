<?php


/* Security */
if (!defined('_PS_VERSION_')) {
    exit;
}
//include_once '../mpbadgesystem/classes/MpBadge.php';
require_once(_PS_MODULE_DIR_.'mpbadgesystem/classes/MpBadge.php');
require_once(_PS_MODULE_DIR_.'mpbadgesystem/classes/MpSellerBadges.php');
require_once(_PS_MODULE_DIR_.'mpbadgesystem/classes/MpSellerBadgesConfiguration.php');
require_once(_PS_MODULE_DIR_.'beesharyapi/lib/WebserviceSpecificManagementArtisans.php');
require_once(_PS_MODULE_DIR_.'beesharyapi/lib/WebserviceSpecificManagementArtisansCategory.php');
require_once(_PS_MODULE_DIR_.'beesharyapi/lib/WebserviceSpecificManagementGetArtisan.php');
require_once(_PS_MODULE_DIR_.'beesharyapi/lib/WebserviceSpecificManagementArtisanBySlug.php');
require_once(_PS_MODULE_DIR_.'beesharyapi/lib/WebserviceSpecificManagementSavoirFaire.php');
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

/**
 * Class beesharyapi
 */
class beesharyapi extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'beesharyapi';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Panos Gergos';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('BeeShary Api');
        $this->description = $this->l('BeeShary Webservice for markerplace sellers for partners');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('BEESHARYAPI')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {

        parent::install();

        $this->registerHook('addWebserviceResources');
        $this->registerHook('actionAddBadgeAfterAddSeller');
        $this->registerHook('actionCheckSellerHasPartnerBadge');
        $this->registerHook('displaySellerPartner');
        $this->registerHook('displaySellerPartnerInfoOnDashboard');
//        $this->registerHook('displayBeeSharyPartnerInfo');
        Configuration::updateValue('BEESHARYAPI', 'BeeShary Api');

        return true;

    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('BEESHARYAPI')
        ) {
            return false;
        }

        return true;
    }

    public function hookAddWebserviceResources()

    {

        return array(

            'artisans' => array(

                'description' => 'Get Artisans list by Partner Id',

                'specific_management' => true,

            ),
//            'artisans_category' => array(
//
//                'description' => 'Get BeeShary Artisans by Activities Category and Partner Id',
//
//                'specific_management' => true,
//
//            ),
            'get_artisan' => array(

                'description' => 'Get BeeShary Artisans by artisan id and Partner Id',

                'specific_management' => true,

            ),
            'artisan_by_slug' => array(

                'description' => 'Get BeeShary Artisans by artisan slug (link_rewrite)',

                'specific_management' => true,

            ),
            'savoir_faire' => array(

                'description' => 'Get BeeShary Artisans "Savoir faire" list',

                'specific_management' => true,

            )
        );

    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch('module:'.$this->name.'/views/templates/widget/partnerBlock.tpl');
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $partnerId = null;
        if (isset($configuration['partner'])) {
            $partnerId = $configuration['partner'];
        }
        // find partner data
        $objMpBadge = new MpBadge();
        $mpBadgeInfo = $objMpBadge->getBadgeInfo($partnerId);
        $partnerData = null;
        if (isset($mpBadgeInfo[0]) && $mpBadgeInfo[0]['id'] == $partnerId) {
            $partnerData = $mpBadgeInfo[0];
        }
        return [
            'partnerId' => $partnerId,
            'partnerData' => $partnerData,
        ];
    }
    /**
     * hook to display the parter logo given the parter id
     * to use into tempalate like this: {hook h="displayBeeSharyPartnerLogo" partner=$smarty.get.partner}
     */
    public function hookDisplayBeeSharyPartnerLogo($params)
    {
        if (!isset($params['partner'])) {
            return null;
        }
        $partnerLogoPath = sprintf("%s/../mpbadgesystem/views/img/badge_img/%s.jpg",dirname(__FILE__),  $params['partner']);
        if (!is_file($partnerLogoPath)) {
            return null;
        }
        $this->context->smarty->assign('partnerId', $params['partner']);
        return $this->fetch('module:beesharyapi/views/templates/hook/display_seller_badge_on_login.tpl');
    }

    public function hookDisplaySellerPartner($params)
    {
        if (!isset($params['seller_id'])) {
            return null;
        }
        $seller_badge_info = $this->displayMpSellerBadge($params['seller_id']);
        if ($seller_badge_info) {
            $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
            $this->context->smarty->assign('seller_badges', $seller_badge_info);
            return $this->fetch('module:beesharyapi/views/templates/hook/display_seller_badge.tpl');
        }
    }

    public function hookDisplaySellerPartnerInfoOnDashboard($params)
    {
        if (!isset($params['seller_id'])) {
            return null;
        }
        $seller_badge_info = $this->displayMpSellerBadge($params['seller_id']);
        if ($seller_badge_info) {
            $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
            $this->context->smarty->assign('seller_badges', $seller_badge_info);
            return $this->fetch('module:beesharyapi/views/templates/hook/display_seller_partner_dashboard.tpl');
        }
    }

    public function displayMpSellerBadge($sellerId)
    {
        $obj_mp_seller_badges = new MpSellerBadges();
        $seller_badge_info = $obj_mp_seller_badges->getSellerBadges($sellerId);
        if ($seller_badge_info) {
            return $seller_badge_info;
        } else {
            return false;
        }
    }

    /**
     * [hookActionAfterAddSeller -> add a badge for the seller based on the partner id
     *
     * @param [type] $params [seller information]
     * actionAddBadgeAfterAddSeller
     * @return [type] [description]
     */
    public function hookActionAddBadgeAfterAddSeller(array $params)
    {
        try {
            $mpSellerId = $params['sellerId'];
            $partnerId = $params['partnerId'];
            $objSellerBadge = new MpSellerBadges();
            $objSellerBadge->deletePrevSellerBadges($mpSellerId);
            $objSellerBadge->badge_id = $partnerId;
            $objSellerBadge->mp_seller_id = $mpSellerId;
            $result = $objSellerBadge->add();

            if (!$result) {
                return false;
            }
            // add badge configuration
            $objSellerBadgeConfiguration = new MpSellerBadgesConfiguration();
            $objSellerBadgeConfiguration->id_seller = $mpSellerId;
            $objSellerBadgeConfiguration->active = 1;
            $objSellerBadgeConfiguration->save();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     *
     * @param array $params
     * @return bool
     */
    public function hookActionCheckSellerHasPartnerBadge(array $params)
    {
        try {
            if (!isset($params['sellerId'])) {
                return false;
            }
            $seller_badge_info = $this->displayMpSellerBadge($params['sellerId']);
            if (false === $seller_badge_info) {
                return false;
            }
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
