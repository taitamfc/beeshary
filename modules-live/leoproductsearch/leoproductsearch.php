<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Quick product search by category block
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

class LeoProductSearch extends Module
{

    public $tabs;
    protected $_postErrors = array();
    private $_html = '';

    public function __construct()
    {
        $this->name = 'leoproductsearch';
        $this->tab = 'search_filter';
        $this->version = '2.1.6';
        $this->author = 'LeoTheme';
        $this->need_instance = 0;
        $this->controllers = array('productsearch');
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Quick product search by category block');
        $this->description = $this->l('Adds a quick product search field to your website.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        //DONGND:: create tab array
        $this->tabs = array(
            array(
                'class_name' => 'AdminLeoProductSearchModule',
                'name' => 'Leo Product Search Configuration',
                'id_parent' => Tab::getIdFromClassName('AdminParentModulesSf'),
            ),
        );
    }

    public function install()
    {
        if (parent::install() && $this->registerLeoHook()) {
            $res = true;
            //DONGND:: add tab
            foreach ($this->tabs as $tab) {
                $newtab = new Tab();
                $newtab->class_name = $tab['class_name'];
                $newtab->id_parent = isset($tab['id_parent']) ? $tab['id_parent'] : 0;
                $newtab->module = 'leoproductsearch';
                foreach (Language::getLanguages(false) as $l) {
                    $newtab->name[$l['id_lang']] = $this->l($tab['name']);
                }
                $res &= $newtab->save();
            }

            $this->_installDataSample();
            $this->createConfiguration();

            return (bool) $res;
        }

        return false;
    }

    public function uninstall()
    {
        if (parent::uninstall() && $this->unregisterLeoHook()) {
            $res = true;

            foreach ($this->tabs as $tab) {
                $id = Tab::getIdFromClassName($tab['class_name']);
                if ($id) {
                    $tab = new Tab($id);
                    $tab->delete();
                }
            }

            $this->deleteConfiguration();

            return (bool) $res;
        }
        return false;
    }

    public function registerLeoHook()
    {
        $res = true;
        $res &= $this->registerHook('top');
        $res &= $this->registerHook('header');
        $res &= $this->registerHook('displayMobileTopSiteMap');
                $res &= $this->registerHook('actionAdminControllerSetMedia');
        return $res;
    }

    /**
     * Common method
     * Unresgister all hook for module
     */
    public function unregisterLeoHook()
    {
        $res = true;
        $res &= $this->unregisterHook('top');
        $res &= $this->unregisterHook('header');
        $res &= $this->unregisterHook('displayMobileTopSiteMap');
                $res &= $this->unregisterHook('actionAdminControllerSetMedia');

        return $res;
    }

    //DONGND:: create configs
    public function createConfiguration()
    {
        Configuration::updateValue('LEOPRODUCTSEARCH_ENABLE', 1);
                Configuration::updateValue('LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH', 1);
                Configuration::updateValue('LEOPRODUCTSEARCH_NUMPRO_DISPLAY', 100);
        Configuration::updateValue('LEOPRODUCTSEARCH_ENABLE_CATEGORY', 1);
        Configuration::updateValue('LEOPRODUCTSEARCH_CATEGORY_MAXDEPTH', 0);
        Configuration::updateValue('LEOPRODUCTSEARCH_ENABLE_PRODUCTIMG', 1);
        Configuration::updateValue('LEOPRODUCTSEARCH_ENABLE_PRODUCTPRICE', 1);

        return true;
    }

    //DONGND:: delete configs
    public function deleteConfiguration()
    {
        Configuration::deleteByName('LEOPRODUCTSEARCH_ENABLE');
        Configuration::deleteByName('LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH');
        Configuration::deleteByName('LEOPRODUCTSEARCH_NUMPRO_DISPLAY');
        Configuration::deleteByName('LEOPRODUCTSEARCH_ENABLE_CATEGORY');
        Configuration::deleteByName('LEOPRODUCTSEARCH_CATEGORY_MAXDEPTH');
        Configuration::deleteByName('LEOPRODUCTSEARCH_ENABLE_PRODUCTIMG');
        Configuration::deleteByName('LEOPRODUCTSEARCH_ENABLE_PRODUCTPRICE');

        return true;
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

        //DONGND:: correct module
        if (Tools::getValue('correctmodule')) {
            $this->correctModule();
        }

        if (((bool) Tools::isSubmit('submitLeoproductsearchConfig')) == true) {
            if (!Validate::isUnsignedInt(Tools::getValue('LEOPRODUCTSEARCH_CATEGORY_MAXDEPTH'))) {
                $this->_html .= $this->displayError($this->l('"Max Depth Of Category" is invalid. Must an integer validity (unsigned).'));
            } elseif (!Validate::isUnsignedInt(Tools::getValue('LEOPRODUCTSEARCH_NUMPRO_DISPLAY'))) {
                $this->_html .= $this->displayError($this->l('"Number product display with AJAX Search" is invalid. Must an integer validity (unsigned).'));
            } else {
                $form_values = $this->getGroupFieldsValues();

                foreach (array_keys($form_values) as $key) {
                    Configuration::updateValue($key, Tools::getValue($key));
                }

                $this->_html = $this->displayConfirmation($this->trans('Successful update.', array(), 'Admin.Notifications.Success'));
            }
        }
    }

    public function getContent()
    {
        $this->errors = array();
        if (!$this->access('configure')) {
            $this->errors[] = $this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error');
//            $this->context->smarty->assign('errors', $this->errors);
            $this->_html .= $this->displayError($this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error'));
        }

        $this->postProcess();

        $this->_html .= $this->renderGroupConfig();

        return $this->_html;
    }

    public function renderGroupConfig()
    {
        $fields_form = array();
        $fields_form[0]['form'] = array(
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Leo Product Search'),
                    'name' => 'LEOPRODUCTSEARCH_ENABLE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Ajax Search'),
                    'name' => 'LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Number product display with AJAX Search'),
                    'name' => 'LEOPRODUCTSEARCH_NUMPRO_DISPLAY',
                    'desc' => $this->l('Default is 100'),
                    'lang' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Search By Category'),
                    'name' => 'LEOPRODUCTSEARCH_ENABLE_CATEGORY',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_CATEGORY_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_CATEGORY_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum Depth Of Category'),
                    'name' => 'LEOPRODUCTSEARCH_CATEGORY_MAXDEPTH',
                    'desc' => $this->l('Set the maximum depth of category sublevels displayed in this block (0 = infinite). '),
                    'lang' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Product Image'),
                    'name' => 'LEOPRODUCTSEARCH_ENABLE_PRODUCTIMG',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_PRODUCTIMG_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_PRODUCTIMG_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Product Price'),
                    'name' => 'LEOPRODUCTSEARCH_ENABLE_PRODUCTPRICE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_PRODUCTPRICE_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'LEOPRODUCTSEARCH_ENABLE_PRODUCTPRICE_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default'
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->name_controller = 'leoproductsearch';
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLeoproductsearchConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getGroupFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $globalform = $helper->generateForm($fields_form);

        //DONGND::
        $this->context->smarty->assign(array(
            'globalform' => $globalform,
            'url_admin' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
        ));
        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/panel.tpl');
    }

    /**
     * Set values for the inputs.
     */
    protected function getGroupFieldsValues()
    {
        $list_value = array();
        $list_value['LEOPRODUCTSEARCH_ENABLE'] = Configuration::get('LEOPRODUCTSEARCH_ENABLE');
        $list_value['LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH'] = Configuration::get('LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH');
        $list_value['LEOPRODUCTSEARCH_NUMPRO_DISPLAY'] = Configuration::get('LEOPRODUCTSEARCH_NUMPRO_DISPLAY');
        $list_value['LEOPRODUCTSEARCH_ENABLE_CATEGORY'] = Configuration::get('LEOPRODUCTSEARCH_ENABLE_CATEGORY');
        $list_value['LEOPRODUCTSEARCH_CATEGORY_MAXDEPTH'] = Configuration::get('LEOPRODUCTSEARCH_CATEGORY_MAXDEPTH');
        $list_value['LEOPRODUCTSEARCH_ENABLE_PRODUCTIMG'] = Configuration::get('LEOPRODUCTSEARCH_ENABLE_PRODUCTIMG');
        $list_value['LEOPRODUCTSEARCH_ENABLE_PRODUCTPRICE'] = Configuration::get('LEOPRODUCTSEARCH_ENABLE_PRODUCTPRICE');

        return $list_value;
    }

    private function _installDataSample()
    {
        if (!file_exists(_PS_MODULE_DIR_ . 'appagebuilder/libs/LeoDataSample.php')) {
            return false;
        }
        require_once(_PS_MODULE_DIR_ . 'appagebuilder/libs/LeoDataSample.php');

        $sample = new Datasample(1);
        return $sample->processImport($this->name);
    }

    public function hookdisplayMobileTopSiteMap($params)
    {
        $this->smarty->assign(array('hook_mobile' => true, 'instantsearch' => false));
        $params['hook_mobile'] = true;
        return $this->hookTop($params);
    }

    public function hookHeader($params)
    {
        if (Configuration::get('LEOPRODUCTSEARCH_ENABLE')) {
            $media_dir = $this->getMediaDir();
            $this->context->controller->registerStylesheet('modules-leosearch', $media_dir.'css/leosearch.css', array('media' => 'all', 'priority' => 150));
            $this->context->controller->registerStylesheet('modules-product_list', _THEME_CSS_DIR_ . 'product_list.css', array('media' => 'all', 'priority' => 150));
            $this->context->controller->registerJavascript('modules-productsearchjs', $media_dir.'js/jquery.autocomplete_productsearch.js', array('position' => 'bottom', 'priority' => 150));
            $this->context->controller->registerStylesheet('modules-autocomplete_productsearch', $media_dir.'css/jquery.autocomplete_productsearch.css', array('media' => 'all', 'priority' => 150));
            Media::addJsDef(array(
                'leo_search_url' => $this->context->link->getModuleLink('leoproductsearch', 'productsearch', array(), Tools::usingSecureMode()),
                'ajaxsearch' => Configuration::get('LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH'),
                'numpro_display' => Configuration::get('LEOPRODUCTSEARCH_NUMPRO_DISPLAY'),
                'txt_not_found' => $this->l('No products found'),
                'lps_show_product_img' => Configuration::get('LEOPRODUCTSEARCH_ENABLE_PRODUCTIMG'),
                'lps_show_product_price' => Configuration::get('LEOPRODUCTSEARCH_ENABLE_PRODUCTPRICE'),
                'leoproductsearch_static_token' => Tools::getToken(false),
                'leoproductsearch_token' => Tools::getToken(),
            ));
            $this->context->controller->registerJavascript('modules-leosearchjs', $media_dir.'js/leosearch.js', array('position' => 'bottom', 'priority' => 150));
        }
    }

    public function hookLeftColumn($params)
    {
        if (Configuration::get('LEOPRODUCTSEARCH_ENABLE')) {
            if (Tools::getValue('search_query') || !$this->isCached('module:leoproductsearch/views/templates/front/leosearch.tpl', $this->getCacheId())) {
                $this->calculHookCommon($params);
                $selectedCateName = '';
                if (Tools::getValue('cate') && Tools::getValue('cate') != '' && $category_obj = new Category(Tools::getValue('cate'))) {
                    $selectedCateName = $category_obj->name;
                }
                $category = new Category((int) Category::getRootCategory()->id, $this->context->language->id);
                $this->smarty->assign(array(
                    'blocksearch_type' => 'block',
                    'search_query' => (string) Tools::getValue('search_query'),
                    'selectedCate' => (string) Tools::getValue('cate'),
                    'selectedCateName' => $selectedCateName,
                    'cates' => $this->getCategories($category),
                    'en_search_by_cat' => Configuration::get('LEOPRODUCTSEARCH_ENABLE_CATEGORY'),
                ));
            }
            Media::addJsDef(array('blocksearch_type' => 'block'));
            // return $this->display(__FILE__, 'leosearch.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
                        return $this->fetch('module:leoproductsearch/views/templates/front/leosearch.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
        }
    }

    public function hookTop($params)
    {
        if (Configuration::get('LEOPRODUCTSEARCH_ENABLE')) {
            if (Tools::getValue('search_query') || !$this->isCached('module:leoproductsearch/views/templates/front/leosearch_top.tpl', $this->getCacheId())) {
                $this->calculHookCommon($params);
                $selectedCateName = '';
                if (Tools::getValue('cate') && Tools::getValue('cate') != '' && $category_obj = new Category(Tools::getValue('cate'), $this->context->language->id)) {
                    $selectedCateName = $category_obj->name;
                }
                $category = new Category((int) Category::getRootCategory()->id, $this->context->language->id);

                $this->smarty->assign(array(
                    'blocksearch_type' => 'top',
                    'search_query' => (string) Tools::getValue('search_query'),
                    'selectedCate' => (string) Tools::getValue('cate'),
                    'selectedCateName' => $selectedCateName,
                    'cates' => $this->getCategories($category),
                    'en_search_by_cat' => Configuration::get('LEOPRODUCTSEARCH_ENABLE_CATEGORY'),
                    'leoproductsearch_static_token' => Tools::getToken(false),
                    'leoproductsearch_token' => Tools::getToken(),
                ));
            }
            // Media::addJsDef(array('blocksearch_type' => 'top'));
            // return $this->display(__FILE__, 'leosearch_top.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
            return $this->fetch('module:leoproductsearch/views/templates/front/leosearch_top.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
        }
    }

    public function hookDisplayNav($params)
    {
        return $this->hookTop($params);
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    // public function hookActionAdminControllerSetMedia()
    // {
                // $this->autoRestoreSampleData();
    // }

    private function calculHookCommon($params)
    {
        $this->smarty->assign(array(
            'ENT_QUOTES' => ENT_QUOTES,
            'search_ssl' => Tools::usingSecureMode(),
            'ajaxsearch' => Configuration::get('LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH'),
            'numpro_display' => Configuration::get('LEOPRODUCTSEARCH_NUMPRO_DISPLAY'),
            // 'ajaxsearch' => 1,
            'instantsearch' => Configuration::get('PS_INSTANT_SEARCH'),
            'self' => dirname(__FILE__),
        ));

        unset($params);
        return true;
    }

    private function getCategories($category)
    {
        $range = '';
        $maxdepth = Configuration::get('LEOPRODUCTSEARCH_CATEGORY_MAXDEPTH');
        if (Validate::isLoadedObject($category)) {
            // if ($maxdepth > 0) {
            // $maxdepth += $category->level_depth;
            // }
            $range = 'AND nleft >= ' . (int) $category->nleft . ' AND nright <= ' . (int) $category->nright;
        }

        $resultIds = array();
        $resultParents = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                        SELECT c.id_parent, c.id_category, c.level_depth, cl.name, cl.link_rewrite
                        FROM `' . _DB_PREFIX_ . 'category` c
                        INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = ' . (int) $this->context->language->id . Shop::addSqlRestrictionOnLang('cl') . ')
                        INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = ' . (int) $this->context->shop->id . ')
                        WHERE (c.`active` = 1 OR c.`id_category` = ' . (int) Configuration::get('PS_HOME_CATEGORY') . ')
                        AND c.`id_category` != ' . (int) Configuration::get('PS_ROOT_CATEGORY') . '
                        ' . ((int) $maxdepth != 0 ? ' AND `level_depth` <= ' . (int) $maxdepth : '') . '
                        ' . $range . '
                        AND c.id_category IN (
                                SELECT id_category
                                FROM `' . _DB_PREFIX_ . 'category_group`
                                WHERE `id_group` IN (' . pSQL(implode(', ', Customer::getGroupsStatic((int) $this->context->customer->id))) . ')
                        )
                        ORDER BY `level_depth` ASC, ' . (Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'cs.`position`') . ' ' . (Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC'));
        foreach ($result as &$row) {
            $resultParents[$row['id_parent']][] = &$row;
            $resultIds[$row['id_category']] = &$row;
        }

        return $this->getTree($resultParents, $resultIds, $maxdepth, ($category ? $category->id : null));
    }

