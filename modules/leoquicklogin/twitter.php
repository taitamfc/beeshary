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

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
include_once(dirname(__FILE__) . '/leoquicklogin.php');
include_once(dirname(__FILE__) . '/classes/twitteroauth.php');

$module = new Leoquicklogin();

define('CONSUMER_KEY', Configuration::get('LEOQUICKLOGIN_TWITTER_APIKEY')); // YOUR CONSUMER KEY
define('CONSUMER_SECRET', Configuration::get('LEOQUICKLOGIN_TWITTER_APISECRET')); //YOUR CONSUMER SECRET KEY
define('OAUTH_CALLBACK', urlencode(Tools::getHTTPHost(true) . __PS_BASE_URI__ . 'modules/' . $module->name . '/twitter.php'));  // Redirect URL

$context = Context::getContext();
if (Tools::getValue('request')) {
    //Fresh authentication
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

    $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

    //Received token info from twitter
    // $_SESSION['token']             = $request_token['oauth_token'];
    // $_SESSION['token_secret']     = $request_token['oauth_token_secret'];
    $context->cookie->twitter_token = $request_token['oauth_token'];
    $context->cookie->twitter_token_secret = $request_token['oauth_token_secret'];

    //Any value other than 200 is failure, so continue only if http code is 200
    if ($connection->http_code == '200') {
        //redirect user to twitter
        $twitter_url = $connection->getAuthorizeURL($request_token['oauth_token'], true, Tools::getValue('lang'));
        Tools::redirect($twitter_url);
    } else {
        die("Error connecting to twitter! Please try again later!");
    }
}

if (Tools::getValue('oauth_token') && Tools::getValue('oauth_token') == $context->cookie->twitter_token) {
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $context->cookie->twitter_token, $context->cookie->twitter_token_secret);
    // $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
    $access_token = $connection->getAccessToken(Tools::getValue('oauth_verifier'));
    if ($connection->http_code == '200') {
        $user_data = $connection->get('account/verify_credentials', array('include_entities' => 'true', 'skip_status' => 'true', 'include_email' => 'true'));
        
        $first_name = $user_data['name'];
        $last_name = $user_data['name'];
        
        // print_r($user_data->email);
        $name = explode(" ", $user_data['name']);
//        $first_name = '';
//        $last_name = '';
        $email = '';
        if (isset($name[0])) {
            $first_name = $name[0];
        }
        if (isset($name[1])) {
            $last_name = $name[1];
        }
        if (isset($user_data['email'])) {
            $email = $user_data['email'];
        }else{
            die("Error, Can not get Email from twitter.com");
        }

        $twitter_callback = $module->buildTwitterLoginCallBack($first_name, $last_name, $email);
        die($twitter_callback);
    } else {
        die("Error, Please try again later!");
    }
}
