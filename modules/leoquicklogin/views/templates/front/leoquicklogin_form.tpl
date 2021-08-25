{* 
* @Module Name: Leo Quick Login
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
*}
<div class="leo-quicklogin-form row{if isset($leo_form_type) && $leo_form_type != ''} {$leo_form_type|escape:'html':'UTF-8'}{/if}">
	{if isset($leo_navigation_style) && $leo_navigation_style}
		<ul class="lql-action{if $leo_form_layout != 'both'} lql-active{else} lql-inactive{/if}">
			<li class="lql-action-bt">
				<p class="lql-bt lql-bt-login{if $leo_form_layout == 'login'} lql-active{/if}">{l s='Login' mod='leoquicklogin'}</p>
			</li>
			<li class="lql-action-bt">
				<p class="lql-bt lql-bt-register{if $leo_form_layout == 'register'} lql-active{/if}">{l s='Register' mod='leoquicklogin'}</p>
			</li>
		</ul>
	{/if}
	<div class="leo-form leo-login-form col-sm-{if $leo_form_layout == 'both'}6{else}12{/if}{if $leo_form_layout == 'login' || $leo_form_layout == 'both'} leo-form-active{else} leo-form-inactive{/if}{if $leo_form_layout != 'both'} full-width{/if}">
		<h3 class="leo-login-title">			
			<span class="title-both">
				{l s='Existing Account Login' mod='leoquicklogin'}
			</span>
		
			<span class="title-only">
				{l s='Login to your account' mod='leoquicklogin'}
			</span>		
		</h3>
		<form class="lql-form-content leo-login-form-content" action="#" method="post">
			<div class="form-group lql-form-mesg has-success">					
			</div>			
			<div class="form-group lql-form-mesg has-danger">					
			</div>
			<div class="form-group lql-form-content-element">
				<input type="email" class="form-control lql-email-login" name="lql-email-login" required="" placeholder="{l s='Email Address' mod='leoquicklogin'}">
			</div>
			<div class="form-group lql-form-content-element">
				<input type="password" class="form-control lql-pass-login" name="lql-pass-login" required="" placeholder="{l s='Password' mod='leoquicklogin'}">
			</div>
			<div class="form-group row lql-form-content-element">				
				<div class="col-xs-6">
					{if $leo_check_cookie}
						<input type="checkbox" class="lql-rememberme" name="lql-rememberme">
						<label class="form-control-label"><span>{l s='Remember Me' mod='leoquicklogin'}</span></label>
					{/if}
				</div>				
				<div class="col-xs-6 text-sm-right">
					<a role="button" href="#" class="leoquicklogin-forgotpass">{l s='Forgot Password' mod='leoquicklogin'} ?</a>
				</div>
			</div>
			<div class="form-group text-right">
				<button type="submit" class="form-control-submit lql-form-bt lql-login-bt btn btn-primary">			
					<span class="leoquicklogin-loading leoquicklogin-cssload-speeding-wheel"></span>
					<i class="leoquicklogin-icon leoquicklogin-success-icon material-icons">&#xE876;</i>
					<i class="leoquicklogin-icon leoquicklogin-fail-icon material-icons">&#xE033;</i>
					<span class="lql-bt-txt">					
						{l s='Login' mod='leoquicklogin'}
					</span>
				</button>
			</div>
			<div class="form-group lql-callregister">
				<a role="button" href="#" class="lql-callregister-action">{l s='No account? Create one here' mod='leoquicklogin'} ?</a>
			</div>
		</form>
		<div class="leo-resetpass-form">
			<h3>{l s='Reset Password' mod='leoquicklogin'}</h3>
			<form class="lql-form-content leo-resetpass-form-content" action="#" method="post">
				<div class="form-group lql-form-mesg has-success">					
				</div>			
				<div class="form-group lql-form-mesg has-danger">					
				</div>
				<div class="form-group lql-form-content-element">
					<input type="email" class="form-control lql-email-reset" name="lql-email-reset" required="" placeholder="{l s='Email Address' mod='leoquicklogin'}">
				</div>
				<div class="form-group">					
					<button type="submit" class="form-control-submit lql-form-bt leoquicklogin-reset-pass-bt btn btn-primary">			
						<span class="leoquicklogin-loading leoquicklogin-cssload-speeding-wheel"></span>
						<i class="leoquicklogin-icon leoquicklogin-success-icon material-icons">&#xE876;</i>
						<i class="leoquicklogin-icon leoquicklogin-fail-icon material-icons">&#xE033;</i>
						<span class="lql-bt-txt">					
							{l s='Reset Password' mod='leoquicklogin'}
						</span>
					</button>
				</div>
				
			</form>
		</div>
	</div>
	
	<div class="leo-form leo-register-form col-sm-{if $leo_form_layout == 'both'}6{else}12{/if}{if $leo_form_layout == 'register' || $leo_form_layout == 'both'} leo-form-active{else} leo-form-inactive{/if}{if $leo_form_layout != 'both'} full-width{/if}">
		<h3 class="leo-register-title">
			{l s='New Account Register' mod='leoquicklogin'}
		</h3>
		<form class="lql-form-content leo-register-form-content" action="#" method="post">
			<div class="form-group lql-form-mesg has-success">					
			</div>			
			<div class="form-group lql-form-mesg has-danger">					
			</div>
			<div class="form-group lql-form-content-element">
				<input type="text" class="form-control lql-register-firstname" name="lql-register-firstname"  placeholder="{l s='First Name' mod='leoquicklogin'}">
			</div>
			<div class="form-group lql-form-content-element">
				<input type="text" class="form-control lql-register-lastname" name="lql-register-lastname" required="" placeholder="{l s='Last Name' mod='leoquicklogin'}">
			</div>
			<div class="form-group lql-form-content-element">
				<input type="email" class="form-control lql-register-email" name="lql-register-email" required="" placeholder="{l s='Email Address' mod='leoquicklogin'}">
			</div>
			<div class="form-group lql-form-content-element">
				<input type="password" class="form-control lql-register-pass" name="lql-register-pass" required="" placeholder="{l s='Password' mod='leoquicklogin'}">
			</div>
			{if $leo_check_terms }
			<div class="form-group lql-form-content-element leo-form-chk">
				<input type="checkbox" class="lql-register-check" name="lql-register-check"  >
				<label class="form-control-label">{l s='I agree to the terms.' mod='leoquicklogin'}</label>
			</div>
			{/if}
			<div class="form-group text-right">				
				<button type="submit" name="submit" class="form-control-submit lql-form-bt lql-register-bt btn btn-primary">			
					<span class="leoquicklogin-loading leoquicklogin-cssload-speeding-wheel"></span>
					<i class="leoquicklogin-icon leoquicklogin-success-icon material-icons">&#xE876;</i>
					<i class="leoquicklogin-icon leoquicklogin-fail-icon material-icons">&#xE033;</i>
					<span class="lql-bt-txt">					
						{l s='Create an Account' mod='leoquicklogin'}
					</span>
				</button>
			</div>
			<div class="form-group lql-calllogin">
				<div>{l s='Already have an account?' mod='leoquicklogin'}</div>
				<a role="button" href="#" class="lql-calllogin-action">{l s='Log in instead' mod='leoquicklogin'}</a>
				{l s='Or' mod='leoquicklogin'}
				<a role="button" href="#" class="lql-calllogin-action lql-callreset-action">{l s='Reset password' mod='leoquicklogin'}</a>
			</div>
		</form>
	</div>
</div>

