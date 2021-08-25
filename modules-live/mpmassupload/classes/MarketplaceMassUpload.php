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

class MarketplaceMassUpload extends ObjectModel
{
    const EMPTY_IDENTIFY = 'empty';

    public $editCsv;                    // 1|0

    public $id;
    public $mass_upload_category;
    public $request_id;
    public $id_seller;
    public $is_approve;
    public $total_records;
    public $status;
    public $is_csv_product_added;
    public $csv_type;                        // 1: For Add | 2: For Update
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'marketplace_mass_upload',
        'primary' => 'id',
        'fields' => array(
            'mass_upload_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'request_id' => array('type' => self::TYPE_STRING, 'required' => true),
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'total_records' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'status' => array('type' => self::TYPE_STRING, 'required' => true),
            'is_approve' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_csv_product_added' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'csv_type' => array('type' => self::TYPE_STRING, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function generateRandomeNumber($numLength)
    {
        $random_number = '';

        $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for ($i = 0; $i < $numLength; $i++) {
            $random_number .= $characters[mt_rand(0, 35)];
        }

        return $random_number;
    }

    public function add($autodate = true, $null_values = false)
    {
        if (!parent::add($autodate, $null_values)) {
            return false;
        }
        return Db::getInstance()->Insert_ID();
    }

    public function update($null_values = false)
    {
        Cache::clean('getContextualValue_' . $this->id . '_*');
        $success = parent::update($null_values);
        return $success;
    }

    public function delete()
    {
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'marketplace_mass_upload` WHERE `id` = ' . (int)$this->id);
        return parent::delete();
    }

    public function getMassUploadByIdSeller($id_seller)
    {
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "marketplace_mass_upload` WHERE `id_seller`=" . $id_seller;
        $list = Db::getInstance()->executeS($sql);
        if (empty($list)) {
            return false;
        } else {
            return $list;
        }
    }

    /*public function getAllRequestByMassUploadCategory($mp_id_shop, $mass_upload_category)
    {
        $list = Db::getInstance()->executeS("SELECT mmu.*,mps.shop_name FROM `"._DB_PREFIX_."marketplace_mass_upload` mmu
                                LEFT JOIN "._DB_PREFIX_ ."marketplace_shop mps
                                ON (mmu.mp_id_shop = mps.id) WHERE mmu.mp_id_shop = '".$mp_id_shop." AND `mmu.mass_upload_category` = '".$mass_upload_category."'");
        if(empty($list))
            return false;
        else
            return $list;
    }*/

    public function getRequestDetailsById($id_mass_upload)
    {
        $sql = "SELECT mmu.*, msil.`shop_name`
				FROM `" . _DB_PREFIX_ . "marketplace_mass_upload` AS mmu
				INNER JOIN `" . _DB_PREFIX_ . "wk_mp_seller` msi ON (mmu.`id_seller`= msi.`id_seller`)
				INNER JOIN `" . _DB_PREFIX_ . "wk_mp_seller_lang` msil ON (msil.id_seller = msi.`id_seller` AND msil.id_lang = " . Configuration::get('PS_LANG_DEFAULT') . ")
				WHERE mmu.`id` = " . (int)$id_mass_upload;

        $list = Db::getInstance()->getRow($sql);
        if ($list) {
            return $list;
        }

        return false;
    }

