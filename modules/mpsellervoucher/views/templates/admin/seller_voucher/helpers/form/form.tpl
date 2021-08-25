{*
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<div class="panel-heading">
		<i class="icon-tag"></i> {l s='Voucher' mod='mpsellervoucher'}
	</div>
    <form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post">
    	{if !isset($voucher_detail)}
			<div class="form-group">
				<label class="col-sm-3 control-label required">{l s='Choose Seller :' mod='mpsellervoucher'}</label>
				<div class="col-sm-6">
					{if isset($seller_list)}
						<select id="id_seller" name="id_seller" class="fixed-width-xl changeSellerLang">
							{foreach $seller_list as $seller}
								<option value="{$seller['id_seller']|escape:'html':'UTF-8'}" {if isset($smarty.post.id_seller)}{if $smarty.post.id_seller == $seller.id_seller}selected="selected"{/if}{/if}>
									{$seller['business_email']|escape:'html':'UTF-8'}
								</option>
							{/foreach}
						</select>
					{else}
						<p>{l s='No seller found.' mod='mpsellervoucher'}</p>
					{/if}
				</div>
			</div>
		{else}
			<input type="hidden" id="id_seller" name="id_seller" value="{$voucher_detail.id_seller}">
			<input type="hidden" name="id_mp_cart_rule" value="{$voucher_detail.id_mp_cart_rule}">
		{/if}

		<input type="hidden" name="seller_default_lang" id="seller_default_lang" value="{if isset($smarty.post.seller_default_lang)}{$smarty.post.seller_default_lang|escape:'html':'UTF-8'}{else}{$current_lang.id_lang|escape:'html':'UTF-8'}{/if}">
		{if $multi_lang}
			<div class="form-group">
				<label class="col-sm-3 control-label">
					{l s='Seller Default Language :' mod='mpsellervoucher'}
				</label>
				<label class="col-sm-3 control-label" id="seller_default_lang_div" style="text-align: left;">{if isset($smarty.post.current_seller_lang)}{$smarty.post.current_seller_lang|escape:'html':'UTF-8'}{else}{$current_lang.name|escape:'html':'UTF-8'}{/if}</label>
				<input type="hidden" name="current_seller_lang" value="{if isset($smarty.post.current_seller_lang)}{$smarty.post.current_seller_lang}{else}{$current_lang.name|escape:'html':'UTF-8'}{/if}">
			</div>
		{/if}

		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#information" data-toggle="tab">
					<i class="icon-info"></i>
					{l s='Information' mod='mpsellervoucher'}
				</a>
			</li>
			<li>
				<a href="#conditions" data-toggle="tab">
					<i class="icon-random"></i>
					{l s='Conditions' mod='mpsellervoucher'}
				</a>
			</li>
			<li>
				<a href="#actions" data-toggle="tab">
					<i class="icon-wrench"></i>
					{l s='Actions' mod='mpsellervoucher'}
				</a>
			</li>
		</ul>
		<div class="tab-content panel collapse in">
			<div class="tab-pane active" id="information">
				<div class="form-group">
					<label class="control-label col-lg-3 required">
						<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='This will be displayed in the cart summary, as well as on the invoice.' mod='mpsellervoucher'}">
							{l s='Name' mod='mpsellervoucher'}
						</span>
					</label>
					<div class="col-sm-8">
						<div class="row">
							<div class="col-lg-10">
								{foreach from=$languages item=language}
									{assign var="voucher_name" value="name_`$language.id_lang`"}
									<input type="text"
									id="name_{$language.id_lang|escape:'html':'UTF-8'}"
									name="name_{$language.id_lang|escape:'html':'UTF-8'}"
									value="{if isset($smarty.post.voucher_name)}{$smarty.post.voucher_name|escape:'html':'UTF-8'}{elseif isset($voucher_detail)}{if isset($voucher_detail.name[$language['id_lang']])}{$voucher_detail.name[$language['id_lang']]|escape:'html':'UTF-8'}{/if}{/if}"
									class="form-control voucher_name_all"
									{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
								{/foreach}
							</div>
							{if $allow_multilang && $total_languages > 1}
							<div class="col-lg-2">
								<button type="button" id="voucher_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									{$current_lang.iso_code|escape:'html':'UTF-8'}
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									{foreach from=$languages item=language}
										<li>
											<a class="voucher_change_lang" href="#" data-lang-iso-code="{$language.iso_code|escape:'html':'UTF-8'}" data-id-lang="{$language.id_lang|escape:'html':'UTF-8'}">{$language.name|escape:'html':'UTF-8'}</a>
										</li>
									{/foreach}
								</ul>
							</div>
							{/if}
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title="{l s='For your eyes only. This will never be displayed to the customer.' mod='mpsellervoucher'}">
							{l s='Description' mod='mpsellervoucher'}
						</span>
					</label>
					<div class="col-lg-8">
						<div class="row">
							<div class="col-lg-10">
								<textarea name="description" rows="2" class="textarea-autosize">{if isset($smarty.post.description)}{$smarty.post.description|escape:'html':'UTF-8'}{elseif isset($voucher_detail)}{if $voucher_detail.description}{$voucher_detail.description|escape:'html':'UTF-8'}{/if}{/if}</textarea>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='This is the code users should enter to apply the voucher to a cart. Either create your own code or generate one by clicking on "Generate".' mod='mpsellervoucher'}">
							{l s='Code' mod='mpsellervoucher'}
						</span>
					</label>
					<div class="col-lg-8">
						<div class="input-group col-lg-4">
							<input type="text" id="code" name="code" value="{if isset($smarty.post.code)}{$smarty.post.code|escape:'html':'UTF-8'}{elseif isset($voucher_detail)}{if $voucher_detail.code}{$voucher_detail.code|escape:'html':'UTF-8'}{/if}{/if}" />
							<span class="input-group-btn">
								<a href="javascript:gencode(8);" class="btn btn-default"><i class="icon-random"></i> {l s='Generate' mod='mpsellervoucher'}</a>
							</span>
						</div>
						<span class="help-block">{l s='Caution! If you leave this field blank, the rule will automatically be applied to benefiting customers.'  mod='mpsellervoucher'}</span>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='If the voucher is not yet in the cart, it will be displayed in the cart summary.' mod='mpsellervoucher'}">
							{l s='Highlight' mod='mpsellervoucher'}
						</span>
					</label>
					<div class="col-lg-8">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="highlight" id="highlight_on" value="1"
							{if isset($smarty.post.highlight)}
								{if $smarty.post.highlight}checked="checked"{/if}
							{elseif isset($voucher_detail)}
								{if $voucher_detail.highlight}checked="checked"{/if}
							{/if}/>
							<label for="highlight_on">{l s='Yes' mod='mpsellervoucher'}</label>
							<input type="radio" name="highlight" id="highlight_off" value="0"
							{if isset($smarty.post.highlight)}
								{if !$smarty.post.highlight}checked="checked"{/if}
							{elseif isset($voucher_detail)}
								{if !$voucher_detail.highlight}checked="checked"{/if}
							{else}
								checked="checked"
							{/if}/>
							<label for="highlight_off">{l s='No' mod='mpsellervoucher'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>

				{* NOTE : Partial use functionality is removed from this module, for reason please check Readme.md file. *}
				{*<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='Only applicable if the voucher value is greater than the cart total.' mod='mpsellervoucher'}
						{l s='If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.' mod='mpsellervoucher'}">
							{l s='Partial use' mod='mpsellervoucher'}
						</span>
					</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="partial_use" id="partial_use_on" value="1"
							{if isset($smarty.post.partial_use)}
								{if $smarty.post.partial_use}checked="checked"{/if}
							{elseif isset($voucher_detail)}
								{if $voucher_detail.partial_use}checked="checked"{/if}
							{else}
								checked="checked"
							{/if}/>
							<label class="t" for="partial_use_on">{l s='Yes' mod='mpsellervoucher'}</label>
							<input type="radio" name="partial_use" id="partial_use_off" value="0"
							{if isset($smarty.post.partial_use)}
								{if !$smarty.post.partial_use}checked="checked"{/if}
							{elseif isset($voucher_detail)}
								{if !$voucher_detail.partial_use}checked="checked"{/if}
							{/if}/>
							<label class="t" for="partial_use_off">{l s='No' mod='mpsellervoucher'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>*}

				<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='Cart rules are applied by priority. A cart rule with a priority of "1" will be processed before a cart rule with a priority of "2".' mod='mpsellervoucher'}">
							{l s='Priority' mod='mpsellervoucher'}
						</span>
					</label>
					<div class="col-lg-1">
						<input type="text" class="input-mini" name="priority" value="{if isset($smarty.post.priority)}{$smarty.post.priority|escape:'html':'UTF-8'}{elseif isset($voucher_detail)}{if $voucher_detail.priority}{$voucher_detail.priority|escape:'html':'UTF-8'}{/if}{else}1{/if}"/>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Status' mod='mpsellervoucher'}</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="active" id="active_on" value="1"
							{if isset($smarty.post.active)}
								{if $smarty.post.active}checked="checked"{/if}
							{elseif isset($voucher_detail)}
								{if $voucher_detail.active}checked="checked"{/if}
							{else}
								checked="checked"
							{/if}/>
							<label class="t" for="active_on">{l s='Yes' mod='mpsellervoucher'}</label>
							<input type="radio" name="active" id="active_off" value="0"
							{if isset($smarty.post.active)}
								{if !$smarty.post.active}checked="checked"{/if}
							{elseif isset($voucher_detail)}
								{if !$voucher_detail.active}checked="checked"{/if}
							{/if}/>
							<label class="t" for="active_off">{l s='No' mod='mpsellervoucher'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="conditions">
				{if $MP_SELLER_CUSTOMER_VOUCHER_ALLOW}
					<div class="form-group">
						<label class="control-label col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip"
								title="{l s='Optional: The cart rule will be available to everyone if you leave this field blank.' mod='mpsellervoucher'}">
								{l s='Limit to a single customer' mod='mpsellervoucher'}
							</span>
						</label>
						<div class="col-lg-8">
							<div class="row">
								<div class="input-group col-lg-12">
									<span class="input-group-addon"><i class="icon-user"></i></span>
									<input type="hidden" class="input_primary" id="for_customer" name="for_customer" value="{if isset($smarty.post.for_customer)}{$smarty.post.for_customer|escape:'html':'UTF-8'}{elseif isset($voucher_detail)}{if isset($voucher_detail.customer)}{$voucher_detail.customer.id_customer}{/if}{/if}" />
									<input type="text" autocomplete="off" class="input-xlarge input_secondary" id="wk_customerFilter" name="customer_name" value="{if isset($smarty.post.customer_name)}{$smarty.post.customer_name|escape}{elseif isset($voucher_detail)}{if isset($voucher_detail.customer)}{$voucher_detail.customer['firstname']}&nbsp;{$voucher_detail.customer['lastname']}&nbsp;({$voucher_detail.customer['email']}){/if}{/if}"/>
									<span class="input-group-addon"><i class="icon-search"></i></span>
									<ul class="list-unstyled suggestion_ul"></ul>
								</div>
							</div>
						</div>
					</div>
				{/if}
				<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
							title="{l s='The default period is one month.' mod='mpsellervoucher'}">
							{l s='Valid' mod='mpsellervoucher'}
						</span>
					</label>
					<div class="col-lg-9">
						<div class="row">
							<div class="col-lg-6">
								<div class="input-group">
									<span class="input-group-addon">{l s='From' mod='mpsellervoucher'}</span>
									<input type="text" class="wk_datepicker input-medium" name="date_from"
									value="{if isset($smarty.post.date_from)}{$smarty.post.date_from}{elseif isset($voucher_detail)}{$voucher_detail.date_from|escape:'html':'UTF-8'}{else}{$defaultDateFrom}{/if}" />
									<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="input-group">
									<span class="input-group-addon">{l s='To' mod='mpsellervoucher'}</span>
									<input type="text" class="wk_datepicker input-medium" name="date_to"
									value="{if isset($smarty.post.date_to)}{$smarty.post.date_to}{elseif isset($voucher_detail)}{$voucher_detail.date_to|escape:'html':'UTF-8'}{else}{$defaultDateTo}{/if}"/>
									<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
							title="{l s='The cart rule will be applied to the first "X" customers only.' mod='mpsellervoucher'}">
							{l s='Total available' mod='mpsellervoucher'}
						</span>
					</label>
					<div class="col-lg-9">
						<input class="form-control" type="text" name="quantity" value="{if isset($smarty.post.quantity)}{$smarty.post.quantity}{elseif isset($voucher_detail)}{$voucher_detail.quantity|escape:'html':'UTF-8'}{else}1{/if}" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
							title="{l s='A customer will only be able to use the cart rule "X" time(s).' mod='mpsellervoucher'}">
							{l s='Total available for each user' mod='mpsellervoucher'}
						</span>
					</label>
					<div class="col-lg-9">
						<input class="form-control" type="text" name="quantity_per_user" value="{if isset($smarty.post.quantity_per_user)}{$smarty.post.quantity_per_user}{elseif isset($voucher_detail)}{$voucher_detail.quantity_per_user|escape:'html':'UTF-8'}{else}1{/if}" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">
						{l s='Restrictions' mod='mpsellervoucher'}
					</label>
					<div class="col-lg-9">
						{if isset($voucher_detail)}
							{if isset($voucher_detail.countries)}
								{assign var=countries_count value=$voucher_detail.countries.unselected|@count + $voucher_detail.countries.selected|@count}
							{else}
								{assign var=countries_count value=0}
							{/if}
						{else}
							{assign var=countries_count value=$countries|@count}
						{/if}
						{if $countries_count > 1}
							<p class="checkbox">
								<label>
									<input type="checkbox" id="country_restriction" name="country_restriction" value="1"
									{if isset($smarty.post.country_restriction)}
										{if $smarty.post.country_restriction}checked="checked"{/if}
									{elseif isset($voucher_detail)}
										{if $voucher_detail.country_restriction}checked{/if}
									{/if}/>
									{l s='Country selection' mod='mpsellervoucher'}
								</label>
							</p>
							<span class="help-block">{l s='This restriction applies to the country of delivery.' mod='mpsellervoucher'}</span>
							<div id="block_country_restriction">
								<br />
								<table class="table">
									<tr>
										<td>
											<p>{l s='Unselected countries' mod='mpsellervoucher'}</p>
											<select id="country_select_1" multiple>
												{if isset($voucher_detail)}
													{if isset($voucher_detail.countries)}
														{foreach from=$voucher_detail.countries.unselected item='country'}
															<option value="{$country.id_country|intval}">&nbsp;{$country.name|escape}</option>
														{/foreach}
													{/if}
												{/if}
											</select>
											<a id="country_select_add" class="btn  btn-default btn-block clearfix wk_move_right">{l s='Add' mod='mpsellervoucher'} <i class="icon-arrow-right"></i></a>
										</td>
										<td>
											<p>{l s='Selected countries' mod='mpsellervoucher'}</p>
											<select name="country_select[]" id="country_select_2" class="input-large selected_option" multiple>
												{if isset($voucher_detail)}
													{if isset($voucher_detail.countries)}
														{foreach from=$voucher_detail.countries.selected item='country'}
															<option value="{$country.id_country|intval}">&nbsp;{$country.name|escape}</option>
														{/foreach}
													{/if}
												{else}
													{foreach from=$countries item='country'}
														<option value="{$country.id_country|intval}">&nbsp;{$country.name|escape}</option>
													{/foreach}
												{/if}
											</select>
											<a id="country_select_remove" class="btn btn-default btn-block clearfix wk_move_left"><i class="icon-arrow-left"></i> {l s='Remove' mod='mpsellervoucher'} </a>
										</td>
									</tr>
								</table>
							</div>
						{/if}

						{if isset($voucher_detail)}
							{if isset($voucher_detail.groups)}
								{assign var=groups_count value=$voucher_detail.groups.unselected|@count + $voucher_detail.groups.selected|@count}
							{else}
								{assign var=groups_count value=0}
							{/if}
						{else}
							{assign var=groups_count value=$groups|@count}
						{/if}
						{if $groups_count > 1}
							<p class="checkbox">
								<label>
									<input type="checkbox" id="group_restriction" name="group_restriction" value="1"
									{if isset($smarty.post.group_restriction)}
										{if $smarty.post.group_restriction}checked="checked"{/if}
									{elseif isset($voucher_detail)}
										{if $voucher_detail.group_restriction}checked="checked"{/if}
									{/if}/>
									{l s='Customer group selection' mod='mpsellervoucher'}
								</label>
							</p>
							<div id="block_group_restriction">
								<br />
								<table class="table">
									<tr>
										<td>
											<p>{l s='Unselected groups' mod='mpsellervoucher'}</p>
											<select id="group_select_1" class="input-large" multiple>
												{if isset($voucher_detail)}
													{if isset($voucher_detail.groups)}
														{foreach from=$voucher_detail.groups.unselected item='group'}
															<option value="{$group.id_group|intval}">&nbsp;{$group.name|escape}</option>
														{/foreach}
													{/if}
												{/if}
											</select>
											<a id="group_select_add" class="btn btn-default btn-block clearfix wk_move_right" >{l s='Add' mod='mpsellervoucher'} <i class="icon-arrow-right"></i></a>
										</td>
										<td>
											<p>{l s='Selected groups' mod='mpsellervoucher'}</p>
											<select name="group_select[]" class="input-large selected_option" id="group_select_2" multiple>
												{if isset($voucher_detail)}
													{if isset($voucher_detail.groups)}
														{foreach from=$voucher_detail.groups.selected item='group'}
															<option value="{$group.id_group|intval}">&nbsp;{$group.name|escape}</option>
														{/foreach}
													{/if}
												{else}
													{foreach from=$groups item='group'}
														<option value="{$group.id_group|intval}">&nbsp;{$group.name|escape}</option>
													{/foreach}
												{/if}
											</select>
											<a id="group_select_remove" class="btn btn-default btn-block clearfix wk_move_left"><i class="icon-arrow-left"></i> {l s='Remove' mod='mpsellervoucher'}</a>
										</td>
									</tr>
								</table>
							</div>
						{/if}
					</div>
				</div>
			</div>

			<div class="tab-pane" id="actions">
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Apply a discount' mod='mpsellervoucher'}</label>
					<div class="col-lg-9">
						<div class="radio">
							<label for="percentage">
								<input type="radio" name="reduction_type" class="reduction_type" id="percentage" value="1"
								{if isset($smarty.post.reduction_type)}
									{if ($smarty.post.reduction_type == 1)}checked{/if}
								{elseif isset($voucher_detail)}
									{if ($voucher_detail.reduction_type == 1)}checked{/if}
								{else}
									checked
								{/if}>
								{l s='Percent (%)' mod='mpsellervoucher'}
							</label>
						</div>
						<div class="radio">
							<label for="amount">
								<input type="radio" name="reduction_type" class="reduction_type" id="amount" value="2"
								{if isset($smarty.post.reduction_type)}
									{if ($smarty.post.reduction_type == 2)}checked{/if}
								{elseif isset($voucher_detail)}
									{if ($voucher_detail.reduction_type == 2)}checked{/if}
								{/if}>
								{l s='Amount' mod='mpsellervoucher'}
							</label>
						</div>
					</div>
				</div>

				<div id="reduction_type_percent" class="form-group">
					<label class="control-label col-lg-3">{l s='Value' mod='mpsellervoucher'}</label>
					<div class="col-lg-9">
						<div class="input-group col-lg-2">
							<span class="input-group-addon">%</span>
							<input type="text" id="reduction_percent" class="input-mini" name="reduction_percent" value="{if isset($smarty.post.reduction_percent)}{$smarty.post.reduction_percent|escape:'html':'UTF-8'}{elseif isset($voucher_detail)}{$voucher_detail.reduction_percent}{else}0{/if}"/>
						</div>
						<span class="help-block"><i class="icon-warning-sign"></i> {l s='Does not apply to the shipping costs' mod='mpsellervoucher'}</span>
					</div>
				</div>

				<div id="reduction_type_amount" class="form-group">
					<label class="control-label col-lg-3">{l s='Amount' mod='mpsellervoucher'}</label>
					<div class="col-lg-7">
						<div class="row">
							<div class="col-lg-4">
								<input type="text" id="reduction_amount" name="reduction_amount" value="{if isset($smarty.post.reduction_amount)}{$smarty.post.reduction_amount|escape:'html':'UTF-8'}{elseif isset($voucher_detail)}{$voucher_detail.reduction_amount}{else}0{/if}"/>
							</div>
							<div class="col-lg-4">
								<select name="reduction_currency">
									{foreach from=$currencies item='currency'}
										<option value="{$currency.id_currency|intval}"
										{if isset($smarty.post.reduction_currency)}
											{if $smarty.post.reduction_currency == $currency.id_currency}selected="selected"{/if}
										{elseif isset($voucher_detail)}
											{if $voucher_detail.currency.id_currency == $currency.id_currency}selected="selected"{/if}
										{else}
											{if $PS_CURRENCY_DEFAULT == $currency.id_currency}selected="selected"{/if}
										{/if}>
											{$currency.iso_code}
										</option>
									{/foreach}
								</select>
							</div>
							<div class="col-lg-4">
								<select name="reduction_tax">
									<option value="0"
									{if isset($smarty.post.reduction_tax)}
										{if !$smarty.post.reduction_tax}selected="selected"{/if}
									{elseif isset($voucher_detail)}
										{if !$voucher_detail.reduction_tax}selected="selected"{/if}
									{else}
										selected="selected"
									{/if}>{l s='Tax excluded' mod='mpsellervoucher'}</option>
									<option value="1"
									{if isset($smarty.post.reduction_tax)}
										{if $smarty.post.reduction_tax}selected="selected"{/if}
									{elseif isset($voucher_detail)}
										{if $voucher_detail.reduction_tax}selected="selected"{/if}
									{/if}>{l s='Tax included' mod='mpsellervoucher'}</option>
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Apply a discount to' mod='mpsellervoucher'}</label>
					<div class="col-lg-9">
						<div class="radio" id="specific_product_btn">
							<label for="specific_product">
								<input type="radio" name="reduction_for" class="reduction_for" id="specific_product" value="1"
								{if isset($smarty.post.reduction_for)}
									{if ($smarty.post.reduction_for == 1)}checked{/if}
								{elseif isset($voucher_detail)}
									{if ($voucher_detail.reduction_for == 1)}checked{/if}
								{else}
									checked
								{/if}>
								{l s='Specific Product' mod='mpsellervoucher'}
							</label>
						</div>
						<div class="radio" id="multiple_product_btn">
							<label for="multiple_product">
								<input type="radio" name="reduction_for" class="reduction_for" id="multiple_product" value="2"
								{if isset($smarty.post.reduction_for)}
									{if ($smarty.post.reduction_for == 2)}checked{/if}
								{elseif isset($voucher_detail)}
									{if ($voucher_detail.reduction_for == 2)}checked{/if}
								{/if}>
								{l s='Multiple Products' mod='mpsellervoucher'}
							</label>
						</div>
					</div>
				</div>

				<div class="form-group" id="specific-product-section">
					<label class="control-label col-sm-3">{l s='Product' mod='mpsellervoucher'}</label>
					<div class="col-sm-9">
						<div class="input-group col-sm-5">
							<input type="text" autocomplete="off" class="input_secondary" id="mpReductionProductFilter" name="mpReductionProductFilter"
							value="{if isset($smarty.post.mpReductionProductFilter)}{$smarty.post.mpReductionProductFilter}{elseif isset($voucher_detail) && isset($voucher_detail.specific_prod.id_mp_product)}{$voucher_detail.specific_prod.product_name}{/if}"/>
							<input type="hidden" class="input_primary" id="mp_reduction_product" name="mp_reduction_product" value="{if isset($smarty.post.mp_reduction_product)}{$smarty.post.mp_reduction_product}{elseif isset($voucher_detail) && isset($voucher_detail.specific_prod.id_mp_product)}{$voucher_detail.specific_prod.id_mp_product}{/if}"/>
							<span class="input-group-addon"><i class="icon-search"></i></span>
							<ul class="list-unstyled suggestion_ul"></ul>
						</div>
					</div>
				</div>

				<div class="form-group" id="multiple-product-section">
					<label class="control-label col-sm-3">{l s='Select Products ' mod='mpsellervoucher'}</label>
					<div class="col-sm-6">
						<select name="multiple_reduction_product[]" class="form-control" multiple="multiple" id="multiple_reduction_product">
							{if $sellerProducts}
								{foreach from=$sellerProducts item=mpProduct}
									<option value="{$mpProduct['mp_id_prod']}" {if isset($smarty.post.multiple_reduction_product) && ($smarty.post.multiple_reduction_product|@count > 0)}{if in_array($mpProduct['mp_id_prod'], $smarty.post.multiple_reduction_product)}selected{/if}{elseif isset($voucher_detail) && ($voucher_detail.multiple_prod|@count > 0)}{if in_array($mpProduct['mp_id_prod'], $voucher_detail.multiple_prod)}selected{/if}{/if}>{$mpProduct['product_name']}</option>
								{/foreach}
							{/if}
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminSellerVoucher')|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='mpsellervoucher'}
			</a>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}" class="btn btn-default pull-right" id="mp_admin_save_button">
				<i class="process-icon-save"></i> {l s='Save' mod='mpsellervoucher'}
			</button>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right" id="mp_admin_saveas_button">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='mpsellervoucher'}
			</button>
		</div>
	</form>
</div>
{strip}
{addJsDef controller_link = $link->getAdminLink('AdminSellerVoucher')}
{addJsDefL name=currentText}{l s='Now' js=1 mod='mpsellervoucher'}{/addJsDefL}
{addJsDefL name=closeText}{l s='Done' js=1 mod='mpsellervoucher'}{/addJsDefL}
{addJsDefL name=timeOnlyTitle}{l s='Choose Time' js=1 mod='mpsellervoucher'}{/addJsDefL}
{addJsDefL name=timeText}{l s='Time' js=1 mod='mpsellervoucher'}{/addJsDefL}
{addJsDefL name=hourText}{l s='Hour' js=1 mod='mpsellervoucher'}{/addJsDefL}
{addJsDefL name=minuteText}{l s='Minute' js=1 mod='mpsellervoucher'}{/addJsDefL}
{/strip}