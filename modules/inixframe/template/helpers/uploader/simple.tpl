{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($files) && $files|count > 0}
	{assign var='show_thumbnail' value=false}
	{foreach $files as $file}
		{if isset($file.image) && $file.type == 'image'}
			{assign var='show_thumbnail' value=true}
		{/if}
	{/foreach}
{if $show_thumbnail}
<div class="row">
	<div class="col-lg-12" id="{$id}-images-thumbnails">
		{foreach $files as $file}
		{if isset($file.image) && $file.type == 'image'}
		<div>
			{$file.image}
			{if isset($file.size)}<p>{l s='File size' mod='inixframe'} {$file.size}kb</p>{/if}
			{if isset($file.delete_url)}
			<p>
				<a class="btn btn-default" href="{$file.delete_url}">
					<i class="icon-trash"></i> {l s='Delete' mod='inixframe'}
				</a>
			</p>
			{/if}
		</div>
		{/if}
		{/foreach}
	</div>
</div>
{/if}
{/if}
{if isset($max_files) && $files|count >= $max_files}
<div class="row">
	<div class="note note-warning">{l s='You have reached the limit (%s) of files to upload, please remove files to continue uploading' mod='inixframe' sprintf=$max_files}</div>
</div>
{else}
<div class="row">
	<div class="col-lg-6">
		<input id="{$id}" type="file" name="{$name}{if isset($multiple) && $multiple}[]{/if}"{if isset($multiple) && $multiple} multiple="multiple"{/if} class="hide" />
		<div class="dummyfile input-group">
			<span class="input-group-addon"><i class="icon-file"></i></span>
			<input id="{$id}-name" class="form-control" type="text" name="filename" readonly />
			<span class="input-group-btn">
				<button id="{$id}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
					<i class="icon-folder-open"></i> {if isset($multiple) && $multiple}{l s='Add files' mod='inixframe'}{else}{l s='Add file' mod='inixframe'}{/if}
				</button>
				{if (!isset($multiple) || !$multiple) && isset($files) && $files|count == 1 && isset($files[0].download_url)}
				<a href="{$files[0].download_url}">
					<button type="button" class="btn btn-default">
						<i class="icon-cloud-download"></i>
						{if isset($size)}{l s='Download current file (%skb)' mod='inixframe' sprintf=$size}{else}{l s='Download current file' mod='inixframe'}{/if}
					</button>
				</a>
				{/if}
			</span>
		</div>

	</div>
</div>
{if isset($multiple) && $multiple && isset($files) && $files|count>1}

    <div class="row">
        <div class=" col-md-12">
        <ul>
        {foreach from=$files item=file}
           {if $file.type == 'file'}
             <li class="col-md-12  margin-top-5">
                 <div class="col-lg-8">
                     <i class="icon-file-o"></i> <strong>{$file.name}</strong>
               {if isset($file.size)}<small>{l s='File size' mod='inixframe'} {$file.size}kb</small>{/if}
                 </div>
                 <div class="col-lg-4">
               {if isset($file.delete_url)}
                    <a class="btn btn-danger btn-sm margin-left-5" href="{$file.delete_url}">
                        <i class="icon-trash"></i> {l s='Delete' mod='inixframe'}
                    </a>
               {/if}
               {if isset($file.download_url)}
                   <a href="{$file.download_url}">
                       <button type="button" class="btn btn-primary btn-sm margin-left-5">
                           <i class="icon-cloud-download"></i>
                           {l s='Download' mod='inixframe'}
                       </button>
                   </a>
               {/if}
                 </div>
           </li>
            {/if}
        {/foreach}
        </ul>
    </div>
</div>
{/if}
        <script type="text/javascript">
{if isset($multiple) && isset($max_files)}
	var {$id}_max_files = {$max_files - $files|count};
{/if}

	$(document).ready(function(){
		$('#{$id}-selectbutton').click(function(e) {
			$('#{$id}').trigger('click');
		});

		$('#{$id}-name').click(function(e) {
			$('#{$id}').trigger('click');
		});

		$('#{$id}-name').on('dragenter', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});

		$('#{$id}-name').on('dragover', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});

		$('#{$id}-name').on('drop', function(e) {
			e.preventDefault();
			var files = e.originalEvent.dataTransfer.files;


            $('#{$id}')[0].files = files;

            if ($('#{$id}')[0].files !== undefined)
            {
                var files = $('#{$id}')[0].files;
                var name  = '';

                $.each(files, function(index, value) {
                    name += value.name+', ';
                });

                $(this).val(name.slice(0, -2));
            }
		});

		$('#{$id}').change(function(e) {
			if ($(this)[0].files !== undefined)
			{
				var files = $(this)[0].files;
				var name  = '';

				$.each(files, function(index, value) {
					name += value.name+', ';
				});

				$('#{$id}-name').val(name.slice(0, -2));
			}
			else // Internet Explorer 9 Compatibility
			{
				var name = $(this).val().split(/[\\/]/);
				$('#{$id}-name').val(name[name.length-1]);
			}

		});

		if (typeof {$id}_max_files !== 'undefined')
		{
			$('#{$id}').closest('form').on('submit', function(e) {
				if ($('#{$id}')[0].files.length > {$id}_max_files) {
					e.preventDefault();
					alert('{l s='You can upload a maximum of %s files'|sprintf:$max_files}');
				}
			});
		}
	});
</script>
{/if}