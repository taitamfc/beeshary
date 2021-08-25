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

class ApQuicklogin extends ApShortCodeBase
{
    public $name = 'ApQuicklogin';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Quicklogin Module'), 'position' => 3, 'desc' => $this->l('You set quick login or social login form from leoquicklogin module'),
            'icon_class' => 'icon icon-chevron-right', 'tag' => 'content');
    }

    public function getConfigList()
    {
        if (Module::isInstalled('leoquicklogin') && Module::isEnabled('leoquicklogin')) {
            include_once(_PS_MODULE_DIR_.'leoquicklogin/leoquicklogin.php');
            $module = new Leoquicklogin();
            $select_list_type = array();

            foreach ($module->list_type as $key => $value) {
                    $select_list_type[] = array('id' => $key, 'name' => $value);
            }

            $select_list_layout = array();

            foreach ($module->list_layout as $key => $value) {
                    $select_list_layout[] = array('id' => $key, 'name' => $value);
            }
            $inputs = array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Select type'),
                    'name' => 'quicklogin_type',
                    'options' => array(
                        'query' => $select_list_type,
                        'id' => 'id',
                        'name' => 'name'
                    ),

                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select layout'),
                    'name' => 'quicklogin_layout',
                    'options' => array(
                        'query' => $select_list_layout,
                        'id' => 'id',
                        'name' => 'name'
                    ),

                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Show Social Login'),
                    'name' => 'quicklogin_sociallogin',
                    'options' => array(
                        'query' => array(
                            array(
                                    'id' => 'enable',
                                    'name' => $this->l('Yes'),
                            ),
                            array(
                                    'id' => 'disable',
                                    'name' => $this->l('No'),
                        )),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
            );
        } else {
            $inputs = array(
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-warning">'.
                    $this->l('"Leoquicklogin module" Module must be installed and enabled before using.').
                    '</div><br/><h4><center>You can take this module at leo-theme or apollo-theme</center></h4>'
                )
            );
        }
        return $inputs;
    }

    public function prepareFontContent($assign, $module = null)
    {
        if (Module::isInstalled('leoquicklogin') && Module::isEnabled('leoquicklogin')) {
            $assign['formAtts']['isEnabled'] = true;
            include_once(_PS_MODULE_DIR_.'leoquicklogin/leoquicklogin.php');
            $module = new Leoquicklogin();
            
            $assign['content_quicklogin'] = $module->processHookCallBack($assign['formAtts']['quicklogin_type'], $assign['formAtts']['quicklogin_layout'], $assign['formAtts']['quicklogin_sociallogin']);
        } else {
            // validate module
            $assign['formAtts']['isEnabled'] = false;
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show Leoquicklogin via Appagebuilder. Please enable Leoquicklogin module.';
        }
        return $assign;
    }
}
