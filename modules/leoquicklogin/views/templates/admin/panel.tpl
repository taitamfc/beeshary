{* 
* @Module Name: Leo Quick Login
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
*}

<div class="panel form-horizontal">
	<div class="form-group">					
		<div class="col-lg-1">
			<a class="megamenu-correct-module btn btn-success" href="{$url_admin}&success=correct&correctmodule=1">
				<i class="icon-AdminParentPreferences"></i>{l s='Correct module' mod='leoquicklogin'}
			</a>
		</div>
		<label class="control-label col-lg-5" style="text-align: left;">* {l s='Please backup the database before correct module' mod='leoquicklogin'}</label>							
	</div>
</div>


<div id="leoquicklogin-group">

	<div class="panel panel-default">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Leo Quick Login Global Config' mod='leoquicklogin'}</div>
		
		<div class="panel-content" id="leoquicklogin-setting">
			<ul class="nav nav-tabs leoquicklogin-tablist" role="tablist">
				<li class="nav-item{if $default_tab == '#fieldset_0'} active{/if}">
					<a class="nav-link" href="#fieldset_0" role="tab" data-toggle="tab">{l s='Quick Login' mod='leoquicklogin'}</a>
				</li>
				<li class="nav-item{if $default_tab == '#fieldset_1_1'} active{/if}">
					<a class="nav-link" href="#fieldset_1_1" role="tab" data-toggle="tab">{l s='Social Login' mod='leoquicklogin'}</a>
				</li>
				
			</ul>
			<div class="tab-content">
				{$globalform nofilter}{* HTML form , no escape necessary *}
			</div>
		</div>	

	</div>
		
</div>