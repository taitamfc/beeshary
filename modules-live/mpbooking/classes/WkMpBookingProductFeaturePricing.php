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

class WkMpBookingProductFeaturePricing extends ObjectModel
{
    public $id_booking_product_info;
    public $feature_price_name;
    public $date_selection_type;
    public $date_from;
    public $date_to;
    public $is_special_days_exists;
    public $special_days;
    public $impact_way;
    public $impact_type;
    public $impact_value;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_booking_product_feature_pricing',
        'primary' => 'id_feature_price_rule',
        'multilang' => true,
        'fields' => array(
            'id_booking_product_info' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'impact_way' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'is_special_days_exists' => array('type' => self::TYPE_INT),
            'date_selection_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'special_days' => array('type' => self::TYPE_STRING),
            'impact_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'impact_value' => array('type' => self::TYPE_FLOAT),
            'active' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),

            /* Lang fields */
            'feature_price_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
    ), );

    const WK_DATE_SELECTION_DATE_RANGE = 1;
    const WK_DATE_SELECTION_SPECIFIC_DATE = 2;

    public function __construct($id = null)
    {
        $this->moduleInstance = Module::getInstanceByName('mpbooking');
        parent::__construct($id);
    }

    public function deleteFeaturePricePlansByIdBookingProductInfo($id_booking_product_info)
    {
        Db::getInstance()->delete(
            'wk_mp_booking_product_feature_pricing_lang',
            '`id_feature_price_rule` IN (SELECT id_feature_price_rule FROM `'._DB_PREFIX_.
            'wk_mp_booking_product_feature_pricing` WHERE `id_booking_product_info`='.(int) $id_booking_product_info.')'
        );
        Db::getInstance()->delete(
            'wk_mp_booking_product_feature_pricing',
            '`id_booking_product_info`='.(int) $id_booking_product_info
        );
        return true;
    }

    public function getProductFeaturePriceRules($id_booking_product_info = 0, $idLang = false, $active = 2)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT bpi.*, bpil.`feature_price_name`, bpil.`id_lang`
                FROM `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing` AS bpi
                INNER JOIN `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing_lang` AS bpil ON
                (bpil.`id_feature_price_rule` = bpi.`id_feature_price_rule`)
                WHERE bpil.`id_lang` = '.(int)$idLang;
        if ($id_booking_product_info) {
            $sql .= ' AND bpi.`id_booking_product_info` = '.(int)$id_booking_product_info;
        }

        if ($active == 1 || $active == 0) {
            $sql .= ' AND bpi.`active` = '.(int)$active;
        }
        return Db::getInstance()->executeS($sql);
    }

    public static function getBookingProductFeaturePriceRulesByDateRange(
        $id_booking_product_info,
        $date_from,
        $date_to,
        $active = 'all'
    ) {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing`
        WHERE `id_booking_product_info`='.(int) $id_booking_product_info.'
        AND `date_from` <= \''.pSQL($date_to).'\' AND `date_to` >= \''.pSQL($date_from).'\'';
        if ($active != 'all') {
            $sql .= ' AND `active` = '.(int) $active;
        }
        return Db::getInstance()->executeS($sql);
    }

    public function checkBookingProductFeaturePriceExistance(
        $id_booking_product_info,
        $date_from,
        $date_to,
        $type = 'date_range',
        $current_Special_days = false,
        $id_feature_price_rule = 0
    ) {
        if ($type == 'specific_date') {
            return Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing`
                WHERE `id_booking_product_info` = '.(int) $id_booking_product_info.'
                AND `date_selection_type` = '.self::WK_DATE_SELECTION_SPECIFIC_DATE.'
                AND `date_from` = \''.pSQL($date_from).'\'
                AND `id_feature_price_rule` != '.(int) $id_feature_price_rule
            );
        } elseif ($type == 'special_day') {
            $featurePrice = Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing`
                WHERE `id_booking_product_info` = '.(int) $id_booking_product_info.'
                AND `date_selection_type` = '.self::WK_DATE_SELECTION_DATE_RANGE.'
                AND `is_special_days_exists` = 1
                AND `date_from` < \''.pSQL($date_to).'\'
                AND `date_to` > \''.pSQL($date_from).'\'
                AND `id_feature_price_rule` != '.(int) $id_feature_price_rule
            );
            if ($featurePrice) {
                $specialDays = json_decode($featurePrice['special_days']);
                $currentSpecialDays = json_decode($current_Special_days);
                $commonValues = array_intersect($specialDays, $currentSpecialDays);
                if ($commonValues) {
                    return $featurePrice;
                }
            }
            return false;
        } elseif ($type == 'date_range') {
            return Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing`
                WHERE `id_booking_product_info` = '.(int) $id_booking_product_info.'
                AND `date_selection_type` = '.self::WK_DATE_SELECTION_DATE_RANGE.'
                AND `is_special_days_exists` = 0
                AND `date_from` <= \''.pSQL($date_to).'\'
                AND `date_to` >= \''.pSQL($date_from).'\'
                AND `id_feature_price_rule` != '.(int) $id_feature_price_rule
            );
        }
    }

    public static function countFeaturePriceSpecialDays($specialDays, $date_from, $date_to)
    {
        $totalDaySeconds = 24 * 60 * 60;
        $specialDaysCount = 0;

        for ($date = strtotime($date_from); $date <= strtotime($date_to) - $totalDaySeconds; $date = ($date + $totalDaySeconds)) {
            if (in_array(Tools::strtolower(Date('D', $date)), $specialDays)) {
                ++$specialDaysCount;
            }
        }
        return $specialDaysCount;
    }

    public static function getBookingProductTotalPrice(
        $id_booking_product_info,
        $date_from,
        $date_to,
        $product_price = false,
        $id_currency = null
    ) {
        $totalPrice = array();
        $totalPrice['total_price_tax_incl'] = 0;
        $totalPrice['total_price_tax_excl'] = 0;
        if (Validate::isLoadedObject(
            $objBookingProductInfo = new WkMpBookingProductInformation($id_booking_product_info)
        )) {
            // set currency
            if (Validate::isLoadedObject(new Currency($id_currency))) {
                $id_currency = (int)$id_currency;
            } else {
                $id_currency = (int)Context::getContext()->currency->id;
            }
            // get tax rate on product
            $taxRate = WkMpBookingProductInformation::getAppliedProductTaxRate(
                $objBookingProductInfo->id_product,
                $objBookingProductInfo->id_mp_product
            );
            // get product price (tax incluce and exclude both)
            if ($product_price === false) {
                if ($idProduct = $objBookingProductInfo->id_product) {
                    $productPriceTI = Product::getPriceStatic((int) $idProduct, true);
                    $productPriceTE = Product::getPriceStatic((int) $idProduct, false);
                } elseif ($idMpProduct = $objBookingProductInfo->id_mp_product) {
                    if (Validate::isLoadedObject($objMpProduct = new WkMpSellerProduct($idMpProduct))) {
                        $productPriceTE = $objMpProduct->price;
                        $productPriceTI = (float) $productPriceTE + ((float) $productPriceTE * $taxRate) / 100;
                    }
                }
            } else {
                $productPriceTI = Tools::convertPrice($product_price['price_tax_incl'], $id_currency);
                $productPriceTE = Tools::convertPrice($product_price['price_tax_excl'], $id_currency);
            }

            $totalDaySeconds = 24 * 60 * 60;
            if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                $lastDateSeconds = 0;
            } else {
                if (strtotime($date_from) == strtotime($date_to)) {
                    $date_to = date('Y-m-d', (strtotime($date_from) + $totalDaySeconds));
                }
                $lastDateSeconds = $totalDaySeconds;
            }
            $featureImpactPriceTE = 0;
            $featureImpactPriceTI = 0;

            for ($date = strtotime($date_from); $date <= (strtotime($date_to) - $lastDateSeconds); $date = ($date + $totalDaySeconds)) {
                $currentDate = date('Y-m-d', $date);
                if ($featurePrice = self::getBookingProductFeaturePricePlanByDateByPriority(
                    $id_booking_product_info,
                    $currentDate
                )) {
                    if ($featurePrice['impact_type'] == 1) {
                        //percentage
                        $featureImpactPriceTE = $productPriceTE * ($featurePrice['impact_value'] / 100);
                        $featureImpactPriceTI = $productPriceTI * ($featurePrice['impact_value'] / 100);
                    } else {
                        //Fixed Price
                        $taxPrice = ($featurePrice['impact_value'] * $taxRate) / 100;
                        $featureImpactPriceTE = Tools::convertPrice($featurePrice['impact_value'], $id_currency);
                        $featureImpactPriceTI = Tools::convertPrice(
                            $featurePrice['impact_value'] + $taxPrice, $id_currency
                        );
                    }
                    if ($featurePrice['impact_way'] == 1) {
                        // Decrease
                        $priceWithFeatureTE = ($productPriceTE - $featureImpactPriceTE);
                        $priceWithFeatureTI = ($productPriceTI - $featureImpactPriceTI);
                    } else {
                        // Increase
                        $priceWithFeatureTE = ($productPriceTE + $featureImpactPriceTE);
                        $priceWithFeatureTI = ($productPriceTI + $featureImpactPriceTI);
                    }
                    if ($priceWithFeatureTI < 0) {
                        $priceWithFeatureTI = 0;
                        $priceWithFeatureTE = 0;
                    }
                    $totalPrice['total_price_tax_incl'] += $priceWithFeatureTI;
                    $totalPrice['total_price_tax_excl'] += $priceWithFeatureTE;
                } else {
                    $totalPrice['total_price_tax_incl'] += $productPriceTI;
                    $totalPrice['total_price_tax_excl'] += $productPriceTE;
                }
            }
        }
        return $totalPrice;
    }

    //priority wise feature price plan on a perticular date
    public static function getBookingProductFeaturePricePlanByDateByPriority($id_booking_product_info, $date)
    {
        //Get priority
        $featurePricePriority = Configuration::get('WK_MP_PRODUCT_FEATURE_PRICING_PRIORITY');
        $featurePricePriority = explode(';', $featurePricePriority);
        if ($featurePricePriority) {
            foreach ($featurePricePriority as $priority) {
                if ($priority == 'specific_date') {
                    $featurePrice = Db::getInstance()->getRow(
                        'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing`
                        WHERE `id_booking_product_info` = '.(int) $id_booking_product_info.'
                        AND `active` = 1
                        AND `date_selection_type` = '.self::WK_DATE_SELECTION_SPECIFIC_DATE.'
                        AND `date_from` = \''.pSQL($date).'\''
                    );
                    if ($featurePrice) {
                        return $featurePrice;
                    }
                } elseif ($priority == 'special_day') {
                    $featurePrice = Db::getInstance()->getRow(
                        'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing`
                        WHERE `id_booking_product_info` = '.(int) $id_booking_product_info.'
                        AND `date_selection_type` = '.self::WK_DATE_SELECTION_DATE_RANGE.'
                        AND `is_special_days_exists` = 1
                        AND `active` = 1
                        AND `date_from` <= \''.pSQL($date).'\'
                        AND `date_to` >= \''.pSQL($date).'\''
                    );
                    if ($featurePrice) {
                        $specialDays = json_decode($featurePrice['special_days']);
                        if (in_array(Tools::strtolower(date('D', strtotime($date))), $specialDays)) {
                            return $featurePrice;
                        }
                    }
                } elseif ($priority == 'date_range') {
                    $featurePrice = Db::getInstance()->getRow(
                        'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing`
                        WHERE `id_booking_product_info` = '.(int) $id_booking_product_info.'
                        AND `date_selection_type` = '.self::WK_DATE_SELECTION_DATE_RANGE.'
                        AND `is_special_days_exists` = 0
                        AND `active` = 1
                        AND `date_from` <= \''.pSQL($date).'\'
                        AND `date_to` >= \''.pSQL($date).'\''
                    );
                    if ($featurePrice) {
                        return $featurePrice;
                    }
                }
            }
        }
        return false;
    }

    // $active=2 for all rules $active=1 for active rules and $active=0 for deactive rules
    public function getBookingPriceRules($id_seller = 'all', $id_lang = 0, $active = 'all')
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }
        $sql = 'SELECT bfp.*, bfpl.`feature_price_name`, bpi.`id_mp_product`, bpi.`id_product` FROM
                `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing` bfp
                INNER JOIN `'._DB_PREFIX_.'wk_mp_booking_product_info` bpi
                ON (bpi.`id_booking_product_info` = bfp.`id_booking_product_info`)
                LEFT JOIN `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing_lang` bfpl
                ON (bfp.id_feature_price_rule = bfpl.id_feature_price_rule)
                WHERE bfpl.`id_lang` = '.(int) $id_lang;

        if ($id_seller != 'all') {
            $sql .= ' AND bpi.`id_seller` = '.(int) $id_seller;
        }
        if ($active != 'all') {
            $sql .= ' AND bfp.`active` = '.(int) $active;
        }
        return Db::getInstance()->executeS($sql);
    }
}
