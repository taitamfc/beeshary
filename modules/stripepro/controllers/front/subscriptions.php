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

class StripeProSubscriptionsModuleFrontController extends ModuleFrontController
{
	 public function __construct()
    {
        $this->auth = true;
        parent::__construct();
    }
	
    public function initContent()
    {
        parent::initContent();
        
        if(Tools::isSubmit('SubmitCancelSub') || Tools::isSubmit('SubmitCancelSubAtPeriodEnd')){
			
			$stripe_subscription_id = Tools::getValue('stripe_subscription_id'); 
			$stripe = new StripePro();       
			$stripe_customer_id = Db::getInstance()->getValue('select `stripe_customer_id` from `'._DB_PREFIX_.'stripepro_subscription` where `stripe_subscription_id`="'.pSQL($stripe_subscription_id).'"');
			if($stripe_customer_id!='')
			if($stripe->cancelSubscription($stripe_customer_id,$stripe_subscription_id,(Tools::isSubmit('SubmitCancelSubAtPeriodEnd')?true:false)))
			$this->context->smarty->assign('confirmation',1);
			else
			$this->errors[] = $this->module->l('Cannot cancel this subscription. Please contact support.', 'stripepro');
			
        }
        
        $subscriptions = Db::getInstance()->executeS("SELECT a.*,p.name p_name,p.link_rewrite p_link_rewrite,CONCAT(b.`name`,' - (',UCASE(b.`currency`),' ',b.`amount`,'/ ',b.`interval_count`,' ',b.`interval`,')') as plan, b.trial_period_days FROM "._DB_PREFIX_."stripepro_subscription a LEFT JOIN "._DB_PREFIX_."stripepro_plans b  ON a.stripe_plan_id = b.stripe_plan_id
        LEFT JOIN "._DB_PREFIX_."product_lang p ON a.id_product = p.id_product && p.id_shop = ".(int)$this->context->shop->id." && p.id_lang = ".(int)$this->context->cookie->id_lang." WHERE a.status!= 'canceled' && a.id_customer = ".(int)$this->context->cookie->id_customer);
        
        foreach($subscriptions as $key=>$sub){
          $product = new Product($sub['id_product']); 
          $link =  new Link();
          $image = Image::getCover($sub['id_product']);
          $subscriptions[$key]['cover'] = $image['id_image'];
          $subscriptions[$key]['p_link'] = $link->getProductLink($product);
        }
        
        $this->context->smarty->assign(array(
         'subscriptions' => $subscriptions,
         'allow_cancel' => Configuration::get('STRIPE_SUBS_CANCEL_OPTN')
         ));
		 
            
        $this->setTemplate('module:stripepro/views/templates/front/subscription.tpl');
    }

}
