{*
*  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
*
*  @author    Línea Gráfica E.C.E. S.L.
*  @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
*  @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
*}
<script>
    var message_directories = "{l s='Collecting directories with images' mod='lgrequality'}";
    var message_images = "{l s='Searching images with jpg or png format' mod='lgrequality'}";
    var message_requality = "{l s='Requalifying images' mod='lgrequality'}";
    var message_recover = "{l s='Recovering images' mod='lgrequality'}";
    var lgrequality_token = "{$lgrequality_token|escape:'htmlall':'UTF-8'}";
</script>
<ps-panel icon="icon-signal" header="{l s='Statistics' mod='lgrequality'}">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            {if isset($ttscan) && $ttscan > 0}
                <div class="col-lg-12 statistic-time">
                    {l s='Scan time' mod='lgrequality'}:
                    <span class="secs">{$ttscan|escape:'htmlall':'UTF-8'}
                        &nbsp;{l s='secs' mod='lgrequality'}</span>
                </div>
                <div class="clearfix"></div>
                <hr/>
            {/if}
            {if isset($ttreq) && $ttreq > 0}
                <div class="col-lg-12 statistic-time">
                    {l s='Requality time' mod='lgrequality'}:
                    <span class="secs">{$ttreq|escape:'htmlall':'UTF-8'}
                        &nbsp;{l s='secs' mod='lgrequality'}</span>
                </div>
                <div class="clearfix"></div>
                <hr/>
            {/if}
            {if isset($ttrec) && $ttrec > 0}
                <div class="col-lg-12 statistic-time">
                    {l s='Recovery time' mod='lgrequality'}:
                    <span class="secs">{$ttrec|escape:'htmlall':'UTF-8'}
                        &nbsp;{l s='secs' mod='lgrequality'}</span>
                </div>
                <div class="clearfix"></div>
                <hr/>
            {/if}
        </div>
        <div class="col-lg-6 col-lg-offset-3">
            <div class="graph">
                <div class="total">
                    {l s='Compression rate' mod='lgrequality'}:&nbsp;
                    <span class="rate">{$compression_diff|escape:'htmlall':'UTF-8'}%</span> /
                    {l s='Directories' mod='lgrequality'}:&nbsp;
                    <span class="dirs">{$directories|escape:'htmlall':'UTF-8'}</span> /
                    {l s='Images' mod='lgrequality'}:&nbsp;
                    <span class="images">{$images|escape:'htmlall':'UTF-8'}</span>
                    <div class="col-lg-12 text left" style="padding-left:0" id="display-container">
                        <div class="col-lg-2 text-left" style="padding-left:0"><img src="{$base_url|escape:'htmlall':'UTF-8'}views/img/publi/ajax-loader.gif"></div>
                        <div class="col-lg-10 text-left" id="display-message">Recolectando directorios</div>
                    </div>
                </div>
                <div class="group">
                    <div class="text">{l s='Original size' mod='lgrequality'}
                        (<span id="size_original">{$size_o|escape:'htmlall':'UTF-8'}</span>MB)
                    </div>
                    <div class="bar-original">100%</div>
                </div>
                <div class="group">
                    <div class="text">{l s='Compressed size' mod='lgrequality'}
                        &nbsp;(<span id="size_compressed">{$size_c|escape:'htmlall':'UTF-8'}</span>MB)
                    </div>
                    <div class="bar-compressed" style="width: {$compression_percentage|escape:'htmlall':'UTF-8'}%;">
                        {$compression_percentage|escape:'htmlall':'UTF-8'}%
                    </div>
                </div>
                <div class="group" id="bar-recover">
                    <div class="text">{l s='Recovered' mod='lgrequality'}
                    </div>
                    <div class="bar-recover" style="width:0%;">
                        0%
                    </div>
                </div>
            </div>
        </div>
    </div>
