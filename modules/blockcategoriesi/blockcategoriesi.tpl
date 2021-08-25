{if $psversion < "1.4.0.0"}
<script type="text/javascript" src="modules/blockcategoriesi/treeManagement.js"></script>
<!-- Block categories module -->
<div id="categories_block_left" class="block">
	<h4>{l s='Categories' mod='blockcategoriesi'}</h4>
	<div class="block_content">
		<ul class="tree {if $isDhtml}dhtml{/if}">
		{foreach from=$blockCategTree.children item=child name=blockCategTree}
			{if $smarty.foreach.blockCategTree.last}
						{include file=$branche_tpl_path node=$child last='true'}
			{else}
						{include file=$branche_tpl_path node=$child}
			{/if}
		{/foreach}
		</ul>
	</div>
</div>
<script type="text/javascript">
// <![CDATA[
	// we hide the tree only if JavaScript is activated
	$('div#categories_block_left ul.dhtml').hide();
// ]]>
</script>
{/if}
<!-- /Block categories module -->
{if $psversion > "1.4.0.0" && $psversion < "1.5.0.0"}

<!-- Block categories module -->

	
	
	
		
		{foreach from=$blockCategTree.children item=child name=blockCategTree}
			{if $smarty.foreach.blockCategTree.last}
				{include file="$branche_tpl_path" node=$child last='true'}
			{else}
				{include file="$branche_tpl_path" node=$child}
			{/if}
		{/foreach}
		
		{* Javascript moved here to fix bug #PSCFI-151 *}
		<script type="text/javascript">
		// <![CDATA[
			// we hide the tree only if JavaScript is activated
			$('div#categories_block_left ul.dhtml').hide();
		// ]]>
		</script>

<script type="text/javascript">
// <![CDATA[
	// we hide the tree only if JavaScript is activated
	$('div#categories_block_left ul.dhtml').hide();
// ]]>
</script>
<!-- /Block categories module -->
{/if}

{if $psversion > "1.5.0.0"}
		
		{foreach from=$blockCategTree.children item=child name=blockCategTree}
		
		{include file="$branche_tpl_path" node=$child}
		
		
{*{if $smarty.foreach.blockCategTree.last}

				{include file="$branche_tpl_path" node=$child last='true'}
			{else}
				{include file="$branche_tpl_path" node=$child}
			{/if}*}
		
		{/foreach}
	
		{* Javascript moved here to fix bug #PSCFI-151 *}
		<script type="text/javascript">
		// <![CDATA[
			// we hide the tree only if JavaScript is activated
			$('div#categories_block_left ul.dhtml').hide();
		// ]]>
		</script>

{/if}