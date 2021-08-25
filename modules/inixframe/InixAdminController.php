<?php

/**
 * Class Inix2AdminController
 */
class Inix2AdminController extends ModuleAdminController {

    /**
     * @var Inix2Module
     */
    public $module;

    /**
     * @var bool
     */
    public $tabs_options = false;

    /**
     * @var bool
     */
    protected $show_cancel_button = false;

    /**
     *
     */
    public function init() {
        // Has to be removed for the next Prestashop version
        global $currentIndex;
        parent::init();

        //Set current index
        $current_index = 'index.php' . (($controller = Tools::getValue('controller')) ? '?controller=' . $controller : '');
        if ($back = Tools::getValue('back')) {
            $current_index .= '&back=' . urlencode($back);
        }
        if (Tools::isSubmit('iniframe')) {
            $current_index .= '&iniframe=1';
        }
        self::$currentIndex = $current_index;
        $currentIndex = $current_index;

        if (!$this->ajax) {
            $this->module->assignUpdate();
        }
        $this->context->smarty->assign(array(
            'module_displayName' => $this->module->displayName,
            'module_name' => $this->module->name,
            'module_version' => $this->module->version,
            'module_local_path' => $this->module->getLocalPath(),
            'module_path_uri' => $this->module->getPathUri(),
            'frame_local_path' => $this->module->getFrameLocalPath(),
            'frame_path_uri' => $this->module->getFramePathUri(),
            'author' => $this->module->author,
            'author_email' => $this->module->author_email,
            'author_domain' => $this->module->author_domain,
            'slogan' => $this->module->slogan,
            'got_feedback' => Configuration::get('FRAME_GOT_FEEDBACK'),
            'show_wellcome' => false,
            'feedback_link' => $this->context->link->getAdminLink('AdminModules') . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $this->module->name . '&iniframe=1&feedback',
            'bugreport_link' => $this->context->link->getAdminLink('AdminModules') . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $this->module->name . '&iniframe=1&bugreport',
            'current' => self::$currentIndex,
        ));
    }

