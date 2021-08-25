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

class MarketplaceProductUpdateModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if ($this->context->customer->id) {
            $idLang = $this->context->language->id;
            $mpIdProduct = Tools::getValue('id');
            $deleteProduct = Tools::getValue('deleteproduct');
            $edited = Tools::getValue('edited');

            $objMpProduct = new SellerProductDetail();
            $objMpCategory = new SellerProductCategory();
            $objMpSeller = new SellerInfoDetail();

            $seller = $objMpSeller->getSellerDetailsByCustomerId($this->context->customer->id);
            if ($seller && $seller['active']) {
                $idSeller = $seller['id'];

                $mpProduct = $objMpProduct->getMarketPlaceProductInfo($mpIdProduct);
                $mpProductLangs = $objMpProduct->getMarketPlaceProductLangInfo($mpIdProduct);
                if ($mpProductLangs) {
                    foreach ($mpProductLangs as $mpProductLang) {
                        $mpProduct['product_name'][$mpProductLang['id_lang']] = $mpProductLang['product_name'];
                        $mpProduct['short_description'][$mpProductLang['id_lang']] = $mpProductLang['short_description'];
                        $mpProduct['description'][$mpProductLang['id_lang']] = $mpProductLang['description'];
                    }
                }

                // If seller of current product and current seller customer is match
                if ($mpProduct['id_seller'] == $idSeller) {
                    
                    // If product uploaded successfully
                    if ($mpSuccess = Tools::getValue('mp_success')) {
                        $this->context->smarty->assign('product_upload', $mpSuccess);
                    }

                    // If delete product
                    if ($deleteProduct) {
                        // if seller delete product, delete process
                        $objMpSellerProduct = new SellerProductDetail($mpIdProduct);
                        if ($objMpSellerProduct->delete()) {
                            Tools::redirect($this->context->link->getModuleLink('marketplace', 'productlist', array('deleted' => 1)));
                        }
                    } elseif ($edited) {
                        // If seller updated the product, update process
                        $idMpProduct = Tools::getValue('id');
                        $price = Tools::getValue('product_price');
                        $quantity = Tools::getValue('product_quantity');
                        $categories = Tools::getValue('product_category');
                        $defaultCategory = Tools::getValue('default_category');
                        $condition = Tools::getValue('product_condition');
                        $imageName = Tools::getValue('image_name');

                        //If multi-lang is OFF then PS default lang will be default lang for seller
                        if (Configuration::get('MP_MULTILANG_ADMIN_APPROVE')) {
                            $defaultLang = Tools::getValue('seller_default_lang');
                        } else {
                            if (Configuration::get('MP_MULTILANG_DEFAULT_LANG') == '1') {//Admin default lang
                                $defaultLang = Configuration::get('PS_LANG_DEFAULT');
                            } elseif (Configuration::get('MP_MULTILANG_DEFAULT_LANG') == '2') {//Seller default lang
                                $defaultLang = Tools::getValue('seller_default_lang');
                            }
                        }

                        
                        // Check fields sizes
                        $className = 'SellerProductDetail';

                        // @TODO:: the call_user_func seems to contains only statics values 
                        $rules = call_user_func(array($className, 'getValidationRules'), $className);
                        $languages = Language::getLanguages();
                        foreach ($languages as $language) {
                            $value = Tools::getValue('product_name_'.$language['id_lang']);
                            if ($value && Tools::strlen($value) > $rules['sizeLang']['link_rewrite']) {
                                $this->errors[] = sprintf($this->module->l('The Product Name field is too long (%2$d chars max).', 'productupdate'),
                                    call_user_func(array($className, 'displayFieldName'), $className),
                                    $rules['sizeLang']['link_rewrite']);
                            }
                        }

                        if (!Tools::getValue('product_name_'.$defaultLang)) {
                            if (Configuration::get('MP_MULTILANG_ADMIN_APPROVE')) {
                                $sellerLang = Language::getLanguage((int) $defaultLang);
                                $this->errors[] = Tools::displayError($this->module->l('Product name is required in '.$sellerLang['name']));
                            } else {
                                $this->errors[] = Tools::displayError($this->module->l('Product name is required'));
                            }
                        } else {
                            //Validate data
                            $this->validateMpProductForm();

                            $objSellerProduct = new SellerProductDetail($idMpProduct);

                            Hook::exec('actionBeforeUpdateproduct', array('mp_product_id' => $idMpProduct));

                            if (!count($this->errors)) {
                                $objSellerProduct->price = $price;
                                $objSellerProduct->quantity = $quantity;
                                $objSellerProduct->id_category = $defaultCategory;
                                $objSellerProduct->condition = $condition;

                                foreach (Language::getLanguages(false) as $language) {
                                    $productIdLang = $language['id_lang'];
                                    $shortDescIdLang = $language['id_lang'];
                                    $descIdLang = $language['id_lang'];

                                    if (Configuration::get('MP_MULTILANG_ADMIN_APPROVE')) {
                                        //if product name in other language is not available then fill with seller language same for others
                                        if (!Tools::getValue('product_name_'.$language['id_lang'])) {
                                            $productIdLang = $defaultLang;
                                        }
                                        if (!Tools::getValue('short_description_'.$language['id_lang'])) {
                                            $shortDescIdLang = $defaultLang;
                                        }
                                        if (!Tools::getValue('product_description_'.$language['id_lang'])) {
                                            $descIdLang = $defaultLang;
                                        }
                                    } else {
                                        //if multilang is OFF then all fields will be filled as default lang content
                                        $productIdLang = $defaultLang;
                                        $shortDescIdLang = $defaultLang;
                                        $descIdLang = $defaultLang;
                                    }

                                    $objSellerProduct->product_name[$language['id_lang']] = Tools::getValue('product_name_'.$productIdLang);

                                    $objSellerProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite(Tools::getValue('product_name_'.$productIdLang));

                                    $objSellerProduct->short_description[$language['id_lang']] = Tools::getValue('short_description_'.$shortDescIdLang);

                                    $objSellerProduct->description[$language['id_lang']] = Tools::getValue('product_description_'.$descIdLang);
                                }

                                $objSellerProduct->save();

                                // Upload product images
                                if (!empty($imageName)) {
                                    $objMpImage = new MarketplaceProductImage();
                                    $objMpImage->uploadProductImage($imageName, $idMpProduct);
                                }

                                // Update new categories
                                Db::getInstance()->delete('marketplace_seller_product_category', 'id_seller_product = '.$idMpProduct);  //Delete previous

                                // Add new category into table
                                $objMpCategory->id_seller_product = $idMpProduct;
                                $objMpCategory->is_default = 1;

                                // Set if more than one category selected
                                $i = 0;
                                foreach ($categories as $category) {
                                    $objMpCategory->id_category = $category;
                                    if ($i != 0) {
                                        $objMpCategory->is_default = 0;
                                    }
                                    $objMpCategory->add();
                                    ++$i;
                                }

                                if ($objSellerProduct->active) {
                                    // Update also in prestashop if product is active
                                    $imageDir = _PS_MODULE_DIR_.'marketplace/views/img/product_img';
                                    $objSellerProduct->updatePsProductByMarketplaceProduct($idMpProduct, $imageDir, 1);
                                }

                                Hook::exec('actionUpdateproductExtrafield', array('marketplace_product_id' => $idMpProduct));

                                Tools::redirect($this->context->link->getModuleLink('marketplace', 'productupdate', array('id' => $idMpProduct, 'edited_conf' => 1)));
                            }
                        }
                    }

                    if (Tools::getValue('added')) {
                        $this->context->smarty->assign('added', 1);
                    }

                    Hook::exec('actionBeforeShowUpdatedProduct', array('marketplace_product_details' => $mpProduct));

                    // Image Details
                    $this->getProductImageDetails($mpProduct);

                    // Category tree
                    $checkedProductCategory = $objMpProduct->getMarketPlaceProductCategories($mpIdProduct);
                    $defaultIdCategory = $objMpCategory->getMpDefaultCategory($mpIdProduct);
                    $categoryTree = $objMpCategory->getCategoryTree($idLang, $checkedProductCategory, $defaultIdCategory);

                    // Default category
                    $idCategory = array();
                    foreach ($checkedProductCategory as $checkIdCategory) {
                        $idCategory[] = $checkIdCategory['id_category'];
                    }
                    $defaultCategory = Category::getCategoryInformations($idCategory, $idLang);

                    //Image Configuration
                    if ($mpProduct && $mpProduct['id_ps_product']) {
                        // display inactive images also, if uploaded in deactive products
                        if ($unactiveImage = $objMpProduct->unactiveImage($mpIdProduct)) {
                            $this->context->smarty->assign('unactive_image', $unactiveImage);
                        }
                        $this->context->smarty->assign('product_activated', 1);
                    } else {
                        if ($unactiveImageOnly = $objMpProduct->unactiveImage($mpIdProduct)) {
                            $this->context->smarty->assign('unactive_image_only', $unactiveImageOnly);
                        }
                    }

                    // Set default lang at every form according to configuration multi-language
                    MpHelper::assignDefaultLang($idSeller);

                    $this->defineJSVars();

                    $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                    $this->context->smarty->assign([
                        'mp_img_dir' => _MODULE_DIR_.'marketplace/views/img/',
                        'module_dir' => _MODULE_DIR_,
                        'default_cat' => $defaultCategory,
                        'defaultcatid' => $defaultIdCategory,
                        'pro_info' => $mpProduct,
                        'is_seller' => 1,
                        'logic' => 3,
                        'id' => $mpIdProduct,
                        'categoryTree' => $categoryTree,
                        'obj_default_currency' => $objDefaultCurrency,
                        'link' => $this->context->link,
                        'seller_default_lang' => $seller['default_lang'],
                        'title_text_color' => Configuration::get('MP_TITLE_TEXT_COLOR'),
                        'title_bg_color' => Configuration::get('MP_TITLE_BG_COLOR'),
                    ]);

                    $this->setTemplate('module:marketplace/views/templates/front/product/productupdate.tpl');
                } else {
                    Tools::redirect($this->context->link->getModuleLink('marketplace', 'dashboard'));
                }
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function validateMpProductForm()
    {
        $price = Tools::getValue('product_price');
        $quantity = Tools::getValue('product_quantity');
        $categories = Tools::getValue('product_category');
        $imageName = Tools::getValue('image_name');

        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            if (!Validate::isCatalogName(Tools::getValue('product_name_'.$language['id_lang']))) {
                $invalidName = 1;
            }

            if (Tools::getValue('short_description_'.$language['id_lang'])) {
                $shortDesc = Tools::getValue('short_description_'.$language['id_lang']);
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

            if (Tools::getValue('product_description_'.$language['id_lang'])) {
                if (!Validate::isCleanHtml(Tools::getValue('product_description_'.$language['id_lang']), (int) Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                    $invalidDesc = 1;
                }
            }
        }

        if (isset($invalidName)) {
            $this->errors[] = $this->module->l('Product name must not have Invalid characters', 'productupdate').' <>;=#{}';
        }

        if (isset($invalidSortDesc)) {
            if ($invalidSortDesc == 1) {
                $this->errors[] = $this->module->l('Short description have not valid data.', 'productupdate');
            } elseif ($invalidSortDesc == 2) {
                $this->errors[] = $this->module->l('This short description field is too long: ', 'productupdate').$limit.$this->module->l(' characters max.', 'productupdate');
            }
        }

        if (isset($invalidDesc)) {
            $this->errors[] = $this->module->l('Product description have not valid data', 'productupdate');
        }

        if ($price != '') {
            if (!Validate::isPrice($price)) {
                $this->errors[] = $this->module->l('product price should be numeric', 'productupdate');
            }
        } else {
            $this->errors[] = $this->module->l('Product price is required field.', 'productupdate');
        }

        if ($quantity != '') {
            if (!Validate::isUnsignedInt($quantity)) {
                $this->errors[] = $this->module->l('product quantity should be numeric', 'productupdate');
            }
        } else {
            $this->errors[] = $this->module->l('product quantity is required field', 'productupdate');
        }

        if (!$categories) {
            $this->errors[] = $this->module->l('You have not selected any category', 'productupdate');
        }

        //validate product images
        if (!empty($imageName)) {
            $this->validProductExt($imageName);
        }
    }

    public function defineJSVars()
    {
        $jsVars = [
                'backend_contrller' => 0,
                'req_prod_name' => $this->module->l('Product name is required in Default Language -', 'productupdate'),
                'char_prod_name' => $this->module->l('Product name should be in character of', 'productupdate'),
                'req_prod_name_other' => $this->module->l('Product name is required', 'productupdate'),
                'char_prod_name_other' => $this->module->l('Product name should be in character', 'productupdate'),
                'req_price' => $this->module->l('Product price is required.', 'productupdate'),
                'num_price' => $this->module->l('Product price should be numeric.', 'productupdate'),
                'req_qty' => $this->module->l('Product quantity is required.', 'productupdate'),
                'num_qty' => $this->module->l('Product quantity should be numeric.', 'productupdate'),
                'req_catg' => $this->module->l('Please select atleast one category.', 'productupdate'),
                'img_remove' => $this->module->l('Remove', 'productupdate'),
                'prev_img' => $this->module->l('Please select previous image', 'productupdate'),
                'path_uploader' => $this->context->link->getModulelink('marketplace', 'uploadcropimage'),
                'stop_img_upload' => $this->module->l('Image already selected', 'productupdate'),
                'imgformat_error' => $this->module->l('Image format not recognized, allowed formats are: .gif, .jpg, .png', 'productupdate'),
                'imgsize_error' => $this->module->l('Invalid image size. Minimum image size must be 200X200.', 'productupdate'),
                'imgbigsize_error' => $this->module->l('You have selected too big file, please select a one smaller image file less than 8MB', 'productupdate'),

                'mp_img_dir' => _MODULE_DIR_,
                'img_ps_dir' => _PS_IMG_DIR_,
                'ajax_urlpath' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
                'space_error' => $this->module->l('Space is not allowed.', 'productupdate'),
                'confirm_delete_msg' => $this->module->l('Do you want to delete the photo?', 'productupdate'),
                'delete_msg' => $this->module->l('Deleted.', 'productupdate'),
                'error_msg' => $this->module->l('An error occurred.', 'productupdate'),
                'iso' => $this->context->language->iso_code,
                'mp_tinymce_path' => _MODULE_DIR_.'marketplace/libs',
                'img_module_dir' => _MODULE_DIR_.'marketplace/views/img/',
                'product_img_path' => _MODULE_DIR_.'marketplace/views/img/uploadimage/',
            ];

        Media::addJsDef($jsVars);
    }

    public function getProductImageDetails($mpProduct)
    {
        if ($mpProduct && $mpProduct['id_ps_product']) {
            $product = new Product($mpProduct['id_ps_product'], false, $this->context->language->id);
            

			
			$images = $product->getImages($this->context->language->id);
            if ($images && !empty($images)) {
                foreach ($images as &$image) {
                    $objImage = new Image($image['id_image']);
                    $image['image_path'] = _THEME_PROD_DIR_.$objImage->getExistingImgPath().'.jpg';
                    $image['product_image'] = $mpProduct['id_ps_product'].'-'.$image['id_image'];
                }
            }

            $this->context->smarty->assign([
                'link_rewrite' => $product->link_rewrite,
                'image_detail' => $images,
                'id_product' => $mpProduct['id_ps_product']
            ]);
        }
    }

    public function validProductExt($images)
    {
        if (!empty($images)) {
            foreach ($images as $image) {
                if (!ImageManager::isCorrectImageFileExt($image)) {
                    $this->errors[] = $image['name'].Tools::displayError('Image format not recognized, allowed formats are: .gif, .jpg, .png', 'productupdate');
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            return true;
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Product Update', array(), 'Breadcrumb'),
            'url' => ''
        ];
        return $breadcrumb;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryPlugin('tablednd');
        $this->registerStylesheet('add_product', 'modules/'.$this->module->name.'/views/css/add_product.css');
        $this->registerStylesheet('marketplace_account', 'modules/'.$this->module->name.'/views/css/marketplace_account.css');
        $this->registerStylesheet('mp-uploadphoto', 'modules/'.$this->module->name.'/views/css/uploadphoto.css');
        $this->registerStylesheet('mp-jcrop', 'modules/'.$this->module->name.'/views/css/jquery.Jcrop.min.css');


        $this->registerJavascript('mp_form_validation', 'modules/'.$this->module->name.'/views/js/mp_form_validation.js');
        $this->registerJavascript('uploaderscript', 'modules/'.$this->module->name.'/views/js/uploaderscript.js');
        $this->registerJavascript('mp-jcrop', 'modules/'.$this->module->name.'/views/js/jquery.Jcrop.min.js');
        $this->registerJavascript('product_multilang', 'modules/'.$this->module->name.'/views/js/product_multilang.js');
        $this->registerJavascript('mp-imageedit', 'modules/'.$this->module->name.'/views/js/imageedit.js');

        //Category tree
        $this->registerJavascript('mp-jquery-ui-1.8.12-js', 'modules/'.$this->module->name.'/views/js/categorytree/jquery-ui-1.8.12.custom/js/jquery-ui-1.11.4.custom.min.js');
        $this->registerStylesheet('mp-jquery-ui-1.8.12-css', 'modules/'.$this->module->name.'/views/js/categorytree/jquery-ui-1.8.12.custom/css/smoothness/jquery-ui-1.8.12.custom.css');
        $this->registerJavascript('mp-checkboxtree', 'modules/'.$this->module->name.'/views/js/categorytree/jquery.checkboxtree.js');
        $this->registerStylesheet('mp-checkbox', 'modules/'.$this->module->name.'/views/js/categorytree/wk.checkboxtree.css');
    }
}
