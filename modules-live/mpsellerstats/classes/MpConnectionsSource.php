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

class MpConnectionsSource extends ObjectModel
{
    public $id_connections;
    public $http_referer;
    public $request_uri;
    public $id_mp_page;
    public $keywords;
    public $date_add;
    public static $uri_max_size = 255;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'mp_connections_source',
        'primary' => 'id_mp_connections_source',
        'fields' => array(
            'id_connections' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_mp_page' =>     array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'http_referer' =>   array('type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'),
            'request_uri' =>    array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'keywords' =>       array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
            'date_add' =>       array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
        ),
    );

    public static function logHttpReferer(Cookie $cookie = null, $idPage)
    {
        if (!$cookie) {
            $cookie = Context::getContext()->cookie;
        }

        if (!isset($cookie->id_connections) || !Validate::isUnsignedId($cookie->id_connections)) {
            return false;
        }

        // If the referrer is not correct, we drop the connection
        if (isset($_SERVER['HTTP_REFERER']) && !Validate::isAbsoluteUrl($_SERVER['HTTP_REFERER'])) {
            return false;
        }

        MpConnectionsSource::addIpAddressDetail($cookie->id_connections);
        $source = new MpConnectionsSource();

        // There are a few more operations if there is a referrer
        if (isset($_SERVER['HTTP_REFERER'])) {
            // If the referrer is internal (i.e. from your own website), then we drop the connection
            $parsed = parse_url($_SERVER['HTTP_REFERER']);
            $parsedHost = parse_url(Tools::getProtocol().Tools::getHttpHost(false, false).__PS_BASE_URI__);

            if (!isset($parsed['host']) || (!isset($parsed['path']) || !isset($parsedHost['path']))) {
                return false;
            }

            $source->http_referer = substr($_SERVER['HTTP_REFERER'], 0, MpConnectionsSource::$uri_max_size);
            $source->keywords = substr(trim(SearchEngine::getKeywords($_SERVER['HTTP_REFERER'])), 0, MpConnectionsSource::$uri_max_size);
        }

        $source->id_connections = (int) $cookie->id_connections;
        $source->request_uri = Tools::getHttpHost(false, false);
        $source->id_mp_page = $idPage;

        if (isset($_SERVER['REQUEST_URI'])) {
            $source->request_uri .= $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['REDIRECT_URL'])) {
            $source->request_uri .= $_SERVER['REDIRECT_URL'];
        }

        if (!Validate::isUrl($source->request_uri)) {
            $source->request_uri = '';
        }
        $source->request_uri = substr($source->request_uri, 0, MpConnectionsSource::$uri_max_size);

        return $source->add();
    }

    public static function addIpAddressDetail($idConnection)
    {
        if ($value = Db::getInstance()->getValue('
                SELECT `ip_address`
                FROM '._DB_PREFIX_.'mp_connections_ipaddress
                WHERE ip_address = '.ip2long(Tools::getRemoteAddr())
                )
            ) {
            return $value;
        }

        if (@filemtime(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_)) {
            $reader = new GeoIp2\Database\Reader(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_);
            try {
                $record = $reader->city(Tools::getRemoteAddr());
            } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
                $record = null;
            }

            if (is_object($record) && Validate::isLanguageIsoCode($record->country->isoCode)) {
                Db::getInstance()->insert(
                    'mp_connections_ipaddress',
                    array(
                        'ip_address' => ip2long(Tools::getRemoteAddr()),
                        'iso_country' => strtoupper($record->country->isoCode),
                        'city' => $record->city->names['en']
                    )
                );
            } else {
                Db::getInstance()->insert(
                    'mp_connections_ipaddress',
                    array(
                        'ip_address' => ip2long(Tools::getRemoteAddr()),
                        'iso_country' => 'NA',
                        'city' => 'NA'
                    )
                );
            }
        }

        return true;
    }

    public static function getTotalVisitor($dateBetween, $pageName = 'shopstore', $idObject = null, $idseller = false)
    {
        $sql = 'SELECT LEFT(mpcs.`date_add`, 10) AS date_add, COUNT(DISTINCT c.`id_guest`) AS total
        FROM `'._DB_PREFIX_.'mp_connections_source` mpcs
        LEFT JOIN `'._DB_PREFIX_.'connections` c ON mpcs.`id_connections` = c.`id_connections`
        LEFT JOIN `'._DB_PREFIX_.'mp_page` mpa ON mpcs.`id_mp_page` = mpa.`id_mp_page`
        LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = mpa.`id_page_type`';

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' JOIN `'._DB_PREFIX_.'wk_mp_seller_product` mpsp ON mpa.`id_object` = mpsp.`id_ps_product`';
        }

        $sql .= 'WHERE pt.`name` = "'.pSQL($pageName).'"
                AND mpcs.`date_add` BETWEEN '.$dateBetween;

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' AND mpsp.`id_seller` = '.(int)$idseller.' GROUP BY LEFT(mpcs.`date_add`, 10)';
        } else {
            $sql .= ' AND mpa.`id_object` = '.(int)$idObject.' GROUP BY LEFT(mpcs.`date_add`, 10)';
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

    public static function getSocialData($dateBetween, $pageName = 'shopstore', $idObject = null, $idseller = false)
    {
        $sql = 'SELECT mpcs.*
        FROM `'._DB_PREFIX_.'mp_connections_source` mpcs
        LEFT JOIN `'._DB_PREFIX_.'mp_page` mpa ON mpcs.`id_mp_page` = mpa.`id_mp_page`
        LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = mpa.`id_page_type`';

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' JOIN `'._DB_PREFIX_.'wk_mp_seller_product` mpsp ON mpa.`id_object` = mpsp.`id_ps_product`';
        }

        $sql .= 'WHERE pt.`name` = "'.pSQL($pageName).'"
                AND mpcs.`date_add` BETWEEN '.$dateBetween;

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' AND mpsp.`id_seller` = '.(int)$idseller;
        } else {
            $sql .= ' AND mpa.`id_object` = '.(int)$idObject;
        }

        $result = Db::getInstance()->executeS($sql);
        $objMpStats = new MpSellerStats();
        $socialArray = array(
            'bing' => array('name' => $objMpStats->l('Bing'), 'count' => 0),
            'direct' => array('name' => $objMpStats->l(' Direct access'), 'count' => 0),
            'external' => array('name' => $objMpStats->l('External Link'), 'count' => 0),
            'facebook' => array('name' => $objMpStats->l('Facebook'), 'count' => 0),
            'google' => array('name' => $objMpStats->l('Google'), 'count' => 0),
            'instagram' => array('name' => $objMpStats->l('Instagram'), 'count' => 0),
            'marketplace' => array('name' => $objMpStats->l('Marketplace'), 'count' => 0),
            'pinterest' => array('name' => $objMpStats->l('Pinterest'), 'count' => 0),
            'twitter' => array('name' => $objMpStats->l('Twitter'), 'count' => 0),
        );

        foreach ($result as $data) {
            if (!$data['http_referer']) {
                $socialArray['direct']['count']++;
            } else {
                $parsed = parse_url($data['http_referer']);
                $parsedHost = parse_url(Tools::getProtocol().Tools::getHttpHost(false, false).__PS_BASE_URI__);
                if ((preg_replace('/^www./', '', $parsed['host']) == preg_replace('/^www./', '', Tools::getHttpHost(false, false))) && !strncmp($parsed['path'], $parsedHost['path'], strlen(__PS_BASE_URI__))) {
                    $socialArray['marketplace']['count']++;
                } elseif (strpos($data['http_referer'], 'google') !== false) {
                    $socialArray['google']['count']++;
                } elseif (strpos($data['http_referer'], 'bing') !== false) {
                    $socialArray['bing']['count']++;
                } elseif (strpos($data['http_referer'], 'facebook') !== false) {
                    $socialArray['facebook']['count']++;
                } elseif (strpos($data['http_referer'], 'twitter') !== false) {
                    $socialArray['twitter']['count']++;
                } elseif (strpos($data['http_referer'], 'instagram') !== false) {
                    $socialArray['instagram']['count']++;
                } elseif (strpos($data['http_referer'], 'pinterest') !== false) {
                    $socialArray['pinterest']['count']++;
                } else {
                    $socialArray['external']['count']++;
                }
            }
        }

        return $socialArray;
    }

    public static function getDemographicDataCountryWise($dateBetween, $pageName = 'shopstore', $idObject = null, $idseller = false)
    {
        $sql = 'SELECT mcip.`iso_country`, COUNT(mcip.`iso_country`) AS count
        FROM `'._DB_PREFIX_.'mp_connections_source` mpcs
        LEFT JOIN `'._DB_PREFIX_.'connections` c ON mpcs.`id_connections` = c.`id_connections`
        LEFT JOIN `'._DB_PREFIX_.'mp_connections_ipaddress` mcip ON mcip.`ip_address` = c.`ip_address`
        LEFT JOIN `'._DB_PREFIX_.'mp_page` mpa ON mpcs.`id_mp_page` = mpa.`id_mp_page`
        LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = mpa.`id_page_type`';

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' JOIN `'._DB_PREFIX_.'wk_mp_seller_product` mpsp ON mpa.`id_object` = mpsp.`id_ps_product`';
        }

        $sql .= 'WHERE pt.`name` = "'.pSQL($pageName).'"
                AND mpcs.`date_add` BETWEEN '.$dateBetween;

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' AND mpsp.`id_seller` = '.(int)$idseller.' GROUP BY mcip.`iso_country` ORDER BY mcip.`iso_country`';
        } else {
            $sql .= ' AND mpa.`id_object` = '.(int)$idObject.' GROUP BY mcip.`iso_country` ORDER BY mcip.`iso_country`';
        }

        $result = Db::getInstance()->executeS($sql);

        if ($result) {
            $context = Context::getContext();
            foreach ($result as &$data) {
                if ($data['iso_country']) {
                    $idCountry = Country::getByIso($data['iso_country']);
                    if ($idCountry) {
                        $data['name'] = Country::getNameById($context->language->id, $idCountry);
                    } else {
                        $data['name'] = 'Anonyme';
                    }
                }
            }
        }

        return $result;
    }

    public static function getDemographicDataCityWise($dateBetween, $pageName = 'shopstore', $idObject = null, $idseller = false)
    {
        $sql = 'SELECT mcip.`city` AS name, COUNT(mcip.`city`) AS count
        FROM `'._DB_PREFIX_.'mp_connections_source` mpcs
        LEFT JOIN `'._DB_PREFIX_.'connections` c ON mpcs.`id_connections` = c.`id_connections`
        LEFT JOIN `'._DB_PREFIX_.'mp_connections_ipaddress` mcip ON mcip.`ip_address` = c.`ip_address`
        LEFT JOIN `'._DB_PREFIX_.'mp_page` mpa ON mpcs.`id_mp_page` = mpa.`id_mp_page`
        LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = mpa.`id_page_type`';

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' JOIN `'._DB_PREFIX_.'wk_mp_seller_product` mpsp ON mpa.`id_object` = mpsp.`id_ps_product`';
        }

        $sql .= 'WHERE pt.`name` = "'.pSQL($pageName).'"
                AND mpcs.`date_add` BETWEEN '.$dateBetween;

        if ($pageName == 'product' && !$idObject) {
            $sql .= ' AND mpsp.`id_seller` = '.(int)$idseller.' GROUP BY mcip.`city` ORDER BY mcip.`city`';
        } else {
            $sql .= ' AND mpa.`id_object` = '.(int)$idObject.' GROUP BY mcip.`city` ORDER BY mcip.`city`';
        }

        $result = Db::getInstance()->executeS($sql);

        return $result;
    }
}
