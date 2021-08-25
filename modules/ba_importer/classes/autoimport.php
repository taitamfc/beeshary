<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *   @author    Buy-addons <contact@buy-addons.com>
 *   @copyright 2007-2020 PrestaShop SA
 *   @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *   International Registered Trademark & Property of PrestaShop SA
 */

class AutoImport extends Ba_importer
{
    public $array_colum = array();
    public $context;
    private $array_result = array();
    
    public $shop_id;
    public $shop_id_group;
    public $get_id_config;
    public $number_add_product = 2000;

    public function funcAutoImport($list_ba_cron, $product_end)
    {
        ob_implicit_flush(1);
        if (is_array($list_ba_cron) && !empty($list_ba_cron)) {
            if (Configuration::getGlobalValue('baautoimpor_is_run') == 0 || $product_end != false) {
                Configuration::updateGlobalValue('baautoimpor_is_run', 1);
                foreach ($list_ba_cron as $key_list_ba_cron => &$cron) {
                    $cron;
                    $i_s_g = $list_ba_cron[$key_list_ba_cron]['id_shop_group'];
                    $i_s = $list_ba_cron[$key_list_ba_cron]['id_shop'];
                    $auto_import = Configuration::get('CONFIGN_AUTO_IMPORT', null, $i_s_g, $i_s);
                    if ($auto_import != false) {
                        if ($this->shouldBeExecuted($list_ba_cron[$key_list_ba_cron])==true || $product_end != false) {
                            $ic = (int) $list_ba_cron[$key_list_ba_cron]['id_cronjob'];
                            $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'ba_cronjobs_importer ';
                            $query .= 'WHERE `id_cronjob` = \''. (int) $ic.'\'';
                            $data_auto_import = Db::getInstance()->ExecuteS($query, true, false);
                            $data_post = $data_auto_import['0']['CONFIGN_DATA_POST'];
                            if ($data_post != null) {
                                $arr_data_post = Tools::jsonDecode($data_post, true);
                                $arr_data_bc1 = Tools::jsonDecode($arr_data_post["arr"], true);
                                $table_name = 'ba_importer_data_' . $data_auto_import['0']['id_importer_config'];
                                $this->get_id_config = $data_auto_import['0']['id_importer_config'];
                                if ($product_end == false) {
                                    if ($arr_data_bc1["import_local"] == 0) {
                                        $url_excel = $arr_data_bc1["url_excel"];
                                        $link_exits = $this->urlExists($url_excel);
                                        if ($link_exits === true) {
                                            $post_file = array();
                                            $post_file[] = strpos(Tools::strtolower($url_excel), ".csv");
                                            $post_file[] = strpos(Tools::strtolower($url_excel), ".xls");
                                            $post_file[] = strpos(Tools::strtolower($url_excel), ".xlsx");
                                            $ext = 0;
                                            foreach ($post_file as $ktfile) {
                                                if ($ktfile == true) {
                                                    $ext = 1;
                                                }
                                            }
                                            if ($ext == 1) {
                                                $arr = explode("/", $url_excel);
                                                $fileName = trim(end($arr));
                                                $saveto = _PS_MODULE_DIR_ . 'ba_importer/stories/' . $fileName;
                                                $this->getImageFromUrl($url_excel, $saveto);
                                                $get_file_name = $fileName;
                                            }
                                        }
                                    }
                                    if ($arr_data_bc1["import_local"] == 2) {
                                        $fp = $arr_data_bc1["ftp_link_excel"];
                                        $arr = explode("/", $fp);
                                        $fileName = trim(end($arr));
                                        $post_file = array();
                                        $post_file[] = strpos(Tools::strtolower($fileName), ".csv");
                                        $post_file[] = strpos(Tools::strtolower($fileName), ".xls");
                                        $post_file[] = strpos(Tools::strtolower($fileName), ".xlsx");
                                        $ext = 0;
                                        foreach ($post_file as $ktfile) {
                                            if ($ktfile >0) {
                                                $ext = 1;
                                            }
                                        }
                                        if ($ext == 1) {
                                            // Connection Settings
                                            $ftp_server = $arr_data_bc1["ftp_server"]; // Address of FTP server.
                                            $ftp_user_name = $arr_data_bc1["ftp_user_name"]; // Username
                                            $ftp_user_pass = $arr_data_bc1["ftp_user_pass"]; // Password
                                            $ftp_user_port = $arr_data_bc1["ftp_user_port"]; // Port
                                            if (empty($ftp_user_port)) {
                                                $ftp_user_port = 21;
                                            }
                                            $ftp_user_transfer_mode = $arr_data_bc1["ftp_user_transfer_mode"];
                                            if ($ftp_user_transfer_mode == 'active') {
                                                $ftp_user_transfer_mode = false;
                                            } else {
                                                $ftp_user_transfer_mode = true;
                                            }
                                            // set up basic connection
                                            $conn_id = ftp_connect($ftp_server, $ftp_user_port);
                                            // login with username and password
                                            $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
                                            if ($login_result === true) {
                                                ftp_pasv($conn_id, $ftp_user_transfer_mode);
                                                $dir_file = _PS_MODULE_DIR_ . $this->name . '/stories/' . $fileName;
                                                $download=ftp_get($conn_id, $dir_file, $fp, FTP_BINARY);
                                                // try to download $server_file and save to $local_file
                                                if ($download !== false) {
                                                    $product_end = '';
                                                    $this->repeatImporter($product_end);
                                                }
                                                ftp_close($conn_id);
                                            }
                                        }
                                        $get_file_name = $fileName;
                                    }
                                    $import_header = $arr_data_bc1['import_header'];
                                    if (!isset($get_file_name) || !$this->validateFile($get_file_name)) {
                                        // Error while downloading file -> stop this cronjob, process other settings
                                        $this->processCronjobError($this->get_id_config) ;
                                        exit();
                                    }
                                    $this->saveDataCsvToDatabase($get_file_name, $table_name, $import_header);
                                }
                                $this->submitAddDb($arr_data_post, $table_name, $ic, $product_end);
                                
                                $query = 'UPDATE ' . _DB_PREFIX_ . 'ba_cronjobs_importer SET ';
                                $query .= '`update_at` = NOW() ';
                                $query .= 'WHERE `id_cronjob` = \''
                                        . (int) $list_ba_cron[$key_list_ba_cron]['id_cronjob'].'\'';

                                Db::getInstance()->query($query);
                                Db::getInstance()->disconnect();
                            }
                        } else {
                            $sl_ba_cronjobs = 'UPDATE ' . _DB_PREFIX_ . 'ba_cronjobs_importer SET imported=1 ';
                            $sl_ba_cronjobs .= 'WHERE `id_cronjob` = \''
                                        . (int) $list_ba_cron[$key_list_ba_cron]['id_cronjob'].'\'';
                            Db::getInstance()->query($sl_ba_cronjobs);
                            Configuration::updateGlobalValue('baautoimpor_is_run', 0);
                            $product_end = '';
                            $this->repeatImporter($product_end);
                        }
                    }
                }
            } else {
                $baimport = new Ba_importer();
                foreach ($list_ba_cron as $key_list_ba_cron => &$cron) {
                    $cron;
                    $ic = (int) $list_ba_cron[$key_list_ba_cron]['id_cronjob'];
                    $query = 'SELECT id_importer_config FROM ' . _DB_PREFIX_ . 'ba_cronjobs_importer ';
                    $query .= 'WHERE `id_cronjob` = \''. (int) $ic.'\'';
                    $iic = Db::getInstance()->getValue($query, false);
                    $query = 'SELECT ba_name_setting FROM ' . _DB_PREFIX_ . 'ba_importer_config ';
                    $query .= 'WHERE `id_importer_config` = \''. (int) $iic.'\'';
                    $bns = Db::getInstance()->getValue($query, false);
                    if (!empty($iic) && !empty($bns)) {
                        $notice = $baimport->l('This Setting added to Queue because Setting ID ');
                        $notice .= sprintf($baimport->l('#%u (named %s) '), $iic, $bns);
                        $notice .= $baimport->l('is running. ');
                        $notice .= $baimport->l('After It is finished to execute Setting ID ');
                        $notice .= sprintf($baimport->l('#%u'), $iic);
                        $notice .= $baimport->l(', this Setting automatic executed from Queue.');
                        $notice .= $baimport->l(' You do not need do anything.');
                        echo $notice;
                    }
                }
            }
        }
        if (is_array($list_ba_cron) && empty($list_ba_cron)) {
            $sl_ba_cronjobs = 'UPDATE ' . _DB_PREFIX_ . 'ba_cronjobs_importer SET imported = 0 WHERE imported=1';
            Db::getInstance()->query($sl_ba_cronjobs);
            Configuration::updateGlobalValue('baautoimpor_is_run', 0);
            Configuration::updateGlobalValue('baautoimpor_id_queue', 0);
        }
        die();
    }

    public function submitAddDb($arr_data_post, $table_name, $id_cronjob, $product_end)
    {
        $db = Db::getInstance();
        $this->context = Context::getContext();
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . $table_name;
        if ($arr_data_post["import_header"] == 0) {
            $array_tmp = array('tmp', 'tmp2');
        } else {
            $array_tmp = array('tmp');
        }
        $end_array = $db->ExecuteS($sql, true, false);
        $array = array_merge($array_tmp, $end_array);
        unset($array[0]);
        
        $warehouse = array();
        $feature_column = array();
        $ar_supplier = array();
        $add_img = array();
        $select_column = $arr_data_post["select"];
        $array_colum = array();
        $category_associated = array();
        $category_associated_id = array();
        $uploadable_files = array();
        $text_fields = array();
        $combination_column = array();
        $ba_combination_image = array();
        $ba_array_shipping = array();
        $ba_array_price = array();
        $ba_array_specific = array();
        $ba_array_virtual = array();
        $ba_array_attachments = array();
        $deliverytimes = array();
        $arr_lang =array();
        $lang = Language::getLanguages();
        foreach ($lang as $value_lang) {
            $arr_lang[$value_lang["id_lang"]] = $value_lang["iso_code"];
        }
        $lang = Language::getLanguages(false);
        foreach ($lang as $value_lang) {
            $arr_lang[$value_lang["id_lang"]] = $value_lang["iso_code"];
        }
        foreach ($select_column as $key_select => $value) {
            $key = $key_select;
            if ($value == "1") {
                $name = $key;
                $array_colum["Name"] = $name;
            }
            if ($value == "5") {
                $description = $key;
                $array_colum["Product Full Description"] = $description;
            }
            if ($value == "6") {
                $short_description = $key;
                $array_colum["Product Short Description"] = $short_description;
            }
            if ($value == "3") {
                $tags = $key;
                $array_colum["Tags"] = $tags;
            }
            foreach ($arr_lang as $key_arr_lang => $value_arr_lang) {
                $key_arr_lang;
                if ($value == "product_name_".$value_arr_lang."") {
                    $name_lang = $key;
                    $array_colum["Name_".$value_arr_lang.""] = $name_lang;
                }
                if ($value == "product_fulldes_".$value_arr_lang."") {
                    $description_lang = $key;
                    $array_colum["Product Full Description ".$value_arr_lang.""] = $description_lang;
                }
                if ($value == "product_shortdes_".$value_arr_lang."") {
                    $short_description_lang = $key;
                    $array_colum["Product Short Description ".$value_arr_lang.""] = $short_description_lang;
                }
                if ($value == "product_tags_".$value_arr_lang."") {
                    $tags_lang = $key;
                    $array_colum["Product Tags ".$value_arr_lang.""] = $tags_lang;
                }
            }
            if ($value == "2") {
                $reference = $key;
                $array_colum["Reference"] = $reference;
            }
            if ($value == "4") {
                $product_id = $key;
                $array_colum["Product ID"] = $product_id;
            }
            if ($value == "status") {
                $status = $key;
                $array_colum["Status"] = $status;
            }
            if ($value == "7") {
                $wholesale_price = $key;
                $array_colum["Pre-tax wholesale price"] = $wholesale_price;
            }
            if ($value == "8") {
                $retail_price = $key;
                $array_colum["Pre-tax retail price"] = $retail_price;
            }
            if ($value == "priceintax") {
                $priceintax = $key;
                $array_colum["priceintax"] = $priceintax;
            }
            if ($value == "ecotax") {
                $array_colum["ecotax"] = $key;
            }
            if ($value == "iso_code") {
                $iso_code = $key;
                $array_colum["iso_code"] = $iso_code;
            }
            if ($value == "9") {
                $meta_title = $key;
                $array_colum["Meta title"] = $meta_title;
            }
            if ($value == "10") {
                $meta_description = $key;
                $array_colum["Meta description"] = $meta_description;
            }
            if ($value == "11") {
                $meta_keywords = $key;
                $array_colum["Meta keywords"] = $meta_keywords;
            }
            if ($value == "12") {
                $friendly_url = $key;
                $array_colum["Friendly URL"] = $friendly_url;
            }
            foreach ($arr_lang as $key_arr_lang => $value_arr_lang) {
                if ($value == "meta_title_".$value_arr_lang."") {
                    $meta_title_lang = $key;
                    $array_colum["Meta title ".$value_arr_lang.""] = $meta_title_lang;
                }
                if ($value == "meta_description_".$value_arr_lang."") {
                    $meta_description_lang = $key;
                    $array_colum["Meta description ".$value_arr_lang.""] = $meta_description_lang;
                }
                if ($value == "friendly_url_".$value_arr_lang."") {
                    $friendly_url_lang = $key;
                    $array_colum["Friendly URL ".$value_arr_lang.""] = $friendly_url_lang;
                }
            }
            if ($value == "redirect_type") {
                $array_colum["redirect_type"] = $key;
            }
            if ($value == "id_type_redirected") {
                $array_colum["id_type_redirected"] = $key;
            }
            if ($value == "delete_product") {
                $array_colum["delete_product"] = $key;
            }
            if ($value == "14") {
                $quantities = $key;
                $array_colum["Quantities"] = $quantities;
            }
            if ($value == "advanced_stock_management") {
                $array_colum["advanced_stock_management"] = $key;
            }
            if ($value == "15") {
                $ean13 = $key;
                $array_colum["EAN13 or JAN"] = $ean13;
            }
            if ($value == "16") {
                $upc = $key;
                $array_colum["UPC"] = $upc;
            }
            if ($value == "isbn") {
                $isbn = $key;
                $array_colum["isbn"] = $isbn;
            }
            if ($value == 'main_category') {
                $main_category = $key;
                $array_colum["main_category"] = $main_category;
            }
            if ($value == 'category_associated') {
                $category_associated[] = $key;
            }
            if ($value == 'main_category_id') {
                $array_colum["main_category_id"] = $key;
            }
            if ($value == 'category_associated_id') {
                $category_associated_id[] = $key;
            }
            if ($value == 'supplier') {
                $ar_supplier[] = $key;
            }
            if ($value == 'supplier_reference') {
                $array_colum["supplier_reference"] = $key;
            }
            if ($value == 'supplier_price_te') {
                $array_colum["supplier_price_te"] = $key;
            }
            if ($value == 'supplier_currency') {
                $array_colum["supplier_currency"] = $key;
            }
            if ($value == 'manufacturer') {
                $manufacturer = $key;
                $array_colum["manufacturer"] = $manufacturer;
            }
            if ($value == 'accessories_ids') {
                $accessories_ids = $key;
                $array_colum["accessories_ids"] = $accessories_ids;
            }
            if ($value == 'accessories_ref') {
                $accessories_ref = $key;
                $array_colum["accessories_ref"] = $accessories_ref;
            }
            if ($value == "17") {
                $main_img = $key;
                $array_colum["main_img"] = $main_img;
            }

            if ($value == "18") {
                $add_img[] = $key;
            }

            if ($value == 'delete_existing_images') {
                $delete_existing_images = $key;
                $array_colum["delete_existing_images"] = $delete_existing_images;
            }
            
            if ($value == "available_now") {
                $array_colum["available_now"] = $key;
            }
            if ($value == "available_later") {
                $array_colum["available_later"] = $key;
            }
            foreach ($arr_lang as $value_arr_lang) {
                if ($value == "available_now_".$value_arr_lang."") {
                    $array_colum["available_now_".$value_arr_lang.""] = $key;
                }
                if ($value == "available_later_".$value_arr_lang."") {
                    $array_colum["available_later_".$value_arr_lang.""] = $key;
                }
            }
            if ($value == "product_minimal_quantity") {
                $array_colum["product_minimal_quantity"] = $key;
            }
            if ($value == "product_available_date") {
                $array_colum["product_available_date"] = $key;
            }

            if ($value == "available_for_order") {
                $array_colum["available_for_order"] = $key;
            }

            if ($value == "visibility") {
                $array_colum["visibility"] = $key;
            }

            if ($value == "condition") {
                $array_colum["condition"] = $key;
            }
            if ($value == "show_condition") {
                $array_colum["show_condition"] = $key;
            }
            if ($value == "online_only") {
                $array_colum["online_only"] = $key;
            }
            if ($value == "uploadable_files") {
                $uploadable_files[] = $key;
            }

            if ($value == "text_fields") {
                $text_fields[] = $key;
            }

            if ($value == "out_of_stock") {
                $array_colum["out_of_stock"] = $key;
            }

            if ($value == "depends_on_stock") {
                $array_colum["depends_on_stock"] = $key;
            }
            
            if (strpos($value, "feature_") === 0) {
                $id = Tools::substr($value, -(Tools::strlen($value) - 8));
                $feature_column[$id] = $key;
            }

            if (strpos($value, "warehouse_") === 0) {
                if (strpos($value, "location") > 0) {
                    $name_id = Tools::substr($value, 0, (Tools::strlen($value) - 9));
                    $id_warehouse = (int) Tools::substr($name_id, -(Tools::strlen($name_id) - 10));
                    $warehouse[$id_warehouse]["location"] = $key;
                }
                if (strpos($value, "quantity") > 0) {
                    $name_id = Tools::substr($value, 0, (Tools::strlen($value) - 9));
                    $id_warehouse = (int) Tools::substr($name_id, -(Tools::strlen($name_id) - 10));
                    $warehouse[$id_warehouse]["quantity"] = $key;
                }
                if (strpos($value, "usable") > 0) {
                    $name_id = Tools::substr($value, 0, (Tools::strlen($value) - 7));
                    $id_warehouse = (int) Tools::substr($name_id, -(Tools::strlen($name_id) - 10));
                    $warehouse[$id_warehouse]["usable"] = $key;
                }
                if (strpos($value, "price") > 0) {
                    $name_id = Tools::substr($value, 0, (Tools::strlen($value) - 6));
                    $id_warehouse = (int) Tools::substr($name_id, -(Tools::strlen($name_id) - 10));
                    $warehouse[$id_warehouse]["price"] = $key;
                }
                if (strpos($value, "iso_code") > 0) {
                    $name_id = Tools::substr($value, 0, (Tools::strlen($value) - 9));
                    $id_warehouse = (int) Tools::substr($name_id, -(Tools::strlen($name_id) - 10));
                    $warehouse[$id_warehouse]["iso_code"] = $key;
                }
                if (strpos($value, "label") > 0) {
                    $name_id = Tools::substr($value, 0, (Tools::strlen($value) - 6));
                    $id_warehouse = (int) Tools::substr($name_id, -(Tools::strlen($name_id) - 10));
                    $warehouse[$id_warehouse]["label"] = $key;
                }
            }

            if (strpos($value, "combination_") === 0) {
                $id_group = (int) Tools::substr($value, -(Tools::strlen($value) - 12));
                $combination_column[$id_group] = $key;
            }

            if (strpos($value, "shipping_") === 0) {
                $key_shipping = Tools::substr($value, -(Tools::strlen($value) - 9));
                $ba_array_shipping[$key_shipping] = $key;
            }
            if (strpos($value, "deliverytimes_") === 0) {
                $deliverytimes[$value] = $key;
            }
            if ($value == "id_carriers") {
                $id_carriers = $key;
                $array_colum["id_carriers"] = $id_carriers;
            }
            
            if (strpos($value, "price_") === 0) {
                $key_price = Tools::substr($value, -(Tools::strlen($value) - 6));
                $ba_array_price[$key_price] = $key;
            }
            
            if (strpos($value, "specific_") === 0) {
                $key_specific = Tools::substr($value, -(Tools::strlen($value) - 9));
                $ba_array_specific[$key_specific] = $key;
            }

            if (strpos($value, "virtual_") === 0) {
                $key_virtual = Tools::substr($value, -(Tools::strlen($value) - 8));
                $ba_array_virtual[$key_virtual] = $key;
            }
            
            if (strpos($value, "attachments_") === 0) {
                $key_attachments = Tools::substr($value, -(Tools::strlen($value) - 12));
                $ba_array_attachments[$key_attachments] = $key;
            }
            
            if ($value == "ba_combination_quantity") {
                $array_colum["ba_combination_quantity"] = $key;
                $array_colum["combination_field"]["ba_combination_quantity"] = $key;
            }
            
            if ($value == "ba_combination_reference") {
                $array_colum["combination_field"]["ba_combination_reference"] = $key;
            }

            if ($value == "ba_combination_ean13") {
                $array_colum["combination_field"]["ba_combination_ean13"] = $key;
            }

            if ($value == "ba_combination_upc") {
                $array_colum["combination_field"]["ba_combination_upc"] = $key;
            }

            if ($value == "ba_combination_wholesale_price") {
                $array_colum["combination_field"]["ba_combination_wholesale_price"] = $key;
            }

            if ($value == "ba_combination_price") {
                $array_colum["combination_field"]["ba_combination_price"] = $key;
            }
            
            if ($value == "ba_combination_price_incl") {
                $array_colum["combination_field"]["ba_combination_price_incl"] = $key;
            }
            if ($value == "ba_combination_weight") {
                $array_colum["combination_field"]["ba_combination_weight"] = $key;
            }
            if ($value == "ba_combination_unit_price_impact") {
                $array_colum["combination_field"]["ba_combination_unit_price_impact"] = $key;
            }
            if ($value == "ba_combination_minimal_quantity") {
                $array_colum["combination_field"]["ba_combination_minimal_quantity"] = $key;
            }
            if ($value == "ba_combination_available_date") {
                $array_colum["combination_field"]["ba_combination_available_date"] = $key;
            }
            if ($value == "ba_combination_default_on") {
                $array_colum["combination_field"]["ba_combination_default_on"] = $key;
            }
            if ($value == "ba_combination_isbn") {
                $array_colum["combination_field"]["ba_combination_isbn"] = $key;
            }
            if ($value == "ba_combination_image") {
                $ba_combination_image[] = $key;
            }
            // since 1.1.5
            if ($value == "ba_combination_id") {
                $array_colum["combination_field"]["ba_combination_id"] = $key;
            }
        }
        $array_colum["category_associated"] = $category_associated;
        $array_colum["category_associated_id"] = $category_associated_id;
        $array_colum["supplier"] = $ar_supplier;
        $array_colum["add_img"] = $add_img;
        $array_colum["uploadable_files"] = $uploadable_files;
        $array_colum["text_fields"] = $text_fields;
        $array_colum["combination"] = $combination_column;
        $array_colum["combination_field"]["combination_images"] = $ba_combination_image;
        $array_colum["warehouse"] = $warehouse;
        $array_colum["shipping"] = $ba_array_shipping;
        $array_colum["price"] = $ba_array_price;
        $array_colum["specific"] = $ba_array_specific;
        $array_colum["virtual"] = $ba_array_virtual;
        $array_colum["attachments"] = $ba_array_attachments;
        $array_colum["deliverytimes"] = $deliverytimes;
        if ($product_end == false || $product_end == '') {
            $product_end = 2;
        }
        $this->addValues($array_colum, $feature_column, $array, $arr_data_post, $id_cronjob, $product_end);
    }
    
