{if isset($partnerId) && $partnerId && $partnerData}

    <div class="top_header">
        <img class="mp_montgolfiere" src="{$urls.base_url}themes/beeshary_child/assets/img/bee-ill-montgolfiere.png" />
        <div class="main_title">{$partnerData['badge_name']}</div>
        <div class="sub_title" style="font-size: 30px;">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-sm-4">&nbsp;
        </div>
        <div class="col-sm-4 text-center" style="font-size: 40px;font-weight: bold;padding-top: 30px">
            <h2 style="font-size: 25px;">{$partnerData['badge_desc']}</h2>
        </div>
        <div class="col-sm-4">&nbsp;
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">&nbsp;
        </div>
        <div class="col-sm-4 text-center" style="padding-top: 10px">
            <img height="130px" src="/modules/mpbadgesystem/views/img/badge_img/{$partnerId|escape:'htmlall':'UTF-8'}.jpg" />
        </div>
        <div class="col-sm-4">&nbsp;
        </div>
    </div>
{/if}