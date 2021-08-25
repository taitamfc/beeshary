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

// vim: foldmethod=marker

/* Generic exception class
 */
//DONGND:: use library from prestashop
require_once(dirname(__FILE__) . '/../../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../../init.php');

if (!class_exists('OAuthException')) {

    class OAuthException extends Exception
    {
        // pass
    }
}

require_once(dirname(__FILE__) . '/OAuthConsumer.php');
require_once(dirname(__FILE__) . '/OAuthToken.php');
require_once(dirname(__FILE__) . '/OAuthSignatureMethod.php');
require_once(dirname(__FILE__) . '/OAuthSignatureMethodHMACSHA1.php');
require_once(dirname(__FILE__) . '/OAuthSignatureMethodPLAINTEXT.php');
require_once(dirname(__FILE__) . '/OAuthSignatureMethodRSASHA1.php');
require_once(dirname(__FILE__) . '/OAuthRequest.php');
require_once(dirname(__FILE__) . '/OAuthServer.php');
require_once(dirname(__FILE__) . '/OAuthDataStore.php');
require_once(dirname(__FILE__) . '/OAuthUtil.php');
