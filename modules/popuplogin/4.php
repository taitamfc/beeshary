<?PHP

echo '
<form method="post" target="_parent" action="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("authentication"):$link->getPageLink("authentication.php"))).''.(Tools::getValue('back','false')!='false' ? '?back='.Tools::getValue('back'):'').'" class="login" >
    <h1>'.$popuplogin->llogin.'</h1>
    <input type="email" name="email" class="login-input" required="yes"  placeholder="'.$popuplogin->lemailaddress.'" autofocus>
     <input type="hidden" name="submitLogin" value="1">
    <input type="password" name="password" required="yes" class="login-input" placeholder="'.$popuplogin->lpassword.'">
    <input type="submit" value="'.$popuplogin->lletmein.'" class="login-submit" name="SubmitLogin">'.$popuplogin->runhook('popuplogin').'
        '.(Configuration::Get('popuplogin_return')==1 ? '<input type="hidden" name="back" value="'.$link->getPageLink("authentication").'"/>':'').'
        '.(Configuration::Get('popuplogin_return')==2 ? '<input type="hidden" name="back" value="'.$link->getPageLink("index").'"/>':'').'
        '.(Configuration::Get('popuplogin_return')==3 ? '<input type="hidden" name="back" value="'.Tools::getValue('back').'"/>':'').'
    <p class="login-help" style="clear:both; overflow:hidden;">		
    <a href="'.$link->getPageLink('authentication').'" target="_parent" style="float:left;">'.$popuplogin->raccount.'</a>
    '.(Configuration::Get('popuplogin_register')==1 ? '<a href="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("authentication"):$link->getPageLink("authentication.php"))).'" target="_parent" style="float:right;">'.$popuplogin->fpassword.'</a>':'').'
    </p>
 </form>
 '."
 <style>
 /*
 * Copyright (c) 2013 Thibaut Courouble
 * http://www.cssflow.com
 * Licensed under the MIT License
 *
 * Sass/SCSS source: http://goo.gl/XUUuN
 * PSD by Alex Montague: http://goo.gl/lMwBA
 */

body {
  font: 12px/20px 'Lucida Grande', Verdana, sans-serif;
  color: #404040;
  background: #ebc9a2;
  width:600px;
  height:350px;
  overflow:hidden;
}

input, textarea, select, label {
  font-family: inherit;
  font-size: 12px;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}

.login {
  margin: 20px auto;
  padding: 18px 20px;
  width: 400px;
  background: #3f65b7;
  background-clip: padding-box;
  border: 1px solid #172b4e;
  border-bottom-color: #142647;
  border-radius: 5px;
  background-image: -webkit-radial-gradient(cover, #437dd6, #3960a6);
  background-image: -moz-radial-gradient(cover, #437dd6, #3960a6);
  background-image: -o-radial-gradient(cover, #437dd6, #3960a6);
  background-image: radial-gradient(cover, #437dd6, #3960a6);
  -webkit-box-shadow: inset 0 1px rgba(255, 255, 255, 0.3), inset 0 0 1px 1px rgba(255, 255, 255, 0.1), 0 2px 10px rgba(0, 0, 0, 0.5);
  box-shadow: inset 0 1px rgba(255, 255, 255, 0.3), inset 0 0 1px 1px rgba(255, 255, 255, 0.1), 0 2px 10px rgba(0, 0, 0, 0.5);
}

.login > h1 {
  margin-bottom: 20px;
  font-size: 16px;
  font-weight: bold;
  color: white;
  text-align: center;
  text-shadow: 0 -1px rgba(0, 0, 0, 0.4);
}

.login-input {
  display: block;
  width: 100%;
  height: 37px;
  margin-bottom: 20px;
  padding: 0 9px;
  color: white;
  text-shadow: 0 1px black;
  background: #2b3e5d;
  border: 1px solid #15243b;
  border-top-color: #0d1827;
  border-radius: 4px;
  background-image: -webkit-linear-gradient(top, rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.2) 20%, rgba(0, 0, 0, 0));
  background-image: -moz-linear-gradient(top, rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.2) 20%, rgba(0, 0, 0, 0));
  background-image: -o-linear-gradient(top, rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.2) 20%, rgba(0, 0, 0, 0));
  background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.2) 20%, rgba(0, 0, 0, 0));
  -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.2);
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.2);
}

.login-input:focus {
  outline: 0;
  background-color: #32486d;
  -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3), 0 0 4px 1px rgba(255, 255, 255, 0.6);
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3), 0 0 4px 1px rgba(255, 255, 255, 0.6);
}

.lt-ie9 .login-input { line-height: 35px; }

.login-submit {
  display: block;
  width: 100%;
  height: 37px;
  margin-bottom: 15px;
  font-size: 14px;
  font-weight: bold;
  color: #294779;
  text-align: center;
  text-shadow: 0 1px rgba(255, 255, 255, 0.3);
  background: #adcbfa;
  background-clip: padding-box;
  border: 1px solid #284473;
  border-bottom-color: #223b66;
  border-radius: 4px;
  cursor: pointer;
  background-image: -webkit-linear-gradient(top, #d0e1fe, #96b8ed);
  background-image: -moz-linear-gradient(top, #d0e1fe, #96b8ed);
  background-image: -o-linear-gradient(top, #d0e1fe, #96b8ed);
  background-image: linear-gradient(to bottom, #d0e1fe, #96b8ed);
  -webkit-box-shadow: inset 0 1px rgba(255, 255, 255, 0.5), inset 0 0 7px rgba(255, 255, 255, 0.4), 0 1px 1px rgba(0, 0, 0, 0.15);
  box-shadow: inset 0 1px rgba(255, 255, 255, 0.5), inset 0 0 7px rgba(255, 255, 255, 0.4), 0 1px 1px rgba(0, 0, 0, 0.15);
}

.login-submit:active {
  background: #a4c2f3;
  -webkit-box-shadow: inset 0 1px 5px rgba(0, 0, 0, 0.4), 0 1px rgba(255, 255, 255, 0.1);
  box-shadow: inset 0 1px 5px rgba(0, 0, 0, 0.4), 0 1px rgba(255, 255, 255, 0.1);
}

.login-help {
  text-align: center;
}

.login-help > a {
  font-size: 11px;
  color: #d4deef;
  text-decoration: none;
  text-shadow: 0 -1px rgba(0, 0, 0, 0.4);
}

.login-help > a:hover {
  text-decoration: underline;
}

::-moz-focus-inner {
  padding: 0;
  border: 0;
}

:-moz-placeholder { color: #bcc0c8 !important; }
::-webkit-input-placeholder { color: #bcc0c8; }
:-ms-input-placeholder { color: #bcc0c8 !important; }

 </style>
";

?>