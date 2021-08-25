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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

include_once(_PS_MODULE_DIR_.'leobootstrapmenu/classes/Btmegamenu.php');
include_once(_PS_MODULE_DIR_.'leobootstrapmenu/classes/BtmegamenuGroup.php');
include_once(_PS_MODULE_DIR_.'leobootstrapmenu/libs/Helper.php');
require_once(_PS_MODULE_DIR_.'leobootstrapmenu/classes/widgetbase.php');
require_once(_PS_MODULE_DIR_.'leobootstrapmenu/classes/widget.php');
//include_once(_PS_MODULE_DIR_.'leobootstrapmenu/leobootstrapmenuAdmin.php');
//class Leobootstrapmenu extends LeobootstrapmenuAdmin implements WidgetInterface

class Leobootstrapmenu extends Module implements WidgetInterface
{
    private $html = '';
    public $widget;
    public $theme_name;
    private $current_group = array('id_group' => 0, 'title' => '', 'group_type' => '');
    public $group_data = array(
        'id_btmegamenu_group' => '',
        'title' => null,
        'id_shop' => '',
        'hook' => '',
        'active' => '',
        'group_type' => '',
        'show_cavas' => '',
        'type_sub' => '',
        'group_class' => '',
    );
    
    private $hook_support = array(
        'displayTop',
        'displayNav1',
        'displayNav2',
        'displayNavFullWidth',
        'displayLeftColumn',
        'displayHome',
        'displayRightColumn',
        'displayFooterBefore',
        'displayFooter',
        'displayFooterAfter',
        'displayLeftColumnProduct',
        'displayFooterProduct',
        'displayRightColumnProduct',
        'displayProductButtons',
        'displayReassurance');

    public function __construct()
    {
        $this->name = 'leobootstrapmenu';
        $this->tab = 'front_office_features';
        $this->version = '4.1.1';
        $this->author = 'LeoTheme';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->controllers = array('widget');
        $this->secure_key = Tools::encrypt($this->name);
        
        parent::__construct();

        
        $this->displayName = $this->l('Leo Bootstrap Megamenu');
        $this->description = $this->l('Leo Bootstrap Megamenu Support Leo Framework Version 4.0.0');
        $this->theme_name = Context::getContext()->shop->theme->getName();
        $this->img_path = _PS_ALL_THEMES_DIR_.$this->theme_name.'/'.$this->getThemeMediaDir('img').'/img/icons/';

        $this->widget = new LeoWidget();
    }
    
    public function install()
    {
        /* Adds Module */
        if (parent::install() &&
                Configuration::updateValue('BTMEGAMENU_iscache', 1)
                && Configuration::updateValue('BTMEGAMENU_cachetime', 24)
                && Configuration::updateValue('BTMEGAMENU_CAVAS', 0)
                && Configuration::updateValue('BTMEGAMENU_GROUP_DE', '')
                && Configuration::updateValue('BTMEGAMENU_CLEARCACHE_HOOK', '')
                && Configuration::updateValue('BTMEGAMENU_ADD_HOOK', 1)
                && Configuration::updateValue('BTMEGAMENU_CURRENT_SHOP', '')
                && $this->registerLeoHook()) {
            $res = true;
            $res &= $this->createTables();
            foreach (LeoBtmegamenuHelper::getModuleTabs() as $tab) {
                $newtab = new Tab();
                $newtab->class_name = $tab['class_name'];
                $newtab->id_parent = isset($tab['id_parent']) ? $tab['id_parent'] : 0;
                $newtab->module = 'leobootstrapmenu';
                foreach (Language::getLanguages(false) as $l) {
                    $newtab->name[$l['id_lang']] = $this->l($tab['name']);
                }
                $res &= $newtab->save();
            }
            
            Configuration::updateValue('AP_INSTALLED_LEOBOOTSTRAPMENU', '1');
            return (bool)$res;
        }

        return false;
    }

    public function uninstall()
    {
        if (parent::uninstall() && $this->unregisterLeoHook()) {
            foreach (LeoBtmegamenuHelper::getModuleTabs() as $tab) {
                $id = Tab::getIdFromClassName($tab['class_name']);
                if ($id) {
                    $tab = new Tab($id);
                    $tab->delete();
                }
            }
            
            $res = $this->deleteTables();
            
            Configuration::deleteByName('BTMEGAMENU_iscache');
            Configuration::deleteByName('BTMEGAMENU_cachetime');
            Configuration::deleteByName('BTMEGAMENU_CAVAS');
            Configuration::deleteByName('BTMEGAMENU_GROUP_DE');
            Configuration::deleteByName('BTMEGAMENU_CLEARCACHE_HOOK');
            Configuration::deleteByName('BTMEGAMENU_ADD_HOOK');
            Configuration::deleteByName('BTMEGAMENU_CURRENT_SHOP');
            
            return $res;
        }
        return false;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'btmegamenu`,
            `'._DB_PREFIX_.'btmegamenu_lang`,
            `'._DB_PREFIX_.'btmegamenu_shop`,
            `'._DB_PREFIX_.'btmegamenu_widgets`,
            `'._DB_PREFIX_.'btmegamenu_group`,
            `'._DB_PREFIX_.'btmegamenu_group_lang`
            ');
    }

    protected function createTables()
    {
        if ($this->installDataSample()) {
            return true;
        }
        $res = 1;
        include_once(dirname(__FILE__).'/install/install.php');
        return $res;
    }

    private function installDataSample()
    {
        if (!file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            return false;
        }
        require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');

        $sample = new Datasample(1);
        return $sample->processImport($this->name);
    }

    public function lazydatabase()
    {
        $sql = ' SELECT * FROM '._DB_PREFIX_.'btmegamenu_widgets WHERE id_shop='.$this->context->shop->id;
        $result = Db::getInstance()->executes($sql);
        
        foreach ($result as &$value) {
            $params = Tools::jsonDecode(call_user_func('base64'.'_decode', $value['params']), true);

            foreach ($params as $key => $param) {
                if (is_string($param) && strpos($param, '<img') !== false) {
                    preg_match_all('/<img[^>]+>/i', $param, $imgs);
                    $icheck = 0;

                    foreach ($imgs[0] as $img) {
                        //if have lazy break
                        if (strpos($img, 'lazy') === false && strpos($img, 'lazyOwl') === false && strpos($img, 'data-src') === false) {
                            $imga = str_replace('src', 'data-src', $img);

                            //has class add more class
                            if (strpos($imga, "class") !== false) {
                                $imga = str_replace('class="', 'class="lazy ', $imga);
                                $imga = str_replace("class='", "class='lazy ", $imga);
                            } else {
                                $imga = str_replace('<img', '<img class="lazy"', $imga);
                            }
                            $icheck = 1;
                            $params[$key] = str_replace($img, $imga, $params[$key]);
                        }
                    }
                }
            }

            if ($icheck) {
                $model = new LeoWidget($value['id_btmegamenu_widgets']);
                $model->params = call_user_func('base64'.'_encode', Tools::jsonEncode($params));

                $model->save();
            }
        }
    }


    public function lazyrolbackdatabase()
    {
        $sql = ' SELECT * FROM '._DB_PREFIX_.'btmegamenu_widgets WHERE id_shop='.$this->context->shop->id;
        $result = Db::getInstance()->executes($sql);
        foreach ($result as &$value) {
            $params = Tools::jsonDecode(call_user_func('base64'.'_decode', $value['params']), true);

            foreach ($params as $key => $param) {
                if (is_string($param) && strpos($param, '<img') !== false) {
                    preg_match_all('/<img[^>]+>/i', $param, $imgs);
                    $icheck = 0;
                    
                    foreach ($imgs[0] as $img) {
                        //if have lazy break
                        if (strpos($img, 'lazy ') !== false || strpos($img, '"lazy') !== false || strpos($img, "'lazy") !== false || strpos($img, " lazy") !== false) {
                            $imga = str_replace('data-src', 'src', $img);
                            $imga = str_replace('<img class="lazy"', '<img', $imga);
                            $imga = str_replace('lazy ', '', $imga);
                            $imga = str_replace(' lazy', '', $imga);

                            $icheck = 1;
                            
                            $params[$key] = str_replace($img, $imga, $params[$key]);
                        }
                    }
                }
            }

            if ($icheck) {
                $model = new LeoWidget($value['id_btmegamenu_widgets']);
                $model->params = call_user_func('base64'.'_encode', Tools::jsonEncode($params));

                $model->save();
            }
        }
    }

    public function postProcess()
    {
        if (count($this->errors) > 0) {
            $this->ajax = Tools::getValue('ajax') || Tools::isSubmit('ajax');
            if ($this->ajax) {
                $array = array('hasError' => true, 'errors' => $this->errors[0]);
                die(Tools::jsonEncode($array));
            }
            return;
        }
        
        //remove current id group and update current shop when change shop
        if (Configuration::get('BTMEGAMENU_GROUP_DE') != '') {
            $check_group_exist = new BtmegamenuGroup(Configuration::get('BTMEGAMENU_GROUP_DE'));
            if ($check_group_exist->id_shop != $this->context->shop->id && Tools::isSubmit('editgroup')) {
                Configuration::updateValue('BTMEGAMENU_GROUP_DE', '');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure=leobootstrapmenu');
                return false;
            }
        }
        
        if (Configuration::get('BTMEGAMENU_GROUP_DE') && Configuration::get('BTMEGAMENU_GROUP_DE') != '' && !Tools::getValue('id_group') && !Tools::isSubmit('addNewGroup') && !Tools::isSubmit('submitGroup') && !Tools::getValue('liveeditor') && !Tools::getValue('importgroup') && !Tools::getValue('importwidgets') && !Tools::getValue('doupdategrouppos') && !Tools::getValue('hook') && !Tools::getValue('correctmodule')) {
            $url = $this->context->link->getAdminLink('AdminModules').'&id_group='.Configuration::get('BTMEGAMENU_GROUP_DE').'&editgroup=1&tab_module=front_office_features&module_name=leobootstrapmenu&configure=leobootstrapmenu';
            Tools::redirectAdmin($url);
            return false;
        }
        
        if (Tools::getValue('hook')) {
            Configuration::updateValue('BTMEGAMENU_CLEARCACHE_HOOK', Tools::getValue('hook'));
            Configuration::updateValue('BTMEGAMENU_GROUP_DE', '');
            $tpl = 'views/templates/hook/megamenu.tpl';
            if (Tools::getValue('hook') == 'all') {
                $this->clearCache();
            } else {
                $this->_clearCache($tpl, Tools::getValue('hook').'_'.$this->name);
            }
        }
        
        if (Tools::isSubmit('save'.$this->name) && Tools::isSubmit('active')) {
            # add + edit menu
            $this->saveMenu();
        }
        if (Tools::isSubmit('save'.$this->name) && Tools::getValue('dodel')) {
            $this->deleteMenu();
        }
        if (Tools::isSubmit('save'.$this->name) && Tools::getValue('delete_many_menu')) {
            $this->deleteManyMenu();
        }
        if (Tools::isSubmit('save'.$this->name) && Tools::getValue('doduplicate')) {
            $this->duplicateMenu();
        }
        if (Tools::isSubmit('addmenuproductlayout')) {
            if ($this->postValidation()) {
                $this->addMenuProductLayout();
            }
        }
        if (Tools::isSubmit('submitGroup')) {
            # add + edit group
            if ($this->postValidation()) {
                $this->saveGroup();
            }
        }
        if (Tools::isSubmit('deletegroup')) {
            if ($this->postValidation()) {
                $this->deleteGroup();
            }
        }
        if (Tools::isSubmit('duplicategroup')) {
            if ($this->postValidation()) {
                $this->duplicateGroup();
            }
        }
        if (Tools::isSubmit('exportgroup')) {
            $this->exportGroup();
        }
        if (Tools::isSubmit('exportwidgets')) {
            $this->exportWidgets();
        }
        if (Tools::isSubmit('changeGStatus')) {
            if ($this->postValidation()) {
                $this->changeStatusGroup();
            }
        }
        
        if (Tools::isSubmit('importgroup')) {
            if ($this->postValidation()) {
                if ($this->importGroup()) {
                    $this->clearCache();
                    Configuration::updateValue('BTMEGAMENU_GROUP_DE', '');
                    Tools::redirectAdmin('index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&configure=leobootstrapmenu&success=importgroup');
                } else {
                    $this->html .= $this->displayError($this->l('The file could not be import.'));
                }
            }
        }
        
        if (Tools::isSubmit('importwidgets')) {
            if ($this->postValidation()) {
                if ($this->importWidgets()) {
                    $this->clearCache();
                    Configuration::updateValue('BTMEGAMENU_GROUP_DE', '');
                    Tools::redirectAdmin('index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&configure=leobootstrapmenu&success=importwidgets');
                } else {
                    $this->html .= $this->displayError($this->l('The file could not be import.'));
                }
            }
        }
        
        if (Tools::getValue('correctmodule')) {
            LeoBtmegamenuHelper::processCorrectModule();
        }
        
        if (Tools::getValue('doupdategrouppos') && Tools::isSubmit('updateGroupPosition')) {
            $this->changePositionGroup();
        }
        
        if (Tools::getValue('doupdatepos') && Tools::isSubmit('updatePosition')) {
            $this->changePositionMenu();
        }
        
        if (Tools::getValue('liveeditor') && Tools::getValue('do') == 'ajxmenuinfo') {
            $this->ajxmenuinfo();
        }
    }
    
