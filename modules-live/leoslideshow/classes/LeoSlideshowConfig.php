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

if (!class_exists('LeoSlideshowConfig')) {

    class LeoSlideshowConfig
    {
        const DISABLE = '0';
        const ENABLE = '1';

        public static function getArrayOptions($ids = array(), $names = array())
        {
            $res = array();
            foreach ($names as $key => $val) {
                # module validation
                unset($val);

                $res[] = array(
                    'id' => $ids[$key],
                    'name' => $names[$key],
                );
            }
            return $res;
        }
        const IVIEW_TIMER_360BAR = '360Bar';
        const IVIEW_TIMER_BAR = 'Bar';
        const IVIEW_TIMER_PIE = 'Pie';

        public static function getTimerStyle()
        {
            $ids = array(self::IVIEW_TIMER_360BAR, self::IVIEW_TIMER_BAR, self::IVIEW_TIMER_PIE);
            $names = array('360Bar', 'Bar', 'Pie');
            return self::getArrayOptions($ids, $names);
        }

        public static function getTimerPosition()
        {
            $ids = array('top-left', 'top-right', 'top-center', 'middle', 'bottom-left', 'bottom-right', 'bottom-center');
            $names = array('Top Left', 'Top Right', 'Top Center', 'Middle', 'Bottom Left', 'Bottom Right', 'Bottom Center');
            return self::getArrayOptions($ids, $names);
        }

        public static function getTimerBarStrokeStyle()
        {
            $ids = array('solid', 'none', 'hidden', 'dotted', 'dashed', 'double', 'groove', 'ridge', 'inset', 'outset', 'initial');
            $names = array('Solid', 'None', 'Hidden', 'Dotted', 'Dashed', 'Double', 'Groove', 'Ridge', 'Inset', 'Outset', 'Initial');
            return self::getArrayOptions($ids, $names);
        }
        const IVIEW_NAV_THUMBNAIL = 'thumbnail';
        const IVIEW_NAV_BULLET = 'bullet';

        public static function getNavigatorType()
        {
            $ids = array(self::IVIEW_NAV_THUMBNAIL, self::IVIEW_NAV_BULLET);
            $names = array('Thumbnail', 'Bullet',);
            return self::getArrayOptions($ids, $names);
        }

        public static function getFx()
        {
            /*
              $ids = array('random', 'left-curtain', 'right-curtain', 'top-curtain', 'bottom-curtain', 'strip-down-right', 'strip-down-left',
              'strip-up-right', 'strip-up-left', 'strip-up-down', 'strip-up-down-left', 'strip-left-right', 'strip-left-right-down', 'slide-in-right',
              'slide-in-left', 'slide-in-up', 'slide-in-down', 'fade', 'block-random', 'block-fade', 'block-fade-reverse', 'block-expand',
              'block-expand-reverse', 'block-expand-random', 'zigzag-top', 'zigzag-bottom', 'zigzag-grow-top', 'zigzag-grow-bottom', 'zigzag-drop-top',
              'zigzag-drop-bottom', 'strip-left-fade', 'strip-right-fade', 'strip-top-fade', 'strip-bottom-fade', 'block-drop-random');
              $names = array('Random', 'Left Curtain', 'Right Curtain', 'Top Curtain', 'Bottom Curtain', 'Strip Down Right', 'Strip Down Left',
              'Strip Up Right', 'Strip Up Left', 'Strip Up Down', 'Strip Up Down Left', 'Strip Left Right', 'Strip Left Right Down', 'Slide In Right',
              'Slide In Left', 'Slide In Up', 'Slide In Down', 'Fade', 'Block Random', 'Block Fade', 'Block Fade Reverse', 'Block Expand',
              'Block Expand Reverse', 'Block Expand Random', 'Zigzag Top', 'Zigzag Bottom', 'Zigzag Grow Top', 'Zigzag Grow Bottom', 'Zigzag Drop Top',
              'Zigzag Drop Bottom', 'Strip Left Fade', 'Strip Right Fade', 'Strip Top Fade', 'Strip Bottom Fade', 'Block Drop Random');
             */
            $ids = array('random', 'left-curtain', 'right-curtain', 'top-curtain', 'bottom-curtain', 'strip-down-right', 'strip-down-left',
                'strip-up-right', 'strip-up-left', 'strip-up-down', 'strip-up-down-left', 'strip-left-right', 'strip-left-right-down', 'slide-in-right',
                'slide-in-left', 'slide-in-up', 'slide-in-down', 'fade', 'strip-left-fade', 'strip-right-fade', 'strip-top-fade', 'strip-bottom-fade');
            $names = array('Random', 'Left Curtain', 'Right Curtain', 'Top Curtain', 'Bottom Curtain', 'Strip Down Right', 'Strip Down Left',
                'Strip Up Right', 'Strip Up Left', 'Strip Up Down', 'Strip Up Down Left', 'Strip Left Right', 'Strip Left Right Down', 'Slide In Right',
                'Slide In Left', 'Slide In Up', 'Slide In Down', 'Fade', 'Strip Left Fade', 'Strip Right Fade', 'Strip Top Fade', 'Strip Bottom Fade');
            return self::getArrayOptions($ids, $names);
        }

        public static function getCaptionTransition()
        {
            $ids = array('wipeLeft', 'wipeRight', 'wipeTop', 'wipeBottom', 'expandLeft', 'expandRight', 'expandTop', 'expandBottom', 'fade');
            $names = array('Wipe Left', 'Wipe Right', 'Wipe Top', 'Wipe Bottom', 'Expand Left', 'Expand Right', 'Expand Top', 'Expand Bottom', 'Fade');
            return self::getArrayOptions($ids, $names);
        }
        const TIMER_HIDE_AUTO = '1';
        const TIMER_HIDE_STOP = '2';
        const TIMER_SHOW_AUTO = '3';
        const TIMER_SHOW_STOP = '4';

        public static function getTimerOption()
        {
            $ids = array(self::TIMER_SHOW_AUTO, self::TIMER_HIDE_AUTO, self::TIMER_HIDE_STOP);
            $names = array('Show and Autorun', 'Hide and Autorun', 'Hide and no Autorun');

            return self::getArrayOptions($ids, $names);
        }

        public static function test()
        {
            $ids = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
            $names = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
            return self::getArrayOptions($ids, $names);
        }
        
        const VERSION_FREE = '0';
        const VERSION_PRO = '1';
        const VERSION_CURRENT = '1';
        
        public static function getPermission()
        {
            if (self::VERSION_CURRENT == self::VERSION_PRO) {
                return true;
            } else {
                return false;
            }
        }
    }
}
