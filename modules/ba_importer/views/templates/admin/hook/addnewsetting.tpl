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
    <div class='advance_content panel' class="col-lg-12">
		<h3>{l s='Create New Setting' mod='ba_importer'}</h3>
        <div class="form-group">
			<label>{l s='Name Settings' mod='ba_importer'}: </label>
			<div class="items_to_import">
				<input id="characters_csv" type="text" name="characters_csv">
			</div>
		</div>
		<div class="form-group advance_button">
			<label></label>
			<div class="items_to_import">
				<button type="submit" class="btn btn-default btn-sm" name="cancelimport">{l s='Cancel' mod='ba_importer'}</button>
			</div>
		</div>
    </div>
</form>