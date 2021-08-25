{* 
* @Module Name: Leo Slideshow
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

{extends file=$layout}

{block name='header_banner'}{/block}
{block name='header_nav'}{/block}
{block name='header_top'}{/block}

{block name='hook_footer_before'}{/block}
{block name='hook_footer'}{/block}
{block name='hook_footer_after'}{/block}

{block name='hook_before_body_closing_tag'}{/block}



{block name='content'}
    {if $leoslideshow_tpl == 1}
        {include file='./leoslideshow.tpl'}
    {else}
        {include file='module:leoslideshow/views/templates/front/leoslideshow.tpl'}
    {/if}
{/block}
