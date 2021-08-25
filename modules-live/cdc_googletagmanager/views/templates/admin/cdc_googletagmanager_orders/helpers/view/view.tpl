{*
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @package   cdc_googletagmanager
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2016 SAS Comptoir du Code
 * @license   Commercial license
 *}


<div class="panel">
	<div class="panel-heading">Log Google Tag Manager</div>
	<table class="table table-responsive">
		<tbody>
		<tr>
			<th>id log</th>
			<td>{$gtm_order_log->id_cdc_gtm_order_log|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<th>order</th>
			<td>{$gtm_order_log->id_order|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<th>refund</th>
			<td>{$gtm_order_log->refund|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<th>sent</th>
			<td>{$gtm_order_log->sent|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<th>re-sent</th>
			<td>{$gtm_order_log->resent|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<th>date_add</th>
			<td>{$gtm_order_log->date_add|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<th>date_upd</th>
			<td>{$gtm_order_log->date_upd|escape:'htmlall':'UTF-8'}</td>
		</tr>
		</tbody>
	</table>
</div>

{if $force_resend}
<div class="alert alert-info">
	<p><b>Force re-send OK</b></p>
	<p>Data will be sent to Google Analytics</p>
</div>
{/if}

<div class="panel">
	<div class="panel-heading">Datalayer raw</div>
	{if !empty($gtm_order_log->datalayer)}
		<pre>{$gtm_order_log->datalayer nofilter}</pre>
	{else}
		<p>No datalayer saved</p>
	{/if}
</div>

<div class="panel">
	<div class="panel-heading">Datalayer JS formatted</div>
	<pre id="datalayer_formatted">
	loading ...
	</pre>
</div>


{if $gtm_order_log->id_cdc_gtm_order_log && !$force_resend}
<div class="panel">
	<div class="panel-heading">Force re-send</div>
	{if !$gtm_order_log->resent}
		<p>If the order does not appear in Google Analytics after 48H, you can re-send this order.</p>
		<p><a href="{$action_resend}" class="btn btn-default">Force re-send order</a></p>
		<div class="alert alert-danger">
			<p>Warning about force re-send:</p>
			<ul>
				<li>The date of the order won't be correct in GA, the current date will be used instead.</li>
				<li>The referrer will be set to null.</li>
				<li>If the order has already been sent to GA, it will be duplicated.</li>
			</ul>
		</div>
	{else}
		<p><b>Order already re-sent.</b></p>
		<p>If the order does not appear in Google Analytics after 48H, this order may contains some errors and cannot be exported to Google Analytics.</p>
		<p>Remember that Google Analytics is an analysis tool and some data can be slightly different from reality. If this error occurs frequently, please contact our support.</p>
	{/if}
</div>
{/if}

<script data-keepinline="true">
var dataLayer_preview = [];
dataLayer_preview.push({$gtm_order_log->datalayer nofilter});
console.log(dataLayer_preview);
$("#datalayer_formatted").text(JSON.stringify(dataLayer_preview, null, 4));
//alert(JSON.stringify(dataLayer_preview, null, 4));
</script>

