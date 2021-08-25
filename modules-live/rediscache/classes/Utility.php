<?php
/**
 * Redis Cache powered by Vopster
 *
 *    @author    Vopster
 *    @copyright 2017 Vopster
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *    @link      https://addons.prestashop.com/en/contact-us?id_product=26866
 */

namespace RedisCache;

class Utility
{

    /**
     * Builds a markup based on a "render array".
     *
     * @param array $elements
     * @return string
     */
    public static function render(&$elements, $cleanup = false)
    {
        $prefix = isset($elements['#prefix']) ? $elements['#prefix'] : '';
        $suffix = isset($elements['#suffix']) ? $elements['#suffix'] : '';
        $markup = isset($elements['#markup']) ? $elements['#markup'] : '';

        $output = '';

        if (empty($elements)) {
            return $output;
        }
        $element_keys = array_keys($elements);
        foreach ($element_keys as $key) {
            if (strpos($key, '#') === false) {
                $markup .= self::render($elements[$key]);
            }
        }

        $output = $prefix . $markup . $suffix;

        if ($cleanup) {
            $output = preg_replace('/\s+/', ' ', str_replace(array("\r", "\n"), '', $output));
        }

        return $output;
    }

    /**
     * Checks a path if matches a given pattern.
     *
     * @param string $path  A path to check.
     * @param string $patterns A pattern to test over the path.
     *
     * @return bool Returns true on match.
     */
    public static function matchPath($path, $patterns)
    {
        $to_replace = array(
            '/(\r\n?|\n)/', // newlines
            '/\\\\\*/', // asterisks
        );
        $replacements = array(
            '|',
            '.*',
        );
        $patterns_quoted = preg_quote($patterns, '/');
        $regex = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';
        return (bool) preg_match($regex, $path);
    }


    /**
     * Builds a bootstrap panel.
     *
     * @param array $data
     * @param string $type
     * @return string
     */
    public static function buildPanel($data, $type = 'default')
    {
        $panel = array();
        $panel['#prefix'] = '<div class="panel">';
        $panel['#suffix'] = '</div>';

        if (isset($data['title'])) {
            $data_key = is_array($data['title']) ? 'content' : '#markup';
            $panel['heading']['#prefix'] = '<div class="panel-heading panel-type-' . $type . '">';
            $panel['heading']['#suffix'] = '</div>';
            $panel['heading']['#markup'] = $data['title'];
        }

        if (isset($data['body'])) {
            $data_key = is_array($data['body']) ? 'content' : '#markup';
            $panel['body']['#prefix'] = '<div class="panel-body">';
            $panel['body']['#suffix'] = '</div>';
            $panel['body'][$data_key] = $data['body'];
        }

        if (isset($data['footer'])) {
            $data_key = is_array($data['footer']) ? 'content' : '#markup';
            $panel['footer']['#prefix'] = '<div class="panel-footer">';
            $panel['footer']['#suffix'] = '</div>';
            $panel['footer']['#markup'] = $data['footer'];
        }

        return self::render($panel);
    }

    /**
     * Rebuilds the class index
     *
     * @return bool
     */
    public static function rebuildClassIndex()
    {
        if (method_exists('Tools', 'generateIndex')) {
            \Tools::generateIndex();
            return true;
        } else {
            $class_index_file = _PS_CACHE_DIR_ . 'class_index.php';
            if (file_exists($class_index_file)) {
                try {
                    unlink($class_index_file);
                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * Checks if debug mode is enabled.
     *
     * @return boolean
     */
    public static function isDebugMode()
    {
        return defined('_PS_MODE_DEV_') ? _PS_MODE_DEV_ : false;
    }
}
