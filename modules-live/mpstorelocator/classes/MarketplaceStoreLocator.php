<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/
require_once(_PS_MODULE_DIR_.'mpextrafield/classes/MarketplaceExtrafieldValue.php');
class MarketplaceStoreLocator extends ObjectModel
{
    public $id_seller;
    public $name;
    public $active;
    public $address1;
    public $address2;
    public $map_address;
    public $map_address_text;
    public $country_id;
    public $state_id;
    public $city_name;
    public $latitude;
    public $longitude;
    public $zip_code;
    public $phone;
    public $email;
    public $fax;

    public $store_open_days;
    public $opening_time;
    public $closing_time;
    public $payment_option;
    public $pickup_start_time;
    public $pickup_end_time;
    public $store_pickup_available;

    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'marketplace_store_locator',
        'primary' => 'id',
        'fields' => array(
                'name' => array(
                    'type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128
                ),
                'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
                'address1' => array(
                    'type' => self::TYPE_STRING, 'validate' => 'isAddress', 'size' => 128, 'required' => true
                ),
                'address2' => array(
                    'type' => self::TYPE_STRING, 'validate' => 'isAddress', 'size' => 128
                ),
                'map_address' => array('type' => self::TYPE_HTML, 'size' => 256),
                'map_address_text' => array('type' => self::TYPE_STRING, 'size' => 256),
                'city_name' => array(
                    'type' => self::TYPE_STRING, 'validate' => 'isCityName', 'required' => true, 'size' => 64
                ),
                'country_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
                'state_id' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
                'latitude' => array('type' => self::TYPE_FLOAT, 'required' => true),
                'longitude' => array('type' => self::TYPE_FLOAT, 'required' => true),
                'zip_code' => array(
                    'type' => self::TYPE_STRING, 'validate' => 'isPostCode', 'size' => 12, 'required' => true
                ),
                // 'hours' => array('type' => self::TYPE_STRING, 'validate' => 'isSerializedArray', 'size' => 65000),
                'store_open_days' => array('type' => self::TYPE_STRING, 'size' => 65000),
                'opening_time' => array('type' => self::TYPE_STRING, 'size' => 65000),
                'closing_time' => array('type' => self::TYPE_STRING, 'size' => 65000),
                'pickup_start_time' => array('type' => self::TYPE_STRING, 'size' => 128),
                'pickup_end_time' => array('type' => self::TYPE_STRING, 'size' => 128),
                'payment_option' => array('type' => self::TYPE_STRING, 'size' => 65000),

                'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 16),
                'fax' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 16),
                'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
                'store_pickup_available' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
                'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
        ),
    );

    public function delete()
    {
        $image_path_logo = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$this->id.'.jpg';
        @unlink($image_path_logo);

        if (!$this->mpDeleteStoreProduct($this->id) || !parent::delete()) {
            return false;
        }

        return true;
    }

    public function mpDeleteStoreProduct($idStore)
    {
        $delete_store_products = Db::getInstance()->delete('marketplace_store_products', 'id_store = '.(int) $idStore);
        if (!$delete_store_products) {
            return false;
        }

        return true;
    }

    public static function getStoreById($id, $active = false)
    {
        $context = Context::getContext();
        $sql = 'SELECT msl.*, cl.`name` as `country_name`, s.`name` as `state_name`
            FROM `'._DB_PREFIX_.'marketplace_store_locator` msl
            LEFT JOIN `'._DB_PREFIX_.'country` c
            ON c.`id_country` = msl.`country_id`
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl 
            ON c.`id_country` = cl.`id_country`
            LEFT JOIN `'._DB_PREFIX_.'state` s
            ON msl.`state_id` = s.`id_state`
            WHERE `id` = '.(int) $id.
            ' AND id_lang ='.(int)$context->language->id;
        if ($active) {
            $sql .= ' AND msl.`active` = 1';
        }
        $stores =  Db::getInstance()->getRow($sql);
        
        if ($stores && !empty($stores)) {
            return $stores;
        }

        return false;
    }

    public static function getAllStore($active = true,$options = [])
    {
        $context = Context::getContext();
		$sql = 'SELECT msl.*, cl.`name` as `country_name`, s.`name` as `state_name`
            FROM `'._DB_PREFIX_.'marketplace_store_locator` msl
            LEFT JOIN `'._DB_PREFIX_.'country` c
            ON c.`id_country` = msl.`country_id`
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl 
            ON c.`id_country` = cl.`id_country`
            LEFT JOIN `'._DB_PREFIX_.'state` s
            ON msl.`state_id` = s.`id_state`
			JOIN `'._DB_PREFIX_.'wk_mp_seller` mps ON (mps.`id_seller` = msl.`id_seller`)
            WHERE msl.`active` = '.(int) $active.
            ' AND id_lang ='.(int)$context->language->id;
			
		
			
		$sql.= ' ORDER BY mps.shop_name_unique ASC ';
		
		if( isset( $options['pagi'] ) && $options['pagi'] == true ){
			
			$count_sql = 'SELECT COUNT(id) as total_records FROM `'._DB_PREFIX_.'marketplace_store_locator`';
			$total_records = Db::getInstance()->executeS($count_sql);
			$tot_records = $total_records['total_records'];
			
			
			$page 			= ( isset( $options['page'] ) ) ? $options['page'] : 1;
			$limit 			= $options['limit'];
			$total_page 	= ceil($tot_records / $limit); // calc pages
			$offset 		= ($limit * $page) - $limit;
			
			$sql .= ' LIMIT '.$limit.' OFFSET '.$offset;
		}
		
        $stores = Db::getInstance()->executeS($sql);
        
        if ($stores && !empty($stores)) {
            return $stores;
        }

        return false;
    }

    /**
     * [getSellerStore get store by id_seller].
     *
     * @param [type] $id_seller [mp seller id]
     * @param bool   $active    [true: if active, false or no need: if all]
     *
     * @return [array]
     */
    public static function getSellerStore($idSeller, $active = false)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'marketplace_store_locator` WHERE `id_seller` = '.(int) $idSeller;
        if ($active) {
            $sql .= ' AND `active` = 1';
        }
        $stores = Db::getInstance()->executeS($sql);
        if ($stores && !empty($stores)) {
            return $stores;
        }

        return false;
    }

    public function activeStoreLocator($status, $id)
    {
        $is_update = Db::getInstance()->update(
            'marketplace_store_locator',
            array('active' => (int) $status), 'id='.(int) $id
        );
        if (!$is_update) {
            return false;
        }

        return true;
    }

    public function getStoreLocatorStatus($id)
    {
        $current_status = Db::getInstance()->getValue(
            'SELECT `active`
            FROM '._DB_PREFIX_.'marketplace_store_locator WHERE `id`='.(int) $id
        );
        return $current_status;
    }

    /**
     * [getMoreStoreDetails get store details with state and country name].
     *
     * @param [array] $stores  [store array]
     * @param [int]   $id_lang [language id]
     *
     * @return [array/false]
     */
	public static function getSellerCustomFields($idSeller = false)
    {
		$mpSeller = [];
		
		$extra_field_value_obj = new MarketplaceExtrafieldValue();
		$extra_fields = $extra_field_value_obj->findExtrafieldValues($idSeller);
		if( count($extra_fields) ){
			foreach( $extra_fields as $extra_field ){
				switch ($extra_field['extrafield_id']) {
					case 1:
						$mpSeller['profession'] = $extra_field['field_value'];
						break;
					case 2:
						$mpSeller['quisuisje'] = $extra_field['field_value'];
						break;
					case 3:
						$mpSeller['mapassion'] = $extra_field['field_value'];
						break;
					case 4:
						$mpSeller['unproverbe'] = $extra_field['field_value'];
						break;
					case 5:
						$mpSeller['labels'] = $extra_field['field_value'];
						$mpSeller['badges'] = self::get_mp_seller_ids($idSeller);
						break;
					case 6:
						$mpSeller['siret'] = $extra_field['field_value'];
						break;
					case 8:
						$mpSeller['pp_theme'] = $extra_field['field_value'];
						break;
					
					default:
						# code...
						break;
				}
			}
		}
		
		return $mpSeller;
    }
	
	public static function get_mp_seller_ids($mp_seller_id)
    {
        $mp_seller_ids = [];
		
		$mp_seller_badges = Db::getInstance()->executeS('SELECT msb.*
			FROM `'._DB_PREFIX_.'mp_seller_badges` msb WHERE msb.mp_seller_id = '.(int) $mp_seller_id);
		
		if( $mp_seller_badges ){
			 foreach( $mp_seller_badges as $mp_seller_badge ){
				$mp_seller_ids[] = $mp_seller_badge['badge_id'];
			}
		}
       
        return $mp_seller_ids;
    } 
	 
    public static function getMoreStoreDetails($stores)
    {
        if (!is_array($stores)) {
            return false;
        }

        $objStoreLocator = new MpStoreLocator();
        $link = new Link();

        foreach ($stores as $key => $data) {
            $stores[$key]['opening_time'] = '';
            $stores[$key]['closing_time'] = '';

            $storeTiming = array();
            $storeOpenDays = json_decode($data['store_open_days']);
            $storeOpeningTime = json_decode($data['opening_time']);
            $storeClosingTime = json_decode($data['closing_time']);
			if( is_array($storeOpenDays) && count($storeOpenDays) > 0 ){
				foreach ($storeOpenDays as $dow => $hour) {
					if ($hour == 1) {
						@$storeTiming[$objStoreLocator->dayOfWeek[$dow]] = date("g:i a", strtotime($storeOpeningTime[$dow])) . ' - ' . date("g:i a", strtotime($storeClosingTime[$dow]));
					} else {
						@$storeTiming[$objStoreLocator->dayOfWeek[$dow]] = $objStoreLocator->l('Closed');
					}
				}
			}
            
            $stores[$key]['hours'] = $storeTiming;
            $stores[$key]['current_day'] = @$objStoreLocator->dayOfWeek[date('N')];
            if (@$storeOpenDays[date('N')]) {
                $stores[$key]['current_hours'] = date("g:i a", strtotime($storeOpeningTime[date('N')])) . ' - ' . date("g:i a", strtotime($storeClosingTime[date('N')]));
            } else {
                $stores[$key]['current_hours'] = $objStoreLocator->l('Closed');
            }
            $stores[$key]['storeLink'] = $link->getModuleLink(
                'mpstorelocator',
                'storedetails',
                array('id_store' => $data['id'])
            );

            $storeConfiguration = MpStoreConfiguration::getStoreConfiguration($data['id_seller']);
			$custom_fields = self::getSellerCustomFields($data['id_seller']);
			$stores[$key]['custom_fields'] = $custom_fields;
			
            if ($storeConfiguration) {
                $stores[$key]['enable_date'] = $storeConfiguration['enable_date'];
                $stores[$key]['enable_time'] = $storeConfiguration['enable_time'];

                $dirName = _PS_MODULE_DIR_.'mpstorelocator/views/img/mp_store_marker_icon/';
                $filePrefix = $data['id_seller'].'_'.$storeConfiguration['id_store_configuration'].'_';
                $fileName = glob($dirName."$filePrefix*.jpg");
    
                if ($fileName) {
                    $fileName = explode('/', $fileName[0]);
                    $fileName = $fileName[count($fileName) - 1];
                    $stores[$key]['markerIcon'] = _MODULE_DIR_.'mpstorelocator/views/img/mp_store_marker_icon/'.$fileName;
                }
            }
        }
        if (!is_array($stores)) {
            return false;
        }

        return $stores;
    }

    public static function getStoreByCity($location, $active = false)
    {
        $context = Context::getContext();
        $idLang = $context->language->id;
        $sql = 'SELECT msl.* , s.`name` as state_name, cl.`name` as country_name
                FROM `'._DB_PREFIX_.'marketplace_store_locator` msl
                LEFT JOIN '._DB_PREFIX_.'country_lang AS cl
                ON (cl.`id_country` = msl.`country_id` AND cl.`id_lang` = '.(int)$idLang.')
                LEFT JOIN '._DB_PREFIX_.'state AS s ON (s.`id_state` = msl.`state_id`)
                WHERE (msl.`city_name` LIKE "%'.$location.'%"
                OR msl.`address1` LIKE "%'.$location.'%"
                OR msl.`address2` LIKE "%'.$location.'%"
                OR msl.`map_address` LIKE "%'.$location.'%"
                OR cl.`name` LIKE "%'.$location.'%"
                OR s.`name`  LIKE "%'.$location.'%")';

        if ($active) {
            $sql .= ' AND msl.`active` = 1';
        }
        
        $stores = Db::getInstance()->executeS($sql);
        if ($stores && !empty($stores)) {
            return $stores;
        }

        return false;
    }

    public static function getActiveStoreByCityAndIdSeller($location, $idSeller)
    {
        $context = Context::getContext();
        $idLang = $context->language->id;
        $sql = 'SELECT msl.* , s.`name` as state_name, cl.`name` as country_name
                FROM `'._DB_PREFIX_.'marketplace_store_locator` msl
                LEFT JOIN '._DB_PREFIX_.'country_lang AS cl
                ON (cl.`id_country` = msl.`country_id` AND cl.`id_lang` = '.(int)$idLang.')
                LEFT JOIN '._DB_PREFIX_.'state AS s
                ON (s.`id_state` = msl.`state_id`)
                WHERE (msl.`city_name` LIKE "%'.$location.'%"
                OR msl.`address1` LIKE "%'.$location.'%"
                OR msl.`address2` LIKE "%'.$location.'%"
                OR msl.`map_address` LIKE "%'.$location.'%"
                OR cl.`name` LIKE "%'.$location.'%"
                OR s.`name`  LIKE "%'.$location.'%")
                AND msl.`active` = 1
                AND msl.`id_seller` = '.$idSeller;

        $stores = Db::getInstance()->executeS($sql);
        if ($stores && !empty($stores)) {
            return $stores;
        }
        return false;
    }

    public static function getStoreByCountry($idCountry)
    {
        $stores = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_store_locator`
            WHERE country_id = '.(int) $idCountry
        );
        if ($stores && !empty($stores)) {
            return $stores;
        }

        return false;
    }

    public static function getStoreByState($idState)
    {
        $stores = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_store_locator`
            WHERE state_id = '.(int) $idState
        );
        if ($stores && !empty($stores)) {
            return $stores;
        }

        return false;
    }

    public static function deleteStoreLocationByStoreId($idStore)
    {
        return Db::getInstance()->delete('marketplace_store_locator', 'id = '.(int) $idStore);
    }

    public function deactivateAllSellerStores($idSeller)
    {
        $is_update = Db::getInstance()->update('marketplace_store_locator', array('active' => 0), 'id_seller='.(int) $idSeller);
        if (!$is_update) {
            return false;
        }
        return true;
    }

    public static function getAllAvailableStore($active = true)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'marketplace_store_locator`
            WHERE `active` = '.(int)$active.
            ' AND `store_pickup_available` = 1';
        return Db::getInstance()->executeS($sql);
    }

    public static function getIdSellerByIdStore($idStore)
    {
        $sql = 'SELECT `id_seller` FROM `'._DB_PREFIX_.'marketplace_store_locator`'.
            ' WHERE `id` IN ('. pSQL(implode(',', $idStore)).')'.
            ' GROUP BY `id_seller`';

        return Db::getInstance()->getValue($sql);
    }
}
