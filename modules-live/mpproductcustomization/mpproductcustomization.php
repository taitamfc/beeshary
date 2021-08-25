<?php
/**
 * 2010-2017 Webkul.
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

include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
include_once 'classes/MarketplaceProductCustomization.php';

class MpProductCustomization extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'mpproductcustomization';
        $this->tab = 'front_office_features';
        $this->version = '5.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 1;
        $this->dependencies = array('marketplace');
        parent::__construct();
        $this->displayName = $this->trans('Marketplace Product Customization');
        $this->description = $this->trans('Marketplace seller can add customization field to the products');
    }

    public function hookDisplayMpProductNavTab()
    {
        if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
            $this->context->smarty->assign('admin', 1);
            return $this->display(__FILE__, 'add_product_customization.tpl');
        } else {
            return $this->fetch('module:mpproductcustomization/views/templates/hook/add_product_customization.tpl');
        }
    }

    public function hookDisplayMpProductTabContent()
    {
        if ($mp_product_id = Tools::getValue('id_mp_product')) {
            $obj_product_customization = new MarketplaceProductCustomization();
            $meta_info = $obj_product_customization->getCustomizationFieldIds($mp_product_id);
            $customization_fields = $obj_product_customization->getLangFieldValue($mp_product_id);
            if ($meta_info) {
                $text_count = 0;
                $files_count = 0;
                foreach ($meta_info as $field) {
                    if ($field['type'] == 1) {
                        ++$text_count;
                    } else {
                        ++$files_count;
                    }
                }
                if ($text_count) {
                    $this->context->smarty->assign('text_count', $text_count);
                }
                if ($files_count) {
                    $this->context->smarty->assign('files_count', $files_count);
                }
                $this->context->smarty->assign('meta_info', $meta_info);
                $this->context->smarty->assign('customization_fields', $customization_fields);
            }

            $product_detail = WkMpSellerProduct::getSellerProductByIdProduct($mp_product_id);
            if ($product_detail) {
                $id_seller = $product_detail['id_seller'];
                // Set default lang at every form according to configuration multi-language
                WkMpHelper::assignDefaultLang($id_seller);
            }
        } else {
            if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
                //$obj_mp_seller = new WkMpSeller();
                $customer_info = WkMpSeller::getAllSeller();
                if ($customer_info) {
                    //get first seller from the list
                    $first_seller_details = $customer_info[0];
                    $mp_id_seller = $first_seller_details['id_seller'];
                } else {
                    $mp_id_seller = 0;
                }
            } else {
                if ($this->context->customer->id) {
                    $id_customer = $this->context->customer->id;
                    $mp_seller = WkMpSeller::getSellerDetailByCustomerId($id_customer);
                    if ($mp_seller && $mp_seller['active']) {
                        $mp_id_seller = $mp_seller['id_seller'];
                    }
                }
            }
            // Set default lang at every form according to configuration multi-language
            WkMpHelper::assignDefaultLang($mp_id_seller);
        }

        if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
            return $this->display(__FILE__, 'admin_add_product_customization_form.tpl');
        } else {
            return $this->fetch('module:mpproductcustomization/views/templates/hook/add_product_customization_form.tpl');
        }
    }

    public function hookDisplayMporderDetailListrow($params)
    {
        $id_order = $params['params']['id_order'];
        $id_order_detail = $params['params']['id_order_detail'];

        $obj_order = new Order($id_order);
        $product = $obj_order->getProducts();

        $this->context->smarty->assign('product', $product[$id_order_detail]);

        return $this->fetch('module:mpproductcustomization/views/templates/hook/customization_detail.tpl');
    }

    public function hookActionBeforeAddMPProduct()
    {
        $file = Tools::getValue('uploadable_files');

        if (!empty($file)) {
            if (!Validate::isUnsignedInt($file)) {
                $this->context->controller->errors[] = $this->trans('The uploadable_files field is invalid.');
            }
        }

        $text = Tools::getValue('text_fields');
        if (!empty($text)) {
            if (!Validate::isUnsignedInt($text)) {
                $this->context->controller->errors[] = $this->trans('The text_fields field is invalid.');
            }
        }
    }

    public function hookActionBeforeUpdateMPProduct()
    {
        $file = Tools::getValue('uploadable_files');
        if (!empty($file)) {
            if (!Validate::isUnsignedInt($file)) {
                $this->context->controller->errors[] = $this->trans('The uploadable_files field is invalid.');
            }
        }
        $text = Tools::getValue('text_fields');
        if (!empty($text)) {
            if (!Validate::isUnsignedInt($text)) {
                $this->context->controller->errors[] = $this->trans('The text_fields field is invalid.');
            }
        }
    }

    public function hookActionAfterAddMPProduct($params)
    {
        $mp_product_id = $params['id_mp_product'];
        $this->addNewCustomizationField($mp_product_id);
    }

    public function hookActionAfterUpdateMPProduct($params)
    {
        $mp_product_id = $params['id_mp_product'];
        $this->saveProductCustomizationField($mp_product_id);
    }

    public function addNewCustomizationField($mp_product_id)
    {
        if ($mp_product_id) {
            $file_field = trim(Tools::getValue('uploadable_files'));
            $text_field = trim(Tools::getValue('text_fields'));
            $obj_product_customization = new MarketplaceProductCustomization();

            if (!$obj_product_customization->createLabels($file_field, $text_field, $mp_product_id)) {
                $this->context->controller->errors[] = $this->l('An error occurred while creating customization fields.');
            }
        }
    }

    public function saveProductCustomizationField($mp_product_id)
    {
        if ($mp_product_id) {
            $file = trim(Tools::getValue('uploadable_files'));
            $text = trim(Tools::getValue('text_fields'));
            $obj_product_customization = new MarketplaceProductCustomization();
            $current_customization = $obj_product_customization->getCustomizationFieldIds($mp_product_id);
            $files_count = 0;
            $text_count = 0;
            if (is_array($current_customization)) {
                foreach ($current_customization as $field) {
                    if ($field['type'] == 1) {
                        ++$text_count;
                    } else {
                        ++$files_count;
                    }
                }
            }

            if (($file < $files_count || $text < $text_count)
                && $obj_product_customization->updatefields($mp_product_id, $files_count - $file, $text_count - $text)
                ) {
                $this->context->controller->errors[] = $this->l('An error occurred while updating the custom configuration.');
            }

            if (!$obj_product_customization->createLabels(
                (int) $file - $files_count,
                $text - $text_count,
                $mp_product_id
            )) {
                $this->context->controller->errors[] = $this->l('An error occurred while creating customization fields.');
            }

            foreach (Language::getLanguages(true) as $language) {
                foreach ($current_customization as $value) {
                    if ($value['type'] == 1) {
                        $lebel_value = Tools::getValue('textname_'.$value['id'].'_'.$language['id_lang']);
                        $required = Tools::getValue('require_'.$value['id']);
                    } else {
                        $lebel_value = Tools::getValue('filename_'.$value['id'].'_'.$language['id_lang']);
                        $required = Tools::getValue('require_'.$value['id']);
                    }

                    $obj_product_customization->updateLebel(
                        $value['id'],
                        $lebel_value,
                        $language['id_lang'],
                        $required
                    );
                }
            }

            $product_detail = WkMpSellerProduct::getSellerProductByIdProduct($mp_product_id);
            $ps_product_id = $product_detail['id_ps_product'];

            if ($ps_product_id) {
                $this->insertIntoPsProduct($mp_product_id, $ps_product_id, $file, $text);
            }
        }
    }

    public function hookActionToogleMPProductCreateStatus($params)
    {
        $mp_product_id = Tools::getValue('id_mp_product');
        $ps_product_id = $params['id_product'];

        $obj_product_customization = new MarketplaceProductCustomization();
        $current_customization = $obj_product_customization->getCustomizationFieldIds($mp_product_id);
        $files_count = 0;
        $text_count = 0;
        if (is_array($current_customization)) {
            foreach ($current_customization as $field) {
                if ($field['type'] == 1) {
                    ++$text_count;
                } else {
                    ++$files_count;
                }
            }
        }

        if ($mp_product_id && $ps_product_id) {
            $this->insertIntoPsProduct($mp_product_id, $ps_product_id, $files_count, $text_count);
        }
    }

    public function insertIntoPsProduct($mp_product_id, $ps_product_id, $file, $text)
    {
        if ($ps_product_id) {
            $obj_product = new Product($ps_product_id);
            $obj_product->uploadable_files = $file;
            $obj_product->text_fields = $text;
            $obj_product->save();
            $meta_info = $obj_product->getCustomizationFieldIds();
            $files_count = 0;
            $text_count = 0;
            if (is_array($meta_info)) {
                foreach ($meta_info as $field) {
                    if ($field['type'] == 1) {
                        ++$text_count;
                    } else {
                        ++$files_count;
                    }
                }
            }

            if (!$obj_product->createLabels(
                (int) $obj_product->uploadable_files - $files_count,
                (int) $obj_product->text_fields - $text_count
            )) {
                $this->context->controller->errors[] = $this->trans('An error occurred while creating customization fields.');
            }

            if (!$obj_product->updateLabels()) {
                $this->context->controller->errors[] = $this->trans('An error occurred while updating customization fields.');
            }

            $obj_product->customizable = ($obj_product->uploadable_files > 0 || $obj_product->text_fields > 0) ? 1 : 0;
            if (($obj_product->uploadable_files != $files_count || $obj_product->text_fields != $text_count)
                && !count($this->context->controller->errors) && !$obj_product->update()) {
                $this->context->controller->errors[] = $this->l('An error occurred while updating the custom configuration.');
            }
            $obj_product->save();
            if ($obj_product->customizable) {
                $obj_product_customization = new MarketplaceProductCustomization();
                $obj_product_customization->updatePsNameField($mp_product_id, $ps_product_id);
            }
        }
    }

    public function hookActionMpProductDelete($params)
    {
        $mp_product_id = $params['id_mp_product'];
        if ($mp_product_id) {
            $obj_product_customization = new MarketplaceProductCustomization();
            $obj_product_customization->deleteMpProductCustomization($mp_product_id);
        }
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($params['object']->id) {
            $new_lang_id = $params['object']->id;

            //Assign all lang's main table in an ARRAY
            $lang_tables = array('mp_product_customization');

            //If Admin create any new language when we do entry in module all lang tables.
            WkMpHelper::updateIdLangInLangTables($new_lang_id, $lang_tables);
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

        if (!parent::Install()
            || !$this->registerHook('displayMpProductNavTab')
            || !$this->registerHook('displayMpProductTabContent')
            || !$this->registerHook('displayMporderDetailListrow')
            || !$this->registerHook('actionBeforeAddMPProduct')
            || !$this->registerHook('actionBeforeUpdateMPProduct')
            || !$this->registerHook('actionAfterAddMPProduct')
            || !$this->registerHook('actionAfterUpdateMPProduct')
            || !$this->registerHook('actionToogleMPProductCreateStatus')
            || !$this->registerHook('actionMpProductDelete')
            || !$this->registerHook('actionObjectLanguageAddAfter')
            ) {
            return false;
        }

        return true;
    }

    public function dropTable()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'mp_product_customization`,
            `'._DB_PREFIX_.'mp_product_customization_lang`');
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->dropTable()) {
            return false;
        }

        return true;
    }
}
