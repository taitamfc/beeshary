{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<div class="col-md-12">
  <div class="row">
    <div class="col-md-12">
      <h2>{l s='Layout Type' mod='appagebuilder'}</h2>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 form-group">
      <select id="aplayout" name="aplayout" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">
      	<option value="default">{l s='Global Layout' mod='appagebuilder'}</option>
      	{foreach $product_layouts as $aplayout}
        <option value="{$aplayout.plist_key}" {if $aplayout.plist_key == $current_layout}selected="selected"{/if}>{$aplayout.name}</option>
        {/foreach}
      </select>
      <br>
      <br>
      <div class="alert alert-info" role="alert">
        <i class="material-icons">help</i>
        <p class="alert-text">
          1. {l s='Please select layout to show product' mod='appagebuilder'}<br/>
          2. {l s='Then you can use variable' mod='appagebuilder'}<br/>
          $product.productLayout<br/>
          3. {l s='To select product layout in file product.tpl' mod='appagebuilder'}<br/>
          <br>
        </p>
      </div>
    </div>
  </div>

  {foreach from=$apextras key=apextrak item=apextrav}
  <div class="row">
    <div class="col-md-12 form-group">
      <fieldset class="form-group">
        <label>{$apextrak}</label>
        <div class="translations tabbable" id="form_tab_hooks_meta_title">
          <div class="translationsFields tab-content">
            {foreach from=$languages item=language}
              <div class="translationsFields-form_tab_hooks_{$apextrak}_{$language.id_lang} tab-pane translation-field translation-label-{$language.iso_code} {if $language.id_lang == $default_language.id_lang}visible{/if}">
                {if $apextrav == 'varchar(255)'}
                <input type="text" id="form_tab_hooks_{$apextrak}_{$language.id_lang}" name="form[tab_hooks][{$apextrak}][{$language.id_lang}]" class="form-control" value="{$data_fields[$apextrak][$language.id_lang]}"/>
                {else}
                <textarea name="form[tab_hooks][{$apextrak}][{$language.id_lang}]" rows="2" class="textarea-autosize">{$data_fields[$apextrak][$language.id_lang]|escape}</textarea>
                {/if}
              </div>
            {/foreach}
          </div>
        </div>
        <p class="alert-text">Use $product.{$apextrak} to get value in product.tpl file</p> 
      </fieldset>
    </div>
  </div>  
  {/foreach}
</div>