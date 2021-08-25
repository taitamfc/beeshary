<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (_PS_MODE_DEV_) {
    error_reporting(E_ALL ^ E_STRICT);
}

// retrocompatability with 1.5
if (!defined('_PS_CORE_DIR_')) {
    define('_PS_CORE_DIR_', _PS_ROOT_DIR_);
}


require_once dirname(__FILE__) . '/classes/InixMail.php';
require_once dirname(__FILE__) . '/classes/UpdateClient.php';

/**
 * Class Inix2Module
 */
class Inix2Module extends Module
{

    public $path;
    public static $currentIndex;
    public $content;
    public $warnings = array();
    public $informations = array();
    public $confirmations = array();
    public $shopShareDatas = false;

    public $_languages = array();
    public $default_form_language;
    public $allow_employee_form_lang;

    public $layout = 'layout.tpl';
    public $bootstrap = false;

    protected $meta_title;

    public $template = 'content.tpl';

    /** @var string Associated table name */
    public $object_table;


    /** @var string Object identifier inside the associated table */
    protected $object_identifier = false;
    protected $object_identifier_name = 'name';
    /** @var string Tab name */
    public $className;

    public $required_database = false;

    /** @var string Security token */
    public $token;

    /** @var string shop | group_shop */
    public $shopLinkType;

    /** @var string Default ORDER BY clause when $_orderBy is not defined */
    protected $_defaultOrderBy = false;
    protected $_defaultOrderWay = 'ASC';

    public $tpl_form_vars = array();
    public $tpl_list_vars = array();
    public $tpl_delete_link_vars = array();
    public $tpl_option_vars = array();
    public $tpl_view_vars = array();
    public $tpl_required_fields_vars = array();

    public $base_tpl_view = null;
    public $base_tpl_form = null;

    /** @var bool if you want more fieldsets in the form */
    public $multiple_fieldsets = false;

    public $fields_value = false;

    /** @var array Errors displayed after post processing */
    public $errors = array();

    /** @var define if the header of the list contains filter and sorting links or not */
    protected $list_simple_header;

    /** @var array list to be generated */
    protected $fields_list;


    /** @var array edit form to be generated */
    protected $fields_form;

    /** @var override of $fields_form */
    protected $fields_form_override;


    /** @var override form action */
    protected $submit_action;

    /** @var array list of option forms to be generated */
    protected $fields_options = array();


    protected $shopLink;

    /** @var string SQL query */
    protected $_listsql = '';

    /** @var array Cache for query results */
    protected $_list = array();

    /** @var define if the header of the list contains filter and sorting links or not */
    protected $toolbar_title;

    /** @var array list of toolbar buttons */
    protected $toolbar_btn = null;

    /** @var boolean scrolling toolbar */
    protected $toolbar_scroll = true;

    /** @var boolean set to false to hide toolbar and page title */
    protected $show_toolbar = true;

    /** @var boolean set to true to show toolbar and page title for options */
    protected $show_toolbar_options = false;

    /** @var integer Number of results in list */
    protected $_listTotal = 0;

    /** @var boolean Automatically join language table if true */
    public $lang = false;

    /** @var array WHERE clause determined by filter fields */
    protected $_filter;

    /** @var array Temporary SQL table WHERE clause determinated by filter fields */
    protected $_tmpTableFilter = '';

    /** @var array Number of results in list per page (used in select field) */
    protected $_pagination = array(20, 50, 100, 300);

    /** @var integer Default number of results in list per page */
    protected $_default_pagination = 20;

    /** @var string ORDER BY clause determined by field/arrows in list header */
    protected $_orderBy;

    /** @var string Order way (ASC, DESC) determined by arrows in list header */
    protected $_orderWay;

    /** @var array list of available actions for each list row - default actions are view, edit, delete, duplicate */
    protected $actions_available = array('view', 'edit', 'delete', 'duplicate');

    /** @var array list of required actions for each list row */
    protected $actions = array();

    /** @var array list of row ids associated with a given action for witch this action have to not be available */
    protected $list_skip_actions = array();

    /* @var boolean don't show header & footer */
    protected $lite_display = false;
    /** @var bool boolean List content lines are clickable if true */
    protected $list_no_link = false;

    protected $allow_export = false;

    /** @var array $cache_lang cache for traduction */
    public static $cache_lang = array();

    /** @var array required_fields to display in the Required Fields form */
    public $required_fields = array();

    /** @var Helper */
    protected $helper;

    /**
     * @var array actions to execute on multiple selections
     * Usage:
     * array(
     *        'actionName' => array(
     *            'text' => $this->l('Message displayed on the submit button (mandatory)'),
     *            'confirm' => $this->l('If set, this confirmation message will pop-up (optional)')),
     *        'anotherAction' => array(...)
     * );
     *
     * If your action is named 'actionName', you need to have a method named bulkactionName() that will be executed
     * when the button is clicked.
     */
    protected $bulk_actions;

    /**
     * @var array ids of the rows selected
     */
    protected $boxes;

    /** @var string Do not automatically select * anymore but select only what is necessary */
    protected $explicitSelect = false;

    /** @var string Add fields into data query to display list */
    protected $_select;

    /** @var string Join tables into data query to display list */
    protected $_join;

    /** @var string Add conditions into data query to display list */
    protected $_where;

    /** @var string Group rows into data query to display list */
    protected $_group;

    /** @var string Having rows into data query to display list */
    protected $_having;

    protected $is_cms = false;

    /** @var string    identifier to use for changing positions in lists
     * (can be omitted if positions cannot be changed) */
    protected $position_identifier;
    protected $position_group_identifier;

    /** @var boolean Table records are not deleted but marked as deleted if set to true */
    protected $deleted = false;
    /**
     * @var bool is a list filter set
     */
    protected $filter;
    protected $noLink;
    protected $specificConfirmDelete = null;
    protected $colorOnBackground;
    /** @var bool If true, activates color on hover */
    protected $row_hover = true;
    /** @string Action to perform : 'edit', 'view', 'add', ... */
    protected $action;
    protected $display;
    protected $_includeContainer = true;
    protected $tab_modules_list = array('default_list' => array(), 'slider_list' => array());

    public $tpl_folder;


    /** @var bool Redirect or not ater a creation */
    protected $_redirect = true;

    /** @var array Name and directory where class image are located */
    public $fieldImageSettings = array();

    /** @var string Image type */
    public $imageType = 'jpg';

    /** @var instanciation of the class associated with the AdminController */
    protected $object;

    /** @var int current object ID */
    protected $id_object;

    /**
     * @var current controller name without suffix
     */
    public $controller_name;

    public $multishop_context = - 1;
    public $multishop_context_group = true;

    /**
     * Current breadcrumb position as an array of tab names
     */
    protected $breadcrumbs;

    //Bootstrap variable
    public $show_page_header_toolbar = false;
    public $page_header_toolbar_title;
    public $page_header_toolbar_btn = array();
    public $show_form_cancel_button;

    public $admin_webpath;
    /**
     * @var bool If ajax parameter is detected in request, set this flag to true
     */
    public $ajax = false;
    protected $json = false;
    protected $status = '';

    public $installed;
    protected $_html = '';

    protected $redirect_after = null;
    protected $form_dependency = null;


    protected $install_hooks = array();


    protected $install_options = array();
    protected $uninstall_options = array();

    protected $install_tabs = array();

    protected $uninstall_tabs = array();

    protected $install_mails = array();
    protected $uninstall_mails = array();

    public $tabs_options = false;

    public $override_folder;
    public $author_email;
    public $author_domain;
    public $slogan;
    public $show_wellcome;
    private $feedback_labels = array(
        'most'       => 'What are the things you like most of our distributions?',
        'additional' => 'Do you wish for us to add additional features to the module?',
        'other'      => 'Are you thinking of purchasing other distribution of ours?',
        'other_yes'  => 'If yes, which?',
        'company'    => 'What is one thing you like about how our company operates?',
        'better'     => 'How can we be better?',
        'name'       => 'Your Name',
        'email'      => 'Email ( for further contacts )',
        'yes'        => 'Yes',
        'no'         => 'No',
    );

    private $feedback_options = array(
        'most'    => array(
            'features',
            'usability',
            'design',
            'maintainability',
            'support',
            'flexibility',
            'efficiency',
            'security',
            'extensibility'
        ),
        'company' => array(
            'our products',
            'the quality of our distribution',
            'the price of your distribution',
            'our customer service'
        ),
        'better'  => array(
            'more features',
            'better usability',
            'modern design',
            'maintainability',
            'faster support',
            'more flexibility',
            'efficiency',
            'more security',
            'extensibility',
            'easy navigation',
            'more features'
        ),

    );

    private $opt_feedback_most;
    private $opt_feedback_company;
    private $opt_feedback_better;

    protected $has_configuration = true;


    /** @var string Module web path (eg. '/shop/modules/inixframe/') */
    protected $_frame_path = null;

    /**
     * @since 1.5.0.1
     * @var string Module local path (eg. '/www/prestashop/modules/inixframe/')
     */
    protected $frame_local_path = null;

    protected $cookie_prefix;
    /**
     * @var Context
     */
    protected $context;


    /**
     * @var string Default module key
     */
    public $module_key = 'c423e95472d0fa15b4dc2a22d4e70d8c';

    /**
     * @var InixUpdateClient
     */
    protected $client;
    public $clean_layout = false;
    public $dist_chanel = 'presta-apps';

    private $config;

    /**
     * Constructor
     *
     * @param string  $name Module unique name
     * @param Context $context
     */
    public function __construct($name = null, Context $context = null)
    {
        if (is_null($context) or (!is_null($context) and is_null($context->controller))) {
            $context       = Context::getContext();
            $this->context = $context;
        }

        $this->init_autoload();
        global $timer_start;
        $this->timer_start = $timer_start;

        $this->initBranding();

        Inix2Config::load();

        parent::__construct($name, $context);
        // make sure that the paths are initialized
        $this->_path                  = __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        $this->local_path = _PS_MODULE_DIR_.$this->name.'/';
        $this->_frame_path = __PS_BASE_URI__ . 'modules/inixframe/';
        $this->frame_local_path = _PS_MODULE_DIR_ . 'inixframe/';
        //$this->base_template_folder = _PS_BO_ALL_THEMES_DIR_.$this->bo_theme.'/template';
        $this->override_folder = 'inixframe/';
        // Get the name of the folder containing the custom tpl files
        $this->tpl_folder = 'inixframe/';
        Inix2Mail::$module = $this;
        /// prevent of loading unneded things int front office
        if (!$this->context->controller instanceof AdminModulesController) {
            return;
        }

        if ($this->multishop_context == - 1) {
            $this->multishop_context = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;
        }

        $this->token = Tools::getAdminTokenLite('AdminModules');

        if (!Shop::isFeatureActive()) {
            $this->shopLinkType = '';
        }

        $this->initShopContext();

        $this->admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
        $this->admin_webpath = preg_replace(
            '/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/',
            '',
            $this->admin_webpath
        );


    }

    /**
     *
     */
    protected function initBranding()
    {
        $branding = $this->loadBranding();

        if ($branding && is_array($branding)) {
            $this->author        = $branding['author'];
            $this->author_domain = $branding['author_domain'];
            $this->author_email  = $branding['author_email'];
            $this->slogan        = $branding['slogan'];
            $this->show_wellcome = $branding['show_wellcome'];


            if (isset($branding['clean_layout']) and (bool) $branding['clean_layout']) {
                $this->clean_layout = true;
                $this->layout       = 'layout-clean.tpl';
                $this->author       = str_replace(array('www.', '.com'), '', $branding['author']);
            } else {
                $this->description .= ' | Email: ' . $this->author_email;

            }

            if (isset($branding['dist_chanel'])) {
                $this->dist_chanel = $branding['dist_chanel'];
            }
        }
    }

    /**
     * Set breadcrumbs array for the controller page
     */
    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {

        if (is_array($tabs) || count($tabs)) {
            $tabs = array();
        }


        $tabs[] = array('name' => $this->displayName, 'class_name' => $this->name);

        $dummy        = array('name' => '', 'href' => '', 'icon' => '');
        $breadcrumbs3 = array(
            'container' => $dummy,
            'tab'       => $dummy,
            'action'    => $dummy
        );
        if (isset($tabs[0])) {
            $breadcrumbs3['tab']['name'] = $tabs[0]['name'];
            $breadcrumbs3['tab']['href'] = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/' . $this->context->link->getAdminLink($tabs[0]['class_name']);
            if (!isset($tabs[1])) {
                $breadcrumbs3['tab']['icon'] = 'icon-' . $tabs[0]['class_name'];
            }
        }
        if (isset($tabs[1])) {
            $breadcrumbs3['container']['name'] = $tabs[1]['name'];
            $breadcrumbs3['container']['href'] = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/' . $this->context->link->getAdminLink($tabs[1]['class_name']);
            $breadcrumbs3['container']['icon'] = 'icon-' . $tabs[1]['class_name'];
        }

        /* content, edit, list, add, details, options, view */
        switch ($this->display) {
            case 'add':
                $breadcrumbs3['action']['name'] = $this->l('Add');
                $breadcrumbs3['action']['icon'] = 'icon-plus';
                break;
            case 'edit':
                $breadcrumbs3['action']['name'] = $this->l('Edit');
                $breadcrumbs3['action']['icon'] = 'icon-pencil';
                break;
            case '':
            case 'list':
                $breadcrumbs3['action']['name'] = $this->l('List');
                $breadcrumbs3['action']['icon'] = 'icon-th-list';
                break;
            case 'details':
            case 'view':
                $breadcrumbs3['action']['name'] = $this->l('View details');
                $breadcrumbs3['action']['icon'] = 'icon-zoom-in';
                break;
            case 'options':
                $breadcrumbs3['action']['name'] = $this->l('Options');
                $breadcrumbs3['action']['icon'] = 'icon-cogs';
                break;
            case 'generator':
                $breadcrumbs3['action']['name'] = $this->l('Generator');
                $breadcrumbs3['action']['icon'] = 'icon-flask';
                break;
        }

        $this->context->smarty->assign('breadcrumbs3', $breadcrumbs3);

        /* BEGIN - Backward compatibility < 1.6.0.3 */
        $this->breadcrumbs[] = $tabs[0]['name'];

        $navigationPipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $this->context->smarty->assign('navigationPipe', $navigationPipe);
        /* END - Backward compatibility < 1.6.0.3 */
    }

    /**
     * set default toolbar_title to admin breadcrumb
     *
     * @return void
     */
    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ? array_unique($this->breadcrumbs) : array($this->breadcrumbs);


        switch ($this->display) {
            case 'edit':
                $this->toolbar_title[] = $this->l('Edit');
                break;

            case 'add':
                $this->toolbar_title[] = $this->l('Add new');
                break;

            case 'view':
                $this->toolbar_title[] = $this->l('View');
                break;
        }
        if ($filter = $this->addFiltersToBreadcrumbs()) {
            $this->toolbar_title[] = $filter;
        }


