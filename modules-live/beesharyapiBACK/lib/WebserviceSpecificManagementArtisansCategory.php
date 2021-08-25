<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class WebserviceSpecificManagementArtisansCategory implements WebserviceSpecificManagementInterface
{


    /** @var WebserviceOutputBuilder */

    protected $objOutput;

    protected $output;


    /** @var WebserviceRequest */

    protected $wsObject;
    protected $urlSegment;


    public function setUrlSegment($segments)
    {

        $this->urlSegment = $segments;

        return $this;

    }


    public function getUrlSegment()

    {

        return $this->urlSegment;


    }

    public function getWsObject()

    {

        return $this->wsObject;

    }


    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        // TODO: Implement setObjectOutput() method.
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


    public function manage()
    {
//        $sql = new DbQuery();
//        $sql->select('*');
//        $sql->from('ps_wk_mp_seller');

        $test =  Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller` '
        );
//        var_dump($test);exit;
        $this->output= json_encode($test);
//        var_dump($this->output);exit;
        return;
        $test =  Db::getInstance()->executeS($sql);
        // TODO: Implement manage() method.
        $objects_products = array();

        $objects_products['empty'] = new Customer();

        $customer_list = Customer::getCustomers();

        foreach ($customer_list as $list) {

            $objects_products[] = new Customer($list['id_customer']);

        }

        $this->_resourceConfiguration = $objects_products['empty']->getWebserviceParameters();

        $this->output .= $this->objOutput->getContent(
            $objects_products,
            null,
            $this->wsObject->fieldsToDisplay,
            $this->wsObject->depth,
            WebserviceOutputBuilder::VIEW_LIST,
            false
        );
    }

    /**
     * This must be return an array with specific values as WebserviceRequest expects.
     *
     * @return array
     */
    public function getContent()
    {
            return 'toot';

        return $this->objOutput->getObjectRender()->overrideContent($this->output);
        // TODO: Implement getContent() method.
    }
}