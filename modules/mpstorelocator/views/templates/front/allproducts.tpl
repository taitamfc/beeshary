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
    <div class="displayCovid">
        <div class="textContainer">
            En raison de la crise sanitaire actuelle, les activités artisanales sont suspendues jusqu’à la levée du confinement.
            <br/>
            Nous avons hâte de vous retrouver très prochainement.
            <br/>
            Prenez soin de vous et de vos proches.


        </div>
    </div>
    <div id="wrapper_header" class="col-md-12">
        <div id="wrapper_header_left">
        </div>
        <div id="wrapper_header_right" class="row">


            <div id="search_thematiques" class="col-md-2">
                <div class="header_title_label">{*<img src="/themes/beeshary_child/assets/img/bee-activite-bl.svg">*}<i class="fas fa-hiking"></i> {l s='Activité' mod='mpstorelocator'}</div>
                <div>
						<!-- PAUL : fix Categories : #13 is parent CAT : Activities -->
						{assign var='Categories' value=Category::getChildren(13, Context::getContext()->language->id)}
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">Toutes les activités</option>
                        <!--<option value="105">À la rencontre d'un éleveur</option>
                        <option value="106">Découverte d'un producteur</option>
                        <option value="109">Gastronomie &amp; Métiers de bouche</option>
                        <option value="110">Vignoble, brasserie &amp; distillerie</option>
                        <option value="118">Modelage, sculpture &amp; céramique</option>
                        <option value="107">Travail du verre, du bois et de la pierre</option>
                        <option value="119">Peinture &amp; Calligraphie</option>
                        <option value="120">Maroquinerie, mode &amp; accessoires</option>
                        <option value="121">Bijoux &amp; Orfèvrerie</option>
                        <option value="111">Savonnerie &amp; parfumerie</option>
                        <option value="108">Histoire et Patrimoine</option>
                        <option value="115">Nature &amp; botanique</option>
                        <option value="112">En musique</option>
                        <option value="116">Insolite</option>
                        <option value="139">Nouvelle catégorie</option> -->

                        {foreach $Categories as $defaultCategoryVal}
                            <option value="{$defaultCategoryVal.id_category}" name="{$defaultCategoryVal.name}">{$defaultCategoryVal.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div id="search_city_block" class="col-md-2">
                <div class="header_title_label">{*<img src="/themes/beeshary_child/assets/img/bee-lieu-bl.svg">*}<i class="fas fa-map-marked-alt"></i> {l s='Lieu' mod='mpstorelocator'}</div>
                <div>
                    <input id="wk_current_location" class="form-control" type="text" value="" placeholder="{l s='Entrer un lieu ou une adresse' mod='mpstorelocator'}">
                </div>
            </div>
            <div id="search_city_block" class="col-md-2">
                <div class="header_title_label">{*<i class="fa fa-compass white" aria-hidden="true"></i>*} <i class="fas fa-compass"></i> {l s='Rayon' mod='mpstorelocator'}</div>
                <div>
                    <input id="wk_store_radius" class="form-control" type="number" value="" placeholder="{l s='Rayon en Km' mod='mpstorelocator'}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="header_title_label">{*<i class="fa fa-calendar white" aria-hidden="true"></i>*} <i class="fas fa-calendar-alt"></i> {l s='Dates de votre sẹour' mod='mpstorelocator'}</div>
                <div>
                    <input id="date_stay" class="form-control" type="date" value="" placeholder="{l s='Du' mod='mpstorelocator'}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="header_title_label">&nbsp;</div>
                <div>
                    <input id="date_end" class="form-control" type="date" value="" placeholder="{l s='Au' mod='mpstorelocator'}">
                </div>
            </div>
            <div class="col-md-2 mt-10">
                <button class="btn btn-primary wkstore-btn" id="wk_store_search">
                    <span>{l s='Rechercher' mod='mpstorelocator'}</span>
                </button>
            </div>
        </div>
    </div><div class="" id="">

    <div class="row">
        <div id="content-wrapper" class="col-lg-12 col-xs-12 all-activites">
            <div id="wrapper_content" class="row">
                <div id="wrapper_content_left" class="col-md-8">
                    <div class="block ApProductList">
                        <ul class="product_list grid row dropdown pl_activites_home">
                            {if isset($products)}
                                {include file="module:mpstorelocator/views/templates/front/product_detail.tpl" products=$products}
                            {/if}
                        </ul>
                    </div>
					
					<div class="load-more">
						<a class="btn btn-primary f-load-more-btn" href="javascript:;">Voir plus</a>
					</div>
                </div>
                <div class="col-md-4">
                    <div id="map-canvas" style="height: 500px;" class="col-md-12"></div>
                </div>
            </div>
        </div>
    </div>
	
	<script>
	
	const list 				= document.querySelector(".pl_activites_home");
	const listItems 		= list.querySelectorAll("li");
	const ajaxLoadMoreBtn 	= document.querySelector(".f-load-more-btn");	
	var page = 2;
	
	ajaxLoadMoreBtn.addEventListener("click", function () {
		
		ajaxLoadMoreBtn.text = 'Loading...';
		
		var xhttp = new XMLHttpRequest();
		xhttp.open("GET", "https://beeshary.com/module/mpstorelocator/allproducts?is_ajax=1&p="+page, true);
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