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

class WkMpBookingProductInformation extends ObjectModel
{
    public $id_product;
    public $id_mp_product;
    public $id_seller;
    public $quantity;
    public $booking_type;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_booking_product_info',
        'primary' => 'id_booking_product_info',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_mp_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'quantity' => array('type' => self::TYPE_INT),
            'booking_type' => array('type' => self::TYPE_INT, 'required' => true),
            'active' => array('type' => self::TYPE_INT),
            'activity_addr' => array('type' => self::TYPE_HTML),
            'activity_city' => array('type' => self::TYPE_STRING),
            'activity_postcode' => array('type' => self::TYPE_STRING),
            'activity_period' => array('type' => self::TYPE_STRING),
            'activity_curious' => array('type' => self::TYPE_STRING),
            'activity_participants' => array('type' => self::TYPE_STRING),
            'activity_material' => array('type' => self::TYPE_HTML),
            'latitude' => array('type' => self::TYPE_STRING),
            'longitude' => array('type' => self::TYPE_STRING),
            'video_link' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    const WK_PRODUCT_BOOKING_TYPE_DATE = 1;
    const WK_PRODUCT_BOOKING_TYPE_TIME_SLOT = 2;

    public function getSellerBookingProductsInfo(
        $idSeller = false,
        $active = 'all',
        $idLang = false,
        $orderby = false,
        $orderway = false,
        $start_point = 0,
        $limit_point = 10000000
    ) {
        if (!$idLang) {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        if (!$orderway) {
            $orderway = 'desc';
        }

        $sql = 'SELECT msp.*, mspl.`product_name` FROM `'._DB_PREFIX_.'wk_mp_seller_product` msp
                INNER JOIN `'._DB_PREFIX_.'wk_mp_booking_product_info` mpbi ON (mpbi.id_mp_product = msp.id_mp_product)
                LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product_lang` mspl ON (mspl.id_mp_product = msp.id_mp_product)
                WHERE mspl.`id_lang` = '.(int) $idLang;

        if ($idSeller) {
            $sql .= ' AND msp.`id_seller` = '.(int) $idSeller;
        }

        if ($active === true || $active === 1) {
            $sql .= ' AND msp.`active` = 1 ';
        } elseif ($active === false || $active === 0) {
            $sql .= ' AND msp.`active` = 0 ';
        }

        if (!$orderby) {
            $sql .= ' ORDER BY msp.`id_mp_product` '.pSQL($orderway);
        } elseif ($orderby == 'name') {
            $sql .= ' ORDER BY mspl.`product_name` '.pSQL($orderway);
        } else {
            $sql .= ' ORDER BY msp.`'.$orderby.'` '.pSQL($orderway);
        }
        $sql .= ' LIMIT '.$start_point.','.$limit_point;

        $mpBookingProducts = Db::getInstance()->executeS($sql);

        if ($mpBookingProducts && !empty($mpBookingProducts)) {
            return $mpBookingProducts;
        }
        return false;
    }

    public static function getBookingProductInfo($id_mp_product = 0, $id_product = 0)
    {
        if ($id_mp_product || $id_product) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_info` WHERE 1';
            if ($id_mp_product) {
                $sql .= ' AND `id_mp_product`='.(int) $id_mp_product;
            }
            if ($id_product) {
                $sql .= ' AND `id_product`='.(int) $id_product;
            }
            return Db::getInstance()->getRow($sql);
        }
        return false;
    }

