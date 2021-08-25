{* 
* @Module Name: Leo Quick Login
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
*}

<script type="text/javascript">
	window.opener.twitterLogin("{$firstname|escape:'html':'UTF-8'}","{$lastname|escape:'html':'UTF-8'}","{$email|escape:'html':'UTF-8'}");
	self.close();	
</script>