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

class SellerProductCategory extends ObjectModel
{
    public $id;
    public $id_category;
    public $id_seller_product;
    public $is_default;

    public static $definition = array(
        'table' => 'marketplace_seller_product_category',
        'primary' => 'id',
        'fields' => array(
            'id_category' => array('type' => self::TYPE_INT),
            'is_default' => array('type' => self::TYPE_INT),
            'id_seller_product' => array('type' => self::TYPE_INT),
        ),
    );

    public function getMpDefaultCategory($mpproductid)
    {
        $defaultcat = Db::getInstance()->getValue(
            'SELECT `id_category` FROM `'._DB_PREFIX_.'marketplace_seller_product`
			WHERE `id` = '.(int) $mpproductid
        );
        if ($defaultcat) {
            return $defaultcat;
        }

        return false;
    }

    // Not using any more, using getHomeCategories funrcion instead of
    public function getPsCategories($id_parent, $id_lang)
    {
        return Db::getInstance()->executeS(
            'SELECT a.`id_category`, a.`id_parent`, l.`name` FROM `'._DB_PREFIX_.'category` a
			LEFT JOIN `'._DB_PREFIX_.'category_lang` l  ON (a.`id_category` = l.`id_category`)
			WHERE a.`id_parent` = '.$id_parent.' 
            AND l.`id_lang` = '.$id_lang.'
			AND l.`id_shop` = '.Context::getContext()->shop->id.'
			AND a.`active` = 1 
            ORDER BY a.`id_category`'
        );
    }

    public function getCategoryTree($id_lang, $checked_catg = false, $default_catg = false)
    {
        $root = Category::getRootCategory();
        //$category = $this->getPsCategories($root->id, $id_lang);
        $category = Category::getHomeCategories($id_lang, true); //getting all active direct child of root category

        if ($category) { //get the id_parent for every category
            foreach ($category as $key => $catg) {
                $obj_category = new Category($catg['id_category'], $id_lang);
                $category[$key]['id_parent'] = $obj_category->id_parent;
            }
        }

        $tree = "<ul id='wk_mp_category_tree'>";
        $tree .= "<li><input type='checkbox' ";
        if ($checked_catg) {
            foreach ($checked_catg as $product_cat) {
                if ($product_cat['id_category'] == $root->id) {
                    $tree .= "checked='checked'";
                }
            }
        } else {
            if ($default_catg == $root->id) {
                $tree .= "checked='checked'";
            }
        }

        if (!$checked_catg && !$default_catg) {
            $tree .= "checked='checked'";
        }

        $tree .= " class='product_category' name='product_category[]' value='".$root->id."'><label>".$root->name.'</label>';

        $exclude = array();
        array_push($exclude, 0);

        foreach ($category as $cat) {
            $goOn = 1;
            $tree .= '<ul>';
            for ($x = 0; $x < count($exclude); ++$x) {
                if ($exclude[$x] == $cat['id_category']) {
                    $goOn = 0;
                    break;
                }
            }
            if ($goOn == 1) {
                $tree .= "<li><input type='checkbox' ";
                if ($checked_catg) {
                    foreach ($checked_catg as $product_cat) {
                        if ($product_cat['id_category'] == $cat['id_category']) {
                            $tree .= "checked='checked'";
                        }
                    }
                } else {
                    if ($default_catg == $cat['id_category']) {
                        $tree .= "checked='checked'";
                    }
                }
                $tree .= " name='product_category[]' class='product_category' value='".$cat['id_category']."'><label>".$cat['name'].'</label>';

                array_push($exclude, $cat['id_category']);
                if ($checked_catg) {
                    $tree .= $this->buildChildCategoryRecursive($cat['id_category'], $id_lang, $checked_catg);
                } else {
                    $tree .= $this->buildChildCategoryRecursive($cat['id_category'], $id_lang);
                }
            }
            $tree .= '</ul>';
        }

        return $tree;
    }

    public function buildChildCategoryRecursive($oldID, $id_lang, $checked_product_cat = false, $defaultcatid = false)
    {
        $depth = '';
        $tempTree = '';
        $tree = '';
        $category = $this->getPsCategories($oldID, $id_lang);

        if (!empty($category)) {
            $tempTree .= '<ul>';
            foreach ($category as $child) {
                if ($child['id_category'] != $child['id_parent']) {
                    $tempTree .= "<li><input type='checkbox'";
                    if (!empty($checked_product_cat)) {
                        foreach ($checked_product_cat as $product_cat) { //For edit products
                            if ($product_cat['id_category'] == $child['id_category']) {
                                $tempTree .= 'checked';
                            }
                        }
                    } else {
                        if ($defaultcatid == $child['id_category']) {
                            $tree .= 'checked';
                        }
                    }
                    $tempTree .= " name='product_category[]' class='product_category' value='".$child['id_category']."'><label>".$child['name'].'</label>';
                    ++$depth;
                    $tempTree .= $this->buildChildCategoryRecursive($child['id_category'], $id_lang, $checked_product_cat, $defaultcatid);
                    --$depth;
                    $exclude = array();
                    array_push($exclude, $child['id_category']);
                }
            }
            $tempTree .= '</ul>';
        }

        return $tempTree;
    }
}
