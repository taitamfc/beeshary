<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Bootstrap Menu
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}
require_once(_PS_MODULE_DIR_.'leobootstrapmenu/libs/Helper.php');
require_once(_PS_MODULE_DIR_.'leobootstrapmenu/classes/widgetbase.php');
require_once(_PS_MODULE_DIR_.'leobootstrapmenu/classes/widget.php');
//require_once(_PS_MODULE_DIR_.'leobootstrapmenu/classes/LeomanagewidgetsOwlCarousel.php');

class AdminLeoWidgetsController extends ModuleAdminControllerCore
{
    public $widget;
    public $base_config_url;
    private $_imageField = array('htmlcontent', 'content', 'information');
    private $_langField = array('widget_title', 'text_link', 'htmlcontent', 'header', 'content', 'information');
    private $_theme_dir = '';
    
    public function __construct()
    {
        $this->widget = new LeoWidget();
        $this->className = 'LeoWidget';
        $this->bootstrap = true;
        $this->table = 'btmegamenu_widgets';
        
        parent::__construct();
        
        $this->fields_list = array(
            'id_btmegamenu_widgets' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 50,
                'class' => 'fixed-width-xs'
            ),
            'key_widget' => array(
                'title' => $this->l('Widget Key'),
                'filter_key' => 'a!key_widget',
                'type' => 'text',
                'width' => 140,
            ),
            'name' => array(
                'title' => $this->l('Widget Name'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'a!name'
            ),
            'type' => array(
                'title' => $this->l('Widget Type'),
                'width' => 50,
                'type' => 'text',
                'filter_key' => 'a!type'
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            ),
            'correctlink' => array(
                'text' => $this->l('Correct Image Link'),
                'confirm' => $this->l('Are you sure you want to change image url from old theme to new theme?'),
                'icon' => 'icon-edit'
            ),
            'insertLang' => array(
                'text' => $this->l('Auto Input Data for New Lang'),
                'confirm' => $this->l('Auto insert data for new language?'),
                'icon' => 'icon-edit'
            ),
        );
        $this->_where = ' AND id_shop='.(int)($this->context->shop->id);
        // $this->_theme_dir = Context::getContext()->shop->getTheme();
        $this->_theme_dir = Context::getContext()->shop->theme->getName();
    }

