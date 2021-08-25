<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class mpstorelocatorFilterStateModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function initContent()
    {
        $id_country = Tools::getValue('id_country');
        $has_states = Country::containsStates($id_country);
        if ($has_states) {
            $states = State::getStatesByIdCountry((int) $id_country);
            if ($states) {
                $jsondata = Tools::jsonEncode($states);
            } else {
                $jsondata = Tools::jsonEncode(array('failed'));
            }
        } else {
            $jsondata = Tools::jsonEncode(array('no_states'));
        }
        die($jsondata);
    }
}
