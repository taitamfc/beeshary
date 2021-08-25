{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<div class="group-input group-{$ap_cf} clearfix">
		<label class="col-sm-12 control-label">
				<i class="fa fa-tags"></i>
				{if $ap_cf == 'profile'}
						{l s='Home version' mod='appagebuilder'}
				{else if $ap_cf == 'header'}
						{l s='Header version' mod='appagebuilder'}
				{else if $ap_cf == 'footer'}
						{l s='Footer version' mod='appagebuilder'}
				{else if $ap_cf == 'product'}
						{l s='Product version' mod='appagebuilder'}
				{else if $ap_cf == 'content'}
						{l s='Content Home' mod='appagebuilder'}
				{else if $ap_cf == 'product_builder'}
						{l s='Product Builder' mod='appagebuilder'}
				{/if}
		</label>
		<div class="col-sm-12">
            {foreach from=$ap_cfdata item=cfdata}
                {if isset($cfdata.friendly_url) && $cfdata.friendly_url != '' && ( $ap_controller=='index' || $ap_controller=='appagebuilderhome')}
                    {* current_page is index.php, and this link is friendly_url  *}
                    <a class="apconfig apconfig-{$ap_cf}{if $cfdata.active} active{/if}" data-type="{$ap_type}" data-id='{$cfdata.id}' data-enableJS="false" href="{$ap_current_url}{$cfdata.friendly_url}.html">{$cfdata.name}</a>
                {else if ($ap_controller=='index' && $ap_cf=='profile')}
                    {* current_page is index.php (rewrite), and this link is id  *}
                    <a class="apconfig apconfig-{$ap_cf}{if $cfdata.active} active{/if}" data-type="{$ap_type}" data-id='{$cfdata.id}' data-enableJS="true" data-url="{$ap_current_url}" href="{$ap_current_url}{$cfdata.friendly_url}.html">{$cfdata.name}</a>
                {else}
                    <a class="apconfig apconfig-{$ap_cf}{if $cfdata.active} active{/if}" data-type="{$ap_type}" data-id='{$cfdata.id}' data-enableJS="true" href="index.php?{$ap_type}={$cfdata.id}">{$cfdata.name}</a>
                {/if}
            {/foreach}
		</div>
</div>