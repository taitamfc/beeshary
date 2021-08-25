{*
* 2017-2018 PHPIST
*
*  @author    PHPIST <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PPHIST
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}

{block name='content'}
    <script src="https://maps.googleapis.com/maps/api/js?key={$MP_GEOLOCATION_API_KEY}&libraries=places&language={$language['iso_code']}&region={$country->iso_code}"></script>
    <div class="location_selector" style="margin-left: 0px; !important">
        <div class="container">
            <div class="mpstorelocator" id="search_widget"
                 data-search-controller-url="{$link->getModuleLink('marketplace', 'searchshops')}">
                <form method="get" action="{$link->getModuleLink('marketplace', 'searchshops')}">
                    <div class="col-sm-4 col-md-4">
                        <div class="form-group">
                            <label> <img src="{$urls.img_url}bee-activite-bl.svg">Thématiques</label>
                            <select id="pp_theme" name="pp_theme">
                                <option value="">Sélectionnez une thématique</option>
                                {foreach from=$cravings.rencontrer_un_artisan.list item=data key=id_cat}
                                    <option value="{$id_cat|intval}"{if isset($smarty.post.pp_theme) && $smarty.post.pp_theme == $id_cat} selected="selected"{/if}>{$data}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <div class="form-group">
                            <label><img src="{$urls.img_url}bee-lieu-bl.svg">Lieu</label>
                            <input type="text" id="pp_place"
                                   name="pp_place"{if isset($smarty.post.pp_place) && $smarty.post.pp_place} value="{$smarty.post.pp_place}"{/if} />
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <div class="form-group">
                            <label style="display:block; height: 21px;"></label>
                            <button type="submit" style="border:none !important; padding-left: 15px" class="btn btn-border"
                                    id="Rechercher">Rechercher
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="wrapper_store">
        <div class="height555" id="wrapper_content">
            <div style="width:100%;" id="wrapper_content_right">
                <div id="map-canvas"></div>
            </div>
        </div>
    </div>
    <div style="clear:both; background:transparent;" class="row community-event-wrap">
        <div class="trouvez">
            <div class="col-sm-12">
                <form action="#">
                    {****<div class="form-group col-md-3 hidden">
                        <select id="seller_city" class="form-control">
                            <option value="">Toutes les régions</option>
                                {foreach from=$sellers_cities item=city}
                                    <option value="{$city|ucfirst|escape:'html':'UTF-8'}">{$city|ucfirst|escape:'html':'UTF-8'}</option>
                                {/foreach}
                        </select>
                    </div>***}
                    <div class="form-group col-md-3 col-xs-12 col-sm-5">
                        {* <select id="seller_cat" class="form-control" style="margin-right: 15px;">
                            <option value="">Toutes les catégories</option>
                            {foreach from=$sellers_cats item=cat}
                                <option value="{$cat.id_category|intval}"{if isset($smarty.post.pp_theme) && $smarty.post.pp_theme == $cat.id_category} selected="selected"{/if}>{$cat.name|ucfirst|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select> *}
                    </div>
                </form>
            </div>
            <div class="tag col-sm-12 hidden">
                {if isset($sel_pp_place) && $sel_pp_place}
                    <span class="city_name_list"> <img
                                src="{$urls.img_url}tag.jpg"/> {$sel_pp_place|ucfirst|escape:'html':'UTF-8'} </span>
                {/if}
                {if isset($sel_pp_theme) && $sel_pp_theme}
                    <span> <img src="{$urls.img_url}tag.jpg"/> <b
                                class="cat_name_list">{$sel_pp_theme|ucfirst|escape:'html':'UTF-8'}</b> </span>
                {/if}
            </div>
            <div class="col-sm-12">
                <div class="craft-shop-list search_stores">
                    {if isset($store_locations) && $store_locations}
                        {foreach from=$store_locations item=store}
                            <div class="home_store col-lg-3 col-xs-12 col-md-5 col-sm-5" data-is-banner="true"
                                 data-shop-banner="{$store.shop_banner|escape:'html':'UTF-8'}">
                                <a href="{$store.store_det_url|escape:'html':'UTF-8'}">
                                    <div class="media craft-shop-media">
                                        <div class="media-left media-middle">
                                            <img class="img-circle" src="{$store.seller_image|escape:'html':'UTF-8'}"/>
                                        </div>
                                        <div class="media-body">
                                            <h3>{$store.shop_name|ucfirst|escape:'html':'UTF-8'}</h3>
                                            <h4>{$store.seller_firstname|ucfirst|escape:'html':'UTF-8'}
                                                , {$store.seller_job|ucfirst|escape:'html':'UTF-8'}</h4>
                                            <p>{$store.city|ucfirst|escape:'html':'UTF-8'} {if isset($store.post_code) && $store.post_code} ({$store.post_code|substr:0:2}){/if}</p>
                                        </div>
                                        <div class="media-corner"><img src="{$urls.img_url}media-corner.jpg" alt=""/>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        {/foreach}
                    {else}
                        <h2 class="text-center">Aucun résultat trouvé</h2>
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/block}


