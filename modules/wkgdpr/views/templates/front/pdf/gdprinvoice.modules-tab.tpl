{*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*}

{if $modules}
    {foreach from=$modules item=moduleData}
        <h2>{l s='Module' mod='wkgdpr'}: {$moduleData['displayName']|escape:'htmlall':'UTF-8'}</h2>
        {if isset($moduleData['data']) && $moduleData['data']}
            {if is_array($moduleData['data'])}
                <table width="100%" class="common-table-style">
                    {foreach from=$moduleData['data'] item=dataArr}
                        {foreach from=$dataArr key=fieldName item=fieldValue}
                            <tr>
                                <td class="header left">&nbsp;&nbsp;&nbsp;<b>{$fieldName|escape:'htmlall':'UTF-8'}</b> : </td>
                                <td class="left">{$fieldValue|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        {/foreach}
                    {/foreach}
                    {if $moduleData['data']|@count > 1}
                        <tr><td colspan="2"><hr></td></tr>
                    {/if}
                </table>
            {else}
                <table width="100%" class="common-table-style">
                    <tr>
                        <td colspan="12" class="center">{$moduleData['data']|escape:'htmlall':'UTF-8'}</td>
                    </tr>
                </table>
            {/if}
        {else}
            <table width="100%" class="common-table-style">
                <tr>
                    <td colspan="12" class="center">{l s='No order found' mod='wkgdpr'}</td>
                </tr>
            </table>
        {/if}
    {/foreach}
{/if}
