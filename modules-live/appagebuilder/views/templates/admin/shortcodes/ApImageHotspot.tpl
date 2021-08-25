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
                            {if $config_val.temp_title.$i.{$lang.id_lang}}
                                <div class="col-lg-5">{$config_val.temp_title.$i.{$lang.id_lang}}</div>
                            {/if}
                            {if $config_val.temp_image.$i.{$lang.id_lang}}
                                <img src="{$path}{$config_val.temp_image.$i.{$lang.id_lang}}">
                            {/if}
                        </div>
                        <div class="col-lg-3">
                            <button class="btn-edit-level2 btn btn-info" type="button"><i class="icon-pencil"></i>
                                {l s='Edit' mod='appagebuilder'}
                            </button>
                            <button class="btn-delete-level2 btn btn-danger" type="button"><i class="icon-trash"></i>
                                {l s='Delete' mod='appagebuilder'}
                            </button>
                        </div>
                    {/if}

                    {assign var=temp_name value="{$i}_{$lang.id_lang}"}
                    
                    {foreach from=$inputs_lang item=input}
                        <input type="hidden" id="{$input}_{$temp_name}" value="{$config_val.$input.$i.{$lang.id_lang}|htmlspecialchars}" name="{$input}_{$temp_name}"/>
                    {/foreach}
                    
                {/foreach}

                {foreach from=$inputs item=input}
                    <input type="hidden" id="{$input}_{$i}" value="{$config_val.$input.$i|htmlspecialchars}" name="{$input}_{$i}"/>
                {/foreach}

            </li>
        {/if}
    {/foreach}
</ul>
<ul id="temp-list" class="hide">
<li id="">
    <div class="col-lg-9"></div>
    <div class="col-lg-3">
        <button class="btn-edit-level2 btn btn-info" type="button"><i class="icon-pencil"></i> {l s='Edit' mod='appagebuilder'}</button>
        <button class="btn-delete-level2 btn btn-danger" type="button"><i class="icon-trash"></i> {l s='Delete' mod='appagebuilder'}</button>
    </div>
</li>