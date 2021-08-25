<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkGdprHelper extends HTMLTemplate
{
    const WK_GDPR_MAIL_DATA_UPDATE = 1;
    const WK_GDPR_MAIL_DATA_ERASURE = 2;
    const WK_GDPR_MAIL_DATA_ACCESS = 3;

    public static function searchCustomer($queryTxt)
    {
        $sql = 'SELECT c.*
            FROM `'._DB_PREFIX_.'customer` AS c
            WHERE (
                c.`email` LIKE "%'.pSQL($queryTxt).'%"
                OR c.`firstname` LIKE "%'.pSQL($queryTxt).'%"
                OR c.`lastname` LIKE "%'.pSQL($queryTxt).'%"
                OR CONCAT(c.`firstname`, " ", c.`lastname`) LIKE "%'.pSQL($queryTxt).'%"
            ) AND c.`id_customer` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'wk_gdpr_anonymous_customer`)';
        return Db::getInstance()->executeS($sql);
    }

    public static function assignDataTableVariables()
    {
        $objWkGdpr = new WkGdpr();
        $jsVars = array(
                'display_name' => $objWkGdpr->l('Display', 'WkGdprHelper'),
                'records_name' => $objWkGdpr->l('records per page', 'WkGdprHelper'),
                'no_product' => $objWkGdpr->l('No records found', 'WkGdprHelper'),
                'show_page' => $objWkGdpr->l('Showing page', 'WkGdprHelper'),
                'show_of' => $objWkGdpr->l('of', 'WkGdprHelper'),
                'no_record' => $objWkGdpr->l('No records available', 'WkGdprHelper'),
                'filter_from' => $objWkGdpr->l('filtered from', 'WkGdprHelper'),
                't_record' => $objWkGdpr->l('total records', 'WkGdprHelper'),
                'search_item' => $objWkGdpr->l('Search', 'WkGdprHelper'),
                'p_page' => $objWkGdpr->l('Previous', 'WkGdprHelper'),
                'n_page' => $objWkGdpr->l('Next', 'WkGdprHelper'),
            );

        Media::addJsDef($jsVars);
    }

    public static function getCustomerDetailById($idCustomer)
    {
        $sql = 'SELECT *
				FROM `'._DB_PREFIX_.'customer`
				WHERE `id_customer` = '.(int)$idCustomer;
        return Db::getInstance()->getRow($sql);
    }

    public static function getCustomerData($idCustomer)
    {
        if (!$idCustomer) {
            return true;
        }

        $context = Context::getContext();

        $objCustomer = new Customer((int)$idCustomer);
        $objGender = new Gender($objCustomer->id_gender, $context->language->id);
        $customerData = array();
        $customer = self::getCustomerDetailById($idCustomer);
        $customer['stats'] = $objCustomer->getStats();
        $customer['language'] = Language::getLanguage($objCustomer->id_lang);
        $customer['gender'] = $objGender->name;

        $customerData['personalInfo'] = $customer;
        // Addresses
        $customerData['addresses'] = $objCustomer->getAddresses($context->language->id);

        // Orders
        $customerData['orders'] = Order::getCustomerOrders($objCustomer->id);
        if ($customerData['orders']) {
            foreach ($customerData['orders'] as &$order) {
                $currency = new Currency($order['id_currency']);
                $order['formated_total_paid_tax_incl'] = Tools::displayPrice(
                    $order['total_paid_tax_incl'],
                    $currency
                );
                $objOrder = new Order($order['id_order']);
                $orderProducts = $objOrder->getProducts();

                if ($orderProducts) {
                    $order['products'] = array();
                    foreach ($orderProducts as $product) {
                        $order['products'][] = array(
                            'id_product' => $product['product_id'],
                            'id_product_attribute' => $product['product_attribute_id'],
                            'name' => $product['product_name'],
                            'qty' => $product['product_quantity'],
                            'reference' => $product['product_reference'],
                            'price' => Tools::displayPrice($product['total_price_tax_incl'], $currency),
                        );
                    }
                }
            }
        }

        // Cart (non-ordered cart)
        $customerData['carts'] = Cart::getCustomerCarts($objCustomer->id, false);
        if ($customerData['carts']) {
            foreach ($customerData['carts'] as &$cart) {
                $objCart = new Cart($cart['id_cart']);
                $currency = new Currency($cart['id_currency']);
                $cartProducts = $objCart->getProducts();

                $cart['nb_products'] = count($cartProducts);
                $cart['formated_total_tax_incl'] = Cart::getTotalCart($cart['id_cart'], true);
                $cart['products'] = array();

                if ($cartProducts) {
                    foreach ($cartProducts as $product) {
                        $cart['products'][] = array(
                            'id_product' => $product['id_product'],
                            'id_product_attribute' => $product['id_product_attribute'],
                            'name' => $product['name'],
                            'attributes' => isset($product['attributes']) ? $product['attributes'] : 0,
                            'qty' => $product['cart_quantity'],
                            'reference' => isset($product['reference']) ? $product['reference'] : 0,
                            'price' => Tools::displayPrice($product['total_wt'], $currency),
                        );
                    }
                }
            }
        }

        // Messages
        $customerData['messages'] = CustomerThread::getCustomerMessages($objCustomer->id);
        if ($customerData['messages']) {
            foreach ($customerData['messages'] as &$message) {
                $message['ip_address'] = long2ip($message['ip_address']);
            }
        }

        // Connections
        $customerData['connections'] = $objCustomer->getLastConnections();


        // Module Data
        $customerData['modules'] = self::getModuleCustomerData($customer);
        unset($objCustomer);
        unset($objGender);
        return $customerData;
    }

    public static function getModuleCustomerData($customer)
    {
        $gdprComplianceModules = Hook::getHookModuleExecList('actionExportGDPRData');
        if (!$gdprComplianceModules) {
            return false;
        }

        $objCustomer = new Customer((int)$customer['id_customer']);

        $moduleData = array();
        $gdprModuleData = Hook::exec('actionExportGDPRData', (array)$objCustomer, null, true);
        if ($gdprModuleData) {
            foreach ($gdprModuleData as $moduleName => $data) {
                $moduleInstance = Module::getInstanceByName($moduleName);
                $data = json_decode($data, true);

                $moduleData[$moduleName] = array(
                    'displayName' => $moduleInstance->displayName,
                    'data' => $data,
                );
            }
        }
        return $moduleData;
    }

    public static function deleteCustomerData($idCustomer)
    {
        if (!$idCustomer) {
            return false;
        }

        $customer = new Customer((int)$idCustomer);
        $customer = (array)$customer;

        // $customer = self::getCustomerDetailById($idCustomer);

        // if deleted the send an email to the customer for data erasure
        WkGdprHelper::sendGdprEmails(WkGdprHelper::WK_GDPR_MAIL_DATA_ERASURE, $idCustomer, 1);

        self::deleteCustomerDataFromModule($customer);
        self::makePrestashopCustomerDataAnonymous($customer);

        $objAnonymousCustomer = new WkGdprAnonymousCustomer();
        $objAnonymousCustomer->id_customer = $idCustomer;
        if ($objAnonymousCustomer->save()) {
            $objGDPRCustReq = new WkGdprCustomerRequests();
            if ($deleteReq = $objGDPRCustReq->getGDPRCustomerRequests(
                $idCustomer,
                WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_TYPE_DELETE
            )) {
                $objGDPRCustReq = new WkGdprCustomerRequests($deleteReq['id_request']);
                $objGDPRCustReq->status = WkGdprCustomerRequests::WK_CUSTOMER_REQUEST_STATE_DONE;
                $objGDPRCustReq->save();
            }

            return true;
        }

        return false;
    }

    public static function deleteCustomerDataFromModule($customer)
    {
        $hookModuleList = Hook::getHookModuleExecList('actionDeleteGDPRCustomer');
        if ($hookModuleList) {
            Hook::exec('actionDeleteGDPRCustomer', $customer);
        }

        return true;
    }

    public static function makePrestashopCustomerDataAnonymous($customer)
    {
        // Make Customer personal Data anonymous
        Db::getInstance()->update(
            'customer',
            array(
                'company' => '',
                'siret' => '',
                'ape' => '',
                'firstname' => 'Anonymous',
                'lastname' => 'Anonymous',
                'email' => 'anonymous'.(int)$customer["id"].'@anonymous.com',
                'birthday' => '0000-00-00',
                'newsletter' => 0,
                'website' => ''
            ),
            'id_customer = '.(int)$customer["id"]
        );

        // Make Customer address Data anonymous
        $customer = new Customer((int)$customer["id"]);
        $context = Context::getContext();
        $customerAddresses = $customer->getAddresses($context->language->id);
        if ($customerAddresses) {
            foreach ($customerAddresses as $address) {
                $fields = array(
                    'company' => '',
                    'firstname' => 'Anonymous',
                    'lastname' => 'Anonymous',
                    'address1' => 'Anonymous',
                    'city' => 'Anonymous'
                );
                $fields['address2'] = $address['address2'] ? 'Anonymous' : $address['address2'];
                $fields['phone'] = $address['phone'] ? '0000000000' : $address['phone'];
                $fields['phone_mobile'] = $address['phone_mobile'] ? '0000000000' : $address['phone_mobile'];

                Db::getInstance()->update(
                    'address',
                    $fields,
                    'id_address = '.(int)$address["id_address"]
                );
            }
        }

        // Customer threads
        $customerThreads = CustomerThread::getCustomerMessages($customer->id);
        if ($customerThreads) {
            $idCustomerThreads = array();
            foreach ($customerThreads as $thread) {
                $idCustomerThreads[] = $thread['id_customer_thread'];
            }
            $idCustomerThreads = array_unique($idCustomerThreads);
            foreach ($idCustomerThreads as $idCustomerThread) {
                $objCustomerThread = new CustomerThread((int)$idCustomerThread);
                $objCustomerThread->email = 'anonymous'.(int)$customer->id.'@anonymous.com';
                $objCustomerThread->save();
            }
        }


        return true;
    }

    public function generateGdprPDF($idCustomer, $download = true)
    {
        if (!$idCustomer) {
            return false;
        }

        $this->context = Context::getContext();
        $this->id_customer = $idCustomer;
        $this->context->smarty->escape_html = false;

        $objPdfRenderer = new PDFGenerator((bool) Configuration::get('PS_PDF_USE_CACHE'), 'P');
        $objPdfRenderer->setFontForLang($this->context->language->iso_code);
        $objPdfRenderer->createHeader($this->getGdprPdfHeader());
        $objPdfRenderer->createFooter($this->getGdprPdfFooter());
        $objPdfRenderer->createContent($this->getContent());
        $objPdfRenderer->writePage();

        // clean the output buffer
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        $fileAttachement = array();
        $fileAttachement['content'] = $objPdfRenderer->render($this->getFilename(), $download);
        $fileAttachement['name'] = $this->getFilename();
        $fileAttachement['invoice']['mime'] = 'application/pdf';
        $fileAttachement['mime'] = 'application/pdf';


        return $fileAttachement;
    }

    public function getGdprPdfHeader()
    {
        $idShop = (int) $this->context->shop->id;

        if (Configuration::get('PS_LOGO_INVOICE', null, null, $idShop) != false
            && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $idShop))
        ) {
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $idShop);
        } elseif (Configuration::get('PS_LOGO', null, null, $idShop) != false
            && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $idShop))
        ) {
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $idShop);
        }

        $width = 0;
        $height = 0;
        if (!empty($logo)) {
            list($width, $height) = getimagesize($logo);
        }

        // Limit the height of the logo for the PDF render
        $maximumHeight = 100;
        if ($height > $maximumHeight) {
            $ratio = $maximumHeight / $height;
            $height *= $ratio;
            $width *= $ratio;
        }

        $this->context->smarty->assign(array(
            'logo_path' => $logo,
            'date' => date('Y-m-d'),
            'width_logo' => $width,
            'height_logo' => $height,
        ));

        return $this->context->smarty->fetch($this->getTemplate('gdprinvoice.header'));
    }

    public function getGdprPdfFooter()
    {
        $shopName = Configuration::get('PS_SHOP_NAME');
        $this->context->smarty->assign(array(
            'shop_name' => $shopName,
        ));

        return $this->context->smarty->fetch($this->getTemplate('gdprinvoice.footer'));
    }

    public function getContent()
    {
        $idCustomer = $this->id_customer;
        $customerData = self::getCustomerData($idCustomer);
        $this->context->smarty->assign($customerData);

        $tpls = array(
            'style_tab' => $this->context->smarty->fetch($this->getTemplate('gdprInvoice.style-tab')),
            'personalInfo_tab' => $this->context->smarty->fetch($this->getTemplate('gdprinvoice.personalInfo-tab')),
            'addresses_tab' => $this->context->smarty->fetch($this->getTemplate('gdprinvoice.addresses-tab')),
            'orders_tab' => $this->context->smarty->fetch($this->getTemplate('gdprinvoice.orders-tab')),
            'carts_tab' => $this->context->smarty->fetch($this->getTemplate('gdprinvoice.carts-tab')),
            'messages_tab' => $this->context->smarty->fetch($this->getTemplate('gdprinvoice.messages-tab')),
            'connections_tab' => $this->context->smarty->fetch($this->getTemplate('gdprinvoice.connections-tab')),
            'modules_tab' => $this->context->smarty->fetch($this->getTemplate('gdprinvoice.modules-tab')),
        );
        $this->context->smarty->assign($tpls);

        return $this->context->smarty->fetch($this->getTemplate('gdprinvoice'));
    }

    public function getFilename()
    {
        return 'Customer-GDPR-Data.pdf';
    }

    public function getBulkFilename()
    {
        return 'invoices.pdf';
    }

    public function getTemplate($template)
    {
        $path = _PS_MODULE_DIR_.'wkgdpr/views/templates/front/pdf/'.$template.'.tpl';

        return $path;
    }

    /**
     * Get Super Admin Of Prestashop
     * @return int Super Admin Employee ID
     */
    public static function getSupperAdmin()
    {
        $data = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'employee` ORDER BY `id_employee`');
        if ($data) {
            foreach ($data as $emp) {
                $employee = new Employee($emp['id_employee']);
                if ($employee->isSuperAdmin()) {
                    return $emp['id_employee'];
                }
            }
        }

        return false;
    }

    public static function sendGdprEmails($mailType, $idCustomer = 0, $sendTo = 1, $idRequest = 0, $toEmail = 0)
    {
        $context = Context::getContext();
        $mailParams = array();
        if ($idCustomer) {
            if (Validate::isLoadedObject($objCustomer = new Customer($idCustomer))) {
                $customerName = $objCustomer->firstname.' '.$objCustomer->lastname;
            }
        }
        $mailParams['{customer_name}'] = $customerName;
        $mailParams['{customer_email}'] = $objCustomer->email;
        $mailParams['{customer_request_content}'] = '';
        if ($idRequest) {
            if (Validate::isLoadedObject($objGDPRCustReq = new WkGdprCustomerRequests($idRequest))) {
                $mailParams['{customer_request_content}'] = $objGDPRCustReq->request_reason;
            }
        }
        // Send email to the customer
        if ($idCustomer && ($sendTo == 1 || $sendTo == 0)) {
            $sendMail = 0;
            $attachment = null;
            if ($mailType == self::WK_GDPR_MAIL_DATA_UPDATE) {
                $mailTemplateFile = 'data_update_request_customer';
                $mailSubject = Mail::l('Pesonal Data Update Request', $context->language->id);
                $sendMail = 1;
            } elseif ($mailType == self::WK_GDPR_MAIL_DATA_ERASURE) {
                $mailTemplateFile = 'data_erasure_request_customer';
                $mailSubject = Mail::l('Pesonal Data Erasure Request', $context->language->id);
                $sendMail = 1;
            } elseif ($mailType == self::WK_GDPR_MAIL_DATA_ACCESS) {
                $mailTemplateFile = 'data_access_request_customer';
                $mailSubject = Mail::l('Pesonal Data Access Request', $context->language->id);
                $sendMail = 1;
                $objHelper = new WkGdprHelper();
                $attachment = $objHelper->generateGdprPDF($idCustomer, false);
            }
            if ($sendMail) {
                if (!$toEmail) {
                    $toEmail = $objCustomer->email;
                }
                if (!Mail::Send(
                    $context->language->id,
                    $mailTemplateFile, //Specify the template file name
                    $mailSubject, //Mail subject with translation
                    $mailParams,
                    $toEmail,
                    null,
                    null,
                    null,
                    $attachment,
                    null,
                    _PS_MODULE_DIR_.'wkgdpr/mails/',
                    false,
                    null,
                    null
                )
                ) {
                    error_log(
                        date('[Y-m-d H:i e] ').'GDPR customer mail error for parameters
                        idCustomer= '.$idCustomer.' and sendTo = '.$sendTo.' and mailType = '.$mailType.PHP_EOL,
                        3,
                        _PS_MODULE_DIR_.'wkgdpr/error.log'
                    );
                }
            }
        }
        //Send email to the admin
        if ((($sendTo == 2 || $sendTo == 0) // check for customer mails
            && (($mailType == self::WK_GDPR_MAIL_DATA_UPDATE
            && Configuration::get('WK_GDPR_ADMIN_MAIL_DATA_UPDATE_REQUEST'))
            || ($mailType == self::WK_GDPR_MAIL_DATA_ERASURE
            && Configuration::get('WK_GDPR_ADMIN_MAIL_DATA_ERASURE_REQUEST'))))
        ) {
            $sendMail = 0;
            if ($mailType == self::WK_GDPR_MAIL_DATA_UPDATE) {
                $mailTemplateFile = 'data_update_request_admin';
                $mailSubject = Mail::l('Pesonal Data Update Request', $context->language->id);
                $sendMail = 1;
            } elseif ($mailType == self::WK_GDPR_MAIL_DATA_ERASURE) {
                $mailTemplateFile = 'data_erasure_request_admin';
                $mailSubject = Mail::l('Pesonal Data Erasure Request', $context->language->id);
                $sendMail = 1;
            }
            $superAdminId = WkGdprHelper::getSupperAdmin();
            if ($sendMail && $superAdminId && Validate::isLoadedObject($superAdmin = new Employee($superAdminId))) {
                $mailParams['{admin_name}'] = $superAdmin->firstname.' '.$superAdmin->lastname;
                if (!$toEmail) {
                    $toEmail = $superAdmin->email;
                }
                if (!Mail::Send(
                    $context->language->id,
                    $mailTemplateFile, //Specify the template file name
                    $mailSubject, //Mail subject with translation
                    $mailParams,
                    $toEmail,
                    null,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_.'wkgdpr/mails/',
                    false,
                    null,
                    null
                )
                ) {
                    error_log(
                        date('[Y-m-d H:i e] ').'GDPR admin mail error for parameters
                        idCustomer= '.$idCustomer.' and sendTo = '.$sendTo.' and mailType = '.$mailType.PHP_EOL,
                        3,
                        _PS_MODULE_DIR_.'wkgdpr/error.log'
                    );
                }
            }
        }
        return true;
    }
}
