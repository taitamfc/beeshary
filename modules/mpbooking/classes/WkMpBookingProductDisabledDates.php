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

class WkMpBookingProductDisabledDates extends ObjectModel
{
    public $id_booking_product_info;
    public $disable_special_days_active;
    public $disabled_dates_slots_active;
    public $disabled_special_days;
    public $disabled_dates_slots;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_booking_product_disabled_dates',
        'primary' => 'id_disabled_dates',
        'fields' => array(
            'id_booking_product_info' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'disable_special_days_active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'disabled_dates_slots_active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'disabled_special_days' => array('type' => self::TYPE_STRING),
            'disabled_dates_slots' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );

    public function validationDisableDatesFieldJs($params)
    {
        if (isset($params['disable_special_days_active']) && $params['disable_special_days_active']) {
            $objModule = new MpBooking();
            if (empty($params['disabled_special_days'])
                || (isset($params['disabled_special_days']) && !count($params['disabled_special_days']))
            ) {
                $data = array(
                    'status' => 'ko',
                    'tab' => 'booking-disable-dates',
                    'multilang' => '0',
                    'inputName' => 'disabled_special_days',
                    'msg' => $objModule->l(
                        'if Disable Special Days option is active, Please select at least one special day to disable.',
                        'WkMpBookingProductDisabledDates'
                    )
                );
                die(json_encode($data));
            }
        }
        if (isset($params['disable_specific_days_active']) && $params['disable_specific_days_active']
            && !count(Tools::jsonDecode($params['disabled_specific_dates_json'], true))
        ) {
            $data = array(
                'status' => 'ko',
                'tab' => 'booking-disable-dates',
                'multilang' => '0',
                'inputName' => 'disabled_specific_dates_json',
                'msg' => $objModule->l(
                    'if Disable Specific Dates option is active, Please select at least one date to disable.',
                    'WkMpBookingProductDisabledDates'
                )
            );
            die(json_encode($data));
        }
    }

    public function getBookingProductDisableDates($id_booking_product_info)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_disabled_dates`
            WHERE `id_booking_product_info`='.(int) $id_booking_product_info
        );
    }

    public function getMpBookingProductDisableDates($id_booking_product_info)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_product_disabled_dates`
            WHERE `id_booking_product_info`='.(int) $id_booking_product_info
        );
    }

    public function getBookingProductDisableDatesInfoFormatted($id_booking_product_info)
    {
        $disableDatesInfo = array();
        $disableDatesInfo['disabledDays'] = array();
        $disableDatesInfo['disabledDates'] = array();
        $bookingDisableDates = $this->getBookingProductDisableDates($id_booking_product_info);
        if ($bookingDisableDates) {
            if ($bookingDisableDates['disable_special_days_active'] && $bookingDisableDates['disabled_special_days']) {
                $disableDatesInfo['disabledDays'] = json_decode(
                    $bookingDisableDates['disabled_special_days'],
                    true
                );
            }
            if ($bookingDisableDates['disabled_dates_slots_active'] && $bookingDisableDates['disabled_dates_slots']) {
                $disabledDates = array();
                $totalDaySeconds = 24 * 60 * 60;
                $bookingDisableDatesArr = json_decode($bookingDisableDates['disabled_dates_slots'], true);
                // if product is date range wise booking product
                $objBookingTimeSlots = new WkMpBookingProductTimeSlotPrices();
                foreach ($bookingDisableDatesArr as $disableDateRange) {
                    if (($disableDateFrom = $disableDateRange['date_from']) && ($disableDateTo = $disableDateRange['date_to'])) {
                        if (Validate::isLoadedObject(
                            $bookingProductInfo = new WkMpBookingProductInformation($id_booking_product_info)
                        )) {
                            if ($bookingProductInfo = (array) $bookingProductInfo) {
                                for ($date = strtotime($disableDateFrom); $date <= (strtotime($disableDateTo)); $date = ($date + $totalDaySeconds)) {
                                    if ($bookingProductInfo['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                                        if (!in_array(date('Y-m-d', $date), $disabledDates)) {
                                            $disableDatesInfo['disabledDates'][] = date('Y-m-d', $date);
                                        }
                                    } else {
                                        $bookingTimeSlots = $objBookingTimeSlots->getBookingProductTimeSlotsOnDate(
                                            $id_booking_product_info,
                                            date('Y-m-d', $date),
                                            true
                                        );
                                        $anySlotActive = false;
                                        if ($bookingTimeSlots) {
                                            foreach ($bookingTimeSlots as $slot) {
                                                if ($slot['active']) {
                                                    $anySlotActive = 1;
                                                }
                                            }
                                        }
                                        if (!$anySlotActive) {
                                            $disableDatesInfo['disabledDates'][] = date('Y-m-d', $date);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $disableDatesInfo;
    }

    public function getBookingProductDisableDatesInDateRange($id_booking_product_info, $date_from, $date_to)
    {
        $bookingDisableDatesInfo = $this->getBookingProductDisableDatesInfoFormatted($id_booking_product_info);
        $disabledDays = $bookingDisableDatesInfo['disabledDays'];
        $disabledDates = $bookingDisableDatesInfo['disabledDates'];
        $dateRangeDisabledDates = array();
        if ($id_booking_product_info) {
            if (Validate::isLoadedObject(
                $bookingProductInfo = new WkMpBookingProductInformation($id_booking_product_info)
            )) {
                if ($bookingProductInfo = (array) $bookingProductInfo) {
                    for ($date = strtotime($date_from); $date <= (strtotime($date_to)); $date = ($date + (24 * 60 * 60))) {
                        if ($bookingProductInfo['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE
                            && !Configuration::get('WK_MP_CONSIDER_DATE_TO')
                            && $date == strtotime($date_to)
                        ) {
                            break;
                        }
                        $currentDate = date('Y-m-d', $date);
                        if (!in_array($currentDate, $dateRangeDisabledDates)) {
                            if ($disabledDates) {
                                if (in_array($currentDate, $disabledDates)) {
                                    $dateRangeDisabledDates[] = $currentDate;
                                }
                            }
                            if ($disabledDays) {
                                $weekDay = date("w", strtotime($currentDate));
                                if (in_array($weekDay, $disabledDays)) {
                                    $dateRangeDisabledDates[] = $currentDate;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $dateRangeDisabledDates;
    }

    public function deleteDisableDatesByIdBookingProductInfo($id_booking_product_info)
    {
        return Db::getInstance()->delete(
            'wk_mp_booking_product_disabled_dates',
            '`id_booking_product_info`='.(int) $id_booking_product_info
        );
    }
}