    /**
     * delete directory recursively
     */
    public function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
        return 1;
    }

    public function getPsProductIdByImageId($ps_image_id)
    {
        $list = Db::getInstance()->getValue("SELECT id_product FROM `" . _DB_PREFIX_ . "image` WHERE `id_image` = '" . $ps_image_id . "'");
        if (empty($list)) {
            return false;
        } else {
            return $list;
        }
    }

    public function getCategoryInfoByCategoryId($id_category)
    {
        $list = Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "category` WHERE `id_category` = '" . (int)$id_category . "' AND active=1");
        if (empty($list)) {
            return false;
        } else {
            return $list;
        }
    }

    public static function hasPrductDefaultCombination($mp_id_product)
    {
        return Db::getInstance()->getRow('SELECT `id_mp_product_attribute`, `mp_default_on` FROM ' . _DB_PREFIX_ . 'wk_mp_product_attribute WHERE `id_mp_product` = ' . (int)$mp_id_product . ' AND mp_default_on = 1');
    }

    public function ColorNameToHex($color_name)
    {
        // standard 147 HTML color names
        $colors = array(
            'aliceblue' => 'F0F8FF',
            'antiquewhite' => 'FAEBD7',
            'aqua' => '00FFFF',
            'aquamarine' => '7FFFD4',
            'azure' => 'F0FFFF',
            'beige' => 'F5F5DC',
            'bisque' => 'FFE4C4',
            'black' => '000000',
            'blanchedalmond ' => 'FFEBCD',
            'blue' => '0000FF',
            'blueviolet' => '8A2BE2',
            'brown' => 'A52A2A',
            'burlywood' => 'DEB887',
            'cadetblue' => '5F9EA0',
            'chartreuse' => '7FFF00',
            'chocolate' => 'D2691E',
            'coral' => 'FF7F50',
            'cornflowerblue' => '6495ED',
            'cornsilk' => 'FFF8DC',
            'crimson' => 'DC143C',
            'cyan' => '00FFFF',
            'darkblue' => '00008B',
            'darkcyan' => '008B8B',
            'darkgoldenrod' => 'B8860B',
            'darkgray' => 'A9A9A9',
            'darkgreen' => '006400',
            'darkgrey' => 'A9A9A9',
            'darkkhaki' => 'BDB76B',
            'darkmagenta' => '8B008B',
            'darkolivegreen' => '556B2F',
            'darkorange' => 'FF8C00',
            'darkorchid' => '9932CC',
            'darkred' => '8B0000',
            'darksalmon' => 'E9967A',
            'darkseagreen' => '8FBC8F',
            'darkslateblue' => '483D8B',
            'darkslategray' => '2F4F4F',
            'darkslategrey' => '2F4F4F',
            'darkturquoise' => '00CED1',
            'darkviolet' => '9400D3',
            'deeppink' => 'FF1493',
            'deepskyblue' => '00BFFF',
            'dimgray' => '696969',
            'dimgrey' => '696969',
            'dodgerblue' => '1E90FF',
            'firebrick' => 'B22222',
            'floralwhite' => 'FFFAF0',
            'forestgreen' => '228B22',
            'fuchsia' => 'FF00FF',
            'gainsboro' => 'DCDCDC',
            'ghostwhite' => 'F8F8FF',
            'gold' => 'FFD700',
            'goldenrod' => 'DAA520',
            'gray' => '808080',
            'green' => '008000',
            'greenyellow' => 'ADFF2F',
            'grey' => '808080',
            'honeydew' => 'F0FFF0',
            'hotpink' => 'FF69B4',
            'indianred' => 'CD5C5C',
            'indigo' => '4B0082',
            'ivory' => 'FFFFF0',
            'khaki' => 'F0E68C',
            'lavender' => 'E6E6FA',
            'lavenderblush' => 'FFF0F5',
            'lawngreen' => '7CFC00',
            'lemonchiffon' => 'FFFACD',
            'lightblue' => 'ADD8E6',
            'lightcoral' => 'F08080',
            'lightcyan' => 'E0FFFF',
            'lightgoldenrodyellow' => 'FAFAD2',
            'lightgray' => 'D3D3D3',
            'lightgreen' => '90EE90',
            'lightgrey' => 'D3D3D3',
            'lightpink' => 'FFB6C1',
            'lightsalmon' => 'FFA07A',
            'lightseagreen' => '20B2AA',
            'lightskyblue' => '87CEFA',
            'lightslategray' => '778899',
            'lightslategrey' => '778899',
            'lightsteelblue' => 'B0C4DE',
            'lightyellow' => 'FFFFE0',
            'lime' => '00FF00',
            'limegreen' => '32CD32',
            'linen' => 'FAF0E6',
            'magenta' => 'FF00FF',
            'maroon' => '800000',
            'mediumaquamarine' => '66CDAA',
            'mediumblue' => '0000CD',
            'mediumorchid' => 'BA55D3',
            'mediumpurple' => '9370D0',
            'mediumseagreen' => '3CB371',
            'mediumslateblue' => '7B68EE',
            'mediumspringgreen' => '00FA9A',
            'mediumturquoise' => '48D1CC',
            'mediumvioletred' => 'C71585',
            'midnightblue' => '191970',
            'mintcream' => 'F5FFFA',
            'mistyrose' => 'FFE4E1',
            'moccasin' => 'FFE4B5',
            'navajowhite' => 'FFDEAD',
            'navy' => '000080',
            'oldlace' => 'FDF5E6',
            'olive' => '808000',
            'olivedrab' => '6B8E23',
            'orange' => 'FFA500',
            'orangered' => 'FF4500',
            'orchid' => 'DA70D6',
            'palegoldenrod' => 'EEE8AA',
            'palegreen' => '98FB98',
            'paleturquoise' => 'AFEEEE',
            'palevioletred' => 'DB7093',
            'papayawhip' => 'FFEFD5',
            'peachpuff' => 'FFDAB9',
            'peru' => 'CD853F',
            'pink' => 'FFC0CB',
            'plum' => 'DDA0DD',
            'powderblue' => 'B0E0E6',
            'purple' => '800080',
            'red' => 'FF0000',
            'rosybrown' => 'BC8F8F',
            'royalblue' => '4169E1',
            'saddlebrown' => '8B4513',
            'salmon' => 'FA8072',
            'sandybrown' => 'F4A460',
            'seagreen' => '2E8B57',
            'seashell' => 'FFF5EE',
            'sienna' => 'A0522D',
            'silver' => 'C0C0C0',
            'skyblue' => '87CEEB',
            'slateblue' => '6A5ACD',
            'slategray' => '708090',
            'slategrey' => '708090',
            'snow' => 'FFFAFA',
            'springgreen' => '00FF7F',
            'steelblue' => '4682B4',
            'tan' => 'D2B48C',
            'teal' => '008080',
            'thistle' => 'D8BFD8',
            'tomato' => 'FF6347',
            'turquoise' => '40E0D0',
            'violet' => 'EE82EE',
            'wheat' => 'F5DEB3',
            'white' => 'FFFFFF',
            'whitesmoke' => 'F5F5F5',
            'yellow' => 'FFFF00',
            'yellowgreen' => '9ACD32'
        );

        $color_name = Tools::strtolower($color_name);
        if (isset($colors[$color_name])) {
            return ('#' . $colors[$color_name]);
        } else {
            return ($color_name);
        }
    }

    public function sendMailToSeller($customer_id, $req_id)
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;
        $obj_customer = new Customer($customer_id);
        $firstname = $obj_customer->firstname;
        $lastname = $obj_customer->lastname;
        $email = $obj_customer->email;
        $currency = Currency::getDefaultCurrency();
        $customer_vars = array(
            '{firstname}' => $firstname,
            '{lastname}' => $lastname,
            '{email}' => $email,
            '{reference_number}' => $req_id,
            '{currency}' => $currency->id
        );
        $template_path = _PS_MODULE_DIR_ . 'mpmassupload/mails/';

        Mail::Send(
            (int)$id_lang,
            'seller_info_about_massupload',
            Mail::l('Mass Upload Request ' . $req_id . ' has been approved by admin', (int)$id_lang),
            $customer_vars,
            $email,
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

    public function getProdIdByImgPsId($ps_id_img)
    {
        $sql = 'SELECT psi.`id_product` AS ps_id_prod, msp.`id_mp_product` AS mp_id_prod
				FROM `' . _DB_PREFIX_ . 'image` AS psi
				INNER JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_product` AS msp ON (psi.id_product = msp.id_ps_product)
				WHERE psi.id_image = ' . (int)$ps_id_img;

        $result = Db::getInstance()->getRow($sql);

        if ($result) {
            return $result;
        }

        return false;
    }

    public function validateCsvHeader($headerArray, $massUploadCategory, $defaultLang, &$csvError, $errorString)
    {
        if ($massUploadCategory == 1) {                                                    // Product CSV
            if ($this->editCsv) {
                if (!in_array('mp_id_product', $headerArray)) {
                    $csvError[] = $errorString['csv_structure'];
                } else {
                    if (count($headerArray) <= 1) {
                        $csvError[] = $errorString['upd_col_req'];
                    }
                }
            } else {                                                                    // Add Product
                $fields = array('name', 'category_id', 'default_category', 'short_description', 'description', 'price', 'quantity', 'image_ref');
                if (!Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {                // Single lang CSV
                    foreach ($fields as $field) {
                        if (!in_array($field, $headerArray)) {
                            $csvError[] = $errorString['csv_structure'];
                            break;
                        }
                    }
                } else {                                                                // Multi-lang CSV
                    $isoCode = Language::getIsoById($defaultLang);
                    foreach ($fields as $field) {
                        if ($field == 'short_description' || $field == 'description') {
                            if (!preg_grep('/' . $field . '_/i', $headerArray)) {
                                $csvError[] = $errorString['csv_structure'];
                                break;
                            }
                        } elseif ($field == 'name') {
                            if (!in_array($field . '_' . $isoCode, $headerArray)) {
                                $csvError[] = $errorString['def_lanf_col_req_1'] . 'name_' . $isoCode . $errorString['def_lanf_col_req_2'];
                                break;
                            }
                        } elseif (!in_array($field, $headerArray)) {
                            $csvError[] = $errorString['csv_structure'];
                            break;
                        }
                    }
                }
            }
        } elseif ($massUploadCategory == 2) {                                            // Combination CSV
            if ($this->editCsv) {                                                        // Edit Combination
                $fields = array('Seller Product ID', 'Attribute (Name:Type)', 'Value');
                foreach ($fields as $field) {
                    if (!in_array($field, $headerArray)) {
                        $csvError[] = $errorString['csv_structure'];
                        break;
                    }
                }

                // if (!Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                // 	$fields = array('Seller Product ID','Attribute (Name:Type)','Value');
                // 	foreach($fields as $field) {
                // 		if(!in_array($field, $headerArray)) {
                // 			$csvError[] = $errorString['csv_structure'];
                // 			break;
                // 		}
                // 	}
                // } else {
                // 	$isoCode = Language::getIsoById($defaultLang);
                // 	$fields = array('Seller Product ID','Attribute_'.$isoCode.'#(Name:Type)','Value_'.$isoCode);
                // 	foreach($fields as $field) {
                // 		if(preg_match('/Attribute_/i', $field) || preg_match('/Value_/i', $field)) {
                // 			if (!in_array('Attribute_'.$isoCode.'#(Name:Type)', $headerArray) || !in_array('Value_'.$isoCode, $headerArray)) {
                // 				$csvError[] = $errorString['def_lanf_com_col_req_1'];
                // 				break;
                // 			}
                // 		} elseif(!in_array($field, $headerArray)) {
                // 			$csvError[] = $errorString['csv_structure'];
                // 			break;
                // 		}
                // 	}
                // }
            } else {                                                                    // Add Combination
                $fields = array('Seller Product ID', 'Attribute (Name:Type)', 'Value', 'Supplier reference', 'Reference', 'EAN13', 'UPC', 'Wholesale price', 'Impact on price', 'Ecotax', 'Quantity', 'Minimal quantity', 'Impact on weight', 'Default (0 = No, 1 = Yes)', 'Combination available date', 'Image ID');
                foreach ($fields as $field) {
                    if (!in_array($field, $headerArray)) {
                        $csvError[] = $errorString['csv_structure'];
                        break;
                    }
                }

                // if (!Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                // 	$fields = array('Seller Product ID','Attribute (Name:Type)','Value','Supplier reference','Reference','EAN13','UPC','Wholesale price','Impact on price','Ecotax','Quantity','Minimal quantity','Impact on weight','Default (0 = No, 1 = Yes)','Combination available date','Image ID');

                // 	foreach($fields as $field) {
                // 		if(!in_array($field, $headerArray)) {
                // 			$csvError[] = $errorString['csv_structure'];
                // 			break;
                // 		}
                // 	}
                // } else {
                // 	$isoCode = Language::getIsoById($defaultLang);
                // 	$fields = array('Seller Product ID','Attribute_'.$isoCode.'#(Name:Type)','Value_'.$isoCode,'Supplier reference','Reference','EAN13','UPC','Wholesale price','Impact on price','Ecotax','Quantity','Minimal quantity','Impact on weight','Default (0 = No, 1 = Yes)','Combination available date','Image ID');

                // 	foreach($fields as $field) {
                // 		if(preg_match('/Attribute_/i', $field) || preg_match('/Value_/i', $field)) {
                // 			if (!in_array('Attribute_'.$isoCode.'#(Name:Type)', $headerArray) || !in_array('Value_'.$isoCode, $headerArray)) {
                // 				$csvError[] = $errorString['def_lanf_com_col_req_1'];
                // 				break;
                // 			}
                // 		} elseif(!in_array($field, $headerArray)) {
                // 			$csvError[] = $errorString['csv_structure'];
                // 			break;
                // 		}
                // 	}
                // }
            }
        }

        return true;
    }

    public function getCsvColumnDetail($file_structure, $defaultIsoCode, $mass_upload_category)
    {
        $multiLangOn = Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE');
        $field_index = array();
        if ($mass_upload_category == 1) {
            foreach ($file_structure as $key => $value) {
                if (trim($value)) {
                    if (preg_match('/name/i', $value)) {
                        if ($multiLangOn) {
                            $name_code_arr = explode("_", $value);
                            $isoCode = $name_code_arr[1];
                            $field_index['product_name'][$isoCode] = $key;
                            if ('name_' . $defaultIsoCode == $value) {
                            }
                        } else {
                            $field_index['product_name'][$defaultIsoCode] = $key;
                        }
                    } elseif (preg_match('/category_id/i', $value)) {
                        $field_index['product_categories'] = $key;
                    } elseif (preg_match('/default_category/i', $value)) {
                        $field_index['default_category'] = $key;
                    } elseif (preg_match('/short_description/i', $value)) {
                        if ($multiLangOn) {
                            $sdesc_code_arr = explode("_", $value);
                            $isoCode = $sdesc_code_arr[2];
                            $field_index['short_description'][$isoCode] = $key;
                        } else {
                            $field_index['short_description'][$defaultIsoCode] = $key;
                        }
                    } elseif (preg_match('/description/i', $value)) {
                        if ($multiLangOn) {
                            $desc_code_arr = explode("_", $value);
                            $isoCode = $desc_code_arr[1];
                            $field_index['description'][$isoCode] = $key;
                        } else {
                            $field_index['description'][$defaultIsoCode] = $key;
                        }
                    } elseif (preg_match('/price/i', $value)) {
                        $field_index['product_price'] = $key;
                    } elseif (preg_match('/quantity/i', $value)) {
                        $field_index['product_quantity'] = $key;
                    } elseif (preg_match('/image_ref/i', $value)) {
                        $field_index['image_ref'] = $key;
                    } elseif (preg_match('/mp_id_product/i', $value)) {
                        $field_index['mp_id_product'] = $key;
                    }
                }
            }
        } elseif ($mass_upload_category == 2) {
            foreach ($file_structure as $key => $value) {
                if (preg_match('/Seller Product ID/i', $value)) {
                    $field_index['mp_id_product'] = $key;
                } elseif (preg_match('/Attribute/i', $value)) {
                    $field_index['attribute_group'][$defaultIsoCode] = $key;
                    // if ($multiLangOn) {
                    // 	$attrgrp_code_arr = explode("_", $value);
                    // 	$code_format_arr = explode("#", $attrgrp_code_arr[1]);
                    // 	$isoCode = $code_format_arr[0];

                    // 	$field_index['attribute_group'][$isoCode] = $key;
                    // } else {
                    // 	$field_index['attribute_group'][$defaultIsoCode] = $key;
                    // }
                } elseif (preg_match('/Value/i', $value)) {
                    $field_index['attribute_value'][$defaultIsoCode] = $key;
                    // if ($multiLangOn) {
                    // 	$attrval_code_arr = explode("_", $value);
                    // 	$isoCode = $attrval_code_arr[1];

                    // 	$field_index['attribute_value'][$isoCode] = $key;
                    // } else {
                    // 	$field_index['attribute_value'][$defaultIsoCode] = $key;
                    // }
                } elseif (preg_match('/Supplier reference/i', $value)) {
                    $field_index['mp_supplier_reference'] = $key;
                } elseif (preg_match('/Reference/i', $value)) {
                    $field_index['mp_reference'] = $key;
                } elseif (preg_match('/EAN13/i', $value)) {
                    $field_index['mp_ean13'] = $key;
                } elseif (preg_match('/UPC/i', $value)) {
                    $field_index['mp_upc'] = $key;
                } elseif (preg_match('/Wholesale price/i', $value)) {
                    $field_index['mp_wholesale_price'] = $key;
                } elseif (preg_match('/Impact on price/i', $value)) {
                    $field_index['mp_price'] = $key;
                } elseif (preg_match('/Ecotax/i', $value)) {
                    $field_index['mp_ecotax'] = $key;
                } elseif (preg_match('/Quantity/', $value)) {
                    $field_index['mp_quantity'] = $key;
                } elseif (preg_match('/Minimal quantity/i', $value)) {
                    $field_index['attribute_minimal_quantity'] = $key;
                } elseif (preg_match('/Impact on weight/i', $value)) {
                    $field_index['attribute_weight'] = $key;
                } elseif (preg_match('/Default/', $value)) {
                    $field_index['mp_default_on'] = $key;
                } elseif (preg_match('/Combination available date/i', $value)) {
                    $field_index['available_date_attribute'] = $key;
                } elseif (preg_match('/Image ID/i', $value)) {
                    $field_index['ps_image_id_arr'] = $key;
                }
            }
        }

        return $field_index;
    }

    public function setCsvAction($csvAction)
    {
        $this->editCsv = 0;
        if ($csvAction == 2) {
            $this->editCsv = 1;
        }
    }

    public function testCsvFile($kwargs)
    {
        $id_customer = $kwargs['id_customer'];
        $id_seller = $kwargs['id_seller'];
        $request_id = $kwargs['request_id'];
        $massUploadCategory = $kwargs['mass_upload_category'];
        $csv_file = $kwargs['csv_file'];
        $img_file = $kwargs['img_file'];
        $approved = $kwargs['approved'];
        $errorString = $kwargs['error_string'];
        $csvAction = $kwargs['csvType'];

        $csvError = array();
        $languages = Language::getLanguages(true);
        $obj_seller_product = new WkMpSellerProduct();

        if ($massUploadCategory == 1) {
            if ($img_file["size"]) {
                $img_file_ext = pathinfo($img_file['name'], PATHINFO_EXTENSION);
                if ($img_file_ext != 'zip') {
                    $csvError[] = $errorString['zip_format'];
                }
            }
        }

        if (!$request_id) {
            $csvError[] = $errorString['request_no_req'];
        }

        if (!$massUploadCategory) {
            $csvError[] = $errorString['file_cate_req'];
        }

        if ($csv_file["size"] <= 0) {
            $csvError[] = $errorString['upload_csv_file'];
        } else {
            $csv_file_ext = pathinfo($csv_file['name'], PATHINFO_EXTENSION);
            if ($csv_file_ext != 'csv') {
                $csvError[] = $errorString['csv_format'];
            }
        }

        if (!count($csvError)) {
            if ($csv_file["size"] > 0) {
                $obj_seller = new WkMpSeller((int)$id_seller);
                $multi_lang_on = Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE');
                if ($multi_lang_on) {
                    $default_lang = $obj_seller->default_lang;
                } else {
                    if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin default lang
                        $default_lang = Configuration::get('PS_LANG_DEFAULT');
                    } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller default lang
                        $default_lang = $obj_seller->default_lang;
                    }
                }
                $default_iso_code = Language::getIsoById($default_lang);

                $file = fopen((string)$csv_file['tmp_name'], "r");
                $file_structure = fgetcsv($file);

                // check if CSV is Add|Edit
                $this->setCsvAction($csvAction);

                if ($massUploadCategory == 2) {    // Add Product combination CSV file
                    if (!Configuration::get('WK_MP_SELLER_PRODUCT_COMBINATION') || !Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE')) {
                        $csvError[] = $errorString['comb_install_enable'];
                        if (Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE')) {
                            Configuration::updateValue('MASS_UPLOAD_COMBINATION_APPROVE', 0);
                            Configuration::updateValue('MASS_UPLOAD_ALLOW_EDIT_COMBINATION', 0);
                        }
                    } elseif ($this->editCsv && !Configuration::get('MASS_UPLOAD_ALLOW_EDIT_COMBINATION')) {
                        $csvError[] = $errorString['edit_comb_not_allowed'];
                    }
                }

                if (!count($csvError)) {
                    // Validate CSV file columns
                    $this->validateCsvHeader($file_structure, $massUploadCategory, $default_lang, $csvError, $errorString);
                }

                if (!count($csvError)) {
                    $line_no = 2; // It starts from 2 because line number 1 contains header array.
                    if ($massUploadCategory == 1) {    // Add/Update Product csv file
                        $className = 'WkMpSellerProduct';
                        $rules = call_user_func(array($className, 'getValidationRules'), $className);
                        $limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
                        if ($img_file['size'] > 0) {
                            $product_image_zip = $img_file['tmp_name'];
                            $productzip = Tools::ZipTest($product_image_zip);
                            if ($productzip) {
                                $prod_img_tmp_path = _PS_MODULE_DIR_ . 'mpmassupload/views/temp_image_zip/' . $request_id;
                                Tools::ZipExtract($product_image_zip, $prod_img_tmp_path);
                            } else {
                                $csvError[] = $errorString['zip_file'];
                            }
                        }

                        // Get field index like `name` field is present in which csv column index
                        $field_index = $this->getCsvColumnDetail($file_structure, $default_iso_code, $massUploadCategory);
                        if (!count($csvError)) {
                            while (($result = fgetcsv($file)) !== false) {
                                $product_name = array();
                                $short_description = array();
                                $product_description = array();
                                foreach ($field_index as $column_name => $column_value) {
                                    if ($column_name == 'product_name') {
                                        foreach ($column_value as $lang_iso_code => $column_index) {
                                            $product_name[$lang_iso_code] = $result[$column_index];
                                        }
                                    } elseif ($column_name == 'product_categories') {
                                        $product_categories = $result[$column_value];
                                    } elseif ($column_name == 'default_category') {
                                        $default_category = $result[$column_value];
                                    } elseif ($column_name == 'short_description') {
                                        foreach ($column_value as $lang_iso_code => $column_index) {
                                            $short_description[$lang_iso_code] = $result[$column_index];
                                        }
                                    } elseif ($column_name == 'description') {
                                        foreach ($column_value as $lang_iso_code => $column_index) {
                                            $product_description[$lang_iso_code] = $result[$column_index];
                                        }
                                    } elseif ($column_name == 'product_price') {
                                        $product_price = $result[$column_value];
                                    } elseif ($column_name == 'product_quantity') {
                                        $product_quantity = $result[$column_value];
                                    } elseif ($column_name == 'image_ref') {
                                        $image_ref = $result[$column_value];
                                    } elseif ($column_name == 'mp_id_product') {
                                        $mp_id_product = $result[$column_value];
                                    }
                                }

                                if ($this->editCsv && isset($mp_id_product)) {
                                    if (!$mp_id_product) {
                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['mp_id_prod_2'];
                                    } else {
                                        // check product belongs to the seller
                                        $mpProdDetail = $obj_seller_product->getSellerProductByIdProduct($mp_id_product);
                                        if (($mpProdDetail['id_seller'] != $id_seller) || !$mpProdDetail) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['mp_id_prod_2'];
                                        }
                                    }
                                }

                                foreach ($languages as $language) {
                                    if (isset($product_name[$language['iso_code']])) {
                                        if ($product_name[$language['iso_code']] && Tools::strlen($product_name[$language['iso_code']]) > $rules['sizeLang']['link_rewrite']) {
                                            $csvError[] = sprintf(
                                                $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_name_011'] . '(%2$d ' . $errorString['prod_name_012'] . ')',
                                                call_user_func(array($className, 'displayFieldName'), $className),
                                                $rules['sizeLang']['link_rewrite']
                                            );
                                        }

                                        if (!Validate::isCatalogName($product_name[$language['iso_code']])) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_name_2'];
                                        }
                                    }

                                    if (isset($short_description[$language['iso_code']])) {
                                        $short_description_val = $short_description[$language['iso_code']];
                                        if ($limit <= 0) {
                                            $limit = 400;
                                        }
                                        if (!Validate::isCleanHtml($short_description_val)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['short_desc_1'];
                                        } elseif (Tools::strlen(strip_tags($short_description_val)) > $limit) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['short_desc_2_1'] . $limit . $errorString['short_desc_2_2'];
                                        }
                                    }

                                    if (isset($product_description[$language['iso_code']])) {
                                        $product_desc_val = $product_description[$language['iso_code']];
                                        if (!Validate::isCleanHtml($product_desc_val, (int)Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['description'];
                                        }
                                    }
                                }

                                // check product name must present in default language
                                if ($this->editCsv) {
                                    if (isset($product_name[$default_iso_code]) && ($this->editCsv && self::EMPTY_IDENTIFY == $product_name[$default_iso_code])) {
                                        $seller_lang = Language::getLanguage((int)$default_lang);
                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_name_def_lang'] . $seller_lang['name'];
                                    }
                                } else {
                                    if (!isset($product_name[$default_iso_code]) || !$product_name[$default_iso_code]) {
                                        $seller_lang = Language::getLanguage((int)$default_lang);
                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_name_def_lang'] . $seller_lang['name'];
                                    }
                                }

                                if (isset($product_price)) {
                                    if ($product_price != '') {
                                        if (!Validate::isPrice($product_price)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_price_1'];
                                        }
                                    } else {
                                        if (!$this->editCsv) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_price_2'];
                                        }
                                    }
                                }

                                if (isset($product_quantity)) {
                                    if ($product_quantity != '') {
                                        if (!Validate::isUnsignedInt($product_quantity)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_qty_1'];
                                        }
                                    } else {
                                        if (!$this->editCsv) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_qty_2'];
                                        }
                                    }
                                }

                                if ($img_file['size'] && isset($image_ref) && $image_ref) {
                                    $img_folder_path = _PS_MODULE_DIR_ . 'mpmassupload/views/temp_image_zip/' . $request_id . '/product_image/' . $image_ref;
                                    foreach (glob($img_folder_path . '/*.*', GLOB_BRACE) as $image_name) {
                                        if (!ImageManager::isCorrectImageFileExt($image_name)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_img'];
                                        }
                                    }
                                }

                                $line_no += 1;
                                if (count($csvError)) {
                                    break;
                                }
                            }
                        }
                    } elseif ($massUploadCategory == 2) {    // Add Product combination CSV file
                        $field_index = $this->getCsvColumnDetail($file_structure, $default_iso_code, $massUploadCategory);
                        if (!isset($field_index['attribute_group'][$default_iso_code])) {
                            $csvError[] = $errorString['attr_grp_011'] . 'Attribute_' . $default_iso_code . $errorString['attr_grp_012'];
                        }
                        if (!isset($field_index['attribute_value'][$default_iso_code])) {
                            $csvError[] = $errorString['attr_val_011'] . 'Attribute_' . $default_iso_code . $errorString['attr_val_012'];
                        }

                        if (!count($csvError)) {
                            $obj_seller_product = new WkMpSellerProduct();
                            while (($result = fgetcsv($file)) !== false) {
                                if (!$result[$field_index['attribute_group'][$default_iso_code]]) {
                                    $seller_lang = Language::getLanguage((int)$default_lang);
                                    $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_grp_02'] . ' (' . $seller_lang['name'] . ')';
                                }
                                if (!$result[$field_index['attribute_value'][$default_iso_code]]) {
                                    $seller_lang = Language::getLanguage((int)$default_lang);
                                    $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_val_02'] . ' (' . $seller_lang['name'] . ')';
                                }

                                $attribute_arr = array();
                                $attribute_value_arr = array();
                                foreach ($field_index as $column_name => $column_value) {
                                    if ($column_name == 'mp_id_product') {
                                        $mp_id_product = $result[$column_value];
                                    } elseif ($column_name == 'attribute_group') {
                                        foreach ($column_value as $lang_iso_code => $column_index) {
                                            $attribute_arr[$lang_iso_code] = $result[$column_index];
                                        }
                                    } elseif ($column_name == 'attribute_value') {
                                        foreach ($column_value as $lang_iso_code => $column_index) {
                                            $attribute_value_arr[$lang_iso_code] = $result[$column_index];
                                        }
                                    } elseif ($column_name == 'mp_supplier_reference') {
                                        $mp_supplier_reference = $result[$column_value];
                                    } elseif ($column_name == 'mp_reference') {
                                        $mp_reference = $result[$column_value];
                                    } elseif ($column_name == 'mp_ean13') {
                                        $mp_ean13 = $result[$column_value];
                                    } elseif ($column_name == 'mp_upc') {
                                        $mp_upc = $result[$column_value];
                                    } elseif ($column_name == 'mp_wholesale_price') {
                                        $mp_wholesale_price = $result[$column_value];
                                    } elseif ($column_name == 'mp_price') {
                                        $mp_price = $result[$column_value];
                                    } elseif ($column_name == 'mp_ecotax') {
                                        $mp_ecotax = $result[$column_value];
                                    } elseif ($column_name == 'mp_quantity') {
                                        $mp_quantity = $result[$column_value];
                                    } elseif ($column_name == 'attribute_minimal_quantity') {
                                        $attribute_minimal_quantity = $result[$column_value];
                                    } elseif ($column_name == 'attribute_weight') {
                                        $attribute_weight = $result[$column_value];
                                    } elseif ($column_name == 'mp_default_on') {
                                        $mp_default_on = $result[$column_value];
                                    } elseif ($column_name == 'available_date_attribute') {
                                        $available_date_attribute = $result[$column_value];
                                    } elseif ($column_name == 'ps_image_id_arr') {
                                        $ps_image_id_arr = $result[$column_value];
                                    }
                                }

                                if (!$attribute_arr) {
                                    $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_grp_1'];
                                } else {
                                    foreach ($languages as $language) {
                                        if (isset($attribute_arr[$language['iso_code']])) {
                                            $attr_grp_arr = array();
                                            foreach (explode(',', $attribute_arr[$language['iso_code']]) as $al_k => $attribute) {
                                                $attribute = trim($attribute);
                                                if (!$attribute) {
                                                    $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_grp_2'] . $errorString['for_language'] . $language['name'];
                                                } else {
                                                    $tab_group = explode(':', $attribute);
                                                    $attrib_name = trim($tab_group[0]);
                                                    $attrib_type = trim($tab_group[1]);
                                                    if (isset($attr_grp_arr[$attrib_name])) {
                                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_grp_3'] . $errorString['for_language'] . $language['name'];
                                                    } elseif (!$attrib_name) {
                                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_grp_4'] . $errorString['for_language'] . $language['name'];
                                                    } elseif (!Validate::isGenericName($attrib_name)) {
                                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_grp_5'] . $errorString['for_language'] . $language['name'];
                                                    } elseif (!$attrib_type) {
                                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_grp_6'] . $errorString['for_language'] . $language['name'];
                                                    } elseif (!Validate::isGenericName($attrib_type)) {
                                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_grp_7'] . $errorString['for_language'] . $language['name'];
                                                    } else {
                                                        $attr_grp_arr[$attrib_name] = $attrib_type;
                                                    }
                                                }
                                            }
                                        }

                                        if (!$attribute_value_arr) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_val_1'];
                                        } else {
                                            if (isset($attribute_value_arr[$language['iso_code']])) {
                                                foreach (explode(',', $attribute_value_arr[$language['iso_code']]) as $avl_k => $attr_group_value) {
                                                    $attr_group_value = trim($attr_group_value);
                                                    if (!$attr_group_value) {
                                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_val_2'] . $errorString['for_language'] . $language['name'];
                                                    } else {
                                                        $tab_groupother = explode(':', $attr_group_value);
                                                        $attr_group_value = trim($tab_groupother[0]);
                                                        if (!$attr_group_value) {
                                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_val_3'] . $errorString['for_language'] . $language['name'];
                                                        } elseif (!Validate::isGenericName($attr_group_value)) {
                                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['attr_val_4'] . $errorString['for_language'] . $language['name'];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                if (!$mp_id_product) {
                                    $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['mp_id_prod_1'];
                                } else {
                                    $mpProdDetail = $obj_seller_product->getSellerProductByIdProduct($mp_id_product);

                                    if (($mpProdDetail['id_seller'] != $id_seller) || !$mpProdDetail) {
                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['mp_id_prod_2'];
                                    }

                                    if (isset($mp_ean13) && $mp_ean13 && (self::EMPTY_IDENTIFY != $mp_ean13)) {
                                        if (!Validate::isEan13($mp_ean13)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['comb_ean13'];
                                        }
                                    }

                                    if (isset($mp_upc) && $mp_upc && (self::EMPTY_IDENTIFY != $mp_upc)) {
                                        if (!Validate::isUpc($mp_upc)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['comb_upc'];
                                        }
                                    }

                                    if (isset($mp_wholesale_price) && $mp_wholesale_price) {
                                        if (!Validate::isPrice($mp_wholesale_price)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['wholesale_price'];
                                        }
                                    }

                                    if (isset($mp_price) && !($this->editCsv && $mp_price == '')) {
                                        if (!$mp_price) {
                                            $mp_price = 0;
                                        }
                                        if (!Validate::isNegativePrice($mp_price)) {  // isNegativePrice check both positive price and negative price and will return true in both the case.
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['impact_price'];
                                        }
                                    }

                                    if (isset($mp_quantity) && !Validate::isUnsignedId($mp_quantity)) {
                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['comb_qty'];
                                    }

                                    if (isset($attribute_minimal_quantity) && !Validate::isUnsignedId($attribute_minimal_quantity)) {
                                        $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['comb_min_qty'];
                                    }

                                    if (isset($attribute_weight) && !($this->editCsv && $attribute_weight == '')) {
                                        if ($attribute_weight == '') {
                                            $attribute_weight = 0;
                                        }
                                        if (!Validate::isFloat($attribute_weight)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['comb_weight'];
                                        }
                                    }

                                    if (isset($mp_default_on) && !($this->editCsv && $mp_default_on == '')) {
                                        if ($mp_default_on == '') {
                                            $mp_default_on = 0;
                                        }
                                        if (!Validate::isBool($mp_default_on)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['comb_default'];
                                        }
                                    }

                                    if (isset($available_date_attribute) && !($this->editCsv && $available_date_attribute == '')) {
                                        if (!Validate::isDateFormat($available_date_attribute)) {
                                            $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['comb_date_formate'];
                                        }
                                    }

                                    if (isset($ps_image_id_arr) && $ps_image_id_arr && (self::EMPTY_IDENTIFY != $ps_image_id_arr)) {
                                        $ps_image_id_arr = array_unique(explode(',', $ps_image_id_arr));
                                        foreach ($ps_image_id_arr as $img_key => $img_val) {
                                            if ($img_val) {
                                                $img_prod_dtl = $this->getProdIdByImgPsId($img_val);
                                                if ($img_prod_dtl['mp_id_prod'] != $mp_id_product) {
                                                    $csvError[] = $errorString['error_prefix'] . $line_no . $errorString['error_suffix'] . $errorString['prod_img_check'];
                                                    break;
                                                }
                                                $ps_image_id_arr[$img_key] = trim($img_val);
                                            }
                                        }
                                    }
                                }

                                if (count($csvError)) {
                                    break;
                                }
                                $line_no += 1;
                            }
                        }
                    }
                }
            }
        }

        if ($massUploadCategory == 1) {
            if ($img_file['size'] > 0) {
                $this->rrmdir(_PS_MODULE_DIR_ . 'mpmassupload/views/temp_image_zip/' . $request_id);
            }
        }

        $return_msg = array();
        if (count($csvError)) {
            $return_msg['is_error'] = 1;
            $return_msg['errors'] = $csvError;
            return $return_msg;
        } else {
            $return_msg['is_error'] = 0;
            $return_msg['csv_row'] = $line_no - 2;
            return $return_msg;
        }
    }

    public function uploadCsvFile($kwargs)
    {
        $id_customer = $kwargs['id_customer'];
        $id_seller = $kwargs['id_seller'];
        $request_id = $kwargs['request_id'];
        $mass_upload_category = $kwargs['mass_upload_category'];
        $total_records = $kwargs['total_records'];
        $approved = $kwargs['approved'];
        $ps_id_shop = $kwargs['ps_id_shop'];
        $csvAction = $kwargs['csvType'];

        // check if CSV is Add|Edit
        $this->setCsvAction($csvAction);

        if (isset($kwargs['toggle_status'])) {
            $toggle_status = $kwargs['toggle_status'];
        }

        if (isset($kwargs['id_mass_upload'])) {
            $id_mass_upload = $kwargs['id_mass_upload'];
        }

        if (isset($kwargs['csv_file'])) {
            $csv_file = $kwargs['csv_file'];
            $csv_tmp_file = $csv_file['tmp_name'];
        } else {
            $csv_tmp_file = _PS_MODULE_DIR_ . 'mpmassupload/views/uploaded_csv/' . $request_id . '.csv';
        }

        $csv_warning = '';
        if ($mass_upload_category == 2) {
            $warning_string = $kwargs['warning_string'];
        }

        $obj_mp_seller = new WkMpSeller((int)$id_seller);

        $multi_lang_on = Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE');
        if ($multi_lang_on) {
            $default_lang = $obj_mp_seller->default_lang;
        } else {
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin default lang
                $default_lang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller default lang
                $default_lang = $obj_mp_seller->default_lang;
            }
        }
        $default_iso_code = Language::getIsoById($default_lang);
        $ps_default_lang = Configuration::get('PS_LANG_DEFAULT');
        $ps_default_iso_code = Language::getIsoById($ps_default_lang);

        $ps_default_lang_pro_name = '';
        $languages = Language::getLanguages(true);

        $obj_massupload = new MarketplaceMassUpload();
        $obj_seller_product_category = new WkMpSellerProductCategory();
        $obj_seller_product = new WkMpSellerProduct();
        $objMpProductImg = new WkMpSellerProductImage();

        $objProduct = new Product();
        $idTaxRulesGroup = $objProduct->getIdTaxRulesGroup();

        if ($mass_upload_category == 1) {    // Add Product csv file
            // In case of admin approve, if image file exist then we have it in our image_uploaded folder not in tmp location.
            if (isset($kwargs['img_file'])) {
                $img_file = $kwargs['img_file'];
                $img_zip_filesize = $img_file['size'];
                $img_tmp_zip = $img_file['tmp_name'];
            } else {
                $image_zip_path = _PS_MODULE_DIR_ . 'mpmassupload/views/image_uploaded/' . $request_id . '.zip';
                if (file_exists($image_zip_path)) {
                    $img_zip_filesize = 1;
                    $img_tmp_zip = $image_zip_path;
                } else {
                    $img_zip_filesize = 0;
                }
            }

            if (!$approved) {
                $obj_massupload->mass_upload_category = $mass_upload_category;
                $obj_massupload->request_id = $request_id;
                $obj_massupload->id_seller = $id_seller;
                $obj_massupload->total_records = $total_records;
                $obj_massupload->is_approve = 0;
                $obj_massupload->status = 'Draft';
                $obj_massupload->csv_type = $this->editCsv ? 2 : 1;
                if ($obj_massupload->add()) {
                    if ($mass_upload_category == 1) {    // Add Product Image File
                        if ($img_zip_filesize > 0) {
                            $image_path = _PS_MODULE_DIR_ . 'mpmassupload/views/image_uploaded/';
                            move_uploaded_file($img_tmp_zip, $image_path . $request_id . '.zip');
                        }
                    }
                }
            } else {
                if ($img_zip_filesize > 0) {
                    $prod_img_tmp_path = _PS_MODULE_DIR_ . 'mpmassupload/views/temp_image_zip/' . $request_id;
                    Tools::ZipExtract($img_tmp_zip, $prod_img_tmp_path);
                }
                $file = fopen((string)$csv_tmp_file, "r");
                $file_structure = fgetcsv($file);

                $field_index = $this->getCsvColumnDetail($file_structure, $default_iso_code, $mass_upload_category);
                while (($result = fgetcsv($file)) !== false) {
                    $product_name = array();
                    $short_description = array();
                    $product_description = array();
                    foreach ($field_index as $column_name => $column_value) {
                        if ($column_name == 'product_name') {
                            foreach ($column_value as $lang_iso_code => $column_index) {
                                $product_name[$lang_iso_code] = $result[$column_index];
                            }
                        } elseif ($column_name == 'product_categories') {
                            $product_categories = $result[$column_value];
                        } elseif ($column_name == 'default_category') {
                            $default_category = $result[$column_value];
                        } elseif ($column_name == 'short_description') {
                            foreach ($column_value as $lang_iso_code => $column_index) {
                                $short_description[$lang_iso_code] = $result[$column_index];
                            }
                        } elseif ($column_name == 'description') {
                            foreach ($column_value as $lang_iso_code => $column_index) {
                                $product_description[$lang_iso_code] = $result[$column_index];
                            }
                        } elseif ($column_name == 'product_price') {
                            $product_price = $result[$column_value];
                        } elseif ($column_name == 'product_quantity') {
                            $product_quantity = $result[$column_value];
                        } elseif ($column_name == 'image_ref') {
                            $image_ref = $result[$column_value];
                        } elseif ($column_name == 'mp_id_product') {
                            $mp_id_product = $result[$column_value];
                        }
                    }

                    // if category is not set by seller into csv file then set home category id
                    $id_category = false;
                    if (isset($product_categories) && $product_categories) {
                        $product_categories = array_unique(explode(',', $product_categories));
                    }
                    if (!$this->editCsv) {
                        // This check is applied because if default category is "5, 6" or "5-6" then also we get array according to id_category = 5
                        if (is_numeric($default_category)) {
                            $getcategory = $obj_massupload->getCategoryInfoByCategoryId($default_category);
                        } else {
                            $getcategory = false;
                        }

                        if ($getcategory) {
                            $id_category = $default_category;
                        } else {
                            foreach ($product_categories as $p_category) {
                                $getcategory = $obj_massupload->getCategoryInfoByCategoryId($p_category);
                                if ($getcategory) {
                                    $id_category = $p_category;
                                    break;
                                }
                            }
                        }

                        if (!$id_category || (int)$id_category < 2) {
                            $id_category = 2;
                        }
                    } else {
                        if (isset($default_category) && $default_category) {
                            if (is_numeric($default_category)) {
                                $getcategory = $obj_massupload->getCategoryInfoByCategoryId($default_category);
                            } else {
                                $getcategory = false;
                            }

                            if ($getcategory) {
                                $id_category = $default_category;
                            } else {
                                if (isset($product_categories) && $product_categories) {
                                    foreach ($product_categories as $p_category) {
                                        $getcategory = $obj_massupload->getCategoryInfoByCategoryId($p_category);
                                        if ($getcategory) {
                                            $id_category = $p_category;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (isset($mp_id_product)) {
                        $obj_seller_product = new WkMpSellerProduct($mp_id_product);
                    } else {
                        $obj_seller_product = new WkMpSellerProduct();
                    }

                    if (!$this->editCsv) {
                        $obj_seller_product->id_seller = $id_seller;
                        $obj_seller_product->id_ps_product = 0; // prestashop product id
                        $obj_seller_product->id_ps_shop = $ps_id_shop;
                        $obj_seller_product->condition = 'new';
                        $obj_seller_product->id_tax_rules_group = $idTaxRulesGroup;
                    }

                    if (isset($product_price) && trim($product_price) != '') {
                        $obj_seller_product->price = $product_price;
                    }

                    if (isset($product_quantity) && trim($product_quantity) != '') {
                        $obj_seller_product->quantity = $product_quantity;
                    }

                    if ($id_category) {
                        $obj_seller_product->id_category = $id_category;
                    }

                    //control product approval setting
                    $active = Configuration::get('WK_MP_PRODUCT_ADMIN_APPROVE') ? 0 : 1;
                    if (!$this->editCsv) {
                        $obj_seller_product->active = $active;
                        $obj_seller_product->status_before_deactivate = $active;
                        $obj_seller_product->admin_approved = $active;
                    }

                    foreach ($languages as $language) {
                        $prodIsoCode = $language['iso_code'];
                        $shortDescIsoCode = $language['iso_code'];
                        $descIsoCode = $language['iso_code'];

                        //if product name in other language is not available then fill with seller language same for others
                        if (!isset($product_name[$language['iso_code']]) && !$this->editCsv) {
                            $prodIsoCode = $default_iso_code;
                        }
                        if (!isset($short_description[$language['iso_code']]) && !$this->editCsv) {
                            $shortDescIsoCode = $default_iso_code;
                        }
                        if (!isset($product_description[$language['iso_code']]) && !$this->editCsv) {
                            $descIsoCode = $default_iso_code;
                        }

                        if (isset($product_name[$prodIsoCode]) && trim($product_name[$prodIsoCode]) != '') {
                            if (($product_name[$prodIsoCode] == self::EMPTY_IDENTIFY) && $this->editCsv) {
                                $prodIsoCode = $default_iso_code;
                            }
                            $obj_seller_product->product_name[$language['id_lang']] = $product_name[$prodIsoCode];
                            $obj_seller_product->link_rewrite[$language['id_lang']] = Tools::link_rewrite($product_name[$prodIsoCode]);
                        }
                        if (isset($short_description[$shortDescIsoCode]) && trim($short_description[$shortDescIsoCode]) != '') {
                            $obj_seller_product->short_description[$language['id_lang']] = ($short_description[$shortDescIsoCode] != self::EMPTY_IDENTIFY) ? $short_description[$shortDescIsoCode] : null;
                        }
                        if (isset($product_description[$shortDescIsoCode]) && trim($product_description[$shortDescIsoCode]) != '') {
                            $obj_seller_product->description[$language['id_lang']] = ($product_description[$shortDescIsoCode] != self::EMPTY_IDENTIFY) ? $product_description[$shortDescIsoCode] : null;
                        }

                        if ($ps_default_lang == $language['id_lang']) {
                            $ps_default_lang_pro_name = $obj_seller_product->product_name[$language['id_lang']];
                        }
                    }

                    $obj_seller_product->save();
                    $mp_product_id = $obj_seller_product->id;

                    if ($mp_product_id) {
                        Db::getInstance()->update('wk_mp_seller_product', array('csv_request_no' => $request_id), 'id_mp_product =' . $mp_product_id);

                        if (!$this->editCsv) {
                            //Add into category table
                            $obj_seller_product_category->id_seller_product = $mp_product_id;
                            $default_added_cat = 0;
                            //set if more than one category selected
                            foreach ($product_categories as $p_category) {
                                $getcategory = $obj_massupload->getCategoryInfoByCategoryId($p_category);
                                if (!(empty($getcategory) || (int)$p_category < 2)) {
                                    $obj_seller_product_category->id_category = trim($p_category);
                                    if ($p_category == $id_category) {
                                        $default_added_cat = 1;
                                        $obj_seller_product_category->is_default = 1;
                                    } else {
                                        $obj_seller_product_category->is_default = 0;
                                    }
                                    $obj_seller_product_category->add();
                                }
                            }
                            if (!$default_added_cat) {
                                $obj_seller_product_category->id_category = trim($id_category);
                                $obj_seller_product_category->is_default = 1;
                                $obj_seller_product_category->add();
                            }
                        } else {
                            if ((isset($product_categories) && $product_categories) || $id_category) {
                                if (isset($product_categories) && $product_categories) {
                                    $obj_seller_product_category->deleteProductCategory($mp_product_id);
                                    $id_category = $obj_seller_product->id_category;
                                } else {
                                    $existingCategory = $obj_seller_product_category->getMultipleCategories($mp_product_id);
                                    $existingCategory = array_column($existingCategory, 'id_category');
                                    if ($categoryKey = array_search($id_category, $existingCategory)) {
                                        if (!$existingCategory[$categoryKey]['is_default']) {
                                            Db::getInstance()->update('wk_mp_seller_product_category', array('is_default' => 0), 'is_default = 1 AND id_mp_product =' . $mp_product_id);
                                            $categoryAction = -1;
                                        }
                                        Db::getInstance()->delete('wk_mp_seller_product_category', 'id_mp_category_product = ' . $existingCategory[$categoryKey]['id_mp_category_product'] . ' AND id_mp_product =' . $mp_product_id);
                                    }
                                }

                                //Add into category table
                                $obj_seller_product_category->id_seller_product = $mp_product_id;
                                $default_added_cat = 0;
                                //set if more than one category selected
                                if (isset($product_categories) && $product_categories) {
                                    foreach ($product_categories as $p_category) {
                                        $getcategory = $obj_massupload->getCategoryInfoByCategoryId($p_category);
                                        if (!(empty($getcategory) || (int)$p_category < 2)) {
                                            $obj_seller_product_category->id_category = trim($p_category);

                                            // if default id_category is not mentioned then that case is also managed by below code
                                            if ($p_category == $id_category) {
                                                $default_added_cat = 1;
                                                $obj_seller_product_category->is_default = 1;
                                            } else {
                                                $obj_seller_product_category->is_default = 0;
                                            }
                                            $obj_seller_product_category->add();
                                        }
                                    }
                                }
                                if (!$default_added_cat && $id_category) {
                                    $obj_seller_product_category->id_category = trim($id_category);
                                    $obj_seller_product_category->is_default = 1;
                                    $obj_seller_product_category->add();
                                }
                            }
                        }


                        if ($img_zip_filesize && isset($image_ref) && $image_ref) {
                            // Delete Product existing image
                            if ($this->editCsv) {
                                $mpProdImg = $objMpProductImg->getProductImageBySellerIdProduct($mp_product_id);
                                if ($mpProdImg) {
                                    foreach ($mpProdImg as $mpImgDetail) {
                                        $objMpProductImg->deleteProductImage($mpImgDetail['seller_product_image_name']);
                                    }
                                }
                            }

                            $img_folder_path = _PS_MODULE_DIR_ . 'mpmassupload/views/temp_image_zip/' . $request_id . '/product_image/' . $image_ref;
                            foreach (glob($img_folder_path . '/*.*', GLOB_BRACE) as $img_tmpname) {
                                if (!empty($img_tmpname)) {
                                    $rand = WkMpHelper::randomImageName();
                                    $image_name = $rand . '.jpg';

                                    Db::getInstance()->insert(
                                        'wk_mp_seller_product_image',
                                        array(
                                            'seller_product_id' => (int)$mp_product_id,
                                            'seller_product_image_name' => $image_name,
                                            'id_ps_image' => 0,
                                            'active' => 0,
                                        )
                                    );

                                    $upload_path = _PS_MODULE_DIR_ . 'marketplace/views/img/product_img/';
                                    ImageManager::resize($img_tmpname, $upload_path . $image_name);
                                }
                            }
                        }

                        if ($this->editCsv) {
                            if ($obj_seller_product->active) {
                                //if product is active then check admin configure value that product after update need to approved by admin or not
                                $deactivateAfterUpdate = WkMpSellerProduct::deactivateProductAfterUpdate($mp_product_id, 1);
                                if (!Configuration::get('WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE')) {
                                    // Update also in prestashop if product is active
                                    $obj_seller_product->updateSellerProductToPs($mp_product_id, 1);
                                }
                            }

                            Hook::exec('actionAfterUpdateMPProduct', array('id_mp_product' => $mp_product_id));
                        } else {
                            // if default approve on, then entry product details in ps_product table
                            if (!Configuration::get('WK_MP_PRODUCT_ADMIN_APPROVE')) {
                                $image_dir = _PS_MODULE_DIR_ . 'marketplace/views/img/product_img';
                                // creating ps_product when admin setting is default
                                $ps_product_id = $obj_seller_product->addSellerProductToPs($mp_product_id, 1);
                                if ($ps_product_id) {
                                    //save ps_product
                                    $obj_seller_product->id_ps_product = $ps_product_id;
                                    $obj_seller_product->save();

                                    // Send images to PS
                                    $obj_seller_product->updatePsProductImage($mp_product_id, $ps_product_id);
                                }

                                $obj_seller_product->sendMail($mp_product_id, 1, 1);
                            }

                            if (Configuration::get('WK_MP_MAIL_ADMIN_PRODUCT_ADD')) {
                                $sellerDetail = WkMpSeller::getSeller($id_seller, Configuration::get('PS_LANG_DEFAULT'));
                                if ($sellerDetail) {
                                    $sellerName = $sellerDetail['seller_firstname'] . ' ' . $sellerDetail['seller_lastname'];
                                    $shopName = $sellerDetail['shop_name'];
                                    $obj_seller_product->mailToAdminOnProductAdd($ps_default_lang_pro_name, $sellerName, $sellerDetail['phone'], $shopName, $sellerDetail['business_email']);
                                }
                            }

                            Hook::exec('actionAfterAddMPProduct', array('id_mp_product' => $mp_product_id));
                        }
                    }
                }

                if ($img_zip_filesize > 0) {
                    $this->rrmdir(_PS_MODULE_DIR_ . 'mpmassupload/views/temp_image_zip/' . $request_id);
                }

                if (!isset($toggle_status)) {
                    $obj_massupload->is_approve = 1;
                    $obj_massupload->status = 'Approved';
                    $obj_massupload->is_csv_product_added = '1';
                    $obj_massupload->mass_upload_category = $mass_upload_category;
                    $obj_massupload->request_id = $request_id;
                    $obj_massupload->id_seller = $id_seller;
                    $obj_massupload->total_records = $total_records;
                    $obj_massupload->csv_type = $this->editCsv ? 2 : 1;
                    if ($obj_massupload->add()) {
                        $obj_massupload->sendMailToSeller($id_customer, $request_id);
                    }
                }
            }
        } elseif ($mass_upload_category == 2) {    // Add Product combination CSV file
            if (!$approved) {
                $obj_massupload->mass_upload_category = $mass_upload_category;
                $obj_massupload->request_id = $request_id;
                $obj_massupload->id_seller = $id_seller;
                $obj_massupload->total_records = $total_records;
                $obj_massupload->is_approve = 0;
                $obj_massupload->status = 'Draft';
                $obj_massupload->csv_type = $this->editCsv ? 2 : 1;
                $obj_massupload->add();
            } else {
                $line_no = 2;

                // In Marketplace V3.0.0 "MP_PRODUCT_ATTRIBUTE_ACTIVATION" doesn't exist and will be added in future
                // $create_attr_permission = Configuration::get('MP_PRODUCT_ATTRIBUTE_ACTIVATION');
                $create_attr_permission = 0;

                $attr_dtl = array();
                $default_comb = array();                    // Used for Add Combination
                $defCombChanged = array();                    // Used for Edit Combination

                $ps_attr_group_arr = AttributeGroup::getAttributesGroups($ps_default_lang);
                $ps_attr_value_arr = Attribute::getAttributes($ps_default_lang);

                $file = fopen((string)$csv_tmp_file, "r");
                $file_structure = fgetcsv($file);
                $field_index = $this->getCsvColumnDetail($file_structure, $default_iso_code, $mass_upload_category);

                while (($result = fgetcsv($file)) !== false) {
                    $attribute_arr = array();
                    $attribute_value_arr = array();
                    foreach ($field_index as $column_name => $column_value) {
                        if ($column_name == 'mp_id_product') {
                            $mp_id_product = $result[$column_value];
                        } elseif ($column_name == 'attribute_group') {
                            foreach ($column_value as $lang_iso_code => $column_index) {
                                $attribute_arr[$lang_iso_code] = $result[$column_index];
                            }
                        } elseif ($column_name == 'attribute_value') {
                            foreach ($column_value as $lang_iso_code => $column_index) {
                                $attribute_value_arr[$lang_iso_code] = $result[$column_index];
                            }
                        } elseif ($column_name == 'mp_supplier_reference') {
                            $mp_supplier_reference = $result[$column_value];
                        } elseif ($column_name == 'mp_reference') {
                            $mp_reference = $result[$column_value];
                        } elseif ($column_name == 'mp_ean13') {
                            $mp_ean13 = $result[$column_value];
                        } elseif ($column_name == 'mp_upc') {
                            $mp_upc = $result[$column_value];
                        } elseif ($column_name == 'mp_wholesale_price') {
                            $mp_wholesale_price = $result[$column_value];
                        } elseif ($column_name == 'mp_price') {
                            $mp_price = $result[$column_value];
                        } elseif ($column_name == 'mp_ecotax') {
                            $mp_ecotax = $result[$column_value];
                        } elseif ($column_name == 'mp_quantity') {
                            $mp_quantity = $result[$column_value];
                        } elseif ($column_name == 'attribute_minimal_quantity') {
                            $attribute_minimal_quantity = $result[$column_value];
                        } elseif ($column_name == 'attribute_weight') {
                            $attribute_weight = $result[$column_value];
                        } elseif ($column_name == 'mp_default_on') {
                            $mp_default_on = $result[$column_value];
                        } elseif ($column_name == 'available_date_attribute') {
                            $available_date_attribute = $result[$column_value];
                        } elseif ($column_name == 'ps_image_id_arr') {
                            $ps_image_id_arr = $result[$column_value];
                            if ($ps_image_id_arr) {
                                $ps_image_id_arr = array_unique(explode(',', $ps_image_id_arr));
                                foreach ($ps_image_id_arr as $img_key => $img_val) {
                                    $ps_image_id_arr[$img_key] = trim($img_val);
                                }
                            }
                        }
                    }

                    $attribute_unity = 0;

                    // If Attribute Or Value is not exist in Prestashop.
                    if ($create_attr_permission) {
                        if (isset($attribute_arr[$ps_default_iso_code])) {
                            $attribute_group_arr = $attribute_arr[$ps_default_iso_code];
                        } else {
                            $attribute_group_arr = $attribute_arr[$default_iso_code];
                        }

                        foreach (explode(',', $attribute_group_arr) as $ag_k => $attr_grp) {
                            $attr_grp = trim($attr_grp);
                            if ($attr_grp) {
                                $tab_group = explode(':', $attr_grp);
                                $attrib_name = trim($tab_group[0]);
                                $attrib_type = trim($tab_group[1]);

                                if (!isset($attr_dtl[$attrib_name])) {
                                    if ($ps_attr_group_arr) {
                                        foreach ($ps_attr_group_arr as $attr_group) {
                                            if ($attrib_name == trim($attr_group['name'])) {
                                                $attr_dtl[$attrib_name]['grp_dtl'] = array(
                                                    'id_attribute_group' => $attr_group['id_attribute_group'],
                                                    'is_color_group' => $attr_group['is_color_group'],
                                                    'already_exist' => 1,
                                                );
                                                break;
                                            }
                                        }
                                    }

                                    // Still attribute group name is not present in array $attr_dtl then it means this attribute group is not present prestashop and we have to create it.
                                    if (!isset($attr_dtl[$attrib_name])) {
                                        $is_color = 0;
                                        $obj_attr_group = new AttributeGroup();
                                        $attr_grp_name_seller_default_lang = '';
                                        $ps_attr_grp_name = '';
                                        foreach ($languages as $lang) {
                                            $attr_iso_code = $lang['iso_code'];
                                            if (!isset($attribute_arr[$attr_iso_code])) {
                                                $attr_iso_code = $default_iso_code;

                                                if (!$attr_grp_name_seller_default_lang) {
                                                    $comb_attr_set_arr = explode(',', $attribute_arr[$attr_iso_code]);
                                                    $attr_grp_dtl = $comb_attr_set_arr[$ag_k];
                                                    $attr_grp_dtl_arr = explode(':', $attr_grp_dtl);
                                                    $attr_grp_name = trim($attr_grp_dtl_arr[0]);
                                                    $attr_grp_name_seller_default_lang = $attr_grp_name;
                                                } else {
                                                    $attr_grp_name = $attr_grp_name_seller_default_lang;
                                                }
                                            } else {
                                                $comb_attr_set_arr = explode(',', $attribute_arr[$attr_iso_code]);
                                                $attr_grp_dtl = $comb_attr_set_arr[$ag_k];
                                                $attr_grp_dtl_arr = explode(':', $attr_grp_dtl);
                                                $attr_grp_name = trim($attr_grp_dtl_arr[0]);
                                            }

                                            if ($lang['id_lang'] == $ps_default_lang) {
                                                $ps_attr_grp_name = $attr_grp_name;
                                            }

                                            $obj_attr_group->name[$lang['id_lang']] = $attr_grp_name;
                                            $obj_attr_group->public_name[$lang['id_lang']] = $attr_grp_name;
                                        }

                                        $obj_attr_group->group_type = $attrib_type;
                                        if ($attrib_type == 'color') {
                                            $is_color = 1;
                                        }

                                        $obj_attr_group->is_color_group = $is_color;
                                        $obj_attr_group->add();
                                        $attr_group_id = $obj_attr_group->id;

                                        Db::getInstance()->insert('layered_indexable_attribute_group', array('id_attribute_group' => $attr_group_id, 'indexable' => 1));

                                        $attr_dtl[$ps_attr_grp_name]['grp_dtl'] = array(
                                            'id_attribute_group' => $attr_group_id,
                                            'is_color_group' => $is_color,
                                            'already_exist' => 0,
                                        );
                                    }
                                }

                                // Till here attribute group work is done now we have to work on attribute value
                                if (isset($attr_dtl[$attrib_name])) {
                                    if (!isset($attr_dtl[$attrib_name]['value'])) {
                                        $attr_dtl[$attrib_name]['value'] = array();
                                    }

                                    if (isset($attribute_value_arr[$ps_default_iso_code])) {
                                        $attr_grp_val_arr = $attribute_value_arr[$ps_default_iso_code];
                                    } else {
                                        $attr_grp_val_arr = $attribute_value_arr[$default_iso_code];
                                    }

                                    $attr_val_arr = explode(',', $attr_grp_val_arr);
                                    $attr_group_value = trim($attr_val_arr[$ag_k]);

                                    if (!isset($attr_dtl[$attrib_name]['value'][$attr_group_value])) {
                                        if (!$attr_dtl[$attrib_name]['grp_dtl']['already_exist']) {        // if attribute group recently created then no need to check group value in prestashop attribute value, directly create attribute value in ps
                                            $attr_val_seller_def_lang = '';
                                            $ps_attr_val = '';
                                            $obj_attribute = new Attribute();
                                            $obj_attribute->id_attribute_group = $attr_dtl[$attrib_name]['grp_dtl']['id_attribute_group'];
                                            foreach ($languages as $lang) {
                                                $attr_val_iso_code = $lang['iso_code'];
                                                if (!isset($attribute_value_arr[$attr_val_iso_code])) {
                                                    $attr_val_iso_code = $default_iso_code;

                                                    if (!$attr_val_seller_def_lang) {
                                                        $comb_attr_val_arr = explode(',', $attribute_value_arr[$attr_val_iso_code]);
                                                        $comb_attr_val = trim($comb_attr_val_arr[$ag_k]);
                                                        $attr_val_seller_def_lang = $comb_attr_val;
                                                    } else {
                                                        $comb_attr_val = $attr_val_seller_def_lang;
                                                    }
                                                } else {
                                                    $comb_attr_val_arr = explode(',', $attribute_value_arr[$attr_val_iso_code]);
                                                    $comb_attr_val = trim($comb_attr_val_arr[$ag_k]);
                                                }

                                                if ($lang['id_lang'] == $ps_default_lang) {
                                                    $ps_attr_val = $comb_attr_val;
                                                }

                                                $obj_attribute->name[$lang['id_lang']] = $comb_attr_val;
                                            }

                                            if ($attrib_type == 'color') {
                                                $color_code = $obj_massupload->ColorNameToHex($comb_attr_val);
                                                if (Tools::strtolower($comb_attr_val) == $color_code) {
                                                    $color_code = '#FFFFFF';
                                                }

                                                $obj_attribute->color = $color_code;
                                            }
                                            $obj_attribute->add();
                                            $attr_group_val_id = $obj_attribute->id;

                                            $attr_dtl[$attrib_name]['value'][$comb_attr_val] = array('id_attribute' => $attr_group_val_id);
                                            $attr_dtl[$attrib_name]['grp_dtl']['already_exist'] = 1;
                                        } else {
                                            if ($ps_attr_value_arr) {
                                                foreach ($ps_attr_value_arr as $attr_val) {
                                                    if ($attr_val['id_attribute_group'] == $attr_dtl[$attrib_name]['grp_dtl']['id_attribute_group']) {
                                                        if ($attr_group_value == trim($attr_val['name'])) {
                                                            $attr_dtl[$attrib_name]['value'][$attr_group_value] = array('id_attribute' => $attr_val['id_attribute']);
                                                            break;
                                                        }
                                                    }
                                                }
                                                if (!isset($attr_dtl[$attrib_name]['value'][$attr_group_value])) {    //if attribut value if not exist in prestashop then we have to create it
                                                    $attr_val_seller_def_lang = '';
                                                    $ps_attr_val = '';
                                                    $obj_attribute = new Attribute();
                                                    $obj_attribute->id_attribute_group = $attr_dtl[$attrib_name]['grp_dtl']['id_attribute_group'];
                                                    foreach ($languages as $lang) {
                                                        $attr_val_iso_code = $lang['iso_code'];
                                                        if (!isset($attribute_value_arr[$attr_val_iso_code])) {
                                                            $attr_val_iso_code = $default_iso_code;

                                                            if (!$attr_val_seller_def_lang) {
                                                                $comb_attr_val_arr = explode(',', $attribute_value_arr[$attr_val_iso_code]);
                                                                $comb_attr_val = trim($comb_attr_val_arr[$ag_k]);
                                                                $attr_val_seller_def_lang = $comb_attr_val;
                                                            } else {
                                                                $comb_attr_val = $attr_val_seller_def_lang;
                                                            }
                                                        } else {
                                                            $comb_attr_val_arr = explode(',', $attribute_value_arr[$attr_val_iso_code]);
                                                            $comb_attr_val = trim($comb_attr_val_arr[$ag_k]);
                                                        }

                                                        if ($lang['id_lang'] == $ps_default_lang) {
                                                            $ps_attr_val = $comb_attr_val;
                                                        }

                                                        $obj_attribute->name[$lang['id_lang']] = $comb_attr_val;
                                                    }

                                                    if ($attrib_type == 'color') {
                                                        $color_code = $obj_massupload->ColorNameToHex($comb_attr_val);
                                                        if (Tools::strtolower($comb_attr_val) == $color_code) {
                                                            $color_code = '#FFFFFF';
                                                        }

                                                        $obj_attribute->color = $color_code;
                                                    }
                                                    $obj_attribute->add();
                                                    $attr_group_val_id = $obj_attribute->id;

                                                    $attr_dtl[$attrib_name]['value'][$comb_attr_val] = array('id_attribute' => $attr_group_val_id);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (isset($mp_default_on)) {
                        if (!$this->editCsv) {                                            // Add COmbiantion
                            // Code for product default combination
                            $default_comb_by_us = false;
                            $comb_dtl = MarketplaceMassUpload::hasPrductDefaultCombination($mp_id_product);
                            if ($comb_dtl) {
                                if ($mp_default_on) {
                                    if (isset($default_comb[$mp_id_product])) {
                                        if (!$default_comb[$mp_id_product]['actual_value']) {
                                            $obj_mp_pro_attr = new WkMpProductAttribute($default_comb[$mp_id_product]['mp_id_product_attribute']);
                                            $obj_mp_pro_attr->mp_default_on = 0;
                                            $obj_mp_pro_attr->save();

                                            WkMpProductAttributeShop::changeAttributeShopDefaultValue($default_comb[$mp_id_product]['mp_id_product_attribute'], 0);

                                            if ($default_comb[$mp_id_product]['id_ps_product_attribute']) {
                                                $obj_comb = new Combination($default_comb[$mp_id_product]['id_ps_product_attribute']);
                                                $obj_comb->default_on = 0;
                                                $obj_comb->save();
                                            }

                                            unset($default_comb[$mp_id_product]);
                                        }
                                    } else {
                                        $mp_default_on = 0;
                                    }
                                }
                            } else {
                                if (!$mp_default_on) {
                                    $default_comb[$mp_id_product]['actual_value'] = $mp_default_on;
                                    $mp_default_on = 1;
                                    $default_comb_by_us = true;
                                }
                            }
                        } else {                                                        // Edit Combination
                            if ($mp_default_on) {
                                if (!in_array($mp_id_product, $defCombChanged)) {
                                    $defCombChanged[] = $mp_id_product;

                                    // set mp_default_on = 0 for existing default combiantion
                                    $comb_dtl = MarketplaceMassUpload::hasPrductDefaultCombination($mp_id_product);
                                    $obj_mp_pro_attr = new WkMpProductAttribute($comb_dtl['id_mp_product_attribute']);
                                    $obj_mp_pro_attr->mp_default_on = 0;
                                    $obj_mp_pro_attr->save();
                                    WkMpProductAttributeShop::changeAttributeShopDefaultValue($comb_dtl['id_mp_product_attribute'], 0);

                                    if ($obj_mp_pro_attr->id_ps_product_attribute) {
                                        $obj_comb = new Combination($obj_mp_pro_attr->id_ps_product_attribute);
                                        $obj_comb->default_on = 0;
                                        $obj_comb->save();
                                    }
                                } else {
                                    $mp_default_on = 0;
                                }
                            }
                        }
                    }

                    $comb_attr_set = array();
                    $all_attr_exist = true;

                    if (isset($attribute_arr[$ps_default_iso_code])) {
                        $attribute_group_arr = $attribute_arr[$ps_default_iso_code];
                    } else {
                        $attribute_group_arr = $attribute_arr[$default_iso_code];
                    }

                    foreach (explode(',', $attribute_group_arr) as $ag_k => $attr_grp) {
                        $attr_grp = trim($attr_grp);
                        if ($attr_grp) {
                            $tab_group = explode(':', $attr_grp);
                            $attrib_name = trim($tab_group[0]);
                            $attrib_type = trim($tab_group[1]);

                            if (!isset($attr_dtl[$attrib_name])) {
                                foreach ($ps_attr_group_arr as $ag_key => $attr_group) {
                                    $attr_dtl[$attr_group['name']]['grp_dtl'] = array('id_attribute_group' => $attr_group['id_attribute_group'], 'is_color_group' => $attr_group['is_color_group']);
                                    if ($attrib_name == trim($attr_group['name'])) {
                                        break;
                                    }
                                }
                            }

                            if (isset($attr_dtl[$attrib_name])) {
                                if (!isset($attr_dtl[$attrib_name]['value'])) {
                                    $attr_dtl[$attrib_name]['value'] = array();
                                }

                                if (isset($attribute_value_arr[$ps_default_iso_code])) {
                                    $attr_grp_val_arr = $attribute_value_arr[$ps_default_iso_code];
                                } else {
                                    $attr_grp_val_arr = $attribute_value_arr[$default_iso_code];
                                }

                                $attr_val_arr = explode(',', $attr_grp_val_arr);
                                $attr_group_value = trim($attr_val_arr[$ag_k]);

                                if (!isset($attr_dtl[$attrib_name]['value'][$attr_group_value])) {
                                    foreach ($ps_attr_value_arr as $attr_val) {
                                        $attr_val['attribute_group'] = trim($attr_val['attribute_group']);
                                        $attr_val['name'] = trim($attr_val['name']);

                                        if (isset($attr_dtl[$attr_val['attribute_group']])) {
                                            if (!isset($attr_dtl[$attr_val['attribute_group']]['value'])) {
                                                $attr_dtl[$attrib_name]['value'] = array();
                                            }

                                            if (!isset($attr_dtl[$attr_val['attribute_group']]['value'][$attr_val['name']])) {
                                                $attr_dtl[$attr_val['attribute_group']]['value'][$attr_val['name']] = array('id_attribute' => $attr_val['id_attribute']);
                                                if ($attr_val['id_attribute_group'] == $attr_dtl[$attrib_name]['grp_dtl']['id_attribute_group']) {
                                                    if ($attr_group_value == $attr_val['name']) {
                                                        $comb_attr_set[] = $attr_val['id_attribute'];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (!isset($attr_dtl[$attrib_name]['value'][$attr_group_value])) {
                                        $all_attr_exist = false;
                                    }
                                } else {
                                    $comb_attr_set[] = $attr_dtl[$attrib_name]['value'][$attr_group_value]['id_attribute'];
                                }
                            } else {
                                $all_attr_exist = false;
                            }
                        }
                    }

                    if (!$all_attr_exist) {
                        $csv_warning .= $warning_string['warning_prefix'] . $line_no . '. ' . $warning_string['attr_not_exist'];
                    } else {
                        $idMpProductAttribute = WkMpProductAttributeCombination::isProductCombinationExists($mp_id_product, $comb_attr_set);
                        if ($this->editCsv && !$idMpProductAttribute) {
                            $csv_warning .= $warning_string['warning_prefix'] . $line_no . '. ' . $warning_string['comb_not_exist'];
                        } elseif (!$this->editCsv && $idMpProductAttribute) {
                            $csv_warning .= $warning_string['warning_prefix'] . $line_no . '. ' . $warning_string['comb_exist'];
                        } else {
                            $kwargs = [
                                'idMpProduct' => isset($mp_id_product) ? $mp_id_product : false,
                                'idMpProductAttribute' => isset($idMpProductAttribute) ? $idMpProductAttribute : false,
                                'productAttributeList' => isset($comb_attr_set) ? $comb_attr_set : false,
                                'mpReference' => isset($mp_reference) ? $mp_reference : false,
                                'mpEan13' => isset($mp_ean13) ? $mp_ean13 : false,
                                'mpUPC' => isset($mp_upc) ? $mp_upc : false,
                                'mpPrice' => isset($mp_price) ? $mp_price : false,
                                'mpWholesalePrice' => isset($mp_wholesale_price) ? $mp_wholesale_price : false,
                                'mpUnitPriceImpact' => isset($attribute_unity) ? $attribute_unity : false,
                                'mpQuantity' => isset($mp_quantity) ? $mp_quantity : false,
                                'mpWeight' => isset($attribute_weight) ? $attribute_weight : false,
                                'mpDefaultOn' => isset($mp_default_on) ? $mp_default_on : false,
                                'mpMinimalQuantity' => isset($attribute_minimal_quantity) ? $attribute_minimal_quantity : false,
                                'mpAvailableDate' => isset($available_date_attribute) ? $available_date_attribute : false,
                                'idImages' => isset($ps_image_id_arr) ? $ps_image_id_arr : false,
                            ];

                            $mpProdAttrDetail = $this->addOrUpdMpProdComb($kwargs);

                            if (!$this->editCsv && $default_comb_by_us) {
                                $default_comb[$mp_id_product]['mp_id_product_attribute'] = $mpProdAttrDetail['idMpProductAttribute'];
                                $default_comb[$mp_id_product]['id_ps_product_attribute'] = $mpProdAttrDetail['idPsProductAttribute'];
                            }
                        }
                    }

                    $line_no += 1;
                }

                if (!isset($toggle_status)) {
                    $obj_massupload->is_approve = 1;
                    $obj_massupload->status = 'Approved';
                    $obj_massupload->is_csv_product_added = '1';
                    $obj_massupload->mass_upload_category = $mass_upload_category;
                    $obj_massupload->request_id = $request_id;
                    $obj_massupload->id_seller = $id_seller;
                    $obj_massupload->total_records = $total_records;
                    $obj_massupload->csv_type = $this->editCsv ? 2 : 1;
                    if ($obj_massupload->add()) {
                        $obj_massupload->sendMailToSeller($id_customer, $request_id);
                    }
                }
            }
        }

        if (!isset($toggle_status)) {
            $csv_path = _PS_MODULE_DIR_ . 'mpmassupload/views/uploaded_csv/';
            move_uploaded_file($csv_tmp_file, $csv_path . $request_id . '.csv');
            if ($approved) {
                fclose($file);
            }
        } else {
            $obj_massupload = new MarketplaceMassUpload($id_mass_upload);
            $obj_massupload->is_csv_product_added = '1';
            if ($obj_massupload->update()) {
                $obj_massupload->sendMailToSeller($id_customer, $request_id);
                if ($mass_upload_category == 1) {
                    if (file_exists($image_zip_path)) {
                        @chmod($image_zip_path, 0777);
                        unlink($image_zip_path);
                    }
                }
            }
        }

        $return_msg = array();
        if ($csv_warning) {
            $return_msg['is_warning'] = 1;
            $return_msg['warning_msg'] = $csv_warning;
        } else {
            $return_msg['is_warning'] = 0;
        }
        return $return_msg;
    }

    public static function deactivateMpProdAfterUpdate($mpIdProduct)
    {
        // Seller can active/deactive products in OFF
        // & Product after update need to approved is ON only for product update page
        if (!Configuration::get('WK_MP_SELLER_PRODUCTS_SETTINGS')
            && Configuration::get('WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE')) {
            //Deactivate the product after seller update that product
            $objSellerProduct = new WkMpSellerProduct($mpIdProduct);
            if ($objSellerProduct->active) {
                $objSellerProduct->active = 0;
                $objSellerProduct->save();
                $productInfo = WkMpSellerProduct::getSellerProductByIdProduct($mpIdProduct);
                if ($productInfo) {
                    $IdPsProduct = $productInfo['id_ps_product'];
                    $objProduct = new Product($IdPsProduct);
                    $objProduct->active = 0;
                    $deactivated = $objProduct->save();
                    if ($deactivated) {
                        WkMpSellerProduct::sendMail($mpIdProduct, 2, 2);

                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function addOrUpdMpProdComb($kwargs)
    {
        foreach ($kwargs as $key => $value) {
            ${$key} = $value;
        }
        $objSellerProduct = new WkMpSellerProduct($idMpProduct);

        if ($idMpProductAttribute) {
            //edit combination
            $editCombi = 1;
            $objMpProductAttribute = new WkMpProductAttribute($idMpProductAttribute);
        } else {
            //Create combination
            $editCombi = 0;
            $objMpProductAttribute = new WkMpProductAttribute();
        }

        if ($idMpProduct !== false) {
            $objMpProductAttribute->id_mp_product = $idMpProduct;
        }
        if ($mpReference !== false) {
            $objMpProductAttribute->mp_reference = $mpReference;
        }
        if ($mpEan13 !== false) {
            $objMpProductAttribute->mp_ean13 = $mpEan13;
        }
        if ($mpUPC !== false) {
            $objMpProductAttribute->mp_upc = $mpUPC;
        }
        if ($mpPrice !== false) {
            $objMpProductAttribute->mp_price = $mpPrice;
        }
        if ($mpWholesalePrice !== false) {
            $objMpProductAttribute->mp_wholesale_price = $mpWholesalePrice;
        }
        if ($mpUnitPriceImpact !== false) {
            $objMpProductAttribute->mp_unit_price_impact = $mpUnitPriceImpact;
        }
        if ($mpQuantity !== false) {
            $objMpProductAttribute->mp_quantity = $mpQuantity;
        }
        if ($mpWeight !== false) {
            $objMpProductAttribute->mp_weight = $mpWeight;
        }
        if ($mpMinimalQuantity !== false) {
            $objMpProductAttribute->mp_minimal_quantity = $mpMinimalQuantity;
        }
        if ($mpAvailableDate !== false) {
            $objMpProductAttribute->mp_available_date = $mpAvailableDate;
        }
        if ($mpDefaultOn !== false) {
            $objMpProductAttribute->mp_default_on = $mpDefaultOn;
        }
        $objMpProductAttribute->save();
        $idMpProductAttribute = $objMpProductAttribute->id;

        if ($editCombi) {
            $defaultCombination = MarketplaceMassUpload::hasPrductDefaultCombination($idMpProduct);
            if (!$defaultCombination) {
                $objMpProductAttribute->mp_default_on = 1;
                $objMpProductAttribute->save();
            }
            WkMpProductAttributeCombination::deleteProductAttributeCombination($idMpProductAttribute);
        }

        $attributeList = array();
        foreach ($productAttributeList as $group) {
            $attributeList[] = array(
                'id_mp_product_attribute' => (int)$idMpProductAttribute,
                'id_ps_attribute' => (int)$group,
            );
        }

        WkMpProductAttributeCombination::insertDataIntoMpproductattributecombination($attributeList);

        $mpReference = $objMpProductAttribute->mp_reference;
        $mpEan13 = $objMpProductAttribute->mp_ean13;
        $mpUPC = $objMpProductAttribute->mp_upc;
        $mpPrice = $objMpProductAttribute->mp_price;
        $mpWholesalePrice = $objMpProductAttribute->mp_wholesale_price;
        $mpUnitPriceImpact = $objMpProductAttribute->mp_unit_price_impact;
        $mpWeight = $objMpProductAttribute->mp_weight;
        $mpMinimalQuantity = $objMpProductAttribute->mp_minimal_quantity;
        $mpAvailableDate = $objMpProductAttribute->mp_available_date;
        $mpDefaultOn = $objMpProductAttribute->mp_default_on;
        $mpQuantity = $objMpProductAttribute->mp_quantity;
        $idImages = $idImages;

        if ($editCombi) {
            self::updateProductAttributeShopData($idMpProductAttribute, $mpPrice, $mpWholesalePrice, $mpUnitPriceImpact, $mpWeight, $mpMinimalQuantity, $mpAvailableDate, $mpDefaultOn);
        } else {
            WkMpProductAttributeShop::insertProductAttributeShopData($idMpProduct, $idMpProductAttribute, $mpPrice, $mpWholesalePrice, $mpUnitPriceImpact, $mpWeight, $mpMinimalQuantity, $mpAvailableDate, $mpDefaultOn);
        }

        //Set Mp combination mp images
        if ($idImages) {
            WkMpProductAttributeImage::setMpImages($idImages, $idMpProductAttribute);
        }

        //Set Mp Quantity
        WkMpStockAvailable::setMpQuantity($idMpProduct, $idMpProductAttribute, $mpQuantity);

        //when combination created/updated then update mp product total quantity
        $currentQty = WkMpProductAttribute::getMpProductQty($idMpProduct);
        if (!$currentQty) {
            $currentQty = 0;
        }

        //Update Seller product qty
        WkMpProductAttribute::updateMpProductQty($currentQty, $idMpProduct);

        if (Configuration::get('WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE')) {
            WkMpSellerProduct::deactivateProductAfterUpdate($idMpProduct, 1);
        } else {
            //if Product is active as a Prestashop product
            if ($objSellerProduct->active && $objSellerProduct->id_ps_product) {
                $idPsProduct = $objSellerProduct->id_ps_product;

                $idPsProductAttribute = $objMpProductAttribute->id_ps_product_attribute;
                if ($idPsProductAttribute) {
                    $objCombination = new Combination($idPsProductAttribute);
                } else {
                    $objCombination = new Combination();
                }

                $objCombination->id_product = $idPsProduct;
                $objCombination->reference = $mpReference;
                $objCombination->ean13 = $mpEan13;
                $objCombination->upc = $mpUPC;
                $objCombination->price = $mpPrice;
                $objCombination->wholesale_price = $mpWholesalePrice;
                $objCombination->unit_price_impact = $mpUnitPriceImpact;
                $objCombination->weight = $mpWeight;
                $objCombination->minimal_quantity = $mpMinimalQuantity;
                $objCombination->available_date = $mpAvailableDate;
                $objCombination->default_on = $mpDefaultOn;
                $objCombination->save();
                $idPsProductAttribute = $objCombination->id;

                if ($editCombi) {
                    //if admin delete combination from catalog then another combination will automatially created when seller update the combination of product.
                    WkMpProductAttributeCombination::deleteProductAttrCombByPsAttrId($idPsProductAttribute);
                }

                foreach ($productAttributeList as $group) {
                    $objMpProductAttribute->insertIntoPsProductCombination($group, $idPsProductAttribute);
                }

                //Update ps product id and ps product attribute id, If product is active
                $objMpAttribute = new WkMpProductAttribute($idMpProductAttribute);
                $objMpAttribute->id_mp_product = $idMpProduct;
                $objMpAttribute->id_ps_product_attribute = $idPsProductAttribute;
                $objMpAttribute->id_ps_product = $idPsProduct;
                $objMpAttribute->save();

                //combination ps Images
                $objCombination->setImages($idImages);

                StockAvailable::setQuantity($idPsProduct, $idPsProductAttribute, $mpQuantity);
            }
        }

        $result = [
            'idMpProductAttribute' => $idMpProductAttribute,
            'idPsProductAttribute' => isset($idPsProductAttribute) ? $idPsProductAttribute : 0,
        ];

        return $result;
    }

    public static function updateProductAttributeShopData($idMpProductAttribute, $mpPrice, $mpWholesalePrice, $mpUnitPriceImpact, $mpWeight, $mpMinimalQuantity, $mpAvailableDate, $combiDefault = false)
    {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'wk_mp_product_attribute_shop
                SET `mp_price` = "' . (float)$mpPrice . '",
                    `mp_wholesale_price` = "' . (float)$mpWholesalePrice . '",
                    `mp_unit_price_impact` = "' . (float)$mpUnitPriceImpact . '",
                    `mp_weight` = "' . (float)$mpWeight . '",
                    `mp_minimal_quantity` = "' . (int)$mpMinimalQuantity . '",
                    `mp_available_date` = "' . $mpAvailableDate . '" ';

        if ($combiDefault) {
            $sql .= ', `mp_default_on` = "' . (int)$combiDefault . '"';
        }

        $sql .= ' WHERE `id_mp_product_attribute` = ' . (int)$idMpProductAttribute;

        return Db::getInstance()->execute($sql);
    }

    public static function generateArchive($sellerProducts, $idCustomer)
    {
        $dir = _PS_MODULE_DIR_ . 'mpmassupload/views/export_csv/' . $idCustomer;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $zip_file_name = 'mpmassupload/views/export_csv/' . $idCustomer . '/product_image.zip';
        if ($sellerProducts) {
            foreach ($sellerProducts as $key => $sellerProduct) {
                $numbrings = $key + 1;
                $productImages = WkMpSellerProduct::getSellerProductImages($sellerProduct['id_mp_product']);
                if ($productImages) {
                    foreach ($productImages as $productImage) {
                        $zip = new ZipArchive();
                        $zip->open(_PS_MODULE_DIR_ . $zip_file_name, ZipArchive::CREATE);
                        $zip->addEmptyDir('/product_image');
                        $zip->addFile(
                            _PS_MODULE_DIR_ . 'marketplace/views/img/product_img/' . $productImage['seller_product_image_name'],
                            'product_image/image' . $numbrings . '/' . $productImage['seller_product_image_name']
                        );
                        $zip->close();
                        header("Content-type: application/zip");
                        header("Content-Disposition: attachment; filename=product_image.zip");
                        header("Pragma: no-cache");
                        header("Expires: 0");
                        readfile(_PS_MODULE_DIR_ . $zip_file_name);
                    }
                }
            }
        }
        exit;

        // return;
    }

    /**
     * RecursiveRemove function
     *
     * @param [type] $dir
     * @return void
     */
    public static function recursiveRemove($dir)
    {
        if (is_dir($dir)) {
            $structure = glob(rtrim($dir, "/").'/*');
            if (is_array($structure)) {
                foreach ($structure as $file) {
                    if (is_dir($file)) self::recursiveRemove($file);
                    elseif (is_file($file)) unlink($file);
                }
            }
            rmdir($dir);
        }
    }
}
