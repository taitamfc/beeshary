<?PHP



echo '
<div id="wrapper">
	<form name="login-form" target="_parent" class="login-form" action="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("authentication"):$link->getPageLink("authentication.php"))).''.(Tools::getValue('back','false')!='false' ? '?back='.Tools::getValue('back'):'').'" method="post">
		<div class="header">
		<h1>'.$popuplogin->llogin.'</h1>
		</div>
		<div class="content">'.$popuplogin->runhook('popuplogin').'
		<input name="email" type="email" class="input username" required="yes"  placeholder="'.$popuplogin->lemailaddress.'" autofocus />
		<div class="user-icon"></div>
		<input type="hidden" name="submitLogin" value="1">
		<input style="margin-left:14px;" name="password" type="password" class="input password" required="yes" placeholder="'.$popuplogin->lpassword.'"/>
		<div class="pass-icon"></div>		
		</div>
		<div class="footer">
		<input type="submit" name="SubmitLogin" value="'.$popuplogin->lletmein.'" class="button" />
        '.(Configuration::Get('popuplogin_return')==1 ? '<input type="hidden" name="back" value="'.$link->getPageLink("authentication").'"/>':'').'
        '.(Configuration::Get('popuplogin_return')==2 ? '<input type="hidden" name="back" value="'.$link->getPageLink("index").'"/>':'').'
        '.(Configuration::Get('popuplogin_return')==3 ? '<input type="hidden" name="back" value="'.Tools::getValue('back').'"/>':'').'
        <a class="register" href="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("password"):$link->getPageLink("password.php"))).'" target="_parent">'.$popuplogin->fpassword.'</a>
		'.(Configuration::Get('popuplogin_register')==1 ? '<a class="register" href="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("authentication"):$link->getPageLink("authentication.php"))).'" target="_parent">'.$popuplogin->raccount.'</a>':'').'
		</div>
	</form>
</div>
<div class="gradient"></div>
';

echo "
 <style>
body {
  font: 12px/20px 'Lucida Grande', Verdana, sans-serif;
  color: #404040;
  background: #ebc9a2;
  width:600px;
  height:310px;
  overflow:hidden;
}
@import url(//fonts.googleapis.com/css?family=Bree+Serif);

/*******************
SELECTION STYLING
*******************/

::selection {
	color: #fff;
	background: #f676b2; /* Safari */
}
::-moz-selection {
	color: #fff;
	background: #f676b2; /* Firefox */
}

/*******************
BODY STYLING
*******************/

* {
	margin: 0;
	padding: 0;
	border: none;
	outline: none;
}

body {
	background: url('./images5/bg.png');
	font-family: \"HelveticaNeue-Light\", \"Helvetica Neue Light\", \"Helvetica Neue\", Helvetica, Arial, \"Lucida Grande\", sans-serif;
	font-weight:300;
	text-align: left;
	text-decoration: none;
	height: 310px;
}

#wrapper {
	/* Center wrapper perfectly */
	width: auto;
	height: 310px;
	margin: 10px auto;
}

/* Download Button (Demo Only) */
.download {
	display: block;
	position: absolute;
	float: right;
	right: 25px;
	bottom: 25px;
	padding: 5px;
	
	font-weight: bold;
	font-size: 11px;
	text-align: right;
	text-decoration: none;
	color: rgba(0,0,0,0.5);
	text-shadow: 1px 1px 0 rgba(256,256,256,0.5);
}

.download:hover {
	color: rgba(0,0,0,0.75);
	text-shadow: 1px 1px 0 rgba(256,256,256,0.5);
}

.download:focus {
	bottom: 24px;
}



.gradient {
	/* Center Positioning */
	width: 600px;
	height: 600px;
	position: fixed;
	left: 50%;
	top: 50%;
	margin-left: -300px;
	margin-top: -300px;
	z-index: -2;
	
	/* Fallback */ 
	background-image: url(./images5/gradient.png); 
	background-repeat: no-repeat; 
	
	/* CSS3 Gradient */
	background-image: -webkit-gradient(radial, 0% 0%, 0% 100%, from(rgba(213,246,255,1)), to(rgba(213,246,255,0)));
	background-image: -webkit-radial-gradient(50% 50%, 40% 40%, rgba(213,246,255,1), rgba(213,246,255,0));
	background-image: -moz-radial-gradient(50% 50%, 50% 50%, rgba(213,246,255,1), rgba(213,246,255,0));
	background-image: -ms-radial-gradient(50% 50%, 50% 50%, rgba(213,246,255,1), rgba(213,246,255,0));
	background-image: -o-radial-gradient(50% 50%, 50% 50%, rgba(213,246,255,1), rgba(213,246,255,0));
}

/*******************
LOGIN FORM
*******************/

.login-form {
	width: 450px;
	margin: 40px auto;
	position: relative;
	
	background: #f3f3f3;
	border: 1px solid #fff;
	border-radius: 5px;
	
	box-shadow: 0 1px 3px rgba(0,0,0,0.5);
	-moz-box-shadow: 0 1px 3px rgba(0,0,0,0.5);
	-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.5);
}

/*******************
HEADER
*******************/

.login-form .header {
	padding: 20px 30px 0px 30px
}

