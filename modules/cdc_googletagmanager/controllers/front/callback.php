<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2016 SAS Comptoir du Code
 * @license   Commercial license
 * @package   cdc_googletagmanager
 */

class cdc_googletagmanagerCallbackModuleFrontController extends ModuleFrontController
{
	private $updated = 0;

	public function __construct()
	{
		// if page is called in https, force ssl
		if (Tools::usingSecureMode()) {
            $this->ssl = true;
        }
        return parent::__construct();
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$p = Tools::getValue('p');
		if(!empty($p)) {
			$param_object = Tools::jsonDecode( base64_decode( urldecode($p) ) , true);

			if(isset($param_object['type'])) {
				switch ($param_object['type']) {
					case 'orderconfirmation':
						$this->orderconfirmation($param_object['params']);
						break;

					case 'orderresend':
						$this->orderresend($param_object['params']);
						break;

					case 'orderrefund':
						$this->orderrefund($param_object['params']);
						break;

					default:
						break;
				}
			}
		}
	}


	/**
	 * callback order confirmation
	 */
	private function orderconfirmation($params)
	{
		$orders = $params['orders'];
		$id_shop = $params['id_shop'];

		foreach ($orders as $order_id) {
			$gtmOrderLog = CdcGtmOrderLog::getByOrderId($order_id, $id_shop);
			if($gtmOrderLog->sent == 0) {
				$gtmOrderLog->sent = 1;
				$gtmOrderLog->save();
				$this->updated++;
			}
		}
	}


	/**
	 * callback order resend
	 */
	private function orderresend($params)
	{
		$orders = $params['orders'];
		$id_shop = $params['id_shop'];

		foreach ($orders as $order_id) {
			// update order log
			$gtmOrderLog = CdcGtmOrderLog::getByOrderId($order_id, $id_shop);
			$gtmOrderLog->resent = ((int) $gtmOrderLog->resent) + 1;
			$gtmOrderLog->save();

			// remove from resend queue
			$order = new Order($order_id);
			if(Validate::isLoadedObject($order)) {
				$id_cart = $order->id_cart;
				$orderResendQueue = Tools::jsonDecode(Configuration::get('CDC_GTM_ORDER_RESEND_Q', null, null, $id_shop), true);
				if(isset($orderResendQueue[$id_cart])) {
					unset($orderResendQueue[$id_cart]);
					Configuration::updateValue('CDC_GTM_ORDER_RESEND_Q', Tools::jsonEncode($orderResendQueue), false, null, $id_shop);
				}
			}

			$this->updated++;
		}
	}

	/**
	 * callback order refund
	 */
	private function orderrefund($params)
	{
		$order_id = $params['order'];
		$id_shop = $params['id_shop'];

		// update CDC_GTM_REFUNDS_Q
		$refundsQueue = Tools::jsonDecode(Configuration::get('CDC_GTM_REFUNDS_Q', null, null, $id_shop), true);
		if(isset($refundsQueue[$order_id])) {
			unset($refundsQueue[$order_id]);
			Configuration::updateValue('CDC_GTM_REFUNDS_Q', Tools::jsonEncode($refundsQueue), false, null, $id_shop);
			$this->updated++;
		}
	}

	public function display()
	{
		echo $this->updated;
		return '';
	}

}