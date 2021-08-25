{**
* 2017-2018 PHPIST.
*
*  @author    Yassine belkaid <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PHPIST
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}

{block name='content'}
<div id="pp_seller_creation">
	<div class="container_custom clearfix">
		<div class="row">
			{if !$partner }
				<div class="top_header">
					<img class="mp_montgolfiere" src="{$urls.base_url}themes/beeshary_child/assets/img/bee-ill-montgolfiere.png" />
					<div class="main_title">{l s='Inscription' mod='mpsellerwiselogin'}</div>
					<div class="sub_title">Bienvenue sur BeeShary</div>
				</div>
			{else}
				<div class="top_header">
					<img class="mp_montgolfiere" src="{$urls.base_url}themes/beeshary_child/assets/img/bee-ill-montgolfiere.png" />
					<div class="main_title">Je m’inscris sur BeeShary avec ma CMAR PACA</div>
				</div>
				<div class="sub_title custom">
					Votre CMAR Provence-Alpes-Côte d’Azur vous souhaite une belle aventure numérique avec son partenaire BeeShary
				</div>
				<div class="text-center" style="padding-top: 10px">
					<img src="/modules/mpbadgesystem/views/img/badge_img/1.jpg" height="130px">
				</div>
				{/if}
			</div>
			<div class="seller_steps">
				<div class="steps">
					<center>
						{foreach from=$steps item=step key=k name="steps"}<div class="step_{$k} step{if $k == 0} active{/if}"><span>{$step|escape:'htmlall':'utf-8'}</span></div>{if !$smarty.foreach.steps.last}<div class="steps_separator"></div>{/if}{/foreach}
					</center>
				</div>
			</div>
			<div class="form_container">
				<form id="seller" class="form_fields" action="{$link->getModuleLink('mpsellerwiselogin', 'sellercreation', ['submitSellerCreationForm' => 1])}" method="post" enctype="multipart/form-data">
					{include file='module:mpsellerwiselogin/views/templates/front/_partials/partner.tpl'}
					{include file='module:mpsellerwiselogin/views/templates/front/_partials/profile_creation.tpl'}
					{include file='module:mpsellerwiselogin/views/templates/front/_partials/store_creation.tpl'}
						{**include file='module:mpsellerwiselogin/views/templates/front/_partials/images_creation.tpl'**}
						{**include file='module:mpsellerwiselogin/views/templates/front/_partials/delivery_method_creation.tpl'**}
					{include file='module:mpsellerwiselogin/views/templates/front/_partials/terms_of_use_creation.tpl'}
					{include file='module:mpsellerwiselogin/views/templates/front/_partials/subscription.tpl'}
				</form>
			</div>
		</div>
	</div>
</div>
{/block}
