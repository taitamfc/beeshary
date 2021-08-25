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

{if isset($is_badge) && $is_badge}
    <span class="badge {$badge_class|escape:'htmlall':'UTF-8'}">{$request_status|escape:'htmlall':'UTF-8'}</span>
{/if}
{if isset($is_link_customer) && $is_link_customer}
    {$customer_name|escape:'htmlall':'UTF-8'} (<a href="{$customer_page_link|escape:'htmlall':'UTF-8'}&id_customer={$id_customer|escape:'htmlall':'UTF-8'}&viewcustomer">#{$id_customer|escape:'htmlall':'UTF-8'}</a>)
{/if}