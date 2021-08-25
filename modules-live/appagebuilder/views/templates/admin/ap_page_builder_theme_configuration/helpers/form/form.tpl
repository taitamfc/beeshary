{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\admin\ap_page_builder_theme_configuration\helpers\form\form.tpl -->
{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'tabConfig'}
        <div class="row">
            {assign var=tabList value=$input.values}
            <ul class="nav nav-tabs" role="tablist">
            {foreach $tabList as $key => $value name="tabList"}
                <li role="presentation" class="tabConfig {if $key == $input.default}active{/if}"><a href="#{$key|escape:'html':'UTF-8'}" class="aptab-config" role="tab" data-toggle="tab" data-value="{$key|escape:'html':'UTF-8'}">{$value|escape:'html':'UTF-8'}</a></li>
            {/foreach}
            </ul>
        </div>
            
        <input type="hidden" id="tab_open" name="tab_open" value="{$input.default}">
        <script>
            $(document).ready(function(){
                $('.aptab-config').click(function(){
                    $('#tab_open').val( $(this).data('value') );
                });
            });
            
            $(document).on('click', '#configuration_form_submit_btn', function(e){
                e.preventDefault();
                var active_tab = $('.form-wrapper ul.nav-tabs li.active a').data('value');
                $('#tab_open').val( active_tab );
                $(this).closest('form').submit();
            });
        </script>
    {/if}
	
	{if $input.type == 'modules_block'}
            {assign var=moduleList value=$input.values}
            {if isset($input.exist_module) && !$input.exist_module}
                <label class="control-label" style="color: red; margin-bottom: 15px; margin-left: 10px;"> {l s='Empty module because not exist file config.xml in theme folder.' mod='appagebuilder'}</label>
                <br />
            {/if}
            <div class="col-lg-9 ">
            {if isset($moduleList) && count($moduleList) > 0}
                <p class="help-block">{l s='You can select one or more Module.' mod='appagebuilder'}</p>
                <table cellspacing="0" cellpadding="0" class="table" style="min-width:40em;">
                    <tr>
                        <th>
                            <input type="checkbox" name="checkme" id="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$input.name|escape:'html':'UTF-8'}[]', this.checked)" />
                        </th>
                        <th>{l s='Name' mod='appagebuilder'}</th>
                        <th>{l s='Back-up File' mod='appagebuilder'}
                            <p class="help-block" style="display: inline;">
                            {$backup_dir}
                            </p>
                        </th>
                    </tr>

                    {foreach from=$moduleList item=module name=moduleItem}
                        <tr {if $smarty.foreach.moduleItem.index % 2}class="alt_row"{/if}>
                            <td> 
                                <input type="checkbox" class="cmsBox" name="{$input.name|escape:'html':'UTF-8'}[]" id="chk_{$module.name|escape:'html':'UTF-8'}" value="{$module.name|escape:'html':'UTF-8'}"/>
                            </td>
                            <td><label for="chk_{$module.name|escape:'html':'UTF-8'}" class="t"><strong>{$module.name|escape:'html':'UTF-8'}</strong></label></td>
                            <td>
                                {if isset($module.files)}
                                <select style="max-width: 500px;" name="file_{$module.name|escape:'html':'UTF-8'}">
                                {foreach from=$module.files item=file name=Modulefile}
                                    <option value="{$file|escape:'html':'UTF-8'}">{$file|escape:'html':'UTF-8'}</option>
                                {/foreach}
                                </select>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}

                </table>
            {/if}
            </div>
            <div class="form-group button-wrapper">
                    <div class="col-lg-9 col-lg-offset-3">
                        <button class="button btn btn-success" name="submitBackup" id="submitBackup" type="submit">
                                 {l s='Back-up to PHP file' mod='appagebuilder'}
                        </button>
                        <button class="button btn btn-danger" name="submitRestore" data-confirm="{l s='Are you sure you want to restore from PHP file?' mod='appagebuilder'}" id="submitRestore" type="submit">
                                 {l s='Restore from PHP file' mod='appagebuilder'}
                        </button>
                        
                        <button class="button btn btn-success" name="submitSample" id="submitSample" type="submit">
                                 {l s='Export Sample Data' mod='appagebuilder'}
                        </button>
                        <button class="button btn btn-danger" name="submitImport" data-confirm="{l s='Are you sure you want to restore data sample of template. You will lost all data of module' mod='appagebuilder'}" id="submitImport" type="submit">
                                 {l s='Restore Sample Data' mod='appagebuilder'}
                        </button>
                        <p class="help-block">{l s='Data Sample is only for theme developer' mod='appagebuilder'}</p>
                    </div>
            </div>
            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <div class="alert alert-info">
                        {l s='With restore function, you will lost all data of module in site for all shop' mod='appagebuilder'}
                        <hr>
                        {l s='You should back-up before do any thing' mod='appagebuilder'}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <button class="button btn btn-success" name="submitExportDBStruct" id="submitExportDBStruct" type="submit">
                            {l s='Export Data Struct' mod='appagebuilder'}
                    </button>
                    <p class="help-block">
                        {$input.folder_data_struct}
                    </p>
                </div>
            </div>
                
            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <button class="button btn btn-success" name="submitUpdateModule" id="submitUpdateModule" type="submit" onclick="javascript:return confirm('{l s='Are you sure you want to Update and Correct Module. Please back-up all things before?' mod='appagebuilder'}')">
                            <i class="icon-AdminParentPreferences"></i> {l s='Update and Correct Module' mod='appagebuilder'}
                    </button>
                </div>
            </div>
                
            <script type="text/javascript">
                $(".button-wrapper .button").click(function(){
                    hasCheckedE = 0;
                    $("[name='moduleList[]']").each(function(){
                        if($(this).is(":checked")){
                            hasCheckedE = 1;
                            return false;
                        }
                    });
                    if(!hasCheckedE){
                        alert("You have to select atleast one module");
                        return false;
                    }
                    dataConfirm = $(this).attr("data-confirm");
                    if(dataConfirm){
                        return confirm(dataConfirm);
                    }
                });
            </script>
	{/if}
    
    {if $input.type == 'font_h'}
