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

include_once '../../config/config.inc.php';
include_once 'mpsellermembership.php';

$token = Tools::getValue('token');
$objMpMembership = new MpSellerMembership();
if ($token != $objMpMembership->secure_key) {
    die('some thing went wrong.');
}

class UpdateDatabase
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $obj_mp_seller_plan_details = new MarketplaceSellerplanDetail();
        $activeSellers = WkMpSeller::getAllSeller();
        if ($activeSellers) {
            $today = date('Y-m-d');
            $warn_day = date('Y-m-d', strtotime('+'.Configuration::get('MP_SELLER_MEMBERSHIP_MAIL_WARN_DAYS').' days'));
            foreach ($activeSellers as $seller) {
                $active_plan = $obj_mp_seller_plan_details->getCurrentActivePlanBySellerId($seller['id_seller']);
                if ($active_plan) {
                    if ($active_plan['expire_on'] < $today) {
                        $this->deactiveAllProductsOfSellerBySellerId($seller['id_seller']);
                        $this->sendDeactiveMail($seller['seller_firstname'], $seller['business_email']);
                        $obj_mp_seller_plan_details->updateCurrentPlanBySellerId($seller['id_seller'], 0);
                    } elseif ($active_plan['expire_on'] == $warn_day) {
                        $this->sendWarnMail($seller['seller_firstname'], $seller['business_email'], $warn_day);
                    }

                    $is_warn_time_left = MarketplaceOldSellerPlan::getInfoByIdSeller($seller['id_seller']);
                    if ($is_warn_time_left) {
                        $obj_old_seller_temp = new MarketplaceOldSellerPlan($is_warn_time_left['id']);
                        $obj_old_seller_temp->delete();
                    }
                } else {
                    $is_warn_time_left = MarketplaceOldSellerPlan::getInfoByIdSeller($seller['id_seller']);
                    if ($is_warn_time_left) {
                        if ($is_warn_time_left['expire_on'] < $today) {
                            $this->deactiveAllProductsOfSellerBySellerId($seller['id_seller']);
                            $this->sendDeactiveMail($seller['seller_firstname'], $seller['business_email']);
                            $obj_old_seller_temp = new MarketplaceOldSellerPlan($is_warn_time_left['id']);
                            $obj_old_seller_temp->delete();
                        } elseif ($is_warn_time_left['expire_on'] == $warn_day) {
                            $this->sendWarnMail($seller['seller_firstname'], $seller['business_email'], $warn_day);
                        }
                    }
                }
            }
        }

        die('ok'); //stop
    }

    public function deactiveAllProductsOfSellerBySellerId($mp_id_seller)
    {
        if ($mp_id_seller) {
            $sellers_all_active_products = MarketplaceSellerplanDetail::getSellerProduct($mp_id_seller, true);
            if ($sellers_all_active_products) {
                foreach ($sellers_all_active_products as $product) {
                    $obj_mp_product = new WkMpSellerProduct($product['id_mp_product']);
                    if ($obj_mp_product->active) {
                        $obj_mp_product->active = 0;
                        $obj_mp_product->save();

                        $product = new Product($product['id_ps_product']);
                        $product->active = 0;
                        $product->save();
                    }
                }
            }
        }
    }

    public function sendWarnMail($name, $email, $date)
    {
        if (Configuration::get('MP_SELLER_MEMBERSHIP_WARNING_MAIL')) {
            $temp_path = _PS_MODULE_DIR_.'mpsellermembership/mails/';
            $templateVars = array(
                '{seller_name}' => $name,
                '{all_plan_link}' => $this->context->link->getModuleLink('mpsellermembership', 'mpmembershipplans'),
                '{expire_date}' => $date,
            );
            $id_lang = $this->context->language->id;
            Mail::Send(
                $id_lang,
                'warning_mail',
                Mail::l('Seller Membership Warning', $id_lang),
                $templateVars,
                $email,
                $name,
                null,
                null,
                null,
                null,
                $temp_path,
                false,
                null,
                null
            );
        }
    }

    public function sendDeactiveMail($name, $email)
    {
        if (Configuration::get('MP_SELLER_MEMBERSHIP_EXPIRE_MAIL')) {
            $temp_path = _PS_MODULE_DIR_.'mpsellermembership/mails/';
            $templateVars = array(
                '{seller_name}' => $name,
                '{all_plan_link}' => $this->context->link->getModuleLink('mpsellermembership', 'mpmembershipplans'),
            );
            $id_lang = $this->context->language->id;
            Mail::Send(
                $id_lang,
                'seller_plan_expire',
                Mail::l('Seller Membership Expire', $id_lang),
                $templateVars,
                $email,
                $name,
                null,
                null,
                null,
                null,
                $temp_path,
                false,
                null,
                null
            );
        }
    }
}

new UpdateDatabase();
