<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Adds image, text or video to your homepage.
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

class LeoSlideshowGroup extends ObjectModel
{

    public $title;
    public $active;
    public $hook;
    public $id_shop;
    public $params;
    public $active_ap;
    public $data = array();
    public $randkey;
    public $leo_module = null;

    /**
     * @see ObjectModel::$definition
     */
    public function setModule($module)
    {
        $this->leo_module = $module;
    }

    public static $definition = array(
        'table' => 'leoslideshow_groups',
        'primary' => 'id_leoslideshow_groups',
        'fields' => array(
            'title' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'hook' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml', 'size' => 64),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'params' => array('type' => self::TYPE_HTML, 'lang' => false),
            'active_ap' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'randkey' => array('type' => self::TYPE_STRING, 'lang' => false, 'size' => 255),
        )
    );

    public function add($autodate = true, $null_values = false)
    {
        $res = parent::add($autodate, $null_values);

        return $res;
    }

    public static function groupExists($id_group)
    {
        $req = 'SELECT gr.`id_leoslideshow_groups` as id_group
                FROM `' . _DB_PREFIX_ . 'leoslideshow_groups` gr
                WHERE gr.`id_leoslideshow_groups` = ' . (int) $id_group;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
        return ($row);
    }

    public function getGroups($active, $id_shop)
    {
        $this->context = Context::getContext();
        if (!isset($id_shop)) {
            $id_shop = $this->context->shop->id;
        }
        $req = 'SELECT * FROM `' . _DB_PREFIX_ . 'leoslideshow' . '_groups` gr
                WHERE (`id_shop` = ' . (int) $id_shop . ')' . ($active ? ' AND gr.`active` = 1' : ' ');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
    }

    /**
     * Delele all Slides follow id_group
     */
    public static function deleteAllSlider($id_group)
    {
        $sql = 'SELECT sl.`id_leoslideshow_slides` as id
                FROM `' . _DB_PREFIX_ . 'leoslideshow_slides` sl
                WHERE sl.`id_group` = ' . (int) $id_group;
        $sliders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($sliders) {
            $where = '';
            foreach ($sliders as $sli) {
                # module validation
                $where .= $where ? ',' . (int) $sli['id'] : (int) $sli['id'];
            }

            # SLIDE
            $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'leoslideshow_slides` '
                    . 'WHERE `id_leoslideshow_slides` IN (' . $where . ')';
            Db::getInstance()->execute($sql);

            # SLIDE_LANG
            $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'leoslideshow_slides_lang` '
                    . 'WHERE `id_leoslideshow_slides` IN (' . $where . ')';
            Db::getInstance()->execute($sql);
        }
    }

    public function delete()
    {
        $res = parent::delete();

        if ($res) {
            $this->deleteAllSlider((int) $this->id);
        }

        return $res;
    }

    /**
     * Get and validate StartWithSlide field.
     */
    public static function showStartWithSlide($start_with_slide = 0, $slider = array())
    {
        $result = 1;
        if (is_array($slider)) {
            $start_with_slide = (int) $start_with_slide;
            $slider_num = count($slider);
            // 1 <= $start_with_slide <= $slider_num
            if (1 <= $start_with_slide && $start_with_slide <= $slider_num) {
                $result = $start_with_slide;
            }
        }

        $result--; // index begin from 0
        return $result;
    }

    public function getDelay()
    {
        $temp_result = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($this->params), true);
        $result = $temp_result['delay'];

        return $result;
    }

    public static function getGroupOption()
    {
        $result = array();
        $mod_group = new LeoSlideshowGroup();
        $groups = $mod_group->getGroups(null, null);

        foreach ($groups as $group) {
            $temp = array();
            $temp['id'] = $group['id_leoslideshow_groups'];
            $temp['name'] = $group['title'];
            $result[] = $temp;
        }
        return $result;
    }

    /**
     * Get group to frontend
     */
    public static function getActiveGroupByHook($hook_name = '', $active = 1)
    {
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'leoslideshow_groups gr
                WHERE gr.id_shop = ' . (int) $id_shop . ' AND gr.hook = "' . pSQL($hook_name) . '"' . ($active ? ' AND gr.`active` = 1' : ' ') . '
                ORDER BY gr.id_leoslideshow_groups';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    /**
     * Get group to preview
     */
    public static function getGroupByID($id_group)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'leoslideshow_groups gr
                WHERE gr.id_leoslideshow_groups = ' . (int) $id_group;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function setData($data = array())
    {
        if (is_array($data)) {
            $this->data = $data;
        }
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function beforeLoad()
    {
        $data = $this->data;

        #Timer
        if ($data['timer_show'] == LeoSlideshowConfig::TIMER_HIDE_AUTO) {
            $data['autoAdvance'] = 1;
        } elseif ($data['timer_show'] == LeoSlideshowConfig::TIMER_HIDE_STOP) {
            $data['autoAdvance'] = 0;
        } elseif ($data['timer_show'] == LeoSlideshowConfig::TIMER_SHOW_AUTO) {
            $data['autoAdvance'] = 1;
        } elseif ($data['timer_show'] == LeoSlideshowConfig::TIMER_SHOW_STOP) {
            $data['autoAdvance'] = 0;
        }

        # Navigator
        if ($data['navigator_type'] == LeoSlideshowConfig::IVIEW_NAV_THUMBNAIL) {
            $data['controlNavThumbs'] = 1;
        } elseif ($data['navigator_type'] == LeoSlideshowConfig::IVIEW_NAV_BULLET) {
            $data['controlNavThumbs'] = 0;
        }

        # Multi language
        $data['playLabel'] = $this->leo_module->l('Play');
        $data['pauseLabel'] = $this->leo_module->l('Pause');
        $data['closeLabel'] = $this->leo_module->l('Close');
        $data['nextLabel'] = $this->leo_module->l('Next');
        $data['previousLabel'] = $this->leo_module->l('Previous');

        $this->data = $data;
        return $this;
    }

    public function loadFrontEnd()
    {
        return $this->getData();
    }

    public function count()
    {
        $sql = 'SELECT id_leoslideshow_groups FROM ' . _DB_PREFIX_ . 'leoslideshow_groups';
        $groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $number_groups = count($groups);
        return $number_groups;
    }

    public static function autoCreateKey()
    {
        $sql = 'SELECT ' . self::$definition['primary'] . ' FROM ' . _DB_PREFIX_ . self::$definition['table'] .
                ' WHERE randkey IS NULL OR randkey = ""';

        $rows = Db::getInstance()->executes($sql);
        foreach ($rows as $row) {
            $mod_group = new LeoSlideshowGroup((int) $row[self::$definition['primary']]);
            include_once(_PS_MODULE_DIR_ . 'leoslideshow/libs/Helper.php');
            $mod_group->randkey = LeoSlideshowHelper::genKey();
            $mod_group->update();
        }
    }
}
