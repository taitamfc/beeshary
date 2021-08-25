<?php

/**
 * Class Inix2Mail
 */
class Inix2Mail extends Mail
{
    /**
     * @var Inix2Module
     */
    public static $module;

    /**
     * @param      $template
     * @param      $subject
     * @param      $template_vars
     * @param null $from
     * @param null $from_name
     * @param null $file_attachment
     *
     * @return bool|int
     */
    public static function FrameSend(
        $template,
        $subject,
        $template_vars,
        $from = null,
        $from_name = null,
        $file_attachment = null
    ) {

        $template_path                          = self::$module->getFrameLocalPath() . 'mails_frame/';
        $template_vars['{module_name}']         = self::$module->name;
        $template_vars['{module_display_name}'] = self::$module->displayName;
        $template_vars['{module_version}']      = self::$module->version;
        $template_vars['{module_author}']       = self::$module->author;
        $template_vars['{ps_version}']          = _PS_VERSION_;


        return self::MySend(
            0,
            $template,
            $subject,
            $template_vars,
            self::$module->author_email,
            self::$module->author,
            $from,
            $from_name,
            $file_attachment,
            null,
            $template_path
        );
    }

    /**
     * @param            $id_lang
     * @param            $template
     * @param            $subject
     * @param            $template_vars
     * @param            $to
     * @param null       $to_name
     * @param null       $from
     * @param null       $from_name
     * @param null       $file_attachment
     * @param null       $mode_smtp
     * @param string     $template_path
     * @param bool|false $die
     * @param null       $id_shop
     * @param null       $bcc
     *
     * @return bool|int
     * @throws Exception
     * @throws PrestaShopException
     */
    public static function MySend(
        $id_lang,
        $template,
        $subject,
        $template_vars,
        $to,
        $to_name = null,
        $from = null,
        $from_name = null,
        $file_attachment = null,
        $mode_smtp = null,
        $template_path = _PS_MAIL_DIR_,
        $die = false,
        $id_shop = null,
        $bcc = null
    ) {


        $configuration = Configuration::getMultiple(array(
            'PS_SHOP_EMAIL',
            'PS_MAIL_METHOD',
            'PS_MAIL_SERVER',
            'PS_MAIL_USER',
            'PS_MAIL_PASSWD',
            'PS_SHOP_NAME',
            'PS_MAIL_SMTP_ENCRYPTION',
            'PS_MAIL_SMTP_PORT',
            'PS_MAIL_TYPE'
        ), null, null, $id_shop);

        // Returns immediatly if emails are deactivated
        if ($configuration['PS_MAIL_METHOD'] == 3) {
            return true;
        }

        // Get the path of theme by id_shop if exist
        if (is_numeric($id_shop) && $id_shop) {
        }

        if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION'])) {
            $configuration['PS_MAIL_SMTP_ENCRYPTION'] = 'off';
        }
        if (!isset($configuration['PS_MAIL_SMTP_PORT'])) {
            $configuration['PS_MAIL_SMTP_PORT'] = 'default';
        }

        // Sending an e-mail can be of vital importance for the merchant, when his password is lost for example, so we must not die but do our best to send the e-mail
        if (!isset($from) || !Validate::isEmail($from)) {
            $from = $configuration['PS_SHOP_EMAIL'];
        }
        if (!Validate::isEmail($from)) {
            $from = null;
        }

        // $from_name is not that important, no need to die if it is not valid
        if (!isset($from_name) || !Validate::isMailName($from_name)) {
            $from_name = $configuration['PS_SHOP_NAME'];
        }
        if (!Validate::isMailName($from_name)) {
            $from_name = null;
        }

        // It would be difficult to send an e-mail if the e-mail is not valid, so this time we can die if there is a problem
        if (!is_array($to) && !Validate::isEmail($to)) {
            Tools::dieOrLog(Tools::displayError('Error: parameter "to" is corrupted'), $die);

            return false;
        }

        if (!is_array($template_vars)) {
            $template_vars = array();
        }

        // Do not crash for this error, that may be a complicated customer name
        if (is_string($to_name) && !empty($to_name) && !Validate::isMailName($to_name)) {
            $to_name = null;
        }

        if (!Validate::isTplName($template)) {
            Tools::dieOrLog(Tools::displayError('Error: invalid e-mail template'), $die);

            return false;
        }

        if (!Validate::isMailSubject($subject)) {
            Tools::dieOrLog(Tools::displayError('Error: invalid e-mail subject'), $die);

            return false;
        }

