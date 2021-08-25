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

require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageBuilderProductsModel.php');

class AdminApPageBuilderProductsController extends ModuleAdminControllerCore
{
    private $theme_name = '';
    public $module_name = 'appagebuilder';
    public $tpl_save = '';
    public $file_content = array();
    public $explicit_select;
    public $order_by;
    public $order_way;
    public $profile_css_folder;
    public $module_path;
//    public $module_path_resource;
    public $str_search = array();
    public $str_relace = array();
    public $theme_dir;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'appagebuilder_products';
        $this->className = 'ApPageBuilderProductsModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->_join = '
            INNER JOIN `'._DB_PREFIX_.'appagebuilder_products_shop` ps ON (ps.`id_appagebuilder_products` = a.`id_appagebuilder_products`)';
        $this->_select .= ' ps.active as active, ';

        $this->order_by = 'id_appagebuilder_products';
        $this->order_way = 'DESC';
        parent::__construct();
        $this->fields_list = array(
            'id_appagebuilder_products' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 50,
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!name'
            ),
            'plist_key' => array(
                'title' => $this->l('Product List Key'),
                'filter_key' => 'a!plist_key',
                'type' => 'text',
                'width' => 140,
            ),
            'class' => array(
                'title' => $this->l('Class'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!class',
                'orderby' => false
            ),
            'active' => array(
                'title' => $this->l('Is Default'),
                'active' => 'status',
                'filter_key' => 'ps!active',
                'align' => 'text-center',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
        $this->theme_dir = _PS_THEME_DIR_;

        $this->_where = ' AND ps.id_shop='.(int)$this->context->shop->id;
        $this->theme_name = _THEME_NAME_;
        $this->profile_css_folder = $this->theme_dir.'modules/'.$this->module_name.'/';
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
//        $this->module_path_resource = $this->module_path.'views/';
        $this->str_search = array('_APAMP_', '_APQUOT_', '_APAPOST_', '_APTAB_', '_APNEWLINE_', '_APENTER_', '_APOBRACKET_', '_APCBRACKET_', '_APOCBRACKET_', '_APCCBRACKET_');
        $this->str_relace = array('&', '\"', '\'', '\t', '\r', '\n', '[', ']', '{', '}');
    }

    public function initToolbar()
    {
        parent::initToolbar();
        
        # SAVE AND STAY
        if($this->display == 'add' || $this->display == 'edit'){
            $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/function.js');

            $this->page_header_toolbar_btn['SaveAndStay'] = array(
                'href' => 'javascript:void(0);',
                'desc' => $this->l('Save and stay'),
                'js' => 'TopSaveAndStay()',
                'icon' => 'process-icon-save',
            );
            Media::addJsDef(array('TopSaveAndStay_Name' => 'submitAdd'.$this->table.'AndStay'));
            
            $this->page_header_toolbar_btn['Save'] = array(
                'href' => 'javascript:void(0);',
                'desc' => $this->l('Save'),
                'js' => 'TopSave()',
                'icon' => 'process-icon-save',
            );
            Media::addJsDef(array('TopSave_Name' => 'submitAdd'.$this->table));
        }
        
        # SHOW LINK EXPORT ALL FOR TOOLBAR
        switch ($this->display) {
            default:
                $this->toolbar_btn['new'] = array(
                    'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                    'desc' => $this->l('Add new'),
                    'class' => 'btn_add_new',
                );
                if (!$this->display && $this->can_import) {
                    $this->toolbar_btn['import'] = array(
                        'href' => self::$currentIndex . '&import' . $this->table . '&token=' . $this->token,
                        'desc' => $this->trans('Import', array(), 'Admin.Actions'),
                        'class' => 'btn_xml_import',
                    );
                }
                if ($this->allow_export) {
                    $this->toolbar_btn['export'] = array(
                        'href' => self::$currentIndex . '&export' . $this->table . '&token=' . $this->token,
                        'desc' => $this->l('Export'),
                        'class' => 'btn_xml_export',
                    );
                    Media::addJsDef(array('record_id' => 'appagebuilder_productsBox[]'));
                }
        }
    }
    
    /**
     * OVERRIDE CORE
     */
    public function processExport($text_delimiter = '"')
    {
        $multilang = false;
        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
            $multilang = $definition['multilang'];
        }

        $record_id = Tools::getValue('record_id');
        $file_name = 'ap_product_list_all.xml';
        # VALIDATE MODULE
        unset($text_delimiter);
        
        if($record_id){
            $record_id_str = implode(", ", $record_id);
            $this->_where = ' AND a.'.$this->identifier.' IN ( '.pSQL($record_id_str).' )';
            $file_name = 'ap_product_list.xml';
        }

        $this->getList($this->context->language->id, null, null, 0, false);
        if (!count($this->_list)) {
            return;
        }

        $data = $this->_list;
        
        $data_all = array();
        foreach (Language::getLanguages() as $key => $lang) {
            $this->getList($lang['id_lang'], null, null, 0, false);
            $data_all[$lang['iso_code']] = $this->_list;
        }
        
        $this->file_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $this->file_content .= '<data>' . "\n";
        $this->file_content .= '<product_list>' . "\n";
        
        if ($data) {
            foreach ($data as $product_detail) {
                $this->file_content .= '<record>' . "\n";
                foreach ($product_detail as $key => $value) {
                    if(isset($definition['fields'][$key]['lang']) && $definition['fields'][$key]['lang'])
                    {
                        # MULTI LANG
                        $this->file_content .= '    <'.$key.'>'. "\n";
                        foreach (Language::getLanguages() as $key_lang => $lang) {
                            $this->file_content .= '        <'.$lang['iso_code'].'>';
                            $this->file_content .= '<![CDATA['.$value.']]>';
                            $this->file_content .= '</'.$lang['iso_code'].'>' . "\n";
                        }
                        $this->file_content .= '    </'.$key.'>' . "\n";
                    }else{
                        # SINGLE LANG
                        $this->file_content .= '    <'.$key.'>';
                        $this->file_content .= '<![CDATA['.$value.']]>';
                        $this->file_content .= '</'.$key.'>' . "\n";
                    }
                }
                $this->file_content .= '</record>' . "\n";
            }
        }
        $this->file_content .= '</product_list>' . "\n";
        $this->file_content .= '</data>' . "\n";
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        echo $this->file_content;
        die();
    }
    
