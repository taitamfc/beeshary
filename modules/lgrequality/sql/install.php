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

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgrequality` (
            `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `path` VARCHAR(255) NOT NULL,
            `file` VARCHAR(255) NOT NULL,
            `ext` VARCHAR(32) NOT NULL,
            `size_o` INT(32) NOT NULL,
            `size_c` INT(32) NOT NULL,
            `date` VARCHAR(32) NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgrequality_stack` (
            `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `path` VARCHAR(255) NOT NULL,
            `procesado` TINYINT(1) NOT NULL,
            `escaneado` TINYINT(1) NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
