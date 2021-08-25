{*
*  2017-2018 PHPIST
*
*  @author    Yassine Belkais <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PHPIST
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}

{block name='content'}
<div class="row">
    <div class="container">
        <div class="col-sm-12">
            <div class="media boutique-media seller_page">
                <div class="media-left text-center">
                    <div class="user-image">
                        <div class="wk_profile_left_bar" {if !isset($seller_banner_path)}style="top:0px;"{/if}>
                			<div class="wk_profile_img">
                                <a {if isset($seller_img_exist)}class="mp-img-preview" href="{$seller_img_path}"{/if}>
                                    <img class="wk_left_img" src="{$seller_img_path}?time={$timestamp}" alt="{l s='Image' mod='marketplace'}" />
                                </a>
                            </div>
                            <p class="text-center">
                                <img src="{$urls.base_url}themes/beeshary/assets/img/bee-couronne-g3.svg" alt="image" /> 
                                <br />
                                Super-abeille
                                <br />
                                Membre depuis le {$mp_seller.date_add|date_format:"%d.%m.%Y"}
                            </p>
                            <p class="text-center">
                                <img src="{$urls.base_url}themes/beeshary/assets/img/bee-speak-g3.svg" alt="image" /> 
                                <br />
                                Je parle 
                                <br />
                                {if isset($mp_seller.langs) && $mp_seller.langs}
                                    {$mp_seller.langs}
                                {else}
                                    {$mp_seller.seller_lang}
                                {/if}
                            </p>
                		</div>
                    </div>
                </div>
                <div class="media-body">
                    <h2>{$mp_seller.seller_firstname|ucfirst}, {$mp_seller.seller_job}</h2>
                    <h4>{$mp_seller.address|ucfirst}{if $mp_seller.post_code} ({$mp_seller.post_code|substr:0:2}){/if}, {$mp_seller.country|ucfirst}</h4>
                    <div class="seller_shop_desc">{$mp_seller.seller_identity|strip_tags|truncate:920 nofilter}</div>
                    <a href="#wk_question_form" data-toggle="modal" data-target="#contactSeller" title="Contacter le Vendeur " class="btn btn-yellow"><img src="{$urls.img_url}bee-send-g4.svg" alt="" /> Contactez-moi</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row craftsman-week-wrap seller_page_middle">
    <div id="custom-text">
        <ul class="clearfix craftsman-week-list" style="height: 490px;">
            <li>
                <div class="craftsman-week">
                    <h2><span style="vertical-align: inherit;"><span style="vertical-align: inherit;"> Ce qui me plaît dans mon métier</span></span></h2>
                    <div class="media craftman-media">
                        <div class="media-body">
                            <h3><span style="vertical-align: inherit;"><span style="vertical-align: inherit;">Témoignage {if $mp_customer->id_gender == 1}d'un passioné{else}d'une passionée{/if}</span></span></h3>
                            <p><span style="vertical-align: inherit;">
                                {$mp_seller.seller_passion|escape:'html':'utf-8'|truncate:1500}
                            </span></p>
                        </div>
                    </div>

                    <div class="button-list clearfix">
                        <ul>
                            <li><a class="btn btn-yellow meet_seller" href="{$link->getModuleLink('marketplace', 'sellerprofile', ['mp_shop_name' => $mp_seller.link_rewrite])}"><img src="{$urls.base_url}themes/beeshary_child/assets/img/bee-rencontrez-bl.svg" alt="" /><span style="vertical-align: inherit;"> Rencontrez l'artisan</span></a></li>
                        </ul>
                    </div>
                </div>
            </li>
            <li>
                {if isset($shop_banner_path) && $shop_banner_path}
                    <img class="img-responsive" src="{$shop_banner_path|escape:'html':'utf-8'}" style="width: 100%;max-height: 490px;" />
                {else}
                    <iframe width="100%" height="518" src="https://www.youtube.com/embed/sjc_Hn7OFH8?controls=1&amp;mute=1&amp;showinfo=0&amp;rel=0&amp;autoplay=1&amp;loop=1&amp;playlist=uNRGWVJ10gQ" frameborder="0"></iframe>
                {/if}
            </li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="container">
        <div class="col-sm-12 seller_activity">
            {if isset($activities) && $activities}
                <h2>Les activités que je vous propose</h2>

              {foreach from=$activities item=activity key=k}
                <div class="col-md-6 row_{$k}">
                    <div class="activity_box">
                        <div class="activity_image" style="background:url({if isset($activity.activity_image) && $activity.activity_image}{$urls.base_url}modules/{$activity.activity_image|escape:'html':'utf-8'}{else}{$urls.img_url}banner-bg.jpg{/if}) no-repeat;">
                        </div>
                        <div class="seller_act_left_side">
                            <div class="seller_act_img">
                                <img src="{$seller_img_path|escape:'html':'utf-8'}" />
                            </div>
                            <div class="lef_btm_info">
                                <span class="seller_name">{$mp_seller.seller_firstname|ucfirst}, {$mp_seller.seller_job}</span>
                                <span class="seller_addr">{$mp_seller.address|ucfirst}{if $mp_seller.post_code} ({$mp_seller.post_code|substr:0:2}){/if}</span>
                            </div>
                        </div>
                        <div class="seller_act_right_side">
                            <div class="seller_act_title">{$activity.product_name|truncate:35|escape:'html':'utf-8'}</div>
                            <div class="seller_act_desc">
                                {if isset($activity.short_description) && $activity.short_description}
                                    {$activity.short_description|truncate:148 nofilter}
                                {else}
                                    {$activity.description|truncate:148 nofilter}
                                {/if}
                            </div>
                            <a href="{$activity.activity_url|escape:'html':'utf-8'}" class="btn btn-yellow join_act_link">
                                Je participe !
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
              {/foreach}
            {/if}
        </div>
        <div class="clearfix"></div>
        <div class="proverb_wrapper">
            <img class="center-block" src="{$urls.img_url}bee-quote-g2.svg" />
            <div class="proverb_text center-block">
            {if isset($mp_seller.seller_proverb) && trim($mp_seller.seller_proverb)}
                {$mp_seller.seller_proverb|escape:'html':'utf-8'}
            {else}
                Don't worry, Bee Shary
            {/if}
            </div>
            <img class="center-block" src="{$urls.img_url}bee-illu-artisan-content-g4.png" />
        </div>
        <div class="clearfix"></div>
        <div class="my_store_online">
            <h2>Ma boutique en ligne</h2>
            <ul class="img_list">
            {foreach from=$mp_products item=mp_product}
                <li class="col-md-2">
                    <a href="{$link->getProductLink($mp_product.id_ps_product|intval)}">
                        <img class="mp_img" src="{$mp_product.mp_img_path}" />
                        <div class="product_title">{$mp_product.name|ucfirst|truncate:20}</div>
                        <div class="product_price">{$mp_product.price}</div>
                    </a>
                </li>
            {/foreach}
            <li class="col-md-2 store_block">
                <a href="{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $mp_seller.link_rewrite])}">
                    <img class="mp_img" src="{$urls.img_url}banner-bg.jpg" />
                    <div class="block_overlap">
                        <img src="{$urls.img_url}bee-shop-bl.svg" />
                        <div class="visit_store">Visitez la boutique</div>
                    </div>
                </a>
            </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="contactSeller" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="text-align:left;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Écrire votre question</h4>
            </div>
            <form id="wk_contact_seller-form" method="post" action="#">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label required">Email </label>
                        <input type="text" name="customer_email" id="customer_email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label required">Objet </label>
                        <input type="text" name="query_subject" class="form-control" id="query_subject">
                    </div>
                    <div class="form-group">
                        <label class="control-label required">Description   </label>
                        <textarea name="query_description" class="form-control" id="query_description" style="height:100px;"></textarea>
                    </div>
                    <input type="hidden" name="id_seller" value="{$mp_seller.id_seller|intval}" />
                    <input type="hidden" name="id_customer" value="{$mp_seller.seller_customer_id|intval}" />
                    <input type="hidden" name="id_product" value="0" />

                    <div class="form-group">
                        <div class="contact_seller_message"></div>
                    </div>

                    <label class="wk_formfield_required_notify">
                        Les champs qui sont marqué  (<span class="required">*</span>)  sont obligatoires à remplir par vous.
                    </label>
                </div>

                <div class="modal-footer">
                    <div class="form-group row">
                        <div class="col-xs-6 col-sm-6 col-md-6" style="text-align:left">
                            <button type="button" class="btn wk_btn_cancel wk_btn_extra" data-dismiss="modal">
                                Annuler 
                            </button>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 wk_text_right">
                            <button type="submit" class="btn btn-success wk_btn_extra" id="wk_contact_seller" name="wk_contact_seller">
                                Envoyer 
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}