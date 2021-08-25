{* 
* @Module Name: Leo Slideshow
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<div class="alert alert-danger" id="slider-warning" style="display:none"></div>
<fieldset>
<div class="panel">
<div class="panel-heading">
	<i class="icon-list-ul"></i> {l s='Slides list' mod='leoslideshow'}
	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leoslideshow&addNewSlider=1&id_group={$id_group|intval}">
			<label><span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add new" data-html="true"><i class="process-icon-new "></i></span></label>
		</a>
	</span>
</div>
        <div class="alert alert-info"><a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leoslideshow&editgroup=1&id_group={$id_group|intval}" alt={l s='Back to' mod='leoslideshow'} {$group_title|escape:'html':'UTF-8'}>{l s='Back to' mod='leoslideshow'} {$group_title|escape:'html':'UTF-8'}</a></div>
	<div id="slidesContent" style="width: 500px; margin-top: 30px;">
		<ul id="slides">
		{foreach from=$slides item=slide}
			<li id="slides_{$slide.id_slide|intval}">
				<strong>#{$slide.id_slide|intval}</strong> {$slide.title|truncate:32:'...'|escape:'html':'UTF-8'}
				<div style="float: right;margin-top: -5px;">
					{$slide.status}{* HTML form , no escape necessary *}
                                        <div class="btn-group">
                                            <a class="btn btn-default dropdown-toggle" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leoslideshow&editSlider=1&id_slide={$slide.id_slide|intval}&id_group={$id_group|intval}"> 
                                                {if $slide.id_slide == $currentSliderID}
                                                    {l s='Editting' mod='leoslideshow'}
                                                {else}
                                                    {l s='Action' mod='leoslideshow'}
                                                {/if}
                                            </a>

                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                                                <span class="caret"></span>&nbsp;
                                            </button>
                                            <ul class="dropdown-menu" style="border: none">
                                                <li style="background-color:#fff;border: none">
                                                   <a class="" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leoslideshow&editSlider=1&id_slide={$slide.id_slide|intval}&id_group={$id_group|intval}"> 
                                                       <i class="icon-edit"></i> {l s='Click to Edit' mod='leoslideshow'}
                                                   </a>
                                                </li>
                                                <li style="background-color:#fff;border: none">
                                                    <a class="color_danger btn-actionslider delete-slide" data-confirm="{l s='Are you sure you want to delete this slider?' mod='leoslideshow'}" data-id-slide="{$slide.id_slide|intval}" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leoslideshow&leoajax=1&action=deleteSlider&id_slide={$slide.id_slide|intval}"><i class="icon-remove-sign"></i> {l s='Delete This slider' mod='leoslideshow'}</a>
                                                </li>
                                                <li style="background-color:#fff;border: none">
                                                   <a class="btn-actionslider" data-confirm="{l s='Are you sure you want to duplicate this slider?' mod='leoslideshow'}" data-id-slide="{$slide.id_slide|intval}" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leoslideshow&leoajax=1&action=duplicateSlider&id_slide={$slide.id_slide|intval}"> 
                                                       <i class="icon-film"></i> {l s='Duplicate This Slider' mod='leoslideshow'}
                                                   </a>
                                                </li>
                                            </ul>
                                            
                                        </div>
                                        
                                        <div class="btn-group"> 
                                            <a class="btn btn-default {if $languages|count > 1}dropdown-toggle {else}slider-preview {/if}color_danger" href="{$previewLink|escape:'html':'UTF-8'}&id_group={$id_group|intval}&id_slide={$slide.id_slide|intval}"><i class="icon-eye-open"></i> {l s='Preview' mod='leoslideshow'}</a>
                                            {if $languages|count > 1}

                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                                                <span class="caret"></span>&nbsp;
                                            </button>
                                            <ul class="dropdown-menu" style="border: none">
                                                {foreach from=$languages item=language}
                                                <li style="background-color:#fff;border: none">
                                                    {$arrayParam = ['secure_key' => $msecure_key, 'id_group' => $id_group, 'id_slide'=>$slide.id_slide]}
                                                    <a href="{$link->getModuleLink('leoslideshow','preview', $arrayParam, null, $language.id_lang)|escape:'html':'UTF-8'}" class="slider-preview">
                                                        <i class="icon-eye-open"></i> {l s='Preview For' mod='leoslideshow'} {$language.name|escape:'html':'UTF-8'}
                                                    </a>
                                                </li>
                                                {/foreach}
                                            </ul>
                                            {/if}
                                        </div>
				</div>
			</li>
		{/foreach}
		</ul>
	</div>
</div>
</fieldset>
<script type="text/javascript">
	var leo_slider_list_link = "{$leo_slider_list_link}";
</script>