    private function addValues(array $array_colum, array $feature_column, array $array, $dp, $ic, $start_import = 2)
    {
        $outputFlie = _PS_MODULE_DIR_."ba_importer/cronjob/log_auto_import_".$this->get_id_config.".txt";
        if (file_exists($outputFlie)) {
            @unlink($outputFlie);
        }
        $arr_data_post = $dp;
        $id_cronjob = (int) $ic;
        $this->shop_id = $arr_data_post['shop_id'];
        $tokenProducts = $arr_data_post['tokenProducts'];
        $this->shop_id_group = $arr_data_post['shop_id_group'];
        $_post = (array) Tools::jsonDecode($arr_data_post['arr']);
        $db = Db::getInstance();
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'feature_lang GROUP BY id_feature';
        $advance_feature = $db->executeS($sql, true, false);
        $feature = array();
        foreach ($advance_feature as $row) {
            $feature[] = $row['name'];
        }

        $end = count($array);
        if ($arr_data_post["demo_mode"] == 1) {
            if ($end > 21) {
                $end = 21;
            }
        }
        $start = $start_import;
        $first_import = false;
        if ($start == 2) {
            if ($_post["import_header"] == 1) {
                $start = 1;
            }
            $first_import = true;
        }
        $product_start = (int) $_post["product_start"];
        if ($_post["import_items"] == "Range" && $product_start>1 && $start<=$product_start) {
            $start = (int) ($_post["product_start"]);
            $first_import = true;
        }
        // since 1.1.0
        if ($first_import == true) {
            $this->cleanProductsInFile($this->get_id_config);
        }
        $product_end_range = (int) $_post["product_end"];
        if ($_post["import_items"] == "Range" && $product_end_range>1 && $end>$product_end_range) {
            $end = $product_end_range;
        }

        $product_end = $start + $this->number_add_product;
        if ($product_end >= $end) {
            $product_end = $end;
        }
        $result = $product_end;

        $array_multi = array();
        $arr_log_import = '';
        $this->array_result = array();
        $arr_id_lang= array();
        $languagesArr = Language::getLanguages(false);
        foreach ($languagesArr as $v) {
            $arr_id_lang[]=(int) ($v['id_lang']);
        }
        $id_default_language = (int) (Configuration::get('PS_LANG_DEFAULT'));
        foreach ($arr_id_lang as $key_id_lang => $value_id_lang) {
            if ($arr_id_lang[$key_id_lang] == $id_default_language) {
                unset($arr_id_lang[$key_id_lang]);
            }
        }
        array_unshift($arr_id_lang, $id_default_language);
        $arr_lang =array();
        $lang = Language::getLanguages();
        foreach ($lang as $value_lang) {
            $arr_lang[$value_lang["id_lang"]] = $value_lang["iso_code"];
        }
        $lang = Language::getLanguages(false);
        foreach ($lang as $value_lang) {
            $arr_lang[$value_lang["id_lang"]] = $value_lang["iso_code"];
        }

        $_name_array = array();
        $_description_array = array();
        $_short_description_array = array();
        $_meta_title_array = array();
        $_meta_description_array = array();
        $_friendly_url_array = array();
        $_tags_array = array();
        $_anow_array = array();
        $_alater_array = array();
        $arr_log_import = date("Y-m-d H:i:s")." - " . $_post['select_settings'] . "\n";
        $this->logImport($arr_log_import, 'auto', $this->get_id_config);
        foreach ($array as $key => $row) {
            if ($key >= $start && $key <= $product_end) {
                @$_name = $row[$array_colum["Name"]];
                @$_description = pSQL($row[$array_colum["Product Full Description"]], true);
                @$_short_description = pSQL($row[$array_colum["Product Short Description"]], true);
                @$_meta_title = $row[$array_colum["Meta title"]];
                @$_meta_description = $row[$array_colum["Meta description"]];
                @$_friendly_url = $row[$array_colum["Friendly URL"]];
                @$redirect_type = $row[$array_colum["redirect_type"]];
                @$id_type_redirected = $row[$array_colum["id_type_redirected"]];
                @$delete_product = $row[$array_colum["delete_product"]];
                @$_tags = $row[$array_colum["Tags"]];
                @$_available_now = $row[$array_colum["available_now"]];
                @$_available_later = $row[$array_colum["available_later"]];
                foreach ($arr_lang as $key_arr_lang => $value_arr_lang) {
                    if (isset($array_colum["Name_".$value_arr_lang.""]) == true) {
                        @$_name_array[$key_arr_lang] = $row[$array_colum["Name_".$value_arr_lang.""]];
                    }
                    if (isset($array_colum["Product Full Description ".$value_arr_lang.""]) == true) {
                        $de_ar = $row[$array_colum["Product Full Description ".$value_arr_lang.""]];
                        @$_description_array[$key_arr_lang] = $de_ar;
                    }
                    if (isset($array_colum["Product Short Description ".$value_arr_lang.""]) == true) {
                        $sh_de_ar = $row[$array_colum["Product Short Description ".$value_arr_lang.""]];
                        @$_short_description_array[$key_arr_lang] = $sh_de_ar;
                    }
                    if (isset($array_colum["Meta title ".$value_arr_lang.""]) == true) {
                        @$_meta_title_array[$key_arr_lang] = $row[$array_colum["Meta title ".$value_arr_lang.""]];
                    }
                    if (isset($array_colum["Meta description ".$value_arr_lang.""]) == true) {
                        $me_de_ar = $row[$array_colum["Meta description ".$value_arr_lang.""]];
                        @$_meta_description_array[$key_arr_lang] = $me_de_ar;
                    }
                    if (isset($array_colum["Friendly URL ".$value_arr_lang.""]) == true) {
                        @$_friendly_url_array[$key_arr_lang] = $row[$array_colum["Friendly URL ".$value_arr_lang.""]];
                    }
                    if (isset($array_colum["Product Tags ".$value_arr_lang.""]) == true) {
                        @$_tags_array[$key_arr_lang] = $row[$array_colum["Product Tags ".$value_arr_lang.""]];
                    }
                    if (isset($array_colum["available_now_".$value_arr_lang.""]) == true) {
                        @$_anow_array[$key_arr_lang] = $row[$array_colum["available_now_".$value_arr_lang.""]];
                    }
                    if (isset($array_colum["available_later_".$value_arr_lang.""]) == true) {
                        @$_alater_array[$key_arr_lang] = $row[$array_colum["available_later_".$value_arr_lang.""]];
                    }
                }
                @$_reference = $row[$array_colum["Reference"]];
                @$_product_id = $row[$array_colum["Product ID"]];
                @$_status = Tools::strtoupper($row[$array_colum["Status"]]);

                @$_wholesale_price = $this->replacePrice($row[$array_colum["Pre-tax wholesale price"]]);
                @$_retail_price = $this->replacePrice($row[$array_colum["Pre-tax retail price"]]);
                @$price_intax = $this->replacePrice($row[$array_colum["priceintax"]]);
                @$ecotax = $this->replacePrice($row[$array_colum["ecotax"]]);
                 
                @$_iso_code = $row[$array_colum["iso_code"]];
                
                @$_meta_keywords = $row[$array_colum["Meta keywords"]];
                @$_quantities = $row[$array_colum["Quantities"]];
                @$_combi_quantity_ori = $row[$array_colum["ba_combination_quantity"]];
                @$_combination_quantity = (int) $_combi_quantity_ori;
                @$_depends_on_stock = $row[$array_colum["depends_on_stock"]];
                @$_advanced_stock_management = $row[$array_colum["advanced_stock_management"]];
                @$_ean13 = $row[$array_colum["EAN13 or JAN"]];
                @$_upc = $row[$array_colum["UPC"]];
                @$_isbn = $row[$array_colum["isbn"]];
                @$_category_default = $row[$array_colum["main_category"]];
                @$_category_default_id = $row[$array_colum["main_category_id"]];
                @$_supplier_reference = $row[$array_colum["supplier_reference"]];
                @$_supplier_price_te = round($this->replacePrice($row[$array_colum["supplier_price_te"]]), 6);
                @$_supplier_currency = $row[$array_colum["supplier_currency"]];
                @$_manufacturer = $row[$array_colum["manufacturer"]];
                @$accessories_ids = $row[$array_colum["accessories_ids"]];
                @$accessories_ref = $row[$array_colum["accessories_ref"]];
                @$_main_img = $row[$array_colum["main_img"]];
                @$_delete_existing_images = $row[$array_colum["delete_existing_images"]];
                @$product_minimal_quantity = $row[$array_colum["product_minimal_quantity"]];
                @$product_available_date = $row[$array_colum["product_available_date"]];
                @$_available_for_order = $row[$array_colum["available_for_order"]];
                @$_visibility = $row[$array_colum["visibility"]];
                @$_condition = $row[$array_colum["condition"]];
                @$_show_condition = $row[$array_colum["show_condition"]];
                @$online_only = $row[$array_colum["online_only"]];
                @$_out_of_stock = $row[$array_colum["out_of_stock"]];
                @$array_uploadable_files = $array_colum["uploadable_files"];
                @$array_text_fields = $array_colum["text_fields"];
                @$array_combination_group = $array_colum["combination"];
                @$array_combination_field = $array_colum["combination_field"];
                @$array_warehouse = $array_colum["warehouse"];
                @$array_shipping = $array_colum["shipping"];
                @$array_id_carriers = $row[$array_colum["id_carriers"]];
                @$array_price = $array_colum["price"];
                @$array_specific = $array_colum["specific"];
                @$array_virtual = $array_colum["virtual"];
                @$array_attachments = $array_colum["attachments"];
                @$deliverytimes = $array_colum["deliverytimes"];
                // getIdByIsoCode
                $_supplier_id_currency = Currency::getIdByIsoCode($_supplier_currency);
                if ($_supplier_id_currency == 0) {
                    $_supplier_id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                }
                if ($_advanced_stock_management != null) {
                    $_advanced_stock_management = $this->dataStatusNo($_advanced_stock_management);
                    if ($_advanced_stock_management == 0) {
                        $_depends_on_stock = 0;
                    }
                }
                if ($_out_of_stock !== null) {
                    $_out_of_stock = (int) $_out_of_stock;
                    if ($_out_of_stock < 0 || $_out_of_stock > 2) {
                        $_out_of_stock = 2;
                    }
                } else {
                    $_out_of_stock = 2;
                }

                // Xử lý dữ liệu trong tab shipping
                $data_array_shipping = array();
                foreach ($array_shipping as $key_shipping => $value_shipping) {
                    if (@$row[$value_shipping] != null) {
                        $data_array_shipping[$key_shipping] = round((float) ($row[$value_shipping]), 6);
                        if ($key_shipping == "additional_shipping_cost") {
                            $data_array_shipping[$key_shipping] = round((float) ($row[$value_shipping]), 2);
                        }
                    }
                }

                // Xử lý Visibility và Condition
                $_data_visibility = "both";
                if ($_visibility != null) {
                    $data_visibility = array(
                        "both" => $this->l = "everywhere",
                        "catalog" => $this->l = "catalog only",
                        "search" => $this->l = "search only",
                        "none" => $this->l = "nowhere",
                    );
                    $_visibility = Tools::strtolower($_visibility);
                    foreach ($data_visibility as $key_data_visibility => $value_data_visibility) {
                        if ($value_data_visibility == $_visibility) {
                            $_data_visibility = pSQL($key_data_visibility);
                        }
                    }
                }
                $_data_condition = "new";
                if ($_condition != null) {
                    $data_condition = array($this->l = "new", $this->l = "used", $this->l = "refurbished");
                    $_condition = Tools::strtolower($_condition);
                    foreach ($data_condition as $value_data_condition) {
                        if ($value_data_condition == $_condition) {
                            $_data_condition = pSQL($value_data_condition);
                        }
                    }
                }

                $uploadable_files = 0;
                $array_name_uploadable_files = array();
                if (!empty($array_uploadable_files)) {
                    foreach ($array_uploadable_files as $name_uploadable_files) {
                        if (@$row[$name_uploadable_files] != "") {
                            $array_name_uploadable_files[] = pSQL(@$row[$name_uploadable_files]);
                            $uploadable_files++;
                        }
                    }
                }

                $text_fields = 0;
                $array_name_text_fields = array();
                if (!empty($array_text_fields)) {
                    foreach ($array_text_fields as $name_text_fields) {
                        if (@$row[$name_text_fields] != "") {
                            $array_name_text_fields[] = pSQL(@$row[$name_text_fields]);
                            $text_fields++;
                        }
                    }
                }

                $__status = $this->dataStatus($_status);
                $_available_for_order = $this->dataStatus($_available_for_order);
                $_show_condition = $this->validateShowCondition($_show_condition);
                $redirect_type = $this->validateRedirection($redirect_type);
                $online_only = $this->validateOnlineOnly($online_only);
                $delete_product = $this->validateDeleteProduct($delete_product);
                // Language
                $id_lang = (int) $this->context->language->id;
                // Currency
                $currency = new Currency();
                $currency_default = $currency->getDefaultCurrency();
                $iso_code_defaut = $currency_default->iso_code;
                if ($_iso_code != null) {
                    if (Tools::strtolower($iso_code_defaut) !== Tools::strtolower($_iso_code)) {
                        $_currencyId = $currency->getIdByIsoCode($_iso_code, $this->shop_id);
                        $currencyTo = new Currency($_currencyId);
                        $_currencyIdDefault = $currency->getIdByIsoCode($iso_code_defaut, $this->shop_id);
                        $currencyFrom = new Currency($_currencyIdDefault);

                        $_wholesale_price = Tools::convertPriceFull($_wholesale_price, $currencyFrom, $currencyTo);

                        $_retail_price = Tools::convertPriceFull($_retail_price, $currencyFrom, $currencyTo);
                    }
                }

                // tab Prices
                $data_array_price = array();
                foreach ($array_price as $key_price => $value_price) {
                    if (isset($row[$value_price])) {
                        if ($key_price == "id_tax_rules_group") {
                            $data_array_price[$key_price] = (int) $row[$value_price];
                        }
                        if ($key_price == "unit_price_ratio") {
                            $unit_price = round($this->replacePrice($row[$value_price]), 6);
                            if ($unit_price != 0) {
                                $unit_price_ratio = $_retail_price / $unit_price;
                                $data_array_price[$key_price] = $unit_price_ratio;
                            }
                        }
                        if ($key_price == "unity") {
                            $data_array_price[$key_price] = pSQL($row[$value_price]);
                        }
                        if ($key_price == "on_sale") {
                            $data_array_price[$key_price] = $this->dataStatus($row[$value_price]);
                        }
                    }
                }
                // id_category_default
                $a = new Category();
                $id_category_default = $a->getRootCategory()->id;
                if (@$_category_default != null) {
                    $_category_default = explode(',', $_category_default);
                    $_category_default = $_category_default[0];
                    $db = Db::getInstance();
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_lang= ';
                    $sql.= (int) $id_lang . ' AND name ="' . pSQL($_category_default) . '"';
                    $category_default_select = $db->executeS($sql, true, false);
                    if (@$category_default_select != null) {
                        foreach ($category_default_select as $category_row) {
                            $id_category_default = $category_row['id_category'];
                        }
                    }
                }
                if (!isset($array_colum["main_category"])) {
                    $id_category_default = null;
                    unset($id_category_default);
                }
                if (@$_category_default_id != null) {
                    $db = Db::getInstance();
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_lang= ';
                    $sql.= (int) $id_lang . ' AND id_category ="' . (int) ($_category_default_id) . '"';
                    $category_default_select = $db->executeS($sql, true, false);
                    if (@$category_default_select != null) {
                        foreach ($category_default_select as $category_row) {
                            $id_category_default = $category_row['id_category'];
                        }
                    }
                }
                // id_supplier
                if (isset($array_colum["supplier"])) {
                    $id_supplier = array();
                    foreach (@$array_colum["supplier"] as $value_sup) {
                        $_supplier = $row[$value_sup];
                        $db = Db::getInstance();
                        $sql = 'SELECT * FROM '
                                . _DB_PREFIX_ . 'supplier WHERE name ="' . pSQL(trim($_supplier)) . '"';
                        $supplier_select = $db->executeS($sql, true, false);
                        if ($_post['sup_exist'] == '0') {
                            if (@$supplier_select == null) {
                                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'supplier(name,date_add,date_upd,active) ';
                                $sql .= 'VALUES(\''.pSQL(trim($_supplier)).'\',NOW(),';
                                $sql .= 'NOW(),\'1\')';
                                $db->query($sql);
                                
                                $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'supplier ORDER BY id_supplier DESC';
                                $ar_id_supplier = $db->getRow($sql, false);
                                $id_supplier1 = (int) $ar_id_supplier['id_supplier'];
                                
                                $languagesArr = Language::getLanguages(false);
                                foreach ($languagesArr as $key_lang => $value_lang) {
                                    $sql = 'REPLACE INTO ' . _DB_PREFIX_ . 'supplier_lang(id_supplier,id_lang) ';
                                    $sql .= 'VALUES('.$id_supplier1.','.(int) $languagesArr[$key_lang]['id_lang'].')';
                                    $db->query($sql);
                                }
                                
                                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'supplier_shop(id_supplier,id_shop) ';
                                $sql .= 'VALUES(\''.$id_supplier1.'\',\''.(int) $this->shop_id .'\')';
                                $db->query($sql);

                                $id_country = Context::getContext()->country->id;
                                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'address(id_country,id_state,id_customer,';
                                $sql .= 'id_manufacturer,id_supplier,id_warehouse,alias,lastname,firstname,address1,';
                                $sql .= 'city,date_add,date_upd,active) VALUES(\''.$id_country.'\',\'0\',\'0\',\'0\',';
                                $sql .= '\''.$id_supplier1.'\',\'0\',\''.pSQL(trim($_supplier)).'\',';
                                $sql .= '\'supplier\',\'supplier\',';
                                $sql .= '\'supplier address\',\'supplier city\',NOW(),';
                                $sql .= 'NOW(),\'1\')';
                                $db->query($sql);
                            }
                        }
                        $sql = 'SELECT * FROM '
                                . _DB_PREFIX_ . 'supplier WHERE active = 1 AND name ="' . pSQL(trim($_supplier)) . '"';
                        $supplier_select = $db->executeS($sql, true, false);
                        if (@$supplier_select != null) {
                            foreach ($supplier_select as $supplier_row) {
                                $id_supplier[] = $supplier_row['id_supplier'];
                            }
                        }
                    }
                }

                // id_manufacturer
                $manufacturer_select = array();
                $id_manufacturer = '';
                if (@$_manufacturer != null) {
                    $db = Db::getInstance();
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'manufacturer WHERE name ="';
                    $sql.= pSQL(trim($_manufacturer)) . '"';
                    $manufacturer_select = $db->executeS($sql, true, false);
                    if ($_post['manu_exist'] == '0') {
                        if (@$manufacturer_select == null) {
                            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'manufacturer(name,date_add,date_upd,active) ';
                            $sql .= 'VALUES(\''.pSQL(trim($_manufacturer)).'\',NOW(),';
                            $sql .= 'NOW(),\'1\')';
                            $db->query($sql);
                            
                            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'manufacturer ORDER BY id_manufacturer DESC';
                            $ar_id_manu = $db->getRow($sql, false);
                            $id_manu = (int) $ar_id_manu['id_manufacturer'];
                            
                            $languagesArr = Language::getLanguages(false);
                            foreach ($languagesArr as $key_lang => $value_lang) {
                                $sql = 'REPLACE INTO ' . _DB_PREFIX_ . 'manufacturer_lang(id_manufacturer,id_lang) ';
                                $sql .= 'VALUES('.$id_manu.','.(int) $languagesArr[$key_lang]['id_lang'].')';
                                $db->query($sql);
                            }
                            
                            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'manufacturer_shop(id_manufacturer,id_shop) ';
                            $sql .= 'VALUES(\''.$id_manu.'\',\''.(int) $this->shop_id .'\')';
                            $db->query($sql);
                        }
                    }
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'manufacturer WHERE active = 1 AND name ="';
                    $sql.= pSQL(trim($_manufacturer)) . '"';
                    $manufacturer_select = $db->executeS($sql, true, false);
                }

                if (@$manufacturer_select != null) {
                    foreach ($manufacturer_select as $manufacturer_row) {
                        $id_manufacturer = $manufacturer_row['id_manufacturer'];
                    }
                }

                if (@$_friendly_url == null) {
                    $_link_rewrite = null;
                } else {
                    $_link_rewrite = Tools::link_rewrite($_friendly_url);
                }
                // tags
                if ($_tags != null) {
                    $tag_array = explode(",", $_tags);
                }
                $tags_array = array();
                if ($_tags_array != null) {
                    foreach ($_tags_array as $key_tags_array => $value_tags_array) {
                        $value_tags_array;
                        $tags_array[$key_tags_array] = explode(",", $_tags_array[$key_tags_array]);
                    }
                }
                // Check exits
                $id_row = array();
                if ($_post["identify_existing_items"] == "Product Name") {
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_lang WHERE name = "';
                    $sql .= pSQL($_name) . '" AND id_lang = "' . (int) $id_lang . '" ';
                    $sql .= 'AND id_shop=' . (int) $this->shop_id;
                    if ($results = Db::getInstance()->ExecuteS($sql, true, false)) {
                        foreach ($results as $p_row) {
                            if ($_post["identify_existing_items"] == "Product Name") {
                                $id_row[] = $p_row['id_product'];
                            }
                        }
                    }
                }
                if ($_post["identify_existing_items"] == "Product Name") {
                    foreach ($arr_lang as $key_arr_lang => $value_arr_lang) {
                        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_lang WHERE name = "';
                        $sql .= pSQL(@$_name_array[$key_arr_lang]) . '" AND id_lang = "' . (int) $key_arr_lang . '" ';
                        $sql .= 'AND id_shop=' . (int) $this->shop_id;
                        if ($results = Db::getInstance()->ExecuteS($sql, true, false)) {
                            foreach ($results as $p_row) {
                                if ($_post["identify_existing_items"] == "Product Name") {
                                    $id_row[] = $p_row['id_product'];
                                }
                            }
                        }
                    }
                }
                if ($_post["identify_existing_items"] == "Reference code") {
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product p';
                    $sql .= ' INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product';
                    $sql .= ' WHERE p.reference = "' . pSQL(@$_reference) . '"';
                    $sql .= ' AND ps.id_shop = '.(int) $this->shop_id;
                    if ($results = Db::getInstance()->ExecuteS($sql, true, false)) {
                        foreach ($results as $p_row2) {
                            if ($_post["identify_existing_items"] == "Reference code") {
                                $id_row[] = $p_row2['id_product'];
                            }
                        }
                    }
                }
                if ($_post["identify_existing_items"] == "Product ID") {
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product p';
                    $sql .= ' INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product';
                    $sql .= ' WHERE p.id_product = "' . (int) $_product_id . '"';
                    $sql .= ' AND ps.id_shop = '.(int) $this->shop_id;
                    if ($results = Db::getInstance()->ExecuteS($sql, true, false)) {
                        foreach ($results as $p_row3) {
                            if ($_post["identify_existing_items"] == "Product ID") {
                                $id_row[] = $p_row3['id_product'];
                            }
                        }
                    }
                }
                if ($_post["identify_existing_items"] == "EAN-13 or JAN barcode") {
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product p';
                    $sql .= ' INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product';
                    $sql .= ' WHERE p.ean13 = "' . pSQL(@$_ean13) . '"';
                    $sql .= ' AND ps.id_shop = '.(int) $this->shop_id;
                    if ($results = Db::getInstance()->ExecuteS($sql, true, false)) {
                        foreach ($results as $p_row4) {
                            if ($_post["identify_existing_items"] == "EAN-13 or JAN barcode") {
                                $id_row[] = $p_row4['id_product'];
                            }
                        }
                    }
                }
                if ($_post["identify_existing_items"] == "UPC barcode") {
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product p';
                    $sql .= ' INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product';
                    $sql .= ' WHERE p.upc = "' . pSQL($_upc) . '"';
                    $sql .= ' AND ps.id_shop = '.(int) $this->shop_id;
                    if ($results = Db::getInstance()->ExecuteS($sql, true, false)) {
                        foreach ($results as $p_row5) {
                            if ($_post["identify_existing_items"] == "UPC barcode") {
                                $id_row[] = $p_row5['id_product'];
                            }
                        }
                    }
                }
                if ($_post["identify_existing_items"] == "Supplier reference") {
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_supplier WHERE '
                        .'product_supplier_reference = "' . pSQL($_supplier_reference) . '"';
                    if ($results = Db::getInstance()->ExecuteS($sql, true, false)) {
                        foreach ($results as $p_row5) {
                            if ($_post["identify_existing_items"] == "Supplier reference") {
                                $id_row[] = $p_row5['id_product'];
                            }
                        }
                    }
                }
                // since 1.1.2
                if ($_post["identify_existing_items"] == "Combination Reference") {
                    $c_ref_column = $array_combination_field['ba_combination_reference'];
                    $c_ref = $row[$c_ref_column];
                    if ($c_ref_column !== null && !empty($c_ref)) {
                        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute';
                        $sql .= ' WHERE reference = "' . pSQL(@$c_ref) . '"';
                        $results = Db::getInstance()->ExecuteS($sql, true, false);
                        if (!empty($results)) {
                            foreach ($results as $p_row2) {
                                $id_row[] = $p_row2['id_product'];
                            }
                        }
                    }
                }
                $array_attribute = array();
                if (!empty($array_combination_group)) {
                    $array_attribute = $this->checkAttribute($array_combination_group, $row);
                }

                if (!empty($id_row)) {
                    if ($_post["existing_items"] == "Update") {
                        $id_row = array_unique($id_row);
                        foreach ($id_row as $row_id) {
                            $id_product = (int) $row_id;
                            $arr_log_import = 'Product ID - '.$id_product . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            // since 1.1.0
                            $this->insertProductsInFile($this->get_id_config, @$this->shop_id, $id_product);

                            /** since 1.0.74 allow delete a product **/
                            if ($delete_product === 1) {
                                $pd = new Product($id_product, false, null, @(int) $this->shop_id);
                                $pd->delete();
                                $arr_log_import = 'Product ID - '.$id_product . " is deleted \n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $title = sprintf($this->l('Product ID #%s is deleted'), $id_product);
                                $href = "index.php?controller=AdminProducts&id_product=" . $id_product
                                    . "&updateproduct&token=" . $tokenProducts;
                                $this->array_result[] = "<li><a href='" . $href . "' target='_blank'>"
                                                        . $title . "</a></li>";
                                continue;
                            }
                            // Tinh toan lai gia truoc thue
                            $id_tax_rules_group_tmp = '';
                            if (($price_intax !== null) && ((float) $_retail_price <= 0)) {
                                $_retail_price = $price_intax;
                                // If a tax is already included in price, withdraw it from price
                                $_p = new Product($id_product);
                                $id_tax_rules_group_tmp = $_p->id_tax_rules_group;
                                if (isset($data_array_price['id_tax_rules_group'])) {
                                    $id_tax_rules_group_tmp = (int) $data_array_price['id_tax_rules_group'];
                                }
                                $_retail_price = $this->calcPricebeforeTax($_retail_price, $id_tax_rules_group_tmp);
                            }
                            //  updateAccessories
                            $this->updateAccessoriesbyRef($id_product, @$accessories_ref);
                            $this->updateAccessories($id_product, @$accessories_ids);

                            $where = "id_product = " . (int) $row_id;
                            $increase_quantity = 0;
                            if ($_post["quantity"] == "increase_quantity") {
                                $increase_quantity = 1;
                                $sql = "SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product = "
                                        .$id_product.' AND id_product_attribute=0';
                                $quantity_old = (int) $db->getValue($sql, false);
                                $_quantities = (int) ($_quantities + $quantity_old);
                            }
                            if ($_post["update_categories"] == "new_categories") {
                                $db->delete('category_product', $where);
                            }
                            $remove_image = 0;
                            $data = array();
                            $arr_log_import = 'Update product...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            if ($_quantities !== null) {
                                $data['quantity'] = (int) @$_quantities;
                            }
                            if ($_out_of_stock !== null) {
                                $data['out_of_stock'] = (int) @$_out_of_stock;
                            }
                            $data_c = $data;
                            $data_c['id_product'] = $id_product;
                            $data_c['id_product_attribute'] = 0;
                            $data_c['id_shop'] = @(int) $this->shop_id;
                            $is = $data_c['id_shop'];
                            $io = (int) StockAvailable::getStockAvailableIdByProductId($id_product, 0, $is);
                            if (!$io) {
                                $shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$is));
                                // if quantities are shared between shops of the group
                                if ($shop_group->share_stock) {
                                    $data_c['id_shop'] = 0;
                                    $data_c['id_shop_group'] = (int)$shop_group->id;
                                } else {
                                    $data_c['id_shop_group'] = 0;
                                }
                                $db->insert('stock_available', $data_c);
                            } else {
                                unset($data_c['id_shop']);
                                unset($data_c['id_shop_group']);
                                $where_s = 'id_stock_available = '.$io;
                                $db->update('stock_available', $data_c, $where_s);
                            }

                            $data = array(
                                'date_upd' => date('Y-m-d H:i:s')
                            );
                            if (@$id_tax_rules_group_tmp != null) {
                                $data['id_tax_rules_group'] = @(int) $id_tax_rules_group_tmp;
                            }
                            
                            if (@$id_manufacturer != null) {
                                $data['id_manufacturer'] = @(int) $id_manufacturer;
                            }

                            if (@$id_supplier != null) {
                                $data['id_supplier'] = @(int) end($id_supplier);
                            }

                            if (@$_supplier_reference != null) {
                                $data['supplier_reference'] = @pSQL($_supplier_reference);
                            }
                            if (@$_ean13 != null) {
                                $data['ean13'] = @pSQL($_ean13);
                            }

                            if (@$_upc !== null) {
                                $data['upc'] = @pSQL($_upc);
                            }
                            if (@$_isbn !== null) {
                                $data['isbn'] = @pSQL($_isbn);
                            }

                            if (@$_retail_price !== null) {
                                $data['price'] = @(float) ($_retail_price);
                            }

                            if (@$_wholesale_price !== null) {
                                $data['wholesale_price'] = @(float) ($_wholesale_price);
                            }

                            if (@$_reference != null) {
                                $data['reference'] = pSQL(@$_reference);
                            }
                            $data['active'] = 1;
                            if (@$_status != null) {
                                $data['active'] = $__status;
                            }
                            if (@$_available_for_order !== null) {
                                $data['available_for_order'] = (int) $_available_for_order;
                            }
                            if (@$_out_of_stock !== null) {
                                $data['out_of_stock'] = (int) $_out_of_stock;
                            }
                            if ($_quantities !== null) {
                                $data['quantity'] = (int) @$_quantities;
                            }
                            if (@$ecotax !== null) {
                                $data['ecotax'] = @(float) ($ecotax);
                            }
                            if (isset($id_category_default)) {
                                $data['id_category_default'] = @(int) $id_category_default;
                            }
                            $data['id_shop_default'] = @(int) $this->shop_id;
                            $data['uploadable_files'] = @(int) $uploadable_files;
                            $data['text_fields'] = @(int) $text_fields;
                            $data['customizable'] = 0;
                            if ($data['uploadable_files']>0 || $data['text_fields']>0) {
                                $data['customizable'] = 1;
                            }
                            $data['visibility'] = pSQL($_data_visibility);
                            $data['condition'] = pSQL($_data_condition);
                            if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                                if ($_show_condition !== null) {
                                    $data['show_condition'] = (int) $_show_condition;
                                }
                                if ($online_only !== null) {
                                    $data['online_only'] = (int) $online_only;
                                }
                                if ($redirect_type !== null) {
                                    $data['redirect_type'] = pSQL($redirect_type);
                                }
                                if ($id_type_redirected !== null) {
                                    $id_redirected_name = $this->validateIDRedirectedColumn();
                                    $data[$id_redirected_name] = (int) $id_type_redirected;
                                }
                            }
                            if (!empty($data_array_shipping)) {
                                foreach ($data_array_shipping as $key_shipping => $value_shipping) {
                                    $data[$key_shipping] = pSQL($value_shipping);
                                }
                            }
                            if (isset($product_minimal_quantity)) {
                                $data['minimal_quantity'] = @(int) $product_minimal_quantity;
                            }
                            if (isset($product_available_date)) {
                                $data['available_date'] = pSQL($product_available_date);
                            }
                            if (!empty($data_array_price)) {
                                foreach ($data_array_price as $key_price => $value_price) {
                                    $data[$key_price] = pSQL($value_price);
                                }
                            }
                            if ($_post["product_type"] == 'product_virtual') {
                                $data['is_virtual'] = 1;
                            } else {
                                $data['is_virtual'] = 0;
                            }

                            $db->update('product', $data, $where);
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            // ADD virtual
                            if ($_post["product_type"] == 'product_virtual') {
                                $array_virtual_tmp = array();
                                $virtual_product_filename = '';
                                foreach ($array_virtual as $key_virtual => $value_virtual) {
                                    $array_virtual_tmp[$key_virtual] = $row[$value_virtual];
                                }
                                
                                if (!empty($array_virtual_tmp['display_filename'])) {
                                    $display_filename = $array_virtual_tmp['display_filename'];
                                    $virtual_product_filename = $this->moveFileDownload($display_filename);
                                }
                                
                                $id_product_download = (int)ProductDownload::getIdFromIdProduct($id_product, false);
                                if ($id_product_download == 0) {
                                    $download = new ProductDownload((int)$id_product_download);
                                    $download->id_product = $id_product;
                                    $download->display_filename = $array_virtual_tmp['display_filename'];
                                    $download->filename = $virtual_product_filename;
                                    if (!isset($array_virtual_tmp['date_add'])) {
                                        $download->date_add = date('Y-m-d H:i:s');
                                    } else {
                                        $download->date_add = $array_virtual_tmp['date_add'];
                                    }
                                    $download->date_expiration = $array_virtual_tmp['date_expiration'];
                                    $download->nb_days_accessible = (int) $array_virtual_tmp['nb_days_accessible'];
                                    $download->nb_downloadable = (int) $array_virtual_tmp['nb_downloadable'];
                                    if (!isset($array_virtual_tmp['active'])) {
                                        $download->active = 1;
                                    } else {
                                        $download->active = (int) $this->dataStatus($array_virtual_tmp['active']);
                                    }
                                    $download->is_shareable = 0;
                                    $download->save();
                                } else {
                                    $array_virtual_tmp['id_product_download'] = $id_product_download;
                                    $array_virtual_tmp['id_product'] = $id_product;
                                    $array_virtual_tmp['filename'] = $virtual_product_filename;
                                    $db->insert('product_download', $array_virtual_tmp, false, true, Db::REPLACE);
                                }
                            }
                            // ADD attachments
                            if (!empty($array_attachments)) {
                                $this->addBaAttachments($array_attachments, $id_product, $row, $_post);
                            }
                            
                            // ADD ID carriers
                            if (!empty($array_id_carriers)) {
                                $array_id_carriers = explode(',', $array_id_carriers);
                                foreach ($array_id_carriers as $key_array_id_carriers => $value_array_id_carriers) {
                                    $key_array_id_carriers;
                                    $check_exist_carrier = 'SELECT * FROM ' . _DB_PREFIX_ . 'carrier WHERE '
                                        .'id_carrier = "'.(int)$value_array_id_carriers.'" '
                                        .'AND active = "1" AND deleted = "0"';
                                    $data_carrier = $db->ExecuteS($check_exist_carrier, true, false);
                                    if (!empty($data_carrier)) {
                                        $data_insert = array(
                                            'id_product' => @$id_product,
                                            'id_carrier_reference' => @$data_carrier[0]['id_reference'],
                                            'id_shop' => @$this->shop_id
                                        );
                                        $db->insert('product_carrier', $data_insert, false, true, Db::REPLACE);
                                    }
                                }
                            }
                            
                            // ADD Tags
                            if (!empty($tag_array)) {
                                $arr_log_import = 'Update tag...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                foreach ($tag_array as $value) {
                                     $id_tag=$this->updateTag(@$value, @$id_lang);
                                    if (strpos(_PS_VERSION_, '1.6.0') === 0 || strpos(_PS_VERSION_, '1.5') === 0) {
                                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "product_tag VALUES('"
                                                . (int) $id_product . "', '" . (int) $id_tag . "')";
                                        $db->query($sql);
                                    } else {
                                        // tu prestashop 1.6.1+ them truong id_lang trong bang image_shop
                                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "product_tag VALUES('"
                                                . (int) $id_product . "', '" . (int) $id_tag . "', '"
                                                . (int) $id_lang . "')";
                                        $db->query($sql);
                                    }
                                }
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                            
                            if (!empty($tags_array)) {
                                $arr_log_import = 'Update tags...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                if ($_post["multi_lang"] == "0") {
                                    foreach ($arr_lang as $key_arr_lang => $value_arr_lang) {
                                        foreach ($tags_array as $key1 => $value1) {
                                            $value1;
                                            if ($key_arr_lang === $key1) {
                                                foreach ($tags_array[$key1] as $key2 => $value2) {
                                                    $key2;
                                                    $id_tag=$this->updateTag(@$value2, @$key_arr_lang);

                                                    $aa = strpos(_PS_VERSION_, '1.6.0');
                                                    $bb = strpos(_PS_VERSION_, '1.5');
                                                    if ($aa === 0 || $bb === 0) {
                                                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "product_tag VALUES('"
                                                                . (int) $id_product . "', '" . (int) $id_tag . "')";
                                                        $db->query($sql);
                                                    } else {
                                                        // tu prestashop1.6.1+ them truong id_lang trong bang image_shop
                                                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "product_tag VALUES('"
                                                                . (int) $id_product . "', '" . (int) $id_tag . "', '"
                                                                . (int) $key_arr_lang . "')";
                                                        $db->query($sql);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }

                            $data = array();
                            $arr_log_import = 'Update product_shop...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            $data = array(
                                'id_shop' => (int) $this->shop_id,
                                'date_upd' => date('Y-m-d H:i:s')
                            );
                            if (isset($id_category_default)) {
                                $data['id_category_default'] = @(int) $id_category_default;
                            }
                            if ($_retail_price !== null) {
                                $data['price'] = @(float) ($_retail_price);
                            }
                            if ($_advanced_stock_management != null) {
                                $data['advanced_stock_management'] = (int) @$_advanced_stock_management;
                            }
                            if ($_wholesale_price != null) {
                                $data['wholesale_price'] = @(float) ($_wholesale_price);
                            }
                            if ($_status != null) {
                                $data['active'] = $__status;
                            }
                            $data['uploadable_files'] = @(int) $uploadable_files;
                            $data['text_fields'] = @(int) $text_fields;
                            $data['customizable'] = 0;
                            if ($data['uploadable_files']>0 || $data['text_fields']>0) {
                                $data['customizable'] = 1;
                            }
                            $data['visibility'] = $_data_visibility;
                            $data['condition'] = $_data_condition;
                            if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                                if ($_show_condition !== null) {
                                    $data['show_condition'] = (int) $_show_condition;
                                }
                                if ($online_only !== null) {
                                    $data['online_only'] = (int) $online_only;
                                }
                                if ($redirect_type !== null) {
                                    $data['redirect_type'] = pSQL($redirect_type);
                                }
                                if ($id_type_redirected !== null) {
                                    $id_redirected_name = $this->validateIDRedirectedColumn();
                                    $data[$id_redirected_name] = (int) $id_type_redirected;
                                }
                            }
                            if (isset($product_minimal_quantity)) {
                                $data['minimal_quantity'] = @(int) $product_minimal_quantity;
                            }
                            if (isset($product_available_date)) {
                                $data['available_date'] = pSQL($product_available_date);
                            }
                            if (!empty($data_array_price)) {
                                foreach ($data_array_price as $key_price => $value_price) {
                                    $data[$key_price] = pSQL($value_price);
                                }
                            }
                            if (@$_available_for_order !== null) {
                                $data['available_for_order'] = (int) $_available_for_order;
                            }
                            if (@$ecotax !== null) {
                                $data['ecotax'] = @(float) ($ecotax);
                            }
                            if (isset($data_array_shipping['additional_shipping_cost'])) {
                                $as = (float) $data_array_shipping['additional_shipping_cost'];
                                $data['additional_shipping_cost'] = $as;
                            }
                            $where2 = " AND id_shop=" . $this->shop_id;
                            $where3 = $where . $where2;
                            
                            $exist_pro_shop = "SELECT * FROM " . _DB_PREFIX_ . "product_shop WHERE " . pSQL($where3);
                            $check_exist_product_shop = $db->ExecuteS($exist_pro_shop, true, false);
                            if (!empty($check_exist_product_shop)) {
                                $db->update('product_shop', $data, $where3);
                            } else {
                                $data['id_product'] = (int) $id_product;
                                $data['date_add'] = date('Y-m-d H:i:s');
                                $db->insert('product_shop', $data);
                            }

                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);

                            $data = array();
                            $arr_log_import = 'Update product_lang...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            if ($_description != null) {
                                $data['description'] = @$_description;
                            }
                            if ($_short_description != null) {
                                $data['description_short'] = @$_short_description;
                            }
                            if ($_link_rewrite != null) {
                                $data['link_rewrite'] = pSQL(@$_link_rewrite);
                            }

                            if ($_link_rewrite != null) {
                                $data['meta_keywords'] = pSQL($_meta_keywords);
                            }

                            if ($_meta_title != null) {
                                $data['meta_title'] = pSQL($_meta_title);
                            }
                            if ($_meta_description != null) {
                                $data['meta_description'] = pSQL($_meta_description);
                            }
                            if ($_name != null) {
                                $data['name'] = pSQL($_name);
                            }
                            if ($_available_now != null) {
                                $data['available_now'] = pSQL($_available_now);
                            }
                            if ($_available_later != null) {
                                $data['available_later'] = pSQL($_available_later);
                            }
                            if (!empty($arr_id_lang)) {
                                foreach ($arr_id_lang as $value_id_lang) {
                                    if ($_post["multi_lang"] == "0") {
                                        if (isset($_name_array[$value_id_lang]) == true) {
                                            $data['name'] = pSQL($_name_array[$value_id_lang]);
                                        }
                                        if (isset($_description_array[$value_id_lang]) == true) {
                                            $data['description'] = pSQL($_description_array[$value_id_lang], true);
                                        }
                                        if (isset($_short_description_array[$value_id_lang]) == true) {
                                            $de_sh = pSQL($_short_description_array[$value_id_lang], true);
                                            $data['description_short'] = $de_sh;
                                        }
                                        if (isset($_meta_title_array[$value_id_lang]) == true) {
                                            $data['meta_title'] = pSQL($_meta_title_array[$value_id_lang]);
                                        }
                                        if (isset($_meta_description_array[$value_id_lang]) == true) {
                                            $data['meta_description'] = pSQL($_meta_description_array[$value_id_lang]);
                                        }
                                        if (isset($_friendly_url_array[$value_id_lang]) == true) {
                                            $data['link_rewrite'] = pSQL($_friendly_url_array[$value_id_lang]);
                                        }
                                        if (isset($_friendly_url_array[$value_id_lang]) == false &&
                                            isset($_name_array[$value_id_lang]) == true) {
                                            $data['link_rewrite'] = Tools::link_rewrite($data['name'], null);
                                        }
                                        if (isset($_anow_array[$value_id_lang]) == true) {
                                            $data['available_now'] = pSQL($_anow_array[$value_id_lang]);
                                        }
                                        if (isset($_alater_array[$value_id_lang]) == true) {
                                            $data['available_later'] = pSQL($_alater_array[$value_id_lang]);
                                        }
                                    }
                                    $where_lang = $where." AND id_lang =". (int) $value_id_lang
                                                ." AND id_shop = ". (int) $this->shop_id;
                                    $exist_pro_lang = "SELECT * FROM " . _DB_PREFIX_ . "product_lang"
                                                ." WHERE " . pSQL($where_lang);
                                    $check_exist_product_lang = $db->ExecuteS($exist_pro_lang, true, false);
                                    if (!empty($check_exist_product_lang)) {
                                        $db->update('product_lang', $data, $where_lang);
                                    } else {
                                        $data['id_product'] = (int) $id_product;
                                        $data['id_lang'] = (int) $value_id_lang;
                                        $data['id_shop'] = (int) $this->shop_id;
                                        $db->insert('product_lang', $data);
                                    }
                                }
                            }
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            // Delivery Time
                            $arr_log_import = 'Update Delivery Time...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            $this->importDeliveryTime($id_product, $this->shop_id, @$deliverytimes, @$row, $_post);
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);

                            $data = array();
                            if (!empty($feature_column)) {
                                if ($_post["multi_lang"] == "0") {
                                    $arr_log_import = 'Update Features Lang...';
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                    $fe = $_post['fea_exist'];
                                    $this->addFeaturesLang($row_id, $id_lang, $feature_column, @$row, $fe);
                                    $arr_log_import = 'finished' . "\n";
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                }
                                if ($_post["multi_lang"] == "1") {
                                    $arr_log_import = 'Update Features...';
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                    foreach ($feature_column as $key_fea => $row2) {
                                        $fe = $_post['fea_exist'];
                                        $this->addFeatures($row_id, $id_lang, $key_fea, @$row[$row2], $fe);
                                    }
                                    $arr_log_import = 'finished' . "\n";
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                }
                            }
                            if (isset($id_category_default)) {
                                $arr_log_import = 'Update ID category default Product...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $db = Db::getInstance();

                                $sql = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'category_product WHERE id_category = "';
                                $sql.= (int) $id_category_default . '"';
                                $total_catid = Db::getInstance()->getValue($sql, false);

                                $data = array(
                                    'id_category' => $id_category_default,
                                    'position' => $total_catid+1,
                                    "id_product" => $row_id
                                );
                                // xoa category_product neu co
                                $db->delete('category_product', "id_category = '$id_category_default' AND " . $where);

                                $db->insert('category_product', $data);
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                            // category_associated
                            if (isset($array_colum["category_associated"]) || isset($array_colum["main_category"])) {
                                $arr_log_import = 'Update Category associated...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $characters_category = $_post['characters_category'];
                                if (isset($array_colum["main_category"])) {
                                    $array_colum["category_associated"][] = @$array_colum["main_category"];
                                }
                                $array_colum["category_associated"] = array_unique($array_colum["category_associated"]);
                            
                                if (@$array_colum["category_associated"]['0'] != null) {
                                    $cate = new Category();
                                    foreach (@$array_colum["category_associated"] as $value) {
                                        @$_category_associated = array();
                                        @$_array_category_associated = array();
                                        @$v_c_a = $row[$value];
                                        $value_category_associated = str_replace($characters_category, '#', $v_c_a);
                                        if (isset($array_colum["main_category"])) {
                                            if ($value != $array_colum["main_category"]) {
                                                $_category_associated = explode(',', $value_category_associated);
                                            }
                                        } else {
                                            $_category_associated[] = $value_category_associated;
                                        }
                                        foreach ($_category_associated as $item_1) {
                                            if (!empty($item_1)) {
                                                $il = $id_lang;
                                                $ish = $this->shop_id;
                                                if ($_post['cate_exist'] == '0') {
                                                    $c_f = $this->searchByPath($il, $ish, $item_1, $this, 'createCat');
                                                } else {
                                                    $c_f = $this->searchByPath($il, $ish, $item_1);
                                                }
                                            } else {
                                                $c_f['id_category'] = null;
                                            }
                                            $_array_category_associated[] = $c_f['id_category'];
                                        }
                                        
                                        foreach ($_array_category_associated as $item_2) {
                                            $item_2=trim($item_2);
                                            if (@$item_2 != null) {
                                                $db = Db::getInstance();

                                                $_category_associated_select = $item_2;
                                                if (!empty($_category_associated_select)) {
                                                    $_c_a_s = (int) @$_category_associated_select;
                                                    $sql = 'SELECT COUNT(*) FROM '
                                                            . _DB_PREFIX_ . 'category_product WHERE id_category = "'
                                                            . @$_c_a_s . '"';
                                                    $total_catid = Db::getInstance()->getValue($sql, false);
                                                    $data = array(
                                                        'id_category' => @$_c_a_s,
                                                        'position' => $total_catid+1,
                                                        "id_product" => $row_id
                                                    );
                                                    // xoa category_product neu co
                                                    $sql = "id_category = '"
                                                            . @$_c_a_s
                                                            . "' AND " . $where;
                                                    Db::getInstance()->delete('category_product', $sql);
                                                    $db->insert('category_product', $data);
                                                }
                                            }
                                        }
                                       
                                        if (isset($array_colum["main_category"])) {
                                            $value_row = $row[$array_colum["main_category"]];
                                            $vca = str_replace($characters_category, '#', $value_row);
                                            if (!empty($vca)) {
                                                $il = $id_lang;
                                                $ish = $this->shop_id;
                                                if ($_post['cate_exist'] == '0') {
                                                    $main_c = $this->searchByPath($il, $ish, $vca, $this, 'createCat');
                                                } else {
                                                    $main_c = $this->searchByPath($il, $ish, $vca);
                                                }
                                            } else {
                                                $main_c['id_category'] = null;
                                            }
                                            $id_category_default = $main_c['id_category'];
                                            if (empty($id_category_default)) {
                                                $id_category_default = $cate->getRootCategory()->id_category;
                                            }
                                            $db = Db::getInstance();
                                            $sql = 'UPDATE ' . _DB_PREFIX_ . 'product SET id_category_default '
                                                . '= \'' . (int) @$id_category_default . '\' WHERE '
                                                . 'id_product='. (int) $row_id;
                                            $db->query($sql);
                                            $sql = 'UPDATE ' . _DB_PREFIX_ . 'product_shop SET id_category_default '
                                                . '= \'' . (int) @$id_category_default . '\' WHERE '
                                                . 'id_shop=\''. (int) $this->shop_id.'\' AND '
                                                . 'id_product='. (int) $row_id;
                                            $db->query($sql);
                                            
                                            if (!empty($id_category_default)) {
                                                $_c_a_s = (int) @$id_category_default;
                                                $sql = 'SELECT COUNT(*) FROM '
                                                        . _DB_PREFIX_ . 'category_product WHERE id_category = "'
                                                        . @$_c_a_s . '"';
                                                $total_catid = Db::getInstance()->getValue($sql, false);
                                                $data = array(
                                                    'id_category' => @$_c_a_s,
                                                    'position' => $total_catid+1,
                                                    "id_product" => $row_id
                                                );
                                                // xoa category_product neu co
                                                $sql = "id_category = '"
                                                        . @$_c_a_s
                                                        . "' AND " . $where;
                                                Db::getInstance()->delete('category_product', $sql);
                                                $db->insert('category_product', $data);
                                            }
                                        }
                                    }
                                    $cate->regenerateEntireNtree();
                                }
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                            // category_associated_id
                            if (isset($array_colum["category_associated_id"])) {
                                $arr_log_import = 'Update Category associated ID...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                foreach (@$array_colum["category_associated_id"] as $value) {
                                    @$_category_associated = $row[$value];
                                    $_category_associated = explode(',', $_category_associated);
                                    foreach ($_category_associated as $item_2) {
                                        $item_2=trim($item_2);
                                        if (@$item_2 != null) {
                                            $db = Db::getInstance();
                                            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_lang= ';
                                            $sql.= (int) $id_lang . ' AND id_category ="'
                                                . (int) ($item_2) . '"';

                                            $_category_associated_select = $db->executeS($sql, true, false);
                                            $sql = 'SELECT COUNT(*) FROM '
                                                    . _DB_PREFIX_ . 'category_product WHERE id_category = "'
                                                    . (int) @$_category_associated_select[0]['id_category'] . '"';

                                            $total_catid = Db::getInstance()->getValue($sql, false);

                                            $an_id_cate = @$_category_associated_select[0]['id_category'];
                                            $data = array(
                                                'id_category' => $an_id_cate,
                                                'position' => $total_catid+1,
                                                "id_product" => $row_id
                                            );
                                            // xoa category_product neu co
                                            $sql = "id_category = '"
                                                    . (int) @$_category_associated_select[0]['id_category']
                                                    . "' AND " . $where;

                                            Db::getInstance()->delete('category_product', $sql);
                                            $db->insert('category_product', $data);
                                        }
                                    }
                                }
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                            // Delete Existing Images
                            if ($_delete_existing_images != null) {
                                $_status_delete_existing_images = $this->dataStatus($_delete_existing_images);
                                if ($_status_delete_existing_images == 1) {
                                    $this->removeImages($id_product);
                                }
                            }

                            // Add Main Image
                            if ($_main_img != null) {
                                $arr_log_import = 'Update Main Image...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                @$_m_img2 = explode(',', $_main_img);
                                $this->addImages($_m_img2[0], $row_id, $array_colum["main_img"], 1, 0, $remove_image);
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }

                            // Add Addtion Image
                            if (isset($array_colum["add_img"])) {
                                $arr_log_import = 'Update Addtion Image...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                foreach ($array_colum["add_img"] as $addimg) {
                                    @$_add_img = $row[$addimg];
                                    @$_add_img = explode(',', $_add_img);
                                    foreach (@$_add_img as $item) {
                                        $this->addImages($item, $row_id, $array_colum["add_img"], 0, 0, $remove_image);
                                    }
                                }
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                            // combination
                            $id_attr=null;
                            $id_attr_speciy_price=null;
                            if ($_post["identify_existing_items_combi"] == 'Attributes') {
                                if (!empty($array_attribute)) {
                                    $arr_log_import = 'Update combination...';
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                    $ac = $array_combination_field;
                                    $at = $array_attribute;
                                    $q = $_combination_quantity;
                                    $ot = $_out_of_stock;
                                    $dp = $_depends_on_stock;
                                    $id_p = $id_product;
                                    $ic = $increase_quantity;
                                    $re = $remove_image;
                                    $bie = $_post;
                                    $id_a = $this->setCombination($bie, $ac, $row, $id_p, $at, $q, $ot, $dp, $ic, $re);
                                    $id_attr_speciy_price = $id_a;
                                    $id_attr = $id_a;
                                    $arr_log_import = 'finished' . "\n";
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                } else {
                                    $combi_data = array();
                                    if ($_post["quantity"] == "increase_quantity") {
                                        $sql = "SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product = "
                                                .$id_product.' AND id_product_attribute=0';
                                        $quantity_old = (int) $db->getValue($sql, false);
                                        $_combination_quantity = (int) ($_combination_quantity + $quantity_old);
                                    }
                                    if ($_combi_quantity_ori !== null && $_combi_quantity_ori !== '') {
                                        $combi_data['quantity'] = (int) @$_combination_quantity;
                                        
                                        $combi_where = 'id_product = '. (int) $id_product;
                                        // changed since 1.0.70+
                                        $i = $this->shop_id;
                                        StockAvailable::setQuantity($id_product, 0, $combi_data['quantity'], $i);
                                        $db->update('product', $combi_data, $combi_where);
                                    }
                                }
                                // warehouse
                                if (!empty($array_warehouse)) {
                                    $arr_log_import = 'Update Stock Warehouse...';
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                    $i_a = (int) @$id_attr;
                                    $data_post = $arr_data_post;
                                    $a_w = $array_warehouse;
                                    $increase = $increase_quantity;
                                    $this->addStockWarehouse($a_w, $row, $id_product, $i_a, $data_post, $increase);
                                }
                                // Supplier
                                if (@$id_supplier != null) {
                                    $arr_log_import = 'Update Supplier...';
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                    $ref = $_supplier_reference;
                                    $price_te = $_supplier_price_te;
                                    $id_cur = $_supplier_id_currency;
                                    $i_a = (int) @$id_attr;
                                    $this->addSupplier($id_supplier, $id_product, $i_a, $ref, $price_te, $id_cur);
                                    $arr_log_import = 'finished' . "\n";
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                }
                            } else {
                                $arr_log_import = 'Update combination BIE...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $ac = $array_combination_field;
                                $q = $_combination_quantity;
                                $out = $_out_of_stock;
                                $dp = $_depends_on_stock;
                                $id_p = $id_product;
                                $in_q = $increase_quantity;
                                $re = $remove_image;
                                $bie = $_post;
                                $id_a = $this->setCombinationBIE($bie, $ac, $row, $id_p, $q, $out, $dp, $in_q, $re);
                                $id_attr_speciy_price = is_array($id_a) ? $id_a[0]:$id_a;
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $id_attr = $id_a;
                                if (!empty($id_attr)) {
                                    $arr_log_import = 'Update Stock Warehouse, Supplier...';
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                    foreach ($id_attr as $key_id_attr => $value_id_attr) {
                                        $key_id_attr;
                                        // warehouse
                                        if (!empty($array_warehouse)) {
                                            $id_at = (int) @$value_id_attr;
                                            $data_post = $arr_data_post;
                                            $aw = $array_warehouse;
                                            $ic = $increase_quantity;
                                            $this->addStockWarehouse($aw, $row, $id_product, $id_at, $data_post, $ic);
                                        }
                                        // Supplier
                                        if (@$id_supplier != null) {
                                            $ref = $_supplier_reference;
                                            $pr_te = $_supplier_price_te;
                                            $icu = $_supplier_id_currency;
                                            $id_at = (int) @$value_id_attr;
                                            $this->addSupplier($id_supplier, $id_product, $id_at, $ref, $pr_te, $icu);
                                        }
                                    }
                                    $arr_log_import = 'finished' . "\n";
                                    $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                } else {
                                    // Supplier
                                    if (@$id_supplier != null) {
                                        $ref = $_supplier_reference;
                                        $pr_te = $_supplier_price_te;
                                        $icu = $_supplier_id_currency;
                                        $this->addSupplier($id_supplier, $id_product, 0, $ref, $pr_te, $icu);
                                    }
                                    
                                    $combi_data = array();
                                    if ($_post["quantity"] == "increase_quantity") {
                                        $sql = "SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product = "
                                                .$id_product.' AND id_product_attribute=0';
                                        $quantity_old = (int) $db->getValue($sql, false);
                                        $_combination_quantity = (int) ($_combination_quantity + $quantity_old);
                                    }
                                    if ($_combi_quantity_ori !== null && $_combi_quantity_ori !== '') {
                                        $combi_data['quantity'] = (int) @$_combination_quantity;
                                        
                                        $combi_where = 'id_product = '. (int) $id_product;
                                        // changed since 1.0.70+
                                        $i = $this->shop_id;
                                        StockAvailable::setQuantity($id_product, 0, $combi_data['quantity'], $i);
                                        $db->update('product', $combi_data, $combi_where);
                                    }
                                }
                            }
                            
                            // 1.7 default combi
                            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute '
                                .'WHERE id_product = "'. $id_product .'"';
                            $get_array_attribute = $db->ExecuteS($sql, true, false);
                            if (!empty($get_array_attribute)) {
                                $cache_default_attribute_not_exist = 0;
                                foreach ($get_array_attribute as $key_get_array_attribute => $value) {
                                    $value;
                                    @$cda = $get_array_attribute[$key_get_array_attribute]['cache_default_attribute'];
                                    if (!empty($cda)) {
                                        $cache_default_attribute_not_exist = 0;
                                        break;
                                    } else {
                                        $cache_default_attribute_not_exist = 1;
                                    }
                                }
                                if ($cache_default_attribute_not_exist == 1) {
                                    $ipa = $get_array_attribute['0']['id_product_attribute'];
                                    $data = array('cache_default_attribute' => $ipa);
                                    $db->update('product', $data, 'id_product='.$id_product);
                                    $db->update('product_shop', $data, 'id_product='.$id_product);
                                }

                                $defaut_on_not_exist = 0;
                                foreach ($get_array_attribute as $key_get_array_attribute => $value) {
                                    $value;
                                    if ($get_array_attribute[$key_get_array_attribute]['default_on'] == 1) {
                                        $defaut_on_not_exist = 0;
                                        break;
                                    } else {
                                        $defaut_on_not_exist = 1;
                                    }
                                }
                                if ($defaut_on_not_exist == 1) {
                                    $data = array('default_on' => 1);
                                    $update_where = 'id_product='.$id_product.' AND id_product_attribute=';
                                    $update_where .= $get_array_attribute['0']['id_product_attribute'];
                                    $db->update('product_attribute', $data, $update_where);
                                    $db->update('product_attribute_shop', $data, $update_where);
                                }
                                Product::updateDefaultAttribute($id_product);
                            }
                            
                             // ADD Specific Prices
                            if (!empty($array_specific)) {
                                $arr_log_import = 'Update Specific Prices...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $i_a_s_p = $id_attr_speciy_price;
                                $this->addSpecificPrices($array_specific, $row, $id_product, $i_a_s_p, $this->shop_id);
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                            //
                            $title = "Row " . $key . " is imported";
                            if ($_name != null) {
                                $title = $_name;
                            }
                            $href = "index.php?controller=AdminProducts&id_product=" . @$id_product
                                    . "&updateproduct&token=" . $tokenProducts;
                            $this->array_result[] = "<li><a href='" . $href . "' target='_blank'>"
                                                    . $title . "</a></li>";
                        }
                    }
                } else {
                    if ($_post["new_items"] == "Add") {
                        if ($_name == null && $_post["multi_lang"] == "1") {
                            $this->array_result[] = "<li class='error_import'>Rows #"
                                    . $key . " can be not imported because Product Name field which is empty.</li>";
                            continue;
                        }
                        if (@$_friendly_url == null) {
                            $_link_rewrite = Tools::link_rewrite(pSQL($_name), null);
                        } else {
                            $_link_rewrite = Tools::link_rewrite($_friendly_url);
                        }
                        // Tinh toan lai gia truoc thue
                        $id_tax_rules_group_tmp = '';
                        if (($price_intax !== null) && ((float) $_retail_price <= 0)) {
                            $_retail_price = $price_intax;
                            // If a tax is already included in price, withdraw it from price
                            $id_tax_rules_group_tmp = null;
                            if (isset($data_array_price['id_tax_rules_group'])) {
                                $id_tax_rules_group_tmp = (int) $data_array_price['id_tax_rules_group'];
                            }
                            $_retail_price = $this->calcPricebeforeTax($_retail_price, $id_tax_rules_group_tmp);
                        }

                        if (!isset($id_category_default)) {
                            $a = new Category();
                            $id_category_default = $a->getRootCategory()->id;
                        }
                        
                        if ($_wholesale_price === null) {
                            $_wholesale_price = 0;
                        }

                        if ($_retail_price === null) {
                            $_retail_price = 0;
                        }
                        if (@$_upc !== null) {
                            $_upc = @pSQL($_upc);
                        }
                        if (@$_isbn !== null) {
                            $_isbn = @pSQL($_isbn);
                        }
                        $arr_log_import = 'Insert product...';
                        $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        $data = array(
                            'id_tax_rules_group' => @(int) $id_tax_rules_group_tmp,
                            'id_manufacturer' => @(int) $id_manufacturer,
                            'id_supplier' => @(int) end($id_supplier),
                            'supplier_reference' => @pSQL($_supplier_reference),
                            'id_category_default' => @(int) $id_category_default,
                            'id_shop_default' => @(int) $this->shop_id,
                            'ean13' => @pSQL($_ean13),
                            'upc' => $_upc,
                            'isbn' => $_isbn,
                            'price' => @(float) ($_retail_price),
                            'wholesale_price' => @(float) ($_wholesale_price),
                            'reference' => pSQL(@$_reference),
                            'available_for_order' => $_available_for_order,
                            'out_of_stock' => (int) $_out_of_stock,
                            'redirect_type' => 404,
                            'available_for_order' => 1,
                            'active' => $__status,
                            'date_add' => date('Y-m-d H:i:s'),
                            'date_upd' => date('Y-m-d H:i:s')
                        );

                        if ($_advanced_stock_management != null) {
                            $data['advanced_stock_management'] = (int) @$_advanced_stock_management;
                        }
                        if ($_quantities !== null) {
                            $data['quantity'] = (int) @$_quantities;
                        }
                        if (@$ecotax !== null) {
                            $data['ecotax'] = @(float) ($ecotax);
                        }

                        $data['uploadable_files'] = @(int) $uploadable_files;
                        $data['text_fields'] = @(int) $text_fields;
                        $data['customizable'] = 0;
                        if ($data['uploadable_files']>0 || $data['text_fields']>0) {
                            $data['customizable'] = 1;
                        }
                        $data['visibility'] = $_data_visibility;
                        $data['condition'] = $_data_condition;
                        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                            if ($_show_condition !== null) {
                                $data['show_condition'] = (int) $_show_condition;
                            }
                            if ($online_only !== null) {
                                $data['online_only'] = (int) $online_only;
                            }
                            if ($redirect_type !== null) {
                                $data['redirect_type'] = pSQL($redirect_type);
                            }
                            if ($id_type_redirected !== null) {
                                $id_redirected_name = $this->validateIDRedirectedColumn();
                                $data[$id_redirected_name] = (int) $id_type_redirected;
                            }
                        }
                        if (!empty($data_array_shipping)) {
                            foreach ($data_array_shipping as $key_shipping => $value_shipping) {
                                $data[$key_shipping] = $value_shipping;
                            }
                        }
                        if (isset($product_minimal_quantity)) {
                            $data['minimal_quantity'] = @(int) $product_minimal_quantity;
                        }
                        if (isset($product_available_date)) {
                            $data['available_date'] = pSQL($product_available_date);
                        }

                        if (!empty($data_array_price)) {
                            foreach ($data_array_price as $key_price => $value_price) {
                                $data[$key_price] = $value_price;
                            }
                        }
                        if ($_product_id != null) {
                            $_product_id = (int) $_product_id;
                            if ($_product_id > 0) {
                                $sql = "SELECT * FROM " . _DB_PREFIX_ . "product WHERE id_product = " . $_product_id;
                                $checkIdExits = $db->ExecuteS($sql, true, false);
                                if (empty($checkIdExits)) {
                                    $data["id_product"] = $_product_id;
                                }
                            }
                        }
                        if ($_post["product_type"] == 'product_virtual') {
                            $data['is_virtual'] = 1;
                        } else {
                            $data['is_virtual'] = 0;
                        }
                        
                        $db->insert('product', $data);
                        $arr_log_import = 'finished' . "\n";
                        $this->logImport($arr_log_import, 'auto', $this->get_id_config);

                        $id_product = (int) $db->Insert_ID();
                        $arr_log_import = 'Product ID - '.$id_product . "\n";
                        $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                    
                        if ($id_product == 0) {
                            $this->array_result[] = "<li class='error_import'>Rows #"
                                    . $key . " Can not be imported</li>";
                            continue;
                        }
                        // since 1.1.0
                        $this->insertProductsInFile($this->get_id_config, @$this->shop_id, $id_product);

                        //  updateAccessories
                        $this->updateAccessoriesbyRef($id_product, @$accessories_ref);
                        $this->updateAccessories($id_product, @$accessories_ids);

                        // ADD virtual
                        if ($_post["product_type"] == 'product_virtual') {
                            $array_virtual_tmp = array();
                            $virtual_product_filename = '';
                            foreach ($array_virtual as $key_virtual => $value_virtual) {
                                $array_virtual_tmp[$key_virtual] = $row[$value_virtual];
                            }
                            
                            if (!empty($array_virtual_tmp['display_filename'])) {
                                $display_filename = $array_virtual_tmp['display_filename'];
                                $virtual_product_filename = $this->moveFileDownload($display_filename);
                            }
                            
                            $id_product_download = (int)ProductDownload::getIdFromIdProduct($id_product, false);
                            if ($id_product_download == 0) {
                                $download = new ProductDownload((int)$id_product_download);
                                $download->id_product = $id_product;
                                $download->display_filename = $array_virtual_tmp['display_filename'];
                                $download->filename = $virtual_product_filename;
                                if (!isset($array_virtual_tmp['date_add'])) {
                                    $download->date_add = date('Y-m-d H:i:s');
                                } else {
                                    $download->date_add = $array_virtual_tmp['date_add'];
                                }
                                $download->date_expiration = $array_virtual_tmp['date_expiration'];
                                $download->nb_days_accessible = (int) $array_virtual_tmp['nb_days_accessible'];
                                $download->nb_downloadable = (int) $array_virtual_tmp['nb_downloadable'];
                                if (!isset($array_virtual_tmp['active'])) {
                                    $download->active = 1;
                                } else {
                                    $download->active = (int) $this->dataStatus($array_virtual_tmp['active']);
                                }
                                $download->is_shareable = 0;
                                $download->save();
                            } else {
                                $array_virtual_tmp['id_product_download'] = $id_product_download;
                                $array_virtual_tmp['id_product'] = $id_product;
                                $array_virtual_tmp['filename'] = $virtual_product_filename;
                                $db->insert('product_download', $array_virtual_tmp, false, true, Db::REPLACE);
                            }
                        }
                        // ADD attachments
                        if (!empty($array_attachments)) {
                            $this->addBaAttachments($array_attachments, $id_product, $row, $_post);
                        }
                        // ADD ID carriers
                        if (!empty($array_id_carriers)) {
                            $array_id_carriers = explode(',', $array_id_carriers);
                            foreach ($array_id_carriers as $key_array_id_carriers => $value_array_id_carriers) {
                                $check_exist_carrier = 'SELECT * FROM ' . _DB_PREFIX_ . 'carrier WHERE '
                                    .'id_carrier = "'.(int)$value_array_id_carriers.'" '
                                    .'AND active = "1" AND deleted = "0"';
                                $data_carrier = $db->ExecuteS($check_exist_carrier, true, false);
                                if (!empty($data_carrier)) {
                                    $data_insert = array(
                                        'id_product' => @$id_product,
                                        'id_carrier_reference' => @$data_carrier[0]['id_reference'],
                                        'id_shop' => @$this->shop_id
                                    );
                                    $db->insert('product_carrier', $data_insert, false, true, Db::REPLACE);
                                }
                            }
                        }
                        // ADD Tags
                        if (!empty($tag_array)) {
                            $arr_log_import = 'Insert tag...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            foreach ($tag_array as $value) {
                                $id_tag=$this->updateTag(@$value, @$id_lang);
                                /////////////
                                if (strpos(_PS_VERSION_, '1.6.0') === 0 || strpos(_PS_VERSION_, '1.5') === 0) {
                                    $data = array(
                                        'id_product' => @$id_product,
                                        'id_tag' => @$id_tag
                                    );

                                    $db->insert('product_tag', $data);
                                } else {
                                    // tu prestashop 1.6.1+ them truong id_lang trong bang image_shop
                                    $data = array(
                                        'id_lang' => $id_lang,
                                        'id_product' => @$id_product,
                                        'id_tag' => @$id_tag
                                    );

                                    $db->insert('product_tag', $data);
                                }
                            }
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        }
                        
                        if (!empty($tags_array)) {
                            $arr_log_import = 'Insert tags...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            if ($_post["multi_lang"] == "0") {
                                foreach ($arr_lang as $key_arr_lang => $value_arr_lang) {
                                    foreach ($tags_array as $key1 => $value1) {
                                        if ($key_arr_lang === $key1) {
                                            foreach ($tags_array[$key1] as $key2 => $value2) {
                                                $id_tag=$this->updateTag(@$value2, @$key_arr_lang);

                                                $aa =strpos(_PS_VERSION_, '1.6.0');
                                                $bb = strpos(_PS_VERSION_, '1.5');
                                                if ($aa === 0 || $bb === 0) {
                                                    $data = array(
                                                        'id_product' => @$id_product,
                                                        'id_tag' => @$id_tag
                                                    );

                                                    $db->insert('product_tag', $data);
                                                } else {
                                                    // tu prestashop 1.6.1+ them truong id_lang trong bang image_shop
                                                    $data = array(
                                                        'id_lang' => $key_arr_lang,
                                                        'id_product' => @$id_product,
                                                        'id_tag' => @$id_tag
                                                    );

                                                    $db->insert('product_tag', $data);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        }

                        $arr_log_import = 'Insert product_shop...';
                        $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        $data = array(
                            'id_product' => @(int) $id_product,
                            'id_shop' => $this->shop_id,
                            'id_category_default' => @(int) $id_category_default,
                            'price' => @(float) ($_retail_price),
                            'wholesale_price' => @(float) ($_wholesale_price),
                            'available_for_order' => $_available_for_order,
                            'active' => $__status,
                            'date_add' => date('Y-m-d H:i:s'),
                            'date_upd' => date('Y-m-d H:i:s'),
                        );

                        if ($_advanced_stock_management != null) {
                            $data['advanced_stock_management'] = (int) @$_advanced_stock_management;
                        }

                        $data['uploadable_files'] = @(int) $uploadable_files;
                        $data['text_fields'] = @(int) $text_fields;
                        $data['customizable'] = 0;
                        if ($data['uploadable_files']>0 || $data['text_fields']>0) {
                            $data['customizable'] = 1;
                        }
                        $data['visibility'] = $_data_visibility;
                        $data['condition'] = $_data_condition;
                        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                            if ($_show_condition !== null) {
                                $data['show_condition'] = (int) $_show_condition;
                            }
                            if ($online_only !== null) {
                                $data['online_only'] = (int) $online_only;
                            }
                            if ($redirect_type !== null) {
                                $data['redirect_type'] = pSQL($redirect_type);
                            }
                            if ($id_type_redirected !== null) {
                                $id_redirected_name = $this->validateIDRedirectedColumn();
                                $data[$id_redirected_name] = (int) $id_type_redirected;
                            }
                        }
                        if (isset($product_minimal_quantity)) {
                            $data['minimal_quantity'] = @(int) $product_minimal_quantity;
                        }
                        if (isset($product_available_date)) {
                            $data['available_date'] = pSQL($product_available_date);
                        }
                        if (@$ecotax !== null) {
                            $data['ecotax'] = @(float) ($ecotax);
                        }
                        if (isset($data_array_shipping['additional_shipping_cost'])) {
                            $as = (float) $data_array_shipping['additional_shipping_cost'];
                            $data['additional_shipping_cost'] = $as;
                        }
                        if (!empty($data_array_price)) {
                            foreach ($data_array_price as $key_price => $value_price) {
                                $data[$key_price] = $value_price;
                            }
                        }
                        $db->insert('product_shop', $data);
                        $arr_log_import = 'finished' . "\n";
                        $this->logImport($arr_log_import, 'auto', $this->get_id_config);

                        $arr_log_import = 'Insert Stock Available...';
                        $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        $data = array(
                            'quantity' => @(int) ($_quantities),
                            'id_product' => @(int) ($id_product),
                            'id_product_attribute' => 0,
                            'id_shop' => $this->shop_id,
                            'id_shop_group' => 0,
                            'out_of_stock' => (int) $_out_of_stock,
                        );
                        if ($_depends_on_stock !== null) {
                            $data['depends_on_stock'] = (int) ($_depends_on_stock);
                        }
                        $is = $data['id_shop'];
                        $io = (int) StockAvailable::getStockAvailableIdByProductId($id_product, 0, $is);
                        if (!$io) {
                            $shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$is));
                            // if quantities are shared between shops of the group
                            if ($shop_group->share_stock) {
                                $data['id_shop'] = 0;
                                $data['id_shop_group'] = (int)$shop_group->id;
                            } else {
                                $data['id_shop_group'] = 0;
                            }
                            $db->insert('stock_available', $data);
                        } else {
                            unset($data['id_shop']);
                            unset($data['id_shop_group']);
                            $where_s = 'id_stock_available = '.$io;
                            $db->update('stock_available', $data, $where_s);
                        }

                        $arr_log_import = 'finished' . "\n";
                        $this->logImport($arr_log_import, 'auto', $this->get_id_config);

                        $data = array(
                            'id_product' => $id_product,
                            'id_shop' => $this->shop_id,
                            'description' => $_description,
                            'description_short' => $_short_description,
                            'link_rewrite' => pSQL($_link_rewrite),
                            'meta_description' => pSQL($_meta_description),
                            'meta_keywords' => pSQL($_meta_keywords),
                            'meta_title' => pSQL($_meta_title),
                            'name' => pSQL($_name),
                            'available_now' => pSQL($_available_now),
                            'available_later' => pSQL($_available_later),
                        );
                        if (!empty($arr_id_lang)) {
                            $arr_log_import = 'Insert product_lang...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            foreach ($arr_id_lang as $value_id_lang) {
                                $data["id_lang"] = $value_id_lang;
                                if ($_post["multi_lang"] == "0") {
                                    if (isset($_name_array[$value_id_lang]) == true) {
                                        $data['name'] = pSQL($_name_array[$value_id_lang]);
                                    }
                                    if (isset($_description_array[$value_id_lang]) == true) {
                                        $data['description'] = pSQL($_description_array[$value_id_lang], true);
                                    }
                                    if (isset($_short_description_array[$value_id_lang]) == true) {
                                        $_s_d_a = $_short_description_array[$value_id_lang];
                                        $data['description_short'] = pSQL($_s_d_a, true);
                                    }
                                    if (isset($_meta_title_array[$value_id_lang]) == true) {
                                        $data['meta_title'] = pSQL($_meta_title_array[$value_id_lang]);
                                    }
                                    if (isset($_meta_description_array[$value_id_lang]) == true) {
                                        $data['meta_description'] = pSQL($_meta_description_array[$value_id_lang]);
                                    }
                                    if (isset($_friendly_url_array[$value_id_lang]) == true) {
                                        $data['link_rewrite'] = pSQL($_friendly_url_array[$value_id_lang]);
                                    }
                                    if (isset($_friendly_url_array[$value_id_lang]) == false &&
                                        isset($_name_array[$value_id_lang]) == true) {
                                        $data['link_rewrite'] = Tools::link_rewrite($data['name'], null);
                                    }
                                    if (isset($_anow_array[$value_id_lang]) == true) {
                                        $data['available_now'] = pSQL($_anow_array[$value_id_lang]);
                                    }
                                    if (isset($_alater_array[$value_id_lang]) == true) {
                                        $data['available_later'] = pSQL($_alater_array[$value_id_lang]);
                                    }
                                }
                                $db->insert('product_lang', $data, false, true, DB::REPLACE);
                            }
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        }
                        
                        // Delivery Time
                        $arr_log_import = 'Update Delivery Time...';
                        $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        $this->importDeliveryTime($id_product, $this->shop_id, @$deliverytimes, @$row, $_post);
                        $arr_log_import = 'finished' . "\n";
                        $this->logImport($arr_log_import, 'auto', $this->get_id_config);

                        if (!empty($feature_column)) {
                            if ($_post["multi_lang"] == "0") {
                                $arr_log_import = 'Insert Features Lang...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $fe = $_post['fea_exist'];
                                $this->addFeaturesLang($id_product, $id_lang, $feature_column, @$row, $fe);
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                            if ($_post["multi_lang"] == "1") {
                                $arr_log_import = 'Insert Features...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                foreach ($feature_column as $key => $row2) {
                                    $this->addFeatures($id_product, $id_lang, $key, @$row[$row2], $_post['fea_exist']);
                                }
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                        }

                        // main_category
                        if (isset($id_category_default)) {
                            $arr_log_import = 'Insert Main category...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            $id_category_default = (int) $id_category_default;
                            $db = Db::getInstance();

                            $sql = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'category_product WHERE id_category = "';

                            $sql.= (int) $id_category_default . '"';

                            $total_catid = Db::getInstance()->getValue($sql, false);

                            $data = array(
                                'id_category' => $id_category_default,
                                'id_product' => $id_product,
                                'position' => $total_catid+1
                            );

                            // xoa category_product neu co
                            $sql = "id_category = '$id_category_default' AND id_product='$id_product'";
                            Db::getInstance()->delete('category_product', $sql);
                            $db->insert('category_product', $data);
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        }

                        // category_associated
                        if (isset($array_colum["category_associated"]) || isset($array_colum["main_category"])) {
                            $arr_log_import = 'Insert Category associated...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            $characters_category = $_post['characters_category'];
                            if (isset($array_colum["main_category"])) {
                                $array_colum["category_associated"][] = @$array_colum["main_category"];
                            }
                            $array_colum["category_associated"] = array_unique($array_colum["category_associated"]);
                            if (@$array_colum["category_associated"]['0'] != null) {
                                $cate = new Category();
                                foreach (@$array_colum["category_associated"] as $value) {
                                    @$_category_associated = array();
                                    @$_array_category_associated = array();
                                    @$value_c_a = $row[$value];
                                    $value_category_associated = str_replace($characters_category, '#', $value_c_a);
                                    if (isset($array_colum["main_category"])) {
                                        if ($value != $array_colum["main_category"]) {
                                            $_category_associated = explode(',', $value_category_associated);
                                        }
                                    } else {
                                        $_category_associated[] = $value_category_associated;
                                    }
                                    foreach ($_category_associated as $item_1) {
                                        if (!empty($item_1)) {
                                            $il = $id_lang;
                                            $ish = $this->shop_id;
                                            if ($_post['cate_exist'] == '0') {
                                                $c_found = $this->searchByPath($il, $ish, $item_1, $this, 'createCat');
                                            } else {
                                                $c_found = $this->searchByPath($il, $ish, $item_1);
                                            }
                                        } else {
                                            $c_found['id_category'] = null;
                                        }
                                        $_array_category_associated[] = $c_found['id_category'];
                                    }
                                    
                                    foreach ($_array_category_associated as $item_2) {
                                        $item_2=trim($item_2);
                                        if (@$item_2 != null) {
                                            $db = Db::getInstance();
                                            
                                            $_category_associated_select = $item_2;
                                            if (!empty($_category_associated_select)) {
                                                $_c_a_s = (int) @$_category_associated_select;
                                                $sql = 'SELECT COUNT(*) FROM '
                                                        . _DB_PREFIX_ . 'category_product WHERE id_category = "'
                                                        . @$_c_a_s . '"';
                                                $total_catid = Db::getInstance()->getValue($sql, false);
                                                $data = array(
                                                    'id_category' => @$_c_a_s,
                                                    'position' => $total_catid + 1,
                                                    'id_product' => $id_product
                                                );
                                                // xoa category_product neu co
                                                $sql = "id_category = '"
                                                        . $_c_a_s
                                                        . "' AND id_product='$id_product'";
                                                Db::getInstance()->delete('category_product', $sql);
                                                $db->insert('category_product', $data);
                                            }
                                        }
                                    }
                                    
                                    if (isset($array_colum["main_category"])) {
                                        $value_row = $row[$array_colum["main_category"]];
                                        $value_c_a = str_replace($characters_category, '#', $value_row);
                                        if (!empty($value_c_a)) {
                                            $il = $id_lang;
                                            $ih = $this->shop_id;
                                            if ($_post['cate_exist'] == '0') {
                                                $main_c = $this->searchByPath($il, $ih, $value_c_a, $this, 'createCat');
                                            } else {
                                                $main_c = $this->searchByPath($il, $ih, $value_c_a);
                                            }
                                        } else {
                                            $main_c['id_category'] = null;
                                        }
                                        $id_category_default = $main_c['id_category'];
                                        if (empty($id_category_default)) {
                                            $id_category_default = $cate->getRootCategory()->id_category;
                                        }
                                        $db = Db::getInstance();
                                        $sql = 'UPDATE ' . _DB_PREFIX_ . 'product SET id_category_default '
                                            . '= \'' . (int) @$id_category_default . '\' WHERE '
                                            . 'id_product='. (int) $id_product;
                                        $db->query($sql);
                                        $sql = 'UPDATE ' . _DB_PREFIX_ . 'product_shop SET id_category_default '
                                            . '= \'' . (int) @$id_category_default . '\' WHERE '
                                            . 'id_shop=\''. (int) $this->shop_id.'\' AND '
                                            . 'id_product='. (int) $id_product;
                                        $db->query($sql);
                                        
                                        if (!empty($id_category_default)) {
                                            $_c_a_s = (int) @$id_category_default;
                                            $sql = 'SELECT COUNT(*) FROM '
                                                    . _DB_PREFIX_ . 'category_product WHERE id_category = "'
                                                    . @$_c_a_s . '"';
                                            $total_catid = Db::getInstance()->getValue($sql, false);
                                            $data = array(
                                                'id_category' => @$_c_a_s,
                                                'position' => $total_catid + 1,
                                                'id_product' => $id_product
                                            );
                                            // xoa category_product neu co
                                            $sql = "id_category = '"
                                                    . $_c_a_s
                                                    . "' AND id_product='$id_product'";
                                            Db::getInstance()->delete('category_product', $sql);
                                            $db->insert('category_product', $data);
                                        }
                                    }
                                }
                                $cate->regenerateEntireNtree();
                            }
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        }
                        // category_associated_id
                        if (isset($array_colum["category_associated_id"])) {
                            $arr_log_import = 'Insert Category associated ID...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            foreach (@$array_colum["category_associated_id"] as $value) {
                                @$_category_associated = $row[$value];
                                $_category_associated = explode(',', $_category_associated);
                                foreach ($_category_associated as $item_2) {
                                    $item_2=trim($item_2);
                                    if (@$item_2 != null) {
                                        $db = Db::getInstance();

                                        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_lang= ';

                                        $sql.= (int) $id_lang . ' AND id_category ="' . (int) ($item_2) . '"';

                                        $_category_associated_select = $db->executeS($sql, true, false);
                                        if (!empty($_category_associated_select)) {
                                            $sql = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_
                                                    . 'category_product WHERE id_category = "'
                                                    . (int) $_category_associated_select[0]['id_category'] . '"';

                                            $total_catid = (int) Db::getInstance()->getValue($sql, false);

                                            $data = array(
                                                'id_category' => (int) $_category_associated_select[0]['id_category'],
                                                'id_product' => $id_product,
                                                'position' => $total_catid + 1
                                            );

                                            // xoa category_product neu co
                                            $sql = "id_category = '"
                                                    . (int) $_category_associated_select[0]['id_category']
                                                    . "' AND id_product='$id_product'";

                                            Db::getInstance()->delete('category_product', $sql);

                                            $db->insert('category_product', $data);
                                        }
                                    }
                                }
                            }
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        }
                        // Add Main Image
                        if ($_main_img != null) {
                            $arr_log_import = 'Insert Main Image...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            @$_main_img2 = explode(',', $_main_img);
                            $this->addImages($_main_img2[0], $id_product, $array_colum["main_img"], 1);
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        }

                        // Add Addtion Image
                        if (isset($array_colum["add_img"])) {
                            $arr_log_import = 'Insert Addtion Image...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            foreach ($array_colum["add_img"] as $addimg) {
                                @$_add_img = $row[$addimg];
                                @$_add_img = explode(',', $_add_img);
                                foreach (@$_add_img as $item) {
                                    if (empty($item)) {
                                        continue;
                                    }
                                    $this->addImages($item, $id_product, $array_colum["add_img"], 0);
                                }
                            }
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        }
                        // combination
                        $id_attr=null;
                        $id_attr_speciy_price=null;
                        if ($_post["identify_existing_items_combi"] == 'Attributes') {
                            if (!empty($array_attribute)) {
                                $arr_log_import = 'Insert combination...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $a_c = $array_combination_field;
                                $at = $array_attribute;
                                $q = $_combination_quantity;
                                $out = $_out_of_stock;
                                $dp = $_depends_on_stock;
                                $bie = $_post;
                                $id_attr = $this->setCombination($bie, $a_c, $row, $id_product, $at, $q, $out, $dp);
                                $id_attr_speciy_price = $id_attr;
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            } else {
                                $combi_data = array();
                                if ($_post["quantity"] == "increase_quantity") {
                                    $sql = "SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product = "
                                            .$id_product.' AND id_product_attribute=0';
                                    $quantity_old = (int) $db->getValue($sql, false);
                                    $_combination_quantity = (int) ($_combination_quantity + $quantity_old);
                                }
                                if ($_combi_quantity_ori !== null && $_combi_quantity_ori !== '') {
                                    $combi_data['quantity'] = (int) @$_combination_quantity;

                                    $combi_where = 'id_product = '. (int) $id_product;
                                    // changed since 1.0.70+
                                    $i = $this->shop_id;
                                    StockAvailable::setQuantity($id_product, 0, $combi_data['quantity'], $i);
                                    $db->update('product', $combi_data, $combi_where);
                                }
                            }
                            // warehouse
                            if (!empty($array_warehouse)) {
                                $arr_log_import = 'Insert Stock Warehouse...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $id_attr = (int) $id_attr;
                                $this->addStockWarehouse($array_warehouse, $row, $id_product, $id_attr, $arr_data_post);
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                            // Supplier
                            if (@$id_supplier != null) {
                                $arr_log_import = 'Insert Supplier...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                $reference = $_supplier_reference;
                                $price_te = $_supplier_price_te;
                                $id_cur = $_supplier_id_currency;
                                $id_attr = (int) @$id_attr;
                                $this->addSupplier($id_supplier, $id_product, $id_attr, $reference, $price_te, $id_cur);
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            }
                        } else {
                            $arr_log_import = 'Insert combination...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            $arr_com = $array_combination_field;
                            $quan = $_combination_quantity;
                            $out = $_out_of_stock;
                            $depen = $_depends_on_stock;
                            $bie = $_post;
                            $id_attr = $this->setCombinationBIE($bie, $arr_com, $row, $id_product, $quan, $out, $depen);
                            $id_attr_speciy_price = is_array($id_attr) ? $id_attr[0]:$id_attr;
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            if (!empty($id_attr)) {
                                $arr_log_import = 'Insert Stock Warehouse, Supplier...';
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                                foreach ($id_attr as $key_id_attr => $value_id_attr) {
                                    // warehouse
                                    if (!empty($array_warehouse)) {
                                        $id_at = (int) @$value_id_attr;
                                        $ar_wa = $array_warehouse;
                                        $this->addStockWarehouse($ar_wa, $row, $id_product, $id_at, $arr_data_post);
                                    }
                                    // Supplier
                                    if (@$id_supplier != null) {
                                        $ref = $_supplier_reference;
                                        $pr_te = $_supplier_price_te;
                                        $id_cur = $_supplier_id_currency;
                                        $id_at = (int) @$value_id_attr;
                                        $this->addSupplier($id_supplier, $id_product, $id_at, $ref, $pr_te, $id_cur);
                                    }
                                }
                                $arr_log_import = 'finished' . "\n";
                                $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            } else {
                                // Supplier
                                if (@$id_supplier != null) {
                                    $ref = $_supplier_reference;
                                    $pr_te = $_supplier_price_te;
                                    $id_cur = $_supplier_id_currency;
                                    $this->addSupplier($id_supplier, $id_product, 0, $ref, $pr_te, $id_cur);
                                }
                                $combi_data = array();
                                if ($_post["quantity"] == "increase_quantity") {
                                    $sql = "SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product = "
                                            .$id_product.' AND id_product_attribute=0';
                                    $quantity_old = (int) $db->getValue($sql, false);
                                    $_combination_quantity = (int) ($_combination_quantity + $quantity_old);
                                }
                                if ($_combi_quantity_ori !== null && $_combi_quantity_ori !== '') {
                                    $combi_data['quantity'] = (int) @$_combination_quantity;

                                    $combi_where = 'id_product = '. (int) $id_product;
                                    // changed since 1.0.70+
                                    $i = $this->shop_id;
                                    StockAvailable::setQuantity($id_product, 0, $combi_data['quantity'], $i);
                                    $db->update('product', $combi_data, $combi_where);
                                }
                            }
                        }
                        
                        // ADD Specific Prices
                        if (!empty($array_specific)) {
                            $arr_log_import = 'Update Specific Prices...';
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                            $i_a_s_p = $id_attr_speciy_price;
                            $this->addSpecificPrices($array_specific, $row, $id_product, $i_a_s_p, $this->shop_id);
                            $arr_log_import = 'finished' . "\n";
                            $this->logImport($arr_log_import, 'auto', $this->get_id_config);
                        }
                        //
                        $title = "Row " . $key . " is imported";
                        if ($_name != null) {
                            $title = $_name;
                        }
                        $href = "index.php?controller=AdminProducts&id_product=" . @$id_product
                                . "&updateproduct&token=" . $arr_data_post['tokenProducts'];
                        $this->array_result[] = "<li><a href='" . $href . "' target='_blank'>" . $title . "</a></li>";
                    } else {
                        $this->array_result[] = '';
                    }
                }
                if ($_post["new_items"] == "Add" || $_post["existing_items"] == "Update") {
                    // uploadable_files
                    if (!empty($array_name_uploadable_files)) {
                        foreach ($array_name_uploadable_files as $name) {
                            $this->customizationField($id_product, 0, $id_lang, $name);
                        }
                    }
                    // text_fields
                    if (!empty($array_name_text_fields)) {
                        foreach ($array_name_text_fields as $name) {
                            $this->customizationField($id_product, 1, $id_lang, $name);
                        }
                    }
                } else {
                    if ($_post["new_items"] == "Ignore") {
                        $this->array_result[] = "<li class='error_import'>Rows #" . $key
                            . " can be not imported because you choose Ignore for New Items in step 1</li>";
                    }
                    if ($_post["existing_items"] == "Ignore") {
                        $this->array_result[] = "<li class='error_import'>Rows #" . $key
                            . " can be not imported because you choose Ignore for Exist Items in step 1</li>";
                    }
                }
            }
        }
        $status_import = 0;
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'ba_cronjobs_importer SET '
                . 'products_imported="'. $product_end .'/'.count($array).'"'
                . ' , status_imported = "2" WHERE id_cronjob = ' . (int) $id_cronjob;
        $db->query($sql);
        if ($end == $result) {
            $status_import = 1;
            $db = Db::getInstance();
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'ba_cronjobs_importer SET '
                    . 'imported=1 WHERE id_cronjob = ' . (int) $id_cronjob;
            $db->query($sql);
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'ba_cronjobs_importer SET '
                    . 'status_imported = "3" WHERE id_cronjob = ' . (int) $id_cronjob;
            $db->query($sql);
            $product_end = '';
            Configuration::updateGlobalValue('baautoimpor_is_run', 0);
            $this->repeatImporter($product_end);
            // xoa excel file
            @unlink(_PS_MODULE_DIR_ . "ba_importer/stories/" . $arr_data_post['file_name']);
            // since 1.1.0
            $this->processProductsNotInFile($this->get_id_config, $_post["productsnotinfile"]);
        }
        $array_multi["status"] = $status_import;
        $array_multi["number_imported"] = $result - $start + 1;
        $array_multi["array_result"] = $this->array_result;
        $array_multi["product_end"] = $product_end;
        sort($array_multi["array_result"]);
        
        // display result
        $html = '<ol>';
        foreach ($array_multi["array_result"] as $r_value) {
            $html.=$r_value;
        }
        $html.='Products imported:'. $array_multi["number_imported"];
        $html .= '</ol>';
        echo $html;
        //
        if ($status_import == 0) {
            $this->repeatImporter($product_end);
        }
    }
    
    public function repeatImporter($product_end)
    {
        if (ob_get_level()>0) {
            ob_end_flush();
        }
        //return true;
        $badir = $this->context->shop->getBaseUrl();
        $id_importer_config = Configuration::get('baautoimpor_id_queue');
        $params = 'batoken='.$this->cookiekeymodule().'&product_end=' . $product_end;
        if ($id_importer_config != false) {
            $params .= '&id_importer_config='.$id_importer_config;
        }
        $badir2 = 'modules/ba_importer/autoimport.php?'.$params;
        $url = $badir . $badir2;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0");
        curl_exec($ch);
        if (curl_errno($ch)) {
             Configuration::updateGlobalValue('baautoimpor_is_run', 0);
        }
        curl_close($ch);
    }
    /* @param1 mảng chỉ số cột trong file excel
       @param2 mảng giá trị 1 hàng trong file excel
       return true/false
    */
    public function addStockWarehouse($array_warehouse, $row, $id_product, $id_pro_attr, $data_post, $increase = 0)
    {
        $arr_data_post = $data_post;
        $increase_quantity = $increase;
        $id_product = (int) $id_product;
        $id_product_attribute = (int) $id_pro_attr;
        $db = Db::getInstance();
        // Warehouses
        foreach ($array_warehouse as $id_warehouse => $array_values) {
            // get product unit price
            $price_te = str_replace(',', '.', @$row[$array_values["price"]]);
            $price_te = round((float) ($price_te), 6);
            // get product unit price currency id
            $currency = new Currency();
            $id_currency = (int) $currency->getIdByIsoCode(@$row[$array_values["iso_code"]], $this->shop_id);

            // if all is ok, add stock
            $warehouse = new Warehouse($id_warehouse);
            $employee = new Employee($arr_data_post["employee_id"]);
            // convert price to warehouse currency if needed
            if ($id_currency != $warehouse->id_currency) {
                // First convert price to the default currency
                $price_converted_to_default_currency = Tools::convertPrice($price_te, $id_currency, false);
                // Convert the new price from default currency to needed currency
                $price_te = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
            }

            // add stock
            $quantity = (int) @$row[$array_values["quantity"]];
            $sql = "SELECT physical_quantity FROM "._DB_PREFIX_."stock WHERE id_warehouse = "
                   .$id_warehouse." AND id_product = ".$id_product." AND id_product_attribute = ".$id_product_attribute;
            $quantity_old = (int) $db->getValue($sql, false);
            if ($increase_quantity == 1) {
                $quantity += $quantity_old;
            }
            $is_usable = $this->dataStatusUsable(@$row[$array_values["usable"]]);
            $name_stock_mvt_reason = pSQL(@$row[$array_values["label"]]);
            $id_stock_mvt_reason = 1;
            if ($name_stock_mvt_reason != "") {
                $sql = "SELECT id_stock_mvt_reason FROM " . _DB_PREFIX_ . "stock_mvt_reason_lang "
                        . "WHERE name = '" . pSQL($name_stock_mvt_reason) . "' AND id_lang = 1";
                $id_stock_mvt_reason = $db->getValue($sql, false);
                if ($id_stock_mvt_reason === false) {
                    $id_stock_mvt_reason = 1;
                } else {
                    $id_stock_mvt_reason = (int) $id_stock_mvt_reason;
                }
            }
            if (!StockMvtReason::exists($id_stock_mvt_reason)) {
                $id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_INC_REASON_DEFAULT');
            }
            
            $info = new Product($id_product);
            $reference = $info->reference;
            $ean13 = $info->ean13;
            $upc = $info->upc;
            if ($id_product_attribute>0) {
                $sql = "SELECT reference, ean13, upc FROM "._DB_PREFIX_."product_attribute"
                        ." WHERE id_product_attribute = "
                        .(int) $id_product_attribute." AND id_product = ".$id_product;
                $result = $db->getRow($sql, false);
                if (!empty($result)) {
                    $reference = $result["reference"];
                    $ean13 = $result["ean13"];
                    $upc = $result["upc"];
                }
            }
            if ($is_usable == true) {
                $usable_quantity = $quantity;
            } else {
                $usable_quantity = $quantity_old;
            }
            $stock_params = array(
                'id_warehouse'=>(int) $id_warehouse,
                'id_product'=>(int) $id_product,
                'id_product_attribute'=>(int) $id_product_attribute,
                'reference'=>(int) $reference,
                'ean13'=>(int) $ean13,
                'upc'=>(int) $upc,
                'physical_quantity'=>(int) $quantity,
                'usable_quantity'=>(int) $usable_quantity,
                'price_te'=>$price_te,
            );
            $sql = "SELECT id_stock FROM "._DB_PREFIX_."stock WHERE id_warehouse = "
                    . $id_warehouse ." AND id_product = "
                    .$id_product." AND id_product_attribute = ".$id_product_attribute;
            $id_stock = (int) $db->getValue($sql, false);
            if ($id_stock > 0) {
                $db->update("stock", $stock_params, "id_stock = ".$id_stock);
            } else {
                $db->insert("stock", $stock_params);
                $id_stock = $db->Insert_ID();
            }
            //
            
            $mvt_params = array(
                'id_stock' => $id_stock,
                'physical_quantity' => $quantity,
                'id_stock_mvt_reason' => $id_stock_mvt_reason,
                'id_supply_order' => "",
                'price_te' => $price_te,
                'last_wa' => null,
                'current_wa' => null,
                'id_employee' => $employee->id,
                'employee_firstname' => $employee->firstname,
                'employee_lastname' => $employee->lastname,
                'date_add' => date('Y-m-d H:i:s'),
                'sign' => 1
            );
            $sql = "SELECT id_stock FROM "._DB_PREFIX_."stock_mvt WHERE id_stock = ". $id_stock;
            $id_stock_mvt = (int) $db->getValue($sql, false);
            if ($id_stock > 0) {
                $db->update("stock_mvt", $mvt_params, "id_stock_mvt = ".$id_stock_mvt);
            } else {
                $db->insert("stock_mvt", $mvt_params);
            }
            
            // update I want to use the advanced stock management system
            $data_update = array();
            $data_update["advanced_stock_management"] = 1;
            $where = "id_product = " . $id_product;
            $db->update("product", $data_update, $where);

            $where .= " AND id_shop=" . $this->shop_id;
            $db->update("product_shop", $data_update, $where);

            $data_update = array();
            $data_update["id_product"] = $id_product;
            $data_update["id_product_attribute"] = $id_product_attribute;
            $data_update["id_warehouse"] = $id_warehouse;
            $data_update["location"] = pSQL(@$row[$array_values["location"]]);
            $db->insert("warehouse_product_location", $data_update, false, true, Db::REPLACE);
        }
        $sql_check_quan = 'SELECT physical_quantity FROM '._DB_PREFIX_.'stock WHERE
            id_product='. $id_product . ' AND id_product_attribute='. $id_product_attribute;
        $data_check_quan = $db->ExecuteS($sql_check_quan, true, false);
        $quan_ware = 0;
        if (!empty($data_check_quan)) {
            foreach ($data_check_quan as $value_data_check_quan) {
                $quan_ware += $value_data_check_quan['physical_quantity'];
            }
        }
        if ($quan_ware > 0) {
            StockAvailable::synchronize($id_product);
        }
    }
    public $id_product_attr = 0;

    // @param1 mảng chỉ số cột trong file excel
    // @param2 mảng giá trị 1 hàng trong file excel
    // return id_attribute
    public function checkAttribute($array_combination_group, $row)
    {
        $db = Db::getInstance();
        $array_id_attribute = array();
        
        foreach ($array_combination_group as $key_combination_group => $value_value_attribute) {
            if (@$row[$value_value_attribute] != null) {
                $sql = "SELECT ". _DB_PREFIX_ . "attribute_lang.id_attribute FROM "
                        . _DB_PREFIX_ . "attribute_lang INNER JOIN "
                        . _DB_PREFIX_ . "attribute ON " . _DB_PREFIX_ . "attribute.id_attribute = "
                        . _DB_PREFIX_ . "attribute.id_attribute WHERE LCASE(name) = '"
                        . pSQL(Tools::strtolower($row[$value_value_attribute]))
                        . "' AND id_attribute_group = " . $key_combination_group;
                $id_attribute = $db->getValue($sql, false);
                if ($id_attribute === false) {
                    $sql = "SELECT count(position) FROM " . _DB_PREFIX_ . "attribute WHERE id_attribute_group = "
                            . (int) $key_combination_group;
                    $position = (int) $db->getValue($sql, false);
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute VALUES('', '" . (int) $key_combination_group
                            . "', '', '" . $position . "')";
                    $db->query($sql);
                    $id_attribute = $db->Insert_ID();
                    
                    $lang = Language::getLanguages();
                    foreach ($lang as $value_lang) {
                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "attribute_lang VALUES('"
                            . (int) $id_attribute . "', '" . (int) $value_lang["id_lang"] . "', '"
                            . pSQL($row[$value_value_attribute]) . "')";
                        $db->query($sql);
                    }
                    $lang = Language::getLanguages(false);
                    foreach ($lang as $value_lang) {
                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "attribute_lang VALUES('"
                            . (int) $id_attribute . "', '" . (int) $value_lang["id_lang"] . "', '"
                            . pSQL($row[$value_value_attribute]) . "')";
                        $db->query($sql);
                    }
                    
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute_shop VALUES('" . (int) $id_attribute . "', '"
                            . (int) $this->shop_id . "')";
                    $db->query($sql);
                }
                $array_id_attribute[$key_combination_group][] = $id_attribute;
            }
        }
        return $array_id_attribute;
    }
    public function deleteImg()
    {
        $dir_img = _PS_MODULE_DIR_ . "ba_importer/images/";
        $scan = @scandir($dir_img);
        unset($scan[0]);
        unset($scan[1]);
        if (!empty($scan)) {
            foreach ($scan as $value) {
                if (strpos("index.php", $value) === false) {
                    @unlink($dir_img . $value);
                    $scan1 = @scandir($dir_img . $value);
                    if ($scan1 != false) {
                        unset($scan1[0]);
                        unset($scan1[1]);
                        if (!empty($scan1)) {
                            foreach ($scan1 as $value1) {
                                @unlink($dir_img . $value . "/" . $value1);

                                @rmdir($dir_img . $value . "/" . $value1);
                            }
                            @rmdir($dir_img . $value);
                        }
                    }
                }
            }
        }
    }
    // moveImages() function changed to ba_importer since 1.1.3+
    public function removeImages($id_product)
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $sql = "SELECT id_image FROM "._DB_PREFIX_."image WHERE id_product = "
                            . $id_product;
        $result_images = $db->executeS($sql, true, false);
        if (!empty($result_images)) {
            foreach ($result_images as $value_id) {
                $obimage = new Image($value_id["id_image"]);
                $obimage->deleteImage();
                
                $db->delete('image', "id_image = ".$value_id["id_image"]);
                $db->delete('image_lang', "id_image = ".$value_id["id_image"]);
                $db->delete('image_shop', "id_image = ".$value_id["id_image"]);
                $db->delete('product_attribute_image', "id_image = ".$value_id["id_image"]);
                $db->delete('ba_abandoned_img', "id_img = ".$value_id["id_image"]);
            }
        }
    }
    public function addImages($image_name, $id_product, $column, $is_cover = 0, $combilation = 0, $remove_image = 0)
    {
        $error_message = "";
        $_caption = "";
        // 1.6.0.1 have not had Tools::strpos function caused error
        if (strpos($image_name, "|") >= 0) {
            $array_img_name = explode("|", $image_name);
            if (count($array_img_name) <= 2) {
                if (count($array_img_name) == 1) {
                    $image_name = trim($array_img_name[0]);
                } else {
                    $_caption = trim($array_img_name[0]);
                    $image_name = trim($array_img_name[1]);
                }
            } else {
                $this->array_result[] = $this->l = "<li  class='error_import'>Image is in column "
                        . $column[0] . "  invalid, You should put it in format: Caption|Image's Name Or"
                        . "Caption|URL to Image</li>";
                return ;
            }
        }
        
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $is_cover = ($is_cover == 1) ? 1 : 0;
        if ($image_name != null) {
            ///// Remove all images of product
            if ($remove_image == 1) {
                if ($combilation == 0) {
                    $sql = "SELECT id_image FROM "._DB_PREFIX_."image WHERE id_product = "
                            . $id_product . " AND cover = ". $is_cover;
                } else {
                    $sql = "SELECT id_image FROM "._DB_PREFIX_."product_attribute_image WHERE id_product_attribute = "
                            .$this->id_product_attr;
                }
                $result_images = $db->executeS($sql, true, false);
                if (!empty($result_images)) {
                    foreach ($result_images as $value_id) {
                        $db->delete('image', "id_image = ".$value_id["id_image"]);
                        $db->delete('image_lang', "id_image = ".$value_id["id_image"]);
                        $db->delete('image_shop', "id_image = ".$value_id["id_image"]);
                        $db->delete('ba_abandoned_img', "id_img = ".$value_id["id_image"]);
                    }
                }
            }
            $sql = "SELECT id_img FROM "._DB_PREFIX_."ba_abandoned_img WHERE name_img = '"
                    .pSQL($image_name)."' AND id_product = ".$id_product. ' AND id_shop = '.(int) $this->shop_id;
            $id_img = (int) $db->getValue($sql, false);
            
            if ($id_img > 0) {
                $sql = "SELECT id_image FROM "._DB_PREFIX_."image WHERE id_image = '"
                    .$id_img."' AND id_product = ".$id_product;
                $check_id_image = (int) $db->getValue($sql, false);
                $sql_img_shop = '';
                if ($this->since1610()) {
                    $sql_img_shop = "SELECT id_image FROM "._DB_PREFIX_."image_shop WHERE id_image = '"
                    .$id_img."' AND id_product = '".$id_product."' AND id_shop = " . (int) $this->shop_id;
                } else {
                    $sql_img_shop = "SELECT id_image FROM "._DB_PREFIX_."image_shop WHERE id_image = '"
                    .$id_img."' "." AND id_shop = " . (int) $this->shop_id;
                }
                $check_id_img_shop = (int) $db->getValue($sql_img_shop, false);
                if ($check_id_image>0) {
                    if ($check_id_img_shop == 0) {
                        $this->updateImageShop($is_cover, $id_img, $id_product);
                    }
                    return $id_img;
                } else {
                    $db->delete('ba_abandoned_img', "id_img = ".$id_img);
                }
            }
            /// lay thong tin cua file
            $dir = _PS_MODULE_DIR_ . "ba_importer";
            $path_old = $dir . '/images/' . $image_name;
            $http = trim(Tools::strtolower($image_name));

            if (strpos($http, "http://") === 0 || strpos($http, "https://") === 0) {
                $arr = explode("/", $image_name);
                $path_old = $dir . '/images/' . trim(end($arr));
                $this->getImageFromUrl($image_name, $path_old);
            }
            $check_exit = file_exists($path_old);
            if ($check_exit === true) {
                $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
                $sql = 'SELECT MAX(position) FROM ' . _DB_PREFIX_ . "image WHERE id_product=$id_product";
                $posistion = $db->getValue($sql, false) + 1;
                if ($is_cover == 0) {
                    $sql = "SELECT id_image FROM "._DB_PREFIX_."image WHERE id_product = "
                            .(int) $id_product." AND cover =1";
                    $result = Db::getInstance()->getValue($sql, false);
                    if ($result === false) {
                        $is_cover = 1;
                    }
                }
                if ($is_cover == 1) {
                    $this->removeCoverPhoto($id_product);
                    $sql = "REPLACE INTO " . _DB_PREFIX_ . "image (cover,position,id_product) VALUES('"
                            . (int) $is_cover . "'," . (int) $posistion . "," . (int) $id_product . ")";
                    Db::getInstance()->query($sql);
                } else {
                    if (strpos(_PS_VERSION_, '1.6.0') === 0 || strpos(_PS_VERSION_, '1.5') === 0) {
                        $sql = "INSERT INTO " . _DB_PREFIX_ . "image (cover,position,id_product) VALUES('',"
                                . (int) $posistion . "," . (int) $id_product . ")";
                        Db::getInstance()->query($sql);
                    } else {
                        $sql = "INSERT INTO " . _DB_PREFIX_ . "image (cover,position,id_product) VALUES(NULL,"
                                . (int) $posistion . "," . (int) $id_product . ")";
                        Db::getInstance()->query($sql);
                    }
                }
                $id_image = (int) $db->Insert_ID();
                $posistion++;
                $lang = Language::getLanguages();
                foreach ($lang as $value_lang) {
                    $_caption = $this->l = $_caption;
                    $data = array(
                        'id_image' => $id_image,
                        'id_lang' => (int) $value_lang["id_lang"],
                        'legend' => pSQL($_caption)
                    );
                    $db->insert('image_lang', $data, false, true, Db::REPLACE);
                }
                $lang = Language::getLanguages(false);
                foreach ($lang as $value_lang) {
                    $_caption = $this->l = $_caption;
                    $data = array(
                        'id_image' => $id_image,
                        'id_lang' => (int) $value_lang["id_lang"],
                        'legend' => pSQL($_caption)
                    );
                    $db->insert('image_lang', $data, false, true, Db::REPLACE);
                }
                $this->updateImageShop($is_cover, $id_image, $id_product);
                $this->moveImages($path_old, (int) $id_image, $id_product, $is_cover);
                $data = array(
                    "id_img" => $id_image,
                    "name_img" => $image_name,
                    "id_product" => $id_product,
                    "id_shop" => (int) $this->shop_id
                );
                $db->insert('ba_abandoned_img', $data, false, true, Db::REPLACE);
                Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_product));
                if ($combilation == 1) {
                    return $id_image;
                } else {
                    return $error_message;
                }
            }
        }
    }
    public function addSupplier($id_supplier, $id_product, $id_attr, $reference, $price_te, $id_currency)
    {
        foreach ($id_supplier as $value_sup) {
            $supplier_id_currency = $id_currency;
            $supplier_price_te = $price_te;
            $supplier_reference = $reference;
            $data = array(
                "id_product" => $id_product,
                "id_product_attribute" => $id_attr,
                "id_supplier" => $value_sup,
                "product_supplier_reference" => pSQL($supplier_reference),
                "product_supplier_price_te" => $supplier_price_te,
                "id_currency" => $supplier_id_currency
            );
            Db::getInstance()->insert("product_supplier", $data, false, true, DB::REPLACE);
        }
    }
    // kiểm tra tag đã tồn tại hay chưa
    public function updateTag($tag_name, $id_lang)
    {
        $db = Db::getInstance();
        $sql = 'SELECT id_tag FROM '._DB_PREFIX_.'tag WHERE id_lang='.(int) $id_lang." AND name='".pSQL($tag_name)."'";
        $id_tag = $db->getValue($sql, false);
        if ($id_tag===false) {
            $data = array(
                'id_lang'     => @$id_lang,
                'name'         => pSQL($tag_name),
            );
            $db->insert('tag', $data);
            @$id_tag = (int) $db->Insert_ID();
        }
        return $id_tag;
    }
    // reset Combination default cua 1 Product
    public function removeCombinationDefault($id_product)
    {
        $db = Db::getInstance();
        $sql = "UPDATE " . _DB_PREFIX_ . "product_attribute SET default_on=NULL "
                                    . " WHERE id_product = " . $id_product;
        $db->query($sql);
        // lấy tất cả Attribute của Product
        $sql = "SELECT id_product_attribute FROM "._DB_PREFIX_."product_attribute WHERE id_product = ".$id_product;
        $arr_id_pro_attr = $db->executeS($sql, true, false);
        if (!empty($arr_id_pro_attr)) {
            foreach ($arr_id_pro_attr as $value) {
                $id_product_attribute = $value["id_product_attribute"];
                $sql = "UPDATE " . _DB_PREFIX_ . "product_attribute_shop SET default_on=NULL "
                    . " WHERE id_product_attribute = " . $id_product_attribute. " AND id_shop=". (int) $this->shop_id;
                $db->query($sql);
            }
        }
    }
    
    protected function shouldBeExecuted($cron)
    {
        $hour = ($cron['hour'] == -1) ? date('H') : $cron['hour'];
        $day = ($cron['day'] == -1) ? date('d') : $cron['day'];
        $month = ($cron['month'] == -1) ? date('m') : $cron['month'];
        $aa = strtotime('Sunday +' . $cron['day_of_week'] . ' days');
        $day_of_week = ($cron['day_of_week'] == -1) ? date('D') : date('D', $aa);

        $day = date('Y').'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($day, 2, '0', STR_PAD_LEFT);
        $execution = $day_of_week.' '.$day.' '.str_pad($hour, 2, '0', STR_PAD_LEFT);
        $now = date('D Y-m-d H');
        
        return !(bool)strcmp($now, $execution);
    }
    
    public function updateImageShop($is_cover, $id_image, $id_product)
    {
        if (strpos(_PS_VERSION_, '1.6.0') === 0 || strpos(_PS_VERSION_, '1.5') === 0) {
            if ($is_cover == 1) {
                $sql = "REPLACE INTO " . _DB_PREFIX_ . "image_shop (cover,id_image,id_shop) VALUES("
                        . (int) $is_cover . "," . (int) $id_image . "," . (int) $this->shop_id . ")";
            } else {
                $sql = "REPLACE INTO " . _DB_PREFIX_ . "image_shop (cover,id_image,id_shop)"
                       ." VALUES(''," . (int) $id_image . "," . (int) $this->shop_id . ")";
            }

            Db::getInstance()->query($sql);
        } else {
            // tu prestashop 1.6.1+ them truong id_product trong b?ng image_shop
            if ($is_cover == 1) {
                $sql = "REPLACE INTO " . _DB_PREFIX_ . "image_shop (cover,id_image,id_shop,id_product) VALUES("
                        . (int) $is_cover . "," . (int) $id_image . ","
                        . (int) $this->shop_id . "," . (int) $id_product . ")";
            } else {
                $sql = "REPLACE INTO " . _DB_PREFIX_ . "image_shop (cover,id_image,id_shop,id_product)"
                       ." VALUES(NULL," . (int) $id_image . ","
                       . (int) $this->shop_id . "," . (int) $id_product . ")";
            }
            Db::getInstance()->query($sql);
        }
    }
    public function processCronjobError($id_setting)
    {
        $id_queue = Configuration::get('baautoimpor_id_queue');
        $id_setting = (int) $id_setting;
        // set this Setting worked
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'ba_cronjobs_importer SET imported=1';
        $sql .= ' WHERE `id_importer_config` = '. $id_setting;
        Db::getInstance()->query($sql);
        // remove this Setting out of Queue
        if (!empty($id_queue)) {
            $arr_queue = explode(',', $id_queue);
            $arr_queue = $this->deleteElement($id_setting, $arr_queue);
            $id_queue = implode(',', $arr_queue);
            Configuration::updateGlobalValue('baautoimpor_id_queue', $id_queue);
        }
        // request other cronjob
        Configuration::updateGlobalValue('baautoimpor_is_run', 0);
        $this->repeatImporter('');
        die();
    }
    public function validateFile($fileName)
    {
        $dir_file = _PS_MODULE_DIR_ . $this->name . '/stories/' . $fileName;
        if (!file_exists($dir_file)) {
            return false;
        }
        if (filesize($dir_file)<=0) {
            return false;
        }
        if (!is_readable($dir_file)) {
            return false;
        }
        return true;
    }
}
