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

<div class="panel clearfix">
	<div class="panel-heading">
		<i class="icon-user"></i>
		{l s='Seller Membership Active Plan Details' mod='mpsellermembership'}
	</div>
	<div class="form-horizontal">
		<div class="row">
			<label class="control-label col-lg-3">{l s='Number Of Product Allowed: ' mod='mpsellermembership'}</label>
			<div class="col-lg-9">
				<p class="form-control-static">{$sellerPlan.num_products_allow|escape:'htmlall':'UTF-8'} {l s='Product(s)' mod='mpsellermembership'}</p>
			</div>
		</div>

		<div class="row">
			<label class="control-label col-lg-3">{l s='Plan Duration: ' mod='mpsellermembership'}</label>
			<div class="col-lg-9">
				<p class="form-control-static">{$sellerPlan.plan_duration|escape:'htmlall':'UTF-8'} {l s='Day(s)' mod='mpsellermembership'}</p>
			</div>
		</div>

		<div class="row">
			<label class="control-label col-lg-3">{l s='Activation Date: ' mod='mpsellermembership'}</label>
			<div class="col-lg-9">
				<p class="form-control-static">{dateFormat date=$sellerPlan.active_from full=0}</p>
			</div>
		</div>

		<div class="row">
			<label class="control-label col-lg-3">{l s='Expire Date: ' mod='mpsellermembership'}</label>
			<div class="col-lg-9">
				<p class="form-control-static">{dateFormat date=$sellerPlan.expire_on full=0}</p>
			</div>
		</div>

		<div class="row">
			<label class="control-label col-lg-3">{l s='Purchase Date: ' mod='mpsellermembership'}</label>
			<div class="col-lg-9">
				<p class="form-control-static">{dateFormat date=$sellerPlan.date_add full=0}</p>
			</div>
		</div>
	</div>
</div>