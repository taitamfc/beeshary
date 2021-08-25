{* 
* @Module Name: Leo Quick Login
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
*}

<div class="lql-social-login clearfix {if $show_button_text}show-bt-txt{/if}">
	<h3 class="lql-social-login-title">
		{l s='Connect with Social Networks' mod='leoquicklogin'}
	</h3>
	{if $fb_enable && $fb_app_id != ''}
		<!--
		<div class="fb-login-button" data-max-rows="1" data-size="large" data-button-type="login_with" data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="false" scope="public_profile,email" onlogin="checkLoginState();"></div>
		-->
		<button class="btn social-login-bt facebook-login-bt" onclick="doFbLogin();"><span class="fa fa-facebook"></span>{if $show_button_text}{l s='Sign in with Facebook' mod='leoquicklogin'}{/if}</button>
		
	{/if}
	<!--
		<div class="g-signin2" data-scope="profile email" data-longtitle="true" data-theme="dark" data-onsuccess="googleSignIn" data-onfailure="googleFail"></div>
	-->
	{if $google_enable && $google_client_id != ''}
		<button class="btn social-login-bt google-login-bt"><span class="fa fa-google"></span>{if $show_button_text}{l s='Sign in with Google' mod='leoquicklogin'}{/if}</button>
	{/if}
	{if $twitter_enable && $twitter_api_key != '' && $twitter_api_secret !== ''}
		<button class="btn social-login-bt twitter-login-bt"><span class="fa fa-twitter"></span>{if $show_button_text}{l s='Sign in with Twitter' mod='leoquicklogin'}{/if}</button>
	{/if}
</div>
{if isset($login_page) && $login_page}
<hr>
{/if}

