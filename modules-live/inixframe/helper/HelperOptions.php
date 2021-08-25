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
 * Use this helper to generate preferences forms, with values stored in the configuration table
 */
class Inix2HelperOptions extends Inix2Helper
{
    public $required = false;

    public function __construct()
    {
        $this->base_folder = 'helpers/options/';
        $this->base_tpl    = 'options.tpl';
        parent::__construct();
    }

    /**
     * Generate a form for options
     *
     * @param array options
     *
     * @return string html
     */
    public function generateOptions($option_list)
    {
        $this->tpl = $this->createTemplate($this->base_tpl);
        $tab       = Tab::getTab($this->context->language->id, $this->id);
        if (!isset($languages)) {
            $languages = Language::getLanguages(false);
        }

        $use_multishop           = false;
        $hide_multishop_checkbox = (Shop::getTotalShops(false, null) < 2) ? true : false;

        $tinymce    = true;
        $products   = true;
        $categories = true;
        $color      = true;
        $date       = false;
        foreach ($option_list as $category => $category_data) {
            if (!is_array($category_data)) {
                continue;
            }

            if (!isset($category_data['image'])) {
                $category_data['image'] = (file_exists(_PS_MODULE_DIR_ . $this->module->name . '/logo.png') ? _MODULE_DIR_ . $this->module->name . '/logo.png' : '../img/t/logo.gif') . '" width="20px"';
            }

            if (!isset($category_data['fields'])) {
                $category_data['fields'] = array();
            }

            $category_data['hide_multishop_checkbox'] = true;
            foreach ($category_data['fields'] as $key => $field) {
                if (empty($field['no_multishop_checkbox']) && !$hide_multishop_checkbox) {
                    $category_data['hide_multishop_checkbox'] = false;
                }

                // Set field value unless explicitly denied
                if (!isset($field['auto_value']) || $field['auto_value']) {
                    $field['value'] = $this->getOptionValue($key, $field);
                }

                // Check if var is invisible (can't edit it in current shop context), or disable (use default value for multishop)
                $isDisabled = $isInvisible = false;
                if (Shop::isFeatureActive()) {
                    if (isset($field['visibility']) && $field['visibility'] > Shop::getContext()) {
                        $isDisabled  = true;
                        $isInvisible = true;
                    } elseif (Shop::getContext() != Shop::CONTEXT_ALL && !Configuration::isOverridenByCurrentContext($key)) {
                        $isDisabled = true;
                    }
                }
                $field['is_disabled']  = $isDisabled;
                $field['is_invisible'] = $isInvisible;

                $field['required'] = isset($field['required']) ? $field['required'] : $this->required;

                if ($field['type'] == 'bool') {
                    $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/switch.css');
                }
                if ($field['type'] == 'color') {
                    if ($color) {
                        // Added JS file
                        $this->context->controller->addJS($this->module->getFramePathUri() . 'js/bootstrap-colorpicker.js');
                        $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/colorpicker.css');
                        $color = false;
                    }
                }
                if ($field['type'] == 'textarea' || $field['type'] == 'textareaLang') {
                    $this->context->controller->addJS($this->module->getFramePathUri() . 'js/jquery.autosize.js');
                }

                if ($field['type'] == 'controllers') {
                    $controllers                   = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
                    $this->tpl_vars['controllers'] = $controllers;
                    $this->context->controller->addJS($this->module->getFramePathUri() . 'js/select2.js');
                    $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/select2.css');
                }
                if ($field['type'] == 'file') {
                    $uploader = new Inix2HelperUploader();
                    $uploader->setId(isset($field['id']) ? $field['id'] : null);
                    $uploader->setName($field['name']);
                    $uploader->setUrl(isset($field['url']) ? $field['url'] : null);
                    $uploader->setMultiple(isset($field['multiple']) ? $field['multiple'] : false);
                    $uploader->setUseAjax(isset($field['ajax']) ? $field['ajax'] : false);
                    $uploader->setMaxFiles(isset($field['max_files']) ? $field['max_files'] : null);
                    $uploader->setModule($this->module);

                    if (isset($field['files']) && $field['files']) {
                        $uploader->setFiles($field['files']);
                    } elseif (isset($field['image']) && $field['image']) { // Use for retrocompatibility
                        $uploader->setFiles(array(
                            0 => array(
                                'type'       => Inix2HelperUploader::TYPE_IMAGE,
                                'image'      => isset($field['image']) ? $field['image'] : null,
                                'size'       => isset($field['size']) ? $field['size'] : null,
                                'delete_url' => isset($field['delete_url']) ? $field['delete_url'] : null
                            )
                        ));
                    }

                    if (isset($field['file']) && $field['file']) { // Use for retrocompatibility
                        $uploader->setFiles(array(
                            0 => array(
                                'type'         => Inix2HelperUploader::TYPE_FILE,
                                'size'         => isset($field['size']) ? $field['size'] : null,
                                'delete_url'   => isset($field['delete_url']) ? $field['delete_url'] : null,
                                'download_url' => isset($field['file']) ? $field['file'] : null
                            )
                        ));
                    }

                    if (isset($field['thumb']) && $field['thumb']) { // Use for retrocompatibility
                        $uploader->setFiles(array(
                            0 => array(
                                'type'  => Inix2HelperUploader::TYPE_IMAGE,
                                'image' => isset($field['thumb']) ? '<img src="' . $field['thumb'] . '" alt="' . $field['title'] . '" title="' . $field['title'] . '" class="thumbnail" />' : null,
                            )
                        ));
                    }

                    $uploader->setTitle(isset($field['title']) ? $field['title'] : null);
                    $field['file'] = $uploader->render();
                }
                if (isset($field['autoload_rte']) and $field['autoload_rte']) {
                    if ($tinymce) {
                        $iso                        = $this->context->language->iso_code;
                        $this->tpl_vars['iso']      = file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en';
                        $this->tpl_vars['path_css'] = _THEME_CSS_DIR_;
                        $this->tpl_vars['ad']       = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_);
                        $this->tpl_vars['tinymce']  = true;

                        $this->context->controller->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');

                        if (file_exists(_PS_ROOT_DIR_ . '/js/tinymce.inc.js')) {
                            $this->context->controller->addJS(_PS_JS_DIR_ . 'tinymce.inc.js');
                        } elseif (file_exists(_PS_ROOT_DIR_ . '/js/admin/tinymce.inc.js')) {
                            $this->context->controller->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
                        } else {
                            $this->context->controller->addJS($this->module->getFramePathUri() . 'js/tinymce.inc.js');
                        }
                        $tinymce = false;
                    }
                }
                if ($field['type'] == 'products') {
                    if ($products) {
                        $this->context->controller->addJqueryPlugin('autocomplete');
                        $this->context->controller->addJS($this->module->getFramePathUri() . 'js/products.js');
                        $object_products = $field['selected_products'];

                        $display_products = array();

                        if (is_string(Tools::getValue($key)) and Tools::getValue($key) != '') {
                            $post_products = explode('-', Tools::getValue($key));
                            array_pop($post_products);
                            $temp_products = array_merge($object_products, $post_products);
                        } elseif (is_array(Tools::getValue($key)) and count(Tools::getValue($key))) {
                            $temp_products = array_merge($object_products, Tools::getValue($key));
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
                }
                if ($field['type'] == 'categories') {
                    if ($categories) {
                        $this->context->controller->addJS($this->module->getFramePathUri() . 'js/tree.js');
                        $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/tree.css');
                        $tree = new InixHelper2TreeCategories(
                            $field['tree']['id'],
                            isset($field['tree']['title']) ? $field['tree']['title'] : null
                        );

                        if (isset($field['name'])) {
                            $tree->setInputName($field['name']);
                        }

                        if (isset($field['tree']['selected_categories'])) {
                            $tree->setSelectedCategories($field['tree']['selected_categories']);
                        }

                        if (isset($field['tree']['disabled_categories'])) {
                            $tree->setDisabledCategories($field['tree']['disabled_categories']);
                        }

                        if (isset($field['tree']['root_category'])) {
                            $tree->setRootCategory($field['tree']['root_category']);
                        } else {
                            $tree->setRootCategory(Category::getRootCategory()->id);
                        }

                        if (isset($field['tree']['use_search'])) {
                            $tree->setUseSearch($field['tree']['use_search']);
                        }

                        if (isset($field['tree']['use_checkbox'])) {
                            $tree->setUseCheckBox($field['tree']['use_checkbox']);
                        }


                        $tree->setModule($this->module);
                        $this->context->smarty->assign('categories_tree', $tree->render());
                        $categories = false;
                    }
                }
                if ($field['type'] == 'select2') {
                    $this->context->controller->addJS($this->module->getFramePathUri() . 'js/select2.js');
                    $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/select2.css');
                }
                // Cast options values if specified
                if ($field['type'] == 'select' && isset($field['cast'])) {
                    foreach ($field['list'] as $option_key => $option) {
                        $field['list'][$option_key][$field['identifier']] = $field['cast']($option[$field['identifier']]);
                    }
                }

                // Fill values for all languages for all lang fields
                if (substr($field['type'], - 4) == 'Lang') {
                    foreach ($languages as $language) {
                        if ($field['type'] == 'textLang') {
                            if (isset($field['safe_output']) and $field['safe_output']) {
                                $value = Tools::getValue(
                                    $key . '_' . $language['id_lang'],
                                    Configuration::get($key, $language['id_lang'])
                                );
                            } else {
                                $value = Tools::getValue(
                                    $key . '_' . $language['id_lang'],
                                    Configuration::get($key, $language['id_lang'])
                                );
                            }
                        } elseif ($field['type'] == 'textareaLang') {
                            $value = Configuration::get($key, $language['id_lang']);
                        } elseif ($field['type'] == 'selectLang') {
                            $value = Configuration::get($key, $language['id_lang']);
                        }
                        $field['languages'][$language['id_lang']] = $value;
                        $field['value'][$language['id_lang']]     = $this->getOptionValue(
                            $key . '_' . strtoupper($language['id_lang']),
                            $field
                        );
                    }
                }
                if ($field['type'] == 'date') {
                    $this->context->controller->addJS($this->module->getFramePathUri() . 'js/bootstrap-datepicker.js');
                    $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/datepicker.css');
                }
                if ($field['type'] == 'datetime') {
                    $this->context->controller->addJS($this->module->getFramePathUri() . 'js/bootstrap-datetimepicker.js');
                    $this->context->controller->addCSS($this->module->getFramePathUri() . 'css/datetimepicker.css');
                }


                // Multishop default value
                $field['multishop_default'] = false;
                if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && !$isInvisible) {
                    $field['multishop_default'] = true;
                    $use_multishop              = true;
                }

                // Assign the modifications back to parent array
                $category_data['fields'][$key] = $field;

                // Is at least one required field present?
                if (isset($field['required']) && $field['required']) {
                    $category_data['required_fields'] = true;
                }
            }
            // Assign the modifications back to parent array
            $option_list[$category] = $category_data;
        }

        $this->tpl->assign(array(
            'title'               => $this->title,
            'toolbar_btn'         => $this->toolbar_btn,
            'show_toolbar'        => $this->show_toolbar,
            'toolbar_scroll'      => $this->toolbar_scroll,
            'current'             => $this->currentIndex,
            'table'               => $this->table,
            'token'               => $this->token,
            'option_list'         => $option_list,
            'current_id_lang'     => Configuration::get('PS_LANG_DEFAULT'),//$this->context->language->id,
            'languages'           => isset($languages) ? $languages : null,
            'currency_left_sign'  => $this->context->currency->getSign('left'),
            'currency_right_sign' => $this->context->currency->getSign('right'),
            'use_multishop'       => $use_multishop,
            'tabs'                => $this->tabs,
        ));

        return parent::generate();
    }

