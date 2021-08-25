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

class WkMpProductWs extends WkMpWebservice
{
    /**
     * Get Booking Products
     *
     * @api /getbookingproduct
     *
     * @method GET
     * @return json
     */
    public function getBookingProduct($idProduct)
    {
        unset($this->output['message']);
        $product = $this->getCatalogProducts($this->context->language->id, $idProduct, null);
        if ($product) {
            $product['booking_form'] = $this->bookingFormDetails($product['id_product']);
            // get booking product images
            $product['extras'] = $this->getBookingProductExtras($product['id_product']);
            if (!empty($product['booking_form'])) {
                $this->output['success'] = true;
                $this->output['item'] = $product;
                return $this->output;
            } else {
                $this->output['success'] = false;
                $this->output['msg'] = 'product is not booking type';
                return $this->output;
            }
        } else {
            $this->output['success'] = false;
            $this->output['msg'] = 'Invalid product';
            return $this->output;
        }
    }

    public function getCatalogProducts($id_lang, $idProduct, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        $sql = 'SELECT *
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ')
                WHERE pl.`id_lang` = ' . (int) $id_lang . ' AND p.`id_product` =
                ' . (int) $idProduct . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                ORDER BY pl.`name`';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    /**
     * Get Seller Products
     *
     * By default return only active products
     * to get all status = all, and deactive status = 0/false
     * @api /sellerproduct
     * @api /sellerproduct/[id_mp_product]
     *
     * @method GET
     * @todo write the code according to the status given in the URL
     * $status = Tools::getValue('status');
     * @return json
     */
    public function sellerProduct()
    {
        $limit = Tools::getValue('limit');
        if ($limit && (count($limit) > 2)) {
            $this->output['success'] = false;
            $this->output['message'] = 'The "limit" value has to be formed as this example: "5,25" or "10"';
            return $this->output;
        }

        $idLang = Tools::getValue('id_lang');
        $lang = new Language($idLang);
        if (!Validate::isLoadedObject($lang)) {
            $idLang = false;
        }

        if (isset($this->wsObject->urlSegment[2])) {
            $idMpProduct = $this->wsObject->urlSegment[2];
            return $this->getProductInformation($idMpProduct, $idLang);
        } else {
            return $this->getProductList($idLang);
        }
    }

    public function getProductInformation($idMpProduct, $idLang)
    {
        $product = WkMpSellerProduct::getSellerProductByIdProduct($idMpProduct, $idLang);
        if ($product) {
            $combinations = $this->getMpCombinationsResume($idMpProduct);
            if ($combinations) {
                $product['combinations'] = $combinations;
            } else {
                $product['combinations'] = array();
            }

            if ($product['ps_id_carrier_reference']) {
                $product['ps_id_carrier_reference'] = unserialize($product['ps_id_carrier_reference']);
            } else {
                $product['ps_id_carrier_reference'] = array();
            }

            // Get product category
            $catg = WkMpSellerProductCategory::getMultipleCategories($product['id_mp_product']);
            if ($catg) {
                $product['product_category'] = $catg;
            } else {
                $product['product_category'] = array();
            }

            // Booking Details
            $product['booking_form'] = $this->bookingFormDetails($product['id_ps_product']);

            $this->output['success'] = true;
            $this->output['item'] = $product;
            return $this->output;
        } else {
            $this->output['success'] = false;
            $this->output['message'] = 'No product available.';
            return $this->output;
        }
    }

    public function getDisabledDays($idBookingProductInfo)
    {
        $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $objDisableDate = new WkMpBookingProductDisabledDates();
        $daysDates = $objDisableDate->getBookingProductDisableDatesInfoFormatted($idBookingProductInfo);
        $daysName = array();
        foreach ($daysDates['disabledDays'] as $k => $value) {
            $daysDates['disabledDays'][$k] = $days[$value];
        }
        return $daysDates;
    }

    public function getBookingProductExtras($idProduct)
    {
        $query = 'SELECT spl.link_rewrite, spi.id_ps_image, s.shop_name_unique, s.seller_firstname, s.seller_lastname, s.city as seller_city,
            CONCAT(\'/modules/marketplace/views/img/seller_img/\', s.profile_image) as seller_image
            FROM `'._DB_PREFIX_.'wk_mp_seller_product_image` spi
            LEFT JOIN `'. _DB_PREFIX_ .'wk_mp_seller_product` sp on spi.seller_product_id = sp.id_mp_product
            LEFT JOIN `'. _DB_PREFIX_ .'wk_mp_seller_product_lang` spl on spl.id_mp_product = sp.id_mp_product
            LEFT JOIN `'. _DB_PREFIX_ .'wk_mp_seller` s on s.id_seller = sp.id_seller
            WHERE spl.id_lang=1 AND sp.id_ps_product = '.(int) $idProduct .'  order by spi.id_ps_image DESC';
        $productImages = Db::getInstance()->executeS($query);
        $extras = null;
        if ($productImages && !empty($productImages) && count($productImages) > 0) {
            $extras = $productImages[0];
        }

        if (isset($extras['link_rewrite']) && isset($extras['id_ps_image'])) {
            $extras['image'] = sprintf('/%s/%s.jpg', $extras['id_ps_image'], $extras['link_rewrite']);
        }
        return $extras;
    }

    public function bookingFormDetails($idProduct)
    {
        $module = new MpWebservice();
        $objBookingProductInfo = new WkMpBookingProductInformation();
        $return = array();
        //$idProduct = Tools::getValue('id_product');
        if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(0, $idProduct)) {
            $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
            $dateFrom = date('Y-m-d');
            if (Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
                $dateTo = date('Y-m-d', strtotime($dateFrom));
            } else {
                $dateTo = date('Y-m-d', strtotime("+1 day", strtotime($dateFrom)));
            }

            $return['date_from'] = date('d-m-Y', strtotime($dateFrom));
            $return['date_to'] = date('d-m-Y', strtotime($dateTo));

            
            $return['disable_dates'] = $this->getDisabledDays($idBookingProductInfo);

            $objBookingOrders = new WkMpBookingOrder();
            $bookingTimeSlotPriceToday = false;
            $bookingTimeSlotPrice = false;
            $objBookingDisableDates = new WkMpBookingProductDisabledDates();
            if ($bookingProductInfo['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                $objTimeSlots = new WkMpBookingProductTimeSlotPrices();
                $bookingTimeSlots = $objTimeSlots->getBookingProductTimeSlotsOnDate(
                    $idBookingProductInfo,
                    $dateFrom,
                    true,
                    1
                );
                if ($bookingTimeSlots) {
                    $flag = 0;
                    $totalSlotsQty = 0;
                    foreach ($bookingTimeSlots as $key => $timeSlot) {
                        $bookedSlotQuantity = $objBookingOrders->getProductTimeSlotOrderedQuantity(
                            $idProduct,
                            $dateFrom,
                            $timeSlot['time_slot_from'],
                            $timeSlot['time_slot_to'],
                            1
                        );
                        $availQty = $bookingProductInfo['quantity'] - $bookedSlotQuantity;
                        $bookingTimeSlots[$key]['available_qty'] = ($availQty < 0) ? 0 : $availQty;
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
                            $idBookingProductInfo,
                            $dateFrom,
                            $dateFrom,
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

                    $return['totalSlotsQty'] = $totalSlotsQty;
                    //$this->context->smarty->assign('totalSlotsQty', $totalSlotsQty);
                    $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                        $idBookingProductInfo,
                        $dateFrom,
                        $dateFrom,
                        $bookingTimeSlotPriceToday,
                        $this->context->currency->id
                    );
                    if ($totalPrice) {
                        $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                        if (!$priceDisplay || $priceDisplay == 2) {
                            $productFeaturePrice = $totalPrice['total_price_tax_incl'];
                        } elseif ($priceDisplay == 1) {
                            $productFeaturePrice = $totalPrice['total_price_tax_excl'];
                        }
                    }
                } else {
                    $productFeaturePrice = 0;
                }
                // get disable dates info for current selected dates
                $selectedDatesDisableInfo = $objBookingDisableDates->getBookingProductDisableDatesInDateRange(
                    $idBookingProductInfo,
                    $dateFrom,
                    $dateFrom
                );
                $return['bookingTimeSlots'] = $bookingTimeSlots;
                //$this->context->smarty->assign('bookingTimeSlots', $bookingTimeSlots);
            } else {
                $totalPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                    $idBookingProductInfo,
                    $dateFrom,
                    $dateTo,
                    $bookingTimeSlotPriceToday,
                    $this->context->currency->id
                );
                if ($totalPrice) {
                    $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                    if (!$priceDisplay || $priceDisplay == 2) {
                        $productFeaturePrice = $totalPrice['total_price_tax_incl'];
                    } elseif ($priceDisplay == 1) {
                        $productFeaturePrice = $totalPrice['total_price_tax_excl'];
                    }
                }
                // get disable dates info for current selected dates
                $selectedDatesDisableInfo = $objBookingDisableDates->getBookingProductDisableDatesInDateRange(
                    $idBookingProductInfo,
                    $dateFrom,
                    $dateTo
                );
            }
            $bookedQuantity = $objBookingOrders->getProductOrderedQuantityInDateRange($idProduct, $dateFrom, $dateTo, 1);
            $maxAvailableQuantity = $bookingProductInfo['quantity'] - $bookedQuantity;

            $objFeaturePrice = new WkMpBookingProductFeaturePricing();
            if ($bookingPricePlans = $objFeaturePrice->getProductFeaturePriceRules(
                $bookingProductInfo['id_booking_product_info'],
                false,
                1
            )) {
                foreach ($bookingPricePlans as &$plan) {
                    $plan['impact_value_formated'] = Tools::displayPrice(Tools::convertPrice($plan['impact_value']));
                }
            }
            //Get featurePrice priority
            $featurePricePriority = Configuration::get('WK_MP_PRODUCT_FEATURE_PRICING_PRIORITY');
            $featurePricePriority = explode(';', $featurePricePriority);
            foreach ($featurePricePriority as $key => $priority) {
                if ($priority == 'date_range') {
                    $featurePricePriority[$key] = $module->l('For Date Range');
                } elseif ($priority == 'specific_date') {
                    $featurePricePriority[$key] = $module->l('For Specific Date');
                } elseif ($priority == 'special_day') {
                    $featurePricePriority[$key] = $module->l('For Special Days');
                }
            }

            $return['booking_type_time_slot'] = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT;
            $return['booking_type_date_range'] = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE;
            $return['selectedDatesDisabled'] = $selectedDatesDisableInfo ? 1 : 0;
            $return['featurePricePriority'] = $featurePricePriority;
            $return['maxAvailableQuantity'] = $maxAvailableQuantity;
            $return['bookingPricePlans'] = $bookingPricePlans;
            $return['bookingProductInformation'] = $bookingProductInfo;
            $return['productFeaturePrice'] = Tools::displayPrice($productFeaturePrice);
            //$return['module_dir'] = _MODULE_DIR_.$this->name;
            $return['show_feature_price_rules'] = Configuration::get('WK_MP_FEATURE_PRICE_RULES_SHOW');
            // $this->context->smarty->assign(
            //     array(
            //         'booking_type_time_slot' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT,
            //         'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
            //         'selectedDatesDisabled' => $selectedDatesDisableInfo ? 1 : 0,
            //         'featurePricePriority' => $featurePricePriority,
            //         'maxAvailableQuantity' => $maxAvailableQuantity,
            //         'bookingPricePlans' => $bookingPricePlans,
            //         'bookingProductInformation' => $bookingProductInfo,
            //         'productFeaturePrice' => Tools::displayPrice($productFeaturePrice),
            //         'module_dir' => _MODULE_DIR_.$this->name,
            //         'show_feature_price_rules' => Configuration::get('WK_MP_FEATURE_PRICE_RULES_SHOW')
            //     )
            // );

            //return $this->fetch('module:mpbooking/views/templates/hook/customerBookingInterface.tpl');
        }
        return $return;
    }

    public function getProductList($idLang)
    {
        $products = $this->getSellerProductDetails($this->idSeller, $idLang);
        if ($products) {
            foreach ($products as &$prod) {
                // Get product category
                $catg = WkMpSellerProductCategory::getMultipleCategories($prod['id_mp_product']);
                if ($catg) {
                    $prod['product_category'] = $catg;
                } else {
                    $prod['product_category'] = array();
                }

                // Get product combination
                $combinations = $this->getMpCombinationsResume($prod['id_mp_product']);
                if ($combinations) {
                    $prod['combinations'] = $combinations;
                } else {
                    $prod['combinations'] = array();
                }

                if ($prod['ps_id_carrier_reference']) {
                    $prod['ps_id_carrier_reference'] = unserialize($prod['ps_id_carrier_reference']);
                } else {
                    $prod['ps_id_carrier_reference'] = array();
                }
            }
            $this->output['success'] = true;
            $this->output['item'] = array_values($products);
            return $this->output;
        } else {
            $this->output['success'] = false;
            $this->output['message'] = 'No product available.';
            return $this->output;
        }
    }

    /**
    * copied from Mp 'WkMpProductAttribute' class
    * with changes
    */
    public function getMpCombinationsResume($mpIdProduct)
    {
        $context = Context::getContext();
        $combinationDetail = array();
        $mpCombinationDetail = $this->getMpProductCombinations($mpIdProduct, $context->language->id, $context->shop->id);
        if (isset($mpCombinationDetail) && $mpCombinationDetail) {
            foreach ($mpCombinationDetail as $valCombination) {
                $idMpProductAttribute = $valCombination['id_mp_product_attribute'];

                if (!isset($combinationDetail[$idMpProductAttribute]['attribute_designation'])) {
                    $combinationDetail[$idMpProductAttribute]['attribute_designation'] = '';
                }
                $combinationDetail[$idMpProductAttribute]['id_mp_product_attribute'] = $idMpProductAttribute;
                $combinationDetail[$idMpProductAttribute]['id_mp_product'] = $valCombination['id_mp_product'];
                $combinationDetail[$idMpProductAttribute]['mp_quantity'] = $valCombination['mp_quantity'];
                $combinationDetail[$idMpProductAttribute]['mp_price_formet'] = Tools::displayPrice($valCombination['mp_price'], new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                $combinationDetail[$idMpProductAttribute]['mp_price'] = $valCombination['mp_price'];
                $combinationDetail[$idMpProductAttribute]['mp_weight'] = $valCombination['mp_weight'];
                $combinationDetail[$idMpProductAttribute]['mp_reference'] = $valCombination['mp_reference'];
                $combinationDetail[$idMpProductAttribute]['mp_default_on'] = $valCombination['mp_default_on'];
                $combinationDetail[$idMpProductAttribute]['attribute_designation'] .= $valCombination['group_name'].' - '.$valCombination['attribute_name'].', ';
                $combinationDetail[$idMpProductAttribute]['product_option_value'][] = $valCombination['id_ps_attribute'];
                $combinationDetail[$idMpProductAttribute]['active'] = $valCombination['active'];
                $combinationDetail[$idMpProductAttribute]['mp_minimal_quantity'] = $valCombination['mp_minimal_quantity'];
                $combinationDetail[$idMpProductAttribute]['images'] = $this->getCombinationImages($idMpProductAttribute, $mpIdProduct);
            }
        }

        return array_values($combinationDetail);
    }

    public function getCombinationImages($idMpProductAttribute, $mpIdProduct)
    {
        $mpCombImages = array();
        $mpProduct = new WkMpSellerProduct($mpIdProduct);
        $images = WkMpProductAttributeImage::getAttributeImages($idMpProductAttribute);
        if ($images) {
            if ($mpProduct->id_ps_product && $mpProduct->active) {
                $product = new Product($mpProduct->id_ps_product, false, $this->context->language->id);
                foreach ($images as $image) {
                    $mpCombImages[] = $this->context->link->getImageLink($product->link_rewrite, $image['id_image']);
                }
            } else {
                $shopURL = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
                foreach ($images as $image) {
                    $mpImage = new WkMpSellerProductImage($image['id_image']);
                    $mpCombImages[] = $shopURL.'modules/marketplace/views/img/product_img/'.$mpImage->seller_product_image_name;
                }
            }
        }

        return $mpCombImages;
    }

    /**
     * Get all combinations of marketplace seller product according to seller product id.
     * copied from Mp 'WkMpProductAttribute' class
     * with changes
     * @param int $idShop      by default shop id will be 1
     * @param int $idMpProduct seller product id
     * @param int $idLang      context language id
     *
     * @return array
     */
    public function getMpProductCombinations($idMpProduct, $idLang, $idShop = 1)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        $combinations = Db::getInstance()->executeS('SELECT
            pac.`id_mp_product_attribute`,
            pa.*,
            product_attribute_shop.*,
            ag.`id_attribute_group`,
            ag.`is_color_group`,
            agl.`name` AS group_name,
            pac.`id_ps_attribute` AS id_ps_attribute,
            al.`name` AS attribute_name FROM `'._DB_PREFIX_.'wk_mp_product_attribute_combination` pac
                LEFT JOIN `'._DB_PREFIX_.'wk_mp_product_attribute` pa ON pa.`id_mp_product_attribute` = pac.`id_mp_product_attribute`
                LEFT JOIN `'._DB_PREFIX_.'wk_mp_product_attribute_shop` product_attribute_shop ON (product_attribute_shop.id_mp_product_attribute = pa.id_mp_product_attribute AND product_attribute_shop.id_shop = '.(int) $idShop.')
                LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_ps_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int) $idLang.')
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int) $idLang.')
                WHERE pac.`id_mp_product_attribute` IN (SELECT id_mp_product_attribute FROM '._DB_PREFIX_.'wk_mp_product_attribute WHERE `id_mp_product` = '.(int) $idMpProduct.')
                GROUP BY pa.`id_mp_product_attribute`, ag.`id_attribute_group`');

        if ($combinations) {
            return $combinations;
        }

        return false;
    }