<div>
    <div class="col-lg-3">
        <h2>{$input.htitle}</h2>
        <p class="help-block">{$input.desc}</p>
    </div>
    <div class="col-lg-9">
        {foreach $input.items as $ikey => $item}
            
            {if ($item.type == 'select')}
                <div class="t_span4 col-lg-4">
                {if isset($item.label) }<h4 class="title-item">{$item.label}</h4>{/if}
                <select name="{$item.name|escape:'html':'utf-8'}"
                        class="{if isset($item.class)}{$item.class|escape:'html':'utf-8'}{/if}"
                        id="{if isset($item.id)}{$item.id|escape:'html':'utf-8'}{else}{$item.name|escape:'html':'utf-8'}{/if}"
                        {if isset($item.multiple) && $item.multiple} multiple="multiple"{/if}
                        {if isset($item.size)} size="{$item.size|escape:'html':'utf-8'}"{/if}
                        {if isset($item.onchange)} onchange="{$item.onchange|escape:'html':'utf-8'}"{/if}
                        {if isset($item.disabled) && $item.disabled} disabled="disabled"{/if}>
                    {if isset($item.options.default)}
                        <option value="{$item.options.default.value|escape:'html':'utf-8'}">{$item.options.default.label|escape:'html':'utf-8'}</option>
                    {/if}
                    {if isset($item.options.optiongroup)}
                        {foreach $item.options.optiongroup.query AS $optiongroup}
                            <optgroup label="{$optiongroup[$item.options.optiongroup.label]}">
                                {foreach $optiongroup[$item.options.options.query] as $option}
                                    <option value="{$option[$item.options.options.id]}"
                                        {if isset($item.multiple)}
                                            {foreach $fields_value[$input.name][$item.name] as $field_value}
                                                {if $field_value == $option[$item.options.options.id]}selected="selected"{/if}
                                            {/foreach}
                                        {else}
                                            {if $fields_value[$input.name][$item.name] == $option[$item.options.options.id]}selected="selected"{/if}
                                        {/if}
                                    >{$option[$item.options.options.name]}</option>
                                {/foreach}
                            </optgroup>
                        {/foreach}
                    {else}
                        {foreach $item.options.query AS $option}
                            {if is_object($option)}
                                <option value="{$option->$item.options.id}"
                                    {if isset($item.multiple)}
                                        {foreach $fields_value[$input.name][$item.name] as $field_value}
                                            {if $field_value == $option->$item.options.id}
                                                selected="selected"
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {if $fields_value[$input.name][$item.name] == $option->$item.options.id}
                                            selected="selected"
                                        {/if}
                                    {/if}
                                >{$option->$item.options.name}</option>
                            {elseif $option == "-"}
                                <option value="">-</option>
                            {else}
                                <option value="{$option[$item.options.id]}"
                                    {if isset($item.multiple)}
                                        {foreach $fields_value[$input.name][$item.name] as $field_value}
                                            {if $field_value == $option[$item.options.id]}
                                                selected="selected"
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {if $fields_value[$input.name][$item.name] == $option[$item.options.id]}
                                            selected="selected"
                                        {/if}
                                    {/if}
                                >{$option[$item.options.name]}</option>

                            {/if}
                        {/foreach}
                    {/if}
                </select>
                </div>
                
                
            {elseif $item.type == 'color'}
                <div class="t_span4 col-lg-4">
                    {if isset($item.label) }<h4 class="title-item">{$item.label}</h4>{/if}
                    <div class="input-group col-lg-5">
                        <input type="color"
                        data-hex="true"
                        {if isset($item.class)} class="{$item.class}"
                        {else} class="color mColorPickerInput"{/if}
                        name="{$input.name}"
                        value="{$fields_value[$input.name][$item.name]|escape:'html':'UTF-8'}" />
                    </div>
                </div>
                
            {elseif $item.type == 'text'}
                <div class="t_span4 col-lg-4">
                    {if isset($item.label) }<h4 class="title-item">{$item.label}</h4>{/if}
                    {assign var='value_text' value=$fields_value[$input.name][$item.name]}
                    {if isset($item.maxchar) || isset($item.prefix) || isset($item.suffix)}
                    <div class="input-group{if isset($item.class)} {$item.class}{/if}">
                    {/if}
                    {if isset($item.maxchar) && $item.maxchar}
                    <span id="{if isset($item.id)}{$item.id}{else}{$item.name}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$item.maxchar|intval}</span></span>
                    {/if}
                    {if isset($item.prefix)}
                    <span class="input-group-addon">
                      {$item.prefix}
                    </span>
                    {/if}
                    <input type="text"
                        name="{$item.name}"
                        id="{if isset($item.id)}{$item.id}{else}{$item.name}{/if}"
                        value="{if isset($item.string_format) && $item.string_format}{$value_text|string_format:$item.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                        class="{if isset($item.class)}{$item.class}{/if}{if $item.type == 'tags'} tagify{/if}"
                        {if isset($item.size)} size="{$item.size}"{/if}
                        {if isset($item.maxchar) && $item.maxchar} data-maxchar="{$item.maxchar|intval}"{/if}
                        {if isset($item.maxlength) && $item.maxlength} maxlength="{$item.maxlength|intval}"{/if}
                        {if isset($item.readonly) && $item.readonly} readonly="readonly"{/if}
                        {if isset($item.disabled) && $item.disabled} disabled="disabled"{/if}
                        {if isset($item.autocomplete) && !$item.autocomplete} autocomplete="off"{/if}
                        {if isset($item.required) && $item.required } required="required" {/if}
                        {if isset($item.placeholder) && $item.placeholder } placeholder="{$item.placeholder}"{/if}
                        />
                    {if isset($item.suffix)}
                    <span class="input-group-addon">
                      {$item.suffix}
                    </span>
                    {/if}

                    {if isset($item.maxchar) || isset($item.prefix) || isset($item.suffix)}
                    </div>
                    {/if}
                    {if isset($item.maxchar) && $item.maxchar}
                    <script type="text/javascript">
                    $(document).ready(function(){
                        countDown($("#{if isset($item.id)}{$item.id}{else}{$item.name}{/if}"), $("#{if isset($item.id)}{$item.id}{else}{$item.name}{/if}_counter"));
                    });
                    </script>
                    {/if}
                </div>
            {/if}
        {/foreach}
    </div>
