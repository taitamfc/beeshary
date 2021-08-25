<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Quick Login And Social Login
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

use PrestaShop\PrestaShop\Core\Crypto\Hashing;

//use PrestaShop\PrestaShop\Core\Crypto\Hashing as Crypto;

class LeoQuickloginLeoCustomerModuleFrontController extends ModuleFrontController
{

    public $php_self;

    public function displayAjax()
    {
        // Add or remove product with Ajax
        $context = Context::getContext();
        $action = Tools::getValue('action');

        $array_result = array();
        $errors = array();
        $success = array();
        // Instance of module class for translations
        //DONGND:: reset password
        if ($action == 'reset-pass') {
            //DONGND:: check validate
            if (!($email = trim(Tools::getValue('lql-email-reset'))) || !Validate::isEmail($email)) {
                $errors[] = $this->l('Invalid email address', 'leocustomer');
            } else {
                //DONGND:: check email exist
                $customer = new Customer();
                $customer->getByEmail($email);
                if (is_null($customer->email)) {
                    $customer->email = $email;
                }

                if (!Validate::isLoadedObject($customer)) {
                    $errors[] = $this->l('This email is not registered as an account', 'leocustomer');
                } elseif (!$customer->active) {
                    $errors[] = $this->l('You cannot regenerate the password for this account.', 'leocustomer');
                } elseif ((strtotime($customer->last_passwd_gen . '+' . ($minTime = (int) Configuration::get('PS_PASSWD_TIME_FRONT')) . ' minutes') - time()) > 0) {
                    $errors[] = $this->l('You can regenerate your password only every ', 'leocustomer') . (int) $minTime . $this->l(' minute(s)', 'leocustomer');
                } else {
                    if (!$customer->hasRecentResetPasswordToken()) {
                        $customer->stampResetPasswordToken();
                        $customer->update();
                    }

                    //DONGND:: send mail to reset password
                    $mailParams = array(
                        '{email}' => $customer->email,
                        '{lastname}' => $customer->lastname,
                        '{firstname}' => $customer->firstname,
                        '{url}' => $this->context->link->getPageLink('password', true, null, 'token=' . $customer->secure_key . '&id_customer=' . (int) $customer->id . '&reset_token=' . $customer->reset_password_token),
                    );

                    if (Mail::Send($this->context->language->id, 'password_query', $this->l('Password query confirmation', 'leocustomer'), $mailParams, $customer->email, $customer->firstname . ' ' . $customer->lastname)) {
                        $success[] = $this->l('If this email address has been registered in our shop, you will receive a link to reset your password at ', 'leocustomer');
                    } else {
                        $errors[] = $this->l('An error occurred while sending the email.', 'leocustomer');
                    }
                }
            }
        }

        //DONGND:: customer login
        if ($action == 'customer-login') {
            //DONGND:: check validate
            if (!($email = trim(Tools::getValue('lql_email_login'))) || !Validate::isEmail($email)) {
                $errors[] = $this->l('Invalid email address', 'leocustomer');
            }
            if (!($pass = trim(Tools::getValue('lql_pass_login'))) || !Validate::isPasswd($pass)) {
                $errors[] = $this->l('Invalid password', 'leocustomer');
            }
            if (!Tools::getValue('lql_remember_login') && Configuration::get('LEOQUICKLOGIN_ENABLE_CHECKCOOKIE') && (int) Configuration::get('LEOQUICKLOGIN_LIFETIME_COOKIE') != 0) {
                $this->context->cookie->customer_last_activity = time();
            }
            if (!count($errors)) {
                Hook::exec('actionAuthenticationBefore');

                //DONGND:: check email exist
                $customer = new Customer();
                $authentication = $customer->getByEmail($email, $pass);

                if (isset($authentication->active) && !$authentication->active) {
                    $errors[] = $this->l('Your account isn\'t available at this time, please contact us', 'leocustomer');
                } elseif (!$authentication || !$customer->id || $customer->is_guest) {
                    $errors[] = $this->l('Authentication failed.', 'leocustomer');
                } else {
                    //DONGND:: update cookie to login
                    $this->context->updateCustomer($customer);

                    Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

                    // Login information have changed, so we check if the cart rules still apply
                    CartRule::autoRemoveFromCart($this->context);
                    CartRule::autoAddToCart($this->context);
                    $success[] = $this->l('You have successfully logged in', 'leocustomer');
                }
            }
        }

        //DONGND:: create new account
        if ($action == 'create-account') {
            //DONGND:: check validate
            if (!($email = trim(Tools::getValue('lql-register-email'))) || !Validate::isEmail($email)) {
                $errors[] = $this->l('Invalid email address', 'leocustomer');
            }
            if (!($pass = trim(Tools::getValue('lql-register-pass'))) || !Validate::isPasswd($pass)) {
                $errors[] = $this->l('Invalid password', 'leocustomer');
            }

            if (!($firstname = trim(Tools::getValue('lql-register-firstname'))) || !Validate::isName($firstname)) {
                $errors[] = $this->l('Invalid first name', 'leocustomer');
            }
            if (!($lastname = trim(Tools::getValue('lql-register-lastname'))) || !Validate::isName($lastname)) {
                $errors[] = $this->l('Invalid last name', 'leocustomer');
            }

            if (!count($errors)) {
                //DONGND:: check email exist to register
                if (Customer::customerExists($email, true, true)) {
                    $errors[] = $this->l('This email is already used, please choose another one or sign in', 'leocustomer');
                } else {
                    $hookResult = array_reduce(
                        Hook::exec('actionSubmitAccountBefore', array(), null, true),
                        function ($carry, $item) {
                            return $carry && $item;
                        },
                        true
                    );
                    //DONGND:: add new account
                    $customer = new Customer();
                    $customer->firstname = $firstname;
                    $customer->lastname = $lastname;
                    $customer->email = $email;
                    $customer->passwd = $this->get('hashing')->hash($pass, _COOKIE_KEY_);

                    if ($hookResult && $customer->save()) {
                        $this->context->updateCustomer($customer);
                        $this->context->cart->update();
                        //DONGND:: send mail new account
                        if (!$customer->is_guest && Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
                            Mail::Send($this->context->language->id, 'account', $this->l('Welcome!', 'leocustomer'), array(
                                '{firstname}' => $customer->firstname,
                                '{lastname}' => $customer->lastname,
                                '{email}' => $customer->email), $customer->email, $customer->firstname . ' ' . $customer->lastname);
                        }

                        Hook::exec('actionCustomerAccountAdd', array(
                            'newCustomer' => $customer,
                        ));
                        $success[] = $this->l('You have successfully created a new account', 'leocustomer');
                    } else {
                        $errors[] = $this->l('An error occurred while creating the new account.', 'leocustomer');
                    }
                }
            }
        }

        //DONGND:: create new account
        if ($action == 'social-login') {
            if (!($email = trim(Tools::getValue('email'))) || !Validate::isEmail($email)) {
                $errors[] = $this->l('Invalid email address', 'leocustomer');
            }

            if (!($firstname = trim(Tools::getValue('first_name'))) || !Validate::isName($firstname)) {
                $errors[] = $this->l('Invalid first name', 'leocustomer');
            }
            if (!($lastname = trim(Tools::getValue('last_name'))) || !Validate::isName($lastname)) {
                $errors[] = $this->l('Invalid last name', 'leocustomer');
            }

            if (!count($errors)) {
                if (Customer::customerExists($email, true, true)) {
                    Hook::exec('actionAuthenticationBefore');

                    //DONGND:: check email exist
                    $customer = new Customer();
                    $authentication = $customer->getByEmail(Tools::getValue('email'), null, true);

                    if (isset($authentication->active) && !$authentication->active) {
                        $errors[] = $this->l('Your account isn\'t available at this time, please contact us', 'leocustomer');
                    } elseif (!$authentication || !$customer->id || $customer->is_guest) {
                        $errors[] = $this->l('Authentication failed.', 'leocustomer');
                    } else {
                        //DONGND:: update cookie to login
                        $this->context->updateCustomer($customer);

                        Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

                        // Login information have changed, so we check if the cart rules still apply
                        CartRule::autoRemoveFromCart($this->context);
                        CartRule::autoAddToCart($this->context);
                        $success[] = $this->l('You have successfully logged in', 'leocustomer');
                    }
                } else {
                    $hookResult = array_reduce(
                        Hook::exec('actionSubmitAccountBefore', array(), null, true),
                        function ($carry, $item) {
                            return $carry && $item;
                        },
                        true
                    );
                    //DONGND:: add new account
                    $customer = new Customer();
                    $customer->firstname = $firstname;
                    $customer->lastname = $lastname;
                    $customer->email = $email;
                    $password = Tools::passwdGen();
                    $customer->passwd = $this->get('hashing')->hash($password, _COOKIE_KEY_);

                    if ($hookResult && $customer->save()) {
                        $this->context->updateCustomer($customer);
                        $this->context->cart->update();
                        //DONGND:: send mail new account
                        if (!$customer->is_guest && Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
                            Mail::Send($this->context->language->id, 'account_social', $this->l('Welcome!', 'leocustomer'), array(
                                '{firstname}' => $customer->firstname,
                                '{lastname}' => $customer->lastname,
                                '{email}' => $customer->email,
                                '{password}' => $password), $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null, _PS_MODULE_DIR_ . $this->module->name . '/mails/');
                        }

                        Hook::exec('actionCustomerAccountAdd', array('newCustomer' => $customer));
                        $success[] = $this->l('You have successfully created a new account', 'leocustomer');
                    } else {
                        $errors[] = $this->l('An error occurred while creating the new account.', 'leocustomer');
                    }
                }
            }
        }

        $array_result['success'] = $success;
        $array_result['errors'] = $errors;
        die(Tools::jsonEncode($array_result));
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->php_self = 'leocustomer';

        if (Tools::getValue('ajax')) {
            return;
        }
        parent::initContent();
    }
}
