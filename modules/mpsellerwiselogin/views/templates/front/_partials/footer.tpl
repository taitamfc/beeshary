{**
* 2010-2017 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

</div>
{if isset($error)}
	<input type="hidden" id="wk_slerror" value="{$error}">
	<div class="error_block">
		<p>
			{if $error == 1}
				{l s='Email is required.' mod='mpsellerwiselogin'}
			{elseif $error == 2}
				{l s='Invalid Email address.' mod='mpsellerwiselogin'}
			{elseif $error == 3}
				{l s='Password is required.' mod='mpsellerwiselogin'}
			{elseif $error == 4}
				{l s='Invalid password.' mod='mpsellerwiselogin'}
			{elseif $error == 5}
				{l s='Entered please enter your valid credentials.' mod='mpsellerwiselogin'}
			{elseif $error == 6}
				{l s='Your account isn\'t available at this time, please contact us.' mod='mpsellerwiselogin'}
			{elseif $error == 7}
				{l s='Authentication failed.' mod='mpsellerwiselogin'}
			{elseif $error == 8}
				{l s='Please Enter Your First Name.' mod='mpsellerwiselogin'}
			{elseif $error == 9}
				{l s='Please Enter Your Last Name.' mod='mpsellerwiselogin'}
			{elseif $error == 11}
				{l s='An account using this email address has already been registered.' mod='mpsellerwiselogin'}
			{elseif $error == 13}
				{l s='Shop Name Is Required.' mod='mpsellerwiselogin'}
			{elseif $error == 14}
				{l s='Please Enter Valid Shop Name.' mod='mpsellerwiselogin'}
			{elseif $error == 15}
				{l s='Unique Shop Name Already Exist.' mod='mpsellerwiselogin'}
			{elseif $error == 16}
				{l s='Phone Number Is Required.' mod='mpsellerwiselogin'}
			{elseif $error == 17}
				{l s='Please Enter Valid Phone Number.' mod='mpsellerwiselogin'}
			{elseif $error == 18}
				{l s='Email Already Exist As Seller.' mod='mpsellerwiselogin'}
			{elseif $error == 19}
				{l s='Unique Shop Name Is Required.' mod='mpsellerwiselogin'}
			{elseif $error == 20}
				{l s='Please Enter Valid Unique Shop Name.' mod='mpsellerwiselogin'}
			{elseif $error == 21}
				{l s='Shop Name Is Required in Default Language.' mod='mpsellerwiselogin'}
			{elseif $error == 22}
				{l s='You are not registered as a seller.' mod='mpsellerwiselogin'}
			{elseif $error == 23}
				{l s='City is required field.' mod='mpsellerwiselogin'}
			{elseif $error == 24}
				{l s='Invalid city name.' mod='mpsellerwiselogin'}
			{elseif $error == 25}
				{l s='Country is required field.' mod='mpsellerwiselogin'}
			{elseif $error == 26}
				{l s='State is required field.' mod='mpsellerwiselogin'}
			{/if}
		</p>
	</div>
{/if}