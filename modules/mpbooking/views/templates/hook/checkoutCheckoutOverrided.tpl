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

{extends file=$checkout_template_file}
{block name='cart_summary_product_line'}
  <div class="media-left">
    <a href="{$product.url}" title="{$product.name}">
      <img class="media-object" src="{$product.cover.small.url}" alt="{$product.name}">
    </a>
  </div>
  <div class="media-body">
    <span class="product-name">
      {$product.name}
      {if isset($product.isBookingProduct) && $product.isBookingProduct}
        </br>
        <span class="booking_product_label">{l s='Réservation' d='Shop.Theme.Catalog'}</span>
      {/if}
    </span>
    {if isset($product.isBookingProduct) && $product.isBookingProduct}
      <span class="product-price float-xs-right">{$product.total_price_tax_excl_formatted}</span>
    {else}
      <span class="product-quantity">x{$product.quantity}</span>
      <span class="product-price float-xs-right">{$product.price}</span>
    {/if}
    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
  </div>
  <style>
    .booking_product_label {
      background-color: red;
      color: #fff;
      font-size: 11px;
      padding: 0px 4px;
      border-radius: 4px;`
    }
  </style>
{/block}

  {block name='order_items_table_head'}
    <h3 class="card-title h3">{l s='Order items' d='Shop.Theme.Checkout'}</h3>
  {/block}
