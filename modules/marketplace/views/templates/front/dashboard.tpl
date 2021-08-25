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

{extends file=$layout}
{block name='content'}
	{if $logged}
		<div class="wk-mp-block">
			{hook h="displayMpMenu"}
			<!-- {hook h="displayMPMyAccountMenu"} -->
			<div class="col-xs-12 col-sm-9">
			    <div class="wk-mp-content dashboard-wrap seller-dashboard">
			        <div class="row">
			            <div class="col-sm-12 section-heading"><h2>Bienvenue sur votre journal de bord {$mp_seller_info.seller_firstname|ucfirst} !</h2></div>
			          <!--{* <div class="col-sm-4">
			                <div class="dashboard-content bg-white">
			                    <div class="dashboard-icon"><img src="{$urls.img_url}bee-avis-j5.svg" alt="" /></div>
			                    <div class="dashboard-content-inner">
			                        <h5>1 nouvel avis sur votre boutique</h5>
			                        <div class="avis_ex">
			                        	<span class="avis_db_lft">Paul a ecrit:</span>
			                        	<span class="avis_db_rgt">"{$mp_seller_info.seller_firstname|ucfirst} est artisan passionne, sa boutique est top..."</span>
			                        </div>
			                        <!--div class="avis_others">Voir l'avis</div-->
			                    </div>
			                </div>
			            </div><!--col-sm-4-->
			            <div class="col-sm-4">
			                <div class="dashboard-content fav_stats">
			                    <div class="dashboard-icon"><img src="{$urls.img_url}bee-fav-stat.svg" alt="" /><span>6</span></div>
			                    <div class="dashboard-content-inner stats">
			                        <p class="stat_title">Votre boutique compte 6 admirateurs de plus ce mois-ci.</p>
			                        <!--a class="see_more" href="javascript:void(0);">Voir mes statistiques</a-->
			                    </div>
			                </div>
			            </div><!--col-sm-4-->
			            <div class="col-sm-4">
			                <div class="dashboard-content progress_stat">
			                    <div class="dashboard-icon"><img src="{$urls.img_url}bee-progress-stat.svg" alt="" /><div>2.8%</div></div>
			                    <div class="dashboard-content-inner stats">
			                        <p class="stat_title">Vos ventes ont progresse de 2.8% en un mois.</p>
			                        <!--a class="see_more" href="javascript:void(0);">Voir mes statistiques</a-->
			                    </div>
			                </div>
			            </div><!--col-sm-4-->*}-->
			            <div class="clearfix"></div>
			            {**<!--div class="col-sm-8">
			                <div class="dashboard-content bg-white">
			                    <div class="dashboard-icon"><img src="/themes/beeshary_child/assets/img/bee-send-j5.png" alt="" /></div>
			                    <div class="dashboard-content-inner">
			                        <h5>Vous avez 2 nouveaux messages.</h5>
			                        <p>Paul a écrit le 8/03/2017 à 8h32 :<span>‘‘Bonjour John, Lorem lorem lorem mmos dis eat...’’</span><br/>
			                            Marie a écrit le 7/03/2017 à 10h05.<span>‘‘Bonjour, Lorem lorem lorem mmos dis...’’</span></p>
			                        <a href="javascript:void(0);">Voir les nouveaux messages</a>
			                    </div>
			                </div>
			            </div>
			            <div class="col-sm-4">
			                <div class="dashboard-content dashboard-content-center">
			                    <div class="dashboard-icon"><img src="/themes/beeshary_child/assets/img/bee-id-shop.svg" alt="" /></div>
			                    <div class="dashboard-content-inner">
			                        <p>Une idée cadeau pour <br/>la fête des mères ?</p>
			                        <a href="javascript:void(0);">Trouver un article sur BeeShary</a>
			                    </div>
			                </div>
			            </div--><!--col-sm-4-->**}
			            <div class="col-sm-12">
			                <div class="row">
			                    <div class="col-sm-12">
			                        <div class="bg-white clearfix mb20">
			                            <div class="col-sm-4 p0">
			                                <div class="dashboard-content">
		                                        {if isset($latest_news) && latest_news}
		                                        	{$latest_news nofilter}
		                                        {/if}


																						<!-- <div class="alert alert-warning" style="font-size: 1.2em;">
																							<div  style="text-align:center;">
																								<img src="{$urls.img_url}info.png" atl="info" style="width:15%;">
																								<span style="font-weight: bold;">Attention</span>
																								<img src="{$urls.img_url}info.png" atl="info" style="width:15%;"><br><br>
																							</div>
																							<div style="text-align:justify;">
																								<span >Pensez à enregistrer vos informations de paiement pour pouvoir vendre vos produits.<br><br>
																								Dans le menu de gauche, cliquez sur <span style="font-weight: bold;">"Gérer mes paiement"</span>
																								puis <span style="font-weight: bold;">"choisir mon pays"</span></span>
																							</div>
																						</div> -->
			                                </div>
			                            </div>
			                            <div class="col-sm-8 p0">
			                            	{if isset($beeshary_video) && beeshary_video}
			                                	<iframe width="100%" height="352" src="{$beeshary_video}?rel=0&controls=0&showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
			                                {/if}
			                            </div>
			                        </div>
			                    </div>
			                </div>
			            </div><!--col-sm-12-->

									<div class="col-sm-4">
										<div class="alert alert-warning" style="font-size: 1em;">
											<div  style="text-align:center;">
												<span class="fa fa-warning"> </span>
												<span style="font-weight: bold; font-size: 1.3em;"> Attention </span>
												<span class="fa fa-warning"> </span>
											</div>
											<div style="text-align:justify;">
												<span >Pensez à enregistrer vos informations de paiement pour pouvoir vendre vos produits.<br><br>
												Dans le menu de gauche, cliquez sur <span style="font-weight: bold;">"Gérer mes paiement"</span>
												puis <span style="font-weight: bold;">"choisir mon pays"</span></span>
											</div>
										</div>
									</div>

			            <div class="col-sm-4">
			                <div class="dashboard-content bg-white passion_db_btm">
			                    <div class="dashboard-icon">
														<img src="{$urls.img_url}bee-activites-b2.svg" alt="" style="float: left;"/>
														<span class="passionDashboard">Transmettez votre passion!</span>
													</div>
			                    <div class="dashboard-content-inner">
			                        <!-- <h5>Transmettez votre passion!</h5> -->
			                        <div class="passion_db">
			                        	Proposez une activite pour rencontrer des voyageurs du monde entier et partage votre savoir-faire.
			                        </div>
			                        <a class="passion_db_link" href="{$link->getModuleLink('mpbooking', 'mpcreateactivity')}">Proposez une activite</a>
			                    </div>
			                </div>
			            </div>
			            <div class="col-sm-4">
			                <div class="dashboard-content question_db_btm">
			                    <div class="dashboard-icon col-md-3"><div class="question_mark"></div></div>
			                    <div class="dashboard-content-inner col-md-9">
			                        <h5>Des questions?</h5>
			                        <div class="passion_db">
			                        	Beeshary vous accompagne dans la creation et la gestion de votre boutique, n'hesitez pas a nous contacter.
			                        </div>
			                        <a class="question_db_link" href="{$link->getPageLink('contact')}">Contacter Beeshary</a>
			                    </div>
			                </div>
			            </div>
			            <div class="clearfix"></div>
			        </div>
			    </div>
			</div>
		</div>
	{/if}
{/block}
