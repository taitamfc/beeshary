
<div class="container">
    <div class="row">
        <div class="col-md-12 no_pad">
            <h1 class="text-center login-title">Je me connecte</h1>
            <div class="socialloginizer">
                <?php if (Configuration::get('PP_FACEBOOK_LOGIN_ENABLE') && $fb_url): ?>
                    <p class="text-lg-center text-md-center text-xs-center">
                        <a href="<?= $fb_url ?>" class="fb-btn">
                            <span>
                                Avec Facebook
                            </span>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if (Configuration::get('PP_GOOGLE_LOGIN_ENABLE') && $gg_url): ?>
                    <p class="text-lg-center text-md-center text-xs-center">
                        <a href="<?= $gg_url ?>" class="gg-btn">
                            <span>
                                Avec Google
                            </span>
                        </a>
                    </p>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div class="account-wall">
                <?php
                echo '<form target="_parent" class="form-signin"  action="' . (($popuplogin->psversion() == 5 || $popuplogin->psversion() == 6 ? $link->getPageLink("authentication") : $link->getPageLink("authentication.php"))) . '' . (Tools::getValue('back', 'false') != 'false' ? '?back=' . Tools::getValue('back') : '') . '" method="post">
                    <input type="hidden" name="submitLogin" value="1" />

                    <div class="form-group">
                        <div class="input-group email_group">
                            <input type="email" class="form-control" name="email" id="email" placeholder="' . $popuplogin->lemailaddress . '" required autofocus />
                            <div class="input-group-addon">
                                <img src="'. _THEME_IMG_DIR_ .'bee_icon_at.png" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group pswrd_group">
                            <input type="password" class="form-control" name="password" id="passwd" placeholder="' . $popuplogin->lpassword . '" required />
                            <div class="input-group-addon">
                                <img src="'. _THEME_IMG_DIR_ .'bee_icon_passwd.png" />
                            </div>
                        </div>
                    </div>

                    ' . (Configuration::Get('popuplogin_return') == 1 ? '<input type="hidden" name="back" value="' . $link->getPageLink("authentication") . '"/>' : '') . '
                    ' . (Configuration::Get('popuplogin_return') == 2 ? '<input type="hidden" name="back" value="' . $link->getPageLink("index") . '"/>' : '') . '
                    ' . (Configuration::Get('popuplogin_return') == 3 ? '<input type="hidden" name="back" value="' . Tools::getValue('back') . '"/>' : '') . '
                    <div class="col-md-6"></div>
                    <div class="col-md-6"><a target="_parent" href="'. $link->getPageLink("password") .'" class="pull-right forgotpasswd">' . $popuplogin->fpassword . '</a><span class="clearfix"></span></div>
                    <center><button name="SubmitLogin" class="connexion-btn" type="submit">Connexion</button></center>
                    ' . $popuplogin->runhook('popuplogin') . '
                </form>
            </div>
            <div class="create_account">
                <p>Vous n\'avez pas encore de compte</p>
                <a class="signup-btn" href="javascript:;">Inscrivez-vous ici</a>
            </div>
        </div>
    </div>
</div>';
// parent.jQuery.fancybox.close()
?>
<style>
.no_pad{padding:0px;}
body {
  height:460px;
}
.form-signin {
    max-width: 550px;
    padding: 15px;
    margin: 0 auto;
}
.form-signin .form-signin-heading, .form-signin .checkbox
{
    margin-bottom: 10px;
}
.form-signin .checkbox
{
    font-weight: normal;
}
.form-signin .form-control
{
    position: relative;
    font-size: 16px;
    height: auto;
    padding: 10px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
.form-signin .form-control:focus
{
    z-index: 2;
}
.form-signin input[type="text"] {
    margin-bottom: -1px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
.form-signin input[type="password"] {
    margin-bottom: 0px;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}
.account-wall
{
    margin-top: 10px;
    padding: 0px 0px 20px 0px;
    /*background-color: #f7f7f7;
    -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);*/
}
.login-title {
    color: #555;
    font-size: 30px;
    font-weight: 600;
    display: block;
    border-bottom: 1px solid #eee;
    margin: 0px 0px;
    padding: 25px 0
}
.profile-img{
    width: 96px;
    height: 96px;
    margin: 0 auto 10px;
    display: block;
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    border-radius: 50%;
}
.need-help
{
    margin-top: 10px;
}
.new-account
{
    display: block;
    margin-top: 10px;
}
.socialloginizer {width: 308px;margin: 50px auto 0px auto;}
.socialloginizer p {float: left;}
.socialloginizer p a.gg-btn, .socialloginizer p a.fb-btn {color: #fff;padding: 15px 20px;font-weight: bold;font-size: 16px;text-decoration: none;}
.socialloginizer p a.fb-btn {background-color: #1a6cb3;}
.socialloginizer p a.gg-btn {background-color: #c55643;}
.account-wall .input-group-addon {background-color: #fff;}
.account-wall .input-group-addon img {width: 49px;height: 45px;}
.account-wall .input-group input {height: 59px;}
.account-wall .input-group.email_group {margin-bottom: 20px;}
.account-wall .forgotpasswd {color: #000;font-weight: bold;margin-right: 55px;margin-top: 15px}
.account-wall .connexion-btn {border: 3px solid #000;width: 300px;margin: 30px 0 0px 110px;padding: 6px 0;font-weight: bold;}
.create_account {text-align: center;}
.create_account a.signup-btn {
    border: 3px solid #000;
    font-weight: bold;
    display: block;
    width: 300px;
    margin: 30px auto;
    color: #000;
    padding: 6px 0;
    text-decoration: none;
}
</style>
