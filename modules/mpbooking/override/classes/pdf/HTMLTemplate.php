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

class HTMLTemplate extends HTMLTemplateCore
{
    protected function getTemplate($template_name)
    {
        if (Module::isInstalled('mpbooking') && Module::isEnabled('mpbooking')) {
            include_once _PS_MODULE_DIR_.'mpbooking/classes/WkMpBookingRequiredClasses.php';
            $overridden_template = _PS_MODULE_DIR_.'mpbooking/views/templates/hook/'.$template_name.'.tpl';
            if (file_exists($overridden_template)) {
                if ($order_details = $this->order_invoice->getProducts()) {
                    $bookingProductInfo = new WkMpBookingProductInformation();
                    $wkBookingsOrders = new WkMpBookingOrder();
                    $bookingProductExists = 0;
                    foreach ($order_details as $id => &$order_detail) {
                        if ($order_detail['reduction_amount_tax_excl'] > 0) {
                            $order_detail['unit_price_tax_excl_before_specific_price'] = $order_detail['unit_price_tax_excl_including_ecotax'] + $order_detail['reduction_amount_tax_excl'];
                        } elseif ($order_detail['reduction_percent'] > 0) {
                            if ($order_detail['reduction_percent'] == 100) {
                                $order_detail['unit_price_tax_excl_before_specific_price'] = 0;
                            } else {
                                $order_detail['unit_price_tax_excl_before_specific_price'] = (100 * $order_detail['unit_price_tax_excl_including_ecotax']) / (100 - $order_detail['reduction_percent']);
                            }
                        }
                        $taxes = OrderDetail::getTaxListStatic($id);
                        $tax_temp = array();
                        foreach ($taxes as $tax) {
                            $obj = new Tax($tax['id_tax']);
                            $translator = Context::getContext()->getTranslator();
                            $tax_temp[] = $translator->trans(
                                '%taxrate%%space%%',
                                array(
                                    '%taxrate%' => ($obj->rate + 0),
                                    '%space%' => '&nbsp;',
                                ),
                                'Shop.Pdf'
                            );
                        }
                        $order_detail['order_detail_tax'] = $taxes;
                        $order_detail['order_detail_tax_label'] = implode(', ', $tax_temp);
                        if ($bookingProductInfo->getBookingProductInfo(
                            0,
                            $order_detail['product_id']
                        )) {
                            if ($bookingProductOrderInfo = $wkBookingsOrders->getBookingProductOrderInfo(
                                $order_detail['product_id'],
                                $order_detail['id_order']
                            )) {
                                foreach ($bookingProductOrderInfo as $keyProduct => $cartBooking) {
                                    $bookingProductOrderInfo[$keyProduct]['total_range_feature_price_tax_excl'] = (float) ($cartBooking['quantity'] * $cartBooking['range_feature_price_tax_incl']);
                                    $bookingProductOrderInfo[$keyProduct]['unit_feature_price_tax_excl'] = (float) $cartBooking['range_feature_price_tax_excl'];
                                }
                                $order_detail['isBookingProduct'] = 1;
                                $order_detail['booking_product_data'] = $bookingProductOrderInfo;
                                $bookingProductExists = 1;
                            }
                        }
                    }
                    unset($tax_temp);
                    unset($order_detail);
                    if (Configuration::get('PS_PDF_IMG_INVOICE')) {
                        foreach ($order_details as &$order_detail) {
                            if ($order_detail['image'] != null) {
                                $name = 'product_mini_' . (int) $order_detail['product_id'] . (isset($order_detail['product_attribute_id']) ? '_' . (int) $order_detail['product_attribute_id'] : '') . '.jpg';
                                $path = _PS_PROD_IMG_DIR_ . $order_detail['image']->getExistingImgPath() . '.jpg';
                                $order_detail['image_tag'] = preg_replace(
                                    '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                                    _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                                    ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                                    1
                                );
                                if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                                    $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                                } else {
                                    $order_detail['image_size'] = false;
                                }
                            }
                        }
                        unset($order_detail); // don't overwrite the last order_detail later
                    }
                    if ($bookingProductExists) {
                        $this->smarty->assign('order_details', $order_details);
                        return $overridden_template;
                    }
                }
            }
        }
        return parent::getTemplate($template_name);
    }

    public function getContent()
    {
        parent::getContent();
    }

    public function getFilename()
    {
        parent::getFilename();
    }

    public function getBulkFilename()
    {
        parent::getBulkFilename();
    }
}
