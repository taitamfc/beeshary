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

class mpmassuploadaddnewuploadrequestModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', [], 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Mass Upload', [], 'Breadcrumb'),
            'url' => ''
        ];

        return $breadcrumb;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submit_csv')) {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', '-1');
            set_time_limit(0);

            if ($this->context->customer->id) {
                $id_customer = $this->context->customer->id;
                $obj_marketplace_seller = new WkMpSeller();
                $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($id_customer);
                if ($mp_seller && $mp_seller['active']) {
                    $obj_massupload = new MarketplaceMassUpload();

                    $id_seller = $mp_seller['id_seller'];
                    $request_id = Tools::getValue('request_id');
                    $mass_upload_category = Tools::getValue('mass_upload_category');
                    $csv_file = $_FILES["product_info"];
                    $img_file = $_FILES["product_image"];

                    $approve_type = Configuration::getGlobalValue('MASS_UPLOAD_APPROVE');
                    if ($approve_type == 'admin') {
                        $approved = 0;
                    } elseif ($approve_type == 'default') {
                        $approved = 1;
                    }

                    $error_string = $this->getErrorString($mass_upload_category);
                    $kwargs = array(
                        'id_customer' => $id_customer,
                        'id_seller' => $id_seller,
                        'request_id' => $request_id,
                        'mass_upload_category' => $mass_upload_category,
                        'csv_file' => $csv_file,
                        'img_file' => $img_file,
                        'approved' => $approved,
                        'error_string' => $error_string,
                        'csvType' => Tools::getValue('csvType'),
                    );

                    $is_file_valid = $obj_massupload->testCsvFile($kwargs);
                    if ($is_file_valid['is_error']) {
                        $this->errors = $is_file_valid['errors'];
                    } else {
                        $ps_id_shop = $this->context->shop->id;

                        $kwargs['total_records'] = $is_file_valid['csv_row'];
                        $kwargs['ps_id_shop'] = $ps_id_shop;

                        if ($mass_upload_category == 2) {
                            $warning_string = $this->getWarningString($mass_upload_category);
                            $kwargs['warning_string'] = $warning_string;
                        }

                        $upload_rtn = $obj_massupload->uploadCsvFile($kwargs);
                        if ($upload_rtn['is_warning']) {
                            $this->context->cookie->wk_massupload_warning = $upload_rtn['warning_msg'];
                            $this->context->cookie->write();
                        }

                        $extra = array('success'=>1);
                        $request_link = $this->context->link->getModuleLink('mpmassupload', 'massuploadview', $extra);
                        Tools::redirect($request_link);
                    }
                } else {
                    Tools::redirect($this->context->link->getPageLink('my-account'));
                }
            }
        }
    }

    public function initContent()
    {
        parent::initContent();

        if (!Configuration::get('WK_MP_SELLER_PRODUCT_COMBINATION')) {
            if (Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE')) {
                Configuration::updateValue('MASS_UPLOAD_COMBINATION_APPROVE', 0);
                Configuration::updateValue('MASS_UPLOAD_ALLOW_EDIT_COMBINATION', 0);
            }
        }

        $link = new Link();
        if ($this->context->customer->id) {
            $id_customer = $this->context->customer->id;

            $obj_marketplace_seller = new WkMpSeller();
            $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($id_customer);
            if ($mp_seller && $mp_seller['active']) {
                $wkSmartVar = array();
                $id_lang = $this->context->language->id;
                $error = Tools::getValue('error');
                if ($error) {
                    if (in_array($error, array(1,2))) {
                        $count = Tools::getValue('count');
                        if ($count) {
                            $this->context->smarty->assign('count', $count);
                        }
                    }
                }

                $this->createCategoryCsv($id_lang);
                $this->createLanguageCsv();

                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $productCsv = [
                        'add' => _MODULE_DIR_ .$this->module->name.'/views/demo/add/lang/multilang_product.csv',
                        'update' => _MODULE_DIR_ .$this->module->name.'/views/demo/update/lang/multilang_product.csv'
                    ];
                } else {
                    $productCsv = [
                        'add' => _MODULE_DIR_ .$this->module->name.'/views/demo/add/non-lang/product.csv',
                        'update' => _MODULE_DIR_ .$this->module->name.'/views/demo/update/non-lang/product.csv'
                    ];
                }

                $random_number = MarketplaceMassUpload::generateRandomeNumber(8);
                $wkSmartVar = [
                    'logic' => 'massupload',
                    'is_seller' => 1,
                    'random_number' => $random_number,
                    'categoryCsv' => _MODULE_DIR_ .'mpmassupload/views/demo/categories.csv',
                    'languageCsv' => _MODULE_DIR_ .'mpmassupload/views/demo/site_languages.csv',
                    'demoZip' => _MODULE_DIR_ .'mpmassupload/views/demo/product_image.zip',
                    'combinationCsv' => _MODULE_DIR_ .'mpmassupload/views/demo/combinations.csv',
                    'productCsv' => $productCsv,
                    'error' => $error,
                    'MASS_UPLOAD_ALLOW_EDIT_COMBINATION' => Configuration::get('MASS_UPLOAD_ALLOW_EDIT_COMBINATION'),
                ];

                if (Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE')) {
                    $wkSmartVar['massupload_combination_approve'] = Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE');
                }

                $this->context->smarty->assign($wkSmartVar);
                $this->defineJSVars();
                $this->setTemplate('module:'.$this->module->name.'/views/templates/front/addnewuploadrequest.tpl');
            } else {
                Tools::redirect(__PS_BASE_URI__.'pagenotfound');
            }
        } else {
            Tools::redirect($link->getPageLink('my-account'));
        }
    }

    protected function createCategoryCsv($id_lang)
    {
        $count = 0;
        $category = new Category();
        $all_cat = $category->getSimpleCategories($id_lang);
        $fp = fopen('modules/mpmassupload/views/demo/categories.csv', 'w');
        foreach ($all_cat as $cat) {
            if ($count == 0) {
                $list = array('S No.', 'Category Name', 'Category Id');
            } else {
                $list = array($count, $cat['name'], $cat['id_category']);
            }

            fputcsv($fp, $list);
            $count = $count + 1;
        }
        return true;
    }

    protected function createLanguageCsv()
    {
        $count = 1;
        $languages = Language::getLanguages();
        $file = fopen('modules/mpmassupload/views/demo/site_languages.csv', 'w');

        $file_header = array('S No.', 'Language Name', 'ISO Code');
        fputcsv($file, $file_header);

        foreach ($languages as $lang) {
            $list = array($count, $lang['name'], $lang['iso_code']);
            fputcsv($file, $list);
            $count += 1;
        }

        fclose($file);
        return true;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->registerStylesheet('request-form', 'modules/'.$this->module->name.'/views/css/requestform.css');
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerJavascript('upload_script', 'modules/'.$this->module->name.'/views/js/upload_script.js');
    }

    public function getErrorString($mass_upload_category)
    {
        $error_string = array();
        $this->module->l('Product name is required', 'addproduct');

        $error_string['csv_format'] = $this->module->l('Invalid file format . Please upload CSV file.', 'addnewuploadrequest');
        $error_string['zip_format'] = $this->module->l('Invalid file format . Please upload image zip file.', 'addnewuploadrequest');
        $error_string['csv_structure'] = $this->module->l('Submitted CSV file is not valid. Please check the structure of CSV file by downloading dummy file.', 'addnewuploadrequest');
        $error_string['upd_col_req'] = $this->module->l('Add column(fields) in CSV which you want to update.', 'addnewuploadrequest');
        $error_string['zip_file'] = $this->module->l('Product image .zip file is not valid, please check and try again.', 'addnewuploadrequest');
        $error_string['error_prefix'] = $this->module->l('Line number: ', 'addnewuploadrequest');
        $error_string['error_suffix'] = $this->module->l(' is having error. ', 'addnewuploadrequest');
        $error_string['request_no_req'] = $this->module->l('Request Number is required.', 'addnewuploadrequest');
        $error_string['file_cate_req'] = $this->module->l('Csv file category is mandatory.', 'addnewuploadrequest');
        $error_string['upload_csv_file'] = $this->module->l('Please upload CSV File.', 'addnewuploadrequest');

        $error_string['mp_id_prod_2'] = $this->module->l('Please enter valid marketplace product id.', 'addnewuploadrequest');

        // For Multi Lang
        $error_string['def_lanf_col_req_1'] = $this->module->l('Product name is required in default language. Please create a column of ', 'addnewuploadrequest');
        $error_string['def_lanf_col_req_2'] = $this->module->l(' and enter language wise product name in it. For more info please download dummy file.', 'addnewuploadrequest');
        $error_string['prod_name_def_lang'] = $this->module->l('Product name is required in.', 'addnewuploadrequest');
        $error_string['prod_name_011'] = $this->module->l('Product Name field is too long ', 'addnewuploadrequest');
        $error_string['prod_name_012'] = $this->module->l('chars max', 'addnewuploadrequest');
        // For Multi Lang

        $error_string['prod_name_1'] = $this->module->l('Product name is required fields.', 'addnewuploadrequest');
        $error_string['prod_name_2'] = $this->module->l('Product name must not have Invalid characters', 'addnewuploadrequest').' <>;=#{}';
        $error_string['short_desc_1'] = $this->module->l('Short description in invalid.', 'addnewuploadrequest');
        $error_string['short_desc_2_1'] = $this->module->l('This short description field is too long.', 'addnewuploadrequest');
        $error_string['short_desc_2_2'] = $this->module->l(' characters max.', 'addnewuploadrequest');
        $error_string['description'] = $this->module->l('Product description in invalid.', 'addnewuploadrequest');
        $error_string['prod_price_1'] = $this->module->l('Product price should be numeric.', 'addnewuploadrequest');
        $error_string['prod_price_2'] = $this->module->l('Product price is required field.', 'addnewuploadrequest');
        $error_string['prod_qty_1'] = $this->module->l('Product quantity should be numeric.', 'addnewuploadrequest');
        $error_string['prod_qty_2'] = $this->module->l('Product quantity is required field.', 'addnewuploadrequest');
        $error_string['prod_img'] = $this->module->l('Image format not recognized, allowed formats are: .gif, .jpg, .png', 'addnewuploadrequest');

        if ($mass_upload_category == 2) {
            $error_string['comb_install_enable'] = $this->module->l('Combiantion CSV is not allowed to be processed.', 'addnewuploadrequest');

            // Edit combination CSV is not allowed
            $error_string['edit_comb_not_allowed'] = $this->module->l('Edit combination by CSV is not allowed.', 'addnewuploadrequest');

            // For Multi Lang
            $error_string['for_language'] = $this->module->l(' for language ', 'addnewuploadrequest');
            // For Multi Lang

            // For Multi Lang
            $error_string['def_lanf_com_col_req_1'] = $this->module->l('Attribute group and attribute value column is required in default language.', 'addnewuploadrequest');
            // For Multi Lang

            $error_string['mp_id_prod_1'] = $this->module->l('Please enter product id.', 'addnewuploadrequest');
            $error_string['attr_grp_1'] = $this->module->l('Please enter combination attribute.', 'addnewuploadrequest');
            $error_string['attr_grp_2'] = $this->module->l('Please enter attribute details', 'addnewuploadrequest');
            $error_string['attr_grp_3'] = $this->module->l('Multiple times same Attribute name is not allowed for one combination.', 'addnewuploadrequest');
            $error_string['attr_grp_4'] = $this->module->l('Attribute Name is required.', 'addnewuploadrequest');
            $error_string['attr_grp_5'] = $this->module->l('Attribute Name is invalid.', 'addnewuploadrequest');
            $error_string['attr_grp_6'] = $this->module->l('Attribute type is required.', 'addnewuploadrequest');
            $error_string['attr_grp_7'] = $this->module->l('Attribute type is invalid.', 'addnewuploadrequest');
            // For Multi Lang
            $error_string['attr_grp_011'] = $this->module->l('Attribute is required in default language. So please create a column of ', 'addnewuploadrequest');
            $error_string['attr_grp_012'] = $this->module->l(' and enter language wise attribute group in it. For more info please download dummy file of combination.', 'addnewuploadrequest');
            $error_string['attr_grp_02'] = $this->module->l('Attribute is required in default language', 'addnewuploadrequest');

            $error_string['attr_val_011'] = $this->module->l('Attribute Value is required in default language. So please create a column of ', 'addnewuploadrequest');
            $error_string['attr_val_012'] = $this->module->l(' and enter language wise attribute value in it. For more info please download dummy file of combination.', 'addnewuploadrequest');
            $error_string['attr_val_02'] = $this->module->l('Attribute value detail is required in default language.', 'addnewuploadrequest');
            // For Multi Lang
            $error_string['attr_val_1'] = $this->module->l('Please enter combination attribute value.', 'addnewuploadrequest');
            $error_string['attr_val_2'] = $this->module->l('Please enter attribute value details.', 'addnewuploadrequest');
            $error_string['attr_val_3'] = $this->module->l('Attribute Value is required.', 'addnewuploadrequest');
            $error_string['attr_val_4'] = $this->module->l('Attribute Value is invalid.', 'addnewuploadrequest');
            $error_string['comb_ean13'] = $this->module->l('Field EAN13 is not valid.', 'addnewuploadrequest');
            $error_string['comb_upc'] = $this->module->l('Field UPC is not valid.', 'addnewuploadrequest');
            $error_string['wholesale_price'] = $this->module->l('Wholesale price must be numeric.', 'addnewuploadrequest');
            $error_string['impact_price'] = $this->module->l('Impact price must be numeric.', 'addnewuploadrequest');
            $error_string['comb_qty'] = $this->module->l('Quantity should be integer.', 'addnewuploadrequest');
            $error_string['comb_min_qty'] = $this->module->l('Minimum quantity  should be integer and greater than 0.', 'addnewuploadrequest');
            $error_string['comb_weight'] = $this->module->l('Impact on weight must be numeric.', 'addnewuploadrequest');
            $error_string['comb_default'] = $this->module->l('Invalid default value, accepted values are 1|0.', 'addnewuploadrequest');
            $error_string['comb_date_formate'] = $this->module->l('Must be valid date format.', 'addnewuploadrequest');
            $error_string['prod_img_check'] = $this->module->l('Please enter image id of respective product.', 'addnewuploadrequest');
        }
        return $error_string;
    }

    public function getWarningString($mass_upload_category)
    {
        $warning_str = array();
        if ($mass_upload_category == 2) {
            $warning_str['warning_prefix'] = $this->module->l('Line number: ', 'addnewuploadrequest');
            $warning_str['attr_not_exist'] = $this->module->l('All the attribute of this combination is not present, so this combination can not be created. Please contact admin for giving the permission of creating new attributes and there values.', 'addnewuploadrequest').'===';
            $warning_str['comb_exist'] = $this->module->l('Combination is already exists for this product.', 'addnewuploadrequest').'===';
            $warning_str['comb_not_exist'] = $this->module->l('Combination is not exists for this product.', 'addnewuploadrequest').'===';
        }
        return $warning_str;
    }

    public function defineJSVars()
    {
        $jsVars = [
                'backend_contrller' => 0,
                'choosefile_fileButtonHtml' => $this->trans('Choose File', [], 'Modules.MpMassUpload'),
                'nofileselect_fileDefaultHtml' => $this->trans('No file selected', [], 'Modules.MpMassUpload'),
            ];
        Media::addJsDef($jsVars);
    }
}
