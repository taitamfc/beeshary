<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpCartRuleProductRule extends ObjectModel
{
	public $id_mp_product_rule;
    public $id_mp_product_rule_group;
    public $type;


	public static $definition = array(
        'table' => 'mp_cart_rule_product_rule',
        'primary' => 'id_mp_product_rule',
        'fields' => array(
            'id_mp_product_rule_group' =>   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'type' =>                       array('type' => self::TYPE_STRING),
        ),
    );

    public function getDataByIdMpProductRuleGroup($id_mp_product_rule_group)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."mp_cart_rule_product_rule` WHERE id_mp_product_rule_group = ".(int)$id_mp_product_rule_group;
        $result = Db::getInstance()->getRow($sql);
        if ($result) {
            return $result;
        }
        return false;
    }

    public function insertIntoMpCartRuleProductRuleValue($id_mp_product_rule, $mpVoucherItems)
    {
        $rowLists = array();
        if (is_array($mpVoucherItems)) {
            foreach ($mpVoucherItems as $idItem) {
                $rowLists[] = array('id_mp_product_rule' => $id_mp_product_rule, 'id_mp_item' => $idItem);
            }
        } else {
            $rowLists[] = array('id_mp_product_rule' => $id_mp_product_rule, 'id_mp_item' => $mpVoucherItems);
        }

        return Db::getInstance()->insert('mp_cart_rule_product_rule_value', $rowLists);
    }

    public function getDataFromMpCartRuleProductRuleValueTable($id_mp_product_rule)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."mp_cart_rule_product_rule_value` WHERE id_mp_product_rule = ".$id_mp_product_rule;
        return Db::getInstance()->executeS($sql);
    }
}