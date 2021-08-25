<div class='box-account' style="margin-top:15px;">
    <div class="box-head" style="padding-bottom: 10px;">
        {*<h2>{l s='Membre de' mod='beesharyapi'}</h2>*}
        {*<div class="wk_border_line"></div>*}
        <img style="vertical-align: text-bottom;" src="{$smarty.const._THEME_IMG_DIR_}bee-drop-g3.svg" />
        <div class="seller_partners_header">
            Mes Labels et distinctions
        </div>

    </div>
    <div class="wk_row">
        {foreach $seller_badges as $badge}
            <span>
                <img src="{$modules_dir|escape:'htmlall':'UTF-8'}mpbadgesystem/views/img/badge_img/{$badge['badge_id']|escape:'htmlall':'UTF-8'}.jpg" title="{$badge['badge_name']|escape:'htmlall':'UTF-8'}"
                     {*width="250px" height="50"*} style="max-height: 120px; max-width: 120px;margin-right:10px;opacity: 1;/*width: auto;*/ /*height: 50px*/"/>
            </span>
        {/foreach}
    </div>
</div>