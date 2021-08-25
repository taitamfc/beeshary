{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}

<tr class="comparison_header">
    <td class="feature-name td_empty">
    	<span>{l s='Reviews' mod='leofeature'}</span>
    </td>
    {foreach from=$list_ids_product item=id_product}
    	<td class="product-{$id_product}"></td>
    {/foreach}
</tr>

{foreach from=$grades item=grade key=grade_id}
    <tr>
    {cycle values='comparison_feature_odd,comparison_feature_even' assign='classname'}
        <td class="{$classname} feature-name">
        	<strong>{$grade}</strong>
        </td>
        {foreach from=$list_ids_product item=id_product}
        	{assign var='tab_grade' value=$product_grades[$grade_id]}
        	<td class="{$classname} comparison_infos ajax_block_product product-{$id_product}" align="center">
        		{if isset($tab_grade[$id_product]) AND $tab_grade[$id_product]}
        			<div class="product-rating">
        				{section loop=6 step=1 start=1 name=average}
        					<input class="auto-submit-star not_uniform" disabled="disabled" type="radio" name="{$grade_id}_{$id_product}_{$smarty.section.average.index}" {if isset($tab_grade[$id_product]) AND $tab_grade[$id_product]|round neq 0 and $smarty.section.average.index eq $tab_grade[$id_product]|round}checked="checked"{/if} />
        				{/section}
        			</div>
        		{else}
        			-
        		{/if}
        	</td>
        {/foreach}
    </tr>				
{/foreach}

{cycle values='comparison_feature_odd,comparison_feature_even' assign='classname'}
<tr>
    <td  class="{$classname} feature-name">
    	<strong>{l s='Average' mod='leofeature'}</strong>
    </td>
	{foreach from=$list_ids_product item=id_product}
		<td class="{$classname} comparison_infos product-{$id_product}" align="center" >
			{if isset($list_product_average[$id_product]) AND $list_product_average[$id_product]}
				<div class="product-rating">
					{section loop=6 step=1 start=1 name=average}
						<input class="auto-submit-star not_uniform" disabled="disabled" type="radio" name="average_{$id_product}" {if $list_product_average[$id_product]|round neq 0 and $smarty.section.average.index eq $list_product_average[$id_product]|round}checked="checked"{/if} />
					{/section}
				</div>
			{else}
				-
			{/if}
		</td>	
	{/foreach}
</tr>

<tr>
<td class="{$classname} comparison_infos feature-name">&nbsp;</td>
    {foreach from=$list_ids_product item=id_product}
    	<td class="{$classname} comparison_infos product-{$id_product}" align="center" >
    		{if isset($product_reviews[$id_product]) AND $product_reviews[$id_product]}		
				<div class="dropup leo-compare-review-dropdown">
					<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">					
							{l s='View reviews' mod='leofeature'}					
					  </button>
					<div class="dropdown-menu">						
						{foreach from=$product_reviews[$id_product] item=review}	
							<div class="dropdown-item well well-sm">
								<strong>{$review.customer_name|escape:'html':'UTF-8'} </strong>
								<small class="date"> {dateFormat date=$review.date_add|escape:'html':'UTF-8' full=0}</small>
								<div class="review_title">{$review.title|escape:'html':'UTF-8'|nl2br}</div>
								<div class="review_content">{$review.content|escape:'html':'UTF-8'|nl2br}</div>
							</div>
						{/foreach}
					</div>
				</div>
    		{else}
    			-
    		{/if}
    	</td>	
    {/foreach}
</tr>
