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

class AdminMarketplacemassuploadController extends ModuleAdminController
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(0);

        $this->bootstrap = true;
        $this->table = 'marketplace_mass_upload';
        $this->className = 'MarketplaceMassUpload';
        $this->context = Context::getContext();

        $this->identifier = 'id';
        parent::__construct();

        $mass_upd_cat = array(1 => 'Products', 2 => 'Combinations');
        $this->explicitSelect = true;
        $this->_join .= "INNER JOIN `" . _DB_PREFIX_ . "wk_mp_seller` msi ON (a.`id_seller`= msi.`id_seller`)
						INNER JOIN `" . _DB_PREFIX_ . "wk_mp_seller_lang` msil ON (msil.id_seller = msi.`id_seller` AND msil.id_lang = " . Configuration::get('PS_LANG_DEFAULT') . ")";
        $this->_select .= "a.id as id, msil.`shop_name`";
        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center'
            ),
            'mass_upload_category' => array(
                'title' => $this->l('Upload Category'),
                'align' => 'center',
                'type' => 'select',
                'list' => $mass_upd_cat,
                'filter_key' => 'a!mass_upload_category',
                'filter_type' => 'int',
                'callback' => 'CallUploadCategory'
            ),
            'request_id' => array(
                'title' => $this->l('Request No.'),
                'align' => 'center'
            ),
            'shop_name' => array(
                'title' => $this->l('Shop Name'),
                'align' => 'center'
            ),
            'total_records' => array(
                'title' => $this->l('Total Records (Product)'),
                'align' => 'center'
            ),
            'csv_type' => array(
                'title' => $this->l('CSV Type'),
                'align' => 'center',
                'callback' => 'getCsvTypeText',
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'center',
                'type' => 'date',
                'filter_key' => 'a!date_add'
            ),
            'is_approve' => array(
                'title' => $this->l('Status'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'csv_toggle_status'
            ),
        );

        $this->bulk_actions = array('delete' => array(
            'text' => $this->l('Delete selected'),
            'icon' => 'icon-trash',
            'confirm' => $this->l('Delete selected items?')
        ));

        if (isset($this->context->cookie->wk_massupload_warning)) {
            $csv_warning = $this->context->cookie->wk_massupload_warning;
            $this->warnings = explode('===', $csv_warning);

            unset($this->context->cookie->wk_massupload_warning);
            $this->context->cookie->write();
        }

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function getCsvTypeText($csvType)
    {
        if ($csvType == 1) {
            return $this->l('Add CSV');
        } elseif ($csvType == 2) {
            return $this->l('Update CSV');
        }
    }

    public function CallUploadCategory($value)
    {
        if ($value == 1) {
            return $this->l('Products');
        } elseif ($value == 2) {
            return $this->l('Combinations');
        }
    }

    public function renderList()
    {
        $this->addRowAction('view');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('Add new seller csv'),
        );
        $this->page_header_toolbar_btn['export'] = array(
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token . '&exportmpproduct=1',
            'desc' => $this->l('Export Seller csv'),
        );
    }

    public function renderForm()
    {
        $obj_mp_seller = new WkMpSeller();
        $mp_sellers = $obj_mp_seller->getAllSeller();

        $random_number = MarketplaceMassUpload::generateRandomeNumber(8);
        $this->fields_value['request_id'] = $random_number;
        $this->fields_value['enable_csv'] = 1;
        $this->fields_value['csvType'] = 1;
        $this->fields_value['allowEditComb'] = Configuration::get('MASS_UPLOAD_ALLOW_EDIT_COMBINATION');
        $this->fields_value['wk_export_id_seller_customer'] = '';
        $this->fields_value['wk_csv_category'] = '';
        $this->fields_value['wk_csv_export_product_columns[]'] = array();
        $this->fields_value['wk_csv_export_combination_columns[]'] = array();
        $this->fields_value['wk_csv_export_langs[]'] = array();

        $csv_category = array(
            array(
                'id' => 1,
                'name' => $this->l('Products')
            ),
        );
        $product_cols = array(
            array(
                'id' => 'mp_id_product',
                'name' => $this->l('mp_id_product')
            ),
            array(
                'id' => 'name',
                'name' => $this->l('name')
            ),
            array(
                'id' => 'category_id',
                'name' => $this->l('category_id')
            ),
            array(
                'id' => 'default_category',
                'name' => $this->l('default_category')
            ),
            array(
                'id' => 'short_description',
                'name' => $this->l('short_description')
            ),
            array(
                'id' => 'description',
                'name' => $this->l('description')
            ),
            array(
                'id' => 'price',
                'name' => $this->l('price')
            ),
            array(
                'id' => 'quantity',
                'name' => $this->l('quantity')
            ),
            array(
                'id' => 'image_ref',
                'name' => $this->l('image_ref')
            )
        );

        $combination_cols = array(
            array(
                'id' => 'Seller Product ID',
                'name' => $this->l('Seller Product ID')
            ),
            array(
                'id' => 'Attribute (Name : Type)',
                'name' => $this->l('Attribute (Name : Type)')
            ),
            array(
                'id' => 'value',
                'name' => $this->l('value')
            ),
            array(
                'id' => 'reference',
                'name' => $this->l('reference')
            ),
            array(
                'id' => 'EAN13',
                'name' => $this->l('EAN13')
            ),
            array(
                'id' => 'UPC',
                'name' => $this->l('UPC')
            ),
            array(
                'id' => 'Wholesale Price',
                'name' => $this->l('Wholesale Price')
            ),
            array(
                'id' => 'Impact on Price',
                'name' => $this->l('Impact on Price')
            ),
            array(
                'id' => 'Quantity',
                'name' => $this->l('Quantity')
            ),
            array(
                'id' => 'Minimal quantity',
                'name' => $this->l('Minimal quantity')
            ),
            array(
                'id' => 'Impact on weight',
                'name' => $this->l('Impact on weight')
            ),
            array(
                'id' => 'Default (0 = NO, 1 = Yes)',
                'name' => $this->l('Impact on weight')
            ),
            array(
                'id' => 'Combination available date',
                'name' => $this->l('Combination available date')
            ),
            array(
                'id' => 'Image ID',
                'name' => $this->l('Image ID')
            ),
        );

        if (Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE')) {
            $csv_category[] = array('id' => 2, 'name' => $this->l('Combinations'));
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add CSV File'),
                'icon' => 'icon-info-sign'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Seller'),
                    'name' => 'id_seller',
                    'required' => true,
                    'options' => array(
                        'query' => $mp_sellers,
                        'id' => 'id_seller',
                        'name' => 'business_email'
                    ),
                    'col' => '3',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Request Number'),
                    'name' => 'request_id',
                    'readonly' => true,
                    'required' => true,
                    'col' => '3',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'allowEditComb',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Category'),
                    'name' => 'csv_category',
                    'required' => true,
                    'options' => array(
                        'query' => $csv_category,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'col' => '3',
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('CSV Type'),
                    'name' => 'csvType',
                    'values' => array(
                        array(
                            'id' => 'typeAdd',
                            'value' => 1,
                            'label' => 'Add'
                        ),
                        array(
                            'id' => 'typeUpdate',
                            'value' => 2,
                            'label' => 'Update'
                        ),
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Upload Info(.csv) File'),
                    'name' => 'csv_file',
                    'required' => true,
                    'col' => '6',
                    'hint' => $this->l('Upload the Products/Combinations csv file.')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Upload Product Image(.zip) File'),
                    'name' => 'img_zip_file',
                    'col' => '6',
                    'hint' => $this->l('Upload the product`s image .zip file.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable CSV File'),
                    'name' => 'enable_csv',
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                    'hint' => $this->l(
                        'If No, then csv file will be uploaded to server but
                         data inside the csv file will not be processed.'
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Upload File'),
                'icon' => 'process-icon-upload',
                'name' => 'wk_submit_mass_upload',
            )
        );

        if (Tools::getValue('exportmpproduct')) {
            $this->fields_form = array(
                'legend' => array(
                    'title' => $this->l('Export CSV File'),
                    'icon' => 'icon-info-sign'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Seller'),
                        'name' => 'wk_export_id_seller_customer',
                        'required' => true,
                        'class' => 'chosen',
                        'options' => array(
                            'query' => $mp_sellers,
                            'id' => 'seller_customer_id',
                            'name' => 'business_email'
                        ),
                        'col' => '3',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Category'),
                        'name' => 'wk_csv_category',
                        'required' => true,
                        'options' => array(
                            'query' => $csv_category,
                            'id' => 'id',
                            'name' => 'name'
                        ),
                        'col' => '3',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Columns'),
                        'name' => 'wk_csv_export_product_columns',
                        'multiple' => true,
                        'required' => true,
                        'options' => array(
                            'query' => $product_cols,
                            'id' => 'id',
                            'name' => 'name'
                        ),
                        'col' => '3',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Columns'),
                        'name' => 'wk_csv_export_combination_columns',
                        'multiple' => true,
                        'required' => true,
                        'options' => array(
                            'query' => $combination_cols,
                            'id' => 'id',
                            'name' => 'name'
                        ),
                        'col' => '3',
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select Language'),
                        'name' => 'wk_csv_export_langs',
                        'options' => array(
                            'query' => Language::getLanguages(true),
                            'id' => 'id_lang',
                            'name' => 'name'
                        ),
                        'col' => '3',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Export CSV'),
                    'icon' => 'process-icon-export',
                    'name' => 'wk_submit_mass_export'
                )
            );
        }

        return parent::renderForm();
    }

    public function processSave()
    {
        if (Tools::isSubmit('wk_submit_mass_upload')) {
            $id_seller = Tools::getValue('id_seller');
            $request_id = Tools::getValue('request_id');
            $mass_upload_category = Tools::getValue('csv_category');
            $approved = Tools::getValue('enable_csv');
            $csv_file = $_FILES["csv_file"];
            $img_file = $_FILES["img_zip_file"];

            $obj_mp_seller = new WkMpSeller($id_seller);
            $obj_massupload = new MarketplaceMassUpload();

            if ($obj_mp_seller->seller_customer_id) {
                $id_customer = $obj_mp_seller->seller_customer_id;
            }

            if (!$obj_mp_seller->id) {
                $this->errors[] = $this->l('Please select a seller for which you want to upload csv file.');
            } else {
                if (!$obj_mp_seller->seller_customer_id) {
                    $this->errors[] = $this->l('Please select a valid seller.');
                }
            }
            if ($approved === false) {
                $this->errors[] = $this->l('Please mention csv file is enabled or not.');
            }

            if (!count($this->errors)) {
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
                    $default_language = Configuration::get('PS_LANG_DEFAULT');

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

                    Tools::redirectAdmin(self::$currentIndex . '&conf=18&token=' . $this->token);
                }
            }
        }
        if (Tools::isSubmit('wk_submit_mass_export')) {
            $idCustomer = Tools::getValue('wk_export_id_seller_customer');
            $idCategory = Tools::getValue('wk_csv_category');
            $collumnsProd = Tools::getValue('wk_csv_export_product_columns');
            $collumnsComm = Tools::getValue('wk_csv_export_combination_columns');
            $languege = Tools::getValue('wk_csv_export_langs');

            if ($idCategory == 1) {
                if ($idCustomer) {
                    if (!$collumnsProd) {
                        $this->errors = $this->l('Please Select atleast one column.');
                        return;
                    }
                    $headings = $collumnsProd;
                    if (in_array('name', $headings)) {
                        unset($headings[array_search('name', $headings)]);
                        if ($languege) {
                            foreach ($languege as $lang) {
                                $string = 'name_' . (new Language($lang))->iso_code;
                                array_push($headings, $string);
                            }
                        }
                    }
                    if (in_array('short_description', $headings)) {
                        unset($headings[array_search('short_description', $headings)]);
                        if ($languege) {
                            foreach ($languege as $lang) {
                                $string = 'short_description_' . (new Language($lang))->iso_code;
                                array_push($headings, $string);
                            }
                        }
                    }
                    if (in_array('description', $headings)) {
                        unset($headings[array_search('description', $headings)]);
                        if ($languege) {
                            foreach ($languege as $lang) {
                                $string = 'description_' . (new Language($lang))->iso_code;
                                array_push($headings, $string);
                            }
                        }
                    }

                    header('Content-type: text/csv');
                    header('Content-Type: application/force-download; charset=UTF-8');
                    header('Cache-Control: no-store, no-cache');
                    header('Content-Disposition: attachment; filename=exportProduct.csv');
                    ob_end_clean();

                    $output = fopen("php://output", "w");

                    //headings of table
                    fputcsv($output, $headings);

                    $obj_marketplace_seller = new WkMpSeller();
                    $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($idCustomer);
                    if ($sellerProducts = WkMpSellerProduct::getSellerProduct($mp_seller['id_seller'])) {
                        foreach ($sellerProducts as $key => $sellerProduct) {
                            $listValue[$key] = array();
                            if (in_array('mp_id_product', $collumnsProd)) {
                                array_push($listValue[$key], $sellerProduct['id_mp_product']);
                            }
                            if (in_array('category_id', $collumnsProd)) {
                                $objSellerProduct = new WkMpSellerProduct();
                                if ($mpProCategory = $objSellerProduct->getSellerProductCategories(
                                    $sellerProduct['id_mp_product']
                                )
                                ) {
                                    $arrayCat = '';
                                    foreach ($mpProCategory as $key1 => $category) {
                                        // $arrayCat[$key1] = $category['id_category'];
                                        if ($key1 == 0) {
                                            $arrayCat .= $category['id_category'];
                                        } else {
                                            $arrayCat .= ', ' . $category['id_category'];
                                        }
                                    }
                                } else {
                                    $arrayCat = '';
                                }
                                array_push($listValue[$key], $arrayCat);
                            }
                            if (in_array('default_category', $collumnsProd)) {
                                array_push($listValue[$key], $sellerProduct['id_category']);
                            }
                            if (in_array('price', $collumnsProd)) {
                                array_push($listValue[$key], $sellerProduct['price']);
                            }
                            if (in_array('quantity', $collumnsProd)) {
                                array_push($listValue[$key], $sellerProduct['quantity']);
                            }
                            if (in_array('image_ref', $collumnsProd)) {
                                array_push($listValue[$key], 'image' . $key);
                            }
                            if (in_array('name', $collumnsProd)) {
                                if ($languege) {
                                    foreach ($languege as $lang) {
                                        $productName = WkMpSellerProduct::getSellerProductByIdProduct(
                                            $sellerProduct['id_mp_product'],
                                            $lang
                                        )['product_name'];
                                        if ($productName) {
                                            array_push(
                                                $listValue[$key],
                                                $productName
                                            );
                                        } else {
                                            array_push($listValue[$key], '');
                                        }
                                    }
                                }
                            }
                            if (in_array('short_description', $collumnsProd)) {
                                if ($languege) {
                                    foreach ($languege as $lang) {
                                        $shortDesc = WkMpSellerProduct::getSellerProductByIdProduct(
                                            $sellerProduct['id_mp_product'],
                                            $lang
                                        )['short_description'];
                                        if ($shortDesc) {
                                            array_push(
                                                $listValue[$key],
                                                $shortDesc
                                            );
                                        } else {
                                            array_push($listValue[$key], '');
                                        }
                                    }
                                }
                            }
                            if (in_array('description', $collumnsProd)) {
                                if ($languege) {
                                    foreach ($languege as $lang) {
                                        $desc = WkMpSellerProduct::getSellerProductByIdProduct(
                                            $sellerProduct['id_mp_product'],
                                            $lang
                                        )['description'];
                                        if ($desc) {
                                            array_push(
                                                $listValue[$key],
                                                $desc
                                            );
                                        } else {
                                            array_push($listValue[$key], '');
                                        }
                                    }
                                }
                            }
                        }
                        //data of table
                        foreach ($listValue as $row) {
                            fputcsv($output, $row);
                        }

                        fclose($output);

                        exit();
                    }
                }
            }

            // for combination csv
            if ($idCategory == 2) {
                if ($idCustomer) {
                    if (!$collumnsComm) {
                        $this->errors = $this->l('Please Select atleast one column.');
                        return;
                    }
                    $headings = $collumnsComm;
                    header('Content-type: text/csv');
                    header('Content-Type: application/force-download; charset=UTF-8');
                    header('Cache-Control: no-store, no-cache');
                    header('Content-Disposition: attachment; filename=exportCombination.csv');
                    ob_end_clean();

                    $output = fopen("php://output", "w");

                    // headings of table
                    fputcsv($output, $headings);

                    $obj_marketplace_seller = new WkMpSeller();
                    $objMassUpload = new MarketplaceMassUpload();
                    $ps_default_lang = Configuration::get('PS_LANG_DEFAULT');
                    $ps_default_iso_code = Language::getIsoById($ps_default_lang);
                    $fields = $objMassUpload->getCsvColumnDetail($collumnsComm, $ps_default_iso_code, 2);
                    $fieldArray = array();
                    foreach ($fields as $key => $field) {
                        $fieldArray[] = $key;
                    }
                    $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($idCustomer);
                    $sellerProducts = WkMpSellerProduct::getSellerProduct($mp_seller['id_seller']);

                    if ($sellerProducts) {
                        $count = 0;
                        foreach ($sellerProducts as $sellerProduct) {
                            $idPsProduct = $sellerProduct['id_ps_product'];
                            $objSellerProdAttr = new WkMpProductAttribute();
                            $sellerProductAttr = $objSellerProdAttr->getProductAttributes($sellerProduct['id_mp_product']);
                            if ($sellerProductAttr) {
                                foreach ($sellerProductAttr as $key => $productAttr) {
                                    $listValue[] = array();
                                    // push seller product id
                                    if (in_array('mp_id_product', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['id_mp_product']);
                                    }
                                    $objPsAttr = WkMpProductAttributeCombination::getPsAttributesSet(
                                        $productAttr['id_mp_product_attribute']
                                    );
                                    $objAttr = new Attribute($objPsAttr[0]['id_ps_attribute']);

                                    if (in_array('attribute_group', $fieldArray)) {
                                        $arrayAttrType = array();
                                        $arrayAttrvalue = array();
                                        if ($objPsAttr) {
                                            foreach ($objPsAttr as $idPsAttr) {
                                                $objAttr = new Attribute($idPsAttr['id_ps_attribute']);
                                                $obAttrGrp = new AttributeGroup($objAttr->id_attribute_group);
                                                $arrayAttrType[] = $obAttrGrp->name[$ps_default_lang] . ' : ' .
                                                    $obAttrGrp->group_type;
                                                $arrayAttrvalue[] = $objAttr->name[$ps_default_lang];
                                            }
                                        }
                                        // push seller product attribute type
                                        array_push($listValue[$count], implode(', ', $arrayAttrType));
                                    }
                                    if (in_array('attribute_value', $fieldArray)) {
                                        $arrayAttrType = array();
                                        $arrayAttrvalue = array();
                                        if ($objPsAttr) {
                                            foreach ($objPsAttr as $idPsAttr) {
                                                $objAttr = new Attribute($idPsAttr['id_ps_attribute']);
                                                $obAttrGrp = new AttributeGroup($objAttr->id_attribute_group);
                                                $arrayAttrType[] = $obAttrGrp->name[$ps_default_lang].' : '.
                                                $obAttrGrp->group_type;
                                                $arrayAttrvalue[] = $objAttr->name[$ps_default_lang];
                                            }
                                        }
                                        // push seller product attribute value
                                        array_push($listValue[$count], implode(', ', $arrayAttrvalue));
                                    }
                                    // push seller product reference
                                    if (in_array('mp_reference', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_reference']);
                                    }
                                    // push seller product EAN13
                                    if (in_array('mp_ean13', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_ean13']);
                                    }
                                    // push seller product UPC
                                    if (in_array('mp_upc', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_upc']);
                                    }
                                    // push seller product Wholesale Price
                                    if (in_array('mp_wholesale_price', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_wholesale_price']);
                                    }
                                    // push seller product Impact on price
                                    if (in_array('mp_price', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_unit_price_impact']);
                                    }
                                    // push seller product Quantity
                                    if (in_array('mp_quantity', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_quantity']);
                                    }
                                    // push seller product minimal quantity
                                    if (in_array('attribute_minimal_quantity', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_minimal_quantity']);
                                    }
                                    // push seller product Impact on weight
                                    if (in_array('attribute_weight', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_weight']);
                                    }
                                    // push seller product default
                                    if (in_array('mp_default_on', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_default_on']);
                                    }
                                    // push seller product combination Date
                                    if (in_array('available_date_attribute', $fieldArray)) {
                                        array_push($listValue[$count], $productAttr['mp_available_date']);
                                    }
                                    // push seller product Image ID
                                    if (in_array('ps_image_id_arr', $fieldArray)) {
                                    }
                                    $count++;
                                }
                            }
                        }
                    }
                    // data of table
                    if (isset($listValue)) {
                        foreach ($listValue as $row) {
                            fputcsv($output, $row);
                        }
                    }

                    fclose($output);
                    exit();
                }
            }
        }
    }

    public function processDelete()
    {
        $csv_path = dirname(__FILE__) . '/../../views/uploaded_csv/';
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $request_no = $object->request_id;
            unlink($csv_path . $request_no . ".csv");
        }
        parent::processDelete();
    }

    protected function processBulkDelete()
    {
        if ($this->tabAccess['delete'] === '1') {
            $success = 1;
            if (is_array($this->boxes) && !empty($this->boxes)) {
                $uploaded_data = Tools::getValue($this->table . 'Box');
                $csv_path = dirname(__FILE__) . '/../../views/uploaded_csv/';
                foreach ($uploaded_data as $upload_id) {
                    $upload_obj = new MarketplaceMassUpload($upload_id);
                    $request_no = $upload_obj->request_id;
                    unlink($csv_path . $request_no . ".csv");
                    $success &= $upload_obj->delete();
                }
                Tools::redirectAdmin(self::$currentIndex . '&conf=2&token=' . $this->token);
            } else {
                $this->errors[] = $this->l('You must select at least one element to delete.');
            }
        } else {
            $this->errors[] = $this->l('You do not have permission to delete this.');
        }
    }

    public function postProcess()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        if ($this->display == 'view') {
            $object = $this->loadObject();
            $id = $object->id;
            $obj = new MarketplaceMassUpload();
            $request_details = $obj->getRequestDetailsById($id);
            $this->context->smarty->assign('request_details', $request_details);
            $this->context->smarty->assign('csv_link', _MODULE_DIR_ . 'mpmassupload/views/uploaded_csv');
        }
        parent::postProcess();
    }

    public function processStatus()
    {
        $this->loadObject(true);
        if (!Validate::isLoadedObject($this->object)) {
            return false;
        }

        if ($this->object->is_approve == 0) {
            $request_no = $this->object->request_id;
            $seller_id = $this->object->id_seller;
            $obj_massupload = new MarketplaceMassUpload($this->object->id);
            $obj_massupload->status = 'Approved';
            $obj_massupload->is_approve = 1;
            if ($obj_massupload->save()) {
                if ($this->object->is_csv_product_added == '0') {
                    if ($obj_massupload->mass_upload_category == 1) {
                        $return_data = $this->createSellerProductsByMassUpload($this->object->id, $request_no, $seller_id, $this->object->total_records);
                    } elseif ($obj_massupload->mass_upload_category == 2) {
                        $return_data = $this->createSellerCombinationsByMassUpload($this->object->id, $request_no, $seller_id, $this->object->total_records);
                    }

                    if ($return_data) {
                        if ($return_data != 'true') {
                            $this->context->cookie->wk_massupload_warning = $return_data;
                            $this->context->cookie->write();
                        }
                    }
                }
            }
        }
        Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
    }

    public function createSellerCombinationsByMassUpload($id, $request_id, $id_seller, $total_records)
    {
        $obj_massupload = new MarketplaceMassUpload($id);
        $obj_mp_seller = new WkMpSeller($id_seller);

        $id_customer = $obj_mp_seller->seller_customer_id;

        $ps_id_shop = $this->context->shop->id;
        $warning_string = $this->getWarningString(2);

        $kwargs = array(
            'id_customer' => $id_customer,
            'id_seller' => $id_seller,
            'request_id' => $request_id,
            'total_records' => $total_records,
            'mass_upload_category' => 2,
            'ps_id_shop' => $ps_id_shop,
            'approved' => 1,
            'toggle_status' => 1,
            'id_mass_upload' => $id,
            'warning_string' => $warning_string,
            'csvType' => $obj_massupload->csv_type,
        );

        $upload_rtn = $obj_massupload->uploadCsvFile($kwargs);
        if ($upload_rtn['is_warning']) {
            return $upload_rtn['warning_msg'];
        }

        return true;
    }

    public function createSellerProductsByMassUpload($id, $request_id, $id_seller, $total_records)
    {
        $obj_massupload = new MarketplaceMassUpload($id);
        $obj_mp_seller = new WkMpSeller($id_seller);

        $id_customer = $obj_mp_seller->seller_customer_id;
        $ps_id_shop = $this->context->shop->id;

        $kwargs = array(
            'id_customer' => $id_customer,
            'id_seller' => $id_seller,
            'request_id' => $request_id,
            'total_records' => $total_records,
            'mass_upload_category' => 1,
            'ps_id_shop' => $ps_id_shop,
            'approved' => 1,
            'toggle_status' => 1,
            'id_mass_upload' => $id,
            'csvType' => $obj_massupload->csv_type,
        );

        $upload_rtn = $obj_massupload->uploadCsvFile($kwargs);
        return true;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJs(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upload_script.js');
    }

    public function getErrorString($mass_upload_category)
    {
        $error_string = array();
        $error_string['csv_format'] = $this->l('Invalid file format . Please upload CSV file.');
        $error_string['zip_format'] = $this->l('Invalid file format . Please upload image zip file.');
        $error_string['csv_structure'] = $this->l('Submitted CSV file is not valid. Please check the structure of CSV file by downloading dummy file.');
        $error_string['upd_col_req'] = $this->l('Add column(fields) in CSV which you want to update.');
        $error_string['zip_file'] = $this->l('Product image .zip file is not valid, please check and try again.');
        $error_string['error_prefix'] = $this->l('Line number: ');
        $error_string['error_suffix'] = $this->l(' is having error. ');
        $error_string['request_no_req'] = $this->l('Request Number is required.');
        $error_string['file_cate_req'] = $this->l('Csv file category is mandatory.');
        $error_string['upload_csv_file'] = $this->l('Please upload CSV File.');

        $error_string['mp_id_prod_2'] = $this->l('Please enter valid marketplace product id.');

        // For Multi Lang
        $error_string['def_lanf_col_req_1'] = $this->l('Product name is required in default language. Please create a column of ');
        $error_string['def_lanf_col_req_2'] = $this->l(' and enter language wise product name in it. For more info please download dummy file.');
        $error_string['prod_name_def_lang'] = $this->l('Product name is required in.');
        $error_string['prod_name_011'] = $this->l('Product Name field is too long ');
        $error_string['prod_name_012'] = $this->l('chars max');
        // For Multi Lang

        $error_string['prod_name_1'] = $this->l('Product name is required fields.');
        $error_string['prod_name_2'] = $this->l('Product name must not have Invalid characters') . ' <>;=#{}';
        $error_string['short_desc_1'] = $this->l('Short description in invalid.');
        $error_string['short_desc_2_1'] = $this->l('This short description field is too long.');
        $error_string['short_desc_2_2'] = $this->l(' characters max.');
        $error_string['description'] = $this->l('Product description in invalid.');
        $error_string['prod_price_1'] = $this->l('Product price should be numeric.');
        $error_string['prod_price_2'] = $this->l('Product price is required field.');
        $error_string['prod_qty_1'] = $this->l('Product quantity should be numeric.');
        $error_string['prod_qty_2'] = $this->l('Product quantity is required field.');
        $error_string['prod_img'] = $this->l('Image format not recognized, allowed formats are: .gif, .jpg, .png');

        if ($mass_upload_category == 2) {
            $error_string['comb_install_enable'] = $this->l('Combiantion CSV is not allowed to be processed.');

            // Edit combination CSV is not allowed
            $error_string['edit_comb_not_allowed'] = $this->l('Edit combination by CSV is not allowed.');

            // For Multi Lang
            $error_string['for_language'] = $this->l(' for language ');
            // For Multi Lang

            // For Multi Lang
            $error_string['def_lanf_com_col_req_1'] = $this->l('Attribute group and attribute value column is required in default language.');
            // For Multi Lang

            $error_string['mp_id_prod_1'] = $this->l('Please enter product id.');
            $error_string['attr_grp_1'] = $this->l('Please enter combination attribute.');
            $error_string['attr_grp_2'] = $this->l('Please enter attribute details');
            $error_string['attr_grp_3'] = $this->l('Multiple times same Attribute name is not allowed for one combination.');
            $error_string['attr_grp_4'] = $this->l('Attribute Name is required.');
            $error_string['attr_grp_5'] = $this->l('Attribute Name is invalid.');
            $error_string['attr_grp_6'] = $this->l('Attribute type is required.');
            $error_string['attr_grp_7'] = $this->l('Attribute type is invalid.');
            // For Multi Lang
            $error_string['attr_grp_011'] = $this->l('Attribute is required in default language. So please create a column of ');
            $error_string['attr_grp_012'] = $this->l(' and enter language wise attribute group in it. For more info please download dummy file of combination.');
            $error_string['attr_grp_02'] = $this->l('Attribute is required in default language');

            $error_string['attr_val_011'] = $this->l('Attribute Value is required in default language. So please create a column of ');
            $error_string['attr_val_012'] = $this->l(' and enter language wise attribute value in it. For more info please download dummy file of combination.');
            $error_string['attr_val_02'] = $this->l('Attribute value detail is required in default language.');
            // For Multi Lang
            $error_string['attr_val_1'] = $this->l('Please enter combination attribute value.');
            $error_string['attr_val_2'] = $this->l('Please enter attribute value details.');
            $error_string['attr_val_3'] = $this->l('Attribute Value is required.');
            $error_string['attr_val_4'] = $this->l('Attribute Value is invalid.');
            $error_string['comb_ean13'] = $this->l('Field EAN13 is not valid.');
            $error_string['comb_upc'] = $this->l('Field UPC is not valid.');
            $error_string['wholesale_price'] = $this->l('Wholesale price must be numeric.');
            $error_string['impact_price'] = $this->l('Impact price must be numeric.');
            $error_string['comb_qty'] = $this->l('Quantity should be integer.');
            $error_string['comb_min_qty'] = $this->l('Minimum quantity  should be integer and greater than 0.');
            $error_string['comb_weight'] = $this->l('Impact on weight must be numeric.');
            $error_string['comb_default'] = $this->l('Invalid default value, accepted values are 1|0.');
            $error_string['comb_date_formate'] = $this->l('Must be valid date format.');
            $error_string['prod_img_check'] = $this->l('Please enter image id of respective product.');
        }
        return $error_string;
    }

    public function getWarningString($mass_upload_category)
    {
        $warning_str = array();
        if ($mass_upload_category == 2) {
            $warning_str['warning_prefix'] = $this->l('Line number: ');
            $warning_str['attr_not_exist'] = $this->l('All the attribute of this combination is not present, so this combination can not be created. Please contact admin for giving the permission of creating new attributes and there values.') . '===';
            $warning_str['comb_exist'] = $this->l('Combination is already exists for this product.') . '===';
            $warning_str['comb_not_exist'] = $this->l('Combination is not exists for this product.') . '===';
        }
        return $warning_str;
    }
}