    //Load Prestashop category with ajax load of plugin jstree.
    public static function getBookingProductCategory()
    {
        // here id_mp_product is ps_product_id but we have used Mp js so variable used as per requirements
        if (!Tools::getValue('id_mp_product')) {
            // Add product
            $catId = Tools::getValue('catsingleId');
            $selectedCatIds = array(Category::getRootCategory()->id); //Root Category will be automatically selected
        } else {
            // Edit product
            $catId = Tools::getValue('catsingleId');
            $selectedCatIds = explode(',', Tools::getValue('catIds'));
        }
        $objBookingProductInfor = new WkMpBookingProductInformation();
        $treeLoad = $objBookingProductInfor->getProductCategory(
            $catId,
            $selectedCatIds,
            Context::getContext()->language->id
        );
        if ($treeLoad) {
            die(Tools::jsonEncode($treeLoad)); //ajax close
        } else {
            die('fail'); //ajax close
        }
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
                $treeLoad =  array(
                    "id" => $root->id_category,
                    "text" => $root->name,
                    "children" => $categoryArray,
                    "state" => array('opened' => true, 'selected' => true)
                );
            } else {
                $treeLoad =  array(
                    "id" => $root->id_category,
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

    public function isBookingProduct($id_mp_product, $id_product)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_info`
            WHERE `id_mp_product`='.(int) $id_mp_product.' AND `id_product`='.(int) $id_product
        );
    }

    //Considering admin can call this function for id_mp_product. Otherwise in the frontend always send id_product
    public static function getAppliedProductTaxRate($id_product = 0, $id_mp_product = 0)
    {
        $taxRate = 0;
        if ($id_product) {
            $productPriceTI = Product::getPriceStatic((int) $id_product, true);
            $productPriceTE = Product::getPriceStatic((int) $id_product, false);
            if ($productPriceTE) {
                $tax = $productPriceTI - $productPriceTE;
                $taxRate = ($tax / $productPriceTE) * 100;
            }
        } elseif ($id_mp_product) {
            $objMpProduct = new WkMpSellerProduct($id_mp_product);
            $idTaxRulesGroup = $objMpProduct->id_tax_rules_group;
            $idCountryDefault = Configuration::get('PS_COUNTRY_DEFAULT');
            $taxesRatesByGroup = TaxRulesGroup::getAssociatedTaxRatesByIdCountry($idCountryDefault);
            if ($taxesRatesByGroup) {
                if (isset($taxesRatesByGroup[$idTaxRulesGroup]) && $taxesRatesByGroup[$idTaxRulesGroup]) {
                    $taxRate = $taxesRatesByGroup[$idTaxRulesGroup];
                }
            }
        }
        return $taxRate;
    }

    // Admin feature price PS product search
    public function searchAdminBookingProductByName($id_lang, $query)
    {
        $sql = new DbQuery();
        $sql->select('bp.`id_booking_product_info`, p.`id_product`, pl.`name`');

        $sql->from('product', 'p');

        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->innerJoin('wk_mp_booking_product_info', 'bp', 'bp.`id_product` = p.`id_product`');
        $sql->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl'));

        $where = 'bp.`id_seller` = 0 AND pl.`name` LIKE \'%'.pSQL($query).'%\'';
        $sql->where($where);
        $sql->join(Product::sqlStock('p', 0));

        $result = Db::getInstance()->executeS($sql);

        if (!$result) {
            return false;
        }
        $results_array = array();
        foreach ($result as $row) {
            $row['price_tax_incl'] = Product::getPriceStatic($row['id_product'], true, null, 2);
            $row['price_tax_excl'] = Product::getPriceStatic($row['id_product'], false, null, 2);
            $results_array[] = $row;
        }
        return $results_array;
    }

    // Admin feature price Mp product search
    public function searchSellerBookingProductByName($id_lang, $query, $id_seller = 0)
    {
        $sql = new DbQuery();
        $sql->select('bmp.`id_booking_product_info`, msp.`id_mp_product`, mpl.`product_name` as name, msp.`ean13`, msp.`upc`, msp.`active`, msp.`reference`');
        $sql->from('wk_mp_seller_product', 'msp');
        $sql->innerJoin('wk_mp_booking_product_info', 'bmp', 'bmp.`id_mp_product` = msp.`id_mp_product`');
        $sql->leftJoin(
            'wk_mp_seller_product_lang', 'mpl', 'msp.`id_mp_product` = mpl.`id_mp_product` AND mpl.`id_lang` = '
            .(int)$id_lang
        );
        $where = 'mpl.`product_name` LIKE \'%'.pSQL($query).'%\' AND msp.`id_seller`='.(int)$id_seller;

        $sql->orderBy('mpl.`product_name` ASC');
        $sql->where($where);
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public function deleteBookingProductInfo($id_mp_product = 0, $id_product = 0)
    {
        if ($id_mp_product || $id_product) {
            if ($id_mp_product) {
                $condition .= '`id_mp_product`='.(int) $id_mp_product;
            }
            if ($id_product) {
                $condition .= '`id_product`='.(int) $id_product;
            }
            return Db::getInstance()->delete('wk_mp_booking_product_info', $condition);
        }
        return false;
    }

    public function validationBookingProductFormFieldJs($params)
    {
        $className = 'WkMpBookingProductInformation';
        $objModule = new MpBooking();
        if (isset($params['default_lang'])) {
            $sellerDefaultLanguage = $params['default_lang'];
        } else {
            $sellerDefaultLanguage = $params['seller_default_lang'];
        }
        $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);
        $quantity = $params['quantity'];
        $categories = $params['product_category'];

        $price = $params['price'];
        if (Configuration::get('WK_MP_SELLER_PRODUCT_REFERENCE') || (Tools::getValue('controller') == 'AdminSellerProductDetail')) {
            $reference = trim($params['reference']);
        } else {
            $reference = '';
        }
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            if (!Validate::isCatalogName($params['product_name_'.$language['id_lang']])) {
                $invalidProductName = 1;
            } elseif ($params['product_name_'.$language['id_lang']] && !Tools::link_rewrite($params['product_name_'.$language['id_lang']])) {
                $invalidProductName = 1;
            }

            if ($params['short_description_'.$language['id_lang']]) {
                $shortDesc = $params['short_description_'.$language['id_lang']];
                $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
                if ($limit <= 0) {
                    $limit = 400;
                }
                if (!Validate::isCleanHtml($shortDesc)) {
                    $invalidSortDesc = 1;
                } elseif (Tools::strlen(strip_tags($shortDesc)) > $limit) {
                    $invalidSortDesc = 2;
                }
            }

            if ($params['description_'.$language['id_lang']]) {
                if (!Validate::isCleanHtml($params['description_'.$language['id_lang']], (int) Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                    $invalidDesc = 1;
                }
            }
        }

        if (!$params['product_name_'.$defaultLang]) {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $sellerLang = Language::getLanguage((int) $defaultLang);
                $msg = sprintf($objModule->l('Product name is required in %s', $className), $sellerLang['name']);
            } else {
                $msg = $objModule->l('Product name is required', $className);
            }
            $data = array(
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '1',
                'inputName' => 'product_name_all',
                'msg' => $msg
            );
            die(Tools::jsonEncode($data));
        } elseif (isset($invalidProductName)) {
            $data = array(
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '1',
                'inputName' => 'product_name_all',
                'msg' => $objModule->l('Product name have Invalid characters', $className)
            );
            die(Tools::jsonEncode($data));
        }

        if (isset($invalidSortDesc)) {
            if ($invalidSortDesc == 1) {
                $data = array(
                    'status' => 'ko',
                    'tab' => 'wk-information',
                    'multilang' => '1',
                    'inputName' => 'wk_short_desc',
                    'msg' => $objModule->l('Short description have not valid data', $className)
                );
                die(Tools::jsonEncode($data));
            } elseif ($invalidSortDesc == 2) {
                $data = array(
                    'status' => 'ko',
                    'tab' => 'wk-information',
                    'multilang' => '1',
                    'inputName' => 'wk_short_desc',
                    'msg' => sprintf($objModule->l('This short description field is too long: %s characters max.', $className), $limit)
                );
                die(Tools::jsonEncode($data));
            }
        }

        if (isset($invalidDesc)) {
            $data = array(
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '1',
                'inputName' => 'wk_desc',
                'msg' => $objModule->l('Product description have not valid data', $className)
            );
            die(Tools::jsonEncode($data));
        }

        //Product Price Js Validation
        if ($price == '') {
            $data = array(
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'price',
                'msg' => $objModule->l('Product price is required field', $className)
            );
            die(Tools::jsonEncode($data));
        } elseif (!Validate::isPrice($price)) {
            $data = array(
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'price',
                'msg' => $objModule->l('Product price should be valid', $className)
            );
            die(Tools::jsonEncode($data));
        }
        //Product Quantity Js Validation
        if ($quantity == '') {
            $data = array(
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'quantity',
                'msg' => $objModule->l('Product quantity is required field', $className)
            );
            die(Tools::jsonEncode($data));
        } elseif (!Validate::isInt($quantity)) {
            $data = array(
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'quantity',
                'msg' => $objModule->l('Product quantity should be valid', $className)
            );
            die(Tools::jsonEncode($data));
        }
        if (!$categories) {
            $data = array(
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'categorycontainer',
                'msg' => $objModule->l('You have not selected any category', $className)
            );
            die(Tools::jsonEncode($data));
        }
        //Product Reference, EAN, UPC Js Validation
        if ($reference && !Validate::isReference($reference)) {
            $data = array(
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'reference',
                'msg' => $objModule->l('Reference is not valid', $className)
            );
            die(Tools::jsonEncode($data));
        }
    }
	
	public function getBookingProductInfoByIdProduct($id_product)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_info` WHERE `id_product`='.(int) $id_product);
    }

    public function getBookingProductInfoByMpIdProduct($id_mp_product)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_info` WHERE `id_mp_product`='.(int) $id_mp_product);
    }
	/**
     * Load Prestashop category with ajax load of plugin jstree.
     */
    public static function getWkBookingProductCategory()
    {
        if (!Tools::getValue('id')) {
            // Add product
            $catId = Tools::getValue('catsingleId');
            $selectedCatIds = array(Category::getRootCategory()->id); //Root Category will be automatically selected
        } else {
            // Edit product
            $catId = Tools::getValue('catsingleId');
            $selectedCatIds = explode(',', Tools::getValue('catIds'));
        }
        $objBookingProductInformation = new WkMpBookingProductInformation();
        $treeLoad = $objBookingProductInformation->getProductCategory($catId, $selectedCatIds, Context::getContext()->language->id);
        if ($treeLoad) {
            die(Tools::jsonEncode($treeLoad)); //ajax close
        } else {
            die('fail'); //ajax close
        }
    }
	public function getSellerBookingProducts($id_seller = null, $active = null)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_info` WHERE 1';
        if (isset($id_seller)) {
            $sql .= ' AND `id_seller`='.(int) $id_seller;
        } else {
            $sql .= ' AND `id_seller` != 0';
        }
        if ($active) {
            $sql .= ' AND `active`='.(int) $active;
        }
        return Db::getInstance()->executeS($sql);
    }
	public static function isBookingProductStatic($id_product)
    {
		if (is_array($id_product))
            return Db::getInstance()->getValue('SELECT `id_product` FROM `'._DB_PREFIX_.'wk_mp_booking_product_info` WHERE `id_product` IN ('. implode(',', array_map('intval', $id_product)) .')');
        return Db::getInstance()->getValue('SELECT `id_product` FROM `'._DB_PREFIX_.'wk_mp_booking_product_info` WHERE `id_product`='.(int) $id_product);
    }
	public function deleteBookingProductByIdProduct($id_product)
    {
        return Db::getInstance()->delete('wk_mp_booking_product_info', '`id_product`='.(int) $id_product);
    }
}
