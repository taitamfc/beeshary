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

class LeoSlideshowSlide extends ObjectModel
{

    public $title;
    public $link;
    public $image;
    public $id_group;
    public $position;
    public $active;
    public $params;
    public $thumbnail;
    public $video;
    public $layersparams;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leoslideshow_slides',
        'primary' => 'id_leoslideshow_slides',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'id_group' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            # Lang fields
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
            'link' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'required' => false, 'size' => 255),
            'image' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
            'thumbnail' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
            'params' => array('type' => self::TYPE_HTML, 'lang' => false),
            'video' => array('type' => self::TYPE_HTML, 'lang' => true),
            'layersparams' => array('type' => self::TYPE_HTML, 'lang' => true)
        )
    );

    public function add($autodate = true, $null_values = false)
    {
        $res = parent::add($autodate, $null_values);
        return $res;
    }

    public function delete()
    {
        $res = true;

//        $images = $this->image;
//        foreach ($images as $image) {
//            if (preg_match('/sample/', $image) === 0)
//            if ($image && file_exists(dirname(__FILE__).'/images/'.$image))
//            $res &= @unlink(dirname(__FILE__).'/images/'.$image);
//        }

        $res &= $this->reOrderPositions();

        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'leoslideshow_slides`
            WHERE `id_leoslideshow_slides` = ' . (int) $this->id;
        $res &= Db::getInstance()->execute($sql);

        $res &= parent::delete();
        return $res;
    }

    public static function sliderExist($id_slider)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT gr.`id_leoslideshow_slides` as id
                    FROM `' . _DB_PREFIX_ . 'leoslideshow_slides` gr
                            WHERE gr.`id_leoslideshow_slides` = ' . (int) $id_slider);
    }

    public function reOrderPositions()
    {
        $id_slide = $this->id;

        $sql = 'SELECT MAX(hss.`position`) as position
            FROM `' . _DB_PREFIX_ . 'leoslideshow_slides` hss
            WHERE hss.`id_group` = ' . (int) $this->id_group;
        $max = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ((int) $max == (int) $id_slide) {
            # module validation
            return true;
        }

        $sql = 'SELECT hss.`position` as position, hss.`id_leoslideshow_slides` as id_slide
            FROM `' . _DB_PREFIX_ . 'leoslideshow_slides` hss
            WHERE hss.`id_group` = ' . (int) $this->id_group . ' AND hss.`position` > ' . (int) $this->position;
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        foreach ($rows as $row) {
            $current_slide = new LeoSlideshowSlide($row['id_slide']);
            --$current_slide->position;
            $current_slide->update();
            unset($current_slide);
        }

        return true;
    }

    public function getDelay()
    {
        $temp_result = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($this->params), true);
        $result = $temp_result['delay'];

        return $result;
    }

    /**
     * System get Delay value from GROUP when SLIDER's Delay <= 0
     */
    public static function showDelay($slide_id = 0, $delay = null, $group_delay = null)
    {
        $default = 9000;

        # Get Delay form SLIDER
        if ($delay > 0) {
            # module validation
            return $delay;
        }

        if (!empty($slide_id)) {
            $mod_slide = new LeoSlideshowSlide($slide_id);
            $s_delay = $mod_slide->getDelay();
            if ($s_delay > 0) {
                # module validation
                return $s_delay;
            }
        }
        # Get Delay form GROUP
        if ($group_delay > 0) {
            # module validation
            return $group_delay;
        }

        if (!empty($slide_id)) {
            $mod_slide = new LeoSlideshowSlide($slide_id);
            $mod_group = new LeoSlideshowGroup($mod_slide->id_group);
            $g_delay = $mod_group->getDelay();
            if ($g_delay > 0) {
                # module validation
                return $g_delay;
            }
        }

        return $default;
    }

    public static function renderTarget($target = '')
    {
        $html = '_self';
        if (!empty($target)) {
            if (LeoSlideshowStatus::SLIDER_TARGET_SAME == $target) {
                # module validation
                $html = '_self';
            } elseif (LeoSlideshowStatus::SLIDER_TARGET_NEW == $target) {
                $html = '_blank';
            }
        }
        return $html;
    }

    public function mergeData($data = array())
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                # module validation
                $this->$key = $value;
            }
        }
        return $this;
    }

    public function mergeSlider($data = array())
    {
        return $this->mergeData($data);
    }

    public function mergeParams($pattern)
    {
        $params_data = array_merge($pattern, Tools::jsonDecode(LeoSlideshowSlide::base64Decode($this->params), true));
        $this->mergeData($params_data);
        return $this;
    }

    public function validate($module)
    {
        $start_timestamp = strtotime($this->start_date_time);
        $end_timestamp = strtotime($this->end_date_time);

        if ($end_timestamp == 0 && $start_timestamp == 0) {
            # module validation
            // $end_timestamp, $start_timestamp are valid
        } elseif ($end_timestamp > $start_timestamp && $end_timestamp != 0 && $start_timestamp != 0) {
            # module validation
            // $end_timestamp, $start_timestamp are valid
        } else {
            # module validation
            throw new Exception($module->l("'Start End Time' must be equal or more than 'Start Date Time'"));
        }
    }

    public function getStatusTime()
    {
        $timestamp = time();
        $start_date_time = strtotime($this->start_date_time);
        $end_date_time = strtotime($this->end_date_time);

        if ($this->active == LeoSlideshowStatus::SLIDER_STATUS_DISABLE) {
            # module validation
            return LeoSlideshowStatus::SLIDER_STATUS_DISABLE;
        }

        # NOT SET TIME
        if ($this->active == LeoSlideshowStatus::SLIDER_STATUS_ENABLE && $start_date_time == 0 && $end_date_time == 0) {
            # module validation
            return LeoSlideshowStatus::SLIDER_STATUS_ENABLE;
        }

        // HAVE SET TIME
        if ($this->active == LeoSlideshowStatus::SLIDER_STATUS_ENABLE && $start_date_time <= $timestamp && $timestamp <= $end_date_time) {
            # module validation
            return LeoSlideshowStatus::SLIDER_STATUS_ENABLE;
        }

        if ($this->active == LeoSlideshowStatus::SLIDER_STATUS_ENABLE && $timestamp < $start_date_time) {
            # module validation
            return LeoSlideshowStatus::SLIDER_STATUS_COMING;
        }

        # DEFAULT
        return LeoSlideshowStatus::SLIDER_STATUS_DISABLE;
    }

    public static function filterSlider($sliders = array(), $slider_data = array())
    {
        // echo '<pre>';
        // print_r($sliders);
        // echo '============';
        foreach ($sliders as $key => $slider) {
            $mod_slide = new LeoSlideshowSlide();
            $mod_slide->mergeSlider($slider)->mergeParams($slider_data);
            if ($mod_slide->getStatusTime() == LeoSlideshowStatus::SLIDER_STATUS_ENABLE) {
                # module validation
                # data is valid
            } else {
                unset($sliders[$key]);
                # module validation
            }
        }
        // echo '<pre>';
        // print_r($sliders);die();

        return $sliders;
    }

    public static function base64Decode($data)
    {
        return call_user_func('base64_decode', $data);
    }

    public static function base64Encode($data)
    {
        return call_user_func('base64_encode', $data);
    }

    /**
     * remove data follow language : delete layer in Deleting Language
     */
    public function completeDatabase($param = array())
    {
        if (isset($param['lang']) && !empty($param['lang'])) {
            $languages = Context::getContext()->controller->getLanguages();
            $lang_arr = array();
            foreach ($languages as $lang) {
                # module validation
                $lang_arr[] = $lang['id_lang'];
            }

            foreach ($this->layersparams as $key => $value) {
                if (!in_array($key, $lang_arr)) {
                    unset($this->title[$key]);
                    unset($this->link[$key]);
                    unset($this->image[$key]);
                    unset($this->thumbnail[$key]);
                    unset($this->video[$key]);
                    unset($this->layersparams[$key]);
                }
                # module validation
                unset($value);
            }
        }
        return $this;
    }

    public function debug()
    {
        $languages = Context::getContext()->controller->getLanguages();

        # layersparams_debug
        $this->layersparams_debug = array();
        foreach ($this->layersparams as $key => $value) {
            $lang_iso = '';
            foreach ($languages as $lang) {
                if ($lang['id_lang'] == $key) {
                    $lang_iso = $lang['iso_code'];
                }
            }
            $this->layersparams_debug['lang_id_' . $key . '_' . $lang_iso] = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($value), true);
        }
        unset($this->layersparams);

        # params_debug
        $this->params_debug = array();
        foreach ($this->params as $key => $value) {
            $lang_iso = '';
            foreach ($languages as $lang) {
                if ($lang['id_lang'] == $key) {
                    $lang_iso = $lang['iso_code'];
                }
            }
            $this->params_debug['lang_id_' . $key . '_' . $lang_iso] = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($value), true);
        }
        unset($this->params);

        # video_debug
        $this->video_debug = array();
        foreach ($this->params as $key => $value) {
            $lang_iso = '';
            foreach ($languages as $lang) {
                if ($lang['id_lang'] == $key) {
                    $lang_iso = $lang['iso_code'];
                }
            }
            $this->video_debug['lang_id_' . $key . '_' . $lang_iso] = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($value), true);
        }
        unset($this->video);
        unset($this->def);
        unset($this->fieldsRequired);
        unset($this->fieldsSize);
        unset($this->fieldsValidate);
        unset($this->fieldsRequiredLang);
        unset($this->fieldsSizeLang);
        unset($this->webserviceParameters);
        unset($this->fieldsValidateLang);
        unset($this->tables);
        return $this;
    }

    /**
     * Get Background-color, or image
     */
    public static function getBackground($group = array(), &$slider = array())
    {
        if (isset($group['background_image']) && $group['background_image'] == '1') {
            # BACKGROUND IS IMAGE
            $slider['background_type'] = 'image';
            $slider['background_color'] = '';

            if (isset($slider['image']) && $slider['image'] != '') {
                # module validation
                // get form slide
            } elseif (isset($group['background_url']) && $group['background_url'] != '') {
                # module validation
                $slider['image'] = $group['background_url']; // get image from group
            } else {
                // slide and group dont have image
                $slider['background_type'] = 'image';
                $slider['image'] = 'views/img/blank.gif';
            }
        } elseif (isset($group['background_image']) && $group['background_image'] == '0') {
            # BACKGROUND IS COLOR
            $slider['background_type'] = 'color';
            $slider['image'] = 'views/img/blank.gif';

            if (isset($slider['video']['background_color']) && $slider['video']['background_color'] != '') {
                # module validation
                // get form slide
            } elseif (isset($group['background_color']) && $group['background_color'] != '') {
                # module validation
                $slider['image'] = $group['background_color']; // get image from group
            } else {
                // Slide and Group dont have image
                $slider['background_type'] = 'image';
                $slider['image'] = 'img/blank.gif';
            }
        }
    }

    public static function showBulletCustomHTML($slide_params, $sliders)
    {
        if ($slide_params['navigator_type'] == LeoSlideshowConfig::IVIEW_NAV_BULLET && $slide_params['enable_custom_html_bullet'] == LeoSlideshowConfig::ENABLE) {
            foreach ($sliders as $key => &$slider) {
                $slider['enable_custom_html_bullet'] = LeoSlideshowConfig::ENABLE;
                $slider['bullet_class'] = $slider['params']['bullet_class'];
                unset($slider['params']['bullet_class']);
                $slider['bullet_description'] = isset($slider['video']['bullet_description']) ? $slider['video']['bullet_description'] : '';
                unset($slider['video']['bullet_description']);
                unset($key);
            }
        } else {
            foreach ($sliders as $key => &$slider) {
                # module validation
                $slider['enable_custom_html_bullet'] = LeoSlideshowConfig::DISABLE;
            }
        }
        return $sliders;
    }

    public function count()
    {
        $sql = 'SELECT id_leoslideshow_slides FROM ' . _DB_PREFIX_ . 'leoslideshow_slides';
        $groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $number_groups = count($groups);
        return $number_groups;
    }
}
