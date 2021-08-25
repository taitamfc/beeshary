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

class ApInstagram extends ApShortCodeBase
{
    public $name = 'ApInstagram';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Instagram'),
            'position' => 6,
            'desc' => $this->l('You can config Instagram box'),
            'icon_class' => 'icon-instagram',
            'tag' => 'social');
    }

    public function getConfigList()
    {
        $accordion_type = array(
            array(
                'value' => 'full',
                'text' => $this->l('Always Full')
            ),
            array(
                'value' => 'accordion',
                'text' => $this->l('Always Accordion')
            ),
            array(
                'value' => 'accordion_small_screen',
                'text' => $this->l('Accordion at small screen')
            ),
        );
//        $soption = ApPageSetting::returnYesNo();
//        $get = array(
//            array(
//                'id' => 'popular',
//                'label' => $this->l('Popular')
//            ),
//            array(
//                'id' => 'tagged',
//                'label' => $this->l('Tagged'),
//            ),
//            array(
//                'id' => 'location',
//                'label' => $this->l('Location')
//            ),
//            array(
//                'id' => 'user',
//                'label' => $this->l('User')
//            ),
//        );
        $sort = array(
            array(
                'id' => 'none',
                'label' => $this->l('None')
            ),
            array(
                'id' => 'most-recent',
                'label' => $this->l('Newest to oldest.'),
            ),
            array(
                'id' => 'least-recent',
                'label' => $this->l('Oldest to newest.')
            ),
            array(
                'id' => 'most-liked',
                'label' => $this->l('Highest # of likes to lowest.')
            ),
            array(
                'id' => 'least-liked',
                'label' => $this->l('Lowest # likes to highest.')
            ),
            array(
                'id' => 'most-commented',
                'label' => $this->l('Highest # of comments to lowest.')
            ),
            array(
                'id' => 'least-commented',
                'label' => $this->l('Lowest # of comments to highest.')
            ),
            array(
                'id' => 'random',
                'label' => $this->l('Random order.')
            ),
        );
        $resolution = array(
            array(
                'id' => 'thumbnail',
                'label' => $this->l('thumbnail - 150x150')
            ),
            array(
                'id' => 'low_resolution',
                'label' => $this->l('low_resolution - 306x306'),
            ),
            array(
                'id' => 'standard_resolution',
                'label' => $this->l('standard_resolution - 612x612')
            )
        );
//        $display_type = array(
//            array(
//                'id' => 'list',
//                'label' => $this->l('Display as List')
//            ),
//            array(
//                'id' => 'carousel',
//                'label' => $this->l('Display as Boostrap Carousel'),
//            ),
//            array(
//                'id' => 'owl-carousel',
//                'label' => $this->l('Display as Owl Carousel')
//            )
//        );
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'desc' => $this->l('Auto hide if leave it blank'),
                'lang' => 'true',
                'form_group_class' => 'aprow_general',
                'desc' => $this->l('The script was get from http://instafeedjs.com/'),
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
                'type'       => 'select',
                'label'   => $this->l('Accordion Type'),
                'name'       => 'accordion_type',
                'options' => array(
                    'query' => $accordion_type,
                    'id'       => 'value',
                    'name'       => 'text' ),
                'default' => 'full',
                'hint'    => $this->l('Select a Accordion Type'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Client ID'),
                'name' => 'client_id',
                'class' => 'ap_instagram',
                'desc' => $this->l('Your API client id from Instagram. Required.'),
                'default' => '3e4a239f6a704208ba131d140a751098',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Access Token'),
                'name' => 'access_token',
                'class' => 'ap_instagram',
//                'desc' => $this->l('A valid oAuth token. Required to use get: "user".'),
                'default' => '3953969014.3e4a239.993032cd15cc463cabd9eea3ce9d1639',
            ),
//            array(
//                'type' => 'text',
//                'label' => $this->l('Target'),
//                'name' => 'target',
//                'class' => 'ap_instagram',
//                'desc' => $this->l('The ID of a DOM element you want to add the images to.'),
//                'default' => '',
//            ),
//            array(
//                'type' => 'textarea',
//                'label' => $this->l('Template'),
//                'name' => 'template',
//                'class' => 'ap_instagram',
//                'desc' => $this->l('(Developer Only) Custom HTML template to use for images.'),
//                'default' => '',
//            ),
//            array(
//                'type' => 'select',
//                'label' => $this->l('Get'),
//                'name' => 'get',
//                'class' => 'ap_instagram',
//                'options' => array(
//                    'query' => $get,
//                    'id' => 'id',
//                    'name' => 'label'
//                ),
//                'desc' => $this->l('Customize what Instafeed fetches'),
//                'default' => 'user',
//            ),
//            array(
//                'type' => 'text',
//                'label' => $this->l('Tag Name'),
//                'name' => 'tag_name',
//                'desc' => $this->l('Name of the tag to get. Use with get: "tagged".'),
//                'default' => '',
//            ),
//            array(
//                'type' => 'text',
//                'label' => $this->l('Location ID'),
//                'name' => 'location_id',
//                'desc' => $this->l('(number) Unique id of a location to get. Use with get: "location".'),
//                'default' => '',
//            ),
            array(
                'type' => 'text',
                'label' => $this->l('User ID'),
                'name' => 'user_id',
                'desc' => $this->l('User ID of Instagram Account. Type Number'),
                'default' => '3953969014',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Sort By'),
                'name' => 'sort_by',
                'class' => 'ap_instagram',
                'options' => array(
                    'query' => $sort,
                    'id' => 'id',
                    'name' => 'label'
                ),
                'desc' => $this->l('Sort the images in a set order. Available options are'),
                'default' => 'none',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Links'),
                'desc' => $this->l('Wrap the images with a link to the photo on Instagram. Set empty to use link of Instagram Image'),
                'name' => 'links',
                'default' => '',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Limit'),
                'desc' => $this->l('Number of Images want to get. Max is 20 images, this is rule of Instagram.'),
                'name' => 'limit',
                'default' => '20',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Resolution'),
                'name' => 'resolution',
                'class' => 'ap_instagram',
                'options' => array(
                    'query' => $resolution,
                    'id' => 'id',
                    'name' => 'label'
                ),
                'desc' => $this->l('Size of the images to show.'),
                'default' => 'thumbnail',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Profile Link'),
                'desc' => $this->l('Create link in footer link to profile'),
                'name' => 'profile_link',
                'default' => '',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="space" style="color:red">'.$this->l('Template Type').'</div>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Carousel Type'),
                'class' => 'form-action',
                'name' => 'carousel_type',
                'options' => array(
                    'query' => array(
                        array('id' => 'list', 'name' => $this->l('Normal List')),
                        array('id' => 'owlcarousel', 'name' => $this->l('Owl Carousel')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
                'default' => 'list'
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
        );
        return $inputs;
    }
    
    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        
        if (!Configuration::get('APPAGEBUILDER_LOAD_INSTAFEED')) {
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Can not show Instagram. Please enable Instafeed library in Appagebuilder Configuration.';
            return $assign;
        } elseif (isset($assign['formAtts']['carousel_type']) && $assign['formAtts']['carousel_type'] == 'owlcarousel') {
            if (!Configuration::get('APPAGEBUILDER_LOAD_OWL')) {
                $assign['formAtts']['lib_has_error'] = true;
                $assign['formAtts']['lib_error'] = 'Can not show Instagram. Please enable Owl Carousel library in Appagebuilder Configuration.';
                return $assign;
            }
        }
        
        $assign['formAtts']['get'] = 'user';
        return $assign;
    }
}
