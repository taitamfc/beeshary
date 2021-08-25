{* 
* @Module Name: Leo Slideshow
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<div class="typos bannercontainer">
    <div class="note"> 
            {l s='NOTE' mod='leoslideshow'}: <p>{l s='These Below Typos are getting in the file' mod='leoslideshow'}:<a href="{$typoDir|escape:'html':'UTF-8'}" target="_blank">{$typoDir|escape:'html':'UTF-8'}</a>
            <br>{l s='you can open this file and add yours css style and it will be listed in here!!!' mod='leoslideshow'}</p>
            <p>{l s='To Select One, You Click The Text Of Each Typo' mod='leoslideshow'}</p>
    </div>

    <div class="typos-wrap">	
        {foreach $captions as $caption}
            <div class="typo {if $caption=='cus_color'}typo-big{/if}"><div class="tp-caption {$caption|escape:'html':'UTF-8'}" data-class="{$caption|escape:'html':'UTF-8'}">{l s='Use This Caption Typo' mod='leoslideshow'}</div></div>
        {/foreach}
     </div>
</div>  
<script type="text/javascript">
$('div.typo').live('click', function() {  
        if(parent.$('#{$field|escape:'html':'UTF-8'}').val())
            parent.$('#{$field|escape:'html':'UTF-8'}').val(parent.$('#{$field|escape:'html':'UTF-8'}').val()+" "+$("div", this).attr("data-class") );
        else
            parent.$('#{$field|escape:'html':'UTF-8'}').val($("div", this).attr("data-class") );
        parent.$('#dialog').dialog('close');
        parent.$('#dialog').remove();	
});
</script>