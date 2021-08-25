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

if (!defined('_PS_VERSION_'))
    exit;

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
    
require_once(dirname(__FILE__) . '/classes/subscription.php');

class StripePro extends PaymentModule
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->name = 'stripepro';
        $this->tab = 'payments_gateways';
        $this->version = '4.1.3';
        $this->author = 'NTS';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_key = '3dd3287465380c358a021f1378ed9f61';
        $this->controllers = array('webhook','subscriptions');

        parent::__construct();

        $this->displayName = $this->l('Stripe Normal and Recurring Payments');
        $this->description = $this->l('Accept payments by Apple Pay, Credit/Debit Cards with single Stripe account (Visa, Mastercard, Amex, Discover and Diners Club)');
        $this->confirmUninstall = $this->l('Warning: all the Stripe customers credit cards and transaction details saved in your database will be deleted. Are you sure you want uninstall this module?');
        
    }
    /**
     * Backoffice Tabs installation
     *
     * @return boolean Backoffice Tabs installation result
     */
    
    public function callInstallTab()
    {        
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminSubscriptions';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = 'Subscriptions';

        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentOrders');
        $tab->module = $this->name;
        return $tab->add();
    }
    

    /**
     * Stripe's module installation
     *
     * @return boolean Install result
     */
    public function install()
    {
        if (Shop::isFeatureActive())
           Shop::setContext(Shop::CONTEXT_ALL);
        
        $ret = parent::install() 
        && $this->registerHook('header') 
        && $this->registerHook('backOfficeHeader') 
        && $this->registerHook('paymentOptions')
        && $this->registerHook('displaycustomerAccount') 
        && $this->registerHook('displayAdminProductsExtra') 
        && $this->registerHook('actionProductUpdate') 
        && $this->callInstallTab()
        && $this->installDb();
        
        Configuration::updateValue('STRIPE_ALLOW_APPLEPAY', 1);
        Configuration::updateValue('STRIPE_CAPTURE_TYPE', 1);
        Configuration::updateValue('STRIPE_MODE', 0);
        Configuration::updateValue('STRIPE_PENDING_ORDER_STATUS', (int)Configuration::get('PS_OS_PAYMENT'));
        Configuration::updateValue('STRIPE_PAYMENT_ORDER_STATUS', (int)Configuration::get('PS_OS_PAYMENT'));
        Configuration::updateValue('STRIPE_SUBS_PAYMENT_ORDER_STATUS', 11);
        Configuration::updateValue('STRIPE_CHARGEBACKS_ORDER_STATUS', (int)Configuration::get('PS_OS_ERROR'));
        Configuration::updateValue('STRIPE_POPUP_TITLE', (int)Configuration::get('PS_SHOP_NAME'));
        Configuration::updateValue('STRIPE_CHKOUT_POPUP', 0);
        Configuration::updateValue('STRIPE_POPUP_DESC', 'Complete your transaction');
        Configuration::updateValue('STRIPE_POPUP_LOCALE', 'auto');
        Configuration::updateValue('STRIPE_WEBHOOK_TOKEN', md5(Tools::passwdGen()));
        Configuration::updateValue('STRIPE_SUBS_PAYMENT_ORDER_NEW', 1);
        Configuration::updateValue('STRIPE_SUBS_CANCEL_OPTN', 0);

        return $ret;
    }

    /**
     * Stripe's module database tables installation
     *
     * @return boolean Database tables installation result
     */
    public function installDb()
    {
        
        return Db::getInstance()->Execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'stripepro_customer` (`id_stripe_customer` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `stripe_customer_id` varchar(32) NOT NULL, `token` varchar(32) NOT NULL, `id_customer` int(10) unsigned NOT NULL,
            `cc_last_digits` int(11) NOT NULL, `date_add` datetime NOT NULL, PRIMARY KEY (`id_stripe_customer`), KEY `id_customer` (`id_customer`),
            KEY `token` (`token`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1') &&
            Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'stripepro_transaction` (`id_stripe_transaction` int(11) NOT NULL AUTO_INCREMENT,
            `type` enum(\'payment\',\'refund\') NOT NULL,`source` varchar(32) NOT NULL DEFAULT \'card\',`btc_address` VARCHAR( 50 ) NOT NULL, `id_stripe_customer` int(10) unsigned NOT NULL, `id_cart` int(10) unsigned NOT NULL,
            `id_order` int(10) unsigned NOT NULL, `id_transaction` varchar(32) NOT NULL, `amount` decimal(10,2) NOT NULL, `status` enum(\'paid\',\'unpaid\',\'uncaptured\') NOT NULL,
            `currency` varchar(3) NOT NULL, `cc_type` varchar(16) NOT NULL, `cc_exp` varchar(8) NOT NULL, `cc_last_digits` int(11) NOT NULL,
            `cvc_check` tinyint(1) NOT NULL DEFAULT \'0\', `fee` decimal(10,2) NOT NULL, `mode` enum(\'live\',\'test\') NOT NULL,
            `date_add` datetime NOT NULL, `charge_back` tinyint(1) NOT NULL DEFAULT \'0\', PRIMARY KEY (`id_stripe_transaction`), KEY `idx_transaction` (`type`,`id_order`,`status`))
                ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1') && Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."stripepro_subscription` (
          `id_stripe_subscription` int(10) NOT NULL AUTO_INCREMENT,
          `stripe_subscription_id` varchar(32) NOT NULL,
          `stripe_customer_id` varchar(32) NOT NULL,
          `id_customer` int(11) NOT NULL,
          `id_product` int(11) NOT NULL,
          `stripe_plan_id` varchar(100) NOT NULL,
          `quantity` int(11) NOT NULL,
          `start` varchar(32) NOT NULL,
          `current_period_start` varchar(32) NOT NULL,
          `current_period_end` varchar(32) NOT NULL,
          `canceled_at` varchar(32) NOT NULL,
           `cancel_at_period_end` tinyint(1) NOT NULL DEFAULT '0',
          `status` enum('trialing','active','past_due','canceled','unpaid') NOT NULL,
          `date_add` datetime NOT NULL,
          PRIMARY KEY (`id_stripe_subscription`)
        ) ENGINE="._MYSQL_ENGINE_."  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1") && Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."stripepro_plans` (
          `id_stripe_plan` int(10) NOT NULL AUTO_INCREMENT,
          `stripe_plan_id` varchar(100) NOT NULL,
          `name` varchar(100) NOT NULL,
          `interval` enum('day','week','month','year') NOT NULL,
          `amount` float NOT NULL,
          `currency` varchar(3) NOT NULL,
          `interval_count` varchar(5) NOT NULL,
          `trial_period_days` int(5) NOT NULL,
          PRIMARY KEY (`id_stripe_plan`)
        ) ENGINE="._MYSQL_ENGINE_."  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1") && Db::getInstance()->Execute('
          CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'stripepro_products` (
            `id_subscription` int(10) NOT NULL AUTO_INCREMENT,
            `id_product` INT( 11 ) UNSIGNED NOT NULL,
            `id_subscription_product` varchar(100) NOT NULL,
            `id_shop` INT( 11 ) UNSIGNED NOT NULL,
            `charge` tinyint(4) UNSIGNED NOT NULL,
            `active` tinyint(4) UNSIGNED NOT NULL,
            PRIMARY KEY (`id_subscription`)
          ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;') && Db::getInstance()->Execute('
          CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'stripepro_subs_order` (
          `id_stripe_subscription` int(11) NOT NULL,
          `id_order` int(11) NOT NULL
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
    }
    
    /**
     * Backoffice Tabs uninstallation
     *
     * @return boolean Backoffice Tabs uninstallation result
     */
    
    public function callUninstallTab()
    {  
        $id_tab = (int)Tab::getIdFromClassName('AdminSubscriptions');
        if ($id_tab>0)
        {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }

        return true;
    }

    /**
     * Stripe's module uninstallation (Configuration values, database tables...)
     *
     * @return boolean Uninstall result
     */
    public function uninstall()
    {
        @Db::getInstance()->Execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."stripepro_transaction`");
        @Db::getInstance()->Execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."stripepro_customer`");
        @Db::getInstance()->Execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."stripepro_subscription`");
        @Db::getInstance()->Execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."stripepro_plans`");
        @Db::getInstance()->Execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."stripepro_products`");
        @Db::getInstance()->Execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."stripepro_subs_order`");
         
        Configuration::deleteByName('STRIPE_PUBLIC_KEY_TEST');
        Configuration::deleteByName('STRIPE_PUBLIC_KEY_LIVE');
        Configuration::deleteByName('STRIPE_PRIVATE_KEY_TEST');
        Configuration::deleteByName('STRIPE_PRIVATE_KEY_LIVE');
        Configuration::deleteByName('STRIPE_MODE');
        Configuration::deleteByName('STRIPE_CAPTURE_TYPE');
        Configuration::deleteByName('STRIPE_CHARGEBACKS_ORDER_STATUS');
        Configuration::deleteByName('STRIPE_SUBS_PAYMENT_ORDER_STATUS');
        Configuration::deleteByName('STRIPE_PENDING_ORDER_STATUS');
        Configuration::deleteByName('STRIPE_PAYMENT_ORDER_STATUS');
        Configuration::deleteByName('STRIPE_WEBHOOK_TOKEN');
        Configuration::deleteByName('STRIPE_POPUP_TITLE');
        Configuration::deleteByName('STRIPE_CHKOUT_POPUP');
        Configuration::deleteByName('STRIPE_POPUP_DESC');
        Configuration::deleteByName('STRIPE_POPUP_LOCALE');
        Configuration::deleteByName('STRIPE_ALLOW_APPLEPAY');
        Configuration::deleteByName('STRIPE_SUBS_PAYMENT_ORDER_NEW');
        Configuration::deleteByName('STRIPE_SUBS_CANCEL_OPTN');
        
        return parent::uninstall() && $this->callUninstallTab();     
    }

     public function hookDisplayCustomerAccount()
    {
        return $this->display(__FILE__, 'my-account.tpl');
    }
    
     public static function getBaseLink()
    {
        return true;
    }
     public static function getLangLink()
    {
        return true;
    }
    
    public function hookDisplayAdminProductsExtra($params) {

        $id_product = $params['id_product'];
        $sampleObj = subscription::loadByIdProduct($id_product);
        $quick_link = $this->context->link->getAdminLink("AdminProducts",true,array('id_product'=>$id_product))."&SubmitListPlans#tab-hooks";

        if(!empty($sampleObj) && isset($sampleObj->id)){
            $this->context->smarty->assign(array(
                'id_subscription_product' => $sampleObj->id_subscription_product,
                'stripe_plans' => Db::getInstance()->ExecuteS("SELECT stripe_plan_id,CONCAT(`name`,' (',UCASE(`currency`),' ',`amount`,'/',`interval`,')') as name FROM "._DB_PREFIX_."stripepro_plans"),
                'stripe_active' => $sampleObj->active,
                'stripe_charge' => $sampleObj->charge,
                'filepath' => $this->_path,
                'quick_link' => $quick_link,
              ));
        }
        
        return $this->display(__FILE__, 'views/templates/admin/subscription.tpl');
   }
   
   public function hookActionProductUpdate($params) {
    
        if(Tools::getIsset('stripepro_submit')) {
            
            $id_product = $params['id_product'];
            $sampleObj = subscription::loadByIdProduct($id_product);
            $sampleObj->id_subscription_product = Tools::getValue('stripe_plan');
            $sampleObj->id_shop = (int)$this->context->shop->id;
            $sampleObj->active = Tools::getValue('stripe_active');
            $sampleObj->charge = Tools::getValue('charge');
            $sampleObj->update();
            
        }
   }

    public function hookHeader($params)
    {
        
        if (Tools::getValue('controller') == 'product')
        {
            $product_id = Tools::getValue('id_product');
            if(!empty($product_id))
            {
                $sub = Db::getInstance()->getValue('select `id_subscription_product` from `'._DB_PREFIX_.'stripepro_products` where `active` = 1 && `id_product`= '.$product_id);
                if($sub != '')
                {
                    $this->context->smarty->assign('Subscribe_text',$this->l('Subscribe'));
                    $this->context->controller->registerJavascript($this->name.'-subscribe', '/modules/'.$this->name.'/views/js/subscribe.js');
                    return $this->display(__FILE__, 'subscribe.tpl');
                }
            }
        } elseif (Tools::getValue('controller') == 'order') {
           
                $this->context->controller->registerStylesheet($this->name.'-frontcss', '/modules/'.$this->name.'/views/css/stripe-prestashop.css');
                if(!Configuration::get('STRIPE_CHKOUT_POPUP') || Configuration::get('STRIPE_ALLOW_APPLEPAY')){
                  $this->context->controller->registerJavascript($this->name.'-stipeV2', 'https://js.stripe.com/v2/', array('server'=>'remote'));
                  $this->context->controller->registerJavascript($this->name.'-paymentjs', '/modules/'.$this->name.'/views/js/stripe-prestashop.js');
                }
                
                $customer_credit_card = Db::getInstance()->getValue('SELECT token FROM '._DB_PREFIX_.'stripepro_customer  WHERE id_customer = '.(int)$this->context->cookie->id_customer);
               if($customer_credit_card!='')
               $this->context->controller->registerJavascript($this->name.'-savedcardjs', '/modules/'.$this->name.'/views/js/stripe-savedcard.js');
                
                if(Configuration::get('STRIPE_CHKOUT_POPUP')){
                  $this->context->controller->registerJavascript($this->name.'-stipeCheckout', 'https://checkout.stripe.com/checkout.js', array('server'=>'remote'));
                  $this->context->controller->registerJavascript($this->name.'-checkoutjs', '/modules/'.$this->name.'/views/js/stripe-checkout.js');
                }
        }
    }
    
    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        
        $this->smarty->assign(
            $this->getTemplateVars()
        );
 
        $payment_options = array();
        
        if(!Configuration::get('STRIPE_CHKOUT_POPUP')){
          
        $embeddedOption = new PaymentOption();
        $embeddedOption->setModuleName($this->name)
                       ->setCallToActionText($this->l('Pay with Credit / Debit Card'))
                       ->setForm($this->display(__FILE__,'views/templates/hook/card-pay.tpl'))
                       ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/powered_by_stripe.png'));
        $payment_options[] = $embeddedOption;
        }
        
        $customer_credit_card = Db::getInstance()->getValue('SELECT token FROM '._DB_PREFIX_.'stripepro_customer  WHERE id_customer = '.(int)$this->context->cookie->id_customer);
       if($customer_credit_card!=''){
        $embeddedOption = new PaymentOption();
        $embeddedOption->setModuleName('savedstripepro')->setCallToActionText($this->l('Quick Pay with existing card'))
                       ->setForm($this->display(__FILE__,'views/templates/hook/savedcard-pay.tpl'))
                       ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/powered_by_stripe.png'));
        $payment_options[] = $embeddedOption;
         }
      
       if(Configuration::get('STRIPE_CHKOUT_POPUP')){
           
        $embeddedOption = new PaymentOption();
        $embeddedOption->setModuleName('stripeCheckout')->setCallToActionText($this->l('Pay with Credit / Debit Card'))
                       ->setAdditionalInformation($this->display(__FILE__,'views/templates/hook/checkout.tpl'))
                       ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/powered_by_stripe.png'));
        $payment_options[] = $embeddedOption;
         }
         
         if(Configuration::get('STRIPE_ALLOW_APPLEPAY')){
           
        $embeddedOption = new PaymentOption();
        $embeddedOption->setModuleName('stripeApplePay')->setCallToActionText($this->l('Apple Pay'))
                       ->setAdditionalInformation($this->display(__FILE__,'views/templates/hook/apple-pay.tpl'))
                       ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/apple.png'));
        $payment_options[] = $embeddedOption;
         }

        return $payment_options;
    }
    
    public function getTemplateVars()
    {
        $amount = $this->context->cart->getOrderTotal();
        $currency = $this->context->currency->iso_code;

        $amount = $this->isZeroDecimalCurrency($currency) ? $amount : $amount * 100;
        $address_delivery = new Address($this->context->cart->id_address_delivery);

        $billing_address = array(
            'line1' => $address_delivery->address1,
            'line2' => $address_delivery->address2,
            'city' => $address_delivery->city,
            'zip_code' => $address_delivery->postcode,
            'country' => $address_delivery->country,
            'phone' => $address_delivery->phone ? $address_delivery->phone : $address_delivery->phone_mobile,
            'email' => $this->context->customer->email,
        );

        if (Configuration::get('PS_SSL_ENABLED')) {
            $domain = Tools::getShopDomainSsl(true);
        } else {
            $domain = Tools::getShopDomain(true);
        }
        
        $country = Country::getIsoById($address_delivery->id_country);
        $logo_url = (Configuration::get('STRIPE_POPUP_LOGO')=='' ? __PS_BASE_URI__.'img/'.Configuration::get('PS_LOGO'):Configuration::get('STRIPE_POPUP_LOGO'));
        $stripe_customer = Db::getInstance()->getRow('SELECT id_stripe_customer,token FROM '._DB_PREFIX_.'stripepro_customer WHERE id_customer = '.(int)$this->context->cookie->id_customer);
        if($stripe_customer['token']!=''){
        $credit_card = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripepro_transaction WHERE type="payment" && id_stripe_customer = "'.$stripe_customer['id_stripe_customer'].'" order by id_stripe_transaction desc');
        $credit_card['token'] = $stripe_customer['token'];
        }
                
        return array(
            'publishableKey' => Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PUBLIC_KEY_LIVE') : Configuration::get('STRIPE_PUBLIC_KEY_TEST'),
            'customer_name' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
            'currency' => $currency,
            'amount_ttl' => $amount,
            'baseDir' => $domain.__PS_BASE_URI__,
            'stripe_mode' => Configuration::get('STRIPE_MODE'),
            'module_dir' => $this->_path,
            'billing_address' => Tools::jsonEncode($billing_address,JSON_HEX_QUOT),
            'stripe_cc' => $this->_path."views/img/stripe-cc.png",
           'stripe_ps_version' => _PS_VERSION_,
           'stripe_allow_zip'  => Configuration::get('STRIPE_ALLOW_ZIP'),
           'stripe_allow_applepay'  => Configuration::get('STRIPE_ALLOW_APPLEPAY'),
           'cu_email' => $this->context->customer->email,
           'popup_title' => (!Configuration::get('STRIPE_POPUP_TITLE')?Configuration::get('PS_SHOP_NAME'):Configuration::get('STRIPE_POPUP_TITLE')),
           'popup_desc' => Configuration::get('STRIPE_POPUP_DESC'),
           'apple_pay_cart_total' => number_format((float)$this->context->cart->getOrderTotal(), 2, '.', ''),
           'country_iso_code' => $country,
           'credit_card' => $credit_card,
           'logo_url' =>  $logo_url,
           'popup_locale' => (Configuration::get('STRIPE_POPUP_LOCALE')=='auto'?$this->context->language->iso_code:Configuration::get('STRIPE_POPUP_LOCALE'))
        );
    }

    /**
     * Process a payment
     *
     * @param string $token Stripe Transaction ID (token)
     */
    public function processPayment(array $params)
    {
        @ini_set('display_errors', 'off');
        
        $token = $params['token'];
        $payment_src = $params['source_type'];
        
        include(dirname(__FILE__).'/lib/Stripe.php');
        \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));

        /* Case 1: Charge an existing customer (or create it and charge it) */
        /* Case 2: Just process the transaction, do not save Stripe customer's details */

            /* Get or Create a Stripe Customer */
            $stripe_customer = Db::getInstance()->getRow('
            SELECT id_stripe_customer, stripe_customer_id, token
            FROM '._DB_PREFIX_.'stripepro_customer
            WHERE id_customer = '.(int)$this->context->cookie->id_customer);

            if (!isset($stripe_customer['id_stripe_customer']))
            {
                try
                {
                    $customer_stripe = \Stripe\Customer::create(array(
                    'description' => $this->l('PrestaShop Customer ID:').' '.(int)$this->context->cookie->id_customer,
                    'source' => $token, 'email' => $this->context->customer->email));
                    $stripe_customer['stripe_customer_id'] = $customer_stripe->id;
                }
                catch (Exception $e)
                {
                    die(Tools::jsonEncode(array(
                              'code' => '0',
                              'msg' => $e->getMessage(),
                          )));
                }
             
                Db::getInstance()->Execute('
                INSERT INTO '._DB_PREFIX_.'stripepro_customer (id_stripe_customer, stripe_customer_id, token, id_customer, cc_last_digits, date_add)
                VALUES (NULL, \''.pSQL($stripe_customer['stripe_customer_id']).'\', \''.pSQL($token).'\', '.(int)$this->context->cookie->id_customer.', '.Tools::getValue('last4').', NOW())');
                $stripe_customer['id_stripe_customer'] = (int) Db::getInstance()->Insert_ID();
            }
            else
            {
                /* Update the credit card in the database */
                if ($token && $token != $stripe_customer['token'])
                {
                    try
                    {
                        $cu = \Stripe\Customer::retrieve($stripe_customer['stripe_customer_id']);
                        $cu->email = $this->context->customer->email;
                        $cu->source = $token;
                        $cu->save();
                    }
                    catch (Exception $e)
                    {
                        die(Tools::jsonEncode(array(
                              'code' => '0',
                              'msg' => $e->getMessage(),
                          )));
                    }
                 
                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'stripepro_customer SET token = "'.$token.'", `cc_last_digits`="'.Tools::getValue('last4').'" WHERE `id_stripe_customer` = '.$stripe_customer['id_stripe_customer']);
                }
            }                
            
                $cart_total = $params['amount'];
                $products = $this->context->cart->getProducts();
                $id_subs = array();
                foreach ($products as $product) // products refer to the cart details
                {
                    $product_subscription = Db::getInstance()->getRow('select * from `'._DB_PREFIX_.'stripepro_products` 
                    where `id_product`='.(int)$product['id_product'].' && `active`=1 && `id_subscription_product`!=""');
                    $plan_id = $product_subscription['id_subscription_product'];
                    $product_charge = $product_subscription['charge'];
                    
                    if($plan_id!=''){
                        /*$coupon_code = Db::getInstance()->getValue('SELECT `code` FROM `'._DB_PREFIX_.'cart_rule` WHERE `id_cart_rule` = (select `id_cart_rule` from `'._DB_PREFIX_.'cart_cart_rule` where id_cart='.$this->context->cart->id.')');
                        $coupon_code = ($coupon_code!=''?$coupon_code:'');*/
                        $subscription_result = $this->addStripeSubscription($stripe_customer['stripe_customer_id'],$plan_id,$product['quantity'],'', $product['id_product']);
                        if(!$subscription_result){
                              die(Tools::jsonEncode(array(
                              'code' => '0',
                              'msg' => $this->l("Unable to create a subscription please try again or try with a different card."),
                          )));
                        }else
                        $id_subs[] = $subscription_result;
                        
                        if(!$product_charge){
                            $p_total_wt = number_format((float)$product['total_wt'], 2, '.', '');
                            if (!$this->isZeroDecimalCurrency($this->context->currency->iso_code)) 
                               $p_total_wt *= 100;
                            $cart_total -= $p_total_wt;
                        }
                    }
                }
                        
                try
                {    
                    $result_json = '';
                   if (($this->isZeroDecimalCurrency($this->context->currency->iso_code) && $cart_total>1) || (!$this->isZeroDecimalCurrency($this->context->currency->iso_code) && $cart_total>50)) {
                    $charge_details = array('customer' => $stripe_customer['stripe_customer_id'], 'amount' => $cart_total, 'currency' => $this->context->currency->iso_code, 'description' => $this->l('PrestaShop Customer ID:').' '.(int)$this->context->cookie->id_customer.' - '.$this->l('PrestaShop Cart ID:').' '.(int)$this->context->cart->id, 'capture' => (Configuration::get('STRIPE_CAPTURE_TYPE')?true:false),"expand" =>array("balance_transaction"));
        
                    $result_json = \Stripe\Charge::create($charge_details);
                   }else
                   $order_status = (int)Configuration::get('STRIPE_PAYMENT_ORDER_STATUS');
                    
                // catch the stripe error the correct way.
                } catch (Exception $e) {
                    die(Tools::jsonEncode(array(
                              'code' => '0',
                              'msg' => $e->getMessage(),
                          )));
                }
                        

        /* Log Transaction details */
        if (isset($result_json->status) && $result_json->status == 'succeeded')
        {
            $order_status = (int)Configuration::get('STRIPE_PAYMENT_ORDER_STATUS');

            if ($result_json->source->address_zip_check == 'fail' || $result_json->source->cvc_check == 'fail')
                $order_status = (int)Configuration::get('STRIPE_PENDING_ORDER_STATUS');
        }
        
        if((in_array(false,$id_subs) || count($id_subs)==0) && !isset($result_json->id))
        $order_status = (int)Configuration::get('PS_OS_ERROR');
        
        if((isset($result_json->status) && $result_json->status!="succeeded"))
        $order_status = (int)Configuration::get('PS_OS_ERROR');

        $amt_paid = $this->isZeroDecimalCurrency($params['currency']) ? $params['amount'] : $params['amount'] / 100;
        parent::validateOrder(
            (int)$this->context->cart->id,
            (int)$order_status,
            (float)$amt_paid,
            $this->displayName,
            null,
            array(),
            null,
            false,
            $this->context->customer->secure_key
        );
        $id_order = Order::getOrderByCartId($this->context->cart->id);
        if (isset($result_json->id)) {
        $new_order = new Order((int)$id_order);
          if (Validate::isLoadedObject($new_order))
          {
              $payment = $new_order->getOrderPaymentCollection();
              if (isset($payment[0]))
              {
                  $payment[0]->transaction_id = pSQL($result_json->id);
                  $payment[0]->save();
              }
          }
        }

        /* Store the transaction details */
        if (isset($result_json->id)) {
            Db::getInstance()->Execute('
            INSERT INTO '._DB_PREFIX_.'stripepro_transaction (type,source,btc_address, id_stripe_customer, id_cart, id_order,
            id_transaction, amount, status, currency, cc_type, cc_exp, cc_last_digits, cvc_check, fee, mode, date_add)
            VALUES (\'payment\',"'.($payment_src=='applepay'?'applepay':$result_json->source->object).'","", '.(isset($stripe_customer['id_stripe_customer']) ? (int)$stripe_customer['id_stripe_customer'] : 0).', '.(int)$this->context->cart->id.', '.(int)$id_order.', \''.pSQL($result_json->id).'\',
            \''.($result_json->amount * 0.01).'\', \''.($result_json->paid == 'true' ? ($result_json->captured ? 'paid' : 'uncaptured'): 'unpaid').'\', \''.pSQL($result_json->currency).'\',
            \''.pSQL($result_json->source->brand).'\', \''.(int)$result_json->source->exp_month.'/'.(int)$result_json->source->exp_year.'\', '.(int)$result_json->source->last4.',
            '.($result_json->source->cvc_check == 'pass' ? 1 : 0).', \''.($result_json->balance_transaction->fee * 0.01).'\', \''.($result_json->livemode == 'true' ? 'live' : 'test').'\', NOW())');
        }
        
        if (count($id_subs)>0) {
            foreach($id_subs as $sub) {
             Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'stripepro_subs_order` (`id_stripe_subscription`, `id_order`)
                VALUES ("'.pSQL($sub).'", '.(int)$id_order.')');
            }
        }
        if (Configuration::get('PS_SSL_ENABLED')) {
            $domain = Tools::getShopDomainSsl(true);
        } else {
            $domain = Tools::getShopDomain(true);
        }

        /* Ajax redirection Order Confirmation */
        die(Tools::jsonEncode(array(
            'code' => '1',
            'url' => $domain.__PS_BASE_URI__.'/index.php?controller=order-confirmation&id_cart='.(int)$this->context->cart->id.'&id_module='.(int)$this->id.'&id_order='.(int)$id_order.'&key='.$this->context->customer->secure_key,
        )));      

    }
    
     public function isZeroDecimalCurrency($currency)
    {
        $zeroDecimalCurrencies = array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','VND','VUV','XAF','XOF','XPF');
        return in_array($currency, $zeroDecimalCurrencies);
    }

    /**
     * Check settings requirements to make sure the Stripe's module will work properly
     *
     * @return boolean Check result
     */
    public function checkSettings()
    {
        if (Configuration::get('STRIPE_MODE'))
            return Configuration::get('STRIPE_PUBLIC_KEY_LIVE') != '' && Configuration::get('STRIPE_PRIVATE_KEY_LIVE') != '';
        else
            return Configuration::get('STRIPE_PUBLIC_KEY_TEST') != '' && Configuration::get('STRIPE_PRIVATE_KEY_TEST') != '';
    }

    /**
     * Check technical requirements to make sure the Stripe's module will work properly
     *
     * @return array Requirements tests results
     */
    public function checkRequirements()
    {
        $tests = array('result' => true);
        $tests['curl'] = array('name' => $this->l('PHP cURL extension must be enabled on your server'), 'result' => extension_loaded('curl'));
        $tests['mbstring'] = array('name' => $this->l('PHP Multibyte String extension must be enabled on your server'), 'result' => extension_loaded('mbstring'));
        if (Configuration::get('STRIPE_MODE'))
            $tests['ssl'] = array('name' => $this->l('SSL must be enabled on your store (before entering Live mode)'), 'result' => Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) && Tools::strtolower($_SERVER['HTTPS']) != 'off'));
        $tests['php52'] = array('name' => $this->l('Your server must run PHP 5.4 or greater'), 'result' => version_compare(PHP_VERSION, '5.4', '>='));
        $tests['configuration'] = array('name' => $this->l('You must sign-up for Stripe and configure your account settings in the module (publishable key, secret key...etc.)'), 'result' => $this->checkSettings());

        foreach ($tests as $k => $test)
            if ($k != 'result' && !$test['result'])
                $tests['result'] = false;

        return $tests;
    }

    /**
     * Display the Back-office interface of the Stripe's module
     *
     * @return string HTML/JS Content
     */
    public function getContent()
    {        
        $errors = array();
        /* Update Configuration Values when settings are updated */
        if (Tools::isSubmit('SubmitStripe'))
        {    
            if (strpos(Tools::getValue('stripe_public_key_test'), "sk") !== false || strpos(Tools::getValue('stripe_public_key_live'), "sk") !== false ) {
                $errors[] = "You've entered your private key in the public key field!";
            }
            if (empty($errors)) {
                $configuration_values = array(
                    'STRIPE_MODE' => Tools::getValue('stripe_mode'),
                    'STRIPE_CAPTURE_TYPE' => Tools::getValue('STRIPE_CAPTURE_TYPE'),
                    'STRIPE_ALLOW_ZIP' =>Tools::getValue('STRIPE_ALLOW_ZIP'),
                    'STRIPE_SUBS_CANCEL_OPTN' =>Tools::getValue('STRIPE_SUBS_CANCEL_OPTN'),
                    'STRIPE_ALLOW_APPLEPAY' => Tools::getValue('STRIPE_ALLOW_APPLEPAY'),
                    'STRIPE_PUBLIC_KEY_TEST' => trim(Tools::getValue('stripe_public_key_test')),
                    'STRIPE_PUBLIC_KEY_LIVE' => trim(Tools::getValue('stripe_public_key_live')), 
                    'STRIPE_PRIVATE_KEY_TEST' => trim(Tools::getValue('stripe_private_key_test')),
                    'STRIPE_PRIVATE_KEY_LIVE' => trim(Tools::getValue('stripe_private_key_live')), 
                    'STRIPE_SUBS_CANCEL_MAIL' => (int)Tools::getValue('STRIPE_SUBS_CANCEL_MAIL'),
                    'STRIPE_SUBS_PAYMENT_ORDER_NEW' => (int)Tools::getValue('STRIPE_SUBS_PAYMENT_ORDER_NEW')
                );

                foreach ($configuration_values as $configuration_key => $configuration_value)
                    Configuration::updateValue($configuration_key, $configuration_value);
            }
        }
        if (Tools::isSubmit('SubmitOrderStatuses'))
        {    
                $configuration_values = array(
                    'STRIPE_PENDING_ORDER_STATUS' => (int)Tools::getValue('stripe_pending_status'),
                    'STRIPE_PAYMENT_ORDER_STATUS' => (int)Tools::getValue('stripe_payment_status'), 
                    'STRIPE_CHARGEBACKS_ORDER_STATUS' => (int)Tools::getValue('stripe_chargebacks_status'),
                    'STRIPE_SUBS_PAYMENT_ORDER_STATUS' => (int)Tools::getValue('stripe_subs_payment_status'),
                );

                foreach ($configuration_values as $configuration_key => $configuration_value)
                    Configuration::updateValue($configuration_key, $configuration_value);
        }
        if (Tools::isSubmit('SubmitStripeCheckout'))
        {    
                $configuration_values = array(
                    'STRIPE_POPUP_LOGO' => Tools::getValue('STRIPE_POPUP_LOGO'),
                    'STRIPE_CHKOUT_POPUP' =>Tools::getValue('STRIPE_CHKOUT_POPUP'),
                    'STRIPE_POPUP_TITLE' =>Tools::getValue('STRIPE_POPUP_TITLE'),
                    'STRIPE_POPUP_DESC' =>Tools::getValue('STRIPE_POPUP_DESC'),
                    'STRIPE_POPUP_LOCALE' => Tools::getValue('STRIPE_POPUP_LOCALE')
                );

                foreach ($configuration_values as $configuration_key => $configuration_value)
                    Configuration::updateValue($configuration_key, $configuration_value);
        }
        
        $requirements = $this->checkRequirements();
        $shopDomainSsl = Tools::getShopDomainSsl(true, true);
        $stripeBOCssUrl = $shopDomainSsl.__PS_BASE_URI__.'modules/'.$this->name.'/views/css/stripe-prestashop-admin.css';
        $logo_url = (Configuration::get('STRIPE_POPUP_LOGO')=='' ? __PS_BASE_URI__.'img/'.Configuration::get('PS_LOGO'):Configuration::get('STRIPE_POPUP_LOGO'));
        $statuses = OrderState::getOrderStates((int)$this->context->cookie->id_lang);
        $plans = Db::getInstance()->getValue("SELECT count(*) FROM "._DB_PREFIX_."stripepro_plans");
        $subs = Db::getInstance()->getValue("SELECT count(*) FROM "._DB_PREFIX_."stripepro_subscription where `status`!='canceled'");
        $statuses_options = array(array('name' => 'stripe_payment_status', 'label' => $this->l('Order status in case of sucessfull payment:'), 'current_value' => Configuration::get('STRIPE_PAYMENT_ORDER_STATUS')),array('name' => 'stripe_pending_status', 'label' => $this->l('Order status in case of unsucessfull address/zip-code check:'), 'current_value' => Configuration::get('STRIPE_PENDING_ORDER_STATUS')),array('name' => 'stripe_subs_payment_status', 'label' => $this->l('Order status in case of the sucessfull payment for a subscription (*Webhook req.):'), 'current_value' => Configuration::get('STRIPE_SUBS_PAYMENT_ORDER_STATUS')),array('name' => 'stripe_chargebacks_status', 'label' => $this->l('Order status in case of a chargeback (dispute) created (*Webhook req.):'), 'current_value' => Configuration::get('STRIPE_CHARGEBACKS_ORDER_STATUS')));
        $subs_products = Db::getInstance()->executeS("SELECT sp.*,pl.name FROM `"._DB_PREFIX_."stripepro_products` sp 
        LEFT JOIN `"._DB_PREFIX_."product_lang` pl ON (pl.id_product=sp.id_product && pl.id_lang=".(int)$this->context->cookie->id_lang.")
        where sp.id_shop = ".(int)$this->context->shop->id." && sp.`active`=1 group by sp.id_product"); 
        if(count($subs_products)>0)
            foreach($subs_products as $key=>$pro)
             $subs_products[$key]['editLink'] = $this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$pro['id_product']));
        $webhook_url = $this->context->link->getModuleLink('stripepro', 'webhook', array(), true).'?token='.Tools::safeOutput(Configuration::get('STRIPE_WEBHOOK_TOKEN'));

         $tplVars = array(
            'errors' => $errors,
            'plans' => $plans,
            'subs' => $subs,
            'subs_products' => $subs_products,
            'logo_url' => $logo_url,
            'webhook_url' => $webhook_url,
            'statuses' => $statuses,
            'statuses_options' => $statuses_options,
            'this_path' => $this->_path,
            'requirements' => $requirements,
            'checkSettings' => $this->checkSettings(),
            'stripeBOCssUrl' => $stripeBOCssUrl
        );
        
        if (Tools::isSubmit('SubmitStripe') || Tools::isSubmit('SubmitStripeCheckout') || Tools::isSubmit('SubmitOrderStatuses') || Tools::isSubmit('SubmitListPlans') || Tools::isSubmit('SubmitSubSync'))
            $tplVars['success'] = true;

        $this->context->smarty->assign($tplVars);
        return $this->display(__FILE__, 'views/templates/admin/settings.tpl');

    }

    public function hookBackOfficeHeader()
    {
        
        /* Update the Stripe Plans list */
        if(Tools::isSubmit('SubmitListPlans') || Tools::getValue('SubmitListPlans'))
         $this->listPlans();
         
         /* Update the Stripe Subscriptions for all existing customers */
        if(Tools::isSubmit('SubmitSubSync'))
         $this->syncAllSubscriptions();
        
        /* Continue if we are on the order's details page (Back-office) */
        if(Tools::getIsset('vieworder') && Tools::getIsset('id_order'))
        {
            $order = new Order((int)Tools::getValue('id_order'));

        /* If the "Refund" button has been clicked, check if we can perform a partial or full refund on this order */
        if (Tools::isSubmit('SubmitStripeRefund') && Tools::getIsset('stripe_amount_to_refund') && Tools::getIsset('id_transaction_stripe'))
        {
            /* Get transaction details and make sure the token is valid */
            $stripe_transaction_details = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripepro_transaction WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = \'payment\' AND status = \'paid\'');
            if (isset($stripe_transaction_details['id_transaction']) && $stripe_transaction_details['id_transaction'] === Tools::getValue('id_transaction_stripe'))
            {
                /* Check how much has been refunded already on this order */
                $stripe_refunded = Db::getInstance()->getValue('SELECT SUM(amount) FROM '._DB_PREFIX_.'stripepro_transaction WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = \'refund\' AND status = \'paid\'');
                if (Tools::getValue('stripe_amount_to_refund') <= number_format($stripe_transaction_details['amount'] - $stripe_refunded, 2, '.', ''))
                    $this->processRefund(Tools::getValue('id_transaction_stripe'), (float)Tools::getValue('stripe_amount_to_refund'), $stripe_transaction_details);
                else
                    $this->_errors['stripe_refund_error'] = $this->l('You cannot refund more than').' '.Tools::displayPrice($stripe_transaction_details['amount'] - $stripe_refunded).' '.$this->l('on this order');
            }
        }
        
        /* If the "Capture" button has been clicked, check if we can perform a partial or full capture on this order */
        if (Tools::isSubmit('SubmitStripeCapture') && Tools::getIsset('stripe_amount_to_capture') && Tools::getIsset('id_transaction_stripe'))
        {
            /* Get transaction details and make sure the token is valid */
            $stripe_transaction_details = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripepro_transaction WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = \'payment\' AND status = \'uncaptured\'');
            if (isset($stripe_transaction_details['id_transaction']) && $stripe_transaction_details['id_transaction'] === Tools::getValue('id_transaction_stripe'))
            {
                if (Tools::getValue('stripe_amount_to_capture') <= number_format($stripe_transaction_details['amount'], 2, '.', ''))
                    $this->processCapture(Tools::getValue('id_transaction_stripe'), (float)Tools::getValue('stripe_amount_to_capture'));
                else
                    $this->_errors['stripe_capture_error'] = $this->l('You cannot capture more than').' '.Tools::displayPrice($stripe_transaction_details['amount'] - $stripe_refunded).' '.$this->l('on this order');
            }
        }

        /* Check if the order was paid with Stripe and display the transaction details */
        if (Db::getInstance()->getValue('SELECT module FROM '._DB_PREFIX_.'orders WHERE id_order = '.(int)Tools::getValue('id_order')) == $this->name)
        {
            /* Get the transaction details */
            $stripe_transaction_details = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripepro_transaction WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = \'payment\' AND status IN (\'paid\',\'uncaptured\')');
            
            $stripe_subs = array();
            /* Get the subscriptions details */
            $subs_order = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'stripepro_subs_order WHERE id_order = '.(int)Tools::getValue('id_order').'');
            foreach($subs_order as $sub_order)
            $stripe_subs[] = $sub_order['id_stripe_subscription'];
            
            $stripe_subs = implode(',',$stripe_subs);
            if($stripe_subs!='')
            $stripe_subs = Db::getInstance()->executeS("SELECT a.*,CONCAT('<b>',b.`name`,'</b> (',UCASE(b.`currency`),' ',b.`amount`,'/ ',b.`interval_count`,b.`interval`,')') as plan FROM "._DB_PREFIX_."stripepro_subscription a LEFT JOIN "._DB_PREFIX_."stripepro_plans b  ON a.stripe_plan_id = b.stripe_plan_id WHERE a.id_stripe_subscription IN (".$stripe_subs.")");

            /* Get all the refunds previously made (to build a list and determine if another refund is still possible) */
            $stripe_refunded = 0;
            $output_refund = '';
            $stripe_refund_details = Db::getInstance()->ExecuteS('SELECT amount, status, date_add FROM '._DB_PREFIX_.'stripepro_transaction
            WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = \'refund\' ORDER BY date_add DESC');
            foreach ($stripe_refund_details as $stripe_refund_detail)
            {
                $stripe_refunded += ($stripe_refund_detail['status'] == 'paid' ? $stripe_refund_detail['amount'] : 0);
                $output_refund .= '<tr'.($stripe_refund_detail['status'] != 'paid' ? ' style="background: #FFBBAA;"': '').'><td>'.
                Tools::safeOutput($stripe_refund_detail['date_add']).'</td><td style="">'.Tools::displayPrice($stripe_refund_detail['amount'], (int)$order->id_currency).
                '</td><td>'.($stripe_refund_detail['status'] == 'paid' ? $this->l('Processed') : $this->l('Error')).'</td></tr>';
            }
            $currency = new Currency((int)$order->id_currency);
            $c_char = $currency->sign;
            $output = '
            <script type="text/javascript">
                $(document).ready(function() {
                    var appendEl;
                    if ($(\'select[name=id_order_state]\').is(":visible")) {
                        appendEl = $(\'select[name=id_order_state]\').parents(\'form\').after($(\'<div/>\'));
                    } else {
                        appendEl = $("#status");
                    }
                    $(\'<div class="panel panel-highlighted" style="padding: 5px 10px;"><fieldset'.(_PS_VERSION_ < 1.5 ? ' style="width: 400px;"' : '').'><legend><img src="../img/admin/money.gif" alt="" />&nbsp;'.$this->l('Stripe Payment/ Subscription Details').'</legend>';
                    
            if (is_array($stripe_subs) && count($stripe_subs)>0){
                $output .= '<u>'.$this->l('Stripe Subscriptions added:').'</u><br><br><ol>';
                    foreach ($stripe_subs as $sub)
                    $output .= '<li>'.$sub['stripe_subscription_id'].' - '.$sub['plan'].' - <b style="color:'.($sub['status']=='active'?'green':'orange').'">'.$sub['status'].'</b> - '.$this->l('Qty').': <b>'.$sub['quantity'].'</b></li>';
                $output .='</ol><a href="'.$this->context->link->getAdminLink("AdminCustomers").'&id_customer='.(int)$order->id_customer.'&viewcustomer">'.$this->l('Manage all subscription for this customer').'</a><hr>';
                }

            if (!empty($stripe_transaction_details['id_transaction'])){
                $output .= $this->l('Stripe Transaction ID:').' <b>'.Tools::safeOutput($stripe_transaction_details['id_transaction']).'</b><br />'.
                $this->l('Payment Source:').' <b> '.($stripe_transaction_details['source'] == 'applepay' ? 'Apple Pay':'Credit Card').'</b><br /><br />'.
                $this->l('Status:').' <span style="font-weight: bold; color: '.($stripe_transaction_details['status'] == 'paid' ? 'green;">'.$this->l('Paid') : '#CC0000;">'.$this->l('Unpaid')).'</span><br />'.
                $this->l('Amount:').' <b>'.Tools::displayPrice($stripe_transaction_details['amount'], (int)$order->id_currency).'</b><br />'.
                $this->l('Processed on:').' <b>'.Tools::safeOutput($stripe_transaction_details['date_add']).'</b><br />';
                
                if($stripe_transaction_details['source']=='card' || $stripe_transaction_details['source']=='applepay'){
                $output .= $this->l('Credit card:').' <b>'.Tools::safeOutput($stripe_transaction_details['cc_type']).'</b> ('.$this->l('Exp.:').' '.Tools::safeOutput($stripe_transaction_details['cc_exp']).')<br />'.$this->l('Last 4 digits:').' <b>xxxx xxxx xxxx  '.sprintf('%04d', $stripe_transaction_details['cc_last_digits']).' </b><br>'.$this->l('CVC Check:').' <b>'.($stripe_transaction_details['cvc_check'] || $stripe_transaction_details['source'] == 'applepay' ? $this->l('OK') : '<span style="color: #CC0000;">'.$this->l('FAILED').'</span>').'</b><br />';
                }
                
                $output .= $this->l('Processing Fee:').' <b>'.Tools::displayPrice($stripe_transaction_details['fee'], (int)$order->id_currency).'</b><br /><br />'.
                $this->l('Mode:').' <span style="font-weight: bold; color: '.($stripe_transaction_details['mode'] == 'live' ? 'green;">'.$this->l('Live') : '#CC0000;">'.$this->l('Test (You will not receive any payment, until you enable the "Live" mode)')).'</span>';
            }
            if (empty($stripe_transaction_details['id_transaction']) && !is_array($stripe_subs) && count($stripe_subs)==0)
                $output .= '<b style="color: #CC0000;">'.$this->l('Warning:').'</b> '.$this->l('The customer paid using Stripe and an error occured (check details at the bottom of this page)');
                
                 $output .= '</fieldset><br />';
                 if(Tools::getIsset('SubmitStripeCapture')){
                 $output .= '<div  class="bootstrap">'.((empty($this->_errors['stripe_capture_error']) && Tools::getIsset('id_transaction_stripe') && Tools::getIsset('SubmitStripeCapture')) ? '<div class="conf confirmation alert alert-success">'.$this->l('Your capture was successfully processed').'</div>' : '').
            (!empty($this->_errors['stripe_capture_error']) ? '<div style="color: #CC0000; font-weight: bold;" class="alert alert-danger">'.$this->l('Error:').' '.Tools::safeOutput($this->_errors['stripe_capture_error']).'</div>' : '').'</div>';
                 }
            
           if($stripe_transaction_details['status'] == 'uncaptured'){
               
               $date2 = $stripe_transaction_details['date_add']; 
               $diff = strtotime($date2 ."+7 days") - strtotime('now');
               
               $secondsInAMinute = 60;
               $secondsInAnHour  = 60 * $secondsInAMinute;
               $secondsInADay    = 24 * $secondsInAnHour;

              // extract days
              $days = floor($diff / $secondsInADay);
              // extract hours
              $hourSeconds = $diff % $secondsInADay;
              $hours = floor($hourSeconds / $secondsInAnHour);

              $timeleft = $days ." days & ". $hours." hrs";
       
            $output .= '<fieldset'.(_PS_VERSION_ < 1.5 ? ' style="width: 400px;"' : '').'><legend><img src="../img/admin/money.gif" alt="" />&nbsp;'.$this->l('Proceed to a full or partial capture via Stripe').'</legend>';
            if($diff>0){
            $output .= '<form action="" method="post">'.$this->l('Capture:').' $ <input type="text" value="'.number_format($stripe_transaction_details['amount'], 2, '.', '').'" name="stripe_amount_to_capture" style="display: inline-block; width: 60px;" /> <input type="hidden" name="id_transaction_stripe" value="'.Tools::safeOutput($stripe_transaction_details['id_transaction']).'" /><input type="submit" class="button" onclick="return confirm(\\\''.addslashes($this->l('Do you want to proceed to this capture?')).'\\\');" name="SubmitStripeCapture" value="'.$this->l('Process Capture').'" /></form><font style="color:red;font-size:13px;"> <br>'.$this->l('NOTE: Time left to Capture payment:').' <b>'.$timeleft.'</b> '.$this->l('otherwise payment will be automatically refunded.').'</font>';}else
            $output .= '<font style="color:red;"> <b>'.$this->l('7 days has been passed so the payment has been refunded.')."</font></b>";
            
            $output .= '</fieldset><br /></div>\').appendTo(appendEl);
                });
            </script>';
                }else {

            $output .= '</fieldset><fieldset'.(_PS_VERSION_ < 1.5 ? ' style="width: 400px;"' : '').'  class="bootstrap'.(empty($stripe_transaction_details['id_transaction'])?' hidden':'').'"><legend><img src="../img/admin/money.gif" alt="" />&nbsp;'.$this->l('Proceed to a full or partial refund via Stripe').'</legend>';
            if(Tools::getIsset('SubmitStripeRefund')){
            $output .= ((empty($this->_errors['stripe_refund_error']) &&  Tools::getIsset('id_transaction_stripe')) ? '<div class="conf confirmation alert alert-success">'.$this->l('Your refund was successfully processed').'</div>' : '').
            (!empty($this->_errors['stripe_refund_error']) ? '<div style="color: #CC0000; font-weight: bold;" class="alert alert-danger">'.$this->l('Error:').' '.Tools::safeOutput($this->_errors['stripe_refund_error']).'</div>' : '');}
            $output .= $this->l('Already refunded:').' <b>'.Tools::displayPrice($stripe_refunded, (int)$order->id_currency).'</b><br /><br />'.($stripe_refunded ? '<table class="table" cellpadding="0" cellspacing="0" style="font-size: 12px;"><tr><th>'.$this->l('Date').'</th><th>'.$this->l('Amount refunded').'</th><th>'.$this->l('Status').'</th></tr>'.$output_refund.'</table><br />' : '').
            ($stripe_transaction_details['amount'] > $stripe_refunded ? '<form action="" method="post">'.$this->l('Refund:'). ' ' . $c_char .' <input type="text" value="'.number_format($stripe_transaction_details['amount'] - $stripe_refunded, 2, '.', '').
            '" name="stripe_amount_to_refund" style="display: inline-block; width: 60px;" /> <input type="hidden" name="id_transaction_stripe" value="'.
            Tools::safeOutput($stripe_transaction_details['id_transaction']).'" /><input type="submit" class="button" onclick="return confirm(\\\''.addslashes($this->l('Do you want to proceed to this refund?')).'\\\');" name="SubmitStripeRefund" value="'.
            $this->l('Process Refund').'" /></form>' : '').'<br /></fieldset></div>\').appendTo(appendEl);
                });
            </script>';
        }

            return $output;
       }
        
      }
      
      if(Tools::getIsset('viewcustomer') && Tools::getIsset('id_customer'))
       {    /* Continue if we are on the Customer's details page (Back-office) */
       
         $stripe_customer_id = Db::getInstance()->getValue('SELECT `stripe_customer_id` FROM '._DB_PREFIX_.'stripepro_customer WHERE id_customer = '.(int)Tools::getValue('id_customer'));
         /* Update the Stripe Subscriptions for all existing customers */
        if(Tools::isSubmit('SubmitCusSubSync'))
         $this->syncSubscriptions($stripe_customer_id);
         
          /* "Add Subsciption" button click will perform the task of adding new subscription to the customer */
        if (Tools::isSubmit('SubmitAddSub')){
            if(Tools::getValue('id_product')=='' || Tools::getValue('id_order')=='') {
            $this->_errors['stripe_subscription_error'] = $this->l('Please select any recurring product and enter existing order ID', 'stripepro');
            } else {
            $id_stripe_plan = Db::getInstance()->getValue('SELECT `id_subscription_product` FROM '._DB_PREFIX_.'stripepro_products WHERE id_product = '.(int)Tools::getValue('id_product'));
            $id_stripe_subscription = $this->addStripeSubscription($stripe_customer_id,$id_stripe_plan,1,'',(int)Tools::getValue('id_product'));
            Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'stripepro_subs_order` (`id_stripe_subscription`, `id_order`)
                VALUES ("'.pSQL($id_stripe_subscription).'", '.(int)Tools::getValue('id_order').')');
                }
        }
            
           /* "Cancel Subsciption" button click will perform the task of cancelling new subscription to the customer */
        if(Tools::isSubmit('SubmitCancelSub'))
            $this->cancelSubscription($stripe_customer_id,Tools::getValue('stripe_subscription_id'));
            
        /* "Cancel Subsciption as period ends" button click will perform the task of cancelling new subscription to the customer */
        if(Tools::isSubmit('SubmitCancelSubAtPeriodEnd'))
            $this->cancelSubscription($stripe_customer_id,Tools::getValue('stripe_subscription_id'),true);
            
          /* Get the subscription details */
            $stripe_subscription_details = Db::getInstance()->executeS("SELECT a.*,CONCAT('<b>',b.`name`,'</b> (',UCASE(b.`currency`),' ',b.`amount`,'/ ',b.`interval_count`,b.`interval`,')') as plan,b.trial_period_days as trial_days FROM "._DB_PREFIX_."stripepro_subscription a LEFT JOIN "._DB_PREFIX_."stripepro_plans b  ON a.stripe_plan_id = b.stripe_plan_id WHERE a.id_customer = ".(int)Tools::getValue('id_customer'));
             
         $output = '
            <script type="text/javascript">
                $(document).ready(function() {
                    var prependEl = $("#container-customer");
                    $(\'';
        
         $output .= '<div class="bootstrap"><div class="col-lg-12"><div class="panel panel-highlighted">';
         $output .= '<fieldset'.(_PS_VERSION_ < 1.5 ? ' style="width: 400px;"' : '').'><legend><img src="'.$this->_path.'views/img/stripe-icon.gif" alt="">&nbsp;'.$this->l('Stripe Subscriptions').'</legend>';
         
         if (!empty($stripe_customer_id)){
             
             if(Tools::getIsset('SubmitAddSub') || Tools::getIsset('SubmitCancelSub') || Tools::getIsset('SubmitListPlans') || Tools::getIsset('SubmitCusSubSync') || Tools::getIsset('SubmitSubSync')){
             $output .= (empty($this->_errors['stripe_subscription_error']) ? '<div class="conf confirmation alert alert-success">'.$this->l('Your request was successfully processed').'</div>' : '').
                (!empty($this->_errors['stripe_subscription_error']) ? '<div style="color: #CC0000; font-weight: bold;" class="alert alert-danger">'.$this->l('Error:').' '.Tools::safeOutput($this->_errors['stripe_subscription_error']).'</div>' : '');}
                
            $output .= '<form action="" method="post" style="float:left;">';
            
            $subs_products = Db::getInstance()->executeS("SELECT sp.*,pl.name FROM `"._DB_PREFIX_."stripepro_products` sp 
        LEFT JOIN `"._DB_PREFIX_."product_lang` pl ON (pl.id_product=sp.id_product && pl.id_lang=".(int)$this->context->cookie->id_lang.")
        where sp.`active`=1 && sp.id_subscription_product!=''");

             if(!empty($subs_products))
             { 
             $output .= '<select name="id_product" style="width:250px; float:left;"><option value="">Select a recurring product...</option>';
             foreach($subs_products as $product)
             $output .= '<option value="'.$product['id_product'].'">'.$product['name'].'</option>';
             $output .= '</select>&nbsp;&nbsp;';
             $output .= '<span style="float:left;line-height: 30px;"><b>&nbsp;&nbsp;'.$this->l('Order ID:').'</b></span><input name="id_order" style="width:100px; float:left;" type="text" value="">';

             $output .= '&nbsp;<input type="submit" class="button btn btn-default" onclick="return confirm(\\\''.addslashes($this->l('Do you want to proceed to add subscription?')).'\\\');" name="SubmitAddSub" value="'.$this->l('Add Subscription').'" />';
                    }else
                    $output .= '<div  class="information alert alert-info">'.$this->l('Please configure a recurring product first in order to create subscriptions from here.').'</div>';
             
              
             $output .= '</form><form action="" method="post" style="border-left:2px solid #cdcdcd;margin-left:25px;float:left; padding-left:20px;">&nbsp;&nbsp;<input type="submit" class="button btn btn-default" onclick="return confirm(\\\''.addslashes($this->l('Do you want to proceed to Sync subscription?')).'\\\');" name="SubmitCusSubSync" value="'.$this->l('Sync Subscriptions for this customer').'" /></form><br /><hr style="clear:both;" />';
             
             foreach($stripe_subscription_details as $subscription)
              {$output .= '<form action="" method="post" style="background:#fff;float:left; border:1px solid #cdcdcd; padding:5px 10px;height: 185px;"><input type="hidden" name="stripe_subscription_id" value="'.$subscription['stripe_subscription_id'].'"><table cellpadding="10" cellspacing="10"><tr><td>'.$this->l('Subscription ID').':</td><td>&nbsp;<b>'.$subscription['stripe_subscription_id'].'</td></tr><tr><td>'.$this->l('Plan').':</td><td style="color:brown;">&nbsp;'.($subscription['plan']==''?$subscription['stripe_plan_id']:$subscription['plan']).'</td></tr><tr><td>'.$this->l('Trial Period').':</td><td>&nbsp;<b>'.$subscription['trial_days'].' '.$this->l('Days').'</td></tr><tr><td>'.$this->l('Quantity').':</td><td>&nbsp;<b>'.$subscription['quantity'].'</td></tr><tr><td>'.$this->l('Current Period').':</td><td>&nbsp;<b>'.date('M d, Y',$subscription['current_period_start']).' '.$this->l('to').' '.date('M d, Y',$subscription['current_period_end']).'</td></tr><tr><td>'.$this->l('Started on').':</td><td>&nbsp;<b>'.date('M d, Y',$subscription['start']).'</td></tr><tr class="'.($subscription['canceled_at']==''?'hidden':'').'"><td>'.$this->l('Canceled at').':</td><td>&nbsp;<b>'.date('M d, Y',$subscription['canceled_at']).'</td></tr><tr class="'.(($subscription['cancel_at_period_end']==0 || $subscription['canceled_at']=='')?'hidden':'').'"><td>'.$this->l('Switch Off date').':</td><td>&nbsp;<b>'.($subscription['cancel_at_period_end']==0?date('M d, Y',$subscription['canceled_at']):date('M d, Y',$subscription['current_period_end'])).'</td></tr><tr><td>'.$this->l('Status').':</td><td>&nbsp;<b style="color:'.($subscription['status']=='active'?'#71B238':($subscription['status']=='canceled'?'red':'orange')).'">'.Tools::strtoupper($subscription['status']).'</b>'.($subscription['cancel_at_period_end']==1 && $subscription['status']=='active'?$this->l(' - Will cancel at period end'):'').'</td></tr></table>'.($subscription['status']=='canceled' || $subscription['cancel_at_period_end']==1?'':'<br>').'<input type="submit" class="button btn btn-default '.($subscription['status']=='canceled' || $subscription['cancel_at_period_end']==1?'hidden':'').'" onclick="return confirm(\\\''.addslashes($this->l('Do you want to Cancel this subscription when period ends?')).'\\\');" name="SubmitCancelSubAtPeriodEnd" value="'.$this->l('Auto Cancel at period end').'" />&nbsp;&nbsp;<input type="submit" class="button btn btn-primary pull-right '.($subscription['status']=='canceled' || $subscription['cancel_at_period_end']==1?'hidden':'').'" onclick="return confirm(\\\''.addslashes($this->l('Do you want to Cancel this subscription?')).'\\\');" name="SubmitCancelSub" value="'.$this->l('Cancel Now').'" /></form>';
              }
             $output .= '</fieldset></div></div></div><div class="clear"></div><div class="separation"></div>\').prependTo(prependEl);
                                  });
                              </script>';
                }else{
                $output .= '<div style="color: #CC0000; font-weight: bold;" class="alert alert-danger">'.$this->l('This customer do not have any Stripe account.').'</div></fieldset></div></div></div><div class="clear"></div><div class="separation"></div>\').prependTo(prependEl);});</script>';
       }
                
            return $output;
    }
        
   }
   
    /**
     * Add subscription to a plan    
     *
     * @param string $stripe_customer_id Stripe Customer ID
     * @param string $stripe_plan_id Stripe Plan ID
     */

    public function addStripeSubscription($stripe_customer_id, $stripe_plan_id, $qty = 1, $coupon = '', $id_product = 0)
    {        
        if(Tools::getIsset('id_customer'))
        $id_customer = (int)Tools::getValue('id_customer');
        else
        $id_customer = (int)$this->context->cookie->id_customer;
                
        include_once(dirname(__FILE__).'/lib/Stripe.php');
        \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));
              
        /* Try to process the capture and catch any error message */
        try
        {  
              $customer = \Stripe\Customer::retrieve($stripe_customer_id);
              
              if($coupon!='')
              $result_json = $customer->subscriptions->create(array("plan" => $stripe_plan_id,"quantity" => $qty, "coupon" => $coupon));
              else
              $result_json = $customer->subscriptions->create(array("plan" => $stripe_plan_id,"quantity" => $qty));
            
        }
        catch (Exception $e)
        {

            $this->_errors['stripe_subscription_error'] = $e->getMessage();
            if (class_exists('Logger'))
                Logger::addLog($this->l('Stripe - subscription failed').' '.$e->getMessage(), 1, null, 'Customer', $id_customer, true);
        }
        
        if(!isset($this->_errors['stripe_subscription_error'])){
        Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'stripepro_subscription (stripe_subscription_id, stripe_customer_id, id_customer, id_product, stripe_plan_id, quantity, start, current_period_start, current_period_end, status, date_add) VALUES (\''.pSQL($result_json->id).'\', \''.pSQL($stripe_customer_id).'\', '.(int)$id_customer.', '.(int)$id_product.',"'.pSQL($stripe_plan_id).'", '.(int)$result_json->quantity.', \''.(int)$result_json->start.'\', \''.(int)$result_json->current_period_start.'\',
        \''.(int)$result_json->current_period_end.'\', \''.pSQL($result_json->status).'\', NOW())');
        return Db::getInstance()->getValue("SELECT MAX(`id_stripe_subscription`) FROM `"._DB_PREFIX_."stripepro_subscription`");
        }

        return false;
    }
    
    /**
     * Cancel subscription for a customer    
     *
     * @param string $stripe_customer_id Stripe Customer ID
     * @param string $stripe_plan_id Stripe Plan ID
     */

    public function cancelSubscription($stripe_customer_id,$stripe_subscription_id,$cancel_at_period_end = false)
    {    
        include_once(dirname(__FILE__).'/lib/Stripe.php');
        \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));

        /* Try to process the capture and catch any error message */
        try
        {
            $cu = \Stripe\Customer::retrieve($stripe_customer_id);
            $result_json = $cu->subscriptions->retrieve($stripe_subscription_id)->cancel(array('at_period_end'=>$cancel_at_period_end));
            
        }
        catch (Exception $e)
        {

            $this->_errors['stripe_subscription_error'] = $e->getMessage();
            if (class_exists('Logger'))
                Logger::addLog($this->l('Stripe - subscription cancelation failed').' '.$e->getMessage(), 1, null, 'Customer', (int)Tools::getIsset('id_customer'), true);
        }
        
        if(!isset($this->_errors['stripe_subscription_error']) && $result_json->id!='')
        {
            if(Tools::getIsset('id_customer'))
            $id_customer = (int)Tools::getValue('id_customer');
            else
            $id_customer = (int)$this->context->cookie->id_customer;
            
            $customer = new Customer((int)$id_customer);
            
            $vars = array(
                '{name}' => $customer->firstname.' '.$customer->lastname,
            );
            
            if(Configuration::get('STRIPE_SUBS_CANCEL_MAIL'))
            Mail::Send(
                (int)$this->context->cookie->id_lang,
                'cancel_subscription',
                Mail::l('Subscription Canceled.'),
                $vars,
                $customer->email,
                $customer->firstname.' '.$customer->lastname,
                null,
                null,
                null,
                null,
                dirname(__FILE__).'/mails/');
                
            if($cancel_at_period_end)
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripepro_subscription` SET `cancel_at_period_end`=1, `canceled_at`=\''.$result_json->canceled_at.'\' where `stripe_subscription_id`=\''.pSQL($stripe_subscription_id).'\'');
            else
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripepro_subscription` SET `status`= \''.$result_json->status.'\', `canceled_at`=\''.$result_json->canceled_at.'\' where `stripe_subscription_id`=\''.pSQL($stripe_subscription_id).'\'');

        return true;
        
        }
        
        return false;
    }
    
    /**
     * Add subscription to a plan    
     *
     * @param string $stripe_customer_id Stripe Customer ID
     * @param string $stripe_plan_id Stripe Plan ID
     */

    public function listPlans()
    {
    
         include_once(dirname(__FILE__).'/lib/Stripe.php');
        \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));

		$result_json = \Stripe\Plan::all(array("limit" => 100));

		if(isset($result_json->data) && count($result_json->data)>0){
            Db::getInstance()->Execute('TRUNCATE TABLE '._DB_PREFIX_.'stripepro_plans');
            foreach($result_json->data as $plan)
               Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'stripepro_plans (`stripe_plan_id`, `name`, `interval`, `amount`, `currency`, `interval_count`, `trial_period_days`) VALUES ("'.pSQL($plan->id).'", "'.pSQL($plan->name).'", "'.pSQL($plan->interval).'",'.sprintf("%.2f", $plan->amount / 100).', "'.pSQL($plan->currency).'", '.(int)$plan->interval_count.','.(int)$plan->trial_period_days.')');
        }
		
		while ($result_json->has_more){
			$result_json = \Stripe\Plan::all(array("limit" => 100, "starting_after" => $plan->id));
			if(isset($result_json->data) && count($result_json->data)>0)
               foreach($result_json->data as $plan)
               Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'stripepro_plans (`stripe_plan_id`, `name`, `interval`, `amount`, `currency`, `interval_count`, `trial_period_days`) VALUES ("'.pSQL($plan->id).'", "'.pSQL($plan->name).'", "'.pSQL($plan->interval).'",'.sprintf("%.2f", $plan->amount / 100).', "'.pSQL($plan->currency).'", '.(int)$plan->interval_count.','.(int)$plan->trial_period_days.')');
		  
		}
    
    return true;
    
    }
    
    /**
     * Synchronize subscriptions for a customer    
     *
     * @param string $stripe_subscription_id Stripe Subscription ID
     */

    public function syncSubscriptions($stripe_customer_id)
    {
        
        include_once(dirname(__FILE__).'/lib/Stripe.php');
        \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));
        
        /* Try to process the capture and catch any error message */
        try
        {
            $result_json = \Stripe\Customer::retrieve($stripe_customer_id);
        }
        catch (Exception $e)
        {
            $this->_errors['stripe_subscription_error'] = $e->getMessage();
            if (class_exists('Logger'))
                Logger::addLog($this->l('Stripe - Subscription update failed').' '.$e->getMessage(), 1, null, 'Customer', (int)Tools::getIsset('id_customer'), true);
        }
        
        if(!isset($this->_errors['stripe_subscription_error'])){
            $subs = $result_json->subscriptions->data;
            
            if(count($subs)>0)
            foreach($subs as $sub){
                Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripepro_subscription` SET `stripe_plan_id`=\''.$sub->plan->id.'\',`quantity` = \''.$sub->quantity.'\',`start`='.$sub->start.', `current_period_start`='.$sub->current_period_start.',`current_period_end`='.$sub->current_period_end.',`canceled_at`="'.$sub->canceled_at.'",`status`=\''.$sub->status.'\' WHERE `stripe_subscription_id` = \''.$sub->id.'\' AND `stripe_customer_id` = \''.$sub->customer.'\'');
        
             }
            }
    return true;
    }
    
    /**
     * Synchronize subscriptions for all customers
     */

    public function syncAllSubscriptions()
    {
        
        @ini_set('max_execution_time', 1000);
        
        include_once(dirname(__FILE__).'/lib/Stripe.php');
        \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));
        
        $stripe_customers = Db::getInstance()->ExecuteS('SELECT stripe_customer_id, id_customer FROM `'._DB_PREFIX_.'stripepro_customer` UNION SELECT stripe_customer_id,id_customer FROM `'._DB_PREFIX_.'stripepro_subscription` ');
                
        foreach($stripe_customers as $stripe_customer){

        /* Try to process the capture and catch any error message */
        try
        {
            $result_json = \Stripe\Customer::retrieve($stripe_customer['stripe_customer_id']);
                        
        }
        catch (Exception $e)
        {

            $this->_errors['stripe_subscription_error'] = $e->getMessage();
            if (class_exists('Logger'))
                Logger::addLog($this->l('Stripe - Subscription update failed').' '.$e->getMessage(), 1, null, 'Customer', (int)Tools::getIsset('id_customer'), true);
        }
        
        if(!isset($this->_errors['stripe_subscription_error'])){
            
            $subs = $result_json->subscriptions->data;
            
            foreach($subs as $sub){
              Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripepro_subscription` SET `stripe_plan_id`=\''.$sub->plan->id.'\',`quantity` = \''.$sub->quantity.'\',`start`='.$sub->start.', `current_period_start`='.$sub->current_period_start.',`current_period_end`='.$sub->current_period_end.',`canceled_at`="'.$sub->canceled_at.'",`status`=\''.$sub->status.'\' WHERE `stripe_subscription_id` = \''.$sub->id.'\' AND `stripe_customer_id` = \''.$sub->customer.'\'');
             }
            }        

        }
    return true;
    
    }
    /**
     * Process a partial or full capture
     *
     * @param string $id_transaction_stripe Stripe Transaction ID (token)
     * @param float $amount Amount to capture
     * @param array $original_transaction Original transaction details
     */

    public function processCapture($id_transaction_stripe, $amount)
    {
        
        include_once(dirname(__FILE__).'/lib/Stripe.php');
        \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));

        /* Try to process the capture and catch any error message */
        try
        {
            $charge = \Stripe\Charge::retrieve($id_transaction_stripe);
            $result_json = $charge->capture(array('amount' => $amount * 100));
            
        }
        catch (Exception $e)
        {

            $this->_errors['stripe_capture_error'] = $e->getMessage();
            if (class_exists('Logger'))
                Logger::addLog($this->l('Stripe - Capture transaction failed').' '.$e->getMessage(), 1, null, 'Cart', (int)$this->context->cart->id, true);
        }
        
        if(!isset($this->_errors['stripe_capture_error']) && $result_json->captured==true){
            $query = 'UPDATE ' . _DB_PREFIX_ . 'stripepro_transaction SET `status` = \'paid\', `amount` = ' . $amount . ' WHERE `id_transaction` = \''. pSQL($id_transaction_stripe).'\'';
            if(!Db::getInstance()->Execute($query))
            return false;
           }
        
        return true;
    }
    
    public function processRefund($id_transaction_stripe, $amount, $original_transaction)
    {
            
        include(dirname(__FILE__).'/lib/Stripe.php');
        \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));

        /* Try to process the refund and catch any error message */
        try
        {
            $charge = \Stripe\Charge::retrieve($id_transaction_stripe);
            if($original_transaction['source']=="card")
            $result_json = $charge->refund(array('amount' => $amount * 100));
            else
            $result_json = $charge->refunds->create(array('amount' => $amount * 100,"refund_address" => $original_transaction['btc_address']));
        }
        catch (Exception $e)
        {
            $this->_errors['stripe_refund_error'] = $e->getMessage();
            if (class_exists('Logger'))
                Logger::addLog($this->l('Stripe - Refund transaction failed').' '.$e->getMessage(), 2, null, 'Cart', (int)$this->context->cart->id, true);
        }
        
        if(!isset($this->_errors['stripe_refund_error']))
        Db::getInstance()->Execute('
        INSERT INTO '._DB_PREFIX_.'stripepro_transaction (type, source, id_stripe_customer, id_cart, id_order,
        id_transaction, amount, status, currency, cc_type, cc_exp, cc_last_digits, fee, mode, date_add)
        VALUES (\'refund\',\''.pSQL($original_transaction['source']).'\', '.(int)$original_transaction['id_stripe_customer'].', '.(int)$original_transaction['id_cart'].', '.
        (int)$original_transaction['id_order'].', \''.pSQL($id_transaction_stripe).'\',
        \''.(float)$amount.'\', \''.(!isset($this->_errors['stripe_refund_error']) ? 'paid' : 'unpaid').'\', \''.pSQL($result_json->currency).'\',
        \'\', \'\', 0, 0, \''.(Configuration::get('STRIPE_MODE') ? 'live' : 'test').'\', NOW())');
    }
}