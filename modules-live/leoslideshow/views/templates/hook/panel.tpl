{* 
* @Module Name: Leo Slideshow
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<div id="leoslideshow-group">

	<div class="panel panel-default">
		<div class="panel-heading"><i class="icon-cogs"></i> {$description}</div>
		
		<div class="panel-content" id="slideshowgeneralsetting">
			<ul class="nav nav-tabs leoslideshow-tablist" role="tablist">
				<li class="nav-item{if $default_tab == '#fieldset_0'} active{/if}">
					<a class="nav-link" href="#fieldset_0" role="tab" data-toggle="tab">{l s='General Setting' mod='leoslideshow'}</a>
				</li>
				<li class="nav-item{if $default_tab == '#fieldset_1_1'} active{/if}">
					<a class="nav-link" href="#fieldset_1_1" role="tab" data-toggle="tab">{l s='Image Setting' mod='leoslideshow'}</a>
				</li>
				<li class="nav-item{if $default_tab == '#fieldset_2_2'} active{/if}">
					<a class="nav-link" href="#fieldset_2_2" role="tab" data-toggle="tab">{l s='CSS Setting' mod='leoslideshow'}</a>
				</li>
				<li class="nav-item{if $default_tab == '#fieldset_3_3'} active{/if}">
					<a class="nav-link" href="#fieldset_3_3" role="tab" data-toggle="tab">{l s='Navigator and Direction' mod='leoslideshow'}</a>
				</li>
				<li class="nav-item{if $default_tab == '#fieldset_4_4'} active{/if}">
					<a class="nav-link" href="#fieldset_4_4" role="tab" data-toggle="tab">{l s='Timer Options' mod='leoslideshow'}</a>
				</li>
				<li class="nav-item{if $default_tab == '#fieldset_5_5'} active{/if}">
					<a class="nav-link" href="#fieldset_5_5" role="tab" data-toggle="tab">{l s='Animation' mod='leoslideshow'}</a>
				</li>
			</ul>
			<div class="tab-content">
				{$globalform}{* HTML form , no escape necessary *}
			</div>
		</div>	

	</div>
		
</div>