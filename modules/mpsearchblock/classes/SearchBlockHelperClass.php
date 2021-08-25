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

class SearchBlockHelperClass
{
    /**
     * Notice: output is associative array have some indexes which are not used in search suggestions drop down
     * but they are used in form search results
     * [getMpProductDetail : get array of product if product name contain search key].
     *
     * @param string|char|int $key  : search key
     * @param int             $sort : [output array in ascending or descending order according to product price or
     * product name]
     *
     * @return array | false  $product_result : Associative array
     */
    public function getMpProductDetail($key, $sort = 0, $ajax = 0)
    {
        $id_lang = Context::getContext()->language->id;
        $order_by = false;
        $order_way = false;
        if ($sort) {
            if ($sort == 1) {
                $order_by = 'p.price';
                $order_way = 'ASC';
            } elseif ($sort == 2) {
                $order_by = 'p.price';
                $order_way = 'DESC';
            } elseif ($sort == 3) {
                $order_by = 'pl.name';
                $order_way = 'ASC';
            } elseif ($sort == 4) {
                $order_by = 'pl.name';
                $order_way = 'DESC';
            }
        }

        $product_result = Search::find(
            $id_lang,
            $key,
            1,
            10000,
            $order_by ? $order_by : 'position',
            $order_way ? $order_way : 'ASC',
            $ajax
        );
        if ($ajax) {
            if ($product_result) {
                foreach ($product_result as $prod_key => $prod_val) {
                    $product_result[$prod_key]['link'] = Context::getContext()->link->getProductLink(
                        $prod_val['id_product'],
                        $prod_val['prewrite'],
                        null,
                        null,
                        (int) $id_lang
                    );

                    $product_result[$prod_key]['name'] = $prod_val['pname'];
                }

                return $product_result;
            }
        } else {
            if ((int)$product_result['total']) {
                return $product_result['result'];
            }
        }

        return false;
    }

