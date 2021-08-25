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
*  @author Buy-addons <contact@buy-addons.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<select class='form-control advance_select' name='select[{$key|escape:"htmlall":"UTF-8"}]'>
    <option value=''>{l s='- None -' mod='ba_importer'}</option>
    <optgroup label="{l s='Product' mod='ba_importer'}">
   
    <optgroup label="{l s='Features' mod='ba_importer'}">
    </optgroup>
    <optgroup label="{l s='Customization' mod='ba_importer'}">
    </optgroup>
    <optgroup label="{l s='Attachments' mod='ba_importer'}">
    </optgroup>
    <optgroup label="{l s='Attributes' mod='ba_importer'}">
        {foreach from=$ba_combination item=row}
            <option value='{$combination_group_id|escape:"htmlall":"UTF-8"}' {if $config_select_importer == $combination_group_id}selected{/if}>{$row.name|escape:"htmlall":"UTF-8"}</option>
        {/foreach}
    </optgroup>
    <optgroup label="{l s='Combination' mod='ba_importer'}">     
    </optgroup>
    <optgroup label="{l s='Warehouses' mod='ba_importer'}">
        {foreach from=$ba_warehouse item=row}       
            <option value='{$warehouse_id|escape:"htmlall":"UTF-8"}' disabled>{$row.name|escape:"htmlall":"UTF-8"}</option>
    </optgroup> 
        <option value='delete_product' {if $config_select_importer == 'delete_product'}selected{/if}>{l s='Delete existed Product (0, N, No = No; 1, Y, Yes = Yes; Default: No)' mod='ba_importer'}</option>
    </optgroup>
</select>