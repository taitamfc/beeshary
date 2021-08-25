<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class Inix2HelperForm extends Inix2Helper
{
    public $id;
    public $first_call = true;

    /** @var array of forms fields */
    protected $fields_form = array();

    /** @var array values ​​of form fields */
    public $fields_value = array();

    public $table;
    public $name_controller = '';

    /** @var string if not null, a title will be added on that list */
    public $title = null;

    /** @var string Used to override default 'submitAdd' parameter in form action attribute */
    public $submit_action;

    public $token;
    public $languages = null;
    public $default_form_language = null;
    public $allow_employee_form_lang = null;
    public $dependency = null;

    public $show_cancel_button = false;
    public $back_url = '#';

    public function __construct()
    {
        $this->base_folder = 'helpers/form/';
        $this->base_tpl    = 'form.tpl';
        parent::__construct();
    }

    public function generateForm($fields_form)
    {
        
        $this->fields_form = $fields_form;

        return $this->generate();
    }

    public function generate()
    {
        $this->tpl = $this->createTemplate($this->base_tpl);
        if (is_null($this->submit_action)) {
            $this->submit_action = 'submitAdd' . $this->table;
        }


        $categories        = true;
        $color             = true;
        $date              = true;
        $tinymce           = true;
        $products          = true;
        $textarea_autosize = true;
        $file              = true;
        $datetime          = true;
        $controllers       = true;
        foreach ($this->fields_form as $fieldset_key => &$fieldset) {
            if (isset($fieldset['form']['input'])) {
                foreach ($fieldset['form']['input'] as $key => &$params) {
                    // If the condition is not met, the field will not be displayed
                    if (isset($params['condition']) && !$params['condition']) {
                        unset($this->fields_form[$fieldset_key]['form']['input'][$key]);
                    }
                    switch ($params['type']) {
                        case 'select2':
                            $this->context->controller->addJS($this->module->getFramePathUri() . 'js/select2.js');
                            $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/select2.css');
                            break;
                        case 'tags':
                            $this->context->controller->addJS($this->module->getFramePathUri() . 'js/jquery.tagsinput.js');
                            $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/jquery.tagsinput.css');
                            break;
                        case 'categories':
                        case 'categories_select':
                            if ($categories) {
                                $this->context->controller->addJS($this->module->getFramePathUri() . 'js/tree.js');
                                $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/tree.css');
                                $tree = new InixHelper2TreeCategories(
                                    $params['tree']['id'],
                                    isset($params['tree']['title']) ? $params['tree']['title'] : null
                                );

                                if (isset($params['name'])) {
                                    $tree->setInputName($params['name']);
                                }

                                if (isset($params['tree']['selected_categories'])) {
                                    $tree->setSelectedCategories($params['tree']['selected_categories']);
                                }

                                if (isset($params['tree']['disabled_categories'])) {
                                    $tree->setDisabledCategories($params['tree']['disabled_categories']);
                                }

                                if (isset($params['tree']['root_category'])) {
                                    $tree->setRootCategory($params['tree']['root_category']);
                                } else {
                                    $tree->setRootCategory(Category::getRootCategory()->id);
                                }

                                if (isset($params['tree']['use_search'])) {
                                    $tree->setUseSearch($params['tree']['use_search']);
                                }

                                if (isset($params['tree']['use_checkbox'])) {
                                    $tree->setUseCheckBox($params['tree']['use_checkbox']);
                                }

                                $tree->setModule($this->module);
                                $this->context->smarty->assign('categories_tree', $tree->render());
                                $categories = false;
                            }
                            break;
                        case 'file':
                            if ($file) {
                                if (isset($params['ajax']) and $params['ajax']) {
                                    $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/ladda.css');
                                }
                                $uploader = new Inix2HelperUploader();
                                $uploader->setId(isset($params['id']) ? $params['id'] : null);
                                $uploader->setName($params['name']);
                                $uploader->setUrl(isset($params['url']) ? $params['url'] : null);
                                $uploader->setMultiple(isset($params['multiple']) ? $params['multiple'] : false);
                                $uploader->setUseAjax(isset($params['ajax']) ? $params['ajax'] : false);
                                $uploader->setMaxFiles(isset($params['max_files']) ? $params['max_files'] : null);
                                $uploader->setModule($this->module);
                                if (isset($params['files']) && $params['files']) {
                                    $uploader->setFiles($params['files']);
                                } elseif (isset($params['image']) && $params['image']) { // Use for retrocompatibility
                                    $uploader->setFiles(array(
                                        0 => array(
                                            'type'       => Inix2HelperUploader::TYPE_IMAGE,
                                            'image'      => isset($params['image']) ? $params['image'] : null,
                                            'size'       => isset($params['size']) ? $params['size'] : null,
                                            'delete_url' => isset($params['delete_url']) ? $params['delete_url'] : null
                                        )
                                    ));
                                }

                                if (isset($params['file']) && $params['file']) { // Use for retrocompatibility
                                    $uploader->setFiles(array(
                                        0 => array(
                                            'type'         => Inix2HelperUploader::TYPE_FILE,
                                            'size'         => isset($params['size']) ? $params['size'] : null,
                                            'delete_url'   => isset($params['delete_url']) ? $params['delete_url'] : null,
                                            'download_url' => isset($params['file']) ? $params['file'] : null
                                        )
                                    ));
                                }

                                if (isset($params['thumb']) && $params['thumb']) { // Use for retrocompatibility
                                    $uploader->setFiles(array(
                                        0 => array(
                                            'type'  => Inix2HelperUploader::TYPE_IMAGE,
                                            'image' => isset($params['thumb']) ? '<img src="' . $params['thumb'] . '" alt="' . (isset($params['title']) ? $params['title'] : '') . '" title="' . (isset($params['title']) ? $params['title'] : '') . '" />' : null,
                                        )
                                    ));
                                }

                                $uploader->setTitle(isset($params['title']) ? $params['title'] : null);
                                $params['file'] = $uploader->render();
                                //$file = false;
                            }
                            break;
                        case 'color':
                            if ($color) {
                                // Added JS file
                                $this->context->controller->addJS($this->module->getFramePathUri() . 'js/bootstrap-colorpicker.js');
                                $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/colorpicker.css');
                                $color = false;
                            }
                            break;
                        case 'birthday':
                            $years             = Tools::dateYears();
                            $months            = Tools::dateMonths();
                            $days              = Tools::dateDays();
                            $params['options'] = array(
                                'days'   => $days,
                                'months' => $months,
                                'years'  => $years
                            );
                            break;
                        case 'date':
                        case 'date_range':
                            if ($date) {
                                $this->context->controller->addJS($this->module->getFramePathUri() . 'js/bootstrap-datepicker.js');
                                $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/datepicker.css');
                                $date = false;
                            }
                            break;
                        case 'datetime':
                            if ($datetime) {
                                $this->context->controller->addJS($this->module->getFramePathUri() . 'js/bootstrap-datetimepicker.js');
                                $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/datetimepicker.css');
                                $datetime = false;
                            }
                            break;
                        case 'textarea':
                            $this->context->controller->addJS($this->module->getFramePathUri() . 'js/jquery.autosize.js');
                            if ($tinymce) {
                                $iso                        = $this->context->language->iso_code;
                                $this->tpl_vars['iso']      = file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en';
                                $this->tpl_vars['path_css'] = _THEME_CSS_DIR_;
                                $this->tpl_vars['ad']       = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_);
                                $this->tpl_vars['tinymce']  = true;

                                $this->context->controller->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
                                $this->context->controller->addJS($this->module->getPathUri() . 'inixframe/js/tinymce.inc.js');
                                $tinymce = false;

                                if (file_exists(_PS_ROOT_DIR_ . '/js/tinymce.inc.js')) {
                                    $this->context->controller->addJS(_PS_JS_DIR_ . 'tinymce.inc.js');
                                } elseif (file_exists(_PS_ROOT_DIR_ . '/js/admin/tinymce.inc.js')) {
                                    $this->context->controller->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
                                } else {
                                    $this->context->controller->addJS($this->module->getFramePathUri() . 'js/tinymce.inc.js');
                                }
                            }

                            if ($textarea_autosize) {
                                if (file_exists(_PS_JS_DIR_ . 'jquery/plugins/jquery.autosize.min.js')) {
                                    $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/plugins/jquery.autosize.min.js');
                                } else {
                                    $this->context->controller->addJS($this->module->getFramePathUri() . 'js/jquery.autosize.min.js');
                                }
                                $textarea_autosize = false;
                            }
                            break;

                        case 'shop':
                            $disable_shops  = isset($params['disable_shared']) ? $params['disable_shared'] : false;
                            $params['html'] = $this->renderAssoShop($disable_shops);
                            if (Shop::getTotalShops(false) == 1) {
                                unset($this->fields_form[$fieldset_key]['form']['input'][$key]);
                            }
                            break;

                        case 'controllers':
                            if ($controllers) {
                                $ctrls                         = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
                                $this->tpl_vars['controllers'] = $ctrls;
                                $this->context->controller->addJS($this->module->getFramePathUri() . 'js/select2.js');
                                $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/select2.css');
                            }
                            break;

                        case 'products':
                            if ($products) {
                                $this->context->controller->addJqueryPlugin('autocomplete');
                                $this->context->controller->addJS($this->module->getFramePathUri() . 'js/products.js');
                                $object_products = $params['selected_products'];

                                $display_products = array();

                                if (Tools::getValue($params['name']) != '') {
                                    $post_products = explode('-', Tools::getValue($params['name']));
                                    array_pop($post_products);
                                    $temp_products = array_merge($object_products, $post_products);
                                } else {
                                    $temp_products = $object_products;
                                }
                                $temp_products = array_map('intval', $temp_products);

                                $temp_products = array_unique($temp_products);
                                foreach ($temp_products as $product_id) {
                                    if ($product_id == 0) {
                                        continue;
                                    }

                                    $res                = DB::getInstance()
                                                            ->getRow('SELECT p.id_product,p.reference, pl.name FROM `' . _DB_PREFIX_ . 'product` p
											JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . $this->context->language->id . ')
											WHERE p.id_product = ' . (int) $product_id);
                                    $display_products[] = $res;
                                }
                                unset($temp_products);
                                $this->tpl_vars['products'] = $display_products;

                                $products = false;
                            }
                            break;
                        case 'change-password':
                            $this->context->controller->addJS($this->module->getFramePathUri() . 'js/vendor/jquery-passy.js');
                            $this->context->controller->addJS($this->module->getFramePathUri() . 'js/jquery.validate.js');
                            break;
                        case 'select2':
                            $this->context->controller->addJS($this->module->getFramePathUri() . 'js/select2.js');
                            $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/select2.css');
                            break;
                        case 'switch':
                            $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/switch.css');
                            break;
                    }
                }
            }
        }

        $this->tpl->assign(array(
            'title'                 => $this->title,
            'toolbar_btn'           => $this->toolbar_btn,
            'show_toolbar'          => $this->show_toolbar,
            'toolbar_scroll'        => $this->toolbar_scroll,
            'submit_action'         => $this->submit_action,
            'firstCall'             => $this->first_call,
            'current'               => $this->currentIndex,
            'token'                 => $this->token,
            'table'                 => $this->table,
            'identifier'            => $this->identifier,
            'name_controller'       => $this->name_controller,
            'languages'             => $this->languages,
            'current_id_lang'       => $this->context->language->id,
            'defaultFormLanguage'   => $this->default_form_language,
            'allowEmployeeFormLang' => $this->allow_employee_form_lang,
            'form_id'               => $this->id,
            'fields'                => $this->fields_form,
            'fields_value'          => $this->fields_value,
            'required_fields'       => $this->getFieldsRequired(),
            'vat_number'            => file_exists(_PS_MODULE_DIR_ . 'vatnumber/ajax.php'),
            'module_dir'            => _MODULE_DIR_,
            'contains_states'       => (isset($this->fields_value['id_country']) && isset($this->fields_value['id_state'])) ? Country::containsStates($this->fields_value['id_country']) : null,
            'dependency'            => $this->dependency,
            'base_url'              => $this->context->shop->getBaseURL(),
            'show_cancel_button'    => $this->show_cancel_button,
            'back_url'              => $this->back_url
        ));

        return parent::generate();
    }

    /**
     * Return true if there are required fields
     */
    public function getFieldsRequired()
    {
        foreach ($this->fields_form as $fieldset) {
            if (isset($fieldset['form']['input'])) {
                foreach ($fieldset['form']['input'] as $input) {
                    if (array_key_exists('required', $input) && $input['required'] && $input['type'] != 'radio') {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Render an area to determinate shop association
     *
     * @return string
     */
    public function renderAssoShop($disable_shared = false, $template_directory = null)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }

        $assos = array();
        if ((int) $this->id) {
            $sql = 'SELECT `id_shop`, `' . bqSQL($this->identifier) . '`
					FROM `' . _DB_PREFIX_ . bqSQL($this->table) . '_shop`
					WHERE `' . bqSQL($this->identifier) . '` = ' . (int) $this->id;

            foreach (Db::getInstance()->executeS($sql) as $row) {
                $assos[$row['id_shop']] = $row['id_shop'];
            }
        } else {
            switch (Shop::getContext()) {
                case Shop::CONTEXT_SHOP:
                    $assos[Shop::getContextShopID()] = Shop::getContextShopID();
                    break;

                case Shop::CONTEXT_GROUP:
                    foreach (Shop::getShops(false, Shop::getContextShopGroupID(), true) as $id_shop) {
                        $assos[$id_shop] = $id_shop;
                    }
                    break;

                default:
                    foreach (Shop::getShops(false, null, true) as $id_shop) {
                        $assos[$id_shop] = $id_shop;
                    }
                    break;
            }
        }

        $tree = new InixHelper2TreeShops('shop-tree', 'Shops');
        if (isset($template_directory)) {
            $tree->setTemplateDirectory($template_directory);
        }
        $tree->setModule($this->module);
        $tree->setSelectedShops($assos);
        $tree->setAttribute('table', $this->table);

        return $tree->render();
    }
}
