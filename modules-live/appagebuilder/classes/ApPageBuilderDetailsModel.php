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

require_once(_PS_MODULE_DIR_.'appagebuilder/classes/shortcodes.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageSetting.php');

class ApPageBuilderDetailsModel extends ObjectModel
{
    public $name;
    public $params;
    public $type;
    public $active;
    public $plist_key;
    public $class_detail;
    public $url_img_preview;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'appagebuilder_details',
        'primary' => 'id_appagebuilder_details',
        'multilang' => false,
        'multishop' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'plist_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'type' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'params' => array('type' => self::TYPE_HTML),
            'class_detail' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'url_img_preview' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
        )
    );

    public function getAllProductProfileByShop()
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $where = ' WHERE id_shop='.(int)$id_shop;
        $sql = 'SELECT p.*, ps.*
                 FROM '._DB_PREFIX_.'appagebuilder_details p
                 INNER JOIN '._DB_PREFIX_.'appagebuilder_details_shop ps ON (ps.id_appagebuilder_details = p.id_appagebuilder_details)'
                .$where;
        return Db::getInstance()->executes($sql);
    }

    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        // validate module
        unset($context);
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        $id_shop = apPageHelper::getIDShop();
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'appagebuilder_details_shop` (`id_shop`, `id_appagebuilder_details`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        if (Db::getInstance()->getValue('SELECT COUNT(p.`id_appagebuilder_details`) AS total FROM `'
                        ._DB_PREFIX_.'appagebuilder_details` p INNER JOIN `'
                        ._DB_PREFIX_.'appagebuilder_details_shop` ps ON(p.id_appagebuilder_details = ps.id_appagebuilder_details) WHERE id_shop='
                        .(int)$id_shop) <= 1) {
            $this->deActiveAll();
        } else if ($this->active) {
            $this->deActiveAll();
        }
        return $res;
    }

    public function toggleStatus()
    {
        $this->deActiveAll();
        return true;
    }

    public function deActiveAll()
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $sql = 'UPDATE '._DB_PREFIX_.'appagebuilder_details_shop SET active=0 where id_shop='.(int)$id_shop;
        Db::getInstance()->execute($sql);
        $where = ' WHERE ps.id_shop='.(int)$id_shop." AND ps.id_appagebuilder_details = '".(int)$this->id."'";
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'appagebuilder_details_shop` ps set ps.active = 1 '.$where);
    }

    public static function getActive()
    {
        $id_shop = Context::getContext()->shop->id;
        if (Tools::getIsset('plist_key') && Tools::getValue('plist_key')) {
            // validate module
            $where = " p.plist_key='".pSQL(Tools::getValue('plist_key'))."' and ps.id_shop=".(int)$id_shop;
        } else {
            // validate module
            $where = ' ps.active=1 and ps.id_shop='.(int)$id_shop;
        }

        $sql = 'SELECT * FROM '._DB_PREFIX_.'appagebuilder_details p
                INNER JOIN '._DB_PREFIX_.'appagebuilder_details_shop ps on(p.id_appagebuilder_details = ps.id_appagebuilder_details) WHERE '
                .pSQL($where);
        return Db::getInstance()->getRow($sql);
    }
    
    public function delete()
    {
        $result = parent::delete();
        
        if ($result) {
            if (isset($this->def['multishop']) && $this->def['multishop'] == true) {
                # DELETE RECORD FORM TABLE _SHOP
                $id_shop_list = Shop::getContextListShopID();
                if (count($this->id_shop_list)) {
                    $id_shop_list = $this->id_shop_list;
                }

                $id_shop_list = array_map('intval', $id_shop_list);
                //DONGND:: fix sql
                $id_shop_list = implode(', ', $id_shop_list);
                
                Db::getInstance()->delete($this->def['table'].'_shop', '`'.$this->def['primary'].'`='.
                    (int)$this->id.' AND id_shop IN ('.pSQL($id_shop_list).')');
            }
        }
        
        return $result;
    }
}
