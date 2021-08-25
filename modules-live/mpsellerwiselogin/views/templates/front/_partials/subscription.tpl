{**
 * 2017-2018 PHPIST.
 *
 *  @author    Yassine belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 *}
{if !$partner }
<div id="sellerSubscriptionForm" style="display:none;">
	<div class="prv_section">
		<a class="prv_subscription" href="javascript:void(0);">
			<img src="{$smarty.const._THEME_IMG_DIR_}bee-fleche.svg" />
		</a>
	</div>
	<!-- Contenedor -->
	<div class="pricing-wrapper clearfix">
		<input type="hidden" id="selectedplan" name="plan_id" value="">
		{foreach from=$stripeplans item=plan key=k name="stripeplans"}
		
			<div onclick="selectplan('{$plan['id']}','{$plan['amount']}');" class="pricing-table {if $k == 0} recommended{/if}">
				{if $plan['interval'] == 'year'}
				<h3 class="pricing-title">Annuel</h3>
				{/if}
				{if $plan['interval'] == 'month'}
				<h3 class="pricing-title">Mensuel</h3>
				{/if}
				<div class="price">€{($plan['amount']/100)|string_format:"%.2f"}<sup>/ 
				
				 
				{if $plan['interval'] == 'year'}
					An
				{/if}
				{if $plan['interval'] == 'month'}
					Mois
				{/if}
				
				</sup></div>
				<ul class="table-list">
					<li>Accès à la e-boutique pour vendre vos produits et vos ateliers</span></li>
					<li>Référencement dans le catalogue << Made In Terroir>> destiné aux comités d'entreprises</li>
					<li>Promotion de votre savoir-faire sur les réseaux sociaux et la presse</li>
					<li>Mise en avant et localisation de vos ateliers et activités auprès des voyageurs grace à notre application mobile</li>
					
				</ul>
				<div class="table-buy ">
					<i class="fa fa-check plancheck" {if $k != 0}style="display:none;"{/if} id="{$plan['id']}" aria-hidden="true"></i>
				</div>
			</div>
			
		{/foreach}
	  
		

	</div> 
	<div class="form-group">
		<script id="stripe-script"
			src="https://checkout.stripe.com/checkout.js" class="pricing-action stripe-button"
			data-key={$pkkey}
			data-image={$stripelogo}
			data-name="Bienvenue"
			data-panel-label="Payez maintenant"
			data-amount=""
			data-locale="fr"
			data-currency="EUR"
			data-label="S'inscrire">
		</script>
	</div>
	 
</div>
<script>
setTimeout(function(){ $("#stripe-script").attr('data-amount', 11000); }, 3000);
function selectplan(selectedplanid,amount){
	$("#stripe-script").attr('data-amount', amount);
	$(".plancheck").hide();
	//alert(selectedplanid);
	$("#selectedplan").val(selectedplanid);
	$("#"+selectedplanid).show();
}
</script>
{/if}