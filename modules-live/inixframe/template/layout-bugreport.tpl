<div class="inixframe">
{if isset($conf) AND $conf}
    <div class="note note-success">
        {l s='Your bug report has been successfully submitted!' mod='inixframe'}
    </div>
    <p class="info">{l s='Thank you for subbmitting a bug report! We will contact you with further information as soon as possible!' mod='inixframe'}</p>
{else}
    {if count($errors) && (!isset($disableDefaultErrorOutPut) || $disableDefaultErrorOutPut == false)}
        <div class="note note-danger">
            <span style="float:right">
                <a id="hideError" href="#"><img alt="X" src="../img/admin/close.png" /></a>
            </span>

            {if count($errors) == 1}
                {$errors[0]}
            {else}
                {l s='%d errors' mod='inixframe' sprintf=$errors|count}
                <br/>
                <ol>
                    {foreach $errors as $error}
                        <li>{$error}</li>
                    {/foreach}
                </ol>
            {/if}
        </div>
    {/if}

    {$page}
{/if}

<p class="feedback_thanks">
    {$author} TEAM
</p>
<p class="feedback_thanks">
    <a href="http://www.{$author_domain}"><img src="http://presta-apps.com/inixframe_assets/logo_wider.png" /></a>
</p>
</div>