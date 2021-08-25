{foreach $seller_badges as $badge}
<div class="row" style="padding-bottom: 20px ">
    <div class="col-sm-12">
        <div class="col-sm-4 text-center">
                <span>
                <img src="{$modules_dir|escape:'htmlall':'UTF-8'}mpbadgesystem/views/img/badge_img/{$badge['badge_id']|escape:'htmlall':'UTF-8'}.jpg"
                     title="{$badge['badge_name']|escape:'htmlall':'UTF-8'}"
                     style="margin-right:10px;opacity: 1;/*width: auto;*/ /*height: 50px*/"/>
            </span>
        </div>
        <div class="col-sm-8 section-heading" style="vertical-align: top">
            <h2>{$badge['badge_desc']}</h2>
        </div>
    </div>
</div>
{/foreach}
