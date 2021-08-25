{*
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
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

{extends file=$layout}
{block name='content'}
    {if $logged}
        <div class="wk-mp-block">
            {hook h="displayMpMenu"}
            <div class="wk-mp-content">
                <div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
                    <span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Statistics' mod='mpsellerstats'}</span>
                </div>
                <div class="clearfix wk-mp-right-column">
                    {include file='module:mpsellerstats/views/templates/front/_partials/mpsellerstats-navtabs.tpl'}
                    {include file='module:mpsellerstats/views/templates/front/_partials/mpsellerstats-daterange.tpl'}
                    {include file='module:mpsellerstats/views/templates/front/_partials/mpsellerstats-chart.tpl'}
                    {include file='module:mpsellerstats/views/templates/front/_partials/mpsellerstats-demographic.tpl'}
                </div>
            </div>
        </div>
    {else}
        <div class="alert alert-danger">
            {l s='You are logged out. Please login.' mod='mpsellerstats'}</span>
        </div>
    {/if}
<link href="{$smarty.const._MODULE_DIR_}marketplace/views/css/libs/graph/nv.d3.css" rel="stylesheet">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{$smarty.const._MODULE_DIR_}marketplace/views/js/libs/graph/d3.v3.min.js"></script>
<script src="{$smarty.const._MODULE_DIR_}marketplace/views/js/libs/graph/nv.d3.min.js"></script>
{/block}