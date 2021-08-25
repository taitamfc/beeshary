{*
* 2007-2019 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="col-xs-12">
	
	<div class="panel">
		<div class="panel-heading">			
			<i class="icon icon-list"></i> {l s='Blacklist email domains' mod='invisiblegrecaptcha'}
		</div>

		<form class="form-inline" action="#">
		  	<div class="form-group">
		    	<label for="domain">{l s='Domain' mod='invisiblegrecaptcha'}:</label>
		    	<input type="text" class="form-control" id="domain" />
		  	</div>
		  	<button type="submit" class="btn btn-primary" id="btn-add-domain">{l s='Add' mod='invisiblegrecaptcha'}</button>
		  	<img src="../img/loader.gif" id="add-domain-loader" style="display: none;">		  	
		</form>

		<table class="table table-borderless" id="table-blacklist-domains">
			<thead>
				<tr>
					<th style="width: 88%">{l s='Domain' mod='invisiblegrecaptcha'}</th>
					<th style="width: 12%">{l s='Action' mod='invisiblegrecaptcha'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$domains item=domain}
					<tr>
						<td>
							<input type="text" value="{$domain.domain}" />
						</td>
						<td>
							<button class="btn btn-primary update-domain" data-domain-id="{$domain.id_grecaptcha_domain}">
								{l s='Update' mod='invisiblegrecaptcha'}
							</button>
							<button class="btn btn-primary delete-domain" data-domain-id="{$domain.id_grecaptcha_domain}">
								<i class="icon icon-trash"></i>
							</button>
						</td>
					</tr>
				{/foreach}								
			</tbody>
		</table>
	</div>
	
</div>

{literal}
<script type="text/javascript">

	var grecaptcha_ajax_link = "{/literal}{$ajax_action_link}{literal}";
	var grecaptcha_update_txt = "{/literal}{l s='Update' mod='invisiblegrecaptcha'}{literal}";
	var grecaptcha_add_success_txt = "{/literal}{l s='Added successfully' mod='invisiblegrecaptcha'}{literal}";
	var grecaptcha_update_success_txt = "{/literal}{l s='Update successful' mod='invisiblegrecaptcha'}{literal}";
	var grecaptcha_delete_success_txt = "{/literal}{l s='Deleted successfully' mod='invisiblegrecaptcha'}{literal}";
	var grecaptcha_domain_required_txt = "{/literal}{l s='Domain can not be empty' mod='invisiblegrecaptcha'}{literal}";
	var grecaptcha_delete_conf_txt = "{/literal}{l s='Delete the selected item?' mod='invisiblegrecaptcha'}{literal}";
	var grecaptcha_get_key_txt = "{/literal}{l s='Get your Google reCaptcha API Key pair' mod='invisiblegrecaptcha'}{literal}";

	$(document).ready(function() {

		$('form#module_default_form .form-wrapper').append('<p><a href="https://www.google.com/recaptcha/admin" target="_blank">' + grecaptcha_get_key_txt + '</a></p>');

		if ($('#GRECAPTCHA_PREVIEW').length && typeof grecaptcha != 'undefined') {
			$('#GRECAPTCHA_PREVIEW').replaceWith('<div id="GRECAPTCHA_PREVIEW"></div>');
		}

		$(document).on('click', '#btn-add-domain', function(e) {			
			e.preventDefault();
			if ($('#domain').val().trim().length == 0) {
				showErrorMessage(grecaptcha_domain_required_txt);
				return false;
			}
			$('#add-domain-loader').show();
			var btn = $(this);
			$(btn).attr('disabled', true);
			$.post(grecaptcha_ajax_link, {action: 'add-domain', domain: $('#domain').val()}, function(response) {
				showSuccessMessage(grecaptcha_add_success_txt);
				$('#add-domain-loader').hide();
				$('#domain').val('');
				$(btn).removeAttr('disabled');
				refreshDomains(response);				
			});
		});

		$(document).on('click', '.update-domain', function(e) {
			var btn = $(this);
			$(btn).attr('disabled', true);
			e.preventDefault();			
			$.post(grecaptcha_ajax_link, {action: 'update-domain', domain: $(this).closest('tr').find('input').val(), id_domain: $(this).data('domain-id')}, function(response) {
				showSuccessMessage(grecaptcha_update_txt);
				$(btn).removeAttr('disabled');
				refreshDomains(response);
			});
		});

		$(document).on('click', '.delete-domain', function(e) {
			var conf = confirm(grecaptcha_delete_conf_txt);

			if (!conf) {
				return false;
			}

			$(this).attr('disabled', true);
			e.preventDefault();			
			$.post(grecaptcha_ajax_link, {action: 'delete-domain', id_domain: $(this).data('domain-id')}, function(response) {
				showSuccessMessage(grecaptcha_delete_success_txt);
				refreshDomains(response);
			});
		});
	});

	$(window).load(function() {		
		if ($('#GRECAPTCHA_PREVIEW').length && typeof grecaptcha != 'undefined') {			
			grecaptcha.render("GRECAPTCHA_PREVIEW", {sitekey: $('#GRECAPTCHA_SITE_KEY').val()});	
		}
	});

	function refreshDomains(data) {
		$('#table-blacklist-domains tbody').html('');
		var domains = JSON.parse(data);
		$.each(domains, function(i, v) {
			var row = '<tr><td><input type="text" value="' + v.domain +'" /></td><td><button class="btn btn-primary update-domain" data-domain-id="'+ v.id_grecaptcha_domain +'">  '+grecaptcha_update_txt+' </button> <button class="btn btn-primary delete-domain" data-domain-id="'+ v.id_grecaptcha_domain +'"><i class="icon icon-trash"></i></button></td></tr>';
			$('#table-blacklist-domains tbody').append(row);
		});
	}
</script>
{/literal}