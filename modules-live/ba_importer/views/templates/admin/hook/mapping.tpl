{*

* 2007-2020 PrestaShop

*

* NOTICE OF LICENSE

*

* This source file is subject to the Academic Free License (AFL 3.0)

* that is bundled with this package in the file LICENSE.txt.

* It is also available through the world-wide-web at this URL:

* http://opensource.org/licenses/afl-3.0.php

* If you did not receive a copy of the license and are unable to

* obtain it through the world-wide-web, please send an email

* to license@prestashop.com so we can send you a copy immediately.

*

* DISCLAIMER

*

* Do not edit or add to this file if you wish to upgrade PrestaShop to newer

* versions in the future. If you wish to customize PrestaShop for your

* needs please refer to http://www.prestashop.com for more information.

*

*  @author    Buy-addons <contact@buy-addons.com>

*  @copyright 2007-2020 PrestaShop SA

*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)

*  International Registered Trademark & Property of PrestaShop SA

*}

<div class='advance_content panel col-lg-12'>
    <h3>{l s='Step 2: Mapping Fields' mod='ba_importer'}</h3>
    <form action='' method='POST' class='form-horizontal col-sm-9' id='form_import'>
        {$mapping_select}{* escape is unnecessary *}
        <input id='dir_file' type='hidden' name='dir_file' value='{$base_uri|escape:"htmlall":"UTF-8"}'/>
        <input id='arr' type='hidden' name='arr' value='{$ba_arr|escape:"htmlall":"UTF-8"}'/>
        <input id='so_hang' type='hidden' name='so_hang' value='{$ba_so_hang|escape:"htmlall":"UTF-8"}'/>
        <input id='demo_mode' type='hidden' name='demo_mode' value='{$ba_demo_mode|escape:"htmlall":"UTF-8"}'/>
        <input id='identify_existing_items' type='hidden' name='identify_existing_items' value='{$identify_existing_items|escape:"htmlall":"UTF-8"}'/>
        <input id='identify_existing_items_combi' type='hidden' name='identify_existing_items_combi' value='{$identify_existing_items_combi|escape:"htmlall":"UTF-8"}'/>
        <input id='manu_exist' type='hidden' name='manu_exist' value='{$manu_exist|escape:"htmlall":"UTF-8"}'/>
        <input id='sup_exist' type='hidden' name='sup_exist' value='{$sup_exist|escape:"htmlall":"UTF-8"}'/>
        <input id='baencode' type='hidden' name='baencode' value='{$baencode|escape:"htmlall":"UTF-8"}'/>
        <input id='product_type' type='hidden' name='product_type' value='{$product_type|escape:"htmlall":"UTF-8"}'/>
        <input id='select_settings' type='hidden' name='select_settings' value='{$select_settings|escape:"htmlall":"UTF-8"}'/>
        <input id='get_id_config' type='hidden' name='get_id_config' value='{$get_id_config|escape:"htmlall":"UTF-8"}'/>
        <input id='multi_lang' type='hidden' name='multi_lang' value='{$multi_lang|escape:"htmlall":"UTF-8"}'/>
        <input id='import_header' type='hidden' name='import_header' value='{$import_header|escape:"htmlall":"UTF-8"}'/>
        <input type='hidden' name='file_name' value='{$ba_file_name|escape:"htmlall":"UTF-8"}'/>
        <input type='hidden' name='tokenProducts' value='{$tokenProducts|escape:"htmlall":"UTF-8"}'/>
        <input type='hidden' name='employee_id' value='{$employee_id|escape:"htmlall":"UTF-8"}'/>
        <input type='hidden' name='shop_id' value='{$shop_id|escape:"htmlall":"UTF-8"}'/>
        <input type='hidden' name='shop_id_group' value='{$shop_id_group|escape:"htmlall":"UTF-8"}'/>
        <div class="form-group ajax_loadding_import" >
            <label class="control-label advance-label"></label>
            <img style="display:none; float: left; margin-right: 10px;" class="advance_select ba_load" src="{$base_uri|escape:"htmlall":"UTF-8"}modules/ba_importer/views/img/ajax_loadding.gif" />
            <div id="so_sp_da_them" style="display:none; float:left">0</div>
            <span id="so_sp_da_them2" style="display:none; float:left">/{$ba_so_hang|escape:"htmlall":"UTF-8"}</span>
            {*<span  style="display:none; float:left; margin-left: 10px;"><input id="check_auto_import" style="float:left; margin-right: 5px;" type="checkbox" />{l s='Auto import' mod='ba_importer'}</span>*}
            <span id="so_sp_da_them3" style="display:none; float:left;margin-left: 10px;">{l s='This is Demo mode, so we only imported 20 for maximum products.' mod='ba_importer'}</span>
        </div>
        <div class='form-group advance_add'>
            <button id="btnAddProduct" type='button' class='btn btn-default' name='submitAddDb' onclick='ba_import()'>{l s='Import product' mod='ba_importer'}</button>
            {if $import_local != 1}
                <span id="or">{l s='Or' mod='ba_importer'}</span>
                <button id="btnNextStep" type='submit' class='btn btn-default' name='btnNextStep' onclick='ba_import_auto()'>{l s='Set Auto Work' mod='ba_importer'}</button>            
            {/if}
            <button type='submit' class='btn btn-default' id='cancelAddDb' name='cancelAddDb'>{l s='Back to Step 1' mod='ba_importer'}</button>
            <a class='btn btn-default' href="{$list_setting_url|escape:'htmlall':'UTF-8'}">{l s='Back to List Settings' mod='ba_importer'}</a>
        </div>
    </form>
    <div id='result' class=' col-sm-3'>
        <h3>{l s='Results' mod='ba_importer'}</h3>
        <ol id='result_ul'></ol>
    </div>
</div>
<script>
    var alert_demo_mode = "{l s='Changing the settings is not available in demo version.' mod='ba_importer'}";
    var total_imported = "{l s='Total products are imported: ' mod='ba_importer'}";
    var products_imported = "{l s='Products imported: ' mod='ba_importer'}";
    var alert_add = "{l s='You MUST choose Product Name while adding New a product.' mod='ba_importer'}";
    var alert_update = "{l s='You choosed' mod='ba_importer'} {$identify_existing_items} {l s='for checking Product existed, so you MUST mapping this field to a column in Excel/CSV file.' mod='ba_importer'}";{* escape is unnecessary *}
    var alert_update_combi = "{l s='You choosed' mod='ba_importer'} {$identify_existing_items_combi} {l s='for checking Combination existed, so you MUST mapping this field to a column in Excel/CSV file.' mod='ba_importer'}";{* escape is unnecessary *}
    var pro_start_import =  {$product_start_import|escape:'htmlall':'UTF-8'};
</script>