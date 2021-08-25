<div class="inixframe">
{if isset($conf) }
    <div class="note note-success">
        {l s='Your feedback has been successfully submitted!' mod='inixframe'}
    </div>
    <p class="info">{l s='You can use the code bellow to get 30%% discount on your next purchase from Presta Apps' mod='inixframe'}</p>
    <p class="vouchercode">30off</p>
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
    Thanks in advance.
    </p>
<p class="feedback_thanks">
    Greeting
</p>
<p class="feedback_thanks">
    {$author} TEAM
</p>
<p class="feedback_thanks">
    <a href="http://www.{$author_domain}"><img src="http://presta-apps.com/inixframe_assets/logo_wider.png" /></a>
</p>
<script type="text/javascript">
    $("#other_yes").parent().hide();
    $("#other_yes").parent().prev().hide();
    $("#other_on").click(function(){
        $("#other_yes").parent().slideDown();
        $("#other_yes").parent().prev().slideDown();
    });

    $("#other_off").click(function(){
        $("#other_yes").parent().slideUp();
        $("#other_yes").parent().prev().slideUp();
    });


</script>
</div>