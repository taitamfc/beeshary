<?php
/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2015 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageSetting.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageBuilderModel.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageBuilderProfilesModel.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/controllers/admin/AdminApPageBuilderPositions.php');

class AdminApPageBuilderHomeController extends ModuleAdminControllerCore
{
    public static $shortcode_lang;
    public static $lang_id;
    public static $language;
    public $error_text = '';
    public $module_name;
    public $module_path;
//    public $module_path_resource;
    public $tpl_path;
    public $theme_dir;
    public $file_content = '';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->show_toolbar = true;
        $this->table = 'appagebuilder';
        $this->className = 'ApPageBuilderHome';
        $this->context = Context::getContext();
        $this->module_name = 'appagebuilder';
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
//        $this->module_path_resource = $this->module_path.'views/';
        $this->tpl_path = _PS_ROOT_DIR_.'/modules/'.$this->module_name.'/views/templates/admin';
        parent::__construct();
        $this->multishop_context = false;
        $this->theme_dir = _PS_THEME_DIR_;
        apPageHelper::loadShortCode(_PS_THEME_DIR_);
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['save'] = array(
            //'short' => $this->l('Save', null, null, false),
            'short' => 'SaveAndStay',
            'href' => 'javascript:;',
            //'desc' => $this->l('Save', null, null, false),
            'desc' => $this->l('Save and stay'),
            'confirm' => 1,
            'js' => 'submitform()'
        );
        $current_id = Tools::getValue('id_appagebuilder_profiles');
        if (!$current_id) {
            $profile = ApPageBuilderProfilesModel::getActiveProfile('index');
            $current_id = $profile['id_appagebuilder_profiles'];
        }
        $lang = '';
        if (Configuration::get('PS_REWRITING_SETTINGS') && count(Language::getLanguages(true)) > 1) {
            $lang = Language::getIsoById($this->context->employee->id_lang).'/';
        }
        $url_preview = $this->context->shop->getBaseUrl().(Configuration::get('PS_REWRITING_SETTINGS') ? '' : 'index.php')
                .$lang.'?id_appagebuilder_profiles='.$current_id;
        $this->page_header_toolbar_btn['preview'] = array(
            //'short' => $this->l('Save', null, null, false),
            'short' => 'Preview',
            'href' => $url_preview,
            'target' => '_blank',
            //'desc' => $this->l('Save', null, null, false),
            'desc' => $this->l('Preview'),
            'confirm' => 0
        );
        parent::initPageHeaderToolbar();
    }

    public function postProcess()
    {
        if (count($this->errors) > 0) {
            if ($this->ajax) {
                $array = array('hasError' => true, 'errors' => $this->errors[0]);
                die(Tools::jsonEncode($array));
            }
            return;
        }
        
        $action = Tools::getValue('action');
//        $type = Tools::getValue('type');
        
        if ($action == 'processPosition') {
            $this->processPosition();
        }
        
        if ($action == 'selectPosition') {
            $this->selectPosition();
        }
        
        if (Tools::isSubmit('submitImportData')) {
            $this->importData(Language::getLanguages(false), (int)$this->context->language->id);
        }
        
        if ($action == 'export') {
            $this->exportData();
        }
        
        //DONGND:: submit save
        if (Tools::isSubmit('submitSaveAndStay')) {
            if (Tools::getValue('data_profile') && Tools::getValue('data_profile') != '') {
                $data_form = Tools::jsonDecode(Tools::getValue('data_profile'), 1);
                if (is_array($data_form)) {
                    $id_profile = Tools::getValue('data_id_profile');
                    self::$language = Language::getLanguages(false);
    //                $data = array();
                    $arr_id = array('header' => 0, 'content' => 0, 'footer' => 0, 'product' => 0);
                    foreach ($data_form as $hook) {
                        $position_id = (int)isset($hook['position_id']) ? $hook['position_id'] : '0';
                        $hook['position'] = (isset($hook['position']) && $hook['position']) ? $hook['position'] : '';
                        $hook['name'] = (isset($hook['name']) && $hook['name']) ? $hook['name'] : 0;
                        $position = Tools::strtolower($hook['position']);
                        $arr_id[$position] = (isset($arr_id[$position]) && $arr_id[$position]) ? $arr_id[$position] : '';
                        // Create new position with name is auto random, and save id of new for other positions reuse
                        // position for other hook in this position to variable $header, $content...
                        if ($position_id == 0 && $arr_id[$position] == 0) {
                            $key = ApPageSetting::getRandomNumber();
                            $position_data = array(
                                'name' => $position.$key,
                                'position' => $position,
                                'position_key' => 'position'.$key);
                            $position_id = apPageHelper::autoCreatePosition($position_data);
                            $arr_id[$position] = $position_id;
                        } else if ($position_id != 0 && $arr_id[$position] == 0) {
                            $arr_id[$position] = $position_id;
                        }

                        $obj_model = new ApPageBuilderModel();
                        $obj_model->id = $obj_model->getIdbyHookName($hook['name'], $arr_id[$position]);
                        $obj_model->hook_name = $hook['name'];
                        $obj_model->page = 'index';
                        $obj_model->id_appagebuilder_positions = $arr_id[$position];
                        if (isset($hook['groups'])) {
                            foreach (self::$language as $lang) {
                                $params = '';
                                if (self::$shortcode_lang) {
                                    foreach (self::$shortcode_lang as &$s_type) {
                                        foreach ($s_type as $key => $value) {
                                            $s_type[$key] = $key.'_'.$lang['id_lang'];
                                            // validate module
                                            unset($value);
                                        }
                                    }
                                }
                                $obj_model->params[$lang['id_lang']] = '';
                                ApShortCodesBuilder::$lang_id = $lang['id_lang'];
                                foreach ($hook['groups'] as $groups) {
                                    $params = $this->getParamByHook($groups, $params, $hook['name']);
                                }

                                $obj_model->params[$lang['id_lang']] = $params;
                            }
                        }

                        if ($obj_model->id) {
                            $this->clearModuleCache();
                            $obj_model->save();
                        } else {
                            $this->clearModuleCache();
                            $obj_model->add();
                        }
                        $path = _PS_ROOT_DIR_.'/cache/smarty/cache/'.$this->module_name;

                        $this->deleteDirectory($path);
                    };

                    $profile = new ApPageBuilderProfilesModel($id_profile);

                    # Fix : must keep other data in param. ( exception + other data )

                    $params = Tools::jsonDecode($profile->params);

                    isset($params->fullwidth_index_hook) ? $this->config_module['fullwidth_index_hook'] = $params->fullwidth_index_hook : false;
                    isset($params->fullwidth_other_hook) ? $this->config_module['fullwidth_other_hook'] = $params->fullwidth_other_hook : false;
                    isset($params->disable_cache_hook) ? $this->config_module['disable_cache_hook'] = $params->disable_cache_hook : false;

                    $profile->params = Tools::jsonEncode($this->config_module);
                    $profile->header = $arr_id['header'];
                    $profile->content = $arr_id['content'];
                    $profile->footer = $arr_id['footer'];
                    $profile->product = $arr_id['product'];
                    $profile->save();
                    $this->confirmations[] = $this->trans('Save successful', array(), 'Admin.Notifications.Success');
                }else{
                    $this->errors[] = $this->trans('Submit data is invalid', array(), 'Admin.Notifications.Success');
                }
            }else{
                $this->errors[] = $this->trans('Not exist data_profile', array(), 'Admin.Notifications.Success');
            }
        }
        
        parent::postProcess();
    }
    
    public function ajaxProcessShowImportForm()
    {
        $id_profile = Tools::getValue('idProfile');
        $helper = new HelperForm();
        $helper->submit_action = 'submitImportData';
        $hook = array();
        $hook[] = array('id' => 'all', 'name' => $this->l('Profile'));
        $hook[] = array('id' => 'header', 'name' => $this->l('Position Header'));
        foreach (ApPageSetting::getHook('header') as $val) {
            $hook[] = array('id' => $val, 'name' => '----'.$val);
        }
        $hook[] = array('id' => 'content', 'name' => $this->l('Position Content'));
        foreach (ApPageSetting::getHook('content') as $val) {
            $hook[] = array('id' => $val, 'name' => '----'.$val);
        }
        $hook[] = array('id' => 'footer', 'name' => $this->l('Position Footer'));
        foreach (ApPageSetting::getHook('footer') as $val) {
            $hook[] = array('id' => $val, 'name' => '----'.$val);
        }
        $hook[] = array('id' => 'product', 'name' => $this->l('Position Product'));
        foreach (ApPageSetting::getHook('product') as $val) {
            $hook[] = array('id' => $val, 'name' => '----'.$val);
        }
        $inputs = array(
            array(
                'type' => 'file',
                'name' => 'importFile',
                'label' => $this->l('File'),
                'desc' => $this->l('Only accept xml file'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Import For'),
                'name' => 'import_for',
                'options' => array(
                    'query' => $hook,
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Select hook you want to import. Override all is only avail for import appagebuilderhome.xml file'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Override'),
                'name' => 'override',
                'is_bool' => true,
                'desc' => $this->l('Override current data or not.'),
                'values' => ApPageSetting::returnYesNo()
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<input type="hidden" name="id_profile" id="id_profile" value="'.$id_profile.'"/>'
            )
        );
        $fields_form = array(
            'form' => array(
                'action' => Context::getContext()->link->getAdminLink('AdminApPageBuilderHome'),
                'input' => $inputs,
//                'name' => 'importData',
                //'buttons' => array(array('title' => $this->l('Save'), 'class' => 'button btn')),
                'submit' => array('title' => $this->l('Save'), 'class' => 'button btn btn-success'),
                'tinymce' => false,
            ),
        );
        $helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
        $array = array('hasError' => false, 'result' => $helper->generateForm(array($fields_form)));
        die(Tools::jsonEncode($array));
    }
    
    /**
     * Show panel : widgets and modules
     * .group-add
     * .btn-new-widget
     */
    public function ajaxProcessRenderList()
    {
        $tpl = $this->createTemplate('shortcodelist.tpl');
        // get list module installed by hook position
        $list_modules = array();


        if (Tools::getValue('reloadModule')) {
            # ReLoad : write to config
            $list_modules = apPageHelper::getModules();

            $ap_cache_module = apPageHelper::correctEnCodeData(Tools::jsonEncode($list_modules));
            Configuration::updateValue('AP_CACHE_MODULE', $ap_cache_module);
        } else {
            $ap_cache_module = Configuration::get('AP_CACHE_MODULE');
            if ($ap_cache_module === false || $ap_cache_module === '') {
                # First Time : write to config
                $list_modules = apPageHelper::getModules();

                $ap_cache_module = apPageHelper::correctEnCodeData(Tools::jsonEncode($list_modules));
                Configuration::updateValue('AP_CACHE_MODULE', $ap_cache_module);
            } else {
                # Second Time : read from config
                $list_modules = Tools::jsonDecode(apPageHelper::correctDeCodeData($ap_cache_module), true);
            }
        }

        // Get list author
        $author = array();
        foreach ($list_modules as $mi) {
            $str = Tools::ucwords(Tools::strtolower($mi['author'] ? $mi['author'] : ''));
            if (!in_array($str, $author) && $str) {
                array_push($author, $str);
            }
        }
        //Get list of image or shortcodeFile
        $tpl->assign(array(
            'author' => $author,
            'listModule' => $list_modules,
            'shortCodeList' => ApShortCodeBase::getShortCodeInfos()
        ));
        $array = array('hasError' => false, 'result' => $tpl->fetch());
        die(Tools::jsonEncode($array));
    }
    
    public function ajaxProcessSaveData()
    {
        $type = Tools::getValue('type');
        $this->saveData('save', $type);
    }
    
    public function saveData($action, $type)
    {
        $data_form = Tools::getValue('dataForm');
        $data_form = Tools::jsonDecode($data_form, 1);
        self::$language = Language::getLanguages(false);
        $data = array();
        $arr_id = array('header' => 0, 'content' => 0, 'footer' => 0, 'product' => 0);
        foreach ($data_form as $hook) {
            $position_id = (int)isset($hook['position_id']) ? $hook['position_id'] : '0';
            $hook['position'] = (isset($hook['position']) && $hook['position']) ? $hook['position'] : '';
            $hook['name'] = (isset($hook['name']) && $hook['name']) ? $hook['name'] : 0;
            $position = Tools::strtolower($hook['position']);
            $arr_id[$position] = (isset($arr_id[$position]) && $arr_id[$position]) ? $arr_id[$position] : '';
            // Create new position with name is auto random, and save id of new for other positions reuse
            // position for other hook in this position to variable $header, $content...
            if ($position_id == 0 && $arr_id[$position] == 0) {
                //DONGND: enable save multithreading
                if (Configuration::get('APPAGEBUILDER_SAVE_MULTITHREARING')) {
                    if ((Configuration::get('APPAGEBUILDER_GLOBAL_HEADER_ID') == 0 && $position == 'header')
                        || (Configuration::get('APPAGEBUILDER_GLOBAL_CONTENT_ID') == 0 && $position == 'content')
                        || (Configuration::get('APPAGEBUILDER_GLOBAL_FOOTER_ID') == 0 && $position == 'footer')
                        || (Configuration::get('APPAGEBUILDER_GLOBAL_PRODUCT_ID') == 0 && $position == 'product')) {
                        $key = ApPageSetting::getRandomNumber();
                        $position_controller = new AdminApPageBuilderPositionsController();
                        $position_data = array('name' => $position.$key,
                            'position' => $position,
                            'position_key' => 'position'.$key);
                        $position_id = $position_controller->autoCreatePosition($position_data);
                        $arr_id[$position] = $position_id;
                        switch ($position) {
                            case 'header':
                                Configuration::updateValue('APPAGEBUILDER_GLOBAL_HEADER_ID', $position_id);
                                break;
                            case 'content':
                                Configuration::updateValue('APPAGEBUILDER_GLOBAL_CONTENT_ID', $position_id);
                                break;
                            case 'footer':
                                Configuration::updateValue('APPAGEBUILDER_GLOBAL_FOOTER_ID', $position_id);
                                break;
                            case 'product':
                                Configuration::updateValue('APPAGEBUILDER_GLOBAL_PRODUCT_ID', $position_id);
                                break;
                        }
                    } else {
                        switch ($position) {
                            case 'header':
                                $arr_id[$position] = Configuration::get('APPAGEBUILDER_GLOBAL_HEADER_ID');
                                break;
                            case 'content':
                                $arr_id[$position] = Configuration::get('APPAGEBUILDER_GLOBAL_CONTENT_ID');
                                break;
                            case 'footer':
                                $arr_id[$position] = Configuration::get('APPAGEBUILDER_GLOBAL_FOOTER_ID');
                                break;
                            case 'product':
                                $arr_id[$position] = Configuration::get('APPAGEBUILDER_GLOBAL_PRODUCT_ID');
                                break;
                        }
                    }
                } else {
                    $key = ApPageSetting::getRandomNumber();
                    $position_controller = new AdminApPageBuilderPositionsController();
                    $position_data = array('name' => $position.$key,
                            'position' => $position,
                            'position_key' => 'position'.$key);
                    $position_id = $position_controller->autoCreatePosition($position_data);
                    $arr_id[$position] = $position_id;
                }
            } else if ($position_id != 0 && $arr_id[$position] == 0) {
                //DONGND: enable save multithreading
                if (Configuration::get('APPAGEBUILDER_SAVE_MULTITHREARING')) {
                    switch ($position) {
                        case 'header':
                            Configuration::updateValue('APPAGEBUILDER_GLOBAL_HEADER_ID', $position_id);
                            break;
                        case 'content':
                            Configuration::updateValue('APPAGEBUILDER_GLOBAL_CONTENT_ID', $position_id);
                            break;
                        case 'footer':
                            Configuration::updateValue('APPAGEBUILDER_GLOBAL_FOOTER_ID', $position_id);
                            break;
                        case 'product':
                            Configuration::updateValue('APPAGEBUILDER_GLOBAL_PRODUCT_ID', $position_id);
                            break;
                    }
                };
                $arr_id[$position] = $position_id;
            }

            $obj_model = new ApPageBuilderModel();
            $obj_model->id = $obj_model->getIdbyHookName($hook['name'], $arr_id[$position]);
            $obj_model->hook_name = $hook['name'];
            $obj_model->page = 'index';
            $obj_model->id_appagebuilder_positions = $arr_id[$position];
            if (isset($hook['groups'])) {
                foreach (self::$language as $lang) {
                    $params = '';
                    if (self::$shortcode_lang) {
                        foreach (self::$shortcode_lang as &$s_type) {
                            foreach ($s_type as $key => $value) {
                                $s_type[$key] = $key.'_'.$lang['id_lang'];
                                // validate module
                                unset($value);
                            }
                        }
                    }
                    $obj_model->params[$lang['id_lang']] = '';
                    ApShortCodesBuilder::$lang_id = $lang['id_lang'];
                    foreach ($hook['groups'] as $groups) {
                        $params = $this->getParamByHook($groups, $params, $hook['name'], $action);
                    }
                    $obj_model->params[$lang['id_lang']] = $params;
                    if ($action == 'export') {
                        $data[$lang['iso_code']] = (isset($data[$lang['iso_code']]) && $data[$lang['iso_code']]) ? $data[$lang['iso_code']] : '';
                        $data[$hook['name']][$lang['iso_code']] = (isset($data[$hook['name']][$lang['iso_code']]) && $data[$hook['name']][$lang['iso_code']]) ? $data[$hook['name']][$lang['iso_code']] : '';

                        if ($type == 'all' || (strpos($type, 'position') !== false)) {
                            $data[$hook['name']][$lang['iso_code']] .= $params;
                        } else {
                            $data[$lang['iso_code']] .= $params;
                        }
                    }
                }
            }
            if ($action == 'save') {
                if ($obj_model->id) {
                    $this->clearModuleCache();
                    $obj_model->save();
                } else {
                    $this->clearModuleCache();
                    $obj_model->add();
                }
                $path = _PS_ROOT_DIR_.'/cache/smarty/cache/'.$this->module_name;
                $this->deleteDirectory($path);
            }
        };

        if ($action == 'save') {
            if (Configuration::get('APPAGEBUILDER_SAVE_MULTITHREARING')) {
                if (Tools::getValue('dataFirst')) {
                    $profile = new ApPageBuilderProfilesModel(Tools::getValue('id_profile'));

                    # Fix : must keep other data in param. ( exception + other data )
                    //print_r($this->config_module);
                    $params = Tools::jsonDecode($profile->params, true);
                    isset($params['fullwidth_index_hook']) ? $this->config_module['fullwidth_index_hook'] = $params['fullwidth_index_hook'] : false;
                    isset($params['fullwidth_other_hook']) ? $this->config_module['fullwidth_other_hook'] = $params['fullwidth_other_hook'] : false;
                    isset($params['disable_cache_hook']) ? $this->config_module['disable_cache_hook'] = $params['disable_cache_hook'] : false;
                    Configuration::updateValue('APPAGEBUILDER_GLOBAL_PROFILE_PARAM', Tools::jsonEncode($this->config_module));
                } else {
                    if (count($this->config_module) > 0) {
                        $array_global_profile_param = Tools::jsonDecode(Configuration::get('APPAGEBUILDER_GLOBAL_PROFILE_PARAM'), true);
                        $array_global_profile_param = array_merge($this->config_module, $array_global_profile_param);
                        Configuration::updateValue('APPAGEBUILDER_GLOBAL_PROFILE_PARAM', Tools::jsonEncode($array_global_profile_param));
                    }
                };
                if (Tools::getValue('dataLast')) {
                    $profile = new ApPageBuilderProfilesModel(Tools::getValue('id_profile'));
                    $params = Tools::jsonDecode($profile->params, true);
                    $profile->params = Configuration::get('APPAGEBUILDER_GLOBAL_PROFILE_PARAM');
                    $profile->header = Configuration::get('APPAGEBUILDER_GLOBAL_HEADER_ID');
                    $profile->content = Configuration::get('APPAGEBUILDER_GLOBAL_CONTENT_ID');
                    $profile->footer = Configuration::get('APPAGEBUILDER_GLOBAL_FOOTER_ID');
                    $profile->product = Configuration::get('APPAGEBUILDER_GLOBAL_PRODUCT_ID');
                    $profile->save();

                    Configuration::updateValue('APPAGEBUILDER_GLOBAL_HEADER_ID', 0);
                    Configuration::updateValue('APPAGEBUILDER_GLOBAL_CONTENT_ID', 0);
                    Configuration::updateValue('APPAGEBUILDER_GLOBAL_FOOTER_ID', 0);
                    Configuration::updateValue('APPAGEBUILDER_GLOBAL_PRODUCT_ID', 0);
                    Configuration::updateValue('APPAGEBUILDER_GLOBAL_PROFILE_PARAM', '');
                }
            } else {
                $profile = new ApPageBuilderProfilesModel(Tools::getValue('id_profile'));

                # Fix : must keep other data in param. ( exception + other data )
                //print_r($this->config_module);
                $params = Tools::jsonDecode($profile->params);
                isset($params->fullwidth_index_hook) ? $this->config_module['fullwidth_index_hook'] = $params->fullwidth_index_hook : false;
                isset($params->fullwidth_other_hook) ? $this->config_module['fullwidth_other_hook'] = $params->fullwidth_other_hook : false;
                isset($params->disable_cache_hook) ? $this->config_module['disable_cache_hook'] = $params->disable_cache_hook : false;

                $profile->params = Tools::jsonEncode($this->config_module);
                $profile->header = $arr_id['header'];
                $profile->content = $arr_id['content'];
                $profile->footer = $arr_id['footer'];
                $profile->product = $arr_id['product'];
                $profile->save();
            };
        };
        return $data;
    }

    public function renderList()
    {
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->context->controller->addCss(apPageHelper::getCssAdminDir().'admin/form.css');
        $this->context->controller->addCss(apPageHelper::getCssAdminDir().'animate.css');
        $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/form.js');
        $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/home.js');
        $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/isotope.pkgd.min.js');
        $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');

        $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/jquery-validation-1.9.0/jquery.validate.js');
        $this->context->controller->addCss(apPageHelper::getJsAdminDir().'admin/jquery-validation-1.9.0/screen.css');

//        $version = Configuration::get('PS_INSTALL_VERSION');
//        $tiny_path = ($version >= '1.6.0.13') ? 'admin/' : '';
//        $tiny_path .= 'tinymce.inc.js';

        // Pham_Khanh_Dong fix loading TINY_MCE library for all Prestashop_Versions
        $tiny_path = 'tinymce.inc.js';
        if (version_compare(_PS_VERSION_, '1.6.0.13', '>')) {
            $tiny_path = 'admin/tinymce.inc.js';
        }

        $this->context->controller->addJS(_PS_JS_DIR_.$tiny_path);
        $bo_theme = ((Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');
        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR.'template')) {
            $bo_theme = 'default';
        }
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
        $this->context->controller->addJs(_PS_JS_DIR_.'vendor/spin.js');
        $this->context->controller->addJs(_PS_JS_DIR_.'vendor/ladda.js');
        //load javascript for menu tree, Product Carousel widget
        $tree = new HelperTreeCategories('123', null);
        $tree->render();
        
        $model = new ApPageBuilderModel();
        $id_profile = Tools::getValue('id_appagebuilder_profiles');
        if (!$id_profile) {
            $result_profile = ApPageBuilderProfilesModel::getActiveProfile('index');
            //if empty default profile redirect to other
            if (!$result_profile) {
                $this->redirect_after = Context::getContext()->link->getAdminLink('AdminApPageBuilderProfiles');
                $this->redirect();
            }
            $id_profile = $result_profile['id_appagebuilder_profiles'];
        } else {
            $profile_obj = new ApPageBuilderProfilesModel($id_profile);
            if ($profile_obj->id) {
                $result_profile['id_appagebuilder_profiles'] = $profile_obj->id;
                $result_profile['name'] = $profile_obj->name;
                $result_profile['header'] = $profile_obj->header;
                $result_profile['content'] = $profile_obj->content;
                $result_profile['footer'] = $profile_obj->footer;
                $result_profile['product'] = $profile_obj->product;
                $result_profile['page'] = $profile_obj->page;
            }
        }
        if (isset($result_profile) && $result_profile) {
            $positions_dum = array();
            // Get default config - data of current position
            $positions_dum['header'] = $result_profile['header'] ? $model->getAllItemsByPosition('header', $result_profile['header'], $id_profile) : array('content' => $this->extractHookDefault(Configuration::get('APPAGEBUILDER_HEADER_HOOK')), 'dataForm' => array());
            $positions_dum['content'] = $result_profile['content'] ? $model->getAllItemsByPosition('content', $result_profile['content'], $id_profile) : array('content' => $this->extractHookDefault(Configuration::get('APPAGEBUILDER_CONTENT_HOOK')), 'dataForm' => array());
            $positions_dum['footer'] = $result_profile['footer'] ? $model->getAllItemsByPosition('footer', $result_profile['footer'], $id_profile) : array('content' => $this->extractHookDefault(Configuration::get('APPAGEBUILDER_FOOTER_HOOK')), 'dataForm' => array());
            $positions_dum['product'] = $result_profile['product'] ? $model->getAllItemsByPosition('product', $result_profile['product'], $id_profile) : array('content' => $this->extractHookDefault(Configuration::get('APPAGEBUILDER_PRODUCT_HOOK')), 'dataForm' => array());
            // Extract for display
            $positions = array();
            $position_data_form = array();
            foreach ($positions_dum as $key => $val) {
                $temp = $val['content'];
                $position_data_form[$key] = Tools::jsonEncode($val['dataForm']);
                foreach ($temp as $key_hook => &$row) {
                    if (!is_array($row)) {
                        $row = array('hook_name' => $key_hook, 'content' => '');
                    }
                    if ($key_hook == 'displayLeftColumn' || $key_hook == 'displayRightColumn') {
                        $row['class'] = 'col-md-3';
                    } else {
                        $row['class'] = 'col-md-12';
                    }
                }
                $positions[$key] = $temp;
            }
            
            // Get list position for dropdowns
            $list_positions = array();
            $list_positions['header'] = $model->getListPositisionByType('header', $this->context->shop->id);
            $list_positions['content'] = $model->getListPositisionByType('content', $this->context->shop->id);
            $list_positions['footer'] = $model->getListPositisionByType('footer', $this->context->shop->id);
            $list_positions['product'] = $model->getListPositisionByType('product', $this->context->shop->id);
            // Get current position name

            $current_position = array();
            $current_position['header'] = $this->getCurrentPosition($list_positions['header'], $result_profile['header']);
            $current_position['content'] = $this->getCurrentPosition($list_positions['content'], $result_profile['content']);
            $current_position['footer'] = $this->getCurrentPosition($list_positions['footer'], $result_profile['footer']);
            $current_position['product'] = $this->getCurrentPosition($list_positions['product'], $result_profile['product']);
            $data_by_hook = array();
            $data_form = '{}';
            $data = $model->getAllItems($result_profile);

            if ($data) {
                $data_by_hook = $data['content'];
                $data_form = Tools::jsonEncode($data['dataForm']);

                foreach ($data_by_hook as $key_hook => &$row) {
                    if (!is_array($row)) {
                        $row = array('hook_name' => $key_hook, 'content' => '');
                    }
                    if ($key_hook == 'displayLeftColumn' || $key_hook == 'displayRightColumn') {
                        $row['class'] = 'col-md-3';
                    } else {
                        $row['class'] = 'col-md-12';
                    }
                }
            }

            // Get list item for dropdown export
            $export_items = array();
            $export_items['Header'] = ApPageSetting::getHook('header');
            $export_items['Content'] = ApPageSetting::getHook('content');
            $export_items['Footer'] = ApPageSetting::getHook('footer');
            $export_items['Product'] = ApPageSetting::getHook('product');
            // get shortcode information
            $shortcode_infos = ApShortCodeBase::getShortCodeInfos();
            //include all short code default
            $shortcodes = Tools::scandir($this->tpl_path.'/ap_page_builder_shortcodes', 'tpl');
            $shortcode_form = array();
            foreach ($shortcodes as $s_from) {
                if ($s_from == 'shortcodelist.tpl') {
                    continue;
                }
                $shortcode_form[] = $this->tpl_path.'/ap_page_builder_shortcodes/'.$s_from;
            }
            
            // ROOT//modules/appagebuilder/views/templates/admin/ap_page_builder_home/home.tpl
            $tpl = $this->createTemplate('home.tpl');
            
            $languages = array();
            foreach (Language::getLanguages(false) as $lang) {
                $languages[$lang['iso_code']] = $lang['id_lang'];
            }
            //DONGND: check enable save multithreading
            if (Configuration::get('APPAGEBUILDER_SAVE_MULTITHREARING')) {
                $check_save_multithreading = 1;
            } else {
                $check_save_multithreading = 0;
            };
            
            //DONGND: check enable save submit
            if (Configuration::get('APPAGEBUILDER_SAVE_SUBMIT')) {
                $check_save_submit = 1;
            } else {
                $check_save_submit = 0;
            };
            
            //DONGND: error when submit
            $errorSubmit = '';
            if (Tools::isSubmit('errorSubmit')) {
                $errorSubmit = $this->l('There was an error during save. Please try again and check the value of server config: max_input_vars, make sure it is greater than 30000');
            }
            
            $tpl->assign(array(
                'positions' => $positions,
                'listPositions' => $list_positions,
                //'positionDataForm' => $position_data_form,
                'dataByHook' => $data_by_hook,
                'exportItems' => $export_items,
                'currentProfile' => $result_profile,
                'currentPosition' => $current_position,
                'profilesList' => $this->getAllProfiles($result_profile['id_appagebuilder_profiles']),
                'tplPath' => $this->tpl_path,
                'ajaxShortCodeUrl' => Context::getContext()->link->getAdminLink('AdminApPageBuilderShortcodes'),
                'ajaxHomeUrl' => Context::getContext()->link->getAdminLink('AdminApPageBuilderHome'),
                'shortcodeForm' => $shortcode_form,
                'moduleDir' => _MODULE_DIR_,
                // Not run with multi_shop (ex block carousel cant get image in backend multi_shop)
//                'imgModuleLink' => _THEME_IMG_DIR_.'modules/'.$this->module_name.'/images/',
                'imgModuleLink' => apPageHelper::getImgThemeUrl(),
                'shortcodeInfos' => Tools::jsonEncode($shortcode_infos),
                'languages' => Tools::jsonEncode($languages),
                'dataForm' => $data_form,
                'errorText' => $this->error_text,
                'imgController' => Context::getContext()->link->getAdminLink('AdminApPageBuilderImages'),
                'widthList' => ApPageSetting::returnWidthList(),
                'lang_id' => (int)$this->context->language->id,
                'idProfile' => $id_profile,
                'checkSaveMultithreading' => $check_save_multithreading,
                'checkSaveSubmit' => $check_save_submit,
                'errorSubmit' => $errorSubmit,
                'listAnimation' => ApPageSetting::getAnimationsColumnGroup(),
                
            ));
            $path_guide = $this->getTemplatePath().'guide.tpl';
            $guide_box = ApPageSetting::buildGuide($this->context, $path_guide, 3);
            return $guide_box.$tpl->fetch();
        } else {
            $this->errors[] = $this->l('Your Profile ID is not exist!');
        }
    }
    
    private function exportData()
    {
        $action = Tools::getValue('action');
        $type = Tools::getValue('type');
        
        $data = $this->saveData($action, $type);
        if ($data) {
            if ($type == 'all') {
                $this->file_content = '<module>';
                foreach ($data as $key => $hook) {
                    $this->file_content .= '<'.$key.'>';
                    if (is_string($hook)) {
                        $hook = array();
                    }
                    foreach ($hook as $lang => $group) {
                        $this->file_content .= '<'.$lang.'>';
                        $this->file_content .= '<![CDATA['.$group.']]>';
                        $this->file_content .= '</'.$lang.'>';
                    }
                    $this->file_content .= '</'.$key.'>';
                }
                $this->file_content .= '</module>';
            } else if (strpos($type, 'position') !== false) {
                // Export position
                $this->file_content = '<'.'position'.'>';
                foreach ($data as $key => $hook) {
                    $this->file_content .= '<'.$key.'>';
                    if (is_string($hook)) {
                        $hook = array();
                    }
                    foreach ($hook as $lang => $group) {
                        $this->file_content .= '<'.$lang.'>';
                        $this->file_content .= '<![CDATA['.$group.']]>';
                        $this->file_content .= '</'.$lang.'>';
                    }
                    $this->file_content .= '</'.$key.'>';
                }
                $this->file_content .= '</position>';
            } else if ($type == 'group') {
                //export group
                foreach ($data as $lang => $group) {
                    if (is_string($group)) {
                        $this->file_content .= '<'.$lang.'>';
                        $this->file_content .= '<![CDATA['.$group.']]>';
                        $this->file_content .= '</'.$lang.'>';
                    }
                }
            } else {
                //export all group in hook
                foreach ($data as $lang => $group) {
                    if (is_string($group)) {
                        $this->file_content .= '<'.$lang.'>';
                        $this->file_content .= '<![CDATA['.$group.']]>';
                        $this->file_content .= '</'.$lang.'>';
                    }
                }
            }
            $href = $this->createXmlFile($type);
            $array = array('hasError' => false, 'result' => $href);
            die(Tools::jsonEncode($array));
        }
    }
            
    private function importData($language, $lang_id)
    {
        $upload_file = new Uploader('importFile');
        $upload_file->setAcceptTypes(array('xml'));
        $file = $upload_file->process();
        $file = $file[0];
        $files_content = simplexml_load_file($file['save_path']);
        $hook_list = array();
        $hook_list = array_merge($hook_list, explode(',', Configuration::get('APPAGEBUILDER_HEADER_HOOK')));
        $hook_list = array_merge($hook_list, explode(',', Configuration::get('APPAGEBUILDER_CONTENT_HOOK')));
        $hook_list = array_merge($hook_list, explode(',', Configuration::get('APPAGEBUILDER_FOOTER_HOOK')));
        $hook_list = array_merge($hook_list, explode(',', Configuration::get('APPAGEBUILDER_PRODUCT_HOOK')));
        $import_for = Tools::getValue('import_for');
        $override = Tools::getValue('override');
        self::$language = Language::getLanguages(false);
        $id_profile = Tools::getValue('id_profile');
        $profile = new ApPageBuilderProfilesModel($id_profile);
        if (!$profile->id || !$profile->header || !$profile->content || !$profile->footer || !$profile->product) {
            // validate module
            die('Pease click save Profile before run import function. click back to try again!');
        }

        $lang_iso = 'en';
        $lang_list = array();
        foreach ($language as $lang) {
            $lang_list[$lang['iso_code']] = $lang['id_lang'];
            if ($lang['id_lang'] == $lang_id) {
                $lang_iso = $lang['iso_code'];
            }
        }
        // Import all mdoule
        if (isset($files_content->module)) {
            if ($import_for != 'all') {
                $this->errors[] = $this->trans('That is not the file for module, please select other file.', array(), 'Admin.Notifications.Error');
                return 'ERORR_ALL';
            }
            $module = $files_content->module;
            foreach ($hook_list as $hook) {
                $import_hook = $module->{$hook};
                $model = new ApPageBuilderModel();
                foreach ($language as $lang) {
                    $obj = $model->getIdbyHookNameAndProfile($hook, $profile, $lang_list[$lang['iso_code']]);
                    if ($override) {
                        $params = apPageHelper::replaceFormId($import_hook->{$lang['iso_code']});
                    } else {
                        $params = $obj['params'];
                        $params .= apPageHelper::replaceFormId($import_hook->{$lang['iso_code']});
                    }
                    $model->updateAppagebuilderLang($obj['id_appagebuilder'], $lang_list[$lang['iso_code']], $params);
                }
            }
        } else if (isset($files_content->position)) {
            // Import a position
            $arr_positions = array('header', 'content', 'footer', 'product');
            if (!in_array($import_for, $arr_positions)) {
                $this->errors[] = $this->trans('That is not file for position, please select import for positon: header or content or footer or product', array(), 'Admin.Notifications.Error');
                return 'ERORR_POSITION';
            }
            $position = $files_content->position;
            $hook_name = '';
            if ($import_for == 'header') {
                $hook_name = 'APPAGEBUILDER_HEADER_HOOK';
            } else if ($import_for == 'content') {
                $hook_name = 'APPAGEBUILDER_CONTENT_HOOK';
            } else if ($import_for == 'footer') {
                $hook_name = 'APPAGEBUILDER_FOOTER_HOOK';
            } else if ($import_for == 'product') {
                $hook_name = 'APPAGEBUILDER_PRODUCT_HOOK';
            }
            $hook_list = explode(',', Configuration::get($hook_name));
            foreach ($hook_list as $hook) {
                $import_hook = $position->{$hook};
                $model = new ApPageBuilderModel();
                foreach ($language as $lang) {
                    $obj = $model->getIdbyHookNameAndProfile($hook, $profile, $lang_list[$lang['iso_code']]);
                    if ($override) {
                        $params = apPageHelper::replaceFormId($import_hook->{$lang['iso_code']});
                    } else {
                        $params = $obj['params'];
                        $params .= apPageHelper::replaceFormId($import_hook->{$lang['iso_code']});
                    }
                    $model->updateAppagebuilderLang($obj['id_appagebuilder'], $lang_list[$lang['iso_code']], $params);
                }
            }
        } else {
            // Import only for a group - a hook
            $arr_positions = array('header', 'content', 'footer', 'product');
            if ($import_for == 'all' || in_array($import_for, $arr_positions)) {
                $this->errors[] = $this->trans('That is not file for module, please select other file.', array(), 'Admin.Notifications.Error');
                return 'ERORR_NOT_ALL';
            }
            $import_hook = $import_for;
            $hook = $import_for;
            foreach ($language as $lang) {
                $model = new ApPageBuilderModel();
                $obj = $model->getIdbyHookNameAndProfile($hook, $profile, $lang_list[$lang['iso_code']]);
                if ($override) {
                    $params = apPageHelper::replaceFormId($files_content->{$lang['iso_code']});
                } else {
                    $params = $obj['params'];
                    $params .= apPageHelper::replaceFormId($files_content->{$lang['iso_code']});
                }
                $model->updateAppagebuilderLang($obj['id_appagebuilder'], $lang_list[$lang['iso_code']], $params);
            }
        }
        // validate module
        unset($lang_iso);
        $this->confirmations[] = $this->trans('Import Success', array(), 'Admin.Notifications.Success');
        return 'ok';
    }

    public function extractHookDefault($str_hook = '')
    {
        $result = array();
        if ($str_hook) {
            $arr = explode(',', $str_hook);
            $len = count($arr);
            for ($i = 0; $i < $len; $i++) {
                $result[$arr[$i]] = $i;
            }
        }
        return $result;
    }

    public function getAllProfiles($id)
    {
        $current_id = Tools::getValue('id_appagebuilder_profiles');
        $profile_obj = new ApPageBuilderProfilesModel($current_id);
        return $profile_obj->getProfilesInPage($id);
    }

    /**
     * Get template a position
     */
    public function selectPosition($id = '')
    {
        $position = Tools::getValue('position');
        $id_position = $id ? $id : (int)Tools::getValue('id');
        $id_duplicate = (int)Tools::getValue('is_duplicate');
        $content = '';
        $tpl_name = 'position.tpl';
        $path = '';

        if (file_exists($this->theme_dir.'modules/'.$this->module->name.'/views/templates/admin/'.$tpl_name)) {
            $path = $this->theme_dir.'modules/'.$this->module->name.'/views/templates/admin/'.$tpl_name;
        } elseif (file_exists($this->getTemplatePath().$this->override_folder.$tpl_name)) {
            $path = $this->getTemplatePath().$this->override_folder.$tpl_name;
        }
        $model = new ApPageBuilderModel();
        $positions_dum = $id_position ?
                $model->getAllItemsByPosition($position, $id_position) :
                array('content' => $this->extractHookDefault(Configuration::get('APPAGEBUILDER_' . Tools::strtoupper($position).'_HOOK')), 'dataForm' => array());
        $list_positions = $model->getListPositisionByType(Tools::strtolower($position), $this->context->shop->id);
        $current_position = $this->getCurrentPosition($list_positions, $id_position);

        foreach ($positions_dum['content'] as $key_hook => &$row) {
            if (!is_array($row)) {
                $row = array('hook_name' => $key_hook, 'content' => '');
            }
            if ($key_hook == 'displayLeftColumn' || $key_hook == 'displayRightColumn') {
                $row['class'] = 'col-md-3';
            } else {
                $row['class'] = 'col-md-12';
            }
        }
        $positions = $positions_dum['content'];
        $data_form = Tools::jsonEncode($positions_dum['dataForm']);
        $id_position = $id_duplicate ? 0 : $id_position;
        $this->context->smarty->assign(array(
            'default' => $current_position,
            'position' => $position,
            'listPositions' => $list_positions,
            'config' => $positions,
        ));
        $content = $this->context->smarty->fetch($path);
        $result = array('status' => 'SUCCESS', 'message' => '', 'html' => $content,
            'position' => $position, 'id' => $id_position, 'data' => $data_form);

        die(Tools::jsonEncode($result));
        // Check this position is using by other profile
    }

    /**
     * Process: add, update, duplicate a position
     */
    public function processPosition()
    {
        $name = Tools::getValue('name');
        $position = Tools::getValue('position');
        $id_position = (int)Tools::getValue('id');
        $mode = Tools::getValue('mode');
        if ($mode == 'duplicate') {
            $adapter = new AdminApPageBuilderPositionsController();
            $id_position = $adapter->duplicatePosition($id_position, 'ajax', $name);
        } else if ($mode == 'new') {
            $key = ApPageSetting::getRandomNumber();
            $name = $name ? $name : $position.$key;
            $position_controller = new AdminApPageBuilderPositionsController();

            $position_data = array(
                'name' => $name,
                'position' => $position,
                'position_key' => 'position'.$key,
            );
            $id_position = $position_controller->autoCreatePosition($position_data);
        } else if ($mode == 'edit') {
            // Edit only name
            $position_controller = new AdminApPageBuilderPositionsController();
            $position_controller->updateName($id_position, $name);
        }
        // Reload position
        if ($mode == 'new' || $mode == 'duplicate') {
            $this->selectPosition($id_position);
        } else {
            die(Tools::jsonEncode(array('status' => 'SUCCESS')));
        }
    }

    private function getCurrentPosition($list, $id)
    {
        if ($list) {
            foreach ($list as $item) {
                if (isset($item['id_appagebuilder_positions']) && $item['id_appagebuilder_positions'] == $id) {
                    return array('id' => $id, 'name' => $item['name']);
                }
            }
        }
        return array('id' => '0', 'name' => '');
    }
    
    private function getParamByHook($groups, $params, $hook, $action = 'save')
    {
        $groups['params']['specific_type'] = (isset($groups['params']['specific_type']) && $groups['params']['specific_type']) ? $groups['params']['specific_type'] : '';
        $groups['params']['controller_pages'] = (isset($groups['params']['controller_pages']) && $groups['params']['controller_pages']) ? $groups['params']['controller_pages'] : '';
        $groups['params']['controller_id'] = (isset($groups['params']['controller_id']) && $groups['params']['controller_id']) ? $groups['params']['controller_id'] : '';
        $params .= '[ApRow'.ApShortCodesBuilder::converParamToAttr($groups['params'], 'ApRow', $this->theme_dir).']';
        //check exception page
        $this->saveExceptionConfig($hook, $groups['params']['specific_type'], $groups['params']['controller_pages'], $groups['params']['controller_id']);
        foreach ($groups['columns'] as $columns) {
            $columns['params']['specific_type'] = (isset($columns['params']['specific_type']) && $columns['params']['specific_type']) ? $columns['params']['specific_type'] : '';
            $columns['params']['controller_pages'] = (isset($columns['params']['controller_pages']) && $columns['params']['controller_pages']) ? $columns['params']['controller_pages'] : '';
            $columns['params']['controller_id'] = (isset($columns['params']['controller_id']) && $columns['params']['controller_id']) ? $columns['params']['controller_id'] : '';
            $this->saveExceptionConfig($hook, $columns['params']['specific_type'], $columns['params']['controller_pages'], $columns['params']['controller_id']);
            $params .= '[ApColumn'.ApShortCodesBuilder::converParamToAttr($columns['params'], 'ApColumn', $this->theme_dir).']';
            foreach ($columns['widgets'] as $widgets) {
                if ($widgets['type'] == 'ApTabs' || $widgets['type'] == 'ApAccordions') {
                    $params .= '['.$widgets['type'].ApShortCodesBuilder::converParamToAttr($widgets['params'], $widgets['type'], $this->theme_dir).']';
                    foreach ($widgets['widgets'] as $sub_widgets) {
                        $type_sub = Tools::substr($widgets['type'], 0, -1);
                        $params .= '['.$type_sub.ApShortCodesBuilder::converParamToAttr($sub_widgets['params'], str_replace('_', '_sub_', $widgets['type']), $this->theme_dir).']';
                        foreach ($sub_widgets['widgets'] as $sub_widget) {
                            $params .= '['.$sub_widget['type']
                                    .ApShortCodesBuilder::converParamToAttr($sub_widget['params'], $sub_widget['type'], $this->theme_dir).'][/'
                                    .$sub_widget['type'].']';
                        }
                        $params .= '[/'.$type_sub.']';
                    }
                    $params .= '[/'.$widgets['type'].']';
                } else {
                    $params .= '['.$widgets['type'].ApShortCodesBuilder::converParamToAttr($widgets['params'], $widgets['type'], $this->theme_dir).'][/'.$widgets['type'].']';
                    if ($widgets['type'] == 'ApModule' && $action == 'save') {
                        $is_delete = (int)$widgets['params']['is_display'];
                        if ($is_delete) {
                            if (!isset($widgets['params']['hook'])) {
                                // FIX : Module not choose hook -> error
                                $widgets['params']['hook'] = '';
                            }
                            $this->deleteModuleFromHook($widgets['params']['hook'], $widgets['params']['name_module']);
                        }
                    } else if ($widgets['type'] == 'ApProductCarousel') {
                        if ($widgets['params']['order_way'] == 'random') {
                            $this->config_module[$hook]['productCarousel']['order_way'] = 'random';
                        }
                    }
                }
            }
            $params .= '[/ApColumn]';
        }
        $params .= '[/ApRow]';
        return $params;
    }
    
    public function clearModuleCache()
    {
        $module = APPageBuilder::getInstance();
        $module->clearHookCache();
    }
    
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($dir.'/'.$item)) {
                chmod($dir.'/'.$item, 0777);
                if (!$this->deleteDirectory($dir.'/'.$item)) {
                    return false;
                }
            }
        }
        return rmdir($dir);
    }
    
    private function deleteModuleFromHook($hook_name, $module_name)
    {
        $res = true;
        $sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
                WHERE `id_hook` IN( 
                    SELECT `id_hook` FROM `'._DB_PREFIX_.'hook`
                    WHERE name ="'.pSQL($hook_name).'") AND `id_module` IN( SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE name ="'.pSQL($module_name).'")';
        $res &= Db::getInstance()->execute($sql);
        return $res;
    }
    
    private function saveExceptionConfig($hook, $type, $page, $ids)
    {
        if (!$type) {
            return;
        }

        if ($type == 'all') {
            if ($type != '') {
                $list = explode(',', $page);
                foreach ($list as $val) {
                    $val = trim($val);
                    if ($val && (!is_array($this->config_module) || !isset($this->config_module[$hook]) || !isset($this->config_module[$hook]['exception']) || !isset($val, $this->config_module[$hook]['exception']))) {
                        $this->config_module[$hook]['exception'][] = $val;
                    }
                }
            }
        } else {
            $this->config_module[$hook][$type] = array();
            if ($type != 'index') {
                $ids = explode(',', $ids);
                foreach ($ids as $val) {
                    $val = trim($val);
                    if (!in_array($val, $this->config_module[$hook][$type])) {
                        $this->config_module[$hook][$type][] = $val;
                    }
                }
            }
        }
    }
    
    public function createXmlFile($title)
    {
        $file_content = '<?xml version="1.0" encoding="UTF-8"?>';
        $file_content .= '<data>';
        $file_content .= $this->file_content;
        $file_content .= '</data>';
        //save file content to sample data

        $folder = $this->theme_dir.'export/';
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        if ($title == 'all') {
            $title = 'appagebuilder';
        }

        ApPageSetting::writeFile($folder, $title.'.xml', $file_content);

        return _THEMES_DIR_._THEME_NAME_.'/'.'export/'.$title.'.xml';
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     * classes\controller\AdminController.php
     */
    public function getTabSlug()
    {
        if (empty($this->tabSlug)) {
            
            // GET RULE FOLLOW AdminApPageBuilderProfiles
            $result = Db::getInstance()->getRow('
                SELECT `id_tab`
                FROM `'._DB_PREFIX_.'tab`
                WHERE UCASE(`class_name`) = "'.'AdminApPageBuilderProfiles'.'"
            ');
            $profile_id = $result['id_tab'];
            $this->tabSlug = Access::findSlugByIdTab($profile_id);
        }

        return $this->tabSlug;
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        if (count($this->errors) <= 0) {
            
            if (!$this->access('edit')){
                if (Tools::isSubmit('submitSaveAndStay')) {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }elseif (Tools::isSubmit('submitImportData')) {
                    $this->errors[] = $this->trans('You do not have permission to import.', array(), 'Admin.Notifications.Error');
                }elseif( Tools::getIsset('action') && Tools::getValue('action') == 'export'){
                    $this->errors[] = $this->trans('You do not have permission to export this.', array(), 'Admin.Notifications.Error');
                }
                
            }
                    
            if (!$this->access('edit') && $this->ajax){
                if(Tools::getValue('action') == 'showImportForm')
                {
                    $this->errors[] = $this->trans('You do not have permission to import.', array(), 'Admin.Notifications.Error');
                } elseif(Tools::getValue('action') == 'renderList')
                {
                    // Allow
                }else{
                    # DEFAULT
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
