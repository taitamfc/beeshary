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

include dirname(__FILE__).'/../../../config/config.inc.php';
include dirname(__FILE__).'/../../../init.php';
include_once dirname(__FILE__).'/../../../classes/SpecificPrice.php';
include_once dirname(__FILE__).'/../../marketplace/classes/WkMpRequiredClasses.php';
include_once dirname(__FILE__).'/../classes/MpPriceSlots.php';

$mp_product_id = Tools::getValue('mp_product_id');
$keywords = Tools::getValue('keywords');
parse_str(Tools::getValue('dataval'), $data);
$cust_search = Tools::getValue('cust_search');
$obj_slotprice = new MpPriceSlots();
if ($cust_search && $keywords) {
    $obj_slotprice->searchCustomer($cust_search, $mp_product_id, $keywords);
} elseif (!$cust_search && $data) {
    $obj_slotprice->processPriceAddition($data);
    die;
} elseif (Tools::getValue('delete_slot')) {
    $obj_slotprice->deleteSlotPrice();
}
