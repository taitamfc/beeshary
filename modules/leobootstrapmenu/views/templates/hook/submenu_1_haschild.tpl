{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

<li data-menu-type="{$menu.type}" class="nav-item parent dropdown-submenu{$menu.menu_class}" {$model->renderAttrs($menu)}>
    {if $menu.show_title}
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="{$model->getLink($menu)}">
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

            {if $model->is_live_edit && $menu.is_group == 0}<b class="caret"></b>{/if}
        </a>
        {if !$model->is_live_edit && $menu.is_group == 0}<b class="caret"></b>{/if}
    {/if}
    {$model->genFrontTree($menu.megamenu_id, $level, $menu, $typesub, $group_type) nofilter}{* HTML form , no escape necessary *}
</li>
            
