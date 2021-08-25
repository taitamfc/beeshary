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

class MangopayTransaction extends ObjectModel
{
    public $id;
    public $transaction_id;
    public $order_reference;
    public $mgp_clientid;
    public $buyer_mgp_userid;
    public $credited_mgp_userid;
    public $credited_mgp_walletid;
    public $payment_type;
    public $amount_paid;
    public $credited_amount;
    public $currency;
    public $fees;
    public $creation_date;
    public $mandate_id;
    public $status;
    public $date_add;
    public $date_upd;

    const WK_PAYMENT_TYPE_CARD = 'CARD';
    const WK_PAYMENT_TYPE_BANKWIRE = 'BANK_WIRE';
    const WK_PAYMENT_TYPE_DIRECT_DEBIT = 'DIRECT_DEBIT';

    public static $definition = array(
        'table' => 'wk_mp_mangopay_transaction',
        'primary' => 'id',
        'fields' => array(
            'transaction_id' => array('type' => self::TYPE_STRING),
            'order_reference' => array('type' => self::TYPE_STRING),
            'mgp_clientid' => array('type' => self::TYPE_STRING),
            'buyer_mgp_userid' => array('type' => self::TYPE_STRING),
            'credited_mgp_userid' => array('type' => self::TYPE_STRING),
            'credited_mgp_walletid' => array('type' => self::TYPE_INT),
            'payment_type' => array('type' => self::TYPE_STRING),
            'amount_paid' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'credited_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'currency' => array('type' => self::TYPE_STRING),
            'fees' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'creation_date' => array('type' => self::TYPE_STRING),
            'mandate_id' => array('type' => self::TYPE_STRING), // for direct debit payments
            'status' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * [saveTransactionDetails Saves data of Pay In transaction if pay In succeeded].
     * @param [array] $payment [array of the details of the payment]
     * @return [Boolean] [Id of the row inserted or false in case of error in saving data]
     */
    public function saveTransactionDetails($payment, $orderReference)
    {
        if ($payment && $orderReference) {
            $this->id_cart = Context::getContext()->cart->id;
            $this->transaction_id = $payment->Id;
            $this->order_reference = $orderReference;
            $this->mgp_clientid = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
            $this->buyer_mgp_userid = $payment->AuthorId;
            $this->credited_mgp_userid = $payment->CreditedUserId;
            $this->credited_mgp_walletid = $payment->CreditedWalletId;
            $this->payment_type = $payment->PaymentType;
            $this->amount_paid = $payment->DebitedFunds->Amount / 100; //convert to dollor from cent
            $this->credited_amount = $payment->CreditedFunds->Amount / 100; //convert to dollor from cent
            $this->fees = $payment->Fees->Amount / 100; //convert to dollor from cent
            $this->currency = $payment->CreditedFunds->Currency;
            $this->creation_date = $payment->CreationDate;
            $this->status = $payment->Status;
            $this->mandate_id = isset($payment->PaymentDetails->MandateId)?$payment->PaymentDetails->MandateId : 0;
            if ($this->save()) {
                return $this->id;
            }
        }
        return false;
    }

    //customized array with previous commission array and other necessay variables for mangopay transfer
    public function getCustomizedCustomerCommissionArray($cust_comm)
    {
        if ($cust_comm) {
            $admin_total_products = 0;
            $admin_total = 0;
            $mangopay_fee = array();
            foreach ($cust_comm['admin'] as $key => $admin_price) {
                $admin_total += $admin_price;
                if ($key != 'own') {
                    $mangopay_fee[$key] = $admin_price;
                } else {
                    $admin_total_products = $admin_price;
                }
            }
            $cust_comm['admin'] = $admin_total;
            $total_amount_admin = 0;
            foreach ($cust_comm as $value) {
                $total_amount_admin += $value;
            }
            $cust_comm_new = array();
            $cust_comm_new['previous'] = $cust_comm;
            $cust_comm_new['mgp_data']['mangopay_fee'] = $mangopay_fee;
            $cust_comm_new['mgp_data']['admin_total_products'] = $admin_total_products;
            $cust_comm_new['mgp_data']['admin_total_amount'] = $total_amount_admin;

            return $cust_comm_new;
        }
        return false;
    }

    /**
     * get the transaction detail by order reference.
     * @param string $orderReference
     * @return array
     */
    public function getTransactionsDetailsByOrderReference($orderReference)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_transaction`
            WHERE `order_reference` = \''.pSQL($orderReference).'\''
        );
    }

    /**
     * [transferAmountsToWallets transfer amount of the orders to the respectives wallet of the sellers and admin].
     * @param [array] $cust_commission_array [array having customer commission array and other related variables values]
     * @return [type] []
     */
    public function transferAmountsToWallets($cust_commission_array, $id_cart, $buyer_id_customer)
    {
        $cust_comm = $cust_commission_array['previous'];
        $mangopay_fee = $cust_commission_array['mgp_data']['mangopay_fee'];
        $admin_total_amount = $cust_commission_array['mgp_data']['admin_total_amount'];
        $admin_total_products = $cust_commission_array['mgp_data']['admin_total_products'];
        $total_admin_tran_fee = 0;
        $cart = new Cart($id_cart);
        $currency = new Currency((int) Currency::getIdByIsoCode(Configuration::get('WK_MP_MANGOPAY_CURRENCY')));
        if ($currency->id != $cart->id_currency) {
            $admin_total_amount = Tools::convertPriceFull(
                $admin_total_amount,
                new Currency($cart->id_currency),
                new Currency($currency->id)
            );
            $admin_total_products = Tools::convertPriceFull(
                $admin_total_products,
                new Currency($cart->id_currency),
                new Currency($currency->id)
            );
        }
        try {
            foreach ($cust_comm as $id_customer => $amount) {
                $mangopayTransactionAmount = $amount;
                if ($currency->id != $cart->id_currency) {
                    $mangopayTransactionAmount = Tools::convertPriceFull(
                        $amount,
                        new Currency($cart->id_currency),
                        new Currency($currency->id)
                    );
                }
                if ($id_customer != 'admin') {
                    $seller_dtl = WkMpSeller::getSellerDetailByCustomerId($id_customer);
                    $seller_id_country = MangopaySellerCountry::sellerCountryID($seller_dtl['id_seller']);
                    if (isset($mangopay_fee[$id_customer]) && $mangopay_fee[$id_customer]) {
                        $fee = $mangopay_fee[$id_customer];
                    } else {
                        $fee = 0;
                    }
                    if ($mangopayTransactionAmount || $fee) {
                        if ($currency->id != $cart->id_currency) {
                            $fee = Tools::convertPriceFull(
                                $fee,
                                new Currency($cart->id_currency),
                                new Currency($currency->id)
                            );
                        }
                        if ($seller_id_country) {
                            //create seller mangopay account in any case if not created before
                            $mgp_seller_details = MangopayMpSeller::sellerMangopayDetails($seller_dtl['id_seller']);
                            if (!$mgp_seller_details) {
                                $obj_mgpservice = new MangopayMpService();
                                $WK_MP_MANGOPAY_USERID = $obj_mgpservice->createMangopayUserLegal(
                                    $seller_id_country,
                                    $seller_dtl['id_seller'],
                                    'Seller'
                                );
                                if ($WK_MP_MANGOPAY_USERID) {
                                    $WK_MP_MANGOPAY_WALLETID = $obj_mgpservice->createMangopayWallet(
                                        $WK_MP_MANGOPAY_USERID
                                    );
                                    if ($WK_MP_MANGOPAY_WALLETID) {
                                        //Saving data in our table
                                        $obj_mgp_mpseller = new MangopayMpSeller();
                                        $obj_mgp_mpseller->mgp_clientid = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
                                        $obj_mgp_mpseller->mgp_userid = $WK_MP_MANGOPAY_USERID;
                                        $obj_mgp_mpseller->mgp_walletid = $WK_MP_MANGOPAY_WALLETID;
                                        $obj_mgp_mpseller->currency_iso = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
                                        $obj_mgp_mpseller->id_seller = $seller_dtl['id_seller'];
                                        $obj_mgp_mpseller->save();

                                        $mgp_seller_details = array(
                                            'mgp_userid' => $WK_MP_MANGOPAY_USERID,
                                            'mgp_walletid' => $WK_MP_MANGOPAY_WALLETID
                                        );
                                    } else {
                                        $admin_total_products += ($mangopayTransactionAmount + $fee);
                                        $total_admin_tran_fee += $fee;
                                    }
                                } else {
                                    $admin_total_products += ($mangopayTransactionAmount + $fee);
                                    $total_admin_tran_fee += $fee;
                                }
                            } else {
                                $wallet_alail = MangopayMpSeller::checkSellerMangopayDetailsAvailable(
                                    Configuration::get('WK_MP_MANGOPAY_CLIENTID'),
                                    $seller_dtl['id_seller'],
                                    Configuration::get('WK_MP_MANGOPAY_CURRENCY')
                                );
                                if ($wallet_alail) {
                                    $mgp_seller_details = array(
                                        'mgp_userid' => $wallet_alail['mgp_userid'],
                                        'mgp_walletid' => $wallet_alail['mgp_walletid']
                                    );
                                } else {
                                    $obj_mgpservice = new MangopayMpService();
                                    $WK_MP_MANGOPAY_WALLETID = $obj_mgpservice->createMangopayWallet(
                                        $mgp_seller_details['mgp_userid']
                                    );
                                    if ($WK_MP_MANGOPAY_WALLETID) {
                                        //Saving data in our table
                                        $obj_mgp_mpseller = new MangopayMpSeller();
                                        $obj_mgp_mpseller->mgp_clientid = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
                                        $obj_mgp_mpseller->mgp_userid = $mgp_seller_details['mgp_userid'];
                                        $obj_mgp_mpseller->mgp_walletid = $WK_MP_MANGOPAY_WALLETID;
                                        $obj_mgp_mpseller->id_seller = $seller_dtl['id_seller'];
                                        $obj_mgp_mpseller->currency_iso = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
                                        $obj_mgp_mpseller->save();

                                        $mgp_seller_details = array('mgp_userid' => $mgp_seller_details['mgp_userid'], 'mgp_walletid' => $WK_MP_MANGOPAY_WALLETID);
                                    } else {
                                        $admin_total_products += ($mangopayTransactionAmount + $fee);
                                        $total_admin_tran_fee += $fee;
                                    }
                                }
                            }

                            if ($mgp_seller_details
                                && $mgp_seller_details['mgp_userid']
                                && $mgp_seller_details['mgp_walletid']
                            ) {
                                if ($transferId = $this->transferMangoPayAmount(
                                    $mgp_seller_details,
                                    ($mangopayTransactionAmount + $fee),
                                    $seller_dtl['id_seller'],
                                    $fee,
                                    $id_cart,
                                    $buyer_id_customer
                                )) {
                                    // Managing Mp table for seller settlement
                                    $sellerSplit = new WkMpSellerPaymentSplit();
                                    $this->moduleInstance = Module::getInstanceByName('mpmangopaypayment');
                                    if (!$sellerSplit->settleSellerAmount(
                                        $seller_dtl['id_seller'],
                                        $amount,
                                        $cart->id_currency,
                                        true,
                                        $this->moduleInstance->displayName,
                                        1,
                                        1,
                                        $transferId
                                    )) {
                                        error_log(
                                            date('[Y-m-d H:i e] ').'Transaction Settlement Error : Error occurec while
                                            making entry with the details :: cart id - '.$id_cart.', Seller Id -
                                            '.$seller_dtl['id_seller'].', Amount - '.$amount.', Currency Id -
                                            '.$cart->id_currency.' , Function settleSellerAmount()'.PHP_EOL,
                                            3,
                                            _PS_MODULE_DIR_.'mpmangopaypayment/error.log'
                                        );
                                    }
                                    // ------- End of code -------
                                } else {
                                    $admin_total_products += ($mangopayTransactionAmount + $fee);
                                    $total_admin_tran_fee += $fee;
                                }
                            }
                        } else {
                            $admin_total_products += ($mangopayTransactionAmount + $fee);
                            $total_admin_tran_fee += $fee;
                        }
                    }
                }
            }
        } catch (\MangoPay\ResponseException $e) {
            error_log(
                date('[Y-m-d H:i e] ').'Mangopay transfer mangopay amount to wallet error:  Error while transfering
                mangopay amount to wallet And Buyer id customer: '.$buyer_id_customer.PHP_EOL.'Error Message :
                '.$e->getMessage().PHP_EOL.PHP_EOL,
                3,
                _PS_MODULE_DIR_.'mangopayprestashop/error.log'
            );
        }
        if ($admin_total_products) {
            $mgp_seller_details = array(
                'mgp_userid' => Configuration::get('WK_MP_MANGOPAY_USERID'),
                'mgp_walletid' => Configuration::get('WK_MP_MANGOPAY_WALLETID')
            );
            $this->transferMangoPayAmount(
                $mgp_seller_details,
                $admin_total_products,
                0,
                $total_admin_tran_fee,
                $id_cart,
                $buyer_id_customer
            );
        }
    }

    /**
     * [transferMangoPayAmount transfer amounts to the respective wallets of sellers and admin].
     * @param [array] $mgp_seller_details [array having seller mangopay user id and wallet id]
     * @param [float] $amount             [amount to transfer to the wallet]
     * @param [int]   $id_seller          [seller id]
     * @param int     $fee                [amount which has to be set as FEE in this transfer]
     * @return [type] []
     */
    public function transferMangoPayAmount(
        $mgp_seller_details,
        $amount,
        $id_seller,
        $fee = 0,
        $id_cart,
        $buyer_id_customer
    ) {
        $mode = Configuration::get('WK_MP_MANGOPAY_MODE');
        $obj_api = new MangoPay\MangoPayApi();
        if ($mode == 'sandbox') {
            $obj_api->Config->BaseUrl = 'https://api.sandbox.mangopay.com';
        } else {
            $obj_api->Config->BaseUrl = 'https://api.mangopay.com';
        }

        $obj_mango_buyer = new MangopayBuyer();
        $buyer_data = $obj_mango_buyer->getBuyerMangopayData(
            $buyer_id_customer,
            Configuration::get('WK_MP_MANGOPAY_CURRENCY')
        );

        $obj_api->Config->ClientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        $obj_api->Config->ClientPassword = Configuration::get('WK_MP_MANGOPAY_PASSPHRASE');
        $obj_api->Config->TemporaryFolder = _PS_MODULE_DIR_.'mpmangopaypayment/temp/';
        try {
            $obj_api_transfer = new MangoPay\Transfer();
            $obj_api_transfer->AuthorId = $buyer_data['mgp_userid'];
            $obj_api_transfer->CreditedUserId = $mgp_seller_details['mgp_userid']; //seller mgp user id

            $obj_api_transfer->DebitedFunds = new MangoPay\Money();
            $obj_api_transfer->DebitedFunds->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $obj_api_transfer->DebitedFunds->Amount = $amount * 100;

            $obj_api_transfer->Fees = new MangoPay\Money();
            $obj_api_transfer->Fees->Currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
            $obj_api_transfer->Fees->Amount = $fee * 100;

            $obj_api_transfer->DebitedWalletId = $buyer_data['mgp_walletid'];
            $obj_api_transfer->CreditedWalletId = $mgp_seller_details['mgp_walletid']; //seller mgp wallet id
            $obj_api_transfer->Tag = 'Transfer from buyer wallet To sellers';
            $transfer_details = $obj_api->Transfers->Create($obj_api_transfer);
            $order_Id = $this->getOrderIdByIdSellerInCart($id_cart);
            if ($transfer_details->Status == 'SUCCEEDED') {
                $orderReference = $this->getOrderReferenceByIdSellerInCart($id_cart);
                $obj_mgp_transfer_details = new MangopayTransferDetails();
                $obj_mgp_transfer_details->order_reference = $orderReference;
                $obj_mgp_transfer_details->mgp_clientid = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
                $obj_mgp_transfer_details->buyer_id_customer = $buyer_id_customer;
                $obj_mgp_transfer_details->id_seller = $id_seller;
                $obj_mgp_transfer_details->currency = Configuration::get('WK_MP_MANGOPAY_CURRENCY');
                $obj_mgp_transfer_details->amount = $transfer_details->DebitedFunds->Amount / 100;
                $obj_mgp_transfer_details->fees = $transfer_details->Fees->Amount / 100;
                $obj_mgp_transfer_details->is_refunded = 0;
                $obj_mgp_transfer_details->refund_transfer_id = 0;
                $obj_mgp_transfer_details->transfer_id = $transfer_details->Id;
                if (!$obj_mgp_transfer_details->save()) {
                    error_log(
                        date('[Y-m-d H:i e] ').'MangopayTransferDetails Save Error : Error occured while making entry
                        with the details :: transfer id - '.$transfer_details->Id.', Seller Id - '.$id_seller.
                        ', order_Id - '.$order_Id.' , Function transferMangoPayAmount()'.PHP_EOL,
                        3,
                        _PS_MODULE_DIR_.'mpmangopaypayment/error.log'
                    );
                }
                return $transfer_details->Id;
            } else {
                error_log(
                    date('[Y-m-d H:i e] ').'MangopayTransferDetails Transfer Error : Error occured while creating the transfer :: transfer id - '.$transfer_details->Id.', Seller Id - '.$id_seller.
                    ', order_Id - '.$order_Id.' , Function transferMangoPayAmount()'.PHP_EOL,
                    3,
                    _PS_MODULE_DIR_.'mpmangopaypayment/error.log'
                );
            }
        } catch (\MangoPay\ResponseException $e) {
            error_log(
                date('[Y-m-d H:i e] ').'Mangopay transfer mangopay amoont error:  Error while transfering mangopay
                amount And Buyer id customer: '.$buyer_id_customer.PHP_EOL.'Error Message : '.$e->getMessage().
                PHP_EOL.PHP_EOL,
                3,
                _PS_MODULE_DIR_.'mangopayprestashop/error.log'
            );
        }
    }

    /**
     * Get order id by the cart.
     * @param int $id_cart
     * @return int
     */
    public function getOrderIdByIdSellerInCart($id_cart)
    {
        $cart_orders = $this->ordersByCartId($id_cart);
        $order_Id = '';
        if ($cart_orders) {
            foreach ($cart_orders as $order) {
                $order_Id = $order['id_order'];
                if ($order_Id) {
                    break;
                }
            }
        }
        return $order_Id;
    }

    /**
     * Get order reference by the cart.
     * @param int $id_cart
     * @return int
     */
    public function getOrderReferenceByIdSellerInCart($idCart)
    {
        $cartOrders = $this->ordersByCartId($idCart);
        $orderReference = '';
        if ($cartOrders) {
            foreach ($cartOrders as $order) {
                $orderReference = (new Order($order['id_order']))->reference;
                if ($orderReference) {
                    break;
                }
            }
        }
        return $orderReference;
    }

    /**
     * Get orders by cart id.
     * @return orders of the cart supplied or false
     */
    public function ordersByCartId($id_cart)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'orders` WHERE `id_cart` = '.(int) $id_cart);
    }

    /**
     * Create the mangopay order.
     * @param [type] $controller_obj
     * @param array $payment
     * @param int $order_status
     * @return void
     */
    public function mangopayOrderCreation($controller_obj, $payment, $order_status, $cart)
    {
        $total_amount = $cart->getOrderTotal(true);
        if ($cart->OrderExists()) {
            //If the order already exists, we need to update the order status

            $obj_order = new Order((int) Order::getOrderByCartId($cart->id));
            $new_history = new OrderHistory();
            $new_history->id_order = (int) $obj_order->id;
            //$new_history->changeIdOrderState((int) Configuration::get('PS_OS_PREPARATION'), $obj_order, true);
            $new_history->changeIdOrderState((int) Configuration::get('PS_OS_PAYMENT'), $obj_order, true);
            $new_history->addWithemail(true);
        } else {
            if ($controller_obj->module->validateOrder(
                (int) $cart->id,
                (int) $order_status,
                (float) $total_amount,
                $controller_obj->module->displayName,
                null,
                array(),
                null,
                false,
                $cart->secure_key
            )) {
                //Saving transaction details
                if ($this->saveTransactionDetails($payment, $controller_obj->module->currentOrderReference)) {
                    if (Configuration::get('WK_MP_MANGOPAY_TRANSFER_STATUS') == $order_status) {
                        $objTransfer = new MangopayTransferDetails();
                        $cartRules = $cart->getCartRules();
                        $cartProducts = $cart->getProducts();
                        $objSplitPayment = new WkMpSellerPaymentSplit();

                        foreach ($cartProducts as $key => $product) {
                            $order_product_data = $objTransfer->getOrderTimeProductData(
                                $cart->id,
                                $product['id_product'],
                                $product['id_product_attribute']
                            );
                            $cartProducts[$key]['price'] = $order_product_data['unit_price_tax_excl'];
                            $cartProducts[$key]['price_wt'] = $order_product_data['unit_price_tax_incl'];
                            $cartProducts[$key]['total'] = $order_product_data['total_price_tax_excl'];
                            $cartProducts[$key]['total_wt'] = $order_product_data['total_price_tax_incl'];
                        }
                        $custComm = $objSplitPayment->paymentGatewaySplitedAmount($cartRules, $cartProducts);
                        $objMgpTransaction = new MangopayTransaction();
                        $newCustComm = $objMgpTransaction->getCustomizedCustomerCommissionArray($custComm);
                        $objMgpTransaction->transferAmountsToWallets(
                            $newCustComm,
                            $cart->id,
                            (int) $cart->id_customer
                        );
                    }
                }
            }
        }
        Tools::redirect(
            'index.php?controller=order-confirmation&id_cart='.(int) $cart->id.'&id_module='.
            (int) $controller_obj->module->id.'&id_order='.$controller_obj->module->currentOrder.
            '&key='.$cart->secure_key
        );
    }

    public function getBankWirePayinByOrderReference($orderReference)
    {
        return Db::getInstance()->getRow(
            'SELECT mt.*, mbd.`id_mangopay_transaction`, mbd.`mgp_wire_reference`, mbd.`mgp_account_type`,
            mbd.`mgp_account_owner_name`, mbd.`mgp_account_iban`, mbd.`mgp_account_bic`, mbd.`declared_amount`
            FROM `'._DB_PREFIX_.'wk_mp_mangopay_transaction` mt
            JOIN `'._DB_PREFIX_.'wk_mp_mangopay_bankwire_details` mbd ON(mt.`id` = mbd.`id_mangopay_transaction`)
            WHERE mt.`order_reference` = \''.pSQL($orderReference).'\''
        );
    }

    public function getTransactionByPayInId($payInId)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_transaction` WHERE `transaction_id` = \''.pSQL($payInId).'\''
        );
    }
}
