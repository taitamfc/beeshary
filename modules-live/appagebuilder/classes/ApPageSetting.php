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

class ApPageSetting
{

    public static function getHookHome()
    {
        return array(
            'displayTop',
            'displayLeftColumn',
            'displayHome',
            'displayRightColumn',
            'displayFooter'
        );
    }
    const HOOK_BOXED = 0;
    const HOOK_FULWIDTH_INDEXPAGE = 1;
    const HOOK_FULWIDTH_OTHERPAGE = 1;
    const ROW_BOXED = 0;
    const ROW_FULWIDTH_INDEXPAGE = 1;
    const HOOK_DISABLE_CACHE = 1;
    const HOOK_ENABLE_CACHE = 0;
    /**
     * hook for fullwidth and boxed
     */
    public static function getIndexHook($type = 1)
    {
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            if ($type == 1) {
                # get name hook
                return array(
                    'displayBanner',
                    'displayNav1',
                    'displayNav2',
                    'displayTop',
                    'displayHome',
                    'displayFooterBefore',
                    'displayFooter',
                    'displayFooterAfter',
                );
            } else if ($type == 2) {
                # get name hook
                return array(
                    'displayBanner' => 'displayBanner',
                    'displayNav1' => 'displayNav1',
                    'displayNav2' => 'displayNav2',
                    'displayTop' => 'displayTop',
                    'displayHome' => 'displayHome',
                    'displayFooterBefore' => 'displayFooterBefore',
                    'displayFooter' => 'displayFooter',
                    'displayFooterAfter' => 'displayFooterAfter',
                );
            } else if ($type == 3) {
                # get default fullwidth or boxed for each hook
                return array(
                    'displayBanner' => self::HOOK_BOXED,
                    'displayNav1' => self::HOOK_BOXED,
                    'displayNav2' => self::HOOK_BOXED,
                    'displayTop' => self::HOOK_BOXED,
                    'displayHome' => self::HOOK_BOXED,
                    'displayFooterBefore' => self::HOOK_BOXED,
                    'displayFooter' => self::HOOK_BOXED,
                    'displayFooterAfter' => self::HOOK_BOXED,
                );
            }
        }
        
