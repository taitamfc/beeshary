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

class WkMpBookingHelper extends ObjectModel
{
    public static function assignDataTableVariables()
    {
        $objMpBooking = new MpBooking();
        $jsVars = array(
                'display_name' => $objMpBooking->l('Affichage', 'MpBooking'),
                'records_name' => $objMpBooking->l('activités par page', 'MpBooking'),
                'no_product' => $objMpBooking->l('Aucune activité', 'MpBooking'),
                'show_page' => $objMpBooking->l('Page', 'MpBooking'),
                'show_of' => $objMpBooking->l('sur', 'MpBooking'),
                'no_record' => $objMpBooking->l('Aucune activité', 'MpBooking'),
                'filter_from' => $objMpBooking->l('filtrés à partir de', 'MpBooking'),
                't_record' => $objMpBooking->l('total', 'MpBooking'),
                'search_item' => $objMpBooking->l('Rechercher', 'MpBooking'),
                'p_page' => $objMpBooking->l('Précedent', 'MpBooking'),
                'n_page' => $objMpBooking->l('Suivant', 'MpBooking'),
            );
        Media::addJsDef($jsVars);
    }

    //To get number of datys between two dates.
    public static function getNumberOfDays($dateFrom, $dateTo)
    {
        $startDate = new DateTime($dateFrom);
        $endDate = new DateTime($dateTo);
        $daysDifference = $startDate->diff($endDate)->days;
        if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
            $daysDifference += 1;
        }
        return $daysDifference;
    }
}
