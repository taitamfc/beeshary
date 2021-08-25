{*
 * 2017-2018 PHPIST
 *
 *  @author    Yassine Belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 *}

{extends file=$layout}

{block name='content'}
<div class="wk-mp-block">
    {hook h="displayMPMyAccountMenu"}
    <div class="wk-mp-content">
        <div class="wk-mp-right-column">
            <div class="row">
                <div class="col-sm-12 section-heading"><h2>Mon profil</h2></div>
                <div class="clearfix"></div>
                {if isset($errors) && $errors|@count}
                    <ul class="alert alert-danger center-block col-sm-10 pull-xs-none">
                    {foreach from=$errors item=error}
                        <li>{$error|escape:'htmlall':'utf-8'}</li>
                    {/foreach}
                    </ul>
                    <div class="clearfix"></div>
                {/if}
                {if $is_modified}
                    <div class="alert alert-success center-block col-sm-10 pull-xs-none">Les modifications ont été mises à jour avec succès</div>
                {/if}
                <form class="col-sm-10 center-block pull-xs-none inputgrpajst" action="{$link->getModuleLink('marketplace', 'customerprofile')|addslashes}" method="post">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First name" value="{$customer->firstname|escape:'html':'utf-8'}" />
                            <div class="input-group-addon">
                                <img src="{$urls.img_url}bee_icon_user.png">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last name" value="{$customer->lastname|escape:'html':'utf-8'}" />
                            <div class="input-group-addon">
                                <img src="{$urls.img_url}bee_icon_user.png">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{$customer->email|escape:'html':'utf-8'}" />
                            <div class="input-group-addon">
                                <img src="{$urls.img_url}bee_icon_at.png">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <div class="input-group-addon">
                                <img src="{$urls.img_url}bee_icon_passwd.png">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_conf" name="password_conf" placeholder="Password confirmation">
                            <div class="input-group-addon">
                                <img src="{$urls.img_url}bee_icon_passwd.png">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="center-block col-sm-3 pull-xs-none" style="margin-left: -13px;">
                          <div class="checkbox">
                            <label><input class="mp_signup_chbx" type="checkbox" name="newsletter" {if $customer->newsletter}checked="checked"{/if} value="1" />Subsribe to newsletter</label>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary center-block" type="submit" name="submitCustomerProfile" value="Modifier le profil" />
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{/block}
