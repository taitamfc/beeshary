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

class MpPage extends ObjectModel
{
    public $id_page_type;
    public $id_object;

    public $name;

    public static $definition = array(
        'table' => 'mp_page',
        'primary' => 'id_mp_page',
        'fields' => array(
            'id_page_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_object' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    /**
     * @return int Current page ID
     */
    public static function getCurrentId()
    {
        $controller = Dispatcher::getInstance()->getController();
        if ('shopstore' == $controller || 'product' == $controller) {
            $pageTypeId = Page::getPageTypeByName($controller);

            // Some pages must be distinguished in order to record exactly what is being seen
            // @todo dispatcher module
            $specialArray = array(
                'product' => 'id_product',
                'shopstore' => 'mp_shop_name',
            );

            $where = '';
            $insertData = array(
                'id_page_type' => $pageTypeId,
            );

            if (array_key_exists($controller, $specialArray)) {
                if ('shopstore' == $controller) {
                    $context = Context::getContext();
                    $mpSeller = WkMpSeller::getSellerByLinkRewrite(Tools::getValue($specialArray[$controller], null), $context->language->id);
                    $objectId = 0;
                    if ($mpSeller) {
                        $objectId = $mpSeller['id_seller'];
                    }
                } else {
                    $objectId = Tools::getValue($specialArray[$controller], null);
                }
                $where = ' AND `id_object` = '.(int) $objectId;
                $insertData['id_object'] = (int) $objectId;
            }

            $sql = 'SELECT `id_mp_page`
                    FROM `'._DB_PREFIX_.'mp_page`
                    WHERE `id_page_type` = '.(int) $pageTypeId.$where;
            $result = Db::getInstance()->getRow($sql);
            if ($result['id_mp_page']) {
                return $result['id_mp_page'];
            }

            Db::getInstance()->insert('mp_page', $insertData, true);

            return Db::getInstance()->Insert_ID();
        }

        return false;
    }

    /**
     * Increase page viewed number by one
     *
     * @param int $idPage Page ID
     */
    public static function setPageViewed($idPage)
    {
        $idDateRange = DateRange::getCurrentRange();
        $context = Context::getContext();

        // Try to increment the visits counter
        $sql = 'UPDATE `'._DB_PREFIX_.'mp_page_viewed`
                SET `counter` = `counter` + 1
                WHERE `id_date_range` = '.(int) $idDateRange.'
                    AND `id_mp_page` = '.(int) $idPage.'
                    AND `id_shop` = '.(int) $context->shop->id;
        Db::getInstance()->execute($sql);

        // If no one has seen the page in this date range, it is added
        if (Db::getInstance()->Affected_Rows() == 0) {
            Db::getInstance()->insert(
                'mp_page_viewed',
                array(
                    'id_date_range' => (int) $idDateRange,
                    'id_mp_page' => (int) $idPage,
                    'counter' => 1,
                    'id_shop' => (int) $context->shop->id,
                    'id_shop_group' => (int) $context->shop->id_shop_group,
                )
            );
        }
    }

    public static function getTotalVisits($dateBetween, $pageName = 'shopstore', $idObject = null, $idseller = false)
    {
        $sql = 'SELECT LEFT(dr.`time_start`, 10) AS date_add, (SUM(mpv.`counter`)) AS total
        FROM `'._DB_PREFIX_.'mp_page_viewed` mpv
        LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON mpv.`id_date_range` = dr.`id_date_range`
        LEFT JOIN `'._DB_PREFIX_.'mp_page` mpa ON mpv.`id_mp_page` = mpa.`id_mp_page`
        LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = mpa.`id_page_type`';

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' JOIN `'._DB_PREFIX_.'wk_mp_seller_product` mpsp ON mpa.`id_object` = mpsp.`id_ps_product`';
        }

        $sql .= 'WHERE pt.`name` = "'.pSQL($pageName).'"
                '.Shop::addSqlRestriction(false, 'mpv').'
                AND dr.`time_start` BETWEEN '.$dateBetween.'
                AND dr.`time_end` BETWEEN '.$dateBetween;

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' AND mpsp.`id_seller` = '.(int)$idseller.' GROUP BY dr.`time_start`';
        } else {
            $sql .= ' AND mpa.`id_object` = '.(int)$idObject.' GROUP BY dr.`time_start`';
        }

        $result = Db::getInstance()->executeS($sql);
        $newArray = array();
        if ($result) {
            foreach ($result as $data) {
                $newArray[strtotime($data['date_add'])] = $data['total'];
            }
        }


        return $newArray;
    }

