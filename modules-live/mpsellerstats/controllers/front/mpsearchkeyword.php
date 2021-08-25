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

class MpSellerStatsMpSearchKeywordModuleFrontController extends ModuleFrontController
{
    protected $mpIdSeller = false;
    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($mpSeller && $mpSeller['active']) {
                $idSeller = $mpSeller['id_seller'];
                $this->mpIdSeller = $idSeller;
                $preselectDateRange = 2;
                WkMpHelper::assignGlobalVariables();
                $dateFrom = date('Y-m-01');
                $dateTo = date('Y-m-t');

                $this->context->smarty->assign(array(
                    'is_seller' => $mpSeller['active'],
                    'nav_logic' => 'search',
                    'logic' => 'seller_stats',
                    'preselectDateRange' => $preselectDateRange,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                ));
                MpPage::assignMonthNameOnJs();
                Media::addJsDef(array(
                    'userFriendlyDateFrom' => date("d-m-Y", strtotime($dateFrom)),
                    'userFriendlyDateTo' => date("d-m-Y", strtotime($dateTo)),
                    'currentDate' => date('d-m-Y'),
                    'stats_link' => $this->context->link->getModuleLink('mpsellerstats', 'mpsearchkeyword'),
                ));

                $this->setTemplate('module:mpsellerstats/views/templates/front/search_keyword.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('authentication'));
        }
    }

    public function displayAjaxGetSearchKeyword()
    {
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');
        if ($preselectDateRange = Tools::getValue('preselectDateRange')) { //For day, month, year button
            $dateRange = MpPage::getPreselectedDateRange($preselectDateRange);
            if ($dateRange) {
                $dateFrom = $dateRange['dateFrom'];
                $dateTo = $dateRange['dateTo'];
            }
        } else { //For datepicker
            $dateFrom = Tools::getValue('dateFrom');
            $dateTo = Tools::getValue('dateTo');
        }

        $dateBetween = ' \''.pSQL($dateFrom).' 00:00:00\' AND \''.pSQL($dateTo).' 23:59:59\' ';
        $searchData = MpPage::getSearchKeyword($dateBetween, $this->mpIdSeller);
        $this->context->smarty->assign('searchData', $searchData);
        $temp_file = $this->context->smarty->fetch(_PS_MODULE_DIR_.'mpsellerstats/views/templates/front/_partials/mpsellerstats-search.tpl');
        $data = array(
            'tpl_file' => $temp_file,
        );
        die(Tools::jsonEncode($data));
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUI('ui.datepicker');
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('mp_global_style', 'modules/'.$this->module->name.'/views/css/mp_global_style.css');
        $this->registerStylesheet('mpstatscss', 'modules/'.$this->module->name.'/views/css/mpstats.css');

        // Include Required Prerequisites
        $this->registerJavascript("moment-js", '//cdn.jsdelivr.net/momentjs/latest/moment.min.js', array('server' => 'remote'));

        // Include Date Range Picker
        $this->registerJavascript("datepicker-js", '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js', array('server' => 'remote'));
        $this->registerStylesheet("datepicker-css", '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css', array('server' => 'remote'));

        $this->registerJavascript('mpsearchkeyword', 'modules/'.$this->module->name.'/views/js/mpsearchkeyword.js');
        //If admin allow to use custom css on Marketplace theme
        if (Configuration::get('WK_MP_ALLOW_CUSTOM_CSS')) {
            $this->registerStylesheet('mp-custom_style-css', 'modules/marketplace/views/css/mp_custom_style.css');
        }
    }
}