        /* Construct multiple recipients list if needed */
        $to_list = new Swift_RecipientList();
        if (is_array($to) && isset($to)) {
            foreach ($to as $key => $addr) {
                $addr = trim($addr);
                if (!Validate::isEmail($addr)) {
                    Tools::dieOrLog(Tools::displayError('Error: invalid e-mail address'), $die);

                    return false;
                }

                if (is_array($to_name)) {
                    if ($to_name && is_array($to_name) && Validate::isGenericName($to_name[$key])) {
                        $to_name = $to_name[$key];
                    }
                }

                if ($to_name == null || $to_name == $addr) {
                    $to_name = '';
                } else {
                    if (function_exists('mb_encode_mimeheader')) {
                        $to_name = mb_encode_mimeheader($to_name, 'utf-8');
                    } else {
                        $to_name = self::mimeEncode($to_name);
                    }
                }

                $to_list->addTo($addr, $to_name);
            }
            $to_plugin = $to[0];
        } else {
            /* Simple recipient, one address */
            $to_plugin = $to;
            if ($to_name == null || $to_name == $to) {
                $to_name = '';
            } else {
                if (function_exists('mb_encode_mimeheader')) {
                    $to_name = mb_encode_mimeheader($to_name, 'utf-8');
                } else {
                    $to_name = self::mimeEncode($to_name);
                }
            }

            $to_list->addTo($to, $to_name);
        }
        if (isset($bcc)) {
            $to_list->addBcc($bcc);
        }
        $to = $to_list;
        try {
            /* Connect with the appropriate configuration */
            if ($configuration['PS_MAIL_METHOD'] == 2) {
                if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT'])) {
                    Tools::dieOrLog(Tools::displayError('Error: invalid SMTP server or SMTP port'), $die);

                    return false;
                }
                $connection = new Swift_Connection_SMTP(
                    $configuration['PS_MAIL_SERVER'],
                    $configuration['PS_MAIL_SMTP_PORT'],
                    ($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'ssl') ? Swift_Connection_SMTP::ENC_SSL :
                    (($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'tls') ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_OFF)
                );
                $connection->setTimeout(4);
                if (!$connection) {
                    return false;
                }
                if (!empty($configuration['PS_MAIL_USER'])) {
                    $connection->setUsername($configuration['PS_MAIL_USER']);
                }
                if (!empty($configuration['PS_MAIL_PASSWD'])) {
                    $connection->setPassword($configuration['PS_MAIL_PASSWD']);
                }
            } else {
                $connection = new Swift_Connection_NativeMail();
            }

            if (!$connection) {
                return false;
            }
            $swift = new Swift($connection, Configuration::get('PS_MAIL_DOMAIN', null, null, $id_shop));
            /* Get templates content */

            if (!file_exists($template_path . $template . '.txt') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT)) {
                Tools::dieOrLog(
                    Tools::displayError('Error - The following e-mail template is missing:') . ' ' . $template_path . $template . '.txt',
                    $die
                );

                return false;
            } elseif (!file_exists($template_path . $template . '.html') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML)) {
                Tools::dieOrLog(
                    Tools::displayError('Error - The following e-mail template is missing:') . ' ' . $template_path . $template . '.html',
                    $die
                );

                return false;
            }
            $template_html = file_get_contents($template_path . $template . '.html');
            $template_txt  = strip_tags(html_entity_decode(
                file_get_contents($template_path . $template . '.txt'),
                null,
                'utf-8'
            ));

            /* Create mail and attach differents parts */
            $message = new Swift_Message($subject);

            $message->setCharset('utf-8');

            /* Set Message-ID - getmypid() is blocked on some hosting */
            $message->setId(Mail::generateId());

            $message->headers->setEncoding('Q');


            ShopUrl::cacheMainDomainForShop((int) $id_shop);


            if ((Context::getContext()->link instanceof Link) === false) {
                Context::getContext()->link = new Link();
            }


            $swift->attachPlugin(new Swift_Plugin_Decorator(array($to_plugin => $template_vars)), 'decorator');
            if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT) {
                $message->attach(new Swift_Message_Part($template_txt, 'text/plain', '8bit', 'utf-8'));
            }
            if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML) {
                $message->attach(new Swift_Message_Part($template_html, 'text/html', '8bit', 'utf-8'));
            }

            if ($file_attachment && !empty($file_attachment)) {
                // Multiple attachments?
                if (!is_array(current($file_attachment))) {
                    $file_attachment = array($file_attachment);
                }

                foreach ($file_attachment as $attachment) {
                    if (isset($attachment['content']) && isset($attachment['name']) && isset($attachment['mime'])) {
                        $message->attach(new Swift_Message_Attachment(
                            $attachment['content'],
                            $attachment['name'],
                            $attachment['mime']
                        ));
                    }
                }
            }
            /* Send mail */
            $send = $swift->send($message, $to, new Swift_Address($from, $from_name));
            $swift->disconnect();

            ShopUrl::resetMainDomainCache();

            return $send;
        } catch (Swift_Exception $e) {
            return false;
        }
    }
}