    public function renderList()
    {
        $this->initToolbar();
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }
        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        return $this->_showWidgetsSetting();
    }

    public function _showWidgetsSetting()
    {
        $media_dir = $this->module->getMediaDir();
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/jquery-validation-1.9.0/jquery.validate.js');
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/admin/jquery-validation-1.9.0/screen.css');
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/admin/admin.css');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/show.js');
        $tpl = $this->createTemplate('widget.tpl');
        $form = '';
        $widget_selected = '';
        $id = (int)Tools::getValue('id_btmegamenu_widgets');
        $key = (int)Tools::getValue('key');
        if (Tools::getValue('id_btmegamenu_widgets')) {
            $model = new LeoWidget((int)Tools::getValue('id_btmegamenu_widgets'));
        } else {
            $model = $this->widget;
        }
        $model->loadEngines();
        $model->id_shop = Context::getContext()->shop->id;

        $types = $model->getTypes();
        if ($key) {
            $widget_data = $model->getWidetByKey($key, Context::getContext()->shop->id);
        } else {
            $widget_data = $model->getWidetById($id, Context::getContext()->shop->id);
        }

        $id = (int)$widget_data['id'];
        $widget_selected = trim(Tools::strtolower(Tools::getValue('wtype')));
        if ($widget_data['type']) {
            $widget_selected = $widget_data['type'];
//            $disabled = true;
        }

        $form = $model->getForm($widget_selected, $widget_data);
        $is_using_managewidget = 1;
        if (!file_exists(_PS_MODULE_DIR_.'leomanagewidgets/leomanagewidgets.php') || !Module::isInstalled('leomanagewidgets')) {
            # validate module
            $is_using_managewidget = 0;
        }
        $tpl->assign(array(
            'types' => $types,
            'form' => $form,
            'is_using_managewidget' => $is_using_managewidget,
            'widget_selected' => $widget_selected,
            'table' => $this->table,
            'max_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
            'PS_ALLOW_ACCENTED_CHARS_URL' => Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'action' => AdminController::$currentIndex.'&add'.$this->table.'&token='.$this->token,
        ));
        return $tpl->fetch();
    }

    public function postProcess()
    {
        
        if ((Tools::isSubmit('saveleowidget') || Tools::isSubmit('saveandstayleowidget')) && Tools::isSubmit('widgets')) {
            if (!Tools::getValue('widget_name')) {
                $this->errors[] = Tools::displayError('Widget Name Empty !');
            }
            if (!count($this->errors)) {
                if (Tools::getValue('id_btmegamenu_widgets')) {
                    $model = new LeoWidget((int)Tools::getValue('id_btmegamenu_widgets'));
                } else {
                    $model = $this->widget;
                }
                $model->loadEngines();
                $model->id_shop = Context::getContext()->shop->id;
//                $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
                $languages = Language::getLanguages(false);

                $tmp = array();


                # GET POST - BEGIN
                //DOGNND:: validate module
                $widget_type = Tools::getValue('widget_type');
                $validate_class = str_replace('_', '', $widget_type);
                $file_name = _PS_MODULE_DIR_.'leobootstrapmenu/classes/widget/'.$widget_type.'.php';
                require_once($file_name);
                $class_name = 'LeoWidget'.Tools::ucfirst($validate_class);
                $widget = new $class_name;
                $keys = array('addbtmegamenu_widgets', 'id_btmegamenu_widgets', 'widget_name', 'widget_type', 'saveandstayleowidget');
                $post = LeoBtmegamenuHelper::getPostAdmin($keys, 0);
                $keys = array('widget_title');
                $post += LeoBtmegamenuHelper::getPostAdmin($keys, 1);
                if ($widget_type == 'links') {
                    $keys = array('list_id_link', 'list_field', 'list_field_lang');
                    $post += LeoBtmegamenuHelper::getPostAdmin($keys, 0);
                }
                if ($widget_type == 'links') {
                    $keys = array_filter(explode(",", Tools::getValue('list_field')));
                } else {
                    $keys = $widget->getConfigKey(0);
                }
                $post += LeoBtmegamenuHelper::getPostAdmin($keys, 0);
                if ($widget_type == 'links') {
                    $keys = array_filter(explode(",", Tools::getValue('list_field_lang')));
                } else {
                    $keys = $widget->getConfigKey(1);
                }
                
                $post += LeoBtmegamenuHelper::getPostAdmin($keys, 1);
                $keys = $widget->getConfigKey(2);
                $post += LeoBtmegamenuHelper::getPostAdmin($keys, 2);
                # GET POST - END

                //auto create folder if not exists
                if ($widget_type == 'image') {
                    if ($post['image_folder_path'] != '') {
                        $path = _PS_ROOT_DIR_.'/'.trim($post['image_folder_path']).'/';
            
                        $path = str_replace('//', '/', $path);
                        
                        if (!file_exists($path)) {
                            $success = @mkdir($path, 0775, true);
                            $chmod = @chmod($path, 0775);
                            if (($success || $chmod) && !file_exists($path.'index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
                                @copy(_PS_IMG_DIR_.'index.php', $path.'index.php');
                            }
                        }
                    }
                }
                
                foreach ($post as $key => $value) {
                    $tmp[$key] = str_replace(array('\'', '\"'), array("'", '"'), $value);
                    foreach ($this->_langField as $fVal) {
                        if (strpos($key, $fVal) !== false) {
                            foreach ($languages as $language) {
                                if (!Tools::getValue($fVal.'_'.$language['id_lang'])) {
                                    $tmp[$fVal.'_'.$language['id_lang']] = $value;
                                }
                            }
                        }
                    }
                }

                $data = array(
                    'id' => Tools::getValue('id_btmegamenu_widgets'),
                    'params' => call_user_func('base64'.'_encode', Tools::jsonEncode($tmp)),
                    'type' => Tools::getValue('widget_type'),
                    'name' => Tools::getValue('widget_name')
                );

                foreach ($data as $k => $v) {
                    $model->{$k} = $v;
                }

                if ($model->id) {
                    if (!$model->update()) {
                        $this->errors[] = Tools::displayError('Can not update new widget');
                    } else {
                        $model->clearCaches();
                        if (Tools::isSubmit('saveandstayleowidget')) {
                            $this->confirmations[] = $this->l('Update successful');
                            Tools::redirectAdmin(self::$currentIndex.'&id_btmegamenu_widgets='.$model->id.'&updatebtmegamenu_widgets&token='.$this->token.'&conf=4');
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
                        }
                    }
                } else {
                    $model->key_widget = time();
                    if (!$model->add()) {
                        $this->errors[] = Tools::displayError('Can not add new widget');
                    } else {
                        $model->clearCaches();
                        if (Tools::isSubmit('saveandstayleowidget')) {
                            $this->confirmations[] = $this->l('Update successful');
                            Tools::redirectAdmin(self::$currentIndex.'&id_btmegamenu_widgets='.$model->id.'&updatebtmegamenu_widgets&token='.$this->token.'&conf=4');
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
                        }
                    }
                }
            }
        }
        if (Tools::isSubmit('submitBulkcorrectlinkbtmegamenu_widgets')) {
            $btmegamenu_widgetsBox = Tools::getValue('btmegamenu_widgetsBox');
            if ($btmegamenu_widgetsBox) {
                foreach ($btmegamenu_widgetsBox as $widgetID) {
                    $model = new LeoWidget($widgetID);
                    $params = Tools::jsonDecode(call_user_func('base64'.'_decode', $model->params), true);

                    $tmp = array();
                    foreach ($params as $widKey => $widValue) {
                        foreach ($this->_imageField as $fVal) {
                            if (strpos($widKey, $fVal) !== false && strpos($widValue, 'img') !== false) {
//                            $widValue = str_replace('src="' . __PS_BASE_URI__ . 'modules/', 'src="' . __PS_BASE_URI__ . 'themes/'.$this->_theme_dir.'/img/modules/', $widValue);
//                            $patterns = array('/\/leomanagewidgets\/data\//','/\/leobootstrapmenu\/img\//','/\/leobootstrapmenu\/images\//'
//                                ,'/\/leomanagewidgets\/images\//','/\/leomenusidebar\/images\//');
//                            $replacements = array('/leomanagewidgets/','/leobootstrapmenu/','/leobootstrapmenu/','/leomanagewidgets/','/leomenusidebar/');
//                            $widValue = preg_replace($patterns, $replacements, $widValue);
                                //remove comment when install theme base
                                //$widValue = str_replace('/prestashop/base-theme/themes/', __PS_BASE_URI__. 'themes/', $widValue);

                                $widValue = preg_replace('/\/themes\/(\w+)\/img/', '/themes/'.$this->_theme_dir.'/img', $widValue);
                                break;
                            }
                        }

                        $tmp[$widKey] = $widValue;
                    }

                    $model->params = call_user_func('base64'.'_encode', Tools::jsonEncode($tmp));
                    $model->save();
                }
            }
        }

        if (Tools::isSubmit('submitBulkinsertLangbtmegamenu_widgets')) {
            $btmegamenu_widgetsBox = Tools::getValue('btmegamenu_widgetsBox');
            $id_currentLang = $this->context->language->id;
            $languages = Language::getLanguages(false);

            if ($btmegamenu_widgetsBox) {
                foreach ($btmegamenu_widgetsBox as $widgetID) {
                    $model = new LeoWidget($widgetID);
                    $tmp = Tools::jsonDecode(call_user_func('base64'.'_decode', $model->params), true);

                    $defauleVal = array();
                    if ($tmp) {
                        foreach ($tmp as $widKey => $widValue) {
                            $defaulArray = explode('_', $widKey);
                            if (strpos($widKey, '_'.$id_currentLang) !== false && $defaulArray[count($defaulArray) - 1] == $id_currentLang) {
                                $defauleVal[$widKey] = $widValue;
                            }
                        }
                    }
                    if ($defauleVal) {
                        foreach ($languages as $lang) {
                            if ($lang['id_lang'] == $id_currentLang) {
                                continue;
                            }

                            foreach ($defauleVal as $widKey => $widValue) {
                                $keyRemove = Tools::substr($widKey, 0, -Tools::strlen('_'.$id_currentLang));
                                $keyReal = $keyRemove.'_'.$lang['id_lang'];
                                if (!isset($tmp[$keyReal]) || trim($tmp[$keyReal]) == '') {
                                    $tmp[$keyReal] = $widValue;
                                }
                            }
                        }
                    }
                    if ($defauleVal) {
                        $model->params = call_user_func('base64'.'_encode', Tools::jsonEncode($tmp));
                        $model->save();
                    }
                }
            }
        }
       
        parent::postProcess();
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
                WHERE UCASE(`class_name`) = "'.'AdminLeoBootstrapMenuModule'.'"
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
            $id = (int)Tools::getValue('id_btmegamenu_widgets');
            if ($id) {
                if (!$this->access('edit')) {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
