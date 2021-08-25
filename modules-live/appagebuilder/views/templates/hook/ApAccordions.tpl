{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApAccordions -->
{if isset($isWrapper) && $isWrapper}
{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
<input type="hidden" class="runCodeJs ap-accordion" 
       data-active_type="{$formAtts.active_type}"
       data-id="{$formAtts.id|escape:'html':'UTF-8'}"
       data-active_accordion="{$formAtts.active_accordion|escape:'html':'UTF-8'}"
    />
<div{if isset($formAtts.id)} id="{$formAtts.id|escape:'html':'UTF-8'}"{/if} class="ap-accordion panel-group {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}">
    {if isset($formAtts.title) && $formAtts.title}
    <h4 class="title_block">{$formAtts.title|rtrim|escape:'html':'UTF-8'}</h4>
    {/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
    {$apContent nofilter}{* HTML form , no escape necessary *}
</div>
{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}

<script type="text/javascript">
ap_list_functions.push(function(){
    
    {if $formAtts.active_type=='set'}
        // ACTION SET ACTIVE
        $('#{$formAtts.id|escape:'html':'UTF-8'} .panel-default:nth-child({$formAtts.active_accordion|escape:'html':'UTF-8'}) .panel-heading .panel-title a').trigger('click');
    {/if}
    
    {if $formAtts.active_type=='showall'}
        // ACTION SHOWALL
        $('#{$formAtts.id|escape:'html':'UTF-8'} .panel-heading .panel-title > a').on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            // show, hidden content
            var div_id = $(this).attr('href');
            $(div_id ).collapse("toggle");
        });
        $('#{$formAtts.id|escape:'html':'UTF-8'} .panel-heading .panel-title > a').trigger('click');
    {/if}

});

</script>

{else}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#{$formAtts.parent_id|escape:'html':'UTF-8'}" aria-controls="{$formAtts.id|escape:'html':'UTF-8'}" aria-expanded="false" class="collapsed"
                       href="#{$formAtts.id|escape:'html':'UTF-8'}">{$formAtts.title|escape:'html':'UTF-8'}</a>
                </h4>
                {if isset($formAtts.sub_title) && $formAtts.sub_title}
                    <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
                {/if}
            </div>
            <div id="{$formAtts.id|escape:'html':'UTF-8'}" class="panel-collapse collapse" role="tabpanel">
                <div class="panel-body">
                    {$apContent nofilter}{* HTML form , no escape necessary *}
                </div>
            </div>
        </div> 
{/if}