    public function processImport()
    {
        $upload_file = new Uploader('importFile');
        $upload_file->setAcceptTypes(array('xml'));
        $file = $upload_file->process();
        $file = $file[0];
        if( !isset($file['save_path']))
        {
            $this->errors[]        = $this->trans('Failed to import.', array(), 'Admin.Notifications.Error');
            return;
        }
        $files_content = simplexml_load_file($file['save_path']);
        $override = Tools::getValue('override');
        
        if (isset($files_content->product_list) && $files_content->product_list)
        {
            foreach ($files_content->product_list->children() as $product_details) {
                if ($override) {
                    
                }else{
                    $obj_model = new ApPageBuilderProductsModel();
                    $obj_model->plist_key = 'plist'.ApPageSetting::getRandomNumber();
                    $obj_model->name = $product_details->name->__toString();
                    $obj_model->class = $product_details->class->__toString();
                    $obj_model->params = $product_details->params->__toString();
                    $obj_model->type = $product_details->type->__toString();
                    $obj_model->active = 0;

                    if ($obj_model->save()) {
                        $this->saveTplFile($obj_model->plist_key, $obj_model->params);
                    }
                }
            }
            $this->confirmations[] = $this->trans('Successful importing.', array(), 'Admin.Notifications.Success');
        }else{
            $this->errors[]        = $this->trans('Wrong file to import.', array(), 'Admin.Notifications.Error');
        }
    }

    public function renderView()
    {
        $object = $this->loadObject();
        if ($object->page == 'product_detail') {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminApPageBuilderProductDetail');
        } else {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminApPageBuilderHome');
        }
        $this->redirect_after .= '&id_appagebuilder_products='.$object->id;
        $this->redirect();
    }

