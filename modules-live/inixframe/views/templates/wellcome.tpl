<div class="portlet">
<div class="portlet-title">
    <div class="caption">
        {l s='Wellcome' mod='inixframe'}
    </div>
</div>
<div class="portlet-body">
    <div class="row">
        <div class=" col-lg-5">
            <div class="note note-success">
                <h4 class="block "><strong>{l s='We are constantly improving!' mod='inixframe'}</strong></h4>

                <p><strong>{l s='Yes!' mod='inixframe'}</strong>{l s=' We are dedicating ourselves to deliver you the best experience, fast!' mod='inixframe'}</p>

                <p>{l s='With our Update service you will receive the latest updates as soon as they hit the public!' mod='inixframe'}</p>

                <p>{l s='Take advantage of it right now, the only thing you need to do is register' mod='inixframe'}</p>
                <p>{l s='If you already have an account, just use it to login' mod='inixframe'}</p>
            </div>
        </div>
        <div class=" col-lg-7">
            <form  action="{$current}&token={$token}&ajax=1&action=register&json=1" class="form-horizontal" role="form" id="wellcome_form">
                <h3 class="col-lg-offset-2">{l s='Login to get the latest updates' mod='inixframe'}</h3>
                <div class="form-group">
                    <label for="inputEmail1" class="col-lg-2 control-label">Email</label>
                    <div class="col-lg-8">
                        <input type="email" class="form-control" id="update_service_email" placeholder="Email" name="update_service_email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword12" class="col-lg-2 control-label">Password</label>
                    <div class="col-lg-8">
                        <input type="password" class="form-control" id="update_service_password" placeholder="Password" name="update_service_password">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <button type="submit" class="btn btn-info fade in">{l s='Login' mod='inixframe'}</button>
                        <button type="submit" class="btn btn-info fade in">{l s='Register' mod='inixframe'}</button>
                        <img src="{$frame_path_uri}img/loading.gif" id="loading_frame"  class="fade" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>