        switch ($this->display) {
            case 'feedback':
                $this->toolbar_title = array($this->l('Feedback'));
                break;
            case 'bugreport':
                $this->toolbar_title = array($this->l('Bug Report'));
                break;
        }
    }

    /**
     * @return string
     */
    public function addFiltersToBreadcrumbs()
    {
        if (Tools::isSubmit('submitFilter') && is_array($this->fields_list)) {
            $filters = array();
            foreach ($this->fields_list as $field => $t) {
                if (isset($t['filter_key'])) {
                    $field = $t['filter_key'];
                }
                if ($val = Tools::getValue($this->object_table . 'Filter_' . $field)) {
                    if (!is_array($val)) {
                        $filter_value = '';
                        if (isset($t['type']) && $t['type'] == 'bool') {
                            $filter_value = ((bool) $val) ? $this->l('yes') : $this->l('no');
                        } elseif (is_string($val)) {
                            $filter_value = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                        }
                        if (!empty($filter_value)) {
                            $filters[] = sprintf($this->l('%s: %s'), $t['title'], $filter_value);
                        }
                    } else {
                        $filter_value = '';
                        foreach ($val as $v) {
                            if (is_string($v) && !empty($v)) {
                                $filter_value .= ' - ' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                            }
                        }
                        $filter_value = ltrim($filter_value, ' -');
                        if (!empty($filter_value)) {
                            $filters[] = sprintf($this->l('%s: %s'), $t['title'], $filter_value);
                        }
                    }
                }
            }

            if (count($filters)) {
                return sprintf($this->l('filter by %s'), implode(', ', $filters));
            }
        }
    }

    /**
     * Check for security token
     */
    public function checkToken()
    {
        $token = Tools::getValue('token');

        return (!empty($token) && $token === $this->token);
    }

    /**
     * Set the filters used for the list display
     */
    public function processFilter()
    {

        // Filter memorization
        if (isset($this->object_table)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$this->cookie_prefix . $key});
                } elseif (stripos($key, $this->object_table . 'Filter_') === 0) {
                    $this->context->cookie->{$this->cookie_prefix . $key} = !is_array($value) ? $value : serialize($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
                }
            }


            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->object_table . 'OrderBy') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $this->_defaultOrderBy) {
                        unset($this->context->cookie->{$this->cookie_prefix . $key});
                    } else {
                        $this->context->cookie->{$this->cookie_prefix . $key} = $value;
                    }
                } elseif (stripos($key, $this->object_table . 'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $this->_defaultOrderWay) {
                        unset($this->context->cookie->{$this->cookie_prefix . $key});
                    } else {
                        $this->context->cookie->{$this->cookie_prefix . $key} = $value;
                    }
                }
            }

        }
        $filters = $this->context->cookie->getFamily($this->cookie_prefix . $this->object_table . 'Filter_');

        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null && !strncmp(
                $key,
                $this->cookie_prefix . $this->object_table . 'Filter_',
                7 + Tools::strlen($this->cookie_prefix . $this->object_table)
            )
            ) {
                $key = Tools::substr($key, 7 + Tools::strlen($this->cookie_prefix . $this->object_table));

                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $filter  = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];

                if ($field = $this->filterToField($key, $filter)) {
                    $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists(
                        'type',
                        $field
                    ) ? $field['type'] : false));
                    if (($type == 'date' || $type == 'datetime') && is_string($value)) {
                        $value = Tools::unSerialize($value);
                    }
                    $key = isset($tmp_tab[1]) ? $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' : '`' . $tmp_tab[0] . '`';

                    // Assignement by reference
                    if (array_key_exists('tmpTableFilter', $field)) {
                        $sql_filter = &$this->_tmpTableFilter;
                    } elseif (array_key_exists('havingFilter', $field)) {
                        $sql_filter = &$this->_filterHaving;
                    } else {
                        $sql_filter = &$this->_filter;
                    }

                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (!Validate::isDate($value[0])) {
                                $this->errors[] = Tools::displayError('The \'From\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' >= \'' . pSQL(Tools::dateFrom($value[0])) . '\'';
                            }
                        }

                        if (isset($value[1]) && !empty($value[1])) {
                            if (!Validate::isDate($value[1])) {
                                $this->errors[] = Tools::displayError('The \'To\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' <= \'' . pSQL(Tools::dateTo($value[1])) . '\'';
                            }
                        }
                    } else {
                        $sql_filter .= ' AND ';
                        $check_key = ($key == $this->object_identifier || $key == '`' . $this->object_identifier . '`');

                        if ($type == 'int' || $type == 'bool') {
                            $sql_filter .= (($check_key || $key == '`active`') ? 'a.' : '') . pSQL($key) . ' = ' . (int) $value . ' ';
                        } elseif ($type == 'decimal') {
                            $sql_filter .= ($check_key ? 'a.' : '') . pSQL($key) . ' = ' . (float) $value . ' ';
                        } elseif ($type == 'select') {
                            $sql_filter .= ($check_key ? 'a.' : '') . pSQL($key) . ' = \'' . pSQL($value) . '\' ';
                        } else {
                            $sql_filter .= ($check_key ? 'a.' : '') . pSQL($key) . ' LIKE \'%' . pSQL($value) . '%\' ';
                        }
                    }
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function postProcess()
    {
        if ($this->ajax) {
            $action = Tools::getValue('action');
            if (!empty($action) && method_exists($this, 'ajaxProcess' . Tools::toCamelCase($action))) {
                return $this->{'ajaxProcess' . Tools::toCamelCase($action)}();
            } elseif (method_exists($this, 'ajaxProcess')) {
                return $this->ajaxProcess();
            }
        } else {
            // Process list filtering
            if ($this->filter) {
                $this->processFilter();
            }

            // If the method named after the action exists, call "before" hooks, then call action method, then call "after" hooks
            if (!empty($this->action) && method_exists($this, 'process' . ucfirst(Tools::toCamelCase($this->action)))) {
                // Call process
                return $this->{'process' . Tools::toCamelCase($this->action)}();
            }
        }
    }

    /**
     *
     */
    public function ajaxProcesshideWellcome()
    {
        Inix2Config::put('FRAME_W_' . strtoupper($this->name), 1);
        die('1');
    }

    /**
     *
     */
    public function ajaxProcesshideConscent()
    {
        Inix2Config::put('IWFRAME_CONSCENT_' . strtoupper($this->name), 1);
        die('1');
    }

    /**
     *
     */
    public function displayAjaxRegister()
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
    public function ajaxProcessRegister()
    {

        if (!Tools::isSubmit('update_service_email')) {
            $this->errors[] = $this->l('Email is required');
        } elseif (!Validate::isEmail(Tools::getValue('update_service_email'))) {
            $this->errors[] = $this->l('Email is invalid!');
        }


        if (!Tools::isSubmit('update_service_password')) {
            $this->errors[] = $this->l('Password is required!');
        } elseif (!Validate::isPasswd(Tools::getValue('update_service_password'))) {
            $this->errors[] = sprintf(
                $this->l('Password should be at least %d charactes long'),
                Validate::PASSWORD_LENGTH
            );
        }


        if (!count($this->errors)) {
            $client   = new InixUpdateClient(Inix2Config::get('IWFRAME_CLIENT_TOKEN'));
            $response = $client->register(
                Tools::getValue('update_service_email'),
                Tools::getValue('update_service_password')
            );

            if ($response === false) {
                $this->errors[] = $this->l('Invalid response!');
            } elseif ($client->getStatus() == 'error') {
                $this->errors = $client->getErrors();
            } else {
                if (!isset($response['client_token'])) {
                    $this->errors[] = $this->l('Invalid response from the server');
                } elseif (!preg_match('/^[a-zA-Z0-9]{36}$/', $response['client_token'])) {
                    $this->errors[] = $this->l('Invalid client token!');
                } else {
                    Inix2Config::put('IWFRAME_CLIENT_TOKEN', $response['client_token']);
                    $this->confirmations[] = $this->l('Successfull registration');
                }
            }
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     */
    public function ajaxProcessProductList()
    {
        $query = Tools::getValue('q', false);
        if (!$query or $query == '' or strlen($query) < 1) {
            die();
        }

        /*
		 * In the SQL request the "q" param is used entirely to match result in database.
		 * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
		 * they are no return values just because string:"(ref : #ref_pattern#)"
		 * is not write in the name field of the product.
		 * So the ref pattern will be cut for the search request.
		 */
        if ($pos = strpos($query, ' (ref:')) {
            $query = substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', false);
        $exclude_packs   = (bool) Tools::getValue('exclude_packs', false);

        $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
               (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
               ($excludeVirtuals ? 'AND p.id_product NOT IN (SELECT pd.id_product FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
               ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '');

        $items = Db::getInstance()->executeS($sql);

        if ($items) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . '|' . (int) ($item['id_product']) . "\n";
            }
        }
        die();
    }

    /**
     * Object Delete images
     */
    public function processDeleteImage()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if (($object->deleteImage())) {
                $redirect = self::$currentIndex . '&add' . $this->object_table . '&' . $this->object_identifier . '=' . Tools::getValue($this->object_identifier) . '&conf=7&token=' . $this->token;
                if (!$this->ajax) {
                    $this->redirect_after = $redirect;
                } else {
                    $this->content = 'ok';
                }
            }
        }
        $this->errors[] = Tools::displayError('An error occurred while attempting to delet the image. (cannot load object).');

        return $object;
    }

    /**
     * @param string $text_delimiter
     *
     * @throws PrestaShopException
     */
    public function processExport($text_delimiter = '"')
    {
        // clean buffer
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }
        $this->getList($this->context->language->id, null, null, 0, false);
        if (!count($this->_list)) {
            return;
        }

        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="' . $this->object_table . '_' . date('Y-m-d_His') . '.csv"');

        $headers = array();
        foreach ($this->fields_list as $datas) {
            $headers[] = Tools::htmlentitiesDecodeUTF8($datas['title']);
        }

        $content = array();
        foreach ($this->_list as $i => $row) {
            $content[$i] = array();
            foreach ($this->fields_list as $key => $value) {
                if (isset($row[$key])) {
                    $content[$i][] = Tools::htmlentitiesDecodeUTF8(Tools::nl2br($row[$key]));
                }
            }

        }
        $this->context->smarty->assign(array(
                'export_precontent' => "\xEF\xBB\xBF",
                'export_headers'    => $headers,
                'export_content'    => $content,
                'text_delimiter'    => $text_delimiter
            ));

        $this->layout = 'layout-export.tpl';
    }

    /**
     * Object Delete
     */
    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $res = true;
            // check if request at least one object with noZeroObject
            if (isset($object->noZeroObject) && count(call_user_func(array(
                    $this->className,
                    $object->noZeroObject
                ))) <= 1
            ) {
                $this->errors[] = Tools::displayError('You need at least one object.') .
                                  ' <b>' . $this->object_table . '</b><br />' .
                                  Tools::displayError('You cannot delete all of the items.');
            } elseif (array_key_exists('delete', $this->list_skip_actions) && in_array(
                $object->id,
                $this->list_skip_actions['delete']
            )
            ) { //check if some ids are in list_skip_actions and forbid deletion
                $this->errors[] = Tools::displayError('You cannot delete this item.');
            } else {
                if ($this->deleted) {
                    if (!empty($this->fieldImageSettings)) {
                        $res = $object->deleteImage();
                    }

                    if (!$res) {
                        $this->errors[] = Tools::displayError('Unable to delete associated images.');
                    }

                    $object->deleted = 1;
                    if ($object->update()) {
                        $this->redirect_after = self::$currentIndex . '&conf=1&token=' . $this->token;
                    }
                } elseif ($res = $object->delete()) {
                    $this->afterDelete($object, $object->id);

                    if ($this->redirect_after == false or !isset($this->redirect_after)) {
                        $this->redirect_after = self::$currentIndex . '&conf=1&token=' . $this->token;
                    }
                }
                $this->errors[] = Tools::displayError('An error occurred during deletion.');
                self::addLog(
                    sprintf($this->l('%s deletion', 'AdminTab', false, false), $this->className),
                    1,
                    null,
                    $this->className,
                    (int) $this->object->id,
                    true,
                    (int) $this->context->employee->id
                );

            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while deleting the object.') .
                              ' <b>' . $this->object_table . '</b> ' .
                              Tools::displayError('(cannot load object)');
        }

        return $object;
    }

    /**
     * Call the right method for creating or updating object
     *
     * @return mixed
     */
    public function processSave()
    {


        if ($this->id_object) {
            $this->object = $this->loadObject();

            return $this->processUpdate();
        } else {
            return $this->processAdd();
        }
    }

    /**
     * Object creation
     */
    public function processAdd()
    {


        /* Checking fields validity */
        $this->validateRules();
        if (count($this->errors) <= 0) {
            $this->object = new $this->className();

            $this->copyFromPost($this->object, $this->object_table);
            $this->beforeAdd($this->object);

            if (method_exists($this->object, 'add') && !$this->object->add()) {
                $this->errors[] = Tools::displayError('An error occurred while creating an object.') .
                                  ' <b>' . $this->object_table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
            } /* voluntary do affectation here */
            elseif (($_POST[$this->object_identifier] = $this->object->id) && $this->postImage($this->object->id) && !count($this->errors) && $this->_redirect) {
                self::addLog(
                    sprintf($this->l('%s addition', 'AdminTab', false, false), $this->className),
                    1,
                    null,
                    $this->className,
                    (int) $this->object->id,
                    true,
                    (int) $this->context->employee->id
                );

                $parent_id = (int) Tools::getValue('id_parent', 1);
                $this->afterAdd($this->object);
                $this->updateAssoShop($this->object->id);
                // Save and stay on same form
                if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd' . $this->object_table . 'AndStay')) {
                    $this->redirect_after = self::$currentIndex . '&' . $this->object_identifier . '=' . $this->object->id . '&conf=3&update' . $this->object_table . '&token=' . $this->token;
                }
                // Save and back to parent
                if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd' . $this->object_table . 'AndBackToParent')) {
                    $this->redirect_after = self::$currentIndex . '&' . $this->object_identifier . '=' . $parent_id . '&conf=3&token=' . $this->token;
                }
                // Default behavior (save and back)
                if (empty($this->redirect_after) && $this->redirect_after !== false) {
                    $this->redirect_after = self::$currentIndex . ($parent_id ? '&' . $this->object_identifier . '=' . $this->object->id : '') . '&conf=3&token=' . $this->token;
                }
            }
        }

        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            // if we have errors, we stay on the form instead of going back to the list
            $this->display = 'edit';

            return false;
        }

        return $this->object;
    }


    /**
     * Object update
     */
    public function processUpdate()
    {
        /* Checking fields validity */
        $this->validateRules();

        if (empty($this->errors)) {
            $id = (int) Tools::getValue($this->object_identifier);

            /* Object update */
            if (isset($id) && !empty($id)) {
                $object = new $this->className($id);
                if (Validate::isLoadedObject($object)) {
                    /* Specific to objects which must not be deleted */
                    if ($this->deleted && $this->beforeDelete($object)) {
                        // Create new one with old objet values
                        $object_new = $object->duplicateObject();
                        if (Validate::isLoadedObject($object_new)) {
                            // Update old object to deleted
                            $object->deleted = 1;
                            $object->update();

                            // Update new object with post values
                            $this->copyFromPost($object_new, $this->object_table);
                            $result = $object_new->update();
                            if (Validate::isLoadedObject($object_new)) {
                                $this->afterDelete($object_new, $object->id);
                            }
                        }
                    } else {
                        $this->copyFromPost($object, $this->object_table);
                        $this->beforeUpdate($object);
                        $result = $object->update();
                        $this->afterUpdate($object);
                    }

                    if ($object->id) {
                        $this->updateAssoShop($object->id);
                    }

                    if (!$result) {
                        $this->errors[] = Tools::displayError('An error occurred while updating an object.') .
                                          ' <b>' . $this->object_table . '</b> (' . Db::getInstance()
                                                                                      ->getMsgError() . ')';
                    } elseif ($this->postImage($object->id) && !count($this->errors) && $this->_redirect) {
                        $parent_id = (int) Tools::getValue('id_parent', 1);
                        // Specific back redirect
                        if ($back = Tools::getValue('back')) {
                            $this->redirect_after = urldecode($back) . '&conf=4';
                        }
                        // Specific scene feature
                        // @todo change stay_here submit name (not clear for redirect to scene ... )
                        if (Tools::getValue('stay_here') == 'on' || Tools::getValue('stay_here') == 'true' || Tools::getValue('stay_here') == '1') {
                            $this->redirect_after = self::$currentIndex . '&' . $this->object_identifier . '=' . $object->id . '&conf=4&updatescene&token=' . $this->token;
                        }
                        // Save and stay on same form
                        // @todo on the to following if, we may prefer to avoid override redirect_after previous value
                        if (Tools::isSubmit('submitAdd' . $this->object_table . 'AndStay')) {
                            $this->redirect_after = self::$currentIndex . '&' . $this->object_identifier . '=' . $object->id . '&conf=4&update' . $this->object_table . '&token=' . $this->token;
                        }
                        // Save and back to parent
                        if (Tools::isSubmit('submitAdd' . $this->object_table . 'AndBackToParent')) {
                            $this->redirect_after = self::$currentIndex . '&' . $this->object_identifier . '=' . $parent_id . '&conf=4&token=' . $this->token;
                        }

                        // Default behavior (save and back)
                        if (empty($this->redirect_after)) {
                            $this->redirect_after = self::$currentIndex . ($parent_id ? '&' . $this->object_identifier . '=' . $object->id : '') . '&conf=4&token=' . $this->token;
                        }
                    }
                    self::addLog(
                        sprintf($this->l('%s edition', 'AdminTab', false, false), $this->className),
                        1,
                        null,
                        $this->className,
                        (int) $object->id,
                        true,
                        (int) $this->context->employee->id
                    );

                } else {
                    $this->errors[] = Tools::displayError('An error occurred while updating an object.') .
                                      ' <b>' . $this->object_table . '</b> ' . Tools::displayError('(cannot load object)');
                }
            }
        }
        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            // if we have errors, we stay on the form instead of going back to the list
            $this->display = 'edit';

            return false;
        }

        if (isset($object)) {
            return $object;
        }

        return;
    }

    /**
     * Change object required fields
     */
    public function processUpdateFields()
    {
        if (!is_array($fields = Tools::getValue('fieldsBox'))) {
            $fields = array();
        }

        $object = new $this->className();
        if (!$object->addFieldsRequiredDatabase($fields)) {
            $this->errors[] = Tools::displayError('An error occurred when attempting to update the required fields.');
        } else {
            $this->redirect_after = self::$currentIndex . '&conf=4&token=' . $this->token;
        }

        return $object;
    }

    /**
     * Change object status (active, inactive)
     */
    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if ($object->toggleStatus()) {
                $matches = array();
                if (preg_match('/[\?|&]controller=([^&]*)/', (string) $_SERVER['HTTP_REFERER'], $matches) !== false
                    && strtolower($matches[1]) != strtolower(preg_replace('/controller/i', '', get_class($this)))
                ) {
                    $this->redirect_after = preg_replace(
                        '/[\?|&]conf=([^&]*)/i',
                        '',
                        (string) $_SERVER['HTTP_REFERER']
                    );
                } else {
                    $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
                }

                $id_category = (($id_category = (int) Tools::getValue('id_category')) && Tools::getValue('id_product')) ? '&id_category=' . $id_category : '';
                $this->redirect_after .= '&conf=5' . $id_category;
            } else {
                $this->errors[] = Tools::displayError('An error occurred while updating the status.');
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.') .
                              ' <b>' . $this->object_table . '</b> ' .
                              Tools::displayError('(cannot load object)');
        }

        return $object;
    }

    /**
     * Change object position
     */
    public function processPosition()
    {
        if (!Validate::isLoadedObject($object = $this->loadObject())) {
            $this->errors[] = Tools::displayError('An error occurred while updating the position for an object.') .
                              ' <b>' . $this->object_table . '</b> ' . Tools::displayError('(cannot load object)');
        } elseif (!$object->updatePosition((int) Tools::getValue('way'), (int) Tools::getValue('fposition'))) {
            $this->errors[] = Tools::displayError('Failed to update the position.');
        } else {
            $id_identifier_str    = ($id_identifier = (int) Tools::getValue($this->object_identifier)) ? '&' . $this->object_identifier . '=' . $id_identifier : '';
            $redirect             = self::$currentIndex . '&' . $this->object_table . 'Orderby=position&' . $this->object_table . 'Orderway=asc&conf=5' . $id_identifier_str . '&token=' . $this->token;
            $this->redirect_after = $redirect;
        }

        return $object;
    }

    /**
     * Cancel all filters for this tab
     */
    public function processResetFilters()
    {


        $filters = $this->context->cookie->getFamily($this->cookie_prefix . $this->object_table . 'Filter_');
        foreach ($filters as $cookie_key => $filter) {
            if (strncmp(
                $cookie_key,
                $this->cookie_prefix . $this->object_table . 'Filter_',
                7 + Tools::strlen($this->cookie_prefix . $this->object_table)
            ) == 0
            ) {
                $key = substr($cookie_key, 7 + Tools::strlen($this->cookie_prefix . $this->object_table));

                /* Table alias could be specified using a ! eg. alias!field */
                if (is_array($this->fields_list) && array_key_exists($key, $this->fields_list)) {
                    $this->context->cookie->$cookie_key = null;
                }
                unset($this->context->cookie->$cookie_key);
            }
        }

        if (isset($this->context->cookie->{'submitFilter' . $this->cookie_prefix . $this->object_table})) {
            unset($this->context->cookie->{'submitFilter' . $this->cookie_prefix . $this->object_table});
        }

        if (isset($this->context->cookie->{$this->cookie_prefix . $this->object_table . 'Orderby'})) {
            unset($this->context->cookie->{$this->cookie_prefix . $this->object_table . 'Orderby'});
        }

        if (isset($this->context->cookie->{$this->cookie_prefix . $this->object_table . 'Orderway'})) {
            unset($this->context->cookie->{$this->cookie_prefix . $this->object_table . 'Orderway'});
        }

        $_POST         = array();
        $this->_filter = false;
        unset($this->_filterHaving);
        unset($this->_having);
    }

    /**
     *
     */
    public function assignUpdate()
    {
        $module_remote_data = @json_decode(Inix2Config::get('IWFRAME_REMOTE_DATA'), true);

        if (is_array($module_remote_data) and isset($module_remote_data[$this->name]['status']) and $module_remote_data[$this->name]['status'] == 'needupdate') {
            $this->context->smarty->assign('update_available', true);
        }
    }

    /**
     *
     */
    public function cleanUpdateData()
    {
        Inix2Config::delete('IWFRAME_LAST_UPDATE_CHECK');
        Inix2Config::delete('IWFRAME_OWN_MODULES');
        Inix2Config::delete('IWFRAME_OWN_MODULES_FORMATED');
        Inix2Config::delete('IWFRAME_REMOTE_DATA');
    }

    /**
     * @return bool
     */
    public function shouldCheckForUpdate()
    {
        if (Inix2Config::get('IWFRAME_LAST_UPDATE_CHECK') == false) {
            return true;
        }

        $last_update_check = Inix2Config::get('IWFRAME_LAST_UPDATE_CHECK');

        return $last_update_check < strtotime('-3 days');

    }

    /**
     * @param $tpl_name
     *
     * @return bool
     */
    public function installMailTpl($tpl_name)
    {
        $mails = true;
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) {
            if (!is_dir($this->getLocalPath() . '/mails/' . $l['iso_code'])) {
                $mails &= @mkdir($this->getLocalPath() . '/mails/' . $l['iso_code']);
            }
            $mails &= @copy(
                $this->getLocalPath() . '/mails/' . $tpl_name . '.html',
                $this->getLocalPath() . '/mails/' . $l['iso_code'] . '/' . $tpl_name . '.html'
            );
            if (file_exists($this->getLocalPath() . '/mails/' . $tpl_name . '.txt')) {
                $mails &= @copy(
                    $this->getLocalPath() . '/mails/' . $tpl_name . '.txt',
                    $this->getLocalPath() . '/mails/' . $l['iso_code'] . '/' . $tpl_name . '.txt'
                );
            } else {
                $mails &= @copy(
                    $this->getLocalPath() . '/mails/' . $tpl_name . '.html',
                    $this->getLocalPath() . '/mails/' . $l['iso_code'] . '/' . $tpl_name . '.txt'
                );
            }
        }

        return $mails;
    }

    /**
     * @param array $errors
     */
    protected function updateCheck(&$errors = array())
    {
        if (class_exists('InixUpdateClient')) {
            if (!isset($this->client)) {
                $this->client = new InixUpdateClient(Inix2Config::get('IWFRAME_CLIENT_TOKEN'));
            }
            $own_modules = $this->getOurModules();
            $response    = $this->client->checkUpdate($own_modules);

            if ($response === false) {
                Inix2Config::put('IWFRAME_LAST_UPDATE_CHECK', time());
                $errors[] = $this->l('Unable to connect to the update service');
            } elseif ($this->client->getStatus() == 'ok') {
                Inix2Config::put('IWFRAME_LAST_UPDATE_CHECK', time());
                Inix2Config::put('IWFRAME_REMOTE_DATA', json_encode($response));

            } else {
                $errors = $this->client->getErrors();
            }
        }
    }

    /**
     *
     */
    protected function doModuleInstall()
    {
        if (class_exists('InixUpdateClient')) {
            if (!isset($this->client)) {
                $this->client = new InixUpdateClient(Inix2Config::get('IWFRAME_CLIENT_TOKEN'));
            }
            if ($this->client->getClientToken() != false) {
                $this->client->moduleInstall($this);
            }

        }
    }

    /**
     *
     */
    protected function doModuleUninstall()
    {
        if (class_exists('InixUpdateClient')) {
            if (!isset($this->client)) {
                $this->client = new InixUpdateClient(Inix2Config::get('IWFRAME_CLIENT_TOKEN'));
            }
            if ($this->client->getClientToken() != false) {
                $this->client->moduleUninstall($this);
            }

        }
    }

    /**
     * @param $new_version
     */
    protected function doModuleUpdate($new_version)
    {
        if (class_exists('InixUpdateClient')) {
            if (!isset($this->client)) {
                $this->client = new InixUpdateClient(Inix2Config::get('IWFRAME_CLIENT_TOKEN'));
            }
            if ($this->client->getClientToken() != false) {
                $this->client->moduleUpdate($this, $new_version);
            }

        }
    }

    /**
     * @return array
     */
    public function getOurModules()
    {
        clearstatcache();
        $modules        = Module::getModulesOnDisk();
        $ownmodules     = array();
        $pretty_modules = array();
        /** @var Inix2Module $module */
        foreach ($modules as $module) {
            $branding_path = $this->checkBranding($module->name);
            if ($branding_path == false) {
                continue;
            }

            $branding = $this->loadBranding($module->name);


            $module_data           = array(
                'name'      => $module->name,
                'version'   => $module->version,
                'status'    => Module::isEnabled($module->name),
                'installed' => Module::isInstalled($module->name),
            );
            $author                = str_replace(array('www.', '.com'), '', $branding['author']);
            $module_data['author'] = $author;
            if (isset($branding['dist_chanel'])) {
                $module_data['dist_chanel'] = $branding['dist_chanel'];
            } else {
                $module_data['dist_chanel'] = $this->dist_chanel;
            }

            $ownmodules[] = $module_data;


            $pretty_modules[] = array(
                'name'        => $module->name,
                'version'     => $module->version,
                'displayname' => $module->displayName,
                'description' => $module->description,
            );


        }
        Inix2Config::put('IWFRAME_OWN_MODULES', json_encode($ownmodules));
        Inix2Config::put('IWFRAME_OWN_MODULES_FORMATED', json_encode($pretty_modules));

        return $ownmodules;
    }

    /**
     * @return array
     */
    protected function loadBranding($module_name = null)
    {
        if (is_null($module_name)) {
            $module_name = $this->name;
        }

        $branding_path = $this->checkBranding();
        $branding      = false;

        if ($branding_path) {
            $branding = json_decode(file_get_contents($branding_path), true);
        }

        return $branding;
    }

    /**
     * @param null $module_name
     *
     * @return bool|string
     */
    protected function checkBranding($module_name = null)
    {
        if (is_null($module_name)) {
            $module_name = $this->name;
        }
        if (file_exists($file = _PS_MODULE_DIR_ . $module_name . '/branding.json')) {
            return $file;
        } elseif (file_exists($file = _PS_MODULE_DIR_ . $module_name . '/inixframe/branding.json')) {
            return $file;
        } elseif (file_exists($file = _PS_MODULE_DIR_ . $module_name . '/branding/branding.json')) {
            return $file;
        }

        return false;

    }

    /**
     * Update options and preferences
     */
    protected function processUpdateOptions()
    {
        $this->beforeUpdateOptions();

        $languages               = Language::getLanguages(false);
        $id_lang_default         = (int) Configuration::get('PS_LANG_DEFAULT');
        $hide_multishop_checkbox = (Shop::getTotalShops(false, null) < 2) ? true : false;
        foreach ($this->fields_options as $category_data) {
            if (!isset($category_data['fields'])) {
                continue;
            }

            $fields = $category_data['fields'];

            foreach ($fields as $field => $values) {
                if (isset($values['type']) && $values['type'] == 'selectLang') {
                    foreach ($languages as $lang) {
                        if (Tools::getValue($field . '_' . strtoupper($lang['iso_code']))) {
                            $fields[$field . '_' . strtoupper($lang['iso_code'])] = array(
                                'type'       => 'select',
                                'cast'       => 'strval',
                                'identifier' => 'mode',
                                'list'       => $values['list']
                            );
                        }
                    }
                }
            }

            // Validate fields
            foreach ($fields as $field => $values) {
                // We don't validate fields with no visibility
                if (!$hide_multishop_checkbox && Shop::isFeatureActive() && isset($values['visibility']) && $values['visibility'] > Shop::getContext()) {
                    continue;
                }

                // Check if field is required
                if ((!Shop::isFeatureActive() && isset($values['required']) && $values['required'])
                    || (Shop::isFeatureActive() && isset($_POST['multishopOverrideOption'][$field]) && isset($values['required']) && $values['required'])
                ) {
                    if (isset($values['type']) && ($values['type'] == 'textLang' or $values['type'] == 'textareaLang')) {
                        $value_default_lang = Tools::getValue($field . '_' . $id_lang_default);
                        if ($value_default_lang == false) {
                            $this->errors[] = sprintf(
                                Tools::displayError('field %s is required at least in default language.'),
                                $values['title']
                            );
                        }

                    } elseif (($value = Tools::getValue($field)) == false && (string) $value != '0') {
                        $this->errors[] = sprintf(Tools::displayError('field %s is required.'), $values['title']);
                    }
                }

                $validate_method = $values['validation'];
                // Check field validator
                if ((isset($values['is_array']) && $values['is_array'])) {
                    if (isset($values['requried']) and $values['requried'] and (!Tools::isSubmit($field) or count(Tools::getValue($field)) == 0 or !Tools::getValue($field))) {
                        $this->errors[] = sprintf(Tools::displayError('fields %s is required!'), $values['title']);
                    } elseif (Tools::getValue($field) && isset($values['validation'])) {
                        foreach (Tools::getValue($field) as $f) {
                            if (!Validate::$validate_method($f)) {
                                $this->errors[] = sprintf(
                                    Tools::displayError('field %s is invalid.'),
                                    $values['title']
                                );
                            }
                        }

                    }
                } else {
                    if (isset($values['type']) && $values['type'] == 'textLang' or $values['type'] == 'textareaLang') {
                        $value_default_lang = Tools::getValue($field . '_' . $id_lang_default);
                        foreach ($languages as $language) {
                            $value = Tools::getValue($field . '_' . $language['id_lang']);
                            if (($value_default_lang or $value) && isset($values['validation'])) {
                                if (!Validate::$validate_method(Tools::getValue(
                                    $field . '_' . $language['id_lang'],
                                    Tools::getValue($field . '_' . $id_lang_default)
                                ))
                                ) {
                                    $this->errors[] = sprintf(
                                        Tools::displayError('field %s is invalid.'),
                                        $values['title']
                                    );
                                }
                            }
                        }
                    } elseif (Tools::getValue($field) && isset($values['validation'])) {
                        if (!Validate::$validate_method(Tools::getValue($field))) {
                            $this->errors[] = sprintf(Tools::displayError('field %s is invalid.'), $values['title']);
                        } else { // Set default value
                            if (Tools::getValue($field) === false && isset($values['default'])) {
                                $_POST[$field] = $values['default'];
                            }
                        }
                    }
                }
            }

            if (!count($this->errors)) {
                foreach ($fields as $key => $options) {
                    if (!$hide_multishop_checkbox && Shop::isFeatureActive() && isset($options['visibility']) && $options['visibility'] > Shop::getContext()) {
                        continue;
                    }

                    if (!$hide_multishop_checkbox && Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && empty($options['no_multishop_checkbox']) && empty($_POST['multishopOverrideOption'][$key])) {
                        Configuration::deleteFromContext($key);
                        continue;
                    }

                    // check if a method updateOptionFieldName is available
                    $method_name = 'updateOption' . Tools::toCamelCase($key, true);
                    if (method_exists($this, $method_name)) {
                        $this->$method_name(Tools::getValue($key));
                    } elseif (isset($options['type']) && ($options['type'] == 'textLang' or $options['type'] == 'textareaLang')) {
                        $list = array();
                        foreach ($languages as $language) {
                            $key_lang = Tools::getValue($key . '_' . $language['id_lang']);
                            if (isset($options['required']) and $options['required'] and $language['id_lang'] != $id_lang_default and $key_lang == '') {
                                $key_lang = Tools::getValue($key . '_' . $id_lang_default);
                            }

                            $val = (isset($options['cast']) ? $options['cast']($key_lang) : $key_lang);
                            if ($this->validateField($val, $options)) {
                                if (Validate::isCleanHtml($val)) {
                                    $list[$language['id_lang']] = $val;
                                } else {
                                    $this->errors[] = Tools::displayError('Can not add configuration ' . $key . ' for lang ' . Language::getIsoById((int) $language['id_lang']));
                                }
                            }
                        }
                        Configuration::updateValue($key, $list, isset($options['html']) ? $options['html'] : false);
                    } else {
                        if ($options['type'] == 'products') {
                            $products = explode('-', Tools::getValue($key));
                            array_pop($products);
                            $val = $products;
                        } else {
                            $val = Tools::getValue($key);
                        }


                        if (isset($options['cast'])) {
                            if (isset($options['is_array']) and $options['is_array']) {
                                $val = array_map($options['cast'], $val);
                            } else {
                                $val = $options['cast']($val);
                            }

                        }

                        if ($this->validateField($val, $options)) {
                            if (isset($options['is_array']) and $options['is_array']) {
                                Configuration::updateValue(
                                    $key,
                                    json_encode($val),
                                    isset($options['html']) ? $options['html'] : false
                                );
                            } elseif (Validate::isCleanHtml($val)) {
                                Configuration::updateValue(
                                    $key,
                                    $val,
                                    isset($options['html']) ? $options['html'] : false
                                );
                            } else {
                                $this->errors[] = Tools::displayError('Can not add configuration ' . $key);
                            }
                        }
                    }
                }
            }
        }

        $this->afterUpdateOptions();
        $this->display = 'list';
        if (empty($this->errors)) {
            $this->redirect_after = self::$currentIndex . '&token=' . $this->token . '&conf=6';
        }
    }


    /**
     *
     */
    protected function processAddfeedback()
    {


        if (Tools::getValue('name') == '') {
            $this->errors[] = Tools::displayError('Name is required!');
        } elseif (!Validate::isGenericName(Tools::getValue('name'))) {
            $this->errors[] = Tools::displayError('Name is invalid!');
        }

        if (Tools::getValue('email') == '') {
            $this->errors[] = Tools::displayError('Email is required!');
        } elseif (!Validate::isEmail(Tools::getValue('email'))) {
            $this->errors[] = Tools::displayError('Email is invalid!');
        }

        $content = '';


        if (!count($this->errors)) {
            foreach ($this->feedback_labels as $name => $label) {
                if ($name == 'yes' or $name == 'no') {
                    continue;
                }
                if ($name == 'other_yes' and Tools::getValue('other') == 0) {
                    continue;
                }


                $content .= '
					<p><strong>' . $this->feedback_labels[$name] . '</strong></p>';
                if ($name == 'other') {
                    $content .= Tools::getValue($name) ? 'Yes' : 'No';

                } elseif (isset($this->feedback_options[$name])) {
                    $checks        = false;
                    $check_content = '';


                    foreach ($this->feedback_options[$name] as $id => $value) {
                        if (Tools::isSubmit($name . '_' . $id)) {
                            $checks = true;
                            $check_content .= ' <p>- ' . $value . '</p>';

                        }
                    }
                    if ($checks) {
                        $content .= $check_content;

                    } else {
                        $content .= '<p>No answer</p>';
                    }
                } elseif (Tools::isSubmit($name) and Tools::getValue($name) != '') {
                    $content .= '
					<p>' . Tools::getValue($name) . '</p>';
                } else {
                    $content .= '<p>No answer</p>';
                }
            }


            $template_vars['{feedback_content}']     = $content;
            $template_vars['{feedback_content_txt}'] = strip_tags($content);
            $template_vars['{client_name}']          = Tools::getValue('name');
            $template_vars['{client_email}']         = Tools::getValue('email');


            if (Inix2Mail::FrameSend(
                'feedback',
                '[Feedback] ' . $this->author,
                $template_vars,
                Tools::getValue('email'),
                Tools::getValue('name'),
                null
            )
            ) {
                Inix2Config::put('FRAME_GOT_FEEDBACK', 1);
                $this->context->smarty->assign('conf', true);
            } else {
                $this->errors[] = Tools::displayError('Problem with sending feedback email!');
            }
        }
    }

    /**
     *
     */
    protected function processAddBugreport()
    {

        $file_attachment = null;
        if (Tools::getValue('description') == '') {
            $this->errors[] = Tools::displayError('You should provide some description of your problem');
        }


        if (Tools::getValue('email') == '') {
            $this->errors[] = Tools::displayError('You should provide email for contact!');
        } elseif (!Validate::isEmail(Tools::getValue('email'))) {
            $this->errors[] = Tools::displayError('Provided emails is invalid!');
        }


        if (isset($_FILES['screenshot']['tmp_name']) and !empty($_FILES['screenshot']['tmp_name'])) {
            $max_size = isset($this->max_image_size) ? $this->max_image_size : 0;
            if ($error = ImageManager::validateUpload($_FILES['screenshot'], Tools::getMaxUploadSize($max_size))) {
                $this->errors[] = $error;
            } else {
                $file_attachment = array(
                    'content' => file_get_contents($_FILES['screenshot']['tmp_name']),
                    'name'    => $_FILES['screenshot']['name'],
                    'mime'    => $_FILES['screenshot']['type'],
                );
            }

        }

        if (!count($this->errors)) {
            require_once _PS_MODULE_DIR_ . 'inixframe/inixframe.php';
            $frame                                = new inixframe();
            $template_vars['{inixframe_version}'] = $frame->version;
            $template_vars['{description}']       = Tools::getValue('description');
            if (Inix2Mail::FrameSend(
                'bugreport',
                '[BUG REPORT] ' . $this->displayName . ' v' . $this->version,
                $template_vars,
                Tools::getValue('email'),
                Configuration::get('PS_SHOP_NAME'),
                $file_attachment,
                $this
            )
            ) {
                $this->context->smarty->assign('conf', true);
            } else {
                $this->errors[] = Tools::displayError('Problem with sending bug report email!');
            }

        }

    }


    /**
     *
     */
    public function initPageHeaderToolbar()
    {
        if (empty($this->toolbar_title)) {
            $this->initToolbarTitle();
        }

        if (!is_array($this->toolbar_title)) {
            $this->toolbar_title = array($this->toolbar_title);
        }

        switch ($this->display) {
            case 'view':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex . '&token=' . $this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->page_header_toolbar_btn['back'] = array(
                        'href' => $back,
                        'desc' => $this->l('Back to list')
                    );
                }
                $obj = $this->loadObject(true);
                if (Validate::isLoadedObject($obj) && isset($obj->{$this->object_identifier_name}) && !empty($obj->{$this->object_identifier_name})) {
                    array_pop($this->toolbar_title);
                    $this->toolbar_title[] = is_array($obj->{$this->object_identifier_name}) ? $obj->{$this->object_identifier_name}[$this->context->employee->id_lang] : $obj->{$this->object_identifier_name};
                }
                break;
            case 'edit':
                $obj = $this->loadObject(true);
                if (Validate::isLoadedObject($obj) && isset($obj->{$this->object_identifier_name}) && !empty($obj->{$this->object_identifier_name})) {
                    array_pop($this->toolbar_title);
                    $this->toolbar_title[] = sprintf(
                        $this->l('Edit: %s'),
                        is_array($obj->{$this->object_identifier_name}) ? $obj->{$this->object_identifier_name}[$this->context->employee->id_lang] : $obj->{$this->object_identifier_name}
                    );
                }
                break;
        }

        if (is_array($this->page_header_toolbar_btn)
            && $this->page_header_toolbar_btn instanceof Traversable
            || count($this->toolbar_title)
        ) {
            $this->show_page_header_toolbar = true;
        }

        if (empty($this->page_header_toolbar_title)) {
            $this->page_header_toolbar_title = array_pop($this->toolbar_title);
        }

        $this->context->smarty->assign(
            'help_link',
            'http://help.prestashop.com/' . $this->context->language->iso_code . '/doc/' . Tools::getValue('controller') . '?version=' . _PS_VERSION_ . '&country=' . $this->context->country->iso_code
        );
    }

    /**
     * assign default action in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items
     *
     */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'feedback':
            case 'bugreport':
                $this->toolbar_btn = array(
                    'save' => array(
                        'href' => '#',
                        'desc' => $this->l('Send')
                    )
                );

                return;
                break;
        }
        if ($this->has_configuration) {
            $obj = $this->loadObject(true);
        }
        switch ($this->display) {
            case 'add':
            case 'edit':
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save']          = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );
                $this->toolbar_btn['save-and-stay'] = array(
                    'short' => 'SaveAndStay',
                    'href'  => '#',
                    'desc'  => $this->l('Save and stay'),
                );
                if (Validate::isLoadedObject($obj)) {
                    $this->toolbar_btn['delete'] = array(
                        'short'   => 'Delete',
                        'href'    => self::$currentIndex . '&amp;' . $this->object_identifier . '=' . (int) $obj->id . '&amp;delete' . $this->object_table . '&token=' . $this->token,
                        'desc'    => $this->l('Delete.'),
                        'confirm' => 1,
                        'js'      => 'if (confirm(\'' . $this->l('Delete?') . '\')){return true;}else{event.preventDefault();}'
                    );
                }

                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex . '&token=' . $this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->toolbar_btn['cancel'] = array(
                        'href' => $back,
                        'desc' => $this->l('Cancel')
                    );
                }
                break;
            case 'view':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex . '&token=' . $this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->toolbar_btn['back'] = array(
                        'href' => $back,
                        'desc' => $this->l('Back to list')
                    );
                }
                break;
            case 'options':
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );
                break;

            default:
                // list
                $this->toolbar_btn['new'] = array(
                    'href' => self::$currentIndex . '&amp;add' . $this->object_table . '&amp;token=' . $this->token,
                    'desc' => $this->l('Add new')
                );
                if ($this->allow_export) {
                    $this->toolbar_btn['export'] = array(
                        'href' => self::$currentIndex . '&amp;export' . $this->object_table . '&amp;token=' . $this->token,
                        'desc' => $this->l('Export')
                    );
                }
        }

    }

    /**
     * Load class object using identifier in $_GET (if possible)
     * otherwise return an empty object, or die
     *
     * @param boolean $opt Return an empty object if load fail
     *
     * @return object
     */
    protected function loadObject($opt = false)
    {
        $id = (int) Tools::getValue($this->object_identifier);
        if ($id && Validate::isUnsignedId($id)) {
            if (!$this->object) {
                $this->object = new $this->className($id);
            }
            if (Validate::isLoadedObject($this->object)) {
                return $this->object;
            }
            // throw exception
            $this->errors[] = Tools::displayError('The object cannot be loaded (or found)');

            return false;
        } elseif ($opt) {
            if (!$this->object) {
                $this->object = new $this->className();
            }

            return $this->object;
        } else {
            $this->errors[] = Tools::displayError('The object cannot be loaded (the identifier is missing or invalid)');

            return false;
        }
    }


    /**
     * @param $key
     * @param $filter
     *
     * @return bool
     */
    protected function filterToField($key, $filter)
    {
        foreach ($this->fields_list as $field) {
            if (array_key_exists('filter_key', $field) && $field['filter_key'] == $key) {
                return $field;
            }
        }
        if (array_key_exists($filter, $this->fields_list)) {
            return $this->fields_list[$filter];
        }

        return false;
    }

    /**
     *
     */
    public function displayNoSmarty()
    {
    }

    /**
     *
     */
    public function displayAjax()
    {

        if ($this->json) {
            $this->status = count($this->errors) ? 'error' : 'ok';
            $this->context->smarty->assign(array(
                'framejson' => true,
                'status'    => $this->status,
            ));
        }


        $this->layout = 'layout-ajax.tpl';

        return $this->displayContent();
    }

    /**
     *
     */
    protected function redirect()
    {
        header('Location: ' . $this->redirect_after);
        exit;
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function displayContent()
    {


        // Use page title from meta_title if it has been set else from the breadcrumbs array


        if (!$this->meta_title) {
            $this->meta_title = isset($this->breadcrumbs[1]) ? $this->breadcrumbs[1] : $this->breadcrumbs[0];
        }
        $this->context->smarty->assign('meta_title', $this->meta_title);

        $template_dirs = $this->context->smarty->getTemplateDir();


        $tpl_action = $this->tpl_folder . $this->display . '.tpl';

        // Check if action template has been override

        foreach ($template_dirs as $template_dir) {
            if (file_exists($template_dir . DIRECTORY_SEPARATOR . $tpl_action) && $this->display != 'view' && $this->display != 'options') {
                if (method_exists($this, $this->display . Tools::toCamelCase($this->className))) {
                    $this->{$this->display . Tools::toCamelCase($this->className)}();
                }
                $this->context->smarty->assign('content', $this->context->smarty->fetch($tpl_action));
                break;
            }
        }

        if (!$this->ajax) {
            $template = $this->createTemplate($this->template);
            $page     = $template->fetch();
        } else {
            $page = $this->content;
        }


        $notifications_type = array('errors', 'warnings', 'informations', 'confirmations');
        foreach ($notifications_type as $type) {
            if (!is_array($this->$type)) {
                $this->$type = (array) $this->$type;
            }

            if ($this->json) {
                $this->context->smarty->assign($type, Tools::jsonEncode(array_unique($this->$type)));
            } else {
                $this->context->smarty->assign($type, array_unique($this->$type));
            }
        }


        if ($this->json) {
            $this->context->smarty->assign('page', Tools::jsonEncode($page));
        } else {
            if ($this->show_page_header_toolbar && !$this->lite_display) {
                // Check if header/footer have been overriden
                if (file_exists($this->getLocalPath() . 'views' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $this->override_folder . 'page_header_toolbar.tpl')) {
                    $tpl = ($this->getLocalPath() . 'views' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $this->override_folder . 'page_header_toolbar.tpl');
                } elseif (file_exists($this->getLocalPath() . 'views' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->override_folder . 'page_header_toolbar.tpl')) {
                    $tpl = ($this->getLocalPath() . 'views' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->override_folder . 'page_header_toolbar.tpl');
                } else {
                    $tpl = ($this->getFrameLocalPath() . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'page_header_toolbar.tpl');
                }
                $this->context->smarty->assign(array('page_header_toolbar' => $this->context->smarty->fetch($tpl)));
            }
            $this->context->smarty->assign('page', $page);
        }

        $this->smartyOutputContent($this->layout);
    }


    /**
     * add a warning message to display at the top of the page
     *
     * @param string $msg
     *
     * @return string|void
     */
    public function displayWarning($msg)
    {
        $this->warnings[] = $msg;
    }

    /**
     * add a info message to display at the top of the page
     *
     * @param string $msg
     */
    public function displayInformation($msg)
    {
        $this->informations[] = $msg;
    }


    /**
     * Declare an action to use for each row in the list
     */
    public function addRowAction($action)
    {
        $action          = strtolower($action);
        $this->actions[] = $action;
    }

    /**
     * Add  an action to use for each row in the list
     */
    public function addRowActionSkipList($action, $list)
    {
        $action = strtolower($action);
        $list   = (array) $list;

        if (array_key_exists($action, $this->list_skip_actions)) {
            $this->list_skip_actions[$action] = array_merge($this->list_skip_actions[$action], $list);
        } else {
            $this->list_skip_actions[$action] = $list;
        }
    }

    /**
     * Assign smarty variables for all default views, list and form, then call other init functions
     */
    public function initContent()
    {


        $this->getLanguages();
        // toolbar (save, cancel, new, ..)
        $this->initToolbar();
        $this->initPageHeaderToolbar();


        if ($this->display == 'feedback') {
            $this->context->controller->addJqueryPlugin('fancybox');
            $this->content .= $this->renderFeedback();

        } elseif ($this->display == 'bugreport') {
            $this->context->controller->addJqueryPlugin('fancybox');
            $this->content .= $this->renderBugReport();
        } elseif ($this->display == 'edit' || $this->display == 'add') {
            if ($this->has_configuration) {
                if (!$this->loadObject(true)) {
                    return;
                }

                $this->content .= $this->renderForm();
            }
        } elseif ($this->display == 'view') {
            if ($this->has_configuration) {
                if ($this->className) {
                    $this->loadObject(true);
                }
                $this->content .= $this->renderView();
            }
        } elseif ($this->display == 'details') {
            if ($this->has_configuration) {
                $this->content .= $this->renderDetails();
            }
        } elseif (!$this->ajax) {
            if ($this->has_configuration) {
                $this->content .= $this->renderKpis();
                $this->content .= $this->renderList();
                $this->content .= $this->renderOptions();

                // if we have to display the required fields form
                if ($this->required_database) {
                    $this->content .= $this->displayRequiredFields();
                }
            }
        }

        $this->context->smarty->assign(array(
            'content'                   => $this->content,
            'lite_display'              => $this->lite_display,
            'url_post'                  => self::$currentIndex . '&token=' . $this->token,
            'show_page_header_toolbar'  => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'title'                     => $this->page_header_toolbar_title,
            'toolbar_btn'               => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn'   => $this->page_header_toolbar_btn
        ));


    }


    /**
     * initialize the invalid doom page of death
     *
     * @return void
     */
    public function initCursedPage()
    {
        $this->layout = 'invalid_token.tpl';
    }


    /**
     *
     */
    public function renderBugReport()
    {
        $this->className    = 'BugReport';
        $this->object_table = 'bugreport';

        $this->show_toolbar   = false;
        $this->toolbar_scroll = false;
        $this->fields_form    = array(
            'legend' => array(
                'title' => $this->l('Bug Report')
            ),
            'input'  => array(
                array(
                    'type'  => 'html',
                    'label' => $this->l('Module'),
                    'name'  => 'module_name',
                    'html'  => '<p>' . $this->displayName . '</p>',
                ),
                array(
                    'type'  => 'html',
                    'label' => $this->l('Version'),
                    'name'  => 'module_version',
                    'html'  => '<p>' . $this->version . '</p>',
                ),
                array(
                    'type'     => 'textarea',
                    'name'     => 'description',
                    'label'    => 'Describe your problem',
                    'cols'     => 70,
                    'rows'     => 7,
                    'required' => true,
                ),
                array(
                    'type'  => 'file',
                    'name'  => 'screenshot',
                    'label' => $this->l('Screenshot'),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'email',
                    'label'    => $this->l('Email'),
                    'size'     => 35,
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Send'),
                'name'  => 'sendbugreport'
            )
        );

        $this->fields_value['module_name']    = $this->displayName;
        $this->fields_value['module_version'] = $this->version;
        $this->fields_value['email']          = Configuration::get('PS_SHOP_EMAIL');


        $this->layout = 'layout-bugreport.tpl';

        return self::renderForm();
    }

    /**
     *
     */
    public function renderFeedback()
    {
        $this->className    = 'Feedback';
        $this->object_table = 'feedback';


        foreach ($this->feedback_options as $field => $v) {
            foreach ($v as $id => $ans) {
                $this->{"opt_feedback_$field"}[] = array('id' => $id, 'name' => $ans);
            }
        }

        $this->show_toolbar   = false;
        $this->toolbar_scroll = false;
        $this->fields_form    = array(
            'legend'      => array(
                'title' => $this->l('Feedback')
            ),
            'description' => ('Thank you for taking the time to fill out this form, all information will be solely used to make our distributions better.') . '<br />' .
                             ('Please feel free to tell us your ideas and suggestions, but also criticism or complaints using the form below.
				            We are constantly working to improve our services, and your feedback can help us detect problem areas we may have overlooked.') . '<br />' .
                             ('As "Thanks You" we are going to provide you with a voucher with <span style="color: red; font-weight:bold"> 30% </span> discount for your next purchase by us!'),
            'input'       => array(
                array(
                    'type'   => 'checkbox',
                    'label'  => $this->feedback_labels['most'],
                    'values' => array(
                        'query' => $this->opt_feedback_most,
                        'id'    => 'id',
                        'name'  => 'name'
                    ),
                    'name'   => 'most'
                ),
                array(
                    'type'  => 'textarea',
                    'label' => $this->feedback_labels['additional'],
                    'name'  => 'additional',
                    'cols'  => 80,
                    'rows'  => 6
                ),
                array(
                    'type'   => 'radio',
                    'label'  => $this->feedback_labels['other'],
                    'name'   => 'other',
                    'class'  => 't',
                    'values' => array(
                        array(
                            'id'    => 'other_on',
                            'value' => 1,
                            'label' => $this->feedback_labels['yes'],
                        ),
                        array(
                            'id'    => 'other_off',
                            'value' => 0,
                            'label' => $this->feedback_labels['no']
                        )
                    )
                ),
                array(
                    'type'  => 'textarea',
                    'label' => $this->feedback_labels['other_yes'],
                    'name'  => 'other_yes',
                    'cols'  => 80,
                    'rows'  => 6
                ),
                array(
                    'type'   => 'checkbox',
                    'label'  => $this->feedback_labels['company'],
                    'values' => array(
                        'query' => $this->opt_feedback_company,
                        'id'    => 'id',
                        'name'  => 'name'
                    ),
                    'name'   => 'company'
                ),
                array(
                    'type'   => 'checkbox',
                    'label'  => $this->feedback_labels['better'],
                    'values' => array(
                        'query' => $this->opt_feedback_better,
                        'id'    => 'id',
                        'name'  => 'name'
                    ),
                    'name'   => 'better'
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->feedback_labels['name'],
                    'name'     => 'name',
                    'size'     => 45,
                    'required' => true,
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->feedback_labels['email'],
                    'name'     => 'email',
                    'size'     => 45,
                    'required' => true,
                ),
            ),
            'submit'      => array(
                'title' => $this->l('Send'),
                'name'  => 'sendfeedback'
            )
        );

        $this->layout = 'layout-feedback.tpl';

        return self::renderForm();

    }

    /**
     * Function used to render the list to display for this controller
     */
    public function renderList()
    {
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);
        // If list has 'active' field, we automatically create bulk action
        if (isset($this->fields_list) && is_array($this->fields_list) && array_key_exists('active', $this->fields_list)
            && !empty($this->fields_list['active'])
        ) {
            if (!is_array($this->bulk_actions)) {
                $this->bulk_actions = array();
            }

            $this->bulk_actions = array_merge(array(
                'enableSelection'  => array(
                    'text' => $this->l('Enable selection'),
                    'icon' => 'icon-power-off text-success'
                ),
                'disableSelection' => array(
                    'text' => $this->l('Disable selection'),
                    'icon' => 'icon-power-off text-danger'
                ),
                'divider'          => array(
                    'text' => 'divider'
                )
            ), $this->bulk_actions);
        }
        $helper = new Inix2HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->l(
                'Bad SQL query',
                'Helper'
            ) . '<br />' . htmlspecialchars($this->_list_error));

            return false;
        }
        $helper->filter = $this->filter;
        $this->setHelperDisplay($helper);
        $helper->tpl_vars             = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper->is_cms = $this->is_cms;


        $list = $helper->generateList($this->_list, $this->fields_list);

        return $list;
    }

    /**
     * Override to render the view page
     */
    public function renderView()
    {
        $helper = new Inix2HelperView($this);
        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_view_vars;
        if (!is_null($this->base_tpl_view)) {
            $helper->base_tpl = $this->base_tpl_view;
        }
        $view = $helper->generateView();

        return $view;
    }

    /**
     * Override to render the view page
     */
    public function renderDetails()
    {
        return $this->renderList();
    }

    /**
     * Function used to render the form for this controller
     */
    public function renderForm()
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }

        if (Tools::getValue('submitFormAjax')) {
            $this->content .= $this->context->smarty->fetch('form_submit_ajax.tpl');
        }
        if ($this->fields_form && is_array($this->fields_form)) {
            if (!$this->multiple_fieldsets) {
                $this->fields_form = array(array('form' => $this->fields_form));
            }

            // For add a fields via an override of $fields_form, use $fields_form_override
            if (is_array($this->fields_form_override) && !empty($this->fields_form_override)) {
                $this->fields_form[0]['form']['input'] = array_merge(
                    $this->fields_form[0]['form']['input'],
                    $this->fields_form_override
                );
            }
            
            $helper = new Inix2HelperForm($this);
            $this->setHelperDisplay($helper);
            $helper->fields_value       = $this->getFieldsValue($this->object);
            $helper->submit_action      = $this->submit_action;
            $helper->show_cancel_button = (isset($this->show_form_cancel_button)) ? $this->show_form_cancel_button : ($this->display == 'add' || $this->display == 'edit');
            $back                       = Tools::safeOutput(Tools::getValue('back', ''));
            if (empty($back)) {
                $back = self::$currentIndex . '&token=' . $this->token;
            }
            if (!Validate::isCleanHtml($back)) {
                die(Tools::displayError());
            }

            $helper->back_url = $back;
            $helper->tpl_vars = $this->tpl_form_vars;
            !is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';

            if (Tools::getValue('back')) {
                $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue('back'));
            } else {
                $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue(self::$currentIndex . '&token=' . $this->token));
            }
            $form = $helper->generateForm($this->fields_form);

            return $form;
        }
    }


    /**
     *
     */
    public function renderKpis()
    {
    }

    /**
     * Function used to render the options for this controller
     */
    public function renderOptions()
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }
        if ($this->fields_options && is_array($this->fields_options)) {
            if (isset($this->display) && $this->display != 'options' && $this->display != 'list') {
                $this->show_toolbar = false;
            } else {
                $this->display = 'options';
            }

            unset($this->toolbar_btn);
            $this->initToolbar();
            $helper = new Inix2HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->id       = (int) Tab::getIdFromClassName('AdminModules');
            $helper->tpl_vars = $this->tpl_option_vars;
            $options          = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    /**
     * this function set various display option for helper list
     *
     * @param Helper $helper
     *
     * @return void
     */
    public function setHelperDisplay(Inix2Helper $helper)
    {
        if (empty($this->toolbar_title)) {
            $this->initToolbarTitle();
        }
        // tocheck
        if ($this->object && $this->object->id) {
            $helper->id = $this->object->id;
        }

        // @todo : move that in Helper
        $helper->title                    = is_array($this->toolbar_title) ? implode(
            ' ' . Configuration::get('PS_NAVIGATION_PIPE') . ' ',
            $this->toolbar_title
        ) : $this->toolbar_title;
        $helper->toolbar_btn              = $this->toolbar_btn;
        $helper->show_toolbar             = $this->show_toolbar;
        $helper->toolbar_scroll           = $this->toolbar_scroll;
        $helper->override_folder          = $this->tpl_folder;
        $helper->actions                  = $this->actions;
        $helper->simple_header            = $this->list_simple_header;
        $helper->bulk_actions             = $this->bulk_actions;
        $helper->currentIndex             = self::$currentIndex;
        $helper->className                = $this->className;
        $helper->table                    = $this->object_table;
        $helper->name_controller          = Tools::getValue('controller');
        $helper->orderBy                  = $this->_orderBy;
        $helper->orderWay                 = $this->_orderWay;
        $helper->listTotal                = $this->_listTotal;
        $helper->shopLink                 = $this->shopLink;
        $helper->shopLinkType             = $this->shopLinkType;
        $helper->identifier               = $this->object_identifier;
        $helper->token                    = $this->token;
        $helper->languages                = $this->_languages;
        $helper->specificConfirmDelete    = $this->specificConfirmDelete;
        $helper->imageType                = $this->imageType;
        $helper->no_link                  = $this->list_no_link;
        $helper->colorOnBackground        = $this->colorOnBackground;
        $helper->ajax_params              = (isset($this->ajax_params) ? $this->ajax_params : null);
        $helper->default_form_language    = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->multiple_fieldsets       = $this->multiple_fieldsets;
        $helper->row_hover                = $this->row_hover;
        $helper->position_identifier      = $this->position_identifier;
        $helper->controller_name          = $this->controller_name;
        $helper->module                   = $this;
        $helper->dependency               = $this->form_dependency;
        // For each action, try to add the corresponding skip elements list
        $helper->list_skip_actions         = $this->list_skip_actions;
        $helper->tabs                      = $this->tabs_options;
        $this->helper                      = $helper;
        $helper->bootstrap                 = $this->bootstrap;
        $helper->position_group_identifier = $this->position_group_identifier;
    }


    /**
     * Init context and dependencies, handles POST and GET
     */
    public function init()
    {
        // Has to be removed for the next Prestashop version
        global $currentIndex;

        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', Tools::getShopDomain(true));
        }
        if (!defined('_PS_BASE_URL_SSL_')) {
            define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
        }

        if (Tools::getValue('ajax')) {
            $this->ajax = '1';
        }


        $this->cookie_prefix = $this->name;
        /* Server Params */
        $protocol_link    = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
        $protocol_content = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

        if (!$this->context->link) {
            $this->context->link = new Link($protocol_link, $protocol_content);
        }


        if (!$this->object_identifier) {
            $this->object_identifier = 'id_' . $this->object_table;
        }
        if (!$this->_defaultOrderBy) {
            $this->_defaultOrderBy = $this->object_identifier;
        }


        // Set current index
        $current_index = 'index.php' . (($controller = Tools::getValue('controller')) ? '?controller=' . $controller : '');
        $current_index .= (Tools::isSubmit('configure') ? '&configure=' . Tools::getValue('configure') : '');
        $current_index .= (Tools::isSubmit('tab_module') ? '&tab_module=' . Tools::getValue('tab_module') : '');
        $current_index .= (Tools::isSubmit('module_name') ? '&module_name=' . Tools::getValue('module_name') : '');
        if (Tools::isSubmit('iniframe')) {
            $current_index .= '&iniframe';
        }


        if ($back = Tools::getValue('back')) {
            $current_index .= '&back=' . urlencode($back);
        }
        self::$currentIndex = $current_index;
        $currentIndex       = $current_index;

        if ((int) Tools::getValue('liteDisplaying')) {
            $this->content_only = false;
            $this->lite_display = true;
        }

        if ($this->ajax && method_exists($this, 'ajaxPreprocess')) {
            $this->ajaxPreProcess();
        }

        if (!$this->ajax) {
            $this->assignUpdate();

        }


        $this->context->smarty->assign(array(
            'table'              => $this->object_table,
            'current'            => self::$currentIndex,
            'token'              => $this->token,
            'host_mode'          => defined('_PS_HOST_MODE_') ? 1 : 0,
            'stock_management'   => (int) Configuration::get('PS_STOCK_MANAGEMENT'),
            'module_displayName' => $this->displayName,
            'module_name'        => $this->name,
            'module_version'     => $this->version,
            'module_local_path'  => $this->getLocalPath(),
            'module_path_uri'    => $this->getPathUri(),
            'frame_local_path'   => $this->getFrameLocalPath(),
            'frame_path_uri'     => $this->getFramePathUri(),
            'author'             => $this->author,
            'author_email'       => $this->author_email,
            'author_domain'      => $this->author_domain,
            'slogan'             => $this->slogan,
            'link'               => $this->context->link,
            'got_feedback'       => Inix2Config::get('FRAME_GOT_FEEDBACK'),
            'show_wellcome'      => $this->show_wellcome and !Inix2Config::get('FRAME_W_' . strtoupper($this->name)),
            'feedback_link'      => $this->context->link->getAdminLink('AdminModules') . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $this->name . '&iniframe=1&feedback',
            'bugreport_link'     => $this->context->link->getAdminLink('AdminModules') . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $this->name . '&iniframe=1&bugreport',
            'show_register'      => ($this->name != 'inixframe' and Inix2Config::get('IWFRAME_CLIENT_TOKEN') == false and Inix2Config::get('IWFRAME_REG_' . strtoupper($this->name)) == false),
            'show_conscent'      => ($this->name != 'inixframe' and !Module::isInstalled('inixframe') and Inix2Config::get('IWFRAME_CLIENT_TOKEN') == false and Inix2Config::get('IWFRAME_CONSCENT_' . strtoupper($this->name)) == false)
        ));

        $this->context->smarty->assign(
            array(
                'submit_form_ajax' => (int) Tools::getValue('submitFormAjax')
            )
        );


        $this->initProcess();
        $this->initBreadcrumbs();
    }


    /**
     * @throws PrestaShopException
     */
    public function initShopContext()
    {
        if (!$this->context->employee->isLoggedBack()) {
            return;
        }

        // Change shop context ?
        if (Shop::isFeatureActive() && Tools::getValue('setShopContext') !== false) {
            $this->context->cookie->shopContext = Tools::getValue('setShopContext');
            $url                                = parse_url($_SERVER['REQUEST_URI']);
            $query                              = (isset($url['query'])) ? $url['query'] : '';
            parse_str($query, $parse_query);
            unset($parse_query['setShopContext'], $parse_query['conf']);
            $this->redirect_after = $url['path'] . '?' . http_build_query($parse_query, '', '&');
        } elseif (!Shop::isFeatureActive()) {
            $this->context->cookie->shopContext = 's-' . Configuration::get('PS_SHOP_DEFAULT');
        } elseif (Shop::getTotalShops(false, null) < 2) {
            $this->context->cookie->shopContext = 's-' . $this->context->employee->getDefaultShopID();
        }

        $shop_id = '';
        Shop::setContext(Shop::CONTEXT_ALL);
        if ($this->context->cookie->shopContext) {
            $split = explode('-', $this->context->cookie->shopContext);
            if (count($split) == 2) {
                if ($split[0] == 'g') {
                    if ($this->context->employee->hasAuthOnShopGroup($split[1])) {
                        Shop::setContext(Shop::CONTEXT_GROUP, $split[1]);
                    } else {
                        $shop_id = $this->context->employee->getDefaultShopID();
                        Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                    }
                } elseif (Shop::getShop($split[1]) && $this->context->employee->hasAuthOnShop($split[1])) {
                    $shop_id = $split[1];
                    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                } else {
                    $shop_id = $this->context->employee->getDefaultShopID();
                    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                }
            }
        }

        // Check multishop context and set right context if need
        if (!($this->multishop_context & Shop::getContext())) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP && !($this->multishop_context & Shop::CONTEXT_SHOP)) {
                Shop::setContext(Shop::CONTEXT_GROUP, Shop::getContextShopGroupID());
            }
            if (Shop::getContext() == Shop::CONTEXT_GROUP && !($this->multishop_context & Shop::CONTEXT_GROUP)) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }
        }

        // Replace existing shop if necessary
        if (!$shop_id) {
            $this->context->shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
        } elseif ($this->context->shop->id != $shop_id) {
            $this->context->shop = new Shop($shop_id);
        }


    }

    /**
     * Retrieve GET and POST value and translate them to actions
     */
    public function initProcess()
    {
        // Manage list filtering
        if (Tools::isSubmit('submitFilter' . $this->object_table)
            || $this->context->cookie->{'submitFilter' . $this->object_table} !== false
            || Tools::getValue($this->object_table . 'Orderby')
            || Tools::getValue($this->object_table . 'Orderway')
        ) {
            $this->filter = true;
        }


        $this->id_object = (int) Tools::getValue($this->object_identifier);

        /* Delete object image */
        if (isset($_GET['deleteImage'])) {
            $this->action = 'delete_image';

        } /* Delete object */
        elseif (isset($_GET['delete' . $this->object_table])) {
            $this->action = 'delete';

        } /* Change object statuts (active, inactive) */
        elseif ((isset($_GET['status' . $this->object_table]) || isset($_GET['status'])) && Tools::getValue($this->object_identifier)) {
            $this->action = 'status';

        } /* Move an object */
        elseif (isset($_GET['fposition'])) {
            $this->action = 'position';

        } elseif (Tools::isSubmit('submitAdd' . $this->object_table)
                  || Tools::isSubmit('submitAdd' . $this->object_table . 'AndStay')
                  || Tools::isSubmit('submitAdd' . $this->object_table . 'AndPreview')
                  || Tools::isSubmit('submitAdd' . $this->object_table . 'AndBackToParent')
        ) {
            // case 1: updating existing entry
            if ($this->id_object) {
                $this->action = 'save';
                if (Tools::isSubmit('submitAdd' . $this->object_table . 'AndStay')) {
                    $this->display = 'edit';
                } else {
                    $this->display = 'list';
                }

            } // case 2: creating new entry
            else {
                $this->action = 'save';
                if (Tools::isSubmit('submitAdd' . $this->object_table . 'AndStay')) {
                    $this->display = 'edit';
                } else {
                    $this->display = 'list';
                }

            }
        } elseif (isset($_GET['add' . $this->object_table])) {
            $this->action  = 'new';
            $this->display = 'add';

        } elseif (isset($_GET['update' . $this->object_table]) && isset($_GET[$this->object_identifier])) {
            $this->display = 'edit';

        } elseif (isset($_GET['view' . $this->object_table])) {
            $this->display = 'view';
            $this->action  = 'view';

        } elseif (isset($_GET['details' . $this->object_table])) {
            if ($this->tabAccess['view'] === '1') {
                $this->display = 'details';
                $this->action  = 'details';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to view this.');
            }
        } elseif (isset($_GET['export' . $this->object_table])) {
            $this->action = 'export';
        } /* Cancel all filters for this tab */
        elseif (isset($_POST['submitReset' . $this->object_table])) {
            $this->action = 'reset_filters';
        } /* Submit options list */
        elseif (Tools::isSubmit('submitInixOptions' . $this->object_table) || Tools::isSubmit('submitInixOptions')) {
            $this->display = 'options';

            $this->action = 'update_options';

        } elseif (Tools::getValue('action') && method_exists(
            $this,
            'process' . ucfirst(Tools::toCamelCase(Tools::getValue('action')))
        )
        ) {
            $this->action = Tools::getValue('action');
        } elseif (Tools::isSubmit('submitFields') && $this->required_database) {
            $this->action = 'update_fields';
        } elseif (is_array($this->bulk_actions)) {
            $submit_bulk_actions = array_merge(array(
                'enableSelection'  => array(
                    'text' => $this->l('Enable selection'),
                    'icon' => 'icon-power-off text-success'
                ),
                'disableSelection' => array(
                    'text' => $this->l('Disable selection'),
                    'icon' => 'icon-power-off text-danger'
                )
            ), $this->bulk_actions);
            foreach ($submit_bulk_actions as $bulk_action => $params) {
                if (Tools::isSubmit('submitBulk' . $bulk_action . $this->object_table) || Tools::isSubmit('submitBulk' . $bulk_action)) {
                    $this->action = 'bulk' . $bulk_action;
                    $this->boxes  = Tools::getValue($this->object_table . 'Box');

                    break;
                } elseif (Tools::isSubmit('submitBulk')) {
                    $this->action = 'bulk' . Tools::getValue('select_submitBulk');
                    $this->boxes  = Tools::getValue($this->object_table . 'Box');

                    break;
                }
            }
        } elseif (!empty($this->fields_options) && empty($this->fields_list)) {
            $this->display = 'options';
        }


        if (Tools::isSubmit('feedback')) {
            $this->display = 'feedback';
        }
        if (Tools::isSubmit('submitAddfeedback')) {
            $this->display = 'feedback';
            $this->action  = 'addFeedback';
        }
        if (Tools::isSubmit('bugreport')) {
            $this->display = 'bugreport';
        }
        if (Tools::isSubmit('submitAddbugreport')) {
            $this->display = 'bugreport';
            $this->action  = 'addbugreport';
        }
    }

    /**
     * Get the current objects' list form the database
     *
     * @param integer $id_lang   Language used for display
     * @param string  $order_by  ORDER BY clause
     * @param string  $_orderWay Order way (ASC, DESC)
     * @param integer $start     Offset in LIMIT clause
     * @param integer $limit     Row count in LIMIT clause
     */
    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        $prefix = $this->name;
        /* Manage default params values */
        $use_limit = true;
        if ($limit === false) {
            $use_limit = false;
        } elseif (empty($limit)) {
            if (isset($this->context->cookie->{$this->cookie_prefix . $this->object_table . '_pagination'}) && $this->context->cookie->{$this->cookie_prefix . $this->object_table . '_pagination'}) {
                $limit = $this->context->cookie->{$this->cookie_prefix . $this->object_table . '_pagination'};
            } else {
                $limit = $this->_default_pagination;
            }
        }

        if (!Validate::isTableOrIdentifier($this->object_table)) {
            throw new PrestaShopException(sprintf('Table name %s is invalid:', $this->object_table));
        }

        if (empty($order_by)) {
            if ($this->context->cookie->{$this->cookie_prefix . $this->object_table . 'Orderby'}) {
                $order_by = $this->context->cookie->{$this->cookie_prefix . $this->object_table . 'Orderby'};
            } elseif ($this->_orderBy) {
                $order_by = $this->_orderBy;
            } else {
                $order_by = $this->_defaultOrderBy;
            }
        }

        if (empty($order_way)) {
            if ($this->context->cookie->{$this->cookie_prefix . $this->object_table . 'Orderway'}) {
                $order_way = $this->context->cookie->{$this->cookie_prefix . $this->object_table . 'Orderway'};
            } elseif ($this->_orderWay) {
                $order_way = $this->_orderWay;
            } else {
                $order_way = $this->_defaultOrderWay;
            }
        }

        $limit = (int) Tools::getValue($this->object_table . '_pagination', $limit);
        if (in_array($limit, $this->_pagination) && $limit != $this->_default_pagination) {
            $this->context->cookie->{$this->cookie_prefix . $this->object_table . '_pagination'} = $limit;
        } else {
            unset($this->context->cookie->{$this->cookie_prefix . $this->object_table . '_pagination'});
        }

        /* Check params validity */
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)
            || !is_numeric($start) || !is_numeric($limit)
            || !Validate::isUnsignedId($id_lang)
        ) {
            throw new PrestaShopException('get list params is not valid');
        }

        if (isset($this->fields_list[$order_by]) && isset($this->fields_list[$order_by]['filter_key'])) {
            $order_by = $this->fields_list[$order_by]['filter_key'];
        }

        /* Determine offset from current page */
        $start = 0;
        if ((int) Tools::getValue('submitFilter' . $this->object_table)) {
            $start = ((int) Tools::getValue('submitFilter' . $this->object_table) - 1) * $limit;
        } elseif (empty($start) && isset($this->context->cookie->{$this->cookie_prefix . $this->object_table . '_start'}) && Tools::isSubmit('export' . $this->object_table)) {
            $start = $this->context->cookie->{$this->cookie_prefix . $this->object_table . '_start'};
        }

        // Either save or reset the offset in the cookie
        if ($start) {
            $this->context->cookie->{$this->cookie_prefix . $this->object_table . '_start'} = $start;
        } elseif (isset($this->context->cookie->{$this->cookie_prefix . $this->object_table . '_start'})) {
            unset($this->context->cookie->{$this->cookie_prefix . $this->object_table . '_start'});
        }

        /* Cache */
        $this->_lang = (int) $id_lang;

        if (preg_match('/[.!]/', $order_by)) {
            $order_by_split = preg_split('/[.!]/', $order_by);
            $order_by       = pSQL($order_by_split[0]) . '.`' . pSQL($order_by_split[1]) . '`';
            $this->_orderBy = (isset($order_by_split) && isset($order_by_split[1])) ? $order_by_split[1] : $order_by;
        } else {
            $this->_orderBy = $order_by;
        }
        $this->_orderWay = Tools::strtoupper($order_way);

        /* SQL table : orders, but class name is Order */
        $sql_table = $this->object_table == 'order' ? 'orders' : $this->object_table;

        // Add SQL shop restriction
        $select_shop = $join_shop = $where_shop = '';
        if ($this->shopLinkType) {
            $select_shop = ', shop.name as shop_name ';
            $join_shop   = ' LEFT JOIN ' . _DB_PREFIX_ . $this->shopLinkType . ' shop
							ON a.id_' . $this->shopLinkType . ' = shop.id_' . $this->shopLinkType;
            $where_shop  = Shop::addSqlRestriction($this->shopShareDatas, 'a', $this->shopLinkType);
        }

        if ($this->multishop_context && Shop::isTableAssociated($this->object_table) && !empty($this->className)) {
            if (Shop::getContext() != Shop::CONTEXT_ALL || !$this->context->employee->isSuperAdmin()) {
                $test_join = !preg_match(
                    '#`?' . preg_quote(_DB_PREFIX_ . $this->object_table . '_shop') . '`? *sa#',
                    $this->_join
                );
                if (Shop::isFeatureActive() && $test_join && Shop::isTableAssociated($this->object_table)) {
                    $this->_where .= ' AND a.' . $this->object_identifier . ' IN (
						SELECT sa.' . $this->object_identifier . '
						FROM `' . _DB_PREFIX_ . $this->object_table . '_shop` sa
						WHERE sa.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')
					)';
                }
            }
        }

        /* Query in order to get results with all fields */
        $lang_join = '';
        if ($this->lang) {
            $lang_join = 'LEFT JOIN `' . _DB_PREFIX_ . $this->object_table . '_lang` b ON (b.`' . $this->object_identifier . '` = a.`' . $this->object_identifier . '` AND b.`id_lang` = ' . (int) $id_lang;
            if ($id_lang_shop) {
                if (!Shop::isFeatureActive()) {
                    $lang_join .= ' AND b.`id_shop` = 1';
                } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $lang_join .= ' AND b.`id_shop` = ' . (int) $id_lang_shop;
                } else {
                    $lang_join .= ' AND b.`id_shop` = a.id_shop_default';
                }
            }
            $lang_join .= ')';
        }

        $having_clause = '';
        if (isset($this->_filterHaving) || isset($this->_having)) {
            $having_clause = ' HAVING ';
            if (isset($this->_filterHaving)) {
                $having_clause .= ltrim($this->_filterHaving, ' AND ');
            }
            if (isset($this->_having)) {
                $having_clause .= $this->_having . ' ';
            }
        }


        $this->_listsql = '
		SELECT SQL_CALC_FOUND_ROWS
		' . ($this->_tmpTableFilter ? ' * FROM (SELECT ' : '');

        if ($this->explicitSelect) {
            foreach ($this->fields_list as $key => $array_value) {
                // Add it only if it is not already in $this->_select
                if (isset($this->_select) && preg_match(
                    '/[\s]`?' . preg_quote($key, '/') . '`?\s*,/',
                    $this->_select
                )
                ) {
                    continue;
                }

                if (isset($array_value['filter_key'])) {
                    $this->_listsql .= str_replace('!', '.', $array_value['filter_key']) . ' as ' . $key . ',';
                } elseif ($key == 'id_' . $this->object_table) {
                    $this->_listsql .= 'a.`' . bqSQL($key) . '`,';
                } elseif ($key != 'image' && !preg_match('/' . preg_quote($key, '/') . '/i', $this->_select)) {
                    $this->_listsql .= '`' . bqSQL($key) . '`,';
                }
            }
            $this->_listsql = rtrim($this->_listsql, ',');
        } else {
            $this->_listsql .= ($this->lang ? 'b.*,' : '') . ' a.*';
        }

        $this->_listsql .= '
		' . (isset($this->_select) ? ', ' . $this->_select : '') . $select_shop . '
		FROM `' . _DB_PREFIX_ . $sql_table . '` a
		' . $lang_join . '
		' . (isset($this->_join) ? $this->_join . ' ' : '') . '
		' . $join_shop . '
		WHERE 1 ' . (isset($this->_where) ? $this->_where . ' ' : '') . ($this->deleted ? 'AND a.`deleted` = 0 ' : '') .
                           (isset($this->_filter) ? $this->_filter : '') . $where_shop . '
		' . (isset($this->_group) ? $this->_group . ' ' : '') . '
		' . $having_clause . '
		ORDER BY ' . (($order_by == $this->object_identifier) ? 'a.' : '') . pSQL($order_by) . ' ' . pSQL($order_way) .
                           ($this->_tmpTableFilter ? ') tmpTable WHERE 1' . $this->_tmpTableFilter : '') .
                           (($use_limit === true) ? ' LIMIT ' . (int) $start . ',' . (int) $limit : '');


        if (!($this->_list = Db::getInstance()->executeS($this->_listsql))) {
            $this->_list_error = Db::getInstance()->getMsgError();
        } else {
            $this->_listTotal = Db::getInstance()
                                  ->getValue('SELECT FOUND_ROWS() AS `' . _DB_PREFIX_ . $this->object_table . '`');
        }
    }


    /**
     * @return array
     */
    public function getLanguages()
    {
        $cookie                         = $this->context->cookie;
        $this->allow_employee_form_lang = 0;//Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        if ($this->allow_employee_form_lang && !$cookie->employee_form_lang) {
            $cookie->employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }
        $use_lang_from_cookie = false;
        $this->_languages     = Language::getLanguages(false);
        if ($this->allow_employee_form_lang) {
            foreach ($this->_languages as $lang) {
                if ($cookie->employee_form_lang == $lang['id_lang']) {
                    $use_lang_from_cookie = true;
                }
            }
        }
        if (!$use_lang_from_cookie) {
            $this->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        } else {
            $this->default_form_language = (int) $cookie->employee_form_lang;
        }

        foreach ($this->_languages as $k => $language) {
            $this->_languages[$k]['is_default'] = (int) ($language['id_lang'] == $this->default_form_language);
        }

        return $this->_languages;
    }


    /**
     * Return the list of fields value
     *
     * @param object $obj Object
     *
     * @return array
     */
    public function getFieldsValue($obj)
    {
        foreach ($this->fields_form as $fieldset) {
            if (isset($fieldset['form']['input'])) {
                foreach ($fieldset['form']['input'] as $input) {
                    if (!isset($this->fields_value[$input['name']])) {
                        if (isset($input['type']) && $input['type'] == 'shop') {
                            if ($obj->id) {
                                $result = Shop::getShopById(
                                    (int) $obj->id,
                                    $this->object_identifier,
                                    $this->object_table
                                );
                                foreach ($result as $row) {
                                    $this->fields_value['shop'][$row['id_' . $input['type']]][] = $row['id_shop'];
                                }
                            }
                        } elseif (isset($input['lang']) && $input['lang']) {
                            foreach ($this->_languages as $language) {
                                $fieldValue = $this->getFieldValue($obj, $input['name'], $language['id_lang']);
                                if (empty($fieldValue)) {
                                    if (isset($input['default_value']) && is_array($input['default_value']) && isset($input['default_value'][$language['id_lang']])) {
                                        $fieldValue = $input['default_value'][$language['id_lang']];
                                    } elseif (isset($input['default_value'])) {
                                        $fieldValue = $input['default_value'];
                                    }
                                }
                                $this->fields_value[$input['name']][$language['id_lang']] = $fieldValue;
                            }
                        } else {
                            $fieldValue = $this->getFieldValue($obj, $input['name']);
                            if ($fieldValue === false && isset($input['default_value'])) {
                                $fieldValue = $input['default_value'];
                            }
                            $this->fields_value[$input['name']] = $fieldValue;
                        }
                    }
                }
            }
        }

        return $this->fields_value;
    }

    /**
     * Return field value if possible (both classical and multilingual fields)
     *
     * Case 1 : Return value if present in $_POST / $_GET
     * Case 2 : Return object value
     *
     * @param object  $obj     Object
     * @param string  $key     Field name
     * @param integer $id_lang Language id (optional)
     *
     * @return string
     */
    public function getFieldValue($obj, $key, $id_lang = null)
    {


        if ($id_lang) {
            $default_value = ($obj->id && isset($obj->{$key}[$id_lang])) ? $obj->{$key}[$id_lang] : false;
        } else {
            $default_value = isset($obj->{$key}) ? $obj->{$key} : false;
        }

        return Tools::getValue($key . ($id_lang ? '_' . $id_lang : ''), $default_value);
    }

    /**
     * Manage page display (form, list...)
     *
     * @param string $className Allow to validate a different class than the current one
     */
    public function validateRules($class_name = false)
    {
        if (!$class_name) {
            $class_name = $this->className;
        }

        $object = new $class_name();

        if (method_exists($this, 'getValidationRules')) {
            $definition = $this->getValidationRules();
        } else {
            $definition = ObjectModel::getDefinition($class_name);
        }

        $default_language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        foreach ($definition['fields'] as $field => $def) {
            $skip = array();
            if (in_array($field, array('passwd', 'no-picture'))) {
                $skip = array('required');
            }

            if (isset($def['lang']) && $def['lang']) {
                if (isset($def['required']) && $def['required']) {
                    $value = Tools::getValue($field . '_' . $default_language->id);
                    if (empty($value)) {
                        $this->errors[$field . '_' . $default_language->id] = sprintf(
                            Tools::displayError('The field %1$s is required at least in %2$s.'),
                            $object->displayFieldName($field, $class_name),
                            $default_language->name
                        );
                    }
                }

                foreach (Language::getLanguages(false) as $language) {
                    $value = Tools::getValue($field . '_' . $language['id_lang']);
                    if (!empty($value)) {
                        if (($error = $object->validateField(
                            $field,
                            $value,
                            $language['id_lang'],
                            $skip,
                            true
                        )) !== true
                        ) {
                            $this->errors[$field . '_' . $language['id_lang']] = $error;
                        }
                    }
                }
            } elseif (($error = $object->validateField($field, Tools::getValue($field), null, $skip, true)) !== true) {
                $this->errors[$field] = $error;
            }
        }


        /* Overload this method for custom checking */
        $this->_childValidation();

        /* Checking for multilingual fields validity */
        if (isset($rules['validateLang']) && is_array($rules['validateLang'])) {
            foreach ($rules['validateLang'] as $field_lang => $function) {
                foreach (Language::getLanguages(false) as $language) {
                    if (($value = Tools::getValue($field_lang . '_' . $language['id_lang'])) !== false && !empty($value)) {
                        if (Tools::strtolower($function) == 'iscleanhtml' && Configuration::get('PS_ALLOW_HTML_IFRAME')) {
                            $res = Validate::$function($value, true);
                        } else {
                            $res = Validate::$function($value);
                        }
                        if (!$res) {
                            $this->errors[$field_lang . '_' . $language['id_lang']] = sprintf(
                                Tools::displayError('The %1$s field (%2$s) is invalid.'),
                                call_user_func(array($class_name, 'displayFieldName'), $field_lang, $class_name),
                                $language['name']
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Overload this method for custom checking
     */
    protected function _childValidation()
    {
    }

    /**
     * Display object details
     */
    public function viewDetails()
    {
    }

    /**
     * Called before deletion
     *
     * @param object $object Object
     *
     * @return boolean
     */
    protected function beforeDelete($object)
    {
        return false;
    }


    /**
     * Called before deletion
     *
     * @param object $object Object
     *
     * @return boolean
     */
    protected function afterDelete($object, $oldId)
    {
        return true;
    }

    /**
     * @param $object
     *
     * @return bool
     */
    protected function afterAdd($object)
    {
        return true;
    }

    /**
     * @param $object
     *
     * @return bool
     */
    protected function afterUpdate($object)
    {
        return true;
    }

    /**
     * Check rights to view the current tab
     *
     * @return boolean
     */

    protected function afterImageUpload()
    {
        return true;
    }

    /**
     * Copy datas from $_POST to object
     *
     * @param object &$object Object
     * @param string $table   Object table
     */
    protected function copyFromPost(&$object, $table)
    {
        /* Classical fields */
        foreach ($_POST as $key => $value) {
            if (array_key_exists($key, $object) && $key != 'id_' . $table) {
                /* Do not take care of password field if empty */
                if ($key == 'passwd' && Tools::getValue('id_' . $table) && empty($value)) {
                    continue;
                }
                /* Automatically encrypt password in MD5 */
                if ($key == 'passwd' && !empty($value)) {
                    $value = Tools::encrypt($value);
                }
                $object->{$key} = $value;
            }
        }

        /* Multilingual fields */
        $rules = call_user_func(array(get_class($object), 'getValidationRules'), get_class($object));
        if (count($rules['validateLang'])) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach (array_keys($rules['validateLang']) as $field) {
                    if (isset($_POST[$field . '_' . (int) $language['id_lang']])) {
                        $object->{$field}[(int) $language['id_lang']] = $_POST[$field . '_' . (int) $language['id_lang']];
                    }
                }
            }
        }
    }

    /**
     * Returns an array with selected shops and type (group or boutique shop)
     *
     * @param string $table
     *
     * @return array
     */
    protected function getSelectedAssoShop($table)
    {
        if (!Shop::isFeatureActive() || !Shop::isTableAssociated($table)) {
            return array();
        }

        $shops = Shop::getShops(true, null, true);
        if (count($shops) == 1 && isset($shops[0])) {
            return array($shops[0], 'shop');
        }

        $assos = array();
        if (Tools::isSubmit('checkBoxShopAsso_' . $table)) {
            foreach (Tools::getValue('checkBoxShopAsso_' . $table) as $id_shop => $value) {
                $assos[] = (int) $id_shop;
            }
        } elseif (Shop::getTotalShops(false) == 1) {// if we do not have the checkBox multishop, we can have an admin with only one shop and being in multishop
            $assos[] = (int) Shop::getContextShopID();
        }

        return $assos;
    }

    /**
     * Update the associations of shops
     *
     * @param int $id_object
     */
    protected function updateAssoShop($id_object)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }

        if (!Shop::isTableAssociated($this->object_table)) {
            return;
        }

        $assos_data = $this->getSelectedAssoShop($this->object_table, $id_object);

        // Get list of shop id we want to exclude from asso deletion
        $exclude_ids = $assos_data;
        foreach (Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop') as $row) {
            if (!$this->context->employee->hasAuthOnShop($row['id_shop'])) {
                $exclude_ids[] = $row['id_shop'];
            }
        }
        Db::getInstance()->delete(
            $this->object_table . '_shop',
            '`' . $this->object_identifier . '` = ' . (int) $id_object . ($exclude_ids ? ' AND id_shop NOT IN (' . implode(
                ', ',
                $exclude_ids
            ) . ')' : '')
        );

        $insert = array();
        foreach ($assos_data as $id_shop) {
            $insert[] = array(
                $this->object_identifier => $id_object,
                'id_shop'                => (int) $id_shop,
            );
        }

        return Db::getInstance()->insert($this->object_table . '_shop', $insert, false, true, Db::INSERT_IGNORE);
    }

    /**
     * @param $value
     * @param $field
     *
     * @return bool
     */
    protected function validateField($value, $field)
    {
        if (isset($field['validation'])) {
            $valid_method_exists = method_exists('Validate', $field['validation']);
            $valid_method_name = $field['validation'];
            if ((!isset($field['empty']) || !$field['empty'] || (isset($field['empty']) && $field['empty'] && $value)) && $valid_method_exists) {
                if (isset($field['is_array']) and $field['is_array']) {
                    foreach ($value as $v) {
                        if (!Validate::$valid_method_name($v)) {
                            $this->errors[] = Tools::displayError($field['title'] . ' : Has incorrect values');

                            return false;
                        }
                    }
                } elseif (!Validate::$valid_method_name($value)) {
                    $this->errors[] = Tools::displayError($field['title'] . ' : Incorrect value');

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Can be overriden
     */
    public function beforeUpdateOptions()
    {
    }

    /**
     *
     */
    public function afterUpdateOptions()
    {

    }

    /**
     * Overload this method for custom checking
     *
     * @param integer $id Object id used for deleting images
     *
     * @return boolean
     */
    protected function postImage($id)
    {
        if (isset($this->fieldImageSettings['name']) && isset($this->fieldImageSettings['dir'])) {
            return $this->uploadImage($id, $this->fieldImageSettings['name'], $this->fieldImageSettings['dir'] . '/');
        } elseif (!empty($this->fieldImageSettings)) {
            foreach ($this->fieldImageSettings as $image) {
                if (isset($image['name']) && isset($image['dir'])) {
                    $this->uploadImage($id, $image['name'], $image['dir'] . '/');
                }
            }
        }

        return !count($this->errors) ? true : false;
    }

    /**
     * @param            $id
     * @param            $name
     * @param            $dir
     * @param bool|false $ext
     * @param null       $width
     * @param null       $height
     *
     * @return bool
     */
    protected function uploadImage($id, $name, $dir, $ext = false, $width = null, $height = null)
    {


        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
            // Delete old image
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $object->deleteImage();
            } else {
                return false;
            }

            // Check image validity
            $max_size = isset($this->max_image_size) ? $this->max_image_size : 0;
            if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize($max_size))) {
                $this->errors[] = $error;
            }

            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmp_name) {
                return false;
            }

            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name)) {
                return false;
            }

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmp_name)) {
                $this->errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');
            }

            // Copy new image
            if (empty($this->errors) && !ImageManager::resize(
                $tmp_name,
                _PS_IMG_DIR_ . $dir . $id . '.' . $this->imageType,
                (int) $width,
                (int) $height,
                ($ext ? $ext : $this->imageType)
            )
            ) {
                $this->errors[] = Tools::displayError('An error occurred while uploading the image.');
            }

            if (count($this->errors)) {
                return false;
            }
            if ($this->afterImageUpload()) {
                unlink($tmp_name);

                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Delete multiple items
     *
     * @return boolean true if succcess
     */
    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $object = new $this->className();

            if (isset($object->noZeroObject)) {
                $objects_count = count(call_user_func(array($this->className, $object->noZeroObject)));

                // Check if all object will be deleted
                if ($objects_count <= 1 || count($this->boxes) == $objects_count) {
                    $this->errors[] = Tools::displayError('You need at least one object.') .
                                      ' <b>' . $this->object_table . '</b><br />' .
                                      Tools::displayError('You cannot delete all of the items.');
                }
            } else {
                $result = true;
                if ($this->deleted) {
                    foreach ($this->boxes as $id) {
                        $to_delete          = new $this->className($id);
                        $to_delete->deleted = 1;
                        $result             = $result && $to_delete->update();
                    }
                } else {
                    $result = $object->deleteSelection(Tools::getValue($this->object_table . 'Box'));
                }

                if ($result) {
                    $this->redirect_after = self::$currentIndex . '&conf=2&token=' . $this->token;
                }
                $this->errors[] = Tools::displayError('An error occurred while deleting this selection.');
            }
        } else {
            $this->errors[] = Tools::displayError('You must select at least one element to delete.');
        }

        if (isset($result)) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Enable multiple items
     *
     * @return boolean true if succcess
     */
    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    /**
     * Disable multiple items
     *
     * @return boolean true if succcess
     */
    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }

    /**
     * Toggle status of multiple items
     *
     * @return boolean true if succcess
     */
    protected function processBulkStatusSelection($status)
    {
        $result = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $object         = new $this->className((int) $id);
                $object->active = (int) $status;
                $result &= $object->update();
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function processBulkAffectZone()
    {
        $result = false;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $object = new $this->className();
            $result = $object->affectZoneToSelection(
                Tools::getValue($this->object_table . 'Box'),
                Tools::getValue('zone_to_affect')
            );

            if ($result) {
                $this->redirect_after = self::$currentIndex . '&conf=28&token=' . $this->token;
            }
            $this->errors[] = Tools::displayError('An error occurred while affecting a zone to the selection.');
        } else {
            $this->errors[] = Tools::displayError('You must select at least one element to affect a new zone.');
        }

        return $result;
    }

    /**
     * Called before Add
     *
     * @param object $object Object
     *
     * @return boolean
     */
    protected function beforeAdd(&$object)
    {
        return true;
    }

    /**
     * Called before Update
     *
     * @param object $object Object
     *
     * @return boolean
     */
    protected function beforeUpdate(&$object)
    {
        return true;
    }

    /**
     * prepare the view to display the required fields form
     */
    public function displayRequiredFields()
    {


        $helper               = new Inix2Helper();
        $helper->currentIndex = self::$currentIndex;
        $helper->token        = $this->token;

        return $helper->renderRequiredFields($this->className, $this->object_identifier, $this->required_fields);
    }


    /**
     * Shortcut to set up a json success payload
     *
     * @param $message "success message"
     */
    public function jsonConfirmation($message)
    {
        $this->json            = true;
        $this->confirmations[] = $message;
        if ($this->status === '') {
            $this->status = 'ok';
        }
    }

    /**
     * Shortcut to set up a json error payload
     *
     * @param $message "error message"
     */
    public function jsonError($message)
    {
        $this->json     = true;
        $this->errors[] = $message;
        if ($this->status === '') {
            $this->status = 'error';
        }
    }

    /**
     * @param     $file
     * @param int $timeout
     *
     * @return bool
     */
    public function isFresh($file, $timeout = 604800000)
    {
        if (file_exists(_PS_ROOT_DIR_ . $file)) {
            if (filesize(_PS_ROOT_DIR_ . $file) < 1) {
                return false;
            }

            return ((time() - filemtime(_PS_ROOT_DIR_ . $file)) < $timeout);
        } else {
            return false;
        }
    }

    /**
     * @param $file_to_refresh
     * @param $external_file
     *
     * @return bool
     */
    public function refresh($file_to_refresh, $external_file)
    {
        $content = Tools::file_get_contents($external_file);
        if ($content) {
            return (bool) file_put_contents(_PS_ROOT_DIR_ . $file_to_refresh, $content);
        }

        return false;
    }


    /**
     * @return bool
     * @throws PrestaShopException
     */
    public function install()
    {
        $install = parent::install();
        $hooks   = true;
        $sqls    = true;
        $tabs    = true;
        $options = true;
        $mails   = true;
        foreach ($this->install_hooks as $hook) {
            $hooks &= $this->registerHook($hook);
        }
        if (!$hooks) {
            $this->_errors[] = Tools::displayError('Problem with installing hooks');
        }


        if (file_exists($this->getLocalPath() . '/install.sql')) {
            $sql = file_get_contents($this->getLocalPath() . '/install.sql');
            if (!$sql) {
                $sqls            = false;
                $this->_errors[] = Tools::displayError('SQL install file is probably corrupted!');
            } else {
                $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
                $sql = str_replace('_ENGINE_', _MYSQL_ENGINE_, $sql);
                $sql = preg_split("/;\s*[\r\n]+/", $sql);

                foreach ($sql as $query) {
                    if (strlen($query) <= 3) {
                        continue;
                    }
                    $sqls &= Db::getInstance()->execute(trim($query));

                }
                if (!$sqls) {
                    $this->_errors[] = Tools::displayError('Problem with creating database tables!');
                }
            }

        }


        foreach ($this->install_options as $option => $value) {
            $options &= Configuration::updateValue($option, $value);
        }

        foreach ($this->install_tabs as $t) {
            $tabs &= $this->installModuleTab(
                $t['class'],
                $t['name'],
                $t['parent'],
                (isset($t['active']) ? $t['active'] : true)
            );
        }

        foreach ($this->install_mails as $m) {
            $mails = $this->installMailTpl($m);
        }


        $status = ($install and $hooks and $sqls and $tabs and $options and $mails);
        if ($status) {
            $this->cleanUpdateData();
            $this->doModuleInstall();
        }

        return $status;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $uninstall = parent::uninstall();
        $sqls      = true;
        $tabs      = true;
        $options   = true;
        if (file_exists($this->getLocalPath() . '/uninstall.sql')) {
            $sql = file_get_contents($this->getLocalPath() . '/uninstall.sql');
            if (!$sql) {
                $sqls            = false;
                $this->_errors[] = Tools::displayError('SQL uninstall file is probably corrupted!');
            } else {
                $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
                $sql = preg_split("/;\s*[\r\n]+/", $sql);

                foreach ($sql as $query) {
                    if (strlen($query) <= 3) {
                        continue;
                    }
                    $sqls &= Db::getInstance()->execute(trim($query));

                }
                if (!$sqls) {
                    $this->_errors[] = Tools::displayError('Problem with removing database tables!');
                }
            }
        }
        foreach ($this->uninstall_options as $option => $value) {
            $options &= Configuration::deleteByName($option);
        }


        foreach ($this->uninstall_tabs as $tab) {
            $tabs &= $this->uninstallModuleTab($tab);
        }

        $langs = Language::getLanguages();

        foreach ($langs as $l) {
            if (is_dir($this->getLocalPath() . '/mails/' . $l['iso_code'])) {
                Tools::deleteDirectory($this->getLocalPath() . '/mails/' . $l['iso_code']);
            }

        }

        $status = ($uninstall and $sqls and $tabs and $options);

        if ($status) {
            $this->doModuleUninstall();
        }

        return $status;
    }

    /**
     * @param           $tabClass
     * @param           $tabName
     * @param int       $tabParent
     * @param bool|true $active
     *
     * @return bool
     */
    public function installModuleTab($tabClass, $tabName, $tabParent = 0, $active = true)
    {
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $tab             = new Tab(Tab::getIdFromClassName($tabClass));
        $tab->name       = array($id_lang_default => $tabName);
        $tab->class_name = $tabClass;
        $tab->module     = $this->name;
        $tab->id_parent  = $tabParent === 0 ? 0 : Tab::getIdFromClassName($tabParent);
        $tab->active     = $active;

        return $tab->save();
    }

    /**
     * @param $tabClass
     *
     * @return bool
     */
    public function uninstallModuleTab($tabClass)
    {

        $idTab = Tab::getIdFromClassName($tabClass);

        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();

        }

        return true;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if(Tools::file_exists_cache(_PS_MODULE_DIR_.'inixframe_fonts/open_sans.css')){
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/inixframe_fonts/open_sans.css');
        } else {
            $this->context->controller->addCSS($this->getFramePathUri() . 'css/open_sans.css');
        }

        $this->context->controller->addCSS($this->getFramePathUri() . 'css/font-awesome.css');
        $this->context->controller->addCSS($this->getFramePathUri() . 'css/bootstrap.css');
        $this->context->controller->addCSS($this->getFramePathUri() . 'css/animate.css');
        $this->context->controller->addCSS($this->getFramePathUri() . 'css/style.css');
        $this->context->controller->addCSS($this->getFramePathUri() . 'css/frame.css');
        $this->context->controller->addCSS($this->getFramePathUri() . 'css/toastr.min.css');

        if (Tools::version_compare(_PS_VERSION_, '1.6.0')) {
            $this->context->controller->addJS($this->getFramePathUri() . 'js/vendor/bootstrap.min.js');
            $this->context->controller->addJS($this->getFramePathUri() . 'js/vendor/moment-with-langs.min.js');
        }


        $this->context->controller->addJS($this->getFramePathUri() . 'js/frame.js');
        $this->context->controller->addJS($this->getFramePathUri() . 'js/toastr.min.js');


        if (!isset($this->context->cookie->iwframe_hide_register)) {
            $this->context->controller->addJS($this->getFramePathUri() . 'views/js/script.js');
        }
        if (Tools::isSubmit('iniframe')) {
            $this->context->controller->addCSS($this->getFramePathUri() . 'css/admin.css');
        }

        $this->init();
        // postProcess handles ajaxProcess
        $this->postProcess();

        if (!empty($this->redirect_after)) {
            $this->redirect();
        }


        $this->initContent();

        // default behavior for ajax process is to use $_POST[action] or $_GET[action]
        // then using displayAjax[action]
        if ($this->ajax) {
            if (Tools::isSubmit('json') and Tools::getValue('json')) {
                $this->json = 1;
            }

            $action = Tools::getValue('action');
            if (!empty($action) && method_exists($this, 'displayAjax' . Tools::toCamelCase($action, true))) {
                $this->{'displayAjax' . $action}();
            } elseif (method_exists($this, 'displayAjax')) {
                $this->displayAjax();
            }
        } else {
            $this->displayContent();
        }

        return $this->_html;
    }

    /**
     * Create a template from the override file, else from the base file.
     *
     * @param string $tpl_name filename
     *
     * @return Template
     */
    public function createTemplate($tpl_name)
    {
        // Use override tpl if it exists
        if (file_exists($this->getLocalPath() . 'views' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name)) {
            return $this->context->smarty->createTemplate(
                $this->getLocalPath() . 'views' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name,
                $this->context->smarty
            );
        }
        if (file_exists($this->getLocalPath() . 'views' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name)) {
            return $this->context->smarty->createTemplate(
                $this->getLocalPath() . 'views' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name,
                $this->context->smarty
            );
        }

        return $this->context->smarty->createTemplate(
            $this->getFrameLocalPath() . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $tpl_name,
            $this->context->smarty
        );
    }


    /**
     * @param $content
     */
    protected function smartyOutputContent($content)
    {
        $this->context->cookie->write();
        $smarty_subtemplate = $this->createTemplate($content);
        $this->_html        = $smarty_subtemplate->fetch();
        if ($this->ajax or $this->json) {
            echo $this->_html;
        }
    }

    /**
     * @return string
     */
    public function getFrameVersion()
    {
        return $this->frame_version;
    }

    /**
     * @return string
     */
    public function getFrameLocalPath()
    {
        return $this->frame_local_path;
    }

    /**
     * @return string
     */
    public function getFramePathUri()
    {
        return $this->_frame_path;
    }

    /**
     * @param            $message
     * @param int        $severity
     * @param null       $error_code
     * @param null       $object_type
     * @param null       $object_id
     * @param bool|false $allow_duplicate
     * @param null       $id_employee
     */
    public static function addLog(
        $message,
        $severity = 1,
        $error_code = null,
        $object_type = null,
        $object_id = null,
        $allow_duplicate = false,
        $id_employee = null
    ) {

        if (class_exists('PrestaShopLogger')) {
            PrestaShopLogger::addLog(
                $message,
                $severity = 1,
                $error_code = null,
                $object_type = null,
                $object_id = null,
                $allow_duplicate = false,
                $id_employee = null
            );
        } else {
            Logger::addLog(
                $message,
                $severity = 1,
                $error_code = null,
                $object_type = null,
                $object_id = null,
                $allow_duplicate = false,
                $id_employee = null
            );
        }
    }

    /**
     * @return array
     */
    public function runUpgradeModule()
    {
        $this->cleanUpdateData();
        $upgrade = parent::runUpgradeModule();
        if ($upgrade['success']) {
            $this->doModuleUpdate($upgrade['upgraded_to']);
        }

        return $upgrade;
    }


    /**
     *
     */
    public function init_autoload()
    {
        /// autoload core inixframe classes
        spl_autoload_register(array($this, 'inixAutload'));

        // if class has autoloader for himself, use it
        if (method_exists($this, 'autoload')) {
            spl_autoload_register(array($this, 'autoload'));
        } else {
            // otherwise use the gerneric autoloader
            spl_autoload_register(array($this, 'autoloadGeneric'));
        }
    }

    /**
     * @param $name
     */
    public function inixAutload($name)
    {

        if ($name == 'Inix2AdminController') {
            require_once _PS_MODULE_DIR_ . 'inixframe/InixAdminController.php';
        } elseif (stristr($name, 'Inix2Helper')) {
            require_once _PS_MODULE_DIR_ . 'inixframe/helper/' . str_replace('Inix2', '', $name) . '.php';
        } elseif (stristr($name, 'InixHelper2')) {
            require_once _PS_MODULE_DIR_ . 'inixframe/helper/' . str_replace('InixHelper2', 'Helper', $name) . '.php';
        } elseif (stristr($name, 'Inix2Tree')) {
            require_once _PS_MODULE_DIR_ . 'inixframe/helper/tree/' . str_replace('Inix2', '', $name) . '.php';
        } elseif($name =='Inix2Config'){
            require_once _PS_MODULE_DIR_ . 'inixframe/classes/Inix2Config.php';
        }
    }

    /**
     * @param $name
     */
    public function autoloadGeneric($name)
    {
        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/classes/' . $name . '.php') and $name . '.php' != 'index.php') {
            require_once _PS_MODULE_DIR_ . $this->name . '/classes/' . $name . '.php';
        }
    }


    /**
     * @param $message
     */
    public function pushFlashMessage($message)
    {
        $flash_key                                                    = $this->name . '_flash_message';
        $messages                                                     = $this->context->cookie->getFamily($flash_key);
        $this->context->cookie->{$flash_key . ':' . count($messages)} = $message;
    }

    /**
     * @return bool
     */
    public function hasFlashMessage()
    {
        $flash_key = $this->name . '_flash_message';

        return (bool) count($messages = $this->context->cookie->getFamily($flash_key));
    }

    /**
     * @return array
     */
    public function getFlashMessage()
    {
        $flash_key = $this->name . '_flash_message';

        return ($this->context->cookie->getFamily($flash_key));
    }

    /**
     * @return void
     */
    public function deleteFlashMessage()
    {
        $flash_key = $this->name . '_flash_message';
        $this->context->cookie->unsetFamily($flash_key);
    }
}
