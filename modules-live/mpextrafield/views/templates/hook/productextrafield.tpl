{**
* 2010-2017 Webkul
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
<style type="text/css">
div.radio input
{
	background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
	border: medium none;
	display: inline-block;
	opacity: unset !important;
	text-align: center;
}
</style>
{if isset($extrafielddetail) }
	<div class="form-group">
		<label class="control-label required" for="profession">Profession</label>
		<input type="text" class="form-control" name="profession" id="profession" placeholder="Profession*" maxlength="100" required="">
	</div>
	
    <div class="form-group">
		<label class="control-label" for="seller_lang">Langue(s) parlée(s)*</label>
		<select class="form-control" id="seller_lang" name="spoken_langs[]" multiple="" data-placeholder="Langue(s) parlée(s)*" required="">
			<option value="108">Français</option>
			<option value="109">Anglais</option>
			<option value="110">Allemand</option>
			<option value="111">Espagnol</option>
			<option value="112">Portuguais</option>
			<option value="113">Italien</option>
			<option value="114">Chinois</option>
			<option value="115">Arabe</option>
			<option value="116">Autre</option>
		</select>
    </div>

    <div class="form-group">
		<label class="control-label" for="pp_theme">Sélectionnez votre savoir-faire</label>
		<select name="pp_theme[]" class="form-control" multiple="">
		   <option value="189">Vignoble, Brasserie & distillerie</option>
		   <option value="190">Travail du Verre</option>
		   <option value="191">Travail du Métal</option>
		   <option value="192">Travail du Cuir</option>
		   <option value="193">Travail du Bois</option>
		   <option value="194">Textile, Mode et accessoires</option>
		   <option value="195">Savonnerie & parfumerie</option>
		   <option value="196">Poterie & Céramique</option>
		   <option value="197">Pierre & Marbre</option>
		   <option value="198">Papier & calligraphie</option>
		   <option value="199">Nature & botanique</option>
		   <option value="200">Mobilier & décoration</option>
		   <option value="201">Insolite</option>
		   <option value="202">Gastronomie et Métiers de bouche</option>
		   <option value="203">Epicerie salée et conserverie</option>
		   <option value="204">Création de bijoux</option>
		   <option value="205">Cosmétiques & produits de bien-être</option>
		   <option value="206">Confitures & Gourmandises sucrées</option>
		   <option value="207">Chocolat & Confiserie</option>
		   <option value="208">Boulangerie, Patisserie et Biscuiterie</option>
		   <option value="209">Artisanat Ecologique & recyclage</option>
		   <option value="210">Art & Peinture</option>
		   <option value="211">Apiculture et produits de la ruche</option>
		   <option value="212">Agriculture, production et élevage</option>
		</select>
    </div>
    

    <div class="form-group">
	  <label class="control-label" for="quisuisje">Quisuisje</label>
      <textarea class="form-control" name="quisuisje" id="quisuisje" placeholder="Qui suis-je ? Je me présente / mon travail en 3 lignes." maxlength="250"></textarea>
    </div>

	<div class="form-group">
	  <label class="control-label" for="mapassion">Ma passion</label>
      <textarea class="form-control" name="mapassion" id="mapassion" placeholder="Ma passion. En quelques mots, qu'est ce qui m'anime/me motive dans mom métier?" maxlength="1000"></textarea>
    </div>

	
	
    <div class="form-group">
		<label class="control-label required" for="unproverbe"> Un proverbe</label>
		<select class="form-control" name="unproverbe" id="unproverbe">
			<option value="">Un proverbe un dicton, une phrase qui me correspond</option>
			<option value="à bon vin point d'enseigne">A bon vin point d'enseigne</option>
			<option value="à chaque jour suffit sa peine">A chaque jour suffit sa peine</option>
			<option value="à chaque problème, une solution">A chaque problème, une solution</option>
			<option value="à l'œuvre on connaît l'artisan">A l'œuvre on connaît l'artisan</option>
			<option value="bien faire, et laisser dire">Bien faire, et laisser dire</option>
			<option value="c'est dans les vieux pots qu'on fait la meilleure soupe">C'est dans les vieux pots qu'on fait la meilleure soupe</option>
			<option value="c'est en forgeant qu'on devient forgeron">C'est en forgeant qu'on devient forgeron</option>
			<option value="ce que femme veut, Dieu le veut">Ce que femme veut, Dieu le veut</option>
			<option value="ce qui ne tue pas rend plus fort">Ce qui ne tue pas rend plus fort</option>
			<option value="il n'est point de sot métier">Il n'est point de sot métier</option>
			<option value="il n'y a que le premier pas qui coûte">Il n'y a que le premier pas qui coûte</option>
			<option value="l'argent est un bon serviteur et un mauvais maître">L'argent est un bon serviteur et un mauvais maître</option>
			<option value="l'erreur est humaine">L'erreur est humaine</option>
			<option value="l'habit ne fait pas le moine">L'habit ne fait pas le moine</option>
			<option value="la critique est aisée mais l'art est difficile">La critique est aisée mais l'art est difficile</option>
			<option value="la fortune sourit aux audacieux">La fortune sourit aux audacieux</option>
			<option value="la parole est d'argent et le silence est d'or">La parole est d'argent et le silence est d'or</option>
			<option value="Le bon vivant n'est pas celui qui mange beaucoup, mais celui qui goûte avec bonheur à toutes les formes de la vie">Le bon vivant n'est pas celui qui mange beaucoup, mais celui qui goûte avec bonheur à toutes les formes de la vie</option>
			<option value="Le bonheur n'est vrai que quand il est partagé">Le bonheur n'est vrai que quand il est partagé</option>
			<option value="les petits ruisseaux font les grandes rivières">Les petits ruisseaux font les grandes rivières</option>
			<option value="Ne remets pas à demain ce que tu peux faire aujourd'hui">Ne remets pas à demain ce que tu peux faire aujourd'hui</option>
			<option value="Plaisir non partagé n'est plaisir qu'à moitié">Plaisir non partagé n'est plaisir qu'à moitié</option>
			<option value="Savoir partager son temps, c'est savoir jouir de la vie">Savoir partager son temps, c'est savoir jouir de la vie</option>
			<option value="Un brin de folie égaye la vie">Un brin de folie égaye la vie</option>
		</select>
        <p>* Champs obligatoires</p>
    </div>
	
	<div class="form-group">
		<label class="control-label required" for="labels"> Mes labels et certificats*</label>
		<select class="form-control has-chosen" id="labels" name="labels[]" multiple="" data-placeholder="Mes labels et certificats*" >
			<option value="1">Je m’inscris sur BeeShary avec ma CMAR PACA</option>
			<option value="2">Métiers d'Art</option>
		</select>
	</div>
	
	<div class="form-group">
		<label class="control-label required" for="siret"> SIRET</label>
		<input type="text" class="form-control " name="siret" id="siret" placeholder="Siret*" maxlength="14">
	</div>
	
{/if}	
	
	


{if isset($extrafielddetail) && false}
	{foreach $extrafielddetail as $extrafield }
		{*<input type="hidden" name="seller_default_lang" value="{$seller_default_lang|escape:'html':'UTF-8'}" id="seller_default_lang">
		<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang|escape:'html':'UTF-8'}" 
		id="current_lang_id">*}
		{if $extrafield.inputtype == 1}
			<div class="form-group">
				<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
				{block name='mp-form-fields-flag'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
				{/block}
				</label>
				{foreach from=$languages item=language}
					{assign var="label_name" value="{$extrafield.attribute_name}_`$language.id_lang`"}
				<input type="text" class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="{$label_name}" id=""
				value="{if isset($smarty.post.{$label_name})}{$smarty.post.{$label_name}}{else if isset($extrafield.default_value[$language.id_lang]) && $extrafield.asplaceholder eq 0}{$extrafield.default_value.{$language.id_lang}}{/if}"
				{if $extrafield.asplaceholder == 1}placeholder="{if isset($extrafield.default_value[$language.id_lang]) && $extrafield.asplaceholder eq 1}{$extrafield.default_value.{$language.id_lang}}{else if isset($extrafield.default_value) && $extrafield.asplaceholder eq 1}{$extrafield.default_value}
						{/if}"{/if} {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
				{/foreach}
			</div>
		{/if}
		{if $extrafield.inputtype ==2}
			<div class="form-group">
				<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
				{block name='mp-form-fields-flag'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
				{/block}
				</label>
				{foreach from=$languages item=language}
				{assign var="textarea_name" value="{$extrafield.attribute_name}_`$language.id_lang`"}
				<textarea name="{$textarea_name}" id="" class="form-control wk_text_field_all wk_text_field_{$language.id_lang}"  {if $extrafield.asplaceholder eq 1}placeholder="{if isset($extrafield.default_value[$language.id_lang]) && $extrafield.asplaceholder eq 1}{$extrafield.default_value[$language.id_lang] nofilter}{else if isset($extrafield.default_value) && $extrafield.asplaceholder eq 1}{$extrafield.default_value}
				{/if}"{/if} {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>{if isset($smarty.post.{$textarea_name})}{$smarty.post.{$textarea_name}}{elseif !empty($extrafield.default_value[$language.id_lang]) && $extrafield.asplaceholder eq 0}{$extrafield.default_value[$language.id_lang] nofilter}{/if}</textarea>
				{/foreach}	
			</div>
		{/if}
		{if $extrafield.inputtype == 3}
		<div class="form-group">
			<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label>
			<div class="row">
				<div class="col-xs-4">
					<div class="" id="" style="width: 82px;">
						<select name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name}[]{/if}" class="form-control" {if $extrafield.multiple==1}multiple{/if}>
							{foreach $extrafield['extfieldoption'] as $extfieldopt}
							<option value="{$extfieldopt.id}" 
							{if isset($smarty.post.{$extrafield.attribute_name})}
								{foreach $smarty.post.{$extrafield.attribute_name} as $key => $smarty_val}
									{if $smarty_val == $extfieldopt.id}
										selected="selected"
									{/if}
								{/foreach}
							{/if}>{$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}
							</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>
		{/if}
		{if $extrafield.inputtype ==4}
		<div class="clearfix form-group">
			<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label><br>
			{foreach $extrafield['extfieldoption'] as $extfieldopt}
			<div class="checkbox">
				<label for="id_check">
					<input type="checkbox" name="{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}[]" value="{$extfieldopt.id|escape:'html':'UTF-8'}" 
					{if isset($smarty.post.{$extrafield.attribute_name})}
						{foreach $smarty.post.{$extrafield.attribute_name} as $key => $smarty_val}
							{if $smarty_val == $extfieldopt.id} 
								checked="checked"
							{/if}
						{/foreach}
					{/if} >{$extfieldopt.display_value|escape:'html':'UTF-8'}
				</label>
			</div>
			{/foreach}
		</div>
		{/if}
		{if $extrafield.inputtype ==5}
		<div class="form-group">
			<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name}
			</label>
			<input class="filestyle extra-file" id="{$extrafield.id}" type="file" value="{if $extrafield.file_type=='1'}1{else if $extrafield.file_type=='2'}2{else if $extrafield.file_type=='3'}3{else}0{/if}" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name}{/if}">
			{if $extrafield.file_type=='1'}
			<p class="help-block">
				{l s='Valid image extensions are gif, jpg, jpeg and png' mod='mpextrafield'}
			</p>
			{else if $extrafield.file_type=='2'}
			<p class="help-block">
				{l s='Valid document extensions are doc,zip and pdf' mod='mpextrafield'}
			</p>
			{else}
			<p class="help-block">
				{l s='Valid extensions are gif,jpg,jpeg,png,zip,pdf,doc' mod='mpextrafield'}
			</p>
			{/if}
		</div>
		{/if}
		{if $extrafield.inputtype == 6}
		<div class="clearfix">
			<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label><br>
			<div class="radio-inline">
				<label for="gender1">
					<div><input type="radio" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}" value="1" {if isset($smarty.post.{$extrafield.attribute_name}) && 1 == $smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}}checked="checked"{/if}>
						{foreach $extrafield.extfieldradio as $extfieldrad}
						{$extfieldrad.left_value|escape:'htmlall':'UTF-8'}
						{/foreach}
					</div>
				</label>
			</div>
			<div class="radio-inline">
				<label for="gender2">
					<div><input type="radio" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}" value="2" {if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}) && 2 == $smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}}checked="checked"{/if}>
						{foreach $extrafield.extfieldradio as $extfieldrad}
						{$extfieldrad.right_value|escape:'htmlall':'UTF-8'}
						{/foreach}
					</div>
				</label>
			</div>
		</div>
		{/if}
	{/foreach}
{/if}