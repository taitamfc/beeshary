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

require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageSetting.php');

class ApPageBuilderShortcodeModel extends ObjectModel
{
    public $shortcode_key;
    // public $id_appagebuilder;
    public $shortcode_name;
    public $active = true;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'appagebuilder_shortcode',
        'primary' => 'id_appagebuilder_shortcode',
        'multilang' => true,
        'multishop' => true,
        'fields' => array(
            'shortcode_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            // 'id_appagebuilder' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            
            'shortcode_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'lang' => true, 'required' => true),
                    
            'active' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
        )
    );
    
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
                INSERT INTO `'._DB_PREFIX_.'appagebuilder_shortcode_shop` (`id_shop`, `id_appagebuilder_shortcode`, `active`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.', '.(int)$this->active.')');
        return $res;
    }
    
    public function update($nullValues = false)
    {
        $id_shop = apPageHelper::getIDShop();
        $res = parent::update($nullValues);
                    
        $res &= Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'appagebuilder_shortcode_shop` ps set ps.active = '.(int)$this->active.'  WHERE ps.id_shop='.(int)$id_shop.' AND ps.id_appagebuilder_shortcode = '.(int)$this->id);
    
        return $res;
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
                    (int)$this->id.' AND id_shop IN ('.pSQL(implode(', ', $id_shop_list)).')');
            }
            
            //DONGND:: delete appagebuilder related shortcode
            $id_appagebuilder = ApPageBuilderModel::getIdByIdShortCode((int)$this->id);
            
            if ($id_appagebuilder) {
                $obj = new ApPageBuilderModel($id_appagebuilder);
                $obj->delete();
            }
        }
        
        return $result;
    }
    
    public function getShortCodeContent($id_appagebuilder = 0, $is_font = 0, $id_lang = 0)
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND p.id_appagebuilder='.(int)$id_appagebuilder;
   
        if ($id_lang) {
            $where .= ' AND pl.id_lang = '.(int)$id_lang;
        } else {
            $id_lang = $context->language->id;
        }
        $sql = 'SELECT p.*, pl.params, pl.id_lang
                FROM `'._DB_PREFIX_.'appagebuilder` p
                    LEFT JOIN `'._DB_PREFIX_.'appagebuilder_shop` ps ON (ps.id_appagebuilder = p.id_appagebuilder)
                    LEFT JOIN `'._DB_PREFIX_.'appagebuilder_lang` pl ON (pl.id_appagebuilder = p.id_appagebuilder)
                    LEFT JOIN `'._DB_PREFIX_.'appagebuilder_shortcode` pp ON (p.id_appagebuilder_shortcode = pp.id_appagebuilder_shortcode)
                    
                '.pSql($where).' ORDER BY p.id_appagebuilder';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
