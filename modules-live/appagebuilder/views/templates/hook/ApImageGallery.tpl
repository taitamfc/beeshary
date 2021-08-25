{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApImageGallery -->
<div class="{$formAtts.class} {$formAtts.form_id} widget ap-image-gallery">
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
    {if isset($images)}
    <div class="widget-images block{if isset($formAtts.class)} {$formAtts.class|escape:'html':'UTF-8'}{/if}">
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
                <div class="row show_image">
                 {foreach from=$images item=image name=images}
                    <div class="image-item {if $columns == 5} col-md-2-4 {else} col-md-{12/$columns|intval}{/if} col-xs-12">
                        <a class="fancybox" data-fancybox-group="apimagegallery{$formAtts.form_id|escape:'html':'UTF-8'}" href= "{$image|escape:'html':'UTF-8'}">
                            <img class="replace-2x img-fluid" src="{$image|escape:'html':'UTF-8'}" alt=""/>
                    	</a>
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>
                
        <div class="alert alert-danger image_error" style="display:none"></div>
        <div class="image-template" style="display:none">
            <div class="image-item {if $columns == 5} col-md-2-4 {else} col-md-{12/$columns|intval}{/if} col-xs-12">
                <a class="fancybox" data-fancybox-group="apimagegallery{$formAtts.form_id|escape:'html':'UTF-8'}" href= "{$image|escape:'html':'UTF-8'}">
                    <img class="replace-2x img-fluid" src="{$image|escape:'html':'UTF-8'}" alt=""/>
                </a>
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
    
{if isset($show_more_btn) && $show_more_btn}
    <button name="show_more" class="show_more" data-assign='{$assign}' data-show_number="{$show_number}">{l s='Load more' mod='appagebuilder' js=1}</button>
    
    <script type="text/javascript">
    ap_list_functions.push(function(){
        $('.{$formAtts.form_id} .show_more').on('click', function(){
            var show_number = $(this).attr('data-show_number');
            var assign = $(this).attr('data-assign');
            
{literal}
            $.ajax({
                headers: {"cache-control": "no-cache"},
                url: prestashop.urls.base_url + 'modules/appagebuilder/apajax.php' + '?rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "Json",
                data: {"widget": 'ApImageGallery', "show_number": show_number, "assign": assign},
{/literal}
                success: function(jsonData) {
                    if (jsonData.hasError)
                    {
                        var errors = '';
                        for(error in jsonData.errors)
                            //IE6 bug fix
                            if(error != 'indexOf')
                                errors += '<li>' + jsonData.errors[error] + '</li>';
                        $('.{$formAtts.form_id} .image_error').html('<ol>' + errors + '</ol>').show();
                    }else{

                        for(var image in jsonData.images)
                        {
                            var html = $('.{$formAtts.form_id} .image-template .image-item').clone();
                            $(html).find('a').attr('href', jsonData.images[image]);
                            $(html).find('img').attr('src', jsonData.images[image]);
                            $(html).appendTo('.{$formAtts.form_id} .show_image');
                        }

                        if(jsonData.show_number == '-1'){
                            $('.{$formAtts.form_id} .show_more').hide();
                        }else{
                            $('.{$formAtts.form_id} .show_more').attr('data-show_number', jsonData.show_number);
                        }
                    }
                }
            });
        });
    });
    </script>
{/if}
</div>