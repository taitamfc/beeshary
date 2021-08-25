{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}

{if isset($smarty.get.data_dlt) && $smarty.get.data_dlt}
    <div class="alert alert-success">
        {l s='Your personal data has been successfully deleted.' mod='wkgdpr'}
    </div>
{elseif isset($smarty.get.dlt_req) && $smarty.get.dlt_req}
    <div class="alert alert-success">
        {l s='Your request for personal data erasure has been successfully submitted. You will recieve an email as soon as your personal data will be deleted.' mod='wkgdpr'}
    </div>
{elseif isset($smarty.get.upd_req) && $smarty.get.upd_req}
    <div class="alert alert-success">
        {l s='Your request for personal data rectification has been successfully submitted. You will recieve an email as soon as your personal data will be updated as per your request.' mod='wkgdpr'}
    </div>
{elseif isset($smarty.get.data_download) && $smarty.get.data_download}
    <div class="alert alert-success">
        {l s='PDF having your data is successfully downloaded.' mod='wkgdpr'}
    </div>
{elseif isset($smarty.get.data_emailed) && $smarty.get.data_emailed}
    <div class="alert alert-success">
        {l s='An email has been sent on your requested email, which contains an attachment with all your personal information.' mod='wkgdpr'}
    </div>
{/if}

<div class="row wk-gdpr-data-container">
    {*Nav Tab Contents*}
    <input type="hidden" id="current_active_tab" value="{if isset($active_tab)}{$active_tab}{/if}">
    <div class="col-sm-3 wk-container-left-nav">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item border-bottom-1">
                <a class="nav-link active" data-toggle="tab" href="#wk-gdpr-data-access" role="tab">{l s='Personal Data Access' mod='wkgdpr'}</a>
            </li>
            <li class="nav-item border-bottom-1">
                <a class="nav-link" data-toggle="tab" href="#wk-gdpr-data-update" role="tab">{l s='Personal Data Rectification' mod='wkgdpr'}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#wk-gdpr-data-erasure" role="tab">{l s='Personal Data Erasure' mod='wkgdpr'}</a>
            </li>
        </ul>
        {* <a class="backDash" href="/module/marketplace/dashboard" title="Retour au tableau de bord">Retour au tableau de bord</a> *}
    </div>

    <div class="col-sm-9">
        <div class="tab-content bordered wk-container-left-nav-tab-content">
            {block name='wk-data-access-container'}
                {include file='module:wkgdpr/views/templates/front/_partials/wk-data-access-container.tpl'}
            {/block}

            {block name='wk-data-update-container'}
                {include file='module:wkgdpr/views/templates/front/_partials/wk-data-update-container.tpl'}
            {/block}

            {block name='wk-data-erasure-container'}
                {include file='module:wkgdpr/views/templates/front/_partials/wk-data-erasure-container.tpl'}
            {/block}
        </div>
    </div>
    <footer class="page-footer">
        <a href="https://beeshary.com/mon-compte" class="account-link btn">
            <i class="material-icons"></i><span>Retour sur votre tableau de bord</span>
        </a>
        <a href="https://beeshary.com/" class="account-link btn">
            <i class="material-icons"></i><span>Accueil</span>
        </a>
    </footer>
</div>

{/block}
