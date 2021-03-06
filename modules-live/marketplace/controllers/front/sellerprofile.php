<?php
/**
 * 2010-2020 Webkul.
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
 *  @copyright 2010-2020 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

class MarketplaceSellerProfileModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {

        parent::initContent();
        $idCustomer = $this->context->customer->id;
        $shopLinkRewrite = Tools::getValue('mp_shop_name');
        if ($shopLinkRewrite) {
            $this->context->smarty->assign('shop_link_rewrite', $shopLinkRewrite);
        }

        // Review process if allowed for customer
        if (Configuration::get('WK_MP_REVIEW_SETTINGS')) {
            if (Tools::isSubmit('submit_feedback')) {
                $this->submitReviewProcess();
            } elseif (Tools::getValue('delete_review') == 1) {
                $this->deleteReviewProcess();
            }
        }

        $mpSeller = WkMpSeller::getSellerByLinkRewrite($shopLinkRewrite, $this->context->language->id);
        if ($mpSeller) {
            $idSeller = $mpSeller['id_seller'];
            if ($mpSeller['active']) {
                //Display price tax Incl or excl and price hide/show according to customer group settings
                $displayPriceTaxIncl = 1;
                $showPriceByCustomerGroup = 1;
                if ($groupAccess = Group::getCurrent()) {
                    if (isset($groupAccess->price_display_method) && $groupAccess->price_display_method) {
                        $displayPriceTaxIncl = 0; //Display tax incl price
                    }
                    if (empty($groupAccess->show_prices)) {
                        $showPriceByCustomerGroup = 0; //Don't display product price
                    }
                }

                $mpProduct = WkMpSellerProduct::getSellerProductWithPs($idSeller, true);
                if ($mpProduct) {
                    foreach ($mpProduct as &$products) {
                        $product = new Product($products['id_ps_product'], true, $this->context->language->id);

                        if ($displayPriceTaxIncl) {
                            $products['retail_price'] = Tools::displayPrice($product->getPriceWithoutReduct(false, $product->getWsDefaultCombination()));
                            $products['price'] = Tools::displayPrice($product->getPrice(true));
                        } else {
                            $products['retail_price'] = Tools::displayPrice($product->getPriceWithoutReduct(true, $product->getWsDefaultCombination()));
                            $products['price'] = Tools::displayPrice($product->getPrice(false));
                        }
                    }

                    $this->context->smarty->assign('mp_shop_product', $mpProduct);
                }

                $objReview = new WkMpSellerReview();
                if ($reviews = $objReview->getReviewsByConfiguration($idSeller)) {
                    Media::addJsDef(array('avg_rating' => $reviews['avg_rating']));
                    $this->context->smarty->assign(array(
                        'avg_rating' => $reviews['avg_rating'],
                        'reviews' => $reviews['reviews'],
                    ));
                }

                // Set left Image column
                $this->setLeftImageBlock($mpSeller['profile_image']);

                //Check if seller banner exist
                $sellerBannerPath = WkMpSeller::getSellerBannerLink($mpSeller);
                if ($sellerBannerPath) {
                    $this->context->smarty->assign('seller_banner_path', $sellerBannerPath);
                }

                $loginShop = '';
                // Get login user marketplace shop details if exist for seller can't review yourself
                if ($idCustomer) {
                    $loginCustomer = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
                    if ($currenctCustomerReview = WkMpSellerReview::getReviewByCustomerIdAndSellerId($idCustomer, $idSeller)) {
                        Media::addJsDef(array('currenct_cust_review' => $currenctCustomerReview));
                        $this->context->smarty->assign('currenct_cust_review', $currenctCustomerReview);
                    } elseif ($loginCustomer) {
                        $loginShop = $loginCustomer['link_rewrite'];
                    }
                }

                $this->context->smarty->assign('login_mp_shop_name', $loginShop);

                if ($idCustomer == $mpSeller['seller_customer_id']) {
                    $this->context->smarty->assign('current_seller_login', 1);
                }

                if ($mpSeller['id_country']) {
                    $mpSeller['country'] = Country::getNameById($this->context->language->id, $mpSeller['id_country']);
                }
                if ($mpSeller['id_state']) {
                    $mpSeller['state'] = State::getNameById($mpSeller['id_state']);
                }

                if (Configuration::get('WK_MP_CONTACT_SELLER_SETTINGS')) {
                    //If admin allowed only registered customers to contact with seller in configuration
                    if ($this->context->customer->id) {
                        $this->context->smarty->assign('contactSellerAllowed', 1);
                    }
                } else {
                    //Anyone can contact to seller
                    $this->context->smarty->assign('contactSellerAllowed', 1);
                }
                $objReview = new WkMpSellerReview();
                $reviews = $objReview->getReviewsByConfiguration($mpSeller['id_seller']);
                $average_ratings                = $reviews['avg_rating'];
                $total_review                   = ( $reviews ) ? count( $reviews['reviews'] ) : 0;
                $store_locator                  = MarketplaceStoreLocator::getSellerStore($mpSeller['id_seller']);
                $mpSeller['store_locator']      = ($store_locator) ? $store_locator[0] : '';
                $mpSeller['city_name']          = ($store_locator) ? $store_locator[0]['city_name'] : '';
                $mpSeller['average_ratings']    = ($average_ratings) ? $average_ratings : 0;
                $mpSeller['left_ratings']       = 5 - $average_ratings;
                $mpSeller['total_review']       = $total_review;

                foreach (Language::getLanguages(true) as $language) {
                    if( $mpSeller['default_lang'] == $language['id_lang'] ){
                        $mpSeller['default_lang_name'] = $language['name'];
                        break;
                    }
                }


                $badges = $this->getSellerBadges($idSeller);
                // PAUL : fix double default label
                $badges_html = '';
                $hadDfLabel = false;
                $dfLabelId = 1;
                if( count($badges) ){
                    foreach( $badges as $badge ){
                        if($hadDfLabel == true && $badge['badge_id'] == $dfLabelId){continue;}
                        if($badge['badge_id'] == $dfLabelId){
                            $hadDfLabel = true;
                        }
                        $badges_html .= '
                        <span>
                            <a href="'.$badge['badge_link'].'" target="_blank">
                                <img class="img_badge_seller" title="'.$badge['badge_name'].'" src="/modules/mpbadgesystem/views/img/badge_img/'.$badge['badge_id'].'.jpg">
                            </a>
                        </span>
                        ';
                    }
                }
                // PAUL
                $mpSeller['badges_html'] = $badges_html;

                $extra_field_value_obj = new MarketplaceExtrafieldValue();
                $extra_fields = $extra_field_value_obj->findExtrafieldValues($idSeller);




                if( count($extra_fields) ){
                    foreach( $extra_fields as $extra_field ){
                        switch ($extra_field['extrafield_id']) {
                            case 1:
                                $mpSeller['profession'] = $extra_field['field_value'];
                                break;
                            case 2:
                                $mpSeller['quisuisje'] = $extra_field['field_value'];
                                break;
                            case 3:
                                $mpSeller['mapassion'] = $extra_field['field_value'];
                                break;
                            case 4:
                                $mpSeller['unproverbe'] = $extra_field['field_value'];
                                $mpSeller['unproverbe'] = str_replace('\\','',$mpSeller['unproverbe']);
                                break;
                            case 5:
                                $mpSeller['labels'] = $extra_field['field_value'];
                                break;
                            case 6:
                                $mpSeller['siret'] = $extra_field['field_value'];
                                break;
                            case 10:
                                $mpSeller['spoken_langs'] = $extra_field['field_value'];
                                $mpSeller['spoken_langs'] = explode(',',$mpSeller['spoken_langs']);
                                break;

                            default:
                                # code...
                                break;
                        }
                    }
                }



                $spoken_langs = [
                    '108'=>'Fran??ais',
                    '109'=>'Anglais',
                    '110'=>'Allemand',
                    '111'=>'Espagnol',
                    '112'=>'Portuguais',
                    '113'=>'Italien',
                    '114'=>'Chinois',
                    '115'=>'Arabe',
                    '116'=>'Autre'
                ];

                foreach( $spoken_langs as $key => $val ){
                    if( !in_array( $key , $mpSeller['spoken_langs']) ){
                        unset( $spoken_langs[$key] );
                    }
                }

                $spoken_langs = implode(', ',$spoken_langs);
                $mpSeller['spoken_langs_html'] = $spoken_langs;

                $this->context->smarty->assign(
                    array(
                        'mp_seller_info' => $mpSeller,
                        'pr_mp_seller_info' => $mpSeller,
                        'name_shop' => $mpSeller['link_rewrite'],
                        'seller_id' => $idSeller,
                        'showPriceByCustomerGroup' => $showPriceByCustomerGroup,
                        'id_customer' => $this->context->customer->id,
                        'customer_email' => $this->context->customer->email,
                        'sellerprofile' => 1,
                        'link' => $this->context->link,
                        'logged' => $this->context->customer->isLogged(),
                        'timestamp' => WkMpHelper::getTimestamp(),
                        'myAccount' => 'index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('marketplace', 'sellerprofile', array('mp_shop_name' => $shopLinkRewrite))),
                    )
                );
                // Assign the seller details view vars
                WkMpSeller::checkSellerAccessPermission($mpSeller['seller_details_access']);

                $this->defineJSVars();
                $this->setTemplate('module:marketplace/views/templates/front/seller/sellerprofile.tpl');
            } else {
                Tools::redirect(__PS_BASE_URI__.'pagenotfound');// seller is deactivated by admin
            }
        } else {
            Tools::redirect(__PS_BASE_URI__.'pagenotfound');
        }
    }

    public function deleteReviewProcess()
    {
        $idCustomer = $this->context->customer->id;

        $idReview = Tools::getValue('review_id');
        if ($idReview && $idCustomer) {
            if (WkMpSellerReview::getReviewByIdAndCustomerId($idCustomer, $idReview)) {
                $objReview = new WkMpSellerReview($idReview);
                if ($objReview->delete()) {
                    $this->context->smarty->assign('review_deleted', 1);
                }
            }
        }
    }

    public function submitReviewProcess()
    {
        $idCustomer = $this->context->customer->id;
        $idSeller = Tools::getValue('seller_id');

        $objSeller = new WkMpSeller($idSeller);
        if ($objSeller->seller_customer_id != $idCustomer) { //Seller is not allowed to review himself
            $feedback = Tools::getValue('feedback');
            $rating = Tools::getValue('rating_image');
            $customer = new Customer($idCustomer);

            $customerReview = WkMpSellerReview::getCustomerReview($idCustomer);
            if ($customerReview) { //If customer has already write review then he can only edit that review
                $objReview = new WkMpSellerReview($customerReview['id_review']);
            } else {
                $objReview = new WkMpSellerReview();
            }
            //Save data in table
            $objReview->id_seller = $idSeller;
            $objReview->id_customer = $idCustomer;
            $objReview->customer_email = $customer->email;
            $objReview->rating = $rating;
            $objReview->review = $feedback;
            if (Configuration::get('WK_MP_REVIEWS_ADMIN_APPROVE')) {
                $objReview->active = 0;
            } else {
                $objReview->active = 1;
            }
            if ($objReview->save()) {
                $param = array('mp_shop_name' => Tools::getValue('mp_shop_name'));
                if (Configuration::get('WK_MP_REVIEWS_ADMIN_APPROVE')) {
                    $param['review_submitted'] = 1;
                } else {
                    $param['review_submit_default'] = 1;
                }
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerprofile', $param));
            }
        } else {
            $this->errors[] = $this->module->l('You can not write review on your profile.', 'sellerprofile');
        }
    }

    public function setLeftImageBlock($mpSellerProfileImage)
    {
        // if ($mpSellerProfileImage && file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/seller_img/'.$mpSellerProfileImage)) {
        // $this->context->smarty->assign('seller_img_path', _MODULE_DIR_.$this->module->name.'/views/img/seller_img/'.$mpSellerProfileImage);
        // $this->context->smarty->assign('seller_img_exist', 1);
        // } else {
        // $this->context->smarty->assign('seller_img_path', _MODULE_DIR_.$this->module->name.'/views/img/seller_img/defaultimage.jpg');
        // }
        $this->context->smarty->assign('seller_img_path', $mpSellerProfileImage );
    }

    public function defineJSVars()
    {
        $jsVars = array(
            'logged' => $this->context->customer->isLogged(),
            'moduledir' => _MODULE_DIR_,
            'mp_image_dir' => _MODULE_DIR_.'marketplace/views/img/',
            'rating_start_path' => _MODULE_DIR_.$this->module->name.'/views/img/',
            'contact_seller_ajax_link' => $this->context->link->getModuleLink('marketplace', 'contactsellerprocess'),
            'rate_req' => $this->module->l('Rating is required.', 'sellerprofile'),
            'not_logged_msg' => $this->module->l('Please login to write a review.', 'sellerprofile'),
            'review_yourself_msg' => $this->module->l('You can not write review to yourself.', 'sellerprofile'),
            'review_already_msg' => $this->module->l('You have already written a review for this seller.', 'sellerprofile'),
            'confirm_msg' => $this->module->l('Are you sure?', 'sellerprofile'),
            'email_req' => $this->module->l('Email is required field.', 'sellerprofile'),
            'invalid_email' => $this->module->l('Email is not valid.', 'sellerprofile'),
            'subject_req' => $this->module->l('Subject is required field.', 'sellerprofile'),
            'description_req' => $this->module->l('Description is required field.', 'sellerprofile'),
            'some_error' => $this->module->l('Some error occured...', 'sellerprofile'),
        );

        Media::addJsDef($jsVars);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'sellerprofile'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Profile', 'sellerprofile'),
            'url' => ''
        );
        return $breadcrumb;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet(
            'mp_store_profile-css',
            'modules/'.$this->module->name.'/views/css/mp_store_profile.css'
        );

        $this->registerStylesheet(
            'mp_seller_rating-css',
            'modules/'.$this->module->name.'/views/css/mp_seller_rating.css'
        );

        $this->registerJavascript(
            'sellerprofile-js',
            'modules/'.$this->module->name.'/views/js/sellerprofile.js'
        );
        $this->registerJavascript(
            'imageedit-js',
            'modules/'.$this->module->name.'/views/js/imageedit.js'
        );
        $this->registerJavascript(
            'contactseller-js',
            'modules/'.$this->module->name.'/views/js/contactseller.js'
        );
        $this->registerJavascript(
            'mp_review_like-js',
            'modules/'.$this->module->name.'/views/js/mp_review_like.js'
        );

        // bxslider removed in PS V1.7
        $this->registerJavascript(
            'bxslider',
            'modules/'.$this->module->name.'/views/js/jquery.bxslider.js'
        );

        $this->registerJavascript(
            'mp-jquery-raty-min',
            'modules/'.$this->module->name.'/views/js/libs/jquery.raty.min.js'
        );

        // mp product slider
        $this->registerStylesheet(
            'ps_gray',
            'modules/'.$this->module->name.'/views/css/product_slider_pager/ps_gray.css'
        );
        $this->registerJavascript(
            'mp_product_slider-js',
            'modules/'.$this->module->name.'/views/js/mp_product_slider.js'
        );
    }

    public function getSellerBadges($mp_seller_id)
    {
        $badge_info = Db::getInstance()->executeS('SELECT msb.*, mb.badge_name,mb.badge_desc,mb.badge_link
            FROM `'._DB_PREFIX_.'mp_seller_badges` msb
            LEFT JOIN `'._DB_PREFIX_.'mp_badges` mb ON msb.badge_id = mb.id
            WHERE msb.mp_seller_id = '.(int) $mp_seller_id.' AND mb.active = 1');
        if (!empty($badge_info)) {
            return $badge_info;
        }

        return false;
    }
}
