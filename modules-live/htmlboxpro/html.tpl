{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-9999 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}
{foreach from=$blocks item=block}
    {assign var='hideblock' value=0}
    {if isset($block.homeonly)}{if $block.homeonly==1}{if Tools::getValue('controller')!='index'}{assign var='hideblock' value=1}{/if}{/if}{/if}
    {if isset($block.date)}
        {if $block.date==1}
            {if $smarty.now|date_format:"%Y-%m-%d" < $block.datefrom}
                {assign var='hideblock' value=1}
            {/if}
            {if $smarty.now|date_format:"%Y-%m-%d" > $block.dateto}
                {assign var='hideblock' value=1}
            {/if}
        {/if}
    {/if}
    {if isset($block.search) && isset($block.query)}
        {if $block.search==1}
            {if Tools::getValue('search_query','false')!='false'}
                {if in_array(Tools::getValue('search_query'), $block.keywords)}
                    {assign var='hideblock' value=0}
                {else}
                    {assign var='hideblock' value=1}
                {/if}
            {else}
                {assign var='hideblock' value=1}
            {/if}
        {/if}
    {/if}

    {if isset($block.bssl)}{if $block.bssl==1}{if $is_https!=1}{assign var='hideblock' value=1}{/if}{/if}{/if}
    {if isset($block.logged)}{if $block.logged==1}{if !$logged}{assign var='hideblock' value=1}{/if}{/if}{/if}
    {if isset($block.logged)}{if $block.logged==2}{if $logged}{assign var='hideblock' value=1}{/if}{/if}{/if}
    {* {if $block.productsonly==1}{if isset($smarty.get['id_product']) && $page_name=='product'}{if !preg_match("/{$smarty.get['id_product']}/",$block.selectedproducts)}{assign var='hideblock' value=1}{/if}{/if}{/if} *}
    {if isset($block.productsonly)}
        {if $block.productsonly==1}
            {if isset($hook_params.product) && isset($hook_params.product.id_product)}
                {if !in_array($hook_params.product.id_product, $block.selectedproducts)}
                    {assign var='hideblock' value=1}
                {else}
                    {assign var='hideblock' value=0}
                {/if}
            {elseif isset($smarty.get.id_product) && Tools::getValue('controller')=='product'}
                {if !in_array($smarty.get.id_product, $block.selectedproducts)}
                    {assign var='hideblock' value=1}
                {/if}
            {else}
                {assign var='hideblock' value=1}
            {/if}
            {if isset($block.selectedproducts['0']) && isset($smarty.get.id_product)}
                {if $block.selectedproducts['0'] == ""}
                    {assign var='hideblock' value=0}
                {/if}
            {/if}            
        {/if}
    {/if}

    {if isset($block.catsonly)}
        {if $block.catsonly==1}
            {if isset($smarty.get.id_category) && Tools::getValue('controller')=='category'}
                {if $block.selected_cats != "-"}
                    {if !in_array($smarty.get.id_category, $block.selected_cats)}
                        {assign var='hideblock' value=1}
                    {/if}
                {/if}
            {else}
                {assign var='hideblock' value=1}
            {/if}
        {/if}
    {/if}


    {if isset($block.cmscatsonly)}{if $block.cmscatsonly==1}{if isset($smarty.get.id_cms_category) && Tools::getValue('controller')=='cms'}{if !in_array($smarty.get.id_cms_category, $block.selected_cmscats)}{assign var='hideblock' value=1}{/if}{else}{assign var='hideblock' value=1}{/if}{/if}{/if}
    {if isset($block.cmsonly)}{if $block.cmsonly==1}{if isset($smarty.get.id_cms) && Tools::getValue('controller')=='cms'}{if !in_array($smarty.get.id_cms, $block.selectedcms)}{assign var='hideblock' value=1}{/if}{else}{assign var='hideblock' value=1}{/if}{/if}{/if}
    {if isset($block.manufsonly)}{if $block.manufsonly==1}{if isset($smarty.get.id_manufacturer) && Tools::getValue('controller')=='manufacturer'}{if !in_array($smarty.get.id_manufacturer, $block.selected_manufs)}{assign var='hideblock' value=1}{/if}{else}{assign var='hideblock' value=1}{/if}{/if}{/if}
    {if isset($block.urlonly)}
        {if $block.urlonly==1}
            {if !in_array(htmlboxpro::currentPageURL(), $block.urls)}
                {assign var='hideblock' value=1}
            {/if}
        {/if}
    {/if}

    {if isset($block.productscat)}
        {if $block.productscat==1}
            {if isset($hook_params.product) && isset($hook_params.product.id_product)}
                {assign var='associated_pcats' value=0}
                {foreach Product::getProductCategories($hook_params.product.id_product) as $category}
                    {if in_array($category, $block.selected_pcats)}
                        {assign var='associated_pcats' value=1}
                    {/if}
                {/foreach}
                {if $associated_pcats==0}
                    {assign var='hideblock' value=1}
                {/if}
            {elseif isset($smarty.get.id_product)}
                {assign var='associated_pcats' value=0}
                {foreach Product::getProductCategories($smarty.get.id_product) as $category}
                    {if in_array($category, $block.selected_pcats)}
                        {assign var='associated_pcats' value=1}
                    {/if}
                {/foreach}
                {if $associated_pcats==0}
                    {assign var='hideblock' value=1}
                {/if}
            {else}
                {assign var='hideblock' value=1}
            {/if}
        {/if}
    {/if}

    {if $block.oconfirmation==1}
        {if Tools::getValue('controller')!='order-confirmation'}
            {assign var='hideblock' value=1}
        {/if}
    {/if}

    {if isset($block.productsman)}
        {if $block.productsman==1}
            {if isset($smarty.get.id_product)}
                {assign var='associated_pmanufs' value=0}
                {if in_array(htmlboxpro::returnAssociatedProductManufacturer(), $block.selected_pmanufs)}
                    {assign var='associated_pmanufs' value=1}
                {/if}
                {if $associated_pmanufs==0}
                    {assign var='hideblock' value=1}
                {/if}
            {else}
                {assign var='hideblock' value=1}
            {/if}
        {/if}
    {/if}

    {if isset($block.supponly)}
        {if $block.supponly==1}
            {if isset($smarty.get.id_product)}
                {assign var='associated_psupp' value=0}
                {if in_array(htmlboxpro::returnAssociatedProductSuppliers(), $block.selected_supp)}
                    {assign var='associated_psupp' value=1}
                {/if}
                {if $associated_psupp==0}
                    {assign var='hideblock' value=1}
                {/if}
            {else}
                {assign var='hideblock' value=1}
            {/if}
        {/if}
    {/if}

    {if isset($block.productsman) && isset($block.productscat)}
        {if $block.productsman==1 && $block.productscat==1}
            {if $associated_pmanufs==1 && $associated_pcats==1}
                {assign var='hideblock' value=0}
            {/if}
        {/if}
    {/if}

    {if isset($block.cgroup)}
        {if $block.cgroup!=0}
            {assign var='hideblock' value=1}
            {if !$logged}
                {if 1 == $block.cgroup || 2 == $block.cgroup}
                    {assign var='associated_group' value=1}
                {/if}
                {if $associated_group==1}
                    {assign var='hideblock' value=0}
                {/if}
            {/if}
            {if $logged}
                {assign var='associated_category' value=0}
                {foreach Customer::getGroupsStatic($customer_popup->id_customer) AS $customer_group}
                    {if $customer_group == $block.cgroup}
                        {assign var='associated_category' value=1}
                    {/if}
                {/foreach}
                {if $associated_category==0}
                    {assign var='hideblock' value=1}
                {else}
                    {assign var='hideblock' value=0}
                {/if}
            {/if}
        {/if}
    {/if}

    {if isset($block.hcgroup)}
        {if $block.hcgroup!=0}
            {if !$logged}
                {assign var='associated_group' value=0}
                {if 1 == $block.hcgroup
                || 2 == $block.hcgroup}
                    {assign var='associated_group' value=1}
                {/if}
                {if $associated_group==1}
                    {assign var='hideblock' value=1}
                {/if}
            {/if}
            {if $logged}
                {assign var='associated_group' value=0}
                {foreach Customer::getGroupsStatic($customer_popup->id_customer) AS $customer_group}
                    {if $customer_group == $block.hcgroup}
                        {assign var='associated_group' value=1}
                    {/if}
                {/foreach}
                {if $associated_group==1}
                    {assign var='hideblock' value=1}
                {/if}
            {/if}
        {/if}
    {/if}

    {if $hideblock!=1}
        {$block.body|stripslashes nofilter}
    {/if}

    {assign var='hideblock' value=0}
{/foreach}