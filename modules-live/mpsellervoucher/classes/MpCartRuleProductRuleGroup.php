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

class MpCartRuleProductRuleGroup extends ObjectModel
{
	public $id_mp_product_rule_group;
    public $id_mp_cart_rule;
    public $quantity;
    public $date_add;
    public $date_upd;


	public static $definition = array(
        'table' => 'mp_cart_rule_product_rule_group',
        'primary' => 'id_mp_product_rule_group',
        'fields' => array(
            'id_mp_cart_rule' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'quantity' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function getDataByIdMpCartRule($id_mp_cart_rule)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."mp_cart_rule_product_rule_group` WHERE id_mp_cart_rule = ".(int)$id_mp_cart_rule;
        $result = Db::getInstance()->getRow($sql);

        if ($result) {
            return $result;
        }
        return false;
    }

    public function updateMpIdProdByIdMpCartRule($id_mp_cart_rule, $mpVoucherItems)
    {
        $mp_prodRuleGroup_dtl = $this->getDataByIdMpCartRule($id_mp_cart_rule);
        if ($mp_prodRuleGroup_dtl) {
            $id_mp_product_rule_group = $mp_prodRuleGroup_dtl['id_mp_product_rule_group'];

            $obj_mp_prod_rule = new MpCartRuleProductRule();
            $mp_prodRule_dtl = $obj_mp_prod_rule->getDataByIdMpProductRuleGroup($id_mp_product_rule_group);
            if ($mp_prodRule_dtl) {
                $id_mp_product_rule = $mp_prodRule_dtl['id_mp_product_rule'];

                $deletedProductRuleValue = Db::getInstance()->delete('mp_cart_rule_product_rule_value', '`id_mp_product_rule` = '.(int)$id_mp_product_rule);
                if ($deletedProductRuleValue) {
                    return $obj_mp_prod_rule->insertIntoMpCartRuleProductRuleValue($id_mp_product_rule, $mpVoucherItems);
                }
            }
        }

        return false;
    }

    public function deleteProductRestrictionByIdMpCartRule($id_mp_cart_rule)
    {
        $mp_prodRuleGroup_dtl = $this->getDataByIdMpCartRule($id_mp_cart_rule);
        if ($mp_prodRuleGroup_dtl) {
            $id_mp_product_rule_group = $mp_prodRuleGroup_dtl['id_mp_product_rule_group'];

            $obj_mp_prod_rule = new MpCartRuleProductRule();
            $mp_prodRule_dtl = $obj_mp_prod_rule->getDataByIdMpProductRuleGroup($id_mp_product_rule_group);
            if ($mp_prodRule_dtl) {
                $id_mp_product_rule = $mp_prodRule_dtl['id_mp_product_rule'];

                Db::getInstance()->delete('mp_cart_rule_product_rule_value', '`id_mp_product_rule` = '.(int)$id_mp_product_rule);
                Db::getInstance()->delete('mp_cart_rule_product_rule', '`id_mp_product_rule` = '.(int)$id_mp_product_rule);
                Db::getInstance()->delete('mp_cart_rule_product_rule_group', '`id_mp_product_rule_group` = '.(int)$id_mp_product_rule_group);

                return true;
            }
        }

        return false;
    }

    public function getVoucherProductRuleInfo($idMpCartRule)
    {
        if (!$idMpCartRule) {
            return false;
        }

        $sql = "SELECT crprv.`id_mp_item`, crprv.`id_mp_product_rule`
                FROM `"._DB_PREFIX_."mp_cart_rule_product_rule_group` AS crprg
                INNER JOIN `"._DB_PREFIX_."mp_cart_rule_product_rule` AS crpr ON (crpr.`id_mp_product_rule_group` = crprg.`id_mp_product_rule_group`)
                INNER JOIN `"._DB_PREFIX_."mp_cart_rule_product_rule_value` AS crprv ON (crprv.`id_mp_product_rule` = crpr.`id_mp_product_rule`)
                WHERE crprg.`id_mp_cart_rule` = ".(int)$idMpCartRule;

        return Db::getInstance()->executeS($sql);
    }
}