{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
{extends file=$layout}
{block name='content'}
</div></div></div>
<div id='mp_store_locator'>
	{* <div class="wk-store-heading">{l s='Store Locator' mod='mpstorelocator'}</div> *}
	<div id="wrapper_store" class="">
		<div id="wrapper_header" class="col-md-12">
			<div id="wrapper_header_left">
			</div>
			<div id="wrapper_header_right" class="row">
				{if isset($enableSearchProduct) && $enableSearchProduct == 1}
					<div id="search_products" class="col-md-6 form-group">
						<div>{l s='Product' mod='mpstorelocator'}</div>
						<div>
						<input type="text" class="form-control" placeholder="{l s='Product Name' mod='mpstorelocator'}" id="select_search_products" {if isset($active_product_id)} id_product="{$active_product_id|escape:'htmlall':'UTF-8'}" {else} id_product="0" {/if} value="{if isset($selectedProductName)}{$selectedProductName|escape:'htmlall':'UTF-8'}{/if}"/>
						<div id="products_ul"></div>
						<div id="divnoproduct"></div>
						<div id="divproductname"></div>
						</div>
					</div>
				{else}
					<input type="hidden" id="select_search_products" id_product="{if isset($active_product_id)}{$active_product_id|escape:'htmlall':'UTF-8'}{/if}"/>
				{/if}
				{* <div id="search_city_field" class="col-md-6">
					
					<div>{l s='Location Search' mod='mpstorelocator'}</div>
					<div>
					<input id="search_city" class="form-control" type="text" placeholder="{l s='Search Location' mod='mpstorelocator'}" />
					
					<img src="{$psModuleDir|escape:'htmlall':'UTF-8'}mpstorelocator/views/img/spinner.gif" id="wk_sl_loader" style="display: none;">
					</div>
				</div> *}
				<div id="search_thematiques" class="col-md-3">
					<div class="header_title_label">{*<img src="/themes/beeshary_child/assets/img/bee-activite-bl.svg"> *} <i class="fas fa-hiking"></i> {l s='Savoir-faire' mod='mpstorelocator'}</div>
					<div>
						<select id="pp_theme" name="pp_theme" class="form-control">
							<option value="">Savoir-faire</option>
							<option value="212">Agriculture, production et élevage</option>
							<option value="211">Apiculture et produits de la ruche</option>
							<option value="210">Art & Peinture</option>
							<option value="209">Artisanat Ecologique & recyclage</option>
							<option value="208">Boulangerie, Patisserie et Biscuiterie</option>
							<option value="207">Chocolat & Confiserie</option>
							<option value="206">Confitures & Gourmandises sucrées</option>
							<option value="205">Cosmétiques & produits de bien-être</option>
							<option value="204">Création de bijoux</option>
							<option value="203">Epicerie salée et conserverie</option>
							<option value="202">Gastronomie et Métiers de bouche</option>
							<option value="201">Insolite</option>
							<option value="200">Mobilier & décoration</option>
							<option value="199">Nature & botanique</option>
							<option value="198">Papier & calligraphie</option>
							<option value="197">Pierre & Marbre</option>
							<option value="196">Poterie & Céramique</option>
							<option value="195">Savonnerie & parfumerie</option>
							<option value="194">Textile, Mode et accessoires</option>
							<option value="193">Travail du Bois</option>
							<option value="192">Travail du Cuir</option>
							<option value="191">Travail du Métal</option>
							<option value="190">Travail du Verre</option>
							<option value="189">Vignoble, Brasserie & distillerie</option>
						</select>
		
					</div>
				</div>
				<div id="search_label" class="col-md-2">
					<div class="header_title_label"> <i class="fas fa-tag" aria-hidden="true"></i> Labels</div>
					<div>
						<select id="pp_badge" name="pp_badge" class="form-control">
							<option value="">Label</option>
							{foreach from=$pp_badges item=pp_badge}
								<option value="{$pp_badge.id}">{$pp_badge.badge_name}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div id="search_city_block" class="col-md-2">
					<div class="header_title_label">{*<img src="/themes/beeshary_child/assets/img/bee-lieu-bl.svg">*} <i class="fas fa-map-marked-alt"></i> {l s='Lieu' mod='mpstorelocator'}</div>
					<div>
						<input id="wk_current_location" class="form-control" type="text" value="" placeholder="{l s='Entrer un lieu ou une adresse' mod='mpstorelocator'}">
					</div>
				</div>
				<div id="search_city_block" class="col-md-2">
					<div class="header_title_label">{*<i class="fa fa-compass white" aria-hidden="true"></i>*} <i class="fas fa-compass"></i> {l s='Rayon' mod='mpstorelocator'}</div>
					<div>
						<input id="wk_store_radius" class="form-control" type="number" value="10" placeholder="{l s='Rayon en Km' mod='mpstorelocator'}">
					</div>
				</div>
				<div class="col-md-3 mt-10">
					<button class="btn btn-primary wkstore-btn" id="wk_store_search">
					<span>{l s='Rechercher' mod='mpstorelocator'}</span>
					</button>
					<button class="btn btn-primary wkstore-btn" id="wk_store_reset">
					<span>{l s='Reset' mod='mpstorelocator'}</span>
					</button>
				</div>
			</div>
		</div><div class="" id="">
                
          <div class="row">
            

            
	<div id="content-wrapper" class="col-lg-12 col-xs-12">
		<div id="wrapper_content ">
			<div id="wrapper_content_right" class="">
				<div id="map-canvas" class="col-md-12"></div>
			</div>
			<div id="wrapper_content_left" class="container-large">
				{if isset($store_locations)}
					{include file="module:mpstorelocator/views/templates/front/stores_detail.tpl" storeLocations=$store_locations}
				{/if}
				
				<div class="load-more">
					<a class="btn btn-primary f-load-more-btn" href="javascript:;">Voir plus</a>
				</div>
					
			</div>
		</div>
	</div>
</div>

	<script>
		const list = document.querySelector(".wk_store_details");
		const listItems = list.querySelectorAll(".wk_store");
		const ajaxLoadMoreBtn = document.querySelector(".f-load-more-btn");
		
		// document.querySelectorAll('.wk_store').forEach((elem) => (elem.style.display = "none"));
		
		// let k = 0;
		// let j = 12;
		
		// var range = '.wk_store:nth-child(n+'+k+'):nth-child(-n+'+j+')';
		// list.querySelectorAll(range).forEach((elem) => (elem.style.display = "block"));
		
		var page = 2;
	
		ajaxLoadMoreBtn.addEventListener("click", function () {
			
			ajaxLoadMoreBtn.text = 'Loading...';
			
			var xhttp = new XMLHttpRequest();
			xhttp.open("GET", "https://beeshary.com/module/mpstorelocator/storedetails?stores=1&is_ajax=1&p="+page, true);
			xhttp.send();
			xhttp.onreadystatechange = function() {
				if ( this.readyState == 4 && this.status == 200) {
				   let responsive = JSON.parse(this.responseText);
				   page = page + 1;
				   list.insertAdjacentHTML('beforeend', responsive.html);
				   
				   ajaxLoadMoreBtn.text = 'Voir plus';
				   
				   if( responsive.p >= responsive.total_page ){
					   ajaxLoadMoreBtn.remove();
				   }
				}
			};
		});

	</script>

{/block}