</div>
    {/if}
    
	{if $input.type == 'font_setup'}
		<h4 style="" class="t-group-attr">Google Fonts Setup </h4>
        <p class="t-help-block">Here you can setup the <a href="//www.google.com/fonts" target="blank">Google web fonts</a> that you want to use in your site.</p>
        <div>
            
            <select class="select_gfont" style="font-size: 13px;height: 34px;margin-bottom: 0;margin-left: 0;margin-right: 0;margin-top: 0;max-width: 250px;width: 100%;display: inline-block">
                <option value="">Please select a font</option>
                {assign var=lgfonts value=$input.list_google_font}
                {foreach $lgfonts as $key => $value name="lgfont"}
                    <option value="{$value}">{$value}</option>
                {/foreach}
            </select>
            <a class="btn btn-success add_gfont" href="#">Add Font</a>
        </div>
        <br  />
        <br  />
{literal}
    

<div id="use_google_font" class="use_google_font panel col-lg-12" style="margin-top: 12px">
    <div class="table-responsive-row clearfix">
        <table class="table appagebuilder_positions">
            <thead>
                <tr class="nodrag nodrop">
                    <th class="center fixed-width-xs"></th>
                    <th class=""><span class="title_box">Name</span></th>
                    <th></th>
                </tr>
            </thead>
            
            <tbody class="list_gfonts"></tbody>
        </table>
    </div>