//        # FIX - Only get language data valid
//        $id_langs = Language::getLanguages(true, false, true);
//        foreach ($result as $key => $val) {
//            if (isset($val['id_lang']) && !in_array($val['id_lang'], $id_langs)) {
//                unset($result[$key]);
//            }
//        }
        
        $data_lang = array();
        if ($is_font) {
            foreach ($result as $row) {
                $data_lang[$row['hook_name']] = $row['params'];
            }
            return $data_lang;
        }
        $ap_helper = new ApShortCodesBuilder();
        ApShortCodesBuilder::$is_front_office = $is_font;
        ApShortCodesBuilder::$is_gen_html = 1;
        foreach ($result as $row) {
            if (isset($data_lang[$row['id_appagebuilder']])) {
                $data_lang[$row['id_appagebuilder']]['params'][$row['id_lang']] = $row['params'];
            } else {
                $data_lang[$row['id_appagebuilder']] = array(
                    'id' => $row['id_appagebuilder'],
                    'hook_name' => $row['hook_name'],
                );
                $data_lang[$row['id_appagebuilder']]['params'][$row['id_lang']] = $row['params'];
            }
        }
        $data_hook = array();
        foreach ($data_lang as $row) {
            //process params
            foreach ($row['params'] as $key => $value) {
                ApShortCodesBuilder::$lang_id = $key;
                if ($key == $id_lang) {
                    ApShortCodesBuilder::$is_gen_html = 1;
                    $row['content'] = $ap_helper->parse($value);
                } else {
                    ApShortCodesBuilder::$is_gen_html = 0;
                    $ap_helper->parse($value);
                }
            }
            $data_hook[$row['hook_name']] = $row;
        }
        
        return array('content' => $data_hook, 'dataForm' => ApShortCodesBuilder::$data_form);
    }
    
    /**
     * Get all items - datas of all hooks by shop Id, lang Id for front-end or back-end
     * @param type $id_profiles
     * @param type $is_font (=0: for back-end; =1: for front-end)
     * @param type $id_lang
     * @return type
     */
    public static function getAllItems($id_appagebuilder, $is_font = 0, $id_lang = 0)
    {
        //print_r("Input: $id_profiles - $is_font - $id_lang"); 2-1-1
        $context = Context::getContext();
//        $id_profiles = (int)$profile['id_appagebuilder_profiles'];
        $id_shop = (int)$context->shop->id;
        $id_lang = $id_lang ? (int)$id_lang : (int)$context->language->id;

        $sql = 'SELECT p.*, pl.params, pl.id_lang
                FROM '._DB_PREFIX_.'appagebuilder p
                    LEFT JOIN '._DB_PREFIX_.'appagebuilder_shop ps ON (ps.id_appagebuilder = p.id_appagebuilder AND id_shop='.(int)$id_shop.')
                    LEFT JOIN '._DB_PREFIX_.'appagebuilder_lang pl ON (pl.id_appagebuilder = p.id_appagebuilder)
                    LEFT JOIN `'._DB_PREFIX_.'appagebuilder_shortcode` pp ON (p.id_appagebuilder_shortcode = pp.id_appagebuilder_shortcode)
                WHERE
                    pl.id_lang='.(int)$id_lang.'
                    AND ps.id_shop='.(int)$id_shop.'
                    AND p.id_appagebuilder = '.(int)$id_appagebuilder.'
                ORDER BY p.id_appagebuilder';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        //echo '<pre>';print_r($result);die;
        $data_lang = array();
        if ($is_font) {
            foreach ($result as $row) {
                $data_lang[$row['hook_name']] = $row['params'];
            }
            return $data_lang;
        }
        $ap_helper = new ApShortCodesBuilder();
        ApShortCodesBuilder::$is_front_office = $is_font;
        ApShortCodesBuilder::$is_gen_html = 1;
        foreach ($result as $row) {
            if (isset($data_lang[$row['id_appagebuilder']])) {
                $data_lang[$row['id_appagebuilder']]['params'][$row['id_lang']] = $row['params'];
            } else {
                $data_lang[$row['id_appagebuilder']] = array(
                    'id' => $row['id_appagebuilder'],
                    'hook_name' => $row['hook_name'],
                );
                $data_lang[$row['id_appagebuilder']]['params'][$row['id_lang']] = $row['params'];
            }
        }
        $data_hook = array_flip(ApPageSetting::getHookHome());
        foreach ($data_lang as $row) {
            //process params
            foreach ($row['params'] as $key => $value) {
                ApShortCodesBuilder::$lang_id = $key;
                if ($key == $id_lang) {
                    ApShortCodesBuilder::$is_gen_html = 1;
                    $row['content'] = $ap_helper->parse($value);
                } else {
                    ApShortCodesBuilder::$is_gen_html = 0;
                    $ap_helper->parse($value);
                }
            }
            $data_hook[$row['hook_name']] = $row;
        }

        return array('content' => $data_hook, 'dataForm' => ApShortCodesBuilder::$data_form);
    }
    
    public static function getShortCode($shortcode_key)
    {
        $id_shop = Context::getContext()->shop->id;
       
        $sql = 'SELECT p.*, pp.`id_appagebuilder` FROM `'._DB_PREFIX_.'appagebuilder_shortcode` p
                INNER JOIN `'._DB_PREFIX_.'appagebuilder_shortcode_shop` ps on(p.`id_appagebuilder_shortcode` = ps.`id_appagebuilder_shortcode`)
				INNER JOIN `'._DB_PREFIX_.'appagebuilder` pp on(p.`id_appagebuilder_shortcode` = pp.`id_appagebuilder_shortcode`) WHERE
                p.`shortcode_key` = "'.pSQL($shortcode_key).'" AND ps.`active`= 1 AND ps.`id_shop` = '.(int)$id_shop;
        
        return Db::getInstance()->getRow($sql);
    }
    
    public static function getListShortCode()
    {
        $id_shop = Context::getContext()->shop->id;
        $id_lang = Context::getContext()->language->id;
       
        $sql = 'SELECT p.*, ps.*, pl.* FROM `'._DB_PREFIX_.'appagebuilder_shortcode` p
                INNER JOIN `'._DB_PREFIX_.'appagebuilder_shortcode_shop` ps on(p.`id_appagebuilder_shortcode` = ps.`id_appagebuilder_shortcode`)
                INNER JOIN `'._DB_PREFIX_.'appagebuilder_shortcode_lang` pl on(p.`id_appagebuilder_shortcode` = pl.`id_appagebuilder_shortcode`) WHERE
                ps.`id_shop` = '.(int)$id_shop.' AND pl.`id_lang` = '.(int)$id_lang;

        return Db::getInstance()->executeS($sql);
    }
}
