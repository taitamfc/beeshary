{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

{if $widget_selected}
	{$form}{* HTML form , no escape necessary *}
	 <script type="text/javascript">
		$('#widget_type').change( function(){
			location.href = '{html_entity_decode($action|escape:'html':'UTF-8')}&wtype='+$(this).val();
		} );
	</script>	
 {else}
 <div class="col-lg-12" style="padding:20px;">
		 {if $is_using_managewidget}
		<div class="col-lg-5">
		<h3>{l s='Only for Module leomanagewidgets' mod='leobootstrapmenu'}</h3> 
			{foreach $types as $widget => $text}
				{if $text.for == 'manage'}
					<div class="col-lg-6">
						<h4><a href="{html_entity_decode($action|escape:'html':'UTF-8')}&wtype={$widget|escape:'html':'UTF-8'}">{$text.label|escape:'html':'UTF-8'}</a></h4>
						<p><i>{$text.explain}{* HTML form , no escape necessary *}</i></p>	
					</div>
				{/if}	
			{/foreach} 
		</div>
		{/if}
		<div class="col-lg-6 col-lg-offset-1">
		{*
		<h3>{l s='For all module (leomanagewidget,leomenubootstrap, leomenusidebar)' mod='leobootstrapmenu'}</h3> 
		*}
			{foreach $types as $widget => $text}
				{if $text.for != 'manage'}
					<div class="col-lg-6">
						<h4><a href="{html_entity_decode($action|escape:'html':'UTF-8')}&wtype={$widget|escape:'html':'UTF-8'}">{$text.label|escape:'html':'UTF-8'}</a></h4>
						<p><i>{$text.explain}{* HTML form , no escape necessary *}</i></p>	
					</div>
				{/if}	
			{/foreach} 
		</div>
</div>		
{/if}
