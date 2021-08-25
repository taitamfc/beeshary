{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<div class="form-group row" id="leoCategoryExtra">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Layout Type' mod='appagebuilder'}">
        {l s='Layout Type' mod='appagebuilder'}
        </span>
      </label>
    <div class="col-lg-9">

    <select id="aplayout" name="aplayout" class="custom-select">
        <option value="default">{l s='default' mod='appagebuilder'}</option>
        {foreach $category_layouts as $aplayout}
        <option value="{$aplayout['plist_key']}" {if $aplayout['plist_key'] == $current_layout}selected="selected"{/if}>{$aplayout['name']}</option>
        {/foreach}
    </select>
    <br/>
    <div class="alert alert-info" role="alert">
        <p class="alert-text">
          1. {l s='Create layout file in Ap PageBuilder > Ap product list builder' mod='appagebuilder'}<br>
          2. {l s='Use code $category.categorylayout to get layout' mod='appagebuilder'}<br>
          3. {l s='Example code download in' mod='appagebuilder'} <a href="https://github.com/ApolloTheme/appagebuilderlayout/blob/master/YOURTHEMENAME/templates/catalog/listing/category.tpl" title="example">{l s='Here' mod='appagebuilder'}</a>
          <br>
        </p>
      </div>
    </div>

    {foreach from=$apextras key=apextrak item=apextrav}
    <div class="form-group">
       <label class="control-label col-lg-3">{$apextrak}</label>
       <div class="col-lg-9">
          <div class="form-group">
            {foreach from=$languages item=language}
                {if $languages|count > 1}
                <div class="row">
                  <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_lang_default}style="display:none"{/if}>
                    <div class="col-lg-9">
                {/if}
                {if $apextrav == 'varchar(255)'}
                    <input id="{$apextrak}_{$language.id_lang|intval}" type="text"  name="{$apextrak}_{$language.id_lang|intval}" value="{if $data_fields}{$data_fields[$apextrak][$language.id_lang]}{/if}"/>
                {else}
                    <textarea name="{$apextrak}_{$language.id_lang|intval}" rows="2" class="textarea-autosize">{if $data_fields}{$data_fields[$apextrak][$language.id_lang]|escape}{/if}</textarea>
                {/if}
                {if $languages|count > 1}
            </div>
            <div class="col-lg-2">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                {$language.iso_code}
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                {foreach from=$languages item=language}
                <li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
                {/foreach}
              </ul>
            </div>
          </div>
        </div>
        {/if}

        {/foreach}
          </div>
          <p class="alert-text">Use $category.{$apextrak} to get value in category.tpl file</p> 
        </div>
    </div>  
    {/foreach}
</div>
<script>
    $(document).ready(function(){
        if($('#category_form .form-wrapper').length)
        {
            $("#leoCategoryExtra").hide();
            $("#leoCategoryExtra").appendTo('#category_form .form-wrapper');
            $("#leoCategoryExtra").show();
        }
        
        if($('form[name="category"] .card-text').length)
        {
            $("#leoCategoryExtra").hide();
            $("#leoCategoryExtra").appendTo('form[name="category"] .card-text');
            $("#leoCategoryExtra").show();
        }

    });
</script>