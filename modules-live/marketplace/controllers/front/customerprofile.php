<?php
/**
*  2017-2018 PHPIST.
*
*  @author    Yassine Belkaid <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PHPIST
*  @license   https://store.webkul.com/license.html
*/

use PrestaShop\PrestaShop\Core\Crypto\Hashing as Crypto;

class MarketplaceCustomerProfileModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        if (!$this->context->customer->isLogged()) {
            Tools::redirect($this->context->link->getPageLink('index'));
        }

        $_errors = array();
        $isModified = false;
        if (Tools::isSubmit('submitCustomerProfile')) {
            $firstname = Tools::getValue('firstname');
            $lastname = Tools::getValue('lastname');
            $email = Tools::getValue('email');
            $passwd = Tools::getValue('password');
            $passwd_conf = Tools::getValue('password_conf');
            $newsletter = (bool)Tools::getValue('newsletter');

            // input validation
            if (!Validate::isName($firstname) || Tools::isEmpty($firstname)) {
                $_errors[] = 'Veuillez saisir votre prénom';
            }
            if (!Validate::isName($lastname) || Tools::isEmpty($lastname)) {
                $_errors[] = 'Veuillez saisir votre nom';
            }
            if (!Validate::isEmail($email) || Tools::isEmpty($email)) {
                $_errors[] = 'Veuillez saisir votre adresse e-mail';
            } else if (Customer::customerExists($email) && $email != $this->context->customer->email) {
                $_errors[] = 'Veuillez saisir une autre adresse e-mail, cette adresse e-mail est déja pris.';
            }
            if (!Tools::isEmpty($passwd)) {
                if (!Validate::isPlaintextPassword($passwd)) {
                    $_errors[] = 'Le mot de passe est invalide';
                }
                if (!Validate::isPlaintextPassword($passwd_conf)) {
                    $_errors[] = 'Le mot de passe de confirmation est invalide';
                }
                if ($passwd !== $passwd_conf) {
                    $_errors[] = 'Les mots de passe ne sont pas les memes.';
                }
            }
            if (!Validate::isBool($newsletter)) {
                $_errors[] = 'Le newsletter est invalide';
            }

            if (!count($_errors)) {
                $cust = new Customer($this->context->customer->id);
                $cust->firstname = $firstname;
                $cust->lastname = $lastname;
                $cust->email = $email;
                $cust->passwd = (new Crypto())->hash($passwd, _COOKIE_KEY_);
                $cust->newsletter = 0;
                if (!empty($newsletter)) {
                    $cust->newsletter = 1;
                }
                $cust->update();
                $this->context->customer = $cust;
                $isModified = true;
            }
        }

        parent::initContent();
        $this->context->smarty->assign(array(
            'logic' => 1,
            'customer' => $this->context->customer,
            'errors' => $_errors,
            'is_modified' => $isModified,
        ));

        $this->setTemplate('module:marketplace/views/templates/front/customer/customerprofile.tpl');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('marketplace_account', 'modules/'.$this->module->name.'/views/css/marketplace_account.css');
        $this->registerStylesheet('marketplace_global', 'modules/'.$this->module->name.'/views/css/mp_global_style.css');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => 'Journal de bord',
            'url' => ''
        );

        $breadcrumb['links'][] = array(
            'title' => 'Mon profil',
            'url' => ''
        );

        return $breadcrumb;
    }
}
