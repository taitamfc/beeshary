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

class LGRequalityStack extends ObjectModel
{
    public $path      = '';
    public $procesado = false;
    public $escaneado = false;
    public static $enabled  = array(
        'img',
        'modules',
        'override',
        'themes',
        'upload',
    );
    public static $extensions = array('jpg', 'jpeg', 'png');

    public static $definition = array(
        'table'     => 'lgrequality_stack',
        'primary'   => 'id',
        'multilang' => false,
        'fields'    => array(
            'path'      => array('type' => self::TYPE_STRING, 'size' => 255),
            'procesado' => array('type' => self::TYPE_BOOL),
            'escaneado' => array('type' => self::TYPE_BOOL),
        )
    );

    public static function evalTruncate()
    {
        if (Tools::getValue('force') || !self::left()) {
            Db::getInstance()->execute(
                'TRUNCATE TABLE `' . _DB_PREFIX_ . 'lgrequality`'
            );
            Db::getInstance()->execute(
                'TRUNCATE TABLE `' . _DB_PREFIX_ . 'lgrequality_stack`'
            );
            return true;
        }
        return false;
    }

    public static function left()
    {
        $sql = 'SELECT 1 FROM `' . _DB_PREFIX_ . 'lgrequality_stack` WHERE `escaneado` = 0';
        return DB::getInstance()->getValue($sql);
    }

    public static function scanDirs($path = false)
    {
        $dirs = array();
        $dirs_listed = glob($path . '/*', GLOB_ONLYDIR);
        foreach ($dirs_listed as $dir) {
            if (self::isExcludedDir($dir)) {
                continue;
            }
            $dirs[] = $dir;
        }
        return $dirs;
    }

    public static function scanDirImages($path = false)
    {
        $ext = implode(',', self::$extensions);
        return glob($path.DIRECTORY_SEPARATOR.'*.{'.$ext.','.Tools::strtoupper($ext).'}', GLOB_BRACE);
    }

    public static function insertDirs($dirs = array(), $blocks = 100)
    {
        if (empty($dirs)) {
            return false;
        }
        $total = count($dirs);
        $count = 0;
        $sql = '';
        foreach ($dirs as $k => $dir) {
            unset($dirs[$k]);
            $count++;
            $sql.= '("'.pSQL($dir).'",0,0),';
            if ($count == $total || $count == $blocks) {
                Db::getInstance()->execute(
                    'INSERT INTO `' . _DB_PREFIX_ . 'lgrequality_stack` (`path`,`procesado`,`escaneado`) VALUES ' .
                    rtrim($sql, ',').';'
                );
            }
        }
        if (count($dirs) > 0) {
            return self::insertDirs($dirs);
        }
        return true;
    }

