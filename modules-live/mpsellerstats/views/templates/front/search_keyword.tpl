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
                    <span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Statistics ' mod='mpsellerstats'}</span>
                </div>
                <div class="clearfix wk-mp-right-column">
                    {include file='module:mpsellerstats/views/templates/front/_partials/mpsellerstats-navtabs.tpl'}
                    {include file='module:mpsellerstats/views/templates/front/_partials/mpsellerstats-daterange.tpl'}
                    <div class="row" id='searchkeyword-table'>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{l s='Keywords: ' mod='mpsellerstats'}</th>
                                        <th>{l s='Occurrences: ' mod='mpsellerstats'}</th>
                                        <th>{l s='Results: ' mod='mpsellerstats'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {else}
        <div class="alert alert-danger">
            {l s='You are logged out. Please login.' mod='mpsellerstats'}</span>
        </div>
    {/if}

{/block}