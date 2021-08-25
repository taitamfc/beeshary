{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<div id="page-content">
        <div id="menu-form" style="display: none; left: 340px; top: 15px; max-width:600px" class="popover top out form-setting">
            <div class="arrow"></div>
            <div style="display: block;" class="popover-title">
				{l s='Sub Menu Setting' mod='leobootstrapmenu'}
                <span class="badge pull-right">{l s='Close' mod='leobootstrapmenu'}</span>
            </div>
            <div class="popover-content"> 
                <form  method="post" action="{$liveedit_action}" enctype="multipart/form-data" >
                    <div class="col-lg-12">	
                        <table class="table table-hover">
                            <tr>
                                <td>{l s='Create Submenu' mod='leobootstrapmenu'}</td>
                                <td>
                                    <select name="menu_submenu" class="menu_submenu">
                                        <option value="0">{l s='No' mod='leobootstrapmenu'}</option>
                                        <option value="1">{l s='Yes' mod='leobootstrapmenu'}</option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>{l s='Submenu Width' mod='leobootstrapmenu'}</td>
                                <td>
                                    <input type="text" name="menu_subwidth" class="menu_subwidth"> 
                                </td>
                            </tr>
                            <tr class="group-submenu">
                                <td>{l s='Group Submenu' mod='leobootstrapmenu'}</td>
                                <td>
                                    <div id="submenu-form" >								
                                        <input type="hidden" name="submenu_id">
                                        <select name="submenu_group" class="submenu_group">
                                            <option value="0">{l s='No' mod='leobootstrapmenu'}</option>
                                            <option value="1">{l s='Yes' mod='leobootstrapmenu'}</option>
                                        </select>	
                                    </div>
                                </td>
                            </tr>
                            <tr class="aligned-submenu">
                                <td>{l s='Align Submenu' mod='leobootstrapmenu'}</td>
                                <td>
                                    <div class="btn-group button-aligned">
                                        <button type="button" class="btn btn-default" data-option="aligned-left"><span class="icon icon-align-left"></span></button>
                                        <button type="button" class="btn btn-default" data-option="aligned-center"><span class="icon icon-align-center"></span></button>
                                        <button type="button" class="btn btn-default" data-option="aligned-right"><span class="icon icon-align-right"></span></button>
                                        <button type="button" class="btn btn-default" data-option="aligned-fullwidth"><span class="icon icon-align-justify"></span></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="button" class="add-row btn btn-success btn-sm">{l s='Add Row' mod='leobootstrapmenu'}</button>
                                    <button type="button" class="remove-row btn btn-default  btn-sm">{l s='Remove Row' mod='leobootstrapmenu'}</button>
                                    | <button type="button" class="add-col btn btn-success  btn-sm">{l s='Add Column' mod='leobootstrapmenu'}</button>
                                </td>
                            </tr>
                        </table>
                        <input type="hidden" name="menu_id">
                    </div>
                </form>
            </div>
        </div>
        <div id="column-form" style="display: none; left: 340px; top: 45px;" class="popover top   form-setting">
            <div class="arrow"></div>
            <div style="display: block;" class="popover-title">
				{l s='Column Setting' mod='leobootstrapmenu'}
                <span class="badge pull-right">{l s='Close' mod='leobootstrapmenu'}</span>
            </div>
            <div class="popover-content"> 
                <form method="post" action="{$liveedit_action}"  enctype="multipart/form-data" >
                    <table class="table table-hover">
                        <tr>
                            <td>{l s='Addition Class' mod='leobootstrapmenu'}</td>
                            <td>
                                <input type="text" name="colclass"> 
                            </td>
                        </tr>
                        <tr>
                            <td>{l s='Column Width' mod='leobootstrapmenu'}</td>
                            <td>
                                <select class="colwidth" name="colwidth">                          
									{for $i=1 to 12}
										<option value="{$i|intval}">{$i|intval}</option>
									{/for}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">	<button type="button" class="remove-col btn btn-default  btn-sm">{l s='Remove Column' mod='leobootstrapmenu'}</button> </td>
                        </tr>	
                    </table>
                </form>
            </div>
        </div>

        <div id="widget-form" style="display: none; left: 340px; min-width:400px" class="popover bottom   form-setting">
            <div class="arrow"></div>
            <div style="display: block;" class="popover-title">
				{l s='Widget Setting' mod='leobootstrapmenu'}
                <span class="badge pull-right">{l s='Close' mod='leobootstrapmenu'}</span>
            </div>
            <div class="popover-content">
				{if !empty($widgets)}
					<select name="inject_widget" class="inject_widget"> 
                        <option value="">{l s='' mod='leobootstrapmenu'}</option>
						{foreach from=$widgets item=w}
							<option value="{$w['key_widget']}">
								{$w['name']}
                            </option>
						{/foreach}
					</select>
                    <button type="button" id="btn-inject-widget" class="btn btn-primary btn-sm">{l s='Insert' mod='leobootstrapmenu'}</button>
				{else}
					<select style="display:none" name="inject_widget" class="inject_widget">
					</select>
					<button style="display:none" type="button" id="btn-inject-widget" class="btn btn-primary btn-sm">{l s='Insert' mod='leobootstrapmenu'}</button>
				{/if}
				<button type="button" id="btn-create-widget" class="btn btn-primary btn-sm">{l s='Create New Widget' mod='leobootstrapmenu'}</button>
            </div>
        </div>

        <div id="content-s">
            <div class="container">
                <div class="page-header">					
                    <h1>{l s='Live Megamenu Editor: ' mod='leobootstrapmenu'}{$group_title} ({$group_type})</h1>
                </div>
                <div class="bs-example">
                    <div class="alert alert-danger fade in">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <strong>{l s='By using this tool, allow to create sub menu having multiple rows and  multiple columns. You can inject widgets inside columns or group sub menus in same level of parent.Note: Some configurations as group, columns width setting will be overrided' mod='leobootstrapmenu'}</strong>  
                    </div>
                </div>
            </div>
            <div id="pav-megamenu-liveedit">
                <div id="toolbar" class="container">
                    <div id="menu-toolbars">
                        <div>
                            <div class="pull-right">
								<a href="{$link->getAdminLink('AdminLeoWidgets')}&widgets=1" class="leo-modal-action btn btn-modeal btn-info btn-action">
    {l s='List Widget' mod='leobootstrapmenu'}</a>
                                -
                                <a href="{$link->getAdminLink('AdminLeoWidgets')}&addbtmegamenu_widgets&widgets=1" class="leo-modal-action btn btn-modeal btn-success btn-action">
    {l s='Create A Widget' mod='leobootstrapmenu'}</a>
                                - 
                                <a href="{$live_site_url}" class="btn btn-modal btn-primary btn-sm btn-action" >
    {l s='Preview On Live Site' mod='leobootstrapmenu'}</a> | 
                                <a id="unset-data-menu" href="#" class="btn btn-danger btn-action">{l s='Reset Configuration' mod='leobootstrapmenu'}</a>
                                <button id="save-data-menu" class="btn btn-warning">{l s='Save' mod='leobootstrapmenu'}</button>
                            </div>
                            <a id="save-data-back" class="btn btn-default" href="{$action_backlink}">
    {l s='Back' mod='leobootstrapmenu'}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="container"><div class="megamenu-wrap">
                        <div class="progress" id="leo-progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 00%;">
                                <span class="sr-only">60% Complete</span>
                            </div>
                        </div>
                        <div id="megamenu-content" class="{if ($group_type == 'vertical')}vertical {$group_type_sub}{/if}">
                        </div></div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{l s='Preview On Live Site' mod='leobootstrapmenu'}</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"></button>
            </div>
        </div> 
    </div> 
</div> 


<script type="text/javascript">
    $(".btn-modal").click(function() {
        $('#myModal .modal-dialog ').css('width', '100%');
        $('#myModal .modal-dialog ').css('height', '90%');
		
        var a = $('<span class="glyphicon glyphicon-refresh"></span><div class="cssload-container"><div class="cssload-speeding-wheel"></div></div><iframe src="' + $(this).attr('href') + '" style="width:100%;height:100%; display:none"/>');
        $('#myModal .modal-body').html(a);
		
        $('#myModal').modal();
        $('#myModal').attr('rel', $(this).attr('rel'));
        $(a).load(function() {

            $('#myModal .modal-body .glyphicon-refresh').hide();
			$('#myModal .modal-body .cssload-container').hide();
            $('#myModal .modal-body iframe').show();
			$('#myModal .modal-body').css('height', '85%');
			$('#myModal .modal-content ').css('height', '100%');
        });
        return false;
    });

    $('#myModal').on('hidden.bs.modal', function() {
        if ($(this).attr('rel') == 'refresh-page') {
            location.reload();
        }
    })

    var live_editor = true;
    var list_tab_live_editor = [];
    var _action = '{$ajxgenmenu|replace:'&amp;':'&'}';
    var _action_menu = '{$ajxmenuinfo|replace:'&amp;':'&'}';
    var _action_widget = '{$action_widget|replace:'&amp;':'&'}';
    var _action_loadwidget = '{$action_loadwidget|replace:'&amp;':'&'}';
    var _id_shop = '{$id_shop}';
    $("#megamenu-content").PavMegamenuEditor({
		"action": _action, 
		"action_menu": _action_menu, 
		"action_widget": _action_widget, 
		"id_shop": _id_shop,
	});

</script>