    /**
     * Assign PrestaShop Product to Seller
     *
     * @api /assignproduct
     * @method GET
     * @return json
     */
    public function assignProduct()
    {
        $idProduct = Tools::getValue('id_product');
        $objMpProduct = new WkMpSellerProduct();
        $idMpProduct = $objMpProduct->assignProductToSeller($idProduct, $this->context->customer->id);
        if ($idMpProduct) {
            $this->output['success'] = true;
            $this->output['message'] = 'Product assigned successfully.';
            $this->output['id_mp_product'] = $idMpProduct;
            return $this->output;
        } else {
            $this->output['success'] = false;
            $this->output['message'] = 'Product already assigned or some issue.';
            return $this->output;
        }
    }

    /**
     * Delete Marketplace Product
     *
     * @param  int $idMpProduct
     * @api /deleteproduct
     * @method DELETE
     * @return json
     */
    public function deleteProduct($fields)
    {
        if ($fields && is_array($fields) && isset($fields['id_mp_product'])) {
            $idMpProduct = $fields['id_mp_product'];
            $objMpProduct = new WkMpSellerProduct($idMpProduct);
            if (!Validate::isLoadedObject($objMpProduct)) {
                $this->output['success'] = false;
                $this->output['message'] = 'Invalid product';
                return $this->output;
            }

            if ($objMpProduct->id_seller != $this->idSeller) {
                $this->output['success'] = false;
                $this->output['message'] = 'Product not found.';
                return $this->output;
            }

            if ($objMpProduct->delete()) {
                $this->output['success'] = true;
                $this->output['message'] = 'Product deleted successfully';
                return $this->output;
            } else {
                $this->output['success'] = false;
                $this->output['message'] = 'Some error while delete';
                return $this->output;
            }
        }
    }

