<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class ProductReviewGrade extends ObjectModel
{
    public $id;

    public $id_product_review;
    
    public $id_product_review_criterion;
    
    public $grade;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leofeature_product_review_grade',
        'primary' => 'id_product_review_grade',
        'fields' => array(
            'id_product_review' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_review_criterion' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'grade' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
        )
    );
}
