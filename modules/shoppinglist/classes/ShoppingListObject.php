<?php
/**
* Class ShoppingListObject
*
* @author Empty
* @copyright 2007-2016 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class ShoppingListObject extends ObjectModel 
{
	/** @var int Id Shopping List */
	public $id_shopping_list;
    
    /** @var int Id Customer */
	public $id_customer;
		
	/** @var string Title */
	public $title;
	
	/** @var date Date Add */
	public $date_add;
    
    /** @var date Date Update */
	public $date_upd;
    
    /** @var int Status */
	public $status;
	
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'shopping_list',
        'primary' => 'id_shopping_list',
        'multilang' => FALSE,
        'fields' => array(
            'id_shopping_list' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => TRUE),
            'title' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );
    
    public function getNumberShoppingListByIdCustomer($idCustomer){
        $result = $this->getByIdCustomer($idCustomer);
        
        return count($result);
    }
	
    public function getByIdCustomer($idCustomer){
        $results = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'shopping_list` shoppinglist
            WHERE shoppinglist.`id_customer` = '.(int)$idCustomer.' '.
            'AND STATUS=1'
        );

        return $results;
    }
    
    public static function loadByIdAndCustomer($idShoppingList, $idCustomer) {
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'shopping_list` shoppinglist
            WHERE shoppinglist.`id_shopping_list` = '.(int)$idShoppingList.' '.
            'AND shoppinglist.`id_customer` = '.(int)$idCustomer
        );
        
        if(!empty($result['id_shopping_list'])) {
            return new ShoppingListObject($result['id_shopping_list']);
        }
        else {
            return null;
        }
    }
    
    public function getOneProduct($idShoppingList, $idProduct, $idProductAttribute) {
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'shopping_list_product` shoppinglistproduct
            WHERE shoppinglistproduct.`id_shopping_list` = '.(int)$idShoppingList.' '.
            'AND shoppinglistproduct.`id_product` = '.(int)$idProduct.' '.
            'AND shoppinglistproduct.`id_product_attribute` = '.(int)$idProductAttribute
        );
        
        return $result;
    }
    
    public function getAllProducts() {
        $results = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'shopping_list_product` shoppinglistproduct
            WHERE shoppinglistproduct.`id_shopping_list` = '.(int)$this->id_shopping_list
        );

        return $results;
    }
    
    public function deleteProduct($idProduct, $idProductAttribute) {
        $result = Db::getInstance()->execute('
            DELETE 
            FROM `'._DB_PREFIX_.'shopping_list_product` 
            WHERE `id_shopping_list` = '.(int)$this->id_shopping_list.' '.
            'AND `id_product` = '.(int)$idProduct.' '.
            'AND `id_product_attribute` = '.(int)$idProductAttribute.' ' 
        );
        
        return $result;
    }
    
    public function addProduct($idProduct, $idProductAttribute, $title) {
        try {
            $result = Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'shopping_list_product`
                VALUES ('.(int)$this->id_shopping_list.', '.(int)$idProduct.', '.(int)$idProductAttribute.', \''. addslashes($title).'\');'
            );
        }
        catch (Exception $e) {
            return false;
        }
        
        return $result;
    }
}
