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

class MpBookingBookingProductCartActionsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $result = array();
        $this->display_column_left = false;
        $this->display_column_right = false;
        $action = Tools::getValue('action');
        $result = array();
        if (isset($action) && $action == 'add_booking_product_to_cart') {
            $idProduct = Tools::getValue('id_product');
            if (!$idProduct) {
                $this->errors[] = $this->module->l('Product Id is missing.', 'bookingproductcartactions');
                $result['status'] = 'ko';
            } else {
                $objBookingProductInfo = new WkMpBookingProductInformation();
                if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                    $idbookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                    $booking_type = Tools::getValue('booking_type');
                    $quantity = Tools::getValue('quantity');
                    if ($booking_type == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                        $dateFrom = date('Y-m-d', strtotime(Tools::getValue('date_from')));
                        $dateTo = date('Y-m-d', strtotime(Tools::getValue('date_to')));
                        $currentDate = date('Y-m-d');

                        //validate values first
                        if ($dateFrom == '' || !Validate::isDate($dateFrom)) {
                            $this->errors[] = $this->module->l('Invalid Date From.', 'bookingproductcartactions');
                        } elseif ($dateTo == '' || !Validate::isDate($dateTo)) {
                            $this->errors[] = $this->module->l('Invalid Date To.', 'bookingproductcartactions');
                        } elseif ($dateFrom < $currentDate) {
                            $this->errors[] = $this->module->l(
                                'Date from should not be before current date.',
                                'bookingproductcartactions'
                            );
                        } elseif (!Validate::isUnsignedInt($quantity) || !$quantity) {
                            $this->errors[] = $this->module->l('Invalid quantity.', 'bookingproductcartactions');
                        }
                        if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                            if ($dateTo < $dateFrom) {
                                $this->errors[] = $this->module->l(
                                    'Date to should be a date after date from.',
                                    'bookingproductcartactions'
                                );
                            }
                        } else {
                            if ($dateTo <= $dateFrom) {
                                $this->errors[] = $this->module->l(
                                    'Date to should be a date after date from.',
                                    'bookingproductcartactions'
                                );
                            }
                        }
                        $objBookingsCart = new WkMpBookingCart();
                        $objBookingOrders = new WkMpBookingOrder();
                        $bookedQty = $objBookingOrders->getProductOrderedQuantityInDateRange(
                            $idProduct,
                            $dateFrom,
                            $dateTo,
                            1
                        );
                        $maxAvailableQuantity = $bookingProductInfo['quantity'] - $bookedQty;
                        $maxAvailableQuantity = $maxAvailableQuantity >= 0 ? $maxAvailableQuantity : 0;
                        if (!$maxAvailableQuantity) {
                            $this->errors[] = $this->module->l(
                                'Required quantity for this date range not available.',
                                'bookingproductcartactions'
                            );
                        }
                        $productQtyToCart = 0;
                        if (!count($this->errors)) {
                            if (!$this->context->cart->id) {
                                if (Context::getContext()->cookie->id_guest) {
                                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                                }
                                $this->context->cart->add();
                                if ($this->context->cart->id) {
                                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                                }
                            }
                            // Data to show Disables dates (Disable dates/slots tab)
                            $objBookingDisableDates = new WkMpBookingProductDisabledDates();
                            // get booking product disable dates
                            $bookingDisableDates = $objBookingDisableDates->getBookingProductDisableDatesInDateRange(
                                $idbookingProductInfo,
                                $dateFrom,
                                $dateTo
                            );
                            $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                            if ($bookingDisableDates && count($bookingDisableDates)) {
                                $tempDateFrom = $dateFrom;
                                $bookingDateRanges = array();
                                for ($date = strtotime($dateFrom); $date <= (strtotime($dateTo)); $date = ($date + (24 * 60 * 60))) {
                                    $currentDate = date('Y-m-d', $date);
                                    $prevdate = date('Y-m-d', strtotime($currentDate) - 86400);
                                    if (in_array($prevdate, $bookingDisableDates)) {
                                        $tempDateFrom = $currentDate;
                                    }
                                    if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                                        $lastDateCondition = strtotime($currentDate) == strtotime($dateTo) && !in_array($currentDate, $bookingDisableDates);
                                    } else {
                                        $lastDateCondition = strtotime($currentDate) == strtotime($dateTo) && !in_array($currentDate, $bookingDisableDates)  && !in_array($prevdate, $bookingDisableDates);
                                    }
                                    if ($lastDateCondition) {
                                        $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                            $idbookingProductInfo,
                                            $tempDateFrom,
                                            $dateTo,
                                            false,
                                            $this->context->currency->id
                                        );
                                        if ($totalPrice) {
                                            if (!$priceDisplay || $priceDisplay == 2) {
                                                $productPrice = $totalPrice['total_price_tax_incl'] * $quantity;
                                            } elseif ($priceDisplay == 1) {
                                                $productPrice = $totalPrice['total_price_tax_excl'] * $quantity;
                                            }
                                        }
                                        $bookingDateRanges[] = array(
                                            'date_from' => $tempDateFrom,
                                            'date_to' => $dateTo,
                                            'price' => Tools::displayPrice($productPrice)
                                        );
                                        $productQtyToCart += WkMpBookingHelper::getNumberOfDays(
                                            $tempDateFrom,
                                            $dateTo
                                        );
                                    } elseif (strtotime($currentDate) != strtotime($dateTo)
                                        && strtotime($currentDate) != strtotime($dateFrom)
                                        && !in_array($prevdate, $bookingDisableDates)
                                        && in_array($currentDate, $bookingDisableDates)
                                    ) {
                                        if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                                            $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                                $idbookingProductInfo,
                                                $tempDateFrom,
                                                $prevdate,
                                                false,
                                                $this->context->currency->id
                                            );
                                            if ($totalPrice) {
                                                if (!$priceDisplay || $priceDisplay == 2) {
                                                    $productPrice = $totalPrice['total_price_tax_incl'] * $quantity;
                                                } elseif ($priceDisplay == 1) {
                                                    $productPrice = $totalPrice['total_price_tax_excl'] * $quantity;
                                                }
                                            }
                                            $bookingDateRanges[] = array(
                                                'date_from' => $tempDateFrom,
                                                'date_to' => $prevdate,
                                                'price' => Tools::displayPrice($productPrice)
                                            );
                                            $productQtyToCart += WkMpBookingHelper::getNumberOfDays(
                                                $tempDateFrom,
                                                $prevdate
                                            );
                                        } else {
                                            $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                                $idbookingProductInfo,
                                                $tempDateFrom,
                                                $currentDate,
                                                false,
                                                $this->context->currency->id
                                            );
                                            if ($totalPrice) {
                                                if (!$priceDisplay || $priceDisplay == 2) {
                                                    $productPrice = $totalPrice['total_price_tax_incl'] * $quantity;
                                                } elseif ($priceDisplay == 1) {
                                                    $productPrice = $totalPrice['total_price_tax_excl'] * $quantity;
                                                }
                                            }
                                            $bookingDateRanges[] = array(
                                                'date_from' => $tempDateFrom,
                                                'date_to' => $currentDate,
                                                'price' => Tools::displayPrice($productPrice)
                                            );
                                            $productQtyToCart += WkMpBookingHelper::getNumberOfDays(
                                                $tempDateFrom,
                                                $currentDate
                                            );
                                        }
                                    }
                                }
                            } else {
                                $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $idbookingProductInfo,
                                    $dateFrom,
                                    $dateTo,
                                    false,
                                    $this->context->currency->id
                                );
                                if (!$priceDisplay || $priceDisplay == 2) {
                                    $productPrice = $totalPrice['total_price_tax_incl'] * $quantity;
                                } elseif ($priceDisplay == 1) {
                                    $productPrice = $totalPrice['total_price_tax_excl'] * $quantity;
                                }
                                $bookingDateRanges[] = array('date_from' => $dateFrom, 'date_to' => $dateTo, 'price' => Tools::displayPrice($productPrice));
                                $productQtyToCart += WkMpBookingHelper::getNumberOfDays($dateFrom, $dateTo);
                            }
                            if (isset($bookingDateRanges) && count($bookingDateRanges)) {
                                foreach ($bookingDateRanges as $dateRange) {
                                    $cartEntryExist = $objBookingsCart->cartProductEntryExistsForDateRange(
                                        $this->context->cart->id,
                                        $idProduct,
                                        $dateRange['date_from'],
                                        $dateRange['date_to']
                                    );
                                    if ($cartEntryExist) {
                                        $objBookingsCart = new WkMpBookingCart($cartEntryExist['id_booking_cart']);
                                        $objBookingsCart->quantity += $quantity;
                                    } else {
                                        $objBookingsCart = new WkMpBookingCart();
                                        $objBookingsCart->id_cart = $this->context->cart->id;
                                        $objBookingsCart->id_order = 0;
                                        $objBookingsCart->id_product = $idProduct;
                                        $objBookingsCart->booking_type = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE;
                                        $objBookingsCart->quantity = $quantity;
                                        $objBookingsCart->date_from = $dateRange['date_from'];
                                        $objBookingsCart->date_to = $dateRange['date_to'];
                                        $objBookingsCart->time_from = '';
                                        $objBookingsCart->time_to = '';
                                        $objBookingsCart->consider_last_date = Configuration::get('WK_MP_CONSIDER_DATE_TO');
                                    }
                                    $objBookingsCart->save();
                                }
                                $result['status'] = 'ok';
                            } else {
                                $result['status'] = 'ko';
                                $this->errors[] = $this->module->l(
                                    'This date range is not available for booking. Please select another.',
                                    'bookingproductcartactions'
                                );
                            }
                        } else {
                            $result['status'] = 'ko';
                        }
                        $bookedQty = $objBookingOrders->getProductOrderedQuantityInDateRange(
                            $idProduct,
                            $dateFrom,
                            $dateTo,
                            1
                        );
                        $maxAvailableQuantity = $bookingProductInfo['quantity'] - $bookedQty;
                        $maxAvailableQuantity = $maxAvailableQuantity >= 0 ? $maxAvailableQuantity : 0;

                        $result['product_qty_to_cart'] = $productQtyToCart * $quantity;
                        $result['available_qty'] = $maxAvailableQuantity;
                    } elseif ($booking_type == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                        $date = date('Y-m-d', strtotime(Tools::getValue('date')));
                        $selectedSlots = Tools::getValue('selected_slots');
                        $bookingTimeSlotPrice = array();
                        $bookingTimeSlotPrice['price_tax_excl'] = 0;

                        //validate values first
                        if ($selectedSlots) {
                            foreach ($selectedSlots as $slot) {
                                if (empty($slot['quantity']) || !$slot['quantity']) {
                                    $this->errors[] = $this->module->l('invalid quantity found.', 'bookingproductcartactions');
                                    break;
                                } elseif (!Validate::isInt($slot['quantity'])) {
                                    $this->errors[] = $this->module->l('invalid quantity found.', 'bookingproductcartactions');
                                    break;
                                }
                            }
                        }
                        $totalProductQty = 0;
                        if (!count($this->errors)) {
                            if (!$this->context->cart->id) {
                                if (Context::getContext()->cookie->id_guest) {
                                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                                }
                                $this->context->cart->add();
                                if ($this->context->cart->id) {
                                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                                }
                            }
                            if ($selectedSlots) {
                                $totalTimeSlotFeaturePrice = array();
                                $totalTimeSlotFeaturePrice['total_price_tax_incl'] = 0;
                                $totalTimeSlotFeaturePrice['total_price_tax_excl'] = 0;
                                $timeSlotsInfo = array();
                                $keySlot = 0;
                                $objBookingOrders = new WkMpBookingOrder();
                                foreach ($selectedSlots as $key => $slot) {
                                    $objBookingsCart = new WkMpBookingCart();
                                    $objBookingSlot = new WkMpBookingProductTimeSlotPrices($slot['id_slot']);
                                    $bookedSlotQuantity = $objBookingOrders->getProductTimeSlotOrderedQuantity(
                                        $idProduct,
                                        $date,
                                        $objBookingSlot->time_slot_from,
                                        $objBookingSlot->time_slot_to,
                                        1
                                    );
                                    $maxAvailableQuantity = $bookingProductInfo['quantity'] - $bookedSlotQuantity;
                                    if ($maxAvailableQuantity >= $slot['quantity']) {
                                        $bookingTimeSlotPrice['price_tax_excl'] = ($objBookingSlot->price);
                                        $taxRate = (float) WkMpBookingProductInformation::getAppliedProductTaxRate(
                                            $idProduct
                                        );
                                        $bookingTimeSlotPrice['price_tax_incl'] = $bookingTimeSlotPrice['price_tax_excl'] * ((100 + $taxRate)/100);
                                        $timeSlotFeaturePrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                            $idbookingProductInfo,
                                            $date,
                                            $date,
                                            $bookingTimeSlotPrice,
                                            $this->context->currency->id
                                        );
                                        $totalTimeSlotFeaturePrice['total_price_tax_incl'] += $timeSlotFeaturePrice['total_price_tax_incl'] * $slot['quantity'] ;
                                        $totalTimeSlotFeaturePrice['total_price_tax_excl'] += $timeSlotFeaturePrice['total_price_tax_excl'] * $slot['quantity'] ;

                                        $cartEntryExist = $objBookingsCart->cartProductEntryExistsForTimeSlot(
                                            $this->context->cart->id,
                                            $idProduct,
                                            $date,
                                            $objBookingSlot->time_slot_from,
                                            $objBookingSlot->time_slot_to
                                        );
                                        if ($cartEntryExist) {
                                            $objBookingsCart = new WkMpBookingCart($cartEntryExist['id_booking_cart']);
                                            $objBookingsCart->quantity += $slot['quantity'];
                                        } else {
                                            $objBookingsCart->id_cart = $this->context->cart->id;
                                            $objBookingsCart->id_product = $idProduct;
                                            $objBookingsCart->booking_type = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT;
                                            $objBookingsCart->quantity = $slot['quantity'];
                                            $objBookingsCart->date_from = $date;
                                            $objBookingsCart->date_to = '';
                                            $objBookingsCart->time_from = $objBookingSlot->time_slot_from;
                                            $objBookingsCart->time_to = $objBookingSlot->time_slot_to;
                                        }
                                        $totalProductQty += $slot['quantity'];
                                        if (!$objBookingsCart->save()) {
                                            $this->errors[] = $this->module->l(
                                                'Some error occurred while saving slot cart data.',
                                                'bookingproductcartactions'
                                            );
                                        }
                                        $timeSlotsInfo[$keySlot]['slot_id'] = $slot['id_slot'];
                                        $timeSlotsInfo[$keySlot]['slot_from'] = $objBookingSlot->time_slot_from;
                                        $timeSlotsInfo[$keySlot]['slot_to'] = $objBookingSlot->time_slot_to;
                                        $timeSlotsInfo[$keySlot]['quantity_avail'] = $maxAvailableQuantity-$slot['quantity'];
                                        $timeSlotsInfo[$keySlot]['quantity'] = $slot['quantity'];
                                        $keySlot++;
                                    } else {
                                        $this->errors[] = $this->module->l(
                                            'Required quantity not available for slot '.$objBookingSlot->time_slot_from.
                                            ' - '.$objBookingSlot->time_slot_to,
                                            'bookingproductcartactions'
                                        );
                                    }
                                }
                                $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                                if (!$priceDisplay || $priceDisplay == 2) {
                                    $productPrice = $totalTimeSlotFeaturePrice['total_price_tax_incl'];
                                } elseif ($priceDisplay == 1) {
                                    $productPrice = $totalTimeSlotFeaturePrice['total_price_tax_excl'];
                                }
                                $result['totalPrice'] = $productPrice;
                                $result['totalPriceFormatted'] = Tools::displayPrice($productPrice);
                                $result['timeSlotsInfo'] = $timeSlotsInfo;
                            } else {
                                $this->errors[] = $this->module->l(
                                    'No time slot is selected.',
                                    'bookingproductcartactions'
                                );
                            }
                            $result['status'] = 'ok';
                            $result['totalQty'] = (int) $totalProductQty;
                        } else {
                            $result['status'] = 'ko';
                        }
                    }
                } else {
                    $result['status'] = 'ko';
                    $this->errors[] = $this->module->l('Not a booking product.', 'bookingproductcartactions');
                }
            }
            $result['errors'] = $this->errors;
            die(json_encode($result));
        } elseif (isset($action) && $action == 'booking_product_price_calc') {
            // for price calculation while changing dates and slots
            $idProduct = Tools::getValue('id_product');
            if (!$idProduct) {
                $this->errors[] = $this->module->l('Product Id is missing.', 'bookingproductcartactions');
                $result['status'] = 'ko';
            } else {
                $objBookingProductInfo = new WkMpBookingProductInformation();
                if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                    $idbookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                    $dateFrom = date('Y-m-d', strtotime(Tools::getValue('date_from')));
                    $dateTo = date('Y-m-d', strtotime(Tools::getValue('date_to')));
                    $quantity = Tools::getValue('quantity');
                    $currentDate = date('Y-m-d');
                    //validate values first
                    if ($dateFrom == '' || !validate::isdate($dateFrom)) {
                        $this->errors[] = $this->module->l('invalid date from.', 'bookingproductcartactions');
                    } elseif ($dateTo == '' || !validate::isdate($dateTo)) {
                        $this->errors[] = $this->module->l('invalid date to.', 'bookingproductcartactions');
                    } elseif ($dateFrom < $currentDate) {
                        $this->errors[] = $this->module->l(
                            'date from should not be before current date.',
                            'bookingproductcartactions'
                        );
                    }
                    if (configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                        if ($dateTo < $dateFrom) {
                            $this->errors[] = $this->module->l(
                                'date to should be a date after date from.',
                                'bookingproductcartactions'
                            );
                        }
                    } else {
                        if ($dateTo <= $dateFrom) {
                            $this->errors[] = $this->module->l(
                                'date to should be a date after date from.',
                                'bookingproductcartactions'
                            );
                        }
                    }
                    if (!count($this->errors)) {
                        $objBookingOrders = new WkMpBookingOrder();
                        $bookedQty = $objBookingOrders->getProductOrderedQuantityInDateRange(
                            $idProduct,
                            $dateFrom,
                            $dateTo,
                            1
                        );
                        $maxAvailableQuantity = $bookingProductInfo['quantity'] - $bookedQty;
                        $result['max_avail_qty'] = $maxAvailableQuantity >= 0 ? $maxAvailableQuantity : 0;

                        // Data to show Disables dates (Disable dates/slots tab)
                        $objBookingDisableDates = new WkMpBookingProductDisabledDates();
                        // get booking product disable dates
                        $bookingDisableDates = $objBookingDisableDates->getBookingProductDisableDatesInDateRange(
                            $idbookingProductInfo,
                            $dateFrom,
                            $dateTo
                        );
                        $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                        $productPriceTotal = 0;
                        if ($bookingDisableDates && count($bookingDisableDates)) {
                            $tempDateFrom = $dateFrom;
                            $bookingDateRanges = array();
                            for ($date = strtotime($dateFrom); $date <= (strtotime($dateTo)); $date = ($date + (24 * 60 * 60))) {
                                $currentDate = date('Y-m-d', $date);
                                $prevdate = date('Y-m-d', strtotime($currentDate) - 86400);
                                if (in_array($prevdate, $bookingDisableDates)) {
                                    $tempDateFrom = $currentDate;
                                }
                                if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                                    $lastDateCondition = strtotime($currentDate) == strtotime($dateTo) && !in_array($currentDate, $bookingDisableDates);
                                } else {
                                    $lastDateCondition = strtotime($currentDate) == strtotime($dateTo) && !in_array($currentDate, $bookingDisableDates)  && !in_array($prevdate, $bookingDisableDates);
                                }
                                if ($lastDateCondition) {
                                    $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                        $idbookingProductInfo,
                                        $tempDateFrom,
                                        $dateTo,
                                        false,
                                        $this->context->currency->id
                                    );
                                    if ($totalPrice) {
                                        if (!$priceDisplay || $priceDisplay == 2) {
                                            $productPrice = $totalPrice['total_price_tax_incl'] * $quantity;
                                        } elseif ($priceDisplay == 1) {
                                            $productPrice = $totalPrice['total_price_tax_excl'] * $quantity;
                                        }
                                        $productPriceTotal += $productPrice;
                                    }
                                    $bookingDateRanges[] = array(
										'date_from' => date('d-m-Y',strtotime($tempDateFrom)),
										'date_to' 	=> date('d-m-Y',strtotime($dateTo)),
                                        'price' => Tools::displayPrice($productPrice)
                                    );
                                } elseif (strtotime($currentDate) != strtotime($dateTo)
                                    && strtotime($currentDate) != strtotime($dateFrom)
                                    && !in_array($prevdate, $bookingDisableDates)
                                    && in_array($currentDate, $bookingDisableDates)
                                ) {
                                    if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                                        $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                            $idbookingProductInfo,
                                            $tempDateFrom,
                                            $prevdate,
                                            false,
                                            $this->context->currency->id
                                        );
                                        if ($totalPrice) {
                                            if (!$priceDisplay || $priceDisplay == 2) {
                                                $productPrice = $totalPrice['total_price_tax_incl'] * $quantity;
                                            } elseif ($priceDisplay == 1) {
                                                $productPrice = $totalPrice['total_price_tax_excl'] * $quantity;
                                            }
                                            $productPriceTotal += $productPrice;
                                        }
                                        $bookingDateRanges[] = array(
											'date_from' => date('d-m-Y',strtotime($tempDateFrom)),
											'date_to' 	=> date('d-m-Y',strtotime($prevdate)),
                                            'price' => Tools::displayPrice($productPrice)
                                        );
                                    } else {
                                        $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                            $idbookingProductInfo,
                                            $tempDateFrom,
                                            $currentDate,
                                            false,
                                            $this->context->currency->id
                                        );
                                        if ($totalPrice) {
                                            if (!$priceDisplay || $priceDisplay == 2) {
                                                $productPrice = $totalPrice['total_price_tax_incl'] * $quantity;
                                            } elseif ($priceDisplay == 1) {
                                                $productPrice = $totalPrice['total_price_tax_excl'] * $quantity;
                                            }
                                            $productPriceTotal += $productPrice;
                                        }
                                        $bookingDateRanges[] = array(
											'date_from' => date('d-m-Y',strtotime($tempDateFrom)),
											'date_to' 	=> date('d-m-Y',strtotime($currentDate)),
                                            'price' => Tools::displayPrice($productPrice)
                                        );
                                    }
                                }
                            }
                            $result['showBookings'] = 1;
                        } else {
                            $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                $idbookingProductInfo,
                                $dateFrom,
                                $dateTo,
                                false,
                                $this->context->currency->id
                            );
                            if (!$priceDisplay || $priceDisplay == 2) {
                                $productPrice = $totalPrice['total_price_tax_incl'] * $quantity;
                            } elseif ($priceDisplay == 1) {
                                $productPrice = $totalPrice['total_price_tax_excl'] * $quantity;
                            }
                            $productPriceTotal += $productPrice;
                            $bookingDateRanges[] = array(
                                'date_from' => date('d-m-Y',strtotime($dateFrom)),
                                'date_to' 	=> date('d-m-Y',strtotime($dateTo)),
                                'price' 	=> Tools::displayPrice($productPrice)
                            );
                            $result['showBookings'] = 0;
                        }
                    }
                    if (!count($this->errors)) {
                        $result['status'] = 'ok';
                        $result['dateRangesBookingInfo'] = $bookingDateRanges;
                        $result['productPrice'] = Tools::displayPrice($productPriceTotal);
                    } else {
                        $result['status'] = 'ko';
                    }
                } else {
                    $result['status'] = 'ko';
                    $this->errors[] = $this->module->l('Not a booking product.', 'bookingproductcartactions');
                }
            }
            $result['errors'] = $this->errors;
            die(json_encode($result));
        } elseif (isset($action) && $action == 'booking_product_time_slots') {
            $idProduct = Tools::getValue('id_product');
            if (!$idProduct) {
                $this->errors[] = $this->module->l('Product Id is missing.', 'bookingproductcartactions');
                $result['status'] = 'ko';
            } else {
                $objBookingProductInfo = new WkMpBookingProductInformation();
                $objBookingOrders = new WkMpBookingOrder();
                if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                    $idbookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                    $date = date('Y-m-d', strtotime(Tools::getValue('date')));
                    $quantity = Tools::getValue('quantity');
                    $objTimeSlots = new WkMpBookingProductTimeSlotPrices();
                    $objBookingsCart = new WkMpBookingCart();
                    $bookingTimeSlots = $objTimeSlots->getBookingProductTimeSlotsOnDate(
                        $idbookingProductInfo,
                        $date,
                        true,
                        1
                    );
                    if ($bookingTimeSlots) {
                        $bookingTimeSlotPrice = false;
                        $bookingTimeSlotPriceToday = false;
                        $flag = 0;
                        $totalSlotsQty = 0;
                        foreach ($bookingTimeSlots as $key => $timeSlot) {
                            $bookedSlotQuantity = $objBookingOrders->getProductTimeSlotOrderedQuantity(
                                $idProduct,
                                $date,
                                $timeSlot['time_slot_from'],
                                $timeSlot['time_slot_to'],
                                1
                            );
                            $bookingTimeSlots[$key]['available_qty'] = $bookingProductInfo['quantity'] - $bookedSlotQuantity;
                            $bookingTimeSlots[$key]['price_tax_excl'] = $timeSlot['price'];

                            $totalSlotsQty += $bookingProductInfo['quantity'] - $bookedSlotQuantity;

                            $taxRate = (float) WkMpBookingProductInformation::getAppliedProductTaxRate($idProduct);
                            $bookingTimeSlots[$key]['price_tax_incl'] = $timeSlot['price'] * ((100 + $taxRate) / 100);
                            $bookingTimeSlotPrice['price_tax_excl'] = $bookingTimeSlots[$key]['price_tax_excl'];
                            $bookingTimeSlotPrice['price_tax_incl'] = $bookingTimeSlots[$key]['price_tax_incl'];
                            if ($flag == 0 && $bookingTimeSlots[$key]['available_qty']) {
                                $bookingTimeSlots[$key]['checked'] = 1;
                                $bookingTimeSlotPriceToday['price_tax_excl'] = $bookingTimeSlots[$key]['price_tax_excl'];
                                $bookingTimeSlotPriceToday['price_tax_incl'] = $bookingTimeSlots[$key]['price_tax_incl'];
                                $flag = 1;
                            } else {
                                $bookingTimeSlots[$key]['checked'] = 0;
                            }
                            $totalFeaturePrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                                $idbookingProductInfo,
                                $date,
                                $date,
                                $bookingTimeSlotPrice,
                                $this->context->currency->id
                            );
                            if ($totalFeaturePrice) {
                                $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                                if (!$priceDisplay || $priceDisplay == 2) {
                                    $bookingTimeSlots[$key]['formated_slot_price'] = Tools::displayPrice(
                                        $totalFeaturePrice['total_price_tax_incl']
                                    );
                                } elseif ($priceDisplay == 1) {
                                    $bookingTimeSlots[$key]['formated_slot_price'] = Tools::displayPrice(
                                        $totalFeaturePrice['total_price_tax_excl']
                                    );
                                }
                            }
                        }
                        if ($flag == 0 && !$bookingTimeSlotPriceToday) {
                            $bookingTimeSlotPriceToday['price_tax_excl'] = 0;
                            $bookingTimeSlotPriceToday['price_tax_incl'] = 0;
                        }
                        $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                            $idbookingProductInfo,
                            $date,
                            $date,
                            $bookingTimeSlotPriceToday,
                            $this->context->currency->id
                        );
                        if ($totalPrice) {
                            $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                            if (!$priceDisplay || $priceDisplay == 2) {
                                $productFeaturePrice = Tools::displayPrice($totalPrice['total_price_tax_incl']);
                            } elseif ($priceDisplay == 1) {
                                $productFeaturePrice = Tools::displayPrice($totalPrice['total_price_tax_excl']);
                            }
                        }
                        $result['totalSlotsQty'] = $totalSlotsQty;
                        $result['bookingTimeSlots'] = $bookingTimeSlots;
                        $result['productTotalFeaturePriceFormated'] = $productFeaturePrice;
                    }
                    if (!count($bookingTimeSlots)) {
                        $result['bookingTimeSlots'] = 'empty';
                        $result['productTotalFeaturePriceFormated'] = Tools::displayPrice(0);
                    }
                    $result['status'] = 'ok';
                } else {
                    $result['status'] = 'ko';
                    $this->errors[] = $this->module->l('Not a booking product.', 'bookingproductcartactions');
                }
            }
            $result['errors'] = $this->errors;
            die(json_encode($result));
        } elseif (isset($action) && $action == 'booking_product_time_slots_price_calc') {
            $idProduct = Tools::getValue('id_product');
            if (!$idProduct) {
                $this->errors[] = $this->module->l('Product Id is missing.', 'bookingproductcartactions');
                $result['status'] = 'ko';
            } else {
                $objBookingProductInfo = new WkMpBookingProductInformation();
                if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                    $date = date('Y-m-d', strtotime(Tools::getValue('date')));
                    $selectedSlots = Tools::getValue('selected_slots');
                    $bookingTimeSlotPrice = array();
                    $bookingTimeSlotPrice['price_tax_excl'] = 0;
                    $totalTimeSlotFeaturePrice['total_price_tax_incl'] = 0;
                    $totalTimeSlotFeaturePrice['total_price_tax_excl'] = 0;
                    foreach ($selectedSlots as $slot) {
                        $objBookingSlot = new WkMpBookingProductTimeSlotPrices($slot['id_slot']);
                        $bookingTimeSlotPrice['price_tax_excl'] = ($objBookingSlot->price);
                        $taxRate = (float) WkMpBookingProductInformation::getAppliedProductTaxRate($idProduct);
                        $bookingTimeSlotPrice['price_tax_incl'] = $bookingTimeSlotPrice['price_tax_excl'] * ((100 + $taxRate)/100);
                        $timeSlotFeaturePrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                            $bookingProductInfo['id_booking_product_info'],
                            $date,
                            $date,
                            $bookingTimeSlotPrice,
                            $this->context->currency->id
                        );
                        $totalTimeSlotFeaturePrice['total_price_tax_incl'] += $timeSlotFeaturePrice['total_price_tax_incl'] * $slot['quantity'] ;
                        $totalTimeSlotFeaturePrice['total_price_tax_excl'] += $timeSlotFeaturePrice['total_price_tax_excl'] * $slot['quantity'] ;
                    }
                    $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                    if (!$priceDisplay || $priceDisplay == 2) {
                        $productPrice = $totalTimeSlotFeaturePrice['total_price_tax_incl'];
                    } elseif ($priceDisplay == 1) {
                        $productPrice = $totalTimeSlotFeaturePrice['total_price_tax_excl'];
                    }
                    if (!count($this->errors)) {
                        $result['status'] = 'ok';
                        $result['productPrice'] = Tools::displayPrice($productPrice);
                    } else {
                        $result['status'] = 'ko';
                    }
                } else {
                    $result['status'] = 'ko';
                    $this->errors[] = $this->module->l('Not a booking product.', 'bookingproductcartactions');
                }
            }
            $result['errors'] = $this->errors;
            die(json_encode($result));
        } elseif (isset($action) && $action == 'remove_booking_product_from_cart') {
            $idProduct = Tools::getValue('id_product');
            if (!$idProduct) {
                $this->errors[] = $this->module->l('Product Id is missing.', 'bookingproductcartactions');
                $result['status'] = 'ko';
            } else {
                $objBookingProductInfo = new WkMpBookingProductInformation();
                if ($objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                    $objBookingsCart = new WkMpBookingCart(Tools::getValue('id_cart_booking'));
                    if ($objBookingsCart->booking_type == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                        $daysCount = (int)WkMpBookingHelper::getNumberOfDays(
                            $objBookingsCart->date_from,
                            $objBookingsCart->date_to
                        );
                    } else {
                        $daysCount = 1;
                    }
                    $quantityToReduce = $daysCount * (int)$objBookingsCart->quantity;
                    if ($this->context->cart->updateQty(
                        (int)$quantityToReduce,
                        (int)$idProduct,
                        null,
                        false,
                        'down',
                        0,
                        null,
                        true
                    )) {
                        if ($objBookingsCart->delete()) {
                            $result['status'] = 'ok';
                            $result['msg'] = $this->module->l(
                                'successfully cart product updated.',
                                'BookingProductCartActions'
                            );
                        } else {
                            $result['status'] = 'failed';
                            $result['msg'] = $this->module->l(
                                'error while deleting room from cart booking table.',
                                'BookingProductCartActions'
                            );
                        }
                    } else {
                        $result['status'] = 'failed';
                        $result['msg'] = $this->module->l(
                            'error while updating cart product.',
                            'BookingProductCartActions'
                        );
                    }
                } else {
                    $result['status'] = 'ko';
                    $this->errors[] = $this->module->l('Not a booking product.', 'bookingproductcartactions');
                }
            }
            $result['errors'] = $this->errors;
            die(json_encode($result));
        }
    }
}