    public static function getPreselectedDateRange($rangeIndicator)
    {
        if (!$rangeIndicator) {
            return 0;
        }

        if ($rangeIndicator == 1) {
            $dateFrom = date('Y-m-d');
            $dateTo = date('Y-m-d');
        } elseif ($rangeIndicator == 2) {
            $dateFrom = date('Y-m-01');
            $dateTo = date('Y-m-t');
        } elseif ($rangeIndicator == 3) {
            $dateFrom = date('Y-01-01');
            $dateTo = date('Y-12-31');
        } elseif ($rangeIndicator == 4) {
            $yesterday = time() - 60 * 60 * 24;
            $dateFrom = date('Y-m-d', $yesterday);
            $dateTo = date('Y-m-d', $yesterday);
        } elseif ($rangeIndicator == 5) {
            $m = (date('m') == 1 ? 12 : date('m') - 1);
            $y = ($m == 12 ? date('Y') - 1 : date('Y'));
            $dateFrom = $y.'-'.$m.'-01';
            $dateTo = $y.'-'.$m.date('-t', mktime(12, 0, 0, $m, 15, $y));
        } elseif ($rangeIndicator == 6) {
            $dateFrom = (date('Y') - 1).date('-01-01');
            $dateTo = (date('Y') - 1).date('-12-31');
        }
        return array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        );
    }

    public static function insertSearchKeyword($params, $idSellers)
    {
        if (strlen($params['searched_query']) >= 3) {
            $context = Context::getContext();
            foreach ($idSellers as $idSeller => $count) {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'mp_statssearch` (`id_shop_group`, `id_shop`, `id_seller`,`keywords`, `results`, `date_add`)
                VALUES ('.(int)$context->shop->id_shop_group.', '.(int)$context->shop->id.', '.(int)$idSeller.', \''.pSQL($params['searched_query']).'\', '.(int)$count.', NOW())';
                Db::getInstance()->execute($sql);
            }
        }
    }

    public static function getSearchKeyword($dateBetween, $idSeller)
    {
        $sql = 'SELECT `keywords`, COUNT(TRIM(`keywords`)) as occurences, MAX(results) as total
        FROM `'._DB_PREFIX_.'mp_statssearch`
        WHERE 1
            '.Shop::addSqlRestriction().'
            AND `id_seller` = '.(int)$idSeller.'
            AND `date_add` BETWEEN '.$dateBetween.
            'GROUP BY `keywords`
            HAVING occurences > 1
            ORDER BY occurences DESC';

        return Db::getInstance()->executeS($sql);
    }

    public static function assignMonthNameOnJs()
    {
        $objMpStats = new MpSellerStats();
        $monthName = array(
            $objMpStats->l('January'),
            $objMpStats->l('February'),
            $objMpStats->l('March'),
            $objMpStats->l('April'),
            $objMpStats->l('May'),
            $objMpStats->l('June'),
            $objMpStats->l('July'),
            $objMpStats->l('August'),
            $objMpStats->l('September'),
            $objMpStats->l('October'),
            $objMpStats->l('November'),
            $objMpStats->l('December')
        );

        $daysOfWeek = array (
            $objMpStats->l('Su'),
            $objMpStats->l('Mo'),
            $objMpStats->l('Tu'),
            $objMpStats->l('We'),
            $objMpStats->l('Th'),
            $objMpStats->l('Fr'),
            $objMpStats->l('Sa')
        );

        Media::addJsDef(array(
            'wkMonthName' => $monthName,
            'wkDaysOfWeek' => $daysOfWeek,
        ));
    }
}