    public function postProcess()
    {
        parent::postProcess();
        
        if (count($this->errors) > 0) {
            return;
        }
        
        if (Tools::getIsset('duplicateappagebuilder_products')) {
            $id = Tools::getValue('id_appagebuilder_products');
            $model = new ApPageBuilderProductsModel($id);
            $duplicate_object = $model->duplicateObject();
            if(isset($model->params)){
                # FIX : insert code can not duplicate
                $duplicate_object->params = $model->params;
            }
            $duplicate_object->name = $this->l('Duplicate of').' '.$duplicate_object->name;
            $old_key = $duplicate_object->plist_key;
            $duplicate_object->plist_key = 'plist'.ApPageSetting::getRandomNumber();
            if ($duplicate_object->add()) {
                //duplicate shortCode
                $filecontent = Tools::file_get_contents(apPageHelper::getConfigDir('theme_profiles').$old_key.'.tpl');
                ApPageSetting::writeFile(apPageHelper::getConfigDir('theme_profiles'), $duplicate_object->plist_key.'.tpl', $filecontent);
                $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                $this->redirect();
            } else {
                Tools::displayError('Can not duplicate Profiles');
            }
        }
        
        
        if (Tools::isSubmit('saveELement')) {
            $filecontent = Tools::getValue('filecontent');
            $fileName = Tools::getValue('fileName');
            if (!is_dir($this->theme_dir.'modules/appagebuilder/views/templates/front/products/')) {
                if (!is_dir($this->theme_dir.'modules/appagebuilder/views/templates/front/products/')) {
                    mkdir($this->theme_dir.'modules/appagebuilder/views/templates/front/products/', 0755, true);
                }
            }
            ApPageSetting::writeFile($this->theme_dir.'modules/appagebuilder/views/templates/front/products/', $fileName.'.tpl', $filecontent);
        }
    }

    public function convertObjectToTpl($object_form)
    {
        $tpl = '';
        foreach ($object_form as $object) {
            if ($object['name'] == 'functional_buttons') {
                $tpl .= ApPageSetting::getProductFunctionalButtons();
                $tpl .= $this->convertObjectToTpl($object['element']);
                $tpl .= '</div>';
            } else if ($object['name'] == 'code') {
                $tpl .= $object['code'];
            } else {
                if (!isset($this->file_content[$object['name']])) {
                    $this->returnFileContent($object['name']);
                }
                $tpl .= $this->file_content[$object['name']];
            }
        }
        return $tpl;
    }

    public function returnFileContent($pelement)
    {
        $tpl_dir = $this->theme_dir.'modules/appagebuilder/views/templates/front/products/'.$pelement.'.tpl';
        if (!file_exists($tpl_dir)) {
            $tpl_dir = _PS_MODULE_DIR_.'appagebuilder/views/templates/front/products/'.$pelement.'.tpl';
        }
        $this->file_content[$pelement] = Tools::file_get_contents($tpl_dir);
        return $this->file_content[$pelement];
    }

