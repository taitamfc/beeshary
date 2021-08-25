<?php
/**
 * 2007-2017 PrestaShop
 *
 * DISCLAIMER
 ** Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/stripepro.php');

if (!defined('_PS_VERSION_'))
    exit;

$stripe = new StripePro();
$context = Context::getContext();

if ($stripe->active && Tools::getIsset('stripeToken') && Tools::getValue('stripeToken')!='') {

    $amount = $context->cart->getOrderTotal();

    if (!$stripe->isZeroDecimalCurrency($context->currency->iso_code)) {
        $amount *= 100;
    }

    $params = array(
        'token' => Tools::getValue('stripeToken'),
        'amount' => $amount,
        'currency' => $context->currency->iso_code,
        'source_type' => str_replace('source_','',Tools::getValue('sourceType')),
    );
     $stripe->processPayment($params);
} else {
    die(Tools::jsonEncode(array(
                'code' => '0',
                'msg' => 'Empty token. Unknown error, please use another card or contact us.',
            )));
}