{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<div class="leo-widget" data-id_widget="{$id_widget}">
{if isset($images)}
    <div class="widget-images block">
            {if isset($widget_heading)&&!empty($widget_heading)}
            <h4 class="title_block">
                    {$widget_heading}
            </h4>
            {/if}
            <div class="block_content clearfix">
                            <div class="images-list clearfix">	
                            <div class="row">
                                    {foreach from=$images item=image name=images}
                                            <div class="image-item {if $columns == 5} col-md-2-4 {else} col-md-{12/$columns}{/if} col-xs-12">
                                                    <a class="fancybox" rel="leogallery{$id_btmegamenu_widgets}" href= "{$link->getImageLink($image.link_rewrite, $image.id_image, $thickimage)}">
                                                            <img class="replace-2x img-fluid{if isset($aplazyload) && $aplazyload} lazy{/if}" {if isset($aplazyload) && $aplazyload}data-src{else}src{/if}="{$link->getImageLink($image.link_rewrite, $image.id_image, $smallimage)}" alt=""/>
                                                    </a>
                                            </div>
                                    {/foreach}
                            </div>
                    </div>
            </div>
    </div>
{/if}
{if $show_widget_bo == 'admin'}
    <div class="w-name">
        <select name="inject_widget" class="inject_widget_name">
            {foreach from=$widgets item=w}
                <option value="{$w['key_widget']}">
                    {$w['name']}
                </option>
            {/foreach}
        </select>
    </div>
{/if}
</div>