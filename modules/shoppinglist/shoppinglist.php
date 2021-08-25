<?php

/**
* MODULE ShoppingList
*
* @author Empty
* @copyright 2007-2016 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/classes/ShoppingListObject.php');

class ShoppingList extends Module {

    public function __construct() {
        $this->name = 'shoppinglist';
        $this->tab = 'others';
		$this->version = '1.7.1';
		$this->author = 'Empty';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->module_key = "6ecaf5d4443aea1ca6093730a3fcf27b";

        parent::__construct();

        $this->displayName = $this->l('Shopping List');
        $this->description = $this->l('Permit to a customer to add/edit shopping list and add Product to them');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Shopping List Module?');
    }

    public function install() {

        include(dirname(__FILE__).'/sql/install.php');
                                
        if (!parent::install() OR 
            !$this->registerHook('displayCustomerAccount') OR
            !$this->registerHook('displayHeader') OR
            !$this->registerHook('ActionAuthentication') OR
            !$this->registerHook('displayProductButtons')
        ) {
            return false;
        }
        
        return true;
    }
    
    public function uninstall() {

        include(dirname(__FILE__).'/sql/uninstall.php');

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }
    
    public function hookDisplayHeader() {
        $this->context->controller->addCSS($this->_path.'views/css/shoppinglist.css');
        $this->context->controller->addJS($this->_path.'views/js/shoppinglist.js');

        // Add cart if no cart found the first time
        if (!$this->context->cart->id)
        {
            $this->context->cart->add();
            if ($this->context->cart->id)
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
        }
        $this->context->cookie->write();
    }
    
    public function hookDisplayProductButtons() {
        if($this->context->customer->isLogged()) {
            $this->createDefaultShoppingListIfNotExist();
            
            $customer = $this->context->cookie->id_customer;
            $shoppingListObj = new ShoppingListObject();
            $shoppingList = $shoppingListObj->getByIdCustomer($customer);
            
            $product = new Product(Tools::getValue('id_product'));

            $this->context->smarty->assign('title', $product->name[1]);
            $this->context->smarty->assign('shoppingList', $shoppingList);
            return $this->display(__FILE__, 'views/templates/hook/product.tpl');
        }
    }
    
    public function hookActionAuthentication() {
        $this->createDefaultShoppingListIfNotExist();
    }
    
    public function hookDisplayCustomerAccount() {
        $this->createDefaultShoppingListIfNotExist();
        return $this->display(__FILE__, 'views/templates/hook/customer_account.tpl');
    }
    
    private function createDefaultShoppingListIfNotExist() {
        //Get Shopping List of User Or If no Shopping List we create One
        $shoppingListObj = new ShoppingListObject();
        if($shoppingListObj->getNumberShoppingListByIdCustomer($this->context->cookie->id_customer) == 0) {
            $shoppingListObj->id_customer = $this->context->cookie->id_customer;
            $shoppingListObj->title = $this->l('My List');
            $shoppingListObj->status = 1;
            $shoppingListObj->date_add = new \DateTime();
            $shoppingListObj->date_upd = new \DateTime();
            
            $shoppingListObj->add();
        }
    }
}
