<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Bootstrap Menu
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

include_once('../../config/config.inc.php');
include_once('../../init.php');
require_once(_PS_MODULE_DIR_.'leobootstrapmenu/leobootstrapmenu.php');
$context = Context::getContext();
$module = new leobootstrapmenu();

if (Tools::getIsset('allWidgets')) {
    $dataForm = Tools::jsonDecode(Tools::getValue('dataForm'), 1);
    foreach ($dataForm as $key => &$widget) {
        $widget['html'] = $module->renderLeoWidget($widget['id_shop'], $widget['id_widget']);
    }
    die(Tools::jsonEncode($dataForm));
}

$id_shop = Tools::getValue('id_shop') ? Tools::getValue('id_shop') : 0;
$id_widget = Tools::getValue('widgets');
echo $module->renderLeoWidget($id_shop, $id_widget);
die;
