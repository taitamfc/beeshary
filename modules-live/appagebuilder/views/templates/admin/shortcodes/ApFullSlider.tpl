{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<ul id="list-slider">
{foreach from=$arr item=i}
    {if $i}
        <li id="{$i}">
        {foreach from=$languages item=lang}
            {if $default_lang == $lang.id_lang}
                <div class="col-lg-9">
                    {if $config_val.tit.$i.{$lang.id_lang}}
                    <div class="col-lg-5">{$config_val.tit.$i.{$lang.id_lang}}</div>
                    {/if}
                    {if $config_val.img.$i.{$lang.id_lang}}
                        <img src="{$path}{$config_val.img.$i.{$lang.id_lang}}">
                    {/if}
                </div>
                <div class="col-lg-3">
                    <button class="btn-edit-fullslider btn btn-info" type="button"><i class="icon-pencil"></i>{l s='Edit' mod='appagebuilder'}</button>
                    <button class="btn-delete-fullslider btn btn-danger" type="button"><i class="icon-trash"></i>{l s='Delete' mod='appagebuilder'}</button>
                </div>
            {/if}
            
            {assign var=descript value=$config_val.descript.$i.{$lang.id_lang}}
            {assign var=descript value=$descript|htmlspecialchars}
            {assign var=descript value=$descript|replace:'\r\n':''}
            {assign var=descript value=$descript|stripslashes}
            {assign var=temp_name value="{$i}_{$lang.id_lang}"}
            <input type="hidden" id="tit_{$temp_name}" value="{$config_val.tit.$i.{$lang.id_lang}|htmlspecialchars}" name="tit_{$temp_name}"/>
            <input type="hidden" id="img_{$temp_name}" value="{$config_val.img.$i.{$lang.id_lang}|htmlspecialchars}" name="img_{$temp_name}"/>
            <input type="hidden" id="link_{$temp_name}" value="{$config_val.link.$i.{$lang.id_lang}|htmlspecialchars}" name="link_{$temp_name}"/>
            <input type="hidden" id="descript_{$temp_name}" value="{$descript}" name="descript_{$temp_name}"/>
        {/foreach}
        </li>
    {/if}
{/foreach}
</ul>
<ul id="temp-list" class="hide">
    <li id="">
        <div class="col-lg-9"></div>
        <div class="col-lg-3">
            <button class="btn-edit-fullslider btn btn-info" type="button"><i class="icon-pencil"></i> {l s='Edit' mod='appagebuilder'}</button>
            <button class="btn-delete-fullslider btn btn-danger" type="button"><i class="icon-trash"></i> {l s='Delete' mod='appagebuilder'}</button>
        </div>
    </li>
</ul>