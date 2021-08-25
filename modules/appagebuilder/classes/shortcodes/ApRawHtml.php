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

class ApRawHtml extends ApShortCodeBase
{
    public $name = 'ApRawHtml';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Raw Html'), 'position' => 3, 'desc' => $this->l('You can put raw html'),
            'icon_class' => 'icon-html5', 'tag' => 'content structure');
    }

    public function getConfigList()
    {
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
            array(
                'type' => 'text',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
            array(
                'type' => 'textarea',
                'name' => 'content_html',
                'class' => 'ap_html_raw raw-'.time(),
                'rows' => '50',
                'lang' => true,
                'label' => $this->l('Raw html'),
                'values' => '',
                'default' => "<div>\n</div>"
            ),
        );
        return $inputs;
    }
}
