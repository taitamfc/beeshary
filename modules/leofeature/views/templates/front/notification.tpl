{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<div class="leo-notification" style="width: {$width_notification}; {$vertical_position}; {$horizontal_position}">
</div>
<div class="leo-temp leo-temp-success">
	<div class="notification-wrapper">
		<div class="notification notification-success">
			{*
			<span class="notification-title"><i class="material-icons">&#xE876;</i></span> 
			*}
			<strong class="noti product-name"></strong>
			<span class="noti noti-update">{l s='The product has been updated in your shopping cart' mod='leofeature'}</span>
			<span class="noti noti-delete">{l s='The product has been removed from your shopping cart' mod='leofeature'}</span>
			<span class="noti noti-add"><strong class="noti-special"></strong> {l s='Product successfully added to your shopping cart' mod='leofeature'}</span>
			<span class="notification-close">X</span>
			
		</div>
	</div>
</div>
<div class="leo-temp leo-temp-error">
	<div class="notification-wrapper">
		<div class="notification notification-error">
			{*
			<span class="notification-title"><i class="material-icons">&#xE611;</i></span>
			*}
			
			<span class="noti noti-update">{l s='Error updating' mod='leofeature'}</span>
			<span class="noti noti-delete">{l s='Error deleting' mod='leofeature'}</span>
			<span class="noti noti-add">{l s='Error adding. Please go to product detail page and try again' mod='leofeature'}</span>
			
			<span class="notification-close">X</span>
			
		</div>
	</div>
</div>
<div class="leo-temp leo-temp-warning">
	<div class="notification-wrapper">
		<div class="notification notification-warning">
			{*
			<span class="notification-title"><i class="material-icons">&#xE645;</i></span> 
			*}
			<span class="noti noti-min">{l s='The minimum purchase order quantity for the product is' mod='leofeature'} <strong class="noti-special"></strong></span>
			<span class="noti noti-max">{l s='There are not enough products in stock' mod='leofeature'}</span>
			
			<span class="notification-close">X</span>
			
		</div>
	</div>
</div>
<div class="leo-temp leo-temp-normal">
	<div class="notification-wrapper">
		<div class="notification notification-normal">
			{*
			<span class="notification-title"><i class="material-icons">&#xE88F;</i></span>
			*}
			<span class="noti noti-check">{l s='You must enter a quantity' mod='leofeature'}</span>
			
			<span class="notification-close">X</span>
			
		</div>
	</div>
</div>
