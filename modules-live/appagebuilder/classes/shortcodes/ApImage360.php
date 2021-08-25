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

class ApImage360 extends ApShortCodeBase
{
    public $name = 'ApImage360';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Image 360'), 'position' => 20, 'desc' => $this->l('Adds multiple 360 images, rotating display objects'),
            'icon_class' => 'icon-image', 'tag' => 'content structure');
    }

    public function getConfigList()
    {
        $href = Context::getContext()->link->getAdminLink('AdminApPageBuilderImages').'&ajax=1&action=manageimage&imgDir=images&widget=ApImage360';
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'lang' => 'true',
                'default' => '',
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
//            array(
//                'type' => 'switch',
//                'label' => $this->l('Magnifier enable'),
//                'name' => 'magnify',
//                'is_bool' => true,
//                'values' => ApPageSetting::returnYesNo(),
//                'default' => 1,
//            ),
//            array(
//                'type' => 'text',
//                'name' => 'magnifier_width',
//                'label' => $this->trans("Magnifier's width"),
//                'default' => '80%',
//            ),
//            array(
//                'type' => 'select',
//                'label' => $this->trans("Magnifier's shape"),
//                'name' => 'magnifier_shape',
//                'options' => array(
//                    'query' => array(
//                        array('id' => 'inner', 'name' => $this->l('Inner')),
//                        array('id' => 'circle', 'name' => $this->l('Circle')),
//                        array('id' => 'square', 'name' => $this->l('Square')),
//                    ),
//                    'id' => 'id',
//                    'name' => 'name'
//                ),
//            ),
//            array(
//                'type' => 'switch',
//                'label' => $this->l('Fullscreen enable'),
//                'name' => 'fullscreen',
//                'is_bool' => true,
//                'values' => ApPageSetting::returnYesNo(),
//                'default' => 1,
//            ),
            array(
                'type' => 'select',
                'label' => $this->l('Spin'),
                'name' => 'spin',
                'options' => array(
                    'query' => array(
                        array('id' => 'drag', 'name' => $this->l('Drag')),
                        array('id' => 'hover', 'name' => $this->l('Hover')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Method for spinning the image'),
            ),
            array(
                'type' => 'text',
                'name' => 'speed',
                'label' => $this->l('Speed'),
//                'lang' => 'true',
                'default' => '50',
                'desc' => $this->l('Mouse drag distance in pixels to next image (1 - 100). Ex 50'),
            ),
            array(
                'type' => 'text',
                'name' => 'mousewheel_step',
                'label' => $this->l('Mousewheel Step'),
//                'lang' => 'true',
                'default' => '1',
                'desc' => $this->l('Number of image to spin on mousewheel'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Smoothing'),
                'name' => 'smoothing',
                'is_bool' => true,
                'values' => ApPageSetting::returnYesNo(),
                'default' => 1,
                'desc' => $this->l('Smoothly stop the image spinning'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Initialization'),
                'name' => 'initialize_on',
                'options' => array(
                    'query' => array(
                        array('id' => 'hover', 'name' => $this->l('hover')),
                        array('id' => 'click', 'name' => $this->l('click')),
                        array('id' => 'load', 'name' => $this->l('load')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'load',
                'desc' => $this->l('Start automatic spin on page load, click or hover'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Autospin duration'),
                'name' => 'autospin',
                'options' => array(
                    'query' => array(
                        array('id' => 'once', 'name' => $this->l('Once')),
                        array('id' => 'twice', 'name' => $this->l('Twice')),
                        array('id' => 'infinite', 'name' => $this->l('Infinite')),
                        array('id' => 'off', 'name' => $this->l('Off')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
//                'desc' => $this->l('Method for spinning the image'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Autospin start'),
                'name' => 'autospin_start',
                'options' => array(
                    'query' => array(
                        array('id' => 'load', 'name' => $this->l('load')),
                        array('id' => 'hover', 'name' => $this->l('hover')),
                        array('id' => 'click', 'name' => $this->l('click')),
                        array('id' => 'load,hover', 'name' => $this->l('load,hover')),
                        array('id' => 'load,click', 'name' => $this->l('load,click')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Start automatic spin on page load, click or hover'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Autospin stops'),
                'name' => 'autospin_stop',
                'options' => array(
                    'query' => array(
                        array('id' => 'hover', 'name' => $this->l('hover')),
                        array('id' => 'click', 'name' => $this->l('click')),
                        array('id' => 'never', 'name' => $this->l('never')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Start automatic spin on page load, click or hover'),
            ),
            array(
                'type' => 'text',
                'name' => 'autospin_speed',
                'label' => $this->l('Autospin Time'),
//                'lang' => 'true',
                'default' => '2000',
                'desc' => $this->l('Value is milisecond. Ex 2000 (2s)'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Autospin direction'),
                'name' => 'autospin_direction',
                'options' => array(
                    'query' => array(
                        array('id' => 'clockwise', 'name' => $this->l('Clockwise')),
                        array('id' => 'anticlockwise', 'name' => $this->l('Anticlockwise')),
                        array('id' => 'alternate-clockwise', 'name' => $this->l('Alternate Clockwise')),
                        array('id' => 'alternate-anticlockwise', 'name' => $this->l('Alternate Anticlockwise')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Method for spinning the image'),
            ),
            array(
                'type' => 'text',
                'name' => 'start_column',
                'label' => $this->l('Start Column'),
                'default' => '1',
                'desc' => $this->l('Column from which to start spin. auto means to start from the middle'),
            ),
//            array(
//                'type' => 'text',
//                'name' => 'start_row',
//                'label' => $this->l('Start Row'),
//                'default' => 'auto',
//                'desc' => $this->l('Row from which to start spin. auto means to start from the middle'),
//            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Loop Column'),
                'name' => 'loop_column',
                'is_bool' => true,
                'values' => ApPageSetting::returnYesNo(),
                'default' => 1,
                'desc' => $this->l('Continue spin after the last image on X-axis'),
            ),
//            array(
//                'type' => 'switch',
//                'label' => $this->l('Loop Row'),
//                'name' => 'loop_row',
//                'is_bool' => true,
//                'values' => ApPageSetting::returnYesNo(),
//                'default' => 1,
//                'desc' => $this->l('Continue spin after the last image on Y-axis'),
//            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Reverse rotation on X-axis'),
                'name' => 'reverse_column',
                'is_bool' => true,
                'values' => ApPageSetting::returnYesNo(),
                'default' => 0,
            ),
//            array(
//                'type' => 'switch',
//                'label' => $this->l('Reverse rotation on Y-axis'),
//                'name' => 'reverse_row',
//                'is_bool' => true,
//                'values' => ApPageSetting::returnYesNo(),
//                'default' => 1,
//            ),
//            array(
//                'type' => 'text',
//                'name' => 'column_increment',
//                'label' => $this->l('Column increment'),
//                'default' => '1',
//                'desc' => $this->l('Load only every second (2) or third (3) column so that spins load faster'),
//            ),
//            array(
//                'type' => 'text',
//                'name' => 'row_increment',
//                'label' => $this->l('Row increment'),
//                'default' => '1',
//                'desc' => $this->l('Load only every second (2) or third (3) row so that spins load faster'),
//            ),
            array(
                'type' => 'text',
                'name' => 'message',
                'label' => $this->l('Message under image'),
                'default' => 'Drag image to spin',
                'lang' => true,
            ),
//            array(
//                'type' => 'text',
//                'name' => 'message_loading',
//                'label' => $this->l('Message Loading'),
//                'default' => 'Text displayed while images are loading.',
//                'lang' => true,
//            ),
//            array(
//                'type' => 'text',
//                'name' => 'message_loading_fullscreen',
//                'label' => $this->l('Message Fullscren Loading'),
//                'default' => 'Loading...',
//                'lang' => true,
//            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show hint message'),
                'name' => 'hint',
                'is_bool' => true,
                'values' => ApPageSetting::returnYesNo(),
                'default' => 1,
            ),
            array(
                'type' => 'text',
                'name' => 'message_desktop_hint',
                'label' => $this->l('Message Desktop Hint'),
                'default' => 'Drag to spin',
                'lang' => true,
                'desc' => $this->l('Text of the hint on Desktop'),
            ),
            array(
                'type' => 'text',
                'name' => 'message_mobile_hint',
                'label' => $this->l('Message Mobile Hint'),
                'default' => 'Swipe to spin',
                'lang' => true,
                'desc' => $this->l('Text of the hint on iOS/Android devices'),
            ),
            array(
                'type' => 'hidden',
                'name' => 'total_slider',
                'default' => '',
            ),
            array(
                'label' => $this->l('Image'),
                'type' => 'selectImg',
                'href' => $href,
                'name' => 'image_360',
                'lang' => false,
                'class' => 'item-add-slide ignore-lang',
                'form_group_class' => 'apfullslider-row select-img',
                'show_image' => false,
            )
        );
        return $inputs;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
    
    /**
     * Widget can override this method and add more config at here
     */
    public function addConfigList($values)
    {
        // Get value with keys special
        $config_val = array();
        $total = isset($values['total_slider']) ? $values['total_slider'] : '';

        $arr = explode('|', $total);
        $inputs = array('image360');
//        $languages = Language::getLanguages(false);
        foreach ($arr as $i) {
            foreach ($inputs as $config) {
                $config_val[$config][$i] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($config.'_'.$i, ''));
            }
        }

        Context::getContext()->smarty->assign(array(
            'arr' => $arr,
            'config_val' => $config_val,
            'image_folder' => apPageHelper::getImgThemeUrl(),
        ));
        $list_slider = Context::getContext()->smarty->fetch(apPageHelper::getShortcodeTemplatePath('ApImage360.tpl'));
        $input = array(
            'type' => 'html',
            'name' => 'default_html',
            'html_content' => $list_slider
        );
        $this->config_list[] = $input;
    }

    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        
        if (!Configuration::get('APPAGEBUILDER_LOAD_IMAGE360')) {
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Please enable Magic360 library in Appagebuilder Configuration.';
            return $assign;
        }

        $total_slider = isset($assign['formAtts']['total_slider']) ? $assign['formAtts']['total_slider'] : '';
        $list = explode('|', $total_slider);
        

        $image_list = array();
        $image_path = apPageHelper::getImgThemeUrl();

        foreach ($list as $item) {
            if (isset($assign['formAtts']['image360_'.$item])) {
                $image_list[] = $assign['formAtts']['image360_'.$item];
            }
        }

        $assign['formAtts']['image_path'] = $image_path;
        $assign['formAtts']['columns'] = count($list);
        $assign['formAtts']['row'] = 1;
        $assign['formAtts']['image_list'] = $image_list;

        // IMAGE DEFAULT
        $min_key = min(array_keys($image_list));
        $assign['formAtts']['image_default'] = $image_list[$min_key];

        return $assign;
    }
}
