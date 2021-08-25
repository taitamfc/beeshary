<?php
/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class SellerInfoDetail extends ObjectModel
{
    public $id;
    public $shop_name_unique;
    public $link_rewrite;
    public $seller_name;
    public $business_email;
    public $phone;
    public $fax;
    public $city;
    public $id_country;
    public $id_state;
    public $default_lang;
    public $facebook_id;
    public $twitter_id;
    public $active;
    public $shop_approved;
    public $seller_customer_id;
    public $date_add;
    public $date_upd;

    public $shop_name;
    public $address;
    public $about_shop;

    public static $definition = array(
        'table' => 'marketplace_seller_info',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'shop_name_unique' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'validate' => 'isLinkRewrite', 'required' => true),
            'seller_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'business_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
            'phone' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isPhoneNumber', 'size' => 32),
            'fax' => array('type' => self::TYPE_STRING),
            'city' => array('type' => self::TYPE_STRING),
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_state' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'default_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'facebook_id' => array('type' => self::TYPE_STRING),
            'twitter_id' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'featured' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'shop_approved' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'seller_customer_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),

            /* Lang fields */
            'shop_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true),
            'address' => array('type' => self::TYPE_STRING, 'lang' => true),
            'about_shop' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
        ),
    );

    public function toggleStatus()
    {
        return true;
    }

    public function delete()
    {
        if (!$this->mpSellerDelete($this->id) || !parent::delete()) {
            return false;
        }

        return true;
    }

    public static function findSellerDefaultLang($seller_id)
    {
        if ($seller_id) {
            return Db::getInstance()->getValue('SELECT `default_lang` FROM  `'._DB_PREFIX_.'marketplace_seller_info` WHERE `id` = '.(int) $seller_id);
        }

        return false;
    }

    public function getCustomerId($id_seller)
    {
        return Db::getInstance()->getValue('SELECT `seller_customer_id` FROM `'._DB_PREFIX_.'marketplace_seller_info` WHERE `id` = '.(int) $id_seller);
    }

    public function sellerLangInfo($seller_id)
    {
        $seller_langinfo = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'marketplace_seller_info_lang` WHERE `id` = '.(int) $seller_id);

        if ($seller_langinfo) {
            return $seller_langinfo;
        }

        return false;
    }

    /*public function getMarketPlaceSellerIdByCustomerId($id_customer)
    {
        use "getSellerDetailsByCustomerId" function in place of "current" function
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_info` WHERE seller_customer_id ='.$id_customer);
    }*/

    public function getSellerInfoBySellerIdCustomer($id_customer)
    {
        $seller_info = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_info` WHERE `seller_customer_id` ='.(int) $id_customer);
        
        if ($seller_info) {
            return $seller_info;
        } else {
            return false;
        }
    }

    public function getSellerDetailsWithLangByCustomerId($id_customer, $lang_id = false)
    {
        if (!$lang_id) {
            $lang_id = Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_info` mpsi
			LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info_lang` msil
			ON (mpsi.`id` = msil.`id`)
			WHERE mpsi.`seller_customer_id` = '.(int) $id_customer.' AND msil.`id_lang` = '.(int) $lang_id
        );
    }

    public function getSellerDetailsByCustomerId($id_customer)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_info`
            WHERE `seller_customer_id` = '.(int) $id_customer
        );
    }

    public function sellerDetailWithLang($seller_id, $lang_id = false)
    {
        if (!$lang_id) {
            $lang_id = Configuration::get('PS_LANG_DEFAULT');
        }

        $seller_info = Db::getInstance()->getRow('
			SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_info` mpsi 
			LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info_lang` msil
			ON (mpsi.`id` = msil.`id`)
			WHERE mpsi.`id` = '.(int) $seller_id.' AND msil.`id_lang` = '.(int) $lang_id);

        if ($seller_info) {
            return $seller_info;
        }

        return false;
    }

    public function sellerDetail($seller_id)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_info`
            WHERE `id` = '.(int) $seller_id
        );
    }

    public function getSellerDetailsByLinkRewrite($link_rewrite, $lang_id = false)
    {
        if (!$lang_id) {
            $lang_id = Configuration::get('PS_LANG_DEFAULT');
        }

        $seller_info = Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_info` mpsi 
            LEFT JOIN `'._DB_PREFIX_."marketplace_seller_info_lang` msil
            ON (mpsi.`id` = msil.`id`)
            WHERE mpsi.`link_rewrite` = '".pSQL($link_rewrite)."' AND msil.`id_lang` = ".(int) $lang_id
        );

        if ($seller_info) {
            return $seller_info;
        }

        return false;
    }

    public static function isShopNameExist($name, $id_seller = false)
    {
        $mp_id_seller = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_."marketplace_seller_info` WHERE link_rewrite ='".pSQL($name)."'");

        if ($id_seller) {
            if ($mp_id_seller) {
                if ($mp_id_seller == $id_seller) {
                    return false;
                }

                return true;
            }
        } else {
            if ($mp_id_seller) {
                return true;
            }
        }

        return false;
    }

    public function getPsCustomerWhoseNotSeller()
    {
        return Db::getInstance()->executeS(
            'SELECT cus.`id_customer`, cus.`email` FROM `'._DB_PREFIX_.'customer` cus
        	WHERE cus.`id_customer` NOT IN (SELECT `seller_customer_id` FROM `'._DB_PREFIX_.'marketplace_seller_info` msi)
			AND cus.active = 1 AND cus.deleted = 0'
        );
    }

    /*public function findIsallCustomerSeller()
    {
        use "getAllSellerInfo" in place in this function
        $customer_info = Db::getInstance()->executeS('
        	SELECT cus.`id_customer`,cus.`email`
			FROM `'._DB_PREFIX_.'customer` cus
			INNER JOIN `'._DB_PREFIX_.'marketplace_seller_info` msi ON ( cus.`id_customer` = msi.`seller_customer_id`) WHERE msi.`active` = 1');
        if (empty($customer_info)) {
            return false;
        }

        return $customer_info;
    }*/

    public function getAllSellerInfo()
    {
        $customer_info = Db::getInstance()->executeS(
            'SELECT cus.`id_customer`, cus.`email`, mpsi.* FROM `'._DB_PREFIX_.'customer` cus 
        	JOIN `'._DB_PREFIX_.'marketplace_seller_info` mpsi ON (mpsi.`seller_customer_id` = cus.`id_customer`) WHERE mpsi.`active` = 1'
        );
        if (empty($customer_info)) {
            return false;
        }

        return $customer_info;
    }

    /*public function findAllActiveSeller()
    {
        use "getAllSellerInfo" in place of this function
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_info` WHERE `active` = 1');
    }*/

    /*public static function isCustomerActiveSeller($id_customer)
    {
        return Db::getInstance()->getValue(
            'SELECT `id` FROM `'._DB_PREFIX_.'marketplace_seller_info`
			WHERE `seller_customer_id`='.(int) $id_customer.' AND `active` = 1'
        );
    }*/

    public function mpSellerDelete($id_seller)
    {
        Hook::exec('actionMpSellerDelete', array('id_seller' => $id_seller));
        // delete from mp customer
        $obj_mp_seller = new self();
        $id_customer = $obj_mp_seller->getCustomerId($id_seller);
        $active_customer = true;
        if ($id_customer) {
            $delete_payment = Db::getInstance()->delete('marketplace_customer_payment_detail', 'seller_customer_id = '.$id_customer);
            $delete_commission = Db::getInstance()->delete('marketplace_commision', 'seller_customer_id = '.$id_customer);

            if (!$delete_payment
                || !$delete_commission
                ) {
                $active_customer = false;
            }
        }

        // delete mp products
        $product_delete = true;
        $mp_products = SellerProductDetail::getMpSellerProductDetailsWithLang($id_seller);
        if ($mp_products) {
            foreach ($mp_products as $product) {
                $obj_mpproduct = new SellerProductDetail($product['id']);
                if (!$obj_mpproduct->delete()) {
                    $product_delete = false;
                }
            }
        }

        // deleting reviews
        $delete_review = Db::getInstance()->delete('marketplace_seller_reviews', 'id_seller = '.$id_seller);

        // delete seller image
        $this->deleteMpSellerImage($id_seller);

        // delete seller shop image
        $this->deleteMpShopImage($id_seller);

        if (!$active_customer
            || !$product_delete
            || !$delete_review) {
            return false;
        }

        return true;
    }

    /*public function getmarketPlaceSellerInfo($seller_id, $lang_id = false)
    {
        return $this->sellerDetail($seller_id, $lang_id);
    }

    public function sellerInfo($seller_id)
    {
        return $this->sellerDetail($seller_id);
    }*/

    public static function getSellerImageLink($id_mp_seller)
    {
        if (!$id_mp_seller) {
            return false;
        }

        if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_mp_seller.'.jpg')) {
            return _MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_mp_seller.'.jpg';
        } else {
            return _MODULE_DIR_.'marketplace/views/img/seller_img/defaultimage.jpg';
        }
    }

    public static function getShopImageLink($id_mp_seller)
    {
        if (!$id_mp_seller) {
            return false;
        }

        $obj_mpseller = new self($id_mp_seller);
        $shopimage = $id_mp_seller.'-'.$obj_mpseller->shop_name_unique.'.jpg';
        if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage)) {
            return _MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage;
        } else {
            return _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
        }
    }

    public function deleteMpSellerImage($id_mp_seller)
    {
        if (!$id_mp_seller) {
            return false;
        }

        if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_mp_seller.'.jpg')) {
            unlink(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_mp_seller.'.jpg');
        } else {
            return false;
        }

        return true;
    }

    public function deleteMpShopImage($id_mp_seller)
    {
        if (!$id_mp_seller) {
            return false;
        }

        $obj_mpseller = new self($id_mp_seller);
        $shopimage = $id_mp_seller.'-'.$obj_mpseller->shop_name_unique.'.jpg';
        if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage)) {
            unlink(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage);
        } else {
            return false;
        }

        return true;
    }

    public static function isSellerEmailExist($seller_email, $id_seller = false)
    {
        $seller_email = pSQL($seller_email);
        $mp_id_seller = Db::getInstance()->getValue(
            'SELECT `id` FROM `'._DB_PREFIX_."marketplace_seller_info`
			WHERE business_email ='$seller_email'"
        );

        if ($id_seller) {
            if ($mp_id_seller) {
                if ($mp_id_seller == $id_seller) {
                    return false;
                }

                return true;
            }
        } else {
            if ($mp_id_seller) {
                return true;
            }
        }

        return false;
    }

    public function findAllActiveSellerInfoByLimit($start_point = 0, $limit_point = 7, $like = false, $all = false, $like_word = 'a', $id_lang)
    {
        if ($like == false && $all == false) {
            $sql = 'SELECT msi.*, msil.*, msi.`seller_customer_id` AS id_customer
                FROM `'._DB_PREFIX_.'marketplace_seller_info` AS msi
                INNER JOIN `'._DB_PREFIX_.'marketplace_seller_info_lang` msil ON (msil.`id` = msi.`id` AND msil.`id_lang` = '.(int)$id_lang.') 
                WHERE msi.`active` = 1 LIMIT '.$start_point.','.$limit_point;
        } elseif ($like == false && $all == true) {
            $sql = 'SELECT msi.*, msil.*, msi.`seller_customer_id` AS id_customer
                FROM `'._DB_PREFIX_.'marketplace_seller_info` AS msi
                INNER JOIN `'._DB_PREFIX_.'marketplace_seller_info_lang` msil ON (msil.`id` = msi.`id` AND msil.`id_lang` = '.(int)$id_lang.') 
                WHERE msi.`active` = 1';
        } elseif ($like == true && $all == false) {
            $sql = 'SELECT msi.*, msil.*, msi.`seller_customer_id` AS id_customer
                    FROM `'._DB_PREFIX_.'marketplace_seller_info` msi
                    LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info_lang` msil ON (msil.`id` = msi.`id` AND msil.`id_lang` = '.(int)$id_lang.')
                    WHERE msi.`active` = 1 AND LOWER(msil.`shop_name`) LIKE "'.$like_word.'%"';
        } elseif ($like == true && $all == true) {
            $sql = 'SELECT msi.*, msil.*, msi.`seller_customer_id` AS id_customer
                    FROM `'._DB_PREFIX_.'marketplace_seller_info` msi
                    LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info_lang` msil ON (msil.`id` = msi.`id` AND msil.`id_lang` = '.(int)$id_lang.')
                    WHERE msi.`active` = 1 AND LOWER(msil.`shop_name`) LIKE "'.$like_word.'%"';
        }

        $seller_info = Db::getInstance()->executeS($sql);
        if (empty($seller_info))
            return false;

        return $seller_info;
    }

    /**
     * [Mail to seller when seller request is active/deactive].
     *
     * @param [type] $mp_id_seller
     * @param [type] $subject
     * @param bool   $mail_for
     * @param bool   $reason_text
     *
     * @return [type]
     */
    public function callMailFunction($mp_id_seller, $subject, $mail_for = false, $reason_text = false)
    {
        $seller_info = $this->sellerDetail($mp_id_seller);
        $id_lang = $seller_info['default_lang']; // Seller default lang

        if ($mail_for == 1) {
            $mail_reason = 'activé';
        } elseif ($mail_for == 2) {
            $mail_reason = 'refusé';
        } elseif ($mail_for == 3) {
            $mail_reason = 'suprimé';
        } else {
            $mail_reason = 'activé';
        }

        $obj_seller = new self($mp_id_seller, $id_lang);
        $mp_seller_name = $obj_seller->seller_name;
        $business_email = $obj_seller->business_email;
        $mp_shop_name = $obj_seller->shop_name;
        $phone = $obj_seller->phone;
        if ($business_email == '') {
            $id_customer = $obj_seller->id_customer;
            $obj_cus = new Customer($id_customer);
            $business_email = $obj_cus->email;
        }

        $templateVars = array(
            '{seller_name}' => $mp_seller_name,
            '{mp_shop_name}' => $mp_shop_name,
            '{mail_reason}' => $mail_reason,
            '{business_email}' => $business_email,
            '{phone}' => $phone,
        );

        if (Configuration::get('MP_SUPERADMIN_EMAIL')) {
            $admin_email = Configuration::get('MP_SUPERADMIN_EMAIL');
        } else {
            $obj_emp = new Employee(1);    //1 for superadmin
            $admin_email = $obj_emp->email;
        }

        $fromTitle = Configuration::get('MP_FROM_MAIL_TITLE');

        if ($reason_text && $reason_text != '') {
            $templateVars['{reason_text}'] = $reason_text;
        } else {
            $templateVars['{reason_text}'] = '';
        }

        $temp_path = _PS_MODULE_DIR_.'marketplace/mails/';

        if ($subject == 1) {
            //Seller Request Approved
            if (Configuration::get('MAIL_SELLER_REQ_APPROVE')) {
                Mail::Send(
                    $id_lang,
                    'seller_active',
                    Mail::l('Seller Request Approved', $id_lang),
                    $templateVars,
                    $business_email,
                    $mp_seller_name,
                    $admin_email,
                    $fromTitle,
                    null,
                    null,
                    $temp_path,
                    false,
                    null,
                    null
                );
            }
        } elseif ($subject == 2) {
            //Seller Request Disapproved

            if (Configuration::get('MAIL_SELLER_REQ_DISAPPROVE')) {
                Mail::Send(
                    $id_lang,
                    'seller_deactive',
                    Mail::l('Seller Request Disapproved', $id_lang),
                    $templateVars,
                    $business_email,
                    $mp_seller_name,
                    $admin_email,
                    $fromTitle,
                    null,
                    null,
                    $temp_path,
                    false,
                    null,
                    null
                );
            }
        } elseif ($subject == 3) {
            //add seller by admin approved
            if (Configuration::get('MAIL_SELLER_REQ_APPROVE')) {
                Mail::Send(
                    $id_lang,
                    'seller_add_admin',
                    Mail::l('Create Marketplace Seller', $id_lang),
                    $templateVars,
                    $business_email,
                    $mp_seller_name,
                    $admin_email,
                    $fromTitle,
                    null,
                    null,
                    $temp_path,
                    false,
                    null,
                    null
                );
            }
        }

        return true;
    }

    /**
     * Fetch the content of $template_name inside the folder marketplace/mails/current_iso_lang/ if found.
     *
     * @param string $template_name template name with extension
     * @param int    $mail_type     Mail::TYPE_HTML or Mail::TYPE_TXT
     * @param array  $var           list send to smarty
     *
     * @return string
     */
    public function getMpEmailTemplateContent($template_name, $mail_type, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }

        $default_mail_template_path = _PS_MODULE_DIR_.'marketplace/mails/'.DIRECTORY_SEPARATOR.Context::getContext()->language->iso_code.DIRECTORY_SEPARATOR.$template_name;

        if (Tools::file_exists_cache($default_mail_template_path)) {
            Context::getContext()->smarty->assign('list', $var);

            return Context::getContext()->smarty->fetch($default_mail_template_path);
        }

        return '';
    }

    public static function assignSellerDetailsView()
    {
        $obj_mp = new Marketplace();
        foreach ($obj_mp->seller_details_view as $seller_view) {
            Context::getContext()->smarty->assign(
                'MP_SELLER_DETAILS_ACCESS_'.$seller_view['id_group'],
                Configuration::get('MP_SELLER_DETAILS_ACCESS_'.$seller_view['id_group'])
            );
        }
    }

    public static function updateSellerDefaultLang($lang_id)
    {
        $ps_def_lang = Configuration::get('PS_LANG_DEFAULT');
        $udpate = Db::getInstance()->update('marketplace_seller_info', array('default_lang' => $ps_def_lang), 'default_lang = '.(int) $lang_id);
        if ($udpate) {
            return true;
        } else {
            return false;
        }
    }

    public static function changeSellerProductStatus($id_seller, $active = false)
    {
        $obj_mpproduct = new SellerProductDetail();
        $seller_products = SellerProductDetail::getMpSellerProductDetailsWithLang($id_seller, false, true);

        // active seller product
        if ($active) {
            if ($seller_products) {
                $obj_mpproduct->changeSellerProductStatusBySellerProductId($id_seller, 1);
            }
        } else {
            $obj_mpproduct->changeSellerProductStatusBySellerProductId($id_seller, 0);
        }

        if ($seller_products) {
            foreach ($seller_products as $product) {
                $obj_product = new Product($product['id_ps_product']);
                $obj_product->active = $active ? 1 : 0;
                $obj_product->save();
            }
        }

        return true;
    }
}