    /**
     *
     */
    public function initProcess() {

        if (Tools::isSubmit('fposition')) {
            $_GET['position'] = Tools::getValue('fposition');
        }
        parent::initProcess();
        if (Tools::isSubmit('submitInixOptions' . $this->table) || Tools::isSubmit('submitInixOptions')) {
            $this->display = 'options';

            if (Tools::version_compare(_PS_VERSION_, '1.7', '<')){
                $checkAccess = $this->tabAccess['edit'];
            } else {
                $checkAccess = $this->access('edit');
            }
            
            if ($checkAccess === '1' OR $checkAccess === true) {
                $this->action = 'update_options';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
    }

    /**
     *
     */
    protected function processUpdateOptions() {
        $this->beforeUpdateOptions();

        $languages = Language::getLanguages(false);
        $id_lang_default = (int) Configuration::get('PS_LANG_DEFAULT');
        $hide_multishop_checkbox = (Shop::getTotalShops(false, null) < 2) ? true : false;
        foreach ($this->fields_options as $category_data) {
            if (!isset($category_data['fields'])) {
                continue;
            }

            $fields = $category_data['fields'];

            foreach ($fields as $field => $values) {
                if (isset($values['type']) && $values['type'] == 'selectLang') {
                    foreach ($languages as $lang) {
                        if (Tools::getValue($field . '_' . strtoupper($lang['iso_code']))) {
                            $fields[$field . '_' . strtoupper($lang['iso_code'])] = array(
                                'type' => 'select',
                                'cast' => 'strval',
                                'identifier' => 'mode',
                                'list' => $values['list']
                            );
                        }
                    }
                }
            }

            // Validate fields
            foreach ($fields as $field => $values) {
                // We don't validate fields with no visibility
                if (!$hide_multishop_checkbox && Shop::isFeatureActive() && isset($values['visibility']) && $values['visibility'] > Shop::getContext()) {
                    continue;
                }

                // Check if field is required
                if ((!Shop::isFeatureActive() && isset($values['required']) && $values['required']) || (Shop::isFeatureActive() && isset($_POST['multishopOverrideOption'][$field]) && isset($values['required']) && $values['required'])
                ) {
                    if (isset($values['type']) && ($values['type'] == 'textLang' or $values['type'] == 'textareaLang')) {
                        $value_default_lang = Tools::getValue($field . '_' . $id_lang_default);
                        if ($value_default_lang == false) {
                            $this->errors[] = sprintf(
                                    Tools::displayError('field %s is required at least in default language.'), $values['title']
                            );
                        }
                    } elseif (($value = Tools::getValue($field)) == false && (string) $value != '0') {
                        $this->errors[] = sprintf(Tools::displayError('field %s is required.'), $values['title']);
                    }
                }

                // Check field validator
                if ((isset($values['is_array']) && $values['is_array'])) {
                    if (isset($values['requried']) and $values['requried'] and ( !Tools::isSubmit($field) or count(Tools::getValue($field)) == 0 or ! Tools::getValue($field))) {
                        $this->errors[] = sprintf(Tools::displayError('fields %s is required!'), $values['title']);
                    } elseif (Tools::getValue($field) && isset($values['validation'])) {
                        foreach (Tools::getValue($field) as $f) {
                            if (!Validate::$values['validation']($f)) {
                                $this->errors[] = sprintf(
                                        Tools::displayError('field %s is invalid.'), $values['title']
                                );
                            }
                        }
                    }
                } else {
                    if (isset($values['type']) && $values['type'] == 'textLang' or $values['type'] == 'textareaLang') {
                        $value_default_lang = Tools::getValue($field . '_' . $id_lang_default);
                        foreach ($languages as $language) {
                            $value = Tools::getValue($field . '_' . $language['id_lang']);
                            if (($value_default_lang or $value) && isset($values['validation'])) {
                                if (!Validate::$values['validation'](Tools::getValue(
                                                        $field . '_' . $language['id_lang'], Tools::getValue($field . '_' . $id_lang_default)
                                        ))
                                ) {
                                    $this->errors[] = sprintf(
                                            Tools::displayError('field %s is invalid.'), $values['title']
                                    );
                                }
                            }
                        }
                    } elseif (Tools::getValue($field) && isset($values['validation'])) {
                        if (!Validate::$values['validation'](Tools::getValue($field))) {
                            $this->errors[] = sprintf(Tools::displayError('field %s is invalid.'), $values['title']);
                        } else { // Set default value
                            if (Tools::getValue($field) === false && isset($values['default'])) {
                                $_POST[$field] = $values['default'];
                            }
                        }
                    }
                }
            }

            if (!count($this->errors)) {
                foreach ($fields as $key => $options) {
                    if (!$hide_multishop_checkbox && Shop::isFeatureActive() && isset($options['visibility']) && $options['visibility'] > Shop::getContext()) {
                        continue;
                    }

                    if (!$hide_multishop_checkbox && Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && empty($options['no_multishop_checkbox']) && empty($_POST['multishopOverrideOption'][$key])) {
                        Configuration::deleteFromContext($key);
                        continue;
                    }

                    // check if a method updateOptionFieldName is available
                    $method_name = 'updateOption' . Tools::toCamelCase($key, true);
                    if (method_exists($this, $method_name)) {
                        $this->$method_name(Tools::getValue($key));
                    } elseif (isset($options['type']) && ($options['type'] == 'textLang' or $options['type'] == 'textareaLang')) {
                        $list = array();
                        foreach ($languages as $language) {
                            $key_lang = Tools::getValue($key . '_' . $language['id_lang']);
                            if (isset($options['required']) and $options['required'] and $language['id_lang'] != $id_lang_default and $key_lang == '') {
                                $key_lang = Tools::getValue($key . '_' . $id_lang_default);
                            }

                            $val = (isset($options['cast']) ? $options['cast']($key_lang) : $key_lang);
                            if ($this->validateField($val, $options)) {
                                if (Validate::isCleanHtml($val)) {
                                    $list[$language['id_lang']] = $val;
                                } else {
                                    $this->errors[] = Tools::displayError('Can not add configuration ' . $key . ' for lang ' . Language::getIsoById((int) $language['id_lang']));
                                }
                            }
                        }
                        Configuration::updateValue($key, $list, isset($options['html']) ? $options['html'] : false);
                    } else {
                        if ($options['type'] == 'products') {
                            $products = explode('-', Tools::getValue($key));
                            array_pop($products);
                            $val = $products;
                        } else {
                            $val = Tools::getValue($key);
                        }

                        if (isset($options['cast'])) {
                            if (isset($options['is_array']) and $options['is_array']) {
                                $val = array_map($options['cast'], $val);
                            } else {
                                $val = $options['cast']($val);
                            }
                        }
                        if ($this->validateField($val, $options)) {
                            if (isset($options['is_array']) and $options['is_array']) {
                                Configuration::updateValue(
                                        $key, json_encode($val), isset($options['html']) ? $options['html'] : false
                                );
                            } elseif (Validate::isCleanHtml($val)) {
                                Configuration::updateValue(
                                        $key, $val, isset($options['html']) ? $options['html'] : false
                                );
                            } else {
                                $this->errors[] = Tools::displayError('Can not add configuration ' . $key);
                            }
                        }
                    }
                }
            }
        }

        $this->afterUpdateOptions();
        $this->display = 'list';
        if (empty($this->errors)) {
            $this->redirect_after = self::$currentIndex . '&token=' . $this->token . '&conf=6';
        }
    }

    /**
     *
     */
    public function setMedia() {
        parent::setMedia();
        $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/bootstrap.css');
        if (Tools::version_compare(_PS_VERSION_, '1.6.0')) {
            $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/animate.css');


            $this->context->controller->addJS($this->module->getFramePathUri() . 'js/vendor/bootstrap.min.js');
            $this->context->controller->addJS($this->module->getFramePathUri() . 'js/vendor/moment-with-langs.min.js');
        }
        $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/font-awesome.css');
        $this->context->controller->addJS($this->module->getFramePathUri() . 'js/frame.js');
        $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/style.css');
        $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/frame.css');
        $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/toastr.min.css');
        $this->context->controller->addJS($this->module->getFramePathUri() . 'js/toastr.min.js');


        if (Tools::isSubmit('iniframe')) {
            $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/admin.css');
        }
    }

    /**
     * @return string
     */
    public function renderModulesList($tracking_source = false) {
        if ($this->getModulesList($this->filter_modules_list)) {
            $helper = new Inix2Helper();

            return $helper->renderModulesList($this->modules_list);
        }
    }

    /**
     * Function used to render the list to display for this controller
     */
    public function renderList() {
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);

        $helper = new Inix2HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->l(
                            'Bad SQL query', 'Helper'
                    ) . '<br />' . htmlspecialchars($this->_list_error));

            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->module = $this->module;
        if(isset($this->_default_pagination)){
            $helper->_default_pagination = $this->_default_pagination;
        }   
        $helper->_pagination = $this->_pagination;

        $helper->tpl_vars = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper->is_cms = $this->is_cms;
        $list = $helper->generateList($this->_list, $this->fields_list);

        return $list;
    }

