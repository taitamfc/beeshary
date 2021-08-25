{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

{if $successful == 1}
	<div class="bootstrap">
		<div class="alert alert-success megamenu-alert">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Successfully' mod='leobootstrapmenu'}
		</div>
	</div>
{/if}
<div class="col-lg-12"> 
	<div class="" style="float: right">
		<div class="pull-right">
			<a href="{$live_editor_url}" class="btn btn-danger">{l s='Live Edit Tools' mod='leobootstrapmenu'}</a>
               {l s='To Make Rich Content For Megamenu' mod='leobootstrapmenu'}
		</div>
	</div>
</div>
 
<div class="tab-content row">
	<div class="tab-pane active" id="megamenu">
	
		<div class="col-md-4">
			<div class="panel panel-default">
				<h3 class="panel-title">{l s='Tree Megamenu Management - Group: ' mod='leobootstrapmenu'}{$current_group_title}{l s=' - Type: ' mod='leobootstrapmenu'}{$current_group_type}</h3>
				<div class="panel-content">{l s='To sort orders or update parent-child, you drap and drop expected menu.' mod='leobootstrapmenu'}
					<hr>
					<p>
						<input type="button" value="{l s='New Menu Item' mod='leobootstrapmenu'}" id="addcategory" data-loading-text="{l s='Processing ...' mod='leobootstrapmenu'}" class="btn btn-danger" name="addcategory">
						<a href="{$admin_widget_link}" class="leo-modal-action btn btn-modeal btn-success btn-info">{l s='List Widget' mod='leobootstrapmenu'}</a>
					</p>
					<p>
						<a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&addmenuproductlayout=1&id_group={$id_group}" class="btn btn-modeal btn-success">{l s='Add Menu Item For Product Multi Layout (Only For Developers)' mod='leobootstrapmenu'}</a>
					</p>
					<hr>										
					{$tree}
                                        <a href="javascript:void(0);" class="btn btn-danger delete_many_menus">
                                            <i class="icon-trash"></i>&nbsp;Delete selected
                                        </a>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			{$helper_form}
		</div>
		<script type="text/javascript">
			var addnew ="{$addnew}"; 
			var action="{$action}";
			$("#content").PavMegaMenuList({
				action:action,
				addnew:addnew
			});
		</script>
	</div>
</div>
<script>
	$('#myTab a[href="#profile"]').tab('show');
</script>