</div>
{/literal}
        
<div>
    <h4 class="t-group-attr">{l s='Google Fonts Subset' mod='appagebuilder'}</h4>
    <p class="t-help-block">{l s='Select which subsets you want to load for the Google fonts.' mod='appagebuilder'}</p>
    <div>
        <div class="leo_checkbox_wrapper ">
            <input name="gfonts_subsets[]" id="gfonts_subsets_latin" value="latin" type="checkbox">
            <label style="cursor: pointer !important;" for="gfonts_subsets_latin">{l s='Latin' mod='appagebuilder'}</label><br>

            <input name="gfonts_subsets[]" id="gfonts_subsets_latin-ext" value="latin-ext" type="checkbox">
            <label style="cursor: pointer !important;" for="gfonts_subsets_latin-ext">{l s='Latin Ext' mod='appagebuilder'}</label><br>

            <input name="gfonts_subsets[]" id="gfonts_subsets_greek" value="greek" type="checkbox">
            <label style="cursor: pointer !important;" for="gfonts_subsets_greek">{l s='Greek' mod='appagebuilder'}</label><br>

            <input name="gfonts_subsets[]" id="gfonts_subsets_cyrillic" value="cyrillic" type="checkbox">
            <label style="cursor: pointer !important;" for="gfonts_subsets_cyrillic">{l s='Cyrillic' mod='appagebuilder'}</label><br>

            <input name="gfonts_subsets[]" id="gfonts_subsets_cyrillic-ext" value="cyrillic-ext" type="checkbox">
            <label style="cursor: pointer !important;" for="gfonts_subsets_cyrillic-ext">{l s='Cyrillic Ext' mod='appagebuilder'}</label><br>

            <input name="gfonts_subsets[]" id="gfonts_subsets_khmer" value="khmer" type="checkbox">
            <label style="cursor: pointer !important;" for="gfonts_subsets_khmer">{l s='Khmer' mod='appagebuilder'}</label><br>

            <input name="gfonts_subsets[]" id="gfonts_subsets_greek-ext" value="greek-ext" type="checkbox">
            <label style="cursor: pointer !important;" for="gfonts_subsets_greek-ext">{l s='Greek Ext' mod='appagebuilder'}</label><br>

            <input name="gfonts_subsets[]" id="gfonts_subsets_vietnamese" value="vietnamese" type="checkbox">
            <label style="cursor: pointer !important;" for="gfonts_subsets_vietnamese">{l s='Vietnamese' mod='appagebuilder'}</label><br>
        </div>
    </div>
