{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<div class="panel-heading">
		<i class="icon-book"></i>
		{l s='Cron Settings' mod='mpsellerinvoice'}
	</div>
	<div class="form-wrapper">
		<div class="alert alert-info">
			<p>{l s='First of all, make sure the curl library is installed on your server to execute your cron tasks.' mod='mpsellerinvoice'}</p>
		</div>
		<div class="alert alert-info">
			<p>{l s='To create commission invoice on time period and threshold amount, please insert the following line in your cron tasks manager for every hour :' mod='mpsellerinvoice'}</p>
			<br>
			<ul class="list-unstyled">
				<li><code>0 * * * * curl {$commissionCron|escape:'htmlall':'UTF-8'}</code></li>
			</ul>
		</div>
	</div>
</div>