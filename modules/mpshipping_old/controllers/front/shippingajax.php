<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpShippingShippingAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $fun = Tools::getValue('fun');
        if ($fun == 'find_country') {
            $idZone = Tools::getValue('id_zone');
            $countryDetail = $this->findCountry($idZone);
            $jsonArrayRev = Tools::jsonEncode($countryDetail);
            echo $jsonArrayRev;
            die; //ajax close
        } elseif ($fun == 'find_state') {
            $idCountry = Tools::getValue('id_country');
            $stateDetail = $this->findState($idCountry);
            $jsonArrayRev = Tools::jsonEncode($stateDetail);
            echo $jsonArrayRev;
            die; //ajax close
        } elseif ($fun == 'find_range') {
            $idZone = Tools::getValue('id_zone');
            $idCountry = Tools::getValue('id_country');
            $idState = Tools::getValue('id_state');
            $shippingMethod = Tools::getValue('shipping_method');
            $mpShippingId = Tools::getValue('mpshipping_id');

            //shipping_method 2 for price
            //shipping_method 1 for weight
            if ($shippingMethod == 2) {
                $deliveryMethod = $this->rangeByPrice($idZone, $mpShippingId);
                if ($deliveryMethod) {
                    $currentPrice = $this->CurrentPrice($deliveryMethod, $idZone, $idCountry, $idState, $mpShippingId);
                    $jsonArrayRev = Tools::jsonEncode($currentPrice);
                    echo $jsonArrayRev;
                } else {
                    echo 0;
                }
            } elseif ($shippingMethod == 1) {
                $deliveryMethod = $this->rangeByWeight($idZone, $mpShippingId);
                if ($deliveryMethod) {
                    $currentPrice = $this->CurrentPrice($deliveryMethod, $idZone, $idCountry, $idState, $mpShippingId);
                    $jsonArrayRev = Tools::jsonEncode($currentPrice);
                    echo $jsonArrayRev;
                } else {
                    echo 0;
                }
            }
            die; //ajax close
        } elseif ($fun == 'range_add') {
            $mpShippingId = Tools::getValue('range_mpshipping_id');
            $idZone = Tools::getValue('range_mpshipping_id_zone');
            $idCountry = Tools::getValue('range_mpshipping_id_country');
            $idState = Tools::getValue('range_mpshipping_id_state');
            $shippingMethod = Tools::getValue('range_shipping_method');
            //$idState = 0 for all
            if ($shippingMethod == 2) {
                $deliveryMethod = $this->rangeByPrice($idZone, $mpShippingId);
                if ($deliveryMethod) {
                    $this->impactEntry($deliveryMethod, $idZone, $idCountry, $idState, $mpShippingId);
                    $success = 1;
                } else {
                    $success = 0;
                }
            } elseif ($shippingMethod == 1) {
                $deliveryMethod = $this->rangeByWeight($idZone, $mpShippingId);
                if ($deliveryMethod) {
                    $this->impactEntry($deliveryMethod, $idZone, $idCountry, $idState, $mpShippingId);
                    $success = 1;
                } else {
                    $success = 0;
                }
            }

            echo $success;
            die; //ajax close
        }
    }

    public function findCountry($idZone)
    {
        $objShippingImp = new MpShippingImpact();

        return $objShippingImp->getCountriesByZoneId($idZone, $this->context->language->id);
    }

    public function findState($idCountry)
    {
        $objShippingImp = new MpShippingImpact();

        return $objShippingImp->getStatesByIdCountry($idCountry);
    }

    public function rangeByPrice($idZone, $mpShippingId)
    {
        $objMpShippingDel = new MpShippingDelivery();
        $deliveryMethod = $objMpShippingDel->getDliveryMethodForPriceRange($idZone, $mpShippingId);
        if ($deliveryMethod) {
            return $deliveryMethod;
        } else {
            return false;
        }
    }

    public function rangeByWeight($idZone, $mpShippingId)
    {
        $objMpShippingDel = new MpShippingDelivery();
        $deliveryMethod = $objMpShippingDel->getDliveryMethodForWeightRange($idZone, $mpShippingId);
        if ($deliveryMethod) {
            return $deliveryMethod;
        } else {
            return false;
        }
    }

    public function impactEntry($deliveryMethod, $idZone, $idCountry, $idState, $mpShippingId)
    {
        $objShippingImp = new MpShippingImpact();
        $objShippingImp->mp_shipping_id = $mpShippingId;
        $objShippingImp->id_zone = $idZone;
        $objShippingImp->id_country = $idCountry;
        $objShippingImp->id_state = $idState;

        foreach ($deliveryMethod as $deliveryMe) {
            $shippingDeliveryId = $deliveryMe['id'];
            $objShippingImp->shipping_delivery_id = $shippingDeliveryId;
            $newImpactPrice = Tools::getValue('delivery'.$shippingDeliveryId);
            $objShippingImp->impact_price = (float) $newImpactPrice;

            $isExistImpact = $objShippingImp->isAllReadyInImpact($mpShippingId, $shippingDeliveryId, $idZone, $idCountry, $idState);
            if ($isExistImpact) {
                $objShippingImp->id = $isExistImpact['id'];
                $objShippingImp->save();
            } else {
                $objShippingImp->add();
            }
        }

        return true;
    }

    //find current impact price by zone and delivery method
    public function CurrentPrice($deliveryMethod, $idZone, $idCountry, $idState, $mpShippingId)
    {
        $currentPriceArray = array();
        foreach ($deliveryMethod as $deliveryMe) {
            $shippingDeliveryId = $deliveryMe['id'];
            $delimiter1 = $deliveryMe['delimiter1'];
            $delimiter2 = $deliveryMe['delimiter2'];
            $idRange = $deliveryMe['id_range'];
            if ($idRange) {
                $mpShippingImpact = new MpShippingImpact();
                $isInImpact = $mpShippingImpact->isAllReadyInImpact($mpShippingId, $shippingDeliveryId, $idZone, $idCountry, $idState);

                if ($isInImpact) {
                    $impactPrice = $isInImpact['impact_price'];
                } else {
                    $impactPrice = 0;
                }

                $currentPriceArray[] = array('id' => $shippingDeliveryId,'delimiter1' => $delimiter1,'delimiter2' => $delimiter2,'id_range' => $idRange,'impact_price' => $impactPrice);
            }
        }

        return $currentPriceArray;
    }
}