</div>

<!-- template -->
<div class="modal fade" id="modal_form"  data-backdrop="0" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
        <span class="sr-only">{l s='Close' mod='appagebuilder'}</span></button>
        <h4 class="modal-title" id="myModalLabel" data-addnew="{l s='Add new Widget' mod='appagebuilder'}" data-edit="{l s='Editting' mod='appagebuilder'}"></h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        
        <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='appagebuilder'}</button>
        <button type="button" class="btn btn-primary save_gfont">{l s='Save changes' mod='appagebuilder'}</button>
      </div>
    </div>
  </div>
    <div class="modal-backdrop fade in" style="height: 995px;"></div>
</div>

<!-- template -->
<table id="html_item_gfont" style="display:none">
    <tbody>
        <tr id="" class="tmp">
            <input type="hidden" class="data_gfont" value="" name=""/><!-- name, id, regular, italic-->
            <td class="row-selector text-center"><!--<input name="appagebuilder_positionsBox[]" value="23" class="noborder" type="checkbox">--></td>
            <td class="gfont_name"></td>
            <td class="text-right">
                <div class="btn-group-action">
                    <div class="btn-group pull-right">
                        <a href="javascript:void(0)" class="btn btn-default edit_gfont" title="View">
                            <i class="icon-pencil"></i> Edit
                        </a>
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-caret-down"></i>&nbsp;
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:void(0)" title="Delete" class="delete delete_gfont">
                                    <i class="icon-trash"></i> Delete
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>

<!-- template -->
<div id="temp_gfont_edit" style="display:none">
	<div>
            <h4 class="t-group-attr">{l s='Font CSS Class' mod='appagebuilder'}</h4>
            <p class="t-help-block">{l s='Using the css property:' mod='appagebuilder'} <code>font-family:"[gfont_name]";</code> {l s='to use this font.' mod='appagebuilder'}</p>
	</div>
    
        <br />
	<div>
            <h4 class="t-group-attr">{l s='Font variants' mod='appagebuilder'}</h4>
            <p class="t-help-block">{l s='Here you can select the font variants you want to load.' mod='appagebuilder'}</p>
            <div class="leo_checkbox_wrapper ">
                <!-- list variant here -->
            </div>
            
            <input id="gfont_id" value="" type="hidden">
            
            <div id="tmp-gfont-variant" style="display: none;">
                <!-- template variant -->
                <label style="cursor: pointer !important;"><input class="" name="[variant_name]" value="[variant_name]" [variant_checked] type="checkbox"> [variant_value]</label>
                <br>
            </div>
	</div>
</div>
<script>
    var global_gfont_list = jQuery.parseJSON('{$fields_value.gfont_list_ori}');
    var global_gfont_api = jQuery.parseJSON('{$fields_value.gfont_api}');
    var global_gfont_subset = jQuery.parseJSON('{$fields_value.gfont_subset}');
    
