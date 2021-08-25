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

include_once(_PS_MODULE_DIR_.'leoslideshow/classes/LeoSlideshowConfig.php');
include_once(_PS_MODULE_DIR_.'leoslideshow/classes/LeoSlideshowGroup.php');
include_once(_PS_MODULE_DIR_.'leoslideshow/classes/LeoSlideshowSlide.php');
include_once(_PS_MODULE_DIR_.'leoslideshow/classes/LeoSlideshowStatus.php');
include_once(_PS_MODULE_DIR_.'leoslideshow/libs/Helper.php');

class LeoSlideshow extends Module
{
    private $html = '';
    private $current_group = array('id_group' => 0, 'title' => '');
    public $group_data = array(
        'id_leoslideshow_groups' => '',
        'title' => '',
        'id_shop' => '',
        'hook' => '',
        'active' => '',
        'auto_play' => '1',
        'delay' => '9000',
        'fullwidth' => '',
        'width' => '960',
        'height' => '350',
        'md_width' => '12',
        'sm_width' => '12',
        'xs_width' => '12',
        'touch_mobile' => '1',
        'stop_on_hover' => '1',
        'shuffle_mode' => '0',
        'image_cropping' => '0',
        'shadow_type' => '2',
        'show_time_line' => '1',
        'time_line_position' => 'top',
        'background_color' => '#d9d9d9',
        'margin' => '0px 0px 18px',
        'padding' => '5px 5px',
        'background_image' => '1',
        'background_url' => '',
        'navigator_type' => 'none',
        'navigator_arrows' => 'verticalcentered',
        'navigation_style' => 'round',
        'offset_horizontal' => '0',
        'offset_vertical' => '20',
        'show_navigator' => '0',
        'hide_navigator_after' => '200',
        'thumbnail_width' => '100',
        'thumbnail_height' => '50',
        #'thumbnail_amount' => '5',
        'group_class' => '',
        'start_with_slide' => '1',
        'timer_show' => LeoSlideshowConfig::TIMER_SHOW_AUTO,
        'timer' => '',
        'tooltipX' => '5',
        'tooltipY' => '-5',
        'controlNav' => '1',
        'controlNavTooltip' => '1',
        'nav_thumbnail_width' => '',
        'nav_thumbnail_height' => '',
        'controlNavHoverOpacity' => '0.6',
        'directionNav' => '1',
        'directionNavHoverOpacity' => '0.6',
        'keyboardNav' => '1',
        'timerPosition' => 'top-right',
        'timerX' => '10',
        'timerY' => '10',
        'timerOpacity' => '0.5',
        'timerBg' => '#000',
        'timerColor' => '#EEE',
        'timerDiameter' => '30',
        'timerPadding' => '4',
        'timerStroke' => '3',
        'timerBarStroke' => '1',
        'timerBarStrokeColor' => '#EEE',
        'blockCols' => '10',
        'timerBarStrokeStyle' => 'solid',
        'captionOpacity' => '1',
        'fx' => 'random',
        'animationSpeed' => '500',
        'strips' => '20',
        'blockRows' => '5',
        'captionSpeed' => '500',
        'enable_custom_html_bullet' => '0',
    );
    private $hook_support = array(
        'displayTop',
        'displayNav1',
        'displayNav2',
        'displayNavFullWidth',
        'displayHome',
        'displayLeftColumn',
        'displayRightColumn',
        'displayFooterProduct',
        'displayFooterBefore',
        'displayFooter',
        'displayFooterAfter',
        'displayLeftColumnProduct',
        'displayRightColumnProduct',
        'displayProductButtons',
        'displayReassurance',
    );
    private $mod_current_slide = array();
    public $slider_data = array(
        'transition' => 'random',
        'slot' => 7,
        'rotation' => 0,
        'duration' => 300,
        'delay' => '5000',
        'enable_link' => 1,
        'target' => '',
        'start_date_time' => '',
        'end_date_time' => '',
        'bullet_class' => '',
    );
    public $theme_name;
    public $img_path;
    public $img_url;
    public $error_text = '';

    public function __construct()
    {
        $this->name = 'leoslideshow';
        $this->tab = 'front_office_features';
        $this->version = '1.0.5';
        $this->author = 'LeoTheme';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Leo Slideshow for your homepage');
        $this->description = $this->l('Adds image, text or video to your homepage.');
        $this->theme_name = LeoSlideshowHelper::getThemeName();
        $this->img_path = LeoSlideshowHelper::getImgThemeDir();
        $this->img_url = LeoSlideshowHelper::getImgThemeUrl();
        // Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'leoslideshow_groups` MODIFY `hook` varchar(64) DEFAULT NULL');
        // Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'leoslideshow_groups` ADD `active_ap` tinyint(1) DEFAULT NULL');
        // Configuration::updateValue('LEOSLIDESHOW_GROUP_DEFAULTTAB', '#fieldset_0');
    }

    /**
     * @see Module::install()
     */
    public function install()
    {
        // Prepare tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminLeoSlideshow';
        $tab->name = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = 'LeoSlideShow';
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;
        
        $tab1 = new Tab();
        $tab1->active = 1;
        $tab1->class_name = 'AdminLeoSlideshowMenuModule';
        $tab1->name = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tab1->name[$lang['id_lang']] = 'Leo Slideshow Configuration';
        }
        $tab1->id_parent = Tab::getIdFromClassName('AdminParentModulesSf');
        $tab1->module = $this->name;

