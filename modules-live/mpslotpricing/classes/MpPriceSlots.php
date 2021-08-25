<?php
/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpPriceSlots extends ObjectModel
{
    public $id;
    public $id_specific_price;
    public $mp_id_product;
    public $id_product_attribute;
    public $id_shop;
    public $id_currency;
    public $id_country;
    public $id_group;
    public $id_customer;
    public $price;
    public $from_quantity;
    public $reduction;
    public $reduction_tax;
    public $reduction_type;
    public $from;
    public $to;

    public static $definition = array(
        'table' => 'mp_price_slots',
        'primary' => 'id',
        'fields' => array(
            'id_specific_price' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'mp_id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'from_quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'reduction' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'reduction_tax' => array('type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true),
            'reduction_type' => array('type' => self::TYPE_STRING, 'validate' => 'isReductionType', 'required' => true),
            'from' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'to' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public function getAllProductSlots($mp_id_product)
    {
        $slots = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_price_slots` WHERE mp_id_product='.(int) $mp_id_product);
        if ($slots) {
            return $slots;
        } else {
            return false;
        }
    }

    public static function checkMinQtyExists($mp_id_product, $min_qty)
    {
        $minimum_qty = Db::getInstance()->getValue('SELECT * FROM `'._DB_PREFIX_.'mp_price_slots` WHERE mp_id_product='.(int) $mp_id_product.' AND min_qty='.(int) $min_qty);
        if ($minimum_qty) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteProductPriceSlot($mp_id_product, $slot_id)
    {
        $result = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mp_price_slots` WHERE mp_id_product='.(int) $mp_id_product.' AND id='.(int) $slot_id);

        return $result;
    }

    public function getSlotDetails($slot_id)
    {
        $slots = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_price_slots` WHERE id='.(int) $slot_id);
        if ($slots) {
            return $slots;
        } else {
            return false;
        }
    }

    public static function getAllPsSlotsId()
    {
        $ps_slots = Db::getInstance()->executeS('SELECT `id_specific_price` FROM `'._DB_PREFIX_.'mp_price_slots`');
        if ($ps_slots) {
            return $ps_slots;
        } else {
            return false;
        }
    }

    public static function exists($id_product, $id_product_attribute, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, $rule = false)
    {
        return (int) Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_price_slots` WHERE 
            `mp_id_product`='.(int) $id_product.' AND 
            `id_product_attribute`='.(int) $id_product_attribute.' AND 
            `id_shop`='.(int) $id_shop.' AND
            `id_group`='.(int) $id_group.' AND
            `id_country`='.(int) $id_country.' AND
            `id_currency`='.(int) $id_currency.' AND
            `id_customer`='.(int) $id_customer.' AND
            `from_quantity`='.(int) $from_quantity.' AND
            `from` >= \''.pSQL($from).'\' AND
            `to` <= \''.pSQL($to).'\'');
    }

    public function searchCustomer($is_search)
    {
        $mp_product_id = Tools::getValue('mp_product_id');
        if ($is_search) {
            $searches = explode(' ', Tools::getValue('keywords'));
            $customers = array();
            $searches = array_unique($searches);
            foreach ($searches as $search) {
                if (!empty($search) && $results = Customer::searchByName($search, 50)) {
                    foreach ($results as $result) {
                        if ($result['active']) {
                            $customers[$result['id_customer']] = $result;
                        }
                    }
                }
            }

            if (count($customers)) {
                $to_return = array(
                    'customers' => $customers,
                    'found' => true
                );
            } else {
                $to_return = array('found' => false);
            }

            die(Tools::jsonEncode($to_return));
        }
    }

    public function processPriceAddition($data)
    {
        sleep(2);
        $id_product = $data['mp_product_id'];
        $id_currency = $data['sp_id_currency'];
        $id_country = $data['sp_id_country'];
        $id_group = $data['sp_id_group'];
        $id_customer = $data['sp_id_customer'];
        $leave_bprice = 0;
        if (isset($data['leave_bprice']) && 1 == $data['leave_bprice']) {
            $leave_bprice = $data['leave_bprice'];
        }
        $sp_price = -1;
        if (isset($data['sp_price'])) {
            $sp_price = $data['sp_price'];
        }

        $price = $leave_bprice ? '-1' : $sp_price;
        $from_quantity = $data['sp_from_quantity'];
        $reduction = (float)($data['sp_reduction']);
        $reduction_tax = $data['sp_reduction_tax'];
        $reduction_type = !$reduction ? 'amount' : $data['sp_reduction_type'];
        $reduction_type = $reduction_type == '-' ? 'amount' : $reduction_type;
        $from = $data['sp_from'];
        if (!$from) {
            $from = '0000-00-00 00:00:00';
        }
        $to = $data['sp_to'];
        if (!$to) {
            $to = '0000-00-00 00:00:00';
        }
        $id_shop = 1;       // hardcoded id_shop = 1
        $id_product_attribute = 0;  // hardcoded id_product_attribute = 0
        if (($price == '-1') && ((float)$reduction == '0')) {
            die('2');   //No reduction value has been submitted
        } elseif ($to != '0000-00-00 00:00:00' && strtotime($to) < strtotime($from)) {
            die('3');   //Invalid date range
        } elseif ($reduction_type == 'percentage' && ((float)$reduction <= 0 || (float)$reduction > 100)) {
            die('4');   //Submitted reduction value (0-100) is out-of-range
        } elseif ($this->validateSpecificPrice($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_product_attribute)) {
            $product_detail = WkMpSellerProduct::getSellerProductByIdProduct($id_product);
            $id_specific_price = 0;
            // product is created in prestashop
            if ($product_detail && $product_detail['id_ps_product']) {
                //adding specific price to prestashop
                $id_specific_price = $this->addSpecificProductPriceToPs($product_detail['id_ps_product'], $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_tax, $reduction_type, $from, $to, $id_product_attribute);
            }
            // saving record to mp slot price table
            if ($this->addSpecificProductPriceToMp($id_product, $id_specific_price, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_tax, $reduction_type, $from, $to, $id_product_attribute)) {
                die('1');
            } else {
                die('0');
            }
        }
    }

    public function validateSpecificPrice($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_combination = 0)
    {
        if (!Validate::isUnsignedId($id_shop) || !Validate::isUnsignedId($id_currency) || !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group) || !Validate::isUnsignedId($id_customer)) {
            die('5');   //'Wrong IDs'
        } elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !Validate::isNegativePrice($price)) || (isset($reduction) && !Validate::isPrice($reduction))) {
            die('6');   //Invalid price/discount amount'
        } elseif (!Validate::isUnsignedInt($from_quantity)) {
            die('7');   //'Invalid quantity'
        } elseif ($reduction && !Validate::isReductionType($reduction_type)) {
            die('8');   //Please select a discount type (amount or percentage).
        } elseif ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to))) {
            die('9');   //The from/to date is invalid.
        } else {
            $product_detail = WkMpSellerProduct::getSellerProductByIdProduct($id_product);
            if ($product_detail) {
                if ($product_detail['id_ps_product']) {
                    if (SpecificPrice::exists((int)$product_detail['id_ps_product'], $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false)) {
                        die('10');  //A specific price already exists for these parameters
                    } else {
                        return true;
                    }
                } else {
                    if (MpPriceSlots::exists((int)$id_product, $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false)) {
                        die('10');  //A specific price already exists for these parameters
                    } else {
                        return true;
                    }
                }
            }
        }
    }

    public function addSpecificProductPriceToPs($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_tax, $reduction_type, $from, $to, $id_product_attribute)
    {
        $specificPrice = new SpecificPrice();
        $specificPrice->id_product = (int)$id_product;
        $specificPrice->id_product_attribute = (int)$id_product_attribute;
        $specificPrice->id_shop = (int)$id_shop;
        $specificPrice->id_currency = (int)($id_currency);
        $specificPrice->id_country = (int)($id_country);
        $specificPrice->id_group = (int)($id_group);
        $specificPrice->id_customer = (int)$id_customer;
        $specificPrice->price = (float)($price);
        $specificPrice->from_quantity = (int)($from_quantity);
        $specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
        $specificPrice->reduction_tax = $reduction_tax;
        $specificPrice->reduction_type = $reduction_type;
        $specificPrice->from = $from;
        $specificPrice->to = $to;
        if (!$specificPrice->add()) {
            return 0;
        } else {
            return $specificPrice->id;
        }
    }

    public function addSpecificProductPriceToMp($id_product, $id_specific_price, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_tax, $reduction_type, $from, $to, $id_product_attribute)
    {
        $mppriceslots = new MpPriceSlots();
        $mppriceslots->id_specific_price = $id_specific_price;
        $mppriceslots->mp_id_product = (int)$id_product;
        $mppriceslots->id_product_attribute = (int)$id_product_attribute;
        $mppriceslots->id_shop = (int)$id_shop;
        $mppriceslots->id_currency = (int)($id_currency);
        $mppriceslots->id_country = (int)($id_country);
        $mppriceslots->id_group = (int)($id_group);
        $mppriceslots->id_customer = (int)$id_customer;
        $mppriceslots->price = (float)($price);
        $mppriceslots->from_quantity = (int)($from_quantity);
        $mppriceslots->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
        $mppriceslots->reduction_tax = $reduction_tax;
        $mppriceslots->reduction_type = $reduction_type;
        $mppriceslots->from = $from;
        $mppriceslots->to = $to;
        if (!$mppriceslots->add()) {
            return false;//die('0'); //An error occurred while updating the specific price
        } else {
            return true; //die('1'); //Success
        }
    }

    public function deleteSlotPrice()
    {
        sleep(2);
        $id_delete = Tools::getValue('id_delete');
        if ($id_delete) {
            $obj_slot = new MpPriceSlots($id_delete);
            if ($obj_slot) {
                $id_specific_price = $obj_slot->id_specific_price;
                if ($id_specific_price || Validate::isUnsignedId($id_specific_price)) {
                    $specificPrice = new SpecificPrice((int)$id_specific_price);
                    if (!$specificPrice->delete()) {
                        die('0');   //An error occurred while attempting to delete the specific price
                    }
                }
                if ($obj_slot->delete()) {
                    die('1');
                } else {
                    die('0');   //An error occurred while attempting to delete the specific price
                }
            }
        }
    }
}
