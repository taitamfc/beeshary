{*
* 2007-2014 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if count($groups) && isset($groups)}

<div class="row">
	<div class="col-lg-6">
        <div class="portlet">

         <div class="portlet-body">
		<table class="table table-advance table-striped table-hover">
			<thead>
				<tr>
					<th class="fixed-width-xs">
						<span class="title_box">
							<input type="checkbox" name="checkme" id="checkme" onclick="checkDelBoxes(this.form, '{$group_input_name}[]', this.checked)" />
						</span>
					</th>
					<th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='inixframe'}</span></th>
					<th>
						<span class="title_box">
							{l s='Group name' mod='inixframe'}
						</span>
					</th>
				</tr>
			</thead>
			<tbody>
			{foreach $groups as $key => $group}
				<tr>
					<td>
						{assign var=id_checkbox value={$group_input_name}|cat:'_'|cat:$group['id_group']}
						<input type="checkbox" name="{$group_input_name}[]" class="{$group_input_name}" id="{$id_checkbox}" value="{$group['id_group']}" {if isset($fields_value[$id_checkbox]) AND  $fields_value[$id_checkbox]}checked="checked"{/if} />
					</td>
					<td>{$group['id_group']}</td>
					<td>
						<label for="{$id_checkbox}">{$group['name']}</label>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
         </div>
        </div>
	</div>
</div>
{else}
<p>
	{l s='No group created' mod='inixframe'}
</p>
{/if}