    /**
     * Override to render the view page
     */
    public function renderView() {
        $helper = new Inix2HelperView($this);
        $this->setHelperDisplay($helper);
        $helper->module = $this->module;
        $helper->tpl_vars = $this->tpl_view_vars;
        if (!is_null($this->base_tpl_view)) {
            $helper->base_tpl = $this->base_tpl_view;
        }
        $view = $helper->generateView();

        return $view;
    }

    /**
     * Function used to render the form for this controller
     */
    public function renderForm() {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }

        if (Tools::getValue('submitFormAjax')) {
            $this->content .= $this->context->smarty->fetch('form_submit_ajax.tpl');
        }
        if ($this->fields_form && is_array($this->fields_form)) {
            if (!$this->multiple_fieldsets) {
                $this->fields_form = array(array('form' => $this->fields_form));
            }

            // For add a fields via an override of $fields_form, use $fields_form_override
            if (is_array($this->fields_form_override) && !empty($this->fields_form_override)) {
                $this->fields_form[0]['form']['input'][] = $this->fields_form_override;
            }

            $helper = new Inix2HelperForm($this);
            $this->setHelperDisplay($helper);
            $helper->module = $this->module;
            $helper->fields_value = $this->getFieldsValue($this->object);
            $helper->tpl_vars = $this->tpl_form_vars;
            $helper->show_cancel_button = $this->show_cancel_button;
            $back = Tools::safeOutput(Tools::getValue('back', ''));
            if (empty($back)) {
                $back = self::$currentIndex . '&token=' . $this->token;
            }
            if (!Validate::isCleanHtml($back)) {
                die(Tools::displayError());
            }

            $helper->back_url = $back;
            !is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';
            if ($this->tabAccess['view']) {
                if (Tools::getValue('back')) {
                    $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue('back'));
                } else {
                    $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue(self::$currentIndex . '&token=' . $this->token));
                }
            }
            $form = $helper->generateForm($this->fields_form);

            return $form;
        }
    }

    /**
     * Function used to render the options for this controller
     */
    public function renderOptions() {
        if ($this->fields_options && is_array($this->fields_options)) {
            if (isset($this->display) && $this->display != 'options' && $this->display != 'list') {
                $this->show_toolbar = false;
            } else {
                $this->display = 'options';
            }

            unset($this->toolbar_btn);
            $this->initToolbar();
            $helper = new Inix2HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->module = $this->module;
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    /**
     * @param Helper $helper
     */
    public function setHelperDisplay(Helper $helper) {
        parent::setHelperDisplay($helper);
        $helper->module = $this->module;
        $helper->override_folder = 'admin/' . $this->override_folder;
        $helper->tabs = $this->tabs_options;
        $helper->is_admin_controller = true;
        $this->helper = $helper;
    }

    /**
     * @param string $tpl_name
     *
     * @return object
     */
    public function createTemplate($tpl_name) {

        // Use override tpl if it exists
        if (file_exists($this->getTemplatePath() . $this->override_folder . $tpl_name) && $this->viewAccess()) {
            return $this->context->smarty->createTemplate(
                            $this->getTemplatePath() . $this->override_folder . $tpl_name, $this->context->smarty
            );
        }

        return $this->context->smarty->createTemplate(
                        $this->module->getFrameLocalPath() . 'template' . DIRECTORY_SEPARATOR . $tpl_name, $this->context->smarty
        );
    }

    /**
     * @param array|string $content
     */
    protected function smartyOutputContent($content) {
        if (!$this->json and ! $this->ajax and ! $this->lite_display) {
            $page = $this->context->smarty->getTemplateVars('page');
            if (!$this->module->clean_layout) {
                $show_update_template = $this->createTemplate('show_update.tpl');
                $footer_template = $this->createTemplate('inixfooter.tpl');
                $page .= $show_update_template->fetch();
                $page .= $footer_template->fetch();
            }
            $this->context->smarty->assign('page', $page);
        }
        $this->context->cookie->write();
        $this->context->smarty->display($content);
    }

    /**
     * @return string
     */
    public function getTemplatePath() {
        return $this->module->getLocalPath() . 'views/templates/admin/';
    }

    /**
     *
     */
    public function ajaxProcessProductList() {
        $this->module->ajaxProcessProductList();
    }

    /**
     *
     */
    public function afterUpdateOptions() {
        
    }

    /**
     * @return void
     */
    public function displayAjax() {

        if (!$this->status) {
            $this->context->smarty->assign('hasresult', 'notok');
        } else {
            $this->context->smarty->assign('hasresult', 'ok');
        }
        $displayAjax = parent::displayAjax();


        return $displayAjax;
    }

}
