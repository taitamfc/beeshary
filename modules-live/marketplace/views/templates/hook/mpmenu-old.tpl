{*
* 2010-2017 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="menu_item">
<div class="wk_shop_left_bar">
	<div class="wk_profile_img">
		<a class="mp-img-preview" href="/modules/marketplace/views/img/shop_img/VvBp4H.jpg">
			<img class="wk_left_img" src="/modules/marketplace/views/img/shop_img/VvBp4H.jpg?time=1502351580" alt="Image">
		</a>
	</div>
	<h4>{$mp_seller_info.seller_firstname},{$mp_seller_info.seller_lastname}</h4>
	<p><img src="/themes/beeshary/assets/img/bee-couronne-g3.svg" alt="image"> Membre BeeShary depuis <br>le {$mp_seller_info.date_add}</p>
	<div class="wk_profile_img_belowlink">
		<a href="#wk_question_form" class="wk_anchor_links open-question-form" data-toggle="modal" data-target="#myModal" title="Contacter le Vendeur ">
			<div class="wk_profile_left_display">
				<span>
					<i class="material-icons"></i> Contacter le Vendeur
				</span>
			</div>
		</a>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" style="text-align:left;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myModalLabel">Écrire votre question</h4>
			</div>
			<form id="wk_contact_seller-form" method="post" action="#">
				<div class="modal-body">
					<div class="form-group">
						<label class="control-label required">Email	</label>
						<input type="text" name="customer_email" id="customer_email" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label required">Objet </label>
						<input type="text" name="query_subject" class="form-control" id="query_subject">
					</div>
					<div class="form-group">
						<label class="control-label required">Description	</label>
						<textarea name="query_description" class="form-control" id="query_description" style="height:100px;"></textarea>
					</div>
					<input type="hidden" name="id_seller" value="1">
					<input type="hidden" name="id_customer" value="18">
					<input type="hidden" name="id_product" value="0">

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
</div>

<div class="modal fade" id="mp_image_preview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-body">
		  	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Fermer</span></button>
		    <img src="" class="mp-image-popup" style="width: 100%;">
		  </div>
		</div>
	</div>
</div>

</div>
	{if $is_seller == 1}
		<div class="list_content">
			<ul>

				<li {if $logic == 1}class="menu_active"{/if}>
					<span>
						<a href="{if isset($dashboard_link)}{$dashboard_link}{else}{$link->getModuleLink('marketplace', 'dashboard')|addslashes}{/if}">
							<i class="material-icons">&#xE871;</i>
							{l s='Dashboard' mod='marketplace'}
						</a>
					</span>
				</li>
				<li class="seller_menu{if $logic == 2} menu_active{/if}">
					<span>
						<a class="seller_menu_main" href="javascript:void(0);">
							<i class="material-icons">person</i> Mon profil
							<i class="fa fa-arrow-right pull-right"></i>
							<i class="fa fa-arrow-down pull-right hidden"></i>
							<div class="clearfix"></div>
						</a>
						<ul class="submenu">
							<li>
								<a href="{if isset($edit_profile_link)}{$edit_profile_link}{else}{$link->getModuleLink('marketplace', 'editprofile')|addslashes}{/if}">{l s='Edit Profile' mod='marketplace'}</a>
							</li>
						</ul>
					</span>
				</li>
				<li class="seller_menu{if $logic == 2} menu_active{/if}">
					<span>
						<a class="seller_menu_main" href="javascript:void(0);">
							<i class="material-icons">&#xE8D1;</i> Ma boutique
							<i class="fa fa-arrow-right pull-right"></i>
							<i class="fa fa-arrow-down pull-right hidden"></i>
							<div class="clearfix"></div>
						</a>
						<ul class="submenu">
							<li>
								<a href="{if isset($shop_link)}{$shop_link}{else}{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $name_shop])|addslashes}{/if}">Voir ma boutique</a>
							</li>
						</ul>
					</span>
				</li>
				<li>
					<span>
						<a href="{if isset($seller_profile_link)}{$seller_profile_link}{else}{$link->getModuleLink('marketplace', 'sellerprofile', ['mp_shop_name' => $name_shop])|addslashes}{/if}">
							<i class="material-icons">&#xE851;</i>
							{l s='Seller Profile' mod='marketplace'}
						</a>
					</span>
				</li>
				<li>
					<span>
						<a href="{if isset($shop_link)}{$shop_link}{else}{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $name_shop])|addslashes}{/if}">
							<i class="material-icons">&#xE8D1;</i>
							{l s='Shop' mod='marketplace'}
						</a>
					</span>
				</li>
				<li {if $logic == 3}class="menu_active"{/if}>
					<span>
						<a href="{if isset($product_list_link)}{$product_list_link}{else}{$link->getModuleLink('marketplace', 'productlist')|addslashes}{/if}">
							<i class="material-icons">&#xE149;</i>
							{l s='Product' mod='marketplace'}
							<span class="wkbadge-primary" style="float:right;">{$totalSellerProducts}</span>
							<div class="clearfix"></div>
						</a>
					</span>
				</li>
				<li {if $logic == 4}class="menu_active"{/if}>
					<span>
						<a href="{if isset($my_order_link)}{$my_order_link}{else}{$link->getModuleLink('marketplace', 'mporder')|addslashes}{/if}">
							<i class="material-icons">&#xE8F6;</i>
							{l s='Orders & Transaction' mod='marketplace'}
						</a>
					</span>
				</li>
				<li {if $logic == 5}class="menu_active"{/if}>
					<span>
						<a href="{if isset($payment_detail_link)}{$payment_detail_link}{else}{$link->getModuleLink('marketplace', 'mppayment')|addslashes}{/if}">
							<i class="material-icons">&#xE8A1;</i>
							{l s='Payment Detail' mod='marketplace'}
						</a>
					</span>
				</li>
				{hook h="displayMPMenuBottom"}
			</ul>
		</div>
	{/if}
</div>