    public function getContent()
    {
        $this->errors = array();
        if (!$this->access('configure')) {
            $this->errors[] = $this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error');
            $this->html .= $this->displayError($this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error'));
        }
        
        LeoBtmegamenuHelper::autoUpdateModule();
        $this->postProcess();

        $this->context->controller->addJqueryUI('ui.sortable');
        
        $this->displaySuccessMessage();

        return $this->html.$this->displayForm();
    }

    /**
     * show megamenu item configuration.
     */
    protected function showFormSetting($id_group = null)
    {
        $media_dir = $this->getMediaDir();
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/jquery.nestable.js');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/form.js');

        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/plugins/jquery.cookie-plugin.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.tabs.min.js');

        $this->context->controller->addCss(__PS_BASE_URI__.'js/jquery/ui/themes/base/jquery.ui.tabs.css');
        $this->context->controller->addCss(__PS_BASE_URI__.$media_dir.'css/admin/form.css');

        $return = '';
        $mod_group = new BtmegamenuGroup();
        $id_shop = $this->context->shop->id;
        $groups = $mod_group->getGroups(null, $id_shop);
        // check when change shop
        if (count($groups) == 0 && Configuration::get('BTMEGAMENU_GROUP_DE') != '') {
            Configuration::updateValue('BTMEGAMENU_GROUP_DE', '');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure=leobootstrapmenu');
            return false;
        }
        
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $this->group_data['title'][$lang['id_lang']] = '';
            $this->group_data['title_fo'][$lang['id_lang']] = '';
        }
        foreach ($groups as $key => $group) {
            if ($group['id_btmegamenu_group'] == Tools::getValue('id_group') || (!Tools::getValue('id_group') && !Tools::isSubmit('addNewGroup') && $group['id_btmegamenu_group'] == Configuration::get('BTMEGAMENU_GROUP_DE'))) {
                $this->current_group['id_group'] = $group['id_btmegamenu_group'];
                $this->current_group['title'] = $group['title'];

                $params = Tools::jsonDecode(LeoBtmegamenuHelper::base64Decode($group['params']), true);
                
                $this->current_group['group_type'] = ($params['group_type'] == 'horizontal')?'Horizontal':'Vertical';
                if ($params) {
                    $group_result = array();
                }
                foreach ($params as $k => $v) {
                    $group_result[$k] = $v;
                }
                $obj_group = new BtmegamenuGroup($group['id_btmegamenu_group']);
                foreach ($languages as $lang) {
                    $group_result['title'][$lang['id_lang']] = $obj_group->title[$lang['id_lang']];
                    $group_result['title_fo'][$lang['id_lang']] = $obj_group->title_fo[$lang['id_lang']];
                }
                // $group_result['title'] = $group['title'];
                $group_result['id_btmegamenu_group'] = $group['id_btmegamenu_group'];
                $group_result['id_shop'] = $group['id_shop'];
                $group_result['hook'] = $group['hook'];
                $group_result['active'] = $group['active'];

                if ($group_result) {
                    $this->group_data = array_merge($this->group_data, $group_result);
                }
            }

            $groups[$key]['status'] = $this->displayGStatus($group['id_btmegamenu_group'], $group['active']);
        }
        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            'update_group_position_link' => $this->context->link->getAdminLink('AdminModules').'&configure=leobootstrapmenu',
            'groups' => $groups,
            'curentGroup' => $this->current_group['id_group'],
            'languages' => $this->context->controller->getLanguages(),
            'exportLink'        => $this->context->link->getAdminLink('AdminModules').'&configure=leobootstrapmenu'.'&exportgroup=1',
            'exportWidgetsLink' => $this->context->link->getAdminLink('AdminModules').'&configure=leobootstrapmenu'.'&exportwidgets=1',
            'msecure_key' => $this->secure_key,
            'list_hook' => $this->hook_support,
            'clearcache_hook' => Configuration::get('BTMEGAMENU_CLEARCACHE_HOOK'),
        ));
        $return .= $this->display(_PS_MODULE_DIR_.'leobootstrapmenu/leobootstrapmenu.php', 'group_list.tpl');
        
        if ((isset($this->renderGroupConfig) && $this->renderGroupConfig) || Tools::isSubmit('editgroup') || Tools::isSubmit('addNewGroup')) {
            $return .= $this->renderGroupConfig();
        };

        if (isset($id_group)) {
            return $return.$this->renderFormConfig();
        }
        return $return;
    }
    
    public function renderGroupConfig()
    {
        $description = $this->l('Add New Group');
        if (!Tools::isSubmit('deletegroup') && !Tools::isSubmit('duplicategroup') && !Tools::isSubmit('addmenuproductlayout') && !Tools::isSubmit('importgroup') && !Tools::isSubmit('importwidgets') && !Tools::isSubmit('addNewGroup') && $this->current_group['id_group']) {
            $description = $this->l('You are editting group:').' '.$this->current_group['title'];
        }

        $select_hook = array();
        $select_hook[] = array('id' => '', 'name' => '');
        foreach ($this->hook_support as $value) {
            $select_hook[] = array('id' => $value, 'name' => $value);
        }
        
        $hidden_config = array('hidden-lg-down' => $this->l('Hidden in Large devices'), 'hidden-md-down' => $this->l('Hidden in Medium devices'),
            'hidden-sm-down' => $this->l('Hidden in Small devices'), 'hidden-xs-down' => $this->l('Hidden in Extra small devices'), 'hidden-sp' => $this->l('Hidden in Smart Phone'));

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $description,
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Group Name'),
                        'name' => 'title_group',
                        'lang' => true,
                        'required' => 1
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title on front end'),
                        'name' => 'title_fo_group',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show in Hook'),
                        'name' => 'hook_group',
                        'options' => array(
                            'query' => $select_hook,
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Group Type'),
                        'name' => 'group[group_type]',
                        'id' => 'group_type',
                        'options' => array('query' => array(
                                array('id' => 'horizontal', 'name' => $this->l('Horizontal')),
                                array('id' => 'vertical', 'name' => $this->l('Vertical')),
                            ),
                            'id' => 'id',
                            'name' => 'name'),
                        'default' => 'horizontal',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show Canvas'),
                        'name' => 'group[show_cavas]',
                        'id' => 'show_cavas',
                        'options' => array('query' => array(
                                array('id' => '1', 'name' => $this->l('Yes')),
                                array('id' => '0', 'name' => $this->l('No')),
                            ),
                            'id' => 'id',
                            'name' => 'name'),
                        'default' => '',
                        'class' => 'group-type-group',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Type Sub'),
                        'name' => 'group[type_sub]',
                        'id' => 'type_sub',
                        'options' => array('query' => array(
                                array('id' => 'auto', 'name' => $this->l('Auto')),
                                array('id' => 'right', 'name' => $this->l('Right')),
                                array('id' => 'left', 'name' => $this->l('Left')),
                            ),
                            'id' => 'id',
                            'name' => 'name'),
                        'default' => '',
                        'class' => 'group-type-group',
                    ),
                    array(
                        'type' => 'group_class',
                        'label' => $this->l('Group Class'),
                        'name' => 'group[group_class]'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable'),
                        'name' => 'active_group',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save Group Configuration'),
                    'class' => 'btn btn-danger')
            ),
        );

        if (Tools::isSubmit('id_group') && BtmegamenuGroup::groupExists((int)Tools::getValue('id_group'))) {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_group');
        } else if ($this->current_group['id_group'] && BtmegamenuGroup::groupExists($this->current_group['id_group'])) {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_group');
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->name_controller = 'slideshow';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGroup';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getGroupFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'hidden_config' => $hidden_config
        );

        return $helper->generateForm(array($fields_form));
    }
    
    public function getGroupFieldsValues()
    {
        $group = array();
        $field = array('id_btmegamenu_group', 'title', 'title_fo', 'id_shop', 'hook', 'active');
        foreach ($this->group_data as $key => $value) {
            if (in_array($key, $field)) {
                if ($key == 'id_btmegamenu_group') {
                    # module validation
                    $group['id_group'] = $value;
                } else {
                    # module validation
                    $group[$key.'_group'] = $value;
                }
                continue;
            }
            $group['group['.$key.']'] = $value;
        }
        return $group;
    }
    
    public function renderFormConfig()
    {
        $this->widget->loadEngines();
            
        $id_lang = $this->context->language->id;
        $id_btmegamenu = (int)Tools::getValue('id_btmegamenu');
        $id_group = (int)Tools::getValue('id_group');
        $obj = new Btmegamenu($id_btmegamenu);
        $obj->setModule($this);
        $tree = $obj->getTree(null, $id_group);
        $categories = LeoBtmegamenuHelper::getCategories();
        $cms_categories = LeoBtmegamenuHelper::getCMSCategories();
        $manufacturers = Manufacturer::getManufacturers(false, $id_lang, true);
        $suppliers = Supplier::getSuppliers(false, $id_lang, true);
        $cmss = CMS::listCms($this->context->language->id, false, true);
        $menus = $obj->getDropdown(null, $obj->id_parent, $id_group);
        if (isset($id_btmegamenu) && $id_btmegamenu != '') {
            foreach ($menus as $key => $menus_val) {
                if ($menus_val ['id'] == $id_btmegamenu) {
                    unset($menus[$key]);
                }
            }
        }
        $page_controller = array();
        foreach (Meta::getPages() as $page) {
            if (strpos($page, 'module') === false) {
                $array_tmp = array();
                $array_tmp['link'] = $page;
                $array_tmp['name'] = $page;
                array_push($page_controller, $array_tmp);
            }
        }
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $soption = array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled')
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled')
            )
        );

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => ($id_btmegamenu)?$this->l('Edit MegaMenu Item.'):$this->l('Create New MegaMenu Item.'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Megamenu ID'),
                    'name' => 'id_btmegamenu',
                    'default' => 0,
                ),
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Group ID'),
                    'name' => 'id_group',
                    'default' => $id_group,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'required' => true,
                    'lang' => true,
                    'default' => '',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Sub Title'),
                    'lang' => true,
                    'name' => 'text',
                    'cols' => 40,
                    'rows' => 10,
                    'default' => '',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Parent ID'),
                    'name' => 'id_parent',
                    'options' => array('query' => $menus,
                        'id' => 'id',
                        'name' => 'title'),
                    'default' => 'url',
                ),
                
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is Active'),
                    'name' => 'active',
                    'values' => $soption,
                    'default' => '1',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Title'),
                    'name' => 'show_title',
                    'values' => $soption,
                    'default' => '1',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Show submenu with'),
                    'name' => 'sub_with',
                    'options' => array('query' => array(
                                array('id' => 'none', 'name' => $this->l('None')),
                                array('id' => 'submenu', 'name' => $this->l('Submenu')),
                                array('id' => 'widget', 'name' => $this->l('Widget')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'default' => 'submenu',
                    'desc' => $this->l('Turn on (select type) or turn off submenu'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Menu Type'),
                    'name' => 'type',
                    'id' => 'menu_type',
                    'desc' => $this->l('Select a menu link type and fill data for following input'),
                    'options' => array('query' => array(
                            array('id' => 'url', 'name' => $this->l('Url')),
                            array('id' => 'category', 'name' => $this->l('Category')),
                            array('id' => 'product', 'name' => $this->l('Product')),
                            array('id' => 'manufacture', 'name' => $this->l('Manufacture')),
                            array('id' => 'supplier', 'name' => $this->l('Supplier')),
                            array('id' => 'cms', 'name' => $this->l('Cms')),
                            array('id' => 'cms_category', 'name' => $this->l('Cms Category')),
                            array('id' => 'html', 'name' => $this->l('Html')),
                            array('id' => 'controller', 'name' => $this->l('Page Controller'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'default' => 'url',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product ID'),
                    'name' => 'product_type',
                    'id' => 'product_type',
                    'class' => 'menu-type-group',
                    'default' => '',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('CMS Type'),
                    'name' => 'cms_type',
                    'id' => 'cms_type',
                    'options' => array('query' => $cmss,
                        'id' => 'id_cms',
                        'name' => 'meta_title'),
                    'default' => '',
                    'class' => 'menu-type-group',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('URL'),
                    'name' => 'url',
                    'id' => 'url_type',
                    'required' => true,
                    'lang' => true,
                    'class' => 'url-type-group-lang',
                    'default' => '',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category Type'),
                    'name' => 'category_type',
                    'id' => 'category_type',
                    'options' => array('query' => $categories,
                        'id' => 'id_category',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'menu-type-group',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('CMS Category Type'),
                    'name' => 'cms_category_type',
                    'id' => 'cms_category_type',
                    'options' => array('query' => $cms_categories,
                        'id' => 'id_cms_category',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'menu-type-group',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Manufacture Type'),
                    'name' => 'manufacture_type',
                    'id' => 'manufacture_type',
                    'options' => array('query' => $manufacturers,
                        'id' => 'id_manufacturer',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'menu-type-group',
                    
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Supplier Type'),
                    'name' => 'supplier_type',
                    'id' => 'supplier_type',
                    'options' => array('query' => $suppliers,
                        'id' => 'id_supplier',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'menu-type-group',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('HTML Type'),
                    'name' => 'content_text',
                    'desc' => $this->l('This menu is only for display content,PLease do not select it for menu level 1'),
                    'lang' => true,
                    'default' => '',
                    'autoload_rte' => true,
                    'class' => 'menu-type-group-lang',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('List Page Controller'),
                    'name' => 'controller_type',
                    'id' => 'controller_type',
                    'options' => array('query' => $page_controller,
                        'id' => 'link',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'menu-type-group',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Parameter of page controller'),
                    'name' => 'controller_type_parameter',
                    'id' => 'controller_type_parameter',
                    'default' => '',
                    'class' => 'menu-type-group',
                    'desc' => 'Eg: ?a=1&b=2',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Target Open'),
                    'name' => 'target',
                    'options' => array('query' => array(
                            array('id' => '_self', 'name' => $this->l('Self')),
                            array('id' => '_blank', 'name' => $this->l('Blank')),
                            array('id' => '_parent', 'name' => $this->l('Parent')),
                            array('id' => '_top', 'name' => $this->l('Top'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'default' => '_self',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Menu Class'),
                    'name' => 'menu_class',
                    'display_image' => true,
                    'default' => ''
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Menu Icon Font'),
                    'name' => 'icon_class',
                    'display_image' => true,
                    'default' => '',
                    'desc' => $this->context->smarty->fetch($this->local_path.'views/templates/admin/icon_front_guide.tpl'),
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Or Menu Icon Image'),
                    'name' => 'image',
                    'display_image' => true,
                    'default' => '',
                    'desc' => $this->l('Use image icon if no use icon Class'),
                    'thumb' => '',
                    'title' => $this->l('Icon Preview'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Group Submenu'),
                    'name' => 'is_group',
                    'values' => $soption,
                    'default' => '0',
                    'desc' => $this->l('Group all sub menu to display in same level')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Column'),
                    'name' => 'colums',
                    'values' => $soption,
                    'default' => '1',
                    'desc' => $this->l('Set each sub menu item as column')
                ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Group access'),
                    'name' => 'groupBox',
                    'values' => Group::getGroups(Context::getContext()->language->id),
                    'hint' => $this->l('Mark all of the customer groups which you would like to have access to this menu.'),
                    'default' => '1',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save Menu Item'),
                'class' => 'button btn btn-danger'
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'save'.$this->name;
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($obj),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        $live_editor_url = AdminController::$currentIndex.'&configure='.$this->name.'&liveeditor=1&id_group='.$id_group.'&token='.Tools::getAdminTokenLite('AdminModules');

        $action = AdminController::$currentIndex.'&configure='.$this->name.'&editgroup=1&id_group='.$id_group.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');
        $helper->toolbar_btn = array(
            'back' => array(
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
        $successful = 0;
        if (Tools::getValue('successful') == 1) {
            $successful = 1;
        }
        $addnew = AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules').'&configure='.$this->name.'&editgroup=1&id_group='.$id_group;
        
        $this->context->smarty->assign(array(
            'successful' => $successful,
            'live_editor_url' => $live_editor_url,
            'addnew' => $addnew,
            'action' => $action,
            'tree' => $tree,
            'admin_widget_link' => Context::getContext()->link->getAdminLink('AdminLeoWidgets'),
            'helper_form' => $helper->generateForm($this->fields_form),
            'current_group_title' => $this->current_group['title'],
            'current_group_type' => $this->current_group['group_type'],
            'id_group' => $id_group,
        ));
        
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        return $output;
    }

    public function getConfigFieldsValues($obj)
    {
        $languages = Language::getLanguages(false);
        $fields_values = array();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $this->image_base_url = Tools::htmlentitiesutf8($protocol.$_SERVER['HTTP_HOST'].__PS_BASE_URI__).'themes/'.$this->theme_name.'/'.$this->getThemeMediaDir('img').'img/icons/';
        foreach ($this->fields_form as $k => $f) {
            foreach ($f['form']['input'] as $j => $input) {
                if (isset($obj->{trim($input['name'])})) {
                    $data = $obj->{trim($input['name'])};

                    if ($input['name'] == 'image' && $data) {
                        $thumb = $this->image_base_url.$data;
                        $this->fields_form[$k]['form']['input'][$j]['thumb'] = $thumb;
                    }

                    if (isset($input['lang'])) {
                        foreach ($languages as $lang) {
                            # validate module
                            $fields_values[$input['name']][$lang['id_lang']] = isset($data[$lang['id_lang']]) ? $data[$lang['id_lang']] : $input['default'];
                        }
                    } else {
                        # validate module
                        $fields_values[$input['name']] = isset($data) ? $data : $input['default'];
                    }
                } else {
                    if (isset($input['lang'])) {
                        foreach ($languages as $lang) {
                            $v = Tools::getValue('title', Configuration::get($input['name'], $lang['id_lang']));
                            $fields_values[$input['name']][$lang['id_lang']] = $v ? $v : $input['default'];
                        }
                    } else {
                        $v = Tools::getValue($input['name'], Configuration::get($input['name']));
                        $fields_values[$input['name']] = $v ? $v : $input['default'];
                    }
                    if ($input['name'] == $obj->type.'_type') {
                        # validate module
                        $fields_values[$input['name']] = $obj->item;
                    }
                    if ($input['name'] == $obj->type.'_type_parameter') {
                        $fields_values[$input['name']] = $obj->item_parameter;
                    }
                }
            }
        }

        $id_menu_groups= $obj->getGroups(null, true);
        $groups = Group::getGroups($this->context->language->id);
        foreach ($groups as $group) {
            $fields_values['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], (in_array($group['id_group'], $id_menu_groups) || (empty($id_menu_groups) && !$obj->id) || in_array('all', $id_menu_groups)));
        }
        return $fields_values;
    }

    /**
     * render menu tree using for editing
     */
    protected function ajxgenmenu()
    {
        $parent = '1';
        $params = array('params' => array());
        $get_params_widget = array();
        $list_root_menu = Btmegamenu::getMenusRoot((int)Tools::getValue('id_group'));
        /* unset mega menu configuration */
        if (Tools::getValue('doreset')) {
            # validate module
            Btmegamenu::resetParamsWidget();
        }
        if (count($list_root_menu) > 0) {
            foreach ($list_root_menu as $list_root_menu_item) {
                $menu_obj = new Btmegamenu($list_root_menu_item['id_btmegamenu']);
                $menu_params_widget = $menu_obj->getParamsWidget();
                if ($menu_params_widget != '') {
                    $get_params_widget[$list_root_menu_item['id_btmegamenu']] = Tools::jsonDecode(LeoBtmegamenuHelper::base64Decode($menu_params_widget));
                }
            }
        }
        $params['params'] = $get_params_widget;
        $obj = new Btmegamenu($parent);
        $obj->setModule($this);
        $tree = $obj->getFrontTree(0, true, $params['params'], Tools::getValue('id_group'));
        $this->context->smarty->assign(array(
            'tree' => $tree,
        ));
        echo $this->context->smarty->fetch($this->local_path.'views/templates/admin/ajxgenmenu.tpl');
    }
    
    /**
     * re-load list widget
     */
    protected function loadwidget()
    {
        $id_shop = $this->context->shop->id;
        $model = $this->widget;
        $widgets = $model->getWidgets($id_shop);
        $type_menu = array('carousel', 'categoriestabs', 'manucarousel', 'map', 'producttabs', 'tab', 'accordion', 'specialcarousel');
        foreach ($widgets as $key => $widget) {
            if (in_array($widget['type'], $type_menu)) {
                unset($widgets[$key]);
            }
        }
        
        $return = '';
        $this->context->smarty->assign(array(
            'widgets' => $widgets,
        ));
        $return = $this->context->smarty->fetch($this->local_path.'views/templates/admin/list_widget.tpl');
        
        echo $return;
    }

    /**
     * Ajax Menu : Save
     */
    public function ajxmenuinfo()
    {
        if (Tools::getValue('params')) {
            $params = trim(html_entity_decode(Tools::getValue('params')));
            $array_params = Tools::jsonDecode($params, true);
            if (count($array_params) > 0) {
                foreach ($array_params as $key => $value) {
                    $menu_obj = new Btmegamenu((int)$key);
                    $menu_obj->updateParamsWidget(LeoBtmegamenuHelper::base64Encode(Tools::jsonEncode($value)));
                }
            }
            $this->clearCache();
        }
    }

    public function getWidget()
    {
        if (Tools::getIsset('allWidgets')) {
            $dataForm = Tools::jsonDecode(Tools::getValue('dataForm'), 1);
            foreach ($dataForm as $key => &$widget) {
                # validate module
                unset($key);
                $widget['html'] = $this->renderLeoWidget($widget['id_shop'], $widget['id_widget']);
            }
            die(Tools::jsonEncode($dataForm));
        }
    }
    
    /**
     * show live editor tools
     */
    protected function showLiveEditorSetting()
    {
        $media_dir = $this->getMediaDir();
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.dialog.min.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.draggable.min.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.droppable.min.js');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/form.js');
        $this->context->controller->addCss(__PS_BASE_URI__.$media_dir.'css/admin/liveeditor.css');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/liveeditor.js');
        $tcss = _PS_ROOT_DIR_.'/themes/'.$this->context->shop->theme->getName().'/'.$this->getThemeMediaDir('css').'css/megamenu.css';

        if (file_exists($tcss)) {
            # validate module
            $this->context->controller->addCss(_THEMES_DIR_.$this->context->shop->theme->getName().'/'.$this->getThemeMediaDir('css').'css/megamenu.css');
        } else {
            # validate module
            $this->context->controller->addCss(__PS_BASE_URI__.$media_dir.'css/megamenu.css');
        }
        
        $id_group = Tools::getValue('id_group');
        $group_obj = new BtmegamenuGroup($id_group, $this->context->language->id);
        $base_config_url = AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getValue('token');
        
        $liveedit_action = $base_config_url.'&liveeditor=1&id_group='.$id_group.'&do=livesave';
        $action_backlink = $base_config_url.'&editgroup=1&id_group='.$id_group;
        $action_widget = $base_config_url.'&liveeditor=1&id_group='.$id_group.'&do=getwidget';
        $action_loadwidget = $base_config_url.'&liveeditor=1&do=loadwidget';
        $ajxgenmenu = $base_config_url.'&liveeditor=1&id_group='.$id_group.'&do=ajxgenmenu';
        $ajxmenuinfo = $base_config_url.'&liveeditor=1&id_group='.$id_group.'&do=ajxmenuinfo';
        $group_title = $group_obj->title;
        
        $params = Tools::jsonDecode(LeoBtmegamenuHelper::base64Decode($group_obj->params), true);
        $group_type = $params['group_type'];
        $group_type_sub = $params['type_sub'];
        $id_shop = $this->context->shop->id;
        $shop = Shop::getShop($id_shop);
        if (!empty($shop)) {
            $live_site_url = $shop['uri'];
        } else {
            $live_site_url = __PS_BASE_URI__;
        }
        $model = $this->widget;
        $widgets = $model->getWidgets($id_shop);
        $type_menu = array('carousel', 'categoriestabs', 'manucarousel', 'map', 'producttabs', 'tab', 'accordion', 'specialcarousel');
        foreach ($widgets as $key => $widget) {
            if (in_array($widget['type'], $type_menu)) {
                unset($widgets[$key]);
            }
        }
        
        $this->context->smarty->assign(array(
            'liveedit_action' => $liveedit_action,
            'widgets' => $widgets,
            'group_title' => $group_title,
            'group_type' => $group_type,
            'group_type_sub' => $group_type_sub,
            'live_site_url' => $live_site_url,
            'action_backlink' => $action_backlink,
            'ajxgenmenu' => $ajxgenmenu,
            'ajxmenuinfo' => $ajxmenuinfo,
            'action_widget' => $action_widget,
            'action_loadwidget' => $action_loadwidget,
            'id_shop' => $id_shop,
            'link' => $this->context->link,
        ));
        
        return $this->display(_PS_MODULE_DIR_.'leobootstrapmenu/leobootstrapmenu.php', 'liveeditor.tpl');
    }

    public function displayForm()
    {
        if (Tools::getValue('liveeditor')) {
            if (Tools::getValue('do')) {
                switch (Tools::getValue('do')) {
                    case 'ajxmenuinfo':
                        echo $this->ajxgenmenu();
                        break;
                    case 'ajxgenmenu':
                        echo $this->ajxgenmenu();
                        break;
                    case 'loadwidget':
                        echo $this->loadwidget();
                        break;
                    case 'getwidget':
                        echo $this->getWidget();
                        break;
                    default:
                        break;
                }
                die;
            } else {
                # validate module
                return $this->showLiveEditorSetting();
            }
        } else {
            # validate module
            if (Tools::getValue('id_group')) {
                $id_group = Tools::getValue('id_group');
                return $this->showFormSetting($id_group);
            } else {
                return $this->showFormSetting();
            }
        }
    }

    /**
     * render widgets
     */
    public function renderLeoWidget($id_shop, $widgets)
    {
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $widgets = explode('|wid-', '|'.$widgets);
        $this->context->smarty->assign(array(
            'link' => $this->context->link,
        ));
        if (!empty($widgets)) {
            unset($widgets[0]);
            $model = $this->widget;
            $model->setTheme(Context::getContext()->shop->theme->getName());
            $model->langID = $this->context->language->id;
            $model->loadWidgets($id_shop);
            $model->loadEngines();
            $output = '';
            foreach ($widgets as $wid) {
                $content = $model->renderContent($wid);
                $output .= $this->getWidgetContent($wid, $content['type'], $content['data']);
            }
            return $output;
        }
        return '';
    }

    public function getWidgetContent($id, $type, $data, $show_widget_id = 1)
    {
        # validate module
        unset($show_widget_id);
        
        $data['id_lang'] = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $model = $this->widget;
        $widgets = $model->getWidgets($id_shop);
        $type_menu = array('carousel', 'categoriestabs', 'manucarousel', 'map', 'producttabs', 'tab', 'accordion', 'specialcarousel');
        foreach ($widgets as $key => $widget) {
            if (in_array($widget['type'], $type_menu)) {
                unset($widgets[$key]);
            }
        }
        $current_context = Context::getContext();
        $this->smarty->assign($data);
        $this->smarty->assign('id_widget', $id);
        $this->smarty->assign('widgets', $widgets);
        $this->smarty->assign('show_widget_bo', $current_context->controller->controller_type);
        $output = $this->display(_PS_MODULE_DIR_.'leobootstrapmenu/leobootstrapmenu.php', 'views/templates/hook/widgets/widget_'.$type.'.tpl');
        return $output;
    }

    public function checkFolderIcon()
    {
        if (file_exists($this->img_path) && is_dir($this->img_path)) {
            return;
        }
        if (!file_exists($this->img_path) && !is_dir($this->img_path)) {
            @mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/', 0777, true);
            // update direction css, js, img for 1.7.4.0
            @mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/', 0777, true);
            @mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/', 0777, true);
            // update direction css, js, img for 1.7.4.0
            @mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/', 0777, true);
            
            if (!file_exists(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
                @copy(_PS_IMG_DIR_.'index.php', _PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/index.php');
            }
            if (!file_exists(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
                @copy(_PS_IMG_DIR_.'index.php', _PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/index.php');
            }
            @mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/img/', 0777, true);
            @mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/img/', 0777, true);
            if (!file_exists(_PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/img/index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
                @copy(_PS_IMG_DIR_.'index.php', _PS_ALL_THEMES_DIR_.$this->theme_name.'/modules/'.$this->name.'/img/index.php');
            }
            if (!file_exists(_PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/img/index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
                @copy(_PS_IMG_DIR_.'index.php', _PS_ALL_THEMES_DIR_.$this->theme_name.'/assets/img/modules/'.$this->name.'/img/index.php');
            }
            @mkdir($this->img_path, 0777, true);
            if (!file_exists($this->img_path.'index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
                @copy(_PS_IMG_DIR_.'index.php', $this->img_path.'index.php');
            }
        }
    }

    public function displayGStatus($id_group, $active)
    {
        # Status Image
        $title = ((int)$active == 0 ? $this->l('Click to Enabled') : $this->l('Click to Disabled'));
        $img = ((int)$active == 0 ? 'disabled.gif' : 'enabled.gif');

        # Status Link
        if ($active == BtmegamenuGroup::GROUP_STATUS_DISABLE) {
            $change_group_status = BtmegamenuGroup::GROUP_STATUS_ENABLE;
        } elseif ($active == BtmegamenuGroup::GROUP_STATUS_ENABLE) {
            $change_group_status = BtmegamenuGroup::GROUP_STATUS_DISABLE;
        }

        $html = '<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&changeGStatus='.$change_group_status.'&id_group='.(int)$id_group.'" title="'.$title.'"><img src="'._PS_ADMIN_IMG_.''.$img.'" alt="" /></a>';
        return $html;
    }
    
    public function postValidation()
    {
        $errors = array();

        if (Tools::isSubmit('submitGroup')) {
            if (Tools::isSubmit('id_group')) {
                if (!Validate::isInt(Tools::getValue('id_group')) && !BtmegamenuGroup::groupExists(Tools::getValue('id_group'))) {
                    $errors[] = $this->l('Invalid id_group');
                }
            }
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->error_text .= implode('<br>', $errors);
            $this->html .= $this->displayError(implode('<br />', $errors));
            return false;
        }

        /* Returns if validation is ok */
        return true;
    }

    /**
     * Run only one when install/change Theme_of_Leo
     */
    public function hookActionAdminBefore($params)
    {
        if (isset($params) && isset($params['controller']) && isset($params['controller']->theme_manager)) {
            // Validate : call hook from theme_manager
        } else {
            // Other module call this hook -> duplicate data
            return;
        }
        
        $this->unregisterHook('actionAdminBefore');
        
        # FIX : update Prestashop by 1-Click module -> NOT NEED RESTORE DATABASE
        $ap_version = Configuration::get('AP_CURRENT_VERSION');
        if ($ap_version != false) {
            $ps_version = Configuration::get('PS_VERSION_DB');
            $versionCompare =  version_compare($ap_version, $ps_version);
            if ($versionCompare != 0) {
                // Just update Prestashop
                Configuration::updateValue('AP_CURRENT_VERSION', $ps_version);
                return;
            }
        }
        
        # WHENE INSTALL THEME, INSERT HOOK FROM DATASAMPLE IN THEME
        $hook_from_theme = false;
        if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
            $sample = new Datasample();
            if ($sample->processHook($this->name)) {
                $hook_from_theme = true;
            };
        }
        
        # INSERT HOOK FROM MODULE_DATASAMPLE
        if ($hook_from_theme == false) {
            $this->registerLeoHook();
        }
        
        # WHEN INSTALL MODULE, NOT NEED RESTORE DATABASE IN THEME
        $install_module = (int)Configuration::get('AP_INSTALLED_LEOBOOTSTRAPMENU', 0);
        if ($install_module) {
            // next : allow restore sample
            Configuration::updateValue('AP_INSTALLED_LEOBOOTSTRAPMENU', '0');
            return;
        }
        
        # INSERT DATABASE FROM THEME_DATASAMPLE
        if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
            $sample = new Datasample();
            $sample->processImport($this->name);
        }
    }
    
    /**
     * Common method
     * Resgister all hook for module
     */
    public function registerLeoHook()
    {
        $res = true;
        $res &= $this->registerHook('header');
        foreach ($this->hook_support as $value) {
            $res &= $this->registerHook($value);
        }
        # Multishop create new shop
        $res &= $this->registerHook('actionAdminShopControllerSaveAfter');
        $res &= $this->registerHook('actionAdminControllerSetMedia');
        
        return $res;
    }

    public function unregisterLeoHook()
    {
        $res = true;
        $res &= $this->unregisterHook('header');
        foreach ($this->hook_support as $value) {
            $res &= $this->unregisterHook($value);
        }
        $res &= $this->unregisterHook('actionAdminShopControllerSaveAfter');
        $res &= $this->unregisterHook('actionAdminControllerSetMedia');
        return $res;
    }
    
    public function importGroup()
    {
        $this->renderGroupConfig = true;
        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['import_file']['name'], '.'), 1));
        if (isset($_FILES['import_file']) && $type == 'txt' && isset($_FILES['import_file']['tmp_name']) && !empty($_FILES['import_file']['tmp_name'])) {
            $content = Tools::file_get_contents($_FILES['import_file']['tmp_name']);
            $content = Tools::jsonDecode(LeoBtmegamenuHelper::base64Decode($content), true);
            if (!is_array($content) || !isset($content['id_btmegamenu_group']) || $content['id_btmegamenu_group'] == '') {
                return false;
            }
            $language_field = array('title', 'text', 'url', 'description', 'content_text', 'submenu_content_text');
            $languages = Language::getLanguages();
            $shop_id = $this->context->shop->id;
            $lang_list = array();
            foreach ($languages as $lang) {
                # module validation
                $lang_list[$lang['iso_code']] = $lang['id_lang'];
            }

            $override_group = Tools::getValue('override_group');
            $override_widget = Tools::getValue('override_widget');
            if ($override_group && BtmegamenuGroup::groupExists($content['id_btmegamenu_group'], $shop_id)) {
                $mod_group = new BtmegamenuGroup($content['id_btmegamenu_group']);
                //edit group
                $mod_group = BtmegamenuGroup::setDataForGroup($mod_group, $content, true);
                if (!$mod_group->update()) {
                    # module validation
                    return false;
                }
                $this->removeAllMenuOfGroup($content['id_btmegamenu_group']);

                if (count($content['list_menu']) > 0) {
                    $list_new_id = array();
                    foreach ($content['list_menu'] as $menu) {
                        $mod_menu = new Btmegamenu();
                        foreach ($menu as $key => $val) {
                            if (in_array($key, $language_field)) {
                                foreach ($val as $key_lang => $val_lang) {
                                    # module validation
                                    $mod_menu->{$key}[$lang_list[$key_lang]] = $val_lang;
                                }
                            } else {
                                # module validation
                                if ($key == 'id_group') {
                                    $mod_menu->{$key} = $mod_group->id;
                                } elseif ($key == 'id_parent') {
                                    if ($val != 0) {
                                        $mod_menu->{$key} = $list_new_id[$val];
                                    } else {
                                        $mod_menu->{$key} = $val;
                                    }
                                } else {
                                    $mod_menu->{$key} = $val;
                                }
                            }
                        }
                        
                        $mod_menu->id = 0;
                        if (!$mod_menu->add()) {
                            return false;
                        }
                        $list_new_id[$menu['id_btmegamenu']] = $mod_menu->id;
                    }
                }
            } else {
                $mod_group = new BtmegamenuGroup();
                $mod_group = BtmegamenuGroup::setDataForGroup($mod_group, $content, false);
                if (!$mod_group->add()) {
                    # module validation
                    return false;
                }
                
                if (count($content['list_menu']) > 0) {
                    $list_new_id = array();
                    foreach ($content['list_menu'] as $menu) {
                        $mod_menu = new Btmegamenu();
                        foreach ($menu as $key => $val) {
                            if (in_array($key, $language_field)) {
                                foreach ($val as $key_lang => $val_lang) {
                                    # module validation
                                    $mod_menu->{$key}[$lang_list[$key_lang]] = $val_lang;
                                }
                            } else {
                                # module validation
                                if ($key == 'id_group') {
                                    $mod_menu->{$key} = $mod_group->id;
                                } elseif ($key == 'id_parent') {
                                    if ($val != 0) {
                                        $mod_menu->{$key} = $list_new_id[$val];
                                    } else {
                                        $mod_menu->{$key} = $val;
                                    }
                                } else {
                                    $mod_menu->{$key} = $val;
                                }
                            }
                        }
                        
                        $mod_menu->id = 0;
                        if (!$mod_menu->add()) {
                            return false;
                        }
                        $list_new_id[$menu['id_btmegamenu']] = $mod_menu->id;
                    }
                }
            }
            //import widget
            if (count($content['list_widget']) > 0) {
                if (!$this->processImportWidgets($content['list_widget'], $override_widget, $shop_id)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function importWidgets()
    {
        $this->renderGroupConfig = true;
        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['import_widgets_file']['name'], '.'), 1));
        if (isset($_FILES['import_widgets_file']) && $type == 'txt' && isset($_FILES['import_widgets_file']['tmp_name']) && !empty($_FILES['import_widgets_file']['tmp_name'])) {
            $content = Tools::file_get_contents($_FILES['import_widgets_file']['tmp_name']);
            $content = Tools::jsonDecode(LeoBtmegamenuHelper::base64Decode($content), true);
            $override_import_widgets = Tools::getValue('override_import_widgets');
            $shop_id = $this->context->shop->id;
            if (count($content) > 0) {
                if (!$this->processImportWidgets($content, $override_import_widgets, $shop_id)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    
    public function processImportWidgets($list_widget, $override, $shop_id)
    {
        $languages = Language::getLanguages();
        
        if (!is_array($list_widget) || !isset($list_widget[0]['id_btmegamenu_widgets']) || $list_widget[0]['id_btmegamenu_widgets'] == '') {
            return false;
        }
        foreach ($list_widget as $widget) {
            $check_widget_exists = LeoWidget::getWidetByKey($widget['key_widget'], $shop_id);
            if ($check_widget_exists['id'] != '' && $override) {
                $mod_widget = new LeoWidget($check_widget_exists['id']);
            }
            if (($override && $check_widget_exists['id'] == '') || (!$override && $check_widget_exists['id'] == '')) {
                $mod_widget = new LeoWidget();
            }
            $mod_widget->name = $widget['name'];
            $mod_widget->type = $widget['type'];
            $params_widget = LeoBtmegamenuHelper::base64Decode($widget['params']);
            
            foreach ($languages as $lang) {
                # module validation
                if (strpos($params_widget, '_'.$lang['iso_code'].'"') !== false) {
                    $params_widget = str_replace('_'.$lang['iso_code'].'"', '_'.$lang['id_lang'].'"', $params_widget);
                }
            }
            $mod_widget->params = LeoBtmegamenuHelper::base64Encode($params_widget);
            
            if ($check_widget_exists['id'] != '' && $override) {
                if (!$mod_widget->save()) {
                    return false;
                }
            }
            
            if (($override && $check_widget_exists['id'] == '') || (!$override && $check_widget_exists['id'] == '')) {
                $mod_widget->key_widget = $widget['key_widget'];
                $mod_widget->id_shop = $shop_id;
                $mod_widget->id = 0;
            
                if (!$mod_widget->add()) {
                    return false;
                }
            }
        }
        return true;
    }

    //remove all menu of group when delete group or when import override
    public function removeAllMenuOfGroup($id_group)
    {
        $res = true;
        
        $list_menu = BtmegamenuGroup::getMenuParentByGroup($id_group);
        if (count($list_menu) > 0) {
            foreach ($list_menu as $key => $list_menu_item) {
                $mod_menu = new Btmegamenu($list_menu_item['id_btmegamenu']);
                $res = $mod_menu->delete();
            }
            // validate module
            unset($key);
            $this->clearCache();
        }
        
        return $res;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function getMediaDir()
    {
        $media_dir = '';
        if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
            $media_dir = 'modules/'.$this->name.'/views/';
        } else {
            $media_dir = 'modules/'.$this->name.'/';
        }
        return $media_dir;
    }

    public function getThemeMediaDir($media = null)
    {
        $media_dir = '';

        if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
            if ($media == 'img') {
                $media_dir = 'assets/img/modules/'.$this->name.'/';
            }
            
            if ($media == 'css') {
                $media_dir = 'assets/css/modules/'.$this->name.'/views/';
            }
        } else {
            $media_dir = 'modules/'.$this->name.'/';
        }
        return $media_dir;
    }
    
    private function saveMenu()
    {
        if (!$id_group = Tools::getValue('id_group')) {
            $this->html .= $this->displayError($this->l('An error occurred while attempting to save.'));
        } else {
            if ($id_btmegamenu = Tools::getValue('id_btmegamenu')) {
                # validate module
                $megamenu = new Btmegamenu((int)$id_btmegamenu);
            } else {
                # validate module
                $megamenu = new Btmegamenu();
            }

            $keys = LeoBtmegamenuHelper::getConfigKey(false);
            $post = LeoBtmegamenuHelper::getPost($keys, false);
            $keys = LeoBtmegamenuHelper::getConfigKey(true);
            $post += LeoBtmegamenuHelper::getPost($keys, true);

            $megamenu->copyFromPost($post);
            $megamenu->id_shop = $this->context->shop->id;

            $megamenu->id_group = $id_group;
            if ($megamenu->type && $megamenu->type != 'html' && Tools::getValue($megamenu->type.'_type')) {
                # validate module
                $megamenu->item = Tools::getValue($megamenu->type.'_type');
                $megamenu->item_parameter = Tools::getValue($megamenu->type.'_type_parameter');
            }
            $url_default = '';
            foreach ($megamenu->url as $menu_url) {
                if ($menu_url) {
                    $url_default = $menu_url;
                    break;
                }
            }
            if ($url_default) {
                foreach ($megamenu->url as &$menu_url) {
                    if (!$menu_url) {
                        $menu_url = $url_default;
                    }
                }
            }
            if ($megamenu->validateFields(false) && $megamenu->validateFieldsLang(false)) {
                if (!Tools::getValue('id_btmegamenu')) {
                    # Auto set position when create menu
                    $megamenu->position = Btmegamenu::getLastPosition((int)$megamenu->id_parent);
                }

                $megamenu->save();
                $megamenu->cleanPositions($megamenu->id_parent);
                if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
                    $this->checkFolderIcon();
                    if (ImageManager::validateUpload($_FILES['image'])) {
                        return false;
                    } elseif (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image']['tmp_name'], $tmp_name)) {
                        return false;
                    } elseif (!ImageManager::resize($tmp_name, $this->img_path.$_FILES['image']['name'])) {
                        return false;
                    }
                    unlink($tmp_name);
                    $megamenu->image = $_FILES['image']['name'];
                    $megamenu->save();
                } else if (Tools::getIsset('delete_icon')) {
                    if ($megamenu->image) {
                        unlink($this->img_path.$megamenu->image);
                        $megamenu->image = '';
                        $megamenu->save();
                    }
                }
                Tools::redirectAdmin(AdminController::$currentIndex.'&configure=leobootstrapmenu&editgroup=1&id_group='.$id_group.'&id_btmegamenu='.$megamenu->id.'&successful=1&save'.$this->name.'&token='.Tools::getValue('token'));
            } else {
                # validate module
                $errors = array();
                $errors[] = $this->l('An error occurred while attempting to save.');
                $errors[] = $this->l('Do not let the requirement fields (*) are empty.');
                if (!Validate::isUnsignedInt(Tools::getValue('colums'))) {
                    $errors[] = $this->l('"Colums" is invalid. Must an integer validity (unsigned).');
                }
            }
            if (isset($errors) && count($errors)) {
                $this->html .= $this->displayError(implode('<br />', $errors));
            } else {
                $this->clearCache();
                $this->html .= $this->displayConfirmation($this->l('Settings updated.'));
            }
        }
    }
    
    private function deleteMenu()
    {
        if (!$id_group = Tools::getValue('id_group')) {
            $this->html .= $this->displayError($this->l('An error occurred while attempting to delete.'));
        } else {
            $obj = new Btmegamenu((int)Tools::getValue('id_btmegamenu'));
            $obj->delete();
            $this->clearCache();
            Tools::redirectAdmin(AdminController::$currentIndex.'&editgroup=1&id_group='.$id_group.'&successful=1&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        }
    }
    
    private function deleteManyMenu()
    {
        if (!$id_group = Tools::getValue('id_group')) {
            $this->html .= $this->displayError($this->l('An error occurred while attempting to delete.'));
        } else {
            $list = array_filter(explode(',', trim(Tools::getValue('list'), ',')));
            if (is_array($list) && $list) {
                foreach ($list as $key => $id) {
                    # validate module
                    unset($key);
                    $obj = new Btmegamenu((int)$id);
                    if ($obj->id) {
                        $obj->delete();
                    }
                }
                $this->clearCache();
            }
            Tools::redirectAdmin(AdminController::$currentIndex.'&editgroup=1&id_group='.$id_group.'&successful=1&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        }
    }
    
    private function duplicateMenu()
    {
        if (!Tools::getValue('id_btmegamenu') || !$id_group = Tools::getValue('id_group')) {
            $this->html .= $this->displayError($this->l('An error occurred while attempting to duplicate.'));
        } else {
            $mod_menu = new Btmegamenu((int)Tools::getValue('id_btmegamenu'));
            $mod_new_menu = new Btmegamenu();

            $defined = $mod_new_menu->getDefinition('Btmegamenu');
            foreach ($defined['fields'] as $ke => $val) {
                # module validation
                unset($val);

                if ($ke == 'id') {
                    continue;
                }

                if ($ke == 'title') {
                    $tmp = array();
                    foreach ($mod_menu->title as $kt => $vt) {
                        $tmp[$kt] = $this->l('Duplicate of').' '.$vt;
                    }

                    $mod_new_menu->{$ke} = $tmp;
                } elseif ($ke == 'position') {
                    $mod_new_menu->{$ke} = Btmegamenu::getLastPosition((int)$mod_menu->id_parent);
                } else {
                    $mod_new_menu->{$ke} = $mod_menu->{$ke};
                }
            }
            $errors = array();
            if (!$mod_new_menu->add()) {
                $errors[] =  $this->l('The menu could not be duplicate.');
            }

            if (isset($errors) && count($errors)) {
                $this->html .= $this->displayError(implode('<br />', $errors));
            } else {
                $this->clearCache();
                Tools::redirectAdmin(AdminController::$currentIndex.'&editgroup=1&id_group='.$id_group.'&successful=1&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
            }
        }
    }
    
    private function addMenuProductLayout()
    {
        $this->renderGroupConfig = true;
        $langs = Language::getLanguages(false);
        //add new menu item for product layout
        if (!$id_group = Tools::getValue('id_group')) {
            $this->html .= $this->displayError($this->l('An error occurred while attempting to save.'));
        } else {
            $megamenu = new Btmegamenu();

            $id_shop = $this->context->shop->id;

            foreach ($langs as $lang) {
                $megamenu->title[$lang['id_lang']] = $this->l('Product Layouts');
                $megamenu->url[$lang['id_lang']] = '#';
            }

            $megamenu->id_parent = 0;
            $megamenu->sub_with = 'widget';
            $megamenu->is_group = 0;
            $megamenu->colums = 1;
            $megamenu->type = 'url';

            $megamenu->menu_class = 'icon-new';
            $megamenu->show_title = 1;
            $megamenu->level_depth = 1;
            $megamenu->active = 1;

            $megamenu->show_sub = 0;
            $megamenu->target = '_self';
            $megamenu->privacy = 0;
            $megamenu->level = 0;
            $megamenu->left = 0;
            $megamenu->right = 0;
            $megamenu->is_cattree = 1;

            $megamenu->id_shop = $id_shop;

            $megamenu->id_group = $id_group;
            $megamenu->position = Btmegamenu::getLastPosition((int)$megamenu->id_parent);
            $megamenu->save();

            if ($megamenu->save()) {
                $megamenu->cleanPositions($megamenu->id_parent);

                //create widget with each product layout for new menu item
                $sql = 'SELECT a.*
                    FROM `'._DB_PREFIX_.'appagebuilder_details` as a
                    INNER JOIN `'._DB_PREFIX_.'appagebuilder_details_shop` ps ON (ps.`id_appagebuilder_details` = a.`id_appagebuilder_details`) WHERE ps.id_shop='.(int)$id_shop;
                $list_productdetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                if (count($list_productdetail) > 1) {
                    $widget_lang_field = array('widget_title', 'htmlcontent');
                    $list_widget = '';
                    foreach ($list_productdetail as $key => $list_productdetail_item) {
                        $layout_order = $key+1;
                        $url_img_preview = '#';
                        if ($list_productdetail_item['url_img_preview'] != '') {
                            $url_img_preview = $list_productdetail_item['url_img_preview'];
                        }

                        $model = new LeoWidget();
                        $model->id_shop = $id_shop;
                        $model->key_widget = time()+$layout_order;
                        $model->name = 'Product Layout '.$layout_order;
                        $model->type = 'html';
                        $tmp = array();
                        foreach ($widget_lang_field as $fVal) {
                            foreach ($langs as $language) {
                                if ($fVal == 'htmlcontent') {
                                    $tmp[$fVal.'_'.$language['id_lang']] = '<a href="?layout='.$list_productdetail_item['plist_key'].'" title="'.$list_productdetail_item['name'].'"><img class="img-fluid" src="'.$url_img_preview.'" alt="'.$list_productdetail_item['name'].'"></a>';
                                } else {
                                    $tmp[$fVal.'_'.$language['id_lang']] = '';
                                }
                            }
                        }
                        $tmp['widget_name'] = 'Product Layout '.$layout_order;
                        $tmp['widget_type'] = 'html';
                        $model->params = call_user_func('base64'.'_encode', Tools::jsonEncode($tmp));
                        $model->add();
                        if ($layout_order == count($list_productdetail)) {
                            $list_widget .= 'wid-'.$model->key_widget;
                        } else {
                            $list_widget .= 'wid-'.$model->key_widget.'|';
                        }
                    }

                    $megamenu->params_widget = call_user_func('base64'.'_encode', '{"subwith":"widget","submenu":1,"group":0,"align":"aligned-fullwidth","cols":1,"rows":[{"cols":[{"widgets":"'.$list_widget.'","colwidth":12,"colclass":"demo-product-detail"}]}]}');
                    $megamenu->save();
                }

                Tools::redirectAdmin(AdminController::$currentIndex.'&configure=leobootstrapmenu&editgroup=1&id_group='.$id_group.'&id_btmegamenu='.$megamenu->id.'&successful=1&save'.$this->name.'&token='.Tools::getValue('token'));
            } else {
                $this->html .= $this->displayError($this->l('An error occurred while attempting to save.'));
            }
        }
    }
    
    private function saveGroup()
    {
        $this->renderGroupConfig = true;
        
        $langs = Language::getLanguages(false);
        $errors = array();
        
        # ACTION - add,edit for GROUP
        /* Sets ID if needed */
        if (Tools::getValue('id_group')) {
            $mod_group = new BtmegamenuGroup((int)Tools::getValue('id_group'));
            if (!Validate::isLoadedObject($mod_group)) {
                $this->html .= $this->displayError($this->l('Invalid id_group'));
                return;
            }
        } else {
            $mod_group = new BtmegamenuGroup();
        }

        /* Sets position */
        foreach ($langs as $lang) {
            $mod_group->title[$lang['id_lang']] = Tools::getValue('title_group_'.$lang['id_lang']);
            $mod_group->title_fo[$lang['id_lang']] = Tools::getValue('title_fo_group_'.$lang['id_lang']);
        }
        /* Sets active */
        $mod_group->active = (int)Tools::getValue('active_group');
        $context = Context::getContext();
        $mod_group->id_shop = $context->shop->id;
        $mod_group->hook = Tools::getValue('hook_group');
        if (!Tools::getValue('id_group')) {
            $mod_group->position = $mod_group->getLastPosition($context->shop->id);
            $mod_group->randkey = LeoBtmegamenuHelper::genKey();
        }
        $params = Tools::getValue('group');
        $mod_group->params = LeoBtmegamenuHelper::base64Encode(Tools::jsonEncode($params));


        # Adds
        if ($mod_group->validateFields(false) && $mod_group->validateFieldsLang(false)) {
            if (!Tools::getValue('id_group')) {
                if (!$mod_group->add()) {
                    $errors[] = $this->displayError($this->l('The group could not be added.'));
                } else {
                    $this->clearCache();
                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&success=add&editgroup=1&id_group='.$mod_group->id);
                }
            } else {
                # Update
                if (!$mod_group->update()) {
                    $errors[] = $this->displayError($this->l('The group could not be updated.'));
                } else {
                    $this->clearCache();
                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&success=update&editgroup=1&id_group='.$mod_group->id);
                }
            }
        } else {
            $errors[] = $this->l('An error occurred while attempting to save.');
            $errors[] = $this->l('Do not let the requirement fields (*) are empty');
        }

        # Save in config to edit next time
        $this->clearCache();
        /* Display errors if needed */
        if (count($errors)) {
            $this->html .= $this->displayError(implode('<br />', $errors));
        } elseif (Tools::isSubmit('submitGroup')) {
            $this->html .= $this->displayConfirmation($this->l('Group added'));
        } else {
            $this->html .= $this->displayConfirmation($this->l('Group updated'));
        }
        
        Configuration::updateValue('BTMEGAMENU_GROUP_DE', (int)Tools::getValue('id_group'));
    }
    
    private function deleteGroup()
    {
        $this->renderGroupConfig = true;
        $mod_group = new BtmegamenuGroup((int)Tools::getValue('id_group'));
        # Delete slider of group
        $res = $mod_group->delete();
        $this->clearCache();
        if (!$res) {
            $this->html .= $this->displayError($this->l('Could not delete'));
        } else {
            $res = $this->removeAllMenuOfGroup(Tools::getValue('id_group'));

            if (!$res) {
                $this->html .= $this->displayError($this->l('Could not delete'));
            } else {
                $this->html .= $this->displayConfirmation($this->l('Group deleted'));
                Configuration::updateValue('BTMEGAMENU_GROUP_DE', '');
                Tools::redirectAdmin('index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&configure=leobootstrapmenu&success=delete');
            }
        }
    }
    
    private function duplicateGroup()
    {
        $this->renderGroupConfig = true;
        $langs = Language::getLanguages(false);
        if (!$id_group = Tools::getValue('id_group')) {
            $this->html .= $this->displayError($this->l('An error occurred while attempting to duplicate.'));
        } else {
            $mod_group = new BtmegamenuGroup((int)Tools::getValue('id_group'));
            $mod_new_group = new BtmegamenuGroup();
            $defined = $mod_new_group->getDefinition('BtmegamenuGroup');
            foreach ($defined['fields'] as $ke => $val) {
                # module validation
                unset($val);

                if ($ke == 'id') {
                    continue;
                }

                if ($ke == 'title') {
                    foreach ($langs as $lang) {
                        $mod_new_group->{$ke}[$lang['id_lang']] = $this->l('Duplicate of').' '.$mod_group->{$ke}[$lang['id_lang']];
                    }
                } elseif ($ke == 'position') {
                    $mod_new_group->{$ke} = BtmegamenuGroup::getLastPosition(Context::getContext()->shop->id);
                } elseif ($ke == 'randkey') {
                    $mod_new_group->{$ke} = LeoBtmegamenuHelper::genKey();
                } else {
                    $mod_new_group->{$ke} = $mod_group->{$ke};
                }
            }
            $list_menu = BtmegamenuGroup::getMenuByGroup($id_group);
            if (!$mod_new_group->add()) {
                $this->html .= $this->displayError($this->l('The group could not be duplicate.'));
            } else {
                //copy menu of old group to new group
                $list_menu = BtmegamenuGroup::getMenuByGroup($id_group);

                $res = true;
                if (count($list_menu) > 0) {
                    $list_new_id = array();
                    foreach ($list_menu as $list_menu_item) {
                        $mod_menu = new Btmegamenu($list_menu_item['id_btmegamenu']);
                        $mod_new_menu = new Btmegamenu();

                        $defined = $mod_new_menu->getDefinition('Btmegamenu');
                        foreach ($defined['fields'] as $ke => $val) {
                            # module validation
                            unset($val);

                            if ($ke == 'id') {
                                continue;
                            }

                            if ($ke == 'id_group') {
                                $mod_new_menu->{$ke} = $mod_new_group->id;
                            } elseif ($ke == 'id_parent') {
                                if ($mod_menu->{$ke} != 0) {
                                    $mod_new_menu->{$ke} = $list_new_id[$mod_menu->{$ke}];
                                } else {
                                    $mod_new_menu->{$ke} = $mod_menu->{$ke};
                                }
                            } else {
                                $mod_new_menu->{$ke} = $mod_menu->{$ke};
                            }
                        }
                        if (!$mod_new_menu->add()) {
                            $res = false;
                        } else {
                            $list_new_id[$list_menu_item['id_btmegamenu']] = $mod_new_menu->id;
                        }
                    }
                }
                if ($res) {
                    //update widget with new menu id
                    $this->html .= $this->displayConfirmation($this->l('Group duplicated'));
                    Configuration::updateValue('BTMEGAMENU_GROUP_DE', '');
                    // Tools::redirectAdmin(AdminController::$currentIndex.'&editgroup=1&id_group='.$id_group.'&successful=1&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
                    Tools::redirectAdmin('index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&configure=leobootstrapmenu&success=duplicategroup');
                } else {
                    $this->html .= $this->displayError($this->l('The group could not be duplicate.'));
                }
            }
        }
    }
    
    private function exportGroup()
    {
        $this->renderGroupConfig = true;
        //export group process
        if (Tools::getValue('exportgroup')) {
            $languages = Language::getLanguages();
            $group = BtmegamenuGroup::getGroupByID(Tools::getValue('id_group'));
            $obj_group = new BtmegamenuGroup(Tools::getValue('id_group'));
            foreach ($languages as $lang) {
                # module validation
                $group['title'][$lang['iso_code']] = $obj_group->title[$lang['id_lang']];
                $group['title_fo'][$lang['iso_code']] = $obj_group->title_fo[$lang['id_lang']];
            }
            //add list menu of group
//            $menus = $this->getMenusByGroup(Tools::getValue('id_group'));
            $id_group = (int)Tools::getValue('id_group');
            $menus = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT btm.*, btml.*
                    FROM '._DB_PREFIX_.'btmegamenu btm
                    LEFT JOIN '._DB_PREFIX_.'btmegamenu_lang btml ON (btm.id_btmegamenu = btml.id_btmegamenu)
                    WHERE btm.id_group = '.(int)$id_group.'
                    ORDER BY btm.id_parent ASC');
            
            $language_field = array('title', 'text', 'url', 'description', 'content_text', 'submenu_content_text');
           
            $lang_list = array();
            foreach ($languages as $lang) {
                # module validation
                $lang_list[$lang['id_lang']] = $lang['iso_code'];
            }
            
            $list_widgets = '';
            
            foreach ($menus as $menus_item) {
                if (Tools::getValue('widgets')) {
                    if ($menus_item['params_widget'] != '') {
                        $list_widgets .= LeoBtmegamenuHelper::base64Decode($menus_item['params_widget']);
                    }
                } else {
                    $menus_item['params_widget'] = '';
                }
                foreach ($menus_item as $key => $value) {
                    if ($key == 'id_lang') {
                        $curent_lang = $lang_list[$value];
                        continue;
                    }
                    if (in_array($key, $language_field)) {
                        $group['list_menu'][$menus_item['id_btmegamenu']][$key][$curent_lang] = $value;
                    } else {
                        # module validation
                        $group['list_menu'][$menus_item['id_btmegamenu']][$key] = $value;
                    }
                }
            }
            
            if (Tools::getValue('widgets')) {
                $widget_include = 'with_widgets';
            } else {
                // $group['params_widget'] = '';
                $widget_include = 'without_widgets';
            }
            //add list menu of group
            $group['list_widget'] = array();
            if ($list_widgets != '' && Tools::getValue('widgets')) {
                $model = new LeoWidget();
                $widget_shop = $model->getWidgets();
                if (count($widget_shop) > 0) {
                    foreach ($widget_shop as $widget_shop_item) {
                        if (strpos($list_widgets, 'wid-'.$widget_shop_item['key_widget']) !== false) {
                            $params_widget = LeoBtmegamenuHelper::base64Decode($widget_shop_item['params']);
                            foreach ($languages as $lang) {
                                # module validation
                                if (strpos($params_widget, '_'.$lang['id_lang'].'"') !== false) {
                                    $params_widget = str_replace('_'.$lang['id_lang'].'"', '_'.$lang['iso_code'].'"', $params_widget);
                                }
                            }
                            $widget_shop_item['params'] = LeoBtmegamenuHelper::base64Encode($params_widget);
                            $group['list_widget'][] = $widget_shop_item;
                        }
                    }
                }
            }
            header('Content-Type: plain/text');
            header('Content-Disposition: Attachment; filename=export_megamenu_group_'.Tools::getValue('id_group').'_'.$widget_include.'_'.time().'.txt');
            header('Pragma: no-cache');
            die(LeoBtmegamenuHelper::base64Encode(Tools::jsonEncode($group)));
        }
    }
    
    private function exportWidgets()
    {
        //export widgets process
        if (Tools::getValue('exportwidgets')) {
            $languages = Language::getLanguages();
            $model = new LeoWidget();
            $widget_shop = $model->getWidgets();
            
            $widgets = array();
            if (count($widget_shop) > 0) {
                foreach ($widget_shop as $widget_shop_item) {
                    $params_widget = LeoBtmegamenuHelper::base64Decode($widget_shop_item['params']);
                    foreach ($languages as $lang) {
                        # module validation
                        if (strpos($params_widget, '_'.$lang['id_lang'].'"') !== false) {
                            $params_widget = str_replace('_'.$lang['id_lang'].'"', '_'.$lang['iso_code'].'"', $params_widget);
                        }
                    }
                    
                    $widget_shop_item['params'] = LeoBtmegamenuHelper::base64Encode($params_widget);
                    $widgets[] = $widget_shop_item;
                }
            }
            
            header('Content-Type: plain/text');
            header('Content-Disposition: Attachment; filename=export_widgets_'.time().'.txt');
            header('Pragma: no-cache');
            die(LeoBtmegamenuHelper::base64Encode(Tools::jsonEncode($widgets)));
        }
    }
    
    private function changeStatusGroup()
    {
        $this->renderGroupConfig = true;
        if (Tools::isSubmit('changeGStatus') && Tools::isSubmit('id_group')) {
            # ACTION - Change status for GROUP : enable or disable a group
            $mod_group = new BtmegamenuGroup((int)Tools::getValue('id_group'));
            $change_status = Tools::getValue('changeGStatus');
            $mod_group->update_flag = false;

            if ($change_status == BtmegamenuGroup::GROUP_STATUS_DISABLE && $mod_group->active != $change_status) {
                $mod_group->active = BtmegamenuGroup::GROUP_STATUS_DISABLE;
                $mod_group->update_flag = true;
            } elseif ($change_status == BtmegamenuGroup::GROUP_STATUS_ENABLE && $mod_group->active != $change_status) {
                $mod_group->active = BtmegamenuGroup::GROUP_STATUS_ENABLE;
                $mod_group->update_flag = true;
            }
            if (true == $mod_group->update_flag) {
                $res = $mod_group->update();
                $this->clearCache();
                $this->html .= ($res ? $this->displayConfirmation($this->l('Change status of group was successful')) : $this->displayError($this->l('The configuration could not be updated.')));
            }
        }

        //save group id in config to edit in next time when open module
        Configuration::updateValue('BTMEGAMENU_GROUP_DE', (int)Tools::getValue('id_group'));
    }

    private function changePositionMenu()
    {
        $list = Tools::getValue('list');
        $root = 0;
        $child = array();
        foreach ($list as $id => $parent_id) {
            if ($parent_id <= 0) {
                # validate module
                $parent_id = $root;
            }
            $child[$parent_id][] = $id;
        }
        $res = true;
        foreach ($child as $id_parent => $menus) {
            $i = 0;
            foreach ($menus as $id_btmegamenu) {
                $sql = 'UPDATE `'._DB_PREFIX_.'btmegamenu` SET `position` = '.(int)$i.', id_parent = '.(int)$id_parent.'
                        WHERE `id_btmegamenu` = '.(int)$id_btmegamenu;
                $res &= Db::getInstance()->execute($sql);
                $i++;
            }
        }
        $this->clearCache();
        $array = array('hasError' => false, 'errors' => $this->l('Update Positions Done'));
        die(Tools::jsonEncode($array));
    }
    
    private function changePositionGroup()
    {
        $list_group = Tools::getValue('list_group');
        $i = 0;
        foreach ($list_group as $id_btmegamenu_group => $val) {
            # validate module
            unset($val);
            $sql = 'UPDATE `'._DB_PREFIX_.'btmegamenu_group` SET `position` = '.(int)$i.'
                    WHERE `id_btmegamenu_group` = '.(int)$id_btmegamenu_group;
            Db::getInstance()->execute($sql);
            $i++;
        }
        $this->clearCache();
        $array = array('hasError' => false, 'errors' => $this->l('Update Group Positions Done'));
        die(Tools::jsonEncode($array));
    }
    
    public function displaySuccessMessage()
    {
        if (count($this->errors) > 0) {
            return;
        }
        
        if (Tools::getValue('success')) {
            switch (Tools::getValue('success')) {
                case 'add':
                    $this->html = $this->displayConfirmation($this->l('Group added'));
                    break;
                case 'update':
                    $this->html = $this->displayConfirmation($this->l('Group updated'));
                    break;
                case 'delete':
                    $this->html = $this->displayConfirmation($this->l('Group deleted'));
                    break;
                case 'clearcache':
                    $this->html = $this->displayConfirmation($this->l('Successful clear cache'));
                    break;
                case 'duplicategroup':
                    $this->html = $this->displayConfirmation($this->l('Duplicate group is successful'));
                    break;
                case 'importgroup':
                    $this->html = $this->displayConfirmation($this->l('Import group is successful'));
                    break;
                case 'importwidgets':
                    $this->html = $this->displayConfirmation($this->l('Import widgets is successful'));
                    break;
                case 'correct':
                    $this->html = $this->displayConfirmation($this->l('Correct Module is successful'));
                    break;
            }
        }
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     */
    public function getPermission($variable, $employee = null)
    {
        if ($variable == 'configure') {
            // Allow see form if permission is : configure, view
            $configure = Module::getPermissionStatic($this->id, 'configure', $employee);
            $view = Module::getPermissionStatic($this->id, 'view', $employee);
            return ($configure || $view);
        }
        
        return Module::getPermissionStatic($this->id, $variable, $employee);
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     */
    public function access($action)
    {
        $employee = null;
        return Module::getPermissionStatic($this->id, $action, $employee);
    }
    
    /**
     * FRONTEND
     ************************************************************************************************************************************************
     */
    public function hookDisplayHeader()
    {
        $media_dir = $this->getMediaDir();
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/megamenu.css', 'all');
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/leomenusidebar.css', 'all');
        $this->context->controller->registerJavascript('modules-leoboostrapmenu-leoboostrapmenu', $media_dir.'js/leobootstrapmenu.js', array('position' => 'bottom', 'priority' => 150));
        Context::getContext()->controller->addJqueryPlugin('fancybox');
        $link = new Link();
        $current_link = $link->getPageLink('', false, $this->context->language->id);
        $this->smarty->assign('current_link', $current_link);
        return $this->display(_PS_MODULE_DIR_.'leobootstrapmenu/leobootstrapmenu.php', 'views/templates/hook/javascript_parameter.tpl');
    }

    protected function getCacheId($name = null, $hook = '')
    {
        $cache_array = array(
            $name !== null ? $name : $this->name,
            $hook,
            date('Ymd'),
            (int)Tools::usingSecureMode(),
            (int)$this->context->shop->id,
            (int)Group::getCurrent()->id,
            (int)$this->context->language->id,
            (int)$this->context->currency->id,
            (int)$this->context->country->id,
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
        );
        return implode('|', $cache_array);
    }

    public function clearCache()
    {
        $tpl = 'views/templates/hook/megamenu.tpl';
        foreach ($this->hook_support as $val) {
            $group = BtmegamenuGroup::getActiveGroupByHook($val);
            foreach ($group as $group_val) {
                $this->_clearCache($tpl, $val.'_'.$this->name.'_'.$group_val['id_btmegamenu_group']);
            }
        }
        
        // clear cache for appagebuilder
        $list_group = BtmegamenuGroup::getGroups();
        foreach ($list_group as $list_group_val) {
            if (isset($list_group_val['form_id']) && $list_group_val['form_id'] != '') {
                $this->_clearCache($tpl, $list_group_val['id_btmegamenu_group'].'_'.$list_group_val['form_id'].'_'.$this->name);
            }
        }
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        //require function of prestashop
    }
    
    public function renderWidget($hookName = null, array $configuration = [])
    {
        return $this->_processHook($hookName);
    }
    
    public function _processHook($hookName)
    {
        $tpl = 'module:leobootstrapmenu/views/templates/hook/megamenu.tpl';
        $group = BtmegamenuGroup::cacheGroupsByFields(array('hook' => $hookName, 'active' => 1), true);
        
        if (!$group) {
            return false;
        }
        $hook_html = '';
        foreach ($group as $group_val) {
            if (!$this->_prepareHook($hookName, $group_val)) {
                return false;
            }
            $hook_html .= $this->fetch($tpl, $this->getCacheId($hookName.'_'.$this->name.'_'.$group_val['id_btmegamenu_group']));
        }
        return $hook_html;
    }
    
    private function _prepareHook($hook_name, $group)
    {
        $tpl = 'module:leobootstrapmenu/views/templates/hook/megamenu.tpl';
        if ($this->isCached($tpl, $this->getCacheId($hook_name.'_'.$this->name.'_'.$group['id_btmegamenu_group']))) {
            return true;
        }
        if (!is_dir(_PS_ROOT_DIR_.'/cache/'.$this->name)) {
            mkdir(_PS_ROOT_DIR_.'/cache/'.$this->name, 0755, true);
        }
        $params_group = Tools::jsonDecode(LeoBtmegamenuHelper::base64Decode($group['params']), true);
        $params = array();
        
        $show_cavas = $params_group['show_cavas'];
        $type_sub = $params_group['type_sub'];
        $group_type = $params_group['group_type'];
        $group_class = $params_group['group_class'];
        $list_root_menu = Btmegamenu::getMenusRoot($group['id_btmegamenu_group']);
        
        $get_params_widget = array();
        if (count($list_root_menu) > 0) {
            foreach ($list_root_menu as $list_root_menu_item) {
                $menu_obj = new Btmegamenu($list_root_menu_item['id_btmegamenu']);
                $menu_params_widget = $menu_obj->getParamsWidget();
                if ($menu_params_widget != '') {
                    $get_params_widget[$list_root_menu_item['id_btmegamenu']] = Tools::jsonDecode(LeoBtmegamenuHelper::base64Decode($menu_params_widget));
                }
            }
        }
        $params['params'] = $get_params_widget;
        $obj = new Btmegamenu();
        $obj->setModule($this);
        $boostrapmenu = trim($obj->getFrontTree(0, false, $params['params'], $group['id_btmegamenu_group'], $hook_name));
        $this->smarty->assign('boostrapmenu', $boostrapmenu);
        $this->smarty->assign('show_cavas', $show_cavas);
        $this->smarty->assign('type_sub', $type_sub);
        $this->smarty->assign('group_type', $group_type);
        $this->smarty->assign('group_class', $group_class);
        $this->smarty->assign('group_title', $group['title_fo']);
        $this->smarty->assign('megamenu_id', $group['id_btmegamenu_group']);
        
        return true;
    }
    
    //function get list group for ApPageBuilder
    public function getGroups()
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $id_lang = Context::getContext()->language->id;
        $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'btmegamenu_group gr
                    LEFT JOIN '._DB_PREFIX_.'btmegamenu_group_lang grl ON gr.id_btmegamenu_group = grl.id_btmegamenu_group AND grl.id_lang = '.(int)$id_lang.'
                    WHERE gr.id_shop = '.(int)$id_shop.' ORDER BY gr.id_btmegamenu_group';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    //function callback for ApPageBuilder
    public function processHookCallBack($group_id, $form_id)
    {
        $tpl = 'module:leobootstrapmenu/views/templates/hook/megamenu.tpl';
            
        $cache_id = $this->getCacheId($group_id.'_'.$form_id.'_'.$this->name);
        if (!$this->isCached($tpl, $cache_id)) {
            $this->prepareHookForApPageBuilder($group_id, $form_id);
        }

        return $this->fetch($tpl, $cache_id);
    }
    
    //function callback for ApPageBuilder
    private function prepareHookForApPageBuilder($group_id, $form_id)
    {
        $error = '';
        $group = BtmegamenuGroup::cacheGroupsByFields(array('id_btmegamenu_group' => (int)$group_id));
        
        if (!$group) {
            $error = 'The Group of LeoBoostrapMenu is not exist, please access ApPageBuilder module then delete it.';
        } elseif ($group['active'] != 1) {
            $error = 'The Group of LeoBoostrapMenu is not active, please access ApPageBuilder module then delete it';
        }

        if ($error) {
            $this->smarty->assign('error', $error);
            return;
        }
        
        $params_group = Tools::jsonDecode(LeoBtmegamenuHelper::base64Decode($group['params']), true);
        $params = array();

        $show_cavas = $params_group['show_cavas'];
        $type_sub = $params_group['type_sub'];
        $group_type = $params_group['group_type'];
        $group_class = $params_group['group_class'];
        $list_root_menu = Btmegamenu::getMenusRoot($group['id_btmegamenu_group']);

        $get_params_widget = array();
        if (count($list_root_menu) > 0) {
            foreach ($list_root_menu as $list_root_menu_item) {
                $menus = Btmegamenu::cacheMenusByFields(array('id_btmegamenu' => (int)$list_root_menu_item['id_btmegamenu']));
                $menu_params_widget = (isset($menus['params_widget']) && $menus['params_widget']) ? $menus['params_widget'] : '';
                if ($menu_params_widget != '') {
                    $get_params_widget[$list_root_menu_item['id_btmegamenu']] = Tools::jsonDecode(LeoBtmegamenuHelper::base64Decode($menu_params_widget));
                }
            }
        }

        $params['params'] = $get_params_widget;
        $obj = new Btmegamenu();
        $obj->setModule($this);
        $boostrapmenu = $obj->getFrontTree(0, false, $params['params'], $group['id_btmegamenu_group']);
        $this->smarty->assign('boostrapmenu', $boostrapmenu);
        $this->smarty->assign('show_cavas', $show_cavas);
        $this->smarty->assign('type_sub', $type_sub);
        $this->smarty->assign('group_type', $group_type);
        $this->smarty->assign('group_class', $group_class);
        $this->smarty->assign('group_title', $group['title_fo']);
        $this->smarty->assign('megamenu_id', $form_id);
    }
    
    /**
     * @Action Create new shop, choose theme then auto restore datasample.
     */
    public function hookActionAdminShopControllerSaveAfter($param)
    {
        if (Tools::getIsset('controller') !== false && Tools::getValue('controller') == 'AdminShop'
                && Tools::getIsset('submitAddshop') !== false && Tools::getValue('submitAddshop')
                && Tools::getIsset('theme_name') !== false && Tools::getValue('theme_name')) {
            $shop = $param['return'];
            if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
                require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
                $sample = new Datasample();
                LeoBtmegamenuHelper::$id_shop = $shop->id;
                $sample->_id_shop = $shop->id;
                $sample->processImport('leobootstrapmenu');
            }
        }
    }
}
