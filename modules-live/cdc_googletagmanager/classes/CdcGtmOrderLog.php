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

/**
 *
 * Object Model to log orders sent to GTM Ganalytics
 *
 */
class CdcGtmOrderLog extends ObjectModel
{

	/**
	 * Fields
	 */
	public $id_cdc_gtm_order_log;
	public $id_order;
	public $id_shop;
	public $refund;
	public $sent;
	public $resent;
	public $datalayer;
	public $date_add;
	public $date_upd;

	/**
	 * Definition
	 * @var unknown
	 */
	public static $definition = array (
			'table' => 'cdc_gtm_order_log',
			'primary' => 'id_cdc_gtm_order_log',
			'fields' => array (
					'id_order' => 	array('type' => self::TYPE_INT),
					'id_shop' => 	array('type' => self::TYPE_INT),
					'refund' => 	array('type' => self::TYPE_STRING),
					'sent' => 		array('type' => self::TYPE_BOOL),
					'resent' => 	array('type' => self::TYPE_INT),
					'datalayer' => 	array('type' => self::TYPE_STRING),
					'date_add' => 	array('type' => self::TYPE_DATE),
					'date_upd' => 	array('type' => self::TYPE_DATE)
			)
	);


	public static function createTable()
	{
		return Db::getInstance()->Execute("
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_.self::$definition['table']."` (
				`id_cdc_gtm_order_log` int(11) NOT NULL AUTO_INCREMENT,
				`id_order` int(11) NOT NULL,
				`id_shop` int(11) NOT NULL,
				`refund` varchar(128) DEFAULT NULL,
				`sent` tinyint(1) NOT NULL DEFAULT '0',
				`resent` tinyint(1) NOT NULL DEFAULT '0',
				`datalayer` TEXT NOT NULL,
				`date_add` datetime DEFAULT NULL,
				`date_upd` datetime DEFAULT NULL,
				PRIMARY KEY (`id_cdc_gtm_order_log`),
				KEY `id_order` (`id_order`)
			) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
	}

	public static function deleteTable()
	{
		return Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.self::$definition['table'].'`');
	}


	public static function getByOrderId($order_id, $shop_id = null)
	{
		$sql = "SELECT id_cdc_gtm_order_log from `"._DB_PREFIX_.self::$definition['table']."` WHERE id_order = '".(int)$order_id."'";

		if(!is_null($shop_id)) {
			$sql .=  " and id_shop = '".(int)$shop_id."'";
		}

		$id_cdc_gtm_order_log = (int) Db::getInstance()->getValue($sql);

		if($id_cdc_gtm_order_log) {
			return new CdcGtmOrderLog($id_cdc_gtm_order_log);
		} else {
			return null;
		}
	}

	public static function countByOrderId($order_id, $shop_id, $where = null)
	{
		$sql = "SELECT count(*) as nb from `"._DB_PREFIX_.self::$definition['table']."` WHERE id_order = '".(int)$order_id."' and id_shop = '".(int)$shop_id."'";
		if($where !== null && is_array($where) && count($where)) {
			foreach ($where as $key => $value) {
				$sql .= " AND `".$key."` = '".pSQL($value)."'";
			}
		}
		$result = Db::getInstance()->getRow($sql);
		$count = 0;
		if(isset($result['nb'])) {
			$count = (int) $result['nb'];
		}
		return $count;
	}


	/**
	 * Return if an order / product can be refunded
	 */
	public static function isRefundable($order_id, $shop_id, $id_product_with_attribute = null)
	{
		$gtmOrderLog = self::getByOrderId($order_id, $shop_id);

		// no gtm log associated to the order
		if(!$gtmOrderLog) {
			//throw new CdcGtmOrderLogException('CdcGtmOrderLog for order '.$order_id.' not found');
			return false;
		}

		if(!$gtmOrderLog->sent) {
			//throw new CdcGtmOrderLogException('CdcGtmOrderLog for order '.$order_id.' not sent to GTM');
			return false;
		}

		// refund not set, we can refund everything
		if(!$gtmOrderLog->refund) {
			return true;
		}

		// order already refunded
		if($gtmOrderLog->refund == "all") {
			return false;
		}

		// list of products refunded
		$products = explode(',', $gtmOrderLog->refund);
		if(in_array($id_product_with_attribute, $products)) {
			// product already refunded
			return false;
		} else {
			// product not refunded
			return true;
		}
	}

	/**
	 * Return all GtmOrderLog not sent:
	 * - sent = 0 AND resent = 0
	 * if $days in specified, search $days in the past
	 * if $days is not specified, search all lines 
	 */
	public static function getNotSent($shop_id = null, $days = null, $limit = null) {
		$sql = "
			SELECT id_cdc_gtm_order_log, id_order, id_shop from `"._DB_PREFIX_.self::$definition['table']."` 
			WHERE sent = 0 and resent = 0";

		if(!is_null($shop_id)) {
			$sql .=  " and id_shop = '".(int)$shop_id."'";
		}

		if(!is_null($days)) {
			$date = date("Y-m-d", strtotime("- ".(int) $days ." days"));
			$sql .= " AND date_add >= '".$date."'";
		}

        if(!is_null($limit)) {
            $sql .= " LIMIT ".(int) $limit;
        }

		return DB::getInstance()->executeS($sql);
	}

	/**
	 * Return true if the order has been sent
	 */
	public static function isSent($order_id, $shop_id) {
		$sql = "
			SELECT count(*) as nb from `"._DB_PREFIX_.self::$definition['table']."` 
			WHERE id_order = '".(int)$order_id."' AND (sent = 1 OR resent = 1) and id_shop = '".(int)$shop_id."'";

		$result = Db::getInstance()->getRow($sql);
		$count = 0;
		if(isset($result['nb'])) {
			$count = (int) $result['nb'];
		}
		return ($count > 0);
	}

	/**
	 * Delete sync log older than n days
	 */
	public static function purge($days_before_purge = 365, $where = null)
	{
		$days_before_purge = (int) $days_before_purge;
		if($days_before_purge < 0) $days_before_purge = 1;

		$sql = "DELETE FROM `"._DB_PREFIX_.self::$definition['table']."` WHERE datediff(now(), `date_add`) > ".$days_before_purge;

		if($where !== null && is_array($where) && count($where)) {
			foreach ($where as $key => $value) {
				$sql .= " AND `".$key."` = '".pSQL($value)."'";
			}
		}

		return Db::getInstance()->execute($sql);
	}

}


class CdcGtmOrderLogException extends Exception
{ }
