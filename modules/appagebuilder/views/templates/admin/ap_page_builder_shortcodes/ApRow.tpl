{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\admin\ap_page_builder_shortcodes\ApRow -->
<div {if !isset($apInfo)}id="default_row"{/if} class="row group-row{if isset($formAtts)} {$formAtts.form_id|escape:'html':'UTF-8'}{/if}{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}">
    <div class="group-controll-left pull-left">
		<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag to sort group' mod='appagebuilder'}" class="group-action gaction-drag label-tooltip"><i class="icon-move"></i> </a>
		&nbsp;
		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span>{l s='Group' mod='appagebuilder'}</span> <span class="caret"></span>
			</button>
			<ul class="dropdown-menu for-group-row" role="menu">
				<li><a href="javascript:void(0)" title="{l s='Edit Group' mod='appagebuilder'}" class="group-action btn-edit" data-type="ApRow"><i class="icon-edit"></i> {l s='Edit Group' mod='appagebuilder'}</a></li>
				<li><a href="javascript:void(0)" title="{l s='Delete Group' mod='appagebuilder'}"  class="group-action btn-delete"><i class="icon-trash"></i> {l s='Delete Group' mod='appagebuilder'}</a></li>
				<li><a href="javascript:void(0)" title="{l s='Export Group' mod='appagebuilder'}" class="group-action btn-export" data-type="group"><i class="icon-cloud-download"></i> {l s='Export Group' mod='appagebuilder'}</a></li>
				<li><a href="javascript:void(0)" title="{l s='Duplicate Group' mod='appagebuilder'}" class="group-action btn-duplicate "><i class="icon-paste"></i> {l s='Duplicate Group' mod='appagebuilder'}</a></li>
				<li><a href="javascript:void(0)" title="{l s='Disable or Enable Group' mod='appagebuilder'}" class="group-action btn-status {if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}"><i class="icon-ok"></i> {l s='Disable or Enable Group' mod='appagebuilder'}</a></li>
			</ul>			
		</div>
		<div class="btn-group animation-section">		
			<button type="button" class="btn btn-default animation-button">
				<i class="icon-magic"></i>
				<span class="animation-status" data-text-default="{l s='Animation' mod='appagebuilder'}" data-text-infinite="{l s='Infinite' mod='appagebuilder'}">{l s='Animation' mod='appagebuilder'}</span>
			</button>
			<div class="form-horizontal animation-wrapper group-animation-wrapper">				
				<div class="form-group">
					<label class="control-label col-lg-5">
						{l s='Select Animation' mod='appagebuilder'}
					</label>
					<div class="col-lg-7">
						<select name="animation" class="animation_select fixed-width-xl">
							{if isset($listAnimation)}
								{foreach $listAnimation as $listAnimation_val}
									<optgroup label="{$listAnimation_val.name}">
										{foreach $listAnimation_val.query as $option}
											<option value="{$option.id}">{$option.name}</option>
										{/foreach}
									</optgroup>
								{/foreach}
							{/if}
						</select>
					</div>
				</div>
				<div class="form-group animate_sub">
					<div class="col-lg-10 col-lg-offset-2">
						<div class="animationSandbox">Prestashop.com</div>								
					</div>
				</div>
				<div class="form-group animate_sub">
					<label class="control-label col-lg-5">
						{l s='Delay' mod='appagebuilder'} ({l s='Default' mod='appagebuilder'}: 1)
					</label>
					<div class="col-lg-7">						
						<div class="input-group fixed-width-xs">
							<input name="animation_delay" value="1" class="fixed-width-xs animation_delay" type="text">
							<span class="input-group-addon">{l s='s' mod='appagebuilder'}</span>							
						</div>						
					</div>
				</div>
				<div class="form-group animate_sub">
					<label class="control-label col-lg-5">
						{l s='Duration' mod='appagebuilder'} ({l s='Default' mod='appagebuilder'}: 1)
					</label>
					<div class="col-lg-7">						
						<div class="input-group fixed-width-xs">
							<input name="animation_duration" value="1" class="fixed-width-xs animation_duration" type="text">
							<span class="input-group-addon">{l s='s' mod='appagebuilder'}</span>							
						</div>						
					</div>
				</div>
				<div class="form-group animate_sub animate_loop">
					<label class="control-label col-lg-5">
						{l s='Iteration count' mod='appagebuilder'} ({l s='Default' mod='appagebuilder'}: 1)
					</label>
					<div class="col-lg-7">						
						<div class="input-group fixed-width-xs">
							<input name="animation_iteration_count" value="1" class="fixed-width-xs animation_iteration_count" type="text">
							<span class="input-group-addon">{l s='times' mod='appagebuilder'}</span>							
						</div>							
					</div>
				</div>
				<div class="form-group animate_sub">
					<div class="col-lg-7 col-lg-offset-5">
						<div class="checkbox">
							<label for="animation_infinite">
								<input name="animation_infinite" class="checkbox-group animation_infinite" value="1" type="checkbox">{l s='Infinite' mod='appagebuilder'}
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-12">
						<button type="button" class="btn btn-primary pull-right btn-save-animation">{l s='Save' mod='appagebuilder'}</button>
						<button type="button" class="btn btn-default pull-right animate-it animate_sub">{l s='Animate demo' mod='appagebuilder'}</button>						
					</div>
				</div>			
			</div>
		</div>	
    </div>
    <div class="group-controll-right pull-right">
        <a href="javascript:void(0)" title="{l s='Add New Column' mod='appagebuilder'}" class="group-action btn-add-column" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus"><i class="icon-plus"></i></a>
        <a href="javascript:void(0)" title="{l s='Set width for all column' mod='appagebuilder'}" class="group-action btn-custom" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" ><i class="icon-th"></i></a>
        <a href="javascript:void(0)" title="{l s='Expand or collapse Group' mod='appagebuilder'}" class="group-action gaction-toggle label-tooltip"><i class="icon-circle-arrow-down"></i></a>
    </div>
    <div class="group-content">
        {if isset($apInfo)}{$apContent}{* HTML form , no escape necessary *}{/if}
    </div>
</div>