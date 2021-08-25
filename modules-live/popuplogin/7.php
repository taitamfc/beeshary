<?PHP

echo '
<form name="login-form" target="_parent" class="login-form" action="' . (($popuplogin->psversion() == 5 || $popuplogin->psversion() == 6 ? $link->getPageLink("authentication") : $link->getPageLink("authentication.php"))) . '' . (Tools::getValue('back', 'false') != 'false' ? '?back=' . Tools::getValue('back') : '') . '" method="post">
    <div class="login">
      <h2>' . $popuplogin->llogin . '</h2>
      <fieldset>
        <input type="text" name="email" type="email" placeholder="' . $popuplogin->lemailaddress . '" autofocus  />
        <input type="hidden" name="submitLogin" value="1">
        <input type="text" name="password"  required="yes" placeholder="' . $popuplogin->lpassword . '" type="password"/>
        ' . (Configuration::Get('popuplogin_return') == 1 ? '<input type="hidden" name="back" value="' . $link->getPageLink("authentication") . '"/>' : '') . '
        ' . (Configuration::Get('popuplogin_return') == 2 ? '<input type="hidden" name="back" value="' . $link->getPageLink("index") . '"/>' : '') . '
        ' . (Configuration::Get('popuplogin_return') == 3 ? '<input type="hidden" name="back" value="' . Tools::getValue('back') . '"/>' : '') . '
      </fieldset>
      <input type="submit" name="SubmitLogin" value="' . $popuplogin->lletmein . '" class="button" />
      ' . $popuplogin->runhook('popuplogin') . '
      <div class="utilities">
          <a class="register" href="' . (($popuplogin->psversion() == 5 || $popuplogin->psversion() == 6 ? $link->getPageLink("password") : $link->getPageLink("password.php"))) . '" target="_parent">' . $popuplogin->fpassword . '</a>
    	  ' . (Configuration::Get('popuplogin_register') == 1 ? '<a class="register" href="' . (($popuplogin->psversion() == 5 || $popuplogin->psversion() == 6 ? $link->getPageLink("authentication") : $link->getPageLink("authentication.php"))) . '" target="_parent">' . $popuplogin->raccount . '</a>' : '') . '
      </div>
    </div>
</form>
';

echo "
<style>
/*! normalize.css v4.0.0 | MIT License | github.com/necolas/normalize.css */html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}article,aside,details,figcaption,figure,footer,header,main,menu,nav,section,summary{display:block}audio,canvas,progress,video{display:inline-block}audio:not([controls]){display:none;height:0}progress{vertical-align:baseline}template,[hidden]{display:none}a{background-color:transparent}a:active,a:hover{outline-width:0}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}b,strong{font-weight:inherit}b,strong{font-weight:bolder}dfn{font-style:italic}h1{font-size:2em;margin:0.67em 0}mark{background-color:#ff0;color:#000}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-0.25em}sup{top:-0.5em}img{border-style:none}svg:not(:root){overflow:hidden}code,kbd,pre,samp{font-family:monospace, monospace;font-size:1em}figure{margin:1em 40px}hr{box-sizing:content-box;height:0;overflow:visible}button,input,select,textarea{font:inherit;margin:0}optgroup{font-weight:bold}button,input,select{overflow:visible}button,select{text-transform:none}button,[type=\"button\"],[type=\"reset\"],[type=\"submit\"]{cursor:pointer}[disabled]{cursor:default}button,html [type=\"button\"],[type=\"reset\"],[type=\"submit\"]{-webkit-appearance:button}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}button:-moz-focusring,input:-moz-focusring{outline:1px dotted ButtonText}fieldset{border:1px solid #c0c0c0;margin:0 2px;padding:0.35em 0.625em 0.75em}legend{box-sizing:border-box;color:inherit;display:table;max-width:100%;padding:0;white-space:normal}textarea{overflow:auto}[type=\"checkbox\"],[type=\"radio\"]{box-sizing:border-box;padding:0}[type=\"number\"]::-webkit-inner-spin-button,[type=\"number\"]::-webkit-outer-spin-button{height:auto}[type=\"search\"]{-webkit-appearance:textfield}[type=\"search\"]::-webkit-search-cancel-button,[type=\"search\"]::-webkit-search-decoration{-webkit-appearance:none}

body {
  font-family: 'Myriad Pro', 'Trebuchet MS', sans-serif;
  background: #137f4f;
  height:340px;
}

.login {
  width: 340px;
  background-color: #1ea167;
  border-radius: 5px;
  padding: 20px;
  background-image: -webkit-linear-gradient(90deg, #168d59, #1ea167);
  position: absolute;
  left: 50%;
  top: 50px;
  margin-left: -180px;
  box-shadow: 0px 1px 0px rgba(255, 255, 255, 0.2) inset, 0px 0px 2px rgba(0, 0, 0, 0.5);
}
.login h2 {
  color: white;
  font-size: 20px;
  margin: 0 0 15px;
  text-shadow: 0px -1px rgba(0, 0, 0, 0.5);
}
.login fieldset {
  border: 0;
  padding: 0;
  margin-bottom: 10px;
}
.login fieldset input {
    outline: none;
    width: 100%;
    height: 12px;
    display: block;
    background: #138050;
    border: 1px solid #0d6b42;
    margin: 0;
    padding: 18px 10px;
    font-size: 13px;
}
.login fieldset input:focus, .login fieldset input:active {
  background-color: #1ea167;
}
.login fieldset input:nth-child(1) {
  border-radius: 5px 5px 0 0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) inset;
}
.login fieldset input:nth-child(2) {
  position: relative;
  top: -1px;
  border-radius: 0 0 5px 5px;
  box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1) inset, 0 1px 0 rgba(255, 255, 255, 0.4);
}
.login fieldset ::-webkit-input-placeholder {
  color: rgba(255, 255, 255, 0.5);
}
.login input[type=\"submit\"] {
  margin: 0;
  display: block;
  padding: 13px 0;
  width: 335px;
  font-size: 13px;
  font-weight: bold;
  border: 0;
  text-shadow: 0px 1px 0px rbga(255, 255, 255, 1);
  background-color: #f6ba35;
  background-image: -webkit-linear-gradient(90deg, #eca418, #ffcd4e);
  border-radius: 5px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
}
.login .utilities {
  margin-top: 20px;
}
.login .utilities a {
  color: #61e5ab;
  text-decoration: none;
  font-size: 12px;
  text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.4);
}
.login .utilities a:hover {
  color: white;
}
.login .utilities a:nth-child(2) {
  display: block;
  float: right;
  width: 50%;
  text-align: right;
}
.login:before, .login:after {
  z-index: -1;
  position: absolute;
  content: \"\";
  left: 5px;
  width: 53%;
  top: 15px;
  height: 80%;
  bottom: 80%;
  max-width: 300px;
  background: rgba(0, 0, 0, 0.7);
  box-shadow: -10px -15px 20px rgba(0, 0, 0, 0.2);
  -webkit-transform: rotate(-3deg);
}
.login:after {
  box-shadow: 10px -15px 20px rgba(0, 0, 0, 0.2) !important;
  -webkit-transform: rotate(3deg);
  right: 5px;
  left: auto;
}

</style>
";
?>