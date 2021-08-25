{*
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
*}

{if $is_17}
	<div class="form-group row">
		<label class="col-md-3 form-control-label"></label>
		<div class="g-recaptcha col-md-6" id="g-recaptcha" data-sitekey="{$recaptcha_site_key}"></div>
		<div class="col-md-3"></div>
	</div>
{else}
	<p class="g-recaptcha required form-group" id="g-recaptcha" data-sitekey="{$recaptcha_site_key}"></p>
{/if}

{if $is_ajax}
<script type="text/javascript">
	loadCaptchaWidget();
</script>
{/if}