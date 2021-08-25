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

class BackwardCompatibility
{

    /**
     * Helper method to compare PS versions.
     *
     * @param string $min
     * @param string $max
     * @return bool
     */
    public static function versionCheck($min, $max = false)
    {
        if ($max) {
            return _PS_VERSION_ >= $min && _PS_VERSION_ <= $max;
        }

        return _PS_VERSION == $min;
    }

    /**
     * Helper method to handle undefined methods for backward compatibility.
     *
     * @param string $method
     * @param mixed $args
     * @return mixed
     */
    public static function undefinedMethod($method, $args)
    {
        switch ($method) {
            case 'displayWarning':
                if (self::versionCheck('1.6.0', '1.6.1')) {
                    $warnings = reset($args);
                    $output = '
                        <div class="bootstrap">
                            <div class="module_warning alert alert-warning" >
                                <button type="button" class="close" data-dismiss="alert">&times;</button>';
                    if (is_array($warnings)) {
                        $output .= '<ul>';
                        foreach ($warnings as $msg) {
                            $output .= '<li>'.$msg.'</li>';
                        }
                        $output .= '</ul>';
                    } else {
                        $output .= $warnings;
                    }
                    // Close div openned previously
                    $output .= '</div></div>';
                    return $output;
                }
                break;
        }
    }

    /**
     * Redirects on the module configuration page after the form was sent to avoid re-submits.
     * This is an issue in PS1.6.
     *
     * @param string $module
     * @param string $submit
     * @return void
     */
    public static function handleSubmit($module, $submit)
    {
        if (self::versionCheck('1.6.0', '1.6.9')) {
            if (Tools::getValue($submit)) {
                Tools::redirectAdmin('index.php?controller=AdminModules&configure='
                . $module .'&tab_module=others&module_name='
                . $module .'&token='.Tools::getAdminTokenLite('AdminModules'));
            }
        }
    }

    /**
     * Backward compatbility install hooks.
     *
     * @param object $module
     * @return bool
     */
    public static function installHooks($module)
    {
        if (self::versionCheck('1.6.0', '1.6.9')) {
            return (
                $module->registerHook("actionOutputHTMLBefore")
                && $module->registerHook("actionDispatcher")
                && $module->registerHook("actionAdminControllerSetMedia")
            );
        }

        return false;
    }

    /**
     * Backward compatbility uninstall hooks.
     *
     * @param object $module
     * @return bool
     */
    public static function uninstallHooks($module)
    {
        if (self::versionCheck('1.6.0', '1.6.9')) {
            return
                $module->unregisterHook("actionOutputHTMLBefore")
                && $module->unregisterHook("actionDispatcher")
                && $module->unregisterHook("actionAdminControllerSetMedia");
        }

        return false;
    }

    /**
     * Backward compatibility to handle settings.inc.php of PS1.6.
     *
     * @param int $status
     * @return void
     */
    public static function overrideDefaultCaching($status)
    {
        if (self::versionCheck('1.6.0', '1.6.9')) {
            $settings_file = _PS_CORE_DIR_ . '/config/settings.inc.php';

            if (!is_writable($settings_file)) {
                die($settings_file . 'is not writable.');
            }

            $rows = file($settings_file);
            $method = (int)$status ? 'CacheRedis' : 'CacheMemcache';

            foreach ($rows as $key => $row) {
                if (strpos($row, '_PS_CACHING_SYSTEM_') !== false) {
                    if ($method == 'CacheRedis') {
                        $rows[$key] = "class_exists('CacheRedis') ? "
                        . "define('_PS_CACHING_SYSTEM_', 'CacheRedis') "
                        . ": define('_PS_CACHING_SYSTEM_', 'CacheMemcache');\n";
                    } else {
                        $rows[$key] = "define('_PS_CACHING_SYSTEM_', 'CacheMemcache');\n";
                    }
                }
                if (strpos($row, '_PS_CACHE_ENABLED_') !== false) {
                    $rows[$key] = "define('_PS_CACHE_ENABLED_', '$status');\n";
                }
            }
            try {
                file_put_contents($settings_file, $rows);
            } catch (\Exception $e) {
            }

            return true;
        }

        return false;
    }
}
