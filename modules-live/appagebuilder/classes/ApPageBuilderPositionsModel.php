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

require_once(_PS_MODULE_DIR_.'appagebuilder/libs/Helper.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/shortcodes.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageSetting.php');

class ApPageBuilderPositionsModel extends ObjectModel
{
    public $name;
    public $params;
    public $position;
    public $position_key;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'appagebuilder_positions',
        'primary' => 'id_appagebuilder_positions',
        'multilang' => false,
        'multishop' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'position' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'position_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'params' => array('type' => self::TYPE_HTML)
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        // validate module
        unset($context);
        parent::__construct($id, $id_lang, $id_shop);
//        Shop::addTableAssociation($this->table, array('type' => 'shop'));     // Delete with all table_shop, Insert all shop. Remove
    }

    public static function getProfileUsingPosition($id)
    {
        $id = (int)$id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'appagebuilder_profiles` P
                WHERE
                    P.`header`='.(int)$id.'
                    OR P.`content`='.(int)$id.'
                    OR P.`footer`='.(int)$id.'
                    OR P.`product`='.(int)$id;
        return Db::getInstance()->executes($sql);
    }
    
    public function add($autodate = true, $null_values = false)
    {
        $id_shop = apPageHelper::getIDShop();
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'appagebuilder_positions_shop` (`id_shop`, `id_appagebuilder_positions`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        return $res;
    }

    public function addAuto($data)
    {
        $id_shop = apPageHelper::getIDShop();
        
        $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_positions` (name, position, position_key)
                VALUES("'.pSQL($data['name']).'", "'.pSQL($data['position']).'", "'.pSQL($data['position_key']).'")';
        Db::getInstance()->execute($sql);
        
        $id = Db::getInstance()->Insert_ID();
        
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'appagebuilder_positions_shop` (`id_shop`, `id_appagebuilder_positions`)
            VALUES('.(int)$id_shop.', '.(int)$id.')');
        return $id;
    }

    public static function getAllPosition()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'appagebuilder_positions`';
        return Db::getInstance()->getRow($sql);
    }

    public static function getPositionById($id)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'appagebuilder_positions` WHERE id_appagebuilder_positions='.(int)$id;
        return Db::getInstance()->getRow($sql);
    }

    public static function updateName($id, $name)
    {
        $id = (int)$id;
        if ($id && $name) {
            $sql = 'UPDATE '._DB_PREFIX_.'appagebuilder_positions SET name=\''.pSQL($name).'\' WHERE id_appagebuilder_positions='.(int)$id;
            return Db::getInstance()->execute($sql);
        }
        return false;
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

                Db::getInstance()->delete($this->def['table'].'_shop', '`'.$this->def['primary'].'`='.
                    (int)$this->id.' AND id_shop IN ('. pSQL(implode(', ', $id_shop_list)).')');
            }
            
            # DELETE DATA AT OTHER TABLE
            $sql = 'SELECT id_appagebuilder FROM '._DB_PREFIX_.'appagebuilder WHERE id_appagebuilder_positions = ' . (int)$this->id;
            $rows = Db::getInstance()->executes($sql);
            
            foreach ($rows as $row) {
                $obj = new ApPageBuilderModel($row['id_appagebuilder']);
                $obj->delete();
            }
            
            # Profile not use this position
            if (in_array($this->position, array('header', 'content', 'footer', 'product'))) {
                $sql = 'UPDATE '._DB_PREFIX_.'appagebuilder_profiles SET `'.bqSQL($this->position).'`=0 WHERE `'.bqSQL($this->position).'`='.(int)$this->id;
                Db::getInstance()->execute($sql);
            }
        }
        return $result;
    }
}
