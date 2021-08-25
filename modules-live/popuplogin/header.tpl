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
{if $ps_version==7}
    {if $ps_version==7}
        <script>
            var baseDir = prestashop.urls.base_url;
            {if Context::getContext()->customer->isLogged()}
                var PopAuthUrl = 'XXXX{$link->getPageLink('my-account')}';
            {else}
                var PopAuthUrl = '{$link->getPageLink('my-account')}';
            {/if}
        </script>
    {/if}
{/if}