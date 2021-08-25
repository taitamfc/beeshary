<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/InixModule.php';

/**
 * Class inixframe
 */
class inixframe extends Inix2Module
{

    /**
     * @var int
     */
    public static $it = 0;

    /**
     *
     */
    function __construct()
    {
        $this->name                   = 'inixframe';
        $this->tab                    = 'other';
        $this->version                = '3.0.1';
        $this->author                 = 'Presta-apps';
        $this->displayName            = 'Presta Apps Dashboard';
        $this->description            = 'Inixweb Framework';
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = array('min' => '1.5.1.0', 'max' => '1.7');
        parent::__construct();

        if (class_exists('InixUpdateClient')) {
            $this->client = new InixUpdateClient(Inix2Config::get('IWFRAME_CLIENT_TOKEN'));
        }

    }

    /**
     * @return bool
     */
    public function install()
    {

        $this->install_hooks = array('displayBackOfficeHeader');

        return parent::install();
    }

    /**
     * @return bool|string
     */
    public function getContent()
    {

        if ($this->clean_layout) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules'));

            return false;
        }

        $this->object_table = 'Configuration';
        $this->className    = 'Configuration';
        $this->display      = 'view';

        $this->context->controller->addCSS($this->getPathUri() . 'views/css/dash.css');
        $this->context->controller->addJS($this->getPathUri() . 'views/js/script.js');

        $this->tpl_view_vars['client_token'] = $this->client->getClientToken();


