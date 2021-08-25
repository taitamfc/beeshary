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

{if isset($searchData) && $searchData}
    {foreach $searchData as $data}
        <tr>
            <td>{$data.keywords}</td>
            <td>{$data.occurences}</td>
            <td>{$data.total}</td>
        </tr>
    {/foreach}
{else}
    <tr>
        <td>
        {l s='Cannot find any keywords that have been searched for more than once.' mod='mpsellerstats'}</td>
    </tr>
{/if}