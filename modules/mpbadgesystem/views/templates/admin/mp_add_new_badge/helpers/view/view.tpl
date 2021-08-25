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

<style type="text/css">
	.row{
		margin-bottom: 20px;
	}
	.badge_content{
		border: 1px solid #D7D7D7;
		border-radius: 4px;
		background-color: #EEEEEE;
		padding:5px 10px !important;
	}

</style>

<div class="leadin">{block name="leadin"}{/block}</div>
{block name="override_tpl"}
   	<div id="fieldset_0" class="panel">
    <h3>View Seller</h3>
	<form class="form-horizontal">
		<div class="row">	
			<label class="col-lg-3 control-label required">{l s='Badge Name' mod='mpbadgesystem'}</label>	
			<div class="col-lg-5 badge_content">{$mp_badge_info['badge_name']|escape:'html':'UTF-8'}</div>	
		</div>	
		<div class="row">
			<label class="col-lg-3 control-label">{l s='Badge Description' mod='mpbadgesystem'}</label>
			<div class="col-lg-5 badge_content">
				{$mp_badge_info['badge_desc']|escape:'htmlall':'UTF-8'}
			</div>
		</div>
		<div class="row">  
			<label class="col-lg-3 control-label">{l s='Badge Image' mod='mpbadgesystem'}</label>
			<div class="prev_image col-lg-5" style="float:left;">
				<img src="../modules/mpbadgesystem/views/img/badge_img/{$mp_badge_info['id']|escape:'html':'UTF-8'}.jpg" width="100" height="100"/>
			</div>
		</div>
			
	</form>
</div>
{/block}
<script type="text/javascript">
	$('.fancybox').fancybox();
</script>
