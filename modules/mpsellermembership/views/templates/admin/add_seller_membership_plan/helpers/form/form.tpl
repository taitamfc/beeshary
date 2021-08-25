{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<div class="panel-heading">
		<i class="icon-pencil"></i>
		{if isset($plan_info)}
			{l s='Edit MemberShip Plan' mod='mpsellermembership'}
		{else}
			{l s='Add MemberShip Plan' mod='mpsellermembership'}
		{/if}
	</div>
	<form id="{$table}_form" class="defaultForm {$name_controller} form-horizontal" action="{$current|escape:'quotes':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'quotes':'UTF-8'}{/if}&token={$token|escape:'quotes':'UTF-8'}" method="post" enctype="multipart/form-data">
	<div class="panel-body">
		{if isset($plan_info)}
			<input type="hidden" name="id" value="{$plan_info.id}" />
		{/if}
		{include file="$self/../../views/templates/admin/_partials/change-language.tpl"}
		<input type="hidden" name="current_lang" id="current_lang" value="{$current_lang.id_lang|escape:'html':'UTF-8'}">
		<div class="form-group">
			<label class="col-lg-3 control-label required">{l s='Plan Name' mod='mpsellermembership'} {include file="$self/../../views/templates/admin/_partials/membership-form-fields-flag.tpl"}</label>
			<div class="col-lg-5">
				{foreach from=$languages item=language}
					{assign var="plan_name" value="plan_name_`$language.id_lang`"}
					<input type="text" 
					id="plan_name_{$language.id_lang|escape:'html':'UTF-8'}" 
					name="plan_name_{$language.id_lang|escape:'html':'UTF-8'}" 
					class="form-control plan_name_all {if $current_lang.id_lang == $language.id_lang}plan_current{/if}"
					data-lang-name="{$language.name|escape:'html':'UTF-8'}"
					{if isset($plan_info)}value="{$plan_info.plan_name[{$language.id_lang|escape:'html':'UTF-8'}]|escape:'html':'UTF-8'}"{/if} 
					{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
				{/foreach}
			</div>
		</div>
				
		<div class="form-group">
			<label class="col-lg-3 control-label required">{l s='Plan Price' mod='mpsellermembership'}</label>
			<div class="col-lg-5">
				<input class="form-control" type="text" name="plan_price" {if isset($smarty.post.plan_price)} value="{$smarty.post.plan_price|escape:'html':'UTF-8'}" {elseif isset($plan_info)} value="{$plan_info.plan_price}" {/if}/>
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 control-label required">{l s='Plan Duration' mod='mpsellermembership'}</label>
			<div class="col-lg-3">
				<input class="form-control" type="text" name="plan_duration" {if isset($smarty.post.plan_duration)} value="{$smarty.post.plan_duration|escape:'html':'UTF-8'}" {elseif isset($plan_info)} value="{$plan_info.plan_duration}" {/if}/>
			</div>
			<div class="col-lg-2">
				<select name="plan_duration_type">
					<option value="1" selected="selected">{l s='Days' mod='mpsellermembership'}</option>
					<option value="30">{l s='Months' mod='mpsellermembership'}</option>
					<option value="360">{l s='Years' mod='mpsellermembership'}</option>
				</select>
			</div>
		</div>
				
		<div class="form-group">
			<label class="col-lg-3 control-label required">{l s='Number Of Product Allow' mod='mpsellermembership'}</label>
			<div class="col-lg-5">
				<input class="form-control" type="text" name="num_products_allow" {if isset($smarty.post.num_products_allow)} value="{$smarty.post.num_products_allow|escape:'html':'UTF-8'}" {elseif isset($plan_info)} value="{$plan_info.num_products_allow}" {/if}/>
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-lg-3 control-label required">{l s='Sequence' mod='mpsellermembership'}</label>
			<div class="col-lg-5">
				<input class="form-control" type="text" name="sequence_number" {if isset($smarty.post.sequence_number)} value="{$smarty.post.sequence_number|escape:'html':'UTF-8'}" {elseif isset($plan_info)} value="{$plan_info.sequence_number}" {/if}/>
			</div>
		</div>

		{if !isset($plan_info)}
			<div class="form-group">
				<label class="col-lg-3 control-label">{l s='Enable plan' mod='mpsellermembership'}</label>
				<div class="col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" checked="checked" value="1" id="plan_active_on" name="plan_active">
						<label for="plan_active_on">{l s='Yes' mod='mpsellermembership'}</label>
						<input type="radio" value="0" id="plan_active_off" name="plan_active">
						<label for="plan_active_off">{l s='No' mod='mpsellermembership'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		{/if}

		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='Plan Logo:' mod='mpsellermembership'}</label>
			<div class="col-lg-5">
				{if isset($plan_logo)}
					<br /><img class="img-thumbnail" src="{$plan_logo|escape:'quotes':'UTF-8'}" width="150" height="150" style="margin-bottom:5px;" />
				{/if}
				<input type="file" name="plan_logo" size="chars" />
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminAddSellerMembershipPlan')|escape:'html':'UTF-8'}" class="btn btn-default">
			<i class="process-icon-cancel"></i>{l s='Cancel' mod='mpsellermembership'}
		</a>
		<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}" class="btn btn-default pull-right">
			<i class="process-icon-save"></i>{l s='Save' mod='mpsellermembership'}
		</button>
	</div>
	</form>
</div>

<script type="text/javascript">
function showMembershipLangField(lang_iso_code, id_lang) {	
	$('#membership_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
		
	$('.plan_name_all').hide();
	$('#plan_name_'+id_lang).show();

	$('.all_lang_icon').attr('src', img_dir_l+id_lang+'.jpg');
	$('#choosedLangId').val(id_lang);
}
</script>