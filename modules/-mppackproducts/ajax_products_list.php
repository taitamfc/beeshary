<?php
/**
* 2010-2016 Webkul.
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

include_once '../../config/config.inc.php';
include_once dirname(__FILE__).'/classes/WkMpPackProduct.php';
/* Getting cookie or logout */

$query = Tools::getValue('prod_letter', false);
if (!$query or $query == '' or Tools::strlen($query) < 1) {
    die();
}

if ($pos = strpos($query, ' (ref:')) {
    $query = Tools::substr($query, 0, $pos);
}

$excludeIds = Tools::getValue('excludeIds', false);

if ($excludeIds && $excludeIds != 'NaN') {
    $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
} else {
    $excludeIds = '';
}

// Excluding downloadable products from packs because download from pack is not supported
$excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', true);
$exclude_packs = (bool)Tools::getValue('exclude_packs', true);
$current_lang_id = Tools::getValue('current_lang_id');
$seller_cust_id = (int) Tools::getValue('seller_cust_id');

if (isset($seller_cust_id) && $seller_cust_id) {
    include_once dirname(__FILE__).'/../marketplace/classes/WkMpSeller.php';
    $seller_info = WkMpSeller::getSellerDetailByCustomerId($seller_cust_id);
    $id_seller = (int) $seller_info['id_seller'];
} else {
    $id_seller = (int) Tools::getValue('seller_id');
}

$context = Context::getContext();

$prev_id_prod = array();
if (Tools::getValue('prev_id')) {
    $prev_id_prod = implode(",", Tools::jsonDecode(Tools::getValue('prev_id'), true));
}

if (version_compare(_PS_VERSION_, '1.6.0.14', '>')) {
    $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, p.`cache_default_attribute`
            FROM `'._DB_PREFIX_.'product` p
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$current_lang_id.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
                ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.') 
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product` msp ON (msp.`id_ps_product` = p.`id_product`)
            WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\')'.(!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ' ').($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'product_download` pd WHERE (pd.id_product = p.id_product))' : '').
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '').' And msp.id_seller='.$id_seller.' GROUP BY p.id_product';
} else {
    $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, MAX(image_shop.`id_image`) id_image, il.`legend`, p.`cache_default_attribute`
            FROM `'._DB_PREFIX_.'product` p
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
            Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)Context::getContext()->language->id.')
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product` msp ON (msp.`id_ps_product` = p.`id_product`)
            WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\')'.
            (!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ' ').
            ($excludeVirtuals ? 'AND p.id_product NOT IN (SELECT pd.id_product FROM `'._DB_PREFIX_.'product_download` pd WHERE (pd.id_product = p.id_product))' : '').
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '').' And msp.id_seller='.$id_seller.
            ' GROUP BY p.id_product';
}

$items = Db::getInstance()->executeS($sql);

if ($items && ($excludeIds || strpos($_SERVER['HTTP_REFERER'], 'AdminScenes') !== false)) {
    foreach ($items as $item) {
        echo trim($item['name']).(!empty($item['reference']) ? ' (ref: '.$item['reference'].')' : '').'|'.(int)($item['id_product'])."\n";
    }
} elseif ($items) {
    // packs
    $results = array();
    foreach ($items as $item) {
        // check if product have combination
        if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
            $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, pai.`id_image`, agl.`name` AS group_name, al.`name` AS attribute_name,
						a.`id_attribute`
					FROM `'._DB_PREFIX_.'product_attribute` pa
					'.Shop::addSqlAssociation('product_attribute', 'pa').'
					LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
					LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
					LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
					LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$current_lang_id.')
					LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$current_lang_id.')
					LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
					WHERE pa.`id_product` = '.(int)$item['id_product'].'
					GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
					ORDER BY pa.`id_product_attribute`';

            $combinations = Db::getInstance()->executeS($sql);
            if (!empty($combinations)) {
                foreach ($combinations as $k => $combination) {
                    $obj_mppack = new WkMpPackProduct();
                    $mp_id_prod_attr = $obj_mppack->getMpAttributeIdByPsAttributeId($combination['id_product_attribute']);
                    if ($mp_id_prod_attr) {
                        $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                        $results[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                        !empty($results[$combination['id_product_attribute']]['name']) ? $results[$combination['id_product_attribute']]['name'] .= ' '.$combination['group_name'].'-'.$combination['attribute_name']
                        : $results[$combination['id_product_attribute']]['name'] = $item['name'].' '.$combination['group_name'].'-'.$combination['attribute_name'];
                        if (!empty($combination['reference'])) {
                            $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                        } else {
                            $results[$combination['id_product_attribute']]['ref'] = !empty($item['reference']) ? $item['reference'] : '';
                        }
                        if (empty($results[$combination['id_product_attribute']]['image'])) {
                            $results[$combination['id_product_attribute']]['image'] = str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $combination['id_image'], 'home_default'));
                        }
                    }
                }
            } else {
                $product = array(
                    'id' => (int)($item['id_product']),
                    'name' => $item['name'],
                    'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                    'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
                );
                array_push($results, $product);
            }
        } else {
            $product = array(
                'id' => (int)($item['id_product']),
                'name' => $item['name'],
                'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
            );
            array_push($results, $product);
        }
    }
    $results = array_values($results);
    echo Tools::jsonEncode($results);
} else {
    Tools::jsonEncode(new stdClass);
}
