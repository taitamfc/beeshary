<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

function upgrade_module_1_1_0()
{

    Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgrequality_stack` (
        `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
        `path` VARCHAR(255) NOT NULL,
        `procesado` TINYINT(1) NOT NULL,
        `escaneado` TINYINT(1) NOT NULL
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
    if (!Db::getInstance()->ExecuteS(
        'SHOW COLUMNS '.
        'FROM '._DB_PREFIX_.'lgrequality_stack '.
        'LIKE "escaneado"'
    )) {
        Db::getInstance()->Execute(
            'ALTER TABLE `'._DB_PREFIX_.'lgrequality_stack` '.
            'ADD `escaneado` TINYINT(1) NOT NULL DEFAULT "0" AFTER `path`'
        );
    }
    Db::getInstance()->execute(
        'TRUNCATE TABLE `' . _DB_PREFIX_ . 'lgrequality`'
    );
    Db::getInstance()->execute(
        'TRUNCATE TABLE `' . _DB_PREFIX_ . 'lgrequality_stack`'
    );
    return true;
}
