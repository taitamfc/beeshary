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

class ApSlideShow extends ApShortCodeBase
{
    public $name = 'ApSlideShow';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Slide show Module'), 'position' => 3, 'desc' => $this->l('You can get group from leoslideshow module'),
            'icon_class' => 'icon icon-chevron-right', 'tag' => 'content slider');
    }

    public function getConfigList()
    {
        if (Module::isInstalled('leoslideshow') && Module::isEnabled('leoslideshow')) {
            include_once(_PS_MODULE_DIR_.'leoslideshow/leoslideshow.php');
            $module = new LeoSlideshow();
            $list = $module->getAllSlides();
            $controller = 'AdminModules';
            $id_lang = Context::getContext()->language->id;
            $params = array('token' => Tools::getAdminTokenLite($controller),
                'configure' => 'leoslideshow',
                'tab_module' => 'front_office_features',
                'module_name' => 'leoslideshow');
            $url = dirname($_SERVER['PHP_SELF']).'/'.Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
            if ($list && count($list) > 0) {
                $inputs = array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select a group for slideshow'),
                        'name' => 'slideshow_group',
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
                        $this->l('Go to page configuration Slider').'</a></div>'
                    )
                );
            } else {
                // Go to page setting of the module LeoSlideShow
                $inputs = array(
                    array(
                        'type' => 'html',
                        'name' => 'default_html',
                        'html_content' => '<div class="alert alert-warning">'.
                        $this->l('There is no group slide in LeoSlideshow Module.').
                        '</div><br/><div><center><a class="btn btn-primary" href="'.$url.'" target="_blank">'.
                        $this->l(' CREATE GROUP SLIDER').'</a></center></div>'
                    )
                );
            }
        } else {
            $inputs = array(
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-warning">'.
                    $this->l('"LeoSlideshow" Module must be installed and enabled before using.').
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
            $status = ' ('.($item['active'] ? $this->l('Active') : $this->l('Deactive')).')';
            $result[] = array('id' => $item['randkey'], 'name' => $item['title'].$status);
        }
        return $result;
    }

    public function prepareFontContent($assign, $module = null)
    {
        if (Module::isInstalled('leoslideshow') && Module::isEnabled('leoslideshow')) {
            $id_shop = (int)Context::getContext()->shop->id;
            $assign['formAtts']['isEnabled'] = true;
            include_once(_PS_MODULE_DIR_.'leoslideshow/leoslideshow.php');
            $module = new LeoSlideshow();
            $link_array = explode(',', $assign['formAtts']['slideshow_group']);
            if ($link_array && !is_numeric($link_array['0'])) {
                $randkey_group = '';
                foreach ($link_array as $val) {
                    // validate module
                    $randkey_group .= ($randkey_group == '') ? "'".pSQL($val)."'" : ",'".pSQL($val)."'";
                }
                $where = ' WHERE randkey IN ('.$randkey_group.') AND id_shop = ' . (int)$id_shop;
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_leoslideshow_groups FROM `'._DB_PREFIX_.'leoslideshow_groups` '.$where);
                $where = '';
                
                if (is_array($result) && !empty($result)) {
                    foreach ($result as $slide) {
                        // validate module
                        $where .= ($where == '') ? $slide['id_leoslideshow_groups'] : ','.$slide['id_leoslideshow_groups'];
                    }
                    $assign['formAtts']['slideshow_group'] = $where;
                    $assign['content_slider'] = $module->processHookCallBack($assign['formAtts']['slideshow_group']);
                } else {
                    $assign['formAtts']['isEnabled'] = false;
                    $assign['formAtts']['lib_has_error'] = true;
                    $assign['formAtts']['lib_error'] = 'Can not show LeoSlideShow via Appagebuilder. Please check that The Group of LeoSlideShow is exist.';
                }
            }
        } else {
            $assign['formAtts']['isEnabled'] = false;
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show LeoSlideShow via Appagebuilder. Please enable LeoSlideShow module.';
        }
        return $assign;
    }
}
