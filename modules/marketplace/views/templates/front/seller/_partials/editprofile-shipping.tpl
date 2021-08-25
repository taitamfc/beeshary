{**
 * 2017-2018 PHPIST.
 *
 *  @author    Yassine belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 *}

<div id="pp_seller_creation">
	<div class="form_container sellerprofile_form_container">
	<div id="sellerCreationForm" class="form_fields editsellerprofile">
		<div id="sellerDeliveryMethodForm">
			<div class="alert alert-danger pp_display_errors_store" style="display: none;"></div>
			<input type="hidden" name="id_ps_wk_mp_seller_delivery" value="{if isset($seller_delivery_obj) && $seller_delivery_obj.id_ps_wk_mp_seller_delivery}{$seller_delivery_obj.id_ps_wk_mp_seller_delivery}{/if}" />
			
			<div class="form-group">
				<div class="input_left">
					<select class="form-control" id="delivery_method" name="delivery_method[]" multiple data-placeholder="Méthode de livraison">
						{foreach from=$delivery_methods item=dm}
							<option value="{$dm}"{if isset($seller_delivery_obj) && $seller_delivery_obj.id_ps_wk_mp_seller_delivery}{if in_array($dm, $seller_delivery_obj.delivery_method)} selected{/if}{/if}>{$dm}</option>
						{/foreach}
					</select>
				</div>

				<select class="form-control input_right" id="delivery_delay" name="delivery_delay">
					<option value="">Délai de livraison</option>
					{foreach from=$delivery_delays item=dd}
						<option value="{$dd}"{if isset($seller_delivery_obj) && $seller_delivery_obj.id_ps_wk_mp_seller_delivery}{if $dd == $seller_delivery_obj.delivery_delay} selected{/if}{/if}>{$dd}</option>
					{/foreach}
				</select>
				<div class="clearfix"></div>
			</div>

			<div class="form-group">
				<div class="input_left">
					<select class="form-control" id="shipping_days" name="shipping_days[]" multiple data-placeholder="Jours d'expédition">
						{foreach from=$shipping_days item=sd}
							<option value="{$sd}"{if isset($seller_delivery_obj) && $seller_delivery_obj.id_ps_wk_mp_seller_delivery}{if in_array($sd, $seller_delivery_obj.shipping_days)} selected{/if}{/if}>{$sd}</option>
						{/foreach}
					</select>
				</div>

				<select class="form-control input_right" id="option_free_delivery" name="option_free_delivery">
					<option value="">Option livraison</option>
					{foreach from=$option_free_deliverys item=ofd key=perd}
						<option value="{$perd}"{if isset($seller_delivery_obj) && $seller_delivery_obj.id_ps_wk_mp_seller_delivery}{if $perd == $seller_delivery_obj.option_free_delivery} selected{/if}{/if}>{$ofd}</option>
					{/foreach}
				</select>
				<div class="clearfix"></div>
			</div>

			<div class="form-group dm_summary">
				<h4>Vos conditions de livraison tel que présentée sur le site</h4>

				<div class="md_summary_text">
					<p class="title">Délais de livraison et jours expédition:</p>
					<p class="summary">Nos jours d'expédition sont les <span class="filled_text shipping_days_fill">{if isset($seller_delivery_obj) && $seller_delivery_obj.id_ps_wk_mp_seller_delivery}{implode(', ', $seller_delivery_obj.shipping_days)}{else}xxxx{/if}</span>.
					Les commandes reçues le week-end seront expédiées le <span class="filled_text shipping_wkd_fill">{$first_shipping_day}</span> suivant. A cela s'ajoute le délai de livraison habituel.<br />
					Nous travaillons avec <span class="filled_text shipping_delivery_method_fill">{if isset($seller_delivery_obj) && $seller_delivery_obj.id_ps_wk_mp_seller_delivery}{implode(' et ', $seller_delivery_obj.delivery_method)}{else}xxxx{/if}</span> pour vous assurer le meilleur suivi possible.</p>

					<div class="frais_livraison"{if isset($seller_delivery_obj) && $seller_delivery_obj.id_ps_wk_mp_seller_delivery}{if $seller_delivery_obj.option_free_delivery == 0} style="display: none;" {/if}{/if}>
						<p class="title">Frais de livraison</p>
						{if isset($seller_delivery_obj) && $seller_delivery_obj.id_ps_wk_mp_seller_delivery}
							{if $seller_delivery_obj.option_free_delivery != 0}
								<p class="summary">Nous offerons les frais de livraison à partir de <span class="filled_text shipping_cost_fill">{$seller_delivery_obj.option_free_delivery}</span> euros d'achat.</p>
							{else}
								<p class="summary">Nous offerons les frais de livraison à partir de <span class="filled_text shipping_cost_fill">x</span> euros d'achat.</p>
							{/if}
						{else}
							<p class="summary">Nous offerons les frais de livraison à partir de <span class="filled_text shipping_cost_fill">x</span> euros d'achat.</p>
						{/if}
					</div>

					<p class="title">Remboursement et échange:</p>
					<p class="summary">Nous veillons à ce que tout soit parfait pour vous. Si d'avantage quelque chose nous échappait et que votre colis arrivait abîmé, n'hesitez pas à nous contacter dans le 3 jours pour que nous trouvions la meilleur solution ensemble.</p>
				</div>
			</div>
		</div> 
	</div>
	</div>
</div>