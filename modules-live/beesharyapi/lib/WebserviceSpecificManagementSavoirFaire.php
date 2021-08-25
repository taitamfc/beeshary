<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class WebserviceSpecificManagementSavoirFaire implements WebserviceSpecificManagementInterface
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
        $query = sprintf("
          select eo.id, eo.extrafield_id, ea.attribute_name, eol.display_value from %smarketplace_extrafield_options eo
            left join %smarketplace_extrafield_association ea on ea.extrafield_id=eo.extrafield_id
            left join %smarketplace_extrafield_options_lang eol on eol.id=eo.id
            where ea.attribute_name='pp_theme' and eol.id_lang=1 order by eol.display_value",
            _DB_PREFIX_,_DB_PREFIX_,_DB_PREFIX_);

        $result = Db::getInstance()->executeS($query);
        $this->output= json_encode($result);
        return;
    }

    /**
     * This must be return an array with specific values as WebserviceRequest expects.
     *
     * @return array
     */
    public function getContent()
    {
        return $this->output;
    }
}
