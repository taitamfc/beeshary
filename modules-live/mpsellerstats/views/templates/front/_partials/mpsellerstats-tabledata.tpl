{*
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($tplData) && $tplData}
    {foreach $tplData as $data}
        {if ($data.count > 0)}
            <tr>
                <td>{if $data.name}{$data.name}{else}{l s='Anonymous' mod='mpsellerstats'}{/if}</td>
                <td>{$data.count}</td>
            </tr>
        {/if}
    {/foreach}
{else}
    <tr>
        <td>
        {l s='Cannot find any data.' mod='mpsellerstats'}</td>
    </tr>
{/if}