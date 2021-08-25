{*
*
* 2010-2019 Webkul.
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
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $element_type eq  'badge_count'}
<span class="badge">{$data.count}</span>
{elseif $element_type eq  'badge_status'}
<span class="badge {if $data.status eq 1}badge-success{elseif $data.status eq 2}badge-danger{/if}">
        {if $data.status eq 0}
            {l s='Pending' mod='mpsellerpaymentrequest'}
        {elseif $data.status eq 1}
            {l s='Approved' mod='mpsellerpaymentrequest'}
        {elseif $data.status eq 2}
            {l s='Declined' mod='mpsellerpaymentrequest'}
        {/if}
</span>
{elseif $element_type eq  'icon_money'}
<i class="icon-money"></i> {l s='Payment Request Settlement' mod='mpsellerpaymentrequest'}
{elseif $element_type eq  'modal_body'}
<div class="modal-body"></div>
{/if}
