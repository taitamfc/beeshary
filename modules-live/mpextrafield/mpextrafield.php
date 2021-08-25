<?php
/**
 * 2010-2017 Webkul
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
 *  @copyright 2010-2017 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__).'/classes/MarketplaceExtraFieldClasses.php';
class MpExtraField extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public $doc_type = array('pdf', 'doc', 'docx', 'xlsx', 'zip'); // file format
    public $img_type = array('gif', 'png', 'jpg', 'jpeg');  // image format
    public function __construct()
    {
        $this->name = 'mpextrafield';
        $this->tab = 'front_office_features';
        $this->version = '5.0.1';
        $this->author = 'Webkul';
        $this->need_instance = 1;
        $this->dependencies = array('marketplace');

        parent::__construct();
        $this->displayName = $this->l('Marketplace Custom Field');
        $this->description = $this->l('Add custom field to marketplace');
    }

    public function callInstallTab()
    {
        $this->installTab('AdminAddextrafield', 'Manage Custom Field', 'AdminMarketplaceManagement');

        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }
        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function callAssociateModuleToShop()
    {
        $module_id = Module::getModuleIdByName($this->name);
        Configuration::updateValue('MPEXTRAFIELD_MODULE_ID', $module_id);
        return true;
    }

    /**
     * [insertPredefineInputType -> prefine input type for admin].
     *
     * @return [type] [description]
     */
    public function insertPredefineInputType()
    {
        $obj_inputtype = new MarketplaceExtrafieldInputtype();
        $obj_inputtype->insertMpExtraFieldAssoc('Text');
        $obj_inputtype->insertMpExtraFieldAssoc('Textarea');
        $obj_inputtype->insertMpExtraFieldAssoc('Dropdown');
        $obj_inputtype->insertMpExtraFieldAssoc('Checkbox');
        $obj_inputtype->insertMpExtraFieldAssoc('File Type');
        $obj_inputtype->insertMpExtraFieldAssoc('Radio Button');

        return true;
    }

    /**
     * [insertPredefineInputValidationType -> prefine validation for input fields].
     *
     * @return [type] [description]
     */
    public function insertPredefineInputValidationType()
    {
        $obj_inputtype = new MarketplaceExtrafieldInputtype();
        $obj_inputtype->insertMpExtraFieldValidation('Text');
        $obj_inputtype->insertMpExtraFieldValidation('Email');
        $obj_inputtype->insertMpExtraFieldValidation('Number');

        return true;
    }

    /**
     * [hookDisplayMpAddProductFooter -> display extra field below the add product].
     *
     * @return [type] [description]
     */
    public function hookDisplayMpAddProductFooter($params)
    {
        $extrafielddetailarray = $this->displayExtraFieldOnAddPage(1);
        $this->context->smarty->assign('extrafielddetail', $extrafielddetailarray);
        if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
                return $this->display(__FILE__, 'adminproductextrafield.tpl');
        } else {
            $id_customer = $this->context->customer->id;
            $sellerDetail = WkMpSeller::getSellerDetailByCustomerId($id_customer);
            if ($sellerDetail) {
                WkMpHelper::assignDefaultLang($sellerDetail['id_seller']);
                //SellerInfoDetail
                return $this->fetch('module:mpextrafield/views/templates/hook/productextrafield.tpl');
            }
        }
    }

    public function displayExtraFieldWithLangDropDown()
    {
        $this->context->smarty->assign('languages', Language::getLanguages());
        $this->context->smarty->assign('total_languages', count(Language::getLanguages()));
        $this->context->smarty->assign('current_lang', Language::getLanguage((int)Configuration::get('PS_LANG_DEFAULT')));
        $this->context->smarty->assign('multi_lang', Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE'));
        $this->context->smarty->assign('multi_def_lang_off', Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG'));
    }

    /**
     * [hookActionBeforeAddMPProduct -> validating all the extra field before saving into database].
     *
     * @return [type] [description]
     */
    public function hookActionBeforeAddMPProduct()
    {
        $this->validateExtraFieldBeforeSubmit(1);
    }

    /**
     * [hookActionAfterAddMPProduct -> save extra field information entered by seller at product add page].
     *
     * @param [type] $params [product details]
     *
     * @return [type] [description]
     */
    public function hookActionAfterAddMPProduct($params)
    {
        $marketplace_product_id = $params['id_mp_product'];
        $mp_seller_product = WkMpSellerProduct::getSellerProductByIdProduct($marketplace_product_id);
        $is_for_shop = 0;
        if ($mp_seller_product) {
            $mp_id_shop = $mp_seller_product['id_mp_product'];
            $mp_id_seller = $mp_seller_product['id_seller'];
        } else {
            $mp_id_shop = 0;
        }

        $obj_extrafield = new MarketplaceExtrafield();
        $extrafielddetail = $obj_extrafield->findActiveExtraAttributeDetailByPage(1, $this->context->language->id);
        if ($extrafielddetail) {
            foreach ($extrafielddetail as $extrafi) {
                $extrafield_id = $extrafi['id'];
                $attribute_name = $extrafi['attribute_name'];

                $field_value = Tools::getValue($attribute_name);
                $inputtype = $extrafi['inputtype'];
                if (is_array($field_value)) {                    
                    $field_value = implode(',', $field_value);
                } else {
                    $field_value = trim($field_value);
                }

                if ($inputtype == 1 || $inputtype == 2) {
                    $this->insertExtraFieldValue($extrafield_id, $attribute_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                } else if ($inputtype == 3 && !empty($field_value)) {
                    $this->insertExtraFieldValue($extrafield_id, $field_value, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                } elseif ($inputtype == 4 && !empty($field_value)) {
                    $this->insertExtraFieldValue($extrafield_id, $field_value, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                } elseif ($inputtype == 6 && !empty($field_value)) {
                    $this->insertExtraFieldValue($extrafield_id, $field_value, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                } elseif ($inputtype == 5 && !empty($_FILES[$attribute_name]['tmp_name'])) {
                    if (is_uploaded_file($_FILES[$attribute_name]['tmp_name'])) {
                        $file = $_FILES[$attribute_name];
                        $pathinfo = pathinfo($file['name']);
                        $img_name = $attribute_name.'_'.$mp_id_seller.'_'.$marketplace_product_id.'.'.$pathinfo['extension'];
                        $path = _PS_MODULE_DIR_.'mpextrafield/views/img/'.$attribute_name.'_'.$mp_id_seller.'_'.$marketplace_product_id.'.'.$pathinfo['extension'];
                        if ($extrafi['file_type'] == 1) {
                            if (is_array($file) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($file['tmp_name'], $tmp_name)) {
                                if (ImageManager::resize($tmp_name, dirname(__FILE__).'/views/img/'.$img_name)) {
                                    $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                }
                            }
                        }

                        if ($extrafi['file_type'] == 2) {
                            if (in_array($pathinfo['extension'], $this->doc_type)) {
                                if (move_uploaded_file($_FILES[$attribute_name]['tmp_name'], $path)) {
                                    $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                }
                            }
                        }

                        if ($extrafi['file_type'] == 3) {
                            if (in_array($pathinfo['extension'], $this->doc_type)) {
                                if (move_uploaded_file($_FILES[$attribute_name]['tmp_name'], $path)) {
                                    $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                }
                            }

                            if (in_array($pathinfo['extension'], $this->img_type)) {
                                if (is_array($file) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($file['tmp_name'], $tmp_name)) {
                                    if (ImageManager::resize($tmp_name, dirname(__FILE__).'/views/img/'.$img_name)) {
                                        $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * [hookDisplayMpUpdateProductFooter -> display extra field on update product page].
     *
     * @return [type] [description]
     */
    public function hookDisplayMpUpdateProductFooter()
    {
        $marketplace_product_id = Tools::getValue('id_mp_product');
        $mp_seller_product = WkMpSellerProduct::getSellerProductByIdProduct($marketplace_product_id);
        if ($mp_seller_product) {
            $mp_id_shop = $mp_seller_product['id_mp_product'];
            $mp_id_seller = $mp_seller_product['id_seller'];
        } else {
            $mp_id_shop = 0;
        }
        $is_for_shop = 0;
        $obj_extrafield = new MarketplaceExtrafield();
        $extrafield = $obj_extrafield->findActiveExtraAttributeDetailByPage(1, $this->context->language->id);
        $extrafielddetailarray = $this->displayExtraFieldOnUpdatePage(1, $marketplace_product_id, $mp_id_shop, $mp_id_seller, $is_for_shop);
        $this->context->smarty->assign(array(
            'extrafielddetail' => $extrafielddetailarray,
            'extrafieldvalue' => $extrafield,
            'controller' => Tools::getValue('controller'),
            'id' => $marketplace_product_id
            ));
        if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
            $this->context->smarty->assign(array(
                'front_path' => $this->context->link->getModuleLink('mpextrafield', 'mediadownload'),
                ));

            return $this->display(__FILE__, 'adminupdateproductextrafield.tpl');
        } else {
            return $this->fetch('module:mpextrafield/views/templates/hook/updateproductextrafield.tpl');
        }
    }

    /**
     * [hookActionBeforeUpdateMPProduct -> validating all the extra field before saving into database].
     *
     * @return [type] [description]
     */
    public function hookActionBeforeUpdateMPProduct()
    {
        $this->validateExtraFieldBeforeSubmit(1);
    }

    /**
     * [hookActionAfterUpdateMPProduct -> save extra field information changed by seller at product update page].
     *
     * @param [type] $params [product details]
     *
     * @return [type] [description]
     */
    public function hookActionAfterUpdateMPProduct($params)
    {
        $marketplace_product_id = $params['id_mp_product'];
        $mp_seller_product = WkMpSellerProduct::getSellerProductByIdProduct($marketplace_product_id);
        $is_for_shop = 0;
        if ($mp_seller_product) {
            $mp_id_shop = $mp_seller_product['id_mp_product'];
            $mp_id_seller = $mp_seller_product['id_seller'];
        } else {
            $mp_id_shop = 0;
        }

        $obj_extrafield = new MarketplaceExtrafield();
        $extrafielddetail = $obj_extrafield->findActiveExtraAttributeDetailByPage(1, $this->context->language->id);
        $obj_extrafield_value = new MarketplaceExtrafieldValue();
        if ($extrafielddetail) {
            foreach ($extrafielddetail as $extrafi) {
                $extrafield_id = $extrafi['id'];
                $attribute_name = $extrafi['attribute_name'];
                $field_value = Tools::getValue($attribute_name);
                $inputtype = $extrafi['inputtype'];
                if (is_array($field_value)) {
                    $field_value = implode(',', $field_value);
                } else {
                    $field_value = trim($field_value);
                }

                $extrafield_value = $obj_extrafield_value->findExtrafieldValue($extrafield_id, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                $extrafield_value_id = $extrafield_value['id'];
                if ($inputtype == 1 || $inputtype == 2) {
                    if ($extrafield_value_id) {
                        $this->updateExtrafieldValueByid($extrafield_value_id, $attribute_name, $inputtype);
                    } else {
                        $this->insertExtraFieldValue($extrafield_id, $attribute_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                    }
                } elseif ((($inputtype == 3 || $inputtype == 4 || $inputtype == 6) && ($extrafi['field_req'] ? !empty($field_value) : true))) {

                    if ($extrafield_value_id) {
                        $this->updateExtrafieldValueByid($extrafield_value_id, $field_value);
                    } else {
                        $this->insertExtraFieldValue($extrafield_id, $field_value, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                    }
                } elseif ($inputtype == 5 && !empty($_FILES[$attribute_name]['tmp_name'])) {
                    if (is_uploaded_file($_FILES[$attribute_name]['tmp_name'])) {
                        $file = $_FILES[$attribute_name];
                        $pathinfo = pathinfo($file['name']);
                        $img_name = $attribute_name.'_'.$mp_id_seller.'_'.$marketplace_product_id.'.'.$pathinfo['extension'];
                        $path = _PS_MODULE_DIR_.'mpextrafield/views/img/'.$attribute_name.'_'.$mp_id_seller.'_'.$marketplace_product_id.'.'.$pathinfo['extension'];
                        if ($extrafi['file_type'] == 1) {
                            if (is_array($file) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($file['tmp_name'], $tmp_name)) {
                                if (ImageManager::resize($tmp_name, dirname(__FILE__).'/views/img/'.$img_name)) {
                                    if ($extrafield_value_id) {
                                        $this->updateExtrafieldValueByid($extrafield_value_id, $img_name);
                                    } else {
                                        $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                    }
                                }
                            }
                        }

                        if ($extrafi['file_type'] == 2) {
                            $temp = explode('.', $_FILES[$attribute_name]['name']);
                            $extension = end($temp);
                            if (in_array($extension, $this->doc_type)) {
                                if (move_uploaded_file($_FILES[$attribute_name]['tmp_name'], $path)) {
                                    if ($extrafield_value_id) {
                                        $this->updateExtrafieldValueByid($extrafield_value_id, $img_name);
                                    } else {
                                        $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                    }
                                }
                            }
                        }

                        if ($extrafi['file_type'] == 3) {
                            if (in_array($pathinfo['extension'], $this->doc_type)) {
                                if (move_uploaded_file($_FILES[$attribute_name]['tmp_name'], $path)) {
                                    if ($extrafield_value_id) {
                                        $this->updateExtrafieldValueByid($extrafield_value_id, $img_name);
                                    } else {
                                        $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                    }
                                }
                            }

                            if (in_array($pathinfo['extension'], $this->img_type)) {
                                if (is_array($file) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($file['tmp_name'], $tmp_name)) {
                                    if (ImageManager::resize($tmp_name, dirname(__FILE__).'/views/img/'.$img_name)) {
                                        if ($extrafield_value_id) {
                                            $this->updateExtrafieldValueByid($extrafield_value_id, $img_name);
                                        } else {
                                            $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * [insertExtraFieldValue -> inserting values into database].
     *
     * @param [type] $extrafield_id          [Field id]
     * @param [type] $field_value            [Field value]
     * @param [type] $mp_id_shop             [Shop id]
     * @param [type] $mp_id_seller           [Seller id]
     * @param [type] $marketplace_product_id [Marketplace Product Id]
     * @param [type] $is_for_shop            [field values are of shop or not]
     *
     * @return [type] [description]
     */
    public function insertExtraFieldValue($extrafield_id, $field_value, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop)
    {
        $obj_extrafield_value = new MarketplaceExtrafieldValue();
        $obj_extrafield_value->extrafield_id = $extrafield_id;
        $obj_extrafield_value->marketplace_product_id = $marketplace_product_id;

        $extraFielsDetail = MarketplaceExtrafield::getExtraFieldDetailById($extrafield_id);
        if ($extraFielsDetail['inputtype'] == 1 || $extraFielsDetail['inputtype'] == 2) {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
                    $default_lang = Tools::getValue('seller_default_lang');
                } else {
                    $default_lang = Tools::getValue('default_lang');
                }
            } else {
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin default lang
                    $default_lang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller default lang
                    if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
                        $default_lang = Tools::getValue('seller_default_lang');
                    } else {
                        $default_lang = Tools::getValue('default_lang');
                    }
                }
            }
            $defaultFieldValue = Tools::getValue($field_value.'_'.$default_lang);
            if (($defaultFieldValue && $extraFielsDetail['field_req']) || (!$defaultFieldValue && !$extraFielsDetail['field_req']) || !$extraFielsDetail['asplaceholder']) {
                foreach (Language::getLanguages(false) as $language) {
                    $field_value_lang_id = $language['id_lang'];

                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        if (!Tools::getValue($field_value.'_'.$language['id_lang'])) {
                            $field_value_lang_id = $default_lang;
                        }
                    } else {
                        //if multilang is OFF then all fields will be filled as default lang content
                        $field_value_lang_id = $default_lang;
                    }
                    $obj_extrafield_value->field_val[$language['id_lang']] = Tools::getValue($field_value.'_'.$field_value_lang_id);
                }
                $obj_extrafield_value->mp_id_seller = $mp_id_seller;
                $obj_extrafield_value->mp_id_shop = $mp_id_shop;
                $obj_extrafield_value->is_for_shop = $is_for_shop;
                $obj_extrafield_value->save();
                //unset($obj_extrafield_value);
            }
        } else {
            $obj_extrafield_value->field_value = $field_value;
            $obj_extrafield_value->mp_id_seller = $mp_id_seller;
            $obj_extrafield_value->mp_id_shop = $mp_id_shop;
            $obj_extrafield_value->is_for_shop = $is_for_shop;
            $obj_extrafield_value->save();
        }

    }

    /**
     * [updateExtrafieldValueByid -> updating the field values into database].
     *
     * @param [type] $extrafield_value_id [unique id of field value]
     * @param [type] $field_value         [field value]
     *
     * @return [type] [description]
     */
    public function updateExtrafieldValueByid($extrafield_value_id, $field_value, $inputtype = false)
    {
        $obj_extrafield_value = new MarketplaceExtrafieldValue($extrafield_value_id);

        if ($inputtype) {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
                    $default_lang = Tools::getValue('seller_default_lang');
                } else {
                    $default_lang = Tools::getValue('default_lang');
                }
            } else {
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin default lang
                    $default_lang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller default lang
                    if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
                        $default_lang = Tools::getValue('seller_default_lang');
                    } else {
                        $default_lang = Tools::getValue('default_lang');
                    }
                }
            }
            foreach (Language::getLanguages(false) as $language) {
                $field_value_lang_id = $language['id_lang'];

                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    if (!Tools::getValue($field_value.'_'.$language['id_lang'])) {
                        $field_value_lang_id = $default_lang;
                    }
                } else {
                    //if multilang is OFF then all fields will be filled as default lang content
                    $field_value_lang_id = $default_lang;
                }
                $obj_extrafield_value->field_val[$language['id_lang']] = Tools::getValue($field_value.'_'.$field_value_lang_id);
            }
            $obj_extrafield_value->field_value = Tools::getValue($field_value.'_1');
            $obj_extrafield_value->save();
        } else {
            $obj_extrafield_value->field_value = $field_value;
            $obj_extrafield_value->save();
        }

    }

    /**
     * [hookDisplayMpSellerRequestFooter -> display extra field below the seller request page].
     *
     * @return [type] [description]
     */
    public function hookDisplayMpSellerRequestFooter()
    {
        $extrafielddetailarray = $this->displayExtraFieldOnAddPage(2);
        $this->context->smarty->assign('extrafielddetail', $extrafielddetailarray);
        if (Tools::getValue('controller') == 'sellerrequest') {
            return $this->fetch('module:mpextrafield/views/templates/hook/productextrafield.tpl');
        }
    }

    /**
     * [hookActionBeforeAddSeller -> validating all the extra field before saving into database].
     *
     * @return [type] [description]
     */
    public function hookActionBeforeAddSeller()
    {
        $this->validateExtraFieldBeforeSubmit(2);
    }

    /**
     * [hookActionAfterAddSeller -> save the extra field values into database from the seller request page].
     *
     * @param [type] $params [seller information]
     *
     * @return [type] [description]
     */
    public function hookActionAfterAddSeller($params)
    {
        $mp_id_seller = $params['id_seller'];
        $is_for_shop = 1;
        $marketplace_product_id = 0;
        $mp_id_shop = $mp_id_seller;
        $obj_extrafield = new MarketplaceExtrafield();
        $obj_extrafield_value = new MarketplaceExtrafieldValue();
        $extrafielddetail = $obj_extrafield->findActiveExtraAttributeDetailByPage(2, $this->context->language->id);
        if ($extrafielddetail) {
            foreach ($extrafielddetail as $extrafi) {
                $extrafield_id = $extrafi['id'];
                $attribute_name = $extrafi['attribute_name'];
                $field_value = Tools::getValue($attribute_name);
                $inputtype = $extrafi['inputtype'];
                if (is_array($field_value)) {
                    $field_value = implode(',', $field_value);
                } else {
                    $field_value = trim($field_value);
                }

                if ($inputtype == 1 || $inputtype == 2) {
                    $this->insertExtraFieldValue($extrafield_id, $attribute_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                } elseif ((($inputtype == 3 || $inputtype == 4 || $inputtype == 6) && ($extrafi['field_req'] ? !empty($field_value) : true))) {
                    $this->insertExtraFieldValue($extrafield_id, $field_value, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                } elseif ($inputtype == 5 && !empty($_FILES[$attribute_name]['tmp_name'])) {
                    if (is_uploaded_file($_FILES[$attribute_name]['tmp_name'])) {
                        $file = $_FILES[$attribute_name];
                        $pathinfo = pathinfo($file['name']);
                        $img_name = $attribute_name.'_'.$mp_id_seller.'.'.$pathinfo['extension'];
                        $path = _PS_MODULE_DIR_.'mpextrafield/views/img/'.$attribute_name.'_'.$mp_id_seller.'.'.$pathinfo['extension'];
                        if ($extrafi['file_type'] == 1) {
                            if (is_array($file) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($file['tmp_name'], $tmp_name)) {
                                if (ImageManager::resize($tmp_name, dirname(__FILE__).'/views/img/'.$img_name)) {
                                    $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                }
                            }
                        }

                        if ($extrafi['file_type'] == 2) {
                            if (in_array($pathinfo['extension'], $this->doc_type)) {
                                if (move_uploaded_file($_FILES[$attribute_name]['tmp_name'], $path)) {
                                    $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                }
                            }
                        }

                        if ($extrafi['file_type'] == 3) {
                            if (in_array($pathinfo['extension'], $this->doc_type)) {
                                if (move_uploaded_file($_FILES[$attribute_name]['tmp_name'], $path)) {
                                    $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                }
                            }

                            if (in_array($pathinfo['extension'], $this->img_type)) {
                                if (is_array($file) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($file['tmp_name'], $tmp_name)) {
                                    if (ImageManager::resize($tmp_name, dirname(__FILE__).'/views/img/'.$img_name)) {
                                        $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * [hookDisplayMpEditProfileInformationBottom -> display extra field on update shop/seller profile page].
     *
     * @return [type] [description]
     */
    public function hookDisplayMpEditProfileInformationBottom()
    {

        if (('AdminSellerInfoDetail' === Tools::getValue('controller') && !Tools::getValue('id_seller'))) {
            $extrafielddetailarray = $this->displayExtraFieldOnAddPage(2);
            $this->context->smarty->assign('extrafielddetail', $extrafielddetailarray);
            return $this->display(__FILE__, 'adminproductextrafield.tpl');
        } else {
            if (Tools::getValue('id_seller')) {
                $mp_id_seller = Tools::getValue('id_seller');
                $mp_id_shop = Tools::getValue('id_seller');
            } else {
                $id_customer = $this->context->customer->id;
                $mp_seller_info = WkMpSeller::getSellerDetailByCustomerId($id_customer);
                $mp_id_seller = $mp_seller_info['id_seller'];
                $mp_id_shop = $mp_seller_info['id_seller'];
            }
            $is_for_shop = 1;
            $marketplace_product_id = 0;
            $obj_extrafield = new MarketplaceExtrafield();
            $extrafield = $obj_extrafield->findActiveExtraAttributeDetailByPage(2, $this->context->language->id);
            $extrafielddetailarray = $this->displayExtraFieldOnUpdatePage(2, $marketplace_product_id, $mp_id_shop, $mp_id_seller, $is_for_shop);
            /* EDIT 27-04-21 by Claire */ 
            $default_value = '';
            foreach ($extrafielddetailarray as $key => $value) {
                if ($value['attribute_name'] == 'spoken_langs') {
                    $default_value = $value['default_value'];
                }
            }
            $default_value = explode(',', $default_value);
            $this->context->smarty->assign(array(
                'extrafielddetail' => $extrafielddetailarray,
                'extrafieldvalue' => $extrafield,
                'id_lang' => $this->context->language->id,
                'id' => $mp_id_shop,
                'get_proverbs' => self::getProverbs(),
                'sl_spoken_langs' => $default_value
            ));
            /*---end claire---------*/
            if (Tools::getValue('controller') == 'AdminSellerInfoDetail') {
                $this->context->smarty->assign(array(
                    'seller_id' => $mp_id_seller,
                    'front_path' => $this->context->link->getModuleLink('mpextrafield', 'mediadownload'),
                    'controller' => 'sellerprofile',
                    ));

                return $this->display(__FILE__, 'adminupdateproductextrafield.tpl');
            } else {
                return $this->fetch('module:mpextrafield/views/templates/hook/updateproductextrafield.tpl');
            }
        }
    }

    public static function getProverbs()
    {
        return [
            "à bon vin point d'enseigne",
            "à chaque jour suffit sa peine",
            "à chaque problème, une solution",
            "à l'œuvre on connaît l'artisan",
            "bien faire, et laisser dire",
            "c'est dans les vieux pots qu'on fait la meilleure soupe",
            "c'est en forgeant qu'on devient forgeron",
            "ce que femme veut, Dieu le veut",
            "ce qui ne tue pas rend plus fort",
            "il n'est point de sot métier",
            "il n'y a que le premier pas qui coûte",
            "l'argent est un bon serviteur et un mauvais maître",
            "l'erreur est humaine",
            "l'habit ne fait pas le moine",
            "la critique est aisée mais l'art est difficile",
            "la fortune sourit aux audacieux",
            "la parole est d'argent et le silence est d'or",
            "Le bon vivant n'est pas celui qui mange beaucoup, mais celui qui goûte avec bonheur à toutes les formes de la vie",
            "Le bonheur n'est vrai que quand il est partagé",
            "les petits ruisseaux font les grandes rivières",
            "Ne remets pas à demain ce que tu peux faire aujourd'hui",
            "Plaisir non partagé n'est plaisir qu'à moitié",
            "Savoir partager son temps, c'est savoir jouir de la vie",
            "Un brin de folie égaye la vie",
        ];
    }

    /**
     * [hookActionBeforeUpdateSeller -> validating all the extra field before saving into database].
     *
     * @return [type] [description]
     */
    public function hookActionBeforeUpdateSeller($param)
    {
        $this->validateExtraFieldBeforeSubmit(2, $param['id_seller']);
    }

    /**
     * [hookActionAfterUpdateSeller -> save extra field values into database from update shop page].
     *
     * @param [type] $params [description]
     *
     * @return [type] [description]
     */
    public function hookActionAfterUpdateSeller($params)
    {
        $mp_id_seller = $params['id_seller'];
        $is_for_shop = 1;
        $marketplace_product_id = 0;
        $mp_id_shop = $mp_id_seller;

        $obj_extrafield = new MarketplaceExtrafield();
        $obj_extrafield_value = new MarketplaceExtrafieldValue();
        $extrafielddetail = $obj_extrafield->findActiveExtraAttributeDetailByPage(2, $this->context->language->id);
        if ($extrafielddetail) {
            foreach ($extrafielddetail as $extrafi) {
                $extrafield_id = $extrafi['id'];
                $attribute_name = $extrafi['attribute_name'];
                $field_value = Tools::getValue($attribute_name);
                $inputtype = $extrafi['inputtype'];
                if (is_array($field_value)) {
                    $field_value = implode(',', $field_value);
                } else {
                    $field_value = trim($field_value);
                }

                $extrafield_value = $obj_extrafield_value->findExtrafieldValue($extrafield_id, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                $extrafield_value_id = $extrafield_value['id'];
                if ($inputtype == 1 || $inputtype == 2) {
                    if ($extrafield_value_id) {
                        $this->updateExtrafieldValueByid($extrafield_value_id, $attribute_name, $inputtype);
                    } else {
                        $this->insertExtraFieldValue($extrafield_id, $attribute_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                    }
                } elseif ((($inputtype == 3 || $inputtype == 4 || $inputtype == 6) && ($extrafi['field_req'] ? !empty($field_value) : true))) {
                    if ($extrafield_value_id) {
                        $this->updateExtrafieldValueByid($extrafield_value_id, $field_value);
                    } else {
                        $this->insertExtraFieldValue($extrafield_id, $field_value, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                    }
                } elseif ($inputtype == 5 && !empty($_FILES[$attribute_name]['tmp_name'])) {
                    if (is_uploaded_file($_FILES[$attribute_name]['tmp_name'])) {
                        $file = $_FILES[$attribute_name];
                        $pathinfo = pathinfo($file['name']);
                        $img_name = $attribute_name.'_'.$mp_id_seller.'.'.$pathinfo['extension'];
                        $path = _PS_MODULE_DIR_.'mpextrafield/views/img/'.$attribute_name.'_'.$mp_id_seller.'.'.$pathinfo['extension'];
                        if ($extrafi['file_type'] == 1) {
                            if (is_array($file) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($file['tmp_name'], $tmp_name)) {
                                if (ImageManager::resize($tmp_name, dirname(__FILE__).'/views/img/'.$img_name)) {
                                    if ($extrafield_value_id) {
                                        $this->updateExtrafieldValueByid($extrafield_value_id, $img_name);
                                    } else {
                                        $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                    }
                                }
                            }
                        }

                        if ($extrafi['file_type'] == 2) {
                            if (in_array($pathinfo['extension'], $this->doc_type)) {
                                if (move_uploaded_file($_FILES[$attribute_name]['tmp_name'], $path)) {
                                    if ($extrafield_value_id) {
                                        $this->updateExtrafieldValueByid($extrafield_value_id, $img_name);
                                    } else {
                                        $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                    }
                                }
                            }
                        }

                        if ($extrafi['file_type'] == 3) {
                            if (in_array($pathinfo['extension'], $this->doc_type)) {
                                if (move_uploaded_file($_FILES[$attribute_name]['tmp_name'], $path)) {
                                    if ($extrafield_value_id) {
                                        $this->updateExtrafieldValueByid($extrafield_value_id, $img_name);
                                    } else {
                                        $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                    }
                                }
                            }

                            if (in_array($pathinfo['extension'], $this->img_type)) {
                                if (is_array($file) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($file['tmp_name'], $tmp_name)) {
                                    if (ImageManager::resize($tmp_name, dirname(__FILE__).'/views/img/'.$img_name)) {
                                        if ($extrafield_value_id) {
                                            $this->updateExtrafieldValueByid($extrafield_value_id, $img_name);
                                        } else {
                                            $this->insertExtraFieldValue($extrafield_id, $img_name, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    public function displayExtraFieldOnAddPage($page)
    {
        $obj_extrafield = new MarketplaceExtrafield();
        $obj_extrafield_option = new MarketplaceExtrafieldOptions();
        $obj_extrafield_radio = new MpExtrafieldOptions();
        $extrafield = $obj_extrafield->findActiveExtraAttributeDetailByPage($page, $this->context->language->id);
        if ($extrafield) {
            $i = 0;
            $extrafielddetailarray = array();
            foreach ($extrafield as $extrafi) {
                $id = $extrafi['id'];
                $inputtype = $extrafi['inputtype'];
                $default_value = $extrafi['default_value'];
                $label_name = $extrafi['label_name'];
                $attribute_name = $extrafi['attribute_name'];
                $asplaceholder = $extrafi['asplaceholder'];
                $validation_type = $extrafi['validation_type'];
                $char_limit = $extrafi['char_limit'];
                $multiple = $extrafi['multiple'];
                $file_type = $extrafi['file_type'];
                $field_req = $extrafi['field_req'];
                $active = $extrafi['active'];
                $extrfieldoption = $obj_extrafield_option->findExtraFieldOptions($id, $this->context->language->id);
                $extrfieldradio = $obj_extrafield_radio->getCustomFieldRadioOptions($id, $this->context->language->id);
                $j = 0;

                if ($inputtype == 3 || $inputtype == 4) {
                    if ($extrfieldoption) {
                        $extrafieldarray = array();
                        foreach ($extrfieldoption as $extrfieldopt) {
                            $extrafieldarray[$j] = array(
                                'id' => $extrfieldopt['id'],
                                'display_value' => $extrfieldopt['display_value'],
                            );
                            ++$j;
                        }
                        $extrafielddetailarray[$i] = array(
                            'id' => $id,
                            'inputtype' => $inputtype,
                            'default_value' => $default_value,
                            'label_name' => $label_name,
                            'attribute_name' => $attribute_name,
                            'asplaceholder' => $asplaceholder,
                            'validation_type' => $validation_type,
                            'char_limit' => $char_limit,
                            'multiple' => $multiple,
                            'file_type' => $file_type,
                            'field_req' => $field_req,
                            'active' => $active,
                            'extfieldoption' => $extrafieldarray,
                        );
                    }
                } elseif ($inputtype == 6) {
                    if ($extrfieldradio) {
                        $extrafieldarray = array();
                        foreach ($extrfieldradio as $extrfieldrad) {
                            $extrafieldarray[$j] = array(
                                'left_value' => $extrfieldrad['left_value'],
                                'right_value' => $extrfieldrad['right_value'],
                            );
                            ++$j;
                        }
                        $extrafielddetailarray[$i] = array(
                            'id' => $id,
                            'inputtype' => $inputtype,
                            'default_value' => $default_value,
                            'label_name' => $label_name,
                            'attribute_name' => $attribute_name,
                            'asplaceholder' => $asplaceholder,
                            'validation_type' => $validation_type,
                            'char_limit' => $char_limit,
                            'multiple' => $multiple,
                            'file_type' => $file_type,
                            'field_req' => $field_req,
                            'active' => $active,
                            'extfieldradio' => $extrafieldarray,
                        );
                    }
                } else {
                    $data = $obj_extrafield->getLabelAndDefaultValueDetailById($id);
                    if ($data) {
                        $new_default_value = array();
                        foreach ($data as $value) {
                            $new_default_value[$value['id_lang']] = $value['default_value'];
                        }
                        $default_value = $new_default_value;
                    }
                    $extrafielddetailarray[$i] = array(
                        'id' => $id,
                        'inputtype' => $inputtype,
                        'default_value' => $default_value,
                        'label_name' => $label_name,
                        'attribute_name' => $attribute_name,
                        'asplaceholder' => $asplaceholder,
                        'validation_type' => $validation_type,
                        'char_limit' => $char_limit,
                        'multiple' => $multiple,
                        'file_type' => $file_type,
                        'field_req' => $field_req,
                        'active' => $active,
                    );
                }
                ++$i;
            }
            return $extrafielddetailarray;
        }
    }

    public function displayExtraFieldOnUpdatePage($page, $marketplace_product_id, $mp_id_shop, $mp_id_seller, $is_for_shop)
    {
        $obj_extrafield = new MarketplaceExtrafield();
        $extrafield = $obj_extrafield->findActiveExtraAttributeDetailByPage($page, $this->context->language->id);
        $obj_extrafield_option = new MarketplaceExtrafieldOptions();
        $obj_extrafield_value = new MarketplaceExtrafieldValue();
        $obj_extrafield_radio = new MpExtrafieldOptions();
        $extrafielddetailarray = array();
        $extrafielddroparray = array();
        if ($extrafield) {
            $i = 0;

            foreach ($extrafield as $extrafi) {
                $id = $extrafi['id'];
                $inputtype = $extrafi['inputtype'];
                $default_value = stripslashes($extrafi['default_value']);
                $label_name = $extrafi['label_name'];
                $attribute_name = $extrafi['attribute_name'];
                $asplaceholder = $extrafi['asplaceholder'];
                $validation_type = $extrafi['validation_type'];
                $char_limit = $extrafi['char_limit'];
                $multiple = $extrafi['multiple'];
                $file_type = $extrafi['file_type'];
                $field_req = $extrafi['field_req'];
                $active = $extrafi['active'];
                $extrafield_value = $obj_extrafield_value->findExtrafieldValue($id, $mp_id_shop, $mp_id_seller, $marketplace_product_id, $is_for_shop);

                $extrfieldoption = $obj_extrafield_option->findExtraFieldOptions($id, $this->context->language->id);
                $extrfieldradio = $obj_extrafield_radio->getCustomFieldRadioOptions($id, $this->context->language->id);

                if ($extrafield_value || $extrfieldradio || $extrfieldoption || $extrafi['file_type']) {
                    if ($extrafield_value['field_value']) {
                        $default_value = $extrafield_value['field_value'];
                        $value_id = $extrafield_value['id'];
                        $asplaceholder = 0;
                    } elseif ($extrafield_value['field_val'] && $extrafi['inputtype'] != 5) {
                        $default_value = $extrafield_value['field_val'];
                        $value_id = $extrafield_value['id'];
                        $asplaceholder = 0;
                    } elseif ($extrafi['asplaceholder'] == 1) {
                        $default_value = $extrafi['default_value'];
                        $value_id = '';
                        $asplaceholder = $extrafi['asplaceholder'];
                    } else {
                        $default_value = $extrafi['default_value'];
                        $value_id = '';
                        $asplaceholder = $extrafi['asplaceholder'];
                    }

                    if ($extrafi['file_type'] && !$extrafield_value['field_value']) {
                        $default_value = $extrafi['default_value'];
                        $value_id = '';
                        $asplaceholder = $extrafi['asplaceholder'];
                    }

                    $j = 0;
                    if ($inputtype == 3) {
                        if ($extrfieldoption) {
                            foreach ($extrfieldoption as $extrfieldopt) {
                                $extrafielddroparray[$j] = array(
                                    'id' => $extrfieldopt['id'],
                                    'display_value' => $extrfieldopt['display_value'],
                                    'selected_value' => $extrafield_value['field_value'],
                                );
                                ++$j;
                            }
                            $extrafielddetailarray[$i] = array(
                                'id' => $id,
                                'inputtype' => $inputtype,
                                'default_value' => $default_value,
                                'value_id' => $value_id,
                                'label_name' => $label_name,
                                'attribute_name' => $attribute_name,
                                'asplaceholder' => $asplaceholder,
                                'validation_type' => $validation_type,
                                'char_limit' => $char_limit,
                                'multiple' => $multiple,
                                'file_type' => $file_type,
                                'field_req' => $field_req,
                                'active' => $active,
                                'extfielddrop' => $extrafielddroparray,
                            );
                        }
                    } elseif ($inputtype == 4) {
                        $extrafieldchkarray = array();
                        if ($extrfieldoption) {
                            foreach ($extrfieldoption as $extrfieldopt) {
                                $extrafieldchkarray[$j] = array(
                                    'id' => $extrfieldopt['id'],
                                    'display_value' => $extrfieldopt['display_value'],
                                    'selected_value' => $extrafield_value['field_value'],
                                );
                                ++$j;
                            }
                            $extrafielddetailarray[$i] = array(
                                'id' => $id,
                                'inputtype' => $inputtype,
                                'default_value' => $default_value,
                                'value_id' => $value_id,
                                'label_name' => $label_name,
                                'attribute_name' => $attribute_name,
                                'asplaceholder' => $asplaceholder,
                                'validation_type' => $validation_type,
                                'char_limit' => $char_limit,
                                'multiple' => $multiple,
                                'file_type' => $file_type,
                                'field_req' => $field_req,
                                'active' => $active,
                                'extfieldcheck' => $extrafieldchkarray,
                            );
                        }
                    } elseif ($inputtype == 6) {
                        if ($extrfieldradio) {
                            $extrafieldarray = array();
                            foreach ($extrfieldradio as $extrfieldrad) {
                                $extrafieldarray[$j] = array(
                                    'left_value' => $extrfieldrad['left_value'],
                                    'right_value' => $extrfieldrad['right_value'],
                                );
                                ++$j;
                            }
                            $extrafielddetailarray[$i] = array(
                                'id' => $id,
                                'inputtype' => $inputtype,
                                'default_value' => $default_value,
                                'value_id' => $value_id,
                                'label_name' => $label_name,
                                'attribute_name' => $attribute_name,
                                'asplaceholder' => $asplaceholder,
                                'validation_type' => $validation_type,
                                'char_limit' => $char_limit,
                                'multiple' => $multiple,
                                'file_type' => $file_type,
                                'field_req' => $field_req,
                                'active' => $active,
                                'selected_value' => $extrafield_value['field_value'],
                                'extfieldradio' => $extrafieldarray,
                            );
                        }
                    } else {
                        $extrafielddetailarray[$i] = array(
                            'id' => $id,
                            'inputtype' => $inputtype,
                            'default_value' => $default_value,
                            'value_id' => $value_id,
                            'label_name' => $label_name,
                            'attribute_name' => $attribute_name,
                            'asplaceholder' => $asplaceholder,
                            'validation_type' => $validation_type,
                            'char_limit' => $char_limit,
                            'multiple' => $multiple,
                            'file_type' => $file_type,
                            'field_req' => $field_req,
                            'active' => $active,
                        );
                    }
                } else {
                    //if (!$asplaceholder) {
                    $data = $obj_extrafield->getLabelAndDefaultValueDetailById($id);
                    if ($data) {
                        $new_default_value = array();
                        foreach ($data as $value) {
                            $new_default_value[$value['id_lang']] = $value['default_value'];
                        }
                        $default_value = $new_default_value;
                    }
                    //}
                    $extrafielddetailarray[$i] = array(
                        'id' => $id,
                        'inputtype' => $inputtype,
                        'default_value' => $default_value,
                        'value_id' => '',
                        'label_name' => $label_name,
                        'attribute_name' => $attribute_name,
                        'asplaceholder' => $asplaceholder,
                        'validation_type' => $validation_type,
                        'char_limit' => $char_limit,
                        'multiple' => $multiple,
                        'file_type' => $file_type,
                        'field_req' => $field_req,
                        'active' => $active,
                    );
                    foreach ($extrafield as $extrafieldinfo) {
                        $extrafieldData = $obj_extrafield->getLabelAndDefaultValueDetailById($extrafieldinfo['id']);
                        if ($extrafieldData) {
                            //foreach ($extrafieldData as $value) {
                            //$extrafielddetailarray[$i]['default_value'][$value['id_lang']] = $extrafieldData[$this->context->language->id]['default_value'];
                            //}
                        }
                    }
                }
                ++$i;
            }


            if ($extrafielddetailarray) {
                return $extrafielddetailarray;
            } else {
                return $extrafield;
            }
        }
    }

    public function validateExtraFieldBeforeSubmit($page, $idSeller = 0)
    {
        $custom_obj = new MarketplaceExtrafield();
        $active_field = $custom_obj->findActiveExtraAttributeDetailByPage($page, $this->context->language->id);
        if ($active_field) {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
                    $default_lang = Tools::getValue('seller_default_lang');
                } else {
                    $default_lang = Tools::getValue('default_lang');
                }
            } else {
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin default lang
                    $default_lang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller default lang
                    if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
                        $default_lang = Tools::getValue('seller_default_lang');
                    } else {
                        $default_lang = Tools::getValue('default_lang');
                    }
                }
            }
            foreach ($active_field as $label_val) {
                $attribute_name = $label_val['attribute_name'];
                $field_value = Tools::getValue($label_val['attribute_name']);

                if (is_array($field_value)) {
                    $field_value = implode(',', $field_value);
                }

                if ($label_val['inputtype'] == 1 || $label_val['inputtype'] == 2) {
                    if ($label_val['field_req'] == 1 && !Tools::getValue($attribute_name.'_'.$default_lang)) {
                        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                            $admin_lang = Language::getLanguage((int) $default_lang);
                            $this->context->controller->errors[] = sprintf($this->l('%s is required in %s'), $label_val['label_name'], $admin_lang['name']);
                        } else {
                            $this->context->controller->errors[] = sprintf($this->l('%s est vide'), $label_val['label_name']);
                        }
                    } else {
                        $languages = Language::getLanguages();
                        $text_error = 0;
                        foreach ($languages as $language) {
                            $field_value = trim(Tools::getValue($attribute_name.'_'.$language['id_lang']));
                            if ($label_val['inputtype'] == 1) {
                                if (!empty($field_value)) {
                                    if ($label_val['validation_type'] == 2 && !Validate::isEmail($field_value)) {
                                        $this->context->controller->errors[] = sprintf($this->l('Email is not valid for %s in %s'), $label_val['label_name'], $language['name']);
                                        //$text_error = 2;
                                    }
                                    if ($label_val['validation_type'] == 1 && !Validate::isName($field_value)) {
                                        //$this->context->controller->errors[] = sprintf($this->l('Invalid input for %s in %s'), $label_val['label_name'], $language['name']);
                                        //$text_error = 3;
                                    }
                                    if ($label_val['validation_type'] == 3 && !is_numeric($field_value)) {
                                        $this->context->controller->errors[] = sprintf($this->l('Please enter only numbers for %s in %s'), $label_val['label_name'], $language['name']);
                                        //$text_error = 4;
                                    }
                                }
                            }

                            if ($label_val['inputtype'] == 1 && Tools::strlen($field_value) > $label_val['char_limit']) {
                                $this->context->controller->errors[] = sprintf($this->l('Maximum character limit(%s) is exceed for %s in %s'), $label_val['char_limit'] , $label_val['label_name'], $language['name']);
                                //$text_error = 5;
                            }

                            if ($label_val['inputtype'] == 2 && Tools::strlen($field_value) > $label_val['char_limit']) {
                                $this->context->controller->errors[] = sprintf($this->l('Le champs "%s" ne doit pas dépasser %s caractères.'), $label_val['label_name'], $label_val['char_limit'], $language['name']);
                                //$text_error = 6;
                            }
                        }

                        // if ($text_error == 1) {
                        //     $this->context->controller->errors[] = sprintf($this->l('%s is empty'), $label_val['label_name']);
                        // } elseif ($text_error == 2) {
                        //     $this->context->controller->errors[] = $this->l('Email is not valid');
                        // } elseif ($text_error == 3) {
                        //     $this->context->controller->errors[] = sprintf($this->l('Invalid input for %s'), $label_val['label_name']);
                        // } elseif ($text_error == 4) {
                        //     $this->context->controller->errors[] = Tools::displayError('Please enter only numbers');
                        //     $this->context->controller->errors[] = $this->l('Please enter only numbers');
                        // } elseif ($text_error == 5) {
                        //     $this->context->controller->errors[] = $this->l('Maximum character limit is exceed for textbox');
                        // } elseif ($text_error == 6) {
                        //     $this->context->controller->errors[] = $this->l('Maximum character limit is exceed for textarea');
                        // }
                    }
                } elseif ($label_val['inputtype'] == 3 || $label_val['inputtype'] == 4 || $label_val['inputtype'] == 6) {
                    if (empty($field_value)) {
                        if ($label_val['field_req'] == 1) {
                            $this->context->controller->errors[] = sprintf($this->l('%s est vide'), $label_val['label_name']);
                        }
                    }
                } elseif ($label_val['inputtype'] == 5) {
                    $extraFieldValue = new MarketplaceExtrafieldValue();
                    if (!$extraFieldValue->getExtrafieldValueId($label_val['id'], $page, $idSeller)) {
                        if ($label_val['field_req'] == 1 && !empty($_FILES[$attribute_name]['tmp_name'])) {
                            $filename = $_FILES[$label_val['attribute_name']]['name'];
                            $ext = pathinfo($filename, PATHINFO_EXTENSION);

                            if ($label_val['file_type'] == 1) {
                                if (!in_array($ext, $this->img_type)) {
                                    $this->context->controller->errors[] = ($this->l('File type is invalid for ').$label_val['label_name']);
                                } elseif ($_FILES[$label_val['attribute_name']]['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
                                    $this->context->controller->errors[] = ($this->l('File is too large selected in ').$label_val['label_name']);
                                }
                            } elseif ($label_val['file_type'] == 2) {
                                if (!in_array($ext, $this->doc_type)) {
                                    $this->context->controller->errors[] = ($this->l('File type is invalid for ').$label_val['label_name']);
                                } elseif ($_FILES[$label_val['attribute_name']]['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
                                    $this->context->controller->errors[] = ($this->l('File is too large selected in ').$label_val['label_name']);
                                }
                            } elseif ($label_val['file_type'] == 3) {
                                $allowed = array_merge($this->img_type, $this->doc_type);
                                if (!in_array($ext, $allowed)) {
                                    $this->context->controller->errors[] = ($this->l('File type is invalid for ').$label_val['label_name']);
                                } elseif ($_FILES[$label_val['attribute_name']]['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
                                    $this->context->controller->errors[] = ($this->l('File is too large selected in ').$label_val['label_name']);
                                }
                            }
                        } elseif ($label_val['field_req'] == 1 && empty($_FILES[$attribute_name]['tmp_name'])) {
                            $this->context->controller->errors[] = ($this->l('File is missing for ').$label_val['label_name']);
                        }
                    }
                }
            }
        }
    }

    /**
     * [hookDisplayProductTab display custom field information on product detail page].
     *
     * @return [type] [description]
     */
    public function hookDisplayFooterProduct()
    {
        $psProductId = Tools::getValue('id_product');
        $mpProductInfo = WkMpSellerProduct::getSellerProductByPsIdProduct($psProductId);
        $mpProductId = $mpProductInfo['id_mp_product'];
        if ($mpProductId) {
            $mpSellerProduct = WkMpSellerProduct::getSellerProductByIdProduct($mpProductId);
            if ($mpSellerProduct) {
                $mpShopId = $mpSellerProduct['id_mp_product'];
                $mpSellerId = $mpSellerProduct['id_seller'];
            } else {
                $mpShopId = 0;
                $mpSellerId = 0;
            }

            $isForShop = 0;

            $extrafielddetailarray = $this->displayExtraFieldOnUpdatePage(1, $mpProductId, $mpShopId, $mpSellerId, $isForShop);
            $this->context->smarty->assign('extrafielddetail', $extrafielddetailarray);
            $this->context->smarty->assign('id_lang', $this->context->language->id);

            return $this->fetch('module:mpextrafield/views/templates/hook/extrafield_product_details.tpl');
        }
    }

    public function hookDisplayMpSellerDetailsBottom()
    {
        $this->context->controller->addCss($this->_path.'views/css/product_display.css');
        $obj_mpshop = new WkMpSeller();
        $shop_link_rewrite = Tools::getValue('mp_shop_name');
        $mp_shop_detail = WkMpSeller::getSellerByLinkRewrite($shop_link_rewrite);
        $mp_id_shop = $mp_shop_detail['id_seller'];
        if ($mp_id_shop) {
            $mp_id_seller = $mp_id_shop;
            $is_for_shop = 1;
            $marketplace_product_id = 0;
            $extrafielddetailarray = $this->displayExtraFieldOnUpdatePage(2, $marketplace_product_id, $mp_id_shop, $mp_id_seller, $is_for_shop);
            $this->context->smarty->assign('extrafielddetail', $extrafielddetailarray);
            $this->context->smarty->assign('id_lang', $this->context->language->id);

            return $this->fetch('module:mpextrafield/views/templates/hook/extrafield_shop_details.tpl');
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        $controllerName = $this->context->controller->php_self;
        if ($controllerName == 'addproduct'
            || $controllerName == 'updateproduct'
            || $controllerName == 'editprofile'
            || $controllerName == 'sellerrequest'
        ) {
            Media::addJsDef(array('maxFileSize' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')));

            $this->context->controller->registerJavascript(
                'module-mpextrafield-image-js',
                'modules/'.$this->name.'/views/js/image_validate.js',
                array('position' => 'bottom', 'priority' => 999)
            );
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controllerName = Tools::getValue('controller');
        if ($controllerName == 'AdminSellerInfoDetail'
            || $controllerName == 'AdminSellerProductDetail'
        ) {
            Media::addJsDef(array('maxFileSize' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')));
            $this->context->controller->addJS($this->_path.'/views/js/image_validate.js');
        }
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        }
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }
        if (!parent::install() || !$this->callInstallTab()
            || !$this->insertPredefineInputType()
            || !$this->insertPredefineInputValidationType()
            || !$this->registerHook('displayMpAddProductFooter')
            || !$this->registerHook('displayMpUpdateProductFooter')
            || !$this->registerHook('displayMpSellerRequestFooter')
            || !$this->registerHook('displayMpEditProfileInformationBottom')
            || !$this->registerHook('displayMpSellerDetailsBottom')
            || !$this->registerHook('actionAfterAddMPProduct')
            || !$this->registerHook('actionAfterUpdateMPProduct')
            || !$this->registerHook('actionAfterAddSeller')
            || !$this->registerHook('actionAfterUpdateSeller')
            || !$this->registerHook('displayRightColumnProduct')
            || !$this->registerHook('displayProductTab')
            || !$this->registerHook('displayProductTabContent')
            || !$this->registerHook('actionBeforeUpdateMPProduct')
            || !$this->registerHook('actionBeforeAddMPProduct')
            || !$this->registerHook('actionBeforeAddSeller')
            || !$this->registerHook('actionBeforeUpdateSeller')
            || !$this->registerHook('displayFooterProduct')
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('displayBackOfficeHeader')
        ) {
            return false;
        } else {
            if (!$this->callAssociateModuleToShop()) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function callUninstallTab()
    {
        $this->uninstallTab('AdminAddextrafield');

        return true;
    }

    public function uninstallTab($class_name)
    {
        $id_tab = (int) Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
        } else {
            return false;
        }
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'marketplace_extrafield`,
            `'._DB_PREFIX_.'marketplace_extrafield_lang`,
            `'._DB_PREFIX_.'marketplace_extrafield_association`,
            `'._DB_PREFIX_.'marketplace_extrafield_value`,
            `'._DB_PREFIX_.'marketplace_extrafield_value_lang`,
            `'._DB_PREFIX_.'marketplace_extrafield_inputtype`,
            `'._DB_PREFIX_.'mp_extrafield_custom_field_options`,
            `'._DB_PREFIX_.'mp_extrafield_custom_field_options_lang`,
            `'._DB_PREFIX_.'mp_extrafield_custom_field_validation`,
            `'._DB_PREFIX_.'marketplace_extrafield_options`,
            `'._DB_PREFIX_.'marketplace_extrafield_options_lang`
            ');
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->deleteTables('')
            || !$this->callUninstallTab()
        ) {
            return false;
        }

        return true;
    }
}