        Configuration::updateValue('AP_INSTALLED_LEOSLIDESHOW', '1');
        /* Adds Module */
        if ($tab->add() && $tab1->add() && parent::install() && Configuration::updateValue('LEOSLIDESHOW_GROUP_DE', '1') && Configuration::updateValue('LEOSLIDESHOW_GROUP_DEFAULTTAB', '#fieldset_0')) {
            $res = true;
            
            /* Sets up configuration */
            $res &= $this->registerLeoHook();
            /* Creates tables */
            $res &= $this->createTables();

            return (bool)$res;
        }
        return false;
    }

    /**
     * Adds samples
     */
    private function installSamples()
    {
        if ($this->checkExistAnyGroup()) {
            return true;
        }
		
         # CHANGE POSITION HOOK DISPLAYHOME TO 1
        foreach (Shop::getContextListShopID() as $shop_id) {
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_module`
                SET `position`=' . 1 . '
                WHERE `id_module` = ' . (int)$this->id . '
                AND `id_hook` = ' . (int) Hook::getIdByName('displayHome'). ' AND `id_shop` = ' . $shop_id;
            Db::getInstance()->execute($sql);
        }
		
        //insearch demo for group slider
        $mod_group = new LeoSlideshowGroup();
        $context = Context::getContext();
        //sample for group
        $mod_group->title = 'Sample Group';
        $mod_group->hook = 'displayHome';
        $mod_group->id_shop = $context->shop->id;
        $mod_group->active = 1;
        $mod_group->params = 'eyJkZWxheSI6IjkwMDAiLCJzdGFydF93aXRoX3NsaWRlIjoiMSIsInRpbWVyX3Nob3ciOiIzIiwic3RvcF9vbl9ob3ZlciI6IjEiLCJzaHVmZmxlX21vZGUiOiIwIiwiZnVsbHdpZHRoIjoiZnVsbHdpZHRoIiwibWRfd2lkdGgiOiIxMiIsInNtX3dpZHRoIjoiMTIiLCJ4c193aWR0aCI6IjEyIiwiaW1hZ2VfY3JvcHBpbmciOiIwIiwid2lkdGgiOiIxMDkwIiwiaGVpZ2h0IjoiNTQwIiwibWFyZ2luIjoiMHB4IDBweCAxOHB4IiwicGFkZGluZyI6IjVweCA1cHgiLCJiYWNrZ3JvdW5kX2ltYWdlIjoiMSIsImJhY2tncm91bmRfdXJsIjoiIiwiYmFja2dyb3VuZF9jb2xvciI6IiNkOWQ5ZDkiLCJncm91cF9jbGFzcyI6IiIsImNvbnRyb2xOYXYiOiIxIiwibmF2aWdhdG9yX3R5cGUiOiJidWxsZXQiLCJlbmFibGVfY3VzdG9tX2h0bWxfYnVsbGV0IjoiMCIsImNvbnRyb2xOYXZUb29sdGlwIjoiMCIsInRvb2x0aXBYIjoiNSIsInRvb2x0aXBZIjoiLTUiLCJuYXZfdGh1bWJuYWlsX3dpZHRoIjoiIiwibmF2X3RodW1ibmFpbF9oZWlnaHQiOiIiLCJjb250cm9sTmF2SG92ZXJPcGFjaXR5IjoiMC42IiwiZGlyZWN0aW9uTmF2IjoiMSIsImRpcmVjdGlvbk5hdkhvdmVyT3BhY2l0eSI6IjAuNiIsImtleWJvYXJkTmF2IjoiMSIsInRpbWVyIjoiMzYwQmFyIiwidGltZXJQb3NpdGlvbiI6InRvcC1yaWdodCIsInRpbWVyWCI6IjEwIiwidGltZXJZIjoiMTAiLCJ0aW1lck9wYWNpdHkiOiIwLjUiLCJ0aW1lckJnIjoiIzAwMCIsInRpbWVyQ29sb3IiOiIjRUVFIiwidGltZXJEaWFtZXRlciI6IjMwIiwidGltZXJQYWRkaW5nIjoiNCIsInRpbWVyU3Ryb2tlIjoiMyIsInRpbWVyQmFyU3Ryb2tlIjoiMSIsInRpbWVyQmFyU3Ryb2tlQ29sb3IiOiIjRUVFIiwidGltZXJCYXJTdHJva2VTdHlsZSI6InNvbGlkIiwiZngiOiJyYW5kb20iLCJhbmltYXRpb25TcGVlZCI6IjUwMCIsInN0cmlwcyI6IjIwIiwiYmxvY2tDb2xzIjoiMTAiLCJibG9ja1Jvd3MiOiI1IiwiY2FwdGlvblNwZWVkIjoiNTAwIiwiY2FwdGlvbk9wYWNpdHkiOiIxIn0=';
        $mod_group->active_ap = '0';
        $mod_group->randkey = '1a9436d12827c870d10cc0d9b19b2c00';
        $mod_group->add();

        //sample for slider
        $languages = Language::getLanguages(false);

        $mod_slide = new LeoSlideshowSlide();
        $mod_slide->id_group = $mod_group->id;
        $mod_slide->position = 0;
        $mod_slide->active = 1;
        $mod_slide->params = 'eyJkZWxheSI6IjUwMDAiLCJncm91cF9pZCI6IjEiLCJlbmFibGVfbGluayI6IjEiLCJ0YXJnZXQiOiJzYW1lIiwic3RhcnRfZGF0ZV90aW1lIjoiIiwiZW5kX2RhdGVfdGltZSI6IiIsImJ1bGxldF9jbGFzcyI6IiJ9';
        # 1
        foreach ($languages as $language) {
            $mod_slide->title[$language['id_lang']] = 'Sample slider 1';
            $mod_slide->link[$language['id_lang']] = '';
            $mod_slide->image[$language['id_lang']] = 'sample_slider_1.png';
            $mod_slide->thumbnail[$language['id_lang']] = '';
            $mod_slide->video[$language['id_lang']] = 'eyJ1c2V2aWRlbyI6IjAiLCJ2aWRlb2lkIjoiIiwidmlkZW9hdXRvIjoiMCIsImJhY2tncm91bmRfY29sb3IiOiIiLCJidWxsZXRfZGVzY3JpcHRpb24iOiIifQ==';
            $mod_slide->layersparams[$language['id_lang']] = 'W3sibGF5ZXJfdmlkZW9fdHlwZSI6InlvdXR1YmUiLCJsYXllcl92aWRlb19pZCI6IiIsImxheWVyX3ZpZGVvX2hlaWdodCI6IjIwMCIsImxheWVyX3ZpZGVvX3dpZHRoIjoiMzAwIiwibGF5ZXJfdmlkZW9fdGh1bWIiOiIiLCJsYXllcl9pZCI6IjFfMSIsImxheWVyX2NvbnRlbnQiOiIiLCJsYXllcl90eXBlIjoidGV4dCIsImxheWVyX3N0YXR1cyI6IjEiLCJsYXllcl9jbGFzcyI6ImxhcmdlX3RleHQiLCJsYXllcl9jYXB0aW9uIjoiRnVsbHkgUmVzcG9uc2l2ZSBTb2x1dGlvbiIsImxheWVyX2ZvbnRfc2l6ZSI6IiIsImxheWVyX2JhY2tncm91bmRfY29sb3IiOiIiLCJsYXllcl9jb2xvciI6IiIsImxheWVyX2xpbmsiOiIiLCJsYXllcl90YXJnZXQiOiJzYW1lIiwibGF5ZXJfdG9wIjoiMTY5IiwibGF5ZXJfbGVmdCI6IjAiLCJsYXllcl90cmFuc2l0aW9uIjoid2lwZWxlZnQiLCJ0aW1lX3N0YXJ0IjpudWxsfSx7ImxheWVyX3ZpZGVvX3R5cGUiOiJ5b3V0dWJlIiwibGF5ZXJfdmlkZW9faWQiOiIiLCJsYXllcl92aWRlb19oZWlnaHQiOiIyMDAiLCJsYXllcl92aWRlb193aWR0aCI6IjMwMCIsImxheWVyX3ZpZGVvX3RodW1iIjoiIiwibGF5ZXJfaWQiOiIxXzIiLCJsYXllcl9jb250ZW50IjoiIiwibGF5ZXJfdHlwZSI6InRleHQiLCJsYXllcl9zdGF0dXMiOiIxIiwibGF5ZXJfY2xhc3MiOiJzbWFsbF90ZXh0IiwibGF5ZXJfY2FwdGlvbiI6IldvcmtzIG9uIERlc2t0b3BzLCBOb3RlYm9va3MsIFRhYmxldHMgX0FTTV8gU21hcnRwaG9uZXMiLCJsYXllcl9mb250X3NpemUiOiIiLCJsYXllcl9iYWNrZ3JvdW5kX2NvbG9yIjoiIiwibGF5ZXJfY29sb3IiOiIiLCJsYXllcl9saW5rIjoiIiwibGF5ZXJfdGFyZ2V0Ijoic2FtZSIsImxheWVyX3RvcCI6IjIyOSIsImxheWVyX2xlZnQiOiIwIiwibGF5ZXJfdHJhbnNpdGlvbiI6IndpcGVyaWdodCIsInRpbWVfc3RhcnQiOm51bGx9LHsibGF5ZXJfdmlkZW9fdHlwZSI6InlvdXR1YmUiLCJsYXllcl92aWRlb19pZCI6IiIsImxheWVyX3ZpZGVvX2hlaWdodCI6IjIwMCIsImxheWVyX3ZpZGVvX3dpZHRoIjoiMzAwIiwibGF5ZXJfdmlkZW9fdGh1bWIiOiIiLCJsYXllcl9pZCI6IjFfMyIsImxheWVyX2NvbnRlbnQiOiIiLCJsYXllcl90eXBlIjoidGV4dCIsImxheWVyX3N0YXR1cyI6IjEiLCJsYXllcl9jbGFzcyI6ImJpZ19ibGFjayIsImxheWVyX2NhcHRpb24iOiJTaG93IG5vdyIsImxheWVyX2ZvbnRfc2l6ZSI6IiIsImxheWVyX2JhY2tncm91bmRfY29sb3IiOiIiLCJsYXllcl9jb2xvciI6IiIsImxheWVyX2xpbmsiOiIiLCJsYXllcl90YXJnZXQiOiJzYW1lIiwibGF5ZXJfdG9wIjoiMjk3IiwibGF5ZXJfbGVmdCI6IjAiLCJsYXllcl90cmFuc2l0aW9uIjoid2lwZWRvd24iLCJ0aW1lX3N0YXJ0IjpudWxsfV0=';
        }
        $mod_slide->add();
        
        #2
        $mod_slide = new LeoSlideshowSlide();
        $mod_slide->id_group = $mod_group->id;
        $mod_slide->position = 0;
        $mod_slide->active = 1;
        $mod_slide->params = 'eyJkZWxheSI6IjUwMDAiLCJncm91cF9pZCI6IjEiLCJlbmFibGVfbGluayI6IjEiLCJ0YXJnZXQiOiJzYW1lIiwic3RhcnRfZGF0ZV90aW1lIjoiIiwiZW5kX2RhdGVfdGltZSI6IiIsImJ1bGxldF9jbGFzcyI6IiJ9';

        foreach ($languages as $language) {
            $mod_slide->title[$language['id_lang']] = 'Sample slider 2';
            $mod_slide->link[$language['id_lang']] = '';
            $mod_slide->image[$language['id_lang']] = 'sample_slider_2.jpg';
            $mod_slide->thumbnail[$language['id_lang']] = '';
            $mod_slide->video[$language['id_lang']] = 'eyJ1c2V2aWRlbyI6IjAiLCJ2aWRlb2lkIjoiIiwidmlkZW9hdXRvIjoiMCIsImJhY2tncm91bmRfY29sb3IiOiIiLCJidWxsZXRfZGVzY3JpcHRpb24iOiIifQ==';
            $mod_slide->layersparams[$language['id_lang']] = 'W3sibGF5ZXJfdmlkZW9fdHlwZSI6InlvdXR1YmUiLCJsYXllcl92aWRlb19pZCI6IiIsImxheWVyX3ZpZGVvX2hlaWdodCI6IjIwMCIsImxheWVyX3ZpZGVvX3dpZHRoIjoiMzAwIiwibGF5ZXJfdmlkZW9fdGh1bWIiOiIiLCJsYXllcl9pZCI6IjFfMSIsImxheWVyX2NvbnRlbnQiOiIiLCJsYXllcl90eXBlIjoidGV4dCIsImxheWVyX3N0YXR1cyI6IjEiLCJsYXllcl9jbGFzcyI6ImxhcmdlX3RleHQiLCJsYXllcl9jYXB0aW9uIjoiU2xpZGVzaG93IiwibGF5ZXJfZm9udF9zaXplIjoiIiwibGF5ZXJfYmFja2dyb3VuZF9jb2xvciI6IiIsImxheWVyX2NvbG9yIjoiIiwibGF5ZXJfbGluayI6IiIsImxheWVyX3RhcmdldCI6InNhbWUiLCJsYXllcl90b3AiOiIxNTgiLCJsYXllcl9sZWZ0IjoiMCIsImxheWVyX3RyYW5zaXRpb24iOiJ3aXBlbGVmdCIsInRpbWVfc3RhcnQiOm51bGx9LHsibGF5ZXJfdmlkZW9fdHlwZSI6InlvdXR1YmUiLCJsYXllcl92aWRlb19pZCI6IiIsImxheWVyX3ZpZGVvX2hlaWdodCI6IjIwMCIsImxheWVyX3ZpZGVvX3dpZHRoIjoiMzAwIiwibGF5ZXJfdmlkZW9fdGh1bWIiOiIiLCJsYXllcl9pZCI6IjFfMiIsImxheWVyX2NvbnRlbnQiOiIiLCJsYXllcl90eXBlIjoidGV4dCIsImxheWVyX3N0YXR1cyI6IjEiLCJsYXllcl9jbGFzcyI6InNtYWxsX3RleHQiLCJsYXllcl9jYXB0aW9uIjoiVGV4dCwgSW1hZ2UsIFZpZGVvLCBTaG9ydGNvZGUsIEhUTUwgQ29udGVudCBMYXllcnMiLCJsYXllcl9mb250X3NpemUiOiIiLCJsYXllcl9iYWNrZ3JvdW5kX2NvbG9yIjoiIiwibGF5ZXJfY29sb3IiOiIiLCJsYXllcl9saW5rIjoiIiwibGF5ZXJfdGFyZ2V0Ijoic2FtZSIsImxheWVyX3RvcCI6IjIxOSIsImxheWVyX2xlZnQiOiIwIiwibGF5ZXJfdHJhbnNpdGlvbiI6IndpcGVyaWdodCIsInRpbWVfc3RhcnQiOm51bGx9LHsibGF5ZXJfdmlkZW9fdHlwZSI6InlvdXR1YmUiLCJsYXllcl92aWRlb19pZCI6IiIsImxheWVyX3ZpZGVvX2hlaWdodCI6IjIwMCIsImxheWVyX3ZpZGVvX3dpZHRoIjoiMzAwIiwibGF5ZXJfdmlkZW9fdGh1bWIiOiIiLCJsYXllcl9pZCI6IjFfMyIsImxheWVyX2NvbnRlbnQiOiIiLCJsYXllcl90eXBlIjoidGV4dCIsImxheWVyX3N0YXR1cyI6IjEiLCJsYXllcl9jbGFzcyI6ImJpZ19ibGFjayIsImxheWVyX2NhcHRpb24iOiJTaG93IG5vdyIsImxheWVyX2ZvbnRfc2l6ZSI6IiIsImxheWVyX2JhY2tncm91bmRfY29sb3IiOiIiLCJsYXllcl9jb2xvciI6IiIsImxheWVyX2xpbmsiOiIiLCJsYXllcl90YXJnZXQiOiJzYW1lIiwibGF5ZXJfdG9wIjoiMjcyIiwibGF5ZXJfbGVmdCI6IjAiLCJsYXllcl90cmFuc2l0aW9uIjoid2lwZXVwIiwidGltZV9zdGFydCI6bnVsbH1d';
        }
        $mod_slide->add();
        
        #3
        $mod_slide = new LeoSlideshowSlide();
        $mod_slide->id_group = $mod_group->id;
        $mod_slide->position = 0;
        $mod_slide->active = 1;
        $mod_slide->params = 'eyJkZWxheSI6IjUwMDAiLCJncm91cF9pZCI6IjEiLCJlbmFibGVfbGluayI6IjEiLCJ0YXJnZXQiOiJzYW1lIiwic3RhcnRfZGF0ZV90aW1lIjoiIiwiZW5kX2RhdGVfdGltZSI6IiIsImJ1bGxldF9jbGFzcyI6IiJ9';

        foreach ($languages as $language) {
            $mod_slide->title[$language['id_lang']] = 'Sample slider 3';
            $mod_slide->link[$language['id_lang']] = '';
            $mod_slide->image[$language['id_lang']] = 'sample_slider_3.png';
            $mod_slide->thumbnail[$language['id_lang']] = '';
            $mod_slide->video[$language['id_lang']] = 'eyJ1c2V2aWRlbyI6IjAiLCJ2aWRlb2lkIjoiIiwidmlkZW9hdXRvIjoiMCIsImJhY2tncm91bmRfY29sb3IiOiIiLCJidWxsZXRfZGVzY3JpcHRpb24iOiIifQ==';
            $mod_slide->layersparams[$language['id_lang']] = 'W3sibGF5ZXJfdmlkZW9fdHlwZSI6InlvdXR1YmUiLCJsYXllcl92aWRlb19pZCI6IiIsImxheWVyX3ZpZGVvX2hlaWdodCI6IjIwMCIsImxheWVyX3ZpZGVvX3dpZHRoIjoiMzAwIiwibGF5ZXJfdmlkZW9fdGh1bWIiOiIiLCJsYXllcl9pZCI6IjFfMSIsImxheWVyX2NvbnRlbnQiOiIiLCJsYXllcl90eXBlIjoidGV4dCIsImxheWVyX3N0YXR1cyI6IjEiLCJsYXllcl9jbGFzcyI6ImxhcmdlX3RleHQiLCJsYXllcl9jYXB0aW9uIjoiVHJ1ZSBNdWx0aS1NZWRpYSBDb250ZW50IiwibGF5ZXJfZm9udF9zaXplIjoiIiwibGF5ZXJfYmFja2dyb3VuZF9jb2xvciI6IiIsImxheWVyX2NvbG9yIjoiIiwibGF5ZXJfbGluayI6IiIsImxheWVyX3RhcmdldCI6InNhbWUiLCJsYXllcl90b3AiOiIxNTQiLCJsYXllcl9sZWZ0IjoiMCIsImxheWVyX3RyYW5zaXRpb24iOiJ3aXBlbGVmdCIsInRpbWVfc3RhcnQiOm51bGx9LHsibGF5ZXJfdmlkZW9fdHlwZSI6InlvdXR1YmUiLCJsYXllcl92aWRlb19pZCI6IiIsImxheWVyX3ZpZGVvX2hlaWdodCI6IjIwMCIsImxheWVyX3ZpZGVvX3dpZHRoIjoiMzAwIiwibGF5ZXJfdmlkZW9fdGh1bWIiOiIiLCJsYXllcl9pZCI6IjFfMiIsImxheWVyX2NvbnRlbnQiOiIiLCJsYXllcl90eXBlIjoidGV4dCIsImxheWVyX3N0YXR1cyI6IjEiLCJsYXllcl9jbGFzcyI6InNtYWxsX3RleHQiLCJsYXllcl9jYXB0aW9uIjoiSFRNTCA1LCBZb3V0dWJlIF9BU01fIFZpbWVvIFZpZGVvIFN1cHBvcnQiLCJsYXllcl9mb250X3NpemUiOiIiLCJsYXllcl9iYWNrZ3JvdW5kX2NvbG9yIjoiIiwibGF5ZXJfY29sb3IiOiIiLCJsYXllcl9saW5rIjoiIiwibGF5ZXJfdGFyZ2V0Ijoic2FtZSIsImxheWVyX3RvcCI6IjIwOCIsImxheWVyX2xlZnQiOiIwIiwibGF5ZXJfdHJhbnNpdGlvbiI6IndpcGVyaWdodCIsInRpbWVfc3RhcnQiOm51bGx9LHsibGF5ZXJfdmlkZW9fdHlwZSI6InlvdXR1YmUiLCJsYXllcl92aWRlb19pZCI6IiIsImxheWVyX3ZpZGVvX2hlaWdodCI6IjIwMCIsImxheWVyX3ZpZGVvX3dpZHRoIjoiMzAwIiwibGF5ZXJfdmlkZW9fdGh1bWIiOiIiLCJsYXllcl9pZCI6IjFfMyIsImxheWVyX2NvbnRlbnQiOiIiLCJsYXllcl90eXBlIjoidGV4dCIsImxheWVyX3N0YXR1cyI6IjEiLCJsYXllcl9jbGFzcyI6ImJpZ19ibGFjayIsImxheWVyX2NhcHRpb24iOiJTaG93IG5vdyIsImxheWVyX2ZvbnRfc2l6ZSI6IiIsImxheWVyX2JhY2tncm91bmRfY29sb3IiOiIiLCJsYXllcl9jb2xvciI6IiIsImxheWVyX2xpbmsiOiIiLCJsYXllcl90YXJnZXQiOiJzYW1lIiwibGF5ZXJfdG9wIjoiMjYzIiwibGF5ZXJfbGVmdCI6IjAiLCJsYXllcl90cmFuc2l0aW9uIjoid2lwZWxlZnQiLCJ0aW1lX3N0YXJ0IjpudWxsfV0=';
        }
        $mod_slide->add();
        
        #COPY IMAGE TO THEME
        if (!is_dir(LeoSlideshowHelper::getImgThemeDir())) {
            mkdir(LeoSlideshowHelper::getImgThemeDir(), 0755, true);
        }
        $theme_dir = _PS_ROOT_DIR_.'/themes/'.LeoSlideshowHelper::getThemeName().'/';
        $module_dir = _PS_MODULE_DIR_.'leoslideshow/';
        include_once(_PS_MODULE_DIR_.'leoslideshow/libs/phpcopy.php');
        PhpCopy::safeCopy($module_dir.'views/img/demo/', $theme_dir.'assets/img/modules/leoslideshow');

        return true;
    }

    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminLeoSlideshow');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        $id_tab = (int)Tab::getIdFromClassName('AdminLeoSlideshowMenuModule');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        Configuration::deleteByName('LEOSLIDESHOW_GROUP_DEFAULTTAB');
        Configuration::deleteByName('LEOSLIDESHOW_GROUP_DE');
        
        /* Deletes Module */
        if (parent::uninstall() && $this->unregisterLeoHook()) {
            /* Deletes tables */
            $res = $this->deleteTables();
            return $res;
        }
        return false;
    }

    /**
     * Creates tables
     */
    protected function createTables()
    {
        if ($this->_installDataSample()) {
            return true;
        }
        /* Group */
        $res = (bool)Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->name.'_groups` (
                `id_'.$this->name.'_groups` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                `title` varchar(255) NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                                `hook` varchar(64) DEFAULT NULL,
                                `active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                                `params` text NOT NULL,
                                `active_ap` tinyint(1) DEFAULT NULL,
                                `randkey` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id_'.$this->name.'_groups`, `id_shop`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');

        /* Slides configuration */
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->name.'_slides` (
              `id_'.$this->name.'_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `id_group` int(11) NOT NULL,
              `position` int(10) unsigned NOT NULL DEFAULT \'0\',
              `active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                          `params` text NOT NULL,
              PRIMARY KEY (`id_'.$this->name.'_slides`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');

        /* Slides lang configuration */
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->name.'_slides_lang` (
              `id_'.$this->name.'_slides` int(10) unsigned NOT NULL,
              `id_lang` int(10) unsigned NOT NULL,
              `title` varchar(255) NOT NULL,
              `link` varchar(255) NOT NULL,
              `image` varchar(255) NOT NULL,
              `thumbnail` varchar(255) NOT NULL,
              `video` text,
              `layersparams` text,
              PRIMARY KEY (`id_'.$this->name.'_slides`,`id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');
        $res &= $this->installSamples();
        return $res;
    }

    private function _installDataSample()
    {
        if (!file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            return false;
        }
        require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');

        $data_sample_class = 'Datasample';

        $sample = new $data_sample_class(1);
        return $sample->processImport($this->name);
    }

    /**
     * deletes tables
     */
    protected function deleteTables()
    {
        //return true;
        return Db::getInstance()->execute('
          DROP TABLE IF EXISTS `'._DB_PREFIX_.$this->name.'_groups`, `'._DB_PREFIX_.$this->name.'_slides`, `'._DB_PREFIX_.$this->name.'_slides_lang`;
        ');
    }
    
    public function postProcess()
    {
        if (count($this->errors) > 0) {
            $this->ajax = Tools::getValue('ajax') || Tools::isSubmit('ajax');
            if ($this->ajax) {
                $array = array('hasError' => true, 'errors' => $this->errors[0]);
                die(Tools::jsonEncode($array));
            }
            return;
        }
        
        if (Tools::isSubmit('importGroup')) {
            $this->importGroup();
        }

        if (Tools::getIsset('leoajax') && Tools::getValue('leoajax') == 1) {
            $this->slideProcessAjax();
        }

        if (Tools::getIsset('correctGroup') && Tools::getValue('correctGroup')) {
            $this->correctDataGroup();
        }

        if (Tools::getIsset('copylang') && Tools::getValue('copylang')) {
            $this->copyLang();
        }

        //  Copy js, css, image, tpl
        if (Tools::getIsset('leo_copy_lib_to_theme') && Tools::getValue('leo_copy_lib_to_theme') == 1) {
            LeoSlideshowHelper::copyToTheme();
        }

        //action for group
        if (Tools::isSubmit('editgroup') || Tools::isSubmit('submitGroup') || Tools::isSubmit('deletegroup') || Tools::isSubmit('addNewGroup') || Tools::isSubmit('exportgroup') || Tools::isSubmit('changeGStatus')) {
            if (Tools::isSubmit('submitGroup') || Tools::isSubmit('deletegroup') || Tools::isSubmit('changeGStatus')) {
                if ($this->postValidation()) {
                    $this->_postProcess();
                }
            }
            //save group id in config to edit in next time when open module
            if (Tools::isSubmit('submitGroup') || Tools::isSubmit('editgroup') || Tools::isSubmit('changeGStatus')) {
                Configuration::updateValue('LEOSLIDESHOW_GROUP_DE', (int)Tools::getValue('id_group'));
            }
        } elseif (Tools::isSubmit('showsliders') || Tools::isSubmit('submitSlider') || Tools::isSubmit('editSlider') || Tools::isSubmit('deleteSlider') || Tools::isSubmit('addNewSlider') || Tools::isSubmit('changeStatus')) {
            # Add new Slide
            if (Tools::isSubmit('submitSlider') || Tools::isSubmit('deleteSlider') || Tools::isSubmit('changeStatus') || Tools::getValue('duplicateSlider')) {
                if ($this->postValidation()) {
                    $this->_postProcess();
                }
            }
        }
        
        //export process
        if (Tools::getValue('exportGroup')) {
            $this->exportGroup();
        }
    }
    
    public function getContent()
    {
        
        $this->errors = array();
        if (!$this->access('configure')) {
            $this->errors[] = $this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error');
            $this->html .= $this->displayError($this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error'));
        }
        
        $this->postProcess();
        
        $media_dir = $this->getMediaDir();
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/jquery-validation-1.9.0/jquery.validate.js');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/show.js');
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/jquery-validation-1.9.0/screen.css');

        $this->html .= $this->headerHTML();

        //action for group
        if (Tools::isSubmit('editgroup') || Tools::isSubmit('submitGroup') || Tools::isSubmit('deletegroup') || Tools::isSubmit('addNewGroup') || Tools::isSubmit('exportgroup') || Tools::isSubmit('changeGStatus')) {
            $this->html .= $this->renderGroupList();
            $this->html .= $this->renderGroupConfig();
        } elseif (Tools::isSubmit('showsliders') || Tools::isSubmit('submitSlider') || Tools::isSubmit('editSlider') || Tools::isSubmit('deleteSlider') || Tools::isSubmit('addNewSlider') || Tools::isSubmit('changeStatus')) {
            # Add new Slide
            $this->html .= $this->renderList();
            $this->html .= $this->renderConfig();
            $this->html .= $this->renderSliderForm();
        } else {
            # Action for slideshow
            $this->html .= $this->renderGroupList();
            $this->html .= $this->renderGroupConfig();
        }

        return $this->html;
    }

    public function getAllSlides()
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'leoslideshow_groups gr
                    WHERE gr.id_shop = '.(int)$id_shop.' AND gr.randkey IS NOT NULL AND gr.randkey != "" '
                . 'ORDER BY gr.id_leoslideshow_groups AND gr.randkey IS NOT NULL';
        
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * this function is only for developer of leotheme.com
     * to correct data for group + slider
     */
    public function correctDataGroup()
    {
        $id_group = Tools::getValue('id_group');
        if ($id_group) {
            $mod_group = new LeoSlideshowGroup($id_group);

            if (Validate::isLoadedObject($mod_group)) {
                //correct group data
                $params = Tools::unSerialize($mod_group->params);
                if ($params) {
                    $mod_group->params = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($params));
                    $mod_group->save();
                }

                //correct slider
                $sliders = $this->getSlides($mod_group->id);
                foreach ($sliders as $slider) {
                    $mod_slide = new LeoSlideshowSlide($slider['id_slide']);
                    if (Validate::isLoadedObject($mod_slide)) {
                        $tmp = Tools::unSerialize($mod_slide->params);
                        if ($tmp) {
                            $mod_slide->params = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($tmp));
                        }

                        $tmp_slide = array();
                        foreach ($mod_slide->video as $key => $value) {
                            $tmp = Tools::unSerialize($value);
                            if ($tmp) {
                                $tmp_slide[$key] = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($tmp));
                            }
                        }
                        if ($tmp_slide) {
                            $mod_slide->video = $tmp_slide;
                        }

                        $tmp_slide = array();
                        foreach ($mod_slide->layersparams as $key => $value) {
                            $tmp = Tools::unSerialize($value);
                            if ($tmp) {
                                $tmp_slide[$key] = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($tmp));
                            }
                        }
                        if ($tmp_slide) {
                            $mod_slide->layersparams = $tmp_slide;
                        }
                        //print_r($sliderObj);die;
                        $mod_slide->save();
                    }
                }
            }
        }
    }

    public function copyLang()
    {
        $id_group = Tools::getValue('id_group');
        if ($id_group) {
            $sliders = $this->getSlides($id_group);
            $mod_slide = new LeoSlideshowSlide();
            $defined = $mod_slide->getDefinition($mod_slide);
            $defined = $defined['fields'];

            foreach ($sliders as $slider) {
                $mod_slide = new LeoSlideshowSlide($slider['id_slide']);
                if ($mod_slide->id) {
                    $languages = Language::getLanguages(false);
                    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

                    $tmp = array();
                    foreach ($languages as $language) {
                        if ($language['id_lang'] == $default_lang) {
                            # module validation
                            foreach ($defined as $key => $val) {
                                # module validation
                                if (isset($val['lang']) && $val['lang'] == 1) {
                                    # module validation
                                    $tmp[$key] = $mod_slide->{$key}[$default_lang];
                                }
                            }
                            break;
                        }
                    }

                    foreach ($languages as $language) {
                        if ($language['id_lang'] != $default_lang) {
                            foreach ($tmp as $key => $val) {
                                if ($key == 'layersparams') {
                                    $layers_params = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($val), true);
                                    if (is_array($layers_params)) {
                                        // PARAMS IS VALID : NOT NULL, NOT EMPTY
                                        foreach ($layers_params as &$layer) {
                                            # module validation
                                            $layer['layer_id'] = str_replace($default_lang.'_', $language['id_lang'].'_', $layer['layer_id']);
                                        }
                                        $mod_slide->layersparams[$language['id_lang']] = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($layers_params));
                                    }
                                } else {
                                    # module validation
                                    $mod_slide->{$key}[$language['id_lang']] = $val;
                                }
                            }
                        }
                    }
                    $mod_slide->update();
                }
            }
        }
        $this->html .= $this->displayConfirmation($this->l('Copy language was successful'));
    }

    public function importGroup()
    {
        include_once(_PS_MODULE_DIR_.'leoslideshow/controllers/admin/AdminLeoSlideshow.php');
        $controller_slide = new AdminLeoSlideshowController();
        $res = $controller_slide->importGroup();
        if (!$res) {
            # module validation
            $this->html .= $this->displayError('The file could not be import.');
        } else {
            # module validation
            $this->html .= $this->displayConfirmation($this->l('Importing was successful'));
        }
    }

    public function renderList()
    {
        //get curent slider data
        if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide'))) {
            # module validation
            $this->mod_current_slide = new LeoSlideshowSlide((int)Tools::getValue('id_slide'));
        } else {
            # module validation
            $this->mod_current_slide = new LeoSlideshowSlide();
        }

        $slides = $this->getSlides(Tools::getValue('id_group'));
        foreach ($slides as $key => $slide) {
            $slides[$key]['status'] = $this->displayStatus($slide['id_slide'], $slide['active'], $slide['id_group'], $slide);
        }

        $mod_group = new LeoSlideshowGroup((int)Tools::getValue('id_group'));
        $id_shop = $this->context->shop->id;

        if ($id_shop != $mod_group->id_shop) {
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        }
        $this->group_data = array_merge($this->group_data, Tools::jsonDecode(LeoSlideshowSlide::base64Decode($mod_group->params), true));
//        $arrayParam['secure_key'] = $this->secure_key;

        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            'slides' => $slides,
            'id_group' => Tools::getValue('id_group'),
            'group_title' => $mod_group->title,
            'languages' => $this->context->controller->getLanguages(),
            'previewLink' => Context::getContext()->link->getModuleLink($this->name, 'preview', array('secure_key' => $this->secure_key)),
            'msecure_key' => $this->secure_key,
            'currentSliderID' => $this->mod_current_slide->id,
            'leo_slider_list_link' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&leoajax=1',
        ));

        return $this->display(__FILE__, 'slide_list.tpl');
    }

    /**
     * return group config form
     */
    public function renderConfig()
    {
        $description = $this->l('Add New Slider');

        if (!Tools::isSubmit('deleteSlider') && !Tools::isSubmit('addNewSlider') && !Tools::isSubmit('showsliders')) {
            $description = $this->l('You are editting slider:').' '.$this->mod_current_slide->title[$this->context->language->id];
        }


        //$fullWidthVideo = array(array('id'=>0,'name'=>$this->l('No')),array('id'=>'youtube','name'=>'Youtube'),array('id'=>'vimeo','name'=>'Vimeo'));
        //general config
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $description,
                    'icon' => 'icon-cogs'
                ),
                //'description' =>$description,
                'input' => array(
                    array(
                        'type' => 'slider_button',
                        'name' => 'slider_button',
                        'lang' => false,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Slider Title'),
                        'name' => 'title',
                        'class' => 'slider-title',
                        'required' => 1,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable'),
                        'name' => 'active_slide',
                        'is_bool' => true,
                        'values' => $this->getSwitchValue('active'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Delay'),
                        'name' => 'slider[delay]',
                        'class' => 'fixed-width-xl digits',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Group'),
                        'name' => 'slider[group_id]',
                        'options' => array(
                            'query' => LeoSlideshowGroup::getGroupOption(),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Link'),
                        'name' => 'slider[enable_link]',
                        'is_bool' => true,
                        'lang' => true,
                        'values' => $this->getSwitchValue('enable_link'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link'),
                        'name' => 'link',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Link Open in'),
                        'name' => 'slider[target]',
                        'options' => array(
                            'query' => LeoSlideshowStatus::getInstance()->getSliderTargetOption(),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Start Date Time'),
                        'name' => 'slider[start_date_time]',
                        'lang' => false,
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Start End Time'),
                        'name' => 'slider[end_date_time]',
                        'lang' => false,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Bullet Number Class'),
                        'name' => 'slider[bullet_class]',
                        'lang' => false,
                        'desc' => $this->l('Setting CSS Class  for bullet, and must be enabled "Custom HTML Enable" in Group.'),
                        'class' => 'fixed-width-xl',
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Bullet Description'),
                        'name' => 'bullet_description',
                        'rows' => 5,
                        'cols' => 60,
                        'lang' => true,
                        'desc' => $this->l('Showing Custom HTML in bullet, and must be enabled "Custom HTML Enable" in Group.'),
                    ),
                    //thumb + main image
                    array(
                        'type' => 'file_lang',
                        'label' => $this->l('Thumbnail'),
                        'name' => 'thumbnail',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'video_config',
                        'label' => $this->l('Video'),
                        'name' => 'slider[video]',
                        'lang' => true,
                    )
                )
            ),
        );

        if (Tools::getValue('id_slide') && $this->slideExists((int)Tools::getValue('id_slide'))) {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_slide');
        }

        $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_group');


        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->name_controller = 'slideshow';
        $id_langs = Language::getIDs(false);
        $lang = new Language($id_langs['0']);
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSlider';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $fields_value = $this->getSliderFieldsValues();
        # Image External Link
        if(isset($fields_value['thumbnail']) && $fields_value['thumbnail']){
            foreach ($this->context->controller->getLanguages() as $lang) {
                $id_lang = $lang['id_lang'];
                if ((!(Tools::substr($fields_value['thumbnail'][$id_lang], 0, 7) == 'http://')) && (!(Tools::substr($fields_value['thumbnail'][$id_lang], 0, 8) == 'https://'))) {

                    $fields_value['thumbnail'][$id_lang] = $this->img_url . $fields_value['thumbnail'][$id_lang];
                }
            }
        }
        
        $helper->tpl_vars = array(
            'fields_value' => $this->getSliderFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'sliderGroup' => $this->group_data,
            'psBaseModuleUri' => $this->img_url,
            'color' => true
        );

        return $helper->generateForm(array($fields_form));
    }

    /**
     * generate data
     */
    public function getSliderFieldsValues()
    {
        $fields = array();
        $slide = $this->mod_current_slide;

        if (isset($this->mod_current_slide->id) && $this->mod_current_slide->id) {
            $fields['id_slide'] = (int)$this->mod_current_slide->id;
            $slide = $this->mod_current_slide;
            $this->slider_data = array_merge($this->slider_data, Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slide->params), true));
        }

        $fields['active_slide'] = Tools::getValue('active_slide', $slide->active);
        $fields['has_picture'] = true;
        $fields['id_group'] = Tools::getValue('id_group', $slide->id_group);

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $fields['image'][$lang['id_lang']] = Tools::getValue('image_'.(int)$lang['id_lang'], $slide->image[$lang['id_lang']]);
            $fields['thumbnail'][$lang['id_lang']] = Tools::getValue('thumbnail_'.(int)$lang['id_lang'], $slide->thumbnail[$lang['id_lang']]);
            $fields['title'][$lang['id_lang']] = Tools::getValue('title_'.(int)$lang['id_lang'], $slide->title[$lang['id_lang']]);
            $fields['link'][$lang['id_lang']] = Tools::getValue('link_'.(int)$lang['id_lang'], $slide->link[$lang['id_lang']]);
            if ($slide->video) {
                if ($slide->video[(int)$lang['id_lang']]) {
                    foreach (Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slide->video[(int)$lang['id_lang']]), true) as $key => $value) {
                        $fields[$key][$lang['id_lang']] = Tools::getValue($key.'_.'.(int)$lang['id_lang'], $value);
                    }
                }
            } else {
                $fields['usevideo'][$lang['id_lang']] = 0;
                $fields['videoid'][$lang['id_lang']] = '';
                $fields['videoauto'][$lang['id_lang']] = 0;
                $fields['background_color'][$lang['id_lang']] = '';
                $fields['bullet_description'][$lang['id_lang']] = '';
            }
        }
        //slider no lang
        foreach ($this->slider_data as $key => $value) {
            $fields['slider['.$key.']'] = Tools::getValue('slider['.$key.']', $value);
        }
        
        //DONGND:: fix id group with data import or duplicate
        $fields['slider[group_id]'] = Tools::getValue('id_group', $slide->id_group);

        //slider with lang
        return $fields;
    }

    /**
     * slider Editor
     */
    public function renderSliderForm()
    {
        $layer_animation = array(array('id' => 'fade', 'name' => $this->l('Fade')), array('id' => 'sft', 'name' => $this->l('Short from Top')), array('id' => 'sfb', 'name' => $this->l('Short from Bottom')),
            array('id' => 'sfr', 'name' => $this->l('Short from Right')), array('id' => 'sfl', 'name' => $this->l('Short from Left')), array('id' => 'lft', 'name' => $this->l('Long from Top')),
            array('id' => 'lfb', 'name' => $this->l('Long from Bottom')), array('id' => 'lfr', 'name' => $this->l('Long from Right')), array('id' => 'lfl', 'name' => $this->l('Long from Left')),
            array('id' => 'randomrotate', 'name' => $this->l('Random Rotate')));
        $layers = array();
        if ($this->mod_current_slide->layersparams) {
            $layers = array();
            //echo "<pre>";print_r($this->_currentSlider->layersparams);die;

            foreach ($this->mod_current_slide->layersparams as $key => $val) {
                $layer = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($val), true);
                //$layer = $std->layers;
                if ($layer) {
//                    foreach ($layer as $k => &$l) {
                    foreach ($layer as $k => &$l) {
                        if (isset($l['layer_caption'])) {
                            $l['layer_caption'] = addslashes(str_replace("'", '&apos;', html_entity_decode(str_replace(array('\n', '\r', '\t'), '', utf8_decode($l['layer_caption'])), ENT_QUOTES, 'UTF-8')));
                        }
                        
                        //DONGND:: fix data when add new language
                        if (isset($l['layer_id']) && $l['layer_id'] != ($key.'_'.($k+1))) {
                            // echo 'test';
                            $l['layer_id'] = $key.'_'.($k+1);
                        }
                    }
                }
                
                $content = Tools::jsonEncode($layer);
                $content = str_replace('\r\n', '', $content);
                $layers[] = array('langID' => $key, 'content' => $content);
            }
        }
        //echo "<pre>";print_r($layers);die;
        $slide_img = $this->mod_current_slide->image;
        $slide_back = array();
        if ($this->mod_current_slide->video) {
            foreach ($this->mod_current_slide->video as $key => $val) {
                $video = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($val), true);
                $slide_back[$key] = '';
                if (isset($video['background_color'])) {
                    $slide_back[$key] = $video['background_color'];
                }
            }
        }
        //echo "<pre>";print_r($sliderBack);die;
//            unset($layers['1']);
//            unset($layers['3']);

        # Image External Link
        if($slide_img){
            foreach ($this->context->controller->getLanguages() as $lang) {
                $id_lang = $lang['id_lang'];
                if ((!(Tools::substr($slide_img[$id_lang], 0, 7) == 'http://')) && (!(Tools::substr($slide_img[$id_lang], 0, 8) == 'https://')) && (!(Tools::substr($slide_img[$id_lang], 0, 10) == 'data:image'))    ) {
                    $slide_img[$id_lang] = $this->img_url . $slide_img[$id_lang];
                }
            }
        }
        
        
        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            // 'leo_admin_module_link' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'leo_admin_module_link' => $this->context->link->getAdminLink('AdminModules'),
            'slideImg' => $slide_img,
            'sliderBack' => $slide_back,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'layerAnimation' => $layer_animation,
            'sliderGroup' => $this->group_data,
            'layers' => $layers,
            'ajaxfilelink' => Context::getContext()->link->getAdminLink('AdminLeoSlideshow'),
            'formLink' => _MODULE_DIR_.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'&action=submitslider',
            'psBaseModuleUri' => $this->img_url,
            'previewLink' => Context::getContext()->link->getModuleLink($this->name, 'preview', array('secure_key' => $this->secure_key)),
            'msecure_key' => $this->secure_key,
            'id_group' => Tools::getValue('id_group'),
            'id_slide' => $this->mod_current_slide->id,
            'delay' => LeoSlideshowSlide::showDelay((int)Tools::getValue('id_slide'), $this->slider_data['delay'], $this->group_data['delay']),
        ));

        return $this->display(__FILE__, 'slider_editor.tpl');
    }

    public function checkExistAnyGroup()
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_.'leoslideshow_groups gr WHERE gr.id_shop = '.(int)$id_shop);
    }

    /**
     * get all slider data
     */
    public function getSlides($id_group, $active = null)
    {
        $this->context = Context::getContext();
        $id_lang = $this->context->language->id;

        $sql = 'SELECT lsl.`id_leoslideshow_slides` as id_slide, lsl.*,lsll.*
                    FROM '._DB_PREFIX_.'leoslideshow_slides lsl
                    LEFT JOIN '._DB_PREFIX_.'leoslideshow_slides_lang lsll ON (lsl.id_leoslideshow_slides = lsll.id_leoslideshow_slides)
                    WHERE lsl.id_group = '.(int)$id_group.'
                    AND lsll.id_lang = '.(int)$id_lang.($active ? ' AND lsl.`active` = 1' : ' ').'
                    ORDER BY lsl.position';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * return list group
     */
    public function renderGroupList()
    {
        $mod_group = new LeoSlideshowGroup();
        $id_shop = $this->context->shop->id;
        $groups = $mod_group->getGroups(null, $id_shop);

        foreach ($groups as $key => $group) {
            if ($group['id_leoslideshow_groups'] == Tools::getValue('id_group') || (!Tools::getValue('id_group') && !Tools::isSubmit('addNewGroup') && $group['id_leoslideshow_groups'] == Configuration::get('LEOSLIDESHOW_GROUP_DE'))) {
                $this->current_group['id_group'] = $group['id_leoslideshow_groups'];
                $this->current_group['title'] = $group['title'];

                $params = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($group['params']), true);
                if ($params) {
                    $group_result = array();
                }
                foreach ($params as $k => $v) {
                    $group_result[$k] = $v;
                }

                $group_result['title'] = $group['title'];
                $group_result['id_leoslideshow_groups'] = $group['id_leoslideshow_groups'];
                $group_result['id_shop'] = $group['id_shop'];
                $group_result['hook'] = $group['hook'];
                $group_result['active'] = $group['active'];

                if ($group_result) {
                    $this->group_data = array_merge($this->group_data, $group_result);
                }
            }

            $groups[$key]['status'] = $this->displayGStatus($group['id_leoslideshow_groups'], $group['active']);
        }
        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            'groups' => $groups,
            'curentGroup' => $this->current_group['id_group'],
            'languages' => $this->context->controller->getLanguages(),
//            'exportLink' => Context::getContext()->link->getAdminLink('AdminLeoSlideshow').'&ajax=1&exportGroup=1',
            'previewLink' => Context::getContext()->link->getModuleLink($this->name, 'preview', array('secure_key' => $this->secure_key)),
            'msecure_key' => $this->secure_key
        ));

        return $this->display(__FILE__, 'group_list.tpl');
    }

    /**
     * return group config form
     */
    public function renderGroupConfig()
    {
        $description = $this->l('Add New Group');
        if (!Tools::isSubmit('deletegroup') && !Tools::isSubmit('addNewGroup') && $this->current_group['id_group']) {
            $description = $this->l('You are editting group:').' '.$this->current_group['title'];
        }

        $select_hook = array();
        $select_hook[] = array('id' => '', 'name' => '');
        foreach ($this->hook_support as $value) {
            $select_hook[] = array('id' => $value, 'name' => $value);
        }

        $full_width = array(array('id' => '', 'name' => $this->l('Boxed')),
            array('id' => 'fullwidth', 'name' => $this->l('Fullwidth')));

        $arr_col = array('12', '10', '9-6', '9', '8', '7-2', '6', '4-8', '4', '3', '2-4', '2');

        $hidden_config = array('hidden-lg-down' => $this->l('Hidden in Large devices'), 'hidden-md-down' => $this->l('Hidden in Medium devices'),
            'hidden-sm-down' => $this->l('Hidden in Small devices'), 'hidden-xs-down' => $this->l('Hidden in Extra small devices'), 'hidden-sp' => $this->l('Hidden in Smart Phone'));

        $fields_form = array();
        $fields_form[0]['form'] = array(
            
                // 'legend' => array(
                    // 'title' => $description,
                    // 'icon' => 'icon-cogs'
                // ),
                
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'LEOSLIDESHOW_GROUP_DEFAULTTAB',
                        'default' => '',
                    ),
                    array(
                        'type' => 'group_button',
                        'id_group' => $this->current_group['id_group'],
                        'name' => 'group_button',
                        'lang' => false,
                    ),
                    // array(
                        // 'type' => 'sperator_form',
                        // 'text' => $this->l('General Setting'),
                        // 'name' => 'sperator_form',
                        // 'show_button' => 1,
                        // 'lang' => false,
                    // ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Group Title'),
                        'name' => 'title_group',
                        'lang' => false,
                        'required' => 1
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show in Hook'),
                        'name' => 'hook_group',
                        'options' => array(
                            'query' => $select_hook,
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Delay'),
                        'name' => 'group[delay]',
                        'class' => 'fixed-width-xl digits',
                        'desc' => $this->l('Enter Time(miniseconds) to show each slide. Default 5000'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Starting Slide'),
                        'name' => 'group[start_with_slide]',
                        'class' => 'fixed-width-xl digits',
                        'desc' => $this->l('"Random Starting Slide" must be NO. The value is equal or greater than 0.'),
                        'lang' => false,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Timer Autorun '),
                        'name' => 'group[timer_show]',
                        'options' => array(
                            'query' => LeoSlideshowConfig::getTimerOption(),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Timer Stop On Hover'),
                        'name' => 'group[stop_on_hover]',
                        'is_bool' => true,
                        'values' => $this->getSwitchValue('stop_on_hover'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Random Starting Slide'),
                        'name' => 'group[shuffle_mode]',
                        'is_bool' => true,
                        'values' => $this->getSwitchValue('shuffle_mode'),
                        'desc' => $this->l('This override "Starting Slide" input'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable'),
                        'name' => 'active_group',
                        'is_bool' => true,
                        'values' => $this->getSwitchValue('active'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Slideshow Width Mode'),
                        'name' => 'group[fullwidth]',
                        'class' => 'slideshow-mode',
                        'options' => array(
                            'query' => $full_width,
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'col_width',
                        'label' => $this->l('Medium and Large Desktops Width'),
                        'name' => 'group[md_width]',
                        'class' => 'mode-width mode-',
                        'lang' => false
                    ),
                    array(
                        'type' => 'col_width',
                        'label' => $this->l('Small devices Tablets Width'),
                        'name' => 'group[sm_width]',
                        'class' => 'mode-width mode-',
                        'arrayVal' => $arr_col,
                        'lang' => false
                    ),
                    array(
                        'type' => 'col_width',
                        'label' => $this->l('Extra small devices Phones'),
                        'name' => 'group[xs_width]',
                        'class' => 'mode-width mode-',
                        'arrayVal' => $arr_col,
                        'lang' => false
                    ),
                    array(
                        'type' => 'sperator_form',
                        'text' => $this->l('Mode Boxed: You can config width of slideshow. It will display float with other module'),
                        'class' => 'alert alert-warning mode-width mode-',
                        'name' => 'sperator_form',
                        'lang' => false,
                    ),
                    array(
                        'type' => 'sperator_form',
                        'text' => $this->l('Mode FullWidth: The slideshow will show 100% in container of hook_slideshow. You have to config width of other module in hook_slideshow'),
                        'class' => 'alert alert-warning mode-width mode-fullwidth',
                        'name' => 'sperator_form',
                        'lang' => false,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default')
        );
        //DONGND:: image setting
        $fields_form[1]['form'] = array(
            'input' => array(
                array(
                        'type' => 'group_button',
                        'id_group' => $this->current_group['id_group'],
                        'name' => 'group_button',
                        'lang' => false,
                    ),
                // array(
                    // 'type' => 'sperator_form',
                    // 'text' => $this->l('Image Setting'),
                    // 'name' => 'sperator_form',
                    // 'lang' => false,
                // ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Image Cropping'),
                    'name' => 'group[image_cropping]',
                    'is_bool' => true,
                    'desc' => $this->l('Auto turn off is you use mode "Boxed" for slideshow'),
                    'values' => $this->getSwitchValue('image_cropping'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image Width'),
                    'name' => 'group[width]',
                    'lang' => false,
                    'desc' => $this->l('Use for resize image and Max-Height')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image Height'),
                    'name' => 'group[height]',
                    'lang' => false,
                    'desc' => $this->l('Use for resize image and Max-Height')
                ),
            ),
            'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default')
        );
        //DONGND:: css setting
        $fields_form[2]['form'] = array(
            'input' => array(
                # CSS SETTING
                array(
                    'type' => 'group_button',
                    'id_group' => $this->current_group['id_group'],
                    'name' => 'group_button',
                    'lang' => false,
                ),
                // array(
                    // 'type' => 'sperator_form',
                    // 'text' => $this->l('CSS Setting'),
                    // 'name' => 'sperator_form',
                    // 'lang' => false,
                // ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Margin'),
                    'name' => 'group[margin]',
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Padding(border)'),
                    'name' => 'group[padding]',
                    'lang' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Background Image'),
                    'name' => 'group[background_image]',
                    'is_bool' => true,
                    'values' => $this->getSwitchValue('background_image'),
                    'desc' => $this->l('Yes, Frontend will show background image. No, Frontend will show background color. '),
                ),
                array(
                    'type' => 'group_background',
                    'label' => $this->l('Background Image'),
                    'name' => 'group[background_url]',
                    'id' => 'background_url',
                    'lang' => false
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Background Color'),
                    'name' => 'group[background_color]',
                    'lang' => false,
                ),
                array(
                    'type' => 'group_class',
                    'label' => $this->l('Group Class'),
                    'name' => 'group[group_class]'
                ),
            ),
            'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default')
        );
        
        //DONGND:: navigatior and direction
        $fields_form[3]['form'] = array(
            'input' => array(
                # Navigator
                array(
                        'type' => 'group_button',
                        'id_group' => $this->current_group['id_group'],
                        'name' => 'group_button',
                        'lang' => false,
                    ),
                // array(
                    // 'type' => 'sperator_form',
                    // 'text' => $this->l('Navigator and Direction'),
                    // 'name' => 'sperator_form',
                    // 'lang' => false,
                // ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Navigator'),
                    'name' => 'group[controlNav]',
                    'is_bool' => true,
                    'values' => $this->getSwitchValue('controlNav'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Navigator Type'),
                    'name' => 'group[navigator_type]',
                    'options' => array(
                        'query' => LeoSlideshowConfig::getNavigatorType(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'class' => 'form-action',
                ),
                array(
                    'type' => 'leo_switch',
                    'label' => $this->l('Custom HTML Enable'),
                    'name' => 'group[enable_custom_html_bullet]',
                    'is_bool' => true,
                    'values' => $this->getSwitchValue('controlNavTooltipp'),
                    'leo_desc' => $this->l('Showing custom HTML for bullets, and changing Custom HTML in editing slide.'),
                    'class' => 'groupnavigator_type_sub groupnavigator_type-'.LeoSlideshowConfig::IVIEW_NAV_BULLET,
                ),
                array(
                    'type' => 'leo_switch',
                    'label' => $this->l('Tooltip Thumbnai'),
                    'name' => 'group[controlNavTooltip]',
                    'is_bool' => true,
                    'values' => $this->getSwitchValue('controlNavTooltip'),
                    'leo_desc' => $this->l('Show Thumbnail Tooltip when hover over Bullet ( only with Background is Image )'),
                    'class' => 'groupnavigator_type_sub groupnavigator_type-'.LeoSlideshowConfig::IVIEW_NAV_BULLET,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('X position threshold'),
                    'name' => 'group[tooltipX]',
                    'class' => 'fixed-width-xl number groupnavigator_type_sub groupnavigator_type-'.LeoSlideshowConfig::IVIEW_NAV_BULLET,
                    'lang' => false,
                    'desc' => $this->l('Set Left for Tooltip Thumbnail in pixel'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Y position threshold'),
                    'name' => 'group[tooltipY]',
                    'class' => 'fixed-width-xl number groupnavigator_type_sub groupnavigator_type-'.LeoSlideshowConfig::IVIEW_NAV_BULLET,
                    'lang' => false,
                    'desc' => $this->l('Set Top Tooltip Thumbnail in pixel'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Thumbnail Width'),
                    'name' => 'group[nav_thumbnail_width]',
                    'lang' => false,
                    'class' => 'fixed-width-xl digits',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Thumbnail Height'),
                    'name' => 'group[nav_thumbnail_height]',
                    'lang' => false,
                    'class' => 'fixed-width-xl digits',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Opacity'),
                    'name' => 'group[controlNavHoverOpacity]',
                    'class' => 'fixed-width-xl number',
                    'desc' => $this->l('Set opacity for Navigator. Value from 0 to 1. Ex 0.6'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Direction'),
                    'name' => 'group[directionNav]',
                    'is_bool' => true,
                    'values' => $this->getSwitchValue('directionNav'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Opacity'),
                    'name' => 'group[directionNavHoverOpacity]',
                    'class' => 'fixed-width-xl number',
                    'desc' => $this->l('Set opacity for Direction. Value from 0 to 1. Ex 0.6'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Key board'),
                    'name' => 'group[keyboardNav]',
                    'is_bool' => true,
                    'values' => $this->getSwitchValue('keyboardNav'),
                ),
            ),
            'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default')
        );
        
        //DONGND:: timer options
        $fields_form[4]['form'] = array(
            'input' => array(
                # TIMER OPTIONS
                array(
                        'type' => 'group_button',
                        'id_group' => $this->current_group['id_group'],
                        'name' => 'group_button',
                        'lang' => false,
                    ),
                // array(
                    // 'type' => 'sperator_form',
                    // 'text' => $this->l('Timer Options'),
                    // 'name' => 'timer_option',
                    // 'lang' => false,
                    // 'class' => 'alert alert-info sperator_form',
                // ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Timer Style'),
                    'name' => 'group[timer]',
                    'options' => array(
                        'query' => LeoSlideshowConfig::getTimerStyle(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'class' => 'form-action timer_option_sub',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Position'),
                    'name' => 'group[timerPosition]',
                    'options' => array(
                        'query' => LeoSlideshowConfig::getTimerPosition(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'class' => 'timer_option_sub',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('X position threshold'),
                    'name' => 'group[timerX]',
                    'class' => 'fixed-width-xl digits timer_option_sub',
                    'lang' => false,
                    'desc' => $this->l('Enter digits to set Left or Right for Timer.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Y position threshold'),
                    'name' => 'group[timerY]',
                    'class' => 'fixed-width-xl digits timer_option_sub',
                    'lang' => false,
                    'desc' => $this->l('Enter digits to set Top or Bottom for Timer.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Opacity'),
                    'name' => 'group[timerOpacity]',
                    'class' => 'fixed-width-xl number timer_option_sub',
                    'desc' => $this->l('Set opacity for Timer in pixel. Value is 0 to 1. Ex 0.6'),
                    'lang' => false,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Background'),
                    'name' => 'group[timerBg]',
                    'class' => 'fixed-width-xl timer_option_sub',
                    'form_group_class' => 'form_sub',
                    'lang' => false,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Timer Color'),
                    'name' => 'group[timerColor]',
                    'class' => 'fixed-width-xl timer_option_sub',
                    'form_group_class' => 'form_sub',
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Timer Diameter'),
                    'name' => 'group[timerDiameter]',
                    'class' => 'fixed-width-xl digits timer_option_sub',
                    'desc' => $this->l('Length of Running Line. Ex 360Bar is 30, Bar is 120, Pie 30.'),
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Timer Padding'),
                    'name' => 'group[timerPadding]',
                    'class' => 'fixed-width-xl digits timer_option_sub',
                    'desc' => $this->l('Height of background. Ex 360Bar is 2, Bar is 4, Pie 4.'),
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Timer Stroke'),
                    'name' => 'group[timerStroke]',
                    'class' => 'fixed-width-xl digits timer_option_sub grouptimer_sub grouptimer-'.LeoSlideshowConfig::IVIEW_TIMER_360BAR.' grouptimer-'.LeoSlideshowConfig::IVIEW_TIMER_BAR,
                    'desc' => $this->l('Height of Running Line. Ex 360Bar is 4, Bar is 4'),
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Border'),
                    'name' => 'group[timerBarStroke]',
                    'class' => 'fixed-width-xl digits timer_option_sub fixed-width-xl grouptimer_sub grouptimer-Bar',
                    'desc' => $this->l('Border of Bar Timer'),
                    'lang' => false,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Border Color'),
                    'name' => 'group[timerBarStrokeColor]',
                    'form_group_class' => 'form_sub',
                    'class' => 'fixed-width-xl timer_option_sub grouptimer_sub grouptimer-Bar',
                    'desc' => $this->l("Color of Bar Timer's border"),
                    'lang' => false,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Border Style'),
                    'name' => 'group[timerBarStrokeStyle]',
                    'options' => array(
                        'query' => LeoSlideshowConfig::getTimerBarStrokeStyle(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'desc' => $this->l("Style of Bar Timer's border"),
                    'class' => 'fixed-width-xl timer_option_sub grouptimer_sub grouptimer-Bar',
                ),
            ),
            'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default')
        );
        
        //DONGND:: animation
        $fields_form[5]['form'] = array(
            'input' => array(
                array(
                        'type' => 'group_button',
                        'id_group' => $this->current_group['id_group'],
                        'name' => 'group_button',
                        'lang' => false,
                    ),
                // array(
                    // 'type' => 'sperator_form',
                    // 'text' => $this->l('Animation'),
                    // 'name' => 'sperator_form',
                    // 'lang' => false,
                // ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Animation'),
                    'name' => 'group[fx]',
                    'options' => array(
                        'query' => LeoSlideshowConfig::getFx(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Speed to change slide'),
                    'name' => 'group[animationSpeed]',
                    'class' => 'fixed-width-xl digits',
                    'desc' => $this->l('Enter Time(miniseconds) to change each slide. Ex 500'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Slide Strips'),
                    'name' => 'group[strips]',
                    'class' => 'fixed-width-xl digits',
                    'desc' => $this->l('Total number of strips in slide. Strip is bigger when enter small number. Ex 20'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Slide Column'),
                    'name' => 'group[blockCols]',
                    'class' => 'fixed-width-xl digits',
                    'desc' => $this->l('Number of columns in slide. Ex 10'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Slide Row'),
                    'name' => 'group[blockRows]',
                    'class' => 'fixed-width-xl digits',
                    'desc' => $this->l('Number of rows in slide. Ex 5'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Caption Speed'),
                    'name' => 'group[captionSpeed]',
                    'class' => 'fixed-width-xl digits',
                    'desc' => $this->l('Enter Time(miniseconds) to show caption. Ex 500'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Caption Opacity'),
                    'name' => 'group[captionOpacity]',
                    'class' => 'fixed-width-xl number',
                    'desc' => $this->l('Set opacity for Caption. Value form 0 to 1. Ex 0.6'),
                ),
            ),
            'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default')
        );

        if (Tools::isSubmit('id_group') && LeoSlideshowGroup::groupExists((int)Tools::getValue('id_group'))) {
            $fields_form[5]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_group');
        } else if ($this->current_group['id_group'] && LeoSlideshowGroup::groupExists($this->current_group['id_group'])) {
            $fields_form[5]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_group');
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->name_controller = 'slideshow';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGroup';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getGroupFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
//            'exportLink' => Context::getContext()->link->getAdminLink('AdminLeoSlideshow').'&exportGroup=1',
            'psBaseModuleUri' => $this->img_url,
            'ajaxfilelink' => Context::getContext()->link->getAdminLink('AdminLeoSlideshow'),
            'leo_width' => $arr_col,
            'hidden_config' => $hidden_config
        );

        $globalform = $helper->generateForm($fields_form);
        //DONGND::
        $this->context->smarty->assign(array(
            'globalform' => $globalform,
            'description' => $description,
            'default_tab' => Configuration::get('LEOSLIDESHOW_GROUP_DEFAULTTAB')
            // 'id_group' => Tools::getValue('id_group'),
            // 'group_title' => $mod_group->title,
            // 'languages' => $this->context->controller->getLanguages(),
            // 'previewLink' => Context::getContext()->link->getModuleLink($this->name, 'preview', array('secure_key' => $this->secure_key)),
            // 'msecure_key' => $this->secure_key,
            // 'currentSliderID' => $this->mod_current_slide->id
        ));
        return $this->display(__FILE__, 'panel.tpl');
    }

    public function getSwitchValue($id)
    {
        return array(array('id' => $id.'_on', 'value' => 1, 'label' => $this->l('Yes')),
            array('id' => $id.'_off', 'value' => 0, 'label' => $this->l('No')));
    }

    public function getGroupFieldsValues()
    {
        $group = array();
        $field = array('id_leoslideshow_groups', 'title', 'id_shop', 'hook', 'active');

        foreach ($this->group_data as $key => $value) {
            if (in_array($key, $field)) {
                if ($key == 'id_leoslideshow_groups') {
                    # module validation
                    $group['id_group'] = $value;
                } else {
                    # module validation
                    $group[$key.'_group'] = $value;
                }
                continue;
            }
            $group['group['.$key.']'] = $value;
        }
        
        $group['LEOSLIDESHOW_GROUP_DEFAULTTAB'] = Tools::getValue('LEOSLIDESHOW_GROUP_DEFAULTTAB', Configuration::get('LEOSLIDESHOW_GROUP_DEFAULTTAB'));
        // echo '<pre>';
        // print_r($group);die();
        return $group;
    }

    public function postValidation()
    {
        $errors = array();

        if (Tools::isSubmit('submitGroup')) {
            if (Tools::isSubmit('id_group')) {
                if (!Validate::isInt(Tools::getValue('id_group')) && !LeoSlideshowGroup::groupExists(Tools::getValue('id_group'))) {
                    $errors[] = $this->l('Invalid id_group');
                }
            }
            $group_value = Tools::getValue('group');
            $arr_int = array(
                'delay' => $this->l('Invalid Delay value'),
                'width' => $this->l('Invalid Width value'),
                'height' => $this->l('Invalid Height value'),
            );

            foreach ($arr_int as $key => $value) {
                if (!Validate::isInt($group_value[$key]) && $group_value[$key] != '') {
                    $errors[] = $value;
                }
            }
            if (!Validate::isColor(Tools::getValue('background_color'))) {
                $errors[] = $this->l('Invalid Background color value');
            }
            
            if (LeoSlideshowConfig::getPermission() == false) {
                # CHECK VERSION
                if (Tools::isSubmit('id_group') == false) {
                    // Add new Group
                    $mod_group = new LeoSlideshowGroup();
                    $number_group = $mod_group->count();
                    
                    if ($number_group >= 1) {
                        $errors[] = $this->l("I'm so sorry, you are using free version of Leo slideshow module, could you please update module to pro version to use this function");
                        $errors[] = $this->display(__FILE__, 'error_freemodule.tpl');
                    }
                }
            }
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->error_text .= implode('<br>', $errors);
            $this->html .= $this->displayError(implode('<br />', $errors));
            return false;
        }

        /* Returns if validation is ok */
        return true;
    }

    public function getErrorLog()
    {
        return $this->error_text;
    }

    private function _postProcess()
    {
        $errors = array();
        if (Tools::isSubmit('submitGroup')) {
            # ACTION - add,edit for GROUP
            /* Sets ID if needed */
            if (Tools::getValue('id_group')) {
                $mod_group = new LeoSlideshowGroup((int)Tools::getValue('id_group'));
                if (!Validate::isLoadedObject($mod_group)) {
                    $this->html .= $this->displayError($this->l('Invalid id_group'));
                    return;
                }
            } else {
                $mod_group = new LeoSlideshowGroup();
            }

            /* Sets position */
            $mod_group->title = Tools::getValue('title_group');
            /* Sets active */
            $mod_group->active = (int)Tools::getValue('active_group');
            $context = Context::getContext();
            $mod_group->id_shop = $context->shop->id;
            $mod_group->hook = Tools::getValue('hook_group');

            $params = Tools::getValue('group');
            $mod_group->params = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($params));

            // print_r(Tools::getValue('LEOSLIDESHOW_GROUP_DEFAULTTAB'));die();
            Configuration::updateValue('LEOSLIDESHOW_GROUP_DEFAULTTAB', Tools::getValue('LEOSLIDESHOW_GROUP_DEFAULTTAB'));
            
            # TODO REMOVE CODE DEVELOPER
            if (property_exists('LeoSlideshowGroup', 'randkey') && empty($mod_group->randkey)) {
                $mod_group->randkey = LeoSlideshowHelper::genKey();
            }
            
            /* Adds */
            if (!Tools::getValue('id_group')) {
                if (!$mod_group->add()) {
                    $errors[] = $this->displayError($this->l('The group could not be added.'));
                } else {
                    $this->clearCache();
                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editgroup=1&id_group='.$mod_group->id.'&conf=3');
                }
            } else {
                # Update
                if (!$mod_group->update()) {
                    $errors[] = $this->displayError($this->l('The group could not be updated.'));
                } else {
                    $this->clearCache();
                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editgroup=1&id_group='.$mod_group->id.'&conf=4');
                }
            }
            # Save in config to edit next time
            $this->clearCache();
        } elseif (Tools::isSubmit('changeGStatus') && Tools::isSubmit('id_group')) {
            # ACTION - Change status for GROUP : enable or disable a group
            $mod_group = new LeoSlideshowGroup((int)Tools::getValue('id_group'));
            $change_status = Tools::getValue('changeGStatus');
            $mod_group->update_flag = false;

            if ($change_status == LeoSlideshowStatus::GROUP_STATUS_DISABLE && $mod_group->active != $change_status) {
                $mod_group->active = LeoSlideshowStatus::GROUP_STATUS_DISABLE;
                $mod_group->update_flag = true;
            } elseif ($change_status == LeoSlideshowStatus::GROUP_STATUS_ENABLE && $mod_group->active != $change_status) {
                $mod_group->active = LeoSlideshowStatus::GROUP_STATUS_ENABLE;
                $mod_group->update_flag = true;
            }

            if (true == $mod_group->update_flag) {
                $res = $mod_group->update();
                $this->clearCache();
                $this->html .= ($res ? $this->displayConfirmation($this->l('Change status of group was successful')) : $this->displayError($this->l('The configuration could not be updated.')));
            }
        } elseif (Tools::isSubmit('deletegroup')) {
            $mod_group = new LeoSlideshowGroup((int)Tools::getValue('id_group'));
            # Delete slider of group
            $slider = $this->getSlides((int)Tools::getValue('id_group'));

            foreach ($slider as $value) {
                $mod_slide = new LeoSlideshowSlide($value['id_leoslideshow_slides']);
                $mod_slide->delete();
            }

            $res = $mod_group->delete();


            $this->clearCache();
            if (!$res) {
                $this->html .= $this->displayError('Could not delete');
            } else {
                $this->html .= $this->displayConfirmation($this->l('Group deleted'));
            }

            Tools::redirectAdmin('index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&configure=leoslideshow&tab_module=leotheme&module_name=leoslideshow&conf=1');
        } elseif (Tools::isSubmit('changeStatus')) {
            # ACTION - Change status for SLIDE : enable or disable a slide
            $mod_slide = new LeoSlideshowSlide((int)Tools::getValue('sslider'));

            $change_status = Tools::getValue('changeStatus');
            $mod_slide->update_flag = false;
            if ($change_status === LeoSlideshowStatus::SLIDER_STATUS_DISABLE && $mod_slide->active != $change_status) {
                $mod_slide->active = LeoSlideshowStatus::SLIDER_STATUS_DISABLE;
                $mod_slide->update_flag = true;
            } elseif ($change_status === LeoSlideshowStatus::SLIDER_STATUS_ENABLE && $mod_slide->active != $change_status) {
                $mod_slide->active = LeoSlideshowStatus::SLIDER_STATUS_ENABLE;
                $mod_slide->update_flag = true;
            }

            if (true == $mod_slide->update_flag) {
                $res = $mod_slide->update();
                $this->clearCache();
                $this->html .= ($res ? $this->displayConfirmation($this->l('Change status of slide was successful')) : $this->displayError($this->l('The configuration could not be updated.')));
            }
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->html .= $this->displayError(implode('<br />', $errors));
        } elseif (Tools::isSubmit('submitGroup')) {
            $this->html .= $this->displayConfirmation($this->l('Slide added'));
        } elseif (Tools::isSubmit('submitGroup')) {
            $this->html .= $this->displayConfirmation($this->l('Slide added'));
        }
    }

    /**
     * The function prepareHookForApPageBuilder duplicated logic from this function
     */
    private function _prepareHook($hook_name)
    {
        if ($this->isCached('module:leoslideshow/views/templates/front/leoslideshow.tpl', $this->getCacheId($hook_name.'_'.$this->name))) {
            return true;
        }

        if (!is_dir(_PS_ROOT_DIR_.'/cache/'.$this->name)) {
            mkdir(_PS_ROOT_DIR_.'/cache/'.$this->name, 0755, true);
        }

        //get slider via hookname
        $group = LeoSlideshowGroup::getActiveGroupByHook($hook_name);
        if (!$group) {
            return false;
        }
        $sliders = $this->getSlides($group['id_leoslideshow_groups'], 1);
        $sliders = LeoSlideshowSlide::filterSlider($sliders, $this->slider_data);
        if (!$sliders) {
            return false;
        }

        $slider_params = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($group['params']), true);
        // echo '<pre>';
        // print_r($slider_params);die();
        $slider_params = array_merge($group, $slider_params);
        $slider_params = array_merge($this->group_data, $slider_params);
        $mod_group = new LeoSlideshowGroup();
        $mod_group->setModule($this);
        $slider_params = $mod_group->setData($slider_params)->beforeLoad()->loadFrontEnd();

        if (isset($slider_params['fullwidth']) && (!empty($slider_params['fullwidth']) || $slider_params['fullwidth'] == 'boxed')) {
            $slider_params['image_cropping'] = false;
        }

        $slider_params['hide_navigator_after'] = $slider_params['show_navigator'] ? 0 : $slider_params['hide_navigator_after'];
        $slider_params['slider_class'] = trim(isset($slider_params['fullwidth']) && !empty($slider_params['fullwidth']) ? $slider_params['fullwidth'] : 'boxed');
        $slider_fullwidth = $slider_params['slider_class'] == 'boxed' ? 'off' : 'on';

        // generate back-ground
        if ($slider_params['background_image'] && $slider_params['background_url'] && file_exists($this->img_path.$slider_params['background_url'])) {
            $slider_params['background'] = 'background: url('.$this->img_url.$slider_params['background_url'].') no-repeat scroll left 0 '.$slider_params['background_color'].';';
        } else {
            $slider_params['background'] = 'background-color:'.$slider_params['background_color'];
        }

        //include library genimage
        if (!class_exists('PhpThumbFactory')) {
            require_once _PS_MODULE_DIR_.'leoslideshow/libs/phpthumb/ThumbLib.inc.php';
        }

        $white_main_img = __PS_BASE_URI__.'modules/'.$this->name.'/views/img/white50.png';

        //process slider
        foreach ($sliders as $key => $slider) {
            $slider['layers'] = array();
            $slider['params'] = array_merge($this->slider_data, Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slider['params']), true));
            $slider['layersparams'] = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slider['layersparams']), true);
            $slider['video'] = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slider['video']), true);

            $slider['data_link'] = '';
            if ($slider['params']['enable_link'] && $slider['link']) {
                // $slider['data_link'] = 'data-link="'.$slider['link'].'"';
                // $slider['data_target'] = 'data-target="'.LeoSlideshowSlide::renderTarget($slider['params']['target']).'"';
                $slider['data_link'] = $slider['link'];
                $slider['data_target'] = LeoSlideshowSlide::renderTarget($slider['params']['target']);
            } else {
                $slider['data_target'] = '';
            }

            $slider['data_delay'] = (int)$slider['params']['delay'];

            //videoURL
            $slider['videoURL'] = '';
            $slider['video']['active'] = '0';
            if ($slider['video']['usevideo'] == 'youtube' || $slider['video']['usevideo'] == 'vimeo') {
                $slider['video']['active'] = '1';
                $slider['video']['videoURL'] = Tools::getCurrentUrlProtocolPrefix() . 'player.vimeo.com/video/'.$slider['video']['videoid'].'/';
                if ($slider['video']['usevideo'] == 'youtube') {
                    $slider['video']['videoURL'] = Tools::getCurrentUrlProtocolPrefix() . 'www.youtube.com/embed/'.$slider['video']['videoid'].'/';
                }
            }

            if ($slider['video']['videoauto'] == 1) {
                $slider['video']['videoauto'] = 'autoplay=1';
            } else {
                $slider['video']['videoauto'] = 'autoplay=0';
            }

            $slider['background_color'] = '';
            if (isset($slider_params['background_color']) && $slider_params['background_color']) {
                $slider['background_color'] = $slider_params['background_color'];
            }
            if (isset($slider['video']['background_color']) && $slider['video']['background_color']) {
                $slider['background_color'] = $slider['video']['background_color'];
            }

            LeoSlideshowSlide::getBackground($slider_params, $slider);

            if ($slider['image'] == '') {
                $slider['image'] = 'views/img/blank.gif';
            }

            if ($slider_params['image_cropping']) {
                //gender main_image
                if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                    $slider['main_image'] = $this->renderThumb($slider['image'], $slider_params['width'], $slider_params['height']);
                } else {
                    $slider['main_image'] = $white_main_img;
                }

                if ($slider['thumbnail'] && file_exists($this->img_path.$slider['thumbnail'])) {
                    # module validation
                    //$slider['thumbnail'] = $this->renderThumb($slider['thumbnail'], $sliderParams['thumbnail_width'], $sliderParams['thumbnail_height']);
                } else if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                    # module validation
                    //$slider['thumbnail'] = $this->renderThumb($slider['image'], $sliderParams['thumbnail_width'], $sliderParams['thumbnail_height']);
                } else {
                    $slider['thumbnail'] = $white_main_img;
                }
            } else {
                $slider['main_image'] = __PS_BASE_URI__.'modules/leoslideshow'.'/views/img/blank.gif';

                if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                    $slider['main_image'] = $this->img_url.$slider['image'];
                }

                if ($slider['thumbnail'] && file_exists($this->img_path.$slider['thumbnail'])) {
                    $slider['thumbnail'] = $this->img_url.$slider['thumbnail'];
                } else if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                    $slider['thumbnail'] = $slider['main_image'];
                } else {
                    $slider['thumbnail'] = $white_main_img;
                }
            }

            if (isset($slider['layersparams']) && $slider['layersparams']) {
                foreach ($slider['layersparams'] as $k => &$layer_css) {
                    if ($layer_css['layer_status'] == 0) {
                        unset($slider['layersparams'][$k]);
                        continue;
                    }

                    $layer_css_val = '';
                    if (isset($layer_css['layer_font_size']) && $layer_css['layer_font_size']) {
                        $layer_css_val = 'font-size:'.$layer_css['layer_font_size'];
                    }
                    if (isset($layer_css['layer_background_color']) && $layer_css['layer_background_color']) {
                        $layer_css_val .= ($layer_css_val != '' ? ';' : '').'background-color:'.$layer_css['layer_background_color'];
                    }
                    if (isset($layer_css['layer_color']) && $layer_css['layer_color']) {
                        $layer_css_val .= ($layer_css_val != '' ? ';' : '').'color:'.$layer_css['layer_color'];
                    }
                    $layer_css['css'] = $layer_css_val;
                    if (!isset($layer_css['layer_link'])) {
                        $layer_css['layer_link'] = $slider['link'];
                    } else {
                        $layer_css['layer_link'] = str_replace('_ASM_', '&', $layer_css['layer_link']);
                    }

                    $layer_css['layer_target'] = LeoSlideshowSlide::renderTarget($layer_css['layer_target']);
                    if (isset($layer_css['layer_caption']) && $layer_css['layer_caption']) {
                        $layer_css['layer_caption'] = utf8_decode($layer_css['layer_caption']);
                    }
                }
            }
            $sliders[$key] = $slider;
        }
        $slider_params['start_with_slide'] = LeoSlideshowGroup::showStartWithSlide($slider_params['start_with_slide'], $sliders);
        $sliders = LeoSlideshowSlide::showBulletCustomHTML($slider_params, $sliders);
        $slider_params['playLabel'] = LeoSlideshowHelper::l('Play');
        $slider_params['pauseLabel'] = LeoSlideshowHelper::l('Pause');
        $slider_params['closeLabel'] = LeoSlideshowHelper::l('Close');
        $slider_params['rtl'] = $this->context->language->is_rtl;
        // echo '<pre>';
        // print_r($sliders);die();
        $this->smarty->assign(array(
            'sliderParams' => $slider_params,
            'sliders' => $sliders,
            'sliderIDRand' => rand(20, rand()),
            'sliderFullwidth' => $slider_fullwidth,
            'sliderImgUrl' => $this->img_url,
            'rand_num' => uniqid(),
        ));

        return true;
    }

    /**
     *
     */
    public function renderThumb($src_file, $width, $height)
    {
        $sub_folder = '/';
        if (!$src_file) {
            return '';
        }
        if (strpos($src_file, '/') !== false) {
            $path = @pathinfo($src_file);
            if (strpos($path['dirname'], '/') !== -1) {
                $sub_folder = $path['dirname'].'/';
                $folder_list = explode('/', $path['dirname']);
                $tmp_folder = '/';
                foreach ($folder_list as $value) {
                    if ($value) {
                        if (!is_dir(_PS_ROOT_DIR_.'/cache/'.$this->name.$tmp_folder.$value)) {
                            mkdir(_PS_ROOT_DIR_.'/cache/'.$this->name.$tmp_folder.$value, 0755, true);
                        }

                        $tmp_folder .= $value.'/';
                    }
                }
            }
            $image_name = $path['basename'];
        } else {
            $image_name = $src_file;
        }

        $path = '';
        if (file_exists($this->img_path.$src_file)) {
            //return image url
            $path = __PS_BASE_URI__.'cache/'.$this->name.$sub_folder.$width.'_'.$height.'_'.$image_name;
            $save_path = _PS_ROOT_DIR_.'/cache/'.$this->name.$sub_folder.$width.'_'.$height.'_'.$image_name;
            if (!file_exists($save_path)) {
                $thumb = PhpThumbFactory::create($this->img_path.$src_file);
                $thumb->adaptiveResize($width, $height);
                $thumb->save($save_path);
            }
        }

        return $path;
    }

    public function _processHook($hookName)
    {
        $id_slide = Tools::getValue('id_slide');
        $id_group = Tools::getValue('id_group');
        if (isset($id_slide) && isset($id_group) && $id_slide && $id_group) {
            # preview
            return;
        }

        if (!$this->_prepareHook($hookName)) {
            return false;
        }

        // return $this->display(__FILE__, ''.$this->name.'.tpl', $this->getCacheId($hookName.'_'.$this->name));
        return $this->fetch('module:leoslideshow/views/templates/front/leoslideshow.tpl', $this->getCacheId($hookName.'_'.$this->name));
    }

    public function hookHeader()
    {
        $media_dir = $this->getMediaDir();
        $this->context->controller->addJS(_PS_JS_DIR_.'jquery/jquery-migrate-1.2.1.min.js');
        $this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/jquery.easing.js');

        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/typo/typo.css', 'all');
        // if (file_exists(_PS_THEME_DIR_.'modules/leoslideshow/css/typo/typo.css')) {
            // $this->context->controller->addCSS(_PS_THEME_DIR_.'modules/leoslideshow/css/typo/typo.css');
        // } else {
            // $this->context->controller->addCSS($this->_path.'css/typo/typo.css');
        // }
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/iView/iview.css', 'all');
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/iView/skin_4_responsive/style.css', 'all');
        $this->context->controller->addJqueryUI('ui.core');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/iView/raphael-min.js');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/iView/iview.js');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/leoslideshow.js');
    }

    public function hookDisplayAfterBodyOpeningTag()
    {
        return $this->_processHook('displayAfterBodyOpeningTag');
    }
  
    public function hookDisplayTop()
    {
        return $this->_processHook('displayTop');
    }
    
    public function hookDisplayNav1()
    {
        return $this->_processHook('displayNav1');
    }

    public function hookDisplayNav2()
    {
        return $this->_processHook('displayNav2');
    }

    public function hookDisplayNavFullWidth()
    {
        return $this->_processHook('displayNavFullWidth');
    }

    public function hookDisplayHome()
    {
        return $this->_processHook('displayHome');
    }

    public function hookDisplayLeftColumn()
    {
        return $this->_processHook('displayLeftColumn');
    }

    public function hookDisplayRightColumn()
    {
        return $this->_processHook('displayRightColumn');
    }

    public function hookDisplayFooterProduct()
    {
        return $this->_processHook('displayFooterProduct');
    }

    public function hookDisplayFooterBefore()
    {
        return $this->_processHook('displayFooterBefore');
    }

    public function hookDisplayFooter()
    {
        return $this->_processHook('displayFooter');
    }

    public function hookDisplayFooterAfter()
    {
        return $this->_processHook('displayFooterAfter');
    }
    
    public function hookDisplayLeftColumnProduct()
    {
        return $this->_processHook('displayLeftColumnProduct');
    }
    
    public function hookDisplayRightColumnProduct()
    {
        return $this->_processHook('displayRightColumnProduct');
    }
    
    public function hookDisplayProductButtons()
    {
        return $this->_processHook('displayProductButtons');
    }
    
    public function hookDisplayReassurance()
    {
        return $this->_processHook('displayReassurance');
    }
    
    public function hookDisplayBeforeBodyClosingTag()
    {
        return $this->_processHook('displayBeforeBodyClosingTag');
    }

    public function clearCache()
    {
        foreach ($this->hook_support as $val) {
            $this->_clearCache(''.$this->name.'.tpl', $val.'_'.$this->name);
        }
    }

    public function hookActionShopDataDuplication($params)
    {
        $sql = 'SELECT gr.*
                FROM `'._DB_PREFIX_.'leoslideshow_groups` gr
                WHERE gr.`id_shop` = '.(int)$params['old_id_shop'];
        $group_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($group_list as $list) {
            $mod_group = new LeoSlideshowGroup();
            foreach ($list as $key => $value) {
                if ($key != 'id' && $key != 'id_shop') {
                    $mod_group->{$key} = $value;
                }
            }

            $mod_group->id_shop = (int)$params['new_id_shop'];
            $mod_group->add();

            //import slider
            $slide_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT sl.id_leoslideshow_slides as id
                            FROM `'._DB_PREFIX_.'leoslideshow_slides` sl
                            WHERE sl.`id_group` = '.(int)$list['id_leoslideshow_groups']);

            $fields = array('active', 'image', 'thumbnail', 'video', 'title', 'layersparams', 'title', 'position', 'link', 'params');
            foreach ($slide_list as $key => $value) {
                $mod_slide = new LeoSlideshowSlide($value['id']);
                $mod_new_slide = new LeoSlideshowSlide();
                $mod_new_slide->id_group = $mod_group->id;
                foreach ($fields as $field) {
                    $mod_new_slide->{$field} = $mod_slide->{$field};
                }

                $mod_new_slide->add();
            }
        }

        $this->clearCache();
    }

    public function headerHTML()
    {
        $media_dir = $this->getMediaDir();

        if (Tools::getValue('controller') != 'AdminModules' && Tools::getValue('configure') != $this->name) {
            return;
        }
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/admin/style.css');
        if (defined('_LEO_MODE_DEV_') && _LEO_MODE_DEV_ === true) {
            if (file_exists(_PS_THEME_DIR_.'assets/css/modules/leoslideshow/views/css/typo/typo.css')) {
                $this->context->controller->addCSS(_PS_THEME_DIR_.'assets/css/modules/leoslideshow/views/css/typo/typo.css');
            } else {
                $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/typo/typo.css');
            }
        } else {
            if (file_exists(_PS_THEME_DIR_.'assets/css/modules/leoslideshow/views/css/typo/typo.css')) {
                $this->context->controller->addCSS(_PS_THEME_DIR_.'assets/css/modules/leoslideshow/views/css/typo/typo.css');
            } else {
                if (file_exists(_PS_THEME_DIR_.'modules/leoslideshow/views/css/typo/typo.css')) {
                    $this->context->controller->addCSS(_PS_THEME_DIR_.'modules/leoslideshow/views/css/typo/typo.css');
                } else {
                    $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/typo/typo.css');
                }
            }
        }
       
        $this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/jquery.colorpicker.js');
        $this->context->controller->addJS(__PS_BASE_URI__.$media_dir.'js/admin/script.js');
        $this->context->controller->addJqueryUI('ui.core');
        $this->context->controller->addJqueryUI('ui.widget');
        $this->context->controller->addJqueryUI('ui.mouse');
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addCSS(_PS_JS_DIR_.'jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css');
        $this->context->controller->addJqueryUI('ui.dialog');
        $this->context->controller->addJqueryPlugin('cooki-plugin');
    }

    public function getNextPosition()
    {
        $sql = 'SELECT MAX(hss.`position`) AS `next_position`
                FROM `'._DB_PREFIX_.''.$this->name.'_slides` hss, `'._DB_PREFIX_.''.$this->name.'` hs
                WHERE hss.`id_'.$this->name.'_slides` = hs.`id_'.$this->name.'_slides` AND hs.`id_shop` = '.(int)$this->context->shop->id;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return ( ++$row['next_position']);
    }

    public function displayGStatus($id_group, $active)
    {
        # Status Image
        $title = ((int)$active == 0 ? $this->l('Click to Enabled') : $this->l('Click to Disabled'));
        $img = ((int)$active == 0 ? 'disabled.gif' : 'enabled.gif');

        # Status Link
        if ($active == LeoSlideshowStatus::GROUP_STATUS_DISABLE) {
            $change_group_status = LeoSlideshowStatus::GROUP_STATUS_ENABLE;
        } elseif ($active == LeoSlideshowStatus::GROUP_STATUS_ENABLE) {
            $change_group_status = LeoSlideshowStatus::GROUP_STATUS_DISABLE;
        }

        $this->context->smarty->assign(array(
            'gstatus' => true,
            'status_link' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&changeGStatus='.$change_group_status.'&id_group='.(int)$id_group,
            'title' => $title,
            'img_link' => _PS_ADMIN_IMG_.$img,
        ));
        return $this->display(__FILE__, 'status.tpl');
    }

    public function displayStatus($id_slide, $active, $group_id, $slide)
    {
        # Status Image
        $title = ((int)$active == 0 ? $this->l('Click to Enabled') : $this->l('Click to Disabled'));
        $src_img = _PS_ADMIN_IMG_;
        $mod_slide = new LeoSlideshowSlide();
        $mod_slide->mergeSlider($slide)->mergeParams($this->slider_data);

        if ($mod_slide->getStatusTime() == LeoSlideshowStatus::SLIDER_STATUS_DISABLE) {
            $img = 'disabled.gif';
        } elseif ($mod_slide->getStatusTime() == LeoSlideshowStatus::SLIDER_STATUS_ENABLE) {
            $img = 'enabled.gif';
        } elseif ($mod_slide->getStatusTime() == LeoSlideshowStatus::SLIDER_STATUS_COMING) {
            $img = 'coming.png';
            $src_img = _MODULE_DIR_.'leoslideshow/views/img/admin/';
        }

        # Status Link
        if ($mod_slide->getStatusTime() == LeoSlideshowStatus::SLIDER_STATUS_DISABLE) {
            $change_slide_status = LeoSlideshowStatus::SLIDER_STATUS_ENABLE;
        } elseif ($mod_slide->getStatusTime() == LeoSlideshowStatus::SLIDER_STATUS_ENABLE) {
            $change_slide_status = LeoSlideshowStatus::SLIDER_STATUS_DISABLE;
        } elseif ($mod_slide->getStatusTime() == LeoSlideshowStatus::SLIDER_STATUS_COMING) {
            $change_slide_status = LeoSlideshowStatus::SLIDER_STATUS_DISABLE;
        }
        
        $this->context->smarty->assign(array(
            'status' => true,
            'status_link' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&changeStatus='.$change_slide_status.'&sslider='.(int)$id_slide.'&showsliders=1&id_group='.(int)$group_id,
            'title' => $title,
            'img_link' => $src_img.$img,
        ));
        
        return $this->display(__FILE__, 'status.tpl');
    }

    public function slideExists($id_slide)
    {
        $sql = 'SELECT `id_'.$this->name.'_slides`
                FROM `'._DB_PREFIX_.''.$this->name.'_slides`
                WHERE `id_'.$this->name.'_slides` = '.(int)$id_slide;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        return ($row);
    }

    protected function getCacheId($name = null, $hook = '')
    {
        $cache_array = array(
            $name !== null ? $name : $this->name,
            $hook,
            date('Ymd'),
            (int)Tools::usingSecureMode(),
            (int)$this->context->shop->id,
            (int)Group::getCurrent()->id,
            (int)$this->context->language->id,
            (int)$this->context->currency->id,
            (int)$this->context->country->id,
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
        );
        return implode('|', $cache_array);
    }

    public function converParams($old_params = '')
    {
        $result = '';
        if ($old_params != '') {
            $data = Tools::unSerialize($old_params);
            $result = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($data));
        }
        return $result;
    }
    # ******************** SLIDE ********************

    public function slideProcessAjax()
    {
        $result = array();
        if (Tools::getValue('action') && Tools::getValue('action') == 'submitslider') {
            # ACTION - edit slide
            if (Tools::getValue('id_slide')) {
                $mod_slide = new LeoSlideshowSlide((int)Tools::getValue('id_slide'));
                $mod_slide->completeDatabase(array('lang' => true));
                if (!Validate::isLoadedObject($mod_slide)) {
                    $this->l('Invalid id_slide');
                    return;
                }
            } else {
                $mod_slide = new LeoSlideshowSlide();
            }

            $mod_slide->id_group = (int)Tools::getValue('id_group');
            $mod_slide->position = (int)Tools::getValue('position');
            $mod_slide->active = (int)Tools::getValue('active_slide');
            $mod_slide->params = LeoSlideshowSlide::base64Encode(Tools::jsonEncode(Tools::getValue('slider')));

            try {
                $post_slide = Tools::getValue('slider');
                $mod_slide->start_date_time = $post_slide['start_date_time'];
                $mod_slide->end_date_time = $post_slide['end_date_time'];
                $mod_slide->bullet_class = $post_slide['bullet_class'];
                $mod_slide->id_group = $post_slide['group_id'];

                $mod_slide->validate($this);
            } catch (Exception $exc) {
                $result = array('error' => 1, 'text' => $exc->getMessage());
                $this->clearCache();
                die(Tools::jsonEncode($result));
            }

            $languages = Language::getLanguages(false);
            $tmp_data = array();
            $tmp_back_color = '';

            foreach ($languages as $language) {
                $mod_slide->title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
                //get data default
                $mod_slide->link[$language['id_lang']] = Tools::getValue('link_'.$language['id_lang']);
                $mod_slide->image[$language['id_lang']] = Tools::getValue('image_'.$language['id_lang']);
                $mod_slide->thumbnail[$language['id_lang']] = Tools::getValue('thumbnail_'.$language['id_lang']);

                $video = array();
                $video['usevideo'] = Tools::getValue('usevideo_'.$language['id_lang']);
                $video['videoid'] = Tools::getValue('videoid_'.$language['id_lang']);
                $video['videoauto'] = Tools::getValue('videoauto_'.$language['id_lang']);
                $video['background_color'] = Tools::getValue('background_color_'.$language['id_lang']);
                $video['bullet_description'] = Tools::getValue('bullet_description_'.$language['id_lang']);

                if ($video['background_color'] == '' && !Tools::getValue('id_slide')) {
                    $video['background_color'] = $tmp_back_color;
                } else {
                    $tmp_back_color = $video['background_color'];
                }

                $mod_slide->video[$language['id_lang']] = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($video));
                $layersparams = new stdClass();
                $layersparams->layers = array();

                if (Tools::getIsset('layers_'.$language['id_lang'])) {
                    $times = Tools::getValue('layer_time');
                    $layers = Tools::getValue('layers_'.$language['id_lang']);

                    foreach ($layers as $key => $value) {
                        $value['time_start'] = $times[$value['layer_id']];
                        //fix for php 5.2 and 5.3
                        $value['layer_caption'] = utf8_encode(str_replace(array('\'', '\"'), array("'", '"'), $value['layer_caption']));
                        $times[$value['layer_id']] = $value;
                    }
                    $k = 0;
                    foreach ($times as $key => $value) {
                        if (is_array($times) && $key == @$value['layer_id']) {
                            $value['layer_id'] = $language['id_lang'].'_'.($k + 1);
                            $layersparams->layers[$k] = $value;
                            $k++;
                        }
                    }
                    $mod_slide->layersparams[$language['id_lang']] = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($layersparams->layers));
                } else {
                    //when add new create sample data for other language
                    if (!Tools::getValue('id_slide') && isset($tmp_data['layersparams']) && $tmp_data['layersparams']) {
                        //set id again
                        foreach ($tmp_data['layersparams'] as &$tmp_layer) {
                            foreach ($tmp_layer as $key => &$value) {
                                if ($key == 'layer_id') {
                                    $valu = explode('_', $value);
                                    $value = str_replace($valu[0].'_', $language['id_lang'].'_', $value);
                                }
                            }
                        }
                        $mod_slide->layersparams[$language['id_lang']] = LeoSlideshowSlide::base64Encode(Tools::jsonEncode($tmp_data['layersparams']));
                    } else {
                        $mod_slide->layersparams[$language['id_lang']] = '';
                    }
                }

                //get data default if add new
                if (!Tools::getValue('id_slide') && $mod_slide->title && empty($tmp_data)) {
                    $tmp_data['title'] = $mod_slide->title[$language['id_lang']];
                    $tmp_data['link'] = $mod_slide->link[$language['id_lang']];
                    $tmp_data['video'] = $mod_slide->video[$language['id_lang']];
                    $tmp_data['image'] = $mod_slide->image[$language['id_lang']];
                    $tmp_data['thumbnail'] = $mod_slide->image[$language['id_lang']];
                    $tmp_data['id_lang'] = $language['id_lang'];
                    $tmp_data['image'] = $mod_slide->image[$language['id_lang']];
                }
                if (!Tools::getValue('id_slide') && !isset($tmp_data['layersparams'])) {
                    $tmp_data['layersparams'] = $layersparams->layers;
                }
            }

            if (!Tools::getValue('id_slide')) {
                # ACTION - add SLIDE
                 
                if (LeoSlideshowConfig::getPermission() == false) {
                    # CHECK VERSION
                    $number_group = $mod_slide->count();

                    if ($number_group >= 2) {
                        $errors = $this->display(__FILE__, 'error_freemodule1.tpl');
                        $result = array('error' => 2, 'text' => $errors );
                        die(Tools::jsonEncode($result));
                    }
                }
                # add default image
                foreach ($mod_slide->title as &$value) {
                    if ($value == '') {
                        $value = $tmp_data['title'];
                    }
                }

                foreach ($mod_slide->link as &$value) {
                    if ($value == '') {
                        $value = $tmp_data['link'];
                    }
                }

                foreach ($mod_slide->image as &$value) {
                    if ($value == '') {
                        $value = $tmp_data['image'];
                    }
                }

                foreach ($mod_slide->video as &$value) {
                    if ($value == '') {
                        $value = $tmp_data['video'];
                    }
                }

                if (!$mod_slide->add()) {
                    $result = array('error' => 1, 'text' => $this->l('The slide could not be added.'));
                }
            } elseif (!$mod_slide->update()) {
                /* Update */
                $result = array('error' => 1, 'text' => $this->l('The slide could not be updated.'));
            }
            $my_link = '&configure=leoslideshow&editSlider=1&id_slide='.$mod_slide->id.'&id_group='.$mod_slide->id_group.'&conf=4';
            $result = array('error' => 0, 'text' => $my_link);

            $this->clearCache();
            die(Tools::jsonEncode($result));
        }

        if (Tools::getValue('action') && Tools::getValue('action') == 'updateSlidesPosition' && Tools::getValue('slides')) {
            $slides = Tools::getValue('slides');

            foreach ($slides as $position => $id_slide) {
                $sql = 'UPDATE `'._DB_PREFIX_.'leoslideshow_slides` SET `position` = '.(int)$position.'
                        WHERE `id_leoslideshow_slides` = '.(int)$id_slide;
                $result = Db::getInstance()->execute($sql);
            }
            $this->clearCache();
            die(Tools::jsonEncode($result));
        }

        if (Tools::getValue('action') && Tools::getValue('action') == 'deleteSlider') {
            $id_slide = Tools::getValue('id_slide');
            $mod_slide = new LeoSlideshowSlide((int)$id_slide);
            if (!$mod_slide->delete()) {
                $result = array('error' => 1, 'text' => $this->l('The slide could not be delete.'));
            }

            $this->clearCache();
            die(Tools::jsonEncode($result));
        }

        if (Tools::getValue('action') && Tools::getValue('action') == 'duplicateSlider') {
            if (LeoSlideshowConfig::getPermission() == false) {
                # CHECK VERSION
                $mod_slide = new LeoSlideshowSlide();
                $number_group = $mod_slide->count();

                if ($number_group >= 2) {
                    $errors = $this->display(__FILE__, 'error_freemodule1.tpl');

                    $result = array('error' => 2, 'text' => $errors );
                    die(Tools::jsonEncode($result));
                }
            }
            
            
            $mod_slide = new LeoSlideshowSlide((int)Tools::getValue('id_slide'));
            $mod_new_slide = new LeoSlideshowSlide();

            $defined = $mod_new_slide->getDefinition('LeoSlideshowSlide');

            foreach ($defined['fields'] as $ke => $val) {
                # module validation
                unset($val);

                if ($ke == 'id') {
                    continue;
                }

                if ($ke == 'title') {
                    $tmp = array();
                    foreach ($mod_slide->title as $kt => $vt) {
                        $tmp[$kt] = $this->l('Duplicate of').' '.$vt;
                    }

                    $mod_new_slide->{$ke} = $tmp;
                } else {
                    $mod_new_slide->{$ke} = $mod_slide->{$ke};
                }
            }
            if (!$mod_new_slide->add()) {
                $result = array('error' => 1, 'text' => $this->l('The slide could not be duplicate.'));
            }

            $this->clearCache();
            die(Tools::jsonEncode($result));
        }
    }

    public function getSliderGroupById($id)
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                    SELECT *
                    FROM '._DB_PREFIX_.'leoslideshow_groups gr
                    WHERE gr.id_shop = '.(int)$id_shop.'
                    AND gr.id_leoslideshow_groups = '.(int)$id);
    }

    public function processHookCallBack($group_id)
    {
        $media_dir = $this->getMediaDir();
        $this->context->controller->addJS($this->_path.'js/jquery.themepunch.enablelog.js');
        $this->context->controller->addJS($this->_path.'js/jquery.themepunch.revolution.js');
        //$this->context->controller->addJS($this->_path.'js/jquery.themepunch.revolution.min.js');
        $this->context->controller->addJS($this->_path.'js/jquery.themepunch.tools.min.js');
        $this->context->controller->addCSS(__PS_BASE_URI__.$media_dir.'css/typo/typo.css', 'all');

        $slideshow_group_obj = new LeoSlideshowGroup($group_id);
        
        if ($slideshow_group_obj->active_ap != 1) {
            $slideshow_group_obj->hook = '';
            $slideshow_group_obj->active_ap = 1;
            $slideshow_group_obj->save();
        }
        
        $res = $this->prepareHookForApPageBuilder($group_id);
        if (!$res) {
            return false;
        } elseif ($res === 2) {
            return $this->l('If you load this Slideshow (not active) from ApPageBuilder module, please access ApPageBuilder module then delete it');
        }

        // return $this->display(__FILE__, ''.$this->name.'.tpl', $this->getCacheId($group_id.'_'.$this->name));
        return $this->fetch('module:leoslideshow/views/templates/front/leoslideshow.tpl', $this->getCacheId($group_id.'_'.$this->name));
    }

    private function prepareHookForApPageBuilder($group_id)
    {
        $tpl = 'module:leoslideshow/views/templates/front/leoslideshow.tpl';
        if (!$this->isCached($tpl, $this->getCacheId($group_id.'_'.$this->name))) {
            if (!is_dir(_PS_ROOT_DIR_.'/cache/'.$this->name)) {
                mkdir(_PS_ROOT_DIR_.'/cache/'.$this->name, 0755, true);
            }

            //get slider via hookname
            $group = $this->getSliderGroupById($group_id);
            if (!$group) {
                return false;
            } elseif ($group['active'] != 1) {
                return 2;
            }
            $sliders = $this->getSlides($group['id_leoslideshow_groups'], 1);
            $sliders = LeoSlideshowSlide::filterSlider($sliders, $this->slider_data);
            if (!$sliders) {
                return false;
            }

            $slider_params = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($group['params']), true);
            $slider_params = array_merge($group, $slider_params);
            $slider_params = array_merge($this->group_data, $slider_params);
            $mod_group = new LeoSlideshowGroup();
            $mod_group->setModule($this);
            $slider_params = $mod_group->setData($slider_params)->beforeLoad()->loadFrontEnd();

            if (isset($slider_params['fullwidth']) && (!empty($slider_params['fullwidth']) || $slider_params['fullwidth'] == 'boxed')) {
                $slider_params['image_cropping'] = false;
            }

            $slider_params['hide_navigator_after'] = $slider_params['show_navigator'] ? 0 : $slider_params['hide_navigator_after'];
            $slider_params['slider_class'] = trim(isset($slider_params['fullwidth']) && !empty($slider_params['fullwidth']) ? $slider_params['fullwidth'] : 'boxed');
            $slider_fullwidth = $slider_params['slider_class'] == 'boxed' ? 'off' : 'on';

            // generate back-ground
            if ($slider_params['background_image'] && $slider_params['background_url'] && file_exists($this->img_path.$slider_params['background_url'])) {
                $slider_params['background'] = 'background: url('.$this->img_url.$slider_params['background_url'].') no-repeat scroll left 0 '.$slider_params['background_color'].';';
            } else {
                $slider_params['background'] = 'background-color:'.$slider_params['background_color'];
            }

            //include library genimage
            if (!class_exists('PhpThumbFactory')) {
                require_once _PS_MODULE_DIR_.'leoslideshow/libs/phpthumb/ThumbLib.inc.php';
            }

            $white_main_img = __PS_BASE_URI__.'modules/'.$this->name.'/views/img/white50.png';

            //process slider

            foreach ($sliders as $key => $slider) {
                $slider['layers'] = array();
                $slider['params'] = array_merge($this->slider_data, Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slider['params']), true));
                $slider['layersparams'] = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slider['layersparams']), true);
                $slider['video'] = Tools::jsonDecode(LeoSlideshowSlide::base64Decode($slider['video']), true);

                $slider['data_link'] = '';
                if ($slider['params']['enable_link'] && $slider['link']) {
                    // $slider['data_link'] = 'data-link="'.$slider['link'].'"';
                    // $slider['data_target'] = 'data-target="'.LeoSlideshowSlide::renderTarget($slider['params']['target']).'"';
                    $slider['data_link'] = $slider['link'];
                    $slider['data_target'] = LeoSlideshowSlide::renderTarget($slider['params']['target']);
                } else {
                    $slider['data_target'] = '';
                }

                $slider['data_delay'] = (int)$slider['params']['delay'];

                //videoURL
                $slider['videoURL'] = '';
                $slider['video']['active'] = '0';
                if ($slider['video']['usevideo'] == 'youtube' || $slider['video']['usevideo'] == 'vimeo') {
                    $slider['video']['active'] = '1';
                    $slider['video']['videoURL'] = Tools::getCurrentUrlProtocolPrefix() . 'player.vimeo.com/video/'.$slider['video']['videoid'].'/';
                    if ($slider['video']['usevideo'] == 'youtube') {
                        $slider['video']['videoURL'] = Tools::getCurrentUrlProtocolPrefix() . 'www.youtube.com/embed/'.$slider['video']['videoid'].'/';
                    }
                }

                if ($slider['video']['videoauto'] == 1) {
                    $slider['video']['videoauto'] = 'autoplay=1';
                } else {
                    $slider['video']['videoauto'] = 'autoplay=0';
                }

                $slider['background_color'] = '';
                if (isset($slider_params['background_color']) && $slider_params['background_color']) {
                    $slider['background_color'] = $slider_params['background_color'];
                }
                if (isset($slider['video']['background_color']) && $slider['video']['background_color']) {
                    $slider['background_color'] = $slider['video']['background_color'];
                }

                LeoSlideshowSlide::getBackground($slider_params, $slider);

                if ($slider['image'] == '') {
                    $slider['image'] = 'views/img/blank.gif';
                }

                if ($slider_params['image_cropping']) {
                    //gender main_image
                    if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                        $slider['main_image'] = $this->renderThumb($slider['image'], $slider_params['width'], $slider_params['height']);
                    } else {
                        $slider['main_image'] = $white_main_img;
                    }

                    if ($slider['thumbnail'] && file_exists($this->img_path.$slider['thumbnail'])) {
                        # module validation
                        //$slider['thumbnail'] = $this->renderThumb($slider['thumbnail'], $sliderParams['thumbnail_width'], $sliderParams['thumbnail_height']);
                    } else if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                        # module validation
                        //$slider['thumbnail'] = $this->renderThumb($slider['image'], $sliderParams['thumbnail_width'], $sliderParams['thumbnail_height']);
                    } else {
                        $slider['thumbnail'] = $white_main_img;
                    }
                } else {
                    $slider['main_image'] = __PS_BASE_URI__.'modules/leoslideshow'.'/views/img/blank.gif';

                    if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                        $slider['main_image'] = $this->img_url.$slider['image'];
                    }else if (((Tools::substr($slider['image'], 0, 7) == 'http://')) || ((Tools::substr($slider['image'], 0, 8) == 'https://')) || ((Tools::substr($slider['image'], 0, 10) == 'data:image'))) {
  		        # Image External Link
                        $slider['main_image'] = $slider['image'];
                    }

                    if ($slider['thumbnail'] && file_exists($this->img_path.$slider['thumbnail'])) {
                        $slider['thumbnail'] = $this->img_url.$slider['thumbnail'];
                    } else if ($slider['image'] && file_exists($this->img_path.$slider['image'])) {
                        $slider['thumbnail'] = $slider['main_image'];
                    }else if (((Tools::substr($slider['thumbnail'], 0, 7) == 'http://')) || ((Tools::substr($slider['thumbnail'], 0, 8) == 'https://')) || ((Tools::substr($slider['thumbnail'], 0, 10) == 'data:image'))) {
                        # Image External Link
                        $slider['thumbnail'] = $slider['thumbnail'];
                    } else {
                        $slider['thumbnail'] = $white_main_img;
                    }
                }

                if ($slider['layersparams']) {
                    foreach ($slider['layersparams'] as $k => &$layer_css) {
                        if ($layer_css['layer_status'] == 0) {
                            unset($slider['layersparams'][$k]);
                            continue;
                        }

                        $layer_css_val = '';
                        if (isset($layer_css['layer_font_size']) && $layer_css['layer_font_size']) {
                            $layer_css_val = 'font-size:'.$layer_css['layer_font_size'];
                        }
                        if (isset($layer_css['layer_background_color']) && $layer_css['layer_background_color']) {
                            $layer_css_val .= ($layer_css_val != '' ? ';' : '').'background-color:'.$layer_css['layer_background_color'];
                        }
                        if (isset($layer_css['layer_color']) && $layer_css['layer_color']) {
                            $layer_css_val .= ($layer_css_val != '' ? ';' : '').'color:'.$layer_css['layer_color'];
                        }
                        $layer_css['css'] = $layer_css_val;
                        if (!isset($layer_css['layer_link'])) {
                            $layer_css['layer_link'] = $slider['link'];
                        } else {
                            $layer_css['layer_link'] = str_replace('_ASM_', '&', $layer_css['layer_link']);
                        }

                        $layer_css['layer_target'] = LeoSlideshowSlide::renderTarget($layer_css['layer_target']);
                        if (isset($layer_css['layer_caption']) && $layer_css['layer_caption']) {
                            $layer_css['layer_caption'] = utf8_decode($layer_css['layer_caption']);
                        }
                        
                        if (isset($layer_css['layer_type']) && $layer_css['layer_type'] == 'image') {
                            if (((Tools::substr($layer_css['layer_content'], 0, 4) != 'http')) && ((Tools::substr($layer_css['layer_content'], 0, 8) != 'data:image'))) {
                                $layer_css['layer_content'] = $this->img_url. $layer_css['layer_content'];
                            }
                        }
                    }
                }
                $sliders[$key] = $slider;
            }
            $slider_params['start_with_slide'] = LeoSlideshowGroup::showStartWithSlide($slider_params['start_with_slide'], $sliders);
            $sliders = LeoSlideshowSlide::showBulletCustomHTML($slider_params, $sliders);
            $slider_params['rtl'] = $this->context->language->is_rtl;

            if (!isset($this->load_from_appagebuilder)) {
                // Default load via appabuilder
                $this->load_from_appagebuilder = 1;
            }
            $this->smarty->assign(array(
                'sliderParams' => $slider_params,
                'sliders' => $sliders,
                'sliderIDRand' => rand(20, rand()),
                'sliderFullwidth' => $slider_fullwidth,
                'sliderImgUrl' => $this->img_url,
                'load_from_appagebuilder' => $this->load_from_appagebuilder,
                'rand_num' => uniqid(),
            ));
        }
        return true;
    }
    
    /**
     * Run only one when install/change Theme_of_Leo
     */
    public function hookActionAdminBefore($params)
    {
        if (isset($params) && isset($params['controller']) && isset($params['controller']->theme_manager)) {
            // Validate : call hook from theme_manager
        } else {
            // Other module call this hook -> duplicate data
            return;
        }
        
        $this->unregisterHook('actionAdminBefore');
        
        # FIX : update Prestashop by 1-Click module -> NOT NEED RESTORE DATABASE
        $ap_version = Configuration::get('AP_CURRENT_VERSION');
        if ($ap_version != false) {
            $ps_version = Configuration::get('PS_VERSION_DB');
            $versionCompare =  version_compare($ap_version, $ps_version);
            if ($versionCompare != 0) {
                // Just update Prestashop
                Configuration::updateValue('AP_CURRENT_VERSION', $ps_version);
                return;
            }
        }
        
        # WHENE INSTALL THEME, INSERT HOOK FROM DATASAMPLE IN THEME
        $hook_from_theme = false;
        if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
            $sample = new Datasample();
            if ($sample->processHook($this->name)) {
                $hook_from_theme = true;
            };
        }
        
        # INSERT HOOK FROM MODULE_DATASAMPLE
        if ($hook_from_theme == false) {
            $this->registerLeoHook();
        }
        
        # WHEN INSTALL MODULE, NOT NEED RESTORE DATABASE IN THEME
        $install_module = (int)Configuration::get('AP_INSTALLED_LEOSLIDESHOW', 0);
        if ($install_module) {
            Configuration::updateValue('AP_INSTALLED_LEOSLIDESHOW', '0');    // next : allow restore sample
            return;
        }
        
        # INSERT DATABASE FROM THEME_DATASAMPLE
        if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
            $sample = new Datasample();
            $sample->processImport($this->name);
        }
    }
    
    /**
     * Common method
     * Resgister all hook for module
     */
    public function registerLeoHook()
    {
        $res = true;
        $res &= $this->registerHook('header');
        $res &= $this->registerHook('actionShopDataDuplication');
        foreach ($this->hook_support as $value) {
            $res &= $this->registerHook($value);
        }
        # Multishop create new shop
        $res &= $this->registerHook('actionAdminShopControllerSaveAfter');
        $res &= $this->registerHook('actionAdminControllerSetMedia');
        
        return $res;
    }

    /**
     * Common method
     * Unresgister all hook for module
     */
    public function unregisterLeoHook()
    {
        $res = true;
        $res &= $this->unregisterHook('header');
        $res &= $this->unregisterHook('actionShopDataDuplication');
        foreach ($this->hook_support as $value) {
            $res &= $this->unregisterHook($value);
        }
        $res &= $this->unregisterHook('actionAdminShopControllerSaveAfter');
        $res &= $this->unregisterHook('actionAdminControllerSetMedia');
        return $res;
    }
    
    /**
     * @Action Create new shop, choose theme then auto restore datasample.
     */
    public function hookActionAdminShopControllerSaveAfter($param)
    {
        if (Tools::getIsset('controller') !== false && Tools::getValue('controller') == 'AdminShop'
                && Tools::getIsset('submitAddshop') !== false && Tools::getValue('submitAddshop')
                && Tools::getIsset('theme_name') !== false && Tools::getValue('theme_name')) {
            $shop = $param['return'];

            if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
                require_once(_PS_MODULE_DIR_.'appagebuilder/libs/Helper.php');
                require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
                $sample = new Datasample();
                apPageHelper::$id_shop = $shop->id;
                $sample->_id_shop = $shop->id;
                $sample->processImport('leoslideshow');
            }
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookActionAdminControllerSetMedia()
    {
        $this->autoRestoreSampleData();
    }
    
    public function processCMS()
    {
        $cms = $this->smarty->smarty->getVariable('cms')->value;
        $cms['content'] = $this->doShortCode($cms['content']);
        $this->smarty->smarty->assign('cms', $cms);
    }
    
    public function getContentCMS($atts, $content = null)
    {
        $group_id = isset($atts['group_id']) ? (int)$atts['group_id'] : 0;
        $id_shop = (int)Context::getContext()->shop->id;
        
        $where = ' WHERE id_leoslideshow_groups = '.(int)$group_id.' AND id_shop = ' . (int)$id_shop;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_leoslideshow_groups FROM `'._DB_PREFIX_.'leoslideshow_groups` '.$where);
        
        
        if (is_array($result) && !empty($result)) {
            $str_group_id = '';
            foreach ($result as $slide) {
                $str_group_id .= ($str_group_id == '') ? $slide['id_leoslideshow_groups'] : ','.$slide['id_leoslideshow_groups'];
            }
            $this->load_from_appagebuilder = 0;
            $content = $this->processHookCallBack($str_group_id);
        } else {
            $content = $this->display(__FILE__, 'cms_error.tpl');
        }

        return $content;
    }
    
    public static function doShortCode($content, $hook_name = '')
    {
        // validate module
        unset($hook_name);
        $pattern = '\[(\[?)(leoslideshow)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
        return preg_replace_callback("/$pattern/s", array(__CLASS__, 'doShortCodeTag'), $content);
    }
    public static function doShortCodeTag($m)
    {
        $attr = self::doShortcodeParseAtts($m[3]);
        if (isset($m[5])) {
            return $m[1] . call_user_func(array(new LeoSlideshow(), 'getContentCMS'), $attr, $m[5]) . $m[6];
        } else {
            return $m[1] . call_user_func(array(new LeoSlideshow(), 'getContentCMS'), $attr, null) . $m[6];
        }
    }
    
    public static function doShortcodeParseAtts($text)
    {
        $atts = array();
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1])) {
                    $atts[Tools::strtolower($m[1])] = stripcslashes($m[2]);
                } elseif (!empty($m[3])) {
                    $atts[Tools::strtolower($m[3])] = stripcslashes($m[4]);
                } elseif (!empty($m[5])) {
                    $atts[Tools::strtolower($m[5])] = stripcslashes($m[6]);
                } elseif (isset($m[7]) and Tools::strlen($m[7])) {
                    $atts[] = stripcslashes($m[7]);
                } elseif (isset($m[8])) {
                    $atts[] = stripcslashes($m[8]);
                }
            }
        } else {
            $atts = ltrim($text);
        }
        return $atts;
    }
    
    // public function isCached($template, $cache_id = null, $compile_id = null)
    // {
        // if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
            // return false;
        // }
        // return parent::isCached($template, $cache_id, $compile_id);
    // }

    /**
     * FIX BUG 1.7.3.3 : install theme lose hook displayHome, displayLeoProfileProduct
     * because ajax not run hookActionAdminBefore();
     */
    public function autoRestoreSampleData()
    {
        if (Hook::isModuleRegisteredOnHook($this, 'actionAdminBefore', (int)Context::getContext()->shop->id)) {
            $theme_manager = new stdclass();
            $theme_manager->theme_manager = 'theme_manager';
            $this->hookActionAdminBefore(array(
                'controller' => $theme_manager,
            ));
        }
    }

    //DONGND:: update direction css, js, img for 1.7.4.0
    public function getMediaDir()
    {
        $media_dir = '';
        if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
            $media_dir = 'modules/'.$this->name.'/views/';
        } else {
            $media_dir = 'modules/'.$this->name.'/';
        }
        return $media_dir;
    }
    
    private function exportGroup()
    {
        $id_group = (int)Tools::getValue('id_group');
        
        $group = LeoSlideshowGroup::getGroupByID($id_group);
        $sliders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT lsll.`id_lang`, lsl.`id_leoslideshow_slides` as id, lsl.*,lsll.*
                    FROM '._DB_PREFIX_.'leoslideshow_slides lsl
                    LEFT JOIN '._DB_PREFIX_.'leoslideshow_slides_lang lsll ON (lsl.id_leoslideshow_slides = lsll.id_leoslideshow_slides)
                    WHERE lsl.id_group = '.(int)$id_group.'
                    ORDER BY lsl.position');
        
        $language_field = array('title', 'link', 'image', 'thumbnail', 'video', 'layersparams');

        $languages = Language::getLanguages();
        $lang_list = array();
        foreach ($languages as $lang) {
            # module validation
            $lang_list[$lang['id_lang']] = $lang['iso_code'];
        }

        foreach ($sliders as $slider) {
            $curent_lang = 'en';
            foreach ($slider as $key => $value) {
                if ($key == 'id_lang') {
                    $curent_lang = $lang_list[$value];
                    continue;
                }
                if (in_array($key, $language_field)) {
                    $group['sliders'][$slider['id']][$key][$curent_lang] = $value;
                } else {
                    # module validation
                    $group['sliders'][$slider['id']][$key] = $value;
                }
            }
        }
        header('Content-Type: plain/text');
        header('Content-Disposition: Attachment; filename=export_group_'.Tools::getValue('id_group').'_'.time().'.txt');
        header('Pragma: no-cache');
        die(LeoSlideshowSlide::base64Encode(Tools::jsonEncode($group)));
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     */
    public function getPermission($variable, $employee = null)
    {
        if ($variable == 'configure') {
            // Allow see form if permission is : configure, view
            $configure = Module::getPermissionStatic($this->id, 'configure', $employee);
            $view = Module::getPermissionStatic($this->id, 'view', $employee);
            return ($configure || $view);
        }
        
        return Module::getPermissionStatic($this->id, $variable, $employee);
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     */
    public function access($action)
    {
        $employee = null;
        return Module::getPermissionStatic($this->id, $action, $employee);
    }
}
