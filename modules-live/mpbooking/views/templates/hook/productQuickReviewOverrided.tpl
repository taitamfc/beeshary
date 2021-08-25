{*
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

{if isset($isBookingProduct) && $isBookingProduct}
  <style type="text/css">
    .date_range_form {
      margin-top: 20px;
    }
    .tabs {
      display: none;
    }
    .product-add-to-cart {
      display: none;
    }
    #block-reassurance {
      display: none;
    }
    .product-prices {
      display: none;
    }
    .booking_product_date {
      font-size: 14px;
      font-weight: bold;
      text-align: center;
    }
    .time_slot_checkbox label {
      text-align: left;
    }
    .booking_time_slots_quantity_wanted {
      width: 50%;
    }
    .unavailable_slot_err {
      text-align: right;
      color: red;
    }
  </style>
{/if}
<div id="quickview-modal-{$product.id}-{$product.id_product_attribute}" class="modal fade quickview" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
       </button>
     </div>
     <div class="modal-body">
      <div class="row">
        <div class="col-md-6 col-sm-6 hidden-xs-down">
          {block name='product_cover_tumbnails'}
            {include file='catalog/_partials/product-cover-thumbnails.tpl'}
          {/block}
          <div class="arrows js-arrows">
            <i class="material-icons arrow-up js-arrow-up">&#xE316;</i>
            <i class="material-icons arrow-down js-arrow-down">&#xE313;</i>
          </div>
        </div>
        <div class="col-md-6 col-sm-6">
          <h1 class="h1">{$product.name}</h1>
          {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
          {/block}
          {block name='product_description_short'}
            <div id="product-description-short" itemprop="description">{$product.description_short nofilter}</div>
          {/block}
          {block name='product_buy'}
            <div class="product-actions">
              <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                <input type="hidden" name="token" value="{$static_token}">
                <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id">
                {block name='product_variants'}
                  {include file='catalog/_partials/product-variants.tpl'}
                {/block}

                {block name='product_add_to_cart'}
                  {include file='catalog/_partials/product-add-to-cart.tpl'}
                {/block}

                {block name='product_refresh'}
                  <input class="product-refresh" data-url-update="false" name="refresh" type="submit" value="{l s='Refresh' d='Shop.Theme.Actions'}" hidden>
                {/block}
            </form>
          </div>
          {hook h='displayProductButtons' product=$product}
        {/block}
        </div>
      </div>
     </div>
     <div class="modal-footer">
    </div>
   </div>
 </div>
</div>
<script>
  var selectedDatesJson = '{$selectedDates nofilter}';
  var disabledDays = {$disabledDays|json_encode nofilter};
  var disabledDates = {$disabledDates|json_encode nofilter};
</script>