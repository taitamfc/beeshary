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

{extends file=$order_details_template_file}
{block name='order_infos'}
    <div id="order-infos">
      <div class="box">
          <div class="row">
            <div class="col-xs-{if $order.details.reorder_url}9{else}12{/if}">
              <strong>
                {l
                  s='Order Reference %reference% - placed on %date%'
                  d='Shop.Theme.Customeraccount'
                  sprintf=['%reference%' => $order.details.reference, '%date%' => $order.details.order_date]
                }
              </strong>
            </div>
            <div class="clearfix"></div>
          </div>
      </div>

      <div class="box">
          <ul>
            <li><strong>{l s='Carrier' d='Shop.Theme.Checkout'}</strong> {$order.carrier.name}</li>
            <li><strong>{l s='Payment method' d='Shop.Theme.Checkout'}</strong> {$order.details.payment}</li>

            {if $order.details.invoice_url}
              <li>
                <a href="{$order.details.invoice_url}">
                  {l s='Download your invoice as a PDF file.' d='Shop.Theme.Customeraccount'}
                </a>
              </li>
            {/if}

            {if $order.details.recyclable}
              <li>
                {l s='You have given permission to receive your order in recycled packaging.' d='Shop.Theme.Customeraccount'}
              </li>
            {/if}

            {if $order.details.gift_message}
              <li>{l s='You have requested gift wrapping for this order.' d='Shop.Theme.Customeraccount'}</li>
              <li>{l s='Message' d='Shop.Theme.Customeraccount'} {$order.details.gift_message nofilter}</li>
            {/if}
          </ul>
      </div>
    </div>
  {/block}

