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

require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageBuilderProfilesModel.php');

class AdminApPageBuilderProfilesController extends ModuleAdminControllerCore
{
    private $theme_name = '';
    public $profile_js_folder = '';
    public $profile_css_folder = '';
    public $module_name = 'appagebuilder';
    public $explicit_select;
    public $order_by;
    public $order_way;
    public $theme_dir;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'appagebuilder_profiles';
        $this->className = 'ApPageBuilderProfilesModel';
        $this->lang = false;
        $this->explicit_select = true;
        $this->allow_export = true;

        parent::__construct();
        $this->theme_dir = _PS_ALL_THEMES_DIR_._THEME_NAME_.'/';

        $this->context = Context::getContext();

        $this->order_by = 'page';
        $this->order_way = 'DESC';
        $alias = 'sa';

        $id_shop = (int)$this->context->shop->id;
        $this->_join .= ' JOIN `'._DB_PREFIX_.'appagebuilder_profiles_shop`
                sa ON (a.`id_appagebuilder_profiles` = sa.`id_appagebuilder_profiles` AND sa.id_shop = '.$id_shop.')';
        $this->_select .= ' sa.active as active, ';

        $this->fields_list = array(
            'id_appagebuilder_profiles' => array(
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
            'profile_key' => array(
                'title' => $this->l('Key'),
                'filter_key' => 'a!profile_key',
                'type' => 'text',
                'width' => 140,
            ),
            'active' => array(
                'title' => $this->l('Is Default'),
                'active' => 'status',
                'filter_key' => $alias.'!active',
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
            ),
            'insertLang' => array(
                'text' => $this->l('Auto Input Data for New Lang'),
                'confirm' => $this->l('Auto insert data for new language?'),
                'icon' => 'icon-edit'
            )
        );

        $this->_where = ' AND sa.id_shop='.(int)$this->context->shop->id;
        $this->theme_name = _THEME_NAME_;
        
        $this->profile_css_folder = _PS_THEME_DIR_.apPageHelper::getCssDir().'profiles/';
        $this->profile_js_folder = _PS_THEME_DIR_.apPageHelper::getJsDir().'profiles/';
        
        if (!is_dir($this->profile_css_folder)) {
            mkdir($this->profile_css_folder, 0755, true);
        }
        if (!is_dir($this->profile_js_folder)) {
            mkdir($this->profile_js_folder, 0755, true);
        }
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
    }
    
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin('tagify');
    }

    public function processDelete()
    {
        $object = $this->loadObject();
        
        if ($object && !$object->active) {
            $object = parent::processDelete();
            if ($object->profile_key) {
                Tools::deleteFile($this->profile_css_folder.$object->profile_key.'.css');
                Tools::deleteFile($this->profile_js_folder.$object->profile_key.'.js');
            }
        } else {
            $this->errors[] = Tools::displayError('Can not delete Default Profile.');
        }
        return $object;
    }

    public function processBulkDelete()
    {
        $arr = $this->boxes;
        if (!$arr) {
            return;
        }
        foreach ($arr as $id) {
            $object = new $this->className($id);
            if ($object && !$object->active) {
                $object->delete();
                if ($object->profile_key) {
                    Tools::deleteFile($this->profile_css_folder.$object->profile_key.'.css');
                    Tools::deleteFile($this->profile_js_folder.$object->profile_key.'.js');
                }
            } else {
                $this->errors[] = Tools::displayError('Can not delete Default Profile.');
            }
        }
        if (empty($this->errors)) {
            $this->confirmations[] = $this->_conf[1];
        }
    }

    public function renderView()
    {
        //echo 'here';die;
        $object = $this->loadObject();
        if ($object->page == 'product_detail') {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminApPageBuilderProductDetail');
        } else {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminApPageBuilderHome');
        }
        $this->redirect_after .= '&id_appagebuilder_profiles='.$object->id;
        $this->redirect();
    }
    
    public function displayViewLink($token = null, $id, $name = null)
    {
        // validate module
        unset($name);
        $token = Context::getContext()->link->getAdminLink('AdminApPageBuilderHome');
        $href = $token . '&id_appagebuilder_profiles='.$id;
        $html = '<a href="'.$href.'" class="btn btn-default" title="View"><i class="icon-search-plus"></i> View</a>';
        return $html;
    }

    public function processBulkinsertLang()
    {
        // Remove resouce and update table profiles
        $arr = $this->boxes;
        if (!$arr) {
            // validate module
            $arr[] = Tools::getValue('id');
        }

        if (!$arr) {
            return;
        }
        foreach ($arr as $item) {
            if ($item) {
                //has profile id
                $pfile = new ApPageBuilderProfilesModel($item);
                $id_positions = array($pfile->header, $pfile->content, $pfile->footer, $pfile->product);
                $list_position = $pfile->getPositionsForProfile($id_positions);
                $list_pos_id = array();
                foreach ($list_position as $v) {
                    // validate module
                    $list_pos_id[] = $v['id_appagebuilder_positions'];
                }
                $s_model = new ApPageBuilderModel();
                $list_short_c = $s_model->getAllItemsByPositionId($list_pos_id);
                $context = Context::getContext();
                $id_lang = (int)$context->language->id;
                foreach ($list_short_c as $shor_code) {
                    $s_model = new ApPageBuilderModel($shor_code['id']);
                    if ($s_model->params) {
                        foreach ($s_model->params as $key => $value) {
                            if ($key != $id_lang) {
                                // validate module
                                $s_model->params[$key] = $s_model->params[$id_lang];
                            }
                            // validate module
                            unset($value);
                        }
                    }
                    $s_model->save();
                }
            }
        }
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
            } else {
                $this->errors[] = Tools::displayError('You can not disable default profile, Please select other profile as default');
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                    .'<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
        return $object;
    }

    public function postProcess()
    {
        parent::postProcess();
        if (count($this->errors) > 0) {
            return;
        }
        if (Tools::getIsset('duplicateappagebuilder_profiles')) {
//            $context = Context::getContext();
//            $id_shop = $context->shop->id;
            $id = Tools::getValue('id_appagebuilder_profiles');
            $model = new ApPageBuilderProfilesModel($id);
            
            if($model){
                $old_key = $model->profile_key;
                $model->profile_key = $profile_key = 'profile'.ApPageSetting::getRandomNumber();
                $model->id = null;
                $model->name = $this->l('Duplicate of ') . $model->name;
                $model->active = '';
                $model->friendly_url = array();
                $duplicate_object = $model->save();
                
                if($duplicate_object){
                    //duplicate shortCode
                    $id_new = $model->id;
                    ApPageSetting::writeFile($this->profile_js_folder, $profile_key.'.js', Tools::file_get_contents($this->profile_js_folder.$old_key.'.js'));
                    ApPageSetting::writeFile($this->profile_css_folder, $profile_key.'.css', Tools::file_get_contents($this->profile_css_folder.$old_key.'.css'));
                    AdminApPageBuilderShortcodesController::duplicateData($id, $id_new);
                    $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                    $this->redirect();
                }else{
                    Tools::displayError('Can not create new profile');
                }
            } else {
                Tools::displayError('Profile is not exist to duplicate');
            }
        }
    }

    public function renderList()
    {
        $this->initToolbar();
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        $this->context->controller->addCss(apPageHelper::getCssAdminDir().'admin/form.css');
        $tpl_name = 'list.tpl';
        $path = '';
        if (file_exists($this->theme_dir.'modules/'.$this->module->name.'/views/templates/admin/'.$tpl_name)) {
            $path = $this->theme_dir.'modules/'.$this->module->name.'/views/templates/admin/'.$tpl_name;
        } elseif (file_exists($this->getTemplatePath().$this->override_folder.$tpl_name)) {
            $path = $this->getTemplatePath().$this->override_folder.$tpl_name;
        }
        $model = new ApPageBuilderProfilesModel();
        $list_profiles = $model->getAllProfileByShop();
        // Build url for back from live edit page, it is stored in cookie and read in fontContent function below
        $controller = 'AdminApPageBuilderHome';
        $id_lang = Context::getContext()->language->id;
        $url_edit_profile_token = Tools::getAdminTokenLite($controller);
        $params = array('token' => $url_edit_profile_token);
        $url_edit_profile = dirname($_SERVER['PHP_SELF']).'/'.Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
        $live_edit_params = array(
            'ap_live_edit' => true,
            'ad' => basename(_PS_ADMIN_DIR_),
            'liveToken' => Tools::getAdminTokenLite('AdminModulesPositions'),
            'id_employee' => (int)Context::getContext()->employee->id,
            'id_shop' => (int)Context::getContext()->shop->id
        );
        $url_live_edit = $this->getLiveEditUrl($live_edit_params);
        $lang = '';
        if (Configuration::get('PS_REWRITING_SETTINGS') && count(Language::getLanguages(true)) > 1) {
            $lang = Language::getIsoById($this->context->employee->id_lang).'/';
        }
        $url_preview = $this->context->shop->getBaseUrl().(Configuration::get('PS_REWRITING_SETTINGS') ? '' : 'index.php').$lang;
        $cookie = new Cookie('url_live_back');
        $cookie->setExpire(time() + 60 * 60);
        $cookie->variable_name = $url_edit_profile;
        $cookie->write();
        // Save token for check valid
        $cookie = new Cookie('ap_token'); //make your own cookie
        $cookie->setExpire(time() + 60 * 60);
        $cookie->variable_name = $live_edit_params['liveToken'];
        $cookie->write();
        $profile_link = $this->context->link->getAdminLink('AdminApPageBuilderProfiles').'&addappagebuilder_profiles';
        $this->context->smarty->assign(array(
            'profile_link' => $profile_link,
            'url_preview' => $url_preview,
            'list_profile' => $list_profiles,
            'url_live_edit' => $url_live_edit,
            'url_profile_detail' => $this->context->link->getAdminLink('AdminApPageBuilderProfiles'),
            'url_edit_profile_token' => $url_edit_profile_token,
            'url_edit_profile' => $url_edit_profile));
        $content = $this->context->smarty->fetch($path);
        $path_guide = $this->getTemplatePath().'guide.tpl';
        $guide_box = ApPageSetting::buildGuide($this->context, $path_guide, 0);
        return $guide_box.parent::renderList().$content;
        //return parent::renderList();
    }

    public function getLiveEditUrl($live_edit_params)
    {
        $lang = '';
        $admin_dir = dirname($_SERVER['PHP_SELF']);
        $admin_dir = Tools::substr($admin_dir, strrpos($admin_dir, '/') + 1);
        $dir = str_replace($admin_dir, '', dirname($_SERVER['SCRIPT_NAME']));
        if (Configuration::get('PS_REWRITING_SETTINGS') && count(Language::getLanguages(true)) > 1) {
            $lang = Language::getIsoById(Context::getContext()->employee->id_lang).'/';
        }
        $url = Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().$dir.$lang.
                Dispatcher::getInstance()->createUrl('index', (int)Context::getContext()->language->id, $live_edit_params);
        return $url;
    }

    public function renderForm()
    {
        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        $groups = Group::getGroups($this->default_form_language, true);
        // UNSET GROUP_BOX
        if ($this->object->id == '') {
            $model = new ApPageBuilderProfilesModel();
            $list_profiles = $model->getAllProfileByShop();
            foreach ($list_profiles as $profile) {
                $group_boxs = $profile['group_box'];
                $aray_group_box = explode(',', $group_boxs);
                foreach ($aray_group_box as $group_box) {
                    if ($group_box!=1&&$group_box!=2&&$group_box!=3) {
                        while ($group = current($groups)) {
                            if ($group['id_group'] == $group_box) {
                                unset($groups[key($groups)]);
                            }
                            next($groups);
                        }
                    }
                }
            }
        }
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
                    'hint' => $this->l('Invalid characters:'),' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'friendly_url',
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'lang' => true,
                    // 'required' => true,
                    'class' => 'copyMeta2friendlyURL',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'cols' => 40,
                    'rows' => 10,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' => array(
                        $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                        $this->l('To add "tags" click in the field, write something, and then press "Enter."')
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Profile Key'),
                    'name' => 'profile_key',
                    'readonly' => 'readonly',
                    'desc' => $this->l('Use it to save as file name of css and js of profile'),
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Apply default profile for these groups'),
                    'name' => 'groupBox',
                    'values' => $groups,
                    'col' => '6',
                    'hint' => $this->l('Select the groups that you would like to apply this profile is default.')
                ),
                array(
                    'type' => 'html',
                    'name' => 'dump_name',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Fullwidth Function: is only for develop')
                    .'<br/>'.$this->l('To use this function, you have to download')
                    .'<br/><a href="http://demothemes.info/prestashop/appagebuilder/header.tpl.zip" title="'.$this->l('Header file').'">'
                    .'<b>header.tpl</b></a>'
                    .'<br/><a href="http://demothemes.info/prestashop/appagebuilder/footer.tpl.zip" title="'.$this->l('Footer file').'">'
                    .'<b>footer.tpl</b></a><br/>'
                    .$this->l('file and compare or override in themes folder').'</div>'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'fullwidth_index_hook',
                    'label' => $this->l('Fullwidth Homepage'),
                    'class' => 'checkbox-group',
                    'desc' => $this->l('The setting full-width for above HOOKS, apply for Home page'),
                    'values' => array(
                        'query' => self::getCheckboxIndexHook(),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'fullwidth_other_hook',
                    'label' => $this->l('Fullwidth other Pages'),
                    'class' => 'checkbox-group',
                    'desc' => $this->l('The setting full-width for above HOOKS, apply for all OTHER pages ( not Home page )'),
                    'values' => array(
                        'query' => self::getCheckboxOtherHook(),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'disable_cache_hook',
                    'label' => $this->l('Disable cache Hooks'),
                    'class' => 'checkbox-group',
                    'desc' => $this->l('Some modules always update data, disable cache for those modules show correct info.'),
                    'values' => array(
                        'query' => self::getCheckboxCacheHook(),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    //'label' => $this->l('Profile For Page'),
                    'name' => 'page',
                    'class' => 'hide',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'index',
                                'name' => $this->l('Index'),
                            ),
                            array(
                                'id' => 'product_detail',
                                'name' => $this->l('Product Detail'),
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                )
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
                    'icon' => 'process-icon-save'
                )
            )
        );
        $is_edit = Tools::getValue('id_appagebuilder_profiles');
        $this->fields_form['input'][] = array(
            'type' => 'textarea',
            'label' => $this->l('Custom Css'),
            'name' => 'css',
            'desc' => sprintf($this->l('Please set write Permission for folder %s'), $this->profile_css_folder),
        );
        $this->fields_form['input'][] = array(
            'type' => 'textarea',
            'label' => $this->l('Custom Js'),
            'name' => 'js',
            'desc' => sprintf($this->l('Please set write Permission for folder %s'), $this->profile_js_folder),
        );
        // Display link view if it existed
        if ($is_edit) {
            $profile_link = $this->context->link->getAdminLink('AdminApPageBuilderHome').'&id_appagebuilder_profiles='.$is_edit;
            $this->fields_form['input'][] = array(
                'type' => 'html',
                'name' => 'default_html',
                'name' => 'dess',
                'html_content' => '<a class="btn btn-info" href="'.$profile_link.'">
                    <i class="icon icon-table"></i> '.$this->l('View and edit use mode Layout design').' >></a>'
            );
        }

        $default_index_hook = $this->getDefaultIndexHook();
        $default_other_hook = $this->getDefaultOtherHook();
        $default_disable_cache_hook = $this->getDefaultDisableCacheHook();
        foreach ($default_index_hook as $key => $value) {
            $this->fields_value['fullwidth_index_hook_'.$key] = $value;
        }
        foreach ($default_other_hook as $key => $value) {
            $this->fields_value['fullwidth_other_hook_'.$key] = $value;
        }
        foreach ($default_disable_cache_hook as $key => $value) {
            $this->fields_value['disable_cache_hook_'.$key] = $value;
        }
        foreach ($groups as $group) {
            $this->fields_value['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], in_array($group['id_group'], explode(',', $this->object->group_box)));
        }

        $path_guide = $this->getTemplatePath().'guide.tpl';
        $guide_box = ApPageSetting::buildGuide($this->context, $path_guide, 2);
        return $guide_box.parent::renderForm();
    }

    /**
     * Read file css + js to form when add/edit
     */
    public function getFieldsValue($obj)
    {
        $file_value = parent::getFieldsValue($obj);
        if ($obj->id && $obj->profile_key) {
            $file_value['css'] = Tools::file_get_contents($this->profile_css_folder.$obj->profile_key.'.css');
            $file_value['js'] = Tools::file_get_contents($this->profile_js_folder.$obj->profile_key.'.js');
        } else {
            $file_value['profile_key'] = 'profile'.ApPageSetting::getRandomNumber();
        }
        return $file_value;
    }

    public function processAdd()
    {
        parent::validateRules();
        if (count($this->errors)) {
            return false;
        }
        if ($this->object = parent::processAdd()) {
            $this->saveCustomJsAndCss($this->object->profile_key, '');
        }
        $this->processParams();
        if (!Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminApPageBuilderHome');
            $this->redirect_after .= '&id_appagebuilder_profiles='.($this->object->id);
            $this->redirect();
        }
    }

    public function processUpdate()
    {
        parent::validateRules();
        if (count($this->errors)) {
            return false;
        }
        if ($this->object = parent::processUpdate()) {
            $this->saveCustomJsAndCss($this->object->profile_key, $this->object->profile_key);
        }

        $this->processParams();
        if (!Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            $this->redirect_after = Context::getContext()->link->getAdminLink('AdminApPageBuilderHome');
            $this->redirect_after .= '&id_appagebuilder_profiles='.($this->object->id);
            $this->redirect();
        }
    }

    /**
     * Get fullwidth hook, save to params
     */
    public function processParams()
    {
        $params = Tools::jsonDecode($this->object->params);
        if ($params === null) {
            $params = new stdClass();
        }

        # get post index hook
        $index_hook = ApPageSetting::getIndexHook();
        $post_index_hooks = array();
        foreach ($index_hook as $key => $value) {
            // validate module
            $post_index_hooks[$value] = Tools::getValue('fullwidth_index_hook_'.$value) ?
                    Tools::getValue('fullwidth_index_hook_'.$value) : ApPageSetting::HOOK_BOXED;
            // validate module
            unset($key);
        }
        $params->fullwidth_index_hook = $post_index_hooks;

        # get post other hook
        $other_hook = ApPageSetting::getOtherHook();
        $post_other_hooks = array();
        foreach ($other_hook as $key => $value) {
            // validate module
            $post_other_hooks[$value] = Tools::getValue('fullwidth_other_hook_'.$value) ? Tools::getValue('fullwidth_other_hook_'.$value) : ApPageSetting::HOOK_BOXED;
            // validate module
            unset($key);
        }
        $params->fullwidth_other_hook = $post_other_hooks;
        
        # get post disable hook
        $cache_hooks = ApPageSetting::getCacheHook();
        $post_disable_hooks = array();
        foreach ($cache_hooks as $key => $value) {
            // validate module
            $post_disable_hooks[$value] = Tools::getValue('disable_cache_hook_'.$value) ? Tools::getValue('disable_cache_hook_'.$value) : ApPageSetting::HOOK_BOXED;
            // validate module
            unset($key);
        }
        $params->disable_cache_hook = $post_disable_hooks;
        

        # Save to params
        $this->object->params = Tools::jsonEncode($params);
        
        # Save group_box
        if (Tools::getValue('groupBox')) {
            $this->object->group_box = implode(',', Tools::getValue('groupBox'));
        } else {
            $this->object->group_box = '';
        }
        
        $this->object->save();
    }

    public function saveCustomJsAndCss($key, $old_key = '')
    {
        # DELETE OLD FILE
        if ($old_key) {
            Tools::deleteFile($this->profile_css_folder.$old_key.'.css');
            Tools::deleteFile($this->profile_js_folder.$old_key.'.js');
        }

        if (Tools::getValue('js') != '') {
            ApPageSetting::writeFile($this->profile_js_folder, $key.'.js', Tools::getValue('js'));
        }
        
        if (Tools::getValue('css') != '') {
            # FIX CUSTOMER CAN NOT TYPE "\"
            $temp = Tools::getAllValues();
            $css = $temp['css'];
            ApPageSetting::writeFile($this->profile_css_folder, $key.'.css', $css);
        }
    }

    /**
     * Generate form : create checkbox in admin form ( add/edit profile )
     */
    public static function getCheckboxIndexHook()
    {
        $ids = ApPageSetting::getIndexHook();
        $names = ApPageSetting::getIndexHook();
        return apPageHelper::getArrayOptions($ids, $names);
    }

    /**
     * Generate form : create checkbox in admin form ( add/edit profile )
     */
    public static function getCheckboxOtherHook()
    {
        $ids = ApPageSetting::getOtherHook();
        $names = ApPageSetting::getOtherHook();
        return apPageHelper::getArrayOptions($ids, $names);
    }

    /**
     * Generate form : create checkbox in admin form ( add/edit profile )
     */
    public static function getCheckboxCacheHook()
    {
        $ids = ApPageSetting::getCacheHook();
        $names = ApPageSetting::getCacheHook();
        return apPageHelper::getArrayOptions($ids, $names);
    }

    /**
     * Get fullwidth hook from database or default
     */
    public function getDefaultIndexHook()
    {
        $params = Tools::jsonDecode($this->object->params);
        return isset($params->fullwidth_index_hook) ? $params->fullwidth_index_hook : ApPageSetting::getIndexHook(3);
    }

    /**
     * Get fullwidth hook from database or default
     */
    public function getDefaultOtherHook()
    {
        $params = Tools::jsonDecode($this->object->params);
        return isset($params->fullwidth_other_hook) ? $params->fullwidth_other_hook : ApPageSetting::getOtherHook(3);
    }

    /**
     * Get fullwidth hook from database or default
     */
    public function getDefaultDisableCacheHook()
    {
        $params = Tools::jsonDecode($this->object->params);
        return isset($params->disable_cache_hook) ? $params->disable_cache_hook : ApPageSetting::getCacheHook(3);
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        
        if (count($this->errors) <= 0) {
            if( Tools::isSubmit('duplicate'.$this->table) ) {
                if ($this->id_object) {
                    if (!$this->access('add'))
                    {
                        $this->errors[] = $this->trans('You do not have permission to duplicate this.', array(), 'Admin.Notifications.Error');
                    }
                }
            }
        }
    }
	
	// public function viewAccess($disable = false){
		// return true;            
	// }
}
