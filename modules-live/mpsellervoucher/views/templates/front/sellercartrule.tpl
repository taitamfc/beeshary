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

{extends file=$layout}
{block name='content'}
{if isset($smarty.get.edit_conf)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Updated Successfully' mod='mpsellervoucher'}
	</p>
{/if}

{if isset($smarty.get.add_conf)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Added Successfully' mod='mpsellervoucher'}
	</p>
{/if}

{if $logged}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color};">
				<span style="color:{$title_text_color};">
					{if !isset($voucher_detail)}
						{l s='Add Voucher' mod='mpsellervoucher'}
					{else}
						{l s='Edit Voucher' mod='mpsellervoucher'}
					{/if}
				</span>
			</div>
			<div class="wk-mp-right-column">
				<form action="{if !isset($voucher_detail)}{$link->getModuleLink('mpsellervoucher', 'sellercartrule')}{else}{$link->getModuleLink('mpsellervoucher', 'sellercartrule', ['id_mp_cart_rule' => {$voucher_detail.id_mp_cart_rule}])}{/if}" method="post" class="std contact-form-box" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16">
					<div class="tabs">
						<ul class="nav nav-tabs">
							<li class="nav-item">
								<a class="nav-link active" href="#information" data-toggle="tab">
									<i class="material-icons">&#xE88E;</i>
									{l s='Information' mod='mpsellervoucher'}
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#conditions" data-toggle="tab">
									<i class="material-icons">&#xE043;</i>
									{l s='Conditions' mod='mpsellervoucher'}
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#actions" data-toggle="tab">
									<i class="material-icons">&#xE869;</i>
									{l s='Actions' mod='mpsellervoucher'}
								</a>
							</li>
						</ul>
						<div class="tab-content" id="tab-content">
							<div class="tab-pane fade in active" id="information">
								<div class="form-group">
									<label for="voucher_name" class="control-label required">{l s='Name :' mod='mpsellervoucher'}</label>
									<div class="row">
										{if $allow_multilang && $total_languages > 1}
										<div class="col-sm-10">
										{else}
										<div class="col-sm-12">
										{/if}
											{foreach from=$languages item=language}
												{assign var="voucher_name" value="name_`$language.id_lang`"}
												<input type="text"
												id="name_{$language.id_lang}"
												name="name_{$language.id_lang}"
												value="{if isset($smarty.post.voucher_name)}{$smarty.post.voucher_name}{elseif isset($voucher_detail)}{if isset($voucher_detail.name[$language['id_lang']])}{$voucher_detail.name[$language['id_lang']]}{/if}{/if}"
												class="form-control voucher_name_all {if $current_lang.id_lang == $language.id_lang}seller_default_lang_class{/if}"
												data-lang-name="{$language.name}"
												{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
											{/foreach}
										</div>
										{if $allow_multilang && $total_languages > 1}
										<div class="col-sm-2">
											<button type="button" id="voucher_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
												{$current_lang.iso_code}
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu">
												{foreach from=$languages item=language}
													<li>
														<a class="voucher_change_lang" href="#" data-lang-iso-code="{$language.iso_code}" data-id-lang="{$language.id_lang}">{$language.name}</a>
													</li>
												{/foreach}
											</ul>
										</div>
										{/if}
									</div>
								</div>
								<div class="form-group">
									<label for="voucher_desc" class="control-label">{l s='Description :' mod='mpsellervoucher'}</label>
									<div class="row">
										<div class="col-sm-12">
											<textarea id="voucher_desc" name="description" cols="2" rows="3" class="form-control">{if isset($smarty.post.description)}{$smarty.post.description}{elseif isset($voucher_detail)}{if $voucher_detail.description}{$voucher_detail.description}{/if}{/if}</textarea>
									  	</div>
								  	</div>
								</div>
								<div class="form-group">
									<label for="voucher_code" class="control-label">
										{l s='Code :' mod='mpsellervoucher'}
									</label>
									<div class="row">
										<div class="col-sm-12">
											<div class="row">
												<div class="col-sm-5 col-xs-12">
													<div class="input-group codeGenerateCont">
														<input type="text" value="{if isset($smarty.post.code)}{$smarty.post.code}{elseif isset($voucher_detail)}{if $voucher_detail.code}{$voucher_detail.code}{/if}{/if}" name="code" id="voucher_code" class="form-control voucher_form_field">
														<span class="input-group-btn">
															<a class="btn btn-default" href="#" id="generateVoucherCode">
															<i class="material-icons">&#xE043;</i>&nbsp;{l s='Generate' mod='mpsellervoucher'}</a>
														</span>
													</div>
												</div>
											</div>
											<p class="help-block">
											<i class="icon-warning-sign"></i>&nbsp;{l s='Caution! If you leave this field blank, the rule will automatically be applied to benefiting customers.' mod='mpsellervoucher'}</p>
										</div>
								  	</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-sm-2">
											<label class="control-label">
												{l s='Highlight :' mod='mpsellervoucher'}
											</label>
										</div>
										<div class="col-sm-10">
											<div class="row">
												<div class="col-sm-12">
													<label for="highlight_yes">
														<input type="radio" name="highlight" value="1" id="highlight_yes"
															{if isset($smarty.post.highlight)}
																{if $smarty.post.highlight}checked="checked"{/if}
															{elseif isset($voucher_detail)}
																{if $voucher_detail.highlight}checked="checked"{/if}
															{/if}>
														<span>{l s='Yes' mod='mpsellervoucher'}</span>
													</label>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<label for="highlight_no">
														<input type="radio" name="highlight" value="0" id="highlight_no"
															{if isset($smarty.post.highlight)}
																{if !$smarty.post.highlight}checked="checked"{/if}
															{elseif isset($voucher_detail)}
																{if !$voucher_detail.highlight}checked="checked"{/if}
															{else}
																checked="checked"
															{/if}>
														<span>{l s='No' mod='mpsellervoucher'}</span>
													</label>
												</div>
											</div>
									  	</div>
									</div>
								</div>
								{* NOTE : Partial use functionality is removed from this module, for reason please check Readme.md file. *}
								{*<div class="form-group">
									<div class="row">
										<div class="col-sm-2">
											<label class="control-label">
												{l s='Partial Use :' mod='mpsellervoucher'}
											</label>
										</div>
										<div class="col-sm-10">
											<div class="row">
												<div class="col-sm-12">
													<label for="partial_use_yes">
														<input type="radio" name="partial_use" value="1" id="partial_use_yes"
														{if isset($smarty.post.partial_use)}
															{if $smarty.post.partial_use}checked="checked"{/if}
														{elseif isset($voucher_detail)}
															{if $voucher_detail.partial_use}checked="checked"{/if}
														{else}
															checked="checked"
														{/if}>
														<span>{l s='Yes' mod='mpsellervoucher'}</span>
													</label>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<label for="partial_use_no">
														<input type="radio" name="partial_use" value="0" id="partial_use_no"
														{if isset($smarty.post.partial_use)}
															{if !$smarty.post.partial_use}checked="checked"{/if}
														{elseif isset($voucher_detail)}
															{if !$voucher_detail.partial_use}checked="checked"{/if}
														{/if}>
														<span>{l s='No' mod='mpsellervoucher'}</span>
													</label>
												</div>
											</div>
									  	</div>
									</div>
								</div>*}
								<div class="form-group">
									<label class="control-label" for="voucher_priority">
										{l s='Priority :' mod='mpsellervoucher'}
									</label>
									<div class="row">
										<div class="col-sm-4">
											<input type="text" id="voucher_priority" class="form-control voucher_form_field" name="priority"
											value="{if isset($smarty.post.priority)}{$smarty.post.priority}{elseif isset($voucher_detail)}{if $voucher_detail.priority}{$voucher_detail.priority}{/if}{/if}">
									  	</div>
								  	</div>
								</div>
							</div>
							<div class="tab-pane fade in" id="conditions">
								{if isset($customers)}
									<div class="form-group">
										<label class="control-label">
											{l s='Limit to a single customer :' mod='mpsellervoucher'}
										</label>
										<div class="row">
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-12">
														<select name="for_customer" class="form-control form-control-select">
															<option value="0">{l s='Select Customer' mod='mpsellervoucher'}</option>
															{foreach from=$customers item=customer}
																<option value="{$customer['id_customer']}"
																{if isset($smarty.post.for_customer)}{if ($smarty.post.for_customer == $customer['id_customer'])}selected="selected"{/if}{elseif isset($voucher_detail)}{if isset($voucher_detail.customer)}{if ($voucher_detail.customer['id_customer'] == $customer['id_customer'])}selected="selected"{/if}{/if}{/if}>{$customer['firstname']}&nbsp;{$customer['lastname']}&nbsp;({$customer['email']})</option>
															{/foreach}
														</select>
													</div>
													{* Commented because of new prestashop theme, can be used afterwards when module improve *}
													{*<div class="col-sm-10">
														<div class="dropdown wk_dropdown_cont" id="customer_btn">
															<button class="btn btn-default dropdown-toggle wk_dropdown_btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<span class="span_display">
																	{if isset($smarty.post.customer_name_dtl)}
																		{$smarty.post.customer_name_dtl}
																	{elseif isset($voucher_detail)}
																		{if isset($voucher_detail.customer)}
																			{$voucher_detail.customer['firstname']}&nbsp;{$voucher_detail.customer['lastname']}&nbsp;({$voucher_detail.customer['email']})
																		{/if}
																	{/if}
																</span>
																<span class="caret"></span>
																<input class="input_primary" type="hidden" name="for_customer"
																value="{if isset($smarty.post.for_customer)}{$smarty.post.for_customer}{elseif isset($voucher_detail)}{if isset($voucher_detail.customer)}{$voucher_detail.customer['id_customer']}{/if}{/if}">
																<input class="input_secondary" type="hidden" name="customer_name_dtl"
																value="{if isset($smarty.post.customer_name_dtl)}{$smarty.post.customer_name_dtl}{elseif isset($voucher_detail)}{if isset($voucher_detail.customer)}{$voucher_detail.customer['firstname']}&nbsp;{$voucher_detail.customer['lastname']}&nbsp;({$voucher_detail.customer['email']}){/if}{/if}">
															</button>
															<ul class="dropdown-menu">
																{foreach from=$customers item=customer}
																	<li><a href="#" data-primary="{$customer['id_customer']}" data-secondary="{$customer['firstname']}&nbsp;{$customer['lastname']}&nbsp;({$customer['email']})" class="dropdown_a">{$customer['firstname']}&nbsp;{$customer['lastname']}&nbsp;({$customer['email']})</a></li>
																{/foreach}
															</ul>
														</div>
												  	</div>
												  	<div class="col-sm-2">
												  		<button class="btn btn-default" id="empty_customer" type="button">
												  			<span>
												  				{l s='Empty' mod='mpsellervoucher'}
												  			</span>
												  		</button>
												  	</div>*}
												</div>
										  		<p class="help-block"></i>&nbsp;{l s='Optional: The cart rule will be available to everyone if you leave this field blank.' mod='mpsellervoucher'}</p>
											</div>
									  	</div>
									</div>
						    	{/if}
						    	<div class="form-group">
									<label class="control-label">
										{l s='Valid :' mod='mpsellervoucher'}
									</label>
									<div class="row">
										<div class="col-sm-6">
											<div class="input-group">
												<span class="input-group-addon">{l s='From' mod='mpsellervoucher'}</span>
												<input readonly type="text" class="form-control voucher_form_field wk_datetimepicker" autocomplete="off" name="date_from" value="{if isset($smarty.post.date_from)}{$smarty.post.date_from}{elseif isset($voucher_detail)}{$voucher_detail.date_from}{else}{$defaultDateFrom}{/if}">
												<span class="input-group-addon"><i class="material-icons">&#xE916;</i></span>
											</div>
									  	</div>
									  	<div class="col-sm-6">
											<div class="input-group">
												<span class="input-group-addon">{l s='To' mod='mpsellervoucher'}</span>
												<input readonly type="text" class="form-control voucher_form_field wk_datetimepicker" autocomplete="off" name="date_to" value="{if isset($smarty.post.date_to)}{$smarty.post.date_to}{elseif isset($voucher_detail)}{$voucher_detail.date_to}{else}{$defaultDateTo}{/if}">
												<span class="input-group-addon"><i class="material-icons">&#xE916;</i></span>
											</div>
									  	</div>
								  	</div>
								</div>
						    	<div class="form-group">
									<label class="control-label" for="voucher_quantity">
										{l s='Total available :' mod='mpsellervoucher'}
									</label>
									<div class="row">
										<div class="col-sm-12">
											<input type="text" id="voucher_quantity" class="form-control voucher_form_field" name="quantity"
											value="{if isset($smarty.post.quantity)}{$smarty.post.quantity}{elseif isset($voucher_detail)}{$voucher_detail.quantity}{else}1{/if}">
									  	</div>
								  	</div>
								</div>
								<div class="form-group">
									<label class="control-label" for="voucher_quantity_per_user">
										{l s='Total available for each user :' mod='mpsellervoucher'}
									</label>
									<div class="row">
										<div class="col-sm-12">
											<input type="text" id="voucher_quantity_per_user" class="form-control voucher_form_field" name="quantity_per_user"
											value="{if isset($smarty.post.quantity_per_user)}{$smarty.post.quantity_per_user}{elseif isset($voucher_detail)}{$voucher_detail.quantity_per_user}{else}1{/if}">
									  	</div>
								  	</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-sm-2">
											<label class="control-label">
												{l s='Restrictions :' mod='mpsellervoucher'}
											</label>
											<p class="help-block"><i class="material-icons">&#xE8B2;</i>&nbsp;{l s='This voucher can only be used for selected values.' mod='mpsellervoucher'}</p>
										</div>
										<div class="col-sm-10 restriction_cont">
											{if $countries|@count > 1}
												<div class="row">
													<div class="col-sm-12 rest_checkbox">
														<label>
															<input type="checkbox" name="country_restriction" value="1" class="restriction_type"
															{if isset($smarty.post.country_restriction)}
																{if $smarty.post.country_restriction}checked{/if}
															{elseif isset($voucher_detail)}
																{if $voucher_detail.country_restriction}checked{/if}
															{/if}>
															<span>{l s='Country selection' mod='mpsellervoucher'}</span>
														</label>
												  	</div>
												  	<div class="col-sm-4 rest_maincont">
												  		<select class="form-control" name="country_select[]" multiple="">
												  			{foreach from=$countries item=country}
												  				<option value="{$country['id_country']}"
												  				{if isset($smarty.post.country_select[$country.id_country])}
												  					selected="selected"
												  				{elseif isset($voucher_detail)}
												  					{if isset($voucher_detail.countries[$country.id_country])}
													  					selected="selected"
												  					{/if}
												  				{/if}>{$country['name']}</option>
															{/foreach}
												  		</select>
												  	</div>
											  	</div>
										  	{/if}
										  	<div class="row">
												<div class="col-sm-12 rest_checkbox">
													<label>
														<input type="checkbox" name="group_restriction" value="1" class="restriction_type"
														{if isset($smarty.post.group_restriction)}
															{if $smarty.post.group_restriction}checked{/if}
														{elseif isset($voucher_detail)}
															{if $voucher_detail.group_restriction}checked{/if}
														{/if}>
														<span>{l s='Customer group selection' mod='mpsellervoucher'}</span>
													</label>
											  	</div>
											  	<div class="col-sm-4 rest_maincont">
											  		<select class="form-control" name="group_select[]" multiple="">
											  			{foreach from=$groups item=group}
											  				<option value="{$group['id_group']}"
											  				{if isset($smarty.post.group_select[$group.id_group])}
											  					selected="selected"
											  				{elseif isset($voucher_detail)}
											  					{if isset($voucher_detail.groups[$group.id_group])}
												  					selected="selected"
											  					{/if}
											  				{/if}>{$group['name']}</option>
														{/foreach}
											  		</select>
											  	</div>
										  	</div>
										  	{*{if isset($cart_rule_restriction)}
											  	<div class="row">
													<div class="col-sm-12 rest_checkbox">
														<label>
															<input type="checkbox" name="cart_rule_restriction" value="1" class="restriction_type">
															<span>{l s='Compatibility with other cart rules' mod='mpsellervoucher'}</span>
														</label>
												  	</div>
												  	<div class="col-sm-4 rest_maincont">
												  		<select class="form-control" name="cart_rule_select[]" multiple="">
												  			{foreach from=$cart_rule_restriction item=cart_rule}
												  				<option value="{$cart_rule['id_mp_cart_rule']}">{$cart_rule['name']}</option>
															{/foreach}
												  		</select>
												  	</div>
											  	</div>
										  	{/if}*}
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade in" id="actions">
						    	<div class="form-group">
									<div class="row">
										<div class="col-sm-2">
											<label class="control-label">
												{l s='Apply a discount :' mod='mpsellervoucher'}
											</label>
										</div>
										<div class="col-sm-10">
											<div class="row">
												<div class="col-sm-12">
													<label for="percentage">
														<input type="radio" name="reduction_type" value="1" id="percentage" class="reduction_type"
														{if isset($smarty.post.reduction_type)}
															{if ($smarty.post.reduction_type == 1)}checked{/if}
														{elseif isset($voucher_detail)}
										  					{if ($voucher_detail.reduction_type == 1)}checked{/if}
														{else}checked{/if}>
														<span>{l s='Percent' mod='mpsellervoucher'}&nbsp;(%)</span>
													</label>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<label for="amount">
														<input type="radio" name="reduction_type" value="2" id="amount" class="reduction_type"
														{if isset($smarty.post.reduction_type)}
															{if ($smarty.post.reduction_type == 2)}checked{/if}
														{elseif isset($voucher_detail)}
										  					{if ($voucher_detail.reduction_type == 2)}checked{/if}
														{/if}>
														<span>{l s='Amount ' mod='mpsellervoucher'}</span>
													</label>
												</div>
											</div>
									  	</div>
									</div>
								</div>
								<div class="form-group" id="reduction_type_percent">
									<label class="control-label">
										{l s='Value :' mod='mpsellervoucher'}
									</label>
									<div class="row">
										<div class="col-sm-4">
											<div class="input-group">
												<span class="input-group-addon">%</span>
												<input type="text" class="form-control voucher_form_field" name="reduction_percent"
												value="{if isset($smarty.post.reduction_percent)}{$smarty.post.reduction_percent}{elseif isset($voucher_detail)}{$voucher_detail.reduction_percent}{else}0{/if}">
											</div>
									  	</div>
								  	</div>
								  	<span class="help-block"><i class="icon-warning-sign"></i>&nbsp;{l s='Does not apply to the shipping costs' mod='mpsellervoucher'}</span>
								</div>
								<div class="form-group" id="reduction_type_amount">
									<label class="control-label">
										{l s='Amount :' mod='mpsellervoucher'}
									</label>
									<div class="row">
										<div class="col-lg-4">
											<input type="text" class="form-control voucher_form_field"
											value="{if isset($smarty.post.reduction_amount)}{$smarty.post.reduction_amount}{elseif isset($voucher_detail)}{$voucher_detail.reduction_amount}{else}0{/if}"
											name="reduction_amount" id="reduction_amount">
										</div>
										<div class="col-lg-4">
											<select name="reduction_currency" class="form-control form-control-select">
												{foreach from=$currencies item=currency}
													<option value="{$currency['id_currency']}" {if isset($smarty.post.reduction_currency)}{if ($smarty.post.reduction_currency == $currency['id_currency'])}selected="selected"{/if}{elseif isset($voucher_detail)}{if ($voucher_detail.reduction_currency == $currency['id_currency'])}selected="selected"{/if}{else}{if ($current_currency['id_currency'] == $currency['id_currency'])}selected="selected"{/if}{/if}>{$currency['iso_code']}</option>
												{/foreach}
											</select>
										</div>
										<div class="col-lg-4">
											{if isset($smarty.post.reduction_tax)}
												{assign var="reduction_tax" value=$smarty.post.reduction_tax}
											{elseif isset($voucher_detail)}
												{assign var="reduction_tax" value=$voucher_detail.reduction_tax}
											{else}
												{assign var="reduction_tax" value=0}
											{/if}
											<select name="reduction_tax" class="form-control form-control-select">
												<option value="0" {if !$reduction_tax}selected="selected"{/if}>{l s='Tax excluded' mod='mpsellervoucher'}</option>
												<option value="1" {if $reduction_tax}selected="selected"{/if}>{l s='Tax included' mod='mpsellervoucher'}</option>
											</select>
										</div>
									</div>
								</div>

								<div class="form-group">
									<div class="row">
										<div class="col-sm-3">
											<label class="control-label">
												{l s='Apply a discount to :' mod='mpsellervoucher'}
											</label>
										</div>
										<div class="col-sm-9">
											<div class="row">
												<div class="col-sm-12">
													<label for="specific_product">
														<input type="radio" class="reduction_for" name="reduction_for" value="1" id="specific_product"
														{if isset($smarty.post.reduction_for)}
															{if ($smarty.post.reduction_for == 1)}checked{/if}
														{elseif isset($voucher_detail)}
															{if ($voucher_detail.reduction_for == 1)}checked{/if}
														{else}checked{/if}>
														<span>{l s='Specific Product' mod='mpsellervoucher'}</span>
													</label>
												</div>
											</div>
											<div class="row" id="multiple_product_btn">
												<div class="col-sm-12">
													<label for="multiple_product">
														<input type="radio" class="reduction_for" name="reduction_for" value="2" id="multiple_product"
														{if isset($smarty.post.reduction_for)}
															{if ($smarty.post.reduction_for == 2)}checked{/if}
														{elseif isset($voucher_detail)}
															{if ($voucher_detail.reduction_for == 2)}checked{/if}
														{/if}>
														<span>{l s='Multiple Products' mod='mpsellervoucher'}</span>
													</label>
												</div>
											</div>
										</div>
									</div>
								</div>


								<div class="form-group" id="specific-product-section">
									<label class="control-label">
										{l s='Select Product :' mod='mpsellervoucher'}
									</label>
									<div class="row">
										<div class="col-lg-5">
											<div class="input-group">
												<input type="text" autocomplete="off" class="form-control voucher_form_field" id="mpReductionProductFilter" name="mp_prod_name"
												value="{if isset($smarty.post.mp_prod_name)}{$smarty.post.mp_prod_name}{elseif isset($voucher_detail) && isset($voucher_detail.specific_prod.id_mp_product)}{$voucher_detail.specific_prod.product_name}{/if}"/>
												<input type="hidden" id="mp_reduction_product" name="mp_reduction_product"
												value="{if isset($smarty.post.mp_reduction_product)}{$smarty.post.mp_reduction_product}{elseif isset($voucher_detail) && isset($voucher_detail.specific_prod.id_mp_product)}{$voucher_detail.specific_prod.id_mp_product}{/if}"/>
												<span class="input-group-addon">
													<i class="material-icons">&#xE8B6;</i>1
												</span>
												<ul class="list-unstyled suggestion_ul"></ul>
											</div>
										</div>
									</div>
								</div>

								<div class="form-group" id="multiple-product-section">
									<label class="control-label">
										{l s='Select Products :' mod='mpsellervoucher'}
									</label>
									<div class="row">
										<div class="col-sm-5">
											<select name="multiple_reduction_product[]" class="form-control" multiple="multiple">
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
							<div class="form-group" style="text-align:center;">
								<button type="submit" id="SubmitVoucher" name="SubmitVoucher" class="btn btn-primary">
									<span>
										{if !isset($voucher_detail)}
											{l s='Add Voucher' mod='mpsellervoucher'}
										{else}
											{l s='Update Voucher' mod='mpsellervoucher'}
										{/if}
									</span>
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
{else}
	<div class="alert alert-danger">
		{l s='You are logged out. Please login to add product.' mod='mpsellervoucher'}</span>
	</div>
{/if}
<script type="text/javascript">
	var mp_seller_id = {$mp_seller_id|intval};
</script>
{/block}