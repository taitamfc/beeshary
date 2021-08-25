{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApBlockCarousel -->
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning leo-lib-error">{$formAtts.lib_error}</div>
    {/if}
{else}
    <div class="block block_carousel exclusive appagebuilder {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}">
        {($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
        <div class="block_content">
            {if !empty($formAtts.slides)}
                {if $formAtts.carousel_type == "slickcarousel"}
                    {assign var=leo_include_file value=$leo_helper->getTplTemplate('ApBlockSlickCarouselItem.tpl', $formAtts['override_folder'])}
                    {include file=$leo_include_file}
                {else}
                    {if $formAtts.carousel_type == 'boostrap'}
                        {assign var=leo_include_file value=$leo_helper->getTplTemplate('ApBlockCarouselItem.tpl', $formAtts['override_folder'])}
                        {include file=$leo_include_file}
                    {else}
                        {assign var=leo_include_file value=$leo_helper->getTplTemplate('ApBlockOwlCarouselItem.tpl', $formAtts['override_folder'])}
                        {include file=$leo_include_file}
                    {/if}
                {/if}
            {else}
                <p class="alert alert-info">{l s='No slide at this time.' mod='appagebuilder'}</p>
            {/if}
        </div>
        {($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
    </div>
{/if}