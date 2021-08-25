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

class MpShippingMethod extends ObjectModel
{
    public $id;
    public $mp_shipping_name;
    public $shipping_method;
    public $mp_id_seller;
    public $id_tax_rule_group;
    public $id_ps_reference;
    public $range_behavior;
    public $tracking_url;
    public $max_width;
    public $max_height;
    public $max_depth;
    public $max_weight;
    public $grade;
    public $is_free;
    public $deleted;
    public $active;
    public $is_done = 0;
    public $shipping_handling;
    public $is_default_shipping;
    public $date_add;
    public $date_upd;

    public $transit_delay;

    public static $definition = array(
        'table' => 'mp_shipping_method',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'mp_shipping_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCarrierName', 'required' => true, 'size' => 64),
            'shipping_method' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'mp_id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_ps_reference' => array('type' => self::TYPE_INT),
            'id_tax_rule_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'range_behavior' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'tracking_url' => array('type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'),
            'max_width' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'max_height' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'max_depth' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'max_weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'grade' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 1),
            'is_free' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL),
            'is_done' => array('type' => self::TYPE_BOOL),
            'shipping_handling' => array('type' => self::TYPE_BOOL),
            'is_default_shipping' => array('type' => self::TYPE_BOOL),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),

            'transit_delay' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
        ),
    );

    public function deleteMpShipping($idShipping)
    {
        $objMpShipping = new self($idShipping);
        $objMpShipProductMap = new MpShippingProductMap();

        $idPsReference = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_ps_reference` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id` = '.(int) $idShipping);
        if ($idPsReference) {
            $del = Db::getInstance()->update('carrier', array('deleted' => 1), 'id_reference = '.$idPsReference);
            if ($del) {
                $objMpShipping->delete();
            }
        } else {
            $objMpShipping->delete();
        }

        $mpShippingProdMap = $objMpShipProductMap->getMpShippingForProducts($idShipping);
        if ($mpShippingProdMap) {
            Db::getInstance()->delete('mp_shipping_product_map', 'mp_shipping_id = '.$idShipping);
        }
    }

    public static function getSellerAllShippingMethod($mpIdSeller)
    {
        $mpShippingDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `mp_id_seller` = '.(int) $mpIdSeller);

        if (empty($mpShippingDetail)) {
            return false;
        } else {
            return $mpShippingDetail;
        }
    }

    public function getAllShippingMethodNotDelete($mpIdSeller, $delete, $isDone = 1)
    {
        $mpShippingDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `mp_id_seller` = '.(int) $mpIdSeller.' AND `deleted` = '.(int) $delete.' AND `is_done` = '.(int) $isDone);

        if (empty($mpShippingDetail)) {
            return false;
        } else {
            return $mpShippingDetail;
        }
    }

    public function addToCarrier($mpShippingId)
    {
        $objMpShipping = new self($mpShippingId);

        $objCarrier = new Carrier();
        $objCarrier->name = $objMpShipping->mp_shipping_name;
        $objCarrier->active = $objMpShipping->active;
        $objCarrier->url = $objMpShipping->tracking_url;
        $objCarrier->range_behavior = $objMpShipping->range_behavior;
        $objCarrier->position = Carrier::getHigherPosition() + 1;
        $objCarrier->shipping_method = $objMpShipping->shipping_method;
        $objCarrier->max_width = $objMpShipping->max_width;
        $objCarrier->max_height = $objMpShipping->max_height;
        $objCarrier->max_depth = $objMpShipping->max_depth;
        $objCarrier->max_weight = $objMpShipping->max_weight;
        $objCarrier->grade = $objMpShipping->grade;
        $objCarrier->shipping_handling = $objMpShipping->shipping_handling;
        $objCarrier->shipping_external = true;
        $objCarrier->external_module_name = 'mpshipping';
        $objCarrier->is_module = 1;
        $objCarrier->need_range = 1;

        if ($objMpShipping->is_free) {
            $objCarrier->is_free = 1;
        }
        $mpShipInfo = array();
        $mpShipLangInfoArr = $this->getMarketPlaceShippingLangInfo($mpShippingId);
        foreach ($mpShipLangInfoArr as $mpShipLangInfo) {
            $mpShipInfo['delay'][$mpShipLangInfo['id_lang']] = $mpShipLangInfo['transit_delay'];
        }

        foreach (Language::getLanguages(true) as $lang) {
            $objCarrier->delay[$lang['id_lang']] = $mpShipInfo['delay'][$lang['id_lang']];
        }
        $objCarrier->save();
        $idPsCarrier = $objCarrier->id; //First time idPsCarrier and id_reference both are same

        if ($objMpShipping->shipping_method == 2) {
            $this->addRangePrice($mpShippingId, $idPsCarrier); //range price
        } else {
            $this->addRangeWeight($mpShippingId, $idPsCarrier); //range weight
        }

        $this->addZone($mpShippingId, $idPsCarrier);
        $this->updateZoneShop($idPsCarrier);
        $this->addCarrierTaxRule($idPsCarrier, $objMpShipping->id_tax_rule_group);
        $this->addCarrierGroup($idPsCarrier, $mpShippingId);

        return $idPsCarrier;
    }

    public function getMarketPlaceShippingLangInfo($id)
    {
        $marketplaceShippingInfo = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method_lang` WHERE `id` = '.$id);

        if (!empty($marketplaceShippingInfo)) {
            return $marketplaceShippingInfo;
        } else {
            return false;
        }
    }

    public function updateToCarrier($mpShippingId, $idReference)
    {
        //Delete Carrier
        Db::getInstance()->update('carrier', array('deleted' => 1), 'id_reference='.$idReference);

        $objMpShipping = new self($mpShippingId);
        $objCarrier = new Carrier();
        $objCarrier->name = $objMpShipping->mp_shipping_name;
        $objCarrier->active = $objMpShipping->active;
        $objCarrier->url = $objMpShipping->tracking_url;
        $objCarrier->range_behavior = $objMpShipping->range_behavior;
        $objCarrier->position = Carrier::getHigherPosition() + 1;
        $objCarrier->shipping_method = $objMpShipping->shipping_method;
        $objCarrier->max_width = $objMpShipping->max_width;
        $objCarrier->max_height = $objMpShipping->max_height;
        $objCarrier->max_depth = $objMpShipping->max_depth;
        $objCarrier->max_weight = $objMpShipping->max_weight;
        $objCarrier->grade = $objMpShipping->grade;
        $objCarrier->shipping_handling = $objMpShipping->shipping_handling;
        $objCarrier->shipping_external = true;
        $objCarrier->external_module_name = 'mpshipping';
        $objCarrier->is_module = 1;
        $objCarrier->need_range = 1;

        if ($objMpShipping->is_free) {
            $objCarrier->is_free = 1;
        } else {
            $objCarrier->is_free = 0;
        }

        $mpShipLangInfoArr = $this->getMarketPlaceShippingLangInfo($mpShippingId);
        $mpShipInfo = array();
        foreach ($mpShipLangInfoArr as $mpShipLangInfo) {
            $mpShipInfo['delay'][$mpShipLangInfo['id_lang']] = $mpShipLangInfo['transit_delay'];
        }

        foreach (Language::getLanguages(true) as $lang) {
            $objCarrier->delay[$lang['id_lang']] = $mpShipInfo['delay'][$lang['id_lang']];
        }

        $objCarrier->save();
        $idPsCarriers = $objCarrier->id;

        if ($objMpShipping->shipping_method == 2) {
            //range price
            Db::getInstance()->delete('range_price', 'id_carrier = '.(int) $idPsCarriers);
            $this->addRangePrice($mpShippingId, $idPsCarriers);
        } else {
            //range weight
            Db::getInstance()->delete('range_weight', 'id_carrier = '.(int) $idPsCarriers);
            $this->addRangeWeight($mpShippingId, $idPsCarriers);
        }

        //Delete data from table before adding new data
        Db::getInstance()->delete('carrier_zone', 'id_carrier='.(int) $idPsCarriers);
        $this->addZone($mpShippingId, $idPsCarriers);

        $this->updateZoneShop($idPsCarriers);

        Db::getInstance()->delete('carrier_tax_rules_group_shop', 'id_carrier = '.(int) $idPsCarriers);
        $this->addCarrierTaxRule($idPsCarriers, $objMpShipping->id_tax_rule_group);

        Db::getInstance()->delete('carrier_group', 'id_carrier = '.(int) $idPsCarriers);
        $this->addCarrierGroup($idPsCarriers, $mpShippingId);

        if ($idReference) {
            $newObjCarrier = new Carrier($idPsCarriers);
            $newObjCarrier->id_reference = $idReference;
            $newObjCarrier->save();
        }

        return $idPsCarriers;
    }

    public function addRangePrice($mpShippingId, $idCarrier)
    {
        $objMpRange = new Mprangeprice();
        $objMpRange->mp_shipping_id = $mpShippingId;
        $rangeDetailInfo = $objMpRange->getAllRangeAccordingToShippingId();
        if ($rangeDetailInfo) {
            foreach ($rangeDetailInfo as $rangeDetail) {
                Db::getInstance()->insert('range_price', array(
                                        'id_carrier' => (int) $idCarrier,
                                        'delimiter1' => $rangeDetail['delimiter1'],
                                        'delimiter2' => $rangeDetail['delimiter2'],
                                    ));
                $rangePriceInsertId = Db::getInstance()->Insert_ID();
                $this->addDelivery($idCarrier, $mpShippingId, $rangePriceInsertId, $rangeDetail['id_range'], true);
            }
        }

        return true;
    }

    public function addRangeWeight($mpShippingId, $idCarrier)
    {
        $objMpRange = new MpRangeWeight();
        $objMpRange->mp_shipping_id = $mpShippingId;
        $rangeDetailInfo = $objMpRange->getAllRangeAccordingToShippingId();
        if ($rangeDetailInfo) {
            foreach ($rangeDetailInfo as $rangeDetail) {
                Db::getInstance()->insert('range_weight', array(
                                        'id_carrier' => (int) $idCarrier,
                                        'delimiter1' => $rangeDetail['delimiter1'],
                                        'delimiter2' => $rangeDetail['delimiter2'],
                                    ));
                $rangeWeightInsertId = Db::getInstance()->Insert_ID();
                $this->addDelivery($idCarrier, $mpShippingId, $rangeWeightInsertId, $rangeDetail['id_range'], false);
            }
        }

        return true;
    }

    public function addZone($mpShippingId, $idCarrier)
    {
        $objMpDel = new MpShippingDelivery();
        $idZoneDetails = $objMpDel->getIdZoneByShiipingId($mpShippingId);

        if ($idZoneDetails) {
            foreach ($idZoneDetails as $idZoneDetail) {
                Db::getInstance()->insert('carrier_zone', array(
                                    'id_carrier' => (int) $idCarrier,
                                    'id_zone' => (int) $idZoneDetail['id_zone'],
                                ));
            }
        }

        return true;
    }

    public function updateZoneShop($idCarrier)
    {
        return Db::getInstance()->update('carrier_shop', array('id_shop' => (int) Context::getContext()->shop->id), 'id_carrier ="'.(int) $idCarrier.'" ');
    }

    public function addCarrierTaxRule($idCarrier, $idTaxRuleGroup)
    {
        return Db::getInstance()->insert('carrier_tax_rules_group_shop', array(
                                    'id_carrier' => (int) $idCarrier,
                                    'id_tax_rules_group' => $idTaxRuleGroup,
                                    'id_shop' => (int) Context::getContext()->shop->id,
                                ));
    }

    public function addCarrierGroup($idCarrier, $mpShippingId)
    {
        $groupDetails = self::getShippingGroup($mpShippingId);
        if ($groupDetails) {
            foreach ($groupDetails as $groupDetail) {
                Db::getInstance()->insert('carrier_group', array(
                                        'id_carrier' => (int) $idCarrier,
                                        'id_group' => $groupDetail['id_group'],
                                    ));
            }
        }

        return true;
    }

    public function addDelivery($idCarrier, $mpShippingId, $idRange, $mpIdRange, $isPriceRange = false)
    {
        $objMpShippingDel = new MpShippingDelivery();
        if ($isPriceRange) {
            $deliveryDetailInfo = $objMpShippingDel->getDeliveryBySIdAndRpId($mpShippingId, $mpIdRange);
            if ($deliveryDetailInfo) {
                foreach ($deliveryDetailInfo as $deliveryDetail) {
                    Db::getInstance()->insert('delivery', array(
                                        'id_carrier' => $idCarrier,
                                        'id_range_price' => $idRange,
                                        'id_zone' => $deliveryDetail['id_zone'],
                                        'price' => $deliveryDetail['base_price'],
                                    ));
                }
            }
        } else {
            $deliveryDetailInfo = $objMpShippingDel->getDeliveryBySIdAndRwId($mpShippingId, $mpIdRange);
            if ($deliveryDetailInfo) {
                foreach ($deliveryDetailInfo as $deliveryDetail) {
                    Db::getInstance()->insert('delivery', array(
                                        'id_carrier' => $idCarrier,
                                        'id_range_weight' => $idRange,
                                        'id_zone' => $deliveryDetail['id_zone'],
                                        'price' => $deliveryDetail['base_price'],
                                    ));
                }
            }
        }

        return true;
    }

    public function getMpShippingMethods($mpIdSeller)
    {
        $mpShippingData = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `mp_id_seller` = '.(int) $mpIdSeller.' AND `deleted` = 0 AND `active` = 1 AND `is_done` = 1');

        if (empty($mpShippingData)) {
            return false;
        } else {
            return $mpShippingData;
        }
    }

    public static function updateDefaultShipping($mpShippingId, $isDefaultShipping)
    {
        return Db::getInstance()->update('mp_shipping_method', array('is_default_shipping' => (int) $isDefaultShipping), '`id` = '.(int) $mpShippingId);
    }

    public function getDefaultMpShippingMethods($mpIdSeller)
    {
        $mpShippingData = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `mp_id_seller` = '.(int) $mpIdSeller.' AND `deleted` = 0 AND `active` = 1 AND `is_done` = 1 AND `is_default_shipping` = 1');

        if (empty($mpShippingData)) {
            return false;
        } else {
            return $mpShippingData;
        }
    }

    public function getMpShippingPsShopId($mpShippingId)
    {
        $psShopId = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_ps_shop` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id` = '.(int) $mpShippingId.'');

        if (empty($psShopId)) {
            return false;
        } else {
            return $psShopId;
        }
    }

    public function insertProductCarrierDetails($idProduct, $idCarrierReference, $idShop)
    {
        $psProductCarrierDetails = Db::getInstance()->insert('product_carrier', array(
                                            'id_product' => (int) $idProduct,
                                            'id_carrier_reference' => (int) $idCarrierReference,
                                            'id_shop' => (int) $idShop,

                                        ));

        return $psProductCarrierDetails;
    }

    public function getAdminShippingMethods()
    {
        $psCarriers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'carrier` WHERE `deleted` = 0 and `active` = 1');

        if (empty($psCarriers)) {
            return false;
        } else {
            return $psCarriers;
        }
    }

    public function getAllProducts($mpIdSeller)
    {
        $mpProducts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_product`
		WHERE `id_seller` = '.$mpIdSeller);

        if (empty($mpProducts)) {
            return false;
        } else {
            return $mpProducts;
        }
    }

    /* ----- After delete mp_shipping_map table -----*/

    public function getAllReferenceId()
    {
        $idPsReference = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_ps_reference` FROM `'._DB_PREFIX_.'mp_shipping_method`');

        if (empty($idPsReference)) {
            return false;
        } else {
            return $idPsReference;
        }
    }

    public static function getMpShippingId($idPsReference)
    {
        $mpShippingId = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE id_ps_reference = '.(int) $idPsReference);

        if (empty($mpShippingId)) {
            return false;
        } else {
            return $mpShippingId;
        }
    }

    public static function getReferenceByMpShippingId($mpShippingId)
    {
        $idPsReference = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_ps_reference` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id` = '.(int) $mpShippingId);

        if (empty($idPsReference)) {
            return false;
        } else {
            return $idPsReference;
        }
    }

    public static function getCarrierIdByReference($idPsReference)
    {
        $idCarrier = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_.'carrier`
			WHERE `id_reference` = '.(int) $idPsReference.' AND deleted = 0 ORDER BY id_carrier DESC');

        if (empty($idCarrier)) {
            return false;
        } else {
            return $idCarrier;
        }
    }

    public static function deletePriceWeightDeliveryById($mpShippingId)
    {
        Db::getInstance()->delete('mp_range_price', 'mp_shipping_id = '.(int) $mpShippingId);
        Db::getInstance()->delete('mp_range_weight', 'mp_shipping_id = '.(int) $mpShippingId);
        Db::getInstance()->delete('mp_shipping_delivery', 'mp_shipping_id = '.(int) $mpShippingId);

        return true;
    }

    public static function getOnlyPrestaCarriers($idLang)
    {
        $carrDetailsFinal = Carrier::getCarriers($idLang, true, false, false, null, ALL_CARRIERS);
        if (!$carrDetailsFinal) {
            return false;
        }

        $onlyPsCarriers = array();
        if ($carrDetailsFinal) {
            foreach ($carrDetailsFinal as $carrVal) {
                $mpCarrier = Db::getInstance()->getRow('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id_ps_reference` = '.(int) $carrVal['id_reference']);

                if (empty($mpCarrier)) {
                    $onlyPsCarriers[] = $carrVal;
                }
            }
        }

        return $onlyPsCarriers;
    }

    public function isSellerShippingByIdReference($psIdRef)
    {
        $isSellerShipping = Db::getInstance()->getRow('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id_ps_reference` = '.(int) $psIdRef);
        if ($isSellerShipping) {
            return true;
        }

        return false;
    }

    public function updateCarriersOnDeactivateOrDelete()
    {
        $adminDefShipping = Configuration::get('MP_SHIPPING_ADMIN_DEFAULT');
        /*Assign new selected shipping methods to the seller produccts which have no seller shipping methods*/
        $allSellerAdminProducts = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_ps_product`!= 0');
        if ($allSellerAdminProducts) {
            $objMpShpMthod = new self();
            foreach ($allSellerAdminProducts as $valProd) {
                $prodObj = new Product($valProd['id_ps_product']);
                $carriersLst = $prodObj->getCarriers();
                $toChange = 1;
                $toAssign = 1;
                foreach ($carriersLst as $valCrr) {
                    $isSellerCrr = $objMpShpMthod->isSellerShippingByIdReference($valCrr['id_reference']);
                    if ($isSellerCrr) {
                        $toChange = 0;
                        if ($valCrr['active'] == 1) {
                            $toAssign = 0;
                        }
                    }
                }
                if ($toChange || $toAssign) {
                    //set carrier using carrier reference
                    $prodObj->setCarriers(unserialize($adminDefShipping));
                }
            }
        }
        /*END*/
    }

    public function enableShipping($mpShippingId, $idPsReferenceAdded)
    {
        $objMpShipping = new self($mpShippingId);
        $objMpShipping->id_ps_reference = $idPsReferenceAdded;
        $objMpShipping->active = 1;
        $objMpShipping->save();

        $imgDir = _PS_MODULE_DIR_.'mpshipping/views/img/logo/';
        if (file_exists($imgDir.$mpShippingId.'.jpg')) {
            copy($imgDir.$mpShippingId.'.jpg', _PS_IMG_DIR_.'s/'.$idPsReferenceAdded.'.jpg');
        }
    }

    public function mailToAdminShippingAdded($mpIdSeller, $mpShippingId)
    {
        /*$obj_seller_info = new WkMpSeller();*/
        $sellerInfo = WkMpSeller::getSeller($mpIdSeller);
        $idLang = $sellerInfo['default_lang']; // Seller default lang


        $objSeller = new WkMpSeller($mpIdSeller, $idLang);
        $mpSellerName = $objSeller->seller_firstname.' '.$objSeller->seller_lastname;
        $businessEmail = $objSeller->business_email;
        $mpShopName = $objSeller->shop_name;
        $phone = $objSeller->phone;

        if ($businessEmail == '') {
            $idCustomer = $objSeller->seller_customer_id;
            $objCus = new Customer($idCustomer);
            $businessEmail = $objCus->email;
        }

        $shippingInfo = $this->getMpShippingInfo($mpShippingId, $idLang);

        if ($shippingInfo['is_free'] == 0) {
            $freeShipping = 'No';
        } else {
            $freeShipping = 'Yes';
        }

        if ($shippingInfo['shipping_handling'] == 0) {
            $handling = 'No';
        } else {
            $handling = 'Yes';
        }

        if ($shippingInfo['active'] == 0) {
            $status = 'Pending';
        } else {
            $status = 'Approved';
        }

        $templateVars = array(
            '{seller_name}' => $mpSellerName,
            '{mp_shop_name}' => $mpShopName,
            '{business_email}' => $businessEmail,
            '{phone}' => $phone,
            '{shipping_name}' => $shippingInfo['mp_shipping_name'],
            '{transit_delay}' => $shippingInfo['transit_delay'],
            '{free_shipping}' => $freeShipping,
            '{handling_cost}' => $handling,
            '{status}' => $status,
        );

        $tempPath = _PS_MODULE_DIR_.'mpshipping/mails/';

        if (Configuration::get('WK_MP_SUPERADMIN_EMAIL')) {
            $adminEmail = Configuration::get('WK_MP_SUPERADMIN_EMAIL');
        } else {
            $idEmployee = WkMpHelper::getSupperAdmin();
            $employee = new Employee($idEmployee);
            $adminEmail = $employee->email;
        }

        Mail::Send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'shipping_added',
            Mail::l('New Shipping method added', (int) Configuration::get('PS_LANG_DEFAULT')),
            $templateVars,
            $adminEmail,
            null,
            null,
            'Mpshipping',
            null,
            null,
            $tempPath,
            false,
            null,
            null
        );
    }

    public function mailToSeller($mpIdSeller, $mpShippingId, $approve)
    {
        /*$obj_seller_info = new WkMpSeller();*/
        $sellerInfo = WkMpSeller::getSeller($mpIdSeller);
        $idLang = $sellerInfo['default_lang']; // Seller default lang

        $objSeller = new WkMpSeller($mpIdSeller, $idLang);
        $mpSellerName = $objSeller->seller_firstname.' '.$objSeller->seller_lastname;
        $businessEmail = $objSeller->business_email;
        $mpShopName = $objSeller->shop_name;
        $phone = $objSeller->phone;

        $businessEmail = $sellerInfo['business_email'];
        if ($businessEmail == '') {
            $idCustomer = $objSeller->seller_customer_id;
            $objCus = new Customer($idCustomer);
            $businessEmail = $objCus->email;
        }
        $shippingInfo = $this->getMpShippingInfo($mpShippingId, $idLang);

        if ($shippingInfo['is_free'] == 0) {
            $freeShipping = 'No';
        } else {
            $freeShipping = 'Yes';
        }

        if ($shippingInfo['shipping_handling'] == 0) {
            $handling = 'No';
        } else {
            $handling = 'Yes';
        }

        if ($shippingInfo['active'] == 0) {
            $status = 'Pending';
        } else {
            $status = 'Approved';
        }

        $templateVars = array(
            '{seller_name}' => $mpSellerName,
            '{mp_shop_name}' => $mpShopName,
            '{business_email}' => $businessEmail,
            '{phone}' => $phone,
            '{shipping_name}' => $shippingInfo['mp_shipping_name'],
            '{transit_delay}' => $shippingInfo['transit_delay'],
            '{free_shipping}' => $freeShipping,
            '{handling_cost}' => $handling,
            '{status}' => $status,

        );

        $tempPath = _PS_MODULE_DIR_.'mpshipping/mails/';

        if ($approve == 1) {
            Mail::Send(
                $idLang,
                'shipping_active',
                Mail::l('Shipping method activated', $idLang),
                $templateVars,
                $businessEmail,
                null,
                null,
                'Mpshipping',
                null,
                null,
                $tempPath,
                false,
                null,
                null
            );
        }
        if ($approve == 0) {
            Mail::Send(
                $idLang,
                'shipping_deactive',
                Mail::l('Shipping method deactivated', $idLang),
                $templateVars,
                $businessEmail,
                null,
                null,
                'Mpshipping',
                null,
                null,
                $tempPath,
                false,
                null,
                null
            );
        }
    }

    public function getMpShippingInfo($mpIdShipping, $idLang)
    {
        $shippingInfo = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method` mp_shp_mthd LEFT JOIN `'._DB_PREFIX_.'mp_shipping_method_lang` mp_shp_mthd_lang ON (mp_shp_mthd.id = mp_shp_mthd_lang.id AND mp_shp_mthd_lang.`id_lang`='.$idLang.') WHERE mp_shp_mthd.id='.$mpIdShipping);

        if ($shippingInfo) {
            return $shippingInfo;
        } else {
            return false;
        }
    }

    public function getDefaultShippingBySellerId($mpIdSeller)
    {
        return Db::getInstance()->executeS(
            'SELECT `id`, `id_ps_reference`
            FROM `'._DB_PREFIX_.'mp_shipping_method`
            WHERE `mp_id_seller`='.(int) $mpIdSeller.
            ' AND is_default_shipping = 1'
        );
    }

    public static function getShippingDistributionByReference($idPsReference)
    {
        $distributionType = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_distribution` WHERE `id_ps_reference` = '.(int) $idPsReference);
        if ($distributionType) {
            return $distributionType;
        } else {
            return false;
        }
    }

    public static function updatePsShippingDistributionType($idPsReference, $shippingDistributeType)
    {
        if ($idPsReference && $shippingDistributeType) {
            $distributionExist = self::getShippingDistributionByReference($idPsReference);
            if ($distributionExist) {
                $updated = Db::getInstance()->update('mp_shipping_distribution', array(
                                        'type' => pSQL($shippingDistributeType)
                                    ), 'id_ps_reference = '.$idPsReference);
            } else {
                $idMpShipping = Db::getInstance()->getRow('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id_ps_reference` = '.(int) $idPsReference);
                if (!$idMpShipping) {
                    $idMpShipping = 0;
                }

                $updated = Db::getInstance()->insert('mp_shipping_distribution', array(
                                        'id_ps_reference' => (int) $idPsReference,
                                        'id_mp_shipping' => (int) $idMpShipping,
                                        'type' => pSQL($shippingDistributeType),
                                    ));
            }

            if ($updated) {
                return true;
            }
        }

        return false;
    }

    public static function setShippingGroup($mpShippingId, $shippingGroup)
    {
        if ($shippingGroup) {
            foreach ($shippingGroup as $idGroup) {
                Db::getInstance()->insert('mp_shipping_method_group', array(
                                                'mp_shipping_id' => (int) $mpShippingId,
                                                'id_group' => (int) $idGroup,
                                            ));
            }
        }
    }

    public static function deleteShippingGroup($mpShippingId)
    {
        return Db::getInstance()->delete('mp_shipping_method_group', 'mp_shipping_id = '.(int)  $mpShippingId);
    }

    public static function getShippingGroup($mpShippingId)
    {
        $shippingGroup = Db::getInstance()->executeS('SELECT `id_group` FROM `'._DB_PREFIX_.'mp_shipping_method_group` WHERE `mp_shipping_id` = '.(int) $mpShippingId);
        if ($shippingGroup) {
            return $shippingGroup;
        }

        return false;
    }

    /**
    * Get Shipping Distribution with Marketplace
    *
    * @param  array $sellerProduct - seller product details
    * @param  array $cart - cart details
    * @param  array $order - order details
    *
    * @return array
    */
    public static function getShippingDistributionData($sellerProduct, $cart, $order = false)
    {
        $distributorShippingCost = array();

        //We have to do this because when customer reorder any product then they don't update id_carrier in cart table. But in payment gateway split time, we don't have order id so we have to get through cart table
        if ($order) {
            //if get distribute shipping amount after order complete
            $distributorShippingCost = self::distributedShippingDataAfterOrder($sellerProduct, $cart, $order);
        } else {
            //if get distribute shipping amount before order complete (for payment gateway split function)
            $distributorShippingCost = self::distributedShippingDataBeforeOrder($sellerProduct, $cart);
        }

        return $distributorShippingCost;
    }

    public static function distributedShippingDataAfterOrder($sellerProduct, $cart, $order)
    {
        $distributorShippingCost = array();
        //Distribute shipping amount with Admin, Seller Or Both (If allowed from configuration)
        if (Configuration::get('MP_SHIPPING_DISTRIBUTION_ALLOW')) {
            $idCarrier = $order->id_carrier;
            $distributionType = 'admin';
            $objCarrier = new Carrier($idCarrier);
            // if mp shipping module is exist and distribution type is set for currect carrier from PS carrier tab
            $distributionExist = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_distribution` WHERE `id_ps_reference` = '.(int) $objCarrier->id_reference);
            if ($distributionExist) {
                $distributionType = $distributionExist['type'];
            }

            if ($sellerProduct && ($distributionType == 'seller' || $distributionType == 'both')) {
                //If shipping is distributed to seller or both (admin & seller)
                if (Module::isEnabled('mpcartordersplit')) {
                    //If mp cart and order split is enabled then shipping full amount will be distribute between each seller
                    $orderProductIds = array();
                    $orderProducts = $order->getProducts();
                    if ($orderProducts) {
                        foreach ($orderProducts as $orderProduct) {
                            $orderProductIds[] = $orderProduct['product_id'];
                        }
                    }
                    $carrierProductList = $cart->getProducts();
                    $sellerProductList = array();
                    if ($carrierProductList) {
                        foreach ($carrierProductList as $carrierProduct) {
                            if ($orderProductIds && in_array($carrierProduct['id_product'], $orderProductIds)) {
                                //Check if product is seller product
                                if ($sellerProductData = WkMpSellerProduct::getSellerProductByPsIdProduct($carrierProduct['id_product'])) {
                                    //Get seller customer id of seller product
                                    if ($sellerDetails = WkMpSeller::getSeller($sellerProductData['id_seller'])) {
                                        $sellerProductList[$sellerDetails['seller_customer_id']][] = $carrierProduct;
                                    }
                                }
                            }
                        }
                    }

                    if ($sellerProductList) {
                        foreach ($sellerProductList as $sellerIdCustomer => $sellerCarrierProduct) {
                            // $sellerIdCustomer index can be 'admin' or actual seller customer id
                            $totalShippingCost = $cart->getPackageShippingCost($idCarrier, true, null, $sellerCarrierProduct);
                            if ($totalShippingCost) {
                                if ($distributionType == 'both') {
                                    //Distribute seller individual cost between admin and seller on basis of commission rate
                                    $distributorShippingCost = self::getDistributionDataForBoth(
                                        $totalShippingCost,
                                        $sellerIdCustomer,
                                        $distributorShippingCost
                                    );
                                } else {
                                    if (isset($distributorShippingCost[$sellerIdCustomer])) {
                                        $distributorShippingCost[$sellerIdCustomer] += $totalShippingCost;
                                    } else {
                                        $distributorShippingCost[$sellerIdCustomer] = $totalShippingCost;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $totalShippingCost = $cart->getPackageShippingCost($idCarrier);
                    if ($totalShippingCost) {
                        //if shipping is distributed to seller or both (not only admin) from PS carrier Tab
                        $distributorsCount = array();
                        $distributorsCount['no_of_sellers'] = 0;
                        $distributorsCount['admin_exist'] = 0;
                        $totalUnit = 0; //total weight or total price

                        foreach ($sellerProduct as $distributorKey => $distributorValue) {
                            if ($distributorKey == 'admin') {
                                $distributorsCount['admin_exist'] += 1;
                            } else {
                                $distributorsCount['no_of_sellers'] += 1;
                            }

                            $addProductUnit = true;
                            if (($distributorKey == 'admin')
                            && !Configuration::get('MP_SHIPPING_ADMIN_DISTRIBUTION')) {
                                //if admin product exist but distribution not allowed for admin then admin product unit will not calculate in totalUnit
                                $addProductUnit = false;
                            }

                            //If particular product is allowed for adding its unit price or weight
                            if ($addProductUnit) {
                                if ($objCarrier->shipping_method == 1) {
                                    //For shipping method - Weight
                                    $totalUnit += $distributorValue['total_product_weight'];
                                } elseif ($objCarrier->shipping_method == 2) {
                                    //For shipping method - Price
                                    $totalUnit += $distributorValue['total_price_tax_incl'];
                                }
                            }
                        }

                        if ((count($sellerProduct) == 1) && ($distributorsCount['no_of_sellers'] == 1) && ($distributorsCount['admin_exist'] == 0)) {
                            //If only one seller product is exist in Order_cart
                            foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                if ($distributionType == 'both') {
                                    //Distribute seller individual cost between admin and seller on basis of commission rate
                                    $distributorShippingCost = self::getDistributionDataForBoth(
                                        $totalShippingCost,
                                        $sellerIdCustomer,
                                        $distributorShippingCost
                                    );
                                } else {
                                    $distributorShippingCost[$sellerIdCustomer] = $totalShippingCost;
                                }
                                break;
                            }
                        } else {
                            //If in order, admin product exist with seller product and Admin set shipping distributed to Seller in Carriers page then shipping distributed between both seller and admin. Otherwise shipping will go to sellers only
                            $adminDistribute = false;
                            if (($distributorsCount['admin_exist'] > 0)
                            && Configuration::get('MP_SHIPPING_ADMIN_DISTRIBUTION')) {
                                $adminDistribute = true;
                            }

                            // totalUnit can be total weight or total amount of all products in Order_cart and after this, we will calculate distributePercent of the basis of each product
                            if ($totalUnit > 0) {
                                if ($adminDistribute) {
                                    //Admin product exists with seller products then divide shipping according to total weight or total price including admin
                                    foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                        if ($objCarrier->shipping_method == 1) {
                                            //For shipping method - Weight
                                            $distributePercent = ($seller['total_product_weight']/$totalUnit) * 100;
                                        } elseif ($objCarrier->shipping_method == 2) {
                                            //For shipping method - Price
                                            $distributePercent = ($seller['total_price_tax_incl']/$totalUnit) * 100;
                                        }

                                        //Get seller and admin individual cost
                                        $individualShippingCost = ($totalShippingCost * $distributePercent) / 100;

                                        if ($distributionType == 'both') {
                                            if ($sellerIdCustomer == 'admin') {
                                                //send admin individual cost to admin
                                                if (isset($distributorShippingCost['admin'])) {
                                                    $distributorShippingCost['admin'] += $individualShippingCost;
                                                } else {
                                                    $distributorShippingCost['admin'] = $individualShippingCost;
                                                }
                                            }
                                            //Now Distribute seller individual cost between admin and seller on basis of commission rate
                                            $distributorShippingCost = self::getDistributionDataForBoth(
                                                $individualShippingCost,
                                                $sellerIdCustomer,
                                                $distributorShippingCost
                                            );
                                        } else {
                                            //if only seller then send individual distribution cost to admin and seller
                                            $distributorShippingCost[$sellerIdCustomer] = $individualShippingCost;
                                        }
                                    }
                                } else {
                                    //Atleast 2 seller's product exist then divide shipping in that sellers only according to total weight or total price (not admin)
                                    foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                        if ($sellerIdCustomer != 'admin') {
                                            if ($objCarrier->shipping_method == 1) {
                                                //For shipping method - Weight
                                                $distributePercent = ($seller['total_product_weight']/$totalUnit) * 100;
                                            } elseif ($objCarrier->shipping_method == 2) {
                                                //For shipping method - Price
                                                $distributePercent = ($seller['total_price_tax_incl']/$totalUnit) * 100;
                                            }

                                            //Get seller and admin individual cost
                                            $individualShippingCost = ($totalShippingCost * $distributePercent) / 100;

                                            if ($distributionType == 'both') {
                                                //Now Distribute seller individual cost between admin and seller on basis of commission rate
                                                $distributorShippingCost = self::getDistributionDataForBoth(
                                                    $individualShippingCost,
                                                    $sellerIdCustomer,
                                                    $distributorShippingCost
                                                );
                                            } else {
                                                //if only seller then send individual distribution cost to admin and seller
                                                $distributorShippingCost[$sellerIdCustomer] = $individualShippingCost;
                                            }
                                        }
                                    }
                                }
                            } else {
                                //Divide equally
                                if ($adminDistribute) {
                                    //Admin product exists with seller products then divide shipping in all members equally including admin
                                    $totalDistributionCount = count($sellerProduct);
                                    foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                        //Get seller and admin individual cost
                                        $individualShippingCost = $totalShippingCost/$totalDistributionCount;

                                        if ($distributionType == 'both') {
                                            if ($sellerIdCustomer == 'admin') {
                                                //send admin individual cost to admin
                                                if (isset($distributorShippingCost['admin'])) {
                                                    $distributorShippingCost['admin'] += $individualShippingCost;
                                                } else {
                                                    $distributorShippingCost['admin'] = $individualShippingCost;
                                                }
                                            }
                                            //Now Distribute seller individual cost between admin and seller on basis of commission rate
                                            $distributorShippingCost = self::getDistributionDataForBoth(
                                                $individualShippingCost,
                                                $sellerIdCustomer,
                                                $distributorShippingCost
                                            );
                                        } else {
                                            //if only seller then send individual distribution cost to admin and seller
                                            $distributorShippingCost[$sellerIdCustomer] = $individualShippingCost;
                                        }
                                    }
                                } else {
                                    //Atleast 2 seller's product exist then divide shipping in that sellers only as equally (not admin)
                                    $totalDistributionCount = $distributorsCount['no_of_sellers'];
                                    foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                        if ($sellerIdCustomer != 'admin') {
                                            //Get seller and admin individual cost
                                            $individualShippingCost = $totalShippingCost/$totalDistributionCount;

                                            if ($distributionType == 'both') {
                                                //Now Distribute seller individual cost between admin and seller on basis of commission rate
                                                $distributorShippingCost = self::getDistributionDataForBoth(
                                                    $individualShippingCost,
                                                    $sellerIdCustomer,
                                                    $distributorShippingCost
                                                );
                                            } else {
                                                //if only seller then send individual distribution cost to admin and seller
                                                $distributorShippingCost[$sellerIdCustomer] = $individualShippingCost;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $distributorShippingCost;
    }

    public static function distributedShippingDataBeforeOrder($sellerProduct, $cart)
    {
        $distributorShippingCost = array();
        if (Configuration::get('MP_SHIPPING_DISTRIBUTION_ALLOW')) {
            $carrierList = array();
            //May be same cart has one or more carrier, So we get all carriers for split before order
            $cartDeliveryList = $cart->getDeliveryOptionList();
            if ($cartDeliveryList) {
                $selectedCarriers = $cart->getDeliveryOption();
                if ($selectedCarriers) {
                    foreach ($selectedCarriers as $idDeliveryAddress => $selectedCarriersString) {
                        $carrierList = $cartDeliveryList[$idDeliveryAddress][$selectedCarriersString]['carrier_list'];
                        break;
                    }
                }
            }

            if ($carrierList) {
                foreach ($carrierList as $idCarrier => $carrierData) {
                    //All Carriers distribution (one by one) will be divide b/w sellers and admin
                    $splitPersons = array();
                    $sellerProductList = array();
                    if (isset($carrierData['product_list'])) {
                        foreach ($carrierData['product_list'] as $carrierProduct) {
                            //Check if product is seller product
                            if ($sellerProductData = WkMpSellerProduct::getSellerProductByPsIdProduct($carrierProduct['id_product'])) {
                                //Get seller customer id of seller product
                                if ($sellerDetails = WkMpSeller::getSeller($sellerProductData['id_seller'])) {
                                    $splitPersons[] = $sellerDetails['seller_customer_id'];
                                    $sellerProductList[$sellerDetails['seller_customer_id']][] = $carrierProduct;
                                }
                            } else {
                                $splitPersons[] = 'admin';
                            }
                        }
                    }

                    //Distribute shipping amount with Admin, Seller Or Both (If allowed from configuration)
                    if ($splitPersons && $idCarrier) {
                        $distributionType = 'admin';
                        $objCarrier = new Carrier($idCarrier);
                        // if mp shipping module is exist and distribution type is set for currect carrier from PS carrier tab
                        $distributionExist = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_distribution` WHERE `id_ps_reference` = '.(int) $objCarrier->id_reference);
                        if ($distributionExist) {
                            $distributionType = $distributionExist['type'];
                        }

                        if ($sellerProduct && ($distributionType == 'seller' || $distributionType == 'both')) {
                            //If shipping is distributed to seller or both (admin & seller)
                            if (Module::isEnabled('mpcartordersplit')) {
                                //If mp cart and order split is enabled then shipping full amount will be distribute between each seller
                                if ($sellerProductList) {
                                    foreach ($sellerProductList as $sellerIdCustomer => $sellerCarrierProduct) {
                                        // $sellerIdCustomer index can be 'admin' or actual seller customer id
                                        $totalShippingCost = $cart->getPackageShippingCost($idCarrier, true, null, $sellerCarrierProduct);
                                        if ($totalShippingCost) {
                                            if ($distributionType == 'both') {
                                                //Distribute seller individual cost between admin and seller on basis of commission rate
                                                $distributorShippingCost = self::getDistributionDataForBoth(
                                                    $totalShippingCost,
                                                    $sellerIdCustomer,
                                                    $distributorShippingCost
                                                );
                                            } else {
                                                if (isset($distributorShippingCost[$sellerIdCustomer])) {
                                                    $distributorShippingCost[$sellerIdCustomer] += $totalShippingCost;
                                                } else {
                                                    $distributorShippingCost[$sellerIdCustomer] = $totalShippingCost;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $totalShippingCost = $cart->getPackageShippingCost($idCarrier);
                                if ($totalShippingCost) {
                                    //if shipping is distributed to seller or both (not only admin) from PS carrier Tab
                                    $distributorsCount = array();
                                    $distributorsCount['no_of_sellers'] = 0;
                                    $distributorsCount['admin_exist'] = 0;
                                    $totalUnit = 0; //total weight or total price

                                    foreach ($sellerProduct as $distributorKey => $distributorValue) {
                                        if (in_array($distributorKey, $splitPersons)) {
                                            if ($distributorKey == 'admin') {
                                                $distributorsCount['admin_exist'] += 1;
                                            } else {
                                                $distributorsCount['no_of_sellers'] += 1;
                                            }

                                            $addProductUnit = true;
                                            if (($distributorKey == 'admin')
                                            && !Configuration::get('MP_SHIPPING_ADMIN_DISTRIBUTION')) {
                                                //if admin product exist but distribution not allowed for admin then admin product unit will not calculate in totalUnit
                                                $addProductUnit = false;
                                            }

                                            //If particular product is allowed for adding its unit price or weight
                                            if ($addProductUnit) {
                                                if ($objCarrier->shipping_method == 1) {
                                                    //For shipping method - Weight
                                                    $totalUnit += $distributorValue['total_product_weight'];
                                                } elseif ($objCarrier->shipping_method == 2) {
                                                    //For shipping method - Price
                                                    $totalUnit += $distributorValue['total_price_tax_incl'];
                                                }
                                            }
                                        }
                                    }

                                    if ((count($sellerProduct) == 1) && ($distributorsCount['no_of_sellers'] == 1) && ($distributorsCount['admin_exist'] == 0)) {
                                        //If only one seller product is exist in Order_cart
                                        foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                            if (in_array($sellerIdCustomer, $splitPersons)) {
                                                if ($distributionType == 'both') {
                                                    //Distribute seller individual cost between admin and seller on basis of commission rate
                                                    $distributorShippingCost = self::getDistributionDataForBoth(
                                                        $totalShippingCost,
                                                        $sellerIdCustomer,
                                                        $distributorShippingCost
                                                    );
                                                } else {
                                                    if (isset($distributorShippingCost[$sellerIdCustomer])) {
                                                        $distributorShippingCost[$sellerIdCustomer] += $totalShippingCost;
                                                    } else {
                                                        $distributorShippingCost[$sellerIdCustomer] = $totalShippingCost;
                                                    }
                                                }
                                                break;
                                            }
                                        }
                                    } else {
                                        //If in order, admin product exist with seller product and Admin set shipping distributed to Seller in Carriers page then shipping distributed between both seller and admin. Otherwise shipping will go to sellers only
                                        $adminDistribute = false;
                                        if (($distributorsCount['admin_exist'] > 0)
                                        && Configuration::get('MP_SHIPPING_ADMIN_DISTRIBUTION')) {
                                            $adminDistribute = true;
                                        }

                                        $individualShippingCost = 0;
                                        // totalUnit can be total weight or total amount of all products in Order_cart and after this, we will calculate distributePercent of the basis of each product
                                        if ($totalUnit > 0) {
                                            if ($adminDistribute) {
                                                //Admin product exists with seller products then divide shipping according to total weight or total price including admin
                                                foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                                    if (in_array($sellerIdCustomer, $splitPersons)) {
                                                        if ($objCarrier->shipping_method == 1) {
                                                            //For shipping method - Weight
                                                            $distributePercent = ($seller['total_product_weight']/$totalUnit) * 100;
                                                        } elseif ($objCarrier->shipping_method == 2) {
                                                            //For shipping method - Price
                                                            $distributePercent = ($seller['total_price_tax_incl']/$totalUnit) * 100;
                                                        }

                                                        //Get seller and admin individual cost
                                                        if (isset($distributorShippingCost[$sellerIdCustomer])) {
                                                            $individualShippingCost += (($totalShippingCost * $distributePercent) / 100);
                                                        } else {
                                                            $individualShippingCost = (($totalShippingCost * $distributePercent) / 100);
                                                        }

                                                        if ($distributionType == 'both') {
                                                            if ($sellerIdCustomer == 'admin') {
                                                                //send admin individual cost to admin
                                                                if (isset($distributorShippingCost['admin'])) {
                                                                    $distributorShippingCost['admin'] += $individualShippingCost;
                                                                } else {
                                                                    $distributorShippingCost['admin'] = $individualShippingCost;
                                                                }
                                                            }
                                                            //Now Distribute seller individual cost between admin and seller on basis of commission rate
                                                            $distributorShippingCost = self::getDistributionDataForBoth(
                                                                $individualShippingCost,
                                                                $sellerIdCustomer,
                                                                $distributorShippingCost
                                                            );
                                                        } else {
                                                            //if only seller then send individual distribution cost to admin and seller
                                                            $distributorShippingCost[$sellerIdCustomer] = $individualShippingCost;
                                                        }
                                                    }
                                                }
                                            } else {
                                                //Atleast 2 seller's product exist then divide shipping in that sellers only according to total weight or total price (not admin)
                                                foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                                    if (in_array($sellerIdCustomer, $splitPersons)) {
                                                        if ($sellerIdCustomer != 'admin') {
                                                            if ($objCarrier->shipping_method == 1) {
                                                                //For shipping method - Weight
                                                                $distributePercent = ($seller['total_product_weight']/$totalUnit) * 100;
                                                            } elseif ($objCarrier->shipping_method == 2) {
                                                                //For shipping method - Price
                                                                $distributePercent = ($seller['total_price_tax_incl']/$totalUnit) * 100;
                                                            }

                                                            //Get seller and admin individual cost
                                                            if (isset($distributorShippingCost[$sellerIdCustomer])) {
                                                                $individualShippingCost += (($totalShippingCost * $distributePercent) / 100);
                                                            } else {
                                                                $individualShippingCost = (($totalShippingCost * $distributePercent) / 100);
                                                            }

                                                            if ($distributionType == 'both') {
                                                                //Now Distribute seller individual cost between admin and seller on basis of commission rate
                                                                $distributorShippingCost = self::getDistributionDataForBoth(
                                                                    $individualShippingCost,
                                                                    $sellerIdCustomer,
                                                                    $distributorShippingCost
                                                                );
                                                            } else {
                                                                //if only seller then send individual distribution cost to admin and seller
                                                                $distributorShippingCost[$sellerIdCustomer] = $individualShippingCost;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            //Divide equally
                                            if ($adminDistribute) {
                                                //Admin product exists with seller products then divide shipping in all members equally including admin
                                                $totalDistributionCount = count($sellerProduct);
                                                foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                                    if (in_array($sellerIdCustomer, $splitPersons)) {
                                                        //Get seller and admin individual cost
                                                        if (isset($distributorShippingCost[$sellerIdCustomer])) {
                                                            $individualShippingCost += ($totalShippingCost/$totalDistributionCount);
                                                        } else {
                                                            $individualShippingCost = $totalShippingCost/$totalDistributionCount;
                                                        }

                                                        if ($distributionType == 'both') {
                                                            if ($sellerIdCustomer == 'admin') {
                                                                //send admin individual cost to admin
                                                                if (isset($distributorShippingCost['admin'])) {
                                                                    $distributorShippingCost['admin'] += $individualShippingCost;
                                                                } else {
                                                                    $distributorShippingCost['admin'] = $individualShippingCost;
                                                                }
                                                            }
                                                            //Now Distribute seller individual cost between admin and seller on basis of commission rate
                                                            $distributorShippingCost = self::getDistributionDataForBoth(
                                                                $individualShippingCost,
                                                                $sellerIdCustomer,
                                                                $distributorShippingCost
                                                            );
                                                        } else {
                                                            //if only seller then send individual distribution cost to admin and seller
                                                            $distributorShippingCost[$sellerIdCustomer] = $individualShippingCost;
                                                        }
                                                    }
                                                }
                                            } else {
                                                //Atleast 2 seller's product exist then divide shipping in that sellers only as equally (not admin)
                                                $totalDistributionCount = $distributorsCount['no_of_sellers'];
                                                foreach ($sellerProduct as $sellerIdCustomer => $seller) {
                                                    if (in_array($sellerIdCustomer, $splitPersons)) {
                                                        if ($sellerIdCustomer != 'admin') {
                                                            //Get seller and admin individual cost
                                                            if (isset($distributorShippingCost[$sellerIdCustomer])) {
                                                                $individualShippingCost += ($totalShippingCost/$totalDistributionCount);
                                                            } else {
                                                                $individualShippingCost = $totalShippingCost/$totalDistributionCount;
                                                            }

                                                            if ($distributionType == 'both') {
                                                                //Now Distribute seller individual cost between admin and seller on basis of commission rate
                                                                $distributorShippingCost = self::getDistributionDataForBoth(
                                                                    $individualShippingCost,
                                                                    $sellerIdCustomer,
                                                                    $distributorShippingCost
                                                                );
                                                            } else {
                                                                //if only seller then send individual distribution cost to admin and seller
                                                                $distributorShippingCost[$sellerIdCustomer] = $individualShippingCost;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $distributorShippingCost;
    }

    public static function getDistributionDataForBoth($totalShippingCost, $sellerIdCustomer, $distributorShippingCost)
    {
        //Distribute seller individual cost between admin and seller on basis of commission rate
        if ($sellerIdCustomer != 'admin') {
            $objMpCommission = new WkMpCommission();
            $commissionBySeller = $objMpCommission->getCommissionRate($sellerIdCustomer);
            if (!is_numeric($commissionBySeller)) {
                if ($globalCommission = Configuration::get('WK_MP_GLOBAL_COMMISSION')) {
                    $commissionRate = $globalCommission;
                } else {
                    $commissionRate = 0;
                }
            } else {
                $commissionRate = $commissionBySeller;
            }

            $adminShippingCommission = ($totalShippingCost * $commissionRate) / 100;
            if (isset($distributorShippingCost['admin'])) {
                $distributorShippingCost['admin'] += $adminShippingCommission;
            } else {
                $distributorShippingCost['admin'] = $adminShippingCommission;
            }

            $sellerShippingAmount = (($totalShippingCost) * (100 - $commissionRate)) / 100;
            if (isset($distributorShippingCost[$sellerIdCustomer])) {
                $distributorShippingCost[$sellerIdCustomer] += $sellerShippingAmount;
            } else {
                $distributorShippingCost[$sellerIdCustomer] = $sellerShippingAmount;
            }
        }

        return $distributorShippingCost;
    }

    /**
     * To get id_order_carrier from prestashop order Carrier table
     *
     * @param int $idOrder
     * @return void
     */
    public function getCarrierOrderInfoByIdOrder($idOrder)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_order_carrier`
            FROM `'._DB_PREFIX_.'order_carrier`
            WHERE `id_order` = '.(int)$idOrder
        );
    }

    public static function getSellerIdByMpShippingId($mpShippingId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `mp_id_seller` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id` = '.(int) $mpShippingId
        );
    }
}
