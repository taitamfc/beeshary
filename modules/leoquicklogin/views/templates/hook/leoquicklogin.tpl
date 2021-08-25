{* 
* @Module Name: Leo Quick Login
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
*}

{if $isLogged}
    <a class="logout" href="{$logout_url|escape:'html':'UTF-8'}" rel="nofollow">
        <i class="material-icons">&#xE879;</i>      
        <span class="hidden-sm-down">{l s='Sign out' mod='leoquicklogin'}</span>
    </a>
    <a class="account" href="{$my_account_url|escape:'html':'UTF-8'}" title="{l s='View My Account' mod='leoquicklogin'}" rel="nofollow">
        <i class="material-icons">&#xE7FD;</i>
        <span class="hidden-sm-down">{$customerName|escape:'html':'UTF-8'}</span>
    </a>
{else}
    {if $leo_type == 'html'}
        {$html_form nofilter}{* HTML form , no escape necessary *}
    {else}
        {if $leo_type == 'dropdown'}
            <div class="dropdown">
        {/if}
        {if $leo_type == 'dropup'}
            <div class="dropup">
        {/if}
            <a href="javascript:void(0)" 
               class="leo-quicklogin-nav leo-quicklogin{if $leo_type == 'dropdown' || $leo_type == 'dropup'} leo-dropdown dropdown-toggle{/if}" 
               data-enable-sociallogin="{if isset($enable_sociallogin)}{$enable_sociallogin|escape:'html':'UTF-8'}{/if}" 
               data-type="{$leo_type|escape:'html':'UTF-8'}" 
               data-layout="{$leo_layout|escape:'html':'UTF-8'}"
               {if $leo_type == 'dropdown' || $leo_type == 'dropup'} 
               data-toggle="dropdown"
               aria-haspopup="true"
               aria-expanded="false"
               {/if}
               title="{l s='Quick Login' mod='leoquicklogin'}"
               rel="nofollow">
                <i class="material-icons">&#xE851;</i>
                <span class="hidden-sm-down">{l s='Quick Login' mod='leoquicklogin'}</span>
            </a>
        {if $leo_type == 'dropdown' || $leo_type == 'dropup'}
                <div class="dropdown-menu leo-dropdown-wrapper">
                    {$html_form nofilter}{* HTML form , no escape necessary *}
                </div>
            </div>
        {/if}
    {/if}
{/if}
