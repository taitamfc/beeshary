<?PHP

echo '<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
<div class="container">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <h1 class="text-center login-title">' . $popuplogin->llogin . '</h1>
            <div class="account-wall">
                <form target="_paren<img class="profile-img" src="//lh5.googleusercontent.com/-b0-k99FZlyE/AAAAAAAAAAI/AAAAAAAAAAA/eu7opA4byxI/photo.jpg?sz=120" alt="">t" class="form-signin"  action="' . (($popuplogin->psversion() == 5 || $popuplogin->psversion() == 6 ? $link->getPageLink("authentication") : $link->getPageLink("authentication.php"))) . '' . (Tools::getValue('back', 'false') != 'false' ? '?back=' . Tools::getValue('back') : '') . '" method="post">
                    <input name="email" type="text" class="form-control" placeholder="' . $popuplogin->lemailaddress . '" required autofocus>
                    <input type="hidden" name="submitLogin" value="1">
                    <input name="password" type="password" class="form-control" placeholder="' . $popuplogin->lpassword . '" required>
                    ' . (Configuration::Get('popuplogin_return') == 1 ? '<input type="hidden" name="back" value="' . $link->getPageLink("authentication") . '"/>' : '') . '
                    ' . (Configuration::Get('popuplogin_return') == 2 ? '<input type="hidden" name="back" value="' . $link->getPageLink("index") . '"/>' : '') . '
                    ' . (Configuration::Get('popuplogin_return') == 3 ? '<input type="hidden" name="back" value="' . Tools::getValue('back') . '"/>' : '') . '
                    <button name="SubmitLogin" class="btn btn-lg btn-primary btn-block" type="submit">' . $popuplogin->lletmein . '</button>
                    <div class="col-md-6"><a target="_parent" href="' . (($popuplogin->psversion() == 5 || $popuplogin->psversion() == 6 ? $link->getPageLink("password") : $link->getPageLink("password.php"))) . '" class="pull-right need-help">' . $popuplogin->fpassword . '</a><span class="clearfix"></span></div>
                    ' . $popuplogin->runhook('popuplogin') . '
                </form>
            </div>
            ' . (Configuration::Get('popuplogin_register') == 1 ? '<a target="_parent" href="' . (($popuplogin->psversion() == 5 || $popuplogin->psversion() == 6 ? $link->getPageLink("authentication") : $link->getPageLink("authentication.php"))) . '" class="text-center new-account">' . $popuplogin->raccount . '</a>':'') . '
        </div>
    </div>
</div>


';

echo "
<style>
body {
  height:460px;
}

.form-signin
{
    max-width: 430px;
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
.form-signin input[type=\"text\"]
{
    margin-bottom: -1px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
.form-signin input[type=\"password\"]
{
    margin-bottom: 10px;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}
.account-wall
{
    margin-top: 20px;
    padding: 40px 0px 20px 0px;
    background-color: #f7f7f7;
    -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
}
.login-title
{
    color: #555;
    font-size: 18px;
    font-weight: 400;
    display: block;
}
.profile-img
{
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
</style>
";
?>