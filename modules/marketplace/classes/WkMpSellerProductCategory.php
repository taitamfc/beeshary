<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpSellerProductCategory extends ObjectModel
{
    public $id_category;
    public $id_seller_product;
    public $is_default; //In marketplace, this field is not using any where but may be it is using in any mp addons

    public $checkCategories;
    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id);

        $this->checkCategories = false;
    }

    public static $definition = array(
        'table' => 'wk_mp_seller_product_category',
        'primary' => 'id_mp_category_product',
        'fields' => array(
            'id_category' => array('type' => self::TYPE_INT),
            'is_default' => array('type' => self::TYPE_INT),
            'id_seller_product' => array('type' => self::TYPE_INT),
        ),
    );

    /**
     * Get Seller's Product Default Category by using Id Product
     *
     * @param  int $mpproductid Seller's Product ID
     * @return int Value of category
     */
    public function getSellerProductDefaultCategory($mpproductid)
    {
        $defaultcategory = Db::getInstance()->getValue('SELECT `id_category` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_mp_product` = '.(int) $mpproductid);
        if ($defaultcategory) {
            return $defaultcategory;
        }

        return false;
    }

    /**
     * Get prestashop jstree category
     *
     * @param  int $catId node id of jstree category
     * @param  int $selectedCatIds selected category in jstree
     * @param  int $idLang content display of selected language
     * @return category load
     */
    public function getProductCategory($catId, $selectedCatIds, $idLang)
    {
        if ($catId == '#') {
            //First time load
            $root = Category::getRootCategory();
            $category = Category::getHomeCategories($idLang, true);
            $categoryArray = array();
            foreach ($category as $catkey => $cat) {
                $categoryArray[$catkey]['id'] = $cat['id_category'];
                $categoryArray[$catkey]['text'] = $cat['name'];
                $subcategory = $this->getPsCategories($cat['id_category'], $idLang);
                $subChildSelect = false;
                if ($subcategory) {
                    $categoryArray[$catkey]['children'] = true;

                    foreach ($subcategory as $subcat) {
                        if (in_array($subcat['id_category'], $selectedCatIds)) {
                            $subChildSelect = true;
                        } else {
                            $this->findChildCategory($subcat['id_category'], $idLang, $selectedCatIds);
                            if ($this->checkCategories) {
                                $subChildSelect = true;
                                $this->checkCategories = false;
                            }
                        }
                    }
                } else {
                    $categoryArray[$catkey]['children'] = false;
                }

                if (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $categoryArray[$catkey]['state'] = array('opened' => true, 'selected' => true);
                } elseif (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == false) {
                    $categoryArray[$catkey]['state'] = array('selected' => true);
                } elseif (!in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $categoryArray[$catkey]['state'] = array('opened' => true);
                }
            }

            $treeLoad = array();
            if (in_array($root->id_category, $selectedCatIds)) {
                $treeLoad =  array("id" => $root->id_category,
                                    "text" => $root->name,
                                    "children" => $categoryArray,
                                    "state" => array('opened' => true, 'selected' => true)
                                );
            } else {
                $treeLoad =  array("id" => $root->id_category,
                                    "text" => $root->name,
                                    "children" => $categoryArray,
                                    "state" => array('opened' => true)
                                );
            }
        } else {
            //If sub-category is selected then its automatically called
            $childcategory = $this->getPsCategories($catId, $idLang);
            $treeLoad = array();
            $singletreeLoad = array();
            foreach ($childcategory as $cat) {
                $subcategoryArray = array();
                $subcategoryArray['id'] = $cat['id_category'];
                $subcategoryArray['text'] = $cat['name'];
                $subcategory = $this->getPsCategories($cat['id_category'], $idLang);

                $subChildSelect = false;
                if ($subcategory) {
                    $subcategoryArray['children'] = true;

                    foreach ($subcategory as $subcat) {
                        if (in_array($subcat['id_category'], $selectedCatIds)) {
                            $subChildSelect = true;
                        } else {
                            $this->findChildCategory($subcat['id_category'], $idLang, $selectedCatIds);
                            if ($this->checkCategories) {
                                $subChildSelect = true;
                                $this->checkCategories = false;
                            }
                        }
                    }
                } else {
                    $subcategoryArray['children'] = false;
                }

                if (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $subcategoryArray['state'] = array('opened' => true, 'selected' => true);
                } elseif (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == false) {
                    $subcategoryArray['state'] = array('selected' => true);
                } elseif (!in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $subcategoryArray['state'] = array('opened' => true);
                }

                $singletreeLoad[] = $subcategoryArray;
            }

            $treeLoad = $singletreeLoad;
        }

        return $treeLoad;
    }

    public function findChildCategory($id_category, $idLang, $selectedCatIds)
    {
        $subcategory = $this->getPsCategories($id_category, $idLang);
        if ($subcategory) {
            foreach ($subcategory as $subcat) {
                if (in_array($subcat['id_category'], $selectedCatIds)) {
                    $this->checkCategories = true;
                    return;
                } else {
                    $this->findChildCategory($subcat['id_category'], $idLang, $selectedCatIds);
                }
            }
        } else {
            return false;
        }
    }

    /**
    * Get seller product categories by using seller product ID
    *
    * @param int $mpProductID Seller Product ID
    * @return array/boolean
    */
    public static function getMultipleCategories($mpProductID)
    {
        $mcategory = Db::getInstance()->executeS(
            'SELECT `id_category` FROM `'._DB_PREFIX_.'wk_mp_seller_product_category`
            WHERE `id_seller_product` = '.(int) $mpProductID
        );

        if (empty($mcategory)) {
            return false;
        }

        $mcat = array();
        foreach ($mcategory as $cat) {
            $mcat[] = $cat['id_category'];
        }

        return $mcat;
    }

    // Not using any more, using getHomeCategories funrcion instead of
    public function getPsCategories($id_parent, $id_lang)
    {
        return Db::getInstance()->executeS(
            'SELECT a.`id_category`, a.`id_parent`, l.`name` FROM `'._DB_PREFIX_.'category` a
            LEFT JOIN `'._DB_PREFIX_.'category_lang` l ON (a.`id_category` = l.`id_category`)
            WHERE a.`id_parent` = '.(int) $id_parent.'
            AND l.`id_lang` = '.(int) $id_lang.'
            AND l.`id_shop` = '.(int) Context::getContext()->shop->id.'
            AND a.`active` = 1
            ORDER BY a.`id_category`'
        );
    }

    /**
    * Delete product category By seller product id
    *
    * @param int $idMpProduct Seller Product ID
    * @return array/boolean
    */
    public function deleteProductCategory($idMpProduct)
    {
        return Db::getInstance()->delete('wk_mp_seller_product_category', 'id_seller_product = '. (int) $idMpProduct);
    }

    /**
    * Copy seller product category into other product
    *
    * @param int $originalMpProductId - Original Product ID
    * @param int $duplicateMpProductId - Duplicate Product ID
    *
    * @return array/boolean
    */
    public static function copyMpProductCategories($originalMpProductId, $duplicateMpProductId)
    {
        $categories = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_product_category`
            WHERE `id_seller_product` = '.(int) $originalMpProductId
        );
        if ($categories) {
            //Add into category table
            foreach ($categories as $pCategory) {
                $objMpCategory = new self();
                $objMpCategory->id_seller_product = $duplicateMpProductId;
                $objMpCategory->id_category = $pCategory['id_category'];
                $objMpCategory->is_default = $pCategory['is_default'];
                $objMpCategory->add();
                unset($objMpCategory);
            }
        }
        return true;
    }
	
	public static function getSellerCategories($id_seller_product, $exl_cats = array(), $as_group = true)
    {
        $sql = 'SELECT '.($as_group ? 'GROUP_CONCAT(`id_category`) as seller_cats' : '*').'
            FROM `'._DB_PREFIX_.'wk_mp_seller_product_category`
            WHERE `id_seller_product` = '.(int)$id_seller_product
            .(count($exl_cats) && is_array($exl_cats) ? ' AND id_category <> '. implode(',', array_map('intval', $exl_cats)) : '');

        if ($as_group)
            return Db::getInstance()->getValue($sql);
        return Db::getInstance()->executeS($sql);
    }

    public static function getSellerCategoriesNames($id_seller_product, $id_lang, $exl_cats = array())
    {
        $sql = 'SELECT cl.`name`, pc.id_category
            FROM `'._DB_PREFIX_.'wk_mp_seller_product_category` pc
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (pc.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
            WHERE pc.`id_seller_product` = '.(int)$id_seller_product.' AND cl.`id_lang` = '.(int)$id_lang
            .(count($exl_cats) && is_array($exl_cats) ? ' AND pc.id_category NOT IN ('. implode(',', array_map('intval', $exl_cats)) .'\')' : '');
        $results = Db::getInstance()->executeS($sql);
        $cat_names = [];

        if ($results) {
            foreach ($results as $res) {
                $cat_names[] = $res['name'];
            }
            unset($results);
        }

        return $cat_names;
    }

    public static function deleteCategoryByCatIdByProdId($idMpProduct, $catID)
    {
        return Db::getInstance()->delete('wk_mp_seller_product_category', 'id_seller_product = '. (int) $idMpProduct .' AND id_category = '.(int)$catID);
    }

    public static function getAllSellersUniqueCategories($id_lang, $cats = array(), $orderby = 'cl.`name`')
    {
        $sql = 'SELECT cl.`name`, pc.`id_category`, coucou.`level_depth`, coucou.`id_parent`
            FROM `'._DB_PREFIX_.'wk_mp_seller_product` sp
            INNER JOIN `'._DB_PREFIX_.'wk_mp_seller_product_category` pc ON (sp.id_mp_product=pc.id_seller_product)
            INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (pc.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
            INNER JOIN `'._DB_PREFIX_.'category` coucou ON (cl.`id_category` = coucou.`id_category`)
            WHERE cl.`id_lang` = '.(int)$id_lang
            .(count($cats) && is_array($cats) ? ' AND pc.id_category IN ('. implode(',', array_map('intval', $cats)) .')' : '')
            .' GROUP BY pc.`id_category` ORDER BY '. pSQL($orderby) .' ASC';
        $results = Db::getInstance()->executeS($sql);

        return $results;
    }

    public static function getCategoriesIdsFromNames($id_lang, array $names)
    {
        if (!is_array($names)) {
            $names = [$names];
        }
        $sql = 'SELECT GROUP_CONCAT(`id_category`) FROM `'._DB_PREFIX_.'category_lang`
            WHERE `id_lang` = '.(int)$id_lang .' AND name IN ('. implode(',', array_map(function($str){
                return '"'. $str .'"';}, $names)) .')';
        return Db::getInstance()->getValue($sql);
    }

    public static function getAllSellersUniqueCategoriesBySellersIds($id_lang, $sellers_ids = array())
    {
        $sql = 'SELECT cl.`name`, pc.`id_category`
            FROM `'._DB_PREFIX_.'wk_mp_seller` s
            INNER JOIN `'._DB_PREFIX_.'wk_mp_seller_product` sp ON (s.id_seller=sp.id_seller)
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product_category` pc ON (sp.id_mp_product=pc.id_seller_product)
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (pc.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
            WHERE cl.`id_lang` = '.(int)$id_lang
            .(count($sellers_ids) && is_array($sellers_ids) ? ' AND sp.id_seller IN ('. implode(',', array_map('intval', $sellers_ids)) .')' : '')
            .' GROUP BY pc.`id_category` ORDER BY cl.`name` ASC';
        $results = Db::getInstance()->executeS($sql);

        return $results;
    }

    public function getCategoryNameByID($id_category)
    {
        return Db::getInstance()->getValue('
        SELECT `name`
        FROM `'._DB_PREFIX_.'category_lang`
        WHERE`id_category` = '.(int) $id_category);
    }

    /**
     * Get a simple list of categories
     *
     * @param int $idLang Language ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource Return array with `id_category` and `name` for every Category
     */
    public static function getSimpleCategories($idLang, $cats = array(), $orderby = 'category_shop.`position`')
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT c.`id_category`, cl.`name`
            FROM `'._DB_PREFIX_.'category` c
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
            '.Shop::addSqlAssociation('category', 'c').'
            WHERE cl.`id_lang` = '.(int) $idLang.'
            AND c.`id_category` != '.Configuration::get('PS_ROOT_CATEGORY')
            .(count($cats) && is_array($cats) ? ' AND c.id_category IN ('. implode(',', array_map('intval', $cats)) .')' : '')
            .'GROUP BY c.id_category ORDER BY '. pSQL($orderby));
    }
}