</script>
{literal}
<script>
    $(document).ready(function(){
        //$('.aptab-config:eq(3)').trigger('click');
        autoAddGoogleItem();
        autoAddGoogleSubset();
    });
    
    function autoAddGoogleItem()
    {
        $.each(global_gfont_list, function(index, value){
            addGoogleItem(value);
        });
    }
    
    function autoAddGoogleSubset()
    {
        $.each(global_gfont_subset, function(index, value){
            $('#gfonts_subsets_'+value).prop('checked', true);
        });
    }
    
    function addGoogleItem(obj)
    {
        $('.list_gfonts').append(   $('#html_item_gfont .tmp').parent().html()  );
        
        $('.list_gfonts tr.tmp .gfont_name').html( obj.gfont_name );
        $('.list_gfonts tr.tmp .data_gfont').val( JSON.stringify(obj) );
        $('.list_gfonts tr.tmp .data_gfont').attr( 'name', 'gfont_items[]' );
        $('.list_gfonts tr.tmp').attr("id", obj['gfont_id']);
        $('.list_gfonts tr.tmp').data("form", obj);


        $('.list_gfonts tr:even').addClass('odd');
        $('.list_gfonts tr:odd').removeClass('odd');
        $('.list_gfonts tr.tmp').removeClass('tmp');
    }
    
    $('.add_gfont').click(function(e) {
        e.preventDefault();
        var gfont_name = $('.select_gfont').val();

        // VALIDATE
        if ( gfont_name == "" || gfont_name == undefined ) {
            alert ('Please select a font.');
            return false;
        }
        var gfont_name_exist = false;
        $('.list_gfonts tr').each(function(){
            var tmp = $(this).data('form').gfont_name;
            if( gfont_name == tmp){
                gfont_name_exist = true;
            }
        });
        if(gfont_name_exist){
            alert ('Font is exist.');
            return false;
        }
        
        // set attribute
        var tmp = {};
        tmp['gfont_id'] = "gfont_" + getRandomNumber();
        tmp['gfont_name'] = gfont_name;
        
        addGoogleItem(tmp);
        
        $('.chk_font_exist').each(function(){
            $(this).append($('<option>', {
                value: gfont_name,
                text: gfont_name,
            }));
        });
    });

    $(document).on('click', '.edit_gfont', function(e){
        var tr_parent = $(this).closest('tr').data('form');
        var gfont_name = tr_parent.gfont_name;
        
        // HTML LIST VARIANT
        var html_variant = '';
        $.each( global_gfont_api[gfont_name]['variants'] , function( key, value ) {
            var output = '';
            output = $('#tmp-gfont-variant').html();
            output = output.replaceAll('[variant_name]', key);
            output = output.replaceAll('[variant_value]', value);
            
            if(tr_parent.hasOwnProperty(key))
                output = output.replaceAll('[variant_checked]', 'checked="checked"');
            else
                output = output.replaceAll('[variant_checked]', '');
            
            html_variant += output;
        });
        $('#temp_gfont_edit .leo_checkbox_wrapper').html(html_variant);
        
        // HTML FONT NAME
        var html = $('#temp_gfont_edit').html();
        html = html.replace("[gfont_name]", tr_parent['gfont_name']);
        
        // HTML MODAL
        $('#myModalLabel').html( $(this).closest('tr').data('form').gfont_name + " font options");
        $('.modal-body').html( html );
        
        // HTML FONT ID
        $('#gfont_id').val(tr_parent['gfont_id']);
        $('#modal_form').modal('show');
    });

    $(document).on('click', '.delete_gfont', function(e){
        if (!confirm('Delete selected item?'))
            return true;    // auto hide dropdow
        
        var gfont_name = $(this).closest('tr').data('form').gfont_name;
        
        $(this).closest('tr').remove();
        $('.list_gfonts tr:even').addClass('odd');
        $('.list_gfonts tr:odd').removeClass('odd');
        
        
        $(".chk_font_exist option[value='"+gfont_name+"']").remove();
    });
    
    $('.save_gfont').click(function(e){
        var gfont_id =  $('#gfont_id').val();
        var tmp = $('#'+gfont_id).data('form');
        
        // SET VARIANT
        $('#modal_form .leo_checkbox_wrapper input:checked').each(function(){
            var value = $(this).val();
            tmp[value] = value;
        });
        
        // SET TO FORM
        $('#'+gfont_id).data('form', tmp);
        $('#'+gfont_id+' .data_gfont').val( JSON.stringify(tmp) );
        
        $('#modal_form').modal('hide');
    });
    

    function getRandomNumber(){
        return (+new Date() + (Math.random() * 10000000000000000)).toString().replace('.', '');
    };
    String.prototype.replaceAll = function(target, replacement) {
      return this.split(target).join(replacement);
    };
</script>
  {/literal}
	{/if}
	{$smarty.block.parent}
{/block}