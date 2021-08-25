<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkGdprCustomerRequests extends ObjectModel
{
    public $id_request;
    public $id_customer;
    public $request_type;
    public $request_reason;
    public $status;
    public $date_add;
    public $date_upd;

    const WK_CUSTOMER_REQUEST_TYPE_DELETE = 1;
    const WK_CUSTOMER_REQUEST_TYPE_UPDATE = 2;

    const WK_CUSTOMER_REQUEST_STATE_PENDING = 1;
    const WK_CUSTOMER_REQUEST_STATE_DONE = 2;

    public static $definition = array(
        'table' => 'wk_gdpr_customer_requests',
        'primary' => 'id_request',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'request_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'request_reason' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public function getGDPRCustomerRequests($idCustomer = 0, $type = 0, $status = 0)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_gdpr_customer_requests` WHERE 1';
        if ($idCustomer) {
            $sql .= ' AND `id_customer` = '.(int)$idCustomer;
        }
        if ($type) {
            $sql .= ' AND `request_type` = '.(int)$type;
        }
        if ($status) {
            $sql .= ' AND `status` = '.(int)$status;
        }
        if ($idCustomer && $type == self::WK_CUSTOMER_REQUEST_TYPE_DELETE) {
            return Db::getInstance()->getRow($sql);
        } else {
            return Db::getInstance()->executeS($sql);
        }
    }
}
