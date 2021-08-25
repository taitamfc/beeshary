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

class MpSellerStatsMpSellerProductStatsModuleFrontController extends ModuleFrontController
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

                if (Tools::getValue('viewstats')) {
                    $idPsProduct = Tools::getValue('id_ps_product');
                    $objProduct = new Product($idPsProduct, false, $this->context->language->id);
                    $this->context->smarty->assign('product_name', $objProduct->name);
                    $this->context->smarty->assign('viewstats', true);
                    Media::addJsDef(array(
                        'id_object' => $idPsProduct,
                    ));
                } else {
                    Media::addJsDef(array(
                        'id_object' => 0,
                    ));
                    //$this->assignMpProduct($idSeller, $dateFrom, $dateTo);
                }
                $this->context->smarty->assign(array(
                    'is_seller' => $mpSeller['active'],
                    'nav_logic' => 'product_stats',
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
                    'stats_link' => $this->context->link->getModuleLink('mpsellerstats', 'mpsellerproductstats'),
                ));

                $this->setTemplate('module:mpsellerstats/views/templates/front/product_stats.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('authentication'));
        }
    }

    protected function assignMpProduct($idSeller, $dateFrom, $dateTo)
    {
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $idLang = $this->context->language->id;
        } else {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        $language = Language::getLanguage((int) $idLang);

        $sellerProduct = WkMpSellerProduct::getSellerProduct($idSeller, true, $idLang);
        $dateBetween = ' \''.pSQL($dateFrom).' 00:00:00\' AND \''.pSQL($dateTo).' 23:59:59\' ';
        if ($sellerProduct) {
            //$sellerProduct = $this->getProductDetails($sellerProduct);
            foreach ($sellerProduct as &$products) {
                if ($products['id_ps_product']) { // if product activated
                    $idPsProduct = $products['id_ps_product'];

                    $objProduct = new Product($idPsProduct, false, $idLang);
                    $cover = Product::getCover($idPsProduct);

                    if ($cover) {
                        $objImage = new Image($cover['id_image']);
                        $products['image_path'] = _THEME_PROD_DIR_.$objImage->getExistingImgPath().'.jpg';
                        $products['cover_image'] = $idPsProduct.'-'.$cover['id_image'];
                    }

                    $products['id_product'] = $idPsProduct;
                    $products['id_lang'] = $idLang;
                    $products['lang_iso'] = $language['iso_code'];
                    $products['obj_product'] = $objProduct;
                } else { //if product not active
                    $unactiveImage = WkMpSellerProduct::getInactiveProductImageByIdProduct($products['id_mp_product']);
                    // product is inactive so by default first image is taken because no one is cover image
                    if ($unactiveImage) {
                        $products['unactive_image'] = $unactiveImage[0]['seller_product_image_name'];
                    }
                }
                $products['visits'] = array_sum(MpPage::getTotalVisits($dateBetween, 'product', $products['id_ps_product']));
                $products['visitor'] = array_sum(MpConnectionsSource::getTotalVisitor($dateBetween, 'product', $products['id_ps_product']));
            }
        }

        $this->context->smarty->assign('product_lists', $sellerProduct);
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
        $socialData = MpConnectionsSource::getSocialData($dateBetween, 'product', Tools::getValue('id_object'), $this->mpIdSeller);
        $this->context->smarty->assign('tplData', $socialData);
        $tpl_social_file = $this->context->smarty->fetch(_PS_MODULE_DIR_.'mpsellerstats/views/templates/front/_partials/mpsellerstats-tabledata.tpl');

        // Demo graphic country data
        $countryData = MpConnectionsSource::getDemographicDataCountryWise($dateBetween, 'product', Tools::getValue('id_object'), $this->mpIdSeller);
        $this->context->smarty->assign('tplData', $countryData);
        $tpl_country_file = $this->context->smarty->fetch(_PS_MODULE_DIR_.'mpsellerstats/views/templates/front/_partials/mpsellerstats-tabledata.tpl');

        // Demo graphic city data
        $countryData = MpConnectionsSource::getDemographicDataCityWise($dateBetween, 'product', Tools::getValue('id_object'), $this->mpIdSeller);
        $this->context->smarty->assign('tplData', $countryData);
        $tpl_city_file = $this->context->smarty->fetch(_PS_MODULE_DIR_.'mpsellerstats/views/templates/front/_partials/mpsellerstats-tabledata.tpl');
        $this->assignMpProduct($this->mpIdSeller, $dateFrom, $dateTo);
        $tpl_product_file = $this->context->smarty->fetch(_PS_MODULE_DIR_.'mpsellerstats/views/templates/front/_partials/mpsellerstata-productdata.tpl');
        $data = array(
            'social_data' => $socialData,
            'tpl_social_file' => $tpl_social_file,
            'tpl_country_file' => $tpl_country_file,
            'tpl_city_file' => $tpl_city_file,
            'tpl_product_file' => $tpl_product_file,
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
        $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);

        $tmpData = array(
            'visits' => MpPage::getTotalVisits($dateBetween, 'product', Tools::getValue('id_object'), $this->mpIdSeller),
            'visitor' => MpConnectionsSource::getTotalVisitor($dateBetween, 'product', Tools::getValue('id_object'), $this->mpIdSeller),
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
