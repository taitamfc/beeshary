<?php
/*
* 2010-2016 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class PS_CURL
{
    protected $_cookieFileLocation = './ps_cron_cookie.txt';
    protected $_useragent = 'Mohit Chandra : PrestaShop Cron';
    protected $_header = array('Content-Type: application/x-www-form-urlencoded');
    protected $_referer = 'http://www.google.com';

    public $_webpage;

    protected $_postFields;
    protected $_status;
    protected $cr; // curl cursor

    public function __destruct()
    {
        curl_close($this->cr);
    }

    public function __construct($url = '')
    {
        $this->_url = $url;
        $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt';
        $this->cr = curl_init();
    }

    public function setCookiFileLocation($path)
    {
        $this->_cookieFileLocation = $path;
    }

    public function setUserAgent($userAgent)
    {
        $this->_useragent = $userAgent;
    }

    public function setHeader($header)
    {
        $this->_header = $header;
    }

    public function setReferer($referer)
    {
        $this->_referer = $referer;
    }

    public function setPost($postFields)
    {
        if (is_array($postFields)) {
            $fields_string = http_build_query($postFields);
        } else {
            $fields_string = $postFields;
        }
        $this->_postFields = $fields_string;
    }

    public function __tostring()
    {
        return $this->_webpage;
    }

    public function call($url = null, $header = null)
    {
        if ($header) {
            $this->_header = $header;
        }

        $this->_url = $url;

        if (!$url) {
            throw new Exception('You should set an URL to call.');
        }

        curl_setopt($this->cr, CURLOPT_URL, $this->_url);
        curl_setopt($this->cr, CURLOPT_HTTPHEADER, $this->_header);
        curl_setopt($this->cr, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->cr, CURLOPT_MAXREDIRS, 3);
        curl_setopt($this->cr, CURLOPT_RETURNTRANSFER, true);
           //curl_setopt($this->cr,CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($this->cr, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->cr, CURLOPT_COOKIESESSION, true);
        curl_setopt($this->cr, CURLOPT_COOKIEFILE, $this->_cookieFileLocation);
        curl_setopt($this->cr, CURLOPT_COOKIEJAR, $this->_cookieFileLocation);

        curl_setopt($this->cr, CURLOPT_POST, true);
        curl_setopt($this->cr, CURLOPT_POSTFIELDS, $this->_postFields);

        curl_setopt($this->cr, CURLOPT_HEADER, true);
            // curl_setopt($this->cr,CURLOPT_NOBODY,true);

            curl_setopt($this->cr, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($this->cr, CURLOPT_REFERER, $this->_referer);

        $this->_webpage = curl_exec($this->cr);

        $this->_status = curl_getinfo($this->cr, CURLINFO_HTTP_CODE);

        return $this->_webpage;
    }

    public function getHttpStatus()
    {
        return $this->_status;
    }
}
