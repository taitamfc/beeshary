
{if $psversion < "1.4.0.0"}

  <div>
                                <div class="category-wrapper">
                                    <a href="javascript:void(0);">
                                        <div class="category-img">
                                            <img src="{$link->getCatImageLink($node.name, $node.id, 'small')}" alt="" />
                                        </div>
                                        <h3>{$node.name|escape:htmlall:'UTF-8'}</h3>
                                    </a>
                                </div>
                            </div>
							
							
<li {if $last == 'true'}class="last"{/if} style="width:160px;">
<img src="{$link->getCatImageLink($node.name, $node.id, 'small')}" align="absmiddle" width="{$image}" style="margin-bottom:3px; float:right" ><a href="{$node.link|escape:htmlall:'UTF-8'}" {if isset($currentCategoryId) && ($node.id == $currentCategoryId)}class="selected"{/if} title="{$node.desc|escape:htmlall:'UTF-8'}" style="margin-left:2px">{$node.name|escape:htmlall:'UTF-8'}</a>
    {if $node.children|@count > 0}
        <ul>
        {foreach from=$node.children item=child name=categoryTreeBranch}
            {if $smarty.foreach.categoryTreeBranch.last}
                    <img src="{$link->getCatImageLink( $child.name, $child.id, 'small')}" align="absmiddle" width="{$images}" style="margin-left:3px; margin-bottom:3px; float:right"><a href="{$child.link|escape:htmlall:'UTF-8'}" {if isset($currentCategoryId) && ($node.id == $currentCategoryId)}class="selected"{/if} title="{$child.desc|escape:htmlall:'UTF-8'}" style="margin-left:8px; font-size:10px">{$child.name|escape:htmlall:'UTF-8'}</a>
            {else}
                        <img src="{$link->getCatImageLink($child.name, $child.id, 'small')}" align="absmiddle" width="{$images}" style="margin-left:3px; margin-bottom:3px; float:right" ><a href="{$child.link|escape:htmlall:'UTF-8'}" {if isset($currentCategoryId) && ($node.id == $currentCategoryId)}class="selected"{/if} title="{$child.desc|escape:htmlall:'UTF-8'}" style="margin-left:8px; font-size:10px">{$child.name|escape:htmlall:'UTF-8'}</a><br/>
            {/if}
        {/foreach}
        </ul>
    {/if}
</li> 
{/if}
{if $psversion > "1.4.0.0" && $psversion < "1.5.0.0"}
 <div>
                                <div class="category-wrapper">
                                    <a href="javascript:void(0);">
                                        <div class="category-img">
                                            <img src="{$link->getCatImageLink($node.name, $node.id, 'small')}" alt="" />
                                        </div>
                                        <h3>{$node.name|escape:htmlall:'UTF-8'}</h3>
                                    </a>
                                </div>
                            </div>
<li {if isset($last) && $last == 'true'}class="last"{/if}>

	<img src="{$img_cat_dir}{$node.id}-small" align="absmiddle" width="{$image}" style="margin-left:3px; margin-bottom:3px; float:right">
  
    <a href="{$node.link}" {if isset($currentCategoryId) && ($node.id == $currentCategoryId)}class="selected"{/if} title="{$node.desc|escape:html:'UTF-8'}"  style="margin-left:2px">{$node.name|escape:html:'UTF-8'}</a>
	{if $node.children|@count > 0}
		<ul>
		{foreach from=$node.children item=child name=categoryTreeBranch}
			{if isset($smarty.foreach.categoryTreeBranch) && $smarty.foreach.categoryTreeBranch.last}
			<img src="{$img_cat_dir}{$child.id}-small" align="absmiddle" width="{$images}" style="margin-bottom:3px; margin-left:8px; float:right" ><a href="{$child.link|escape:htmlall:'UTF-8'}" {if isset($currentCategoryId) && ($node.id == $currentCategoryId)}class="selected"{/if} title="{$child.desc|escape:htmlall:'UTF-8'}" style="margin-left:8px;font-size:10px">{$child.name|escape:htmlall:'UTF-8'}</a>
			{else}
			<img src="{$img_cat_dir}{$child.id}-small" align="absmiddle" width="{$images}" style="margin-bottom:3px;margin-left:8px; float:right" ><a href="{$child.link|escape:htmlall:'UTF-8'}" {if isset($currentCategoryId) && ($node.id == $currentCategoryId)}class="selected"{/if} title="{$child.desc|escape:htmlall:'UTF-8'}" style="margin-left:8px; font-size:10px">{$child.name|escape:htmlall:'UTF-8'}</a><br/>
			{/if}
		{/foreach}
		</ul>
	{/if}
</li>
{/if}
{if $psversion > "1.5.0.0"}

 <div>
                                <div class="category-wrapper">
                                    <a href="{$node.link|escape:'htmlall':'UTF-8'}" {if isset($currentCategoryId) && $node.id == $currentCategoryId}class="selected"{/if} title="{$node.desc|escape:'htmlall':'UTF-8'}">
                                        <div class="category-img">
                                            <img src="{$link->getCatImageLink($node.name, $node.id, 'small_default')}"  width="100%" alt="" />
                                        </div>
                                        <h3>{$node.name|escape:htmlall:'UTF-8'}</h3>
                                    </a>
                                </div></div>
                         
<!--li {if isset($last) && $last == 'true'}class="last"{/if}>
<img src="{$link->getCatImageLink($node.name, $node.id, 'small_default')}" align="absmiddle" width="{$image}" style="margin-left:3px; margin-bottom:3px; float:right">
	<a href="{$node.link|escape:'htmlall':'UTF-8'}" {if isset($currentCategoryId) && $node.id == $currentCategoryId}class="selected"{/if} title="{$node.desc|escape:'htmlall':'UTF-8'}">{$node.name|escape:'htmlall':'UTF-8'}</a>
	{if $node.children|@count > 0}
		<ul>
		{foreach from=$node.children item=child name=categoryTreeBranch}
			{if $smarty.foreach.categoryTreeBranch.last}
				{include file="$branche_tpl_path" node=$child last='true'}
			{else}
				{include file="$branche_tpl_path" node=$child last='false'}
			{/if}
		{/foreach}
		</ul>
	{/if}
</li-->
{/if}