</ps-panel>
<ps-panel icon="icon-cogs" header="{l s='Configuration' mod='lgrequality'}">
    {* 7200 secs = 120 mins *}
    {if $time_out < 7200}
        <ps-alert-warn>{l s='The time out of your server is' mod='lgrequality'}
            {l s='The time out of your server is' mod='lgrequality'}
            <strong>{$time_out|escape:'htmlall':'UTF-8'}&nbsp;{l s='seconds' mod='lgrequality'}</strong>
            ({($time_out/60)|escape:'htmlall':'UTF-8'}&nbsp;{l s='mins' mod='lgrequality'}).
            {l s=' We recommend that it be greater than' mod='lgrequality'}
            <strong>7200&nbsp;{l s='seconds' mod='lgrequality'}</strong>&nbsp;(120&nbsp;{l s='mins' mod='lgrequality'})
            {l s='to process all the images on the web' mod='lgrequality'}.
        </ps-alert-warn>
    {/if}
    <form class="form-horizontal" method="post" action="" id="lg_main_form">
        <ps-input-text name=""
                       label="{l s='Time out' mod='lgrequality'}"
                       help="{l s='This value corresponds to the time out configured in your server, so it is not modifiable.' mod='lgrequality'}"
                       value="{$time_out|escape:'htmlall':'UTF-8'}"
                       suffix="{l s='secs' mod='lgrequality'}"
                       fixed-width="sm"
                       disabled="disabled">
        </ps-input-text>
        <ps-input-text name="reduction_jpg"
                       label="{l s='JPG reduction' mod='lgrequality'}"
                       help="{l s='This value corresponds to the JPG and JPEG quality. The maximum value is 100 and the minimum value is 1.' mod='lgrequality'}"
                       size="3"
                       value="{$reduction_jpg|escape:'htmlall':'UTF-8'}"
                       fixed-width="sm"
                       maxlength="3">
        </ps-input-text>
        <ps-input-text name="reduction_png"
                       label="{l s='PNG reduction' mod='lgrequality'}"
                       help="{l s='This value corresponds to the PNG quality. The maximum value is 9 and the minimum value is 1.' mod='lgrequality'}"
                       size="2"
                       value="{$reduction_png|escape:'htmlall':'UTF-8'}"
                       fixed-width="sm"
                       maxlength="1">
        </ps-input-text>
        <ps-checkboxes label="{l s='Excluded directories' mod='lgrequality'}">
            <ps-checkbox name="exclude_products"
                         value="1"
                         {if $exclude_products}checked="true"{/if}>
                {l s='Products images' mod='lgrequality'}
            </ps-checkbox>
            <ps-checkbox name="exclude_categories"
                         value="1"
                         {if $exclude_categories}checked="true"{/if}>
                {l s='Categories images' mod='lgrequality'}
            </ps-checkbox>
            <ps-checkbox name="exclude_suppliers"
                         value="1"
                         {if $exclude_suppliers}checked="true"{/if}>
                {l s='Suppliers images' mod='lgrequality'}
            </ps-checkbox>
            <ps-checkbox name="exclude_tmp"
                         value="1"
                         {if $exclude_tmp}checked="true"{/if}>
                {l s='Temporals images' mod='lgrequality'}
            </ps-checkbox>
        </ps-checkboxes>
        <ps-panel-footer>
            <ps-panel-footer-link title="{l s='Scan' mod='lgrequality'}"
                                  icon="process-icon-update"
                                  direction="left"
                                  name="lgrequality_scan"
                                  class="submit-button">
            </ps-panel-footer-link>
            <ps-panel-footer-link title="{l s='Recover' mod='lgrequality'}"
                                  icon="process-icon-reset"
                                  direction="left"
                                  name="lgrequality_recover"
                                  class="submit-button"
                                  {if !$has_data || !$has_processed}style="display:none"{/if}>
            </ps-panel-footer-link>
            <ps-panel-footer-link title="{l s='Optimize' mod='lgrequality'}"
                                  icon="process-icon-ok"
                                  direction="left"
                                  name="lgrequality_requality"
                                  class="submit-button"
                                  {if !$has_data}style="display:none"{/if}>
            </ps-panel-footer-link>
            <ps-panel-footer-submit title="{l s='Save' mod='lgrequality'}"
                                    icon="process-icon-save"
                                    direction="right"
                                    name="lgrequality_save">
            </ps-panel-footer-submit>
        </ps-panel-footer>
    </form>
</ps-panel>

{* Notification popup *}
{if $ttreq > 0}
    <div class="lgoverlay">
        <div class="lgpopup">
            <span>
                {if $compression_diff > 0}
                    {l s='The images of your website have been optimized as much as possible successfully.' mod='lgrequality'}
                {else}
                    {l s='The images on your website were optimized as much as possible.' mod='lgrequality'}
                {/if}
            </span>
        </div>
    </div>
{/if}
