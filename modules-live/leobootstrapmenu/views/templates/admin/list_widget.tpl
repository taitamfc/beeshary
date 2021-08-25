{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<option value=""></option>
{foreach from=$widgets item=w}
<option value="{$w['key_widget']}">{$w['name']}</option>
{/foreach}
        