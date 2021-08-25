<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('htmlboxpro.php');


if (Tools::getValue('action') == 'updateSlidesPosition')
{

    $slides = Tools::getValue('elements' . Tools::getValue('hook'));
    foreach ($slides as $position => $id_slide)
    {
        $res = Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'hbp_block` SET `position` = ' . (int)$position . '
			WHERE `id` = ' . (int)$id_slide);
    }
}

