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

class OAuthToken
{

    // access tokens and request tokens
    public $key;
    public $secret;

    /**
     * key = the token
     * secret = the token secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * generates the basic string serialization of a token that a server
     * would respond to request_token and access_token calls with
     */
    public function toString()
    {
        return "oauth_token=" .
                OAuthUtil::urlencodeRfc3986($this->key) .
                "&oauth_token_secret=" .
                OAuthUtil::urlencodeRfc3986($this->secret);
    }

    public function __toString()
    {
        return $this->toString();
    }
}
