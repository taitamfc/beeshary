<?php
/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2015 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class LeoProcessData
{
    public static function getManufacturersSelect($params)
    {
        $id_lang = Context::getContext()->language->id;
        //fix for previos version
        if ($params['order_by'] == 'position') {
            $params['order_by'] = 'id_manufacturer';
        }
        if (isset($params['order_way']) && $params['order_way'] == 'random') {
            $order = ' RAND()';
        } else {
            $order = (isset($params['order_by']) ? ' '.pSQL($params['order_by']) : '').(isset($params['order_way']) ? ' '.pSQL($params['order_way']) : '');
        }
        $sql = 'SELECT m.*, ml.`description`, ml.`short_description`
			FROM `'._DB_PREFIX_.'manufacturer` m
			'.Shop::addSqlAssociation('manufacturer', 'm').'
			INNER JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.(int)$id_lang.')
			WHERE m.`active` = 1 '.(isset($params['manuselect']) ? 'AND m.`id_manufacturer` IN ('.pSQL($params['manuselect']).')' : '').'
			ORDER BY '.$order;
        $manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($manufacturers === false) {
            return false;
        }
        $total_manufacturers = count($manufacturers);
        $rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS');
        for ($i = 0; $i < $total_manufacturers; $i++)
            $manufacturers[$i]['link_rewrite'] = ($rewrite_settings ? Tools::link_rewrite($manufacturers[$i]['name']) : 0);
        return $manufacturers;
    }
}
