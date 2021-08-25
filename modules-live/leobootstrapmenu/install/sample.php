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

$languages = Language::getLanguages(false);
$mod_group_sample = new BtmegamenuGroup();
$context = Context::getContext();

foreach ($languages as $language) {
    $mod_group_sample->title[$language['id_lang']] = 'Sample Megamenu';
}
$mod_group_sample->hook = 'displayTop';
$mod_group_sample->position = 0;
$mod_group_sample->id_shop = $context->shop->id;
$mod_group_sample->active = 1;
$mod_group_sample->params = 'eyJncm91cF90eXBlIjoiaG9yaXpvbnRhbCIsInNob3dfY2F2YXMiOiIxIiwidHlwZV9zdWIiOiJhdXRvIiwiZ3JvdXBfY2xhc3MiOiIifQ==';
$mod_group_sample->randkey = 'ac70e5b81cccd4671f8c75a464e569bd';
$mod_group_sample->add();

$id_group_sample = $mod_group_sample->id;

for ($i = 1; $i <= 3; ++$i) {
    $mod_menu_sample = new Btmegamenu();
    $mod_menu_sample->id_parent = 0;
    $mod_menu_sample->id_group = $id_group_sample;
    $mod_menu_sample->sub_with = 'submenu';
    $mod_menu_sample->is_group = 0;
    $mod_menu_sample->item = 'index';
    $mod_menu_sample->colums = 1;
    $mod_menu_sample->type = 'controller';
    $mod_menu_sample->is_content = 0;
    $mod_menu_sample->show_title = 1;
    $mod_menu_sample->level_depth = 1;
    $mod_menu_sample->active = 1;
    $mod_menu_sample->position = $i;
    $mod_menu_sample->show_sub = 0;
    $mod_menu_sample->target = '_self';
    $mod_menu_sample->privacy = 0;
    $mod_menu_sample->level = 0;
    $mod_menu_sample->left = 0;
    $mod_menu_sample->right = 0;
    $mod_menu_sample->is_cattree = 1;

    foreach ($languages as $language) {
        $mod_menu_sample->title[$language['id_lang']] = 'Sample menu '.$i;
    }
    $mod_menu_sample->save();
}
