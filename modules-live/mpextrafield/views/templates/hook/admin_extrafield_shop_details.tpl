{**
* 2010-2019 Webkul
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($extrafielddetail)}

    {foreach $extrafielddetail as $extrafield}
        {if $extrafield.inputtype == 1 && ((!empty($extrafield.default_value) && $extrafield.default_value|@count == 0) || ($extrafield.default_value|is_array && !empty($extrafield.default_value.$id_lang)))}
            <div class="row">
                <label class="control-label col-lg-3">{$extrafield.label_name|escape:'htmlall':'UTF-8'} : </label>
                <div class="col-lg-9">
                <p class="form-control-static">{if $extrafield.default_value|is_array}{$extrafield.default_value.$id_lang|escape:'htmlall':'UTF-8'}{else}{$extrafield.default_value|escape:'htmlall':'UTF-8'}{/if}</p>
                </div>
            </div>
        {/if}
        {if $extrafield.inputtype == 2 && ((!empty($extrafield.default_value) && $extrafield.default_value|@count == 0) || ($extrafield.default_value|is_array && !empty($extrafield.default_value.$id_lang)))}
            <div class="row">
                <label class="control-label col-lg-3">{$extrafield.label_name|escape:'htmlall':'UTF-8'} : </label>
                <div class="col-lg-9">
                    <p class="form-control-static">{if $extrafield.default_value|is_array}{$extrafield.default_value.$id_lang|escape:'htmlall':'UTF-8'}{else}{$extrafield.default_value|escape:'htmlall':'UTF-8'}{/if}</p>
                </div>
            </div>
        {/if}
        {if $extrafield.inputtype == 3}
            {if $extrafield.multiple != 1}
                {foreach $extrafield['extfielddrop'] as $extfieldopt}
                    {if !empty($extfieldopt.selected_value)}
                        {assign var=select value="1"}
                    {/if}
                {/foreach}
            {else if $extrafield.multiple == 1}
                {foreach $extrafield['extfielddrop'] as $extfieldopt}
                    {assign var=select value=","|explode:$extfieldopt.selected_value}
                    {if in_array($extfieldopt.id, $select)}
                        {assign var=select value="1"}
                    {/if}
                {/foreach}
            {/if}
        {if isset($select)}
        <div class="row">
            <label class="control-label col-lg-3">{$extrafield.label_name|escape:'htmlall':'UTF-8'} : </label>
            <div class="col-lg-9">
                {if $extrafield.multiple != 1}
                {foreach $extrafield['extfielddrop'] as $extfieldopt}
                {if $extfieldopt.selected_value == $extfieldopt.id}
                <p class="form-control-static">{$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}</p>
            {/if}
            {/foreach}
            {else if $extrafield.multiple == 1}
                <span>
                <p class="form-control-static">
                {foreach $extrafield['extfielddrop'] as $key => $extfieldopt}
                {assign var=select value=","|explode:$extfieldopt.selected_value}
                    {if in_array($extfieldopt.id, $select)}
                {$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}
                    {if isset($extrafield['extfielddrop'][$key + 1])},{/if}
                {/if}
            {/foreach}
                </p>
                </span>
            {/if}
            </div>
        </div>
        {/if}
        {/if}
        {if $extrafield.inputtype ==4 && !empty($extrafield['extfieldcheck'][0].selected_value)}
            <div class="row">
                <label class="control-label col-lg-3">{$extrafield.label_name|escape:'htmlall':'UTF-8'} : </label>
                <div class="col-lg-9">
                    <p class="form-control-static">
                        {foreach $extrafield['extfieldcheck'] as $extfieldopt}
                        {assign var=check value=","|explode:$extfieldopt.selected_value}
                        {foreach $check as $key => $chk}
                            {if $chk == $extfieldopt.id}
                                {$extfieldopt.display_value|escape:'html':'UTF-8'}
                                {if isset($check[$key + 1])},
                                {/if}
                            {/if}
                        {/foreach}
                        {/foreach}
                    </p>
                </div>
            </div>
        {/if}
        {if $extrafield.inputtype ==5}
            {if isset($extrafield.default_value) && $extrafield.value_id && $extrafield.default_value != '0'}
                <div class="row">
                    <label class="control-label col-lg-3">{$extrafield.label_name|escape:'htmlall':'UTF-8'} : </label>
                    <div class="col-lg-9">
                        <a href="{$link->getModuleLink('mpextrafield', 'mediadownload',['id_attachment' => $extrafield['value_id']])|escape:'html':'UTF-8'}" class="btn btn-primary"><i class="material-icons">&#xE2C4;</i> {l s='Download file' mod='mpextrafield'}</a>
                        <input type="hidden" value="{$extrafield.id|escape:'htmlall':'UTF-8'}" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}">
                    </div>
                </div>
            {/if}
        {/if}
        {if $extrafield.inputtype == 6 && !empty($extrafield.selected_value)}
            <div class="row">
                <label class="control-label col-lg-3">{$extrafield.label_name|escape:'htmlall':'UTF-8'} : </label>
                <div class="col-lg-9">
                {foreach $extrafield['extfieldradio'] as $extfieldopt}
                    {if $extfieldopt.selected_value == $extfieldopt.id}
                        <p>{$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}</p>
                    {/if}
                {/foreach}
                </div>
            </div>
        {/if}

    {/foreach}
{/if}
