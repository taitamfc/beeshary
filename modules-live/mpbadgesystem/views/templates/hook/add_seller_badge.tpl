{**
* 2010-2017 Webkul
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<style>
#addSellerBadge{
	float:left;
	width:200px;
}
h5{
	font-style:italic;
	color:#969614;
	margin-bottom:10px;
	margin-top:0px;
	font-size: 14px;
}
.addbadge{
	padding:7px 8px !important;
	float:left;
}
#seller_badge{
	float:left;
	position:relative;
	margin-right:10px;
}
#seller_badge .remove_badge { 
    display: none; 
    position: absolute;
    top: 0;
    right: 0;
}
#seller_badge:hover{
	background-color : transparent;
}
#seller_badge:hover .remove_badge {
 	display: block; 
}
</style>

{if isset($seller_bagde_tab) && $seller_bagde_tab}
<li>
	<a href="#wk-seller-badges" data-toggle="tab">
		<i class="icon-certificate"></i>
		{l s='Seller Badges' mod='mpbadgesystem'}
	</a>
</li>
{else}

<div class="tab-pane" id="wk-seller-badges">  
	<div class="form-group">
		<label class="control-label col-lg-3 conf_label"> {l s='Show badges to customers' mod='mpbadgesystem'}</label>
		<div class="col-lg-9 "> 
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" value="1" id="active" name="badge_configuration" {if isset($badgeConfiguration) && $badgeConfiguration.active == 1}checked="checked"{/if}>
				<label for="active">{l s='Yes' mod='mpbadgesystem'} </label>
				<input type="radio" value="0" {if isset($badgeConfiguration)} {if $badgeConfiguration.active == 0}checked="checked"{/if}{else}checked="checked"{/if} id="deactive" name="badge_configuration">
				<label for="deactive"> {l s='No' mod='mpbadgesystem'} </label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Seller Badges' mod='mpbadgesystem'}</label>
		<input type="hidden" value="{$mp_id_seller|escape:'htmlall':'UTF-8'}" name="mp_id_seller" id="mp_id_seller">
		<div class="col-lg-9">
			<a class="btn btn-default addbadge" href="#addSellerBadge">{l s='Add New Badges' mod='mpbadgesystem'}</a>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			{if !empty($seller_badge_info)}
				{foreach $seller_badge_info as $seller_badge}
				<div id="seller_badge">
					<img src="{$modules_dir|escape:'htmlall':'UTF-8'}mpbadgesystem/views/img/badge_img/{$seller_badge['badge_id']|escape:'htmlall':'UTF-8'}.jpg" title="{$seller_badge['badge_name']|escape:'htmlall':'UTF-8'}" width="100" height="100" style="float:left;"/>
					<img class="remove_badge" badge_id = '{$seller_badge['badge_id']|escape:'htmlall':'UTF-8'}' src="{$modules_dir|escape:'htmlall':'UTF-8'}mpbadgesystem/views/img/cross.png" title="Remove Badge" style="float:left;"/>
				</div>
				{/foreach}
			{/if}
		</div>
	</div>

	<div id="addSellerBadge" style="display:none;">
		{if !empty($badges)}
		<h5>{l s='Add Badges' mod='mpbadgesystem'} :</h5>
			<input type="hidden" value="{$ajax_link|escape:'quotes':'UTF-8'}" id="ajax_link">
			<input type="hidden" value="{$mp_id_seller|escape:'htmlall':'UTF-8'}" name="mp_id_seller" id="mp_id_seller">
			{foreach $badges as $badge}
			<div style="width:100%;margin-bottom:5px;">
				<input class="badges" type="checkbox" id="badge_id" name="badges[]" value="{$badge['id']|escape:'htmlall':'UTF-8'}" {if !empty($seller_badges) && in_array($badge['id'],$seller_badges)}checked{/if}><span><img src="{$modules_dir|escape:'htmlall':'UTF-8'}mpbadgesystem/views/img/badge_img/{$badge['id']|escape:'htmlall':'UTF-8'}.jpg" width="50" height="50"/></span>
				<span>{$badge['badge_name']|escape:'htmlall':'UTF-8'}</span>
			</div>
			{/foreach}
			<input type="submit" value="{l s='ADD' mod='mpbadgesystem'}" id="add_badge" style="margin-top:10px;"  />
		{else}
			<h3>{l s='No badges added yet' mod='mpbadgesystem'} !!</h3>
		{/if}
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('.addbadge').fancybox();
	$('#add_badge').click(function(e){
		e.preventDefault();
		var mp_id_seller = $('#mp_id_seller').val();
		var check_msg = "{l s='Please select any badge first.' js=1 mod='mpbadgesystem'}";
		var success_msg = "{l s='Badges added successfully for the seller.' js=1 mod='mpbadgesystem'}";
		var error_msg = "{l s='Some error occurs while adding badges, try again after some time.' js=1 mod='mpbadgesystem'}";

        if($('.badges:checked').length > 0){
        	var badge_ids = [];
        	$('.badges:checked').each(function() {
        		var badge_id = $(this).val();
        		badge_ids.push(badge_id);
        	});
        	$.ajax({
				type: 'POST',
				url: $('#ajax_link').val(),
				async: true,
				cache: false,
				datatype:'json',
				data:{
					ajax:true,
					action:'addSellerBadge',
					'badge_id[]':badge_ids,
					mp_id_seller:mp_id_seller
					},
				success: function(data1)
					{
						if(data1 == 1){
							parent.$.fancybox.close();
							alert(success_msg);
							location.reload();
						}else{
							parent.$.fancybox.close();
							alert(error_msg);
						}
					}
			});
    	}else{
    		alert(check_msg);
    	}
		});
		$('.remove_badge').click(function(){
			var badge_id = $(this).attr('badge_id');
			var mp_id_seller = $('#mp_id_seller').val();
			var success_msg = '{l s='Badge deleted successfully.' js=1 mod='mpbadgesystem'}';
			var error_msg = '{l s='Some error occurs while deleting badge, try again after some time.' js=1 mod='mpbadgesystem'}'
			$.ajax({
				type: 'POST',
				url: $('#ajax_link').val(),
				async: true,
				cache: false,
				datatype:'json',
				data:{
					ajax:true,
					action:'removeSellerBadge',
					badge_id:badge_id,
					mp_id_seller:mp_id_seller
					},
				success: function(data1)
					{
						if(data1 == 1){
							alert(success_msg);
							location.reload();
						}else{
							alert(error_msg);
						}
					}
			});
		});
    });
</script>
{/if}