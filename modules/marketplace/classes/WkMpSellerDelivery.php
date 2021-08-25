<?php
/**
* 2017-2018 PHPIST.
*
*  @author    Yassine Belkaid <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PHPIST
*  @license   MIT
*/

class WkMpSellerDelivery extends ObjectModel
{
    public $id;
    public $id_seller;
    public $delivery_method;
    public $delivery_delay;
    public $shipping_days;
    public $option_free_delivery;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_seller_delivery',
        'primary' => 'id_ps_wk_mp_seller_delivery',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT,'required' => true),
            'delivery_method' => array('type' => self::TYPE_STRING),
            'delivery_delay' => array('type' => self::TYPE_STRING),
            'shipping_days' => array('type' => self::TYPE_STRING),
            'option_free_delivery' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );

    /**
     * Get seller's shipping infos
     *
     * @param int $idSeller
     * @return array
     */
    public static function geDeliveryInfostBySellerId($idSeller)
    {
        return Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'wk_mp_seller_delivery` WHERE `id_seller` = '.(int) $idSeller);
    }
}
