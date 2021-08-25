{* 
* @Module Name: Leo Quick Login
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
*}

<div class="modal lql-social-modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
			<h5 class="modal-title lql-social-modal-mesg lql-social-loading">
				<span class="leoquicklogin-cssload-speeding-wheel"></span>
			</h5>
			<h5 class="modal-title lql-social-modal-mesg error-email">
				<i class="material-icons">&#xE033;</i>
				{l s='Can not login without email!' mod='leoquicklogin'}
			</h5>
			<h5 class="modal-title lql-social-modal-mesg error-email">				
				{l s='Please check your social account and give the permission to use your email info' mod='leoquicklogin'}
			</h5>
			<h5 class="modal-title lql-social-modal-mesg error-login">
				<i class="material-icons">&#xE033;</i>
				{l s='Can not login!' mod='leoquicklogin'}
			</h5>
			<h5 class="modal-title lql-social-modal-mesg error-login">
				{l s='Please contact with us or try to login with another way' mod='leoquicklogin'}
			</h5>
			<h5 class="modal-title lql-social-modal-mesg success">
				<i class="material-icons">&#xE876;</i>
				{l s='Successful!' mod='leoquicklogin'}
			</h5>
			<h5 class="modal-title lql-social-modal-mesg success">			
				{l s='Thanks for logging in' mod='leoquicklogin'}
			</h5>
		  </div>
		  
		  		 
		</div>
	  </div>
	
</div>