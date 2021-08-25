{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
 <!-- @file modules\appagebuilder\views\templates\hook\ApTabs -->
{if $tab_name == 'ApTabs'}
<script type="text/javascript">
    ap_list_functions.push(function(){
        {if isset($formAtts.fade_effect) && $formAtts.fade_effect}
            // ACTION USE EFFECT
            $("#{$formAtts.id|escape:'html':'UTF-8'} .tab-pane").addClass("fade");
        {/if}
            
        {if $formAtts.active_tab >= 0}
            // ACTION SET ACTIVE
            $('#{$formAtts.id|escape:'html':'UTF-8'} .nav a:eq({$formAtts.active_tab|escape:'html':'UTF-8'})').trigger('click');
        {/if}
    });
</script>
<div{if isset($formAtts.id)} id="{$formAtts.id|escape:'html':'UTF-8'}"{/if} class="ApTabs {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}">

	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}

    {if isset($formAtts.title) && $formAtts.title}
    <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
    {/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
    {if $formAtts.tab_type =='tabs-left'}
        <div class="block_content">
            <ul class="nav nav-tabs col-md-3" role="tablist">
                {foreach from=$subTabContent item=subTab}
                    <li class="nav-item {(isset($subTab.css_class)) ? $subTab.css_class : ''|escape:'html':'UTF-8'}">
                        <a href="#{$subTab.id|escape:'html':'UTF-8'}" class="nav-link {$subTab.form_id|escape:'html':'UTF-8'}" role="tab" data-toggle="tab">
                            <span>{$subTab.title|escape:'html':'UTF-8'}</span>
                            {if isset($subTab.sub_title) && $subTab.sub_title}
                                <div class="sub-title-widget">{$subTab.sub_title nofilter}</div>
                            {/if}
                            {if isset($subTab.image) && $subTab.image}<img class="img-fluid" src="{$path}{$subTab.image|escape:'html':'UTF-8'}" alt="{$subTab.title}"/>{/if}
                        </a>
                    </li>
                {/foreach}
            </ul>
            <div class="tab-content col-md-9">
                {$apContent nofilter}{* HTML form , no escape necessary *}
            </div>
        </div>
    {/if}
    {if $formAtts.tab_type =='tabs-right'}
        <div class="block_content">
            <div class="tab-content col-md-9">
                {$apContent nofilter}{* HTML form , no escape necessary *}
            </div>
            <ul class="nav nav-tabs col-md-3" role="tablist">
                {foreach from=$subTabContent item=subTab}
                    <li class="nav-item {(isset($subTab.css_class)) ? $subTab.css_class : ''|escape:'html':'UTF-8'}">
                        <a href="#{$subTab.id|escape:'html':'UTF-8'}" class="nav-link {$subTab.form_id|escape:'html':'UTF-8'}" role="tab" data-toggle="tab">
                            <span>{$subTab.title|escape:'html':'UTF-8'}</span>
                            {if isset($subTab.sub_title) && $subTab.sub_title}
                                <div class="sub-title-widget">{$subTab.sub_title nofilter}</div>
                            {/if}
                            {if isset($subTab.image) && $subTab.image}<img class="img-fluid" src="{$path}{$subTab.image|escape:'html':'UTF-8'}" alt="{$subTab.title}"/>{/if}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}
    {if $formAtts.tab_type =='tabs-below'}
        <div class="block_content">
            <div class="tab-content">
                {$apContent nofilter}{* HTML form , no escape necessary *}
            </div>
            <ul class="nav nav-tabs" role="tablist">
                {foreach from=$subTabContent item=subTab}
                    <li class="nav-item {(isset($subTab.css_class)) ? $subTab.css_class : ''|escape:'html':'UTF-8'}">
                        <a href="#{$subTab.id|escape:'html':'UTF-8'}" class="nav-link {$subTab.form_id|escape:'html':'UTF-8'}" role="tab" data-toggle="tab">
                            <span>{$subTab.title|escape:'html':'UTF-8'}</span>
                            {if isset($subTab.sub_title) && $subTab.sub_title}
                                <div class="sub-title-widget">{$subTab.sub_title nofilter}</div>
                            {/if}
                            {if isset($subTab.image) && $subTab.image}<img class="img-fluid" src="{$path}{$subTab.image|escape:'html':'UTF-8'}" alt="{$subTab.title}"/>{/if}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}
    {if $formAtts.tab_type =='tabs-top'}
        <div class="block_content">
            <ul class="nav nav-tabs" role="tablist">
                {foreach from=$subTabContent item=subTab}
                    <li class="nav-item {(isset($subTab.css_class)) ? $subTab.css_class : ''|escape:'html':'UTF-8'}">
                        <a href="#{$subTab.id|escape:'html':'UTF-8'}" class="nav-link {$subTab.form_id|escape:'html':'UTF-8'}" role="tab" data-toggle="tab">
                            <span>{$subTab.title|escape:'html':'UTF-8'}</span>
                            {if isset($subTab.sub_title) && $subTab.sub_title}
                                <div class="sub-title-widget">{$subTab.sub_title nofilter}</div>
                            {/if}
                            {if isset($subTab.image) && $subTab.image}<img class="img-fluid" src="{$path}{$subTab.image|escape:'html':'UTF-8'}" alt="{$subTab.title}"/>{/if}
                        </a>
                    </li>
                {/foreach}
            </ul>
            <div class="tab-content">
                {$apContent nofilter}{* HTML form , no escape necessary *}
            </div>
        </div>
    {/if}

	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
</div>
{/if}
{if $tab_name == 'ApTab'}
    <div id="{$tabID|escape:'html':'UTF-8'}" class="tab-pane">
{*        {if isset($formAtts.image) && $formAtts.image}<img class="img-fluid" src="{$path}{$formAtts.image|escape:'html':'UTF-8'}" alt="{$formAtts.title}"/>{/if}
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
            <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
        {/if}
*}
        {$apContent nofilter}{* HTML form , no escape necessary *}
    </div>
{/if}
