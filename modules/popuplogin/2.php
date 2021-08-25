<?php

echo '
<script>


	$(document).ready(function(){
 
	$("#submit1").hover(
	function() {
	$(this).animate({"opacity": "0"}, "slow");
	},
	function() {
	$(this).animate({"opacity": "1"}, "slow");
	});
 	});


</script>
<form method="post" target="_parent" action="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("authentication"):$link->getPageLink("authentication.php"))).''.(Tools::getValue('back','false')!='false' ? '?back='.Tools::getValue('back'):'').'" style="height:300px; overflow:hidden;">
	<div id="wrapper">
		<div id="wrappertop"></div>
		<div id="wrappermiddle">
			<h2>'.$popuplogin->llogin.'</h2>
			<div id="username_input">
				<div id="username_inputleft"></div>
				<div id="username_inputmiddle">
					<input required="required" type="text" name="email" id="url" value="'.$popuplogin->lemailaddress.'" onclick="this.value = \'\'">
					<img id="url_user" src="./images/mailicon.png" alt="">
				</div>
				<div id="username_inputright"></div>
			</div>
			<div id="password_input">
				<div id="password_inputleft"></div>
				<div id="password_inputmiddle">
				    <input type="hidden" name="submitLogin" value="1">
					<input required="required" type="password" name="password" id="url" value="'.$popuplogin->lpassword.'" onclick="this.value = \'\'">
					<img id="url_password" src="./images/passicon.png" alt="">
				</div>
				<div id="password_inputright"></div>
			</div>
			<div id="submit">
				<form>
				<input type="image" src="./images/submit_hover.png" id="submit1" name="SubmitLogin" value="'.$popuplogin->lletmein.'">
				<input type="image" src="./images/submit.png" id="submit2" name="SubmitLogin" value="'.$popuplogin->lletmein.'">
                '.(Configuration::Get('popuplogin_return')==1 ? '<input type="hidden" name="back" value="'.$link->getPageLink("authentication").'"/>':'').'
                '.(Configuration::Get('popuplogin_return')==2 ? '<input type="hidden" name="back" value="'.$link->getPageLink("index").'"/>':'').'
                '.(Configuration::Get('popuplogin_return')==3 ? '<input type="hidden" name="back" value="'.Tools::getValue('back').'"/>':'').'
				</form>
			</div>
			<div id="links_left">
			<a href="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("password"):$link->getPageLink("password.php"))).'" target="_parent">'.$popuplogin->fpassword.'</a>
			</div>
			<div id="links_right">
            '.(Configuration::Get('popuplogin_register')==1 ? '<a href="'.(($popuplogin->psversion()==5 || $popuplogin->psversion()==6 ? $link->getPageLink("authentication"):$link->getPageLink("authentication.php"))).'" target="_parent">'.$popuplogin->raccount.'</a>':'').'
            </div>
		</div>
		<div id="wrapperbottom"></div>
	</div>
</form>
';

echo "
<style>
html {margin:0;padding:0;border:0;}body, div, span, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, code, del, dfn, em, img, q, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, aside, dialog, figure, footer, header, hgroup, nav, section {margin:0;padding:0;border:0;font-weight:inherit;font-style:inherit;font-size:100%;font-family:inherit;vertical-align:baseline;}article, aside, dialog, figure, footer, header, hgroup, nav, section {display:block;}body {line-height:1.5;background:white;}table {border-collapse:separate;border-spacing:0;}caption, th, td {text-align:left;font-weight:normal;float:none !important;}table, th, td {vertical-align:middle;}blockquote:before, blockquote:after, q:before, q:after {content:'';}blockquote, q {quotes:\"\" \"\";}a img {border:none;}:focus {outline:0;}

html {
	min-height: 100%;
}
body {
    width: 600px;
	height:300px;
	position: relative;
	font-family: Arial, Helvetica, sans-serif;
	color: #888;
	font-size: 13px;
	line-height: 20px;
	background:url(./images/BG.jpg);
}
#wrapper {	
	height: 100%;
}
#wrapper h2 {
    margin-top:0px;
    }
