{*
* 2017-2018 PHPIST
*
*  @author    Yassine belkaid <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PHPIST
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}

{block name='content'}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="wk-mp-right-column">
				<div class="content_top center-block text-center">
					{if $logic == 3}
					    <img src="{$urls.img_url}bee-post-g4.svg" />
					    <div class="content_top_title">Mes achats</div>
				    {else}
				    	<img src="{$urls.img_url}bee-activites-g4.svg" />
					    <div class="content_top_title">Mes réservations en cours</div>
				    {/if}
					<div class="content_top_info"></div>
				</div>
				<h6>{l s='Here are the orders you\'ve placed since your account was created.' d='Shop.Theme.Customeraccount'}</h6>

				{if $purchases}
				<table class="table table-striped table-bordered table-labeled hidden-sm-down">
				      <thead class="">
				        <tr>
				          <th>{l s='Order reference' d='Shop.Theme.Checkout'}</th>
				          <th>{l s='Date' d='Shop.Theme.Checkout'}</th>
				          <th>{l s='Total price' d='Shop.Theme.Checkout'}</th>
				          <th class="hidden-md-down">{l s='Payment' d='Shop.Theme.Checkout'}</th>
				          <th class="hidden-md-down">{l s='Status' d='Shop.Theme.Checkout'}</th>
				          <th>{l s='Invoice' d='Shop.Theme.Checkout'}</th>
				          <th>&nbsp;</th>
				        </tr>
				      </thead>
				      <tbody>
				        {foreach from=$purchases item=purchase}
				          <tr>
				            <th scope="row">{$purchase.details.reference}</th>
				            <td>{$purchase.details.order_date}</td>
				            <td class="text-xs-right">{$purchase.totals.total.value}</td>
				            <td class="hidden-md-down">{$purchase.details.payment}</td>
				            <td>
				              <span
				                class="label label-pill {$purchase.history.current.contrast}"
				                style="background-color:{$purchase.history.current.color}"
				              >
				                {$purchase.history.current.ostate_name}
				              </span>
				            </td>
				            <td class="text-xs-center hidden-md-down">
				              {if $purchase.details.invoice_url}
				                <a href="{$purchase.details.invoice_url}"><i class="material-icons">&#xE415;</i></a>
				              {else}
				                -
				              {/if}
				            </td>
				            <td class="text-xs-center order-actions">
				              	<a href="{$purchase.details.details_url}" data-link-action="view-order-details">
				                	<i class="material-icons">&#xE8B6;</i>
				              	</a>
				              {if $purchase.details.reorder_url}
				                <a href="{$purchase.details.reorder_url}"><i class="material-icons">&#xE863;</i></a>
				              {/if}
				            </td>
				          </tr>
				        {/foreach}
				      </tbody>
				</table>

				<div class="orders hidden-md-up">
				      {foreach from=$purchases item=purchase}
				        <div class="order">
				          <div class="row">
				            <div class="col-xs-10">
				              <a href="{$purchase.details.details_url}"><h3>{$purchase.details.reference}</h3></a>
				              <div class="date">{$purchase.details.order_date}</div>
				              <div class="total">{$purchase.totals.total.value}</div>
				              <div class="status">
				                <span
				                  class="label label-pill {$purchase.history.current.contrast}"
				                  style="background-color:{$purchase.history.current.color}"
				                >
				                  {$purchase.history.current.ostate_name}
				                </span>
				              </div>
				            </div>
				            <div class="col-xs-2 text-xs-right">
				                <div>
				                  <a href="{$purchase.details.details_url}" data-link-action="view-order-details" title="{l s='Details' d='Shop.Theme.Customeraccount'}">
				                    <i class="material-icons">&#xE8B6;</i>
				                  </a>
				                </div>
				                {if $purchase.details.reorder_url}
				                  <div>
				                    <a href="{$purchase.details.reorder_url}" title="{l s='Reorder' d='Shop.Theme.Actions'}">
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
{/block}