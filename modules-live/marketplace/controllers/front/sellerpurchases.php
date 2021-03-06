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
include_once _PS_MODULE_DIR_.'mpbooking/classes/WkMpBookingRequiredClasses.php';
use PrestaShop\PrestaShop\Adapter\Order\OrderPresenter;

class MarketplaceSellerPurchasesModuleFrontController extends ModuleFrontController
{
    public $order_presenter;
 
    public function initContent()
    {
        parent::initContent();

        if (!isset($this->context->customer->id) || !$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('marketplace', 'sellerpurchases')));
        }
		$action = Tools::getValue('action','');

        $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
        if (!$seller || !$seller['active']) {
			if( $action != 'display_activities' ){
				Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
			}
        }

		
		
        
        $this->order_presenter = new OrderPresenter();
        $purchases = $this->getTemplateVarPurchases($action);
		
		$logic = ( $action == 'display_activities' || $action == 'user_activities' ) ? 4 : 3;

        $this->context->smarty->assign(array(
            'purchases' => $purchases,
            'action' => $action,
            'logic' => $logic,
            'is_seller' => $seller['active'],
            'mp_seller_info' => $seller,
        ));
		

        $this->setTemplate('module:marketplace/views/templates/front/order/sellerpurchases.tpl');
    }

    public function getTemplateVarPurchases($action = null)
    {
        $orders = array();
		if ($action && $action == 'user_activities') {
            $customer_orders = $this->getMyActivitiesOrders($this->context->customer->id);
        }elseif($action && $action == 'display_activities') {
            $customer_orders = $this->getCustomerActivitiesOrders($this->context->customer->id);
        } else {
            $customer_orders = $this->getCustomerProductsOrders($this->context->customer->id);
        }
        foreach ($customer_orders as $customer_order) {
            $order = new Order((int) $customer_order['id_order']);
			if( $action != 'display_activities' && $action != 'user_activities' ){
				$orders[$customer_order['id_order']] = $this->order_presenter->present($order);
			}else{
				$customer 			= new Customer ($order->id_customer);
				$order->customer 	= $customer;
				$orders[$customer_order['id_order']]['order'] = $order;
				$products = $order->getProducts();
				foreach( $products as $key => $product ){
					$product = $this->_get_booking_data( $product );
					$products[$key] = $product;

				}
				
				$orders[$customer_order['id_order']]['products'] 	= $products;
			}

        }


        return $orders;
    }
	
	private function _get_booking_data( $product ){
		$bookingProductInfo = new WkMpBookingProductInformation();
		$wkBookingsOrders = new WkMpBookingOrder();
		
		if ($bookingProductInfo->getBookingProductInfo(
			0,
			$product['product_id']
		)) {
			if ($bookingProductOrderInfo = $wkBookingsOrders->getBookingProductOrderInfo(
				$product['product_id'],
				$product['id_order']
			)) {
				foreach ($bookingProductOrderInfo as $keyProduct => $cartBooking) {
					$bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_excl'] = (float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_incl']);
					$bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_excl'] = (float) $cartBooking['range_feature_price_tax_excl'];
					$bookingProductOrderInfo[$keyProduct]['duration'] = $this->_get_the_booking_duration($cartBooking);
					$bookingProductOrderInfo[$keyProduct]['the_time'] = $this->_get_the_booking_time($cartBooking);
					$bookingProductOrderInfo[$keyProduct]['time_status'] = $this->_get_the_booking_time_status($cartBooking);
					$bookingProductOrderInfo[$keyProduct]['date_from'] = date('d-m-Y' , strtotime( $cartBooking['date_from'] ) );
					$bookingProductOrderInfo[$keyProduct]['product_real_price_tax_excl'] = number_format( $bookingProductOrderInfo[$keyProduct]['product_real_price_tax_excl'] );
				}
				$product['isBookingProduct'] = 1;
				$product['booking_product_data'] = $bookingProductOrderInfo;
			}
		}
		
		$sellerProduct 		= WkMpSellerProduct::getSellerProductByPsIdProduct($product['product_id']);
		$store_locator 		= MarketplaceStoreLocator::getSellerStore($sellerProduct['id_seller']);
		$seller				= WkMpSeller::getSeller($sellerProduct['id_seller']);

		
		$city_name = '';
		if( $store_locator ){
			$city_name = implode(' ',[ $store_locator[0]['address1'],$store_locator[0]['zip_code'],$store_locator[0]['city_name']  ]);
		}
		$product['city_name'] 	= $city_name;
		$product['seller_name'] = $seller['shop_name_unique'];
		$product['business_email'] = $seller['business_email'];
		
		return $product;
	}
	
	private function _get_the_booking_duration($cartBooking){
		$duration = 0;
		
		$start 		= date('Y-m-d' , strtotime( $cartBooking['date_from'] ) ) .' '.$cartBooking['time_from'];
		$end 		= date('Y-m-d' , strtotime( $cartBooking['date_to'] ) ) .' '.$cartBooking['time_to'];
		
		$start 		= date('Y-m-d H:i', strtotime($start) );
		$end 		= date('Y-m-d H:i', strtotime($end) );
	
		$time1 		= new DateTime( $start );
		$time2 		= new DateTime( $end );
		$interval 	= $time1->diff($time2);
		
		$duration = gmdate('H:i', $interval);
		
		return $duration;
	}
	private function _get_the_booking_time($cartBooking){
		$start 		= $cartBooking['time_from'];
		$end 		= $cartBooking['time_to'];
		
		$start 		= date('H:i', strtotime($start) );
		$end 		= date('H:i', strtotime($end) );
		
		$the_time = $start .' - '.$end;
		return $the_time;
	}
	private function _get_the_booking_time_status($cartBooking){
		$date_from 		= $cartBooking['date_from'];
		$start 			= $cartBooking['time_from'];
		$end 			= $cartBooking['time_to'];
		
		$the_start 		= date('d-m-Y', strtotime($date_from) ) .' '.date('H:i', strtotime($start) );
		
		$is_pass = '?? venir';
		if (new DateTime() > new DateTime($the_start)) {
			$is_pass = 'termin??e';
		}
		return $is_pass;
	}
    
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'mporder'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Orders', 'mporder'),
            'url' => '',
        );

        return $breadcrumb;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('marketplace_account', 'modules/'.$this->module->name.'/views/css/marketplace_account.css');
        $this->registerStylesheet('marketplace_global', 'modules/'.$this->module->name.'/views/css/mp_global_style.css');

        //data table file included
        // $this->registerStylesheet('datatable_bootstrap', 'modules/'.$this->module->name.'/views/css/datatable_bootstrap.css');
        // $this->registerJavascript('mp-jquery-dataTables', 'modules/'.$this->module->name.'/views/js/jquery.dataTables.min.js');
        // $this->registerJavascript('mp-dataTables.bootstrap', 'modules/'.$this->module->name.'/views/js/dataTables.bootstrap.js');
        // $this->registerJavascript('mp-order', 'modules/'.$this->module->name.'/views/js/mporder.js');
    }

    /**
     * Get customer orders (Products only)
     *
     * @param int $id_customer Customer id
     * @param bool $show_hidden_status Display or not hidden order statuses
     * @return array Customer orders
     */
    public static function getCustomerProductsOrders($id_customer, $show_hidden_status = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT o.*, (SELECT SUM(od.`product_quantity`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products
        FROM `'._DB_PREFIX_.'orders` o
        WHERE o.`id_customer` = '.(int)$id_customer.
        Shop::addSqlRestriction(Shop::SHARE_ORDER).'
        GROUP BY o.`id_order`
        ORDER BY o.`date_add` DESC');
        if (!$res) {
            return array();
        }

        foreach ($res as $key => $val) {
            $orderDets = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT GROUP_CONCAT(`product_id`) FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` = \''.(int)$val['id_order'].'\'');

            if ($orderDets) {
                if (WkMpBookingProductInformation::isBookingProductStatic(explode(',', $orderDets))) {
                    unset($res[$key]);
                    continue;
                }
            }

            $res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`, os.`color` as order_state_color
                FROM `'._DB_PREFIX_.'order_history` oh
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
                INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$context->language->id.')
            WHERE oh.`id_order` = '.(int)$val['id_order'].(!$show_hidden_status ? ' AND os.`hidden` != 1' : '').'
                ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
            LIMIT 1');

            if ($res2) {
                $res[$key] = array_merge($res[$key], $res2[0]);
            }
        }
        return $res;
    }

    /**
     * Get customer orders (Activiies only)
     *
     * @param int $id_customer Customer id
     * @param bool $show_hidden_status Display or not hidden order statuses
     * @return array Customer orders
     */
	 
	/* display_activities - seller book someone */
    public static function getCustomerActivitiesOrders($id_customer, $show_hidden_status = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT o.*, (SELECT SUM(od.`product_quantity`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products
        FROM `'._DB_PREFIX_.'orders` o
        WHERE o.`id_customer` = '.(int)$id_customer.
        Shop::addSqlRestriction(Shop::SHARE_ORDER).'
        GROUP BY o.`id_order`
        ORDER BY o.`date_add` DESC');
		
		
		
        if (!$res) {
            return array();
        }

        foreach ($res as $key => $val) {
            $orderDets = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT GROUP_CONCAT(`product_id`) FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` = \''.(int)$val['id_order'].'\'');
            if ($orderDets) {
                if (!WkMpBookingProductInformation::isBookingProductStatic(explode(',', $orderDets))) {
                    unset($res[$key]);
                    continue;
                }
            }

            $res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`, os.`color` as order_state_color
                FROM `'._DB_PREFIX_.'order_history` oh
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
                INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$context->language->id.')
            WHERE oh.`id_order` = '.(int)$val['id_order'].(!$show_hidden_status ? ' AND os.`hidden` != 1' : '').'
                ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
            LIMIT 1');

            if ($res2) {
                $res[$key] = array_merge($res[$key], $res2[0]);
            }
        }

        return $res;
    }
	/* user_activities - someone book me */
	public static function getMyActivitiesOrders($id_customer, $show_hidden_status = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        // $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        // SELECT o.*, (SELECT SUM(od.`product_quantity`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products
        // FROM `'._DB_PREFIX_.'orders` o
		// LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product` wmsp ON (wmsp.`id_ps_product` = od.`product_id`)
        // WHERE wmsp.`id_seller` = '.(int)$id_customer.
        // Shop::addSqlRestriction(Shop::SHARE_ORDER).'
        // GROUP BY o.`id_order`
        // ORDER BY o.`date_add` DESC');
		
		$sql = '
        SELECT o.*, ( SELECT SUM(od.`product_quantity`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = o.`id_order` ) nb_products
        FROM `'._DB_PREFIX_.'orders` o
        WHERE o.`id_customer` = '.(int)$id_customer.
        Shop::addSqlRestriction(Shop::SHARE_ORDER).'
        GROUP BY o.`id_order`
        ORDER BY o.`date_add` DESC';
		
		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT email FROM `'._DB_PREFIX_.'customer` WHERE id_customer = '.$id_customer);
		$customerEmail = $res[0]['email'];
		
		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_seller FROM `'._DB_PREFIX_.'wk_mp_seller` WHERE seller_customer_id = "'.$id_customer.'"');
		$id_seller = $res[0]['id_seller'];
		
		/*
		$id_seller = WkMpSeller::getCustomerIdBySellerId($id_customer);
		*/


		$sql = 'SELECT od.* FROM `'._DB_PREFIX_.'order_detail` od 
		LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product` wmsp ON (wmsp.`id_ps_product` = od.`product_id`)
		WHERE wmsp.`id_seller` = '.(int)$id_seller.'
		GROUP BY od.`id_order`
		';
		
		
		
		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	
		
        if (!$res) {
            return array();
        }

        foreach ($res as $key => $val) {
            $orderDets = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT GROUP_CONCAT(`product_id`) FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` = \''.(int)$val['id_order'].'\'');

            if ($orderDets) {
                if (!WkMpBookingProductInformation::isBookingProductStatic(explode(',', $orderDets))) {
                    unset($res[$key]);
                    continue;
                }
            }

            $res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`, os.`color` as order_state_color
                FROM `'._DB_PREFIX_.'order_history` oh
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
                INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$context->language->id.')
            WHERE oh.`id_order` = '.(int)$val['id_order'].(!$show_hidden_status ? ' AND os.`hidden` != 1' : '').'
                ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
            LIMIT 1');

            if ($res2) {
                $res[$key] = array_merge($res[$key], $res2[0]);
            }
        }

        return $res;
    }
}
