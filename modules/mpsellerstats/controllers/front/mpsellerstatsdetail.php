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

class MpSellerStatsMpSellerStatsDetailModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($mpSeller && $mpSeller['active']) {
                $idSeller = $mpSeller['id_seller'];
                $preselectDateRange = 2;
                WkMpHelper::assignGlobalVariables();
                $dateFrom = date('Y-m-01');
                $dateTo = date('Y-m-t');
                $this->context->smarty->assign(array(
                    'is_seller' => $mpSeller['active'],
                    'logic' => 'seller_stats',
                    'nav_logic' => 'seller_stats',
                    'preselectDateRange' => $preselectDateRange,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                ));

                MpPage::assignMonthNameOnJs();
                Media::addJsDef(array(
                    'userFriendlyDateFrom' => date("d-m-Y", strtotime($dateFrom)),
                    'userFriendlyDateTo' => date("d-m-Y", strtotime($dateTo)),
                    'currentDate' => date('d-m-Y'),
                    'id_object' => $idSeller,
                    'stats_link' => $this->context->link->getModuleLink('mpsellerstats', 'mpsellerstatsdetail'),
                ));

                $this->setTemplate('module:mpsellerstats/views/templates/front/seller_stats.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('authentication'));
        }
    }

    public function displayAjaxGetShopPageSocialVisits()
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

        // Social data
        $socialData = MpConnectionsSource::getSocialData($dateBetween, 'shopstore', Tools::getValue('id_object'));
        $this->context->smarty->assign('tplData', $socialData);
        $tpl_social_file = $this->context->smarty->fetch(_PS_MODULE_DIR_.'mpsellerstats/views/templates/front/_partials/mpsellerstats-tabledata.tpl');

        // Demo graphic country data
        $countryData = MpConnectionsSource::getDemographicDataCountryWise($dateBetween, 'shopstore', Tools::getValue('id_object'));
        $this->context->smarty->assign('tplData', $countryData);
        $tpl_country_file = $this->context->smarty->fetch(_PS_MODULE_DIR_.'mpsellerstats/views/templates/front/_partials/mpsellerstats-tabledata.tpl');

        // Demo graphic city data
        $countryData = MpConnectionsSource::getDemographicDataCityWise($dateBetween, 'shopstore', Tools::getValue('id_object'));
        $this->context->smarty->assign('tplData', $countryData);
        $tpl_city_file = $this->context->smarty->fetch(_PS_MODULE_DIR_.'mpsellerstats/views/templates/front/_partials/mpsellerstats-tabledata.tpl');

        $data = array(
            'social_data' => $socialData,
            'tpl_social_file' => $tpl_social_file,
            'tpl_country_file' => $tpl_country_file,
            'tpl_city_file' => $tpl_city_file,
        );

        die(Tools::jsonEncode($data));
    }

    public function displayAjaxGetShopPageVisits()
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

        $params = array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        );

        die(Tools::jsonEncode(array('dashtrends' => $this->mpShopPagedData($params))));
    }

    public function mpShopPagedData($params)
    {
        // Retrieve, refine and add up data for the selected period
        $this->mpShopPageData = $this->refineData($params['dateFrom'], $params['dateTo']);

        return array(
            'data_value' => array(
                'visits_score' => array_sum($this->mpShopPageData['visits']),
                'visitor_score' => array_sum($this->mpShopPageData['visitor']),
            ),
            'data_chart' => array('dash_trends_chart1' => $this->getChartTrends()),
        );
    }

    public function getChartTrends()
    {
        $chartData = array();
        foreach (array_keys($this->mpShopPageData) as $chartKey) {
            foreach ($this->mpShopPageData[$chartKey] as $key => $value) {
                $chartData[$chartKey][] = array($key, $value);
            }
        }

        $charts = array(
            'visits' => $this->module->l('Visits'),
            'visitor' => $this->module->l('Visitor'),
        );

        $gfxColor = array('#00aff0','#72c279');

        $i = 0;
        $data = array(
            'chart_type' => 'line_chart_trends',
            'date_format' => $this->context->language->date_format_lite,
        );

        foreach ($charts as $key => $title) {
            $data['data'][] = array(
                'id' => $key,
                'key' => $title,
                'color' => $gfxColor[$i],
                'values' => $chartData[$key],
                //'disabled' => ($key == 'sales' ? false : true)
            );
            $i++;
        }

        return $data;
    }

    protected function refineData($dateFrom, $dateTo)
    {
        $dateBetween = ' \''.pSQL($dateFrom).' 00:00:00\' AND \''.pSQL($dateTo).' 23:59:59\' ';
        $tmpData = array(
            'visits' => MpPage::getTotalVisits($dateBetween, 'shopstore', Tools::getValue('id_object')),
            'visitor' => MpConnectionsSource::getTotalVisitor($dateBetween, 'shopstore', Tools::getValue('id_object')),
        );

        $refinedData = array();
        $from = strtotime($dateFrom.' 00:00:00');
        $to = min(time(), strtotime($dateTo.' 23:59:59'));
        for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date)) {
            $refinedData['visits'][$date] = isset($tmpData['visits'][$date]) ? $tmpData['visits'][$date] : 0;
            $refinedData['visitor'][$date] = isset($tmpData['visitor'][$date]) ? $tmpData['visitor'][$date] : 0;
        }

        if (empty($refinedData)) {
            $refinedData['visits'][0] = 0;
            $refinedData['visitor'][0] = 0;
        }

        return $refinedData;
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

        $this->registerJavascript('mpstats', 'modules/'.$this->module->name.'/views/js/mpstats.js');
        //If admin allow to use custom css on Marketplace theme
        if (Configuration::get('WK_MP_ALLOW_CUSTOM_CSS')) {
            $this->registerStylesheet('mp-custom_style-css', 'modules/marketplace/views/css/mp_custom_style.css');
        }
    }
}
