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

class AdminSubscriptionsController extends AdminController
{
    public $module;
    public function __construct()
    {        
        $this->bootstrap = true;
        $this->table = 'stripepro_subscription';
        $this->className = 'Customer';
        $this->module = 'stripepro';
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->context = Context::getContext();
        $this->multishop_context_group = false;
        $id_lang = (int)$this->context->language->id;
        //$this->explicitSelect = true;
        
        parent::__construct();

        $this->_defaultOrderBy = 'id_stripe_subscription';
        $this->_defaultOrderWay = 'DESC';
        $this->allow_export = true;
                    
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = a.id_product && id_lang='.$id_lang.')
                        LEFT JOIN `'._DB_PREFIX_.'stripepro_plans` sp ON (sp.stripe_plan_id = a.stripe_plan_id)
                        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer = a.id_customer)
                        LEFT JOIN `'._DB_PREFIX_.'stripepro_subs_order` sso ON (sso.id_stripe_subscription = a.id_stripe_subscription)';

        $this->_select .= 'if(a.`start`!="",DATE_FORMAT(FROM_UNIXTIME(a.`start`), "%Y-%m-%d %H:%i:%s"),a.`date_add`) start,sp.interval,sp.interval_count,a.start no_renewals,if(a.`canceled_at` !="",DATE_FORMAT(FROM_UNIXTIME(a.`canceled_at`), "%Y-%m-%d %h:%i:%s"),"--") `canceled`,sso.id_order,c.*,pl.name product_name,c.id_customer as edit,sp.name as plan_name,if(sp.`trial_period_days`>0,CONCAT(sp.`trial_period_days`," days"),"--") trial_period_days, sp.`trial_period_days` as trial_days';

        $this->_use_found_rows = false;
        $this->fields_list = array
        (
            'id_stripe_subscription' => array(
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'remove_onclick' => true
            ),
            'id_order' => array(
                    'title' => $this->l('Order ID'),
                    'filter_key' => 'sso!id_order',
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'remove_onclick' => true
            ),
            'id_customer' => array(
                    'title' => $this->l('User ID'),
                    'callback' => 'printViewIcon',
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'filter_key' => 'a!id_customer',
                    'remove_onclick' => true
            ),
            'firstname' => array(
                    'title' => $this->l('Name'),
                    'filter_key' => 'c!firstname',
                    'remove_onclick' => true
            ),
            'lastname' => array(
                'title' => $this->l('Surname'),
                'filter_key' => 'c!lastname',
                'remove_onclick' => true
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'filter_key' => 'c!email',
                'remove_onclick' => true
            ),
            'product_name' => array(
                'title' => $this->l('Product'),
                'filter_key' => 'pl!name',
                'remove_onclick' => true
            ),
            'id_product' => array(
                'title' => $this->l('PID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'a!id_product',
                'remove_onclick' => true
            ),
            'plan_name' => array(
                'title' => $this->l('Plan'),
                'filter_key' => 'sp!name',
                'remove_onclick' => true
            ),
            'stripe_plan_id' => array(
                'title' => $this->l('Plan ID'),
                'filter_key' => 'a!stripe_plan_id',
                'remove_onclick' => true
            ),
            'trial_period_days' => array(
                'title' => $this->l('Trial'),
                'align' => 'text-right',
                'filter_key' => 'sp!trial_period_days',
                'remove_onclick' => true
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'class' => 'fixed-width-xs',
                'callback' => 'status_colors',
                'type' => 'select',
                'list' => array('trialing'=>'trialing', 'active'=>'active', 'past_due'=>'past_due', 'canceled'=>'canceled'),
                'filter_key' => 'a!status',
                'remove_onclick' => true
            ),
            'no_renewals' => array(
                'title' => $this->l('Renewals'),
                'callback' => 'count_renewals',
                'align' => 'center',
                'remove_onclick' => true
            ),
            'start' => array(
                'title' => $this->l('Started on'),
                'filter_key' => 'a!date_add',
                'align' => 'text-right',
                'type' => 'datetime',
                'remove_onclick' => true
            ),
            'canceled' => array(
                'title' => $this->l('Canceled at'),
                'filter_key' => 'a!canceled_at',
                'align' => 'text-right',
                'type' => 'datetime',
                'remove_onclick' => true
            ),
            /*'edit' => array(
                'title' => $this->l('--'),
                'callback' => 'printEditIcon',
                'align' => 'center',
                'filter' => false,
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true
            ),*/
        );
        
    }
    
    public function count_renewals($start, $tr)
    {
        if($tr['canceled_at']=='' && $tr['status']=='canceled')
        return 0;
        
        if($tr['canceled_at']!='')
        $now = $tr['canceled_at'];
        else
        $now = time();
        
        if($start=='')
        $start = $tr['date_add'];
        
        $days = ceil(abs($now - $start) / 86400);
        if($tr['interval']=='week')
        $sub_days = 7*$tr['interval_count'];
        elseif($tr['interval']=='month')
        $sub_days = 30*$tr['interval_count'];
        elseif($tr['interval']=='year')
        $sub_days = 365*$tr['interval_count'];
        else
        $sub_days = $tr['interval_count'];
        
        //return $days.'-'.$sub_days.'-'.$tr['interval_count'];
        if($tr['interval']=='day')
        $renewals = $days-$tr['trial_days']-$sub_days;
        else
        $renewals = floor(($days-$tr['trial_days'])/$sub_days);

      return (($renewals)>0?$renewals:0);                    
    }
    
    public function status_colors($status)
    {
        $color = ($status=='trialing'?'orange':($status=='active'?'#32CD32':($status=='past_due'?'red':'brown')));
      return '<span class="label" style="background:'.$color.';">'.$status.'</span>';                    
    }
    
    public function printViewIcon($id_customer,$tr)
    {
        
        $link = new Link();
        $link = $link->getAdminLink('AdminCustomers').'&amp;viewcustomer&amp;id_customer='.$tr['id_customer'];
        
        $html = '<span class="btn-group-action">
    <span class="btn-group">
        <a class="btn btn-default" href="'.$link.'" title="View this Customer">
            <i class="icon-search-plus"></i> &nbsp;'.$id_customer.'
        </a>
    </span>
</span>';
        return $html;

    }
    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
        
    }

}