    public function renderList()
    {
        if (Tools::getIsset('pelement')) {
            $helper = new HelperForm();
            $helper->submit_action = 'saveELement';
            $inputs = array(
                array(
                    'type' => 'textarea',
                    'name' => 'filecontent',
                    'label' => $this->l('File Content'),
                    'desc' => $this->l('Please carefully when edit tpl file'),
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'fileName',
                )
            );
            $fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => sprintf($this->l('You are Editing file: %s'), Tools::getValue('pelement').'.tpl'),
                        'icon' => 'icon-cogs'
                    ),
                    'action' => Context::getContext()->link->getAdminLink('AdminApPageBuilderShortcodes'),
                    'input' => $inputs,
                    'name' => 'importData',
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'button btn btn-default pull-right'
                    ),
                    'tinymce' => false,
                ),
            );
            $helper->tpl_vars = array(
                'fields_value' => $this->getFileContent()
            );
            return $helper->generateForm(array($fields_form));
        }
        $this->initToolbar();
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        return $this->ImportForm() . parent::renderList();
    }
    
    public function ImportForm()
    {
        $helper = new HelperForm();
        $helper->submit_action = 'import' . $this->table;
        $inputs = array(
            array(
                'type' => 'file',
                'name' => 'importFile',
                'label' => $this->l('File'),
                'desc' => $this->l('Only accept xml file'),
            ),
        );
        $fields_form = array(
            'form' => array(
                'action' => Context::getContext()->link->getAdminLink('AdminApPageBuilderShortcodeController'),
                'input' => $inputs,
                'submit' => array('title' => $this->l('Import'), 'class' => 'button btn btn-success'),
                'tinymce' => false,
            ),
        );
        $helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
        $helper->identifier = $this->identifier;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->table = 'xml_import';
        $html = $helper->generateForm(array($fields_form));

        return $html;
    }

    public function getFileContent()
    {
        $pelement = Tools::getValue('pelement');
        $tpl_dir = $this->theme_dir.'modules/appagebuilder/views/templates/front/products/'.$pelement.'.tpl';
        if (!file_exists($tpl_dir)) {
            $tpl_dir = _PS_MODULE_DIR_.'appagebuilder/views/templates/front/products/'.$pelement.'.tpl';
        }
        return array('fileName' => $pelement, 'filecontent' => Tools::file_get_contents($tpl_dir));
    }

    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);
        $this->helper->module = APPageBuilder::getInstance();
    }

    public function renderForm()
    {
        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/form.js');
        $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/product-list.js');
        $this->context->controller->addCss(apPageHelper::getCssAdminDir().'admin/form.css');
        $source_file = Tools::scandir(_PS_MODULE_DIR_.'appagebuilder/views/templates/front/products/', 'tpl');
        if (is_dir($this->theme_dir.'modules/appagebuilder/views/templates/front/products/')) {
            $source_template_file = Tools::scandir($this->theme_dir.'modules/appagebuilder/views/templates/front/products/', 'tpl');
            $source_file = array_merge($source_file, $source_template_file);
        }
        $elements = array();
        $icon_list = ApPageSetting::getProductElementIcon();
        foreach ($source_file as $value) {
            $fileName = basename($value, '.tpl');
            if ($fileName == 'index') {
                continue;
            }
            $elements[$fileName] = array(
                'name' => str_replace('_', ' ', $fileName),
                'icon' => (isset($icon_list[$fileName]) ? $icon_list[$fileName] : 'icon-sun'));
        }
        $params = array('gridLeft' => array(), 'gridRight' => array());

        $this->object->params = str_replace($this->str_search, $this->str_relace, $this->object->params);

        if (isset($this->object->params)) {
            $params = Tools::jsonDecode($this->object->params, true);
        }

        //$params['gridLeft'] = $this->replaceSpecialStringToHtml($params['gridLeft']);
        //$params['gridRight'] = $this->replaceSpecialStringToHtml($params['gridRight']);

        $block_list = array(
            'gridLeft' => array('title' => 'Product-Image', 'class' => 'left-block'),
            'gridRight' => array('title' => 'Product-Meta', 'class' => 'right-block'),
        );
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Ap Profile Manage'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product List Key'),
                    'name' => 'plist_key',
                    'readonly' => 'readonly',
                    'desc' => $this->l('Tpl File name'),
                ),
                array(
                    'label' => $this->l('Class'),
                    'type' => 'text',
                    'name' => 'class',
                    'width' => 140
                ),
                array(
                    'type' => 'ap_proGrid',
                    'name' => 'ap_proGrid',
                    'label' => $this->l('Layout'),
                    'elements' => $elements,
                    'params' => $params,
                    'blockList' => $block_list
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'params'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and Stay'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save')
            )
        );
        return parent::renderForm();
    }

    public function replaceSpecialStringToHtml($arr)
    {
        foreach ($arr as &$v) {
            if ($v['name'] == 'code') {
                // validate module
                $v['code'] = str_replace($this->str_search, $this->str_relace, $v['code']);
            } else {
                if ($v['name'] == 'functional_buttons') {
                    foreach ($v as &$f) {
                        if ($f['name'] == 'code') {
                            // validate module
                            $f['code'] = str_replace($this->str_search, $this->str_relace, $f['code']);
                        }
                    }
                }
            }
        }
        return $arr;
    }

    public function getFieldsValue($obj)
    {
        $file_value = parent::getFieldsValue($obj);
        if (!$obj->id) {
            $num = ApPageSetting::getRandomNumber();
            $file_value['plist_key'] = 'plist'.$num;
            $file_value['name'] = $file_value['plist_key'];
            $file_value['class'] = 'product-list-'.$num;
        }
        return $file_value;
    }

    public function processAdd()
    {
        if ($obj = parent::processAdd()) {
            $this->saveTplFile($obj->plist_key, $obj->params);
        }
    }

    public function processUpdate()
    {
        if ($obj = parent::processUpdate()) {
            $this->saveTplFile($obj->plist_key, $obj->params);
        }
    }

    public function processDelete()
    {
        $object = $this->loadObject();
        Tools::deleteFile(apPageHelper::getConfigDir('theme_profiles').$object->plist_key.'.tpl');
        parent::processDelete();
    }

    public function saveTplFile($plist_key, $params = '')
    {
        $data_form = str_replace($this->str_search, $this->str_relace, $params);
        $data_form = Tools::jsonDecode($data_form, true);

        $grid_left = $data_form['gridLeft'];
        $grid_right = $data_form['gridRight'];
        $tpl_grid = ApPageSetting::getProductContainer();
        $tpl_grid .= ApPageSetting::getProductLeftBlock().$this->convertObjectToTpl($grid_left)."</div>\n";
        $tpl_grid .= ApPageSetting::getProductRightBlock().$this->convertObjectToTpl($grid_right)."</div>\n";
        $tpl_grid .= ApPageSetting::getProductContainerEnd();
        $folder = apPageHelper::getConfigDir('theme_profiles');
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        $file = $plist_key.'.tpl';
        $tpl_grid = preg_replace('/\{\*[\s\S]*?\*\}/', '', $tpl_grid);
        $tpl_grid = str_replace(" mod='appagebuilder'", '', $tpl_grid);
        ApPageSetting::writeFile($folder, $file, apPageHelper::getLicenceTPL().$tpl_grid);
    }

    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if ($object->toggleStatus()) {
                $matches = array();
                if (preg_match('/[\?|&]controller=([^&]*)/', (string)$_SERVER['HTTP_REFERER'], $matches) !== false && Tools::strtolower($matches[1]) != Tools::strtolower(preg_replace('/controller/i', '', get_class($this)))) {
                    $this->redirect_after = preg_replace('/[\?|&]conf=([^&]*)/i', '', (string)$_SERVER['HTTP_REFERER']);
                } else {
                    $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                }
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                    .'<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
        return $object;
    }
    
    public function displayDuplicateLink($token = null, $id = null, $name = null)
    {
        $controller = 'AdminApPageBuilderProducts';
        $token = Tools::getAdminTokenLite($controller);
        $html = '<a href="#" title="Duplicate" onclick="confirm_link(\'\', \'Duplicate Product List ID '.$id.'. If you wish to proceed, click &quot;Yes&quot;. If not, click &quot;No&quot;.\', \'Yes\', \'No\', \'index.php?controller='.$controller.'&amp;id_appagebuilder_products='.$id.'&amp;duplicateappagebuilder_products&amp;token='.$token.'\', \'#\')">
            <i class="icon-copy"></i> Duplicate
        </a>';
        
        // validate module
        unset($name);
        
        return $html;
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function access($action, $disable = false)
    {
        if (Tools::getIsset('update'.$this->table) && Tools::getIsset($this->identifier)) {
            // Allow person see "EDIT" form
            $action = 'view';
        }
        return parent::access($action, $disable);
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        # SET ACTION : IMPORT DATA
        if ($this->can_import && Tools::getIsset('import' . $this->table)) {
            if ($this->access('edit')) {
                $this->action = 'import';
            }
        }
        
        if (count($this->errors) <= 0) {
            if( Tools::isSubmit('duplicate'.$this->table) ) {
                if ($this->id_object) {
                    if (!$this->access('add'))
                    {
                        $this->errors[] = $this->trans('You do not have permission to duplicate this.', array(), 'Admin.Notifications.Error');
                    }
                }
            }elseif($this->can_import && Tools::getIsset('import' . $this->table)){
                if (!$this->access('edit')) {
                    $this->errors[] = $this->trans('You do not have permission to import data.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
