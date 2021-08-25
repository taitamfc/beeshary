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

class ApMegamenu extends ApShortCodeBase
{
    public $name = 'ApMegamenu';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Megamenu Module'), 'position' => 3, 'desc' => $this->l('You can get group from leobootstrapmenu module'),
            'icon_class' => 'icon icon-chevron-right', 'tag' => 'content');
    }

    public function getConfigList()
    {
        if (Module::isInstalled('leobootstrapmenu') && Module::isEnabled('leobootstrapmenu')) {
            include_once(_PS_MODULE_DIR_.'leobootstrapmenu/leobootstrapmenu.php');
            $module = new Leobootstrapmenu();
            $list = $module->getGroups();
            // $controller = 'AdminModules';
//            $id_lang = Context::getContext()->language->id;
            // $params = array('token' => Tools::getAdminTokenLite($controller),
                // 'configure' => 'Leobootstrapmenu',
                // 'tab_module' => 'front_office_features',
                // 'module_name' => 'Leobootstrapmenu');
            //$url = dirname($_SERVER['PHP_SELF']).'/'.Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
                        $url = Context::getContext()->link->getAdminLink('AdminLeoBootstrapMenuModule');
            if ($list && count($list) > 0) {
                $inputs = array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select a group for megamenu'),
                        'name' => 'megamenu_group',
                        'options' => array(
                            'query' => $this->getListGroup($list),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                        'form_group_class' => 'value_by_categories',
                        'default' => 'all'
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'default_html',
                        'html_content' => '<div class=""><a class="" href="'.$url.'" target="_blank">'.
                        $this->l('Go to page configuration megamenu').'</a></div>'
                    )
                );
            } else {
                // Go to page setting of the module LeoSlideShow
                $inputs = array(
                    array(
                        'type' => 'html',
                        'name' => 'default_html',
                        'html_content' => '<div class="alert alert-warning">'.
                        $this->l('There is no group in Leobootstrapmenu Module.').
                        '</div><br/><div><center><a class="btn btn-primary" href="'.$url.'" target="_blank">'.
                        $this->l(' CREATE GROUP ').'</a></center></div>'
                    )
                );
            }
        } else {
            $inputs = array(
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-warning">'.
                    $this->l('"Leobootstrapmenu" Module must be installed and enabled before using.').
                    '</div><br/><h4><center>You can take this module at leo-theme or apollo-theme</center></h4>'
                )
            );
        }
        return $inputs;
    }

    public function getListGroup($list)
    {
        $result = array();
        foreach ($list as $item) {
            $status = ' (ID: '.$item['id_btmegamenu_group'].' - '.($item['active'] ? $this->l('Active') : $this->l('Deactive')).')';
            $result[] = array('id' => $item['randkey'], 'name' => $item['title'].$status);
        }
        return $result;
    }

    public function prepareFontContent($assign, $module = null)
    {
        if (Module::isInstalled('leobootstrapmenu') && Module::isEnabled('leobootstrapmenu')) {
            $id_shop = (int)Context::getContext()->shop->id;
            $assign['formAtts']['isEnabled'] = true;
            include_once(_PS_MODULE_DIR_.'leobootstrapmenu/leobootstrapmenu.php');
            $module = new Leobootstrapmenu();
            $link_array = explode(',', $assign['formAtts']['megamenu_group']);
            if ($link_array && !is_numeric($link_array['0'])) {
                $randkey_group = '';
                foreach ($link_array as $val) {
                    // validate module
                    $randkey_group .= ($randkey_group == '') ? "'".pSQL($val)."'" : ",'".pSQL($val)."'";
                }

                $where = ' WHERE randkey IN ('.$randkey_group.') AND id_shop = ' . (int)$id_shop;
                $sql = 'SELECT id_btmegamenu_group FROM `'._DB_PREFIX_.'btmegamenu_group` '.$where;
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                
                if (is_array($result) && empty($result)) {
                    $assign['formAtts']['isEnabled'] = false;
                    $assign['formAtts']['lib_has_error'] = true;
                    $assign['formAtts']['lib_error'] = 'Can not show LeoBootstrapMenu via Appagebuilder. Please check that The Group of LeoBootstrapMenu is exist.';
                    return $assign;
                }
                
                $where = '';
                foreach ($result as $blog) {
                    // validate module
                    $where .= ($where == '') ? $blog['id_btmegamenu_group'] : ','.$blog['id_btmegamenu_group'];
                }
                $assign['formAtts']['megamenu_group'] = $where;
            }
            $form_id = explode("_", $assign['formAtts']['form_id']);
            $assign['content_megamenu'] = $module->processHookCallBack($assign['formAtts']['megamenu_group'], $form_id[1]);
        } else {
            // validate module
            $assign['formAtts']['isEnabled'] = false;
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show LeoBootstrapMenu via Appagebuilder. Please enable LeoBootstrapMenu module.';
        }
        return $assign;
    }
}
