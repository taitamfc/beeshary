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

require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoFrameworkHelper.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/libs/google_fonts.php');

/**
 *
 * NOT extends ModuleAdminControllerCore, because override tpl : ROOT/modules/appagebuilder/views/templates/admin/ap_page_builder_theme_configuration/helpers/form/form.tpl
 */
class AdminApPageBuilderThemeConfigurationController extends ModuleAdminController
{
    protected $max_image_size = null;
    public $module_name = 'appagebuilder';
    public $img_path;
    public $folder_name;
    public $module_path;
    public $tpl_path;
    public $theme_dir;
    
    /**
     * @var Array $overrideHooks
     */
    protected $themeName;
    
    /**
     * @var Array $overrideHooks
     */
    protected $themePath = '';
    
    /**
     * save config
     */
    public $submitSaveSetting = false;


    public function __construct()
    {
        parent::__construct();
        $this->theme_dir = _PS_THEME_DIR_;
        $this->folder_name = Tools::getIsset('imgDir') ? Tools::getValue('imgDir') : 'images';
        $this->bootstrap = true;
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->themeName = apPageHelper::getThemeName();
        $this->img_path = apPageHelper::getImgThemeDir($this->folder_name);
        $this->img_url = apPageHelper::getImgThemeUrl($this->folder_name);
        $this->className = 'ApPageBuilderPositionsModel';
        $this->context = Context::getContext();
        $this->module_path = __PS_BASE_URI__.'modules/'.$this->module_name.'/';
        $this->tpl_path = _PS_ROOT_DIR_.'/modules/'.$this->module_name.'/views/templates/admin';
        $this->module_path = __PS_BASE_URI__.'modules/appagebuilder/';
//        $this->module_path_resource = $this->module_path.'views/';
        $this->themePath = _PS_ALL_THEMES_DIR_.$this->themeName.'/';
    }
    
    public function initPageHeaderToolbar()
    {
        $this->context->controller->addJquery();
        $this->context->controller->addJqueryUI('ui.sortable');     // FILE FORM.js required this
        $this->context->controller->addJqueryUI('ui.draggable');     // FILE FORM.js required this
        $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/form.js');
        $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/home.js');
        
        Context::getContext()->controller->addJqueryPlugin('colorpicker');
        
        Media::addJsDef(array(
            'ap_controller'  => 'AdminApPageBuilderThemeConfigurationController',
        ));
        
        $this->context->controller->addCss(apPageHelper::getCssAdminDir().'admin/style_AdminApPageBuilderThemeConfiguration.css', 'all');
        parent::initPageHeaderToolbar();
    }
    