#wrapper {
	width: 350px;
	margin:auto;
	position: relative;
    padding-top:20px;
}

#wrappertop {
	background:url(./images/wrapper_top.png) no-repeat;
	height:22px;
}

#wrappermiddle {
	background:url(./images/wrapper_middle.png) repeat-y;
	height:210px;
}

#wrapperbottom {
	background:url(./images/wrapper_bottom.png) no-repeat;
	height:22px;
}

#wrapper h2 {
	margin-left:20px;
	font-size:20px;
	font-weight:bold;
	font-family:Myriad Pro;
	text-transform:uppercase;
	position:absolute;
	text-shadow: #fff 2px 2px 2px;
}

#username_input {
	margin-left:25px;
	position:absolute;
	width:300;
	height:50px;
	margin-top:30px;
}

#username_inputleft {
	float:left;
	background:url(./images/input_left.png) no-repeat;
	width:12px;
	height:50px;
}

#username_inputmiddle {
	float:left;
	background:url(./images/input_middle.png) repeat-x;
	width:276px;
	height:50px;
}

#username_inputright {
	float:left;
	background:url(./images/input_right.png) no-repeat;
	width:12px;
	height:50px;
}

#url{
	display:block;
	width:276px;
	height:45px;
	background:transparent;
	border:0;
	color:#bdbdbd;
	font-family:helvetica, sans-serif;
	font-size:14px;
	padding-left:20px;
}

#url_user {
	position:absolute;
	display:block;
	margin-top:-28px;
	float:left;
	padding-right:10px;
}

#password_input {
	margin-left:25px;
	position:absolute;
	width:300;
	height:50px;
	margin-top:90px;
}

#password_inputleft {
	float:left;
	background:url(./images/input_left.png) no-repeat;
	width:12px;
	height:50px;
}

#password_inputmiddle {
	float:left;
	background:url(./images/input_middle.png) repeat-x;
	width:276px;
	height:50px;
}

#password_inputright {
	float:left;
	background:url(./images/input_right.png) no-repeat;
	width:12px;
	height:50px;
}

#url_password {
	display:block;
	position:absolute;
	margin-top:-32px;
	float:left;
	margin-left:4px;
}

#submit{
	float:left;
	position:relative;
	padding:0;
	margin-top:150px;
	margin-left:25px;
	width:300px;
	height:40px;
	border:0;
}

#submit1 {
	position:absolute;
	z-index: 10;
	border:0;
}

#submit2 {
	position:absolute;
	margin-top:0px;
	border:0;
}

#links_left{
	float:left;
	position:relative;
	padding-top:5px;
	margin-left:25px;
}

#links_left a{
	color:#bbb;
	font-size:11px;
	text-decoration:none;
	transition: color 0.5s linear;
	-moz-transition: color 0.5s linear;
	-webkit-transition: color 0.5s linear;
	-o-transition: color 0.5s linear;
}

#links_left a:hover{
	color:#292929;
}

#links_right{
	float:right;
	position:relative;
	padding-top:5px;
	margin-right:25px;
}

#links_right a{
	color:#bbb;
	font-size:11px;
	text-decoration:none;
	transition: color 0.5s linear;
	-moz-transition: color 0.5s linear;
	-webkit-transition: color 0.5s linear;
	-o-transition: color 0.5s linear;
} 

#links_right a:hover{
	color:#292929;
}

#powered{
	float:right;
	position:relative;
	padding-top:3px;
	margin-right:5px;
	font-size:11px;
}

#powered a{
	color:#aaa;
	font-size:11px;
	text-decoration:none;
	transition: color 0.5s linear;
	-moz-transition: color 0.5s linear;
	-webkit-transition: color 0.5s linear;
	-o-transition: color 0.5s linear;
}

#powered a:hover{
	color:#292929;
}
</style>
";

?>