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
{if isset($success)}
    {if $success == 1}
        <p class="alert alert-success">
            {l s='Product csv file has been uploaded Successfully. All uploaded products are created successfully, For more details please check your product list.' mod='mpmassupload'}
        </p>
    {else if $success == 2}
        <p class="alert alert-success">
            {l s='Product csv file has been uploaded Successfully. Request has been submitted to admin for uploaded products csv, these products will be active after admin confirmation.' mod='mpmassupload'}
        </p>
    {/if}
{/if}

{if isset($warning_arr)}
    <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {foreach from=$warning_arr key=list_k item=warning}
            {if $warning}
                <p><strong>{$list_k|intval + 1})</strong>&nbsp;{$warning}</p>
            {/if}
        {/foreach}
    </div>
{/if}

<div class="wk-mp-block">
    {hook h="displayMpMenu"}
    <div class="wk-mp-content">
        <div class="page-title" style="background-color:{$title_bg_color};">
            <span style="color:{$title_text_color};">{l s='Mass Upload' mod='mpmassupload'}</span>
        </div>
        <div class="wk-mp-right-column">
            <div class="tab">
                <p class="add-new-csv-cont clearfix">
                    <a href="{$link_new_request}" class="btn btn-primary pull-right">
                        <i class="material-icons">&#xE2C4;</i>
                        <span>{l s='Upload CSV' mod='mpmassupload'}</span>
                    </a>
                    {if $sellerHasProducts}
                        <a href="{$link_update_request}" class="btn btn-primary pull-right">
                            <i class="material-icons">&#xE2C6;</i>
                            <span>{l s='Export CSV' mod='mpmassupload'}</span>
                        </a>
                    {/if}
                </p>
                <ul class="nav nav-tabs" style="clear:both;">
                    <li class="nav-item">
                        <a class="nav-link active" href="#products_mass" data-toggle="tab">
                            <i class="material-icons">&#xE88E;</i>
                            {l s='Products' mod='mpmassupload'}
                        </a>
                    </li>
                    {if isset($massupload_combination_approve)}
                        <li class="nav-item">
                            <a class="nav-link" href="#combinations_mass" data-toggle="tab">
                                <i class="material-icons">&#xE335;</i>
                                {l s='Combinations' mod='mpmassupload'}
                            </a>
                        </li>
                    {/if}
                </ul>
                <div class="tab-content" id="tab-content">
                    <div class="tab-pane fade in active" id="products_mass">
                        <table class="table table-responsive" style="width:100%;">
                            <thead class="upload_list_head">
                                <tr class="first last">
                                    <th>{l s='#Request No' mod='mpmassupload'}</th>
                                    <th>{l s='Total Records' mod='mpmassupload'}</th>
                                    <th>{l s='Download CSV' mod='mpmassupload'}</th>
                                    <th>{l s='Status' mod='mpmassupload'}</th>
                                    <th>{l s='CSV Type' mod='mpmassupload'}</th>
                                    <th>{l s='Requested Date' mod='mpmassupload'}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {if isset($request_list)}
                                    {foreach $request_list as $list}
                                        {if $list.mass_upload_category == 1}
                                            <tr class="even" id="mp_banner_{$list['id']}">
                                                <td>{$list['request_id']}</td>
                                                <td>{$list['total_records']}</td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary-outline" href="{$smarty.const._MODULE_DIR_}mpmassupload/views/uploaded_csv/{$list['request_id']}.csv">
                                                        <i class="material-icons">&#xE2C4;</i> {l s='Download file' mod='mpmassupload'}
                                                    </a>
                                                </td>
                                                <td>
                                                    {if $list['is_approve']}
                                                        <p style="color:#78D07D">
                                                            <i class="material-icons">&#xE5CA;</i>
                                                        </p>
                                                    {else}
                                                        <p style="color:#C45C67">
                                                            <i class="material-icons">&#xE5CD;</i>
                                                        </p>
                                                    {/if}
                                                </td>
                                                <td>
                                                    {if $list['csv_type'] == 1}
                                                        <span>{l s='Add' mod='mpmassupload'}</span>
                                                    {elseif $list['csv_type'] == 2}
                                                        <span>{l s='Update' mod='mpmassupload'}</span>
                                                    {/if}
                                                </td>
                                                <td>{$list['date_add']}</td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                {/if}
                            </tbody>
                        </table>
                    </div>
                    {if isset($massupload_combination_approve)}
                        <div class="tab-pane fade" id="combinations_mass">
                            <table class="table" style="width:100%;">
                                <thead class="upload_list_head">
                                    <tr class="first last">
                                        <th>{l s='#Request No' mod='mpmassupload'}</th>
                                        <th>{l s='Total Records' mod='mpmassupload'}</th>
                                        <th>{l s='Download CSV' mod='mpmassupload'}</th>
                                        <th>{l s='Approved' mod='mpmassupload'}</th>
                                        <th>{l s='CSV Type' mod='mpmassupload'}</th>
                                        <th>{l s='Requested Date' mod='mpmassupload'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {if isset($request_list)}
                                        {foreach $request_list as $list}
                                            {if $list.mass_upload_category == 2}
                                                <tr class="even" id="mp_banner_{$list['id']}">
                                                    <td>{$list['request_id']}</td>
                                                    <td>{$list['total_records']}</td>
                                                    <td>
                                                        <a class="btn btn-default" href="{$smarty.const._MODULE_DIR_}mpmassupload/views/uploaded_csv/{$list['request_id']}.csv">
                                                            <i class="material-icons">&#xE2C4;</i>
                                                            <i class="icon-download"></i> {l s='Download file' mod='mpmassupload'}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {if $list['is_approve']}
                                                            <p style="color:#78D07D">
                                                                <i class="material-icons">&#xE5CA;</i>
                                                            </p>
                                                        {else}
                                                            <p style="color:#C45C67">
                                                                <i class="material-icons">&#xE5CD;</i>
                                                            </p>
                                                        {/if}
                                                    </td>
                                                    <td>
                                                        {if $list['csv_type'] == 1}
                                                            <span>{l s='Add' mod='mpmassupload'}</span>
                                                        {elseif $list['csv_type'] == 2}
                                                            <span>{l s='Update' mod='mpmassupload'}</span>
                                                        {/if}
                                                    </td>
                                                    <td>{$list['date_add']}</td>
                                                </tr>
                                            {/if}
                                        {/foreach}
                                    {/if}
                                </tbody>
                            </table>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
{/block}