    /**
     * Type = image
     */
    public function displayOptionTypeImage($key, $field, $value)
    {
        echo '<table cellspacing="0" cellpadding="0">';
        echo '<tr>';

        $i = 0;
        foreach ($field['list'] as $theme) {
            echo '<td class="center" style="width: 180px; padding:0px 20px 20px 0px;">';
            echo '<input type="radio" name="' . $key . '" id="' . $key . '_' . $theme['name'] . '_on" style="vertical-align: text-bottom;" value="' . $theme['name'] . '"' . (_THEME_NAME_ == $theme['name'] ? 'checked="checked"' : '') . ' />';
            echo '<label class="t" for="' . $key . '_' . $theme['name'] . '_on"> ' . Tools::strtolower($theme['name']) . '</label>';
            echo '<br />';
            echo '<label class="t" for="' . $key . '_' . $theme['name'] . '_on">';
            echo '<img src="../themes/' . $theme['name'] . '/preview.jpg" alt="' . Tools::strtolower($theme['name']) . '">';
            echo '</label>';
            echo '</td>';
            if (isset($field['max']) && ($i + 1) % $field['max'] == 0) {
                echo '</tr><tr>';
            }
            $i ++;
        }
        echo '</tr>';
        echo '</table>';
    }

    /**
     * Type = price
     */
    public function displayOptionTypePrice($key, $field, $value)
    {
        echo $this->context->currency->getSign('left');
        $this->displayOptionTypeText($key, $field, $value);
        echo $this->context->currency->getSign('right') . ' ' . $this->l('(tax excl.)', 'Helper');
    }

    /**
     * Type = disabled
     */
    public function displayOptionTypeDisabled($key, $field, $value)
    {
        echo $field['disabled'];
    }

    public function getOptionValue($key, $field)
    {

        if (isset($field['is_array']) and $field['is_array']) {
            $value = Tools::getValue($key, json_decode(Configuration::get($key), true));
            if (!$value) {
                $value = array();
            }
        } else {
            $value = Tools::getValue($key, Configuration::get($key));
            if (!Validate::isCleanHtml($value)) {
                $value = Configuration::get($key);
            }
        }

        if (isset($field['defaultValue']) && ($value === false or is_null($value))) {
            $value = $field['defaultValue'];
        }

        return $value;
    }
}
