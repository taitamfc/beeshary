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

class StripeproWebhookModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->auth = false;
        parent::__construct();
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $stripe = new StripePro();
        if ($stripe->active)
        {
            if (Tools::getIsset('token') && Configuration::get('STRIPE_WEBHOOK_TOKEN') == Tools::getValue('token'))
            {
                include($this->module->getLocalPath().'lib/Stripe.php');
                \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODE') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));
                $event_json = Tools::jsonDecode(@Tools::file_get_contents('php://input'));
                if (isset($event_json->id))
                {
                    /* In case there is an issue with the event, Stripe throw an exception, just ignore it. */
                    try
                    {
                        /* To double-check and for more security, we retrieve the original event directly from Stripe */
                        $event = \Stripe\Event::retrieve($event_json->id);  
                        $data = $event->data->object;

                        /* We are handling chargebacks here */
                        if ($event->type == 'charge.dispute.created')
                        {
                            $id_order = (int)Db::getInstance()->getValue('SELECT `id_order` FROM `'._DB_PREFIX_.'stripepro_transaction` WHERE `id_transaction` = \''.pSQL($data->id).'\' AND `charge_back` = 0');
                            if ($id_order)
                            {
                                $order = new Order((int)$id_order);
                                if (Validate::isLoadedObject($order))
                                {
                                    if (Configuration::get('STRIPE_CHARGEBACKS_ORDER_STATUS') != -1)
                                        if ($order->getCurrentState() != Configuration::get('STRIPE_CHARGEBACKS_ORDER_STATUS'))
                                        {
                                            $order->setCurrentState((int)Configuration::get('STRIPE_CHARGEBACKS_ORDER_STATUS'));
                                            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripepro_transaction` SET `charge_back` = 1 WHERE `id_transaction` = \''.pSQL($data->id).'\' AND `charge_back` = 0');
                                        }

                                    $message = new Message();
                                    $message->message = $stripe->l('A chargeback occured on this order and was reported by Stripe on').' '.date('Y-m-d H:i:s');
                                    $message->id_order = (int)$order->id;
                                    $message->id_employee = 1;
                                    $message->private = 1;
                                    $message->date_add = date('Y-m-d H:i:s');
                                    $message->add();
                                }
                            }
                        }
                        
                        /* We are handling Subscription payment here */
                        if ($event->type == 'invoice.payment_succeeded')
                        {
                            $id_order = (int)Db::getInstance()->getValue('SELECT b.`id_order` FROM `'._DB_PREFIX_.'stripepro_subscription` a,`'._DB_PREFIX_.'stripepro_subs_order` b WHERE a.`stripe_subscription_id` = \''.pSQL($data->subscription).'\' AND a.`stripe_customer_id` = \''.pSQL($data->customer).'\' AND a.`id_stripe_subscription` = b.`id_stripe_subscription` order by b.id_order asc', false);
                            
                           // $time = ($event->created-$data->period_start)/3600;
                            if ($id_order != '')
                            {
                                $order = new Order((int)$id_order);
                                $date_add = date('y-m-d',strtotime($order->date_add));
                                $today = date('y-m-d');
                                if (Validate::isLoadedObject($order) && $date_add!=$today)
                                {
                                    if(Configuration::get('STRIPE_SUBS_PAYMENT_ORDER_NEW')) 
                                    {
                                       $sub_row = Db::getInstance()->getRow('SELECT b.id_product,b.id_stripe_subscription FROM `'._DB_PREFIX_.'stripepro_subs_order` a, `'._DB_PREFIX_.'stripepro_subscription` b WHERE b.`id_stripe_subscription`=a.`id_stripe_subscription` && b.`stripe_subscription_id` = \''.pSQL($data->subscription).'\' && a.`id_order` = '.(int)$id_order);
									  $id_product = $sub_row['id_product'];
									  $id_stripe_subscription = $sub_row['id_stripe_subscription'];
							  
									  $order = new Order((int)$id_order);
										  
									  $new_order_total = $data->total*0.01;
									  //$new_order_total_tax_excl = number_format((float)$new_order_total_tax_excl, 2, '.', '');                  
									  $order->total_discounts = 0;
									  $order->total_discounts_tax_incl = 0;
									  $order->total_discounts_tax_excl = 0;
									  $order->total_paid = $new_order_total;
									  $order->total_paid_tax_incl = $new_order_total;
									  $order->total_paid_tax_excl = $new_order_total;
									  $order->total_products = $new_order_total;
									  $order->total_products_wt = $new_order_total;
									  $order->add();
									  $orderlist = OrderDetail::getList($id_order);
									  foreach($orderlist as $od){
										  if($od['product_id']==$id_product){
											$oDetail = new OrderDetail($od['id_order_detail']);
											$oDetail->id_order = (int)$order->id;
											$oDetail->add();
										  }
									  }
									  $order->setCurrentState((int)Configuration::get('STRIPE_PAYMENT_ORDER_STATUS'));
									  $order->setInvoice();
									  $order->addOrderPayment($new_order_total);
									  Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'stripepro_subs_order` (`id_stripe_subscription`, `id_order`)
											  VALUES ('.$id_stripe_subscription.', '.(int)$order->id.')');
                                    } else {
                                    
                                      if (Configuration::get('STRIPE_SUBS_PAYMENT_ORDER_STATUS') != -1)
                                          if ($order->getCurrentState() != Configuration::get('STRIPE_SUBS_PAYMENT_ORDER_STATUS'))
                                              $order->setCurrentState((int)Configuration::get('STRIPE_SUBS_PAYMENT_ORDER_STATUS'));
                                          
                                      $message = new Message();
                                      $message->message = $stripe->l('A Subscription has been paid on this order and was reported by Stripe on').' '.date('Y-m-d H:i:s');
                                      $message->id_order = (int)$order->id;
                                      $message->id_employee = 1;
                                      $message->private = 1;
                                      $message->date_add = date('Y-m-d H:i:s');
                                      $message->add();
                                    }
                                }
                            }
                        }
                        
                        /* We are handling Customer subscription status update here */
                        if ($event->type == 'customer.subscription.updated' || $event->type == 'customer.subscription.deleted' || $event->type == 'customer.subscription.trial_will_end')
                        {
                          Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripepro_subscription` SET `stripe_plan_id`="'.$data->plan->id.'",`quantity` = "'.$data->quantity.'",`start`="'.$data->start.'", `current_period_start`='.$data->current_period_start.',`current_period_end`='.$data->current_period_end.',`canceled_at`="'.$data->canceled_at.'",`status`="'.$data->status.'" WHERE `stripe_subscription_id` = "'.$data->id.'" AND `stripe_customer_id` = "'.$data->customer.'"');

                        }
                    }
                    catch (Exception $e)
                    {
                        $this->set_http_response_code(200);
                        exit;
                    }
                    $this->set_http_response_code(200);
                    exit;
                }
            }
        }
        $this->set_http_response_code(200);
        exit;
    }
    
    private function set_http_response_code($code = NULL) {

        if (!function_exists('http_response_code')) {
            if ($code !== NULL) {
                $text = 'OK';
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1');
                header($protocol . ' ' . $code . ' ' . $text);
                $GLOBALS['http_response_code'] = $code;
            } else
                $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

            return $code;
        }else
        return http_response_code($code);
        
      }
    
}
