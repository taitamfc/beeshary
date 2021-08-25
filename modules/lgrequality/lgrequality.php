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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'classes'.DIRECTORY_SEPARATOR.'LGRequalityStack.php';

class Lgrequality extends Module
{
    protected $directories_structure = '';

    public function __construct()
    {
        $this->name = 'lgrequality';
        $this->tab = 'quick_bulk_update';
        $this->version = '1.1.2';
        $this->author = 'Línea Gráfica';
        $this->need_instance = 1;
        $this->module_key = 'd771caeab6d06b31049adeea0f065cbd';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Image Compressor – Improve your SEO');
        $this->description = $this->l('Easily compress your images to increase your shop’s speed');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install()
            && $this->registerHook('backOfficeHeader')
            && Configuration::updateValue('LG_REQUALITY_JPG', 70)
            && Configuration::updateValue('LG_REQUALITY_PNG', 7)
            && Configuration::updateValue('LG_EXCLUDE_PRODUCTS', 0)
            && Configuration::updateValue('LG_EXCLUDE_CATEGORIES', 0)
            && Configuration::updateValue('LG_EXCLUDE_SUPPLIERS', 0)
            && Configuration::updateValue('LG_EXCLUDE_TMPS', 0)
            && Configuration::updateValue('LG_DIRECTORY_SCAN', _PS_ROOT_DIR_);
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall()
            && Configuration::deleteByName('LG_REQUALITY_JPG')
            && Configuration::deleteByName('LG_REQUALITY_PNG')
            && Configuration::deleteByName('LG_EXCLUDE_PRODUCTS')
            && Configuration::deleteByName('LG_EXCLUDE_CATEGORIES')
            && Configuration::deleteByName('LG_EXCLUDE_SUPPLIERS')
            && Configuration::deleteByName('LG_EXCLUDE_TMPS')
            && Configuration::deleteByName('LG_DIRECTORY_SCAN');
    }

    public function hookBackOfficeHeader()
    {
        if (pSQL(Tools::getValue('configure')) == $this->name) {
            $this->context->controller->addJQuery();
            $this->context->controller->addJS($this->_path . 'views/js/riot_compiler.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/jquery.lgspinner.js');
            $this->context->controller->addJS($this->_path . 'views/js/lgrequality.js');
            $this->context->controller->addCSS($this->_path . 'views/css/lgrequality.css');
            $this->context->controller->addCSS($this->_path . '/views/css/publi/lgpubli.css');
        }
    }

    public function getContent()
    {
        if (Tools::getValue('ajax')) {
            switch (Tools::getValue('action')) {
                case 'dirs':
                    $this->ajaxProcessDirs();
                    break;
                case 'scan':
                    $this->ajaxProcessScan();
                    break;
                case 'requality':
                    $this->ajaxProcessRequality();
                    break;
                case 'recover':
                    $this->ajaxProcessRecover();
                    break;
            }
        }
        $output = '';
        $ttscan = $ttreq = $ttrec = 0;

        if (Tools::isSubmit('lgrequality_save')) {
            if ($this->saveConfiguration()) {
                $output = $this->displayConfirmation($this->l('Configuration saved successfully'));
            } else {
                $output = $this->displayError($this->l('Has been an error while trying saving configuration'));
            }
        }
        $size_o = LGRequalityStack::sizeOrigin();
        $size_c = LGRequalityStack::sizeCompressed();

        if ($size_c > 0 && $size_o > $size_c) {
            $compression_percentage = round(($size_c * 100) / $size_o, 2);
        } else {
            $compression_percentage = 100;
        }
        $compression_diff = round(100 - $compression_percentage, 2);

        $has_data = (bool)Db::getInstance()->getValue(
            'SELECT count(*) 
            FROM `' . _DB_PREFIX_ . 'lgrequality`'
        );
        $has_processed = (bool)Db::getInstance()->getValue(
            'SELECT count(*) 
            FROM `' . _DB_PREFIX_ . 'lgrequality` 
            WHERE `size_c` > 0'
        );
        $directories = LGRequalityStack::countDirs();
        $images = LGRequalityStack::countImages();
        $this->context->smarty->assign(array(
            'has_data' => $has_data,
            'has_processed' => $has_processed,
            'reduction_jpg' => (int)Configuration::get('LG_REQUALITY_JPG'),
            'reduction_png' => (int)Configuration::get('LG_REQUALITY_PNG'),
            'exclude_products' => (int)Configuration::get('LG_EXCLUDE_PRODUCTS'),
            'exclude_categories' => (int)Configuration::get('LG_EXCLUDE_CATEGORIES'),
            'exclude_suppliers' => (int)Configuration::get('LG_EXCLUDE_SUPPLIERS'),
            'exclude_tmp' => (int)Configuration::get('LG_EXCLUDE_TMP'),
            'time_out' => (int)ini_get('max_execution_time'),
            'ttscan' => (int)$ttscan,
            'ttreq' => (int)$ttreq,
            'ttrec' => (int)$ttrec,
            'size_o' => $size_o,
            'size_c' => $size_c > 0 ? $size_c : $size_o,
            'compression_percentage' => $compression_percentage,
            'compression_diff' => $compression_diff,
            'directories' => (int)$directories,
            'images' => (int)$images,
            'base_url' => _MODULE_DIR_. $this->name . DIRECTORY_SEPARATOR,
            'lgrequality_token' => Tools::getAdminTokenLite('AdminModules'),
        ));

        $output .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name
            . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . 'templates'
            . DIRECTORY_SEPARATOR . 'admin'
            . DIRECTORY_SEPARATOR . 'configure.tpl'
        );

