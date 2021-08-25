<?php
/**
* 2015-2016 NTS
*
* DISCLAIMER
*
* You are NOT allowed to modify the software. 
* It is also not legal to do any changes to the software and distribute it in your own name / brand. 
*
* @author    NTS
* @copyright 2015-2016 NTS
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of NTS
*/

class subscription extends ObjectModel
{
	/** @var integer */
	public $id_subscription;
		
	/** @var integer */
	public $id_product;
	 
	/** @var integer */
	public $id_subscription_product;
	
	/** @var integer */
	public $id_shop;
	
	/** @var integer */
	public $charge;
	
	/** @var integer */
	public $active;
	
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'stripepro_products',
        'primary' => 'id_subscription',
        'multilang' => FALSE,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => TRUE),
            'id_subscription_product' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'charge' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'active' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
        ),
    );
	
    public static function loadByIdProduct($id_product){
		
		$context = Context::getContext();
		$exits = Db::getInstance()->getValue('
            SELECT id_subscription
            FROM `'._DB_PREFIX_.'stripepro_products` a
            WHERE a.id_shop = '.(int)$context->shop->id.' && a.`id_product` = '.(int)$id_product
        );
		
		if($exits=='')
		Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'stripepro_products` (`id_product`,`id_shop`)
            values('.(int)$id_product.','.(int)$context->shop->id.')');
		
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'stripepro_products` a
            WHERE a.id_shop = '.(int)$context->shop->id.' && a.`id_product` = '.(int)$id_product
        );
        
        
        return new subscription($result['id_subscription']);
    }
}

