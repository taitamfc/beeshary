<?php
/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpMessagingDetails extends ObjectModel
{
    /**
     * update seller number
     * @param type $mpIdSeller int
     * @param type $countryCode int
     * @param type $mobileNumber int
     * @return type boolean
     */
    public static function updateSellerMessagingNumber($mpIdSeller, $countryCode, $mobileNumber)
    {
        if ($mpIdSeller && $countryCode && $mobileNumber) {
            return Db::getInstance()->update(
                'marketplace_seller_info',
                [
                    'seller_messaging_country_code' => $countryCode,
                    'seller_messaging_mobile_number' => $mobileNumber,
                ],
                '`id` = '.(int) $mpIdSeller
            );
        }

        return false;
    }

    /**
     * get order ids by order reference number
     * @param type $reference string
     * @return type 2- dimensional array
     */
    public static function getOrderId($reference)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_order` FROM `'._DB_PREFIX_.'orders`
			WHERE `reference`= \''. pSQL($reference).'\''
        );
    }
}
