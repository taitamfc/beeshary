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

class LGValidate extends ObjectModel
{

    public static function isExcludedDir($dir)
    {
        $img_root_dir = str_replace('/', DIRECTORY_SEPARATOR, _PS_IMG_DIR_);
        $excluded = false;

        if ((bool)Configuration::get('LG_EXCLUDE_PRODUCTS')) {
            $excluded = (!$excluded) ?
                strrpos($dir, $img_root_dir . 'p' . DIRECTORY_SEPARATOR) !== false : true;
        }
        if (((bool)Configuration::get('LG_EXCLUDE_CATEGORIES'))) {
            $excluded = (!$excluded) ?
                strrpos($dir, $img_root_dir . 'c' . DIRECTORY_SEPARATOR) !== false : true;
        }
        if ((bool)Configuration::get('LG_EXCLUDE_SUPPLIERS')) {
            $excluded = (!$excluded) ?
                strrpos($dir, $img_root_dir . 'su' . DIRECTORY_SEPARATOR) !== false : true;
        }
        if ((bool)Configuration::get('LG_EXCLUDE_TMP')) {
            $excluded = (!$excluded) ?
                strrpos($dir, $img_root_dir . 'tmp' . DIRECTORY_SEPARATOR) !== false : true;
        }
        return $excluded;
    }

    public static function isImage($path)
    {
        // Usamos @ para evitar errores con archivos que no son realmente imagenes o directorios vacíos
        if ($a = @getimagesize($path)) {
            $image_type = $a[2];
            return in_array($image_type, array(IMAGETYPE_JPEG, IMAGETYPE_PNG));
        }
        return false;
    }
}
