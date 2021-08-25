{*
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.type == 'mpresources'}
        <div class="alert alert-info">{l s='Set the resource permissions for this key:' mod='mpwebservice'}</div>
        <table class="table accesses">
            <thead>
                <tr>
                    <th><span class="title_box">{l s='Resource' mod='mpwebservice'}</span></th>
                    <th><input type="checkbox" id="wk_select_all_mpapi"></th>
                </tr>
            </thead>
            <tbody>
                {foreach $mpresources as $mpapi}
                    <tr>
                        <td><span class="pull-left">{$mpapi}</span></td>
                        <td>
                            <input class="form-check-input" type="checkbox" value="{$mpapi}" name="mpapi[]"
                            {if isset($selected_mpresources) && $selected_mpresources.mpresource}
                                {foreach $selected_mpresources.mpresource as $mpresource}
                                    {if $mpapi == $mpresource}
                                        checked="checked"
                                    {/if}
                                {/foreach}
                            {/if}
                            />
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