        if (!Tools::isSubmit('ajax') && ($this->shouldCheckForUpdate() or Tools::isSubmit('refresh'))) {
            $this->cleanUpdateData();
            $this->getOurModules();
            $this->updateCheck($this->errors);

            $banners = $this->client->fetchBanners();
            if (is_array($banners) and isset($banners['banners'])) {
                Inix2Config::put('IWFRAME_BANNERS', $banners['banners']);
            }

            if (Inix2Config::get('IWFRAME_REMOTE_DATA') === false) {
                $this->errors[] = $this->l('Failed to retrieve remote data!');
            }

            if (!count($this->errors)) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=inixframe&conf=4');
            }
        }


        $module_remote_data = @json_decode(Inix2Config::get('IWFRAME_REMOTE_DATA'), true);

        $own_modules_formated = json_decode(Inix2Config::get('IWFRAME_OWN_MODULES_FORMATED'), true);


        $saved_banners = Inix2Config::get('IWFRAME_BANNERS', array());

        if (!is_array($saved_banners)) {
            $saved_banners = array();
        }

        $tpl_banners = array();
        foreach ($saved_banners as $position => $b) {
            list($row, $col) = explode('-', $position);
            $tpl_banners[$row][$col] = $b;
        }

        $this->tpl_view_vars['banners'] = $tpl_banners;


        if (is_array($module_remote_data) and is_array($own_modules_formated)) {
            $aligned = array();
            foreach ($own_modules_formated as $module) {
                if (isset($module_remote_data[$module['name']]['status'])) {
                    $module['status'] = $module_remote_data[$module['name']]['status'];
                } else {
                    $module['status'] = 'error';
                    $module['error']  = $module_remote_data[$module['name']]['error'];
                }
                if ($module['status'] == 'needupdate') {
                    array_unshift($aligned, $module);
                } else {
                    array_push($aligned, $module);
                }

            }

            $this->tpl_view_vars['own_modules'] = $aligned;
            $this->tpl_view_vars['remote_data'] = $module_remote_data;
        } elseif (is_array($own_modules_formated)) {
            $aligned = array();
            foreach ($own_modules_formated as $module) {
                $module['status'] = 'error';
                $module['error']  = $this->l('No Remote data');

                array_push($aligned, $module);
            }

            $this->tpl_view_vars['own_modules'] = $aligned;
            $this->tpl_view_vars['remote_data'] = array();
        }

        return parent::getContent();

    }


    /**
     * @return string
     */
    public function getFrameLocalPath()
    {
        return _PS_MODULE_DIR_ . 'inixframe/';
    }

    /**
     * @return string
     */
    public function getFramePathUri()
    {
        return __PS_BASE_URI__ . 'modules/inixframe/';
    }


    /**
     *
     */
    public function hookDisplayBackOfficeHeader()
    {
        if ($this->shouldCheckForUpdate()) {
            $this->updateCheck();
        }

    }

    /**
     *
     */
    public function displayAjaxFetch()
    {
        $this->json = true;
        if (!count($this->errors)) {
            $this->status = 'ok';
        } else {
            $this->status = 'error';
        }

        return $this->displayAjax();
    }

    /**
     *
     */
    public function ajaxProcessFetch()
    {

        if (!$this->client->getClientToken()) {
            $this->errors[] = $this->l('You should login to update your module');
        }
        if (!Tools::isSubmit('module')) {
            $this->errors[] = $this->l('Module name is required');
        } elseif (!Validate::isModuleName(Tools::getValue('module'))) {
            $this->errors[] = $this->l('Module name invalid!');
        }
        /** @var Inix2Module $module */
        $module = Module::getInstanceByName(Tools::getValue('module'));
        if ($module == false) {
            $this->errors[] = $this->l('Modules sis invalid!');
        }


        if (!count($this->errors)) {
            $author = 'presta-apps';
            if (stristr($module->author, 'hot-presta')) {
                $author = 'hot-presta';
            }


            $response = $this->client->fetch($module->name, $author, $module->dist_chanel);

            if ($response === false) {
                $this->errors[] = $this->l('Invalid response!');
            } elseif ($this->client->getStatus() == 'error') {
                $this->errors = $this->client->getErrors();
            } else {
                if (!isset($response['archive'])) {
                    $this->errors[] = $this->l('Invalid response from the server');
                } else {
                    if (!is_dir($this->getFrameLocalPath() . 'tmp')) {
                        mkdir($this->getFrameLocalPath() . 'tmp');
                    }

                    list($file_content, $hash) = explode(':', $response['archive']);

                    $tmp_filename = $this->getFrameLocalPath() . 'tmp/' . $module->name . '_latest.zip';

                    file_put_contents($tmp_filename, base64_decode($file_content));
                    if (md5_file($tmp_filename) != $hash) {
                        $this->errors[] = $this->l('Downloaded archive is different from that on the server');
                        unlink($tmp_filename);
                        Tools::deleteDirectory($this->getFrameLocalPath() . 'tmp');
                    } else {
                        if (!Tools::ZipTest($tmp_filename)) {
                            $this->errors[] = $this->l('Invalid archive downloaded');
                        } else {
                            $ret = Tools::ZipExtract($tmp_filename, _PS_MODULE_DIR_);
                            if (!$ret) {
                                $this->errors[] = $this->l('Problem with extracting module archive!');
                            } else {
                                $this->confirmations[] = $this->l('Module updated');
                                $this->cleanUpdateData();
                            }
                        }
                        unlink($tmp_filename);
                        Tools::deleteDirectory($this->getFrameLocalPath() . 'tmp');
                    }
                }
            }
        }
    }

    /**
     *
     */
    public function displayAjaxUpgrade()
    {
        $this->json = true;
        if (!count($this->errors)) {
            $this->status = 'ok';
        } else {
            $this->status = 'error';
        }

        return $this->displayAjax();
    }

    /**
     *
     */
    public function ajaxProcessUpgrade()
    {

        $this->json = true;

        if (!Tools::isSubmit('module')) {
            $this->errors[] = $this->l('Module name is required');
        } elseif (!Validate::isModuleName(Tools::getValue('module'))) {
            $this->errors[] = $this->l('Module name invalid!');
        }
        /** @var Inix2Module $module */
        $module = Module::getInstanceByName(Tools::getValue('module'));
        if ($module === false) {
            $this->errors[] = $this->l('Modules is invalid!');
        }

        if (!count($this->errors)) {
            unlink(_PS_MODULE_DIR_ . $module->name . '/config.xml');
            clearstatcache();
            $module->installed = (int) (bool) Module::isInstalled(Tools::getValue('module'));
            // Upgrade Module process, init check if a module could be upgraded
            if ($module->installed) {
                $sql                      = new DbQuery();
                $module->database_version = DB::getInstance()->getValue($sql->select('version')->from('module')
                                                                            ->where('name = \'' . pSQL($module->name) . '\''));
                $v                        = '';
                if (Module::initUpgradeModule($module)) {
                    // When the XML cache file is up-to-date, the module may not be loaded yet
                    if (!class_exists($module->name)) {
                        require_once(_PS_MODULE_DIR_ . $module->name . '/' . $module->name . '.php');
                    }
                    /** @var Inix2Module $object */
                    if ($object = new $module->name()) {
                        $update_data = $object->runUpgradeModule();
                        if (count($errors_module_list = $object->getErrors())) {
                            $this->errors = array_merge($this->errors, $errors_module_list);
                        } elseif (count($conf_module_list = $object->getConfirmations())) {
                            $this->confirmations = array_merge($this->confirmations, $conf_module_list);
                        }
                        $v = $update_data['upgraded_to'];
                        unset($object);
                    }
                } elseif (Module::getUpgradeStatus($module->name)) {
                    // When the XML cache file is up-to-date, the module may not be loaded yet
                    if (!class_exists($module->name)) {
                        if (file_exists(_PS_MODULE_DIR_ . $module->name . '/' . $module->name . '.php')) {
                            require_once(_PS_MODULE_DIR_ . $module->name . '/' . $module->name . '.php');
                            $object                = new $module->name();
                            $this->confirmations[] = sprintf($this->l('Current version: %s'), $object->version);
                            $this->confirmations[] = $this->l('No file upgrades applied (none exist).');
                            $v                     = $object->version;

                            unset($object);
                        }
                    }
                }

                if (!empty($v)) {
                    $this->client->moduleUpdate($module, $v);
                }
            }
            $this->cleanUpdateData();
        }
    }
}
