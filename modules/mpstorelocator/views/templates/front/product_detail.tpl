{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApProductList -->
{if !isset($apAjax)}
    <div class="block {$formAtts.class|escape:'html':'UTF-8'}">
		{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
		<input type="hidden" class="apconfig" value='{$apPConfig nofilter}{* HTML form , no escape necessary *}'/>
		{if isset($formAtts.title) && !empty($formAtts.title)}
		<h4 class="title_block">
			{$formAtts.title|escape:'html':'UTF-8'}
		</h4>
		{/if}
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
            <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
        {/if}
{/if}
{if isset($products) && $products}
    {if !isset($apAjax)}
    <!-- Products list -->
    <ul{if isset($id) && $id} id="{$id|intval}"{/if} class="product_list grid row{if isset($class) && $class} {$class|escape:'html':'UTF-8'}{/if} {if isset($productClassWidget)}{$productClassWidget|escape:'html':'UTF-8'}{/if}">
    {/if}
        {foreach from=$products item=product name=products}
            <li class="ajax_block_product{if isset($formAtts.use_animation) && $formAtts.use_animation} has-animation{/if} product_block 
                {if $scolumn == 5} col-lg-2-4 {else} col-lg-{12/$scolumn|intval}{/if} 
                col-sm-6 col-xs-6 col-sp-12 {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if}"{if isset($formAtts.use_animation) && $formAtts.use_animation} data-animation="fadeInUp" data-animation-delay="{$smarty.foreach.products.iteration*100}ms" data-animation-duration="2s" data-animation-iteration-count="1"{/if}>
                    {if isset($product_item_path)}
                        {include file="$product_item_path"}
                    {/if}
            </li>
        {/foreach}
    {if !isset($apAjax)}
    </ul>
    <!-- End Products list -->
    {if isset($formAtts.use_showmore) && $formAtts.use_showmore}
    <div class="box-show-more open">
        <a href="javascript:void(0)" class="btn btn-default btn-show-more" data-use-animation="{if isset($formAtts.use_animation) && $formAtts.use_animation}1{else}0{/if}" data-page="{$p|intval}" data-loading-text="{l s='Loading...' mod='appagebuilder'}">
            <span>{l s='Show more' mod='appagebuilder'}</span></a>
    </div>
    {/if}

    </div>
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
    {/if}
	{else}
</div>
{/if}
