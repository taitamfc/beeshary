{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<div class="panel form-horizontal">
	<div class="form-group">					
		<div class="col-lg-1">
			<a class="megamenu-correct-module btn btn-success" href="{$url_admin}&success=correct&correctmodule=1">
				<i class="icon-AdminParentPreferences"></i>{l s='Correct module' mod='leofeature'}
			</a>
		</div>
		<label class="control-label col-lg-3">* {l s='Please backup the database before run correct module to safe' mod='leofeature'}</label>							
	</div>
</div>

<div id="leofeature-group">

	<div class="panel panel-default">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Leo Feature Global Config' mod='leofeature'}</div>
		
		<div class="panel-content" id="leofeature-setting">
			<ul class="nav nav-tabs leofeature-tablist" role="tablist">
				<li class="nav-item{if $default_tab == '#fieldset_0'} active{/if}">
					<a class="nav-link" href="#fieldset_0" role="tab" data-toggle="tab">{l s='Ajax Cart' mod='leofeature'}</a>
				</li>
				<li class="nav-item{if $default_tab == '#fieldset_1_1'} active{/if}">
					<a class="nav-link" href="#fieldset_1_1" role="tab" data-toggle="tab">{l s='Product Review' mod='leofeature'}</a>
				</li>
				<li class="nav-item{if $default_tab == '#fieldset_2_2'} active{/if}">
					<a class="nav-link" href="#fieldset_2_2" role="tab" data-toggle="tab">{l s='Product Compare' mod='leofeature'}</a>
				</li>
				<li class="nav-item{if $default_tab == '#fieldset_3_3'} active{/if}">
					<a class="nav-link" href="#fieldset_3_3" role="tab" data-toggle="tab">{l s='Product Wishlist' mod='leofeature'}</a>
				</li>
				
			</ul>
			<div class="tab-content">
				{$globalform}{* HTML form , no escape necessary *}
			</div>
		</div>	

	</div>
		
</div>