{**
 * 2017-2018 PHPIST.
 *
 *  @author    Yassine belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 *}

<div id="sellerDeliveryMethodForm" style="display: none;">
	<div class="prv_section">
		<a class="prv_images" href="javascript:void(0);">
			<img src="{$smarty.const._THEME_IMG_DIR_}bee-fleche.svg" />
		</a>
	</div>
	<div class="alert alert-danger pp_display_errors_store" style="display: none;"></div>
	<div class="form-group">
        <img class="center-block store_top_pic" src="{$urls.base_url}themes/beeshary_child/assets/img/picto-boutique.jpg" />
        <div class="pp_seller_profile_title">{l s='Delivery method' mod='mpsellerwiselogin'}</div>
        <div class="pp_seller_profile_subtitle">Sélectionnez votre mode de livraison. Vous pouvez définir un ou plusieurs jours d’expédition pour vous permettre d’être plus flexible dans la gestion de vos ventes</div>
    </div>

	<div class="form-group">
		<div class="input_left">
			<select class="form-control" id="delivery_method" name="delivery_method[]" multiple data-placeholder="{l s='Delivery method' mod='mpsellerwiselogin'}">
				<option value="Colissimo">Colissimo</option>
				<option value="Transporteur express">Transporteur express</option>
				<option value="Point relais Mondial Relay">Point relais Mondial Relay</option>
				<option value="Livraison libre">Livraison libre</option>
			</select>
		</div>

		<select class="form-control input_right" id="delivery_delay" name="delivery_delay">
			<option value="">{l s='Delivery delay' mod='mpsellerwiselogin'}</option>
			<option value="3 jours">3 jours</option>
			<option value="7 jours">7 jours</option>
			<option value="1 semaine">1 semaine</option>
			<option value="2 semaines">2 semaines</option>
			<option value="3 semaines">3 semaines</option>
		</select>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<div class="input_left">
			<select class="form-control" id="shipping_days" name="shipping_days[]" multiple data-placeholder="{l s='Shipping days' mod='mpsellerwiselogin'}">
				<option value="Lundi">Lundi</option>
				<option value="Mardi">Mardi</option>
				<option value="Mercredi">Mercredi</option>
				<option value="Jeudi">Jeudi</option>
				<option value="Vendredi">Vendredi</option>
				<option value="Samedi">Samedi</option>
			</select>
		</div>

		<select class="form-control input_right" id="option_free_delivery" name="option_free_delivery">
			<option value="">{l s='Option free delivery' mod='mpsellerwiselogin'}</option>
			<option value="30">Livraison offerte à partir de 30€</option>
			<option value="50">Livraison offerte à partir de 50€</option>
			<option value="90">Livraison offerte à partir de 90€</option>
			<option value="0">Non offerte</option>
		</select>
		<div class="clearfix"></div>
	</div>

	<div class="form-group dm_summary">
		<h4>Vos conditions de livraison telles que présentées sur le site</h4>

		<div class="md_summary_text">
			<p class="title">Délais de livraison et jours expédition:</p>
			<p class="summary">Nos jours d'expédition sont les <span class="filled_text shipping_days_fill">xxxx</span>.
			Les commandes reçues le week-end seront expédiées le <span class="filled_text shipping_wkd_fill">xx</span> suivant. A cela s'ajoute le délai de livraison habituel.<br />
			Nous travaillons avec <span class="filled_text shipping_delivery_method_fill">xxxx</span> pour vous assurer le meilleur suivi possible.</p>

			<div class="frais_livraison">
				<p class="title">Frais de livraison</p>
				<p class="summary">Nous offrons les frais de livraison à partir de <span class="filled_text shipping_cost_fill">x</span> euros d'achat.</p>
			</div>

			<br>

			<p class="title">Remboursement et échange:</p>
			<p class="summary">Nous veillons à ce que tout soit parfait pour vous. Si d'aventure quelque chose nous échappait et que votre colis arrivait abîmé, n'hésitez pas à nous contacter dans le 3 jours pour que nous trouvions la meilleure solution ensemble.</p>
		</div>
	</div>

	<div class="form-group">
		<a id="submitDeliveryMethod" href="#" class="next_btn">{l s='Next' mod='mpsellerwiselogin'}</a>
	</div>
</div>
