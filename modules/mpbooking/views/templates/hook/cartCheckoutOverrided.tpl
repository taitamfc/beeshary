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

{extends file=$cart_template_file}
{block name='cart_overview'}
  <div class="cart-overview js-cart" data-refresh-url="{url entity='cart' params=['ajax' => true, 'action' => 'refresh']}">
    {if $presentedCart.products}
    <ul class="cart-items">
      {foreach from=$presentedCart.products item=product}
        {if isset($product.isBookingProduct) && $product.isBookingProduct}
          {if isset($product.booking_product_data) && $product.booking_product_data}
            {foreach from=$product.booking_product_data item=bookingProduct}
              <li class="cart-item">
                <div class="product-line-grid">
                  <!--  product left content: image-->
                  <div class="product-line-grid-left col-md-3 col-xs-4">
                    <span class="product-image media-middle">
                      <img src="{$product.cover.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}">
                    </span>
                  </div>

                  <!--  product left body: description -->
                  <div class="product-line-grid-body col-md-3 col-xs-8">
                    <div class="product-line-info">
                      <a class="label" href="{$product.url}">{$product.name}</a>
                    </div>

                    <div class="product-line-info">
                      <span class="value">
                        {if (!$priceDisplay || $priceDisplay == 2)}
                          {$bookingProduct['unit_feature_price_tax_incl_formated']}
                        {else}
                          {$bookingProduct['unit_feature_price_tax_excl_formated']}
                        {/if}
                        </span>
                      {if $product.unit_price_full}
                        <div class="unit-price-cart">{$product.unit_price_full}</div>
                      {/if}
                    </div>

                    <br/>

                    {foreach from=$product.attributes key="attribute" item="value"}
                      <div class="product-line-info">
                        <span class="label">{$attribute}:</span>
                        <span class="value">{$value}</span>
                      </div>
                    {/foreach}

                    {if $product.customizations|count}
                      <br/>
                      {foreach from=$product.customizations item="customization"}
                        <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                        <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                              </div>
                              <div class="modal-body">
                                {foreach from=$customization.fields item="field"}
                                  <div class="product-customization-line row">
                                    <div class="col-sm-3 col-xs-4 label">
                                      {$field.label}
                                    </div>
                                    <div class="col-sm-9 col-xs-8 value">
                                      {if $field.type == 'text'}
                                        {if (int)$field.id_module}
                                          {$field.text nofilter}
                                        {else}
                                          {$field.text}
                                        {/if}
                                      {elseif $field.type == 'image'}
                                        <img src="{$field.image.small.url}">
                                      {/if}
                                    </div>
                                  </div>
                                {/foreach}
                              </div>
                            </div>
                          </div>
                        </div>
                      {/foreach}
                    {/if}
                  </div>

                  <!--  product left body: description -->
                  <div class="product-line-grid-right product-line-actions col-md-6 col-xs-12">
                    <div class="row">
                      <div class="col-xs-4 hidden-md-up"></div>
                      <div class="col-md-10 col-xs-6">
                        <div class="row">
                          <div class="col-md-5 col-xs-6 qty booking_product_date" style="font-size: 14px; font-weight: bold; text-align: center;">
                            {if $bookingProduct['booking_type'] == $booking_type_date_range}
                              {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br> {l s='To' mod='psbooking'}</br> {$bookingProduct['date_to']|date_format:"%e %b, %Y"}
                            {else}
                              {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br>
                              {$bookingProduct['time_from']} - {$bookingProduct['time_to']}
                            {/if}
                          </div>
                          <div class="col-md-2" style="line-height: 36px;">
                            <input class="booking-product-range-qty" type="hidden" value="{}">
                            {$bookingProduct['quantity']}
                          </div>
                          <div class="col-md-5 col-xs-2 price">
                            <span class="product-price">
                              <strong>
                                {if isset($product.is_gift) && $product.is_gift}
                                  <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
                                {else}
                                  {if (!$priceDisplay || $priceDisplay == 2)}
                                    {$bookingProduct['totalPriceTI']}
                                  {else}
                                    {$bookingProduct['totalPriceTE']}
                                  {/if}
                                {/if}
                              </strong>
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2 col-xs-2 text-xs-right">
                        <div class="cart-line-product-actions ">
                          <a
                            style                  = "color: #414141;display: inline-block;margin-top: .3125rem;"
                            href                   = "#"
                            id-cart-booking        = "{$bookingProduct['id_booking_cart']}"
                            class                  = "remove-booking-product"
                            id-product             = "{$product.id_product|escape:'javascript'}"
                            id-product-attribute   = "{$product.id_product_attribute|escape:'javascript'}"
                          >
                            {if !isset($product.is_gift) || !$product.is_gift}
                            <i class="material-icons pull-xs-left">delete</i>
                            {/if}
                          </a>
                          {hook h='displayCartExtraProductActions' product=$product}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="clearfix"></div>
                </div>
              </li>
            {/foreach}
          {/if}
        {else}
          <li class="cart-item">
            <div class="product-line-grid">
              <!--  product left content: image-->
              <div class="product-line-grid-left col-md-3 col-xs-4">
                <span class="product-image media-middle">
                  <img src="{$product.cover.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}">
                </span>
              </div>

              <!--  product left body: description -->
              <div class="product-line-grid-body col-md-3 col-xs-8">
                <div class="product-line-info">
                  <a class="label" href="{$product.url}">{$product.name}</a>
                </div>

                <div class="product-line-info">
                  <span class="value">{$product.price}</span>
                  {if $product.unit_price_full}
                    <div class="unit-price-cart">{$product.unit_price_full}</div>
                  {/if}
                </div>

                <br/>

                {foreach from=$product.attributes key="attribute" item="value"}
                  <div class="product-line-info">
                    <span class="label">{$attribute}:</span>
                    <span class="value">{$value}</span>
                  </div>
                {/foreach}

                {if $product.customizations|count}
                  <br/>
                  {foreach from=$product.customizations item="customization"}
                    <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                    <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                          </div>
                          <div class="modal-body">
                            {foreach from=$customization.fields item="field"}
                              <div class="product-customization-line row">
                                <div class="col-sm-3 col-xs-4 label">
                                  {$field.label}
                                </div>
                                <div class="col-sm-9 col-xs-8 value">
                                  {if $field.type == 'text'}
                                    {if (int)$field.id_module}
                                      {$field.text nofilter}
                                    {else}
                                      {$field.text}
                                    {/if}
                                  {elseif $field.type == 'image'}
                                    <img src="{$field.image.small.url}">
                                  {/if}
                                </div>
                              </div>
                            {/foreach}
                          </div>
                        </div>
                      </div>
                    </div>
                  {/foreach}
                {/if}
              </div>

              <!--  product left body: description -->
              <div class="product-line-grid-right product-line-actions col-md-6 col-xs-12">
                <div class="row">
                  <div class="col-xs-4 hidden-md-up"></div>
                  <div class="col-md-10 col-xs-6">
                    <div class="row">
                      <div class="col-md-7 col-xs-6 qty">
                        {if isset($product.is_gift) && $product.is_gift}
                          <span class="gift-quantity">{$product.quantity}</span>
                        {else}
                          <input
                            class="js-cart-line-product-quantity"
                            data-down-url="{$product.down_quantity_url}"
                            data-up-url="{$product.up_quantity_url}"
                            data-update-url="{$product.update_quantity_url}"
                            data-product-id="{$product.id_product}"
                            type="text"
                            value="{$product.quantity}"
                            name="product-quantity-spin"
                            min="{$product.minimal_quantity}"
                          />
                        {/if}
                      </div>
                      <div class="col-md-5 col-xs-2 price">
                        <span class="product-price">
                          <strong>
                            {if isset($product.is_gift) && $product.is_gift}
                              <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
                            {else}
                              {$product.total}
                            {/if}
                          </strong>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-2 col-xs-2 text-xs-right">
                    <div class="cart-line-product-actions ">
                      <a
                          class                       = "remove-from-cart"
                          rel                         = "nofollow"
                          href                        = "{$product.remove_from_cart_url}"
                          data-link-action            = "delete-from-cart"
                          data-id-product             = "{$product.id_product|escape:'javascript'}"
                          data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript'}"
                          data-id-customization       = "{$product.id_customization|escape:'javascript'}"
                      >
                        {if !isset($product.is_gift) || !$product.is_gift}
                        <i class="material-icons pull-xs-left">delete</i>
                        {/if}
                      </a>
                      {hook h='displayCartExtraProductActions' product=$product}
                    </div>
                  </div>
                </div>
              </div>

              <div class="clearfix"></div>
            </div>
          </li>
        {/if}
        {if $product.customizations|count >1}
        <hr>
        {/if}
      {/foreach}
    </ul>
    {else}
      <span class="no-items">{l s='There are no more items in your cart' d='Shop.Theme.Checkout'}</span>
    {/if}
  </div>
{/block}
