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

{extends file=$order_confirmation_template_file}
{block name='order_confirmation_table'}
  {block name='order-items-table-head'}
  <div id="order-items" class="col-md-8">
    <h3 class="card-title h3">{l s='Order items' d='Shop.Theme.Checkout'}</h3>
  {/block}
    <div class="order-confirmation-table" style="text-align: center;">
      <table class="table">
        {foreach from=$orderProducts item=product}
          {if isset($product.isBookingProduct) && $product.isBookingProduct}
            {if isset($product.booking_product_data) && $product.booking_product_data}
              {foreach from=$product.booking_product_data item=bookingProduct}
                <div class="order-line row">
                  <div class="col-sm-2 col-xs-3">
                    <span class="image">
                      <img src="{$product.cover.medium.url}" />
                    </span>
                  </div>
                  <div class="col-sm-4 col-xs-9 details">
                    {if $add_product_link}<a href="{$product.url}" target="_blank">{/if}
                      <span>{$product.name}</span>
                    {if $add_product_link}</a>{/if}
                    {if $product.customizations|count}
                      {foreach from=$product.customizations item="customization"}
                        <div class="customizations">
                          <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                        </div>
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
                    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                  </div>
                  <div class="col-sm-6 col-xs-12 qty">
                    <div class="row">
                      <div class="col-xs-4 text-sm-right text-xs-left booking_date_range" style="font-size: 14px; font-weight: bold; text-align: center!important;">
                        {if $bookingProduct['booking_type'] == $booking_type_date_range}
                          {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br>
                          {l s='To' mod='psbooking'}</br>
                          {$bookingProduct['date_to']|date_format:"%e %b, %Y"}
                        {else}
                          {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br>
                          {$bookingProduct['time_from']} - {$bookingProduct['time_to']}
                        {/if}
                      </div>
                      <div class="col-xs-1">{$bookingProduct['quantity']}</div>
                      <div class="col-xs-3">
                        {if (!$priceDisplay || $priceDisplay == 2)}
                          {$bookingProduct['unit_feature_price_tax_incl_formated']}
                        {else}
                          {$bookingProduct['unit_feature_price_tax_excl_formated']}
                        {/if}
                      </div>
                      <div class="col-xs-4 text-xs-right bold">
                        {if (!$priceDisplay || $priceDisplay == 2)}
                          {$bookingProduct['total_range_feature_price_tax_incl_formated']}
                        {else}
                          {$bookingProduct['total_range_feature_price_tax_excl_formated']}
                        {/if}
                      </div>
                    </div>
                  </div>
                </div>
              {/foreach}
            {/if}
          {else}
            <div class="order-line row">
              <div class="col-sm-2 col-xs-3">
                <span class="image">
                  <img src="{$product.cover.medium.url}" />
                </span>
              </div>
              <div class="col-sm-4 col-xs-9 details">
                {if $add_product_link}<a href="{$product.url}" target="_blank">{/if}
                  <span>{$product.name}</span>
                {if $add_product_link}</a>{/if}
                {if $product.customizations|count}
                  {foreach from=$product.customizations item="customization"}
                    <div class="customizations">
                      <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                    </div>
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
                {hook h='displayProductPriceBlock' product=$product type="unit_price"}
              </div>
              <div class="col-sm-6 col-xs-12 qty">
                <div class="row">
                  <div class="col-xs-5">{$product.quantity}</div>
                  <div class="col-xs-3 text-sm-right text-xs-left">{$product.price}</div>
                  <div class="col-xs-4 text-xs-right bold">{$product.total}</div>
                </div>
              </div>
            </div>
          {/if}
        {/foreach}
      <hr />
      <table>
        {foreach $subtotals as $subtotal}
          {if $subtotal.type !== 'tax'}
            <tr>
              <td>{$subtotal.label}</td>
              <td>{$subtotal.value}</td>
            </tr>
          {/if}
        {/foreach}
        {if $subtotals.tax.label !== null}
          <tr class="sub">
            <td>{$subtotals.tax.label}</td>
            <td>{$subtotals.tax.value}</td>
          </tr>
        {/if}
        <tr class="font-weight-bold">
          <td><span class="text-uppercase">{$totals.total.label}</span> {$labels.tax_short}</td>
          <td>{$totals.total.value}</td>
        </tr>
      </table>
    </div>
  </div>
{/block}
