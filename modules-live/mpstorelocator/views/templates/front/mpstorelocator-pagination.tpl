{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
 
<div>
    <div class="col-sm-6">
        {if ($n*$p) < $nb_products }
            {assign var='productShowing' value=$n*$p}
        {else}
            {assign var='productShowing' value=($n*$p-$nb_products-$n*$p)*-1}
        {/if}
 
        {if $p==1}
            {assign var='productShowingStart' value=1}
        {else}
            {assign var='productShowingStart' value=$n*$p-$n+1}
        {/if}
 
        {if $nb_products > 1}
            <p>{l s='Showing' mod='mpstorelocator'} {$productShowingStart} - {$productShowing} {l s='of' mod='mpstorelocator'} {$nb_products} {l s='items' mod='mpstorelocator'}</p>
        {else}
            <p>{l s='Showing' mod='mpstorelocator'} {$productShowingStart} - {$productShowing} {l s='of' mod='mpstorelocator'} 1 {l s='item' mod='mpstorelocator'}</p>
        {/if}
    </div>
 
    <div class="col-sm-6 wk-pagination">
        <div class="text-right wk-right-pagination">
            <ul class="pagination pagination-sm">
                <li class="page-item">
                    <a class="page-link {if $p == 1}wk-disabled{/if}" {if $p > 1}href="{$wk_controller_page}&p={$p-1}"{/if} aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">{l s='Previous' mod='mpstorelocator'}</span>
                    </a>
                </li>
                {for $i = 1 to $page_count}
                    <li class="page-item">
                        <a class="page-link {if $p == $i}wk-page-active{/if}" href="{$wk_controller_page}&p={$i}">{$i}</a>
                    </li>
                {/for}
                <li class="page-item">
                    <a class="page-link {if $p == $page_count}wk-disabled{/if}" {if $p < $page_count}href="{$wk_controller_page}&p={$p+1}"{/if} aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">{l s='Next' mod='mpstorelocator'}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
body#module-mpstorelocator-storedetails div.wk-pagination {
    text-align: right;
}
body#module-mpstorelocator-storedetails div.wk-right-pagination {
    display: inline-block;
}
body#module-mpstorelocator-storedetails .wk-page-active {
    color: #2fb5d2 !important;
}
</style>