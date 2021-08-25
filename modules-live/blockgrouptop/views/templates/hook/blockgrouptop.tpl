{* 
* @Module Name: Blockleoblogs
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo Prestashop Theme Framework for Prestashop 1.5.x
*}

<!-- Block languages module -->
<div id="leo_block_top">
	<div class="dropdown js-dropdown">
		<span class="expand-more hidden-sm-down" data-toggle="dropdown">{l s='Setting' mod='blockgrouptop'}</span>
	    <a data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="hidden-sm-down">
	        <i class="material-icons expand-more">&#xE5C5;</i>
	    </a>	    
		<div class="dropdown-menu">
			<div class="language-selector">
				<span>{l s='Language' mod='blockgrouptop'}</span>
				<ul class="link">
					{foreach from=$languages item=language}
			          	<li {if $language.id_lang == $current_language.id_lang} class="current" {/if}>
			            	<a href="{url entity='language' id=$language.id_lang}" class="dropdown-item">{$language.name_simple}</a>
			          	</li>
			        {/foreach}
				</ul>
			</div>
			<div class="currency-selector">
				<span>{l s='Currency' mod='blockgrouptop'}</span>
				<ul class="link">
					{foreach from=$currencies item=currency}
			        	<li {if $currency.current} class="current" {/if}>
			          		<a title="{$currency.name}" rel="nofollow" href="{$currency.url}" class="dropdown-item">{$currency.iso_code} {$currency.sign}</a>
			        	</li>
			      	{/foreach}
				</ul>
			</div>
			{if $enable_userinfo == 1}
				<div class="useinfo-selector">
					<ul class="user-info">
					{if $logged}
					<li>
					  <a
						class="account" 
						href="{$my_account_url}"
						title="{l s='View my customer account' mod='blockgrouptop'}"
						rel="nofollow"
					  >
						<span>{l s='Hello' mod='blockgrouptop'} {$customerName}</span>
					  </a>
					</li>
					<li>
					  <a
						class="logout"
						href="{$logout_url}"
						rel="nofollow"
					  >
						{l s='Sign out' mod='blockgrouptop'}
					  </a>
					</li>
					{else}
					<li>
					  <a
						class="signin"
						href="{$my_account_url}"
						title="{l s='Log in to your customer account' mod='blockgrouptop'}"
						rel="nofollow"
					  >
						<span>{l s='Sign in' mod='blockgrouptop'}</span>
					  </a>
					</li>
					{/if}
					<li>
					<a
					  class="myacount"
					  href="{$my_account_url}"
					  title="{l s='My account' mod='blockgrouptop'}"
					  rel="nofollow"
					>
					  <span>{l s='My account' mod='blockgrouptop'}</span>
					</a>
					</li>
					<li>
					<a
					  class="checkout"
					  href="{url entity='cart' params=['action' => show]}"
					  title="{l s='Checkout' mod='blockgrouptop'}"
					  rel="nofollow"
					>
					  <span>{l s='Checkout' mod='blockgrouptop'}</span>
					</a>
					</li>
					</ul>
				</div>
			{/if}
		</div>
	</div>
</div>

<!-- /Block languages module -->