        if ($type == 1) {
            # get name hook
            return array(
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayHome',
                'displayFooterBefore',
                'displayFooter',
                'displayFooterAfter',
            );
        } else if ($type == 2) {
            # get name hook
            return array(
                'displayNav1' => 'displayNav1',
                'displayNav2' => 'displayNav2',
                'displayTop' => 'displayTop',
                'displayHome' => 'displayHome',
                'displayFooterBefore' => 'displayFooterBefore',
                'displayFooter' => 'displayFooter',
                'displayFooterAfter' => 'displayFooterAfter',
            );
        } else if ($type == 3) {
            # get default fullwidth or boxed for each hook
            return array                (
                'displayNav1' => self::HOOK_BOXED,
                'displayNav2' => self::HOOK_BOXED,
                'displayTop' => self::HOOK_BOXED,
                'displayHome' => self::HOOK_BOXED,
                'displayFooterBefore' => self::HOOK_BOXED,
                'displayFooter' => self::HOOK_BOXED,
                'displayFooterAfter' => self::HOOK_BOXED,
            );
        }
    }

    /**
     * hook for fullwidth and boxed
     */
    public static function getOtherHook($type = 1)
    {
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            if ($type == 1) {
                # get name hook
                return array(
                    'displayBanner',
                    'displayNav1',
                    'displayNav2',
                    'displayTop',
                    'displayHome',
                    'displayFooterBefore',
                    'displayFooter',
                    'displayFooterAfter',
                );
            } else if ($type == 2) {
                # get name hook
                return array(
                    'displayBanner' => 'displayBanner',
                    'displayNav1' => 'displayNav1',
                    'displayNav2' => 'displayNav2',
                    'displayTop' => 'displayTop',
                    'displayHome' => 'displayHome',
                    'displayFooterBefore' => 'displayFooterBefore',
                    'displayFooter' => 'displayFooter',
                    'displayFooterAfter' => 'displayFooterAfter',
                );
            } else if ($type == 3) {
                # get default value
                return array(
                    'displayBanner' => self::HOOK_BOXED,
                    'displayNav1' => self::HOOK_BOXED,
                    'displayNav2' => self::HOOK_BOXED,
                    'displayTop' => self::HOOK_BOXED,
                    'displayHome' => self::HOOK_BOXED,
                    'displayFooterBefore' => self::HOOK_BOXED,
                    'displayFooter' => self::HOOK_BOXED,
                    'displayFooterAfter' => self::HOOK_BOXED,
                );
            }
        }
        
        if ($type == 1) {
            # get name hook
            return array(
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayHome',
                'displayFooterBefore',
                'displayFooter',
                'displayFooterAfter',
            );
        } else if ($type == 2) {
            # get name hook
            return array(
                'displayNav1' => 'displayNav1',
                'displayNav2' => 'displayNav2',
                'displayTop' => 'displayTop',
                'displayHome' => 'displayHome',
                'displayFooterBefore' => 'displayFooterBefore',
                'displayFooter' => 'displayFooter',
                'displayFooterAfter' => 'displayFooterAfter',
            );
        } else if ($type == 3) {
            # get default value
            return array(
                'displayNav1' => self::HOOK_BOXED,
                'displayNav2' => self::HOOK_BOXED,
                'displayTop' => self::HOOK_BOXED,
                'displayHome' => self::HOOK_BOXED,
                'displayFooterBefore' => self::HOOK_BOXED,
                'displayFooter' => self::HOOK_BOXED,
                'displayFooterAfter' => self::HOOK_BOXED,
            );
        }
    }

    public static function getCacheHook($type = 1)
    {
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            if ($type == 1) {
                # get name hook
                return array(
                    'displayBanner',
                    'displayNav1',
                    'displayNav2',
                    'displayTop',
                    'displayHome',
                    'displayFooterBefore',
                    'displayFooter',
                    'displayFooterAfter',
                );
            } else if ($type == 2) {
                # get name hook
                return array
                    (
                    'displayTop' => 'displayTop',
                    'displayHome' => 'displayHome',
                    'displayFooter' => 'displayFooter',
                );
            } else if ($type == 3) {
                # get default value
                return array
                    (
                    'displayTop' => self::HOOK_ENABLE_CACHE,
                    'displayHome' => self::HOOK_ENABLE_CACHE,
                    'displayFooter' => self::HOOK_ENABLE_CACHE,
                );
            }
        }
        
        if ($type == 1) {
            # get name hook
            return array(
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayHome',
                'displayFooterBefore',
                'displayFooter',
                'displayFooterAfter',
            );
        } else if ($type == 2) {
            # get name hook
            return array
                (
                'displayTop' => 'displayTop',
                'displayHome' => 'displayHome',
                'displayFooter' => 'displayFooter',
            );
        } else if ($type == 3) {
            # get default value
            return array
                (
                'displayTop' => self::HOOK_ENABLE_CACHE,
                'displayHome' => self::HOOK_ENABLE_CACHE,
                'displayFooter' => self::HOOK_ENABLE_CACHE,
            );
        }
    }

    public static function getPositionsName()
    {
        return array('header', 'content', 'footer', 'product');
    }

    /**
     * Get list hooks by type
     * @param type $type: string in {all, header, footer, content, product}
     * @return array
     */
    public static function getHook($type = 'all')
    {
        $list_hook = array();
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            $hook_header_default = array(
                'displayBanner',
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayNavFullWidth',
            );
        } else {
            $hook_header_default = array(
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayNavFullWidth',
            );
        }
        $hook_content_default = array(
            'displayLeftColumn',
            'displayHome',
            'displayRightColumn',
        );
        $hook_footer_default = array(
            'displayFooterBefore',
            'displayFooter',
            'displayFooterAfter',
        );
        $hook_product_default = array(
            'displayLeftColumnProduct',
            'displayRightColumnProduct',
            'displayReassurance',
            'displayProductButtons',
            'displayFooterProduct',
        );
        if ($type == 'all') {
            $list_hook = array_merge($hook_header_default, $hook_content_default, $hook_footer_default, $hook_product_default);
        } else if ($type == 'header') {
            $list_hook = $hook_header_default;
        } else if ($type == 'content') {
            $list_hook = $hook_content_default;
        } else if ($type == 'footer') {
            $list_hook = $hook_footer_default;
        } else if ($type == 'product') {
            $list_hook = $hook_product_default;
        }
        return $list_hook;
    }

    public static function getProductContainer()
    {
        $html = '';
        $html .= '<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">' . "\n";
        $html .= '  <div class="thumbnail-container">' . "\n";
        return $html;
    }
    public static function getProductContainerEnd()
    {
        $html = '';
        $html .= '  </div>' . "\n";
        $html .= '</article>' . "\n";
        return $html;
    }

    public static function getProductFunctionalButtons()
    {
        return '<div class="functional-buttons clearfix">';
    }

    public static function getProductLeftBlock()
    {
        return '    <div class="product-image">';
    }

    public static function getProductRightBlock()
    {
        return '    <div class="product-meta">';
    }

    public static function getProductElementIcon()
    {
//        return array(
//            'add_to_cart' => 'icon-shopping-cart',
//            'color' => 'icon-circle',
//            'compare' => 'icon-bar-chart',
//            'description' => 'icon-file-text',
//            'display_product_price_block' => 'icon-dollar',
//            'flags' => 'icon-flag',
//            'functional_buttons' => 'icon-puzzle-piece',
//            'name' => 'icon-file',
//            'product_delivery_time' => 'icon-time',
//            'reviews' => 'icon-star',
//            'status' => 'icon-question-sign',
//            'view' => 'icon-eye-open',
//            'quick_view' => 'icon-eye-open',
//            'image_container' => 'icon-picture',
//            'price' => 'icon-money',
//            'wishlist' => 'icon-heart',
//        );
        return array(
            'add_to_cart' => 'icon-shopping-cart',
            'add_to_cart_attribute' => 'icon-list',
            'add_to_cart_quantity' => 'icon-sort',
        'product_variants' => 'icon-circle',
            'compare' => 'icon-bar-chart',
            'description' => 'icon-file-text',
            'display_product_price_block' => 'icon-dollar',
        'product_flags' => 'icon-flag',
            'functional_buttons' => 'icon-puzzle-piece',
        'product_name' => 'icon-file',
            'product_delivery_time' => 'icon-time',
            'reviews' => 'icon-star',
            'status' => 'icon-question-sign',
            'view' => 'icon-eye-open',
        'quickview' => 'icon-eye-open',
        'product_thumbnail' => 'icon-picture',
        'product_price_and_shipping' => 'icon-money',
            'wishlist' => 'icon-heart',
        'product_description_short' => 'icon-file-text-o',
        'product_description' => 'icon-file-text',
        );
    }

    public static function writeFile($folder, $file, $value)
    {
        $file = $folder.'/'.$file;
        $handle = fopen($file, 'w+');
        fwrite($handle, ($value));
        fclose($handle);
    }

    public static function getRandomNumber()
    {
        return rand() + time();
    }

    public static function returnYesNo()
    {
        return array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => self::l('Enabled')
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => self::l('Disabled')
        ));
    }

    public static function returnTrueFalse()
    {
        return array(array(
                'id' => 'active_on',
                'value' => 'true',
                'label' => self::l('Enabled')
            ),
            array(
                'id' => 'active_off',
                'value' => 'false',
                'label' => self::l('Disabled')
        ));
    }

    public static function getOrderByBlog()
    {
        return array(
            array(
                'id' => 'id_leoblogcat', 'name' => self::l('Category')),
            array(
                'id' => 'id_leoblog_blog', 'name' => self::l('ID')),
            array(
                'id' => 'meta_title', 'name' => self::l('Title')),
            array(
                'id' => 'date_add', 'name' => self::l('Date add')),
            array(
                'id' => 'date_upd', 'name' => self::l('Date update')),
        );
    }

    public static function getOrderByManu()
    {
        return array(
            array(
                'id' => 'id_manufacturer', 'name' => self::l('ID')),
            array(
                'id' => 'name', 'name' => self::l('Name')),
            array(
                'id' => 'date_add', 'name' => self::l('Date add')),
            array(
                'id' => 'date_upd', 'name' => self::l('Date update')),
        );
    }

    public static function getOrderBy()
    {
        return array(
//            array(
//                'id' => 'position', 'name' => self::l('Position')),    // remove to increase speed
            array(
                'id' => 'id_product', 'name' => self::l('ID')),
            array(
                'id' => 'name', 'name' => self::l('Name')),
            array(
                'id' => 'reference', 'name' => self::l('Reference')),
            array(
                'id' => 'price', 'name' => self::l('Base price')),
            array(
                'id' => 'date_add', 'name' => self::l('Date add')),
            array(
                'id' => 'date_upd', 'name' => self::l('Date update')),
        );
    }

    public static function getColumnGrid()
    {
        return array(
            'xl' => self::l('Extra large devices - Desktops (≥1200px)'),
            'lg' => self::l('Large devices - Desktops (≥992px)'),
            'md' => self::l('Medium devices - Tablets (≥768px)'),
            'sm' => self::l('Small devices (≥576px)'),
            'xs' => self::l('Extra small devices (<576px)'),
            'sp' => self::l('Smart Phones (< 480px)'),
        );
    }

    public static function l($text)
    {
        return $text;
    }

    public static function getAnimations()
    {
        return array(
            'none' => array(
                'name' => self::l('Turn off'),
                'query' => array(
                    array(
                        'id' => 'none',
                        'name' => self::l('None'),
                    )
                )
            ),
            'attention_seekers' => array(
                'name' => self::l('Attention Seekers'),
                'query' => array(
                    array(
                        'id' => 'bounce',
                        'name' => self::l('bounce'),
                    ),
                    array(
                        'id' => 'flash',
                        'name' => self::l('flash'),
                    ), array(
                        'id' => 'pulse',
                        'name' => self::l('pulse'),
                    ), array(
                        'id' => 'rubberBand',
                        'name' => self::l('rubberBand'),
                    ), array(
                        'id' => 'shake',
                        'name' => self::l('shake'),
                    ), array(
                        'id' => 'swing',
                        'name' => self::l('swing'),
                    ), array(
                        'id' => 'tada',
                        'name' => self::l('tada'),
                    ), array(
                        'id' => 'wobble',
                        'name' => self::l('wobble'),
                    )
                )
            ),
            'Bouncing_Entrances' => array(
                'name' => self::l('Bouncing Entrances'),
                'query' => array(
                    array(
                        'id' => 'bounceIn',
                        'name' => self::l('bounceIn'),
                    ),
                    array(
                        'id' => 'bounceInDown',
                        'name' => self::l('bounceInDown'),
                    ),
                    array(
                        'id' => 'bounceInLeft',
                        'name' => self::l('bounceInLeft'),
                    ),
                    array(
                        'id' => 'bounceInRight',
                        'name' => self::l('bounceInRight'),
                    ),
                    array(
                        'id' => 'bounceInUp',
                        'name' => self::l('bounceInUp'),
                    )
                ),
            ),
            'Bouncing_Exits' => array(
                'name' => self::l('Bouncing Exits'),
                'query' => array(
                    array(
                        'id' => 'bounceOut',
                        'name' => self::l('bounceOut'),
                    ),
                    array(
                        'id' => 'bounceOutDown',
                        'name' => self::l('bounceOutDown'),
                    ),
                    array(
                        'id' => 'bounceOutLeft',
                        'name' => self::l('bounceOutLeft'),
                    ),
                    array(
                        'id' => 'bounceOutRight',
                        'name' => self::l('bounceOutRight'),
                    ),
                    array(
                        'id' => 'bounceOutUp',
                        'name' => self::l('bounceOutUp'),
                    )
                ),
            ),
            'Fading_Entrances' => array(
                'name' => self::l('Fading Entrances'),
                'query' => array(
                    array(
                        'id' => 'fadeIn',
                        'name' => self::l('fadeIn'),
                    ),
                    array(
                        'id' => 'fadeInDown',
                        'name' => self::l('fadeInDown'),
                    ),
                    array(
                        'id' => 'fadeInDownBig',
                        'name' => self::l('fadeInDownBig'),
                    ),
                    array(
                        'id' => 'fadeInLeft',
                        'name' => self::l('fadeInLeft'),
                    ),
                    array(
                        'id' => 'fadeInLeftBig',
                        'name' => self::l('fadeInLeftBig'),
                    ),
                    array(
                        'id' => 'fadeInRight',
                        'name' => self::l('fadeInRight'),
                    ),
                    array(
                        'id' => 'fadeInRightBig',
                        'name' => self::l('fadeInRightBig'),
                    ),
                    array(
                        'id' => 'fadeInRight',
                        'name' => self::l('fadeInRight'),
                    ),
                    array(
                        'id' => 'fadeInRightBig',
                        'name' => self::l('fadeInRightBig'),
                    ),
                    array(
                        'id' => 'fadeInUp',
                        'name' => self::l('fadeInUp'),
                    ),
                    array(
                        'id' => 'fadeInUpBig',
                        'name' => self::l('fadeInUpBig'),
                    ),
                ),
            ),
            'Fading_Exits' => array(
                'name' => self::l('Fading Exits'),
                'query' => array(
                    array(
                        'id' => 'fadeOut',
                        'name' => self::l('fadeOut'),
                    ),
                    array(
                        'id' => 'fadeOutDown',
                        'name' => self::l('fadeOutDown'),
                    ),
                    array(
                        'id' => 'fadeOutDownBig',
                        'name' => self::l('fadeOutDownBig'),
                    ),
                    array(
                        'id' => 'fadeOutLeft',
                        'name' => self::l('fadeOutLeft'),
                    ),
                    array(
                        'id' => 'fadeOutRight',
                        'name' => self::l('fadeOutRight'),
                    ),
                    array(
                        'id' => 'fadeOutRightBig',
                        'name' => self::l('fadeOutRightBig'),
                    ),
                    array(
                        'id' => 'fadeOutUp',
                        'name' => self::l('fadeOutUp'),
                    ),
                    array(
                        'id' => 'fadeOutUpBig',
                        'name' => self::l('fadeOutUpBig'),
                    )
                ),
            ),
            'Flippers' => array(
                'name' => self::l('Flippers'),
                'query' => array(
                    array(
                        'id' => 'flip',
                        'name' => self::l('flip'),
                    ),
                    array(
                        'id' => 'flipInX',
                        'name' => self::l('flipInX'),
                    ),
                    array(
                        'id' => 'flipInY',
                        'name' => self::l('flipInY'),
                    ),
                    array(
                        'id' => 'flipOutX',
                        'name' => self::l('flipOutX'),
                    ),
                    array(
                        'id' => 'flipOutY',
                        'name' => self::l('flipOutY'),
                    )
                ),
            ),
            'Lightspeed' => array(
                'name' => self::l('Lightspeed'),
                'query' => array(
                    array(
                        'id' => 'lightSpeedIn',
                        'name' => self::l('lightSpeedIn'),
                    ),
                    array(
                        'id' => 'lightSpeedOut',
                        'name' => self::l('lightSpeedOut'),
                    )
                ),
            ),
            'Rotating_Entrances' => array(
                'name' => self::l('Rotating Entrances'),
                'query' => array(
                    array(
                        'id' => 'rotateIn',
                        'name' => self::l('rotateIn'),
                    ),
                    array(
                        'id' => 'rotateInDownLeft',
                        'name' => self::l('rotateInDownLeft'),
                    ),
                    array(
                        'id' => 'rotateInDownRight',
                        'name' => self::l('rotateInDownRight'),
                    ),
                    array(
                        'id' => 'rotateInUpLeft',
                        'name' => self::l('rotateInUpLeft'),
                    ),
                    array(
                        'id' => 'rotateInUpRight',
                        'name' => self::l('rotateInUpRight'),
                    )
                ),
            ),
            'Rotating_Exits' => array(
                'name' => self::l('Rotating Exits'),
                'query' => array(
                    array(
                        'id' => 'rotateOut',
                        'name' => self::l('rotateOut'),
                    ),
                    array(
                        'id' => 'rotateOutDownLeft',
                        'name' => self::l('rotateOutDownLeft'),
                    ),
                    array(
                        'id' => 'rotateOutDownRight',
                        'name' => self::l('rotateOutDownRight'),
                    ),
                    array(
                        'id' => 'rotateOutUpLeft',
                        'name' => self::l('rotateOutUpLeft'),
                    ),
                    array(
                        'id' => 'rotateOutUpRight',
                        'name' => self::l('rotateOutUpRight'),
                    )
                ),
            ),
            'Specials' => array(
                'name' => self::l('Specials'),
                'query' => array(
                    array(
                        'id' => 'hinge',
                        'name' => self::l('hinge'),
                    ),
                    array(
                        'id' => 'rollIn',
                        'name' => self::l('rollIn'),
                    ),
                    array(
                        'id' => 'rollOut',
                        'name' => self::l('rollOut'),
                    )
                ),
            ),
            'Zoom Entrances' => array(
                'name' => self::l('Zoom Entrances'),
                'query' => array(
                    array(
                        'id' => 'zoomIn',
                        'name' => self::l('zoomIn'),
                    ),
                    array(
                        'id' => 'zoomInDown',
                        'name' => self::l('zoomInDown'),
                    ),
                    array(
                        'id' => 'zoomInLeft',
                        'name' => self::l('zoomInLeft'),
                    ),
                    array(
                        'id' => 'zoomInRight',
                        'name' => self::l('zoomInRight'),
                    ),
                    array(
                        'id' => 'zoomInUp',
                        'name' => self::l('zoomInUp'),
                    )
                ),
            ),
            'Zoom_Exits' => array(
                'name' => self::l('Zoom Exits'),
                'query' => array(
                    array(
                        'id' => 'zoomOut',
                        'name' => self::l('zoomOut'),
                    ),
                    array(
                        'id' => 'zoomOutDown',
                        'name' => self::l('zoomOutDown'),
                    ),
                    array(
                        'id' => 'zoomOutLeft',
                        'name' => self::l('zoomOutLeft'),
                    ),
                    array(
                        'id' => 'zoomOutRight',
                        'name' => self::l('zoomOutRight'),
                    ),
                    array(
                        'id' => 'zoomOutUp',
                        'name' => self::l('zoomOutUp'),
                    )
                ),
            )
        );
    }
    
    //DONGND:: build list animation for group and column
    public static function getAnimationsColumnGroup()
    {
        return array(
            'none' => array(
                'name' => self::l('Turn off'),
                'query' => array(
                    array(
                        'id' => 'none',
                        'name' => self::l('None'),
                    )
                )
            ),
            'Fading_Entrances' => array(
                'name' => self::l('Fading Entrances'),
                'query' => array(
                    array(
                        'id' => 'fadeIn',
                        'name' => self::l('fadeIn'),
                    ),
                    array(
                        'id' => 'fadeInDown',
                        'name' => self::l('fadeInDown'),
                    ),
                    array(
                        'id' => 'fadeInDownBig',
                        'name' => self::l('fadeInDownBig'),
                    ),
                    array(
                        'id' => 'fadeInLeft',
                        'name' => self::l('fadeInLeft'),
                    ),
                    array(
                        'id' => 'fadeInLeftBig',
                        'name' => self::l('fadeInLeftBig'),
                    ),
                    array(
                        'id' => 'fadeInRight',
                        'name' => self::l('fadeInRight'),
                    ),
                    array(
                        'id' => 'fadeInRightBig',
                        'name' => self::l('fadeInRightBig'),
                    ),
                    array(
                        'id' => 'fadeInUp',
                        'name' => self::l('fadeInUp'),
                    ),
                    array(
                        'id' => 'fadeInUpBig',
                        'name' => self::l('fadeInUpBig'),
                    ),
                ),
            ),
            'Bouncing_Entrances' => array(
                'name' => self::l('Bouncing Entrances'),
                'query' => array(
                    array(
                        'id' => 'bounceIn',
                        'name' => self::l('bounceIn'),
                    ),
                    array(
                        'id' => 'bounceInDown',
                        'name' => self::l('bounceInDown'),
                    ),
                    array(
                        'id' => 'bounceInLeft',
                        'name' => self::l('bounceInLeft'),
                    ),
                    array(
                        'id' => 'bounceInRight',
                        'name' => self::l('bounceInRight'),
                    ),
                    array(
                        'id' => 'bounceInUp',
                        'name' => self::l('bounceInUp'),
                    )
                ),
            ),
            'Zoom Entrances' => array(
                'name' => self::l('Zoom Entrances'),
                'query' => array(
                    array(
                        'id' => 'zoomIn',
                        'name' => self::l('zoomIn'),
                    ),
                    array(
                        'id' => 'zoomInDown',
                        'name' => self::l('zoomInDown'),
                    ),
                    array(
                        'id' => 'zoomInLeft',
                        'name' => self::l('zoomInLeft'),
                    ),
                    array(
                        'id' => 'zoomInRight',
                        'name' => self::l('zoomInRight'),
                    ),
                    array(
                        'id' => 'zoomInUp',
                        'name' => self::l('zoomInUp'),
                    )
                ),
            ),
            'attention_seekers' => array(
                'name' => self::l('Attention Seekers'),
                'query' => array(
                    array(
                        'id' => 'bounce',
                        'name' => self::l('bounce'),
                    ),
                    array(
                        'id' => 'flash',
                        'name' => self::l('flash'),
                    ),
                    array(
                        'id' => 'pulse',
                        'name' => self::l('pulse'),
                    ),
                    array(
                        'id' => 'rubberBand',
                        'name' => self::l('rubberBand'),
                    ),
                    array(
                        'id' => 'shake',
                        'name' => self::l('shake'),
                    ),
                    array(
                        'id' => 'swing',
                        'name' => self::l('swing'),
                    ),
                    array(
                        'id' => 'tada',
                        'name' => self::l('tada'),
                    ),
                    array(
                        'id' => 'wobble',
                        'name' => self::l('wobble'),
                    )
                )
            ),
            'Flippers' => array(
                'name' => self::l('Flippers'),
                'query' => array(
                    array(
                        'id' => 'flip',
                        'name' => self::l('flip'),
                    ),
                    array(
                        'id' => 'flipInX',
                        'name' => self::l('flipInX'),
                    ),
                    array(
                        'id' => 'flipInY',
                        'name' => self::l('flipInY'),
                    ),
                    array(
                        'id' => 'flipOutX',
                        'name' => self::l('flipOutX'),
                    ),
                    array(
                        'id' => 'flipOutY',
                        'name' => self::l('flipOutY'),
                    )
                ),
            ),
            'Lightspeed' => array(
                'name' => self::l('Lightspeed'),
                'query' => array(
                    array(
                        'id' => 'lightSpeedIn',
                        'name' => self::l('lightSpeedIn'),
                    ),
                    array(
                        'id' => 'lightSpeedOut',
                        'name' => self::l('lightSpeedOut'),
                    )
                ),
            ),
            'Rotating_Entrances' => array(
                'name' => self::l('Rotating Entrances'),
                'query' => array(
                    array(
                        'id' => 'rotateIn',
                        'name' => self::l('rotateIn'),
                    ),
                    array(
                        'id' => 'rotateInDownLeft',
                        'name' => self::l('rotateInDownLeft'),
                    ),
                    array(
                        'id' => 'rotateInDownRight',
                        'name' => self::l('rotateInDownRight'),
                    ),
                    array(
                        'id' => 'rotateInUpLeft',
                        'name' => self::l('rotateInUpLeft'),
                    ),
                    array(
                        'id' => 'rotateInUpRight',
                        'name' => self::l('rotateInUpRight'),
                    )
                ),
            ),
            'Specials' => array(
                'name' => self::l('Specials'),
                'query' => array(
                    array(
                        'id' => 'hinge',
                        'name' => self::l('hinge'),
                    ),
                    array(
                        'id' => 'rollIn',
                        'name' => self::l('rollIn'),
                    ),
                    array(
                        'id' => 'rollOut',
                        'name' => self::l('rollOut'),
                    )
                ),
            ),
            'Bouncing_Exits' => array(
                'name' => self::l('Bouncing Exits'),
                'query' => array(
                    array(
                        'id' => 'bounceOut',
                        'name' => self::l('bounceOut'),
                    ),
                    array(
                        'id' => 'bounceOutDown',
                        'name' => self::l('bounceOutDown'),
                    ),
                    array(
                        'id' => 'bounceOutLeft',
                        'name' => self::l('bounceOutLeft'),
                    ),
                    array(
                        'id' => 'bounceOutRight',
                        'name' => self::l('bounceOutRight'),
                    ),
                    array(
                        'id' => 'bounceOutUp',
                        'name' => self::l('bounceOutUp'),
                    )
                ),
            ),
            'Fading_Exits' => array(
                'name' => self::l('Fading Exits'),
                'query' => array(
                    array(
                        'id' => 'fadeOut',
                        'name' => self::l('fadeOut'),
                    ),
                    array(
                        'id' => 'fadeOutDown',
                        'name' => self::l('fadeOutDown'),
                    ),
                    array(
                        'id' => 'fadeOutDownBig',
                        'name' => self::l('fadeOutDownBig'),
                    ),
                    array(
                        'id' => 'fadeOutLeft',
                        'name' => self::l('fadeOutLeft'),
                    ),
                    array(
                        'id' => 'fadeOutRight',
                        'name' => self::l('fadeOutRight'),
                    ),
                    array(
                        'id' => 'fadeOutRightBig',
                        'name' => self::l('fadeOutRightBig'),
                    ),
                    array(
                        'id' => 'fadeOutUp',
                        'name' => self::l('fadeOutUp'),
                    ),
                    array(
                        'id' => 'fadeOutUpBig',
                        'name' => self::l('fadeOutUpBig'),
                    )
                ),
            ),
            'Rotating_Exits' => array(
                'name' => self::l('Rotating Exits'),
                'query' => array(
                    array(
                        'id' => 'rotateOut',
                        'name' => self::l('rotateOut'),
                    ),
                    array(
                        'id' => 'rotateOutDownLeft',
                        'name' => self::l('rotateOutDownLeft'),
                    ),
                    array(
                        'id' => 'rotateOutDownRight',
                        'name' => self::l('rotateOutDownRight'),
                    ),
                    array(
                        'id' => 'rotateOutUpLeft',
                        'name' => self::l('rotateOutUpLeft'),
                    ),
                    array(
                        'id' => 'rotateOutUpRight',
                        'name' => self::l('rotateOutUpRight'),
                    )
                ),
            ),
            'Zoom_Exits' => array(
                'name' => self::l('Zoom Exits'),
                'query' => array(
                    array(
                        'id' => 'zoomOut',
                        'name' => self::l('zoomOut'),
                    ),
                    array(
                        'id' => 'zoomOutDown',
                        'name' => self::l('zoomOutDown'),
                    ),
                    array(
                        'id' => 'zoomOutLeft',
                        'name' => self::l('zoomOutLeft'),
                    ),
                    array(
                        'id' => 'zoomOutRight',
                        'name' => self::l('zoomOutRight'),
                    ),
                    array(
                        'id' => 'zoomOutUp',
                        'name' => self::l('zoomOutUp'),
                    )
                ),
            )
        );
    }

    public static function requireShortCode($short_code, $theme_dir = '')
    {
        if (file_exists($theme_dir.'modules/appagebuilder/classes/shortcodes/'.$short_code)) {
            return $theme_dir.'modules/appagebuilder/classes/shortcodes/'.$short_code;
        }
        if (file_exists(_PS_MODULE_DIR_.'appagebuilder/classes/shortcodes/'.$short_code)) {
            return _PS_MODULE_DIR_.'appagebuilder/classes/shortcodes/'.$short_code;
        }
        return false;
    }

    public static function getControllerId($controller, $ids)
    {
        switch ($controller) {
            case 'product':
                $current_id = Tools::getValue('id_product');
                if ($current_id == $ids || (is_array($ids) && in_array($current_id, $ids))) {
                    return $current_id;
                }
                break;
            case 'category':
                $current_id = Tools::getValue('id_category');
                if ($current_id == $ids || (is_array($ids) && in_array($current_id, $ids))) {
                    return $current_id;
                }
                break;
            case 'cms':
                $current_id = Tools::getValue('id_cms');
                if ($current_id == $ids || (is_array($ids) && in_array($current_id, $ids))) {
                    return $current_id;
                }
                break;
            default:
                return false;
        }
    }

    public static function getAllowOverrideHook()
    {
        return array('rightcolumn', 'leftcolumn', 'home', 'top', 'footer');
    }

    public static function returnWidthList()
    {
        return array('12', '11', '10', '9.6', '9', '8', '7.2', '7', '6', '4.8', '5', '4', '3', '2.4', '2', '1');
    }

    public static function getDefaultNameImage($type = 'small')
    {
        $sep = '_';
        $arr = array('small' => 'small'.$sep.'default', 'thickbox' => 'thickbox'.$sep.'default');
        return $arr[$type];
    }

    public static function getModeDebugLog()
    {
        return 0;
    }

    public static function buildGuide($context, $path = '', $current_step = 1)
    {
        $skip = Tools::getIsset('skip') ? Tools::getValue('skip') : '';
        $done = Tools::getIsset('done') ? Tools::getValue('done') : '';
        $reset = Tools::getIsset('ap_guide_reset') ? Tools::getValue('ap_guide_reset') : '';
        if ($skip || $done) {
            Configuration::updateValue('APPAGEBUILDER_GUIDE', 4);
            return '';
        }
        if ($reset) {
            Configuration::updateValue('APPAGEBUILDER_GUIDE', 1);
        }
        $status = Configuration::get('APPAGEBUILDER_GUIDE');
        if ($status > 3) {
            return '';
        }
        // Save next step
        if ($status < $current_step) {
            Configuration::updateValue('APPAGEBUILDER_GUIDE', $current_step);
        }
        if ($current_step == 0) {
            $current_step = $status;
        }
        $url1 = 'index.php?controller=adminmodules&configure=appagebuilder&token='.Tools::getAdminTokenLite('AdminModules')
                .'&tab_module=Home&module_name=appagebuilder';
        $url2 = '';
        $url3 = '';
        $next_step = '';
        // Add new profile
        if ($current_step == 1) {
            $next_step = $context->link->getAdminLink('AdminApPageBuilderProfiles').'&addappagebuilder_profiles';
        }
        if ($current_step == 2) {
            $url2 = $context->link->getAdminLink('AdminApPageBuilderProfiles').'&addappagebuilder_profiles';
            $next_step = $context->link->getAdminLink('AdminApPageBuilderHome');
        }
        if ($current_step == 3) {
            $url2 = $context->link->getAdminLink('AdminApPageBuilderProfiles').'&addappagebuilder_profiles';
            $url3 = $context->link->getAdminLink('AdminApPageBuilderHome');
            $next_step = $context->link->getAdminLink('AdminApPageBuilderHome');
        }
        $context->smarty->assign(array(
            'is_guide' => 1,
            'url1' => $url1,
            'url2' => $url2,
            'url3' => $url3,
            'next_step' => $next_step,
            'step' => $current_step));
        return $context->smarty->fetch($path);
    }

    public static function listFontAwesome()
    {
        return array(
            array('value' => 'icon-font'),
            array('value' => 'icon-bold'),
            array('value' => 'icon-adjust'),
            array('value' => 'icon-calendar'),
            array('value' => 'icon-bookmark'),
            array('value' => 'icon-bolt'),
            array('value' => 'icon-book'),
            array('value' => 'icon-certificate'),
            array('value' => 'icon-bullhorn'),
            array('value' => 'icon-check'),
            array('value' => 'icon-check-square-o'),
            array('value' => 'icon-comments-o'),
            array('value' => 'icon-comment'),
            array('value' => 'icon-credit-card'),
            array('value' => 'icon-thumbs-up'),
            array('value' => 'icon-thumbs-down'),
            array('value' => 'icon-thumbs-o-up'),
            array('value' => 'icon-thumbs-o-down'),
            array('value' => 'icon-truck'),
            array('value' => 'icon-angle-left'),
            array('value' => 'icon-angle-right'),
            array('value' => 'icon-angle-up'),
            array('value' => 'icon-angle-down'),
            array('value' => 'icon-angle-double-left'),
            array('value' => 'icon-angle-double-right'),
            array('value' => 'icon-angle-double-up'),
            array('value' => 'icon-angle-double-down'),
            array('value' => 'icon-arrow-left'),
            array('value' => 'icon-arrow-right'),
            array('value' => 'icon-arrow-up'),
            array('value' => 'icon-arrow-down'),
            array('value' => 'icon-align-left'),
            array('value' => 'icon-align-right'),
            array('value' => 'icon-align-center'),
            array('value' => 'icon-align-justify'),
            array('value' => 'icon-arrow-circle-o-left'),
            array('value' => 'icon-arrow-circle-o-right'),
            array('value' => 'icon-toggle-left'),
            array('value' => 'icon-toggle-right'),
            array('value' => 'icon-eye'),
            array('value' => 'icon-eye-slash'),
            array('value' => 'icon-smile-o'),
            array('value' => 'icon-spinner'),
            array('value' => 'icon-user'),
            array('value' => 'icon-users'),
            array('value' => 'icon-user-plus'),
            array('value' => 'icon-user-times'),
            array('value' => 'icon-user-md'),
            array('value' => 'icon-user-secret'),
            array('value' => 'icon-female'),
            array('value' => 'icon-male'),
            array('value' => 'icon-quote-left'),
            array('value' => 'icon-quote-right'),
            array('value' => 'icon-html5'),
            array('value' => 'icon-css3'),
            array('value' => 'icon-android'),
            array('value' => 'icon-google'),
            array('value' => 'icon-apple'),
            array('value' => 'icon-windows'),
            array('value' => 'icon-linux'),
            array('value' => 'icon-youtube'),
            array('value' => 'icon-twitter'),
            array('value' => 'icon-yahoo'),
            array('value' => 'icon-skype'),
            array('value' => 'icon-trello'),
            array('value' => 'icon-slack'),
            array('value' => 'icon-wordpress'),
            array('value' => 'icon-drupal'),
            array('value' => 'icon-joomla'),
        );
    }
    
    public static function getOverrideHook()
    {
        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            $list_hook = array(
                'displayBanner',
                'displayNav1',
                'displayNav2',
                'displayTop',
                'displayHome',
                'displayLeftColumn',
                'displayRightColumn',
                'displayFooterBefore',
                'displayFooter',
                'displayFooterAfter',
                'displayFooterProduct',
                'displayRightColumnProduct',
                'displayLeftColumnProduct',
            );

            return $list_hook;
        }
        
        $list_hook = array(
            'displayNav1',
            'displayNav2',
            'displayTop',
            'displayHome',
            'displayLeftColumn',
            'displayRightColumn',
            'displayFooterBefore',
            'displayFooter',
            'displayFooterAfter',
            'displayFooterProduct',
            'displayRightColumnProduct',
            'displayLeftColumnProduct',
        );
        
        return $list_hook;
    }
}