    /**
     * OVERRIDE ROOT\classes\controller\AdminController.php
     * Assign smarty variables for all default views, list and form, then call other init functions
     */
    public function initContent()
    {
        if (!$this->viewAccess()) {
            $this->errors[] = $this->l('You do not have permission to view this.');
            return;
        }

        $this->getLanguages();
        $this->initToolbar();
        $this->initTabModuleList();
        $this->initPageHeaderToolbar();
        
        $this->content .= $this->renderForm();
        // FIXME: Sorry. I'm not very proud of this, but no choice... Please wait sf refactoring to solve this.
        if (get_class($this) != 'AdminCarriersController') {
            $this->content .= $this->renderModulesList();
        }
        $this->content .= $this->renderKpis();
        $this->content .= $this->renderList();
        $this->content .= $this->renderOptions();

        // if we have to display the required fields form
        if ($this->required_database) {
            $this->content .= $this->displayRequiredFields();
        }

        $this->context->smarty->assign(array(
            'maintenance_mode' => !(bool)Configuration::get('PS_SHOP_ENABLE'),
            'debug_mode' => (bool)_PS_MODE_DEV_,
            'content' => $this->content,
            'lite_display' => $this->lite_display,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    public function renderForm()
    {
        $soption = array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled'),
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled'),
            )
        );
        
        $tskins = LeoFrameworkHelper::getSkins($this->themeName);
//        $directions = LeoFrameworkHelper::getLayoutDirections($this->themeName);
        
        $this->lang = true;
        $skins = array();
        $skins[] = array('name' => $this->l('Default'), 'id' => 'default');
        $skins = array_merge_recursive($skins, $tskins);

        $this->initToolbar();
        $this->context->controller->addJqueryUI('ui.sortable');
        
        $sample = new Datasample();
        $moduleList = $sample->getModuleList();
        
        $fields_form = array(
//            'legend' => array(
//                'title' => $this->l('Ap Theme Configuration'),
//                'icon' => 'icon-folder-close'
//            ),
            'input' => array(
                'config' => array(
                    'type' => 'tabConfig',
                    'name' => 'title',
                    'values' => array(
                        'aprow_general' => $this->l('General Setting'),
                        'aprow_pages' => $this->l('Pages Setting'),
//                        'aprow_font' => $this->l('Font Setting'),
                        'aprow_font_setup' => $this->l('Google Font'),
                        'aprow_font_option' => $this->l('Font Setting'),
                        'aprow_data' => $this->l('Data Sample'),
                    ),
                    'default' => Tools::getValue('tab_open') ? Tools::getValue('tab_open') : 'aprow_general',
                    'save' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Css With Prestashop Standard'),
                    'name' => $this->getConfigName('load_css_type'),
                    'default' => 0,
                    'values' => $soption,
                    'desc' => $this->l('Use prestashop standard to load css or load with sperator link in header.tpl. If you want to load css follow prestashop standard please drag Appagebuilder module in end of position header'),
                    'form_group_class' => 'aprow_general',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Move Appagebuilder To End of hook Header'),
                    'name' => $this->getConfigName('move_end_header'),
                    'default' => 0,
                    'values' => $soption,
                    'desc' => $this->l('If you select yes, we will move Appagebuilder to end of hook header'),
                    'form_group_class' => 'aprow_general',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Responsive'),
                    'name' => $this->getConfigName('enable_responsive'),
                    'default' => 0,
                    'values' => $soption,
                    'form_group_class' => 'aprow_general',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Layout Width'),
                    'name' => $this->getConfigName('layout_width'),
                    'default' => 'auto',
                    'cast' => 'intval',
                    'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                    'form_group_class' => 'aprow_general',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Panel Tool'),
                    'name' => $this->getConfigName('paneltool'),
                    'default' => 0,
                    'values' => $soption,
                    'hint' => $this->l('Whethere to display Panel Tool appearing on left of site.'),
                    'form_group_class' => 'aprow_general',
                    'desc' => $this->l('Yes : Auto load library JS jquery.cooki-plugin.js'),
                ),
				array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Display Sub Category'),
                    'name' => $this->getConfigName('subcategory'),
                    'default' => 0,
                    'values' => $soption,
                    'hint' => $this->l('Whethere to display list of sub category in category page .'),
                    'form_group_class' => 'aprow_general',
                    'desc' => $this->l('Available with categories that have subcategories'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Float Header'),
                    'name' => $this->getConfigName('enable_fheader'),
                    'default' => 0,
                    'values' => $soption,
                    'hint' => $this->l('Select NO when you don not want your header float'),
                    'form_group_class' => 'aprow_general',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Back to Top'),
                    'name' => $this->getConfigName('backtop'),
                    'default' => 0,
                    'values' => $soption,
                    'hint' => $this->l('Show a Scroll To Top button.'),
                    'form_group_class' => 'aprow_general',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Default Skin'),
                    'name' => $this->getConfigName('default_skin'),
                    'default' => 'default',
                    'options' => array(
                        'query' => $skins,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'aprow_general',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Products Listing Mode'),
                    'name' => $this->getConfigName('listing_grid_mode'),
                    'default' => 'grid',
                    'options' => array('query' => array(
                            array('id' => 'grid', 'name' => $this->l('Grid Mode')),
                            array('id' => 'list', 'name' => $this->l('List Mode')),
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('Display Products In List Mode Or Grid Mode In Product List....'),
                    'form_group_class' => 'aprow_pages',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Columns in Default Module On Desktop'),
                    'name' => $this->getConfigName('listing_product_column_module'),
                    'default' => ' ',
                    'options' => array('query' => array(
                            array('id' => '', 'name' => $this->l('Default')),
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns')),
                            array('id' => '4', 'name' => $this->l('4 Columns')),
                            array('id' => '5', 'name' => $this->l('5 Columns')),
                            array('id' => '6', 'name' => $this->l('6 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in default module of prestashop.'),
                    'form_group_class' => 'aprow_pages',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Columns in Product List page On Desktop'),
                    'name' => $this->getConfigName('listing_product_column'),
                    'default' => ' ',
                    'options' => array('query' => array(
                            array('id' => '', 'name' => $this->l('Default')),
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns')),
                            array('id' => '4', 'name' => $this->l('4 Columns')),
                            array('id' => '5', 'name' => $this->l('5 Columns')),
                            array('id' => '6', 'name' => $this->l('6 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'aprow_pages',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Large devices (>=992px)'),
                    'name' => $this->getConfigName('listing_product_largedevice'),
                    'default' => ' ',
                    'options' => array('query' => array(
                            array('id' => '', 'name' => $this->l('Default')),
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns')),
                            array('id' => '4', 'name' => $this->l('4 Columns')),
                            array('id' => '5', 'name' => $this->l('5 Columns')),
                            array('id' => '6', 'name' => $this->l('6 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'aprow_pages',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Medium devices - Tablet (>=768px)'),
                    'name' => $this->getConfigName('listing_product_tablet'),
                    'default' => '',
                    'options' => array('query' => array(
                            array('id' => '', 'name' => $this->l('Default')),
                            array('id' => '1', 'name' => $this->l('1 Column')),
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns')),
                            array('id' => '4', 'name' => $this->l('4 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'aprow_pages',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Small devices (>=576px)'),
                    'name' => $this->getConfigName('listing_product_smalldevice'),
                    'default' => '',
                    'options' => array('query' => array(
                            array('id' => '', 'name' => $this->l('Default')),
                            array('id' => '1', 'name' => $this->l('1 Column')),
                            array('id' => '2', 'name' => $this->l('2 Columns')),
                            array('id' => '3', 'name' => $this->l('3 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'aprow_pages',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Extra Small devices (<567px)'),
                    'name' => $this->getConfigName('listing_product_extrasmalldevice'),
                    'default' => '',
                    'options' => array('query' => array(
                            array('id' => '', 'name' => $this->l('Default')),
                            array('id' => '1', 'name' => $this->l('1 Column')),
                            array('id' => '2', 'name' => $this->l('2 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'aprow_pages',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Grid Columns On Smart Phone (<480px)'),
                    'name' => $this->getConfigName('listing_product_mobile'),
                    'default' => '',
                    'options' => array('query' => array(
                            array('id' => '', 'name' => $this->l('Default')),
                            array('id' => '1', 'name' => $this->l('1 Column')),
                            array('id' => '2', 'name' => $this->l('2 Columns'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'desc' => $this->l('How many column display in grid mode of product list.'),
                    'form_group_class' => 'aprow_pages',
                ),
                
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Detail Tab Type'),
                    'name' => $this->getConfigName('enable_ptab'),
                    'default' => 'default',
                    'options' => array('query' => array(
                            array('id' => 'default', 'name' => $this->l('Default')),
                            array('id' => 'tab', 'name' => $this->l('Tab')),
                            array('id' => 'accordion', 'name' => $this->l('Accordion'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
//                    'desc' => $this->l('Select no when you don not want to use tab in product detail'),
                    'form_group_class' => 'aprow_pages',
                ),
//                array(
//                    'type' => 'switch',
//                    'label' => $this->l('Enable Custom Font'),
//                    'name' => $this->getConfigName('enable_customfont'),
//                    'default' => 0,
//                    'values' => $soption,
//                    'form_group_class' => 'aprow_font',
//                ),
//                /* FONT 1 */
//                array(
//                    'type' => 'text',
//                    'label' => $this->l('Google Link'),
//                    'name' => $this->getConfigName('engine1_google_link'),
//                    'default' => '',
//                    'identifier' => 'id',
//                    'class' => 'localfont',
//                    'desc' => $this->l('For Example: http://fonts.googleapis.com/css?family=Gorditas'),
//                    'form_group_class' => 'aprow_font',
//                ),
//                array(
//                    'type' => 'text',
//                    'label' => $this->l('Google Font Family'),
//                    'name' => $this->getConfigName('engine1_google_font'),
//                    'default' => '',
//                    'desc' => $this->l('For Example: "Gorditas", "cursive"'),
//                    'identifier' => 'id',
//                    'form_group_class' => 'aprow_font',
//                ),
//                array(
//                    'type' => 'textarea',
//                    'label' => $this->l('Css Selector'),
//                    'name' => $this->getConfigName('font1_selector'),
//                    'default' => '',
//                    'rows' => '6',
//                    'cols' => '',
//                    'desc' => $this->l('Example: body, h1,h2,h3, #yourstyle, .myrule div'),
//                    'form_group_class' => 'aprow_font',
//                ),
//                array(
//                    'type' => 'html',
//                    'name' => 'default_html',
//                    'html_content' => '<hr/>',
//                    'default' => '',
//                    'form_group_class' => 'aprow_font',
//                    'save' => false,
//                ),
//                /* FONT 2 */
//                array(
//                    'type' => 'text',
//                    'label' => $this->l('Google Link'),
//                    'name' => $this->getConfigName('engine2_google_link'),
//                    'default' => '',
//                    'identifier' => 'id',
//                    'class' => 'localfont',
//                    'form_group_class' => 'aprow_font',
//                ),
//                array(
//                    'type' => 'text',
//                    'label' => $this->l('Google Font Family'),
//                    'name' => $this->getConfigName('engine2_google_font'),
//                    'default' => '',
//                    'identifier' => 'id',
//                    'form_group_class' => 'aprow_font',
//                ),
//                array(
//                    'type' => 'textarea',
//                    'label' => $this->l('Css Selector'),
//                    'name' => $this->getConfigName('font2_selector'),
//                    'default' => '',
//                    'rows' => '6',
//                    'cols' => '',
//                    'form_group_class' => 'aprow_font',
//                ),
//                array(
//                    'type' => 'html',
//                    'name' => 'default_html',
//                    'html_content' => '<hr/>',
//                    'default' => '',
//                    'form_group_class' => 'aprow_font',
//                    'save' => false,
//                ),
//                /* FONT 3 */
//                array(
//                    'type' => 'text',
//                    'label' => $this->l('Google Link'),
//                    'name' => $this->getConfigName('engine3_google_link'),
//                    'default' => '',
//                    'identifier' => 'id',
//                    'class' => 'localfont',
//                    'form_group_class' => 'aprow_font',
//                ),
//                array(
//                    'type' => 'text',
//                    'label' => $this->l('Google Font Family'),
//                    'name' => $this->getConfigName('engine3_google_font'),
//                    'default' => '',
//                    'identifier' => 'id',
//                    'form_group_class' => 'aprow_font',
//                ),
//                array(
//                    'type' => 'textarea',
//                    'label' => $this->l('Css Selector'),
//                    'name' => $this->getConfigName('font3_selector'),
//                    'default' => '',
//                    'rows' => '6',
//                    'cols' => '',
//                    'form_group_class' => 'aprow_font',
//                ),
                array(
                    'type' => 'font_setup',
                    'name' => 'title',
                    'values' => array(''),
                    'list_google_font' => array_keys(GoogleFont::getAllGoogleFonts()),
                    'default' => Tools::getValue('tab_open') ? Tools::getValue('tab_open') : 'aprow_general',
                    'save' => false,
                    'form_group_class' => 'aprow_font_setup',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Load Font'),
                    'name' => $this->getConfigName('enable_loadfont'),
                    'default' => 0,
                    'values' => $soption,
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H1 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => $this->getConfigName('font_h1'),
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => $this->getConfigName('h1_font_family'),
                            'default' => apPageHelper::getFontFamily('default'),
                            'options' => array(
                                'query' => apPageHelper::getFontFamily(),
                                'id' => 'id',
                                'name' => 'name'),
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => $this->getConfigName('h1_font_size'),
                            'default' => '36',
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => $this->getConfigName('h1_height'),
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => $this->getConfigName('h1_font_weight'),
                            'default' => $this->getFontWeight('default'),
                            'options' => array(
                                'query' => $this->getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => $this->getConfigName('h1_font_style'),
                            'default' => $this->getFontStyle('default'),
                            'options' => array(
                                'query' => $this->getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
//                        array(
//                            'type' => 'color',
//                            'name' => $this->getConfigName('h1_font_color'),
//                            'label' => $this->l('Font Color'),
//                            'lang' => false,
//                            'default' => '',
//                            'class' => 'input-level2 temp_hpcolor',
//                            'form_group_class' => 'row-level2',
//                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'aprow_font_option',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H2 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => $this->getConfigName('font_h2'),
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => $this->getConfigName('h2_font_family'),
                            'default' => apPageHelper::getFontFamily('default'),
                            'options' => array(
                                'query' => apPageHelper::getFontFamily(),
                                'id' => 'id',
                                'name' => 'name'),
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => $this->getConfigName('h2_font_size'),
                            'default' => '30',
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => $this->getConfigName('h2_height'),
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => $this->getConfigName('h2_font_weight'),
                            'default' => $this->getFontWeight('default'),
                            'options' => array(
                                'query' => $this->getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => $this->getConfigName('h2_font_style'),
                            'default' => $this->getFontStyle('default'),
                            'options' => array(
                                'query' => $this->getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'aprow_font_option',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H3 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => $this->getConfigName('font_h3'),
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => $this->getConfigName('h3_font_family'),
                            'default' => apPageHelper::getFontFamily('default'),
                            'options' => array(
                                'query' => apPageHelper::getFontFamily(),
                                'id' => 'id',
                                'name' => 'name'),
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => $this->getConfigName('h3_font_size'),
                            'default' => '24',
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => $this->getConfigName('h3_height'),
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => $this->getConfigName('h3_font_weight'),
                            'default' => $this->getFontWeight('default'),
                            'options' => array(
                                'query' => $this->getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => $this->getConfigName('h3_font_style'),
                            'default' => $this->getFontStyle('default'),
                            'options' => array(
                                'query' => $this->getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'aprow_font_option',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H4 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => $this->getConfigName('font_h4'),
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => $this->getConfigName('h4_font_family'),
                            'default' => apPageHelper::getFontFamily('default'),
                            'options' => array(
                                'query' => apPageHelper::getFontFamily(),
                                'id' => 'id',
                                'name' => 'name'),
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => $this->getConfigName('h4_font_size'),
                            'default' => '18',
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => $this->getConfigName('h4_height'),
                            'default' => '28',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => $this->getConfigName('h4_font_weight'),
                            'default' => $this->getFontWeight('default'),
                            'options' => array(
                                'query' => $this->getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => $this->getConfigName('h4_font_style'),
                            'default' => $this->getFontStyle('default'),
                            'options' => array(
                                'query' => $this->getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'aprow_font_option',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H5 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => $this->getConfigName('font_h5'),
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => $this->getConfigName('h5_font_family'),
                            'default' => apPageHelper::getFontFamily('default'),
                            'options' => array(
                                'query' => apPageHelper::getFontFamily(),
                                'id' => 'id',
                                'name' => 'name'),
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => $this->getConfigName('h5_font_size'),
                            'default' => '14',
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => $this->getConfigName('h5_height'),
                            'default' => '20',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => $this->getConfigName('h5_font_weight'),
                            'default' => $this->getFontWeight('default'),
                            'options' => array(
                                'query' => $this->getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => $this->getConfigName('h5_font_style'),
                            'default' => $this->getFontStyle('default'),
                            'options' => array(
                                'query' => $this->getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'aprow_font_option',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('H6 Typography'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => $this->getConfigName('font_h6'),
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => $this->getConfigName('h6_font_family'),
                            'default' => apPageHelper::getFontFamily('default'),
                            'options' => array(
                                'query' => apPageHelper::getFontFamily(),
                                'id' => 'id',
                                'name' => 'name'),
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => $this->getConfigName('h6_font_size'),
                            'default' => '12',
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => $this->getConfigName('h6_height'),
                            'default' => '20',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => $this->getConfigName('h6_font_weight'),
                            'default' => $this->getFontWeight('default'),
                            'options' => array(
                                'query' => $this->getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => $this->getConfigName('h6_font_style'),
                            'default' => $this->getFontStyle('default'),
                            'options' => array(
                                'query' => $this->getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'aprow_font_option',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('P Tag'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => $this->getConfigName('font_p'),
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => $this->getConfigName('p_font_family'),
                            'default' => apPageHelper::getFontFamily('default'),
                            'options' => array(
                                'query' => apPageHelper::getFontFamily(),
                                'id' => 'id',
                                'name' => 'name'),
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => $this->getConfigName('p_font_size'),
                            'default' => '36',
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => $this->getConfigName('p_height'),
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => $this->getConfigName('p_font_weight'),
                            'default' => $this->getFontWeight('default'),
                            'options' => array(
                                'query' => $this->getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => $this->getConfigName('p_font_style'),
                            'default' => $this->getFontStyle('default'),
                            'options' => array(
                                'query' => $this->getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'aprow_font_option',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('A Tag'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => $this->getConfigName('font_a'),
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => $this->getConfigName('a_font_family'),
                            'default' => apPageHelper::getFontFamily('default'),
                            'options' => array(
                                'query' => apPageHelper::getFontFamily(),
                                'id' => 'id',
                                'name' => 'name'),
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => $this->getConfigName('a_font_size'),
                            'default' => '36',
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => $this->getConfigName('a_height'),
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => $this->getConfigName('a_font_weight'),
                            'default' => $this->getFontWeight('default'),
                            'options' => array(
                                'query' => $this->getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => $this->getConfigName('a_font_style'),
                            'default' => $this->getFontStyle('default'),
                            'options' => array(
                                'query' => $this->getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<hr />',
                    'form_group_class' => 'aprow_font_option',
                    'save' => false,
                ),
                array(
                    'type' => 'font_h',
                    'htitle' => $this->l('Span Tag'),
                    'desc'  => '',
                    'hdesc'  => $this->l('Specify the typography properties for headings.'),
                    'name' => $this->getConfigName('font_span'),
                    'items' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => $this->getConfigName('span_font_family'),
                            'default' => apPageHelper::getFontFamily('default'),
                            'options' => array(
                                'query' => apPageHelper::getFontFamily(),
                                'id' => 'id',
                                'name' => 'name'),
                            'class' => 'chk_font_exist',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Font Size'),
                            'name' => $this->getConfigName('span_font_size'),
                            'default' => '36',
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Line Height'),
                            'name' => $this->getConfigName('span_height'),
                            'default' => '40',
                            'desc' => $this->l('Number of pixel. You can input "auto" or "number", such as: 1170'),
                            'form_group_class' => 'aprow_general',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Weight'),
                            'name' => $this->getConfigName('span_font_weight'),
                            'default' => $this->getFontWeight('default'),
                            'options' => array(
                                'query' => $this->getFontWeight(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Style'),
                            'name' => $this->getConfigName('span_font_style'),
                            'default' => $this->getFontStyle('default'),
                            'options' => array(
                                'query' => $this->getFontStyle(),
                                'id' => 'id',
                                'name' => 'name'),
                        ),
                    ),
                    'default' => '',
                    'form_group_class' => 'aprow_font_option',
                ),
                array(
                    'type' => 'modules_block',
                    'label' => $this->l('Module List:'),
                    'name' => 'moduleList',
                    'values' => $moduleList,
                    'exist_module' => $sample->existThemeConfigFile(),
                    'default' => '',
                    'form_group_class' => 'aprow_data',
                    'save' => false,
                    'folder_data_struct' => str_replace('\\', '/', _PS_MODULE_DIR_.'appagebuilder/install'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        
        if (version_compare(Configuration::get('PS_VERSION_DB'), '1.7.3.0', '>=')) {
            $fields_form['input']['config']['values']['aprow_rtl'] = $this->l('Righ to Left');
            
            $fields_form['input'][] =
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<button class="button btn btn-danger" name="submit_rtl_prestashop" id="submit_rtl_prestashop" type="submit">1. Generate RTL stylesheet</button>',
                    'form_group_class' => 'aprow_rtl',
                );
            
            $fields_form['input'][] =
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<button class="button btn btn-success" name="submit_rtl_leo" id="submit_rtl_leo" type="submit">2. Use class RTL of theme</button>',
                    'form_group_class' => 'aprow_rtl',
                );
            
            $fields_form['input'][] =
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-info">'.$this->l('1. Generate RTL stylesheet :  create file *_rtl.css in current theme like Prestashop default.')
                                                                    .'</br>'  .$this->l('2. Use class RTL of theme and delete all *_rtl.css file in current theme.').'</div>',
                    'form_group_class' => 'aprow_rtl',
                );
        }
        
        $theme_customizations = LeoFrameworkHelper::getLayoutSettingByTheme($this->themeName);
        if (isset($theme_customizations['layout'])) {
            foreach ($theme_customizations['layout'] as $key => $value) {
                $o = array(
                    'label' => $this->module->getTranslator()->trans((isset($value['title']) ? $value['title'] : $key)),
                    'name' => $this->getConfigName(trim($key)),
                    'default' => $value['default'],
                    'type' => 'select',
                    'options' => array(
                        'query' => $value['option'],
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'desc' => isset($value['desc']) ? $this->module->getTranslator()->trans($value['desc']) : null,
                    'form_group_class' => 'aprow_general',
                );
                array_push($fields_form['input'], $o);
            }
        }
        
        $this->fields_form = $fields_form;
        $this->tpl_form_vars['backup_dir'] = $sample->backup_dir;
        
        if ($this->submitSaveSetting && Tools::isSubmit('submitAddconfiguration')) {
            # SAVING CONFIGURATION
            $this->saveThemeConfigs();
            $this->confirmations[] = 'Your configurations have been saved successfully.';
        }
        return parent::renderForm();
    }
    
    public function postProcess()
    {
        if (count($this->errors) > 0) {
            return;
        }
        $dataSample = new Datasample();
        if (Tools::isSubmit('submitBackup')) {
            $dataSample->processBackUp();
            $folder = str_replace('\\', '/', _PS_CACHE_DIR_.'backup/themes/');
            $this->confirmations[] = 'Back-up to PHP file is successful. <br/>' . $folder;
        } else if (Tools::isSubmit('submitRestore')) {
            $dataSample->restoreBackUpFile();
            $this->confirmations[] = 'Restore from PHP file is successful.';
        } else if (Tools::isSubmit('submitSample')) {
            $dataSample->processSample();
            $folder = str_replace('\\', '/', _PS_ALL_THEMES_DIR_.apPageHelper::getThemeName().'/samples/');
            $this->confirmations[] = 'Export Sample Data is successful. <br/>' . $folder;
        } else if (Tools::isSubmit('submitImport')) {
            $dataSample->processImport();
            $this->confirmations[] = 'Restore Sample Data is successful.';
        } else if (Tools::isSubmit('submitExportDBStruct')) {
            $dataSample->exportDBStruct();
            $dataSample->exportThemeSql();
            $folder = str_replace('\\', '/', _PS_MODULE_DIR_.'appagebuilder/install');
            $this->confirmations[] = 'Export Data Struct is successful. <br/>' . $folder;
        } else if (Tools::isSubmit('submitUpdateModule')) {
            apPageHelper::processCorrectModule();
            $this->confirmations[] = 'Update and Correct Module is successful.';
        } else if (Tools::isSubmit('submit_rtl_prestashop')) {
            $this->generateRTL();
            $this->confirmations[] = apPageHelper::getThemeName() . ' theme generate RTL stylesheet';
        } else if (Tools::isSubmit('submit_rtl_leo')) {
            $this->removeRTL();
            $this->confirmations[] = apPageHelper::getThemeName() . ' theme use class RTL of theme';
        } else if (Tools::isSubmit('submitAddconfiguration')) {
            $this->saveThemeConfigsBefore();
            $this->submitSaveSetting = true;
        }
    }
    
    public function generateRTL()
    {
        $theme_name = apPageHelper::getThemeName();
        Language::getRtlStylesheetProcessor()
                ->setProcessFOThemes(array($theme_name))
                ->setRegenerate(true)
                ->process();
    }
    
    public function removeRTL()
    {
        $directory = _PS_ALL_THEMES_DIR_._THEME_NAME_;
        $allFiles = Tools::scandir($directory, 'css', '', true);

        foreach ($allFiles as $key => $file) {
            if (Tools::substr(rtrim($file, '.css'), -4) !== '_rtl') {
                unset($allFiles[$key]);
            }
        }
        # REMOVE FILE _rtl
        foreach ($allFiles as $key => $file) {
            unlink($directory . DIRECTORY_SEPARATOR . $file);
        }
    }
    
    public function saveThemeConfigsBefore()
    {
        //$helper = LeoFrameworkHelper::getInstance();
        
        // SET COOKIE AGAIN
        $theme_cookie_name = $this->getConfigName('PANEL_CONFIG');
        $arrayConfig = array('default_skin', 'layout_mode', 'header_style', 'enable_fheader', 'sidebarmenu');
        # Remove value in cookie
        foreach ($arrayConfig as $value) {
            unset($_COOKIE[$theme_cookie_name.'_'.$value]);
            setcookie($theme_cookie_name.'_'.$value, '', 0, '/');
        }
        
        # WRITE LOAD GOOGLE FONT
        if (apPageHelper::getPostConfig('enable_loadfont') == 1) {
            $content_font = '';
            if ($gfont_items = Tools::getValue('gfont_items')) {
                # LOAD FONT
                foreach ($gfont_items as $gfont_items) {
                    $item = Tools::jsonDecode($gfont_items, true);
                    $gfont_name = str_replace(' ', '+', $item['gfont_name']);
                    unset($item['gfont_id']);
                    unset($item['gfont_name']);
                    $temp_font = $this->renderGoogleLinkFont($gfont_name, $item);

                    if (!empty($content_font) && !empty($temp_font)) {
                        $content_font .= '|'. $temp_font;
                    } else {
                        $content_font .= $temp_font;
                    }
                }

                # LOAD SUBSETS
                $gfonts_subsets = Tools::getValue('gfonts_subsets');
                if (!empty($content_font) && $gfonts_subsets) {
                    $content_font .= '&'.implode(',', $gfonts_subsets);
                }

                # LOAD ONE LINK
                if (!empty($content_font)) {
                    $content_font = '@import url("//fonts.googleapis.com/css?family='.$content_font.'");' ."\n\n";
                }
            }

            # WRITE ATTRIBUTE FONT
            $content_font .= $this->renderCSSFont('h1');
            $content_font .= $this->renderCSSFont('h2');
            $content_font .= $this->renderCSSFont('h3');
            $content_font .= $this->renderCSSFont('h4');
            $content_font .= $this->renderCSSFont('h5');
            $content_font .= $this->renderCSSFont('h6');
            $content_font .= $this->renderCSSFont('p');
            $content_font .= $this->renderCSSFont('a');
            $content_font .= $this->renderCSSFont('span');
            LeoFrameworkHelper::writeToCache($this->themePath.apPageHelper::getCssDir(), 'fonts-cuttom2', $content_font, 'css');
        }
        
        # SAVING GOOGLE FONT
        $gfont_items = Tools::getValue('gfont_items');
        if ($gfont_items) {
            $str_gfont_items = implode('__________', $gfont_items);
            Configuration::updateValue($this->getConfigName('google_font'), $str_gfont_items);
        } else {
            Configuration::updateValue($this->getConfigName('google_font'), '');
        }
        
        # SAVING SUBSET
        $gfonts_subsets = Tools::getValue('gfonts_subsets');
        if ($gfonts_subsets) {
            $gfonts_subsets = implode(',', $gfonts_subsets);
            Configuration::updateValue($this->getConfigName('google_subset'), $gfonts_subsets);
        } else {
            Configuration::updateValue($this->getConfigName('google_subset'), '');
        }
    }
    
    /**
     * alias from apPageHelper::getConfigName()
     */
    public function getConfigName($name)
    {
        return apPageHelper::getConfigName($name);
    }
    
    /**
     * Update Theme Configurations
     */
    public function saveThemeConfigs()
    {
        $languages = Language::getLanguages(false);
        $content_setting = '';
        //$content_font = '';
        //$helper = LeoFrameworkHelper::getInstance();
        
        foreach ($this->fields_form['input'] as $input) {
            if (isset($input['lang'])) {
                $data = array();
                foreach ($languages as $lang) {
                    $value = Tools::getValue(trim($input['name']).'_'.$lang['id_lang']);
                    $data[$lang['id_lang']] = $value ? $value : $input['default'];
                }

                if ($input['name'] == Tools::strtoupper($this->themeName).'_COPYRIGHT') {
                    Configuration::updateValue(trim($input['name']), $data, true);
                } else {
                    Configuration::updateValue(trim($input['name']), $data);
                }
            } else if ($input['type'] == 'font_h') {
                foreach ($input['items'] as $item) {
                    if (isset($item['name'])) {
//                        $value = Tools::getValue($item['name'], Configuration::get($item['name']));
//                        $item['default'] = isset($item['default']) ? $item['default'] : '';
//                        $dataSave = $value ? $value : $item['default'];
//                        Configuration::updateValue(trim($item['name']), $dataSave);
                        $value = Tools::getValue($item['name'], Configuration::get($item['name']));
                        Configuration::updateValue(trim($item['name']), $value);
                    }
                }
            } else {
                $value = Tools::getValue($input['name'], Configuration::get($input['name']));
                $input['default'] = isset($input['default']) ? $input['default'] : '';
                $dataSave = $value ? $value : $input['default'];
                
                if (isset($input['save']) && $input['save']== false) {
                    // NOT SAVE
                } else if ($input['type'] == 'font_h') {
                    // NOT SAVE
                } else {
                    Configuration::updateValue(trim($input['name']), $dataSave);
                }
                if ($input['name'] == $this->getConfigName('move_end_header') && $value == 1) {
                    apPageHelper::moveEndHeader();
                }
                if (trim($input['name']) == $this->getConfigName('listing_grid_mode')) {
                    if (trim($dataSave) == '') {
                        $dataSave = 'grid';
                    }
                    $content_setting .= '{assign var="LISTING_GRID_MODE" value="'.$dataSave.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == $this->getConfigName('listing_product_column')) {
                    if (trim($dataSave) == '') {
                        $dataSave = '3';
                    }
                    $content_setting .= '{assign var="LISTING_PRODUCT_COLUMN" value="'.$dataSave.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == $this->getConfigName('listing_product_largedevice')) {
                    if (trim($dataSave) == '') {
                        $dataSave = '3';
                    }
                    $content_setting .= '{assign var="LISTING_PRODUCT_LARGEDEVICE" value="'.$dataSave.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == $this->getConfigName('listing_product_tablet')) {
                    if (trim($dataSave) == '') {
                        $dataSave = '2';
                    }
                    $content_setting .= '{assign var="LISTING_PRODUCT_TABLET" value="'.$dataSave.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == $this->getConfigName('listing_product_smalldevice')) {
                    if (trim($dataSave) == '') {
                        $dataSave = '2';
                    }
                    $content_setting .= '{assign var="LISTING_PRODUCT_SMALLDEVICE" value="'.$dataSave.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == $this->getConfigName('listing_product_extrasmalldevice')) {
                    if (trim($dataSave) == '') {
                        $dataSave = '2';
                    }
                    $content_setting .= '{assign var="LISTING_PRODUCT_EXTRASMALLDEVICE" value="'.$dataSave.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == $this->getConfigName('listing_product_mobile')) {
                    if (trim($dataSave) == '') {
                        $dataSave = '1';
                    }
                    $content_setting .= '{assign var="LISTING_PRODUCT_MOBILE" value="'.$dataSave.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == $this->getConfigName('listing_product_column_module')) {
                    if (trim($dataSave) == '') {
                        $dataSave = '4';
                    }
                    $content_setting .= '{assign var="LISTING_PRODUCT_COLUMN_MODULE" value="'.$dataSave.'" scope="global"}'."\n";
                } elseif (trim($input['name']) == $this->getConfigName('enable_responsive')) {
                    # validate module
                    $content_setting .= '{assign var="ENABLE_RESPONSIVE" value="'.$dataSave.'" scope="global"}'."\n";
                }
            }
        }
        
        $folder = $this->themePath.'templates/layouts/';
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        LeoFrameworkHelper::writeToCache($this->themePath.'templates/layouts/', 'setting', apPageHelper::getLicenceTPL().$content_setting, 'tpl');
    }
    
    public function renderGoogleLinkFont($gfont_name, $attribute)
    {
        $output = '';
        if (is_array($attribute) && $attribute) {
            $str_att = '';
            foreach ($attribute as $value) {
                $str_att .= ','.$value;
            }
            $str_att = trim($str_att, ',');
            
            $output = $gfont_name . ':' . $str_att;
        } else {
            $output = $gfont_name;
        }
        
        return $output;
    }
    
    public function renderCSSFont($tag)
    {
        $html = '';
        if (apPageHelper::getPostConfig($tag . '_font_family')) {
            $html .= ' font-family:' . apPageHelper::getPostConfig($tag . '_font_family') . ';';
        }
        if ((int)apPageHelper::getPostConfig($tag . '_font_size')) {
            $html .= ' font-size:' . (int)apPageHelper::getPostConfig($tag . '_font_size') . 'px;';
        }
        if ((int)apPageHelper::getPostConfig($tag . '_height')) {
            $html .= ' height:' . (int)apPageHelper::getPostConfig($tag . '_height') . 'px;';
        }
        if ((int)apPageHelper::getPostConfig($tag . '_font_weight')) {
            $html .= ' font-weight:' . (int)apPageHelper::getPostConfig($tag . '_font_weight') . ';';
        }
        if (apPageHelper::getPostConfig($tag . '_font_style')) {
            $html .= ' font-style:' . apPageHelper::getPostConfig($tag . '_font_style') . ';';
        }
        
        $output = '';
        if (!empty($html)) {
            $output = $tag . ' {'.$html.' }'."\n";
        }
        
        return $output;
    }
    
    public function getFieldsValue($obj)
    {
        unset($obj);
        $languages = Language::getLanguages(false);
        $fields_values = array();
        
        foreach ($this->fields_form as $f) {
            foreach ($f['form']['input'] as $input) {
                if (isset($input['lang'])) {
                    foreach ($languages as $lang) {
                        $v = Tools::getValue($input['name'], Configuration::get($input['name'], $lang['id_lang']));
                        $input['default'] = isset($input['default']) ? $input['default'] : '';
                        $fields_values[$input['name']][$lang['id_lang']] = $v ? $v : $input['default'];
                    }
                } else if ($input['type'] == 'font_h') {
                    if ($input['type'] == 'font_h') {
                        foreach ($input['items'] as $item) {
                            if (isset($item['name'])) {
//                                $item_value = Tools::getValue($item['name'], Configuration::get($item['name']));
//                                $fields_values[ $input['name'] ][ $item['name'] ]  = $item_value ? $item_value : $item['default'];
                                $item_value = Tools::getValue($item['name'], Configuration::get($item['name']));
                                $fields_values[ $input['name'] ][ $item['name'] ]  = $item_value;
                            }
                        }
                    }
                } else {
                    $v = Tools::getValue($input['name'], Configuration::get($input['name']));
                    $input['default'] = isset($input['default']) ? $input['default'] : '';
                    $fields_values[$input['name']] = $v ? $v : $input['default'];
                }
            }
        }
        // Font setup : list fonts in google
        $fields_values['gfont_api'] = Tools::jsonEncode(GoogleFont::getAllGoogleFonts());
        
        // Font setup : list fonts in database
        $google_font_cfg = Configuration::get($this->getConfigName('google_font'));
        $fields_values['gfont_list_ori'] = '[]';
        if ($google_font_cfg) {
            $google_fonts = explode('__________', $google_font_cfg);
            foreach ($google_fonts as &$font) {
                $font = Tools::jsonDecode($font, true);
            }
            $fields_values['gfont_list'] = $google_fonts;
            $fields_values['gfont_list_ori'] = Tools::jsonEncode($google_fonts);
        }
        
        // Font setup : list subset in database
        $google_subset_cfg = Configuration::get($this->getConfigName('google_subset'));
        $fields_values['gfont_subset'] = '[]';
        if ($google_subset_cfg) {
            $google_subset = explode(',', $google_subset_cfg);
            $fields_values['gfont_subset'] = Tools::jsonEncode($google_subset);
        }
        
        return $fields_values;
    }
    
    public function getGoogleFont()
    {
        return array_keys(GoogleFont::getAllGoogleFonts());
    }
    
    public function getFontWeight($default = false)
    {
        if ($default == 'default') {
            return '';
        }
        $result = array(
            array( 'id' => '', 'name' => '----- Select -----'),
            array( 'id' => '400', 'name' => '400 (Normal)'),
            array( 'id' => '700', 'name' => '700 (Bold)'),
            array( 'id' => '100', 'name' => '100'),
            array( 'id' => '200', 'name' => '200'),
            array( 'id' => '300', 'name' => '300'),
            array( 'id' => '500', 'name' => '500'),
            array( 'id' => '600', 'name' => '600'),
            array( 'id' => '800', 'name' => '800'),
            array( 'id' => '900', 'name' => '900'),
        );
        return $result;
    }
    
    public function getFontStyle($default = false)
    {
        if ($default == 'default') {
            return '';
        }
        $result = array(
            array( 'id' => '', 'name' => '----- Select -----'),
            array( 'id' => 'normal', 'name' => 'Normal'),
            array( 'id' => 'italic', 'name' => 'Italic'),
        );
        return $result;
    }
}
