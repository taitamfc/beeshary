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

class ApHtml extends ApShortCodeBase
{
    public $name = 'ApHtml';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Html'), 'position' => 3, 'desc' => $this->l('You can put html'),
            'icon_class' => 'icon-html5', 'tag' => 'content structure');
    }

    public function getConfigList()
    {
        $accordion_type = array(
            array(
                'value' => 'full',
                'text' => $this->l('Always Full')
            ),
            array(
                'value' => 'accordion',
                'text' => $this->l('Always Accordion')
            ),
            array(
                'value' => 'accordion_small_screen',
                'text' => $this->l('Accordion at small screen')
            ),
        );
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
                'autoload_rte' => false,
                'values' => '',
                'class' => 'sub_title',
                'default' => '',
            ),
            array(
                'type' => 'text',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
            array(
                'type'       => 'select',
                'label'   => $this->l('Accordion Type'),
                'name'       => 'accordion_type',
                'options' => array(
                    'query' => $accordion_type,
                    'id'       => 'value',
                    'name'       => 'text' ),
                'default' => 'full',
                'hint'    => $this->l('Select a Accordion Type'),
            ),
            array(
                'type' => 'textarea',
                'name' => 'content_html',
                'class' => 'ap_html',
                'rows' => '50',
                'lang' => true,
                'label' => $this->l('Html'),
                'values' => '',
                'autoload_rte' => true,
                'default' => "<div>\n</div>"
            ),
        );
        return $inputs;
    }
}
