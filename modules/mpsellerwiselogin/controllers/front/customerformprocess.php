<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpSellerWiseLoginCustomerFormProcessModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if ($this->context->cookie->logged) {
            $customer = new Customer($this->context->cookie->id_customer);
            $customer->logout();
        }

        if (Tools::isSubmit('registrationform')) {
            $this->registrationProcess();
        } elseif (Tools::isSubmit('loginform')) {
            $this->loginProcess();
        }
    }

    public function loginProcess()
    {
        Hook::exec('actionBeforeAuthentication');

        $passwd = trim(Tools::getValue('passwd'));
        $email = trim(Tools::getValue('email'));
        $customer = new Customer();
        $link = new link();

        if (empty($email)) {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 1], true));
        } elseif (!Validate::isEmail($email)) {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 2], true));
        } elseif (empty($passwd)) {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 3], true));
        } elseif (!Validate::isPasswd($passwd)) {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 4], true));
        } else {
            $authentication = $customer->getByEmail(trim($email), trim($passwd));
            if (!$authentication) {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 5], true));
            } else {
                if (isset($authentication->active) && !$authentication->active) {
                    Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 6], true));
                } elseif (!$authentication || !$customer->id) {
                    Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 7], true));
                } else {
                    $obj_seller_detail = new WkMpSeller();
                    $seller_detail = $obj_seller_detail->getSellerDetailByCustomerId($customer->id);
                    if (!$seller_detail) {
                        Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 22], true));
                    } else {
                        $this->context->cookie->id_customer = (int) ($customer->id);
                        $this->context->cookie->customer_lastname = $customer->lastname;
                        $this->context->cookie->customer_firstname = $customer->firstname;
                        $this->context->cookie->logged = 1;
                        $customer->logged = 1;
                        $this->context->cookie->is_guest = $customer->isGuest();
                        $this->context->cookie->passwd = $customer->passwd;
                        $this->context->cookie->email = $customer->email;

                        // Add customer to the context
                        $this->context->customer = $customer;

                        if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int) Cart::lastNoneOrderedCart($this->context->customer->id)) {
                            $this->context->cart = new Cart($id_cart);
                        } else {
                            $id_carrier = (int) $this->context->cart->id_carrier;
                            $this->context->cart->id_carrier = 0;
                            $this->context->cart->setDeliveryOption(null);
                            $this->context->cart->id_address_delivery = (int) Address::getFirstCustomerAddressId((int) ($customer->id));
                            $this->context->cart->id_address_invoice = (int) Address::getFirstCustomerAddressId((int) ($customer->id));
                        }

                        $this->context->cart->id_customer = (int) $customer->id;
                        $this->context->cart->secure_key = $customer->secure_key;

                        if ($this->ajax && isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                            $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
                            $this->context->cart->setDeliveryOption($delivery_option);
                        }

                        $this->context->cart->save();
                        $this->context->cookie->id_cart = (int) $this->context->cart->id;
                        $this->context->cookie->write();
                        $this->context->cart->autosetProductAddress();

                        Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);

                        // Login information have changed, so we check if the cart rules still apply
                        CartRule::autoRemoveFromCart($this->context);
                        CartRule::autoAddToCart($this->context);

                        Tools::redirect($link->getModuleLink('marketplace', 'dashboard'));
                    }
                }
            }
        }
    }

    public function registrationProcess()
    {
        $link = new link();

        $customer_id = Tools::getValue('ps_customer_id');
        if (!$customer_id) {
            Hook::exec('actionBeforeSubmitAccount');

            $seller_firstname = Tools::getValue('firstname');
            $seller_lastname = Tools::getValue('lastname');
            $seller_email = Tools::getValue('email');
            $seller_pass = Tools::getValue('passwd');

            if (empty($seller_firstname)) {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 8], true));
            } elseif (empty($seller_lastname)) {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 9], true));
            } elseif (!Validate::isEmail($seller_email) || empty($seller_email)) {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 2], true));
            } elseif (Customer::customerExists($seller_email)) {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 11], true));
            } elseif (empty($seller_pass)) {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 4], true));
            }
        } else {
            $customer = new Customer((int) $customer_id);

            $seller_firstname = $customer->firstname;
            $seller_lastname = $customer->lastname;
            $seller_email = $customer->email;
        }

        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $default_lang = Tools::getValue('seller_default_lang');
        } else {
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {
                $default_lang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {
                $default_lang = Tools::getValue('current_lang');
            }
        }

        $shop_name_unique = trim(Tools::getValue('mp_shop_name_unique'));
        $shop_name = trim(Tools::getValue('mp_shop_name_'.$default_lang));
        $phone = trim(Tools::getValue('mp_seller_phone'));

        $country = trim(Tools::getValue('seller_country'));
        $state = trim(Tools::getValue('seller_state'));
        $city = trim(Tools::getValue('seller_city'));

        $languages = Language::getLanguages();
        $shop_name_error = 0;
        foreach ($languages as $language) {
            if (!Validate::isCatalogName(Tools::getValue('shop_name_'.$language['id_lang']))) {
                $shop_name_error = 1;
            }
        }

        if ($shop_name == '') {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 21], true));
            } else {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 13], true));
            }
        } elseif ($shop_name_error == 1) {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 14], true));
        }

        if ($shop_name_unique == '') {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 19], true));
        } elseif (!Validate::isCatalogName($shop_name_unique)) {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 20], true));
        } elseif (WkMpSeller::isShopNameExist($shop_name_unique)) {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 15], true));
        } elseif ($phone == '') {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 16], true));
        } elseif (!Validate::isPhoneNumber($phone)) {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 17], true));
        } elseif (WkMpSeller::isSellerEmailExist($seller_email)) {
            Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 18], true));
        } elseif (Configuration::get('WK_MP_SELLER_COUNTRY_NEED')) {
            if ($city == '') {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 23], true));
            } elseif (!Validate::isName($city)) {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 24], true));
            }

            if (!$country) {
                Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 25], true));
            }

            if (Tools::getValue('state_avl')) { //if state available in selected country
                if (!$state) {
                    Tools::redirect($link->getModuleLink('mpsellerwiselogin', 'sellerlogin', ['error' => 26], true));
                }
            }
        }

        Hook::exec('actionBeforeAddSeller');

        if (empty($customer_id)) {
            $customer = new Customer();
            $customer->validateController();
            $customer->email = Tools::getValue('email');
            $customer->lastname = Tools::getValue('lastname');
            $customer->firstname = Tools::ucwords(Tools::getValue('firstname'));
            $customer->is_guest = 0;
            $customer->active = 1;
            $customer->save();

            $customer_id = $customer->id;
            $this->sendConfirmationMail($customer_id); // mail to customer when their account created successfully
            
            $this->contextUpdate($customer);
            $this->context->cart->update();

            Hook::exec('actionCustomerAccountAdd', array(
                '_POST' => $_POST,
                'newCustomer' => $customer,
            ));
        } else {
            $this->contextUpdate($customer);
            $this->context->cart->update();
        }

        $obj_seller_detail = new WkMpSeller();
        $obj_seller_detail->shop_name_unique = $shop_name_unique;
        $obj_seller_detail->business_email = $seller_email;
        $obj_seller_detail->seller_firstname = $seller_firstname;
        $obj_seller_detail->seller_lastname = $seller_lastname;
        $obj_seller_detail->link_rewrite = Tools::link_rewrite($shop_name_unique);
        $obj_seller_detail->phone = $phone;
        if (Configuration::get('WK_MP_SELLER_COUNTRY_NEED')) {
            $obj_seller_detail->city = $city;
            $obj_seller_detail->id_country = $country;
            $obj_seller_detail->id_state = $state;
        }
        $obj_seller_detail->default_lang = Tools::getValue('seller_default_lang');

        $defaultLang = Tools::getValue('seller_default_lang');
        $shopName = trim(Tools::getValue('mp_shop_name_'.$defaultLang));

        if (Configuration::get('WK_MP_SELLER_ADMIN_APPROVE') == 0) {
            $active = 1;
        } else {
            $active = 0;
        }
        $obj_seller_detail->active = $active;
        $obj_seller_detail->shop_approved = $active;
        $obj_seller_detail->seller_customer_id = $customer_id;

        if (Configuration::get('WK_MP_SHOW_SELLER_DETAILS')) {
            //display all seller details for new seller
            $obj_seller_detail->seller_details_access = Configuration::get('WK_MP_SELLER_DETAILS_ACCESS');
        }

        foreach (Language::getLanguages(true) as $language) {
            $shop_lang_id = $language['id_lang'];
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                //if shop name in other language is not available then fill with seller language same for others
                if (!Tools::getValue('mp_shop_name_'.$language['id_lang'])) {
                    $shop_lang_id = $default_lang;
                }
            } else {
                //if multilang is OFF then all fields will be filled as default lang content
                $shop_lang_id = $default_lang;
            }
            $obj_seller_detail->shop_name[$language['id_lang']] = Tools::getValue('mp_shop_name_'.$shop_lang_id);
        }
        $obj_seller_detail->save();

        $idSeller = $obj_seller_detail->id;

        //If seller default active approval is ON then mail to seller of account activation
        if ($idSeller && $obj_seller_detail->active) {
            WkMpSeller::sendMail($idSeller, 1, 1);
        }

        //Mail to Admin on seller request
        $sellerName = $seller_firstname.' '.$seller_lastname;
        $obj_seller_detail->mailToAdminWhenSellerRequest($sellerName, $shopName, $seller_email, $phone);

        Hook::exec('actionAfterAddSeller', array('id_seller' => $idSeller));

        Tools::redirect($link->getModuleLink('marketplace', 'dashboard'));
    }

    public function contextUpdate(Customer $customer)
    {
        $this->context->customer = $customer;
        $this->context->smarty->assign('confirmation', 1);
        $this->context->cookie->id_customer = (int) $customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        // if register process is in two steps, we display a message to confirm account creation
        if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE')) {
            $this->context->cookie->account_created = 1;
        }
        $customer->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
        // Update cart address
        $this->context->cart->secure_key = $customer->secure_key;
    }
    
    public function sendConfirmationMail($id_customer)
    {
        if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            return true;
        }

        $id_lang = $this->context->language->id;
        $customer = new Customer($id_customer);
        Mail::Send(
            (int) $id_lang,
            'account',
            Mail::l('Welcome!', (int) $id_lang),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => Tools::getValue('passwd')),
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            false,
            null,
            null
        );
    }
}