    /**
     * Notice: output is associative array have some indexes which are not used in search suggestions drop down
     * but they are used in form search results
     * [getMpShopDetail : get array of shop if shop name contain search key].
     *
     * @param [string|char|int] $key  [search value(key)]
     * @param int               $sort [output array in ascending or descending order according to shop name]
     *
     * @return [array | false (if search key is not present in any shop name)]
     */
    public function getMpShopDetail($key, $sort = 0)
    {
        $id_lang = Context::getContext()->language->id;
        $sql = 'SELECT DISTINCT msi.`shop_name_unique`,
                    msi.`link_rewrite` AS mp_shop_rewrite,
                    mpsil.`shop_name` AS mp_shop_name,
                    CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`) AS mp_seller_name,
                    msi.`id_seller` AS mp_id_shop,
                    mpsil.`about_shop` AS mp_shop_desc,
                    msi.`id_seller` AS mp_id_seller,
                    msi.`shop_image` AS shop_image
                FROM '._DB_PREFIX_.'wk_mp_seller AS msi
                INNER JOIN '._DB_PREFIX_.'wk_mp_seller_lang AS mpsil ON (
                    mpsil.`id_seller` = msi.`id_seller` AND mpsil.`id_lang` = '.$id_lang."
                )
                WHERE msi.`active` = 1 AND mpsil.`shop_name` LIKE '%".$key."%' ";

        if ($sort) {
            if ($sort == 3) {
                $sql .= 'ORDER BY mpsil.`shop_name`';
            } elseif ($sort == 4) {
                $sql .= 'ORDER BY mpsil.`shop_name` DESC';
            }
        }

        $shop_result = Db::getInstance()->executeS($sql);
        if ($shop_result) {
            foreach ($shop_result as $shop_key => $shop_val) {
                $shop_result[$shop_key]['link'] = Context::getContext()->link->getModuleLink(
                    'marketplace',
                    'shopstore',
                    array('mp_shop_name' => $shop_val['mp_shop_rewrite'])
                );
            }

            return $shop_result;
        }

        return false;
    }

    /**
     * Notice: output is associative array have some indexes which are not used in search suggestions drop down
     * but they are used in form search results
     * [getMpSellerDetail get array of shops if seller name contain search key].
     *
     * @param [string|char] $key  [search value(key)]
     * @param int           $sort [output array in ascending or descending order according to seller name]
     *
     * @return [array | false (if search key is not present in any seller name)]
     */
    public function getMpSellerDetail($key, $sort = 0)
    {
        $id_lang = Context::getContext()->language->id;
        $sql = 'SELECT DISTINCT msi.`link_rewrite` AS mp_shop_rewrite,
                    msi.`shop_name_unique` AS mp_shop_name,
                    CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`) AS mp_seller_name,
                    msi.`id_seller` AS mp_id_shop,
                    msil.`about_shop` AS mp_shop_desc,
                    msi.`id_seller` AS mp_id_seller,
                    msi.`profile_image` AS profile_image
                FROM '._DB_PREFIX_.'wk_mp_seller AS msi
                INNER JOIN '._DB_PREFIX_.'wk_mp_seller_lang AS msil ON (
                    msil.`id_seller` = msi.`id_seller` AND msil.`id_lang` = '.$id_lang.'
                )
                WHERE msi.`active` = 1
                AND CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`) LIKE "%'.$key.'%"';

        if ($sort) {
            if ($sort == 3) {
                $sql .= 'ORDER BY msi.`seller_firstname`';
            } elseif ($sort == 4) {
                $sql .= 'ORDER BY msi.`seller_firstname` DESC';
            }
        }

        $seller_result = Db::getInstance()->executeS($sql);
        if ($seller_result) {
            foreach ($seller_result as $sell_key => $sell_val) {
                $seller_result[$sell_key]['link'] = Context::getContext()->link->getModuleLink(
                    'marketplace',
                    'sellerprofile',
                    array('mp_shop_name' => $sell_val['mp_shop_rewrite'])
                );
            }

            return $seller_result;
        } else {
            return false;
        }
    }
    public function getMpSellerDetailByProfession($key, $sort = 0)
    {
        $id_lang = Context::getContext()->language->id;
        $sql = 'SELECT DISTINCT msi.`link_rewrite` AS mp_shop_rewrite,
                    msi.`shop_name_unique` AS mp_shop_name,
                    CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`) AS mp_seller_name,
                    msi.`id_seller` AS mp_id_shop,
                    msi.`id_seller` AS mp_id_seller,
                    msi.`profile_image` AS profile_image, 
                    msil.`field_value` AS field_value
                FROM '._DB_PREFIX_.'wk_mp_seller AS msi
                INNER JOIN '._DB_PREFIX_.'marketplace_extrafield_value AS msil ON (
                    msil.`mp_id_seller` = msi.`id_seller`
                )
                WHERE msil.`field_value`  LIKE "%'.$key.'%" GROUP BY msil.`mp_id_seller` ';

        if ($sort) {
            if ($sort == 3) {
                $sql .= 'ORDER BY msil.`field_value`';
            } elseif ($sort == 4) {
                $sql .= 'ORDER BY msil.`field_value` DESC';
            }
        }

        $seller_result = Db::getInstance()->executeS($sql);
        if ($seller_result) {
            foreach ($seller_result as $sell_key => $sell_val) {
                $seller_result[$sell_key]['link'] = Context::getContext()->link->getModuleLink(
                    'marketplace',
                    'sellerprofile',
                    array('mp_shop_name' => $sell_val['mp_shop_rewrite'])
                );
            }

            return $seller_result;
        } else {
            return false;
        }
    }

    /**
     * Notice: output is associative array have some indexes which are not used in search suggestions drop down
     * but they are used in form search results
     * [getMpShopLocationDetail : get array of shop if shop location contain search key].
     *
     * @param [string|char|int] $key  [search value(key)]
     * @param int               $sort [output array in ascending or descending order for shop name]
     *
     * @return [array | false (if search key is not present in any shop location)]
     */
    public function getMpShopLocationDetail($key, $sort = 0)
    {
        $id_lang = Context::getContext()->language->id;
        $sql = 'SELECT DISTINCT msil.`shop_name` AS mp_shop_name,
                    CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`) AS mp_seller_name,
                    msi.`id_seller` AS mp_id_shop,
                    msil.`about_shop` AS mp_shop_desc,
                    msi.`id_seller` AS mp_id_seller,
                    msi.`link_rewrite` AS mp_shop_rewrite,
                    msi.`shop_image` AS shop_image
                FROM '._DB_PREFIX_.'wk_mp_seller AS msi
                INNER JOIN '._DB_PREFIX_.'wk_mp_seller_lang AS msil ON (
                    msil.`id_seller` = msi.`id_seller` AND msil.`id_lang` = '.$id_lang.'
                )
                LEFT JOIN '._DB_PREFIX_.'country_lang AS cl ON (
                    cl.`id_country` = msi.`id_country` AND cl.`id_lang` = '.(int)$id_lang.'
                )
                LEFT JOIN '._DB_PREFIX_.'state AS s ON (s.`id_state` = msi.`id_state`)
                WHERE msi.`active` = 1
                AND (
                    msi.`address` LIKE "%'.$key.'%"
                    OR cl.`name` LIKE "%'.$key.'%"
                    OR s.`name`  LIKE "%'.$key.'%"
                    OR msi.`city` LIKE "%'.$key.'%"
                )';

        if ($sort) {
            if ($sort == 3) {
                $sql .= ' ORDER BY msil.`shop_name`';
            } elseif ($sort == 4) {
                $sql .= ' ORDER BY msil.`shop_name` DESC';
            }
        }

        $address_result = Db::getInstance()->executeS($sql);
        if ($address_result) {
            foreach ($address_result as $adr_key => $adr_val) {
                $address_result[$adr_key]['link'] = Context::getContext()->link->getModuleLink(
                    'marketplace',
                    'shopstore',
                    array('mp_shop_name' => $adr_val['mp_shop_rewrite'])
                );
            }

            return $address_result;
        } else {
            return false;
        }
    }

    /**
     * Notice: output is associative array have some indexes which are not used in search suggestions drop down
     * but they are used in form search results
     * [getPsCategoryDetail : get array of categories if category contain search key].
     *
     * @param [string|char|int] $key  [search value(key)]
     * @param int               $sort [output array in ascending or descending order for category name]
     *
     * @return [array | false (if search key is not present in any category)]
     */
    public function getPsCategoryDetail($key, $sort = 0)
    {
        $id_lang = Context::getContext()->language->id;
        $sql = 'SELECT cl.`name` AS name,
                cl.`id_category` AS id_category,
                cl.`description` AS description,
                cl.`link_rewrite` AS link_rewrite
                FROM '._DB_PREFIX_.'category_lang AS cl
                INNER JOIN '._DB_PREFIX_."category AS c ON (c.`id_category` = cl.`id_category`)
                WHERE c.`active` = 1
                AND cl.`name` LIKE '%".$key."%'
                AND cl.`id_lang` = ".(int) $id_lang.'
                AND c.`level_depth` NOT IN (0, 1)';

        if ($sort) {
            if ($sort == 3) {
                $sql .= 'ORDER BY cl.`name`';
            } elseif ($sort == 4) {
                $sql .= 'ORDER BY cl.`name` DESC';
            }
        }

        $category_result = Db::getInstance()->executeS($sql);
        if ($category_result) {
            foreach ($category_result as $cat_key => $cat_val) {
                $category_result[$cat_key]['id_image'] = Tools::file_exists_cache(
                    _PS_CAT_IMG_DIR_.$cat_val['id_category'].'.jpg'
                ) ? (int) $cat_val['id_category'] : Language::getIsoById($id_lang).'-default';

                $category_result[$cat_key]['link'] = Context::getContext()->link->getCategoryLink(
                    (int) $cat_val['id_category'],
                    null,
                    (int) $id_lang
                );
            }

            return $category_result;
        } else {
            return false;
        }
    }
}
