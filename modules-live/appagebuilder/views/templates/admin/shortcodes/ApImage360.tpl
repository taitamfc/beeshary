{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<ul id="list-slider" class="ul-apimage360">
{foreach from=$arr item=i}
    {if $i && $config_val.image360.$i}
        {assign var=image_name value=$config_val.image360.$i}
        {assign var=input_name value="image360_{$i}"}

        <li id="{$i}" name="{$image_name}">
            <div class="col-lg-9">
                <div class="col-lg-5">
                    <img data-position="" data-name="{$image_name}" class="img-thumbnail" src="{$image_folder}{$image_name}">
                    <input type="hidden" value="{$image_name}" class="ApImage360" id="{$input_name}" name="{$input_name}">
                </div>
                <div class="col-lg-4">{$image_name}</div>
            </div>
            <div class="col-lg-3" style="text-align: right;">
                <button type="button" class="btn-delete-fullslider btn btn-danger"><i class="icon-trash"></i> {l s='Delete' mod='appagebuilder'}</button>
            </div>
        </li>
    {/if}
{/foreach}
</ul>
