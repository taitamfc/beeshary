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

class WkMpBookingProductTimeSlotPrices extends ObjectModel
{
    public $id_booking_product_info;
    public $date_from;
    public $date_to;
    public $time_slot_from;
    public $time_slot_to;
    public $price;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_booking_time_slots_prices',
        'primary' => 'id_time_slots_price',
        'fields' => array(
            'id_booking_product_info' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'time_slot_from' => array('type' => self::TYPE_STRING, 'required' => true),
            'time_slot_to' => array('type' => self::TYPE_STRING, 'required' => true),
            'price' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'active' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );

    //send  $status=2 for all slots 1 for active and 0 for incative
    public function getBookingProductTimeSlotsOnDate(
        $id_booking_product_info,
        $date,
        $deactive_disabled_dates = false,
        $status = 2
    ) {
        $date = date('Y-m-d', strtotime($date));
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_time_slots_prices`
        WHERE `id_booking_product_info`='.(int) $id_booking_product_info.'
        AND `date_from` <= \''.pSql($date).'\'
        AND `date_to` >= \''.pSql($date).'\'';
        if ($status == 1) {
            $sql .= ' AND `active` = 1';
        } elseif ($status == 0) {
            $sql .= ' AND `active` = 0';
        }
        $timeSlots = Db::getInstance()->executeS($sql);
        if ($deactive_disabled_dates) {
            if ($timeSlots) {
                $objBookingDisableDates = new WkMpBookingProductDisabledDates();
                $bookingDisableDates = $objBookingDisableDates->getBookingProductDisableDates($id_booking_product_info);
                if ($bookingDisableDates) {
                    $underDisabledays = 0;
                    if ($bookingDisableDates['disable_special_days_active']
                        && $bookingDisableDates['disabled_special_days']
                    ) {
                        $disabledDays = Tools::jsonDecode($bookingDisableDates['disabled_special_days'], true);
                        $weekDay = date("w", strtotime($date));
                        if (in_array($weekDay, $disabledDays)) {
                            $underDisabledays = 1;
                        }
                    }
                    foreach ($timeSlots as $key => $slot) {
                        if ($bookingDisableDates['disabled_dates_slots_active']
                            && $bookingDisableDates['disabled_dates_slots']
                        ) {
                            $bookingDisableDatesArray = json_decode(
                                $bookingDisableDates['disabled_dates_slots'],
                                true
                            );
                            foreach ($bookingDisableDatesArray as $disableDateRange) {
                                if (($dateFrom = $disableDateRange['date_from'])
                                    && ($dateTo = $disableDateRange['date_to'])
                                ) {
                                    if (strtotime($dateFrom) <= strtotime($date)
                                        &&  strtotime($dateTo) >= strtotime($date)
                                    ) {
                                        if (isset($disableDateRange['slots_info'])) {
                                            if (count($disableDateRange['slots_info'])) {
                                                foreach ($disableDateRange['slots_info'] as $slotInfo) {
                                                    if ($slotInfo['time_from'] == $slot['time_slot_from']
                                                        && $slotInfo['time_to'] == $slot['time_slot_to']
                                                    ) {
                                                        $timeSlots[$key]['active'] = 0;
                                                    }
                                                }
                                            } else {
                                                $timeSlots[$key]['active'] = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($underDisabledays) {
                            $timeSlots[$key]['active'] = 0;
                        }
                        if (($status == 1 && !$timeSlots[$key]['active'])
                            || ($status == 0 && $timeSlots[$key]['active'])
                        ) {
                            unset($timeSlots[$key]);
                        }
                    }
                }
            }
        }
        return $timeSlots;
    }

    public function getProductAllTimeSlotsOnDateRange($id_booking_product_info, $date_from, $date_to)
    {
        $date_from = date('Y-m-d', strtotime($date_from));
        $date_to = date('Y-m-d', strtotime($date_to));
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_time_slots_prices`
            WHERE `id_booking_product_info`='.(int) $id_booking_product_info.'
            AND `date_from` <= \''.pSql($date_from).'\'
            AND `date_to` >= \''.pSql($date_to).'\''
        );
    }

    public function getBookingProductAllTimeSlotsFormatted($id_booking_product_info)
    {
        $timeSlots = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_time_slots_prices`
            WHERE `id_booking_product_info`='.(int) $id_booking_product_info
        );
        if ($timeSlots) {
            $timeSlotsFormatted = array();
            foreach ($timeSlots as $key => $timeSlot) {
                $date_key = date('Y-m-d', strtotime($timeSlot['date_from'])).'_'.
                date('Y-m-d', strtotime($timeSlot['date_to']));
                $timeSlotsFormatted[$date_key]['date_from'] = date('d-m-Y', strtotime($timeSlot['date_from']));
                $timeSlotsFormatted[$date_key]['date_to'] = date('d-m-Y', strtotime($timeSlot['date_to']));
                $timeSlotsFormatted[$date_key]['id_booking_product_info'] = $timeSlot['id_booking_product_info'];
                $timeSlotsFormatted[$date_key]['time_slots'][$key]['time_from'] = $timeSlot['time_slot_from'];
                $timeSlotsFormatted[$date_key]['time_slots'][$key]['time_to'] = $timeSlot['time_slot_to'];
                $timeSlotsFormatted[$date_key]['time_slots'][$key]['slot_price'] = $timeSlot['price'];
                $timeSlotsFormatted[$date_key]['time_slots'][$key]['id_slot'] = $timeSlot['id_time_slots_price'];
                $timeSlotsFormatted[$date_key]['time_slots'][$key]['active'] = $timeSlot['active'];
            }
            return $timeSlotsFormatted;
        }
        return false;
    }

    public function getProductTimeSlotsSelectedDates($id_booking_product_info)
    {
        $totalDaySeconds = 24 * 60 * 60;
        $selectedDates = array();
        $timeSlotsDates = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_time_slots_prices`
            WHERE `id_booking_product_info`='.(int) $id_booking_product_info.'
            GROUP BY date_From, date_to'
        );
        foreach ($timeSlotsDates as $slotDates) {
            $dateFrom = date('Y-m-d', strtotime($slotDates['date_from']));
            $dateTo = date('Y-m-d', strtotime($slotDates['date_to']));
            for ($date = strtotime($dateFrom); $date <= strtotime($dateTo); $date = ($date + $totalDaySeconds)) {
                $currentDate = date('Y-m-d', $date);
                // check if day is in disabled days
                $timeSlotsOnDate = $this->getBookingProductTimeSlotsOnDate(
                    $id_booking_product_info,
                    $currentDate,
                    true,
                    1
                );
                if ($timeSlotsOnDate) {
                    if (!in_array($currentDate, $selectedDates)) {
                        $selectedDates[] = $currentDate;
                    }
                }
            }
        }
        return $selectedDates;
    }

    public function getProductTimeSlotDetails($id_booking_product_info, $date, $time_slot_from, $time_slot_to)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_time_slots_prices`
            WHERE `id_booking_product_info`='.(int) $id_booking_product_info.'
            AND `date_from` <= \''.pSql($date).'\'
            AND `date_to` >= \''.pSql($date).'\''.'
            AND `time_slot_from` = \''.pSql($time_slot_from).'\'
            AND `time_slot_to` = \''.pSql($time_slot_to).'\''
        );
    }

    public function deleteTimeSlotsByIdBookingProductInfo($id_booking_product_info)
    {
        return Db::getInstance()->delete(
            'wk_mp_booking_time_slots_prices',
            '`id_booking_product_info`='.(int) $id_booking_product_info
        );
    }

    public function validateTimeSlotsDuplicacyInOtherDateRanges(
        $id_booking_product_info,
        $dateFrom,
        $dateTo,
        $timeFrom,
        $timeto
    ) {
        $moduleInstance = new MpBooking();
        $dateFrom = date('Y-m-d', strtotime($dateFrom));
        $dateTo = date('Y-m-d', strtotime($dateTo));
        $error = false;
        $timSlotsInfo = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_time_slots_prices`
            WHERE `id_booking_product_info`='.(int) $id_booking_product_info.'
            AND `date_from` <= \''.pSql($dateTo).'\'
            AND `date_to` >= \''.pSql($dateFrom).'\''
        );
        if ($timSlotsInfo) {
            foreach ($timSlotsInfo as $timeSlotRow) {
                if ((strtotime($timeFrom) <= strtotime($timeSlotRow['time_slot_to']))
                    && (strtotime($timeto) >= strtotime($timeSlotRow['time_slot_from']))
                ) {
                    $error = $moduleInstance->l('Time Slot ', 'WkMpBookingProductTimeSlotPrices').$timeFrom.
                    $moduleInstance->l(' to ', 'WkMpBookingProductTimeSlotPrices').$timeto.
                    $moduleInstance->l(' for the date range ').date('Y-m-d', strtotime($dateFrom)).
                    $moduleInstance->l(' To ', 'WkMpBookingProductTimeSlotPrices').date('Y-m-d', strtotime($dateTo)).
                    $moduleInstance->l(' not saved because of Duplicacy.', 'WkMpBookingProductTimeSlotPrices');
                    break;
                }
            }
        }
        return $error;
    }
	
	public function deleteBookingProductTimeSlots($id_product)
    {
        return Db::getInstance()->delete('wk_mp_booking_time_slots_prices', '`id_product`='.(int) $id_product);
    }

    public function deleteBookingProductTimeSlotsByIdMpProduct($id_booking_product_info)
    {
        return Db::getInstance()->delete('wk_mp_booking_time_slots_prices', '`id_booking_product_info`='.(int) $id_booking_product_info);
    }

    public static function deleteBookingProductTimeSlotsByIdMpProductByPsProduct($id_booking_product_info, $id_product)
    {
        return Db::getInstance()->delete('wk_mp_booking_time_slots_prices', '`id_booking_product_info`='.(int) $id_booking_product_info .' AND `id_product`='.(int) $id_product);
    }
}
