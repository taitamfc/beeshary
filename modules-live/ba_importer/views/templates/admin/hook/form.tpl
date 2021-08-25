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
* to license@buy-addons.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Buy-addons <contact@buy-addons.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<form action='' method='POST' enctype="multipart/form-data">

    <div class='advance_content panel'>

        <h3>{l s='Upload Excel File' mod='ba_importer'}</h3>
        
            <div class="form-group">

                <label>{l s='Select Settings' mod='ba_importer'}:</label>                              
                <div class="items_to_import">               
                    <input id="select_settings" type="text" class="name_setting" name="select_settings" placeholder="Name Setting" value="{$select_iss|escape:'htmlall':'UTF-8'}">
                    
                </div>
            </div>

            <div class="form-group">

                <label>{l s='Source' mod='ba_importer'}:</label>
                
                <select class="import_items" id="mySelectBox" name='import_local'>
                    <option id="import_local_yes" value='1' {if $arr_confign_bc1.import_local == '1'}selected{/if}>{l s='Import from local' mod='ba_importer'}</option>
                    <option id="import_local_no" value='0' {if $arr_confign_bc1.import_local == '0'}selected{/if}>{l s='Import from Remote URL' mod='ba_importer'}</option>
                    <option id="import_ftp" value='2' {if $arr_confign_bc1.import_local == '2'}selected{/if}>{l s='Import from FTP' mod='ba_importer'}</option>
                </select>

            </div>
            
            <div class="form-group upload_file" id="local_excel" {if $arr_confign_bc1.import_local == '1'}style="display:block"{/if}>

                <label>{l s='File(Excel/CSV)' mod='ba_importer'}:*</label>

                <input type="file" id="exampleInputFile" name='filexls'>

            </div>

            <div class="form-group upload_file" id="form_import_ftp" {if $arr_confign_bc1.import_local == '2'}style="display:block"{/if}>

                <label>{l s='Information FTP' mod='ba_importer'}:</label>

                <div class="items_to_import_ftp">
					<div class="row">
						<span class="ftp-label">{l s='Host' mod='ba_importer'}: </span><input type="text" class="form-control" id="ftp_server" name="ftp_server" value="{$arr_confign_bc1.ftp_server|escape:'htmlall':'UTF-8'}" placeholder="Address of FTP server"/>
						<span class="ftp-label">{l s='Username' mod='ba_importer'}: </span><input type="text" class="form-control" id="ftp_user_name" name="ftp_user_name" value="{$arr_confign_bc1.ftp_user_name|escape:'htmlall':'UTF-8'}" placeholder="Username"/>
						<span class="ftp-label">{l s='Password' mod='ba_importer'}: </span><input type="password" class="form-control" id="ftp_user_pass" name="ftp_user_pass" value="{$arr_confign_bc1.ftp_user_pass|escape:'htmlall':'UTF-8'}" placeholder="Password"/>
						<span class="ftp-label">{l s='Port' mod='ba_importer'}: </span><input type="text" class="form-control" id="ftp_user_port" name="ftp_user_port" value="{$arr_confign_bc1.ftp_user_port|escape:'htmlall':'UTF-8'}" placeholder="Port"/>
					</div>
					<div class="row">
					<span class="ftp-label ftp_label_transfer_mode">{l s='Transfer Mode' mod='ba_importer'}: </span>
					<select name="ftp_user_transfer_mode" class="form-control" id="ftp_user_transfer_mode">
						<option value="passive"{if $arr_confign_bc1.ftp_user_transfer_mode == 'passive'} selected{/if}>{l s='Passive' mod='ba_importer'}</option>
						<option value="active"{if $arr_confign_bc1.ftp_user_transfer_mode == 'active'} selected{/if}>{l s='Active' mod='ba_importer'}</option>
					</select>
					</div>
					<div class="ftp_link_excel_wrapper row">
						<span class="ftp-label">{l s='Link to CSV/Excel File' mod='ba_importer'}: </span>
						<input type="text" class="form-control" id="ftp_link_excel" name="ftp_link_excel" value="{$arr_confign_bc1.ftp_link_excel|escape:'htmlall':'UTF-8'}" placeholder="Link excel"/>
					</div>
                </div>

            </div>

            <div class="form-group upload_file" id="url_excel" {if $arr_confign_bc1.import_local == '0'}style="display:block"{/if}>

                <label>{l s='Url' mod='ba_importer'}:</label>

                <div class="items_to_import">

                    <input id="link_excel" type="text" class="form-control" name="url_excel" value="{$arr_confign_bc1.url_excel|escape:'htmlall':'UTF-8'}" placeholder="Nhập link đến file excel"/>

                </div>

            </div>

            <div class="form-group">

                <label>{l s='Images(Zip file)' mod='ba_importer'}:</label>

                <input type="file" id="exampleInputImg" name='img'>

            </div>

			<div class="form-group">

                <label>{l s='Files for Virtual Product (Zip file)' mod='ba_importer'}:</label>

                <input type="file" id="exampleInputFile" name='exampleFile'>

            </div>
			
			<div class="form-group">

                <label>{l s='Files for Attachments (Zip file)' mod='ba_importer'}:</label>

                <input type="file" id="attachmentsInputFile" name='attachmentsFile'>

            </div>
			
            <div class="form-group">

                <label>{l s='New Items' mod='ba_importer'}:</label>

                <select class="import_items" name="new_items">

                    <option value='Add' {if $arr_confign_bc1.new_items == 'Add'}selected{/if}>{l s='Add' mod='ba_importer'}</option>

                    <option value='Ignore' {if $arr_confign_bc1.new_items == 'Ignore'}selected{/if}>{l s='Ignore' mod='ba_importer'}</option>

                </select>

            </div>

            <div class="form-group">

                <label>{l s='Existing Items' mod='ba_importer'}:</label>

                <select class="import_items" name="existing_items">

                    <option value='Update' {if $arr_confign_bc1.existing_items == 'Update'}selected{/if}>{l s='Update' mod='ba_importer'}</option>

                    <option value='Ignore' {if $arr_confign_bc1.existing_items == 'Ignore'}selected{/if}>{l s='Ignore' mod='ba_importer'}</option>

                </select>

            </div>
			
            <div class="form-group">

                <label>{l s='Product Type' mod='ba_importer'}:</label>

                <select class="import_items" name="product_type">

                    <option value='product_standard' {if $arr_confign_bc1.product_type == 'product_standard'}selected{/if}>{l s='Standard product' mod='ba_importer'}</option>

                    <option value='product_virtual' {if $arr_confign_bc1.product_type == 'product_virtual'}selected{/if}>{l s='Virtual product' mod='ba_importer'}</option>

                </select>

            </div>

            <div class="form-group">

                <label>{l s='Identify Existing Product by Matching Field' mod='ba_importer'}:</label>

                <select class="import_items" name="identify_existing_items">
                    <option value='- None -' {if $arr_confign_bc1.identify_existing_items == '- None -'}selected{/if}>{l s='- None -' mod='ba_importer'}</option>                  
                    <option value='Product Name' {if $arr_confign_bc1.identify_existing_items == 'Product Name'}selected{/if}>{l s='Product Name' mod='ba_importer'}</option>               
                    <option value='Product ID' {if $arr_confign_bc1.identify_existing_items == 'Product ID'}selected{/if}>{l s='Product ID' mod='ba_importer'}</option>
                    <option value='Reference code' {if $arr_confign_bc1.identify_existing_items == 'Reference code'}selected{/if}>{l s='Reference code' mod='ba_importer'}</option>
                    <option value='EAN-13 or JAN barcode' {if $arr_confign_bc1.identify_existing_items == 'EAN-13 or JAN barcode'}selected{/if}>{l s='EAN-13 or JAN barcode' mod='ba_importer'}</option>
                    <option value='UPC barcode' {if $arr_confign_bc1.identify_existing_items == 'UPC barcode'}selected{/if}>{l s='UPC barcode' mod='ba_importer'}</option>
					<option value='Supplier reference' {if $arr_confign_bc1.identify_existing_items == 'Supplier reference'}selected{/if}>{l s='Supplier reference' mod='ba_importer'}</option>		
					<option value='Combination Reference' {if $arr_confign_bc1.identify_existing_items == 'Combination Reference'}selected{/if}>{l s='Combination Reference' mod='ba_importer'}</option>
                </select>

            </div>
            
            <div class="form-group">

                <label>{l s='Identify Existing Combination by Matching Field' mod='ba_importer'}:</label>

                <select class="import_items" name="identify_existing_items_combi">
                    <option value='Attributes' {if $arr_confign_bc1.identify_existing_items_combi == 'Attributes'}selected{/if}>{l s='Attributes' mod='ba_importer'}</option>                  
                    <option value='Combi Reference code' {if $arr_confign_bc1.identify_existing_items_combi == 'Combi Reference code'}selected{/if}>
					{l s='Reference code' mod='ba_importer'}
					</option>
                    <option value='Combi EAN-13 or JAN barcode' {if $arr_confign_bc1.identify_existing_items_combi == 'Combi EAN-13 or JAN barcode'}selected{/if}>
					{l s='EAN-13 or JAN barcode' mod='ba_importer'}
					</option>
                    <option value='Combi UPC barcode' {if $arr_confign_bc1.identify_existing_items_combi == 'Combi UPC barcode'}selected{/if}>
					{l s='UPC barcode' mod='ba_importer'}
					</option>
					<option value='Combination ID (Attribute ID)' {if $arr_confign_bc1.identify_existing_items_combi == 'Combination ID (Attribute ID)'}selected{/if}>
					{l s='Combination ID (Attribute ID)' mod='ba_importer'}
					</option>
                </select>

            </div>
            
            <div class="form-group">

                <label>{l s='Update Quantity/Stock' mod='ba_importer'}:</label>

                <select class="import_items" name="quantity">
                    <option value='new_quantity' {if $arr_confign_bc1.quantity == 'new_quantity'}selected{/if}>{l s='Set Quantity/Stock to New Value' mod='ba_importer'}</option>
                    <option value='increase_quantity' {if $arr_confign_bc1.quantity == 'increase_quantity'}selected{/if}>{l s='Increase current Quantity/Stock' mod='ba_importer'}</option>
                </select>

            </div>

            <div class="form-group">

                <label>{l s='Update Categories' mod='ba_importer'}:</label>

                <select class="import_items" name="update_categories">
                    <option value='more_categories' {if $arr_confign_bc1.update_categories == 'more_categories'}selected{/if}>{l s='Add more Categories' mod='ba_importer'}</option>
                    <option value='new_categories' {if $arr_confign_bc1.update_categories == 'new_categories'}selected{/if}>{l s='Remove all OLD categories & Add New' mod='ba_importer'}</option>
                </select>

            </div>

            {*<div class="form-group">

                <label>{l s='Update Images' mod='ba_importer'}:</label>

                <select class="import_items" name="update_images">
                    <option value='more_images' {if $arr_confign_bc1.update_images == 'more_images'}selected{/if}>{l s='Add more Images' mod='ba_importer'}</option>
                    <option value='new_images' {if $arr_confign_bc1.update_images == 'new_images'}selected{/if}>{l s='Remove all Images before import' mod='ba_importer'}</option>
                </select>

            </div>*}
            
            <div class="form-group">

                <label>{l s='Encode' mod='ba_importer'}:</label>

                <select class="import_items" name="baencode">

                    <option value='utf8' {if $arr_confign_bc1.baencode == 'utf8'}selected{/if}>{l s='UTF-8' mod='ba_importer'}</option>

                    <option value='ansi' {if $arr_confign_bc1.baencode == 'ansi'}selected{/if}>{l s='ANSI' mod='ba_importer'}</option>

                </select>

            </div>
			
            <div class="form-group">

                <label>{l s='CSV Field Delimiter: ' mod='ba_importer'}</label>

                <div class="items_to_import">
                
                    <input id="characters_csv" type="text" class="form-control" name="characters_csv" value='{$arr_confign_bc1.characters_csv|escape:"htmlall":"UTF-8"}'/>

                </div>

            </div>

            <div class="form-group">

                <label>{l s='Category\'s Delimiter: ' mod='ba_importer'}</label>

                <div class="items_to_import">
                
                    <input id="characters_category" type="text" class="form-control" name="characters_category" value='{$arr_confign_bc1.characters_category|escape:"htmlall":"UTF-8"}'/>

                </div>

            </div>
			
            <div class="form-group">

                <label>{l s='Use the first row as headers' mod='ba_importer'}:</label>

                <div class="items_to_import">

                    <input type="radio" name="import_header" value="0" {if $arr_confign_bc1.import_header == '0'}checked{/if}> <span>{l s='Yes' mod='ba_importer'} </span>

                    <input type="radio" name="import_header" value="1" {if $arr_confign_bc1.import_header == '1'}checked{/if} > <span>{l s='No' mod='ba_importer'} </span>

                </div>

            </div>

            <div class="form-group">
                <label>{l s='Multi languages' mod='ba_importer'}:</label>
                <div class="items_to_import">
                    <input type="radio" name="multi_lang" value="0" {if $arr_confign_bc1.multi_lang == '0'}checked{/if}> <span>{l s='Yes' mod='ba_importer'} </span>

                    <input type="radio" name="multi_lang" value="1" {if $arr_confign_bc1.multi_lang == '1'}checked{/if} > <span>{l s='No' mod='ba_importer'} </span>
                </div>
            </div>        
            <div class="form-group">
                <label>{l s='Remove Combination if Combiantion quantity = 0' mod='ba_importer'}:</label>
                <div class="items_to_import">
                    <input type="radio" name="combi_quanti" value="0" {if $arr_confign_bc1.combi_quanti == '0'}checked{/if}> <span>{l s='Yes' mod='ba_importer'} </span>
                    <input type="radio" name="combi_quanti" value="1" {if $arr_confign_bc1.combi_quanti == '1'}checked{/if} > <span>{l s='No' mod='ba_importer'} </span>
                </div>

            </div>          
            <div class="form-group">
                <label>{l s='Add New Categories if Categories not exist' mod='ba_importer'}:</label>
                <div class="items_to_import">
                    <input type="radio" name="cate_exist" value="0" {if $arr_confign_bc1.cate_exist == '0'}checked{/if}> <span>{l s='Yes' mod='ba_importer'} </span>
                    <input type="radio" name="cate_exist" value="1" {if $arr_confign_bc1.cate_exist == '1'}checked{/if} > <span>{l s='No' mod='ba_importer'} </span>
                </div>
            </div>           
            <div class="form-group">
                <label>{l s='Add New Manufacturer if Manufacturer not exist' mod='ba_importer'}:</label>
                <div class="items_to_import">
                    <input type="radio" name="manu_exist" value="0" {if $arr_confign_bc1.manu_exist == '0'}checked{/if}> <span>{l s='Yes' mod='ba_importer'} </span>
                    <input type="radio" name="manu_exist" value="1" {if $arr_confign_bc1.manu_exist == '1'}checked{/if} > <span>{l s='No' mod='ba_importer'} </span>
                </div>
            </div>            
            <div class="form-group">
                <label>{l s='Add New Suppliers if Suppliers not exist' mod='ba_importer'}:</label>
                <div class="items_to_import">
                    <input type="radio" name="sup_exist" value="0" {if $arr_confign_bc1.sup_exist == '0'}checked{/if}> <span>{l s='Yes' mod='ba_importer'} </span>
                    <input type="radio" name="sup_exist" value="1" {if $arr_confign_bc1.sup_exist == '1'}checked{/if} > <span>{l s='No' mod='ba_importer'} </span>
                </div>
            </div>			
            <div class="form-group">
                <label>{l s='Create Feature as Customize Feature if not exist' mod='ba_importer'}:</label>
                <div class="items_to_import">
                    <input type="radio" name="fea_exist" value="0" {if $arr_confign_bc1.fea_exist == '0'}checked{/if}> <span>{l s='Yes' mod='ba_importer'} </span>
                    <input type="radio" name="fea_exist" value="1" {if $arr_confign_bc1.fea_exist == '1'}checked{/if} > <span>{l s='No' mod='ba_importer'} </span>
                </div>
            </div>
			<div class="form-group">
                <label>{l s='Process Products are not in File (csv, xls, xlsx)' mod='ba_importer'}:</label>
                <select class="import_items productsnotinfile" name="productsnotinfile">
                    <option value='- None -' {if $arr_confign_bc1.productsnotinfile == '- None -'}selected{/if}>{l s='- None -' mod='ba_importer'}</option>                   
                    <option value='disable' {if $arr_confign_bc1.productsnotinfile == 'disable'}selected{/if}>{l s='Disable' mod='ba_importer'}</option>                   
                    <option value='quantity' {if $arr_confign_bc1.productsnotinfile == 'quantity'}selected{/if}>{l s='Set Quantity = 0' mod='ba_importer'}</option>
                    <option value='delete' {if $arr_confign_bc1.productsnotinfile == 'delete'}selected{/if}>{l s='Delete products' mod='ba_importer'}</option>                 
                </select>
            </div>			
            <div class="form-group">
                <label>{l s='Items to import' mod='ba_importer'}:</label>
                <div class="items_to_import">
                    <input type="radio" name="import_items" id="import_all" value="All"  {if $arr_confign_bc1.import_items == 'All'}checked{/if}> <span>{l s='All' mod='ba_importer'}</span>
                    <input type="radio" name="import_items" id="import_range" value="Range" {if $arr_confign_bc1.import_items == 'Range'}checked{/if}> <span>{l s='Range' mod='ba_importer'}</span>
                    <span>{l s=' - From product' mod='ba_importer'}</span>
                    <input type="text" class="form-control" name="product_start" value="{$arr_confign_bc1.product_start|escape:'htmlall':'UTF-8'}"/>
                    <span>{l s=' to ' mod='ba_importer'}</span>
                    <input type="text" class="form-control" name="product_end" value="{$arr_confign_bc1.product_end|escape:'htmlall':'UTF-8'}"/>
                </div>
            </div>
            <div class="form-group advance_button">
                <label></label>
                <div class="items_to_import">
                    <button type="submit" class="btn btn-default btn-sm btn-primary" name="submitimport" onclick="return basubmitimport()">{l s='Next step' mod='ba_importer'}</button>
                    <button type="submit" class="btn btn-default btn-sm" name="cancelimport">{l s='Cancel' mod='ba_importer'}</button>
                </div>               
            </div>

    </div>

</form>
