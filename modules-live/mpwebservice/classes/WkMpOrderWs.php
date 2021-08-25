<?php
/**
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpOrderWs extends WkMpWebservice
{
    /**
     * Get Seller Order List
     * Provide id_order to get order details
     * @api /sellerorder
     *
     *
     * @todo get order and then associated order_details like ps order api for particular id_order
     * @return json
     */
    public function sellerOrder()
    {
        $idOrder = Tools::getValue('id_order'); //prestashop id_order
        if ($idOrder) {
            $objMpOrderDetail = new WkMpSellerOrderDetail();
            $mpOrderDetails = $objMpOrderDetail->getSellerProductFromOrder($idOrder, $this->context->customer->id);
            if ($mpOrderDetails && is_array($mpOrderDetails)) {
                foreach ($mpOrderDetails as &$orderDetails) {
                    $orderDetails['booking'] = $this->getBookingProductInfo($orderDetails);
                }
                $this->output['success'] = true;
                $this->output['order'] = $mpOrderDetails;
                return $this->output;
            } else {
                $this->output['success'] = false;
                $this->output['message'] = 'Invalid order id';
                return $this->output;
            }
        } else {
            $objMpOrder = new WkMpSellerOrder();
            $mporders = $objMpOrder->getSellerOrders($this->context->language->id, $this->context->customer->id);
            if ($mporders && is_array($mporders)) {
                $objMpOrderDetail = new WkMpSellerOrderDetail();
                foreach ($mporders as &$mporder) {
                    $mpOrderDetails = $objMpOrderDetail->getSellerProductFromOrder($mporder['id_order'], $this->context->customer->id);
                    $bookingData = array();
                    foreach ($mpOrderDetails as $k => &$orderDetails) {
            // dump($orderDetails);die;

                        $bookingData[] = $this->getBookingProductInfo($orderDetails);
                    }
                    $mporder['booking'] = $bookingData;
                }
                $this->output['success'] = true;
                $this->output['orderList'] = $mporders;
                return $this->output;
            } else {
                $this->output['success'] = false;
                $this->output['message'] = 'No order available.';
                return $this->output;
            }
        }

        return $this->output;
    }

    public function getBookingProductInfo($orderDetails)
    {
        //$bookingInfo = new WkMpBookingProductInformation();
        $bookingOrders = new WkMpBookingOrder();

        //$bookingProductInfo = $bookingInfo->getBookingProductInfo(0, $orderDetails['product_id']);
        $bookingProductOrderInfo = $bookingOrders->getBookingProductOrderInfo($orderDetails['product_id'], $orderDetails['id_order']);

        if ($bookingProductOrderInfo) {
            return $bookingProductOrderInfo;
        } else {
            return array();
        }
    }

    /**
     * Create Cart for booking product
     * input json
     * {'id_product','id_currency','booking_type','quantity','date_from','date_to','selected_slots','date'}
     * @api /createbookingcart
     *
     * @return json
     */
    public function createBookingCart($fields)
    {
        unset($this->output['message']);
        $idProduct = $fields['id_product'];
        if (!$idProduct) {
            $this->output['success'] = false;
            $this->output['msg'] = 'Product Id is missing.';
            return $this->output;
        } else {
            // $objMpProduct = new WkMpSellerProduct($idProduct);
            // if (!Validate::isLoadedObject($objMpProduct)) {
            //     $this->output['success'] = false;
            //     $this->output['message'] = 'Invalid product';
            //     return false;
            // } else {
            //     $idProduct = $objMpProduct->id_ps_product;
            // }
            // --- create cart code
            $idCustomer = (int)$fields['id_customer'];
            $context = Context::getContext();
//            $idCart = Cart::lastNoneOrderedCart($idCustomer);
            $idCart = false;
            if (!$idCart) {
                $customer = new Customer($idCustomer);
                $context->customer = $customer;
                $context->cart->id_customer = $idCustomer;
                if ($customer->secure_key < 0) {
                    $customer->secure_key = md5(uniqid(mt_rand(0, mt_getrandmax()), true));
                }
                $context->cart->secure_key = $customer->secure_key;
                $context->cart->id_currency = $fields['id_currency'];
                $context->cart->add();
                $context->cart->updateQty(
                    $fields['quantity'],
                    $fields['id_product'],
                    null,
                    null,
                    'up',
                    0,
                    new Shop($context->cart->id_shop)
                );
            } else {
                $context->cart = new Cart($idCart);
            }

            // --- create cart code end
            $objBookingProductInfo = new WkMpBookingProductInformation();
            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
                $idbookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                $booking_type = $fields['booking_type'];
                $quantity = $fields['quantity'];
                if ($booking_type == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                    $dateFrom = date('Y-m-d', strtotime($fields['date_from']));
                    $dateTo = date('Y-m-d', strtotime($fields['date_to']));
                    $currentDate = date('Y-m-d');

                    //validate values first
                    if ($dateFrom == '' || !Validate::isDate($dateFrom)) {
                        $this->output['success'] = false;
                        $this->output['msg'] = 'Invalid Date From.';
                        return $this->output;
                    } elseif ($dateTo == '' || !Validate::isDate($dateTo)) {
                        $this->output['success'] = false;
                        $this->output['msg'] = 'Invalid Date To.';
                        return $this->output;
                    } elseif ($dateFrom < $currentDate) {
                        $this->output['success'] = false;
                        $this->output['msg'] = 'Date from should not be before current date.';
                        return $this->output;
                    } elseif (!Validate::isUnsignedInt($quantity) || !$quantity) {
                        $this->output['success'] = false;
                        $this->output['msg'] = 'Invalid quantity.';
                        return $this->output;
                    }
                    if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                        if ($dateTo < $dateFrom) {
                            $this->output['success'] = false;
                            $this->output['msg'] = 'Date to should be a date after date from.';
                            return $this->output;
                        }
                    } else {
                        if ($dateTo <= $dateFrom) {
                            $this->output['success'] = false;
                            $this->output['msg'] = 'Date to should be a date after date from.';
                            return $this->output;
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
                        $this->output['success'] = false;
                        $this->output['msg'] = 'Required quantity for this date range not available.';
                        return $this->output;
                    }
                    $productQtyToCart = 0;
                    $errors = array();
                    if (!count($errors)) {
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
                            $this->output['success'] = true;
                            $this->output['msg'] = 'saved with success';
                            $this->output['id_cart'] = (int) $context->cart->id;
                            return $this->output;
                        } else {
                            $this->output['success'] = false;
                            $this->output['msg'] = 'This date range is not available for booking. Please select another.';
                            return $this->output;
                        }
                    } else {
                        $this->output['success'] = false;
                        $this->output['msg'] = 'something went wrong';
                        return $this->output;
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
                    $date = date('Y-m-d', strtotime($fields['on_date']));
                    $postSlots = $fields['slots'];
                    $selectedSlots = array();
                    if (!empty($postSlots)) {
                        $objTimeSlot = new WkMpBookingProductTimeSlotPrices();
                        foreach ($postSlots as $key => $postSlot) {
                            $slotDetails = $objTimeSlot->getProductTimeSlotDetails($idbookingProductInfo, $date, $postSlot['time_from'], $postSlot['time_to']);
                            if (!empty($slotDetails)) {
                                $selectedSlots[$key]['id_slot'] = $slotDetails['id_time_slots_price'];
                                $selectedSlots[$key]['quantity'] = $postSlot['quantity'];
                            } else {
                                $this->output['success'] = false;
                                $this->output['msg'] = 'Not found time slots';
                                return $this->output;
                            }
                        }
                    } else {
                        $this->output['success'] = false;
                        $this->output['msg'] = 'At least one slot must be selected for booking.';
                        return $this->output;
                    }
                    $bookingTimeSlotPrice = array();
                    $bookingTimeSlotPrice['price_tax_excl'] = 0;
                    //validate values first
                    if ($selectedSlots) {
                        foreach ($selectedSlots as $slot) {
                            if (empty($slot['quantity']) || !$slot['quantity']) {
                                $this->output['success'] = false;
                                $this->output['msg'] = 'invalid quantity found.';
                                return $this->output;
                            } elseif (!Validate::isInt($slot['quantity'])) {
                                $this->output['success'] = false;
                                $this->output['msg'] = 'invalid quantity found.';
                                return $this->output;
                            }
                        }
                    }
                    $totalProductQty = 0;
                    $errors = array();
                    if (!count($errors)) {
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
                                        $this->output['success'] = false;
                                        $this->output['msg'] = 'Some error occurred while saving slot cart data.';
                                        return $this->output;
                                    }
                                    $timeSlotsInfo[$keySlot]['slot_id'] = $slot['id_slot'];
                                    $timeSlotsInfo[$keySlot]['slot_from'] = $objBookingSlot->time_slot_from;
                                    $timeSlotsInfo[$keySlot]['slot_to'] = $objBookingSlot->time_slot_to;
                                    $timeSlotsInfo[$keySlot]['quantity_avail'] = $maxAvailableQuantity-$slot['quantity'];
                                    $timeSlotsInfo[$keySlot]['quantity'] = $slot['quantity'];
                                    $keySlot++;
                                } else {
                                    $this->output['success'] = false;
                                    $this->output['msg'] = 'Required quantity not available for slot '.$objBookingSlot->time_slot_from.' - '.$objBookingSlot->time_slot_to;
                                    return $this->output;
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
                            $this->output['success'] = false;
                            $this->output['msg'] = 'No time slot is selected.';
                            return $this->output;
                        }
                        $this->output['success'] = true;
                        $this->output['msg'] = 'success';
                        $this->output['id_cart'] = (int) $context->cart->id;
                        $this->output['totalQty'] = (int) $totalProductQty;
                        return $this->output;
                    } else {
                        $this->output['success'] = false;
                        $this->output['msg'] = 'error';
                        return $this->output;
                    }
                }
            } else {
                $this->output['success'] = false;
                $this->output['msg'] = 'Not a booking product.';
                return $this->output;
            }
        }
        // $result['errors'] = $this->errors;
        // die(json_encode($result));
    }

    private function getBookingOrderInfoByCustomerId($id_customer, $id_Lang)
    {
        $sql = 'SELECT bo.*, ol.`product_name`, ol.`id_order_detail`, o.`reference`, o.`payment`, o.`date_add`, ords.`name` AS order_status, extras.*
                FROM `'._DB_PREFIX_.'wk_mp_booking_order` AS bo
                INNER JOIN `'._DB_PREFIX_.'orders` AS o ON
                (bo.`id_order` = o.`id_order`)
                INNER JOIN `'._DB_PREFIX_.'order_detail` AS ol ON
                (bo.`id_order` = ol.`id_order` AND bo.`id_product` = ol.`product_id`)
                INNER JOIN `'._DB_PREFIX_.'order_state_lang` ords ON (o.`current_state` = ords.`id_order_state`)
                LEFT JOIN (
                    select spi.seller_product_id, spl.link_rewrite, spi.id_ps_image, s.shop_name_unique, s.seller_firstname, s.seller_lastname, s.city as seller_city, CONCAT(\'/\', spi.id_ps_image, \'/\', spl.link_rewrite, \'.jpg\') as image, CONCAT(\'/modules/marketplace/views/img/seller_img/\', s.profile_image) as seller_image
                        from `'._DB_PREFIX_.'wk_mp_seller_product_image` spi
                         left join `'._DB_PREFIX_.'wk_mp_seller_product` sp on spi.seller_product_id = sp.id_mp_product
                         left join `'._DB_PREFIX_.'wk_mp_seller_product_lang` spl on spl.id_mp_product = sp.id_mp_product
                            left join `'._DB_PREFIX_.'wk_mp_seller` s on s.id_seller = sp.id_seller
                    ) as extras on extras.seller_product_id=bo.id_mp_product
                WHERE ords.`id_lang` = '.(int) $id_Lang.' AND o.`id_customer` = '.(int)$id_customer . '  group by bo.id_booking_order';
        $orders = Db::getInstance()->executeS($sql);
        return $orders;
    }

    public function getCustomerBooking($idCustomer)
    {
        $cusOrders = $this->getBookingOrderInfoByCustomerId($idCustomer, $this->context->language->id);
        if ($cusOrders) {
            $this->output['success'] = true;
            $this->output['msg'] = 'success';
            $this->output['booking_order'] = $cusOrders;
            return $this->output;
        } else {
            $this->output['success'] = false;
            $this->output['msg'] = 'Not a booking order.';
            return $this->output;
        }
        // dump($idCustomer);
        // die;
    }

    /**
     * Create Seller Order
     * Create Cart using Prestashop /cart api then provide id_cart
     * @api /createorder
     *
     * @return json
     */
    public function createOrder($fields)
    {
        $objCart = new Cart($fields['id_cart']);
        $objAddress = new Address($objCart->id_address_delivery);
        $this->context->country = new Country($objAddress->id_country, (int)$this->context->language->id);

        $module = $fields['module'];
        $idCart = $fields['id_cart'];
        $idCustomer = $fields['id_customer'];
        $totalPaid = $fields['total_paid'];
        $payment = $fields['payment'];
        $orderStatus = $fields['current_state'];
        $paymentModule = Module::getInstanceByName($module);
        $customer = new Customer($idCustomer);
        $paymentModule->validateOrder(
            $idCart,
            $orderStatus,
            $totalPaid,
            $payment,
            null,
            array('transaction_id' => $fields['transaction_id']),
            null,
            false,
            $objCart->secure_key
        );
        if ($paymentModule->currentOrder) {
            if (isset($fields['message']) && !empty($fields['message'])) {
                $this->saveOrderMessage($fields['message'], $paymentModule->currentOrder, $idCart, $idCustomer);
            }

            $this->output['success'] = true;
            $this->output['message'] = 'Order created successfully.';
            $this->output['id_order'] = $paymentModule->currentOrder;
            $this->output['reference'] = $paymentModule->currentOrderReference;
            return $this->output;
        } else {
            $this->output['success'] = false;
            $this->output['message'] = 'Error while creating order.';
            return $this->output;
        }
    }

    /**
     * Update order status
     *
     * @api /api/seller/updateorderstatus
     * @method POST
     * @post data:
     *    {
	 *      "id_order": "7",
	 *      "id_order_state": "2"
     *    }
     * @param array $fields
     * @return void
     */
    public function updateOrderStatus($fields)
    {
        //$idSeller = $this->idSeller;
        $idOrder = $fields['id_order'];
        $idOrderStatus = $fields['id_order_state'];

        $objOrderStatus = new WkMpSellerOrderStatus();

        /*$order = new Order($idOrder);
        $products = $order->getProducts();
        if ($products) {
            $flag = true;
            foreach ($products as $prod) {
                $isProductSeller = WkMpSellerProduct::checkPsProduct(
                    $prod['product_id'],
                    $idSeller
                );

                if (!$isProductSeller) {
                    $flag = false;
                    break;
                }
            }
        }

        $objOrderStatus->processSellerOrderStatus($idOrder, $idSeller, $idOrderStatus);*/

        // this order is belong to only current seller
        //if ($flag) {
            $objOrderStatus->updateOrderByIdOrderAndIdOrderState($idOrder, $idOrderStatus);
        //}

        $this->output['success'] = true;
        $this->output['message'] = 'Order status updated successfully.';
        return $this->output;
    }
}
