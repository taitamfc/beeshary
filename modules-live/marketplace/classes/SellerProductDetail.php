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

class SellerProductDetail extends ObjectModel
{
    public $id;
    public $id_seller;
    public $price;
    public $quantity;
    public $id_category;
    public $active;
    public $id_ps_product;  // prestashop id product first time 0 when product is not created in ps
    public $id_ps_shop;
    public $condition;
    public $admin_assigned;  // if product assigned by admin to seller this will be 1
    public $date_add;
    public $date_upd;

    public $product_name;
    public $short_description;
    public $description;
    public $link_rewrite;

    public static $definition = array(
        'table' => 'marketplace_seller_product',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'required' => true),
            'price' => array('type' => self::TYPE_FLOAT,'validate' => 'isPrice', 'required' => true),
            'quantity' => array('type' => self::TYPE_INT),
            'id_category' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_ps_product' => array('type' => self::TYPE_INT, 'required' => true),
            'id_ps_shop' => array('type' => self::TYPE_INT),
            'condition' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('new', 'used', 'refurbished'), 'default' => 'new'),
            'admin_assigned' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),

            /* Lang fields */
            'product_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true),
            'short_description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'link_rewrite' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isLinkRewrite',
                'required' => true,
                'size' => 128,
            ),
        ),
    );

    public function toggleStatus()
    {
        return true;
    }

    public function delete()
    {
        if (!$this->deleteMpProduct($this->id) || !parent::delete()) {
            return false;
        }

        return true;
    }

    /**
     * deleting from all tables if product is activated.
     * @param [int] $mp_id_product
     * @return [boolean]
     */
    public function deleteMpProduct($mp_id_product)
    {
        $obj_mpproduct = new self($mp_id_product);
        Hook::exec('actionMpProductDelete', array('mp_id_product' => $mp_id_product));
        if ($obj_mpproduct->id) {
            //if activated

            if (!$obj_mpproduct->admin_assigned && $obj_mpproduct->id_ps_product) {
                //delete only seller created products not the admin assigned products from catalog list
                $obj_product = new Product($obj_mpproduct->id_ps_product);
                $obj_product->delete();
            }
        }

        $delete_mpcatg = Db::getInstance()->delete('marketplace_seller_product_category', 'id_seller_product = '.(int) $mp_id_product);

        if (!$delete_mpcatg
            || !$this->deleteMpProductImage($mp_id_product)) {
            return false;
        }

        return true;
    }

    public function deleteMpProductImage($mp_id_product = false, $mp_id_image = false, $id_ps_image = false)
    {
        if ($mp_id_product) {
            $product_images = $this->getMpProductImages($mp_id_product);
            if ($product_images) {
                foreach ($product_images as $image) {
                    if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$image['seller_product_image_id'].'.jpg')) {
                        if (unlink(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$image['seller_product_image_id'].'.jpg')) {
                            if (!Db::getInstance()->delete('marketplace_product_image', 'seller_product_id = '.(int) $mp_id_product)) {
                                return false;
                            }
                        }
                    }
                }
            }
        } elseif ($mp_id_image) {
            if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$mp_id_image.'.jpg')) {
                if (unlink(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$mp_id_image.'.jpg')) {
                    if (!Db::getInstance()->delete('marketplace_product_image', 'seller_product_image_id = '.$mp_id_image)) {
                        return false;
                    }
                }
            }
        } elseif ($id_ps_image) {
            $obj_mp_image = new MarketplaceProductImage();
            $mp_image_details = $obj_mp_image->getProductImageByPsIdImage($id_ps_image);
            if ($mp_image_details) {
                if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$mp_image_details['seller_product_image_id'].'.jpg')) {
                    if (unlink(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$mp_image_details['seller_product_image_id'].'.jpg')) {
                        if (!Db::getInstance()->delete('marketplace_product_image', 'id = '.$mp_image_details['id'])) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * [get mp seller active product with ps image details
     * and link used for product slider on seller profile and shop page].
     * @param [int] $mp_id_seller [mp id seller]
     * @param [int] $id_ps_shop   [ps id shop]
     * @param [int] $id_lang      [language id]
     * @return [array/bool] [array/false]
     */
    public function getActiveMpProductWithImage($idSeller, $idPsShop, $idLang)
    {
        $mpProducts = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product` mpsp
			JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = mpsp.`id_ps_product`)
			JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`)
			WHERE mpsp.`id_seller` = '.$idSeller.' AND pl.`id_shop` = '.$idPsShop.'
			AND pl.`id_lang` = '.$idLang.' AND p.active = 1 AND mpsp.id_ps_product != 0 ORDER BY p.`date_add` LIMIT 10'
        );

        if ($mpProducts) {
            foreach ($mpProducts as $key => $product) {
                $objProduct = new Product($product['id_product'], true, $idLang);
                $mpProducts[$key]['product'] = $objProduct;
                $mpProducts[$key]['lang_iso'] = Context::getContext()->language->iso_code;
                $mpProducts[$key]['price'] = $objProduct->price;
                $cover = Product::getCover($product['id_product']);
                if ($cover) {
                    $mpProducts[$key]['image'] = $product['id_product'].'-'.$cover['id_image'];
                } else {
                    $mpProducts[$key]['image'] = 0;
                }
            }

            return $mpProducts;
        }

        return false;
    }

    /**
     * [getPriceByIdCurrency convert the price by currency rate].
     * @param [float] $price       [price]
     * @param [int]   $id_currency [currency id]
     * @return [float/false]
     */
    public static function getPriceByIdCurrency($price, $id_currency = false)
    {
        if (!$id_currency) {
            $id_currency = Context::getContext()->currency->id;
        }

        if ($price != '') {
            $obj_curreny = Currency::getCurrency($id_currency);
            $price_conversion_rate = $obj_curreny['conversion_rate'];

            return ($price * $price_conversion_rate);
        }

        return false;
    }

    public static function getMpProductImages($mp_id_product)
    {
        $product_images = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_product_image`
			WHERE `seller_product_id` = '.(int) $mp_id_product
        );

        if ($product_images && !empty($product_images)) {
            return $product_images;
        }

        return false;
    }

    public static function getMpSellerProductDetailsWithLang($id_seller, $id_lang = false, $active = false)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = 'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product` msp
				LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_product_lang` mspl ON (mspl.id = msp.id)
				WHERE msp.`id_seller` = '.$id_seller.' AND mspl.`id_lang` = '.$id_lang;

        if ($active) {
            $sql .= ' AND `active` = 1';
        }

        $mp_products = Db::getInstance()->executeS($sql);
        if ($mp_products && !empty($mp_products)) {
            return $mp_products;
        }

        return false;
    }

    public static function getMpProductDetailsWithLangByIdProduct($id_product, $id_lang = false)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product` msp
            JOIN `'._DB_PREFIX_.'marketplace_seller_product_lang` mspl ON (mspl.id = msp.id)
            WHERE msp.`id` = '.(int) $id_product.' AND mspl.`id_lang` = '.$id_lang
        );
    }

    public static function getMpProductDetailsByIdProduct($id_product)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product` WHERE `id` = '.(int) $id_product);
    }

    /**
     * [create marketplace product in prestashop while activating].
     * @param [int]    $mp_product_id [id product marketplace]
     * @param [string] $image_dir     [image upload directory]
     * @param [int]    $active        [status]
     * @return [int/array] [id_product added to prestashop OR false]
     */
    public function createPsProductByMarketplaceProduct($mp_product_id, $image_dir, $active)
    {
        $count = 0;
        $default_tax_rule_group = 1;
        $product_info = $this->getMpProductDetailsByIdProduct($mp_product_id, 2);

        $prod_lang_info_arr = $this->getMarketPlaceProductLangInfo($mp_product_id);
        foreach ($prod_lang_info_arr as $prod_lang_info) {
            $product_info['product_name'][$prod_lang_info['id_lang']] = $prod_lang_info['product_name'];
            $product_info['short_description'][$prod_lang_info['id_lang']] = $prod_lang_info['short_description'];
            $product_info['description'][$prod_lang_info['id_lang']] = $prod_lang_info['description'];
            $product_info['link_rewrite'][$prod_lang_info['id_lang']] = $prod_lang_info['link_rewrite'];
        }

        $quantity = (int) $product_info['quantity'];
        $category_id = (int) $product_info['id_category'];

        // Add Product
        $product = new Product();
        $product->name = array();
        $product->description = array();
        $product->description_short = array();
        $product->link_rewrite = array();
        foreach (Language::getLanguages(false) as $lang) {
            $product->name[$lang['id_lang']] = $product_info['product_name'][$lang['id_lang']];
            $product->description[$lang['id_lang']] = $product_info['description'][$lang['id_lang']];
            $product->description_short[$lang['id_lang']] = $product_info['short_description'][$lang['id_lang']];
            $product->link_rewrite[$lang['id_lang']] = $product_info['link_rewrite'][$lang['id_lang']];
        }

        $product->id_shop_default = Context::getContext()->shop->id;
        $product->id_category_default = $category_id;
        $product->price = $product_info['price'];
        $product->active = $active;
        $product->indexed = 1;
        $product->condition = $product_info['condition'];

        $obj_tax = new TaxRulesGroup($default_tax_rule_group);
        if ($obj_tax->active == 0) {
            $product->id_tax_rules_group = 0;
        } else {
            $product->id_tax_rules_group = 1;
        }

        $product->save();
        $ps_product_id = $product->id;

        foreach (Language::getLanguages(false) as $lang) {
            Search::indexation($product_info['link_rewrite'][$lang['id_lang']], $ps_product_id);
        }

        if ($ps_product_id > 0) {
            if ($category_id > 0) {
                $category_ids = $this->getMultipleCategories($mp_product_id);
                $product->addToCategories($category_ids);
            }

            if ($quantity >= 0) {
                StockAvailable::updateQuantity($ps_product_id, null, $quantity);
            }

            $image_list = $this->unactiveImage($mp_product_id);
            if ($image_list) {
                foreach ($image_list as $image) {
                    $old_path = $image_dir.'/'.$image['seller_product_image_id'].'.jpg';
                    $position = $count + 1;
                    $image_obj = new Image();
                    $image_obj->id_product = $ps_product_id;
                    $image_obj->position = $position;

                    if ($count == 0) {
                        $image_obj->cover = 1;
                    } else {
                        $image_obj->cover = 0;
                    }

                    $image_obj->add();
                    $image_id = $image_obj->id;
                    $new_path = $image_obj->getPathForCreation();
                    $imagesTypes = ImageType::getImagesTypes('products');

                    foreach ($imagesTypes as $image_type) {
                        ImageManager::resize($old_path, $new_path.'-'.$image_type['name'].'.jpg', $image_type['width'], $image_type['height']);
                    }

                    ImageManager::resize($old_path, $new_path.'.jpg');
                    Hook::exec('actionWatermark', array('id_image' => $image_id, 'id_product' => $ps_product_id));
                    Hook::exec('actionPsMpImageMap', array('mp_product_id' => $mp_product_id, 'mp_id_image' => $image['id'], 'ps_id_product' => $ps_product_id, 'ps_id_image' => $image_id));
                    //updating mp_product_image status
                    MarketplaceProductImage::updateStatusAndPsIdImageById($image['id'], 1, $image_id);
                    $count = $count + 1;
                }
            }

            return $ps_product_id;
        }

        return false;
    }

    public function getMultipleCategories($mp_product_id)
    {
        $mcategory = Db::getInstance()->executeS(
            'SELECT `id_category` FROM `'._DB_PREFIX_.'marketplace_seller_product_category`
			WHERE `id_seller_product` = '.(int) $mp_product_id
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

    /**
     * [update marketplace product in prestashop].
     * @param [int]    $mp_product_id   [id product marketplace]
     * @param [string] $image_dir       [image upload directory]
     * @param [int]    $active          [status]
     * @param [int]    $main_product_id [id_product prestashop product id]
     * @return [int/array] [id_product added to prestashop OR false]
     */
    public function updatePsProductByMarketplaceProduct($mp_product_id, $image_dir, $active)
    {
        $count = 0;
        $id_lang = Context::getContext()->language->id;
        $product_info = $this->getMpProductDetailsByIdProduct($mp_product_id);
        $main_product_id = $product_info['id_ps_product'];

        $prod_lang_info_arr = $this->getMarketPlaceProductLangInfo($mp_product_id);
        foreach ($prod_lang_info_arr as $prod_lang_info) {
            $product_info['product_name'][$prod_lang_info['id_lang']] = $prod_lang_info['product_name'];
            $product_info['short_description'][$prod_lang_info['id_lang']] = $prod_lang_info['short_description'];
            $product_info['description'][$prod_lang_info['id_lang']] = $prod_lang_info['description'];
            $product_info['link_rewrite'][$prod_lang_info['id_lang']] = $prod_lang_info['link_rewrite'];
        }

        $quantity = (int) $product_info['quantity'];
        $category_id = (int) $product_info['id_category'];
        $id_ps_shop = (int) $product_info['id_ps_shop'];

        // Add Product
        $product = new Product($product_info['id_ps_product']);
        $product->name = array();
        $product->description = array();
        $product->description_short = array();
        $product->link_rewrite = array();
        foreach (Language::getLanguages(false) as $lang) {
            $product->name[$lang['id_lang']] = $product_info['product_name'][$lang['id_lang']];
            $product->description[$lang['id_lang']] = $product_info['description'][$lang['id_lang']];
            $product->description_short[$lang['id_lang']] = $product_info['short_description'][$lang['id_lang']];
            $product->link_rewrite[$lang['id_lang']] = $product_info['link_rewrite'][$lang['id_lang']];
        }

        $product->id_shop_default = Context::getContext()->shop->id;
        $product->id_category_default = $category_id;
        $product->price = $product_info['price'];
        $product->active = $active;
        $product->indexed = 1;
        $product->condition = $product_info['condition'];

        // comment blow code because when any marketplace product will update, default tax group will be applied on that product
        /*$default_tax_rule_group = 1;
        $obj_tax = new TaxRulesGroup($default_tax_rule_group);
        if ($obj_tax->active == 0) {
            $product->id_tax_rules_group = 0;
        } else {
            $product->id_tax_rules_group = 1;
        }*/

        $product->save();
        $ps_product_id = $product->id;

        foreach (Language::getLanguages(false) as $lang) {
            Search::indexation($product_info['link_rewrite'][$lang['id_lang']], $ps_product_id);
        }

        if ($ps_product_id > 0) {
            if ($category_id > 0) {
                $category_ids = $this->getMultipleCategories($mp_product_id);
                $product->updateCategories($category_ids);
            }

            $combination_exist = 0;
            if (Module::isEnabled('mpcombination')) {
                $product_combi = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_product_attribute` WHERE `mp_id_product` = '.(int) $mp_product_id);
                if ($product_combi) {
                    $combination_exist = 1;
                }
            }

            if (!$combination_exist) {
                if ($quantity >= 0) {
                    StockAvailable::setQuantity($ps_product_id, 0, $quantity, $id_ps_shop);
                }
            }

            $image_list = $this->unactiveImage($mp_product_id);
            if ($image_list) {
                $have_cover = false;
                // if one of the other image is already have cover
                $images = Image::getImages($id_lang, $main_product_id);
                if ($images) {
                    foreach ($images as $img) {
                        if ($img['cover'] == 1) {
                            $have_cover = true;
                        }
                    }
                }

                foreach ($image_list as $image) {
                    $old_path = $image_dir.'/'.$image['seller_product_image_id'].'.jpg';
                    //$position = $count + 1;
                    $image_obj = new Image();
                    $image_obj->id_product = $ps_product_id;
                    $image_obj->position = Image::getHighestPosition($main_product_id) + 1;

                    if ($count == 0) {
                        if (!$have_cover) {
                            $image_obj->cover = 1;
                        }
                    } else {
                        $image_obj->cover = 0;
                    }

                    $image_obj->add();
                    $image_id = $image_obj->id;
                    $new_path = $image_obj->getPathForCreation();
                    $imagesTypes = ImageType::getImagesTypes('products');

                    foreach ($imagesTypes as $image_type) {
                        ImageManager::resize($old_path, $new_path.'-'.$image_type['name'].'.jpg', $image_type['width'], $image_type['height']);
                    }

                    ImageManager::resize($old_path, $new_path.'.jpg');
                    Hook::exec('actionWatermark', array('id_image' => $image_id, 'id_product' => $ps_product_id));
                    Hook::exec('actionPsMpImageMap', array('mp_product_id' => $mp_product_id, 'mp_id_image' => $image['id'], 'ps_id_product' => $ps_product_id, 'ps_id_image' => $image_id));
                    //updating mp_product_image status ...
                    MarketplaceProductImage::updateStatusAndPsIdImageById($image['id'], 1, $image_id);
                    $count = $count + 1;
                }
            }

            return $ps_product_id;
        }

        return false;
    }

    /**
     * [assignProductToSeller assign prestashop product to marketplace seller].
     * @param [int] $id_product  [prestashop id product]
     * @param [int] $id_customer [prestashop id customer]
     * @return [mp_id_product/false]
     */
    public function assignProductToSeller($id_product, $id_customer)
    {
        $id_lang = Context::getContext()->language->id;
        $mp_img_path = _PS_MODULE_DIR_.'marketplace/views/img/product_img/';

        $obj_mp_seller = new SellerInfoDetail();
        $obj_seller_product_category = new SellerProductCategory();
        $obj_mp_product_img = new MarketplaceProductImage();
        $mp_seller = $obj_mp_seller->getSellerDetailsByCustomerId($id_customer);

        if (!$mp_seller) {
            return false;
        }

        $id_seller = $mp_seller['id'];

        //get product details
        $obj_product = new Product($id_product);

        //insert into marketplace_seller_product table
        $obj_seller_product = new self();
        $obj_seller_product->id_seller = $id_seller;
        $obj_seller_product->price = $obj_product->price;
        $obj_seller_product->id_ps_product = $id_product;
        $obj_seller_product->quantity = StockAvailable::getQuantityAvailableByProduct($id_product);
        $obj_seller_product->id_category = $obj_product->id_category_default;
        $obj_seller_product->active = 1;
        $obj_seller_product->admin_assigned = 1;  // if product assigned by admin to seller
        $obj_seller_product->id_ps_shop = Context::getContext()->shop->id;

        foreach (Language::getLanguages(false) as $lang) {
            $obj_seller_product->product_name[$lang['id_lang']] = $obj_product->name[$lang['id_lang']];
            $obj_seller_product->description[$lang['id_lang']] = $obj_product->description[$lang['id_lang']];
            $obj_seller_product->short_description[$lang['id_lang']] = $obj_product->description_short[$lang['id_lang']];
            $obj_seller_product->link_rewrite[$lang['id_lang']] = $obj_product->link_rewrite[$lang['id_lang']];
        }

        $obj_seller_product->save();
        $id_mp_product = $obj_seller_product->id;

        if ($id_mp_product) {
            //get prestashop product categories
            $categories = $obj_product->getCategories();

            if (!$categories) {
                return false;
            }

            //save product categories in marketplace
            $obj_seller_product_category->id_seller_product = $id_mp_product;
            foreach ($categories as $category) {
                $obj_seller_product_category->id_category = $category;
                if ($category == $obj_product->id_category_default) {
                    $obj_seller_product_category->is_default = 1;
                } else {
                    $obj_seller_product_category->is_default = 0;
                }

                $obj_seller_product_category->add();
            }

            //upload prestashop product images to marketplace
            $images = $obj_product->getImages($id_lang);
            if ($images) {
                foreach ($images as $image) {
                    $rand_name = MpHelper::randomImageName();

                    // save to marketplace image table
                    $obj_mp_product_img->seller_product_id = (int) $id_mp_product;
                    $obj_mp_product_img->seller_product_image_id = pSQL($rand_name);
                    $obj_mp_product_img->active = 1;
                    $obj_mp_product_img->add();

                    $obj_image = new Image($image['id_image']);
                    $ps_img_path = _PS_PROD_IMG_DIR_.$obj_image->getImgPath().'.jpg';
                    ImageManager::resize($ps_img_path, $mp_img_path.$rand_name.'.jpg');
                }
            }

            Hook::exec(
                'actionAfterAssignProduct',
                array(
                    'id_seller' => $id_seller,
                    'id_product' => $id_product,
                    'mp_id_product' => $id_mp_product,
                )
            );

            return $id_mp_product;
        }

        return false;
    }

    //@id is marketplace product id
    public function getMarketPlaceProductInfo($id)
    {
        $marketplaceproductinfo = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product` where id ='.$id);

        if (!empty($marketplaceproductinfo)) {
            return $marketplaceproductinfo;
        } else {
            return false;
        }
    }

    public function getMarketPlaceProductLangInfo($id)
    {
        $marketplaceproductinfo = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product_lang` WHERE `id` = '.$id);

        if (!empty($marketplaceproductinfo)) {
            return $marketplaceproductinfo;
        } else {
            return false;
        }
    }

    //@id is marketplace product id
    public function getMarketPlaceProductCategories($id)
    {
        $seller_product_categories = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'marketplace_seller_product_category` where id_seller_product ='.$id);

        if (!empty($seller_product_categories)) {
            return $seller_product_categories;
        } else {
            return false;
        }
    }

    public function unactiveImage($id)
    {
        $unactive_image = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_product_image`
            WHERE seller_product_id = '.$id.' AND active = 0'
        );
        if (!empty($unactive_image)) {
            return $unactive_image;
        }

        return false;
    }

    public function getProductsByOrderId($id_order)
    {
        $product_list = Db::getInstance()->ExecuteS('SELECT `product_id`,`product_quantity` FROM `'._DB_PREFIX_.'order_detail` WHERE id_order='.$id_order.'');

        if ($product_list) {
            return $product_list;
        } else {
            return false;
        }
    }

    public function getSellerIdByProduct($mp_id_product)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_seller` FROM `'._DB_PREFIX_.'marketplace_seller_product`
			WHERE id = '.(int) $mp_id_product
        );
    }

    public function getCustomerIdBySellerId($id)
    {
        return Db::getInstance()->getValue('SELECT `id_customer` FROM `'._DB_PREFIX_.'marketplace_seller_info` WHERE `id`='.(int) $id);
    }

    public function getSellerInfo($id)
    {
        return Db::getInstance()->getRow('SELECT `firstname`,`lastname`,`email` FROM `'._DB_PREFIX_.'customer` WHERE `id_customer`='.(int) $id);
    }

    public function getProductInfo($id)
    {
        return Db::getInstance()->getRow('SELECT `name` FROM `'._DB_PREFIX_.'product_lang` WHERE `id_product`= '.(int) $id.' and `id_lang`=1');
    }

    public function getCustomerInfo($id)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE `id_customer`='.(int) $id);
    }

    public function getDeliverAddress($id)
    {
        return Db::getInstance()->getValue('SELECT `id_address_delivery` FROM `'._DB_PREFIX_.'orders` WHERE `id_order`='.(int) $id);
    }

    public function getShippingInfo($id)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'address` WHERE `id_address`='.$id.'');
    }

    public function getState($id)
    {
        return Db::getInstance()->getValue('SELECT `name` FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int) $id);
    }

    public function getCountry($id)
    {
        return Db::getInstance()->getValue('SELECT `name` FROM `'._DB_PREFIX_.'country_lang` WHERE `id_country`='.(int) $id.' and `id_lang`=1 ');
    }

    public function deleteMarketPlaceSellerProduct($id)
    {
        return Db::getInstance()->delete('marketplace_seller_product', 'id = '.(int) $id);
    }

    public function findAllActiveSellerProductByLimit($start_point = 0, $limit_point = 8, $order_by = 'desc', $id_lang)
    {
        $seller_product = Db::getInstance()->executeS(
            'SELECT mpsp.*, mpspl.*, mpsp.`id_ps_product` AS main_id_product
            FROM `'._DB_PREFIX_.'marketplace_seller_product` mpsp
            JOIN `'._DB_PREFIX_.'marketplace_seller_product_lang` mpspl ON (mpspl.`id` = mpsp.`id` AND mpspl.`id_lang` = '.(int)$id_lang.')
			WHERE mpsp.`active` = 1 ORDER BY mpsp.`id` '.$order_by.' LIMIT '.$start_point.','.$limit_point
        );

        if ($seller_product) {
            return $seller_product;
        }

        return false;
    }

    /**
     * [callMailFunction : mail when render action perform for product].
     * @param [type] $mp_product_id [mp product id]
     * @param [type] $subject       [mail subject]
     * @param bool   $mail_for      [1 active product, 2 deactive product, 3 delete product]
     * @return [bolean] [description]
     */
    public function callMailFunction($mp_product_id, $subject, $mail_for = false, $reason_text = false)
    {
        $obj_seller_prod_val = new self($mp_product_id);
        $mp_id_seller = $obj_seller_prod_val->id_seller;
        $obj_seller_val = new SellerInfoDetail($mp_id_seller);
        $id_lang = $obj_seller_val->default_lang;

        $obj_seller_product = new self($mp_product_id, $id_lang);

        if ($mail_for == 1) {
            $mail_reason = 'activé';
        } elseif ($mail_for == 2) {
            $mail_reason = 'refusé';
        } elseif ($mail_for == 3) {
            $mail_reason = 'supprimé';
        } else {
            $mail_reason = 'activé';
        }

        $product_name = $obj_seller_product->product_name;
        $id_category = $obj_seller_product->id_category;
        $id_ps_shop = $obj_seller_product->id_ps_shop;
        $quantity = $obj_seller_product->quantity;

        $obj_category = new Category($id_category, $id_lang);
        $category_name = $obj_category->name;

        $obj_seller = new SellerInfoDetail($mp_id_seller, $id_lang);
        $mp_seller_name = $obj_seller->seller_name;
        $mp_shop_name = $obj_seller->shop_name;
        $business_email = $obj_seller->business_email;
        if ($business_email == '') {
            $id_customer = $obj_seller->id_customer;
            $obj_customer = new Customer($id_customer);
            $business_email = $obj_customer->email;
        }

        $obj_shop = new Shop($id_ps_shop);
        $ps_shop_name = $obj_shop->name;

        $temp_path = _PS_MODULE_DIR_.'marketplace/mails/';
        $templateVars = array(
            '{seller_name}' => $mp_seller_name,
            '{product_name}' => $product_name,
            '{mp_shop_name}' => $mp_shop_name,
            '{mail_reason}' => $mail_reason,
            '{category_name}' => $category_name,
            '{quantity}' => $quantity,
            '{ps_shop_name}' => $ps_shop_name,
        );
        if ($reason_text && $reason_text != '') {
            $templateVars['{reason_text}'] = $reason_text;
        } else {
            $templateVars['{reason_text}'] = '';
        }

        if (Configuration::get('MP_SUPERADMIN_EMAIL')) {
            $admin_email = Configuration::get('MP_SUPERADMIN_EMAIL');
        } else {
            $obj_emp = new Employee(1);    //1 for superadmin
            $admin_email = $obj_emp->email;
        }

        $fromTitle = Configuration::get('MP_FROM_MAIL_TITLE');

        if ($subject == 1) {
            //Product Activated
            if (Configuration::get('MAIL_SELLER_PRODUCT_APPROVE')) {
                Mail::Send(
                    $id_lang,
                    'product_active',
                    Mail::l('Product Activated', $id_lang),
                    $templateVars,
                    $business_email,
                    $mp_seller_name,
                    $admin_email,
                    $fromTitle,
                    null,
                    null,
                    $temp_path,
                    false,
                    null,
                    null
                );
            }
        } elseif ($subject == 2) {
            //Product Deactivated
            if (Configuration::get('MAIL_SELLER_PRODUCT_DISAPPROVE')) {
                Mail::Send(
                    $id_lang,
                    'product_deactive',
                    Mail::l('Product Deactivated', $id_lang),
                    $templateVars,
                    $business_email,
                    $mp_seller_name,
                    $admin_email,
                    $fromTitle,
                    null,
                    null,
                    $temp_path,
                    false,
                    null,
                    null
                );
            }
        }

        return true;
    }

    /**
     * [findAllProductInMarketPlaceShop product with pagination].
     * @param [int] $id_seller [description]
     * @param [int] $p         [page number]
     * @param [int] $n         [Number of products per page]
     * @param bool  $orderby
     * @param bool  $orderway
     * @return [array]
     */
    public function findAllActiveProductInMarketPlaceShop($id_seller, $orderby = false, $orderway = false, $id_lang = false)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        if (!$orderway) {
            $orderway = 'desc';
        }

        $sql = 'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product` mslp
            JOIN `'._DB_PREFIX_.'marketplace_seller_product_lang` mslg ON (mslg.`id` = mslp.`id`)
            WHERE mslp.`id_seller` ='.$id_seller.'
            AND mslg.`id_lang`='.(int) $id_lang.'
            AND mslp.`active` = 1';
            //

        if (!$orderby) {
            $sql .= ' ORDER BY mslp.`id` '.$orderway;
        } else if ($orderby == 'name') {
            $sql .= ' ORDER BY mslg.`product_name` '.$orderway;
        } else {
            $sql .= ' ORDER BY mslp.`'.$orderby.'` '.$orderway;
        }

        return Db::getInstance()->executeS($sql);
    }

    public function getCustomerIdByMpIdProduct($mp_product_id)
    {
        return DB::getInstance()->getValue(
            'SELECT `seller_customer_id` FROM `'._DB_PREFIX_.'marketplace_seller_product` AS mpsp
			JOIN `'._DB_PREFIX_.'marketplace_seller_info` AS msi
			WHERE mpsp.`id_seller` = msi.`id`
			AND mpsp.`id` = '.(int) $mp_product_id
        );
    }

    public function changeSellerProductStatusBySellerProductId($seller_id_product, $status)
    {
        return Db::getInstance()->update('marketplace_seller_product', array('active' => $status), 'id='.$seller_id_product);
    }

    public function changeStatusAndPsIdProductByMpIdProduct($id, $status, $id_ps_product)
    {
        return Db::getInstance()->update('marketplace_seller_product', array('active' => $status, 'id_ps_product' => $id_ps_product), 'id='.$id);
    }

    public function getMpProductDetailsByPsProductId($id_product)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'marketplace_seller_product
            WHERE id_ps_product='.(int) $id_product
        );
    }

    public function mailToAdminOnProductAdd($product_name, $seller_name, $phone, $shop_name, $business_email_id)
    {
        $obj_emp = new Employee(1);    //1 for superadmin
        if (Configuration::get('MP_SUPERADMIN_EMAIL')) {
            $admin_email = Configuration::get('MP_SUPERADMIN_EMAIL');
        } else {
            $admin_email = $obj_emp->email;
        }

        $seller_vars = array(
            '{product_name}' => $product_name,
            '{seller_name}' => $seller_name,
            '{seller_shop}' => $shop_name,
            '{seller_email_id}' => $business_email_id,
            '{seller_phone}' => $phone,
        );

        $template_path = _PS_MODULE_DIR_.'/marketplace/mails/';
        Mail::Send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'mp_product_add',
            Mail::l('New product added', (int) Configuration::get('PS_LANG_DEFAULT')),
            $seller_vars,
            $admin_email,
            null,
            null,
            null,
            null,
            null,
            $template_path,
            false,
            null,
            null
        );
    }

    public function mailToAdminOnProductDelete($product_name, $active, $seller_name, $phone, $shop_name, $business_email_id)
    {
        $obj_emp = new Employee(1);    //1 for superadmin
        if (Configuration::get('MP_SUPERADMIN_EMAIL')) {
            $admin_email = Configuration::get('MP_SUPERADMIN_EMAIL');
        } else {
            $admin_email = $obj_emp->email;
        }

        if ($active == 0) {
            $product_status = 'Pending';
        } else {
            $product_status = 'Approved';
        }

        $seller_vars = array(
            '{product_name}' => $product_name,
            '{product_status}' => $product_status,
            '{seller_name}' => $seller_name,
            '{seller_shop}' => $shop_name,
            '{seller_email_id}' => $business_email_id,
            '{seller_phone}' => $phone,
        );

        $template_path = _PS_MODULE_DIR_.'/marketplace/mails/';
        Mail::Send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'mp_product_delete',
            Mail::l('Product Deleted', (int) Configuration::get('PS_LANG_DEFAULT')),
            $seller_vars,
            $admin_email,
            null,
            null,
            null,
            null,
            null,
            $template_path,
            false,
            null,
            null
        );
    }
}
