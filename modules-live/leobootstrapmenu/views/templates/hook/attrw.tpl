{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

{if isset($menu.megaconfig->subwidth) && $menu.megaconfig->subwidth}
    {if $group_type == 'horizontal'}
        style="width:{$menu.megaconfig->subwidth}px;"
    {else}
        {if (isset($typesub) && $typesub == 'left') }
            style="width:{$menu.megaconfig->subwidth}px; left: -{$menu.megaconfig->subwidth}px;"
        {else}
            style="width:{$menu.megaconfig->subwidth}px; left: 100%;"
        {/if}
    {/if}
{/if}