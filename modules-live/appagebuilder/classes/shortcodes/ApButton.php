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

class ApButton extends ApShortCodeBase
{
    public $name = 'ApButton';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Buttons'),
            'position' => 5,
            'desc' => $this->l('Custom color, size and create a link for button'),
            'icon_class' => 'icon-edit',
            'tag' => 'content control');
    }

    public function getConfigList()
    {
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'desc' => $this->l('Auto hide if leave it blank'),
                'lang' => 'true',
                'form_group_class' => 'aprow_general',
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
                'type' => 'text',
                'name' => 'name',
                'label' => $this->l('Name'),
                'lang' => 'true',
                'form_group_class' => 'aprow_general',
                'default' => 'Button'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Use Outline Button'),
                'class' => 'form-action',
                'name' => 'use_outline_button',
                'options' => array(
                    'query' => array(
                        array('id' => 'no', 'name' => $this->l('No')),
                        array('id' => 'yes', 'name' => $this->l('Yes')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => '0'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Button Type'),
                'name' => 'button_type',
                'options' => array('query' => $this->getDataButtonType(),
                    'id' => 'value',
                    'name' => 'text'),
                'default' => '1',
                'form_group_class' => 'use_outline_button_sub use_outline_button-no',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Button Type'),
                'name' => 'outline_button_type',
                'options' => array('query' => $this->getDataOutlineButtonType(),
                    'id' => 'value',
                    'name' => 'text'),
                'default' => '1',
                'form_group_class' => 'use_outline_button_sub use_outline_button-yes',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Button Size'),
                'name' => 'button_size',
                'options' => array('query' => $this->getDataSize(),
                    'id' => 'value',
                    'name' => 'text'),
                'default' => 'btn-lg',
                'desc' => $this->l('Select a button size')
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Is Block'),
                'name' => 'is_block',
                'desc' => $this->l('If is block, will display width is full 100%'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0'
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<hr/>'
            ),
            array(
                'type' => 'text',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => $this->l('You can add code in ').'<a href="'.self::getUrlProfileEdit().'" target="_blank">here</a>'
            ),
            array(
                'type' => 'text',
                'name' => 'url',
                'label' => $this->l('Url'),
                'desc' => $this->l('Exmaple: http://prestashop.com'),
                'lang' => 'true',
                'default' => ''
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Open new window'),
                'name' => 'is_blank',
                'desc' => $this->l('If is Yes, will open new tab with url when click'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0'
            ),
        );
        return $inputs;
    }
    
    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        
        if ($assign['formAtts']['use_outline_button'] == 'yes') {
            $assign['formAtts']['button_type'] = $assign['formAtts']['outline_button_type'];
        }
        return $assign;
    }
    
    
    
    
    private function getDataButtonType()
    {
        $data = array(
            array(
                'value' => 'btn-primary',
                'text' => $this->l('Button Primary')
            ),
            array(
                'value' => 'btn-secondary',
                'text' => $this->l('Button Secondary')
            ),
            array(
                'value' => 'btn-success',
                'text' => $this->l('Button Success')
            ),
            array(
                'value' => 'btn-info',
                'text' => $this->l('Button Info')
            ),
            array(
                'value' => 'btn-warning',
                'text' => $this->l('Button Warning')
            ),
            array(
                'value' => 'btn-danger',
                'text' => $this->l('Button Danger')
            ),
            array(
                'value' => 'btn-link',
                'text' => $this->l('Button Link')
            )
        );
        
        return $data;
    }
    
    private function getDataOutlineButtonType()
    {
        $data = array(
            array(
                'value' => 'btn-outline-primary',
                'text' => $this->l('Button Outline Primary')
            ),
            array(
                'value' => 'btn-outline-secondary',
                'text' => $this->l('Button Outline Secondary')
            ),
            array(
                'value' => 'btn-outline-success',
                'text' => $this->l('Button Outline Success')
            ),
            array(
                'value' => 'btn-outline-info',
                'text' => $this->l('Button Outline Info')
            ),
            array(
                'value' => 'btn-outline-warning',
                'text' => $this->l('Button Outline Warning')
            ),
            array(
                'value' => 'btn-outline-danger',
                'text' => $this->l('Button Outline Danger')
            ),
        );
        
        return $data;
    }
    
    private function getDataSize()
    {
        $data = array(
            array(
                'value' => 'btn-lg',
                'text' => $this->l('Size Large')
            ),
            array(
                'value' => 'btn-sm',
                'text' => $this->l('Size Small')
            ),
        );
                
        return $data;
    }
}