    public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
    {
        if (is_null($id_category)) {
            $id_category = $this->context->shop->getCategory();
        }

        $children = array();

        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth)) {
            foreach ($resultParents[$id_category] as $subcat) {
                $children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
            }
        }

        if (isset($resultIds[$id_category])) {
            $link = $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']);
            $name = $resultIds[$id_category]['name'];
            // $desc = $resultIds[$id_category]['description'];
            $level_depth = $resultIds[$id_category]['level_depth'];
        } else {
            // $link = $name = $desc = '';
            $link = $name = '';
        }

        return array(
            'id_category' => $id_category,
            'level_depth' => $level_depth,
            'link' => $link,
            'name' => $name,
            // 'desc'=> $desc,
            'children' => $children
        );
    }

    public function correctModule()
    {
        if (!Configuration::hasKey('LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH')) {
            Configuration::updateValue('LEOPRODUCTSEARCH_ENABLE_AJAXSEARCH', 1);
        }
        if (!Configuration::hasKey('LEOPRODUCTSEARCH_NUMPRO_DISPLAY')) {
            Configuration::updateValue('LEOPRODUCTSEARCH_NUMPRO_DISPLAY', 100);
        }
        if (Tools::getValue('success')) {
            switch (Tools::getValue('success')) {
                case 'correct':
                    $this->_html .= $this->displayConfirmation($this->l('Correct Module is successful'));
                    break;
            }
        }
    }

    // public function isCached($template, $cache_id = null, $compile_id = null)
    // {
        // if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
            // return false;
        // }
        // return parent::isCached($template, $cache_id, $compile_id);
    // }

        /**
     * FIX BUG 1.7.3.3 : install theme lose hook displayHome, displayLeoProfileProduct
     * because ajax not run hookActionAdminBefore();
     */
    // public function autoRestoreSampleData()
    // {
        // if (Hook::isModuleRegisteredOnHook($this, 'actionAdminBefore', (int)Context::getContext()->shop->id)) {
            // $theme_manager = new stdclass();
            // $theme_manager->theme_manager = 'theme_manager';
            // $this->hookActionAdminBefore(array(
                // 'controller' => $theme_manager,
            // ));
        // }
    // }

    //DONGND:: update direction css, js, img for 1.7.4.0
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
}
