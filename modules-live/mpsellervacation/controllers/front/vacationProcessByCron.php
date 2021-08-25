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

class MpSellerVacationVacationProcessByCronModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $obj_seller_vacation_detail = new SellerVacationDetail();
        $all_sller_info = WkMpSeller::getAllSeller();
        if ($all_sller_info) {
            foreach ($all_sller_info as $seller_detail) {
                $flag = 1;
                $all_vacations = $obj_seller_vacation_detail->getValidVacationDetailsBySellerId((int)$seller_detail['id_seller']);
                $seller_product_detail = $obj_seller_vacation_detail->getMpSellerProductDetail((int)$seller_detail['id_seller']);
                if ($all_vacations) {
                    foreach ($all_vacations as $vacation_detail) {
                        if ($vacation_detail['active']) {
                            $first = strtotime($vacation_detail['from']);
                            $last = strtotime($vacation_detail['to']);
                            $date_range = SellerVacationDetail::getDateRange($first, $last);
                            $is_in_vacation = in_array(date('Y-m-d'), $date_range);
                            if ($is_in_vacation) {
                                if ($seller_product_detail) {
                                    if ($vacation_detail['addtocart']) {
                                        $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, 1);
                                    } else {
                                        $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, 0);
                                    }
                                }
                                $flag = 0;
                                break;
                            }
                        }
                    }
                }
                if ($flag) {
                    $expired_vacation_details = $obj_seller_vacation_detail->getAllExpiredVacationsBySellerId((int)$seller_detail['id_seller']);
                    if ($expired_vacation_details) {
                        if ($seller_product_detail) {
                            $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, 1);
                        }
                        foreach ($expired_vacation_details as $expired_vacation_info) {
                            $obj_seller_vacation_detail = new SellerVacationDetail($expired_vacation_info['id']);
                            if ($obj_seller_vacation_detail->active) {
                                $obj_seller_vacation_detail->addtocart = 1;
                                $obj_seller_vacation_detail->active = 0;
                                $obj_seller_vacation_detail->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
