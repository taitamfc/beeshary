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

/**
 * The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104]
 * where the Signature Base String is the text and the key is the concatenated values (each first
 * encoded per Parameter Encoding) of the Consumer Secret and Token Secret, separated by an '&'
 * character (ASCII code 38) even if empty.
 *   - Chapter 9.2 ("HMAC-SHA1")
 */
class OAuthSignatureMethodHMACSHA1 extends OAuthSignatureMethod
{

    public function getName()
    {
        return "HMAC-SHA1";
    }

    public function buildSignature($request, $consumer, $token)
    {
        $base_string = $request->getSignatureBaseString();
        $request->base_string = $base_string;

        $key_parts = array(
            $consumer->secret,
            ($token) ? $token->secret : ""
        );

        $key_parts = OAuthUtil::urlencodeRfc3986($key_parts);
        $key = implode('&', $key_parts);
        $signature = call_user_func('hash_hmac', 'sha1', $base_string, $key, true);
        return call_user_func('base64_encode', $signature);
    }
}
