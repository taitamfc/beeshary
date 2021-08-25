{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApImageGalleryProduct -->
<div class="widget col-lg-12 col-md-6 col-sm-6 col-xs-6 col-sp-12">
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
    <!-- {$smallimage|escape:'html':'UTF-8'}     -->
    {if isset($images)}
    <div class="widget-images block">
        {if isset($formAtts.title)&&!empty($formAtts.title)}
        <h4 class="title_block">
            {$formAtts.title|escape:'html':'UTF-8'}
        </h4>
        {/if}
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
            <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
        {/if}
        <div class="block_content clearfix">
                <div class="images-list clearfix">    
                <div class="row">
                 {foreach from=$images item=image name=images}
                    <div class="image-item {if $columns == 5} col-md-2-4 {else} col-md-{12/$columns|intval}{/if} col-xs-12">
                        <a class="fancybox" rel="leogallery{$formAtts.form_id|escape:'html':'UTF-8'}" href= "{$link->getImageLink($image.link_rewrite, $image.id_image, $thickimage)|escape:'html':'UTF-8'}">
                            <img class="replace-2x img-fluid" src="{$link->getImageLink($image.link_rewrite, $image.id_image, $smallimage)|escape:'html':'UTF-8'}" alt=""/>
                    	</a>
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
    <script type="text/javascript">
        ap_list_functions.push(function(){
        $(".fancybox").fancybox({
            openEffect : 'none',
            closeEffect : 'none'
        });
    });
    </script>
    {/if} 
</div>