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

class SellerVacationDetail extends ObjectModel
{
    public $id;
    public $id_seller;
    public $from;
    public $to;
    public $addtocart;
    public $active = 1;
    public $date_add;
    public $date_upd;

    public $description;

    public static $definition = array(
        'table' => 'marketplace_seller_vacation',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT,'required' => true),
            'from' => array('type' => self::TYPE_DATE,'validate' => 'isDateFormat','required' => true),
            'to' => array('type' => self::TYPE_DATE,'validate' => 'isDateFormat','required' => true),
            'addtocart' => array('type' => self::TYPE_BOOL,'validate' => 'isBool','copy_post' => false),
            'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool','copy_post' => false),
            'date_add' => array('type' => self::TYPE_DATE,'validate' => 'isDateFormat','required' => false),
            'date_upd' => array('type' => self::TYPE_DATE,'validate' => 'isDateFormat','required' => false),

            'description' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
        ),
    );

    /**
     * deleting seller vacation by admin.
     *
     * @return [type] [description]
     */
    public function delete()
    {
        if (!$this->productNormal($this->id) || !parent::delete()) {
            return false;
        }

        return true;
    }

    /**
     * making product normal after deleting seller vacation by admin.
     *
     * @param [type] $id [description]
     *
     * @return [type] [description]
     */
    public function productNormal($id)
    {
        $id_seller = Db::getInstance()->getValue('SELECT `id_seller` FROM `'._DB_PREFIX_.'marketplace_seller_vacation` WHERE `id` ='.(int) $id);
        if ($id_seller) {
            $seller_product_detail = $this->getMpSellerProductDetail($id_seller);
            if ($seller_product_detail) {
                $this->mpSellerVacationEnableDisableAddToCart($seller_product_detail, 1);
            }
            return true;
        }
    }

    /**
     * Displaying vacation details for particular seller.
     *
     * @param [type] $seller_id [description]
     */
    public function getMarketPlaceSellerVacationDetails($seller_id, $id_lang = false)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        $mps_vacation_detail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_vacation` as msv JOIN `'._DB_PREFIX_.'marketplace_seller_vacation_lang` as msvl ON (msv.`id` = msvl.`id`) WHERE msv.`id_seller`='.(int) $seller_id.' AND msvl.`id_lang`='.(int) $id_lang);

        if (!empty($mps_vacation_detail)) {
            return $mps_vacation_detail;
        } else {
            return false;
        }
    }

    /**
     * getting seller vacation detail for updating.
     *
     * @param [type] $edit_id [description]
     */
    public function getMpsVacationDetailByEditId($id)
    {
        $mpsv_edit_detail = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_vacation` WHERE `id` ='.(int) $id);

        if (!empty($mpsv_edit_detail)) {
            return $mpsv_edit_detail;
        } else {
            return false;
        }
    }

    public function getMpsVacationLangDetailByEditId($id)
    {
        $mpsv_edit_detail = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_vacation_lang` WHERE `id` ='.(int) $id);

        if (!empty($mpsv_edit_detail)) {
            return $mpsv_edit_detail;
        } else {
            return false;
        }
    }

    /**
     * deleting vacation detail.
     *
     * @param [type] $del_id [description]
     */
    public function deleteMpSellerVacationDetail($del_id)
    {
        $seller_id = $this->mpSellerVacationSellerId();
        $seller_product_detail = $this->getMpSellerProductDetail($seller_id);
        if ($seller_product_detail) {
            $this->mpSellerVacationEnableDisableAddToCart($seller_product_detail, 1);
        }

        $obj_vacation = new self($del_id);
        $obj_vacation->delete();

        return true;
    }

    /**
     * getting product id , product name from marketplace.
     *
     * @param [type] $id_seller [description]
     *
     * @return [type] [description]
     */
    public function getMpSellerProductDetail($id_seller)
    {
        $seller_product_info = Db::getInstance()->executeS('SELECT `id_mp_product`, `active`, `id_ps_product` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_seller` ='.(int) $id_seller.' AND `active` = 1');
        if ($seller_product_info) {
            return $seller_product_info;
        }
        return false;
    }

    /**
     * getting prestashop product id.
     *
     * @param [type] $mps_product_id [description]
     *
     * @return [type] [description]
     */
    public function getPrestaShopProductId($mps_product_id)
    {
        $id_product = Db::getInstance()->getRow('SELECT `id_ps_product` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_mp_product` ='.(int) $mps_product_id);
        if (empty($id_product)) {
            return false;
        } else {
            return $id_product;
        }
    }

    /**
     * getting marketplace product detail by product id.
     *
     * @param [type] $id_product [description]
     *
     * @return [type] [description]
     */
    public function getMpSellerProductDetailByIdProduct($id_product)
    {
        $marketplace_seller_id = Db::getInstance()->getRow('SELECT `id_seller` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_ps_product` ='.(int) $id_product);
        if ($marketplace_seller_id) {
            return $marketplace_seller_id;
        }

        return false;
    }

    /**
     * check from_date is today.
     *
     * @param [type] $from_date [description]
     *
     * @return [type] [description]
     */
    public static function mpSellerVacationCheckFromDate($from_date)
    {
        if ($from_date == date('Y-m-d')) {
            return $from_date;
        } else {
            return false;
        }
    }

    /**
     * enable and disable add to cart button.
     *
     * @param [type] $seller_product_detail [description]
     * @param [type] $orderValue            [description]
     *
     * @return [type] [description]
     */
    public function mpSellerVacationEnableDisableAddToCart($seller_product_detail, $orderValue)
    {
        if ($seller_product_detail) {
            foreach ($seller_product_detail as $value) {
                $id_product = $this->getPrestaShopProductId($value['id_mp_product']);
                if ($id_product) {
                    Db::getInstance()->update('product', array('available_for_order' => $orderValue), 'id_product = '.(int) $id_product['id_ps_product']);
                    Db::getInstance()->update('product_shop', array('available_for_order' => $orderValue), 'id_product = '.(int) $id_product['id_ps_product']);
                }
            }
        }
    }

    /**
     * check vacation is valid or not.
     *
     * @param [type] $id_seller [description]
     *
     * @return [type] [description]
     */
    public function mpSellerValidVacation($id_seller, $id_lang = false)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        $valid_vacation_info = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_vacation` as msv JOIN `'._DB_PREFIX_.'marketplace_seller_vacation_lang` as msvl ON (msv.`id` = msvl.`id`) WHERE msv.`id_seller` ='.(int) $id_seller.'  AND msvl.`id_lang`='.(int) $id_lang.' AND msv.`from` <= CURDATE() AND msv.`to` >= CURDATE()');

        if (!empty($valid_vacation_info)) {
            return $valid_vacation_info;
        } else {
            return false;
        }
    }

    /**
     * check vacation is expired or not.
     *
     * @param [type] $id_seller [description]
     *
     * @return [type] [description]
     */
    public function mpSellerVacationExpire($vacation_id)
    {
        $vacation_expire_id = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'marketplace_seller_vacation` WHERE `id` ='.(int) $vacation_id.' AND `to` < CURDATE()');
        if ($vacation_expire_id) {
            return $vacation_expire_id;
        }
        return false;
    }

    /**
     * getting market place product detail  which are active , by using product id.
     *
     * @param [type] $marketplace_product_id [description]
     *
     * @return [type] [description]
     */
    public function getMpSellerProductDetailByProductId($marketplace_product_id)
    {
        $mps_product_detail = Db::getInstance()->getRow('SELECT `id_mp_product`,`id_seller`,`active` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_mp_product` ='.(int) $marketplace_product_id.' AND `active` = 1');
        if (!empty($mps_product_detail)) {
            return $mps_product_detail;
        } else {
            return false;
        }
    }

    /**
     * check from date is today by using seller id.
     *
     * @param [type] $id_seller [description]
     *
     * @return [type] [description]
     */
    public function mpSellerVacationCheckFromDateBySellerId($id_seller)
    {
        $vacation_info = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_vacation` WHERE id_seller ='.(int) $id_seller.' AND `from` > CURDATE()');
        if (!empty($vacation_info)) {
            return $vacation_info;
        } else {
            return false;
        }
    }

    /**
     * reset add to cart button.
     *
     * @return [type] [description]
     */
    public function resetAddToCartButton()
    {
        $vacation_detail = Db::getInstance()->executeS('SELECT `id_seller` FROM `'._DB_PREFIX_.'marketplace_seller_vacation` WHERE `addtocart` = 0');
        if ($vacation_detail) {
            foreach ($vacation_detail as $value) {
                $seller_product_detail = $this->getMpSellerProductDetail($value['id_seller']);
                if ($seller_product_detail) {
                    foreach ($seller_product_detail as $value) {
                        $id_product = $this->getPrestaShopProductId($value['id_mp_product']);
                        if ($id_product) {
                            Db::getInstance()->update('product', array('available_for_order' => true), 'id_product = '.(int) $id_product['id_ps_product']);
                            Db::getInstance()->update('product_shop', array('available_for_order' => true), 'id_product = '.(int) $id_product['id_ps_product']);
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * check previous vacation exits or not.
     *
     * @param [type] $id_seller [description]
     *
     * @return [type] [description]
     */
    public function checkPreviousVcationDetail($id_seller, $id_vacation = false)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_vacation` WHERE `id_seller` ='.(int) $id_seller;
        if ($id_vacation) {
            $sql .= ' AND `id` !='.(int)$id_vacation;
        }
        $previous_vacation_detail = Db::getInstance()->executeS($sql);
        if ($previous_vacation_detail) {
            return $previous_vacation_detail;
        }
        return false;
    }

    /**
     * getting seller id by using customer id.
     *
     * @return [type] [description]
     */
    public function mpSellerVacationSellerId()
    {
        $this->context = Context::getContext();
        if (isset($this->context->customer->id)) {
            $customer_id = $this->context->customer->id;
            $obj_marketplace_seller = new WkMpSeller();
            $seller_info = $obj_marketplace_seller->getSellerDetailByCustomerId($customer_id);
            if ($seller_info) {
                $seller_id = $seller_info['id_seller'];

                return $seller_id;
            } else {
                return false;
            }
        }
    }

    /**
     * getting seller name and email for sending mail.
     *
     * @param [type] $id_seller [description]
     *
     * @return [type] [description]
     */
    public function getMpSellerDetailsForMail($id_seller)
    {
        $seller_info = Db::getInstance()->getRow('SELECT CONCAT(`seller_firstname`,\' \',`seller_lastname`) as seller_name, `business_email` FROM `'._DB_PREFIX_.'wk_mp_seller` WHERE `id_seller`='.(int) $id_seller);
        if (empty($seller_info)) {
            return false;
        } else {
            return $seller_info;
        }
    }

    public static function getValidVacationDetailsBySellerId($id_seller)
    {
        $vacation_detail = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_vacation` WHERE `id_seller` ='.(int) $id_seller.' AND `to` >= CURDATE()');
        if ($vacation_detail) {
            return $vacation_detail;
        }
        return false;
    }

    public static function disableDates($previous_vacation_detail)
    {
        foreach ($previous_vacation_detail as $previous_vacation_info) {
            $date_from = strtotime($previous_vacation_info['from']);
            $date_to = strtotime($previous_vacation_info['to']);
            $datediff = $date_to - $date_from;
            $dates[] = Self::getDateRange($date_from, $date_to, 'Y-n-j');
        }
        foreach ($dates as $value) {
            foreach ($value as $val) {
                $disable_dates[] = $val;
            }
        }
        return $disable_dates;
    }

    public static function getDateRange($first, $last, $output_format = 'Y-m-d', $step = '+1 day')
    {
        $dates = array();
        while ($first <= $last) {
            $dates[] = date($output_format, $first);
            $first = strtotime($step, $first);
        }
        return $dates;
    }

    public function getAllExpiredVacationsBySellerId($id_seller)
    {
        $vacation_expire_id = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_vacation` WHERE id_seller ='.(int) $id_seller.' AND `to` < CURDATE()');
        if ($vacation_expire_id) {
            return $vacation_expire_id;
        }
        return false;
    }

    /**
     * enable or disable 'add to cart' button for particular product.
     *
     * @param [type] $active      [description]
     * @param [type] $add_to_cart [description]
     * @param [type] $id_product  [description]
     *
     * @return [type] [description]
     */
    public function enableDisableAddToCartForParticularProduct($active, $addtocart, $id_ps_product)
    {
        if ($active && $id_ps_product) {
            Db::getInstance()->update('product', array('available_for_order' => $addtocart), 'id_product = '.(int) $id_ps_product);
            Db::getInstance()->update('product_shop', array('available_for_order' => $addtocart), 'id_product = '.(int) $id_ps_product);
        }
    }
}
