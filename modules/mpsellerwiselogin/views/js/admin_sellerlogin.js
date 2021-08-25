/**
* 2010-2017 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function()
{
	$('#banner_img-selectbutton').click(function(e)
	{
		$('#banner_img').trigger('click');
	});

	$('#banner_img-name').click(function(e) 
	{
		$('#banner_img').trigger('click');
	});

	$('#banner_img-name').on('dragenter', function(e) 
	{
		e.stopPropagation();
		e.preventDefault();
	});

	$('#banner_img-name').on('dragover', function(e) 
	{
		e.stopPropagation();
		e.preventDefault();
	});

	$('#banner_img-name').on('drop', function(e) 
	{
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;
		$('#banner_img')[0].files = files;
		$(this).val(files[0].name);
	});

	$('#banner_img').change(function(e) 
	{
		if ($(this)[0].files !== undefined)
		{
			var files = $(this)[0].files;
			var name  = '';

			$.each(files, function(index, value) {
				name += value.name+', ';
			});

			$('#banner_img-name').val(name.slice(0, -2));
		}
		else // Internet Explorer 9 Compatibility
		{
			var name = $(this).val().split(/[\\/]/);
			$('#banner_img-name').val(name[name.length-1]);
		}
	});

	if (typeof banner_img_max_files !== 'undefined')
	{
		$('#banner_img').closest('form').on('submit', function(e) 
		{
			if ($('#banner_img')[0].files.length > banner_img_max_files) 
			{
				e.preventDefault();
				alert('You can upload a maximum of  files');
			}
		});
	}


	$('#wk_logo-selectbutton').click(function(e)
	{
		$('#wk_logo').trigger('click');
	});

	$('#wk_logo-name').click(function(e) 
	{
		$('#wk_logo').trigger('click');
	});

	$('#wk_logo-name').on('dragenter', function(e) 
	{
		e.stopPropagation();
		e.preventDefault();
	});

	$('#wk_logo-name').on('dragover', function(e) 
	{
		e.stopPropagation();
		e.preventDefault();
	});

	$('#wk_logo-name').on('drop', function(e) 
	{
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;
		$('#wk_logo')[0].files = files;
		$(this).val(files[0].name);
	});

	$('#wk_logo').change(function(e) 
	{
		if ($(this)[0].files !== undefined)
		{
			var files = $(this)[0].files;
			var name  = '';

			$.each(files, function(index, value) {
				name += value.name+', ';
			});

			$('#wk_logo-name').val(name.slice(0, -2));
		}
		else // Internet Explorer 9 Compatibility
		{
			var name = $(this).val().split(/[\\/]/);
			$('#wk_logo-name').val(name[name.length-1]);
		}
	});

	if (typeof wk_logo_max_files !== 'undefined')
	{
		$('#wk_logo').closest('form').on('submit', function(e) 
		{
			if ($('#wk_logo')[0].files.length > wk_logo_max_files) 
			{
				e.preventDefault();
				alert('You can upload a maximum of  files');
			}
		});
	}

	/*---- Select Theme Controller Js ----*/

	$('#login_theme').on('change', function()
	{
		var id_theme = $('#login_theme').val();
		$('#theme_preview').attr( "src", preview_img_dir+'/theme'+id_theme+'.jpg');
	});

	/*---- Select Theme Controller Js ----*/
});