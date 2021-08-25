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

class Reviews extends ObjectModel
{
    public $id_review;
    public $id_seller;
    public $id_customer;
    public $customer_email;
    public $rating;
    public $review;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'marketplace_seller_reviews',
        'primary' => 'id_review',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT),
            'id_customer' => array('type' => self::TYPE_INT),
            'customer_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'rating' => array('type' => self::TYPE_INT),
            'review' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function delete()
    {
        // check if delete from render list then delete all comment of this seller
        if (Tools::getValue('viewmarketplace_seller_reviews') === false && Tools::getValue('controller') == 'AdminReviews') {
            Db::getInstance()->delete('marketplace_seller_reviews', ' `id_seller` = '.(int) $this->id_seller.' AND `id_review` != '.(int) $this->id_review);
        }

        return parent::delete();
    }

    public function getSellerReviewById($id)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_reviews`
            WHERE id_review = '.(int) $id);
    }

    public static function getSellerReviewByIdSeller($id_seller, $active = true)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_reviews`
			WHERE `id_seller` ='.(int) $id_seller.'
			AND `active` ='.($active ? '1' : '0').' ORDER BY date_add DESC'
        );
    }

    public static function getReviewByCustomerIdAndSellerId($id_customer, $mp_seller_id)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_reviews`
            WHERE `id_customer` = '.(int) $id_customer.'
            AND `id_seller` = '.(int) $mp_seller_id);
    }

    public static function getReviewByIdAndCustomerId($id_customer, $id)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_reviews`
            WHERE `id_customer` = '.(int) $id_customer.'
            AND `id_review` = '.(int) $id);
    }

    public static function getSellerAvgRating($idSeller)
    {
        $reviews = self::getSellerReviewByIdSeller($idSeller);
        if ($reviews) {
            $rating = 0;
            foreach ($reviews as $review) {
                $rating = $rating + $review['rating'];
            }
            return (double) ($rating / count($reviews));
        }

        return false;
    }
}
