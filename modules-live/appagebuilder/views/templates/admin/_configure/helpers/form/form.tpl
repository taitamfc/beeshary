{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\admin\_configure\helpers\form\form -->
{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'blockLink'}
<script>

function getMaxIndex()
{
    if($('.link_group').length == 0)
    {
        return 1;
    }
    else
    {
        var list_index = [];
        $('.link_group').each(function(){
            list_index.push($(this).data('index'));
        })
        return Math.max.apply(Math,list_index) + 1;
    }
}

function updateNewLink(total_link, scroll_to_new_e, current_index, allow_add_fieldname)
{
    var array_id_lang = $.parseJSON(list_id_lang);
    if(allow_add_fieldname){
        $('.form-group.link_group.new .form-action').trigger("change"); // FIX show_hide input follow select_box
        hideOtherLanguage(id_language); // FIX when add new link, only show input in current_lang

        updateField('add','link_title_'+total_link,true);
        updateField('add','link_url_'+total_link,true);

        updateField('add','target_type_'+total_link, false);
        updateField('add','link_type_'+total_link, false);
        updateField('add','cmspage_id_'+total_link, false);
        updateField('add','category_id_'+total_link, false);
        updateField('add','product_id_'+total_link, false);
        updateField('add','manufacture_id_'+total_link, false);
        updateField('add','page_id_'+total_link, false);
        updateField('add','page_param_'+total_link, false);
        updateField('add','supplier_id_'+total_link, false);
    }

    $('.link_group.new .form-group .tmp').each(function(){
        // RENAME INPUT
        var e_obj = $(this);
        if($(this).closest(".translatable-field").length)
        {
            // MULTI_LANG
            $.each(array_id_lang, function( index, value ) {
                if (current_index == 0)
                {
                    // ADD NEW
                    switch(e_obj.attr('id'))
                    {
                        case 'link_title_'+value:
                            e_obj.attr('id','link_title_'+total_link+'_'+value);
                            e_obj.attr('name','link_title_'+total_link+'_'+value);
                            break;
                        case 'link_url_'+value:
                            e_obj.attr('id','link_url_'+total_link+'_'+value);
                            e_obj.attr('name','link_url_'+total_link+'_'+value);
                            break;
                    }
                }
            });

        }else{
            // ONE_LANG
            switch(e_obj.attr('id'))
            {
                case 'link_title_'+id_language:
                    e_obj.attr('id','link_title_'+total_link+'_'+id_language);
                    e_obj.attr('name','link_title_'+total_link+'_'+id_language);
                    break;
                case 'link_url_'+id_language:
                    e_obj.attr('id','link_url_'+total_link+'_'+id_language);
                    e_obj.attr('name','link_url_'+total_link+'_'+id_language);
                    break;
                default:
                    var old_id = e_obj.attr('id');
                    var old_name = e_obj.attr('name');
                    e_obj.attr('id',old_id+'_'+total_link);
                    e_obj.attr('name',old_name+'_'+total_link);
                    break;
            }
        }
    });
    $("#total_link").val(total_link);
}

function updateField(action, value, is_lang)
{
    if (action == 'add')
    {
        if (is_lang == true)
        {
            $('#list_field_lang').val($('#list_field_lang').val()+value+',');
        }
        else
        {
            $('#list_field').val($('#list_field').val()+value+',');
        }
    }
    else
    {
        // REMOVE
        if (is_lang == true)
        {
            var old_list_field_lang = $('#list_field_lang').val();
            var new_list_field_lang = old_list_field_lang.replace(value+',','');
            $('#list_field_lang').val(new_list_field_lang);
        }
        else
        {
            var old_list_field = $('#list_field').val();
            var new_list_field = old_list_field.replace(value+',','');
            $('#list_field').val(new_list_field);
        }
    }

    // UPDATE INDEX FORM 2,3,4,5,
    $('#list_id_link').val('');
    $('.link_group').each(function(){
        $('#list_id_link').val($('#list_id_link').val()+$(this).data('index')+',');
    })	
}

$(document).off("click", ".add-new-link");
$(document).on("click", ".add-new-link", function(e) {
    e.preventDefault();
    addLinkForm();
});

/**
 * ACTION FOR BUTTON ADD NEW
 * param : index for edit ajax_widget
 */
function addLinkForm( index ){
    var maxIndex = getMaxIndex();
    var allow_add_fieldname = true;
    if(index){
        maxIndex = index;
        allow_add_fieldname = false;
    }

    var new_link_html = '';
    new_link_html += '<div class="form-group link_group new">';

    $('.parent-tmp').each(function(){
        new_link_html += $(this).prop('outerHTML');
        new_link_html = new_link_html.replace('parent-tmp hidden','');
        new_link_html = new_link_html.replace('parent-tmp','');
        new_link_html = new_link_html.replace('display: none;','');
    });

    new_link_html += "<div class='form-group'>";
                    new_link_html += "<div class='col-lg-3'></div>";
                    new_link_html += "<div class='col-lg-9'>";
                        new_link_html += "<button class='fr btn btn-warning remove_link'>"+remove_button_text+"</button>";
                    new_link_html += '</div>';
                new_link_html += '</div>';
            new_link_html += '</div>';

    $(new_link_html).insertBefore('.form-group.frm-add-new-link').data('index', maxIndex);

    updateNewLink(maxIndex, true , 0, allow_add_fieldname);
    $('.link_group.new').removeClass('new');
}

$(document).off("click", ".remove_link");
$(document).on("click", ".remove_link", function(e) {
    e.preventDefault();

    $(this).closest('.link_group').find('.tmp').each(function(){
        // REMOVE FORM list_field, list_field_lang
        var name_val = $(this).attr('name');
        if($(this).closest(".translatable-field").length)
        {
            name_val = name_val.substring(0, name_val.lastIndexOf('_'));
            updateField('remove',name_val,true);
        }
        else
        {
            updateField('remove',name_val,false);
        }
    });

    $(this).closest('.link_group').fadeOut(function(){
        // REMOVE FORM
        $(this).remove();
        var total_link = parseInt($("#total_link").val())-1;
        $("#total_link").val(total_link);

        $('#list_id_link').val('');
        $('.link_group').each(function(){
            $('#list_id_link').val($('#list_id_link').val()+$(this).data('index')+',');
        })
    });
});

$(document).off("change", ".form-action");

$(".form-action").each(function(e) {
    $(this).attr('data-name', $(this).attr('name') );
});
$(document).on("change", ".link_group .form-action", function(e) {
    var elementName = $(this).attr('data-name');
    $('.' + elementName + '_sub', $(this).closest('.form-group.link_group')).hide();
    $('.' + elementName + '-' + $(this).val(), $(this).closest('.form-group.link_group')).show();
});

/**
 * AJAX FOR EDIT BLOCKLINK WIDGET
 */
function editWidgetLink()
{
    if ($('#list_id_link').length && $('#list_id_link').val() != '')
    {
        var list_id_link = $('#list_id_link').val().split(',');
        $.each(list_id_link, function( index, value ) {
            if (value != '')
            {
                // GENERATE FORM
                addLinkForm(value);
            }
        });

        $.each(listData, function( index, value ) {
            // FILL DATA INTO FORM
            $('#'+index).val(value);
            $('#'+index).val(value).prop('selected', true);;
        });

        setTimeout(function(){
            // SHOW_HIDE INPUT FOLLOW SELECT_BOX
            $('.form-group.link_group .form-action').trigger("change");
        }, 500);
    }
}
editWidgetLink();
</script>
	{/if}
	{if $input.type == 'tabConfig'}
		<div class="row">
			{assign var=tabList value=$input.values}
			<ul class="nav nav-tabs" role="tablist">
			{foreach $tabList as $key => $value name="tabList"}
				<li role="presentation" class="{if $smarty.foreach.tabList.first}active{/if}"><a href="#{$key|escape:'html':'UTF-8'}" class="aptab-config" role="tab" data-toggle="tab">{$value|escape:'html':'UTF-8'}</a></li>
			{/foreach}
			</ul>
		</div>
	{/if}
	{if $input.type == 'selectImg'}
        {if isset($input.lang) AND $input.lang}
		<div class="row selectImg lang">
			{foreach from=$languages item=language}
				{if $languages|count > 1}
					<div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" data-lang="{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
				{/if}
					<div class="col-lg-6">
						{if isset($input.show_image) && $input.show_image != false}
							{if isset($fields_value[$input.name][$language.id_lang]) && $fields_value[$input.name][$language.id_lang]}
							<img src="{$path_image|escape:'html':'UTF-8'}{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}" class="img-thumbnail" data-img="">
							{/if}
						{/if}
                                                <div style="margin-top: 10px; font-size: 13px;">
						<a class="choose-img {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" data-fancybox-type="iframe" href="{$input.href|escape:'html':'UTF-8'}" data-for="#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}">{l s='Select image' mod='appagebuilder'}</a>
                                                -
                                                <a class="reset-img {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" data-fancybox-type="iframe" href="{$input.href|escape:'html':'UTF-8'}" data-for="#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}">{l s='Reset image' mod='appagebuilder'}</a>
                                                </div>
						<input id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" type="text" name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" class="hide img-value{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}" value="{if isset($fields_value[$input.name][$language.id_lang]) && ($fields_value[$input.name][$language.id_lang])}{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}{/if}"/>

					</div>
                        
				{if isset($input.lang) AND $input.lang }
				{if $languages|count > 1}
					<div class="col-lg-2">
						<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
							{$language.iso_code|escape:'html':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=lang}
							<li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
							{/foreach}
						</ul>
					</div>
				{/if}
				{/if}
                
				{if $languages|count > 1}
					</div>
				{/if}
				<script type="text/javascript">
				$(document).ready(function(){
					$('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}-selectbutton').click(function(e){
						$('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}').trigger('click');
					});
					$('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}').change(function(e){
						var val = $(this).val();
						var file = val.split(/[\\/]/);
						$('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}-name').val(file[file.length-1]);
					});
				});
			</script>
			{/foreach}
		</div>
        {else}
            <div class="row selectImg">
                <div class="col-lg-6">
                    {if isset($input.show_image) && $input.show_image != false}
                        {if isset($fields_value[$input.name]) && $fields_value[$input.name]}
                        <img src="{$path_image|escape:'html':'UTF-8'}{$fields_value[$input.name]|escape:'html':'UTF-8'}" class="img-thumbnail" data-img="">
                        {/if}
                    {/if}
                    <div></div>
                    <a class="choose-img {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" data-fancybox-type="iframe" href="{$input.href|escape:'html':'UTF-8'}" data-for="#{$input.name|escape:'html':'UTF-8'}">{l s='Select image' mod='appagebuilder'}</a> - 
                    <a href="javascript:void(0)" onclick="resetLeoImage();">{l s='Reset' mod='appagebuilder'}</a>
                    <input id="{$input.name|escape:'html':'UTF-8'}" type="text" name="{$input.name|escape:'html':'UTF-8'}" class="hide input-s-image" value="{if isset($fields_value[$input.name]) && ($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'} {/if}"/>
                    <script type="text/javascript">
                        function resetLeoImage(){
                            // Reset img and hidden input
                            $(".img-thumbnail").hide();
                            $(".img-thumbnail").attr('src','');
                            $(".input-s-image").val('');
                        }
                    </script>            
                </div>

                    <script type="text/javascript">
                    $(document).ready(function(){
                        $('#{$input.name|escape:'html':'UTF-8'}-selectbutton').click(function(e){
                            $('#{$input.name|escape:'html':'UTF-8'}').trigger('click');
                        });
                        $('#{$input.name|escape:'html':'UTF-8'}').change(function(e){
                            var val = $(this).val();
                            var file = val.split(/[\\/]/);
                            $('#{$input.name|escape:'html':'UTF-8'}-name').val(file[file.length-1]);
                        });
                    });
                </script>
            </div>
            
        {/if}
	{/if}
	{if $input.type == 'img_cat'}
		{assign var=tree value=$input.tree}
			{assign var=imageList value=$input.imageList}
			{assign var=selected_images value=$input.selected_images}
		<div class="form-group form-select-icon">
			<label class="control-label col-lg-3 " for="categories"> {l s='Categories' mod='appagebuilder'} </label>
			<div class="col-lg-9">
			{$tree}{* HTML form , no escape necessary *}
			</div>
			<input type="hidden" name="category_img" id="category_img" value='{$selected_images|escape:'html':'UTF-8'}'/>
			<div id="list_image_wrapper" style="display:none">
				<div class="list-image">
					<img id="apci" src="" class="hidden" path="{$input.path_image|escape:'html':'UTF-8'}" widget="ApCategoryImage"/>
					<a data-for="#apci" href="{$input.href_image|escape:'html':'UTF-8'}" class="apcategoryimage field-link choose-img"> [{l s='Select image' mod='appagebuilder'}]</a>
					<a href="javascript:;" class="apcategoryimage field-link remove-img hidden"> [{l s='Remove image' mod='appagebuilder'}]</a>
				  </div>
			</div>
			<script type="text/javascript">
				full_loaded = true;
				intiForApCategoryImage();
			</script>
		</div>
	{/if}
	{if $input.type == 'categories'}
		<script type="text/javascript">
			var full_loaded = undefined;
		</script>
	{/if}
	{if $input.type == 'bg_img'}
		<div class="col-lg-9 ">
			<input type="text" name="bg_img" id="bg_img" value="{$fields_value['bg_img']|escape:'html':'UTF-8'}" class="">
          {if strpos($fields_value['bg_img'], "/") !== false}
              <img id="s-image"{if !$fields_value['bg_img']} class="hidden"{/if} src="{$fields_value['img_link']|escape:'html':'UTF-8'}{$fields_value['bg_img']|escape:'html':'UTF-8'}"/>
          {else if isset($fields_value['bg_img']) && $fields_value['bg_img']}
              <img id="s-image"{if !$fields_value['bg_img']} class="hidden"{/if} src="{$input.img_link|escape:'html':'UTF-8'}{$fields_value['bg_img']|escape:'html':'UTF-8'}"/>
          {else}
              <img id="s-image"{if !$fields_value['bg_img']} class="hidden"{/if} src=""/>
          {/if}
			<div>
				<a class="choose-img" data-fancybox-type="iframe" href="{$link->getAdminLink('AdminApPageBuilderImages')|escape:'html':'UTF-8'}&ajax=1&action=manageimage&imgDir=images" data-for="#bg_img">{l s='Select image' mod='appagebuilder'}</a> -
				<a class="reset-img" href="javascript:void(0)" onclick="resetBgImage();">{l s='Reset' mod='appagebuilder'}</a>
			</div>
			<p class="help-block">{l s='Please put image link or select image' mod='appagebuilder'}</p>
		</div>
		<script type="text/javascript">
			function resetBgImage(){
				// Reset img and hidden input
				$("#s-image").addClass('hiden');
				$("#s-image").attr('src','');
				$("#bg_img").val('');
			}
		</script>
	{/if}    
	{if $input.type == 'apExceptions'}
		<div class="well">
				<div>
						{l s='Please specify the files for which you do not want it to be displayed.' mod='appagebuilder'}<br />
						{l s='Please input each filename, separated by a comma (",").' mod='appagebuilder'}<br />
						{l s='You can also click the filename in the list below, and even make a multiple selection by keeping the Ctrl key pressed while clicking, or choose a whole range of filename by keeping the Shift key pressed while clicking.' mod='appagebuilder'}<br />
						{$exception_list}{* HTML form , no escape necessary *}
				</div>
		</div>
	{/if}
	{if $input.type == 'ApColumnclass' || $input.type == 'ApRowclass'}
		<div class="">
			<div class="well">
				<div class="row">
				   {if $input.type == 'ApRowclass'} 
				   <label class="choise-class col-lg-12"><input type="checkbox" class="chk-row" data-value="row" value="1"> {l s='Use class row' mod='appagebuilder'}</label>
				   {/if}
				   <label class="control-label col-lg-1">{$input.leolabel}</label>
					<div class="col-lg-11"><input type="text" class="element_class" value="{$fields_value['class']|escape:'html':'UTF-8'}" name="class"></div>
				</div><br/>
				<div class="desc-bottom">
				{l s='Insert new or select classes for toggling content across viewport breakpoints' mod='appagebuilder'}<br>
				<ul class="ap-col-class">
					<li>
						<label class="choise-class"><input class="select-class" name="hidden_from[]" type="radio" data-value="hidden-lg-down" value="1"> {l s='Hidden from Large devices' mod='appagebuilder'}</label>
					</li>
					<li>
						<label class="choise-class"><input class="select-class" name="hidden_from[]" type="radio" data-value="hidden-md-down" value="1"> {l s='Hidden from Medium devices' mod='appagebuilder'}</label>
					</li>
					<li>
						<label class="choise-class"><input class="select-class" name="hidden_from[]" type="radio" data-value="hidden-sm-down" value="1"> {l s='Hidden from Small devices' mod='appagebuilder'}</label>
					</li>
					<li>
						<label class="choise-class"><input class="select-class" name="hidden_from[]" type="radio" data-value="hidden-xs-down" value="1"> {l s='Hidden from Extra small devices' mod='appagebuilder'}</label>
					</li>
					{*<li>
						<label class="choise-class"><input class="select-class" name="hidden_from[]" type="checkbox" data-value="hidden-xs-up" value="1"> {l s='Hidden from Smart Phone' mod='appagebuilder'}</label>
					</li>*}
				</ul>
				</div>
			</div>
		</div>
	{/if}

	{if $input.type == 'bg_select'}
		{$image_uploader}{* HTML form , no escape necessary *}
	{/if}
	{if $input.type == 'column_width'}
		<div class="panel panel-default">
			<div class="panel-body">
				<p>{l s='Responsive: You can config width for each Devices' mod='appagebuilder'}</p>
			</div>
			<table class="table">
				<thead><tr>
					  <th>{l s='Devices' mod='appagebuilder'}</th>
					  <th>{l s='Width' mod='appagebuilder'}</th>
				</tr></thead>
				<tbody>
					{foreach $input.columnGrids as $gridKey=>$gridValue}
					<tr>
						<td>
							<span class="col-{$gridKey|escape:'html':'UTF-8'}"></span>
							{$gridValue|escape:'html':'UTF-8'}
						</td>
						<td>
							<div class="btn-group">
								<input type='hidden' class="col-val" name='{$gridKey|escape:'html':'UTF-8'}' value="{$fields_value[$gridKey]|escape:'html':'UTF-8'}"/>
								<button type="button" class="btn btn-default apbtn-width dropdown-toggle" tabindex="-1" data-toggle="dropdown">
									<span class="width-val ap-w-{$fields_value[$gridKey]|replace:'.':'-'|escape:'html':'UTF-8'}">{$fields_value[$gridKey]|escape:'html':'UTF-8'}/12 - ( {math equation="x/y*100" x=$fields_value[$gridKey]|replace:'-':'.' y=12 format="%.2f"} % )</span><span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									{foreach from=$widthList item=itemWidth}
									<li>
										<a class="width-select" href="javascript:void(0);" tabindex="-1">
											<span data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" class="width-val ap-w-{if $itemWidth|strpos:"."|escape:'html':'UTF-8'}{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}{else}{$itemWidth|escape:'html':'UTF-8'}{/if}">{$itemWidth|escape:'html':'UTF-8'}/12 - ( {math equation="x/y*100" x=$itemWidth|replace:'-':'.' y=12 format="%.2f"} % )</span>
										</a>
									</li>
									{/foreach}
								</ul>
							</div>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	{/if}
    {if $input.type == 'reloadControler'}
		<div class="col-lg-9 ">
			<div style="margin-top: 5px; font-size: 13px;">
				<a class="reload-controller-exception" href="javascript:void(0);">{l s='Reload' mod='appagebuilder'}</a>
			</div>
		</div>
            <script>
                $(document).off('click', '.reload-controller-exception').on('click', '.reload-controller-exception', function(){
                    $($globalthis.currentElement).data('form').reloadControllerException = '1';
                    var idFormApRow = $($globalthis.currentElement).data('form').form_id;
                    $('.'+idFormApRow+' .btn-edit').first().click();
                    $($globalthis.currentElement).data('form').reloadControllerException = '0';
                });
            </script>
	{/if}
	{$smarty.block.parent}
{/block}