<?php

/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2015 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}
if (!class_exists("LeoFriendlyUrl")) {

    class LeoFriendlyUrl {

        /** @var string[] Adds excluded $_GET keys for redirection */
        public $redirectionExtraExcludedKeys = array();

        public static function getInstance() {
            static $_instance;
            if (!$_instance) {
                $_instance = new LeoFriendlyUrl();
            }
            return $_instance;
        }

        /**
         * REFERRENCE ROOT\classes\Link.php
         * function getProductLink()
         */
//        public function getApPagebuilderLink() {
//
//            $link = Context::getContext()->link;
//            $idLang = Context::getContext()->language->id;
//            $idShop = null;
//            $relativeProtocol = false;
//
//            $url = $link->getBaseLink($idShop, null, $relativeProtocol).$this->getLangLink($idLang, null, $idShop).$profile_data['friendly_url'];
//
//            return $url;
//        }
        
        /**
         * REFERRENCE ROOT\classes\Link.php
         * CORE function not PUBLIC
         */
        public function getLangLink($idLang = null, Context $context = null, $idShop = null)
        {
            $this->allow = (int) Configuration::get('PS_REWRITING_SETTINGS');
            
            static $psRewritingSettings = null;
            if ($psRewritingSettings === null) {
                $psRewritingSettings = (int) Configuration::get('PS_REWRITING_SETTINGS', null, null, $idShop);
            }

            if (!$context) {
                $context = Context::getContext();
            }

            if ((!$this->allow && in_array($idShop, array($context->shop->id,  null))) || !Language::isMultiLanguageActivated($idShop) || !$psRewritingSettings) {
                return '';
            }

            if (!$idLang) {
                $idLang = $context->language->id;
            }

            return Language::getIsoById($idLang).'/';
        }

        /**
         * Redirects to canonical URL.
         * REFERRENCE ROOT\classes\controller\FrontController.php
         * @param string $canonical_url
         */
        public function canonicalRedirection($canonical_url = '') {
            if (!$canonical_url || !Configuration::get('PS_CANONICAL_REDIRECT') || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET') {
                return;
            }

            $canonical_url = preg_replace('/#.*$/', '', $canonical_url);

            $match_url = rawurldecode(Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            if (!preg_match('/^' . Tools::pRegexp(rawurldecode($canonical_url), '/') . '([&?].*)?$/', $match_url)) {
                $params = array();
                $url_details = parse_url($canonical_url);

                if (!empty($url_details['query'])) {
                    parse_str($url_details['query'], $query);
                    foreach ($query as $key => $value) {
                        $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                    }
                }
                $excluded_key = array('isolang', 'id_lang', 'controller', 'fc', 'id_product', 'id_category', 'id_manufacturer', 'id_supplier', 'id_cms', 'id_appagebuilder_profiles');
                $excluded_key = array_merge($excluded_key, $this->redirectionExtraExcludedKeys);
                foreach ($_GET as $key => $value) {
                    if (!in_array($key, $excluded_key) && Validate::isUrl($key) && Validate::isUrl($value)) {
                        $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                    }
                }

                $str_params = http_build_query($params, '', '&');
                if (!empty($str_params)) {
                    $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url) . '?' . $str_params;
                } else {
                    $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url);
                }

                // Don't send any cookie
                Context::getContext()->cookie->disallowWriting();
                if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $_SERVER['REQUEST_URI'] != __PS_BASE_URI__) {
                    die('[Debug] This page has moved<br />Please use the following URL instead: <a href="' . $final_url . '">' . $final_url . '</a>');
                }

                $redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';
                header('HTTP/1.0 ' . $redirect_type . ' Moved');
                header('Cache-Control: no-cache');
                Tools::redirectLink($final_url);
            }
        }

    }

}