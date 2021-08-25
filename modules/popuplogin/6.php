<?PHP



echo '
<div class="stand">
  <div class="outer-screen">
    <div class="inner-screen">
      <div class="form">
      	<form name="login-form" target="_parent" class="login-form" action="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("authentication"):$link->getPageLink("authentication.php"))).''.(Tools::getValue('back','false')!='false' ? '?back='.Tools::getValue('back'):'').'" method="post">
        <input type="text" name="email" type="email" class="zocial-dribbble" placeholder="'.$popuplogin->lemailaddress.'" autofocus />
        <input type="hidden" name="submitLogin" value="1">
        <input type="text" name="password" type="password" required="yes" placeholder="'.$popuplogin->lpassword.'" />
        <input type="submit" name="SubmitLogin" value="'.$popuplogin->lletmein.'"/>
        '.$popuplogin->runhook('popuplogin').'
        '.(Configuration::Get('popuplogin_return')==1 ? '<input type="hidden" name="back" value="'.$link->getPageLink("authentication").'"/>':'').'
        '.(Configuration::Get('popuplogin_return')==2 ? '<input type="hidden" name="back" value="'.$link->getPageLink("index").'"/>':'').'
        '.(Configuration::Get('popuplogin_return')==3 ? '<input type="hidden" name="back" value="'.Tools::getValue('back').'"/>':'').'
        <a class="register" href="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("password"):$link->getPageLink("password.php"))).'" target="_parent">'.$popuplogin->fpassword.'</a>
		'.(Configuration::Get('popuplogin_register')==1 ? '<a class="register" href="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("authentication"):$link->getPageLink("authentication.php"))).'" target="_parent">'.$popuplogin->raccount.'</a>':'').'
		</form>
      </div>
    </div>
  </div>
</div>
<script src=\'//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js\'></script>

';

echo "
<style>

body{
  margin: 0px;
  padding: 0px;
  background: #1abc9d;
  height:420px;
}


.inner-screen{
  width: 100%;
  background: #1abc9d;
  margin: 0px auto;
  padding-top: 80px;
}

.form{
  width: 400px;
  background: #edeff1;
  margin: 0px auto;
  padding: 10px 0px;
  border-radius: 10px;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
}

input[type=\"text\"]{
  display: block;
  width: 309px;
  height: 35px;
  margin: 15px auto;
  background: #fff;
  border: 0px;
  padding: 5px;
  font-size: 16px;
   border: 2px solid #fff;
  transition: all 0.3s ease;
  border-radius: 5px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
}

input[type=\"text\"]:focus{
  border: 2px solid #1abc9d
}

input[type=\"submit\"]{
  display: block;
  background: #1abc9d;
  width: 314px;
  padding: 12px;
  cursor: pointer;
  color: #fff;
  border: 0px;
  margin: auto;
  border-radius: 5px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  font-size: 17px;
  transition: all 0.3s ease;
}

input[type=\"submit\"]:hover{
  background: #09cca6
}

a{
  text-align: center;
  font-family: Arial;
  color: gray;
  display: block;
  margin: 15px auto;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 12px;
}

a:hover{
  color: #1abc9d;
}


::-webkit-input-placeholder {
   color: gray;
}

:-moz-placeholder { /* Firefox 18- */
   color: gray;
}

::-moz-placeholder {  /* Firefox 19+ */
   color: gray;
}

:-ms-input-placeholder {
   color: gray;
}
</style>
";
?>