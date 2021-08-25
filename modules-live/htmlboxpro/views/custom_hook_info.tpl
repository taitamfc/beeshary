{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-9999 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}
<div class="panel">
<h3><i class='icon-cogs'></i> {l s='This block uses custom hook' mod='htmlboxpro'} {$custom_hook_name}</h3>
    <div class="alert alert-info">
        {l s='This block is attached to custom hook. To display it in .tpl file use:' mod='htmlboxpro'}
    </div>
    <pre>{literal}{hook::exec('{/literal}{$custom_hook_name}{literal}') nofilter}{/literal}</pre>
    <br/>
    <div class="alert alert-info">
        {l s='If you want to use this custom hook inside list of products and display contents for selected products on that list use code:' mod='htmlboxpro'}
    </div>
    <pre>{literal}{hook h='{/literal}{$custom_hook_name}{literal}' product=$product}{/literal}</pre>
</div>