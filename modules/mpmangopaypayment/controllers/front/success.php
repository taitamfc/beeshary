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

class MpMangopayPaymentSuccessModuleFrontController extends ModuleFrontController
{
    private $objApi;

    public function __construct()
    {
        parent::__construct();
        $this->paymentErrors = array();
        $this->objApi = new MangoPay\MangoPayApi();

        if (Configuration::get('WK_MP_MANGOPAY_MODE') == 'sandbox') {
            $this->objApi->Config->BaseUrl = 'https://api.sandbox.mangopay.com';
        } else {
            $this->objApi->Config->BaseUrl = 'https://api.mangopay.com';
        }
        $this->objApi->Config->ClientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        $this->objApi->Config->ClientPassword = Configuration::get('WK_MP_MANGOPAY_PASSPHRASE');
        $this->objApi->Config->TemporaryFolder = _PS_MODULE_DIR_.'mpmangopaypayment/temp/';
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Mangopay Payment', array(), 'Breadcrumb'),
            'url' => '',
        );
        return $breadcrumb;
    }

    public function initContent()
    {
        if ($this->module->active) {
            try {
                parent::initContent();
                // This callback is for the mangopay bankwire payment. Orders for this transaction are already created
                // in the shop. This callback is for bankwire payment succeeded. So we have to just change the status
                // of the orders created by this mangopay banwire transaction
                if (($resourceId = Tools::getValue('RessourceId')) && ($eventType = Tools::getValue('EventType'))) {
                    if ($eventType == 'PAYIN_NORMAL_SUCCEEDED') {
                        $objMgpTransaction = new MangopayTransaction();
                        if ($payInDetails = $objMgpTransaction->getTransactionByPayInId($resourceId)) {
                            if (Validate::isLoadedObject(
                                $objMgpTransaction = new MangopayTransaction($payInDetails['id'])
                            )) {
                                // save transsaction status as SUCCEEDED
                                $objMgpTransaction->status = 'SUCCEEDED';
                                $objMgpTransaction->save();
                                $orderReference = $payInDetails['order_reference'];
                                $objMgpConfig = new MangopayConfig();
                                if ($orders = $objMgpConfig->getOrderIdsByOrderReference($orderReference)) {
                                    // if orders found for the transaction then set the order status
                                    foreach ($orders as $order) {
                                        $objOrder = new Order($order['id_order']);
                                        $orderHistory = new OrderHistory();
                                        $orderHistory->id_order = (int)$order['id_order'];
                                        $orderHistory->changeIdOrderState(
                                            (int)Configuration::get('PS_OS_PAYMENT'),
                                            $objOrder,
                                            true
                                        );
                                        $orderHistory->addWithemail(true, null);
                                    }
                                } else {
                                    error_log(
                                        date('[Y-m-d H:i e] ').' success.php Transaction id not found for resource id:'.
                                        $resourceId.PHP_EOL.PHP_EOL.PHP_EOL,
                                        3,
                                        _PS_MODULE_DIR_.
                                        'mpmangopaypayment/error.log'
                                    );
                                }
                            } else {
                                error_log(
                                    date('[Y-m-d H:i e] ').' success.php Transaction not found for resource id:'.
                                    $resourceId.PHP_EOL.PHP_EOL.PHP_EOL,
                                    3,
                                    _PS_MODULE_DIR_.
                                    'mpmangopaypayment/error.log'
                                );
                            }
                        } else {
                            error_log(
                                date('[Y-m-d H:i e] ').' success.php Transaction id not found for resource id:'.
                                $resourceId.PHP_EOL.PHP_EOL.PHP_EOL,
                                3,
                                _PS_MODULE_DIR_.
                                'mpmangopaypayment/error.log'
                            );
                        }
                    }
                    die('OK');
                } elseif (($mandateId = Tools::getValue('MandateId')) && ($idCart = Tools::getValue('id_cart'))) {
                    // This callback is for mangopay direct debit payment. We get 'MandateId' and 'id_cart' in this
                    // callback. we have to create order for this cart and transaction
                    if (Validate::isLoadedObject($objCart = new Cart($idCart))) {
                        if (($totalAmount = $objCart->getOrderTotal(true))
                            && ($idCustomer = $objCart->id_customer)
                        ) {
                            $mgpCurrency = new Currency(
                                (int)Currency::getIdByIsoCode(Configuration::get('WK_MP_MANGOPAY_CURRENCY'))
                            );
                            // amount conversion to the amount for wich currency mangopay account is configured
                            if ($mgpCurrency->id != $objCart->id_currency) {
                                $totalAmount = Tools::convertPriceFull(
                                    $totalAmount,
                                    new Currency($objCart->id_currency),
                                    new Currency($mgpCurrency->id)
                                );
                            }
                            $objMangopayService = new MangopayMpService();
                            $objMangopayBuyer = new MangopayBuyer();
                            if ($mangopayBuyerData = $objMangopayBuyer->getBuyerMangopayData(
                                $idCustomer,
                                Configuration::get('WK_MP_MANGOPAY_CURRENCY')
                            )) {
                                // create direct debit direct payment with the details get from URL
                                $payInResult = $objMangopayService->createDirectDebitDirectpayment(
                                    $mangopayBuyerData['mgp_userid'],
                                    $mangopayBuyerData['mgp_walletid'],
                                    $mandateId,
                                    $totalAmount,
                                    0
                                );
                                if (isset($payInResult['Status']) && $payInResult['Status'] != 'FAILED') {
                                    $objMgpTransaction = new MangopayTransaction();
                                    $objMgpTransaction->mangopayOrderCreation(
                                        $this,
                                        (object) $payInResult,
                                        (int)Configuration::get('PS_OS_PREPARATION'),
                                        $objCart
                                    );
                                    //order created successfully then page will redirect to the order confirmation page
                                } else {
                                    $this->paymentErrors[] = $this->module->l(
                                        'Some error has been occurred. please try again.',
                                        'success'
                                    );
                                }
                            } else {
                                $this->paymentErrors[] = $this->module->l(
                                    'Some buyer information is missing. Please contact to customer support',
                                    'success'
                                );
                            }
                        } else {
                            $this->paymentErrors[] = $this->module->l(
                                'Cart amount does not match with the transaction amount. Please contact to customer
                                 support',
                                'success'
                            );
                        }
                    } else {
                        $this->paymentErrors[] = $this->module->l(
                            'Cart is not loaded. May be an order is already created for this cart.',
                            'success'
                        );
                    }
                    // if FAILED then order will not be created then page will redirected to the order checkout page
                    // $orderProcess = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order';
                    // Tools::redirect($this->context->link->getPageLink("$orderProcess", true));
                } elseif (($transactionId = Tools::getValue('transactionId'))
                    && ($idCart = Tools::getValue('id_cart'))
                ) {
                    // This callback is for mangopay card 3D payment. We get 'transactionId' and 'id_cart' in this
                    // callback. we have to create order for this cart and transaction
                    if (Validate::isLoadedObject($objCart = new Cart($idCart))) {
                        if (($totalAmount = $objCart->getOrderTotal(true))
                            && ($idCustomer = $objCart->id_customer)
                        ) {
                            $objMgpTransaction = new MangopayTransaction();
                            if (!$objMgpTransaction->getTransactionByPayInId($transactionId)) {
                                if ($payInResult = (array) $this->objApi->PayIns->Get($transactionId)) {
                                    // if save card feature is not active or customer do not choose to save the card
                                    // then deactive this card after pay IN
                                    if (isset($payInResult['Status']) && $payInResult['Status'] != 'FAILED') {
                                        $objMangopayBuyer = new MangopayBuyer();
                                        if ($mangopayBuyerData = $objMangopayBuyer->getBuyerMangopayData(
                                            $idCustomer,
                                            $payInResult['CreditedFunds']->Currency
                                        )) {
                                            $mgpCurrency = new Currency(
                                                (int)Currency::getIdByIsoCode($payInResult['CreditedFunds']->Currency)
                                            );
                                            // amount conversion to currency on which mangopay account is configured
                                            if ($mgpCurrency->id != $objCart->id_currency) {
                                                $totalAmount = Tools::convertPriceFull(
                                                    $totalAmount,
                                                    new Currency($objCart->id_currency),
                                                    new Currency($mgpCurrency->id)
                                                );
                                            }
                                            $totalAmount = (string)($totalAmount * 100);
                                            //check the amount of the transaction is equal to the cart total amount
                                            //check mangopay id user of cart's customer is same as mangopay user id of
                                            //transaction. So that customer and cart will be validated with transaction
                                            if ($payInResult['CreditedFunds']->Amount == $totalAmount
                                                && $payInResult['AuthorId'] == $mangopayBuyerData['mgp_userid']
                                            ) {
                                                // is saved card functionality is not enabled in the configuration then
                                                // disable the card after transaction is made with the card
                                                if ((Configuration::get('WK_MP_MANGOPAY_PAYIN_TYPE') == 1)
                                                    && !Tools::getValue('sv_cd')
                                                    && isset($payInResult['PaymentDetails']->CardId)
                                                ) {
                                                    $objCard = new MangoPay\Card();
                                                    $objCard->Id = $payInResult['PaymentDetails']->CardId;
                                                    $objCard->Active = 0;
                                                    $objCard->Validity = \MangoPay\CardValidity::Invalid;
                                                    $this->objApi->Cards->Update($objCard);
                                                }
                                                $idOrderStatus = Configuration::get('PS_OS_PAYMENT');
                                                $objMgpTransaction->mangopayOrderCreation(
                                                    $this,
                                                    (object)$payInResult,
                                                    $idOrderStatus,
                                                    $objCart
                                                );
                                                //order created successfully then page will redirect to the order
                                                //confirmation page.
                                            }
                                        }
                                    } else {
                                        $this->paymentErrors[] = $this->module->l(
                                            'Invalid transaction found. Please try again.',
                                            'success'
                                        );
                                    }
                                } else {
                                    $this->paymentErrors[] = $this->module->l(
                                        'Unknown Transaction Id',
                                        'success'
                                    );
                                }
                            } else {
                                $this->paymentErrors[] = $this->module->l(
                                    'An order has already created for this transaction',
                                    'success'
                                );
                            }
                        }
                    } else {
                        $this->paymentErrors[] = $this->module->l(
                            'Cart is not loaded. May be an order is already created for this cart.',
                            'success'
                        );
                    }
                    // if FAILED then order will not be created then page will redirected to the order checkout page
                    // $orderProcess = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order';
                    // Tools::redirect($this->context->link->getPageLink("$orderProcess", true));
                } else {
                    $this->paymentErrors[] = $this->module->l(
                        'No information found for the transaction. Please contact to the customer support.',
                        'success'
                    );
                }
            } catch (Exception $e) {
                error_log(
                    date('[Y-m-d H:i e] ').' Errors on success.php :'.PHP_EOL.json_encode($e).PHP_EOL.PHP_EOL,
                    3,
                    _PS_MODULE_DIR_.
                    'mpmangopaypayment/error.log'
                );
                $this->paymentErrors[] = $this->module->l('Some error occurred. Please try again later.', 'success');
            }
        } else {
            $this->paymentErrors[] = $this->module->l(
                'Currently Mangopay Payment is not available. Please try again later.',
                'success'
            );
        }
        if (count($this->paymentErrors)) {
            $this->context->smarty->assign('mangopay_error_messages', $this->paymentErrors);
            $this->setTemplate('module:mpmangopaypayment/views/templates/front/error_messages.tpl');
        }
    }
}
