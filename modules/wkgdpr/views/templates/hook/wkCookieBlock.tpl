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

<style>
    .wk-cookie-block-wrapper {
        background-color: {$WK_GDPR_COOKIE_BLOCK_BG_COLOR};
        border: 1px solid {$WK_GDPR_COOKIE_BLOCK_BORDER_COLOR};
    }

    .wk-cookie-accept-btn {
        border: 1px solid {$WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR};
        background-color: {$WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR};
        color: {$WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR};
    }

    .wk-cookie-accept-btn:hover {
        color: {$WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR};
        background-color: {$WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR};
        border: 1px solid {$WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR};
    }
    .wk-cookie-block-content {
        color: {$WK_GDPR_COOKIE_BLOCK_TEXT_COLOR};
    }
</style>

{if $WK_GDPR_COOKIE_BLOCK_POSITION == 'left' || $WK_GDPR_COOKIE_BLOCK_POSITION == 'right'}
    <div class="wk-cookie-block-wrapper">
        {if ($WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW)}
            <p class="wk-cookie-block-header">
                <img src="{if isset($cookie_block_icon)}{$cookie_block_icon}{/if}" />
            </p>
        {/if}
        <div class="wk-cookie-block-content">
            {$WK_GDPR_COOKIE_BLOCK_CONTENT nofilter}
        </div>
        <p class="wk-cookie-block-footer">
            <button class="btn btn-default wk-cookie-accept-btn" data-cookie_block_token = "{$wk_cookie_block_token}">
                <span>
                    {l s='GOT IT' mod='wkgdpr'}!
                </span>
            </button>
        </p>
        <span class="wk-cookie-close">
            <img src="{if isset($cookie_cross_icon)}{$cookie_cross_icon}{/if}" />
        </span>
    </div>

    <style>
        .wk-cookie-block-wrapper {
            {if $WK_GDPR_COOKIE_BLOCK_POSITION == 'left'}
                left: 25px;
            {elseif $WK_GDPR_COOKIE_BLOCK_POSITION == 'right'}
                right: 25px;
            {/if}
        }
    </style>
{/if}
