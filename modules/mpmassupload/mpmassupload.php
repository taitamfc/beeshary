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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__).'/classes/MarketplaceMassUpload.php';
include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';

class MpMassUpload extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    private $_html = '';
    public function __construct()
    {
        $this->name = 'mpmassupload';
        $this->tab = 'front_office_features';
        $this->version = '5.2.0';
        $this->author = 'Webkul';
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->secure_key = Tools::hash($this->name);
        $this->dependencies = array('marketplace');

        parent::__construct();
        $this->displayName = $this->l('Marketplace Mass Upload');
        $this->description = $this->l('Seller can upload bulk products through CSV.');
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

        if (!parent::install()
            || !$this->callInstallTab()
            || !$this->alterMarketplaceSellerProduct()
            || !$this->registerHook('displayMPMyAccountMenu')
            || !$this->registerHook('displayMPMenuBottom')
            || !$this->registerHook('addColumnSellerProductList')
        ) {
            return false;
        } else {
            Configuration::updateValue('MASS_UPLOAD_APPROVE', 'admin');
            Configuration::updateValue('MASS_UPLOAD_COMBINATION_APPROVE', 0);
            Configuration::updateValue('MASS_UPLOAD_ALLOW_EDIT_COMBINATION', 0);
            Configuration::updateValue('MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES', 0);
            Configuration::updateValue('MASS_UPLOAD_DELETE_PRODUCT_IMAGES_ZIP', 0);

            if (!$this->callAssociateModuleToShop()) {
                return false;
            } else {
                return true;
            }
        }
    }

    // Add csv request no
    private function alterMarketplaceSellerProduct()
    {
        Db::getInstance()->query('ALTER TABLE '._DB_PREFIX_ .'wk_mp_seller_product ADD `csv_request_no` VARCHAR(10) character set utf8 Default NULL');
        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminMarketplacemassupload', 'Mass Upload Request', 'AdminMarketplaceManagement');
        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int)Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }


        $tab->module = $this->name;
        return $tab->add();
    }

    public function getContent()
    {
        $this->context->controller->addJs($this->_path.'views/js/upload_script.js');

        if (Tools::isSubmit('btnUploadSubmit') || Tools::isSubmit('btnExportSubmit')) {
            $this->_postProcess();
        } else {
            $this->_html .= '<br />';
        }

        return $this->_html .= $this->renderForm();
    }

    public function renderForm()
    {
        $WK_MP_SELLER_PRODUCT_COMBINATION = Configuration::get('WK_MP_SELLER_PRODUCT_COMBINATION');
        $MASS_UPLOAD_COMBINATION_APPROVE = Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE');

        $fields_form = array();
        $fields_form[0]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Mass Upload Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Mass Uploaded Approved By Admin'),
                        'name' => 'MASS_UPLOAD_APPROVE',
                        'required' => false,
                        'class'    => 't',
                        'col' => 7,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => '1',
                                'label' => $this->l('Enabled')
                                ),
                            array(
                                'id' => 'active_off',
                                'value' => '0',
                                'label' => $this->l('Disabled')
                                )
                        ),
                        'hint' => $this->l('Mass Uploaded Approved By Admin')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Seller can upload Combination CSV file'),
                        'name' => 'MASS_UPLOAD_COMBINATION_APPROVE',
                        'desc' => ($WK_MP_SELLER_PRODUCT_COMBINATION ? false : $this->l('You must allow feature :
                        "Sellers can create combinations for their products" from marketplace configuration')),
                        'required' => false,
                        'class'    => 't',
                        'col' => 7,
                        'is_bool' => true,
                        'disabled' => ($WK_MP_SELLER_PRODUCT_COMBINATION ? false : true),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => '1',
                                'label' => $this->l('Enabled')
                                ),
                            array(
                                'id' => 'active_off',
                                'value' => '0',
                                'label' => $this->l('Disabled')
                                )
                        ),
                        'hint' => $this->l('If Yes, Seller can upload Combination CSV file')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Allow seller to edit combination using CSV'),
                        'name' => 'MASS_UPLOAD_ALLOW_EDIT_COMBINATION',
                        'desc' => $this->l('If you allow this feature, then seller can edit combination from CSV'),
                        'is_bool' => true,
                        'disabled' => ($MASS_UPLOAD_COMBINATION_APPROVE ? false : true),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => '1',
                                'label' => $this->l('Enabled')
                                ),
                            array(
                                'id' => 'active_off',
                                'value' => '0',
                                'label' => $this->l('Disabled')
                                )
                        ),
                        'hint' => $this->l('If Yes, Seller can upload Combination CSV file')
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'btnUploadSubmit'
                )
            );

            $fields_form[1]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Mass Export Configuration'),
                    'icon' => 'icon-info'
                ),
                'description' => $this->l(
                    'You can set cron to delete zip files downloaded by sellers.
                     all product image zip files will be force delete,
                     in case somebody is downloading the zip file at the same time his downloading will be failed.
                    '
                ).'5 0 * * * curl '.Tools::getShopDomainSsl(true, true).
                __PS_BASE_URI__.'modules/mpmassupload/cronjobdeleteprodzip.php?token='.$this->secure_key,
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Seller can download product image zip file'),
                        'name' => 'MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES',
                        'hint' => $this->l('Allow seller to download his product Image zip file.'),
                        'desc' => $this->l(
                            'If you allow this feature, then seller can download product image zip file'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => '1',
                                'label' => $this->l('Enabled')
                                ),
                            array(
                                'id' => 'active_off',
                                'value' => '0',
                                'label' => $this->l('Disabled')
                                )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Delete all Product Image Zip file'),
                        'name' => 'MASS_UPLOAD_DELETE_PRODUCT_IMAGES_ZIP',
                        'hint' => $this->l('On save all the generated Product Image zip files will be deleted.'),
                        'desc' => $this->l(
                            'On click save all the existing Seller\'s Product Images Zip files, will be deleted.'
                        ),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => '1',
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => '0',
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'btnExportSubmit'
                )
            );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;

        //$this->fields_form = array();
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues()
    {
        $val = (Configuration::get('MASS_UPLOAD_APPROVE') == 'admin') ? 1 : 0;

        return array(
            'MASS_UPLOAD_APPROVE' => Tools::getValue('MASS_UPLOAD_APPROVE', $val),
            'MASS_UPLOAD_COMBINATION_APPROVE' => Tools::getValue('MASS_UPLOAD_COMBINATION_APPROVE', Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE')),
            'MASS_UPLOAD_ALLOW_EDIT_COMBINATION' => Tools::getValue('MASS_UPLOAD_ALLOW_EDIT_COMBINATION', Configuration::get('MASS_UPLOAD_ALLOW_EDIT_COMBINATION')),
            'MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES' => Tools::getValue(
                'MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES',
                Configuration::get('MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES')
            ),
            'MASS_UPLOAD_DELETE_PRODUCT_IMAGES_ZIP' => Tools::getValue(
                'MASS_UPLOAD_DELETE_PRODUCT_IMAGES_ZIP',
                Configuration::get('MASS_UPLOAD_DELETE_PRODUCT_IMAGES_ZIP')
            ),
        );
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnUploadSubmit')) {
            $val = (Tools::getValue('MASS_UPLOAD_APPROVE') == '1') ? 'admin' : 'default';
            $editCombAllow = Tools::getValue('MASS_UPLOAD_COMBINATION_APPROVE') ? Tools::getValue('MASS_UPLOAD_ALLOW_EDIT_COMBINATION') : 0;

            Configuration::updateValue('MASS_UPLOAD_APPROVE', $val);
            Configuration::updateValue('MASS_UPLOAD_COMBINATION_APPROVE', (int)Tools::getValue('MASS_UPLOAD_COMBINATION_APPROVE'));
            Configuration::updateValue('MASS_UPLOAD_ALLOW_EDIT_COMBINATION', (int)$editCombAllow);

            $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
        }
        if (Tools::isSubmit('btnExportSubmit')) {
            Configuration::updateValue('MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES', (int)Tools::getValue('MASS_UPLOAD_ALLOW_DOWNLOAD_IMAGES'));
            Configuration::updateValue('MASS_UPLOAD_DELETE_PRODUCT_IMAGES_ZIP', (int)Tools::getValue('MASS_UPLOAD_DELETE_PRODUCT_IMAGES_ZIP'));

            if (Configuration::get('MASS_UPLOAD_DELETE_PRODUCT_IMAGES_ZIP')) {
                $zip_file_path = _PS_MODULE_DIR_.'mpmassupload/views/export_csv/';
                MarketplaceMassUpload::recursiveRemove($zip_file_path);
            }
        }
    }

    public function callAssociateModuleToShop()
    {
        $module_id = Module::getModuleIdByName($this->name);
        Configuration::updateGlobalValue('MP_MASS_UPLOAD_MODULE_ID', $module_id);
        return true;
    }

    public function hookAddColumnSellerProductList()
    {
        return 'csv_request_no-CSV Request No.';
    }

    public function hookDisplayMPMyAccountMenu()
    {
        $id_customer = $this->context->customer->id;
        $obj_marketplace_seller = new WkMpSeller();
        $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($id_customer);
        if ($mp_seller && $mp_seller['active']) {
            $link = new Link();
            $extra = array('come_from'=>'my-account');
            $massuploadview = $link->getModuleLink('mpmassupload', 'massuploadview', $extra);
            $this->context->smarty->assign("massuploadview", $massuploadview);
            $this->context->smarty->assign("mpmenu", "0");
            return $this->display(__FILE__, 'mass_upload_view.tpl');
        }
    }

    public function hookDisplayMPMenuBottom()
    {
        $id_customer = $this->context->customer->id;
        $obj_marketplace_seller = new WkMpSeller();
        $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($id_customer);
        if ($mp_seller && $mp_seller['active']) {
            $link = new Link();
            $extra = array('come_from'=>'my-account');
            $massuploadview = $link->getModuleLink('mpmassupload', 'massuploadview', $extra);
            $this->context->smarty->assign("massuploadview", $massuploadview);
            $this->context->smarty->assign("mpmenu", "1");
            return $this->display(__FILE__, 'mass_upload_view.tpl');
        }
    }

    public function drop_table($table_name_without_prefix)
    {
        $drop =Db::getInstance()->execute("DROP TABLE `"._DB_PREFIX_.$table_name_without_prefix."`");
        if (!$drop) {
            return false;
        }
        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }
        return true;
    }

    private function deleteCsvColumnFromMPSP()
    {
        Db::getInstance()->query('ALTER TABLE '._DB_PREFIX_ .'wk_mp_seller_product DROP COLUMN `csv_request_no`');
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->drop_table('marketplace_mass_upload')
            || !$this->uninstallTab()
            || !$this->deleteCsvColumnFromMPSP()
            ) {
            return false;
        }

        $request_obj = new MarketplaceMassUpload();
        $csv_dir = dirname(__FILE__).'/views/uploaded_csv';
        chmod($csv_dir, 0777);
        $request_obj->rrmdir($csv_dir);
        mkdir($csv_dir);
        return true;
    }
}
