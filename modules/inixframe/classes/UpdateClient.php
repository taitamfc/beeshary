<?php

/**
 * Class InixUpdateClient
 */
class InixUpdateClient
{

    /**
     * @var string
     */
    public $service_url = 'http://service.presta-sandbox.com/';
    /**
     * @var string
     */
    public $service_api_uri = 'api/';
    /**
     * @var string
     */
    public $service_api_url;

    /**
     * @var
     */
    private $errors;
    /**
     * @var
     */
    private $warnings;
    /**
     * @var
     */
    private $confirmations;
    /**
     * @var
     */
    private $status;


    /**
     * @var array
     */
    private $system_messages = array('errors', 'warnings', 'confirmations', 'status');


    /**
     * @var
     */
    private $shop_domain;

    /**
     * @var
     */
    private $client_token;

    /**
     * @param bool|false $token
     */
    function __construct($token = false)
    {
        //testing
        if ($service_url = @getenv('UPDATE_SERVICE_URL')) {
            $this->service_url = $service_url;
        }

        $this->service_api_url = $this->service_url . $this->service_api_uri;

        if ($token) {
            $this->setClientToken($token);
        }

    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return array
     */
    public function getConfirmations()
    {
        return $this->confirmations;
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getClientToken()
    {
        if (isset($this->client_token)) {
            return $this->client_token;
        }

        return false;
    }

    /**
     * @param mixed $client_token
     */
    public function setClientToken($client_token)
    {
        $this->client_token = $client_token;
    }


    /**
     * @param $enpoint
     * @param $data
     *
     * @return bool|mixed
     */
    protected function doRequest($enpoint, $data)
    {

        if ($this->getClientToken() !== false) {
            $data['token'] = $this->getClientToken();
        }
        $data['domain'] = $this->getShopDomain();
        $postdata       = http_build_query($data);

        $opts     = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $response = Tools::file_get_contents($this->service_api_url . $enpoint, false, stream_context_create($opts));


        $response_data = json_decode($response, true);


        if (is_null($response_data) or !is_array($response_data)) {
            return false;
        }
        foreach ($this->system_messages as $message) {
            if (isset($response_data[$message])) {
                $this->$message = $response_data[$message];

                unset($response_data[$message]);
            }
        }

        return $response_data;
    }

    /**
     * @return string
     */
    public function getShopDomain()
    {

        if (!isset($this->shop_domain)) {
            $shop              = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
            $this->shop_domain = $shop->domain;
        }

        return $this->shop_domain;
    }

    /**
     * @param mixed $shop_domain
     */
    public function setShopDomain($shop_domain)
    {
        $this->shop_domain = $shop_domain;
    }


    /**
     * @param $email
     * @param $password
     *
     * @return bool|mixed
     */
    public function register($email, $password)
    {

        $data = array(
            'email'    => $email,
            'password' => $password,

        );

        $response = $this->doRequest('register', $data);

        return $response;
    }

    /**
     * @param $modules
     *
     * @return bool|mixed
     */
    public function checkUpdate($modules)
    {

        $data = array(
            'modules' => $modules,
        );

        return $this->doRequest('update-check', $data);
    }

    /**
     * @param            $module
     * @param            $author
     * @param bool|false $dist_chanel
     *
     * @return bool|mixed
     */
    public function fetch($module, $author, $dist_chanel = false)
    {

        $data = array(
            'module' => $module,
            'author' => $author,

        );
        if ($dist_chanel) {
            $data['dist_chanel'] = $dist_chanel;
        }

        $response = $this->doRequest('fetch', $data);

        return $response;
    }


    /**
     * @param Inix2Module $module
     *
     * @return bool|mixed
     */
    public function moduleInstall($module)
    {

        $data = array(
            'module'  => $module->name,
            'version' => $module->version,
            'status'  => Module::isEnabled($module->name),
        );

        return $this->doRequest('module-install', $data);
    }

    /**
     * @param Inix2Module $module
     *
     * @return bool|mixed
     */
    public function moduleUninstall($module)
    {

        $data = array(
            'module'  => $module->name,
            'version' => $module->version,
            'status'  => Module::isEnabled($module->name),
        );

        return $this->doRequest('module-uninstall', $data);
    }

    /**
     * @param Inix2Module $module
     *
     * @param             $new_version
     *
     * @return bool|mixed
     */
    public function moduleUpdate($module, $new_version)
    {

        $data = array(
            'module'    => $module->name,
            'version'   => $new_version,
            'status'    => Module::isEnabled($module->name),
            'installed' => Module::isInstalled($module->name)
        );

        return $this->doRequest('module-update', $data);
    }


    /**
     * @return bool|mixed
     */
    public function fetchBanners()
    {
        return $this->doRequest('banners', array());
    }
}
