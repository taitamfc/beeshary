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

class mpmassuploadexportdetailsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $link = new Link();
        if ($id_customer = $this->context->customer->id) {
            $obj_marketplace_seller = new WkMpSeller();
            $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($id_customer);
            $allowDownloadImages = Configuration::get('MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES');
            $wkSmartVar = [
                'allowDownloadImages' => $allowDownloadImages,
                'logic' => 'massupload',
                'is_seller' => 1,
                'languages' => Language::getLanguages(true),
                'MASS_UPLOAD_ALLOW_EDIT_COMBINATION' => Configuration::get('MASS_UPLOAD_ALLOW_EDIT_COMBINATION'),
                'exportControllerLink' => $link->getModuleLink('mpmassupload', 'exportdetails')
            ];
            if (Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE')) {
                $wkSmartVar['massupload_combination_approve'] = Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE');
            }

            $this->context->smarty->assign($wkSmartVar);

            $this->setTemplate('module:' . $this->module->name . '/views/templates/front/exportproduct.tpl');
        }
    }

    public function postProcess()
    {
        $objMassUpload = new MarketplaceMassUpload();
        if (Tools::isSubmit('export_csv')) {
            if ($idCustomer = $this->context->customer->id) {
                $collumns = Tools::getValue('wk_massupload_selected_col');
                $languege = Tools::getValue('wk_massupload_selected_lang');
                if (!$collumns) {
                    $this->errors = $this->l('Please Select atleast one column.');
                    return;
                }
                $headings = $collumns;
                if (in_array('name', $headings)) {
                    unset($headings[array_search('name', $headings)]);
                    if ($languege) {
                        foreach ($languege as $lang) {
                            $string = 'name_'.(new Language($lang))->iso_code;
                            array_push($headings, $string);
                        }
                    }
                }
                if (in_array('short_description', $headings)) {
                    unset($headings[array_search('short_description', $headings)]);
                    if ($languege) {
                        foreach ($languege as $lang) {
                            $string = 'short_description_'.(new Language($lang))->iso_code;
                            array_push($headings, $string);
                        }
                    }
                }
                if (in_array('description', $headings)) {
                    unset($headings[array_search('description', $headings)]);
                    if ($languege) {
                        foreach ($languege as $lang) {
                            $string = 'description_'.(new Language($lang))->iso_code;
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
                $sellerProducts = WkMpSellerProduct::getSellerProduct($mp_seller['id_seller']);
                if ($sellerProducts) {
                    foreach ($sellerProducts as $key => $sellerProduct) {
                        $listValue[$key] = array();
                        if (in_array('mp_id_product', $collumns)) {
                            array_push($listValue[$key], $sellerProduct['id_mp_product']);
                        }
                        if (in_array('category_id', $collumns)) {
                            $objSellerProduct = new WkMpSellerProduct;
                            $mpProCategory = $objSellerProduct->getSellerProductCategories($sellerProduct['id_mp_product']);
                            if ($mpProCategory) {
                                $arrayCat = '';
                                foreach ($mpProCategory as $key1 => $category) {
                                    if ($key1 == 0) {
                                        $arrayCat .= $category['id_category'];
                                    } else {
                                        $arrayCat .= ', '.$category['id_category'];
                                    }
                                }
                            }
                            array_push($listValue[$key], $arrayCat);
                        }
                        if (in_array('default_category', $collumns)) {
                            array_push($listValue[$key], $sellerProduct['id_category']);
                        }
                        if (in_array('price', $collumns)) {
                            array_push($listValue[$key], $sellerProduct['price']);
                        }
                        if (in_array('quantity', $collumns)) {
                            array_push($listValue[$key], $sellerProduct['quantity']);
                        }
                        if (Configuration::get('MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES')) {
                            if (in_array('image_ref', $collumns)) {
                                array_push($listValue[$key], 'image'.$key+1);
                            }
                        }
                        if (in_array('name', $collumns)) {
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
                        if (in_array('short_description', $collumns)) {
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
                        if (in_array('description', $collumns)) {
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

                    exit;
                }
            }
        }
        /* @ [Zip download] */
        if (Tools::getValue('zipexp') == 1 && Configuration::get('MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES')) {
            if ($idCustomer = $this->context->customer->id) {
                $obj_marketplace_seller = new WkMpSeller();
                $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($idCustomer);
                $sellerProducts = WkMpSellerProduct::getSellerProduct($mp_seller['id_seller']);

                MarketplaceMassUpload::generateArchive($sellerProducts, $idCustomer);
            }
        }
        // for combination csv
        if (Tools::isSubmit('export_comb_csv')) {
            if ($idCustomer = $this->context->customer->id) {
                $collumns = Tools::getValue('wk_massupload_selected_col');
                if (!$collumns) {
                    $this->errors = $this->l('Please Select atleast one column.');
                    return;
                }
                $headings = $collumns;
                header('Content-type: text/csv');
                header('Content-Type: application/force-download; charset=UTF-8');
                header('Cache-Control: no-store, no-cache');
                header('Content-Disposition: attachment; filename=exportCombination.csv');
                ob_end_clean();

                $output = fopen("php://output", "w");

                // headings of table
                fputcsv($output, $headings);

                $obj_marketplace_seller = new WkMpSeller();
                $ps_default_lang = Configuration::get('PS_LANG_DEFAULT');
                $ps_default_iso_code = Language::getIsoById($ps_default_lang);
                $fields = $objMassUpload->getCsvColumnDetail($collumns, $ps_default_iso_code, 2);
                $fieldArray = array();
                foreach ($fields as $key => $field) {
                    $fieldArray[] = $key;
                }
                $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($idCustomer);
                $sellerProducts = WkMpSellerProduct::getSellerProduct($mp_seller['id_seller']);

                if ($sellerProducts) {
                    $count = 0;
                    foreach ($sellerProducts as $sellerProduct) {
                        $objSellerProdAttr = new WkMpProductAttribute();
                        $sellerProductAttr = $objSellerProdAttr->getProductAttributes($sellerProduct['id_mp_product']);
                        if ($sellerProductAttr) {
                            foreach ($sellerProductAttr as $key => $productAttr) {
                                $listValue[] = array();
                                // push seller product id
                                if (in_array('mp_id_product', $fieldArray)) {
                                    array_push($listValue[$count], $productAttr['id_mp_product']);
                                }
                                $objPsAttr= WkMpProductAttributeCombination::getPsAttributesSet(
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
                                            $arrayAttrType[] = $obAttrGrp->name[$ps_default_lang].' : '.
                                            $obAttrGrp->group_type;
                                            $arrayAttrvalue[] = $objAttr->name[$ps_default_lang];
                                        }
                                    }
                                    // push seller product attribute type
                                    array_push($listValue[$count], implode(', ', $arrayAttrType));
                                    // push seller product attribute value
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
                                // if (in_array('ps_image_id_arr', $fieldArray)) {

                                // }
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

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('request-form', 'modules/' . $this->module->name . '/views/css/requestform.css');
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerJavascript('upload_script', 'modules/' . $this->module->name . '/views/js/upload_script.js');
        $this->registerJavascript('upload_script', 'modules/' . $this->module->name . '/views/js/exportproduct.js');
    }
}
