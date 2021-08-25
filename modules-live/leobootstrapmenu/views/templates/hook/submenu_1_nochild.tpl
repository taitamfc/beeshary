{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

<li data-menu-type="{$menu.type}" class="nav-item {$menu.menu_class}" {$model->renderAttrs($menu)}>
    {if $menu.show_title}
        <a class="nav-link" href="{$model->getLink($menu)}">
            {if $menu.icon_class}
                {if $menu.icon_class != $menu.icon_class|strip_tags}
                    <span class="hasicon menu-icon-class">{$menu.icon_class}
                {else}
                    <span class="hasicon menu-icon-class"><i class="{$menu.icon_class}"></i>
                {/if}
            {elseif $menu.image }
                <span class="hasicon menu-icon" style="background:url('{$model->image_base_url}{$menu.image}') no-repeat">
            {/if}

            {if $menu.show_title}
                <span class="menu-title">{$menu.title}</span>
            {/if}
            {if $menu.text}
                <span class="sub-title">{$menu.text}</span>
            {/if}
            {if $menu.description}
                <span class="menu-desc">{$menu.description}</span>
            {/if}

            {if $menu.image || $menu.icon_class}
                </span>
            {/if}
        </a>

    {/if}
</li>
            
