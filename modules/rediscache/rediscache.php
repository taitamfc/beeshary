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

if (!defined('_PS_VERSION_')) {
    exit;
}

define('REDIS_HELPER', dirname(__FILE__) . '/classes/RedisHelper.php');
define('REDIS_BACKWARD_COMPATIBILITY', dirname(__FILE__) . '/classes/BackwardCompatibility.php');
define('REDIS_UTILITY', dirname(__FILE__) . '/classes/Utility.php');

class RedisCache extends Module
{
    const MODE_SIMPLE = 'simple';
    const MODE_ADVANCED = 'advanced';

    /**
     * An instance of PHP redis
     *
     * @var object
     */
    protected $redis = false;

    protected $mode = self::MODE_SIMPLE;

    public static function loadDependencies()
    {
        $dependencies = [
            REDIS_HELPER,
            REDIS_BACKWARD_COMPATIBILITY,
            REDIS_UTILITY,
        ];

        foreach ($dependencies as $dependency) {
            include_once $dependency;
        }
    }

    /**
     * Initialize module.
     */
    public function __construct()
    {
        $this->name = 'rediscache';
        $this->tab = 'others';
        $this->version = '1.1.1';
        $this->author = 'Vopster';
        $this->need_instance = 1;
        $this->author_address = '0xC19A75eBD3CeE40cBb3713fCC42A2E3B4675589f';
        $this->bootstrap = true;
        $this->secure_key = md5(uniqid(rand(), true));


        $this->ps_versions_compliancy = array(
            'min' => '1.6.0.4',
        );

        parent::__construct();

        self::loadDependencies();

        $this->displayName = $this->l('Redis Cache');

        $this->description
        = $this->l('High performance caching solution using Redis');

        $this->module_key = '08f1cdba1d2ca5a7557f8c4322031a26';

        if (RedisHelper::dependencyCheck($this)) {
            $this->redis = RedisHelper::getConnection($this);
        }
    }

