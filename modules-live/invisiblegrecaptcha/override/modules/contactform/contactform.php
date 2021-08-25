<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class ContactformOverride extends Contactform
{
    public function sendMessage()
    {
        if (Module::isEnabled('invisiblegrecaptcha')) {
            $grecaptcha = Module::getInstanceByName('invisiblegrecaptcha');
            $grecaptcha_config = $grecaptcha->getConfigurations();
            if ($grecaptcha->hasSpamWordsFound(Tools::getValue('message'))) {
                $this->context->controller->errors[] = $grecaptcha_config['MESSAGE_BLOCKED'];
            } elseif (!$grecaptcha->isEmailDomainAllowed(Tools::getValue('from'))) {
                $this->context->controller->errors[] = $grecaptcha_config['EMAIL_DOMAIN_BLOCKED'];
            } elseif ($grecaptcha_config['GRECAPTCHA_SECRET_KEY']
                && $grecaptcha_config['GRECAPTCHA_SITE_KEY']) {
                if ($grecaptcha->verifyCaptcha(Tools::getValue("g-recaptcha-response")) == false) {
                    $this->context->controller->errors[] = $grecaptcha_config['CAPTCHA_FAILED'];
                } else {
                    parent::sendMessage();
                }
            } else {
                parent::sendMessage();
            }
        } else {
            parent::sendMessage();
        }
    }
}
