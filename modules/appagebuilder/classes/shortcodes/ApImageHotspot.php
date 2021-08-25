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

class ApImageHotspot extends ApShortCodeBase
{
    public $name = 'ApImageHotspot';
    public $for_module = 'manage';

    public $inputs_lang = array('temp_title', 'temp_image', 'temp_description');
    public $inputs = array('temp_top', 'temp_left', 'temp_hpcolor', 'temp_location', 'temp_textalign', 'temp_trigger', 'temp_opacity', 'temp_width', 'temp_margin', 'temp_padding', 'temp_textcolor', 'temp_backcolor', 'temp_class', 'temp_imagealign');

    public function getInfo()
    {
        return array('label' => $this->l('Image Hotspot'),
            'position' => 5,
            'desc' => $this->l('Display tooltip in your image when user hover over points'),
            'icon_class' => 'icon-image',
            'tag' => 'content');
    }

    public function getConfigList()
    {
        Context::getContext()->smarty->assign('path_image', apPageHelper::getImgThemeUrl());
        $href = Context::getContext()->link->getAdminLink('AdminApPageBuilderImages').'&ajax=1&action=manageimage&imgDir=images';
        $ad = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $iso_tiny_mce = Context::getContext()->language->iso_code;
        $iso_tiny_mce = (file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
        $list_slider = '<button type="button" id="btn-add-hotpot" class="btn btn-default btn-add-level2">
        <i class="icon-plus-sign-alt"></i> '.$this->l('Add Hotspot').'</button><hr/>';
        $list_slider_button = '<div id="frm-level2" class="row-level2 frm-level2">
                            <div class="form-group">
                                <div class="col-lg-12 ">
                                    <button type="button" class="btn btn-primary btn-save-level2"
                                    data-error="'.$this->l('Please enter the title').'">'.$this->l('Save').'</button>
                                    <button type="button" class="btn btn-default btn-reset-level2">'.$this->l('Reset').'</button>
                                    <button type="button" class="btn btn-default btn-cancel-level2">'.$this->l('Cancel').'</button>
                                </div>
                            </div>
                            <script>
                                var ad = "'.$ad.'";
                                var iso = "'.$iso_tiny_mce.'";
                            </script>
                            <hr/>
                        </div>';
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'desc' => $this->l('Auto hide if leave it blank'),
                'lang' => 'true',
                'default' => ''
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Sub Title'),
                'lang' => true,
                'values' => '',
                'autoload_rte' => false,
                'default' => '',
            ),
            array(
                'label' => $this->l('Image'),
                'type' => 'selectImg',
                'href' => $href,
                'name' => 'image',
                'lang' => true,
                'show_image' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'alt',
                'label' => $this->l('Alt'),
                'default' => ''
            ),
            array(
                'type' => 'text',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
            array(
                'type' => 'text',
                'name' => 'url',
                'label' => $this->l('Link to'),
                'lang' => true,
                'desc' => 'Example: http://prestashop.com',
                'default' => ''
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Open new tab'),
                'name' => 'is_open',
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0',
            ),
            array(
                'type' => 'text',
                'name' => 'width',
                'label' => $this->l('Image size width'),
                'desc' => $this->l('Example: auto, 100%, 100px'),
                'default' => '100%'
            ),
            array(
                'type' => 'text',
                'name' => 'height',
                'label' => $this->l('Image size height'),
                'desc' => $this->l('Example: auto, 100%, 100px'),
                'default' => 'auto'
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => $list_slider
            ),
            array(
                'type' => 'text',
                'name' => 'temp_title',
                'label' => $this->l('Title'),
                'lang' => 'true',
                'default' => '',
                'class' => 'input-level2 temp_title js-multilang',
                'form_group_class' => 'row-level2 row2-title',
            ),
            array(
                'label' => $this->l('Image'),
                'type' => 'selectImg',
                'href' => $href,
                'name' => 'temp_image',
                'lang' => true,
                'class' => 'input-level2 temp_image js-multilang',
                'form_group_class' => 'row-level2 row2-image',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Image Align'),
                'name' => 'temp_imagealign',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'left',
                            'name' => $this->l('Left'),
                        ),
                        array(
                            'id' => 'right',
                            'name' => $this->l('Right'),
                        ),
                        array(
                            'id' => 'normal',
                            'name' => $this->l('Normal'),
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'left',
                'class' => 'input-level2 temp_imagealign',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Description'),
                'name' => 'temp_description',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'lang' => true,
                'default' => '',
                'class' => 'input-level2 temp_description js-multilang',
                'form_group_class' => 'row-level2 row2-description',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_top',
                'label' => $this->l('Hotpot Top'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_top',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_left',
                'label' => $this->l('Hotpot Left'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_left',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'color',
                'name' => 'temp_hpcolor',
                'label' => $this->l('Hotpot Color'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_hpcolor',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Location'),
                'name' => 'temp_location',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'top',
                            'name' => $this->l('top'),
                        ),
                        array(
                            'id' => 'right',
                            'name' => $this->l('right'),
                        ),
                        array(
                            'id' => 'bottom',
                            'name' => $this->l('bottom'),
                        ),
                        array(
                            'id' => 'left',
                            'name' => $this->l('left'),
                        ),
                        array(
                            'id' => 'top-left',
                            'name' => $this->l('top-left'),
                        ),
                        array(
                            'id' => 'top-right',
                            'name' => $this->l('top-right'),
                        ),
                        array(
                            'id' => 'right-top',
                            'name' => $this->l('right-top'),
                        ),
                        array(
                            'id' => 'right-bottom',
                            'name' => $this->l('right-bottom'),
                        ),
                        array(
                            'id' => 'bottom-left',
                            'name' => $this->l('bottom-left'),
                        ),
                        array(
                            'id' => 'bottom-right',
                            'name' => $this->l('bottom-right'),
                        ),
                        array(
                            'id' => 'left-top',
                            'name' => $this->l('left-top'),
                        ),
                        array(
                            'id' => 'left-bottom',
                            'name' => $this->l('left-bottom'),
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'none',
                'class' => 'input-level2 temp_location',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Text Align'),
                'name' => 'temp_textalign',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'left',
                            'name' => $this->l('Left'),
                        ),
                        array(
                            'id' => 'top',
                            'name' => $this->l('Top'),
                        ),
                        array(
                            'id' => 'right',
                            'name' => $this->l('Right'),
                        ),
                        array(
                            'id' => 'center',
                            'name' => $this->l('Center'),
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'left',
                'class' => 'input-level2 temp_textalign',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Mouse event'),
                'name' => 'temp_trigger',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'hover',
                            'name' => $this->l('hover'),
                        ),
                        array(
                            'id' => 'hoverable',
                            'name' => $this->l('hoverable'),
                        ),
                        array(
                            'id' => 'click',
                            'name' => $this->l('click'),
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'hover',
                'class' => 'input-level2 temp_trigger',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_opacity',
                'label' => $this->l('Opacity'),
                'lang' => false,
                'default' => '0.6',
                'class' => 'input-level2 temp_opacity',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_class',
                'label' => $this->l('Class'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_class',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_width',
                'label' => $this->l('Width'),
                'lang' => false,
                'default' => '200px',
                'class' => 'input-level2 temp_width',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_margin',
                'label' => $this->l('Margin'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_margin',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'text',
                'name' => 'temp_padding',
                'label' => $this->l('Padding'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_padding',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'color',
                'name' => 'temp_textcolor',
                'label' => $this->l('Text Color'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_textcolor',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'color',
                'name' => 'temp_backcolor',
                'label' => $this->l('Backgroud Color'),
                'lang' => false,
                'default' => '',
                'class' => 'input-level2 temp_backcolor',
                'form_group_class' => 'row-level2',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<script type="text/javascript" src="'.__PS_BASE_URI__.apPageHelper::getJsDir().'colorpicker/js/leo.jquery.colorpicker.js"></script>',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => $list_slider_button
            ),
            array(
                'type' => 'hidden',
                'name' => 'total_slider',
                'default' => ''
            ),
        );
        return $inputs;
    }

    public function addConfigList($values)
    {
        // Get value with keys special
        $config_val = array();
        $total = isset($values['total_slider']) ? $values['total_slider'] : '';
        $arr = explode('|', $total);
        
        $inputs_lang = $this->inputs_lang;
        $inputs = $this->inputs;


        $languages = Language::getLanguages(false);
        foreach ($arr as $i) {
            foreach ($inputs_lang as $config) {
                foreach ($languages as $lang) {
                    $config_val[$config][$i][$lang['id_lang']] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($config.'_'.$i.'_'.$lang['id_lang'], ''));
                }
            }
            foreach ($inputs as $config) {
                $config_val[$config][$i] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($config.'_'.$i, ''));
            }
        }

        Context::getContext()->smarty->assign(array(
            'lang' => $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT')),
            'default_lang' => $lang->id,
            'arr' => $arr,
            'languages' => $languages,
            'config_val' => $config_val,
            'path' => apPageHelper::getImgThemeUrl(),
            'inputs_lang' => $this->inputs_lang,
            'inputs' => $this->inputs,
        ));
        $list_slider = Context::getContext()->smarty->fetch(apPageHelper::getShortcodeTemplatePath('ApImageHotspot.tpl'));
        
        $input = array(
            'type' => 'html',
            'name' => 'default_html',
            'html_content' => $list_slider,
        );
        // Append new input type html
        $this->config_list[] = $input;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
    
    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        if (!Configuration::get('APPAGEBUILDER_LOAD_IMAGEHOTPOT')) {
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Please enable Image Hotpot library in Appagebuilder Configuration.';
            return $assign;
        }
        $assign['path'] = apPageHelper::getImgThemeUrl();
        $total_slider = isset($assign['formAtts']['total_slider']) ? $assign['formAtts']['total_slider'] : '';
        $list = explode('|', $total_slider);
        $list_items = array();
        $lang = Language::getLanguage(Context::getContext()->language->id);
        $id_lang = $lang['id_lang'];
        
        $inputs_lang = $this->inputs_lang;
        $inputs = $this->inputs;
        
        foreach ($list as $number) {
            if ($number) {
                $item = array();
                $item['id'] = $number;

                foreach ($inputs_lang as $key) {
                    # MULTI-LANG
                    $name = $key.'_'.$number.'_'.$id_lang;
                    $item[$key] = isset($assign['formAtts'][$name]) ? $assign['formAtts'][$name] : '';

                    // Description
                    if ($key == 'temp_description' && isset($assign['formAtts'][$name]) && $assign['formAtts'][$name]) {
                        $item[$key] = str_replace($this->str_search, $this->str_relace_html, $assign['formAtts'][$name]);
                    }

                    // Image
                    if ($key == 'temp_image' && isset($assign['formAtts'][$name]) && $assign['formAtts'][$name]) {
                        $item[$key] = apPageHelper::getImgThemeUrl() . $assign['formAtts'][$name];
                    } else if ($key == 'temp_image') {
                        $item[$key] = '';
                    }
                }
                foreach ($inputs as $key) {
                    # SINGLE-LANG
                    $name = $key.'_'.$number;
                    $item[$key] = isset($assign['formAtts'][$name]) ? $assign['formAtts'][$name] : '';
                }

                $list_items[] = $item;
            }
        }
        $assign['formAtts']['items'] = $list_items;

        return $assign;
    }
}
