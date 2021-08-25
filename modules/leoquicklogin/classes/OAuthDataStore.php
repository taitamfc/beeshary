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

class OAuthDataStore
{

    public function lookupConsumer($consumer_key)
    {
        // implement me
    }

    public function lookupToken($consumer, $token_type, $token)
    {
        // implement me
    }

    public function lookupNonce($consumer, $token, $nonce, $timestamp)
    {
        // implement me
    }

    public function newRequestToken($consumer, $callback = null)
    {
        // return a new token attached to this consumer
    }

    public function newAccessToken($token, $consumer, $verifier = null)
    {
        // return a new access token attached to this consumer
        // for the user associated with this token if the request token
        // is authorized
        // should also invalidate the request token
    }
}
