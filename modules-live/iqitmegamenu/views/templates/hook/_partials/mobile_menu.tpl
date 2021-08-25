{*
* 2007-2017 IQIT-COMMERCE.COM
*
* NOTICE OF LICENSE
*
*  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
*  @copyright 2007-2017 IQIT-COMMERCE.COM
*  @license   GNU General Public License version 2
*
* You can not resell or redistribute this software.
*
*}

{function name="mobile_links" nodes=[] first=false}
	{strip}
		{if $nodes|count}
			{if !$first}<ul>{/if}
			{foreach from=$nodes item=node}
				{if isset($node.title)}
					<li>{if isset($node.children)}<div class="responsiveInykator">+</div>{/if}<a href="{$node.href}">{$node.title}</a>
						{if isset($node.children)}
							{mobile_links nodes=$node.children first=false}
						{/if}
					</li>
				{/if}
			{/foreach}
			{if !$first}</ul>{/if}
		{/if}
	{/strip}
{/function}


{if isset($menu)}
	{mobile_links nodes=$menu first=true}
{/if}