    public static function insertImages($images = array(), $blocks = 100)
    {
        $total = count($images);
        $count = 0;
        $sql = '';
        foreach ($images as $k => $image) {
            unset($images[$k]);
            $count++;
            $ext = Tools::substr($image, strrpos($image, '.') + 1);
            $file = str_replace('.'.$ext, '', basename($image));
            if (file_exists($image . '_lgbk')) {
                $date = gmdate(
                    'd-m-Y H:i:s',
                    filemtime($image . '_lgbk')
                );
                $size_o = filesize($image . '_lgbk');
                $size_c = filesize($image);
            } else {
                $date = '';
                $size_o = filesize($image);
                $size_c = 0;
            }
            $sql.= '(
                    "' . pSQL(dirname($image)) . '",
                    "' . pSQL($file) . '",
                    "' . pSQL($ext) . '",
                    "' . pSQL($size_o) . '",
                    "' . pSQL($size_c) . '",
                    "' . pSQL($date) . '"
                ),';
            if ($count == $total || $count == $blocks) {
                Db::getInstance()->execute(
                    'INSERT INTO `' . _DB_PREFIX_ . 'lgrequality` ' .
                    '(`path`,`file`,`ext`,`size_o`,`size_c`,`date`) VALUES ' .
                    rtrim($sql, ',') . ';'
                );
            }
        }
        if (!empty($images)) {
            return self::insertImages($images);
        }
        return true;
    }

    public static function getDirs()
    {
        $sql = 'SELECT `path` FROM `' . _DB_PREFIX_ . 'lgrequality_stack` WHERE `escaneado` = 0';
        if (!$rows = Db::getInstance()->executeS($sql)) {
            return array();
        }
        $data = array();
        foreach ($rows as $row) {
            $data[] = $row['path'];
        }
        return $data;
    }

    public static function countDirs()
    {
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'lgrequality_stack`';
        return (int)DB::getInstance()->getValue($sql);
    }

    public static function countImages()
    {
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'lgrequality`';
        return (int)DB::getInstance()->getValue($sql);
    }

    public static function size($processed = false)
    {
        $sql = 'SELECT DISTINCT SUM(size_o) FROM ' . _DB_PREFIX_ . 'lgrequality';
        if ($processed) {
            $sql.= ' WHERE date != ""';
        }
        $size = Db::getInstance()->getValue($sql);
        return number_format((($size/1024)/1024), 2, '.', '');
    }

    public static function sizeOrigin()
    {
        $sql = 'SELECT DISTINCT SUM(size_o) FROM ' . _DB_PREFIX_ . 'lgrequality';
        $size = Db::getInstance()->getValue($sql);
        return number_format((($size/1024)/1024), 2, '.', '');
    }

    public static function sizeCompressed()
    {
        $sql = 'SELECT DISTINCT SUM(size_c) FROM ' . _DB_PREFIX_ . 'lgrequality';
        $size = Db::getInstance()->getValue($sql);
        return number_format((($size/1024)/1024), 2, '.', '');
    }

    public static function isExcludedDir($dir)
    {
        $dir = preg_replace('/([\\|\/]+)/', DIRECTORY_SEPARATOR, $dir);
        $img_root_dir = str_replace('/', DIRECTORY_SEPARATOR, _PS_IMG_DIR_);
        $excluded = $is_enabled = false;
        if (Tools::substr(basename($dir), 0, 1)=='.') {
            return true;
        }
        if (!self::scanDirImages($dir) && !glob($dir.'/*', GLOB_ONLYDIR)) {
            return true;
        }
        if (!empty($dir)) {
            foreach (self::$enabled as $enabled) {
                $main_path = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $enabled;
                $lenght = Tools::strlen($main_path);
                if ($dir == $main_path || Tools::substr($dir, 0, $lenght+1) == $main_path.DIRECTORY_SEPARATOR) {
                    $is_enabled = true;
                    break;
                }
            }
        }
        if (!$is_enabled) {
            return true;
        }
        if ((bool)Configuration::get('LG_EXCLUDE_PRODUCTS')) {
            $excluded = (!$excluded) ?
                $dir == $img_root_dir . 'p' ||
                strrpos($dir, $img_root_dir . 'p' . DIRECTORY_SEPARATOR) !== false : true;
        }
        if (((bool)Configuration::get('LG_EXCLUDE_CATEGORIES'))) {
            $excluded = (!$excluded) ?
                $dir == $img_root_dir . 'c' ||
                strrpos($dir, $img_root_dir . 'c' . DIRECTORY_SEPARATOR) !== false : true;
        }
        if ((bool)Configuration::get('LG_EXCLUDE_SUPPLIERS')) {
            $excluded = (!$excluded) ?
                $dir == $img_root_dir . 'su' ||
                strrpos($dir, $img_root_dir . 'su' . DIRECTORY_SEPARATOR) !== false : true;
        }
        if ((bool)Configuration::get('LG_EXCLUDE_TMP')) {
            $excluded = (!$excluded) ?
                $dir == $img_root_dir . 'tmp' ||
                strrpos($dir, $img_root_dir . 'tmp' . DIRECTORY_SEPARATOR) !== false : true;
        }
        return $excluded;
    }

    public static function compressImages($images)
    {
        $date = date('d-m-Y H:i:s');
        $sql = '';
        foreach ($images as $image_row) {
            $image_path = $image_row['path'];
            if (!file_exists($image_path)) {
                $size = (int)Db::getInstance()->getValue(
                    'SELECT `size_o` FROM `' . _DB_PREFIX_ . 'lgrequality` '.
                    'WHERE `id` ='.(int)$image_row['id']
                );
                $sql .= '(' . $image_row['id'] . ',' . $size . ',"' . $date . '"),';
                continue;
            } elseif (!ImageManager::checkImageMemoryLimit($image_path)) {
                $sql .= '(' . $image_row['id'] . ',' . filesize($image_path) . ',"' . $date . '"),';
                continue;
            } else {
                // Si existe su correspondiente backup se usara para comprimir de nuevo la imagen
                if (file_exists($image_path . '_lgbk')) {
                    copy($image_path . '_lgbk', $image_path);
                }
                $info = getimagesize($image_path);
                if ($info['mime'] == 'image/jpeg') {
                    $image = imagecreatefromjpeg($image_path);
                } elseif ($info['mime'] == 'image/png') {
                    $image = imagecreatefrompng($image_path);
                    imageAlphaBlending($image, true);
                    imageSaveAlpha($image, true);
                } else {
                    $sql .= '(' . $image_row['id'] . ',' . filesize($image_path) . ',"' . $date . '"),';
                    continue;
                }

                if ($image) {
                    //backup origin file
                    if (!file_exists($image_path . '_lgbk')) {
                        copy($image_path, $image_path . '_lgbk');
                    }
                    //save file
                    if ($info['mime'] == 'image/jpeg') {
                        imagejpeg($image, $image_path, (int)Configuration::get('LG_REQUALITY_JPG'));
                    }
                    if ($info['mime'] == 'image/png') {
                        $ext = @Tools::strtolower(array_pop(explode('.', $image_path)));
                        if (in_array($ext, array('jpg', 'jpeg'))) {
                            imagejpeg($image, $image_path, (int)Configuration::get('LG_REQUALITY_JPG'));
                        } else {
                            imagepng($image, $image_path, (int)Configuration::get('LG_REQUALITY_PNG'));
                        }
                    }
                    // Si el tamaño del fichero comprimido es mayor que el original, se restaura el original
                    if (filesize($image_path) >= filesize($image_path . '_lgbk')) {
                        copy($image_path . '_lgbk', $image_path);
                    }
                    $size_c = filesize($image_path);
                    $sql .= '(' . $image_row['id'] . ',' . $size_c . ',"' . $date . '"),';
                }
            }
            unset($image_path, $image, $size_c);
        }
        if ($sql != '') {
            Db::getInstance()->execute(
                'INSERT INTO ' . _DB_PREFIX_ . 'lgrequality (`id`,`size_c`,`date`) VALUES '.
                rtrim($sql, ',').
                'ON DUPLICATE KEY UPDATE `size_c` = VALUES(`size_c`),`date` = VALUES(`date`) '
            );
        }
    }
}
