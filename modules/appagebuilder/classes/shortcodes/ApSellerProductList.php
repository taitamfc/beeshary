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

class ApSellerProductList extends ApShortCodeBase
{
    public $name = 'ApSellerProductList';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Ap Seller Product List'),
            'position' => 5,
            'desc' => $this->l('Ap Seller Product List'),
            'icon_class' => 'icon-info-sign',
            'tag' => 'content');
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
            )
        );
        return $inputs;
    }
}
