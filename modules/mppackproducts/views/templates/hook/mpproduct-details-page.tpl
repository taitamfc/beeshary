{*
* 2010-2016 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2016 Webkul IN
*}

{if isset($isPackProduct) && $isPackProduct}
	<h4 style="border-bottom: 1px solid #ccc;">{l s='Pack Contents' mod='mppackproducts'}</h4>
	{foreach from=$packProducts key=k item=v}
		<div class="col-sm-4 col-xs-12">
			<div class="col-sm-12 col-xs-12 pk_sug_prod"> 
				<img class="img-responsive pk_sug_img" src="{$link->getImageLink($v['link_rewrite'], $v['img_id'], 'home_default')}" style="width: 100%">
				<p class="text-center">({$v['quantity']} x) {$v['product_name']}</p>
			</div>
		</div>
	{/foreach}
{/if}