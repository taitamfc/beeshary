<?php
/**
 * Redis Cache powered by Vopster
 *
 *    @author    Vopster
 *    @copyright 2017 Vopster
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *    @link      https://addons.prestashop.com/en/contact-us?id_product=26866
 */

$sql = array();

$sql[_DB_PREFIX_.'redis_config'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'redis_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL UNIQUE,
  `value` text ,
  PRIMARY KEY (`id`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
