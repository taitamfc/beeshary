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

class ApBlog extends ApShortCodeBase
{
    public $name = 'ApBlog';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Blog'), 'position' => 3, 'desc' => $this->l('You can display blogs'),
            'icon_class' => 'icon icon icon-chevron-right', 'tag' => 'content slider');
    }

    public function getConfigList()
    {
        if (Module::isInstalled('leoblog') && Module::isEnabled('leoblog')) {
            include_once(_PS_MODULE_DIR_.'leoblog/leoblog.php');
            $module = new Leoblog();
//            $list = array();
            $controller = 'AdminLeoblogDashboard';
            $id_lang = Context::getContext()->language->id;
            $params = array('token' => Tools::getAdminTokenLite($controller));
            $url = dirname($_SERVER['PHP_SELF']).'/'.Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
            $selected_categories = array();
            if (Tools::getIsset('chk_cat')) {
                $chk_cat = Tools::getValue('chk_cat');
                $arr_key = explode(',', $chk_cat);
                
                $randkey_cat = '';
                foreach ($arr_key as $val) {
                    $randkey_cat .= ($randkey_cat == '') ? "'".pSQL($val)."'" : ",'".pSQL($val)."'";
                }
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'leoblogcat`';
                $sql .= ' WHERE randkey IN ('.$randkey_cat.')';
            
                $arr_cat = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                foreach ($arr_cat as $cat) {
                    $selected_categories[] = $cat['id_leoblogcat'];
                }
            }
            
            $data_category = '';
            $data_category = $module->getTreeForApPageBuilder($selected_categories);
            $inputs = array(
                array(
                    'type' => 'text',
                    'name' => 'title',
                    'label' => $this->l('Title'),
                    'desc' => $this->l('Auto hide if leave it blank'),
                    'lang' => 'true',
                    'default' => ''
                ),
                array(
                    'type' => 'textarea',
                    'name' => 'sub_title',
                    'label' => $this->l('Sub Title'),
                    'lang' => true,
                    'values' => '',
                    'autoload_rte' => false,
                    'default' => '',
                ),
                array(
                    'type' => 'text',
                    'name' => 'class',
                    'label' => $this->l('CSS Class'),
                    'default' => ''
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class=""><a class="" href="'.$url.'" target="_blank">'.
                    $this->l('Go to page configuration Blog').'</a></div>'
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Step 1: Blog Filter').'</div>',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => "<div class='latest-blog-category'>$data_category</div>",
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="sperator"></div>',
                    'form_group_class' => 'value_by_tags',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Step 2: Blog Order And Limit').'</div>',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image Blog Width'),
                    'name' => 'bleoblogs_width',
                    'class' => 'fixed-width-xs',
                    'desc' => $this->l('Define the width of images displayed in this block.'),
                    'default' => 690,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image Blog Height'),
                    'name' => 'bleoblogs_height',
                    'class' => 'fixed-width-xs',
                    'desc' => $this->l('Define the height of images displayed in this block.'),
                    'default' => 300,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show View All'),
                    'name' => 'bleoblogs_show',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'default' => 1,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show title'),
                    'name' => 'show_title',
                    'values' => ApPageSetting::returnYesNo(),
                    'default' => 1,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show description'),
                    'name' => 'show_desc',
                    'values' => ApPageSetting::returnYesNo(),
                    'default' => 1,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Image:'),
                    'name' => 'bleoblogs_sima',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'default' => 1,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Author:'),
                    'name' => 'bleoblogs_saut',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'default' => 1,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Category:'),
                    'name' => 'bleoblogs_scat',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'default' => 1,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Created Date:'),
                    'name' => 'bleoblogs_scre',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'default' => 1,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Counter:'),
                    'name' => 'bleoblogs_scoun',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'default' => 1,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Hits:'),
                    'name' => 'bleoblogs_shits',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'default' => 1,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Order Way'),
                    'class' => 'form-action',
                    'name' => 'order_way',
                    'options' => array(
                        'query' => array(
                            array('id' => 'asc', 'name' => $this->l('Asc')),
                            array('id' => 'desc', 'name' => $this->l('Desc')),
                            array('id' => 'random', 'name' => $this->l('Random'))),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'default' => 'desc'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Order By'),
                    'name' => 'order_by',
                    'options' => array(
                        'query' => ApPageSetting::getOrderByBlog(),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'form_group_class' => 'order_type_sub order_type-asc order_type-desc',
                    'default' => 'id_leoblog_blog'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Limit'),
                    'name' => 'nb_blogs',
                    'default' => '10',
                    'desc' => $this->l('Define the number of blogs displayed in this block.'),
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Step 3: Carousel Setting').'</div>',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Carousel Type'),
                    'class' => 'form-action',
                    'name' => 'carousel_type',
                    'options' => array(
                        'query' => array(
                            array('id' => 'boostrap', 'name' => $this->l('Bootstrap')),
                            array('id' => 'owlcarousel', 'name' => $this->l('Owl Carousel')),
                            array('id' => 'slickcarousel', 'name' => $this->l('Slick Carousel')),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'default' => 'boostrap'
                ),
            //Owl Carousel begin
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Items per Row').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'items',
                'label' => $this->l('Items'),
                'desc' => $this->l('Typing number of items. Default'),
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
                'default' => '5',
            ),
            array(
                'type' => 'text',
                'name' => 'itemsdesktop',
                'label' => $this->l('Items_Desktop'),
                'desc' => $this->l('Typing number of items ( with Screen < 1200 )'),
                'default' => '4',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemsdesktopsmall',
                'label' => $this->l('Items_Desktop_Small'),
                'desc' => $this->l('Typing number of items ( with Screen < 992 )'),
                'default' => '3',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemstablet',
                'label' => $this->l('Items_Tablet'),
                'desc' => $this->l('Typing number of items ( with Screen < 768 )'),
                'default' => '2',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemsmobile',
                'label' => $this->l('Items_Mobile'),
                'desc' => $this->l('Typing number of items ( with Screen < 576 )'),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itemscustom',
                'label' => $this->l('Items_Custom'),
                'desc' => $this->l('(Advance User) Example: [[0, 2], [576, 3], [768, 4], [992, 5], [1200, 6]]. The format is [x,y] whereby x=browser width and y=number of slides displayed'),
                'default' => '',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'itempercolumn',
                'label' => $this->l('Items per Column'),
                'desc' => $this->l('Number of item per one column. Same with number of line for one page'),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Effect').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Autoplay'),
                'name' => 'autoplay',
                'is_bool' => true,
                'desc' => $this->l('Yes - scroll per page. No - scroll per item. This affect next/prev buttons and mouse/touch dragging.'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Stop on Hover'),
                'name' => 'stoponhover',
                'is_bool' => true,
                'desc' => $this->l('Stop autoplay on mouse hover'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Responsive'),
                'name' => 'responsive',
                'is_bool' => true,
                'desc' => $this->l('You can use Owl Carousel on desktop-only websites too! Just change that to "false" to disable resposive capabilities'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Navigation'),
                'name' => 'navigation',
                'is_bool' => true,
                'desc' => $this->l('Display "next" and "prev" buttons.'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Auto Height'),
                'name' => 'autoHeight',
                'is_bool' => true,
                'desc' => $this->l('Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Mouse Drag'),
                'name' => 'mouseDrag',
                'is_bool' => true,
                'desc' => $this->l('On DeskTop - Turn off/on mouse events.'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Touch Drag'),
                'name' => 'touchdrag',
                'is_bool' => true,
                'desc' => $this->l('On Mobile - Turn off/on touch events.'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('Lazy Load: This function is only work when have one item per column').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Lazy Load'),
                'name' => 'lazyload',
                'values' => ApPageSetting::returnYesNo(),
                'desc' => $this->l('Delays loading of images. Images outside of viewport will not be loaded before user scrolls to them. Great for mobile devices to speed up page loadings'),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Lazy Follow'),
                'name' => 'lazyfollow',
                'is_bool' => true,
                'desc' => $this->l('When pagination used, it skips loading the images from pages that got skipped. It only loads the images that get displayed in viewport. If set to false, all images get loaded when pagination used. It is a sub setting of the lazy load function.'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Lazy Effect'),
                'name' => 'lazyeffect',
                'options' => array(
                    'query' => array(
                        array('id' => 'fade', 'name' => $this->l('fade')),
                        array('id' => 'false', 'name' => $this->l('No')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'desc' => $this->l('Default is fadeIn on 400ms speed. Use false to remove that effect.'),
                'default' => 'fade',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Pagination Enable'),
                'name' => 'pagination',
                'is_bool' => true,
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0',
                'desc' => $this->l('Show Pagination below owl-carousel.'),
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Pagination Numbers'),
                'name' => 'paginationnumbers',
                'is_bool' => true,
                'desc' => $this->l('Show numbers inside Pagination'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="font-size:13px">'.$this->l('NEXT PAGE').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Scroll per Page'),
                'name' => 'scrollPerPage',
                'is_bool' => true,
                'desc' => $this->l('Yes - scroll per Page. No - scroll per Item. This affect next/prev buttons and mouse/touch dragging.'),
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Scroll Page Speed'),
                'name' => 'paginationspeed',
                'desc' => $this->l('Time to next page. Ex 800 ( Milliseconds )'),
                'default' => '800',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Scroll Item Speed'),
                'name' => 'slidespeed',
                'desc' => $this->l('Time to next item. Ex 200 (Milliseconds)'),
                'default' => '200',
                'form_group_class' => 'carousel_type_sub carousel_type-owlcarousel',
            ),
            //Owl Carousel end
                //boostrap carousel begin
                array(
                    'type' => 'text',
                    'name' => 'nbitemsperpage',
                    'label' => $this->l('Number of Items per Page'),
                    'desc' => $this->l('How many product you want to display in a Page. Divisible by Item per Line (Desktop, Table, mobile)(default:12)'),
                    'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
                    'default' => '12',
                ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space">'.$this->l('Items per Row').'</div>',
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_Desktop ( >= 1200 )'),
                'name' => 'nbitemsperline_desktop',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                        array('id' => '12', 'name' => $this->l('12 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 4'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_SmallDesktop ( >= 992 )'),
                'name' => 'nbitemsperline_smalldesktop',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                        array('id' => '12', 'name' => $this->l('12 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 3'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_Tablet ( >= 768 )'),
                'name' => 'nbitemsperline_tablet',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                        array('id' => '12', 'name' => $this->l('12 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 3'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_SmallDevices ( >= 576 )'),
                'name' => 'nbitemsperline_smalldevices',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 2'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_ExtraSmallDevices ( >= 480 )'),
                'name' => 'nbitemsperline_extrasmalldevices',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 1'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Items_Smartphone ( < 480 )'),
                'name' => 'nbitemsperline_smartphone',
                'default' => '',
                'options' => array('query' => array(
                        array('id' => '', 'name' => $this->l('Default')),
                        array('id' => '1', 'name' => $this->l('1 item')),
                        array('id' => '2', 'name' => $this->l('2 items')),
                        array('id' => '3', 'name' => $this->l('3 items')),
                        array('id' => '4', 'name' => $this->l('4 items')),
                        array('id' => '5', 'name' => $this->l('5 items')),
                        array('id' => '6', 'name' => $this->l('6 items')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
                'desc' => $this->l('How many product you want to display in a row of page. Default 1'),
                'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
            ),
                array(
                    'type' => 'text',
                    'name' => 'interval',
                    'label' => $this->l('interval'),
                    'desc' => $this->l('The amount of time to delay between automatically cycling an item. If false, carousel will not automatically cycle.'),
                    'default' => '5000',
                    'form_group_class' => 'carousel_type_sub carousel_type-boostrap carousel_type-desc',
                ),
                    //boostrap carousel end
                    //Slick carousel start
            array(
                'type' => 'select',
                'label' => $this->l('Vertical'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'name' => 'slick_vertical',
                'options' => array(
                    'query' => array(
                        array('id' => '0', 'name' => $this->l('No')),
                        array('id' => '1', 'name' => $this->l('Yes')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => '0'
            ),
            array(
                'type' => 'select',
                'name' => 'slick_autoplay',
                'label' => $this->l('Auto play'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'desc' => $this->l(''),
                'default' => '1',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('Yes')),
                        array('id' => '0', 'name' => $this->l('No')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
            ),
            array(
                'type' => 'select',
                'name' => 'slick_pauseonhover',
                'label' => $this->l('Pause on Hover'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'desc' => $this->l(''),
                'default' => '1',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('Yes')),
                        array('id' => '0', 'name' => $this->l('No')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
            ),
            array(
                'type' => 'select',
                'name' => 'slick_loopinfinite',
                'label' => $this->l('Loop Infinite'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'desc' => $this->l(''),
                'default' => '0',
                'options' => array('query' => array(
                        array('id' => '0', 'name' => $this->l('No')),
                        array('id' => '1', 'name' => $this->l('Yes')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
            ),
            array(
                'type' => 'select',
                'name' => 'slick_arrows',
                'label' => $this->l('Prev/Next Arrows'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'desc' => $this->l(''),
                'default' => '1',
                'options' => array('query' => array(
                        array('id' => '1', 'name' => $this->l('Yes')),
                        array('id' => '0', 'name' => $this->l('No')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
            ),
            array(
                'type' => 'select',
                'name' => 'slick_dot',
                'label' => $this->l('Show dot indicators'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'desc' => $this->l(''),
                'default' => '0',
                'options' => array('query' => array(
                        array('id' => '0', 'name' => $this->l('No')),
                        array('id' => '1', 'name' => $this->l('Yes')),
                    ),
                    'id' => 'id',
                    'name' => 'name'),
            ),
            array(
                'type' => 'select',
                'name' => 'slick_centermode',
                // 'class' => 'form-action',
                'label' => $this->l('Center mode'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'desc' => $this->l(''),
                'default' => '0',
                'options' => array('query' => array(
                        array('id' => '0', 'name' => $this->l('No')),
                        array('id' => '1', 'name' => $this->l('Yes')),
                    ),
                    'id' => 'id',
                    'name' => 'name')
            ),
            array(
                'type' => 'text',
                'name' => 'slick_centerpadding',
                'label' => $this->l('Center padding'),
                'desc' => $this->l('Only for center mode. Unit is px (pixel). Default: 60(px)'),
                'default' => '60',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'slick_row',
                'label' => $this->l('Num Row'),
                'desc' => $this->l('Show number row display. Ex 1 or 1,2,3,4 '),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'slick_slidestoshow',
                'label' => $this->l('Slides To Show'),
                'desc' => $this->l('Show number row display. Ex 1 or 1,2,3,4 '),
                'default' => '5',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'slick_slidestoscroll',
                'label' => $this->l('Slides To Scroll'),
                'desc' => $this->l('Show number row display. Ex 1 or 1,2,3,4 '),
                'default' => '1',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),
            array(
                'type' => 'text',
                'name' => 'slick_items_custom',
                'label' => $this->l('Display for other screen'),
                'desc' => $this->l('(Advance User) Example: [[1200, 6],[992, 5],[768, 4], [576, 3],[480, 2]]. The format is [x,y] whereby x=browser width and y=number of slides displayed'),
                'default' => '[[1200, 6],[992, 5],[768, 4], [576, 3],[480, 2]]',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),
           
            array(
                'type' => 'select',
                'name' => 'slick_custom_status',
                // 'class' => 'form-action',
                'label' => $this->l('Enable custom js slick'),
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'desc' => $this->l('Only for developers and advanced users. Goto http://kenwheeler.github.io/slick/ for option'),
                'options' => array('query' => array(
                        array('id' => '0', 'name' => $this->l('No')),
                        array('id' => '1', 'name' => $this->l('Yes')),
                    ),
                    'id' => 'id',
                    'name' => 'name',
                ),
                'default' => '0',
            ),
            array(
                'type' => 'textarea',
                'name' => 'slick_custom',
                'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
                'label' => $this->l('Custom js Slick'),
                'values' => '',
                'autoload_rte' => false,
                'default' => '{
  dots: true,
  infinite: false,
  speed: 300,
  slidesToShow: 4,
  slidesToScroll: 4,
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3,
        infinite: true,
        dots: true
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
  ]
}',
                // 'form_group_class' => 'carousel_type_sub carousel_type-slickcarousel',
            ),

            //Slick carousel end
            );
        } else {
            $inputs = array(
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-warning">'.
                    $this->l('"LeoBlog" Module must be installed and enabled before using.').
                    '</div><br/><h4><center>You can take this module at leo-theme or apollo-theme</center></h4>'
                )
            );
        }
        return $inputs;
    }
    
    public function endRenderForm()
    {
        // KEEP OLD DATA
        if (Tools::getIsset('nbitemsperline') && Tools::getValue('nbitemsperline')) {
            $this->helper->tpl_vars['fields_value']['nbitemsperline_desktop'] = Tools::getValue('nbitemsperline');
            $this->helper->tpl_vars['fields_value']['nbitemsperline_smalldesktop'] = Tools::getValue('nbitemsperline');
            $this->helper->tpl_vars['fields_value']['nbitemsperline_tablet'] = Tools::getValue('nbitemsperline');
        }
        
        if (Tools::getIsset('nbitemsperlinetablet') && Tools::getValue('nbitemsperlinetablet')) {
            $this->helper->tpl_vars['fields_value']['nbitemsperline_smalldevices'] = Tools::getValue('nbitemsperlinetablet');
        }
        
        if (Tools::getIsset('nbitemsperlinemobile') && Tools::getValue('nbitemsperlinemobile')) {
            $this->helper->tpl_vars['fields_value']['nbitemsperline_extrasmalldevices'] = Tools::getValue('nbitemsperlinemobile');
            $this->helper->tpl_vars['fields_value']['nbitemsperline_smartphone'] = Tools::getValue('nbitemsperlinemobile');
        }
    }

    public function prepareFontContent($assign, $module = null)
    {
        if (Module::isInstalled('leoblog') && Module::isEnabled('leoblog')) {
            $id_shop = (int)Context::getContext()->shop->id;
            if (isset($assign['formAtts']['carousel_type']) && $assign['formAtts']['carousel_type'] == 'owlcarousel') {
                if (!Configuration::get('APPAGEBUILDER_LOAD_OWL')) {
                    $assign['formAtts']['lib_has_error'] = true;
                    $assign['formAtts']['lib_error'] = 'Can not show Blog. Please enable Owl Carousel library in Appagebuilder Configuration.';
                    return $assign;
                }
            }
            $assign['formAtts']['isEnabled'] = true;
            include_once(_PS_MODULE_DIR_.'leoblog/leoblog.php');
            $assign['formAtts']['leo_blog_helper'] = LeoBlogHelper::getInstance();
            $module = new Leoblog();
            //get id of blog via link-url
//            if (isset($assign['formAtts']['chk_cat'])) {
//                $link_array = explode(',', $assign['formAtts']['chk_cat']);
//            }
//            else {
//                $link_array = array();
//            }
//            if ($link_array && !is_numeric($link_array['0'])) {
//                $where = '';
//                foreach ($link_array as $val) {
//                    // validate module
//                    $where .= ($where == '') ? "'".$val."'" : ",'".$val."'";
//                }
//
//                $where = ' WHERE link_rewrite IN (".$where.") AND id_lang = '.(int)Context::getContext()->language->id;
//                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_leoblogcat FROM `'._DB_PREFIX_.'leoblogcat_lang` '.$where);
//                $where = '';
//                foreach ($result as $blog) {
//                    // validate module
//                    $where .= ($where == '') ? $blog['id_leoblogcat'] : ','.$blog['id_leoblogcat'];
//                }
//                $assign['formAtts']['chk_cat'] = $where;
//            }
            
            // GET ID_BLOG VIA randkey - BEGIN
            $arr_key = array(-1);  // DEFAULT
            if (isset($assign['formAtts']['chk_cat'])) {
                $arr_key = explode(',', $assign['formAtts']['chk_cat']);
            }
            $randkey_cat = '';
            foreach ($arr_key as $val) {
                $randkey_cat .= ($randkey_cat == '') ? "'".pSQL($val)."'" : ",'".pSQL($val)."'";
            }
            
            $where = ' WHERE randkey IN ('.$randkey_cat.')';
            $sql = 'SELECT cat.id_leoblogcat FROM `'._DB_PREFIX_.'leoblogcat` cat';
            $sql .= ' JOIN `'._DB_PREFIX_.'leoblogcat_shop` cat_s ON cat.id_leoblogcat = cat_s.id_leoblogcat  AND id_shop = ' . (int)$id_shop;
            $sql .= $where;
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $where = '-1';
            foreach ($result as $blog) {
                // validate module
                $where .= ($where == '') ? $blog['id_leoblogcat'] : ','.$blog['id_leoblogcat'];
            }
            $assign['formAtts']['chk_cat'] = $where;
            // GET ID_BLOG VIA randkey - END
            
            
            $config = LeoBlogConfig::getInstance();
            $config->setVar('listing_leading_img_width', $assign['formAtts']['bleoblogs_width']);
            $config->setVar('listing_leading_img_height', $assign['formAtts']['bleoblogs_height']);
            $assign['products'] = $module->getBlogsFont($assign['formAtts'], $module);
            $assign['carouselName'] = 'carousel-'.ApPageSetting::getRandomNumber();
            if ($assign['formAtts']['carousel_type'] == 'boostrap') {
                if (isset($assign['formAtts']['nbitemsperline']) && $assign['formAtts']['nbitemsperline']) {
                    $assign['formAtts']['nbitemsperline_desktop'] = $assign['formAtts']['nbitemsperline'];
                    $assign['formAtts']['nbitemsperline_smalldesktop'] = $assign['formAtts']['nbitemsperline'];
                    $assign['formAtts']['nbitemsperline_tablet'] = $assign['formAtts']['nbitemsperline'];
                }
                if (isset($assign['formAtts']['nbitemsperlinetablet']) && $assign['formAtts']['nbitemsperlinetablet']) {
                    $assign['formAtts']['nbitemsperline_smalldevices'] = $assign['formAtts']['nbitemsperlinetablet'];
                }
                if (isset($assign['formAtts']['nbitemsperlinemobile']) && $assign['formAtts']['nbitemsperlinemobile']) {
                    $assign['formAtts']['nbitemsperline_extrasmalldevices'] = $assign['formAtts']['nbitemsperlinemobile'];
                    $assign['formAtts']['nbitemsperline_smartphone'] = $assign['formAtts']['nbitemsperlinemobile'];
                }

                $assign['formAtts']['nbitemsperline_desktop'] = isset($assign['formAtts']['nbitemsperline_desktop']) && $assign['formAtts']['nbitemsperline_desktop']  ? (int)$assign['formAtts']['nbitemsperline_desktop'] : 4;
                $assign['formAtts']['nbitemsperline_smalldesktop'] = isset($assign['formAtts']['nbitemsperline_smalldesktop']) && $assign['formAtts']['nbitemsperline_smalldesktop'] ? (int)$assign['formAtts']['nbitemsperline_smalldesktop'] : 4;
                $assign['formAtts']['nbitemsperline_tablet'] = isset($assign['formAtts']['nbitemsperline_tablet']) && $assign['formAtts']['nbitemsperline_tablet'] ? (int)$assign['formAtts']['nbitemsperline_tablet'] : 3;
                $assign['formAtts']['nbitemsperline_smalldevices'] = isset($assign['formAtts']['nbitemsperline_smalldevices']) && $assign['formAtts']['nbitemsperline_smalldevices'] ? (int)$assign['formAtts']['nbitemsperline_smalldevices'] : 2;
                $assign['formAtts']['nbitemsperline_extrasmalldevices'] = isset($assign['formAtts']['nbitemsperline_extrasmalldevices']) && $assign['formAtts']['nbitemsperline_extrasmalldevices'] ? (int)$assign['formAtts']['nbitemsperline_extrasmalldevices'] : 1;
                $assign['formAtts']['nbitemsperline_smartphone'] = isset($assign['formAtts']['nbitemsperline_smartphone']) && $assign['formAtts']['nbitemsperline_smartphone'] ? (int)$assign['formAtts']['nbitemsperline_smartphone'] : 1;

                $assign['tabname'] = 'carousel-'.ApPageSetting::getRandomNumber();
                $assign['itemsperpage'] = (int)$assign['formAtts']['nbitemsperpage'];
                $assign['nbItemsPerLine'] = (int)$assign['formAtts']['nbitemsperline_desktop'];

                $assign['scolumn'] = '';

                if ($assign['formAtts']['nbitemsperline_desktop'] == '5') {
                    $assign['scolumn'] .= ' col-xl-2-4';
                } else {
                    $assign['scolumn'] .= ' col-xl-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_desktop']));
                }

                if ($assign['formAtts']['nbitemsperline_smalldesktop'] == '5') {
                    $assign['scolumn'] .= ' col-lg-2-4';
                } else {
                    $assign['scolumn'] .= ' col-lg-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_smalldesktop']));
                }

                if ($assign['formAtts']['nbitemsperline_tablet'] == '5') {
                    $assign['scolumn'] .= ' col-md-2-4';
                } else {
                    $assign['scolumn'] .= ' col-md-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_tablet']));
                }

                if ($assign['formAtts']['nbitemsperline_smalldevices'] == '5') {
                    $assign['scolumn'] .= ' col-sm-2-4';
                } else {
                    $assign['scolumn'] .= ' col-sm-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_smalldevices']));
                }

                if ($assign['formAtts']['nbitemsperline_extrasmalldevices'] == '5') {
                    $assign['scolumn'] .= ' col-xs-2-4';
                } else {
                    $assign['scolumn'] .= ' col-xs-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_extrasmalldevices']));
                }

                if ($assign['formAtts']['nbitemsperline_smartphone'] == '5') {
                    $assign['scolumn'] .= ' col-sp-2-4';
                } else {
                    $assign['scolumn'] .= ' col-sp-' .str_replace('.', '-', ''.(int)(12 / $assign['formAtts']['nbitemsperline_smartphone']));
                }
            }
                    
            //DONGND:: create data for owl carousel with item custom
            if ($assign['formAtts']['carousel_type'] == 'owlcarousel') {
                //DONGND:: build data for fake item loading
                $assign['formAtts']['number_fake_item'] = $assign['formAtts']['items'];
                $array_fake_item = array();
                $array_fake_item['m'] = $assign['formAtts']['itemsmobile'];
                $array_fake_item['sm'] = $assign['formAtts']['itemstablet'];
                $array_fake_item['md'] = $assign['formAtts']['itemsdesktopsmall'];
                $array_fake_item['lg'] = $assign['formAtts']['itemsdesktop'];
                $array_fake_item['xl'] = $assign['formAtts']['items'];
                $assign['formAtts']['array_fake_item'] = $array_fake_item;
                if (isset($assign['formAtts']['itemscustom']) && $assign['formAtts']['itemscustom'] != '') {
                    $array_item_custom = Tools::jsonDecode($assign['formAtts']['itemscustom']);
                    $array_item_custom_tmp = array();
                    $array_number_item = array();
                    foreach ($array_item_custom as $array_item_custom_val) {
                        $size_window = $array_item_custom_val[0];
                        $number_item = $array_item_custom_val[1];
                        if (0 <= $size_window && $size_window < 576) {
                            $array_item_custom_tmp['m'] = $number_item;
                        } else if (576 <= $size_window && $size_window < 768) {
                            $array_item_custom_tmp['sm'] = $number_item;
                        } else if (768 <= $size_window && $size_window < 992) {
                            $array_item_custom_tmp['md'] = $number_item;
                        } else if (992 <= $size_window && $size_window < 1200) {
                            $array_item_custom_tmp['lg'] = $number_item;
                        } else if ($size_window >= 1200) {
                            $array_item_custom_tmp['xl'] = $number_item;
                        }
                        $array_item_custom_tmp[$size_window] = $number_item;
                        $array_number_item[] = $number_item;
                    };
                    $assign['formAtts']['array_fake_item'] = array_merge($array_fake_item, $array_item_custom_tmp);
                    
                    if (max($array_number_item) > $assign['formAtts']['items']) {
                        $assign['formAtts']['number_fake_item'] = max($array_number_item);
                    }
                }
            };
            
            if ($assign['formAtts']['carousel_type'] == 'slickcarousel') {
                if (isset($assign['formAtts']['slick_items_custom'])) {
                    $assign['formAtts']['slick_items_custom'] = str_replace($this->str_search, $this->str_relace, $assign['formAtts']['slick_items_custom']);
                }
                if (isset($assign['formAtts']['slick_custom'])) {
                    $str_relace = array('&', '\"', '\'', '', '', '', '[', ']');
                    $assign['formAtts']['slick_custom'] = str_replace($this->str_search, $str_relace, $assign['formAtts']['slick_custom']);
                }
                if (isset($assign['formAtts']['slick_items_custom'])) {
                    $assign['formAtts']['slick_items_custom'] = Tools::jsonDecode($assign['formAtts']['slick_items_custom']);
                }
            
                //DONGND:: build data for fake item loading
                $assign['formAtts']['number_fake_item'] = $assign['formAtts']['slick_slidestoshow']*$assign['formAtts']['slick_row'];
                
                if (isset($assign['formAtts']['slick_items_custom']) && $assign['formAtts']['slick_items_custom'] != '') {
                    $array_item_custom = $assign['formAtts']['slick_items_custom'];
                    $array_item_custom_tmp = array();
                    $array_number_item = array();
                    foreach ($array_item_custom as $array_item_custom_val) {
                        $size_window = $array_item_custom_val[0];
                        $number_item = $array_item_custom_val[1];
                        if (0 <= $size_window && $size_window < 576) {
                            $array_item_custom_tmp['m'] = $number_item;
                        } else if (576 <= $size_window && $size_window < 768) {
                            $array_item_custom_tmp['sm'] = $number_item;
                        } else if (768 <= $size_window && $size_window < 992) {
                            $array_item_custom_tmp['md'] = $number_item;
                        } else if (992 <= $size_window && $size_window < 1200) {
                            $array_item_custom_tmp['lg'] = $number_item;
                        } else if ($size_window >= 1200) {
                            $array_item_custom_tmp['xl'] = $assign['formAtts']['slick_slidestoshow'];
                        }
                        $number_item = $number_item*$assign['formAtts']['slick_row'];
                        $array_item_custom_tmp[$size_window] = $number_item;
                        $array_number_item[] = $number_item;
                    };
                    $assign['formAtts']['array_fake_item'] = $array_item_custom_tmp;
                    
                    if (max($array_number_item) > $assign['formAtts']['slick_slidestoshow']) {
                        $assign['formAtts']['number_fake_item'] = max($array_number_item);
                    }
                }
            }
        } else {
            // validate module
            $assign['formAtts']['isEnabled'] = false;
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show LeoBlog via Appagebuilder. Please enable LeoBlog module.';
        }
        return $assign;
    }
}
