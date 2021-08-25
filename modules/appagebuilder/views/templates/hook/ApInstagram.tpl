{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApInstagram -->
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning leo-lib-error">{$formAtts.lib_error}</div>
    {/if}
{else}
    
    {if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}{* Default : always full *}
    <div class="block {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'} instagram-block">
        {($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
        {if isset($formAtts.title) && $formAtts.title}
        <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
        {/if}
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
            <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
        {/if}
        {if isset($formAtts.client_id) && $formAtts.client_id}
        <div class="block_content">
            <div id="instafeed"></div>
            <p class="link-instagram">
            {if isset($formAtts.profile_link) && $formAtts.profile_link}
                <a href="https://instagram.com/{$formAtts.profile_link|escape:'html':'UTF-8'}" title="{l s='View all in instagram' mod='appagebuilder'}"><i class="fa fa-instagram"></i>&nbsp;&nbsp;{l s='View all in instagram' mod='appagebuilder'}</a>
            {/if}
            </p>
        </div>
        {/if}
        {($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
    </div>

{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen')}{* Case : full or accordion*}
    <div class="block {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'} instagram-block block-toggler{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{/if}">
        {($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
        {if isset($formAtts.title) && $formAtts.title}
            <div class="title clearfix" data-target="#widget-instagram-{$formAtts.form_id|escape:'html':'UTF-8'}" data-toggle="collapse">
                <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
                <span class="float-xs-right">
                  <span class="navbar-toggler collapse-icons">
                    <i class="material-icons add">&#xE313;</i>
                    <i class="material-icons remove">&#xE316;</i>
                  </span>
                </span>
            </div>
        {/if}
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
            <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
        {/if}
        {if isset($formAtts.client_id) && $formAtts.client_id}
        <div class="collapse block_content" id="widget-instagram-{$formAtts.form_id|escape:'html':'UTF-8'}">
            <div id="instafeed"></div>
            <p class="link-instagram">
            {if isset($formAtts.profile_link) && $formAtts.profile_link}
                <a href="https://instagram.com/{$formAtts.profile_link|escape:'html':'UTF-8'}" title="{l s='View all in instagram' mod='appagebuilder'}"><i class="fa fa-instagram"></i>&nbsp;&nbsp;{l s='View all in instagram' mod='appagebuilder'}</a>
            {/if}
            </p>
        </div>
        {/if}
        {($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
    </div>
    {/if}

        
<script type="text/javascript">
    ap_list_functions.push(function(){
            var feed = new Instafeed({
               clientId: "{$formAtts.client_id|escape:'html':'UTF-8'}",
    {if isset($formAtts.access_token) && $formAtts.access_token}
               accessToken: "{$formAtts.access_token|escape:'html':'UTF-8'}",
    {/if}
    {if isset($formAtts.user_id) && $formAtts.user_id}
               userId: {$formAtts.user_id|escape:'html':'UTF-8'},
    {/if}
    {if isset($formAtts.get) && $formAtts.get}
               get: "{$formAtts.get|escape:'html':'UTF-8'}",
    {/if}
    {if isset($formAtts.sort_by) && $formAtts.sort_by}
               sortBy: "{$formAtts.sort_by|escape:'html':'UTF-8'}",
    {/if}
    {if isset($formAtts.limit) && $formAtts.limit}
               limit: "{$formAtts.limit|intval}",
    {/if}
    {if isset($formAtts.resolution) && $formAtts.resolution}
               resolution: "{$formAtts.resolution|escape:'html':'UTF-8'}",
    {/if}
    {if isset($formAtts.target) && $formAtts.target}
               target: "{$formAtts.target|escape:'html':'UTF-8'}",
    {/if}
    {if isset($formAtts.template) && $formAtts.template}
               template: "{$formAtts.template|escape:'html':'UTF-8'}",
    {/if}
    {if isset($formAtts.tag_name) && $formAtts.tag_name}
               tagName: "{$formAtts.tag_name|escape:'html':'UTF-8'}",
    {/if}
    {if isset($formAtts.location_id) && $formAtts.location_id}
               locationId: {$formAtts.get|escape:'html':'UTF-8'},
    {/if}
    {if isset($formAtts.links) && $formAtts.links}
               links: "{$formAtts.links}{*full link can not validate*}",
    {/if}    

            {if isset($formAtts.carousel_type) && $formAtts.carousel_type !== "list"}
                after: function() {
                    {if isset($formAtts) && isset($formAtts.lazyload) && $formAtts.lazyload}
                        {* ENABLE LAZY LOAD OWL_CAROUSEL *}
                        var html = $("#instafeed").html();
                        html = html.replace(/ src="/g,' class="lazyOwl" data-src="');
                        $("#instafeed").html(html);
                    {/if}
                        
                    {if $formAtts.carousel_type == "boostrap"}

                    {else}
                        {if isset($formAtts.itempercolumn) && $formAtts.itempercolumn > 1}
                            // CASE : 2,3 images in one column
                            var photos = [];
                            $("#instafeed img").each(function() {
                                {*photos.push( $(this).wrap('<p/>').parent().html());*}
                                {*photos.push( $(this).get(0).outerHTML);*}
                                photos.push( $(this).parent().prop('outerHTML'));
                            });
                            $("#instafeed").html('');
                            $("#instafeed").addClass('owl-loading');

                            var itempercolumn = {$formAtts.itempercolumn};

                            var photos = array_chunk(photos,itempercolumn);
                            var total_column = photos.length;

                            for (i = 0; i < total_column; i++)
                            {
                                if(i == 0){
                                    var img_html = '<div class="item first">';
                                }else{
                                    var img_html = '<div class="item">';
                                }

                                for(j=0; j<photos[i].length; j++)
                                {
                                    img_html += '<div class="block-carousel-container">';
                                    img_html += '   <div class="left-block">';
                                    img_html += '       <div class="block-carousel-image-container image">';

                                    img_html += photos[i][j];

                                    img_html += '       </div>';
                                    img_html += '   </div>';
                                    img_html += '</div>';
                                }

                                $("#instafeed").html( $("#instafeed").html() + img_html );
                            }
                        {/if}

                        $('#instafeed').imagesLoaded( function() {
                            $('#instafeed').owlCarousel({
                                items :             {if $formAtts.items}{$formAtts.items|intval}{else}false{/if},
                                itemsDesktop :      {if isset($formAtts.itemsdesktop) && $formAtts.itemsdesktop}[1200,{$formAtts.itemsdesktop|intval}]{else}false{/if},
                                itemsDesktopSmall : {if isset($formAtts.itemsdesktopsmall) && $formAtts.itemsdesktopsmall}[992,{$formAtts.itemsdesktopsmall|intval}]{else}false{/if},
                                itemsTablet :       {if isset($formAtts.itemstablet) && $formAtts.itemstablet}[768,{$formAtts.itemstablet|intval}]{else}false{/if},
                                itemsMobile :       {if isset($formAtts.itemsmobile) && $formAtts.itemsmobile}[576,{$formAtts.itemsmobile|intval}]{else}false{/if},
                                itemsCustom :       {if isset($formAtts.itemscustom) && $formAtts.itemscustom}{$formAtts.itemscustom|escape:'html':'UTF-8'}{else}false{/if},
                                singleItem :        false,       // true : show only 1 item
                                itemsScaleUp :      false,
                                slideSpeed :        {if $formAtts.slidespeed}{$formAtts.slidespeed|intval}{else}200{/if},        //  change speed when drag and drop a item
                                paginationSpeed :   {if $formAtts.paginationspeed}{$formAtts.paginationspeed|intval}{else}800{/if},       // change speed when go next page
                                autoPlay :          {if $formAtts.autoplay}true{else}false{/if},       // time to show each item
                                stopOnHover :       {if $formAtts.stoponhover}true{else}false{/if},
                                navigation :        {if $formAtts.navigation}true{else}false{/if},
                                navigationText :    ["&lsaquo;", "&rsaquo;"],
                                scrollPerPage :     {if $formAtts.scrollperpage}true{else}false{/if},
                                pagination :        {if $formAtts.pagination}true{else}false{/if},       // show bullist
                                paginationNumbers : {if $formAtts.paginationnumbers}true{else}false{/if},       // show number
                                responsive :        {if $formAtts.responsive}true{else}false{/if},
                                lazyLoad :          {if $formAtts.lazyload}true{else}false{/if},
                                lazyFollow :        {if $formAtts.lazyfollow}true{else}false{/if},       // true : go to page 7th and load all images page 1...7. false : go to page 7th and load only images of page 7th
                                lazyEffect :        "{$formAtts.lazyeffect|escape:'html':'UTF-8'}",
                                autoHeight :        {if $formAtts.autoheight}true{else}false{/if},
                                mouseDrag :         {if $formAtts.mousedrag}true{else}false{/if},
                                touchDrag :         {if $formAtts.touchdrag}true{else}false{/if},
                                addClassActive :    true,
                                direction:          {if $formAtts.rtl}'rtl'{else}false{/if},
                                
                                afterInit: OwlLoaded,
                                afterAction : SetOwlCarouselFirstLast,
                            });
                        });
                        function OwlLoaded(el){
                            el.removeClass('owl-loading').addClass('owl-loaded').parents('.owl-row').addClass('hide-loading');
                            if ($(el).parents('.tab-pane').length && !$(el).parents('.tab-pane').hasClass('active'))
                                el.width('100%');
                        };
                    {/if}
                }
                {/if}
            });

            feed.run();
    });

            var array_chunk = function(arr, chunkSize) {
                var groups = [], i;
                for (i = 0; i < arr.length; i += chunkSize) {
                    groups.push(arr.slice(i, i + chunkSize));
                }
                return groups;
            }
</script>
{/if}