    /**
     * Add/Update Seller Product
     *
     * @api /saveproduct
     * @method POST
     * @todo save product combination and feature
     * @param array $fields input
     * @Tested  Add/Update OK
     */
    public function saveProduct($fields)
    {
        if ($fields && is_array($fields)) {
            foreach ($fields as $key => $field) {
                $_POST[$key] = $field;
            }

            $images = Tools::getValue('images');

            $editProduct = false;
            if ($idMpProduct = Tools::getValue('id_mp_product')) {
                $objSellerProduct = new WkMpSellerProduct($idMpProduct);
                if (!Validate::isLoadedObject($objSellerProduct)) {
                    $this->output['success'] = false;
                    $this->output['message'] = 'Invalid product.';
                    return $this->output;
                }

                // Seller can not update other seller product
                if ($objSellerProduct->id_seller != $this->idSeller) {
                    $this->output['success'] = false;
                    $this->output['message'] = 'Product not found.';
                    return $this->output;
                }

                if ($images && !empty($images) && isset($images['image']) && !empty($images['image'])) {
                    $deleteMpImages = WkMpSellerProduct::deleteSellerProductImage($idMpProduct);
                    if ($deleteMpImages) {
                        $product = new Product($objSellerProduct->id_ps_product);
                        $product->deleteImages();
                    }
                }
                $editProduct = true;
            } else {
                $objSellerProduct = new WkMpSellerProduct();
            }

            // Get data from add product form
            $id_customer = $this->context->customer->id;
            $quantity = Tools::getValue('quantity');
            $minimalQuantity = Tools::getValue('minimal_quantity');
            $condition = Tools::getValue('condition');
            $price = Tools::getValue('price');
            $wholesalePrice = Tools::getValue('wholesale_price');
            $unitPrice = Tools::getValue('unit_price');
            $unity = Tools::getValue('unity');
            $idTaxRulesGroup = Tools::getValue('id_tax_rules_group');

            // height, width, depth and weight
            $width = Tools::getValue('width');
            $width = empty($width) ? '0' : str_replace(',', '.', $width);
            $height = Tools::getValue('height');
            $height = empty($height) ? '0' : str_replace(',', '.', $height);
            $depth = Tools::getValue('depth');
            $depth = empty($depth) ? '0' : str_replace(',', '.', $depth);
            $weight = Tools::getValue('weight');
            $weight = empty($weight) ? '0' : str_replace(',', '.', $weight);

            // Admin Shipping
            $psIDCarrierReference = Tools::getValue('ps_id_carrier_reference');
            if ($psIDCarrierReference) {
                $psIDCarrierReference = serialize($psIDCarrierReference);
            } else {
                $psIDCarrierReference = 0;  // No Shipping Selected By Admin
            }

            $reference = trim(Tools::getValue('reference'));
            $ean13JanBarcode = trim(Tools::getValue('ean13'));
            $upcBarcode = trim(Tools::getValue('upc'));
            $defaultCategory = Tools::getValue('default_category');
            $categories = Tools::getValue('product_category');
            $categories = explode(',', $categories);
            $sellerDefaultLanguage = Tools::getValue('default_lang');
            $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);
            $metaTitle = Tools::getValue('meta_title');
            $metaDescription = Tools::getValue('meta_description');
            $linkRewrite = Tools::getValue('link_rewrite');
            $productName = Tools::getValue('product_name');
            $shortDescription = Tools::getValue('short_description');
            $description = Tools::getValue('description');
            $availableNow = Tools::getValue('available_now');
            $availableLater = Tools::getValue('available_later');
            $outOfStock = Tools::getValue('out_of_stock');
            $availableDate = Tools::getValue('available_date');
            $productFeature = Tools::getValue('product_features');
            $productCombination = Tools::getValue('product_combinations');
            $status = Tools::getValue('active');

            if (Configuration::get('WK_MP_SELLER_PRODUCT_VISIBILITY')) {
                $availableForOrder = trim(Tools::getValue('available_for_order'));
                $showPrice = $availableForOrder ? 1 : trim(Tools::getValue('show_price'));
                $onlineOnly = trim(Tools::getValue('online_only'));
                $visibility = trim(Tools::getValue('visibility'));
            }

            if (!$productName) {
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $sellerLang = Language::getLanguage((int) $defaultLang);
                    $this->output['success'] = false;
                    $this->output['message'] = sprintf('Product name is required in %s', $sellerLang['name']);
                    return $this->output;
                } else {
                    $this->output['success'] = false;
                    $this->output['message'] = 'Product name is required';
                    return $this->output;
                }
            } else {
                $errors = WkMpSellerProduct::validateMpProductForm();
                if ($errors && is_array($errors)) {
                    $this->output['success'] = false;
                    $this->output['message'] = $errors;
                    return $this->output;
                }

                $mpSeller = WkMpSeller::getSellerDetailByCustomerId($id_customer);
                $idSeller = $mpSeller['id_seller'];

                if ($editProduct) {
                    Hook::exec('actionBeforeUpdateMPProduct', array('id_mp_product' => $idMpProduct));
                } else {
                    Hook::exec('actionBeforeAddMPProduct', array('id_seller' => $idSeller));
                }

                if (empty($this->errors)) {
                    $objSellerProduct->id_seller = $idSeller;

                    if ($editProduct) {
                        $objMpAttribute = new WkMpProductAttribute();
                        $hasAttribute = $objMpAttribute->getProductAttributes($idMpProduct);
                        if (!$hasAttribute) {
                            $objSellerProduct->quantity = $quantity;
                            $objSellerProduct->minimal_quantity = $minimalQuantity;
                        }
                    } else {
                        $objSellerProduct->quantity = $quantity;
                        $objSellerProduct->minimal_quantity = $minimalQuantity;
                    }

                    if (!$editProduct) {
                        $objSellerProduct->id_ps_product = 0;
                    }
                    $objSellerProduct->id_category = $defaultCategory;
                    $objSellerProduct->id_ps_shop = $this->context->shop->id;
                    $objSellerProduct->condition = $condition;

                    //Pricing
                    $objSellerProduct->price = $price;
                    $objSellerProduct->wholesale_price = $wholesalePrice;
                    $objSellerProduct->unit_price = $unitPrice; //(Total price divide by unit price)
                    $objSellerProduct->unity = $unity;
                    $objSellerProduct->id_tax_rules_group = $idTaxRulesGroup;

                    if (Configuration::get('WK_MP_SELLER_ADMIN_SHIPPING') || Module::isEnabled('mpshipping')) {
                        $objSellerProduct->width = $width;
                        $objSellerProduct->height = $height;
                        $objSellerProduct->depth = $depth;
                        $objSellerProduct->weight = $weight;
                        $objSellerProduct->ps_id_carrier_reference = $psIDCarrierReference;
                    }

                    if (Configuration::get('WK_MP_SELLER_PRODUCT_AVAILABILITY')) {
                        $objSellerProduct->out_of_stock = $outOfStock;
                        $objSellerProduct->available_date = $availableDate;
                    }

                    if (Configuration::get('WK_MP_SELLER_PRODUCT_REFERENCE')) {
                        $objSellerProduct->reference = $reference;
                    }
                    if (Configuration::get('WK_MP_SELLER_PRODUCT_EAN')) {
                        $objSellerProduct->ean13 = $ean13JanBarcode;
                    }
                    if (Configuration::get('WK_MP_SELLER_PRODUCT_UPC')) {
                        $objSellerProduct->upc = $upcBarcode;
                    }

                    if (!$editProduct) {
                        //control product approval setting
                        if (Configuration::get('WK_MP_PRODUCT_ADMIN_APPROVE')) {
                            $objSellerProduct->active = 0;
                            $objSellerProduct->status_before_deactivate = 0;
                        } else {
                            $objSellerProduct->active = 1;
                            $objSellerProduct->status_before_deactivate = 1;
                            $objSellerProduct->admin_approved = 1;
                        }
                    } else {
                        //edit product
                        if ($objSellerProduct->admin_approved && Configuration::get('WK_MP_SELLER_PRODUCTS_SETTINGS')) {
                            $objSellerProduct->active = $status;
                            $objSellerProduct->status_before_deactivate = $status;
                        }
                    }

                    foreach (Language::getLanguages(false) as $language) {
                        $objSellerProduct->product_name[$language['id_lang']] = $productName;
                        $objSellerProduct->short_description[$language['id_lang']] = $shortDescription;
                        $objSellerProduct->description[$language['id_lang']] = $description;

                        //Product SEO
                        if (Configuration::get('WK_MP_SELLER_PRODUCT_SEO')) {
                            $objSellerProduct->meta_title[$language['id_lang']] = $metaTitle;
                            $objSellerProduct->meta_description[$language['id_lang']] = $metaDescription;

                            // Friendly URL
                            if ($linkRewrite) {
                                $objSellerProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite($linkRewrite);
                            } else {
                                $objSellerProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite($productName);
                            }
                        } else {
                            $objSellerProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite($productName);
                        }

                        //For Avalailiblity Preferences
                        if (Configuration::get('WK_MP_SELLER_PRODUCT_AVAILABILITY')) {
                            $objSellerProduct->available_now[$language['id_lang']] = $availableNow;
                            $objSellerProduct->available_later[$language['id_lang']] = $availableLater;
                        }
                    }

                    if (Configuration::get('WK_MP_SELLER_PRODUCT_VISIBILITY')) {
                        $objSellerProduct->available_for_order = $availableForOrder;
                        $objSellerProduct->show_price = $showPrice;
                        $objSellerProduct->online_only = $onlineOnly;
                        $objSellerProduct->visibility = $visibility;
                    }

                    $objSellerProduct->save();
                    $mpIdProduct = $objSellerProduct->id;
                    if ($mpIdProduct) {
                        //for ps product
                        if (!$status) {
                            //While deactive
                            //Update id_image as mp_image_id when product is going to deactivate
                            WkMpProductAttributeImage::setCombinationImagesAsMp($mpIdProduct);

                            if ($objSellerProduct->id_ps_product) {
                                $psProductId = $objSellerProduct->id_ps_product;
                                $product = new Product($psProductId);
                                $product->active = 0;
                                $product->save();
                            }
                        } else {
                            //While activate
                            $objSellerProduct->updateSellerProductToPs($mpIdProduct, 1);
                        }

                        //Add into category table
                        $objMpCategory = new WkMpSellerProductCategory();
                        $objMpCategory->id_seller_product = $mpIdProduct;

                        if ($editProduct) {
                            // for Updating new categories first delete previous category
                            $objMpCategory->deleteProductCategory($mpIdProduct);
                        }

                        // upload product image
                        if ($images && !empty($images) && isset($images['image']) && !empty($images['image'])) {
                            $this->uploadProductImage($images, $mpIdProduct);
                        }

                        //set if more than one category selected
                        if ($categories) {
                            foreach ($categories as $pCategory) {
                                $objMpCategory->id_category = $pCategory;
                                if ($pCategory == $defaultCategory) {
                                    $objMpCategory->is_default = 1;
                                } else {
                                    $objMpCategory->is_default = 0;
                                }

                                $objMpCategory->add();
                            }
                        }

                        if (Configuration::get('WK_MP_PRODUCT_FEATURE')) {
                            if ($productFeature) {
                                if ($editProduct) {
                                    WkMpProductFeature::deleteProductFeature($mpIdProduct);
                                }
                                $this->setProductFeature($productFeature, $defaultLang);
                                WkMpProductFeature::processProductFeature($mpIdProduct, $defaultLang);
                            }
                        }

                        if ($editProduct) {
                            if ($objSellerProduct->active) {
                                //if product is active then check admin configure value
                                //that product after update need to approved by admin or not
                                WkMpSellerProduct::deactivateProductAfterUpdate($mpIdProduct);
                                if (!Configuration::get('WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE')) {
                                    // Update also in prestashop if product is active
                                    $objSellerProduct->updateSellerProductToPs($mpIdProduct, 1);
                                }
                            }
                        } else {
                            if (!Configuration::get('WK_MP_PRODUCT_ADMIN_APPROVE')) {
                                $idProduct = $objSellerProduct->addSellerProductToPs($mpIdProduct, 1);
                                if ($idProduct) {
                                    $objSellerProduct->id_ps_product = $idProduct;
                                    $objSellerProduct->save();
                                }
                                WkMpSellerProduct::sendMail($mpIdProduct, 1, 1);
                            }
                        }

                        if (Configuration::get('WK_MP_MAIL_ADMIN_PRODUCT_ADD')) {
                            $sellerDetail = WkMpSeller::getSeller($idSeller, Configuration::get('PS_LANG_DEFAULT'));
                            if ($sellerDetail) {
                                $sellerName = $sellerDetail['seller_firstname'].' '.$sellerDetail['seller_lastname'];
                                $shopName = $sellerDetail['shop_name'];
                                $objSellerProduct->mailToAdminOnProductAdd(
                                    $productName,
                                    $sellerName,
                                    $sellerDetail['phone'],
                                    $shopName,
                                    $sellerDetail['business_email']
                                );
                            }
                        }
                    }

                    if ($editProduct) {
                        Hook::exec('actionAfterUpdateMPProduct', array(
                            'id_mp_product' => $mpIdProduct,
                            'id_mp_product_attribute' => 0
                        ));
                    } else {
                        Hook::exec('actionAfterAddMPProduct', array('id_mp_product' => $mpIdProduct));
                    }

                    if ($productCombination && Configuration::get('WK_MP_SELLER_PRODUCT_COMBINATION')) {
                        //Delete combinations
                        $objSellerProduct->deleteCombinationAssociations($mpIdProduct);

                        if ($objSellerProduct->id_ps_product) {
                            $objProduct = new Product($objSellerProduct->id_ps_product);
                            $objProduct->deleteProductAttributes();
                        }

                        $saveComb = $this->saveProductCombination($mpIdProduct, $productCombination);
                        if (!$saveComb) {
                            $this->output['message'] = 'Product added successfully but error while adding combinations';
                        }
                    }
                    $this->output['success'] = true;
                    if ($editProduct) {
                        $this->output['message'] = 'Product updated successfully.';
                    } else {
                        $this->output['message'] = 'Product added successfully.';
                    }
                    $this->output['id_mp_product'] = $mpIdProduct;
                    return $this->output;
                }
            }
        }
    }

    /**
     * Save Combination VALUES
     * @todo Validate fields, image selection validation for this product
     * @param  int $mpIdProduct
     * @param  array $productCombination
     */
    public function saveProductCombination($mpIdProduct, $productCombination)
    {
        $productCombination = $productCombination['product_combination'];
        if (isset($productCombination[0])) {
            foreach ($productCombination as $combination) {
                $idMpAttribute = $this->saveProductCombinationPart($mpIdProduct, $combination);
            }
        } else {
            $idMpAttribute = $this->saveProductCombinationPart($mpIdProduct, $productCombination);
        }

        if (!$idMpAttribute) {
            return false;
        }

        return true;
    }

    public function saveProductCombinationPart($mpIdProduct, $combination)
    {
        $productAttributeList = $combination['product_option_value'];

        if (!is_array($productAttributeList)) {
            $productAttributeList = array($productAttributeList);
        }

        if (isset($combination['id_images']) && is_array($combination['id_images'])) {
            $images = array();
            foreach ($combination['id_images'] as $idImage) {
                $images[] = $idImage;
            }
        }
        $idMpProductAttribute = false; //Provide If combination update
        $idMpAttribute = WkMpProductAttribute::createOrUpdateMpProductCombination(
            $mpIdProduct,
            $idMpProductAttribute,
            $productAttributeList,
            $combination['reference'],
            $combination['ean13'],
            $combination['upc'],
            $combination['isbn'],
            $combination['price'],
            $combination['wholesale_price'],
            $combination['unit_price_impact'],
            $combination['quantity'],
            $combination['weight'],
            $combination['minimal_quantity'],
            $combination['available_date'],
            $images
        );
        return $idMpAttribute;
    }

    /**
     * Prepare POST data for processProductFeature function
     * @param array $productFeature product feature
     * @param int $defaultLang Seller default language
     */
    public function setProductFeature($productFeature, $defaultLang)
    {
        $productFeature = $productFeature['product_feature'];
        $_POST['wk_feature_row'] = count($productFeature);
        foreach ($productFeature as $k => $feature) {
            $k = $k+1;
            $_POST['wk_mp_feature_'.$k] = $feature['id'];
            if (isset($feature['custom_value'])) {
                $_POST['wk_mp_feature_custom_'.$defaultLang.'_'.$k] = $feature['custom_value'];
            } else {
                $_POST['wk_mp_feature_val_'.$k] = $feature['id_feature_value'];
            }
        }
    }

    public function uploadProductImage($images, $mpIdProduct)
    {
        if ($images && is_array($images)) {
            if (isset($images['image']['url'])) {
                $this->uploadProductImagePart($images['image'], $mpIdProduct);
            } else {
                foreach ($images['image'] as $image) {
                    $this->uploadProductImagePart($image, $mpIdProduct);
                }
            }
        }
    }

    public function uploadProductImagePart($image, $mpIdProduct)
    {
        $imageDir = _PS_MODULE_DIR_.'marketplace/views/img/product_img/';
        $ext = pathinfo($image['url'], PATHINFO_EXTENSION);
        $imgName = Tools::passwdGen(6).'.'.$ext;
        $uploadPathWithName = $imageDir.$imgName;
        $objMpImage = new WkMpSellerProductImage();
        $objMpImage->seller_product_id = $mpIdProduct;
        $objMpImage->seller_product_image_name = $imgName;
        if (isset($image['position'])) {
            $objMpImage->position = $image['position'];
        }
        if (isset($image['cover'])) {
            $objMpImage->cover = $image['cover'];
        }
        $objMpImage->save();
        $this->downloadProductImage($image['url'], $uploadPathWithName);
    }

    /**
     * Download image from a URL in the specific directory
     *
     * @param  url $imageLink  Download path URL
     * @param  url $uploadPathWithName Upload path with image name
     */
    public function downloadProductImage($imageLink, $uploadPathWithName)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $imageLink);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $file = fopen($uploadPathWithName, 'w') or die('Can not open '.$uploadPathWithName);
        fwrite($file, $response);
        fclose($file);
    }

    public function getSellerProductDetails($idSeller, $limit)
    {
        $status = true; // Getting only active products
        $shopURL = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        $filters = $this->getFilters($limit);
        if (isset($filters['limit'])) {
            $products = WkMpSellerProduct::getSellerProduct(
                $idSeller,
                $status,
                false,
                false,
                false,
                $filters['limit'][0],
                $filters['limit'][1]
            );
        } else {
            $products = WkMpSellerProduct::getSellerProduct($idSeller, $status);
        }
        if ($products) {
            foreach ($products as &$product) {
                $images = WkMpSellerProduct::getSellerProductImages($product['id_mp_product']);
                if ($images && is_array($images)) {
                    foreach ($images as $k => $image) {
                        $product['images'][$k]['url'] = $shopURL.'modules/marketplace/views/img/product_img/'
                        .$image['seller_product_image_name'];
                        if (isset($image['cover']) && $image['cover']) {
                        	$product['images'][$k]['cover'] = $image['cover'];
                        } else {
                        	$product['images'][$k]['cover'] = 0;
                        }
                        $product['images'][$k]['id_mp_product_image'] = $image['id_mp_product_image'];
                        $product['images'][$k]['position'] = $image['position'];
                    }
                }
            }
        }
        return $products;
    }

    public function getFilters($limit)
    {
        $return = array();
        if ($limit) {
            $limit = explode(',', $limit);
            if (isset($limit[0]) && Validate::isUnsignedInt($limit[0])) {
                $return['limit'][0] = (int) $limit[0];
            } else {
                $return['limit'][0] = 0;
            }

            if (isset($limit[1]) && Validate::isUnsignedInt($limit[1])) {
                $return['limit'][1] = (int) $limit[1];
            } else {
                // If only first value provided then make it like 0,6 to get first 6 element
                if ($return['limit'][0]) {
                    $return['limit'][1] = $return['limit'][0];
                    $return['limit'][0] = 0;
                } else {
                    $return['limit'][1] = 10000000;
                }
            }
        }

        return $return;
    }
}
