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

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');

include_once(dirname(__FILE__) . '/leofeature.php');
include_once(dirname(__FILE__) . '/classes/ProductReviewCriterion.php');
include_once(dirname(__FILE__) . '/classes/ProductReview.php');

$module = new Leofeature();

if (!$module->isTokenValid() || !Tools::getValue('action')) {
    $result = '';
    die($result);
};

if (Tools::getValue('action') == 'render-modal-review') {
    $result = $module->renderModalReview(Tools::getValue('id_product'), Tools::getValue('is_logged'));
    die($result);
};

if (Tools::getValue('action') == 'add-new-review') {
    $array_result = array();
    $result = true;
    $id_guest = 0;
    $context = Context::getContext();

    $id_customer = $context->customer->id;
    if (!$id_customer) {
        $id_guest = $context->cookie->id_guest;
    }

    $id_product_review = Tools::getValue('id_product_review');
    $new_review_title = Tools::getValue('new_review_title');
    $new_review_content = Tools::getValue('new_review_content');
    $new_review_customer_name = Tools::getValue('new_review_customer_name');
    $criterion = Tools::getValue('criterion');
    $errors = array();
    // Validation
    if (!Validate::isInt($id_product_review)) {
        $errors[] = $module->l('Product ID is incorrect', 'psajax_review');
    }
    if (!$new_review_title || !Validate::isGenericName($new_review_title)) {
        $errors[] = $module->l('Title is incorrect', 'psajax_review');
    }
    if (!$new_review_content || !Validate::isMessage($new_review_content)) {
        $errors[] = $module->l('Comment is incorrect', 'psajax_review');
    }
    if (!$id_customer && (!Tools::isSubmit('new_review_customer_name') || !$new_review_customer_name || !Validate::isGenericName($new_review_customer_name))) {
        $errors[] = $module->l('Customer name is incorrect', 'psajax_review');
    }
    if (!$context->customer->id && !Configuration::get('LEOFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS')) {
        $errors[] = $module->l('You must be connected in order to send a review', 'psajax_review');
    }
    if (!count($criterion)) {
        $errors[] = $module->l('You must give a rating', 'psajax_review');
    }

    $product = new Product($id_product_review);
    if (!$product->id) {
        $errors[] = $module->l('Product not found', 'psajax_review');
    }

    if (!count($errors)) {
        $customer_review = ProductReview::getByCustomer($id_product_review, $id_customer, true, $id_guest);
        if (!$customer_review || ($customer_review && (strtotime($customer_review['date_add']) + (int) Configuration::get('LEOFEATURE_PRODUCT_REVIEWS_MINIMAL_TIME')) < time())) {
            $review = new ProductReview();
            $review->content = strip_tags($new_review_content);
            $review->id_product = (int) $id_product_review;
            $review->id_customer = (int) $id_customer;
            $review->id_guest = $id_guest;
            $review->customer_name = $new_review_customer_name;
            if (!$review->customer_name) {
                $review->customer_name = pSQL($context->customer->firstname . ' ' . $context->customer->lastname);
            }
            $review->title = $new_review_title;
            $review->grade = 0;
            $review->validate = 0;
            $review->save();

            $grade_sum = 0;
            foreach ($criterion as $id_product_review_criterion => $grade) {
                $grade_sum += $grade;
                $product_review_criterion = new ProductReviewCriterion($id_product_review_criterion);
                if ($product_review_criterion->id) {
                    $product_review_criterion->addGrade($review->id, $grade);
                }
            }

            if (count($criterion) >= 1) {
                $review->grade = $grade_sum / count($criterion);
                // Update Grade average of comment
                $review->save();
            }
            $result = true;
            Tools::clearCache($context->smarty, $module->getTemplatePath('leo_list_product_review.tpl'));
        } else {
            $result = false;
            $errors[] = $module->l('Please wait before posting another comment', 'psajax_review') . ' ' . Configuration::get('LEOFEATURE_PRODUCT_REVIEWS_MINIMAL_TIME') . ' ' . $module->l('seconds before posting a new review', 'psajax_review');
        }
    } else {
        $result = false;
    }

    $array_result['result'] = $result;
    $array_result['errors'] = $errors;
    if ($result) {
        $array_result['sucess_mess'] = $module->l('Your comment has been added. Thank you!', 'psajax_review');
    }
    die(Tools::jsonEncode($array_result));
}

if (Tools::getValue('action') == 'add-review-usefull') {
    $id_product_review = Tools::getValue('id_product_review');
    $is_usefull = Tools::getValue('is_usefull');

    if (ProductReview::isAlreadyUsefulness($id_product_review, Context::getContext()->cookie->id_customer)) {
        die('0');
    }

    if (ProductReview::setReviewUsefulness((int) $id_product_review, (bool) $is_usefull, Context::getContext()->cookie->id_customer)) {
        die('1');
    }

    die('0');
}

if (Tools::getValue('action') == 'add-review-report') {
    $id_product_review = Tools::getValue('id_product_review');

    if (ProductReview::isAlreadyReport($id_product_review, Context::getContext()->cookie->id_customer)) {
        die('0');
    }

    if (ProductReview::reportReview((int) $id_product_review, Context::getContext()->cookie->id_customer)) {
        die('1');
    }

    die('0');
}