        return $this->getP('top') .
            $output .
            $this->display(__FILE__, 'views/templates/admin/prestui/ps-tags.tpl') .
            $this->getP('bottom');
    }

    private function getP($template)
    {
        $iso_langs = array('es', 'en', 'fr');
        $current_iso_lang = $this->context->language->iso_code;
        $iso = (in_array($current_iso_lang, $iso_langs)) ? $current_iso_lang : 'en';

        $this->context->smarty->assign(
            array(
                'iso' => $iso,
                'base_url' => _MODULE_DIR_. $this->name . DIRECTORY_SEPARATOR,
            )
        );

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name
            . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . 'templates'
            . DIRECTORY_SEPARATOR . 'admin'
            . DIRECTORY_SEPARATOR . '_p_' . $template . '.tpl'
        );
    }

    protected function saveConfiguration()
    {
        $reduction_jpg = (int)Tools::getValue('reduction_jpg', 70);
        $reduction_jpg = $reduction_jpg > 100 ? 100 : $reduction_jpg;
        $reduction_jpg = $reduction_jpg < 1 ? 1 : $reduction_jpg;

        $reduction_png = (int)Tools::getValue('reduction_png', 7);
        $reduction_png = $reduction_png > 9 ? 9 : $reduction_png;
        $reduction_png = $reduction_png < 1 ? 1 : $reduction_png;

        return
            Configuration::updateValue(
                'LG_REQUALITY_JPG',
                $reduction_jpg
            )
            && Configuration::updateValue(
                'LG_REQUALITY_PNG',
                $reduction_png
            )
            && Configuration::updateValue(
                'LG_EXCLUDE_PRODUCTS',
                pSQL(Tools::getValue('exclude_products', 0)[0])
            )
            && Configuration::updateValue(
                'LG_EXCLUDE_CATEGORIES',
                pSQL(Tools::getValue('exclude_categories', 0)[0])
            )
            && Configuration::updateValue(
                'LG_EXCLUDE_SUPPLIERS',
                pSQL(Tools::getValue('exclude_suppliers', 0)[0])
            )
            && Configuration::updateValue(
                'LG_EXCLUDE_TMP',
                pSQL(Tools::getValue('exclude_tmp', 0)[0])
            )
            && Configuration::updateValue(
                'LG_DIRECTORY_SCAN',
                pSQL(Tools::getValue('directory_scan', _PS_ROOT_DIR_))
            );
    }

    /***************************************************************************************************************/
    /*                                                                                                             */
    /*                                                  Ajax Calls                                                 */
    /*                                                                                                             */
    /***************************************************************************************************************/
    public function ajaxProcessDirs()
    {
        if (LGRequalityStack::evalTruncate()) {
            $dirs = LGRequalityStack::scanDirs(_PS_ROOT_DIR_);
            LGRequalityStack::insertDirs($dirs);
            die('{"directories":'.(int)count($dirs).',"finish":false}');
        } else {
            $dirs = LGRequalityStack::getDirs();
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'lgrequality_stack` SET `escaneado` = 1 '.
                'WHERE `escaneado` = 0'
            );
            if (empty($dirs)) {
                die('{"directories":' . LGRequalityStack::countDirs() . ',"finish":true}');
            }
            $total = 0;
            foreach ($dirs as $dir) {
                $dirs_new = LGRequalityStack::scanDirs($dir);
                LGRequalityStack::insertDirs($dirs_new);
                $total+= count($dirs_new);
            }
            die('{"directories":'.LGRequalityStack::countDirs().',"finish":'.($total>0?'false':'true').'}');
        }
    }

    public function ajaxProcessScan()
    {
        if (!$rows = Db::getInstance()->executeS(
            'SELECT `id`,`path` FROM `' . _DB_PREFIX_ . 'lgrequality_stack` '.
            'WHERE `procesado` = 0 LIMIT 300'
        )) {
            $sql = 'TRUNCATE TABLE `' . _DB_PREFIX_ . 'lgrequality`';
            Db::getInstance()->execute($sql);
            $sql = 'TRUNCATE TABLE `' . _DB_PREFIX_ . 'lgrequality_stack`';
            Db::getInstance()->execute($sql);
            $dirs = LGRequalityStack::scanDirs(_PS_ROOT_DIR_);
            LGRequalityStack::insertDirs($dirs);
        }
        $ids = array();
        $total  = LGRequalityStack::countDirs();
        $images = array();
        foreach ($rows as $row) {
            $ids[] = $row['id'];
            $images = array_merge($images, LGRequalityStack::scanDirImages($row['path']));
        }
        LGRequalityStack::insertImages($images);
        if (!empty($ids)) {
            Db::getInstance()->execute(
                'UPDATE ' . _DB_PREFIX_ . 'lgrequality_stack ' .
                'SET procesado = 1 WHERE id IN(' . implode(',', $ids) . ')'
            );
        }
        $ok = Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'lgrequality_stack '.
            'WHERE procesado = 1'
        );
        $percent = ($ok*100)/$total;
        if (100>$percent) {
            $percent = number_format($percent, 2, '.', '');
        } else {
            $percent = (int)$percent;
        }
        $size   = LGRequalityStack::size();
        $images = LGRequalityStack::countImages();
        die('{"size":'.$size.',"percent":'.$percent.',"images":'.$images.'}');
    }

    public function ajaxProcessRequality()
    {
        if (Tools::getValue('force')) {
            Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'lgrequality SET size_c = 0');
        }
        if (!$rows = Db::getInstance()->executeS(
            'SELECT `id`, CONCAT(`path`,"'.pSQL(DIRECTORY_SEPARATOR).'",`file`,".",`ext`) AS `path` '.
            'FROM ' . _DB_PREFIX_ . 'lgrequality r '.
            'WHERE r.`size_o` > 0 AND r.`size_c` = 0 LIMIT 300'
        )) {
            $size = LGRequalityStack::sizeCompressed();
            die('{"size":'.$size.',"percent":100}');
        } else {
            $total = LGRequalityStack::countImages();
            LGRequalityStack::compressImages($rows);
            $ok = Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'lgrequality` '.
                'WHERE `size_c` > 0'
            );
            $percent = number_format(($ok*100)/$total, 2, '.', '');
            $size = LGRequalityStack::sizeCompressed();
            die('{"size":'.$size.',"percent":'.$percent.'}');
        }
    }

    public function ajaxProcessRecover()
    {
        if (!$rows = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'lgrequality WHERE size_c > 0 LIMIT 300'
        )) {
            Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'lgrequality');
            Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'lgrequality_stack');
            die('{"size":0,"percent":100}');
        } else {
            $ids = array();
            $total = LGRequalityStack::countImages();
            foreach ($rows as $row) {
                $ids[] = $row['id'];
                $file = $row['file'].'.'.$row['ext'];
                if (file_exists($row['path'].DIRECTORY_SEPARATOR.$file)
                    && file_exists($row['path'].DIRECTORY_SEPARATOR.$file.'_lgbk')
                ) {
                    if (unlink($row['path'].DIRECTORY_SEPARATOR.$file)) {
                        rename($row['path'].DIRECTORY_SEPARATOR.$file.'_lgbk', $row['path'].DIRECTORY_SEPARATOR.$file);
                    }
                }
            }
            if (!empty($ids)) {
                Db::getInstance()->execute(
                    'UPDATE '._DB_PREFIX_.'lgrequality '.
                    'SET size_c = 0 WHERE id IN('.implode(',', $ids).')'
                );
            }
            $ok = Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `'._DB_PREFIX_.'lgrequality` '.
                'WHERE `size_c` = 0'
            );
            if ($ok == $total) {
                Db::getInstance()->execute('TRUNCATE TABLE '. _DB_PREFIX_.'lgrequality');
                Db::getInstance()->execute('TRUNCATE TABLE '. _DB_PREFIX_.'lgrequality_stack');
            }
            $percent = (int)(($ok*100)/$total);
            die('{"size":0,"percent":'.$percent.'}');
        }
    }
}
