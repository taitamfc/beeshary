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

class PaymentModule extends PaymentModuleCore
{
    protected function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }
        if (Module::isInstalled('mpbooking') && Module::isEnabled('mpbooking')) {
            include_once _PS_MODULE_DIR_.'mpbooking/classes/WkMpBookingRequiredClasses.php';
            $pathToFindEmail = _PS_MODULE_DIR_.'mpbooking/mails/'.$this->context->language->iso_code.
            DIRECTORY_SEPARATOR.$template_name;
            if (Tools::file_exists_cache($pathToFindEmail)) {
                $isExistBookingProduct = false;
                $bookingProductInfo = new WkMpBookingProductInformation();
                $wkBookingsCart = new WkMpBookingCart();
                if (!isset($this->context)) {
                    $this->context = Context::getContext();
                }
                foreach ($var as $key => &$product) {
                    $idProduct = $product['id_product'];
                    if ($productInfo = $bookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                        $isExistBookingProduct = true;
                        if ($bookingProductCartInfo = $wkBookingsCart->getBookingProductCartInfo(
                            $product['id_product'],
                            $this->context->cart->id
                        )) {
                            $idBookingProductInfo = $productInfo['id_booking_product_info'];
                            foreach ($bookingProductCartInfo as $keyProduct => $cartBooking) {
                                if ($cartBooking['booking_type'] == 1) {
                                    $numDays = WkMpBookingHelper::getNumberOfDays(
                                        $cartBooking['date_from'],
                                        $cartBooking['date_to']
                                    );
                                    $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'] * $numDays;
                                    $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                        $idBookingProductInfo,
                                        $cartBooking['date_from'],
                                        $cartBooking['date_to'],
                                        false,
                                        $this->context->currency->id
                                    );
                                    $bookingProductCartInfo[$keyProduct]['totalPriceTE'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_excl']));
                                    $bookingProductCartInfo[$keyProduct]['totalPriceTI'] = Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_incl']));
                                } elseif ($cartBooking['booking_type'] == 2) {
                                    $bookingTimeSlotPrice = false;
                                    $objTimeSlot = new WkMpBookingProductTimeSlotPrices();
                                    $slotDetails = $objTimeSlot->getProductTimeSlotDetails(
                                        $idBookingProductInfo,
                                        $cartBooking['date_from'],
                                        $cartBooking['time_from'],
                                        $cartBooking['time_to']
                                    );
                                    if ($slotDetails) {
                                        $bookingTimeSlotPrice['price_tax_excl'] = $slotDetails['price'];
                                        $taxRate = (float) WkMpBookingProductInformation::getAppliedProductTaxRate(
                                            $idProduct
                                        );
                                        $bookingTimeSlotPrice['price_tax_incl'] = $bookingTimeSlotPrice['price_tax_excl'] * ((100 + $taxRate) / 100);
                                        $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartBooking['quantity'];
                                        $totalPriceBookingProduct = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                            $idBookingProductInfo,
                                            $cartBooking['date_from'],
                                            $cartBooking['date_from'],
                                            $bookingTimeSlotPrice,
                                            $this->context->currency->id
                                        );
                                    }
                                }
                                $bookingProductCartInfo[$keyProduct]['unit_feature_price'] = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::displayPrice((float)$totalPriceBookingProduct['total_price_tax_incl']) : Tools::displayPrice((float)$totalPriceBookingProduct['total_price_tax_excl']);
                                $bookingProductCartInfo[$keyProduct]['total_range_feature_price_formated'] = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_incl'])) : Tools::displayPrice((float) ($cartBooking['quantity'] * $totalPriceBookingProduct['total_price_tax_excl']));
                            }
                            $var[$key]['isBookingProduct'] = 1;
                            $var[$key]['booking_product_data'] = $bookingProductCartInfo;
                        }
                    }
                }
                if ($isExistBookingProduct) {
                    $this->context->smarty->assign('list', $var);
                    return $this->context->smarty->fetch($pathToFindEmail);
                }
            }
        }
        return parent::getEmailTemplateContent($template_name, $mail_type, $var);
    }
}
