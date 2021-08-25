{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="wk-seller-review-box">
    <div class="row">
        <div class="col-md-8">
            <div class="wk_reviews_customer_details">
                <span>{$review.customer_name}</span>
            </div>
            <div class="wk_customer_ratings">
                {assign var=i value=0}
                {while $i != $review.rating}
                    {* <img src="{$smarty.const._MODULE_DIR_}/marketplace/views/img/star-on.png" /> *}
                    <i class="vendorStar fas fa-star"></i>
                {assign var=i value=$i+1}
                {/while}

                {assign var=k value=0}
                {assign var=j value=5-$review.rating}
                {while $k!=$j}
                    {* <img src="{$smarty.const._MODULE_DIR_}/marketplace/views/img/star-off.png" /> *}
                    <i class="vendorStar far fa-star"></i>
                {assign var=k value=$k+1}
                {/while}
            </div>
            {* <div>({$review.customer_email})</div> *}
        </div>
        <div class="col-md-4 wk_text_right">
            <span><i class="material-icons">&#xE8AE;</i> {dateFormat date=$review.date_upd full=1}</span>
        </div>
    </div>
    {if !empty($review.review)}
        <div class="wk_review_content">{$review.review}</div>
    {/if}
    {block name='mp-seller-review-like'}
        {include file='module:marketplace/views/templates/front/seller/_partials/seller-review-like.tpl'}
    {/block}
</div>