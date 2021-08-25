{*
* 2010-2019 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="row" id="wk-customer-data">
	{if isset($gdprRequestInfo) && $gdprRequestInfo}
		<div class="col-sm-12">
			<div class="panel clearfix">
				<div class="col-sm-6">
					<p>
						<b>{l s='Request Type' mod='wkgdpr'}: </b>&nbsp;
						{if $gdprRequestInfo['request_type'] == $WK_CUSTOMER_REQUEST_TYPE_DELETE}
							<span class="badge badge-warning">{l s='Data Erasure' mod='wkgdpr'}</span>
						{else}
							<span class="badge badge-success">{l s='Data Update' mod='wkgdpr'}</span>
						{/if}
					</p>
					<p>
						<b>{l s='Customer' mod='wkgdpr'} : </b> &nbsp;<a target="_blank" href="{$gdprRequestInfo['customer_link']|escape:'htmlall':'UTF-8'}">{$gdprRequestInfo['customer_name']|escape:'htmlall':'UTF-8'} ({$gdprRequestInfo['customer_email']|escape:'htmlall':'UTF-8'})</a>
					</p>
				</div>
				<div class="col-sm-6">
					<p>
						<b>{l s='Request Status' mod='wkgdpr'}: </b>&nbsp;
						{if $gdprRequestInfo['status'] == $WK_CUSTOMER_REQUEST_STATE_PENDING}
							<span class="badge badge-critical">{l s='Pending' mod='wkgdpr'}</span>
							{if $gdprRequestInfo['request_type'] == $WK_CUSTOMER_REQUEST_TYPE_UPDATE}
								&nbsp;&nbsp;<a href="{$link->getAdminLink('AdminGdprCustomerDataManagement')|escape:'htmlall':'UTF-8'}&amp;viewwk_gdpr_customer_requests&amp;change_req_status&amp;status={$WK_CUSTOMER_REQUEST_STATE_DONE|escape:'htmlall':'UTF-8'}&amp;id_request={$gdprRequestInfo['id_request']|escape:'htmlall':'UTF-8'}" class="btn btn-primary">
									<span>{l s='change status to Fulfilled' mod='wkgdpr'}</span>
								</a>
							{/if}
						{else}
							<span class="badge badge-success">{l s='Fulfilled' mod='wkgdpr'}</span>
							{if $gdprRequestInfo['request_type'] == $WK_CUSTOMER_REQUEST_TYPE_UPDATE}
								&nbsp;&nbsp;<a href="{$link->getAdminLink('AdminGdprCustomerDataManagement')|escape:'htmlall':'UTF-8'}&amp;viewwk_gdpr_customer_requests&amp;change_req_status&amp;status={$WK_CUSTOMER_REQUEST_STATE_PENDING|escape:'htmlall':'UTF-8'}&amp;id_request={$gdprRequestInfo['id_request']|escape:'htmlall':'UTF-8'}" class="btn btn-primary">
									<span>{l s='change status to Pending' mod='wkgdpr'}</span>
								</a>
							{/if}
						{/if}
					</p>
					<p><b>{l s='Request Time' mod='wkgdpr'}: </b> &nbsp;{$gdprRequestInfo['date_add']|escape:'htmlall':'UTF-8'}</p>
				</div>
				<div class="col-sm-12">
					<p><b>{l s='Request Message' mod='wkgdpr'}: </b> &nbsp;{$gdprRequestInfo['request_reason']|escape:'htmlall':'UTF-8'}</p>
				</div>
			</div>
		</div>
	{/if}

	{if !$isCustomerDataErased}
		<div class="col-sm-12">
			<div class="panel clearfix">
				<a href="{$customerDataManagementLink|escape:'htmlall':'UTF-8'}&wkDownloadGdprPdf=1&id_customer={$personalInfo['id_customer']|escape:'htmlall':'UTF-8'}{if isset($smarty.get.id_request)}&id_request={$smarty.get.id_request|escape:'htmlall':'UTF-8'}{/if}" class="btn btn-default">
					<span><i class="icon-download"></i> {l s='Download PDF' mod='wkgdpr'}</span>
				</a>
				<a href="{$customerDataManagementLink|escape:'htmlall':'UTF-8'}&eraseCustomerData=1&id_customer={$personalInfo['id_customer']|escape:'htmlall':'UTF-8'}{if isset($smarty.get.id_request)}&id_request={$smarty.get.id_request|escape:'htmlall':'UTF-8'}{/if}" class="btn btn-danger" id="customer-data-erase-btn">
					<span><i class="icon-trash"></i> {l s='Erase Data' mod='wkgdpr'}</span>
				</a>
			</div>
		</div>
	{/if}

	<div class="col-sm-12">
		{* Information Panel *}
		<div class="panel clearfix">
			<div class="panel-heading">
				<i class="icon-user"></i> {l s='Personal Information' mod='wkgdpr'}

				<div class="panel-heading-action">
					<a class="btn btn-default" href="{$viewCustomerLink|escape:'htmlall':'UTF-8'}">
						<i class="icon-search-plus"></i> {l s='View' mod='wkgdpr'}
					</a>
				</div>
			</div>
			<div class="form-horizontal">
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<label class="control-label col-sm-3">{l s='Social Title' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{$personalInfo['gender']|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Name' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{$personalInfo['firstname']|escape:'htmlall':'UTF-8'} {$personalInfo['lastname']|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Email' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{$personalInfo['email']|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Age' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">
									{if $personalInfo['birthday'] != '0000-00-00'}
										{$personalInfo['stats']['age']|escape:'htmlall':'UTF-8'} {l s='years old' mod='wkgdpr'} {l s='(birth date: %s)' sprintf=[$personalInfo['birthday']] mod='wkgdpr'}
									{else}
										{l s='Unknown' mod='wkgdpr'}
									{/if}
								</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Registration Date' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{dateFormat date=$personalInfo['date_add']|escape:'htmlall':'UTF-8' full=1}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Latest Update' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{dateFormat date=$personalInfo['date_upd']|escape:'htmlall':'UTF-8' full=1}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Language' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{$personalInfo['language']['name']|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<label class="control-label col-sm-3">{l s='Last Visit' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{dateFormat date=$personalInfo['stats']['last_visit']|escape:'htmlall':'UTF-8' full=1}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Newsletter' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">
									{if $personalInfo['newsletter']}
										<span class="label label-success"><i class="icon-check"></i></span>
									{else}
										<span class="label label-danger"><i class="icon-remove"></i></span>
									{/if}
								</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Newsletter Subscription Date' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">
									{if $personalInfo['newsletter_date_add'] != '0000-00-00 00:00:00'}
										{dateFormat date=$personalInfo['newsletter_date_add']|escape:'htmlall':'UTF-8' full=1}
									{/if}
								</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Company' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{$personalInfo['company']|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Siret' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{$personalInfo['siret']|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Ape' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{$personalInfo['ape']|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-3">{l s='Website' mod='wkgdpr'}:</label>
							<div class="col-sm-9">
								<p class="form-control-static">{$personalInfo['website']|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
					</div>
				</div>


			</div>
		</div>

		{* Address Panel *}
		<div class="panel clearfix">
			<div class="panel-heading">
				<i class="icon-map-marker"></i> {l s='Addresses' mod='wkgdpr'}
			</div>
			<div class="form-horizontal">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>{l s='Alias' mod='wkgdpr'}</th>
								<th>{l s='Company' mod='wkgdpr'}</th>
								<th>{l s='Name' mod='wkgdpr'}</th>
								<th>{l s='City' mod='wkgdpr'}</th>
								<th>{l s='Country' mod='wkgdpr'}</th>
								<th>{l s='State' mod='wkgdpr'}</th>
								<th>{l s='Postcode' mod='wkgdpr'}</th>
								<th>{l s='Address' mod='wkgdpr'}</th>
								<th>{l s='Phone' mod='wkgdpr'}</th>
								<th>{l s='Mobile' mod='wkgdpr'}</th>
								<th>{l s='Action' mod='wkgdpr'}</th>
							</tr>
						</thead>
						<tbody>
							{if $addresses}
								{foreach from=$addresses item=address}
									<tr>
										<td>{$address['alias']|escape:'htmlall':'UTF-8'}</td>
										<td>{$address['company']|escape:'htmlall':'UTF-8'}</td>
										<td>{$address['firstname']|escape:'htmlall':'UTF-8'} {$address['lastname']|escape:'htmlall':'UTF-8'}</td>
										<td>{$address['city']|escape:'htmlall':'UTF-8'}</td>
										<td>{$address['country']|escape:'htmlall':'UTF-8'}</td>
										<td>{$address['state']|escape:'htmlall':'UTF-8'}</td>
										<td>{$address['postcode']|escape:'htmlall':'UTF-8'}</td>
										<td>
											{$address['address1']|escape:'htmlall':'UTF-8'}
											{if $address['address2']}
												, <br>
												{$address['address2']|escape:'htmlall':'UTF-8'}
											{/if}
										</td>
										<td>{$address['phone']|escape:'htmlall':'UTF-8'}</td>
										<td>{$address['phone_mobile']|escape:'htmlall':'UTF-8'}</td>
										<td>
											<div class="btn-group">
												<a href="{$addressLink|escape:'htmlall':'UTF-8'}&id_address={$address['id_address']|escape:'htmlall':'UTF-8'}" class="btn btn-default" title="View">
													<i class="icon-search-plus"></i> {l s='View' mod='wkgdpr'}
												</a>
											</div>
										</td>
									</tr>
								{/foreach}
							{else}
								<tr>
									<td class="list-empty" colspan="11">
										<div class="list-empty-msg">
											<i class="icon-warning-sign list-empty-icon"></i>
											{l s='No records found' mod='wkgdpr'}
										</div>
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>

		{* Order Panel *}
		<div class="panel clearfix">
			<div class="panel-heading">
				<i class="icon-credit-card"></i> {l s='Orders' mod='wkgdpr'}
			</div>
			<div class="form-horizontal">
				<div class="table-responsive">
					<table class="table table-striped {if $orders}wk-gdpr-datatable{/if}">
						<thead>
							<tr>
								<th>{l s='ID Order' mod='wkgdpr'}</th>
								<th>{l s='Reference' mod='wkgdpr'}</th>
								<th class="text-center">{l s='Items Count' mod='wkgdpr'}</th>
								<th>{l s='Total' mod='wkgdpr'}</th>
								<th>{l s='Payment' mod='wkgdpr'}</th>
								<th>{l s='Status' mod='wkgdpr'}</th>
								<th>{l s='Date' mod='wkgdpr'}</th>
								<th class="no-sort">{l s='Action' mod='wkgdpr'}</th>
							</tr>
						</thead>
						<tbody>
							{if $orders}
								{foreach from=$orders item=order}
									<tr>
										<td>{$order['id_order']|escape:'htmlall':'UTF-8'}</td>
										<td>{$order['reference']|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">{$order['nb_products']|escape:'htmlall':'UTF-8'}</td>
										<td>{$order['formated_total_paid_tax_incl']|escape:'htmlall':'UTF-8'}</td>
										<td>{$order['payment']|escape:'htmlall':'UTF-8'}</td>
										<td>
											<span class="label color_field" style="background-color:{$order['order_state_color']|escape:'htmlall':'UTF-8'};color:white">
												{$order['order_state']|escape:'htmlall':'UTF-8'}
											</span>
										</td>
										<td>{$order['date_add']|escape:'htmlall':'UTF-8'}</td>
										<td>
											<div class="btn-group">
												<a href="{$viewOrderLink|escape:'htmlall':'UTF-8'}&id_order={$order['id_order']|escape:'htmlall':'UTF-8'}" class="btn btn-default" title="View">
													<i class="icon-search-plus"></i> {l s='View' mod='wkgdpr'}
												</a>
											</div>
										</td>
									</tr>
								{/foreach}
							{else}
								<tr>
									<td class="list-empty" colspan="8">
										<div class="list-empty-msg">
											<i class="icon-warning-sign list-empty-icon"></i>
											{l s='No records found' mod='wkgdpr'}
										</div>
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>

		{* Cart Panel *}
		<div class="panel clearfix">
			<div class="panel-heading">
				<i class="icon-shopping-cart"></i> {l s='Cart' mod='wkgdpr'}
			</div>
			<div class="form-horizontal">
				<div class="table-responsive">
					<table class="table table-striped {if $carts}wk-gdpr-datatable{/if}">
						<thead>
							<tr>
								<th>{l s='ID Cart' mod='wkgdpr'}</th>
								<th class="text-center">{l s='Items Count' mod='wkgdpr'}</th>
								<th>{l s='Total' mod='wkgdpr'}</th>
								<th>{l s='Date' mod='wkgdpr'}</th>
								<th class="no-sort">{l s='Action' mod='wkgdpr'}</th>
							</tr>
						</thead>
						<tbody>
							{if $carts}
								{foreach from=$carts item=cart}
									<tr>
										<td>{$cart['id_cart']|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">{$cart['nb_products']|escape:'htmlall':'UTF-8'}</td>
										<td>{$cart['formated_total_tax_incl']|escape:'htmlall':'UTF-8'}</td>
										<td>{$cart['date_add']|escape:'htmlall':'UTF-8'}</td>
										<td>
											<div class="btn-group">
												<a href="{$viewCartLink|escape:'htmlall':'UTF-8'}&id_cart={$cart['id_cart']|escape:'htmlall':'UTF-8'}" class="btn btn-default" title="View">
													<i class="icon-search-plus"></i> {l s='View' mod='wkgdpr'}
												</a>
											</div>
										</td>
									</tr>
								{/foreach}
							{else}
								<tr>
									<td class="list-empty" colspan="5">
										<div class="list-empty-msg">
											<i class="icon-warning-sign list-empty-icon"></i>
											{l s='No records found' mod='wkgdpr'}
										</div>
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>

		{* Message Panel *}
		<div class="panel clearfix">
			<div class="panel-heading">
				<i class="icon-envelope"></i> {l s='Messages' mod='wkgdpr'}
			</div>
			<div class="form-horizontal">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>{l s='Id Customer Thread' mod='wkgdpr'}</th>
								<th>{l s='IP Address' mod='wkgdpr'}</th>
								<th>{l s='Email' mod='wkgdpr'}</th>
								<th>{l s='Message' mod='wkgdpr'}</th>
								<th>{l s='Status' mod='wkgdpr'}</th>
								<th>{l s='Date' mod='wkgdpr'}</th>
								<th>{l s='Action' mod='wkgdpr'}</th>
							</tr>
						</thead>
						<tbody>
							{if $messages}
								{foreach from=$messages item=message}
									<tr>
										<td>{$message['id_customer_thread']|escape:'htmlall':'UTF-8'}</td>
										<td>{$message['ip_address']|escape:'htmlall':'UTF-8'}</td>
										<td>{$message['email']|escape:'htmlall':'UTF-8'}</td>
										<td>{$message['message']|escape:'htmlall':'UTF-8'}</td>
										<td>{$message['status']|escape:'htmlall':'UTF-8'}</td>
										<td>{$message['date_add']|escape:'htmlall':'UTF-8'}</td>
										<td>
											<div class="btn-group">
												<a href="{$customerThreadsLink|escape:'htmlall':'UTF-8'}&id_customer_thread={$message['id_customer_thread']|escape:'htmlall':'UTF-8'}" class="btn btn-default" title="View">
													<i class="icon-search-plus"></i> {l s='View' mod='wkgdpr'}
												</a>
											</div>
										</td>
									</tr>
								{/foreach}
							{else}
								<tr>
									<td class="list-empty" colspan="7">
										<div class="list-empty-msg">
											<i class="icon-warning-sign list-empty-icon"></i>
											{l s='No records found' mod='wkgdpr'}
										</div>
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>

		{* Connection Panel *}
		<div class="panel clearfix">
			<div class="panel-heading">
				<i class="icon-time"></i> {l s='Connections' mod='wkgdpr'}
			</div>
			<div class="form-horizontal">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th class="text-center">{l s='Date' mod='wkgdpr'}</th>
								<th class="text-center">{l s='Pages viewed' mod='wkgdpr'}</th>
								<th class="text-center">{l s='Total time' mod='wkgdpr'}</th>
								<th class="text-center">{l s='IP Address' mod='wkgdpr'}</th>
							</tr>
						</thead>
						<tbody>
							{if $connections}
								{foreach from=$connections item=connection}
									<tr>
										<td class="text-center">{dateFormat date=$connection['date_add']|escape:'htmlall':'UTF-8' full=0}</td>
										<td class="text-center">{$connection['pages']|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">{$connection['time']|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">{$connection['ipaddress']|escape:'htmlall':'UTF-8'}</td>
									</tr>
								{/foreach}
							{else}
								<tr>
									<td class="list-empty" colspan="4">
										<div class="list-empty-msg">
											<i class="icon-warning-sign list-empty-icon"></i>
											{l s='No records found' mod='wkgdpr'}
										</div>
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="panel clearfix">
			<div class="panel-heading">
				<i class="icon-puzzle-piece"></i> {l s='Modules Data' mod='wkgdpr'}
			</div>
				{if $modules}
					{foreach from=$modules item=moduleData}
						<div class="panel clearfix">
							<div class="panel-heading">
								{$moduleData['displayName']|escape:'htmlall':'UTF-8'}
							</div>
							{if $moduleData['data']}
								{if is_array($moduleData['data'])}
									{assign var="modDataFirstRow" value=$moduleData['data']|@reset}
									<div class="table-responsive">
									{if $modDataFirstRow|@count < 7}
										<table class="table table-striped">
											<thead>
												<tr>
													{foreach from=$modDataFirstRow key=fieldName item=fieldValue}
														<th class="text-center">{$fieldName|escape:'htmlall':'UTF-8'}</th>
													{/foreach}
												</tr>
											</thead>
											<tbody>
												{foreach from=$moduleData['data'] item=dataArr}
													<tr>
														{foreach from=$dataArr key=fieldName item=fieldValue}
															<td class="text-center">{$fieldValue|escape:'htmlall':'UTF-8'}</td>
														{/foreach}
													</tr>
												{/foreach}
											</tbody>
										</table>
										{else}
										<table class="table table-striped">
											{foreach from=$modDataFirstRow key=fieldName item=fieldValue}
												<tr>
													<td class="text-left"><b>{$fieldName|escape:'htmlall':'UTF-8'}</b></td>
													<td class="text-left">{$fieldValue|escape:'htmlall':'UTF-8'}</td>
												</tr>
											{/foreach}
										</table>
									{/if}
									</div>
								{else}
									{$moduleData['data']|escape:'htmlall':'UTF-8'}
								{/if}
							{else}
								<div class="alert alert-warning">
									{l s='No Data Found' mod='wkgdpr'}
								</div>
							{/if}
						</div>
					{/foreach}
				{/if}
		</div>
	</div>
</div>

{strip}
	{addJsDefL name=eraseConfirmString}{l s='Are you sure you want to erase customer data?' js=1 mod='wkgdpr'}{/addJsDefL}
{/strip}