    /**
     * Handles some backward compatibility cases.
     */
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $args);
        }

        return BackwardCompatibility::undefinedMethod($method, $args);
    }

    /**
     * Installs redis cache in Prestashop.
     *
     * @see    Module::install()
     * @return bool The status of the installation process
     */
    public function install()
    {
        if ($this->isAlreadyInstalled()) {
            return true;
        }

        if (Rediscache\Utility::isDebugMode()) {
            $this->_errors[] = $this->l('To install and rediscache module,
                you need to disable "Debug mode" in Advanced Parameters > Performance.');
            return false;
        }

        if (!RedisHelper::dependencyCheck($this)) {
            $this->_errors[] = $this->l('To install rediscache module,
                you need to install php-redis extension. For more information
                see the INSTALL.txt file in this module.');

            return false;
        }

        $this->extendAutoloader();

        return (
            parent::install()
            && $this->installDb()
            && $this->installHooks()
            && $this->installTab()
        );
    }

    /**
     * Provides the install hooks.
     *
     * @return bool
     */
    protected function installHooks()
    {
        if ($backwardCompatibilityHooks = BackwardCompatibility::installHooks($this)) {
            return $backwardCompatibilityHooks;
        }

        return (
            $this->registerHook("actionOutputHTMLBefore")
            && $this->registerHook("actionDispatcherBefore")
            && $this->registerHook("actionAdminControllerSetMedia")
        );
    }

    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminRediscache';
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Redis Cache';
        }

        $tab->id_parent = (int)Tab::getIdFromClassName('AdminAdminPreferences');
        $tab->module = $this->name;

        return $tab->add();
    }

    /**
     * Installs the dabase table.
     *
     * @return void
     */
    public function installDb()
    {
        $return = true;
        $sql = array();

        include dirname(__FILE__) . '/sql_install.php';

        foreach ($sql as $s) {
            $return &= Db::getInstance()->execute($s);
        }

        $defaults = RedisHelper::getDefaultConfig();
        RedisHelper::saveMultipleConfigs($defaults);

        return $return;
    }

    /**
     * Uninstalls redis cache from Prestashop.
     *
     * @see    Module::uninstall()
     * @return bool The status of the uninstallation process
     */
    public function uninstall()
    {
        RedisHelper::overrideDefaultCaching(0);

        return parent::uninstall()
        && $this->uninstallTab()
        && $this->uninstallHooks()
        && $this->uninstallDb()
        && $this->removeOverrides();
    }

    /**
     * Removes the tab.
     *
     * @return void
     */
    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminRediscache');
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
        }

        return false;
    }

    /**
     * Removes the override files.
     *
     * @return bool
     */
    protected function removeOverrides()
    {
        $override_files = array(
            _PS_OVERRIDE_DIR_ . 'classes/cache/CacheRedis.php',
        );

        foreach ($override_files as $override_file) {
            if (file_exists($override_file)) {
                try {
                    unlink($override_file);
                } catch (\Exception $e) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Uninstalls the database tables.
     *
     * @return bool
     */
    public function uninstallDb()
    {
        $sql = array();

        include dirname(__FILE__) . '/sql_install.php';

        $tables = array_keys($sql);

        foreach ($tables as $name) {
            Db::getInstance()->execute('DROP TABLE IF EXISTS ' . $name);
        }

        return true;
    }

    /**
     * Provides the uninstall hooks.
     *
     * @return mixed
     */
    protected function uninstallHooks()
    {
        if ($backwardCompatibilityHooks = BackwardCompatibility::uninstallHooks($this)) {
            return $backwardCompatibilityHooks;
        }

        return (
            $this->unregisterHook("actionDispatcherBefore")
            && $this->unregisterHook("actionOutputHTMLBefore")
            && $this->registerHook("actionAdminControllerSetMedia")
        );
    }

    /**
     * Enables the tab on module enable.
     *
     * @param boolean $force_all
     * @return bool
     */
    public function enable($force_all = false)
    {
        return (
            parent::enable($force_all)
            && Tab::enablingForModule($this->name)
        );
    }

    /**
     * Disables the tab on module disable.
     *
     * @param boolean $force_all
     * @return bool
     */
    public function disable($force_all = false)
    {
        return (
            parent::disable($force_all)
            && Tab::disablingForModule($this->name)
        );
    }

    /**
     * Verifies if an update is being processed.
     *
     * @return boolean
     */
    public function isUpdating()
    {
        $db_version = Db::getInstance()->getValue('SELECT `version` FROM `'
        . _DB_PREFIX_ . 'module` WHERE `name` = \'' . pSQL($this->name) . '\'');

        return version_compare($this->version, $db_version, '>');
    }

    /**
     *
     */
    public function isAlreadyInstalled()
    {
        if (Db::getInstance()->getValue('SELECT `id_module` FROM `'
        . _DB_PREFIX_ . 'module` WHERE name =\'' . pSQL($this->name) . '\'')) {
            return true;
        }
        return false;
    }

    /**
     * Builds the caching system markup.
     *
     * @return void
     */
    protected function getCachingSystemsMarkup()
    {
        $markup = array();

        $markup['warning']['#prefix'] = '<div class="alert alert-warning" role="alert">';
        $markup['warning']['#suffix'] = '</div>';
        $markup['warning']['title'] = array(
            '#prefix' => '<h3>',
            '#suffix' => '</h3>',
            '#markup' => $this->l('The caching system is overriden by Redis Cache module.'),
        );
        $markup['warning']['description'] = array(
            '#prefix' => '<p class="alert-text">',
            '#suffix' => '</p>',
            '#markup' => $this->l("To re-enable the default cache management,
            you need to uninstall or disable Redis Cache module."),
        );

        $markup['configuration']['#prefix'] = '<div class="alert alert-info" role="alert">';
        $markup['configuration']['#suffix'] = '</div>';
        $markup['configuration']['title'] = array(
            '#prefix' => '<h3>',
            '#suffix' => '</h3>',
            '#markup' => $this->l('Cache Management'),
        );
        $markup['configuration']['description'] = array(
            '#prefix' => '<p class="alert-text">',
            '#suffix' => '</p>',
            '#markup' => $this->l('Manage caching from the module configuration page.'),
        );

        $admin_link = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules');

        $markup['configuration']['cache_management'] = array(
            '#prefix' => '<div style="text-align:center; margin: 30px;">',
            '#suffix' => '</div>',
            '#markup' => '<a class="btn btn-primary  pointer" href="'
            . $admin_link . '"><i class="material-icons">settings</i>'
            . $this->l('Configure Redis Cache')
            . '</a>',
        );

        return Rediscache\Utility::render($markup, true);
    }

    /**
     * Adds an extra javascript to handle the admin UI.
     *
     * @return void
     */
    public function hookActionAdminControllerSetMedia($params)
    {
        if ($this->isUpdating() || !Module::isEnabled($this->name)) {
            return false;
        }

        $module_base_path = _MODULE_DIR_ . $this->name;

        $this->context->controller->addJS($module_base_path . '/views/js/rediscache.js', 'all');

        $jsParams = array(
            'ps_version' => _PS_VERSION_,
            'redis_admin_ajax_url' => $this->context->link->getAdminLink('AdminRediscache'),
            'redis_caching_systems' => $this->getCachingSystemsMarkup(),
        );

        foreach ($jsParams as $jsParam => $value) {
            Media::addJsDef(array($jsParam => $value));
        }
    }

    /**
     * Builds and handles the module configuration form
     *
     * @see    Module::getContent()
     * @return string The output of the configartion page
     */
    public function getContent()
    {
        $content = [];
        $configs = RedisHelper::getConfig();

        if (Rediscache\Utility::isDebugMode()) {
            return $this->displayWarning(
                $this->l(
                    'Caching is disabled while debug mode is enabled. Turn off debug mode '
                    . 'in order to enable caching.'
                )
            );
        }

        if (!RedisHelper::dependencyCheck($this)) {
            return $this->displayWarning(
                $this->l(
                    'php-redis extension is not installed or enabled.'
                )
            );
        } else {
            if (Tools::isSubmit('submit_' . $this->name)) {
                $configs = RedisHelper::getConfig();
                RedisHelper::overrideDefaultCaching($configs['PS_REDIS_STATUS']);
                if ($configs['PS_REDIS_STATUS'] == 1 && $this->redis) {
                    try {
                        $this->redis->flushAll();
                    } catch (\Exception $e) {
                    }
                }

                if (RedisHelper::saveMultipleConfigs($configs)) {
                    $content['message']['updated']['#markup'] = $this->displayConfirmation(
                        $this->l('Redis setttings updated')
                    );
                } else {
                    $content['message']['error']['#markup'] = $this->displayError(
                        $this->l('Something went wrong. Check your logs.')
                    );
                }

                BackwardCompatibility::handleSubmit($this->name, 'submit_' . $this->name);
            }

            if ($this->redis) {
                $redis_info = array();

                $content['message']['redis-failure'] = array();

                try {
                    $redis_info = $this->redis->info();
                } catch (\RedisException $e) {
                    $error_msg = $this->l('Couldn\'t connect to redis server.');

                    if (empty($configs['PS_REDIS_PASSWORD'])) {
                        $error_msg .= " " . $this->l('Maybe need to AUTH?');
                    } else {
                        $error_msg .= " " . $this->l('Maybe the password is wrong?');
                    }

                    $content['message']['redis-failure'][]['#markup'] = $this->displayError($error_msg);
                    $this->redis = false;
                }

                /**
                 * Validate session connection if enabled.
                 */
                if ($configs['PS_REDIS_SESSION_CACHE_STATUS'] == 1) {
                    $session_save_path = RedisHelper::buildSessionSavePath();
                    if (!RedisHelper::testConnection($session_save_path)) {
                        $content['message']['redis-failure'][]['#markup'] =
                            $this->displayError('PHP Session handler cannot connect to Redis.');
                    }
                }

                if (!empty($redis_info)) {
                    $panel = array(
                        'title' => $this->l('Redis server status & tips'),
                        'body' => array(
                            'stats-table' => array(
                                '#prefix' => '<table class="table">',
                                '#suffix' => '</table>',
                                'redis-connection' => array(
                                    '#prefix' => '<tr>',
                                    '#suffix' => '</tr>',
                                    'label' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => $this->l('Connection'),
                                    ),
                                    'data' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => RedisHelper::getConfig('PS_REDIS_CONNECTION')
                                        . ' <span style="color: green; font-weight: bold;">('
                                        . $this->l('connected') . ')</span>',
                                    ),
                                ),
                                'redis-library-version' => array(
                                    '#prefix' => '<tr>',
                                    '#suffix' => '</tr>',
                                    'label' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => $this->l('PHPRedis Library Version'),
                                    ),
                                    'data' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => phpversion('redis'),
                                    ),
                                ),
                                'redis-version' => array(
                                    '#prefix' => '<tr>',
                                    '#suffix' => '</tr>',
                                    'label' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => $this->l('Redis Server Version'),
                                    ),
                                    'data' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => $redis_info['redis_version'],
                                    ),
                                ),
                                'redis-mode' => array(
                                    '#prefix' => '<tr>',
                                    '#suffix' => '</tr>',
                                    'label' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => $this->l('Server Mode'),
                                    ),
                                    'data' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => $redis_info['redis_mode'],
                                    ),
                                ),
                                'maxmemory' => array(
                                    '#prefix' => '<tr>',
                                    '#suffix' => '</tr>',
                                    'label' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => $this->l('Max-Memory'),
                                    ),
                                    'data' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => '<strong>' . $redis_info['maxmemory_human']
                                        . '</strong>' . ' / '
                                        . $redis_info['total_system_memory_human'],
                                    ),
                                ),
                                'maxmemory-policy' => array(
                                    '#prefix' => '<tr>',
                                    '#suffix' => '</tr>',
                                    'label' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => $this->l('Max-Memory Policy'),
                                    ),
                                    'data' => array(
                                        '#prefix' => '<td>',
                                        '#suffix' => '</td>',
                                        '#markup' => $redis_info['maxmemory_policy'],
                                    ),
                                ),
                            ),
                        ),
                    );

                    foreach ($panel['body']['stats-table'] as $key => &$row) {
                        if (isset($row['data'])) {
                            $row['comment'] = array(
                                '#prefix' => '<td>',
                                '#suffix' => '</td>',
                            );
                        }

                        $tips = array();
                        $redis_auth = RedisHelper::getConfig('PS_REDIS_PASSWORD');
                        $auth_enabled = Tools::strlen(trim($redis_auth)) == 0;
                        switch ($key) {
                            case 'redis-connection':
                                $tips['set-password'] = array(
                                    'text' => $this->l('It is recommended to secure your
                                    redis server with a password.'),
                                    'condition' => $auth_enabled,
                                    'url' => array(
                                        'href' => 'https://redis.io/topics/security#authentication-feature',
                                    ),
                                );

                                $tips['persistent-connection'] = array(
                                    'text' => $this->l('Persistent connection is an experimental feature.
                                    If you experience issues try to disable it.'),
                                    'condition' => RedisHelper::getConfig('PS_REDIS_PERSISTENT') == 1,
                                );

                                // $tips['tls-connection']['text'] =
                                //     $this->l('If your redis-server supports TLS connection'
                                //        .', use it to increase security.');
                                // $tips['tls-connection']['text'] .= ' ' .
                                //     $this->l('Example: tls://127.0.0.1:6379');

                                $connection_string = RedisHelper::getConfig('PS_REDIS_CONNECTION');
                                $tips['socket-based-connection'] = array(
                                    'text' => $this->l('Socket based connections are slightly faster.'),
                                    'condition' => strpos($connection_string, '.sock') === false,
                                    'url' => array(
                                        'href' => 'https://www.revsys.com/12days/unix-sockets',
                                    ),
                                );

                                break;

                            case 'redis-version':
                                $major_version = explode('.', $redis_info['redis_version'])[0];

                                $tips['upgrade-version'] = array(
                                    'text' => $this->l('It is recommended to upgrade your
                                    redis server to at least 5.x'),
                                    'condition' => $major_version < 5,
                                );
                                break;

                            case 'maxmemory':
                                $tips['set-max-mem'] = array(
                                    'text' => $this->l('It is recommended to set a maximum memory
                                    limit for your redis server.'),
                                    'url' => array(
                                        'href' => 'https://redis.io/topics/lru-cache#maxmemory-configuration-directive',
                                    ),
                                );

                                break;

                            case 'maxmemory-policy':
                                $tips['avoid-no-eviction'] = array(
                                    'text' => $this->l('It is highly recommended to set an eviction policy
                                    (try to avoid noeviction). Recommended: allkayes-lru.'),
                                    'url' => array(
                                        'href' => 'https://redis.io/topics/lru-cache#eviction-policies',
                                    ),
                                );

                                break;
                        }

                        if (!empty($tips)) {
                            $row['comment']['list'] = array(
                                '#prefix' => '<ul>',
                                '#suffix' => '</ul>',
                            );

                            foreach ($tips as $key => $tip) {
                                if (isset($tip['condition']) && !$tip['condition']) {
                                    continue;
                                }

                                $row['comment']['list'][$key] = array(
                                    '#prefix' => '<li>',
                                    '#suffix' => '</li>',
                                    'type' => array(
                                        '#markup' => isset($tip['type'])
                                        ? ($tip['type'] . ': ') : ('<strong>' . $this->l('TIP') . '</strong>: '),
                                    ),
                                    'text' => array(
                                        '#markup' => $tip['text'],
                                    ),
                                );

                                if (isset($tip['url']['href'])) {
                                    $row['comment']['list'][$key]['link'] = array(
                                        '#prefix' => '<a target="_blank" href="' . $tip['url']['href'] . '"> (',
                                        '#suffix' => ')</a>',
                                        '#markup' => $this->l('learn more'),
                                    );
                                }
                            }
                        }
                    }
                    $content['redis_info']['#markup'] = RedisCache\Utility::buildPanel($panel);
                }
            } else {
                $content['message']['status']['#prefix'] =
                    '<div class="alert alert-warning">';
                $content['message']['status']['#suffix'] =
                    '</div>';
                $content['message']['status']['#markup'] =
                $this->l('Caching is disabled.');
            }

            $content['configForm'] = [
                '#markup' => $this->getConfigForm(),
            ];
        }

        return Rediscache\Utility::render($content);
    }

    /**
     * Extends the autoloader so new class can be added as an override.
     */
    protected function extendAutoloader()
    {
        PrestaShopAutoload::getInstance()->index['CacheRedis'] = array(
            'path' => '',
            'type' => 'class',
            'override' => false,
        );

        PrestaShopAutoload::getInstance()->index['CacheRedisCore'] = array(
            'path' => 'classes/cache/CacheRedis.php',
            'type' => 'class',
            'override' => false,
        );
    }

    /**
     * Creates the configuration form for the module
     *
     * @return array The module's configuration form
     */
    public function getConfigForm()
    {
        if (Rediscache\Utility::isDebugMode()) {
            return ;
        }

        $connection_configuration = array();
        $database_options = array();
        $fpc = array();
        // $session_cache = array();

        if ($this->redis) {
            for ($i = 0; $i < (int) $this->redis->config("GET", 'databases')['databases']; $i++) {
                $database_options[] = array(
                    'id_option' => $i,
                    'name' => $this->l('DB') . ' ' . $i,
                );
            }
        }

        $connection_inputs = array(
            'enable_cache' => array(
                'type' => 'switch',
                'label' => $this->l('Enable Redis Cache'),
                'name' => 'PS_REDIS_STATUS',
                'desc' =>
                $this->l(
                    'Enables Redis as a cache backend.'
                ),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            'connection_string' => array(
                'type' => 'text',
                'label' => $this->l('Redis connection string'),
                'name' => 'PS_REDIS_CONNECTION',
                'class' => 'fixed-width-xl',
                'desc' =>
                $this->l(
                    'TCP or UNIX socket.'
                ),
            ),
            'cache_prefix' => array(
                'type' => 'text',
                'label' => $this->l('Redis cache prefix'),
                'name' => 'PS_REDIS_PREFIX',
                'class' => 'fixed-width-xl',
            ),
            'redis_pass' => array(
                'type' => 'password',
                'label' => $this->l('Password (optional)'),
                'name' => 'PS_REDIS_PASSWORD',
                'class' => 'fixed-width-lg',
                'desc' =>
                $this->l(
                    'Provide a password to your redis server connection.'
                ),
            ),
            'redis_custom_conf' => array(
                'type' => 'textarea',
                'label' => $this->l('Override Redis Configuration'),
                'name' => 'PS_REDIS_CUSTOM_CONFIG',
                'cols' => 30,
                'rows' => 10,
                'desc' =>
                $this->l(
                    'Define custom redis configuration formatted as "key:value" (without quotes). One per line.'
                ),
                'conf_type' => self::MODE_ADVANCED,
            ),
            'persistent_connection' => array(
                'type' => 'switch',
                'label' => $this->l('Use persistent connection'),
                'name' => 'PS_REDIS_PERSISTENT',
                'desc' =>
                $this->l(
                    'Establishes a persistent connection
                        instead of a standard connection.'
                ),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            'cache_db' => array(
                'type' => 'select',
                'label' => $this->l('Redis Database'),
                'name' => 'PS_REDIS_DB',
                'class' => 'fixed-width-xl',
                'options' => array(
                    'query' => $database_options,
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'dependency' => array(
                    'redis' => true,
                ),
            ),
        );

        $this->massageConfigurationInputs($connection_inputs);

        $connection_configuration['form'] = array(
            'legend' => array(
                'title' => $this->l('Redis connection'),
                'icon' => 'icon-cogs',
            ),
            'input' => $connection_inputs,
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        $fpc_inputs = array(
            'cache_index_page' => array(
                'type' => 'switch',
                'label' => $this->l('Cache Home Page'),
                'name' => 'PS_REDIS_CACHE_OBJECT_INDEX',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            'cache_product_pages' => array(
                'type' => 'switch',
                'label' => $this->l('Cache Product Pages'),
                'name' => 'PS_REDIS_CACHE_OBJECT_PRODUCTS',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            'cache_category_pages' => array(
                'type' => 'switch',
                'label' => $this->l('Cache Category Pages'),
                'name' => 'PS_REDIS_CACHE_OBJECT_CATEGORIES',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            'cache_cms_pages' => array(
                'type' => 'switch',
                'label' => $this->l('Cache CMS Pages'),
                'name' => 'PS_REDIS_CACHE_OBJECT_CMS',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            'cache_contact_page' => array(
                'type' => 'switch',
                'label' => $this->l('Cache Contact Page'),
                'name' => 'PS_REDIS_CACHE_OBJECT_CONTACT',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            'cache_stores_page' => array(
                'type' => 'switch',
                'label' => $this->l('Cache Stores Page'),
                'name' => 'PS_REDIS_CACHE_OBJECT_STORES',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            'cache_sitemap_page' => array(
                'type' => 'switch',
                'label' => $this->l('Cache Sitemap Page'),
                'name' => 'PS_REDIS_CACHE_OBJECT_SITEMAP',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            'cache_db_queries' => array(
                'type' => 'switch',
                'label' => $this->l('Cache Database Queries'),
                'name' => 'PS_REDIS_CACHE_OBJECT_DB_QUERIES',
                'desc' => '<div class="alert alert-warning">' .
                $this->l('Caching database queries in combination '
                . ' with other modules may cause issues such as duplicated orders.')
                . '<br />'
                . $this->l('If you experience any issues it is recommended to turn off this feature.')
                . '</div>',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
        );

        $fpc['form'] = array(
            'legend' => array(
                'title' => $this->l('Full-Page Cache & Database Caching Settings'),
                'icon' => 'icon-cogs',
            ),
            'input' => $fpc_inputs,
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        // $session_cache_inputs = array(
        //     'session_cache' => array(
        //         'type' => 'switch',
        //         'label' => $this->l('Enable Session Cache'),
        //         'name' => 'PS_REDIS_SESSION_CACHE_STATUS',
        //         'values' => array(
        //             array(
        //                 'id' => 'active_on',
        //                 'value' => 1,
        //                 'label' => $this->l('Enabled'),
        //             ),
        //             array(
        //                 'id' => 'active_off',
        //                 'value' => 0,
        //                 'label' => $this->l('Disabled'),
        //             ),
        //         ),
        //     ),
        //     'session_connection_string' => array(
        //         'type' => 'text',
        //         'label' => $this->l('Redis connection string'),
        //         'name' => 'PS_REDIS_SESSION_CONNECTION',
        //         'class' => 'fixed-width-xl',
        //         'desc' =>
        //         $this->l(
        //             'TCP or UNIX socket.'
        //         ),
        //     ),
        //     'session_redis_pass' => array(
        //         'type' => 'password',
        //         'label' => $this->l('Password (optional)'),
        //         'name' => 'PS_REDIS_SESSION_PASSWORD',
        //         'class' => 'fixed-width-lg',
        //         'desc' =>
        //         $this->l(
        //             'Provide a password to your redis server connection.'
        //         ),
        //     ),
        //     'session_cache_db' => array(
        //         'type' => 'select',
        //         'label' => $this->l('Redis Session Database'),
        //         'name' => 'PS_REDIS_SESSION_DB',
        //         'class' => 'fixed-width-xl',
        //         'options' => array(
        //             'query' => $database_options,
        //             'id' => 'id_option',
        //             'name' => 'name',
        //         ),
        //         'dependency' => array(
        //             'redis' => true,
        //         ),
        //     ),
        // );

        // $this->massageConfigurationInputs($session_cache_inputs);

        // $session_cache['form'] = array(
        //     'legend' => array(
        //         'title' => $this->l('PHP Session Cache (Redis)'),
        //         'icon' => 'icon-cogs',
        //     ),
        //     'input' => $session_cache_inputs,
        //     'submit' => array(
        //         'title' => $this->l('Save'),
        //     ),
        // );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang
        = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
        Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_' . $this->name;
        $helper->currentIndex
        = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name
        . '&tab_module=' . $this->tab
        . '&module_name=' . $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => RedisHelper::getConfig(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($connection_configuration, $fpc));
    }

    /**
     *
     */
    protected function isControllerCacheEnabled($controller = false)
    {
        if (!$controller) {
            $controller = Dispatcher::getInstance()->getController();
        }

        /**
         * We don't allow serving admin pages from cache.
         */
        if ($controller == 'AdminModules') {
            return false;
        }

        $controllers_cache_map = array(
            'product' => 'PS_REDIS_CACHE_OBJECT_PRODUCTS',
            'cms' => 'PS_REDIS_CACHE_OBJECT_CMS',
            'category' => 'PS_REDIS_CACHE_OBJECT_CATEGORIES',
            'contact' => 'PS_REDIS_CACHE_OBJECT_CONTACT',
            'stores' => 'PS_REDIS_CACHE_OBJECT_STORES',
            'sitemap' => 'PS_REDIS_CACHE_OBJECT_SITEMAP',
            'index' => 'PS_REDIS_CACHE_OBJECT_INDEX',
        );

        if (isset($controllers_cache_map[$controller])) {
            $config_key = $controllers_cache_map[$controller];
            return RedisHelper::getConfig($config_key);
        }

        return false;
    }

    protected function massageConfigurationInputs(&$input_elements)
    {
        foreach ($input_elements as $key => $element) {
            if ($this->mode == self::MODE_SIMPLE
                && (isset($element['conf_type']) && $element['conf_type'] == self::MODE_ADVANCED)) {
                unset($input_elements[$key]);
            }

            if (isset($element['dependency']['redis']) && $element['dependency']['redis'] && !$this->redis) {
                unset($input_elements[$key]);
            }
        }
    }

    /**
     * Backward Compatibility Hook
     * PS1.6
     */
    public function hookActionDispatcher()
    {
        if (!BackwardCompatibility::versionCheck('1.6.0', '1.6.9')) {
            return;
        }
        $this->hookActionDispatcherBefore();
    }

    /**
     * If a cached page exists for the current request
     * return it and abort
     * PS1.7+
     */
    public function hookActionDispatcherBefore()
    {
        if (!$this->redis || RedisHelper::bypass()) {
            return;
        }

        try {
            $this->redis->ping();
        } catch (\Exception $e) {
            return;
        }

        /**
         * We don't return the cached page if it's disabled
         */
        if (!$this->isControllerCacheEnabled()) {
            return;
        }

        $key = RedisHelper::getKey($this->context);

        $cached = $this->redis->get($key);

        if ($cached) {
            ob_clean();
            header("X-RedisCache: " . $cached['hash']);
            die($cached['html']);
        }
    }

    /**
     * Store response in cache
     *
     * @param array $params
     */
    public function hookActionOutputHTMLBefore(&$params)
    {
        if (!$this->redis || RedisHelper::bypass()) {
            return;
        }

        try {
            $this->redis->ping();
        } catch (\Exception $e) {
            return;
        }

        /**
         * We don't store disabled pages in cache.
         */
        if (!$this->isControllerCacheEnabled()) {
            return;
        }

        $key = RedisHelper::getKey($this->context);
        RedisHelper::store($key, $params["html"]);
    }
}
