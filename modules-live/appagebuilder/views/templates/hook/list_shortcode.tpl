{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!DOCTYPE html>
<html>
	<head>	
		<style>			
			.list-sc-header
			{
				text-align: center;
			}
			
			.list-sc-header .btn-success
			{
				background: #5cb85c;
				border-color: #4cae4c;
				color: #fff;
				text-align: center;
				padding: 6px 12px;
				vertical-align: middle;
				border-radius: 4px;
				text-decoration: none;
			}
			
			.list-sc-header .btn-info
			{
				background: #5bc0de;
				border-color: #46b8da;
				color: #fff;
				text-align: center;
				padding: 6px 12px;
				vertical-align: middle;
				border-radius: 4px;
				text-decoration: none;
			}
			
			.list-sc-item
			{
				float: left;
				width: 30%;
				text-align: center;
				border: 1px solid #000;
				cursor: pointer;
				background: #337ab7;
				color: #fff;
				padding: 5px;
			}
			
			.list-sc-item:hover
			{
				background: #286090;
			}
			
			.list-sc-item.inactive
			{
				background: #f0ad4e;
			}
			
			.list-sc-item.inactive:hover
			{
				background: #ec971f;
			}
			
			.list-sc-content
			{
				margin-top: 20px;
				padding-left: 5%;
			}
			
			
		</style>
		<script type="text/javascript" src="{$js_dir}jquery/jquery-1.11.0.min.js"></script>
	</head>
	<body>
		<!--
		<div class="cssload-container">
			<div class="cssload-speeding-wheel"></div>
		</div>
		-->
		<div class="list-sc-header">
			<a target="_blank" class="sc-direct btn btn-info" href="{$shortcode_url}">{l s='List Shortcode' mod='appagebuilder'}</a>
			<a target="_blank" class="sc-direct btn btn-success" href="{$shortcode_url_add}">{l s='Create Shortcode' mod='appagebuilder'}</a>
		</div>
		{if count($list_shortcode) > 0}
			<div class="list-sc-content">
				{foreach from=$list_shortcode item=list_shortcode_item}
					<div class="list-sc-item{if !$list_shortcode_item['active']} inactive{/if}" data-shortcode-key="{$list_shortcode_item['shortcode_key']}">
						<div class="sc-name">
							{$list_shortcode_item['shortcode_name']}
						</div>
						<div class="sc-key">
							{$list_shortcode_item['shortcode_key']}
						</div>
						<div class="sc-status">
							(ID: {$list_shortcode_item['id_appagebuilder_shortcode']} - 
							{if $list_shortcode_item['active']}
								{l s='Active' mod='appagebuilder'}					
							{else}
								{l s='Inactive' mod='appagebuilder'}
							{/if}
							)
						</div>
					</div>
				{/foreach}
			</div>
		{/if}
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('.list-sc-item').click(function(){
					var shortcode_key = $(this).data('shortcode-key');
					var shortcode_txt = '[ApSC sc_key='+shortcode_key+'][/ApSC]';
					parent.tinyMCE.execCommand('mceInsertContent', false,shortcode_txt);
					parent.tinyMCE.activeEditor.windowManager.close();
				});
				
				$('.sc-direct').click(function(){
					window.open($(this).attr('href'), '_blank');
					parent.tinyMCE.activeEditor.windowManager.close();
				});
			});
		</script>
	</body>
</html>