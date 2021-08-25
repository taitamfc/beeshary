{* 
* @Module Name: Leo Product Search
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
*}

{function name="lps_categories" nodes=[] depth=0}
  {strip}
    {if $nodes|count}
        {foreach from=$nodes item=node}         
            <a href="#" data-cate-id="{$node.id_category|escape:'htmlall':'UTF-8'|stripslashes}" data-cate-name="{$node.name}" class="cate-item cate-level-{$node.level_depth}{if isset($selectedCate) && $node.id_category eq $selectedCate} active{/if}" >{if $node.level_depth > 1}{str_repeat('-', $node.level_depth)}{/if}{$node.name}</a>           
            {lps_categories nodes=$node.children depth=$depth+1}           
        {/foreach}
    {/if}
  {/strip}
{/function}

<!-- Block search module -->
<div id="leo_search_block_left" class="block exclusive{if $en_search_by_cat} search-by-category{/if}">
	<h4 class="title_block">{l s='Search' mod='leoproductsearch'}</h4>
	<form method="get" action="{$link->getPageLink('productsearch', true)|escape:'html':'UTF-8'}" id="leosearchbox">
		<input type="hidden" name="fc" value="module" />
		<input type="hidden" name="module" value="leoproductsearch" />
		<input type="hidden" name="controller" value="productsearch" />
		
		<input type="hidden" name="orderby" value="position" />
		<input type="hidden" name="orderway" value="desc" />
		
    	<label for="search_query_block">{l s='Search products:' mod='leoproductsearch'}</label>
		<div class="block_content clearfix">
			<div class="list-cate-wrapper"{if !$en_search_by_cat} style="display: none"{/if}>
				<input id="leosearch-cate-id" name="cate" value="{if isset($selectedCate)}{$selectedCate}{/if}" type="hidden">
				<a id="dropdownListCate" class="select-title" rel="nofollow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span>{if $selectedCateName != ''}{$selectedCateName}{else}{l s='All Categories' mod='leoproductsearch'}{/if}</span>
					<i class="material-icons pull-xs-right">keyboard_arrow_down</i>
				</a>
				<div class="list-cate dropdown-menu" aria-labelledby="dropdownListCate">
					<a href="#" data-cate-id="" data-cate-name="{l s='All Categories' mod='leoproductsearch'}" class="cate-item{if $selectedCate == ''} active{/if}" >{l s='All Categories' mod='leoproductsearch'}</a>
					<a href="#" data-cate-id="{$cates.id_category|escape:'htmlall':'UTF-8'|stripslashes}" data-cate-name="{$cates.name}" class="cate-item cate-level-{$cates.level_depth}{if isset($selectedCate) && $cates.id_category eq $selectedCate} active{/if}" >{if $cates.level_depth > 1}{str_repeat('-', $cates.level_depth)}{/if}{$cates.name}</a>
					{lps_categories nodes=$cates.children}
				</div>
			</div>
			<div class="leoproductsearch-loading cssload-speeding-wheel">				
			</div>
			<input class="search_query form-control grey" type="text" id="leo_search_query_block" name="search_query" value="{$search_query|escape:'htmlall':'UTF-8'|stripslashes}" />
			<button type="submit" id="leo_search_button" class="btn btn-default button button-small"><span><i class="material-icons search">search</i></span></button> 
		</div>
	</form>
</div>
<!-- /Block search module -->