.login-form .header h1 {
	font-family: 'Bree Serif', serif;
	font-weight: 300;
	font-size: 28px;
	line-height:34px;
	color: #414848;
	text-shadow: 1px 1px 0 rgba(256,256,256,1.0);
	margin-bottom: 10px;
}

.login-form .header span {
	font-size: 11px;
	line-height: 16px;
	color: #678889;
	text-shadow: 1px 1px 0 rgba(256,256,256,1.0);
}

/*******************
CONTENT
*******************/

.login-form .content {
	padding: 0 30px 75px 30px;
}

/* Input field */
.login-form .content .input {
	width: 188px;
	padding: 15px 25px;
	
	font-family: \"HelveticaNeue-Light\", \"Helvetica Neue Light\", \"Helvetica Neue\", Helvetica, Arial, \"Lucida Grande\", sans-serif;
	font-weight: 400;
	font-size: 14px;
	color: #9d9e9e;
	text-shadow: 1px 1px 0 rgba(256,256,256,1.0);
	
	background: #fff;
	border: 1px solid #fff;
	border-radius: 5px;
	
	box-shadow: inset 0 1px 3px rgba(0,0,0,0.50);
	-moz-box-shadow: inset 0 1px 3px rgba(0,0,0,0.50);
	-webkit-box-shadow: inset 0 1px 3px rgba(0,0,0,0.50);
    display:inline-block;
    float:left;
}

/* Second input field */
.login-form .content .password, .login-form .content .pass-icon {
	margin-top: 0px;
}

.login-form .content .input:hover {
	background: #dfe9ec;
	color: #414848;
}

.login-form .content .input:focus {
	background: #dfe9ec;
	color: #414848;
	
	box-shadow: inset 0 1px 2px rgba(0,0,0,0.25);
	-moz-box-shadow: inset 0 1px 2px rgba(0,0,0,0.25);
	-webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,0.25);
}

.user-icon, .pass-icon {
	width: 46px;
	height: 47px;
	display: block;
	position: absolute;
	left: 0px;
	padding-right: 2px;
	z-index: -1;
	
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-bottomleft: 5px;
	-webkit-border-top-left-radius: 5px;
	-webkit-border-bottom-left-radius: 5px;
}

.user-icon {
	top:66px; /* Positioning fix for slide-in, got lazy to think up of simpler method. */
	background: rgba(65,72,72,0.75) url(./images5/user-icon.png) no-repeat center;	
}

.pass-icon {
	top:66px;
	background: rgba(65,72,72,0.75) url(./images5/pass-icon.png) no-repeat center;
}

.content input:focus + div{
	left: -46px;
}

/* Animation */
.input, .user-icon, .pass-icon, .button, .register {
	transition: all 0.5s ease;
	-moz-transition: all 0.5s ease;
	-webkit-transition: all 0.5s ease;
	-o-transition: all 0.5s ease;
	-ms-transition: all 0.5s ease;
}

/*******************
FOOTER
*******************/

.login-form .footer {
	padding: 10px 30px 10px 30px;
	overflow: auto;
	
	background: #d4dedf;
	border-top: 1px solid #fff;
	
	box-shadow: inset 0 1px 0 rgba(0,0,0,0.15);
	-moz-box-shadow: inset 0 1px 0 rgba(0,0,0,0.15);
	-webkit-box-shadow: inset 0 1px 0 rgba(0,0,0,0.15);
}

/* Login button */
.login-form .footer .button {
	float:right;
	padding: 11px 25px;
	
	font-family: 'Bree Serif', serif;
	font-weight: 300;
	font-size: 18px;
	color: #fff;
	text-shadow: 0px 1px 0 rgba(0,0,0,0.25);
	
	background: #56c2e1;
	border: 1px solid #46b3d3;
	border-radius: 5px;
	cursor: pointer;
	
	box-shadow: inset 0 0 2px rgba(256,256,256,0.75);
	-moz-box-shadow: inset 0 0 2px rgba(256,256,256,0.75);
	-webkit-box-shadow: inset 0 0 2px rgba(256,256,256,0.75);
}

.login-form .footer .button:hover {
	background: #3f9db8;
	border: 1px solid rgba(256,256,256,0.75);
	
	box-shadow: inset 0 1px 3px rgba(0,0,0,0.5);
	-moz-box-shadow: inset 0 1px 3px rgba(0,0,0,0.5);
	-webkit-box-shadow: inset 0 1px 3px rgba(0,0,0,0.5);
}

.login-form .footer .button:focus {
	position: relative;
	bottom: -1px;
	
	background: #56c2e1;
	
	box-shadow: inset 0 1px 6px rgba(256,256,256,0.75);
	-moz-box-shadow: inset 0 1px 6px rgba(256,256,256,0.75);
	-webkit-box-shadow: inset 0 1px 6px rgba(256,256,256,0.75);
}

/* Register button */
.login-form .footer .register {
	display: inline-block;
	float: right;
	padding: 10px;
	margin-right: 20px;
	
	background: none;
	border: none;
	cursor: pointer;
	
	font-family: 'Bree Serif', serif;
	font-weight: 300;
	font-size: 14px;
	color: #414848;
	text-shadow: 0px 1px 0 rgba(256,256,256,0.5);
}

.login-form .footer .register:hover {
	color: #3f9db8;
}

.login-form .footer .register:focus {
	position: relative;
	bottom: -1px;
}

 </style>
";

?>