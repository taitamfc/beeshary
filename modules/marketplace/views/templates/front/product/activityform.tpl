{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}

{block name='content'}
<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		{if isset($errors) && $errors|@count}
			<ul class="alert alert-danger">
			{foreach from=$errors item=error}
				<li>{$error|escape:'htmlall':'utf-8'}</li>
			{/foreach}
			</ul>
		{/if}
		<div class="wk-mp-right-column">
			<div class="wk_product_list" id="mpbooking_activity_crt">
				<form id="activityImagesCreationForm" class="form_fields seller_db" action="{$submit_url}" method="post" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16" >
					<input type="hidden" name="id_mp_product" value="{$id_mp_product|intval}" />
					<input type="hidden" name="id_seller" value="{$id_seller|intval}" />
					<input type="hidden" id="price" name="price" value="{if isset($smarty.post.price)}{$smarty.post.price}{else if isset($product_info)}{$product_info.price}{else}0.000000{/if}" data-action="input_excl" />
    				<input type="hidden" name="id_booking_product" {if isset($product_info.id_ps_product) && $product_info.id_ps_product} value="{$product_info.id_ps_product}"{/if} />
    				<input id="latitude" type="hidden" name="latitude"{if isset($booking_info) && $booking_info} value="{$booking_info.latitude}"{/if} />
				    <input id="longitude" type="hidden" name="longitude"{if isset($booking_info) && $booking_info} value="{$booking_info.longitude}"{/if} />

				    <div class="content_top center-block text-center">
					    <img src="{$urls.img_url}bee-activites-g4.svg" />
					    <div class="content_top_title">Mes activités</div>
						<div class="content_top_info"></div>
					</div>
					<div class="clearfix"></div>
					<div class="pp_display_errors_activity alert alert-danger" style="display: none;"></div>

					<div class="tabs">
						<ul class="nav nav-tabs">
							<li class="nav-item">
								<a class="nav-link active" href="#information" data-toggle="tab">
									<i class="material-icons">&#xE88E;</i>
									{l s='Information' mod='marketplace'}
								</a>
							</li>
						  {if isset($action) && $action == 'update'}
							<li class="nav-item">
								<a class="nav-link" href="#images" data-toggle="tab">
									<i class="material-icons">&#xE410;</i>
									{l s='Images' mod='marketplace'}
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#calendar-slots" data-toggle="tab">
									<i class="material-icons">&#xE916;</i>
									Calendrier des disponibilités
								</a>
							</li>
						  {/if}
						</ul>
						<div class="tab-content" id="tab-content">
							<div class="tab-pane fade in active" id="information">
								<div class="form-group">
						        	{if isset($mpb_categories) && $mpb_categories|@count}
		        						<select name="mpb_categories[]" id="mpb_categories" data-placeholder="Choisissiez une ou plusieurs catégories pour votre activité" multiple class="form-control cats_list">
		        							{foreach $mpb_categories as $_cat}
		        								<option value="{$_cat.id_category|intval}"{if isset($product_cats) && in_array($_cat.id_category ,$product_cats)} selected="selected"{/if}>{$_cat.name|escape:'htmlall':'utf_8'}</option>
		        							{/foreach}
		        						</select>
		        					{/if}
								</div>

								<div class="form-group">
									<input type="text" class="form-control" name="activity_name" id="activity_name" placeholder="Nom de l'activité" maxlength="100" required {if isset($smarty.post.activity_name)}value="{$smarty.post.activity_name|escape:'htmlall':'utf-8'}"{elseif isset($product_info.product_name) && $product_info.product_name} value="{$product_info.product_name.{$id_lang}}"{/if} />
								</div>

								<div class="form-group">
									<textarea class="form-control" name="activity_short_desc" id="activity_short_desc" placeholder="Votre activité en une phrase" maxlength="500" required>{if isset($smarty.post.activity_short_desc)}{$smarty.post.activity_short_desc|escape:'htmlall':'utf-8'}{elseif isset($product_info.short_description) && $product_info.short_description}{$product_info.short_description.{$id_lang}}{/if}</textarea>
								</div>

								<div class="form-group">
									<textarea class="form-control" name="activity_desc" id="activity_desc" placeholder="Description de l'activité" rows="4" required>{if isset($smarty.post.activity_desc)}{$smarty.post.activity_desc|escape:'htmlall':'utf-8'}{elseif isset($product_info.description) && $product_info.description} {$product_info.description.{$id_lang}}{/if}</textarea>
								</div>

								<div class="form-group">
									<input type="text" class="form-control" name="activity_addr" id="activity_addr" placeholder="Adresse de votre activité" maxlength="500" required {if isset($smarty.post.activity_addr)}value="{$smarty.post.activity_addr|escape:'htmlall':'utf-8'}"{elseif isset($booking_info.activity_addr) && $booking_info.activity_addr} value="{$booking_info.activity_addr}"{/if} />
								</div>
								<div class="form-group">
									<input type="text" class="form-control input_left" name="activity_city" id="activity_city" placeholder="Ville de votre activité" maxlength="80" required {if isset($smarty.post.activity_city)}value="{$smarty.post.activity_city|escape:'htmlall':'utf-8'}"{elseif isset($booking_info.activity_city) && $booking_info.activity_city} value="{$booking_info.activity_city}"{/if} />
									<input type="text" class="form-control input_right" name="activity_postcode" id="activity_postcode" placeholder="Code postal de votre activité" maxlength="6" required {if isset($smarty.post.activity_postcode)}value="{$smarty.post.activity_postcode|escape:'htmlall':'utf-8'}"{elseif isset($booking_info.activity_postcode) && $booking_info.activity_postcode} value="{$booking_info.activity_postcode}"{/if} />
									<div class="clearfix"></div>
								</div>

								<div class="form-group">
		    						<select name="activity_period" id="activity_period" class="form-control input_left" required>
		    							<option value="">Durée de l'activité</option>
		    							{foreach from=$activity_periods item=period}
		    								<option value="{$period}"{if $period == $booking_info.activity_period}  selected="selected"{/if}>{$period}</option>
		    							{/foreach}
		    						</select>
		    						<select name="activity_participants" id="activity_participants" class="form-control input_right" required>
		    							<option value="">Nombre max de participants</option>
		    							{foreach from=$activity_participants item=participant}
		    								<option value="{$participant}"{if $participant == $booking_info.quantity}  selected="selected"{/if}>{$participant}</option>
		    							{/foreach}
		    						</select>
		        					<div class="clearfix"></div>
								</div>

								<div class="form-group">
									<div class="input_left activity_curious">
			    						<select name="activity_curious[]" id="activity_curious" class="form-control" multiple data-placeholder="Curieux concernés">
			    							{foreach from=$activity_curious item=curious}
			    								<option value="{$curious}"{if isset($activity_curious_opts) && in_array($curious, $activity_curious_opts)} selected="selected"{/if}>{$curious}</option>
			    							{/foreach}
			    						</select>
									</div>
		    						<input type="text" class="form-control input_right" name="activity_material" id="activity_material" placeholder="Matériel à prévoir (optionnel)" maxlength="500" {if isset($smarty.post.activity_material)}value="{$smarty.post.activity_material|escape:'htmlall':'utf-8'}"{elseif isset($booking_info.activity_material) && $booking_info.activity_material} value="{$booking_info.activity_material}"{/if} />
		        					<div class="clearfix"></div>
								</div>

								<div class="form-group">
		    						<input type="text" class="form-control" name="activity_video" id="activity_video" placeholder="Votre lien de la video" maxlength="225" {if isset($smarty.post.activity_video)}value="{$smarty.post.activity_video|escape:'htmlall':'utf-8'}"{elseif isset($booking_info.video_link) && $booking_info.video_link} value="{$booking_info.video_link}"{/if} />
		    						    <small>example: https://www.youtube.com/embed/xxxxxxxx</small><br />
		    						    <small>example: https://player.vimeo.com/video/xxxxxxxx</small>
								</div>

								{if isset($action) && $action == 'add'}
								<div class="form-group terms_activity">
									<input type="checkbox" id="terms_activity" checked="checked" /> J'accepte les conditions générales d'utilisation et la politique de confidentialité
									<div class="clearfix"></div>
								</div>
								{else}
									<input class="hidden" type="checkbox" id="terms_activity" checked="checked" />
								{/if}
							</div>
						  {if isset($action) && $action == 'update'}
							<div class="tab-pane fade in" id="images">
								{include file='module:marketplace/views/templates/front/product/_partials/updateproduct-images.tpl' cropWidth=960 cropHeight=600 aspectRatio=1.6 }
							</div>

							<div class="tab-pane fade in" id="calendar-slots">
								<div class="tab-pane fade in" id="wk-booking_configuration">
									<div class="time_slots_prices_content col-sm-12">
							            <div class="single_date_range_slots_container row" date_range_slot_num="0">
							                <div  class="form-group table-responsive-row col-sm-12 booking_date_ranges">
							                    <table class="table">
							                    <thead>
							                        <tr>
							                            <th class="center">
							                                <span>DU{**l s='Date From' mod='mpbooking'**}</span>
							                            </th>
							                            <th class="center">
							                                <span>AU{**l s='Date To' mod='mpbooking'**}</span>
							                            </th>
							                        </tr>
							                    </thead>
							                    <tbody>
							                        <tr>
							                        <td class="center">
							                            <div class="input-group">
							                                <input autocomplete="off" class="form-control sloting_date_from" type="text" name="sloting_date_from[]" value="" readonly>
							                                <span class="input-group-addon">
							                                    <i class="material-icons">&#xE8A3;</i>
							                                </span>
							                            </div>
							                        </td>

							                        <td class="center">
							                            <div class="input-group">
							                                <input autocomplete="off" class="form-control sloting_date_to" type="text" name="sloting_date_to[]" value="" readonly>
							                                <span class="input-group-addon">
							                                    <i class="material-icons">&#xE8A3;</i>
							                                </span>
							                            </div>
							                        </td>
							                        </tr>
							                    </tbody>
							                    </table>
							                </div>
							                <div  class="form-group table-responsive-row col-sm-12 time_slots_prices_table_div">
							                    <table class="table time_slots_prices_table">
							                        <thead>
							                            <tr>
							                                <th class="center">
							                                    <span>DU{**l s='Slot Time From' mod='mpbooking'**}</span>
							                                </th>
							                                <th class="center">
							                                    <span>AU{**l s='Slot Time To' mod='mpbooking'**}</span>
							                                </th>
							                                <th class="center">
							                                    <span>{l s='Price (tax excl.)' mod='mpbooking'}</span>
							                                </th>
							                            </tr>
							                        </thead>
							                        <tbody>
							                            <tr>
							                                <td class="center">
							                                    <div class="input-group">
							                                    <input autocomplete="off" class="booking_time_from form-control" type="text" name="booking_time_from0[]" readonly>
							                                    <span class="input-group-addon">
							                                        <i class="material-icons">&#xE192;</i>
							                                    </span>
							                                    </div>
							                                </td>
							                                <td class="center">
							                                    <div class="input-group">
							                                    <input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to0[]" readonly>
							                                    <span class="input-group-addon">
							                                        <i class="material-icons">&#xE192;</i>
							                                    </span>
							                                    </div>
							                                </td>
							                                <td class="center">
							                                    <div class="input-group">
							                                    <input type="hidden" value="{$idBookingProductInformation}" name="idTable">
							                                    <input type="text" class="form-control" name="slot_range_price0[]" value="{$product_info.price|round:2}">
							                                    <span class="input-group-addon">{$defaultCurrencySign}</span>
							                                    </div>
							                                </td>
							                            </tr>
							                        </tbody>
							                    </table>
							                    <div class="form-group">
							                        <div class="col-lg-12">
							                            <button class="add_more_time_slot_price btn btn-primary" class="btn btn-default" type="button" data-size="s" data-style="expand-right">
							                                <i class="fa fa-calendar-o"></i> Ajouter un autre créneau
							                                {***l s='Add More Slots' mod='mpbooking'**}
							                            </button>
							                        </div>
							                    </div>
							                </div>
							            </div>
							        </div>
							        <div class="clearfix"></div>
								</div>

								<div class="form-group">
							        <div class="col-lg-12">
							            <button class="btn btn-primary pull-right" id="add_more_date_ranges" class="btn btn-default" type="button" data-size="s" data-style="expand-right">
							                <i class="fa fa-calendar-o"></i> Ajouter une autre date 
							                {**l s='Add More Date Ranges' mod='mpbooking'**}
							            </button>
							        </div>
							    </div>
							    <div class="clearfix"></div>
							</div>
						  {/if}
							<div class="form-group" style="text-align:center;">
								<button type="submit" id="SubmitCreate" class="btn btn-yellow form-control-submitm">
									<span>Valider</span>
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	{***block name='image_upload'}
		{include file='module:marketplace/views/templates/front/_partials/uploadimage_popup.tpl'}
	{/block****}
</div>
{if isset($MP_GEOLOCATION_API_KEY) && $MP_GEOLOCATION_API_KEY}
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={$MP_GEOLOCATION_API_KEY}&libraries=places&language=fr&region=FR"></script>
{/if}
{/block}