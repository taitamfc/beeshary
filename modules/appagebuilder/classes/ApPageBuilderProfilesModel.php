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

class ApPageBuilderProfilesModel extends ObjectModel
{
    public $name;
    public $friendly_url;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $group_box;
    public $params;
    public $page;
    public $profile_key;
    public $header;
    public $content;
    public $footer;
    public $product;
    public $active;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'appagebuilder_profiles',
        'primary' => 'id_appagebuilder_profiles',
        'multilang' => true,
        'multishop' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'friendly_url' => array('type' => self::TYPE_STRING, 'size' => 255, 'lang' => true, 'validate' => 'isLinkRewrite'),
            'meta_title' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'lang' => true),
            'meta_description' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'lang' => true),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'lang' => true),
            'group_box' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'page' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'profile_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'header' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'content' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'footer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'params' => array('type' => self::TYPE_HTML)
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        // validate module
        unset($context);
        parent::__construct($id, $id_lang, $id_shop);
        $this->loadDataShop();
    }
    
    public function loadDataShop()
    {
        if ($this->def['multishop'] == true) {
            $sql = 'SELECT * FROM ' ._DB_PREFIX_.$this->def['table'] . '_shop WHERE ' .$this->def['primary'] . ' =' .(int)$this->id;
            $this->data_shop = Db::getInstance()->getRow($sql);
            
            if (isset($this->data_shop['active'])) {
                $this->active = $this->data_shop['active'];
            }
        }
    }

    public function toggleStatus()
    {
        $this->deActiveAll($this->page);
        return true;
    }

    public function deActiveAll($page)
    {
        // validate module
        unset($page);
        $id_shop = apPageHelper::getIDShop();
        //$where = ' WHERE ps.id_shop='.$id_shop." AND ps.id_appagebuilder_profiles != '".(int)$this->id."'";
        $where = ' WHERE ps.id_shop='.(int)$id_shop;
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'appagebuilder_profiles_shop` ps set ps.active = 0 '.$where);
        $where = ' WHERE ps.id_shop='.$id_shop." AND ps.id_appagebuilder_profiles = '".(int)$this->id."'";
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'appagebuilder_profiles_shop` ps set ps.active = 1 '.$where);
    }

    public function getAllProfileByShop()
    {
        try {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
            $id_lang = $context->language->id;
//            $where = ' WHERE id_shop='.(int)$id_shop.' AND id_lang='.(int)$id_lang;
            $sql = 'SELECT p.*, ps.*,pl.friendly_url
                        FROM '._DB_PREFIX_.'appagebuilder_profiles p
                        INNER JOIN '._DB_PREFIX_.'appagebuilder_profiles_lang pl ON (p.id_appagebuilder_profiles = pl.id_appagebuilder_profiles)
                        INNER JOIN '._DB_PREFIX_.'appagebuilder_profiles_shop ps ON (ps.id_appagebuilder_profiles = p.id_appagebuilder_profiles)
                        WHERE id_shop='.(int)$id_shop.' AND id_lang='.(int)$id_lang;
            return Db::getInstance()->executes($sql);
        } catch (Exception $exc) {
            return array(); // OLD MODULE, NOT NEED SHOW DATA
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        $id_shop = apPageHelper::getIDShop();
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'appagebuilder_profiles_shop` (`id_shop`, `id_appagebuilder_profiles`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        if (Db::getInstance()->getValue('SELECT COUNT(p.`id_appagebuilder_profiles`) AS total FROM `'._DB_PREFIX_.'appagebuilder_profiles` p
                INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_shop` ps ON(p.id_appagebuilder_profiles = ps.id_appagebuilder_profiles)
                WHERE id_shop='.(int)$id_shop) <= 1) {
                $this->deActiveAll($this->page);
        } else if ($this->active) {
            $this->deActiveAll($this->page);
        }
        return $res;
    }

    public function update($null_values = false)
    {
        // validate module
        unset($null_values);
        if ($this->active) {
            $this->deActiveAll($this->page);
        }

        return parent::update();
    }

    public function getProfilesInPage($id = 0)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $where = ' WHERE ps.id_shop='.(int)$id_shop." AND p.page='".pSQL($this->page)."'";
        if ($id) {
            $where .= ' AND p.id_appagebuilder_profiles !='.(int)$id;
        }
        $inner_join = 'INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_shop` ps ON (ps.id_appagebuilder_profiles = p.id_appagebuilder_profiles)';
        $sql = 'SELECT p.* from `'._DB_PREFIX_.'appagebuilder_profiles` p '.$inner_join.$where;
        return Db::getInstance()->executes($sql);
    }

    public static function getActiveProfile($page)
    {
        // validate module
        unset($page);

        # Fix bug http://screencast.com/t/flCEjya6
        $updatePositions = Tools::getValue('action');
        $ajax = Tools::getValue('ajax');
        if ($updatePositions == 'updatePositions' && $ajax == '1') {
            return null;
        }

        $result = null;
        $is_use_co = Configuration::get('APPAGEBUILDER_COOKIE_PROFILE');
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $id_profile = ApPageBuilderProfilesModel::getIdProfileFromRewrite();
        # SET PROFILE FOLLOW GROUP_CUSTOMER
        $current_group_id = Group::getCurrent()->id;
        if (isset($current_group_id) && $current_group_id == 3) {
            $customer = Context::getContext()->customer;
            $sql = 'SELECT id_group FROM `'._DB_PREFIX_.'customer_group` WHERE id_customer='.(int)$customer->id;
            $array_group_id = Db::getInstance()->executeS($sql);
            foreach ($array_group_id as $group_id) {
                if ($group_id !=1 && $group_id !=2 && $group_id !=3) {
                    $current_group_id = $group_id['id_group'];
                }
            }
        }
        $model = new ApPageBuilderProfilesModel();
        $list_profiles = $model->getAllProfileByShop();
        if ($list_profiles) {
            foreach ($list_profiles as $profile) {
                $group_boxs = $profile['group_box'];
                $aray_group_box = explode(',', $group_boxs);
                foreach ($aray_group_box as $group_box) {
                    if (isset($current_group_id) && $current_group_id == $group_box) {
                        $profile_current = $profile['id_appagebuilder_profiles'];
                    }
                }
            }
        }
        if (isset($profile_current) && $profile_current != "") {
            $id_profile = $profile_current;
        }
        //check cookie
        if ($is_use_co) {
            if (!$id_profile && $context->cookie->ap_profile) {
                $id_profile = $context->cookie->ap_profile;
            } else {
                $context->cookie->ap_profile = $id_profile;
            }
        }

        $id_lang = $context->language->id;
        $cache_id = 'ApPageBuilderProfilesModel::getActiveProfile_'.md5((int)$id_profile.(int)$id_lang.(int)$id_shop);
        if (!Cache::isStored($cache_id)) {
            if ($id_profile) {
                $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND p.id_appagebuilder_profiles='.(int)$id_profile;
            } else {
                $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND ps.active=1 ';
            }
            $inner_join = ' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_lang` pl ON (pl.id_appagebuilder_profiles = p.id_appagebuilder_profiles) AND id_lang='.(int)$id_lang;
            $inner_join .= ' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_shop` ps ON (ps.id_appagebuilder_profiles = p.id_appagebuilder_profiles)';
            $sql = 'SELECT p.*, pl.* from `'._DB_PREFIX_.'appagebuilder_profiles` p '.$inner_join.$where;
            $result = Db::getInstance()->getRow($sql);
            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }
        foreach (array('header', 'content', 'footer', 'product') as $val) {
            $pos_key = 'ap_'.$val;
            if (Tools::getIsset($val)) {
                $result[$val] = Tools::getValue($val);
                
                if (Tools::isSubmit('controller') && Tools::getValue('controller') === 'AdminNewsletterPro') {
                     // Conflict with Newsletter Pro module
                } else {
                    $context->cookie->{$pos_key} = $result[$val];
                }
            } else if ($is_use_co && $context->cookie->{$pos_key}) {
                $result[$val] = $context->cookie->{$pos_key};
            }
        }

        return $result;
    }

    public function getProfile($id)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'appagebuilder_profiles` WHERE id_appagebuilder_profiles='.(int)$id;
        $object = Db::getInstance()->getRow($sql);
        return $object ? $object : null;
    }

    public function duplicateProfile($id, $name, $profile_key, $id_shop)
    {
        $new_id = 0;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'appagebuilder_profiles` WHERE id_appagebuilder_profiles='.(int)$id;
        $object_duplicated = Db::getInstance()->getRow($sql);
        if ($object_duplicated) {
            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_profiles`(name, profile_key, page) VALUES("'
                    .pSQL($name).pSQL($object_duplicated['name']).'", "'.pSQL($profile_key).'", "index")';
            Db::getInstance()->execute($sql);
            $new_id = Db::getInstance()->Insert_ID();
            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_profiles_shop`(id_appagebuilder_profiles, id_shop, active) VALUES('
                    .(int)$new_id.', '.(int)$id_shop.', 0)';
            Db::getInstance()->execute($sql);
            return $new_id;
        }
        return 0;
    }

    public function customDuplicateObject($message)
    {
        $object_duplicated = parent::duplicateObject();
        $object_duplicated->active = 0;
        $object_duplicated->name = $message.' '.$object_duplicated->name;
        return $object_duplicated;
    }

    public function save($null_values = false, $autodate = true)
    {
        // validate module
        unset($null_values);
        unset($autodate);
        $context = Context::getContext();
        $this->id_shop = $context->shop->id;
        if ($this->active) {
            $this->deActiveAll($this->page);
        }
        return parent::save();
    }

    public static function deleteById($id)
    {
        $id = (int)$id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'appagebuilder_profiles` WHERE id_appagebuilder_profiles='.(int)$id;
        $object_duplicated = Db::getInstance()->getRow($sql);
        if ($object_duplicated) {
            $sql = 'DELETE FROM `'._DB_PREFIX_.'appagebuilder_profiles` WHERE id_appagebuilder_profiles='.(int)$id;
            Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM `'._DB_PREFIX_.'appagebuilder_profiles_lang` WHERE id_appagebuilder_profiles='.(int)$id;
            Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM `'._DB_PREFIX_.'appagebuilder_profiles_shop` WHERE id_appagebuilder_profiles='.(int)$id;
            Db::getInstance()->execute($sql);
            return $object_duplicated;
        }
        return array();
    }

    public function getPositionsForProfile($id_positions)
    {
        if ($id_positions) {
            $id_positions = implode(',', array_map('intval', $id_positions));
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'appagebuilder_positions` WHERE id_appagebuilder_positions IN('.pSQL($id_positions).')';
            return Db::getInstance()->executes($sql);
        }
        return array();
    }

    public function delete($params = array())
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
                    (int)$this->id.' AND id_shop IN ('.pSQL(implode(', ', $id_shop_list)).')');
                
                if (isset($params['import_sample']) && $params['import_sample'] == true) {
                    $position = new ApPageBuilderPositionsModel((int)$this->header);
                    $position->delete();
                    $position = new ApPageBuilderPositionsModel((int)$this->content);
                    $position->delete();
                    $position = new ApPageBuilderPositionsModel((int)$this->footer);
                    $position->delete();
                    $position = new ApPageBuilderPositionsModel((int)$this->product);
                    $position->delete();
                }
            }
        }
        
        return $result;
    }
    
    /**
     * $id_profile = ApPageBuilderProfilesModel::getIdProfileFromRewrite();
     */
    public static function getIdProfileFromRewrite($linkRewrive = '')
    {
        static $id_appagebuilder_profiles = null;
        
        if ($id_appagebuilder_profiles === null) {
            $context = Context::getContext();
            $id_shop = (int)$context->shop->id;
            $id_lang = (int)$context->language->id;
                
            # ACCESS BY id_appagebuilder_profiles
            $id_temp = Tools::getIsset('id_appagebuilder_profiles') ? Tools::getValue('id_appagebuilder_profiles') : false;
            if ($id_temp !== false) {
                $id_appagebuilder_profiles = $id_temp;
                return $id_appagebuilder_profiles;
            }

            # ACCESS BY friendly_url
            $linkRewrive = explode('/', $_SERVER['REQUEST_URI']);
            $linkRewrive = rtrim(end($linkRewrive), '.html');
            if (strpos($linkRewrive, '?')) {
                // REMOVE ? FROM URL
                $temp_str = explode("?", $linkRewrive);
                $linkRewrive = $temp_str[0];
                $linkRewrive = rtrim($linkRewrive, '.html');
            }
            if (!empty($linkRewrive)) {
                $sql = 'SELECT p.`id_appagebuilder_profiles` FROM `'._DB_PREFIX_.'appagebuilder_profiles` p';
                $sql .= ' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_lang` pl ON(pl.id_appagebuilder_profiles = p.id_appagebuilder_profiles AND pl.friendly_url=\''.pSQL($linkRewrive).'\' AND id_lang='.(int)$id_lang.' ) ';
                $sql .= ' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_shop` ps ON(ps.id_appagebuilder_profiles = p.id_appagebuilder_profiles AND ps.id_shop ='.(int)$id_shop.')';

                $id_appagebuilder_profiles = Db::getInstance()->getValue($sql);
                return $id_appagebuilder_profiles;
            }
            
            
            
            # ACESS BY active_profile
            $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND ps.active=1 ';

            $inner_join = ' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_lang` pl ON (pl.id_appagebuilder_profiles = p.id_appagebuilder_profiles) AND id_lang='.(int)$id_lang;
            $inner_join .= ' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_shop` ps ON (ps.id_appagebuilder_profiles = p.id_appagebuilder_profiles)';
            $sql = 'SELECT p.id_appagebuilder_profiles, p.name, p.profile_key, pl.id_lang, ps.id_shop, ps.active from `'._DB_PREFIX_.'appagebuilder_profiles` p '.$inner_join.$where;

            $active_profile = Db::getInstance()->getRow($sql);
            
            if ($active_profile) {
                return $active_profile['id_appagebuilder_profiles'];
            }
            
            return false;
        }
        return $id_appagebuilder_profiles;
    }
    
    public static function getAllProfileRewrite($id_profile = null)
    {
        if ($id_profile) {
            $id_shop = Context::getContext()->shop->id;
        
            if ($id_profile) {
                $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND p.id_appagebuilder_profiles='.(int)$id_profile;
            }

            $inner_join = ' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_lang` pl ON (pl.id_appagebuilder_profiles = p.id_appagebuilder_profiles)';
            $inner_join .= ' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_shop` ps ON (ps.id_appagebuilder_profiles = p.id_appagebuilder_profiles)';
            $sql = 'SELECT p.id_appagebuilder_profiles, p.name, pl.id_lang, pl.friendly_url from `'._DB_PREFIX_.'appagebuilder_profiles` p '.$inner_join.$where;
            $result = Db::getInstance()->executeS($sql);
            
            foreach ($result as $key => $value) {
                $result[$key]['iso_code'] = Language::getIsoById($result[$key]['id_lang']);
            }
            // validate module
            unset($value);
            
            return $result;
        }
        return array();
    }
}
