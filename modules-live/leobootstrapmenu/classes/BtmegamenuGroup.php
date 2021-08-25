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

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class BtmegamenuGroup extends ObjectModel
{
    public $title;
    public $title_fo;
    public $active;
    public $hook;
    public $position;
    public $id_shop;
    public $params;
    
    //DONGD:: check call via appagebuilder
    public $active_ap;
    public $randkey;
    public $data = array();
    public $form_id;
    
    const GROUP_STATUS_DISABLE = '0';
    const GROUP_STATUS_ENABLE = '1';
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'btmegamenu_group',
        'primary' => 'id_btmegamenu_group',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            //'hook' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 64),
            'hook' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml', 'size' => 64),
            'position' => array('type' => self::TYPE_INT),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'params' => array('type' => self::TYPE_HTML, 'lang' => false),
            
            'active_ap' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'randkey' => array('type' => self::TYPE_STRING, 'lang' => false, 'size' => 255),
            'form_id' => array('type' => self::TYPE_STRING, 'lang' => false, 'size' => 255),
            # Lang fields
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'title_fo' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
        )
    );

    public function add($autodate = true, $null_values = false)
    {
        $res = parent::add($autodate, $null_values);

        return $res;
    }

    public static function groupExists($id_group, $id_shop = null)
    {
        $req = 'SELECT gr.`id_btmegamenu_group` as id_group
                FROM `'._DB_PREFIX_.'btmegamenu_group` gr
                WHERE gr.`id_btmegamenu_group` = '.(int)$id_group;
        if ($id_shop != null) {
            $req .= ' AND gr.`id_shop` = '.(int)$id_shop;
        }
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
        return ($row);
    }

    public static function getGroups()
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $id_lang = $context->language->id;
        $cacheId = 'leobootstrapmenu_classes_BtmegamenuGroup.php_____getGroups()_' . md5($id_shop.$id_lang);
        
        if (!Cache::isStored($cacheId)) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'btmegamenu_group` gr
                LEFT JOIN '._DB_PREFIX_.'btmegamenu_group_lang grl ON gr.id_btmegamenu_group = grl.id_btmegamenu_group AND grl.id_lang = '.(int)$id_lang.'
                WHERE (`id_shop` = '.(int)$id_shop.')';
            
                $result = Db::getInstance()->executes($sql);
                Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }
    
    /**
     * $key : field in db
     * $value : value in db
     * $one :  default return one record
     */
    public static function cacheGroupsByFields($params = array(), $one = false)
    {
        $result = array();
        
        $groups = self::getGroups();
        foreach ($groups as $group) {
            $check_field = true;
            foreach ($params as $key => $value) {
                if ($group[$key] != $value) {
                    $check_field = false;
                    break;
                }
            }
            
            if ($check_field) {
                if ($one === false) {
                    $result = $group;
                    break;
                } else {
                    $result[] = $group;
                }
            }
        }
        
        return $result;
    }
    
    
    
    public function delete()
    {
        $res = true;

        $sql = 'DELETE FROM `'._DB_PREFIX_.'btmegamenu_group` '
                .'WHERE `id_btmegamenu_group` = '.(int)$this->id;
        $res &= Db::getInstance()->execute($sql);
        $sql = 'DELETE FROM `'._DB_PREFIX_.'btmegamenu_group_lang` '
                .'WHERE `id_btmegamenu_group` = '.(int)$this->id;
        $res &= Db::getInstance()->execute($sql);
        $sql = 'SELECT bt.`id_btmegamenu` as id
                FROM `'._DB_PREFIX_.'btmegamenu` bt
                WHERE bt.`id_group` = '.(int)$this->id;
        $btmegamenu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($btmegamenu) {
            $where = '';
            foreach ($btmegamenu as $bt) {
                $where .= $where ? ','.(int)$bt['id'] : (int)$bt['id'];
            }
            $sql = 'DELETE FROM `'._DB_PREFIX_.'btmegamenu` '
                    .'WHERE `id_btmegamenu` IN ('.$where.')';
            Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM `'._DB_PREFIX_.'btmegamenu_lang` '
                    .'WHERE `id_btmegamenu` IN ('.$where.')';
            Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM `'._DB_PREFIX_.'btmegamenu_shop` '
                    .'WHERE `id_btmegamenu` IN ('.$where.')';
            Db::getInstance()->execute($sql);
        }
        
        $res &= parent::delete();
        return $res;
    }

    /**
     * Get group to frontend
     */
    public static function getActiveGroupByHook($hook_name = '', $active = 1)
    {
        $id_shop = Context::getContext()->shop->id;
        $id_lang = Context::getContext()->language->id;
        $sql = '
                SELECT *
                FROM '._DB_PREFIX_.'btmegamenu_group gr
                LEFT JOIN '._DB_PREFIX_.'btmegamenu_group_lang grl ON gr.id_btmegamenu_group = grl.id_btmegamenu_group AND grl.id_lang = '.(int)$id_lang.'
                WHERE gr.id_shop = '.(int)$id_shop.'
                AND gr.hook = "'.pSQL($hook_name).'"'.
                ($active ? ' AND gr.`active` = 1' : ' ').'
                ORDER BY gr.position';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Get group to preview
     */
    public static function getGroupByID($id_group)
    {
        $sql = '
            SELECT *
            FROM '._DB_PREFIX_.'btmegamenu_group gr
            WHERE gr.id_btmegamenu_group = '.(int)$id_group;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function count()
    {
        $sql = 'SELECT id_btmegamenu_group FROM '._DB_PREFIX_.'btmegamenu_group';
        $groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $number_groups = count($groups);
        return $number_groups;
    }
    
    
    // get last position of group
    public static function getLastPosition($id_shop)
    {
        return (Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'btmegamenu_group` WHERE `id_shop` = '.(int)$id_shop));
    }
    
    // get all menu of group
    public static function getMenuByGroup($id_group)
    {
        $sql = 'SELECT `id_btmegamenu`,`id_parent` FROM `'._DB_PREFIX_.'btmegamenu`
                WHERE `id_group` = '.(int)$id_group.'
                ORDER BY `id_parent` ASC';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
    
    // get all menu parent of group
    public static function getMenuParentByGroup($id_group)
    {
        $sql = 'SELECT `id_btmegamenu`,`id_parent` FROM `'._DB_PREFIX_.'btmegamenu`
                WHERE `id_group` = '.(int)$id_group.' AND `id_parent` = 0';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
    
    // set data for group when import
    public static function setDataForGroup($group, $content, $override)
    {
        $languages = Language::getLanguages();
            
        $lang_list = array();
        foreach ($languages as $lang) {
            # module validation
            $lang_list[$lang['iso_code']] = $lang['id_lang'];
        }
        if (is_array($content['title'])) {
            foreach ($content['title'] as $key => $title_item) {
                if (isset($lang_list[$key])) {
                    $group->title[$lang_list[$key]] = $title_item;
                }
            }
        } else {
            foreach ($languages as $lang) {
                $group->title[$lang['id_lang']] = $content['title'];
            }
        }
        if (is_array($content['title_fo'])) {
            foreach ($content['title_fo'] as $key => $title_item) {
                if (isset($lang_list[$key])) {
                    $group->title_fo[$lang_list[$key]] = $title_item;
                }
            }
        } else {
            $group_title_fo = '';
            foreach ($languages as $lang) {
                if ($lang['iso_code'] == 'en') {
                    $group_title_fo = 'Categories';
                }
                if ($lang['iso_code'] == 'es') {
                    $group_title_fo = 'Categorías';
                }
                if ($lang['iso_code'] == 'fr') {
                    $group_title_fo = 'Catégories';
                }
                if ($lang['iso_code'] == 'de') {
                    $group_title_fo = 'Kategorien';
                }
                if ($lang['iso_code'] == 'it') {
                    $group_title_fo = 'Categorie';
                }
                if ($lang['iso_code'] == 'ar') {
                    $group_title_fo = 'ال�?ئات';
                }
                $group->title_fo[$lang['id_lang']] = $group_title_fo;
            }
        }
        
        $group->id_shop = Context::getContext()->shop->id;
        $group->hook = $content['hook'];
        if (!$override) {
            $group->position = self::getLastPosition(Context::getContext()->shop->id);
            include_once(_PS_MODULE_DIR_.'leobootstrapmenu/libs/Helper.php');
            $group->randkey = LeoBtmegamenuHelper::genKey();
        }
        $group->active = $content['active'];
        $group->params = $content['params'];
        $group->active_ap = $content['active_ap'];
        return $group;
    }
    
    public static function autoCreateKey()
    {
        $sql = 'SELECT '.self::$definition['primary'].' FROM '._DB_PREFIX_.bqSQL(self::$definition['table']).
                ' WHERE randkey IS NULL OR randkey = ""';
        
        $rows = Db::getInstance()->executes($sql);
        foreach ($rows as $row) {
            $mod_group = new BtmegamenuGroup((int)$row[self::$definition['primary']]);
            include_once(_PS_MODULE_DIR_.'leobootstrapmenu/libs/Helper.php');
            $mod_group->randkey = LeoBtmegamenuHelper::genKey();
            $mod_group->update();
        }
    }
}