{block name='order_detail'}
	{if $order.details.is_returnable}
      <form id="order-return-form" action="{$urls.pages.order_follow}" method="post">
		  <div class="box hidden-sm-down">
		    <table id="order-products" class="table table-bordered return">
		      <thead class="thead-default">
		        <tr>
		          <th class="head-checkbox"><input type="checkbox"/></th>
		          <th>{l s='Product' d='Shop.Theme.Catalog'}</th>
		          <th>{l s='Quantity' d='Shop.Theme.Catalog'}</th>
		          <th>{l s='Returned' d='Shop.Theme.CustomerAccount'}</th>
		          <th>{l s='Unit price' d='Shop.Theme.Catalog'}</th>
		          <th>{l s='Total price' d='Shop.Theme.Catalog'}</th>
		        </tr>
		      </thead>
		      {foreach from=$orderProducts item=product}
            {if isset($product.isBookingProduct) && $product.isBookingProduct}
              {if isset($product.booking_product_data) && $product.booking_product_data}
                {foreach from=$product.booking_product_data item=bookingProduct}
    		          <tr>
    		          <td>
    		            {if !$product.customizations}
    		              <span id="_desktop_product_line_{$product.id_order_detail}">
    		                <input type="checkbox" id="cb_{$product.id_order_detail}" name="ids_order_detail[{$product.id_order_detail}]" value="{$product.id_order_detail}">
    		              </span>
    		            {else}
    		              {foreach $product.customizations  as $customization}
    		                <span id="_desktop_product_customization_line_{$product.id_order_detail}_{$customization.id_customization}">
    		                  <input type="checkbox" id="cb_{$product.id_order_detail}" name="customization_ids[{$product.id_order_detail}][]" value="{$customization.id_customization}">
    		                </span>
    		              {/foreach}
    		            {/if}
    		          </td>
    		          <td>
    		            <strong>{$product.name}</strong><br/>
    		            {if $product.reference}
    		              {l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference}<br/>
    		            {/if}
    		            {if $product.customizations}
    		              {foreach from=$product.customizations item="customization"}
    		                <div class="customization">
    		                  <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
    		                </div>
    		                <div id="_desktop_product_customization_modal_wrapper_{$customization.id_customization}">
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
    		                </div>
    		              {/foreach}
    		            {/if}
    		          </td>
    		          <td class="qty">
    		            {if !$product.customizations}
    		              <div class="current">
    		                {$product.quantity}
    		              </div>
    		              {if $product.quantity > $product.qty_returned}
    		                <div class="select" id="_desktop_return_qty_{$product.id_order_detail}">
    		                  <select name="order_qte_input[{$product.id_order_detail}]" class="form-control form-control-select">
    		                    {section name=quantity start=1 loop=$product.quantity+1-$product.qty_returned}
    		                      <option value="{$smarty.section.quantity.index}">{$smarty.section.quantity.index}</option>
    		                    {/section}
    		                  </select>
    		                </div>
    		              {/if}
    		            {else}
    		              {foreach $product.customizations as $customization}
    		                 <div class="current">
    		                  {$customization.quantity}
    		                </div>
    		                <div class="select" id="_desktop_return_qty_{$product.id_order_detail}_{$customization.id_customization}">
    		                  <select
    		                    name="customization_qty_input[{$customization.id_customization}]"
    		                    class="form-control form-control-select"
    		                  >
    		                    {section name=quantity start=1 loop=$customization.quantity+1}
    		                      <option value="{$smarty.section.quantity.index}">{$smarty.section.quantity.index}</option>
    		                    {/section}
    		                  </select>
    		                </div>
    		              {/foreach}
    		              <div class="clearfix"></div>
    		            {/if}
    		          </td>
                  <td class="text-center">
                    <p>{l s='quantity' mod='mpbooking'}-{$product.qty_returned}</p>
                    <p class="booking_date_range" style="font-size: 14px; font-weight: bold;">
                      {if $bookingProduct['booking_type'] == $booking_type_date_range}
                        {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br>
                        {l s='To' mod='mpbooking'}</br>
                        {$bookingProduct['date_to']|date_format:"%e %b, %Y"}
                      {else}
                        {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br>
                        {$bookingProduct['time_from']} - {$bookingProduct['time_to']}
                      {/if}
                    </p>
                  </td>
    		          <td class="text-xs-right">{$bookingProduct['product_real_price_tax_excl_formated']}</td>
                  <td class="text-xs-right">{$bookingProduct['total_range_feature_price_tax_excl_formated']}</td>
    		          </tr>
                {/foreach}
              {/if}
            {else}
              <tr>
                <td>
                  {if !$product.customizations}
                    <span id="_desktop_product_line_{$product.id_order_detail}">
                      <input type="checkbox" id="cb_{$product.id_order_detail}" name="ids_order_detail[{$product.id_order_detail}]" value="{$product.id_order_detail}">
                    </span>
                  {else}
                    {foreach $product.customizations  as $customization}
                      <span id="_desktop_product_customization_line_{$product.id_order_detail}_{$customization.id_customization}">
                        <input type="checkbox" id="cb_{$product.id_order_detail}" name="customization_ids[{$product.id_order_detail}][]" value="{$customization.id_customization}">
                      </span>
                    {/foreach}
                  {/if}
                </td>
                <td>
                  <strong>{$product.name}</strong><br/>
                  {if $product.reference}
                    {l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference}<br/>
                  {/if}
                  {if $product.customizations}
                    {foreach from=$product.customizations item="customization"}
                      <div class="customization">
                        <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                      </div>
                      <div id="_desktop_product_customization_modal_wrapper_{$customization.id_customization}">
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
                      </div>
                    {/foreach}
                  {/if}
                </td>
                <td class="qty">
                  {if !$product.customizations}
                    <div class="current">
                      {$product.quantity}
                    </div>
                    {if $product.quantity > $product.qty_returned}
                      <div class="select" id="_desktop_return_qty_{$product.id_order_detail}">
                        <select name="order_qte_input[{$product.id_order_detail}]" class="form-control form-control-select">
                          {section name=quantity start=1 loop=$product.quantity+1-$product.qty_returned}
                            <option value="{$smarty.section.quantity.index}">{$smarty.section.quantity.index}</option>
                          {/section}
                        </select>
                      </div>
                    {/if}
                  {else}
                    {foreach $product.customizations as $customization}
                       <div class="current">
                        {$customization.quantity}
                      </div>
                      <div class="select" id="_desktop_return_qty_{$product.id_order_detail}_{$customization.id_customization}">
                        <select
                          name="customization_qty_input[{$customization.id_customization}]"
                          class="form-control form-control-select"
                        >
                          {section name=quantity start=1 loop=$customization.quantity+1}
                            <option value="{$smarty.section.quantity.index}">{$smarty.section.quantity.index}</option>
                          {/section}
                        </select>
                      </div>
                    {/foreach}
                    <div class="clearfix"></div>
                  {/if}
                </td>
                <td class="text-xs-right">{$product.qty_returned}</td>
                <td class="text-xs-right">{$product.price}</td>
                <td class="text-xs-right">{$product.total}</td>
              </tr>
            {/if}
		      {/foreach}
		      <tfoot>
		        {foreach $order.subtotals as $line}
		          {if $line.value}
		            <tr class="text-xs-right line-{$line.type}">
		              <td colspan="5">{$line.label}</td>
		              <td colspan="2">{$line.value}</td>
		            </tr>
		          {/if}
		        {/foreach}
		        <tr class="text-xs-right line-{$order.totals.total.type}">
		          <td colspan="5">{$order.totals.total.label}</td>
		          <td colspan="2">{$order.totals.total.value}</td>
		        </tr>
		      </tfoot>
		    </table>
		  </div>

		  <div class="order-items hidden-md-up box">
		    {foreach from=$order.products item=product}
		      <div class="order-item">
		        <div class="row">
		          <div class="checkbox">
		            {if !$product.customizations}
		              <span id="_mobile_product_line_{$product.id_order_detail}"></span>
		            {else}
		              {foreach $product.customizations  as $customization}
		                <span id="_mobile_product_customization_line_{$product.id_order_detail}_{$customization.id_customization}"></span>
		              {/foreach}
		            {/if}
		          </div>
		          <div class="content">
		            <div class="row">
		              <div class="col-sm-5 desc">
		                <div class="name">{$product.name}</div>
		                {if $product.reference}
		                  <div class="ref">{l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference}</div>
		                {/if}
		                {if $product.customizations}
		                  {foreach $product.customizations as $customization}
		                    <div class="customization">
		                      <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
		                    </div>
		                    <div id="_mobile_product_customization_modal_wrapper_{$customization.id_customization}">
		                    </div>
		                  {/foreach}
		                {/if}
		              </div>
		              <div class="col-sm-7 qty">
		                <div class="row">
		                  <div class="col-xs-4 text-sm-left text-xs-left">
		                    {$product.price}
		                  </div>
		                  <div class="col-xs-4">
		                    {if $product.customizations}
		                      {foreach $product.customizations as $customization}
		                        <div class="q">{l s='Quantity' d='Shop.Theme.Catalog'}: {$customization.quantity}</div>
		                        <div class="s" id="_mobile_return_qty_{$product.id_order_detail}_{$customization.id_customization}"></div>
		                      {/foreach}
		                    {else}
		                      <div class="q">{l s='Quantity' d='Shop.Theme.Catalog'}: {$product.quantity}</div>
		                      {if $product.quantity > $product.qty_returned}
		                        <div class="s" id="_mobile_return_qty_{$product.id_order_detail}"></div>
		                      {/if}
		                    {/if}
		                    {if $product.qty_returned > 0}
		                      <div>{l s='Returned' d='Shop.Theme.CustomerAccount'}: {$product.qty_returned}</div>
		                    {/if}
		                  </div>
		                  <div class="col-xs-4 text-xs-right">
		                    {$product.total}
		                  </div>
		                </div>
		              </div>
		            </div>
		          </div>
		        </div>
		      </div>
		    {/foreach}
		  </div>
		  <div class="order-totals hidden-md-up box">
		    {foreach $order.subtotals as $line}
		      {if $line.value}
		        <div class="order-total row">
		          <div class="col-xs-8"><strong>{$line.label}</strong></div>
		          <div class="col-xs-4 text-xs-right">{$line.value}</div>
		        </div>
		      {/if}
		    {/foreach}
		    <div class="order-total row">
		      <div class="col-xs-8"><strong>{$order.totals.total.label}</strong></div>
		      <div class="col-xs-4 text-xs-right">{$order.totals.total.value}</div>
		    </div>
		  </div>

		  <div class="box">
		    <header>
		      <h3>{l s='Merchandise return' d='Shop.Theme.CustomerAccount'}</h3>
		      <p>{l s='If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.' d='Shop.Theme.CustomerAccount'}</p>
		    </header>
		    <section class="form-fields">
		      <div class="form-group">
		        <textarea cols="67" rows="3" name="returnText" class="form-control"></textarea>
		      </div>
		    </section>
		    <footer class="form-footer">
		      <input type="hidden" name="id_order" value="{$order.details.id}">
		      <button class="form-control-submit btn btn-primary" type="submit" name="submitReturnMerchandise">
		        {l s='Request a return' d='Shop.Theme.CustomerAccount'}
		      </button>
		    </footer>
		  </div>
		</form>
    {else}
      <div class="box hidden-sm-down">
		  <table id="order-products" class="table table-bordered">
		    <thead class="thead-default">
		      <tr>
		        <th>{l s='Product' d='Shop.Theme.Catalog'}</th>
		        <th>{l s='Quantity' d='Shop.Theme.Catalog'}</th>
		        <th>{l s='Unit price' d='Shop.Theme.Catalog'}</th>
		        <th>{l s='Total price' d='Shop.Theme.Catalog'}</th>
		      </tr>
		    </thead>
		    {foreach from=$orderProducts item=product}
		    	{if isset($product.isBookingProduct) && $product.isBookingProduct}
            {if isset($product.booking_product_data) && $product.booking_product_data}
              {foreach from=$product.booking_product_data item=bookingProduct}
      		      <tr>
      		        <td>
      		          <strong>
      		            <a {if isset($product.download_link)}href="{$product.download_link}"{/if}>
      		              {$product.name}
      		            </a>
      		          </strong><br/>
      		          {if $product.reference}
      		            {l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference}<br/>
      		          {/if}
      		          {if $product.customizations}
      		            {foreach from=$product.customizations item="customization"}
      		              <div class="customization">
      		                <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
      		              </div>
      		              <div id="_desktop_product_customization_modal_wrapper_{$customization.id_customization}">
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
      		              </div>
      		            {/foreach}
      		          {/if}
      		        </td>
      		        <td>
      		          <p>
                      {l s='quantity' mod='mpbooking'}-{if $product.customizations}
        		            {foreach $product.customizations as $customization}
        		              {$customization.quantity}
        		            {/foreach}
        		          {else}
        		            {$bookingProduct['quantity']}
        		          {/if}
                    </p>
                    <p class="booking_date_range" style="font-size: 14px; font-weight: bold;">
                      {if $bookingProduct['booking_type'] == $booking_type_date_range}
                        {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br>
                        {l s='To' mod='mpbooking'}</br>
                        {$bookingProduct['date_to']|date_format:"%e %b, %Y"}
                      {else}
                        {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br>
                        {$bookingProduct['time_from']} - {$bookingProduct['time_to']}
                      {/if}
                    </p>
      		        </td>
      		        <td class="text-xs-right">{$bookingProduct['unit_feature_price_tax_excl_formated']}</td>
      		        <td class="text-xs-right">{$bookingProduct['total_range_feature_price_tax_excl_formated']}</td>
      		      </tr>
              {/foreach}
            {/if}
          {else}
            <tr>
              <td>
                <strong>
                  <a {if isset($product.download_link)}href="{$product.download_link}"{/if}>
                    {$product.name}
                  </a>
                </strong><br/>
                {if $product.reference}
                  {l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference}<br/>
                {/if}
                {if $product.customizations}
                  {foreach from=$product.customizations item="customization"}
                    <div class="customization">
                      <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                    </div>
                    <div id="_desktop_product_customization_modal_wrapper_{$customization.id_customization}">
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
                    </div>
                  {/foreach}
                {/if}
              </td>
              <td>
                {if $product.customizations}
                  {foreach $product.customizations as $customization}
                    {$customization.quantity}
                  {/foreach}
                {else}
                  {$product.quantity}
                {/if}
              </td>
              <td class="text-xs-right">{$product.price}</td>
              <td class="text-xs-right">{$product.total}</td>
            </tr>
          {/if}
		    {/foreach}
		    <tfoot>
		      {foreach $order.subtotals as $line}
		        {if $line.value}
		          <tr class="text-xs-right line-{$line.type}">
		            <td colspan="3">{$line.label}</td>
		            <td>{$line.value}</td>
		          </tr>
		        {/if}
		      {/foreach}
		      <tr class="text-xs-right line-{$order.totals.total.type}">
		        <td colspan="3">{$order.totals.total.label}</td>
		        <td>{$order.totals.total.value}</td>
		      </tr>
		    </tfoot>
		  </table>
		</div>

		<div class="order-items hidden-md-up box">
		  {foreach from=$order.products item=product}
		    <div class="order-item">
		      <div class="row">
		        <div class="col-sm-5 desc">
		          <div class="name">{$product.name}</div>
		          {if $product.reference}
		            <div class="ref">{l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference}</div>
		          {/if}
		          {if $product.customizations}
		            {foreach $product.customizations as $customization}
		              <div class="customization">
		                <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
		              </div>
		              <div id="_mobile_product_customization_modal_wrapper_{$customization.id_customization}">
		              </div>
		            {/foreach}
		          {/if}
		        </div>
		        <div class="col-sm-7 qty">
		          <div class="row">
		            <div class="col-xs-4 text-sm-left text-xs-left">
		              {$product.price}
		            </div>
		            <div class="col-xs-4">
		              {if $product.customizations}
		                {foreach $product.customizations as $customization}
		                  {$customization.quantity}
		                {/foreach}
		              {else}
		                {$product.quantity}
		              {/if}
		            </div>
		            <div class="col-xs-4 text-xs-right">
		              {$product.total}
		            </div>
		          </div>
		        </div>
		      </div>
		    </div>
		  {/foreach}
		</div>
		<div class="order-totals hidden-md-up box">
		  {foreach $order.subtotals as $line}
		    {if $line.value}
		      <div class="order-total row">
		        <div class="col-xs-8"><strong>{$line.label}</strong></div>
		        <div class="col-xs-4 text-xs-right">{$line.value}</div>
		      </div>
		    {/if}
		  {/foreach}
		  <div class="order-total row">
		    <div class="col-xs-8"><strong>{$order.totals.total.label}</strong></div>
		    <div class="col-xs-4 text-xs-right">{$order.totals.total.value}</div>
		  </div>
		</div>
    {/if}
{/block}
