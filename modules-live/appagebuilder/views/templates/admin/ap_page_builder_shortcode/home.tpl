{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\admin\ap_page_builder_home\home -->
{if isset($errorText) && $errorText}
<div class="error alert alert-danger">
    {$errorText|escape:'html':'UTF-8'}
</div>
{/if}
{if isset($errorSubmit) && $errorSubmit}
<div class="error alert alert-danger">
    {$errorSubmit|escape:'html':'UTF-8'}
</div>
{/if}

<div id="home_wrapper" class="default">
    <div class="position-cover row">
    {include file='./position.tpl'}
    </div>
</div>

<div id="ap_loading" class="ap-loading">
    <div class="spinner">
        <div class="cube1"></div>
        <div class="cube2"></div>
    </div>
</div>
{include file="$tplPath/ap_page_builder_shortcode/home_form.tpl"}
<script type="text/javascript">
		{addJsDef imgModuleLink=$imgModuleLink}
		{addJsDef apAjaxShortCodeUrl=$ajaxShortCodeUrl}
		{addJsDef apAjaxHomeUrl=$ajaxHomeUrl}
		{addJsDef apImgController=$imgController}
		
    $(document).ready(function(){
        var $apHomeBuilder = $(document).apPageBuilder();
        $apHomeBuilder.process('{$dataForm}{* HTML form , no escape necessary *}','{$shortcodeInfos}{* HTML form , no escape necessary *}','{$languages}{* HTML form , no escape necessary *}');
        $apHomeBuilder.ajaxShortCodeUrl = apAjaxShortCodeUrl;
        $apHomeBuilder.ajaxHomeUrl = apAjaxHomeUrl;
        $apHomeBuilder.lang_id = '{$lang_id|escape:'html':'UTF-8'}';
        $apHomeBuilder.imgController = apImgController;        
    });
</script>