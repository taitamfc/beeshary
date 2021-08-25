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

class MpShippingImpact extends ObjectModel
{
    public $id;
    public $mp_shipping_id;
    public $shipping_delivery_id;
    public $id_zone;
    public $id_country;
    public $id_state;
    public $impact_price;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_shipping_impact',
        'primary' => 'id',
        'fields' => array(
            'mp_shipping_id' => array('type' => self::TYPE_INT, 'required' => true),
            'shipping_delivery_id' => array('type' => self::TYPE_INT, 'required' => true),
            'id_zone' => array('type' => self::TYPE_INT, 'required' => true),
            'id_country' => array('type' => self::TYPE_INT, 'required' => true),
            'id_state' => array('type' => self::TYPE_INT),
            'impact_price' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function getImpactPriceWithRange($totalOrderPriceWt, $mpShippingId, $idZone, $idCountry, $idState, $byPrice = 0, $noState = 0)
    {
        if ($byPrice) {
            $shipDelId = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id`='.$mpShippingId.' AND `id_zone`='.$idZone.' AND `mp_id_range_price`= (SELECT `id` FROM `'._DB_PREFIX_.'mp_range_price` WHERE `mp_shipping_id`='.$mpShippingId.' AND `delimiter1`<='.$totalOrderPriceWt.' AND `delimiter2`>'.$totalOrderPriceWt.')');
        } else {
            $shipDelId = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id`='.$mpShippingId.' AND `id_zone`='.$idZone.' AND `mp_id_range_weight`= (SELECT `id` FROM `'._DB_PREFIX_.'mp_range_weight` WHERE `mp_shipping_id`='.$mpShippingId.' AND `delimiter1`<='.$totalOrderPriceWt.' AND `delimiter2`>'.$totalOrderPriceWt.')');
        }

        if ($noState) {
            $sql = 'SELECT `impact_price` FROM `'._DB_PREFIX_.'mp_shipping_impact` WHERE `mp_shipping_id`='.$mpShippingId.' AND `id_zone`='.$idZone.' AND `id_country`='.(int) $idCountry.' AND `shipping_delivery_id`='.(int) $shipDelId;
        } else {
            $curIdState = Db::getInstance()->getValue('SELECT `id_state` FROM `'._DB_PREFIX_.'mp_shipping_impact` WHERE `mp_shipping_id`='.$mpShippingId.' AND `id_zone`='.$idZone.' AND `id_country`='.(int) $idCountry.' AND `id_state`='.(int) $idState.' AND `shipping_delivery_id`='.(int) $shipDelId);

            if ($curIdState && $curIdState == $idState) {
                $sql = 'SELECT `impact_price` FROM `'._DB_PREFIX_.'mp_shipping_impact` WHERE `mp_shipping_id`='.$mpShippingId.' AND `id_zone`='.$idZone.' AND `id_country`='.(int) $idCountry.' AND `id_state`='.(int) $idState.' AND `shipping_delivery_id`='.(int) $shipDelId;
            } else {
                $sql = 'SELECT `impact_price` FROM `'._DB_PREFIX_.'mp_shipping_impact` WHERE `mp_shipping_id`='.$mpShippingId.' AND `id_zone`='.$idZone.' AND `id_country`='.(int) $idCountry.' AND `id_state`=0 AND `shipping_delivery_id`='.(int) $shipDelId;
            }
        }
        $result = Db::getInstance()->getValue($sql);
        if ($result) {
            return $result;
        }

        return 0;
    }

    public function getCountriesByZoneId($idZone, $idLang)
    {
        $sql = 'SELECT DISTINCT c.id_country, cl.name
				FROM `'._DB_PREFIX_.'country` c
				'.Shop::addSqlAssociation('country', 'c', false).'
				LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_country` = c.`id_country`)
				LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
				WHERE (c.`id_zone` = '.(int) $idZone.' OR s.`id_zone` = '.(int) $idZone.')
				AND cl.`id_lang` = '.(int) $idLang.' AND c.`active` = 1';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getStatesByIdCountry($idCountry)
    {
        if (empty($idCountry)) {
            die(Tools::displayError());
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT s.`id_state`, s.`name` FROM `'._DB_PREFIX_.'state` s WHERE s.`id_country` = '.(int) $idCountry.' AND s.`active` = 1');
    }

    public function isAllReadyInImpact($mpShippingId, $shippingDeliveryId, $idZone, $idCountry, $idState)
    {
        $isExistImpact = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_.'mp_shipping_impact WHERE mp_shipping_id = '.(int) $mpShippingId.' AND shipping_delivery_id = '.(int) $shippingDeliveryId.' AND id_zone='.$idZone.' AND id_country = '.(int) $idCountry.' AND id_state = '.(int) $idState);

        if (empty($isExistImpact)) {
            return false;
        } else {
            return $isExistImpact;
        }
    }

    public static function getAllImpactPriceByMpshippingid($mpShippingId)
    {
        $allImpactPrice = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'mp_shipping_impact WHERE mp_shipping_id = '.(int) $mpShippingId);

        if ($allImpactPrice) {
            return $allImpactPrice;
        } else {
            return false;
        }
    }

    public static function getZonenameByZoneid($idZone)
    {
        $allZone = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_.'zone WHERE id_zone = '.(int) $idZone);

        if ($allZone) {
            return $allZone;
        } else {
            return false;
        }
    }

    public static function updateImpactAfterUpdateShipping($mpShippingId, $firstDelId, $firstRangeId, $rangeType)
    {
        $allImpc = self::getAllImpactPriceByMpshippingid($mpShippingId);
        if ($allImpc) {
            foreach ($allImpc as $valIpc) {
                $objShpDel = new MpShippingDelivery($valIpc['shipping_delivery_id']);
                $mpRangeWeightId = $objShpDel->mp_id_range_weight;
                $mpRangePriceId = $objShpDel->mp_id_range_price;

                if ($mpRangePriceId) {
                    if ($rangeType != 2) {
                        Db::getInstance()->delete('mp_range_price', 'mp_shipping_id = '.(int) $mpShippingId);
                        Db::getInstance()->delete('mp_shipping_impact', 'mp_shipping_id = '.(int) $mpShippingId);
                        break;
                    }
                    $objRngPrc = new MpRangePrice($mpRangePriceId);
                    if (isset($objRngPrc->delimiter1)) {
                        $delm1 = $objRngPrc->delimiter1;
                        $delm2 = $objRngPrc->delimiter2;
                        $nwRngPrcId = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_range_price` WHERE `delimiter2` != 0 AND  `delimiter1`>='.$delm1.' AND `delimiter2`<='.$delm2.' AND `id`>='.$firstRangeId);
                    }
                    if (isset($nwRngPrcId) && $nwRngPrcId) {
                        $nwShpDelId = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id`='.$mpShippingId.' AND `id_zone`='.$valIpc['id_zone'].' AND `mp_id_range_price`='.$nwRngPrcId);
                        if ($nwShpDelId) {
                            $allUpdate = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'mp_shipping_impact SET `shipping_delivery_id` = '.(int) $nwShpDelId.' WHERE `id` = '.(int) $valIpc['id']);
                        } else {
                            $objImpc = new self($valIpc['id']);
                            $objImpc->delete();
                        }
                    } else {
                        $objImpc = new self($valIpc['id']);
                        $objImpc->delete();
                    }
                } elseif ($mpRangeWeightId) {
                    if ($rangeType != 1) {
                        Db::getInstance()->delete('mp_range_weight', 'mp_shipping_id = '.(int) $mpShippingId);
                        Db::getInstance()->delete('mp_shipping_impact', 'mp_shipping_id = '.(int) $mpShippingId);
                        break;
                    }
                    $objRngPrc = new MpRangeWeight($mpRangeWeightId);
                    $delm1 = $objRngPrc->delimiter1;
                    $delm2 = $objRngPrc->delimiter2;
                    $nwRngWeightId = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_range_weight` WHERE `delimiter2` != 0 AND `delimiter1`>='.$delm1.' AND `delimiter2`<='.$delm2.' AND `id`>='.$firstRangeId);
                    if ($nwRngWeightId) {
                        $nwShpDelId = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id`='.$mpShippingId.' AND `id_zone`<='.$valIpc['id_zone'].' AND `mp_id_range_weight`='.$nwRngWeightId);
                        if ($nwShpDelId) {
                            $allUpdate = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'mp_shipping_impact SET `shipping_delivery_id` = '.(int) $nwShpDelId.' WHERE `id` = '.(int) $valIpc['id']);
                        } else {
                            $objImpc = new self($valIpc['id']);
                            $objImpc->delete();
                        }
                    } else {
                        $objImpc = new self($valIpc['id']);
                        $objImpc->delete();
                    }
                } else {
                    $objImpc = new self($valIpc['id']);
                    $objImpc->delete();
                }
            }
        }
        if ($rangeType == 2) {
            Db::getInstance()->delete('mp_range_price', 'mp_shipping_id = '.(int) $mpShippingId.' AND id < '.(int) $firstRangeId);
        } elseif ($rangeType == 1) {
            Db::getInstance()->delete('mp_range_weight', 'mp_shipping_id = '.(int) $mpShippingId.' AND id < '.(int) $firstRangeId);
        }
        Db::getInstance()->delete('mp_shipping_delivery', 'mp_shipping_id = '.(int) $mpShippingId.' AND id < '.(int) $firstDelId);
        if (isset($allUpdate) && $allUpdate) {
            return true;
        } else {
            return false;
        }
    }
}
