<?php

/**
* Class ShoppingListAccountShoppingListModuleFrontController
*
* @author Empty
* @copyright 2007-2016 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class ShoppingListAccountShoppingListModuleFrontController extends ModuleFrontController {
    private $messages;
    
    public function __construct()
	{
		parent::__construct();
        $this->success = null;
        $this->errors = null;
        $this->context = Context::getContext();
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
        
        $action = Tools::getValue('action');
        switch($action) {
            case 'add':             $this->addShoppingList();               break;
            case 'delete':  
            case 'deleteConfirm':   $this->deleteShoppingList();            break;
            case 'update':          $this->updateShoppingList();            break;
            default:                $this->indexShoppingList();             break;
        }
	}

	/**
	 * Index shopping list
	 */
	public function indexShoppingList()
	{
        $shoppingListObj = new ShoppingListObject();
        
        //Get List of all 
        $shoppingList = $shoppingListObj->getByIdCustomer($this->context->cookie->id_customer);
        
        //$this->context->smarty->assign('messages', $this->messages);
        //$this->context->smarty->assign('errors', $this->errors);
        $this->context->smarty->assign([
            'shoppingList' => $shoppingList,
            'logic' => 3,
        ]);
        $this->setTemplate('module:shoppinglist/views/templates/front/accountshoppinglistindex.tpl');
	}
    
    /**
	 * Add shopping list
	 */
	public function addShoppingList() {
        $shoppingListObj = new ShoppingListObject();
        
        $title = Tools::getValue('title');
        if (!empty($title)) {
            $shoppingListObj->id_customer = $this->context->cookie->id_customer;
            $shoppingListObj->title = $title;
            $shoppingListObj->status = 1;
            $date = new \DateTime();
            $shoppingListObj->date_add = $date;
            $shoppingListObj->date_upd = $date;
            
            try {
                $shoppingListObj->add();
                $this->success[] = $this->module->l('Shopping List added', 'accountshoppinglist');
            }
            catch (Exception $e) {
                $this->errors[] = $this->module->l('Error! Perhaps this Shopping list already exist', 'accountshoppinglist');
            }
            
            $this->indexShoppingList();
        }
        else {
            $this->context->smarty->assign('introduction', $this->module->l('Add a new Shopping list', 'accountshoppinglist'));
            $this->context->smarty->assign('action', 'add');
            $this->context->smarty->assign('submit', $this->module->l('Add', 'accountshoppinglist'));
            $this->context->smarty->assign('shoppingListObj', $shoppingListObj);
            $this->context->smarty->assign('logic', 3);
            $this->setTemplate('module:shoppinglist/views/templates/front/accountshoppinglistform.tpl');
        }
    }
    
    /**
	 * Update shopping list
	 */
	public function updateShoppingList() {      
        $idShoppingList = Tools::getValue('id_shopping_list');
        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        
        $title = Tools::getValue('title');
        if (!empty($title)) {
            $shoppingListObj->title = $title;
            $shoppingListObj->date_upd = new \DateTime();
            try {
                $shoppingListObj->update();
                $this->success[] = $this->module->l('Shopping List updated', 'accountshoppinglist');
            }
            catch (Exception $e) {
                $this->errors[] = $this->module->l('Error! Perhaps this Shopping list already exist', 'accountshoppinglist');
            }
            
            $this->indexShoppingList();
        }
        else {
            $this->context->smarty->assign('introduction', $this->module->l('Update a shopping list', 'accountshoppinglist'));
            $this->context->smarty->assign('action', 'update');
            $this->context->smarty->assign('submit', $this->module->l('Update', 'accountshoppinglist'));
            $this->context->smarty->assign('shoppingListObj', $shoppingListObj);
            $this->setTemplate('module:shoppinglist/views/templates/front/accountshoppinglistform.tpl');
        }
    }
    
    /**
	 * Delete shopping list
	 */
	public function deleteShoppingList()
	{
        $action = Tools::getValue('action');
        $idShoppingList = Tools::getValue('id_shopping_list');
        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        
        if($shoppingListObj == null) {
            $this->errors[] = $this->module->l('An error occur', 'accountshoppinglist');
            $this->indexShoppingList();
            return;
        } 
        if($action == "delete") {
            $this->context->smarty->assign('shoppingListObj', $shoppingListObj);
            $this->setTemplate('module:shoppinglist/views/templates/front/accountshoppinglistdelete.tpl');
        }
        if($action == "deleteConfirm") {
            //It's not possible to delete when only one shopping list
            if($shoppingListObj->getNumberShoppingListByIdCustomer($this->context->cookie->id_customer) > 1) {
                $shoppingListObj->status = 0;
                $shoppingListObj->date_upd = new \DateTime();
                $shoppingListObj->update();
                $this->success[] = $this->module->l('Shopping List deleted', 'accountshoppinglist');
            }
            else {
                $this->errors[] = $this->module->l('Impossible to delete this shopping list! It\'s necessary to have one shopping list', 'accountshoppinglist');
            }
            
            $this->indexShoppingList();
        }
	}

    public function getBreadcrumbLinks() {
        $breadcrumb = parent::getBreadcrumbLinks();

        // $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        // $breadcrumb['links'][] = [
        //     'title' => $this->module->l('My Shopping List', 'accountshoppinglist'),
        //     'url' => $this->context->link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'index'])
        // ];
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => 'Journal de bord',
            'url' => ''
        );

        $breadcrumb['links'][] = array(
            'title' => 'Mes favoris',
            'url' => ''
        );

        return $breadcrumb;
    }

    public function getTemplateVarPage() {
        $page = parent::getTemplateVarPage();
        $page['body_classes']['page-customer-account'] = true;
        return $page;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('marketplace_global', 'modules/marketplace/views/css/mp_global_style.css');
    }
}