{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<fieldset>
	{*
	<div class="panel">
        <div class="panel-content">
            <a class="btn btn-default btn-primary" onclick="javascript:return confirm('{l s='Do you want to copy CSS, JS folder to current theme folder?' mod='leobootstrapmenu'}')" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&leo_copy_lib_to_theme=1">
                <i class="icon-AdminParentPreferences"></i> {l s='Copy CSS, JS to theme' mod='leobootstrapmenu'}</a>
        </div>
	</div>
	*}
	{if count($groups) > 0}
		<div class="panel form-horizontal">
			<h3>{l s='Megamenu Control Panel' mod='leobootstrapmenu'}</h3>
			
			<div class="form-wrapper">
									
				<div class="form-group">
					<label class="control-label col-md-1">{l s='Select Hook' mod='leobootstrapmenu'}</label>
					<div class="col-md-2">
						<select class="list_hook" class=" fixed-width-xl">
							<option {if $clearcache_hook == '' || $clearcache_hook == 'all'}selected="selected"{/if} value="all">{l s='All hook' mod='leobootstrapmenu'}</option>
							{foreach from=$list_hook item=hook}
								<option {if $clearcache_hook == $hook}selected="selected"{/if} value="{$hook}">{$hook}</option>
							{/foreach}
							
						</select>
					</div>
					<div class="col-md-2">
						<a class="clear_cache btn btn-success" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&success=clearcache&hook=">
							<i class="icon-AdminTools"></i> {l s='Clear cache' mod='leobootstrapmenu'}
						</a>
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-md-3">{l s='Backup the database before run correct module to safe' mod='leobootstrapmenu'}</label>
					<div class="col-md-9">
						<a class="megamenu-correct-module btn btn-success" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&success=correct&correctmodule=1">
							<i class="icon-AdminParentPreferences"></i> {l s='Correct module' mod='leobootstrapmenu'}
						</a>
					</div>
					
				</div>
			</div>
		</div>
	{/if}
    <div id="groupLayer" class="panel col-md-12">
        <h3>{l s='Group List' mod='leobootstrapmenu'}</h3>
		{*
        <div class="alert alert-info"><a href="http://www.leotheme.com/guides/prestashop17/leo_slider_layer/" target="_blank">{l s='Click to see configuration guide' mod='leobootstrapmenu'}</a></div>
		*}
		
        
		
		<div class="group-header col-md-8 col-xs-12">
			<ol>
				<li>
					<div class="col-md-1 col-xs-1 text-center">
						<span class="title_box ">
							{l s='ID' mod='leobootstrapmenu'}
						</span>
					</div>
					<div class="col-md-6 col-xs-3">
						<span class="title_box ">
							{l s='Group Name' mod='leobootstrapmenu'}
						</span>
					</div>
					<div class="col-md-1 col-xs-2">
						<span class="title_box ">{l s='Status' mod='leobootstrapmenu'}</span>
					</div>
					<div class="col-md-2 col-xs-2">
						<span class="title_box ">
							{l s='Hook' mod='leobootstrapmenu'}
						</span>
					</div>
					<div class="col-md-2 col-xs-4 text-right">
						<a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&addNewGroup=1" class="btn btn-default">
							<i class="icon-plus"></i> {l s='Add new Group' mod='leobootstrapmenu'}
						</a>
					</div>
				</li>
			</ol>
		</div>
		<div class="group-wrapper col-md-8 col-xs-12">
			<ol class="tree-group">
				{foreach from=$groups item=group}
					<li id="list_group_{$group.id_btmegamenu_group}" class="nav-item">
						
							<div class="col-md-1 col-xs-1 text-center"><strong>#{$group.id_btmegamenu_group|intval}</strong></div>
							<div class="col-md-6 col-xs-3" class="pointer">
								{$group.title|escape:'html':'UTF-8'}
							</div>
							<div class="col-md-1 col-xs-2">
								{$group.status}{* HTML form , no escape necessary *}&nbsp;&nbsp;&nbsp;
							</div>
							
							<div class="col-md-2 col-xs-2">
								{$group.hook}
							</div>

							<div class="col-md-2 col-xs-4">
								<div class="btn-group-action">
									<div class="btn-group pull-right">
										{if $group.id_btmegamenu_group != $curentGroup}
											<a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&editgroup=1&id_group={$group.id_btmegamenu_group|escape:'html':'UTF-8'}" title="{l s='Edit Group' mod='leobootstrapmenu'}" class="edit btn btn-default">
												<i class="icon-pencil"></i> {l s='Edit' mod='leobootstrapmenu'}
											</a>
										{else}
											<a href="#" title="{l s='Editting' mod='leobootstrapmenu'}" class="btn editting" style="color:#BBBBBB">
												{l s='Editting' mod='leobootstrapmenu'}
											</a>
										{/if}
										<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<span class="caret"></span>&nbsp;
										</button>
										<ul class="dropdown-menu">
											
											<li>
												<a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&deletegroup=1&id_group={$group.id_btmegamenu_group|intval}" onclick="if (confirm('{l s='Delete Selected Group?' mod='leobootstrapmenu'}')) {
														return true;
													} else {
														event.stopPropagation();
														event.preventDefault();
													}
													;" title="{l s='Delete' mod='leobootstrapmenu'}" class="delete">
													<i class="icon-trash"></i> {l s='Delete' mod='leobootstrapmenu'}
												</a>
																													
											</li>
											<li>
												<a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&duplicategroup=1&id_group={$group.id_btmegamenu_group|intval}" onclick="if (confirm('{l s='Duplicate Selected Group?' mod='leobootstrapmenu'}')) {
														return true;
													} else {
														event.stopPropagation();
														event.preventDefault();
													}
													;" title="{l s='Duplicate' mod='leobootstrapmenu'}" class="duplicate">
													<i class="icon-copy"></i> {l s='Duplicate' mod='leobootstrapmenu'}
												</a>
																													
											</li>
											
											<li>
												<a href="{$exportLink}&id_group={$group.id_btmegamenu_group|intval}&widgets=1" title="{l s='Export Group With Widgets' mod='leobootstrapmenu'}" class="export">
													<i class="icon-external-link-sign"></i> {l s='Export Group With Widgets' mod='leobootstrapmenu'}
												</a>
																													
											</li>
											
											<li>
												<a href="{$exportLink}&id_group={$group.id_btmegamenu_group|intval}&widgets=0" title="{l s='Export Group Without Widgets' mod='leobootstrapmenu'}" class="export">
													<i class="icon-external-link"></i> {l s='Export Group Without Widgets' mod='leobootstrapmenu'}
												</a>
																													
											</li>
											
										</ul>

									</div>
								</div>				
							</div>
						
					</li> 
				{/foreach}
			</ol>
		</div>
		
		<div class="group-footer import-group col-md-5">
			<form method="post" enctype="multipart/form-data" action="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&importgroup=1">
				<div class="row">
					<div class="form-group">							
						<input type="file" class="hide" name="import_file" id="import_file">
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input type="text" readonly="" name="filename" class="disabled" id="import_file-name">
							<span class="input-group-btn">
								<button class="btn btn-default" name="submitAddAttachments" type="button" id="import_file-selectbutton">
									<i class="icon-folder-open"></i> {l s='Choose a file' mod='leobootstrapmenu'}
								</button>
							</span>
						</div>
						<p class="help-block color_danger">{l s='Please upload *.txt only' mod='leobootstrapmenu'}</p>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-4" for="title_group">
							{l s='Overide group or not:' mod='leobootstrapmenu'}
						</label>
						<div class="input-group col-lg-3 col-xs-3">
							<span class="switch prestashop-switch">
								<input type="radio" value="1" id="override_group_on" name="override_group">
								<label for="override_group_on">
									<i class="icon-check-sign color_success"></i> {l s='Yes' mod='leobootstrapmenu'}
								</label>
								<input type="radio" checked="checked" value="0" id="override_group_off" name="override_group">
								<label for="override_group_off">
									<i class="icon-ban-circle color_danger"></i> {l s='No' mod='leobootstrapmenu'}
								</label>
								<a class="slide-button btn btn-default"></a>
							</span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-lg-4" for="title_group">
							{l s='Overide widgets or not:' mod='leobootstrapmenu'}
						</label>
						<div class="input-group col-lg-3 col-xs-3">
							<span class="switch prestashop-switch">
								<input type="radio" value="1" id="override_widget_on" name="override_widget">
								<label for="override_widget_on">
									<i class="icon-check-sign color_success"></i> {l s='Yes' mod='leobootstrapmenu'}
								</label>
								<input type="radio" checked="checked" value="0" id="override_widget_off" name="override_widget">
								<label for="override_widget_off">
									<i class="icon-ban-circle color_danger"></i> {l s='No' mod='leobootstrapmenu'}
								</label>
								<a class="slide-button btn btn-default"></a>
							</span>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-12">
							<button class="btn btn-default dash_trend_right" name="importGroup" id="import_file_submit_btn" type="submit">
								 {l s='Import Group' mod='leobootstrapmenu'}
							</button>
						</div>
					</div>                                                                                                                            
				</div>
			</form>
		</div>
		
		<div class="group-footer import-widgets col-md-5">
			<form method="post" enctype="multipart/form-data" action="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&importwidgets=1">
				<div class="row">
					<div class="form-group">							
						<input type="file" class="hide" name="import_widgets_file" id="import_widgets_file">
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input type="text" readonly="" name="filename" class="disabled" id="import_widgets_file-name">
							<span class="input-group-btn">
								<button class="btn btn-default" name="submitAddAttachments" type="button" id="import_widgets_file-selectbutton">
									<i class="icon-folder-open"></i> {l s='Choose a file' mod='leobootstrapmenu'}
								</button>
							</span>
						</div>
						<p class="help-block color_danger">{l s='Please upload *.txt only' mod='leobootstrapmenu'}</p>
					</div>					
					
					<div class="form-group">
						<label class="control-label col-lg-4" for="title_group">
							{l s='Overide widgets or not:' mod='leobootstrapmenu'}
						</label>
						<div class="input-group col-lg-3 col-xs-3">
							<span class="switch prestashop-switch">
								<input type="radio" value="1" id="override_import_widgets_on" name="override_import_widgets">
								<label for="override_import_widgets_on">
									<i class="icon-check-sign color_success"></i> {l s='Yes' mod='leobootstrapmenu'}
								</label>
								<input type="radio" checked="checked" value="0" id="override_import_widgets_off" name="override_import_widgets">
								<label for="override_import_widgets_off">
									<i class="icon-ban-circle color_danger"></i> {l s='No' mod='leobootstrapmenu'}
								</label>
								<a class="slide-button btn btn-default"></a>
							</span>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-3">
							<button class="btn btn-default dash_trend_right" name="importWidgets" id="import_widgets_file_submit_btn" type="submit">
								 {l s='Import Widgets' mod='leobootstrapmenu'}
							</button>
						</div>
						
						<div class="col-lg-3">
							{*
							<button class="btn btn-default dash_trend_up" name="exportWidgets" id="export_file_submit_btn" type="submit">
								 {l s='Export Widgets of Shop' mod='leobootstrapmenu'}
							</button>
							*}
							<a class="export-widgets" href="{$exportWidgetsLink}" title="Export Widgets Of Shop">
								<i class="icon-external-link-sign"></i>
								{l s='Export Widgets Of Shop' mod='leobootstrapmenu'}
							</a>
						</div>
					</div>                                                                                                                            
				</div>
			</form>
		</div>
    </div>
</fieldset>
<script type="text/javascript">
	var update_group_position_link = "{$update_group_position_link}";
    $(document).ready(function() {
        //import export fix
        $('#import_file-selectbutton').click(function(e){
                $('#import_file').trigger('click');
        });
        $('#import_file').change(function(e){
                var val = $(this).val();
                var file = val.split(/[\\/]/);
                $('#import_file-name').val(file[file.length-1]);
        });
        $('#import_file_submit_btn').click(function(e){
            if($("#import_file-name").val().indexOf(".txt") != -1){
                if($("#override_group_on").is(":checked")) return confirm("{l s='Are you sure to override group?' mod='leobootstrapmenu'}");
				if($("#override_widget_on").is(":checked")) return confirm("{l s='Are you sure to override widgets?' mod='leobootstrapmenu'}");
                return true;
            }else{
                alert("{l s='Please upload txt file' mod='leobootstrapmenu'}");
                $('#import_file').val("");
                $('#import_file-name').val("");
                return false;
            }
		});

		//import export widgets fix
        $('#import_widgets_file-selectbutton').click(function(e){
                $('#import_widgets_file').trigger('click');
        });
        $('#import_widgets_file').change(function(e){
                var val = $(this).val();
                var file = val.split(/[\\/]/);
                $('#import_widgets_file-name').val(file[file.length-1]);
        });
        $('#import_widgets_file_submit_btn').click(function(e){
            if($("#import_widgets_file-name").val().indexOf(".txt") != -1){
                if($("#override_import_widgets_on").is(":checked")) return confirm("{l s='Are you sure to override widgets?' mod='leobootstrapmenu'}");
                return true;
            }else{
                alert("{l s='Please upload txt file' mod='leobootstrapmenu'}");
                $('#import_widgets_file').val("");
                $('#import_widgets_file-name').val("");
                return false;
            }
		});		
        
        $(".group-preview").click(function() {
            eleDiv = $(this).parent().parent().parent();
            if ($(eleDiv).hasClass("open"))
                eleDiv.removeClass("open");

            var url = $(this).attr("href") + "&content_only=1";
            $('#dialog').remove();
            $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe name="iframename2" src="' + url + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
            $('#dialog').dialog({
                title: 'Preview Management',
                close: function(event, ui) {

                },
                bgiframe: true,
                width: 1024,
                height: 780,
                resizable: false,
                draggable:false,
                modal: true
            });
            return false;
        });
    });
</script>
