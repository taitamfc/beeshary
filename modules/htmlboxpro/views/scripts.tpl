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

<link href="../modules/htmlboxpro/views/css/styles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{$ps_base_uri}js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="../modules/htmlboxpro/views/js/script.js"></script>
<script type="text/javascript">
    var iso = "{$isoTinyMCE}";
    var pathCSS = "{$theme_css_dir}";
    var ad = "{$ad}";
    {literal}
    function toggleEditor(id){
        tinyMCE.execCommand("mceToggleEditor", false, id);
    }
    {/literal}
</script>

{if Configuration::get('hbp_tiny') == 1}
    {if Configuration::get('hbp_forceurls') != 1}
        <script type="text/javascript" src="../modules/htmlboxpro/views/js/tinymce16.inc.js"></script>
    {else}
        <script type="text/javascript" src="../modules/htmlboxpro/views/js/tinymce16-force-urls.inc.js"></script>
    {/if}
{else}
    <script type="text/javascript" src="{$ps_base_uri}js/admin/tinymce.inc.js"></script>
        {literal}
            <script>
                $().ready(function () {tinySetup(); });
            </script>
        {/literal}
{/if}
