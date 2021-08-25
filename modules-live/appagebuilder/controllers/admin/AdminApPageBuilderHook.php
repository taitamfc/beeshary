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

require_once(_PS_MODULE_DIR_.'appagebuilder/libs/Helper.php');

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class AdminApPageBuilderHookController extends ModuleAdminControllerCore
{
    /**
     * @var Boolean $display_key
     *
     * @access protected
     */
    public $display_key = 0;
    /**
     * @var Array $hookspos
     *
     * @access protected
     */
    public $hookspos = array();
    /**
     * @var Array $ownPositions
     *
     * @access protected
     */
//    public $ownPositions = array();
    /**
     * @var String $theme_name
     *
     * @access protected
     */
    public $theme_name;
    /**
     * @var String $theme_name
     *
     * @access protected
     */
    public $themeKey;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'leohook';
        $this->className = 'AdminApPageBuilderHook';
        $this->lang = true;
        $this->context = Context::getContext();
        parent::__construct();
        $this->display_key = (int)Tools::getValue('show_modules');

//        $this->ownPositions = array(
//            'displayHeaderRight',
//            'displaySlideshow',
//            'topNavigation',
//            'displayPromoteTop',
//            'displayBottom',
//            'displayMassBottom'
//        );
        $this->hookspos = ApPageSetting::getHook();
        $this->theme_name = _THEME_NAME_;
    }
    
    /**
     * Build List linked Icons Toolbar
     */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['save'] = array(
                'href' => 'index.php?tab=AdminApPageBuilderHook&token='.Tools::getAdminTokenLite('AdminApPageBuilderHook').'&action=savepos',
                'id' => 'savepos',
                'desc' => $this->l('Save Positions')
            );
        }
        parent::initPageHeaderToolbar();
    }

    /**
     * get live Edit URL
     */
    public function getLiveEditUrl($live_edit_params)
    {
        $url = $this->context->shop->getBaseURL().Dispatcher::getInstance()->createUrl('index', (int)$this->context->language->id, $live_edit_params);
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $url = str_replace('index.php', '', $url);
        }
        return $url;
    }

    /**
     * add toolbar icons
     */
    public function initToolbar()
    {
        $this->context->smarty->assign('toolbar_scroll', 1);
        $this->context->smarty->assign('show_toolbar', 1);
        $this->context->smarty->assign('toolbar_btn', $this->toolbar_btn);
        $this->context->smarty->assign('title', $this->toolbar_title);
    }

    /**
     * render list of modules following positions in the layout editor.
     */
    public function renderList()
    {
        $filePath = _PS_ALL_THEMES_DIR_.$this->theme_name.'';
        $showed = true;
        $xml = simplexml_load_file($filePath.'/config.xml');

        if (isset($xml->theme_key)) {
            $this->themeKey = trim((string)$xml->theme_key);
        }
        $this->themeKey = '1111';
        if ($this->themeKey) {
            $this->initToolbarTitle();
            $this->initToolbar();
            $hookspos = $this->hookspos;

            foreach ($hookspos as $hook) {
                if (Hook::getIdByName($hook)) {
                    // validate module
                } else {
                    $new_hook = new Hook();
                    $new_hook->name = pSQL($hook);
                    $new_hook->title = pSQL($hook);
                    $new_hook->add();
//                    $id_hook = $new_hook->id;
                }
            }

//            $sql = 'UPDATE `'._DB_PREFIX_.'hook` SET position=1, live_edit=1
//                        WHERE name in ("'.implode('","', $hookspos).'")  ';
//            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);

            $modules = Module::getModulesInstalled(0);
            $assoc_modules_id = array();

            $assoc_modules_id = $module_instances = array();
            foreach ($modules as $module) {
                if ($tmp_instance = Module::getInstanceById((int)$module['id_module'])) {
                    // We want to be able to sort modules by display name
                    $module_instances[$tmp_instance->displayName] = $tmp_instance;
                    // But we also want to associate hooks to modules using the modules IDs
                    $assoc_modules_id[(int)$module['id_module']] = $tmp_instance->displayName;
                }
            }
            $hooks = Hook::getHooks(!(int)Tools::getValue('hook_position'));

            $hookModules = array();

            $hookedModules = array();
            foreach ($hooks as $key => $hook) {
                // validate module
                unset($key);
                
                $k = $hook['name'];
                $k = (Tools::strtolower(Tools::substr($k, 0, 1)).Tools::substr($k, 1));
                if (in_array($k, $hookspos)) {
                    // Get all modules for this hook or only the filtered module
                    $hookModules[$k]['modules'] = Hook::getModulesFromHook($hook['id_hook'], $this->display_key);
                    $hookModules[$k]['module_count'] = count($hookModules[$k]['modules']);

                    if (is_array($hookModules[$k]['modules']) && !empty($hookModules[$k]['modules'])) {
                        foreach ($hookModules[$k]['modules'] as $module_key => $module) {
                            if (isset($assoc_modules_id[$module['id_module']])) {
                                $hookedModules[] = $module['id_module'];
                                $hookModules[$k]['modules'][$module_key]['instance'] = $module_instances[$assoc_modules_id[$module['id_module']]];
                            }
                        }
                    }
                }
            }

            $instances = array();
            foreach ($modules as $module) {
                if ($tmp_instance = Module::getInstanceById($module['id_module'])) {
                    foreach ($hookspos as $hk) {
                        $retro_hook_name = Hook::getRetroHookName($hk);
                        $hook_callable = is_callable(array($tmp_instance, 'hook'.$hk));
                        $hook_retro_callable = is_callable(array($tmp_instance, 'hook'.$retro_hook_name));
                        if ($hook_retro_callable || $hook_callable) {
                            $instances[$tmp_instance->displayName] = $tmp_instance;
                            break;
                        }
                    }
                }
            }
            ksort($instances);

            $tpl = $this->createTemplate('panel.tpl');

//            $this->context->controller->addJqueryUI('ui.sortable');
//            $this->context->controller->addJqueryUI('ui.draggable');
            $this->context->controller->addCss(apPageHelper::getCssAdminDir().'admin/style_hook_cpanel.css');
            $this->context->controller->addJs(apPageHelper::getJsAdminDir().'/jquery-ui-1.10.3.custom.min.js', 'all');
            $tpl->assign(array(
                'showed' => $showed,
                'toolbar' => $this->context->smarty->fetch('toolbar.tpl'),
                'modules' => $instances,
                'hookspos' => $hookspos,
                'URI' => __PS_BASE_URI__.'modules/',
                'hookModules' => $hookModules,
                'noModuleConfig' => $this->l('No Configuration For This Module'),
                'currentURL' => 'index.php?tab=AdminApPageBuilderHook&token='.Tools::getAdminTokenLite('AdminApPageBuilderHook').'',
//                'moduleEditURL' => 'index.php?tab=AdminApPageBuilderHook&token='.Tools::getAdminTokenLite('AdminApPageBuilderHook').'',
                  'moduleEditURL' => 'index.php?controller=adminmodules&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module=Home',
            ));

            return $tpl->fetch();
        } else {
            $tpl = $this->createTemplate('error.tpl');
            $tpl->assign(array(
                'showed' => false,
                'themeURL' => 'index.php?controller=AdminThemes&token='.Tools::getAdminTokenLite('AdminThemes')
            ));
            return $tpl->fetch();
        }
    }

    /**
     * Process posting data
     */
    public function postProcess()
    {
        if (count($this->errors) > 0) {
            if ($this->ajax) {
                $array = array('hasError' => true, 'errors' => $this->errors[0]);
                die(Tools::jsonEncode($array));
            }
            return;
        }
        
        if (Tools::getValue('action') && Tools::getValue('action') == 'modulehook') {
            // AJAX EDIT HOOK - READ
            $id = (int)Tools::getValue('id');

            $tmp_instance = Module::getInstanceById($id);
            $hooks = array();

            foreach ($this->hookspos as $hk) {
                $retro_hook_name = Hook::getRetroHookName($hk);
                $hook_callable = is_callable(array($tmp_instance, 'hook'.$hk));
                $hook_retro_callable = is_callable(array($tmp_instance, 'hook'.$retro_hook_name));

                if ($hook_retro_callable || $hook_callable) {
                    $hooks[] = $hk;
                }
            }
            $hooks = implode('|', $hooks);
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'leohook` WHERE id_module='.(int)$id.' AND theme="'.pSQL($this->theme_name).'" AND id_shop='.(int)($this->context->shop->id);

            if ($row = Db::getInstance()->getRow($sql)) {
                die('{"hasError" : false, "hook" : "'.$row['name_hook'].'","hooks":"'.$hooks.'"}');
            } else {
                die('{"hasError" : true, "errors" : "Can not update module position","hooks":"'.$hooks.'"}');
            }
        }
        if (Tools::getValue('action') && Tools::getValue('action') == 'overridehook') {
            // AJAX EDIT HOOK - SAVE
            $id_module = (int)Tools::getValue('hdidmodule');
            $name_hook = Tools::getValue('name_hook');
            if (is_numeric($name_hook)) {
                $sql = 'DELETE  FROM`'._DB_PREFIX_.'leohook` WHERE id_module='.(int)$id_module.' AND theme="'.pSQL($this->theme_name).'" AND id_shop='.(int)($this->context->shop->id);

                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                die('{"hasError" : false, "errors" : "done!delete module position"}');
            } elseif ($name_hook) {
                $desHook = Tools::getValue('deshook');
                $desHookId = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT id_hook FROM `'._DB_PREFIX_."hook` WHERE name='".pSQL($desHook)."'");
                $sql = 'SELECT *
                    FROM `'._DB_PREFIX_.'leohook` WHERE id_hook= '.(int)$desHookId.' AND id_module='.(int)$id_module.' AND theme="'.pSQL($this->theme_name).'" AND id_shop='.(int)($this->context->shop->id);

                if ($row = Db::getInstance()->getRow($sql)) {
                    $sql = ' UPDATE `'._DB_PREFIX_.'leohook`  SET `id_hook`='.(int)$desHookId.', name_hook="'.pSQL($name_hook).'"
                         WHERE id_module='.(int)$id_module.' AND theme="'.pSQL($this->theme_name).'" AND id_shop='.(int)($this->context->shop->id);
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                } else {
                    $sql = ' INSERT INTO `'._DB_PREFIX_.'leohook` (id_hook, id_module, id_shop, theme, name_hook)
                        VALUES('.(int)$desHookId.','.(int)$id_module.','.(int)($this->context->shop->id).',"'.pSQL($this->theme_name).'","'.pSQL($name_hook).'")';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                }
                die('{"hasError" : false, "errors" : "done!update module position"}');
            }
            die('{"hasError" : true, "errors" : "Can not update module position"}');
        }
        if (Tools::getValue('action') && Tools::getValue('action') == 'savepos') {
            // SUBMIT - SAVE HOOK
            $positions = Tools::getValue('position');
            $way = (int)(Tools::getValue('way'));
            $unhook = Tools::getValue('unhook');
            $id_shop = Context::getContext()->shop->id;

            if (is_array($unhook)) {
                foreach ($unhook as $id_module => $str_hookId) {
                    $hookIds = explode(',', $str_hookId);
                    foreach ($hookIds as $hookId) {
                        $module = Module::getInstanceById($id_module);
                        if (Validate::isLoadedObject($module)) {
                            !$module->unregisterHook((int)$hookId, array($id_shop));
                        }
                    }
                }
            }

            if (is_array($positions) && !empty($positions)) {
                foreach ($positions as $pos) {
                    $tmp = explode('|', $pos);
                    if (count($tmp) == 2 && $tmp[0] && $tmp[1]) {
                        $position = $tmp[0];
                        $hookId = Hook::getIdByName($position);
                        $oldhooks = explode(',', Tools::getValue($position));

                        $ids = explode(',', $tmp[1]);
                        if ($hookId && count($oldhooks)) {
                            foreach ($ids as $index => $id_module) {
                                $module = Module::getInstanceById($id_module);

                                if (Validate::isLoadedObject($module) && isset($oldhooks[$index]) && is_numeric($oldhooks[$index]) && $oldhooks[$index] != $hookId) {
                                    // MOVE MODULE TO OTHER HOOK
                                    $sql = 'UPDATE `'._DB_PREFIX_.'hook_module` SET id_hook='.(int)$hookId.'
                                            WHERE id_module='.(int)$id_module.' AND id_hook='.(int)$oldhooks[$index].' AND id_shop='.(int)($id_shop);
                                    try {
                                        // FIX: Drag a module to one hook in 2 times. Save 500 error
                                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                                    } catch (Exception $ex) {
                                    }
                                } elseif (Validate::isLoadedObject($module) && (!isset($oldhooks[$index]) || !(int)$oldhooks[$index])) {
                                    $this->registerHook($id_module, $hookId, array($id_shop));
                                    echo 'new:'.$id_module;
                                }
                                $module->updatePosition($hookId, $way, $index + 1);
                            }
                        }
                    }
                }
            }
            die('{"hasError" : false, "errors" : "update module position"}');
        }
    }

    public function registerHook($id_module, $id_hook, $shop_list = null)
    {
        // If shop lists is null, we fill it with all shops
        if (is_null($shop_list)) {
            $shop_list = Shop::getShops(true, null, true);
        }

        $return = true;
        foreach ($shop_list as $shop_id) {
            // Check if already register
            $sql = 'SELECT hm.`id_module`
                FROM `'._DB_PREFIX_.'hook_module` hm, `'._DB_PREFIX_.'hook` h
                WHERE hm.`id_module` = '.(int)($id_module).' AND h.`id_hook` = '.(int)$id_hook.'
                AND h.`id_hook` = hm.`id_hook` AND `id_shop` = '.(int)$shop_id;


            if (Db::getInstance()->getRow($sql)) {
                continue;
            }

            // Get module position in hook
            $sql = 'SELECT MAX(`position`) AS position
                FROM `'._DB_PREFIX_.'hook_module`
                WHERE `id_hook` = '.(int)$id_hook.' AND `id_shop` = '.(int)$shop_id;
            if (!$position = Db::getInstance()->getValue($sql)) {
                $position = 0;
            }

            // Register module in hook
            $return &= Db::getInstance()->insert('hook_module', array(
                'id_module' => (int)$id_module,
                'id_hook' => (int)$id_hook,
                'id_shop' => (int)$shop_id,
                'position' => (int)($position + 1),
            ));
        }
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     * OVERRIDE CORE
     */
    public function initProcess()
    {
        parent::initProcess();
        
        if (count($this->errors) <= 0) {
            if(Tools::getIsset('updateleohook') && Tools::getValue('updateleohook')){
                if (!$this->access('edit'))
                {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            }
        }
    }
}
