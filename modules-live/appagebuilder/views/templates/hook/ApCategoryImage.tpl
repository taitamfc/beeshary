{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApCategoryImage -->
{function name=apmenu level=0}
<ul class="level{$level|intval} {if $level == 0} ul-{$random|escape:'html':'UTF-8'}{/if}">
{foreach $data as $category}
	{if isset($category.children) && is_array($category.children)}
	<li class="cate_{$category.id_category|intval}" >
		<a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)|escape:'html':'UTF-8'}">
			<span class="cate_content">
				<span class="cover-img">
					{if isset($category.image)}
					<img height = '10px' src='{$category["image"]|escape:'html':'UTF-8'}' alt='{$category["name"]|escape:'html':'UTF-8'}' 
						 {if $formAtts.showicons == 0 || ($level gt 0 && $formAtts.showicons == 2)} style="display:none"{/if}/>
					{/if}
				</span>
				<span class="cat_name">{$category.name|escape:'html':'UTF-8'}</span>
			</span>
		</a>
		{apmenu data=$category.children level=$level+1}
	</li>
	{else}
	<li class="cate_{$category.id_category|intval}">
		<a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)|escape:'html':'UTF-8'}">
			<span class="cate_content">
				<span class="cover-img">
					{if isset($category.image)}
					<img height = '10px' src='{$category["image"]|escape:'html':'UTF-8'}' alt='{$category["name"]|escape:'html':'UTF-8'}' 
						 {if $formAtts.showicons == 0 || ($level gt 0 && $formAtts.showicons == 2)} style="display:none"{/if}/>
					{/if}
				</span>
				<span>{$category.name|escape:'html':'UTF-8'}</span>
			</span>
		</a>
	</li>
	{/if}
{/foreach}
</ul>
{/function}

{if isset($categories)}
<div class="widget-category_image block {if isset($formAtts.class)}{$formAtts.class|escape:'html':'UTF-8'}{/if}">
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
	{if isset($formAtts.title) && !empty($formAtts.title)}
	<h4 class="title_block">
		{$formAtts.title|escape:'html':'UTF-8'}
	</h4>
	{/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
	<div class="block_content">
		{foreach from = $categories key=key item =cate}
			{apmenu data=$cate}
		{/foreach}
		<div id="view_all_wapper_{$random|escape:'html':'UTF-8'}" class="view_all_wapper hide">
			<a class="btn btn-primary view_all" href="javascript:void(0)">{l s='View all' mod='appagebuilder'}</a>
		</div> 
	</div>
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
</div>
{/if}
<script type="text/javascript">
{literal} 
	ap_list_functions.push(function(){
		$(".view_all_wapper").hide();
		var limit = {/literal}{$formAtts.limit|intval}{literal};
		var level = {/literal}{$formAtts.cate_depth|intval}{literal} - 1;
		$("ul.ul-{/literal}{$random|escape:'html':'UTF-8'}, ul.ul-{$random|escape:'html':'UTF-8'} ul"{literal}).each(function(){
			var element = $(this).find(">li").length;
			var count = 0;
			$(this).find(">li").each(function(){
				count = count + 1;
				if(count > limit){
					// $(this).remove();
					$(this).hide();
				}
			});
			if(element > limit) {
				view = $(".view_all","#view_all_wapper_{/literal}{$random|escape:'html':'UTF-8'}"){literal}.clone(1);
				// view.appendTo($(this).find("ul.level" + level));
				view.appendTo($(this));
				var href = $(this).closest("li").find('a:first-child').attr('href');
				$(view).attr("href", href);
			}
		})
	});
{/literal}
</script>