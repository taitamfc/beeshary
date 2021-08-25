{*
 * 2017-2018 PHPIST
 *
 *  @author    Yassine Belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 *}

{extends file=$layout}

{block name='content'}
<div class="wk-mp-block">
    {hook h="displayMPMyAccountMenu"}
    <div class="wk-mp-content">
        <div class="wk-mp-right-column">
            <div class="row">
                <div class="col-sm-12 section-heading"><h2>Mes achats</h2></div>
                {if $orders}
                <table class="table table-striped table-bordered table-labeled col-sm-down">
                  <thead class="">
                    <tr>
                      <th>{l s='Reference' d='Shop.Theme.Checkout'}</th>
                      <th>{l s='Date' d='Shop.Theme.Checkout'}</th>
                      <th>{l s='Total price' d='Shop.Theme.Checkout'}</th>
                      <th class="hidden-md-down">{l s='Payment' d='Shop.Theme.Checkout'}</th>
                      <th class="hidden-md-down">{l s='Status' d='Shop.Theme.Checkout'}</th>
                      <th>{l s='Invoice' d='Shop.Theme.Checkout'}</th>
                      <th>&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    {foreach from=$orders item=order}
                      <tr>
                        <th scope="row">{$order.details.reference}</th>
                        <td>{$order.details.order_date}</td>
                        <td class="text-xs-right">{$order.totals.total.value}</td>
                        <td class="hidden-md-down">{$order.details.payment}</td>
                        <td>
                          <span
                            class="label label-pill {$order.history.current.contrast}"
                            style="background-color:{$order.history.current.color}"
                          >
                            {$order.history.current.ostate_name}
                          </span>
                        </td>
                        <td class="text-xs-center hidden-md-down">
                          {if $order.details.invoice_url}
                            <a href="{$order.details.invoice_url}"><i class="material-icons">&#xE415;</i></a>
                          {else}
                            -
                          {/if}
                        </td>
                        <td class="text-xs-center order-actions">
                          {***<!--a href="{$order.details.details_url}" data-link-action="view-order-details">
                            <i class="material-icons">&#xE8B6;</i>
                          </a-->***}
                          {if $order.details.reorder_url}
                            <a href="{$order.details.reorder_url}"><i class="material-icons">&#xE863;</i></a>
                          {/if}
                        </td>
                      </tr>
                    {/foreach}
                    {if $orders.subscription_invoice}
                    {foreach from=$orders.subscription_invoice item=order_sub}
                      <tr>
                          <th>{$order_sub.number}</th>
                          <td>{$order_sub.date|date_format:'%d/%m/%Y'}</td>
                          <td  class="text-xs-right">{$order_sub.total/100} &euro;</td>
                          <td class="hidden-md-down">Abonnement</td>
                          <td>
                          <span class="label label-pill" style="background-color:green">
                            Actif
                          </span>
                        </td>
                          <td class="text-xs-center hidden-md-down">
                          {if $order_sub.invoice_pdf}
                            <a href="{$order_sub.invoice_pdf}"><i class="material-icons">&#xE415;</i></a>
                          {else}
                            -
                          {/if}
                        </td>
                        <td class="text-xs-center order-actions">
                          
                        </td>
                      </tr>
                      {/foreach}
                    {/if}
                  </tbody>
                </table>

                <div class="orders hidden-md-up">
                  {foreach from=$orders item=order}
                    <div class="order">
                      <div class="row">
                        <div class="col-xs-10">
                          <a href="{$order.details.details_url}"><h3>{$order.details.reference}</h3></a>
                          <div class="date">{$order.details.order_date}</div>
                          <div class="total">{$order.totals.total.value}</div>
                          <div class="status">
                            <span
                              class="label label-pill {$order.history.current.contrast}"
                              style="background-color:{$order.history.current.color}"
                            >
                              {$order.history.current.ostate_name}
                            </span>
                          </div>
                        </div>
                        <div class="col-xs-2 text-xs-right">
                            <!--div>
                              <a href="{$order.details.details_url}" data-link-action="view-order-details" title="{l s='Details' d='Shop.Theme.Customeraccount'}">
                                <i class="material-icons">&#xE8B6;</i>
                              </a>
                            </div-->
                            {if $order.details.reorder_url}
                              <div>
                                <a href="{$order.details.reorder_url}" title="{l s='Reorder' d='Shop.Theme.Actions'}">
                                  <i class="material-icons">&#xE863;</i>
                                </a>
                              </div>
                            {/if}
                        </div>
                      </div>
                    </div>
                  {/foreach}
                </div>
                {/if}
            </div>
        </div>
    </div>
</div>
{/block}
