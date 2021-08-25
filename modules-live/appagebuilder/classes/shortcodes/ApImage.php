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

class ApImage extends ApShortCodeBase
{
    public $name = 'ApImage';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Image'),
            'position' => 5,
            'desc' => $this->l('Single Image'),
            'icon_class' => 'icon-image',
            'tag' => 'content');
    }

    public function getConfigList()
    {
        Context::getContext()->smarty->assign('path_image', apPageHelper::getImgThemeUrl());
        $href = Context::getContext()->link->getAdminLink('AdminApPageBuilderImages').'&ajax=1&action=manageimage&imgDir=images';
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
                'type' => 'select',
                'label' => $this->l('Animations'),
                'name' => 'animation',
                'class' => 'animation-select',
                'options' => array(
                    'optiongroup' => array(
                        'label' => 'name',
                        'query' => ApPageSetting::getAnimations(),
                    ),
                    'options' => array(
                        'id' => 'id',
                        'name' => 'name',
                        'query' => 'query',
                    ),
                ),
                'form_group_class' => 'apimage_animation',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div id="animationSandbox">Prestashop.com</div>',
                'form_group_class' => 'apimage_animation animate_sub',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Delay'),
                'name' => 'animation_delay',
                'default' => '0.5',
                'suffix' => 's',
                'class' => 'fixed-width-xs',
                'form_group_class' => 'apimage_animation animate_sub',
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
                'type' => 'textarea',
                'label' => $this->l('Description'),
                'name' => 'description',
                'cols' => 40,
                'rows' => 10,
                'value' => true,
                'lang' => true,
                'default' => '',
                'autoload_rte' => true,
            )
        );
        return $inputs;
    }
    
    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }
    
    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        $assign['path'] = apPageHelper::getImgThemeUrl();

        if (!isset($assign['formAtts']['animation']) || $assign['formAtts']['animation'] == 'none') {
            $assign['formAtts']['animation'] = 'none';
            $assign['formAtts']['animation_delay'] = '';
        } elseif ($assign['formAtts']['animation'] != 'none' && (int)$assign['formAtts']['animation_delay'] > 0) {
            // validate module
            $assign['formAtts']['animation_delay'] .= 's';
        } elseif ($assign['formAtts']['animation'] != 'none' && (int)$assign['formAtts']['animation_delay'] <= 0) {
            // Default delay
            $assign['formAtts']['animation_delay'] = '1s';
        }
        if (isset($assign['formAtts']['image'])) {
            $assign['formAtts']['image'] = trim($assign['formAtts']['image']);
        }
        return $assign;
    }
}
