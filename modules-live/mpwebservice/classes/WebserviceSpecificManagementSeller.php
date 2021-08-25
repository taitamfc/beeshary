<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WebserviceSpecificManagementSeller implements WebserviceSpecificManagementInterface
{
    protected $objOutput;
    protected $output;
    protected $wsObject;
    public static $allowedMethods = array(
        'sellerinfo',
        'sellerproduct',
        'saveproduct',
        'deleteproduct',
        'assignproduct',
        'sellerorder',
        'createbookingcart', // api for customer: without seller auth_key
        'createorder', // api for customer: without seller auth_key
        'updateorderstatus', // api for customer: without seller auth_key
        'mpimages',
        'customerbooking', // api for customer: without seller auth_key
        'getbookingproduct', // api for customer: without seller auth_key
    );
    public $imgToDisplay = null;
    public $objMpImage;

    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;
        return $this;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;
        return $this;
    }

    public function getWsObject()
    {
        return $this->wsObject;
    }

    public function manage()
    {
        $authkey = Tools::getValue('auth_key');
        $adminAuthkey = Tools::getValue('admin_auth_key');
        if ($adminAuthkey) {
            if ($adminAuthkey == Configuration::get('MP_ADMIN_WS_KEY')) {
                $idSeller = Tools::getValue('id_seller');
                if ($idSeller) {
                    $objSeller = new WkMpSeller($idSeller);
                    if (!Validate::isLoadedObject($objSeller)) {
                        throw new WebserviceException('Invalid id_seller', array(48, 400));
                    } else {
                        $this->setSellerContext($idSeller);
                    }
                } else {
                    throw new WebserviceException('id_seller is required', array(48, 400));
                }
            } else {
                throw new WebserviceException('Invalid admin authentication key', array(48, 400));
            }
        } elseif ($authkey) {
            if (!$this->validateAuthKey($authkey)) {
                throw new WebserviceException('Invalid authentication key', array(48, 400));
            }
        } else {
            $apiWithoutAuthKey = array(
                'customerbooking',
                'createbookingcart',
                'createorder',
                'updateorderstatus',
                'getbookingproduct'
            );
            if (!in_array($this->wsObject->urlSegment[1], $apiWithoutAuthKey)) {
                throw new WebserviceException('Invalid authentication key', array(48, 400));
            }
            // if ($this->wsObject->urlSegment[1] != 'customerbooking') {
            //     throw new WebserviceException('Invalid authentication key', array(48, 400));
            // }
        }

        $this->wsMobikulCRUD();
        return $this->wsObject->getOutputEnabled();
    }

    protected function validateAuthKey($authKey)
    {
        if ($keyInfo = WkMpWebserviceKey::getKeyDetails($authKey)) {
            $apis = Tools::jsonDecode($keyInfo['mpresource']);
            if (isset($this->wsObject->urlSegment[1])) {
                if (!in_array($this->wsObject->urlSegment[1], $apis)) {
                    throw new WebserviceException(sprintf('Resource of type "%s" is not allowed
                    with this authentication key', $this->wsObject->urlSegment[1]), array(48, 400));
                }
            }
            // Using customer id to get all details of seller
            $this->setSellerContext($keyInfo['id_seller']);
            return true;
        }
        return false;
    }

    public function setSellerContext($idSeller)
    {
        $objSeller = new WkMpSeller($idSeller);
        if (Validate::isLoadedObject($objSeller)) {
            Context::getContext()->customer->id = $objSeller->seller_customer_id;
        } else {
            Context::getContext()->customer = new Customer();
        }
    }

    public function getContent()
    {
        if (!empty($this->output) && is_array($this->output)) {
            // new only json output
            $this->objOutput->setHeaderParams('Content-Type', 'application/json');
            $content = json_encode($this->output, JSON_UNESCAPED_UNICODE);
            return (false !== $content) ? $content : '';
            //-----------


            $outputXML = $this->objOutput->getObjectRender()->overrideContent($this->output);
            if (isset($this->wsObject->urlFragments['outputformat'])
            && ($this->wsObject->urlFragments['outputformat'] == 'json')) {
                $outputXML = simplexml_load_string($outputXML, null, LIBXML_NOCDATA);
                $this->objOutput->setHeaderParams('Content-Type', 'application/json');
                $content = json_encode($outputXML);
                $content = preg_replace_callback(
                    "/\\\\u(MJYETAWAJ1BMAILEHYRTZJTQGXCD3HW6[a-f0-9]{4})/",
                    function ($matches) {
                        return iconv('UCS-4LE', 'UTF-8', pack('V', hexdec('U' . $matches[1])));
                    },
                    $content
                );
                return $content;
            } else {
                return $outputXML;
            }
        } elseif (isset($this->objMpImage->imgToDisplay) && $this->objMpImage->imgToDisplay) {
            if (empty($this->objMpImage->imgExtension)) {
                $imginfo = getimagesize($this->objMpImage->imgToDisplay);
                $this->objMpImage->imgExtension = image_type_to_extension($imginfo[2], false);
            }
            $imageResource = false;
            $types = array(
                'jpg' => array(
                    'function' => 'imagecreatefromjpeg',
                    'Content-Type' => 'image/jpeg',
                ),
                'jpeg' => array(
                    'function' => 'imagecreatefromjpeg',
                    'Content-Type' => 'image/jpeg',
                ),
                'png' => array('function' => 'imagecreatefrompng',
                    'Content-Type' => 'image/png',
                ),
                'gif' => array(
                    'function' => 'imagecreatefromgif',
                    'Content-Type' => 'image/gif',
                ),
            );
            if (array_key_exists($this->objMpImage->imgExtension, $types)) {
                $imageResource = @$types[$this->objMpImage->imgExtension]['function']($this->objMpImage->imgToDisplay);
            }
            if (!$imageResource) {
                throw new WebserviceException(sprintf('Unable to load the image "%s"', str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $this->objMpImage->imgToDisplay)), array(47, 500));
            } else {
                if (array_key_exists($this->objMpImage->imgExtension, $types)) {
                    $this->objOutput->setHeaderParams('Content-Type', $types[$this->objMpImage->imgExtension]['Content-Type']);
                }
                return Tools::file_get_contents($this->objMpImage->imgToDisplay);
            }
        }
    }

    protected function wsMobikulCRUD()
    {
        $objMpSeller = new WkMpSellerWs($this->objOutput, $this->wsObject, $this->output);
        $objMpSellerProduct = new WkMpProductWs($this->objOutput, $this->wsObject, $this->output);
        $objMpSellerOrder = new WkMpOrderWs($this->objOutput, $this->wsObject, $this->output);
        $this->objMpImage = new WkMpImageWs($this->objOutput, $this->wsObject, $this->output);
        switch ($this->wsObject->method) {
            case 'GET':
                switch ($this->wsObject->urlSegment[1]) {
                    case 'sellerinfo':
                        $this->output = $objMpSeller->sellerInfo();
                        break;
                    case 'sellerproduct':
                        $this->output = $objMpSellerProduct->sellerProduct();
                        break;
                    case 'assignproduct':
                        if ($adminAuthkey = Tools::getValue('admin_auth_key')) {
                            if ($adminAuthkey == Configuration::get('MP_ADMIN_WS_KEY')) {
                                if (!isset($this->wsObject->urlFragments['id_product'])) {
                                    throw new WebserviceException('You have to set the \'id_product\' parameters to get a result', array(100, 400));
                                }
                                $this->output = $objMpSellerProduct->assignProduct();
                            } else {
                                throw new WebserviceException('Permission denied', array(100, 400));
                            }
                        } else {
                            throw new WebserviceException('Permission denied', array(100, 400));
                        }
                        break;
                    case 'sellerorder':
                        $this->output = $objMpSellerOrder->sellerOrder();
                        break;
                    case 'mpimages':
                        $this->output = $this->objMpImage->manageMpImages();
                        break;
                    case 'customerbooking':
                        $idCustomer = Tools::getValue('id_customer');
                        if (!$idCustomer) {
                            $this->output['success'] = false;
                            $this->output['msg'] = 'Required id_customer';
                            return $this->output;
                        }
                        $this->output = $objMpSellerOrder->getCustomerBooking($idCustomer);
                        break;
                    case 'getbookingproduct':
                        $idProduct = Tools::getValue('id_product');
                        if (!$idProduct) {
                            $this->output['success'] = false;
                            $this->output['msg'] = 'Required id_product';
                            return $this->output;
                        }
                        $this->output = $objMpSellerProduct->getBookingProduct($idProduct);
                        break;
                    case '':
                        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('marketplace_methods', array());
                        foreach (self::$allowedMethods as $method_name) {
                            $more_attr = array(
                                'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true',
                            );
                            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader($method_name, array(), $more_attr, false);
                        }
                        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('marketplace_methods', array());
                        break;
                    default:
                        $exception = new WebserviceException(
                            sprintf(
                                'Method with name "%s" does not exist in Marketplace Library for Prestashop,',
                                $this->wsObject->urlSegment[1]
                            ),
                            array(48, 400)
                        );
                        throw $exception->setDidYouMean($this->wsObject->urlSegment[1], self::$allowedMethods);
                }
                break;
            case 'HEAD':
            case 'PUT':
                switch ($this->wsObject->urlSegment[1]) {
                    case 'mpimages':
                        $this->output = $this->objMpImage->manageMpImages();
                        break;
                }
                break;
            case 'DELETE':
                switch ($this->wsObject->urlSegment[1]) {
                    case 'deleteproduct':
                        $fields = $this->getPostFields();
                        $this->output = $objMpSellerProduct->deleteProduct($fields);
                        break;
                    case 'mpimages':
                        $this->output = $this->objMpImage->manageMpImages();
                        break;
                }
                break;
            case 'POST':
                switch ($this->wsObject->urlSegment[1]) {
                    case 'saveproduct':
                        $fields = $this->getPostFields();
                        $this->output = $objMpSellerProduct->saveProduct($fields);
                        break;
                    case 'createorder':
                        $fields = $this->getPostFields();
                        $this->output = $objMpSellerOrder->createOrder($fields);
                        break;
                    case 'createbookingcart':
                        $fields = $this->getPostFields();
                        $this->output = $objMpSellerOrder->createBookingCart($fields);
                        break;
                    case 'updateorderstatus':
                        $fields = $this->getPostFields();
                        $this->output = $objMpSellerOrder->updateOrderStatus($fields);
                        break;
                    case 'mpimages':
                        $this->output = $this->objMpImage->manageMpImages();
                        break;
                }
                break;
            default:
                throw new WebserviceException('This HTTP method is not allowed', array(67, 405));
        }
    }

    public function getPostFields()
    {
        $putresource = fopen('php://input', 'r');
        $inputJson = '';
        while ($putData = fread($putresource, 1024)) {
            $inputJson .= $putData;
        }
        fclose($putresource);
        $headers = $this->getAllHeaders();
        return Tools::jsonDecode($inputJson, true);
        // this code does not work. the headers are not correctly read
//        if ('application/json' == $headers['Content-Type']) {
//            return Tools::jsonDecode($inputJson, true);
//        } else {
//            WebserviceRequest::getInstance()->setError(500, 'Invalid json.', 127);
//            return;
//        }
    }

    /**
     * Get the POST (xml/json) data by POST request
     *
     * @param  boolean $head main node
     * @return array xml to array convert
     */
    public function getXMLFields($head = false)
    {
        $putresource = fopen('php://input', 'r');
        $inputXML = '';
        while ($putData = fread($putresource, 1024)) {
            $inputXML .= $putData;
        }
        fclose($putresource);
        $headers = $this->getAllHeaders();

        if ('application/json' == $headers['Content-Type']) {
            // If input type is json
            $array = Tools::jsonDecode($inputXML, true);
            if (isset($array['json']) && $array['json'] && isset($array[$head])) {
                return ($head ? $array[$head] : $array);
            } else {
                WebserviceRequest::getInstance()->setError(500, 'Invalid json.', 127);
                return;
            }
        } else {
            // If input type is xml
            if (isset($inputXML) && strncmp($inputXML, 'xml=', 4) == 0) {
                $inputXML = Tools::substr($inputXML, 4);
            }
        }

        try {
            $xml = new SimpleXMLElement($inputXML);
        } catch (Exception $error) {
            throw new WebserviceException('XML error : '.$error->getMessage()."\n".'XML length : '.Tools::strlen($inputXML)."\n".'Original XML : '.$inputXML, array(67, 405));
        }

        $xmlEntities = $xml->children();
        // Convert multi-dimention xml into an array
        $array = Tools::jsonDecode(Tools::jsonEncode($xmlEntities), true);

        return ($head ? $array[$head] : $array);
    }

    public function getAllHeaders()
    {
        $retarr = array();
        $headers = array();

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = array_merge($_ENV, $_SERVER);
            foreach ($headers as $key => $val) {
                //we need this header
                if (strpos(Tools::strtolower($key), 'content-type') !== false) {
                    continue;
                }
                if (Tools::strtoupper(Tools::substr($key, 0, 5)) != 'HTTP_') {
                    unset($headers[$key]);
                }
            }
        }
        //Normalize this array to Cased-Like-This structure.
        foreach ($headers as $key => $value) {
            $key = preg_replace('/^HTTP_/i', '', $key);
            $key = str_replace(' ', '-', ucwords(Tools::strtolower(str_replace(array('-', '_'), ' ', $key))));
            $retarr[$key] = $value;
        }
        ksort($retarr);
        return $retarr;
    }
}
