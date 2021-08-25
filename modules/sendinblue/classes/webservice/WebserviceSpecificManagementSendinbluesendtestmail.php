<?php
/**
 * 2007-2021 Sendinblue
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@sendinblue.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Sendinblue <contact@sendinblue.com>
 * @copyright 2007-2021 Sendinblue
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Sendinblue
 */

use Sendinblue\Services\ConfigService;

class WebserviceSpecificManagementSendinbluesendtestmail extends WebserviceSpecificManagementSendinblueAbstract
{
    public function manage()
    {
        $result = \MailCore::sendMailTest(
            \ConfigurationCore::get(ConfigService::MAIL_METHOD),
            \ConfigurationCore::get(ConfigService::MAIL_SERVER),
            \Tools::htmlentitiesUTF8(\Tools::getValue(ConfigService::SEND_TEST_CONTENT)),
            \Tools::htmlentitiesUTF8(ConfigService::SEND_TEST_SUBJECT),
            ConfigService::SEND_TEST_TYPE,
            \Tools::getValue(ConfigService::SEND_TEST_EMAIL),
            \ConfigurationCore::get(ConfigService::MAIL_EMAIL),
            \ConfigurationCore::get(ConfigService::MAIL_USER),
            \ConfigurationCore::get(ConfigService::MAIL_PASSWD),
            \ConfigurationCore::get(ConfigService::MAIL_PORT),
            \ConfigurationCore::get(ConfigService::MAIL_ENCRYPTION)
        );

        if (true === $result) {
            $this->response = [self::SUCCESS => true];
        } else {
            $this->response = [
                self::SUCCESS => false,
                self::ERROR => $result,
            ];
        }
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return json_encode($this->response);
    }
}
