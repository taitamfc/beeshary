<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class WebserviceSpecificManagementArtisans implements WebserviceSpecificManagementInterface
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

        if (!isset($this->wsObject->urlFragments['partner_id']) || !isset($this->wsObject->urlFragments['partner_id'])) {
            throw new WebserviceException('You have to set the \'partner_id\' parameter to get a result', array(400, 400));
        }
	$partnerId = $this->wsObject->urlFragments['partner_id'];

	$savoirFaireId = null;

        if (isset($this->wsObject->urlFragments['savoir'])) {
            $savoirFaireId = $this->wsObject->urlFragments['savoir'];
            $query = sprintf("
          select s.*, b.*, sl.*, count(DISTINCT pr.id_mp_product) as total_products from %swk_mp_seller s
              left join %smp_seller_badges b on b.mp_seller_id=s.id_seller
              left join %swk_mp_seller_lang sl on sl.id_seller=s.id_seller
              left join %swk_mp_seller_product pr on pr.id_seller=s.id_seller
              left join %smarketplace_extrafield_value extra on extra.mp_id_seller = s.id_seller
          where b.badge_id=%s and sl.id_lang=1 and extra.field_value=%s group by s.id_seller",
                _DB_PREFIX_,_DB_PREFIX_,_DB_PREFIX_,_DB_PREFIX_, _DB_PREFIX_, $partnerId, $savoirFaireId);
        } else {
            $query = sprintf("
          select s.*, b.*, sl.*, count(DISTINCT pr.id_mp_product) as total_products from %swk_mp_seller s
              left join %smp_seller_badges b on b.mp_seller_id=s.id_seller
              left join %swk_mp_seller_lang sl on sl.id_seller=s.id_seller
              left join %swk_mp_seller_product pr on pr.id_seller=s.id_seller
          where b.badge_id=%s and sl.id_lang=1 group by s.id_seller",
                _DB_PREFIX_,_DB_PREFIX_,_DB_PREFIX_, _DB_PREFIX_, $partnerId);
        }
       /* $query = sprintf("
          select * from %swk_mp_seller s left join ps_mp_seller_badges b on b.mp_seller_id=s.id_seller left join ps_wk_mp_seller_lang sl on sl.id_seller=s.id_seller
          where b.badge_id=%s and sl.id_lang=1",
            _DB_PREFIX_, $partnerId);
	*/
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
