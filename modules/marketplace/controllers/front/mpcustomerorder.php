<?php
/**
*  2017-2018 PHPIST.
*
*  @author    Yassine Belkaid <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PHPIST
*  @license   https://store.webkul.com/license.html
*/

use PrestaShop\PrestaShop\Adapter\Order\OrderPresenter;

class MarketplaceMpCustomerOrderModuleFrontController extends ModuleFrontController
{
    public $order_presenter;

    public function initContent()
    {
        if (!$this->context->customer->isLogged()) {
            Tools::redirect($this->context->link->getPageLink('index'));
        }

        $this->order_presenter = new OrderPresenter();
        $orders = $this->getTemplateVarOrders();

        if (count($orders) <= 0) {
            $this->warning[] = $this->trans('You have not placed any orders.', array(), 'Shop.Notifications.Warning');
        }

       
        //echo $this->context->customer->stripe_customer_id;
        if(Configuration::get('STRIPE_MODE') == 0){
			$secret_key = Configuration::get('STRIPE_PRIVATE_KEY_TEST');
		}else{
			$secret_key = Configuration::get('STRIPE_PRIVATE_KEY_LIVE');
		}
        $customer_stripe_id = $this->context->customer->stripe_customer_id;
        $url = 'https://api.stripe.com/v1/customers/'.$customer_stripe_id.'/subscriptions';
        $headers = array('Authorization: Bearer '. $secret_key);
        //open connection        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, false);
       // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);        
        $invoice = json_decode($output,true);
        $invoice_pdf=array();
        if($invoice){
            foreach($invoice['data'] as $key=>$val){
               $invoice_id = $val['latest_invoice'];
               $url = 'https://api.stripe.com/v1/invoices/'.$invoice_id;
               $headers = array('Authorization: Bearer '. $secret_key);
               //open connection               
               $ch = curl_init();
               curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
               curl_setopt($ch, CURLOPT_URL, $url);
               curl_setopt($ch, CURLOPT_POST, false);
              // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
               $output = curl_exec($ch);
               curl_close($ch);
               $invoice_pdf[] = json_decode($output,true);               
            }
        }
        $orders['subscription_invoice']=$invoice_pdf;
        $this->context->smarty->assign(array(
            'logic' => 2,
            'orders' => $orders,
        ));
        parent::initContent();

        // $this->defineJSVars();
        $this->setTemplate('module:marketplace/views/templates/front/order/customerorder.tpl');
    }

    public function getTemplateVarOrders()
    {
        $orders = array();
        $customer_orders = Order::getCustomerOrders($this->context->customer->id);
        foreach ($customer_orders as $customer_order) {
            $order = new Order((int) $customer_order['id_order']);
            $orders[$customer_order['id_order']] = $this->order_presenter->present($order);
        }

        return $orders;
    }

    public function defineJSVars()
    {
        $jsVars = array(
            'logged' => $this->context->customer->isLogged(),
            'moduledir' => _MODULE_DIR_,
            'mp_image_dir' => _MODULE_DIR_.'marketplace/views/img/',
        );
            
        Media::addJsDef($jsVars);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('marketplace_account', 'modules/'.$this->module->name.'/views/css/marketplace_account.css');
        $this->registerStylesheet('marketplace_global', 'modules/'.$this->module->name.'/views/css/mp_global_style.css');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => 'Journal de bord',
            'url' => ''
        );

        $breadcrumb['links'][] = array(
            'title' => 'Mes achats',
            'url' => ''
        );

        return $breadcrumb;
    }
}
