/**
* 2007-2019 PrestaShop
*
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(document).ready(function() {
	if (prestashop.page.page_name == 'contact' && $('#g-recaptcha').length == 0) {		
		$('.form-fields').append('<div class="form-group row"><label class="col-md-3 form-control-label"></label><div class="col-md-6"><div id="g-recaptcha"></div></div></div>');
	}	
});

$(window).load(function() {
	if (prestashop.page.page_name == 'contact' && $('#g-recaptcha').length) {
		grecaptcha.render("g-recaptcha", {sitekey: captcha_site_key, action: prestashop.page.page_name });
	}
});