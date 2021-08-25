{*
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
{if isset($smarty.get.delete)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Deleted Successfully' mod='mpwebservice'}
	</p>
{else if isset($smarty.get.status)}
<p class="alert alert-success">
	<button data-dismiss="alert" class="close" type="button">×</button>
	{l s='Status updated Successfully' mod='mpwebservice'}
</p>
{else if isset($smarty.get.save)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Saved Successfully' mod='mpwebservice'}
	</p>
{/if}
{if $logged}
<div class="wk-mp-block">
    {hook h="displayMpMenu"}
    <div class="wk-mp-content">
        <div class="page-title" style="background-color:{$title_bg_color};">
            <span style="color:{$title_text_color};">{l s='Webservice' mod='mpwebservice'}</span>
        </div>
		<div class="wk-mp-right-column">
			{if isset($smarty.get.action) && ($smarty.get.action == 'add' || $smarty.get.action == 'edit')}
				<form class="form-vertical" action="{$smarty.server.REQUEST_URI}" method="post">
					<div class="form-wrapper">
						<div class="form-group">
							<label for="key" class="control-label required">
								{l s='Key' mod='mpwebservice'}
							</label>
							<div class="row">
								<div class="col-md-9">
									<input type="text" class="form-control" name="key" id="code" value="{if isset($smarty.post.key)}{$smarty.post.key}{elseif isset($selected_mpresources)}{$selected_mpresources.key}{/if}"
								/>
								<small class="form-text text-muted">{l s='auth_key in your API URL' mod='mpwebservice'}</small>
								</div>
								<div class="col-md-2">
									<button type="button" class="btn btn-default" onclick="gencode(32)">{l s='Generate!' mod='mpwebservice'}
									</button>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="key_description" class="control-label">
								{l s='Key Description' mod='mpwebservice'}
							</label>
							<textarea class="form-control" name="key_description" id="key_description" cols="110" rows="3"/>{if isset($smarty.post.key_description)}{$smarty.post.key_description}{elseif isset($selected_mpresources)}{$selected_mpresources.description}{/if}</textarea>
						</div>

						{if $WK_WS_KEY_SELLER_STATUS}
							<div class="form-group">
								<label for="status" class="control-label">{l s='Status' mod='mpwebservice'}</label>
								<div class="col-md-12">
									<div class="radio-inline">
										<label for="" class="top">
										<input type="radio" name="status" id="" value="1" {if isset($selected_mpresources)}{if $selected_mpresources.active == 1}checked="checked"{/if}{else}checked="checked"{/if}/>
	                        			{l s='Yes' mod=mpwebservice}</label>
									</div>
									<div class="radio-inline">
										<label for="" class="top">
										<input type="radio" name="status" id="" value="0" {if isset($selected_mpresources)  && $selected_mpresources.active == 0}checked="checked"{/if}/>
	                        			{l s='No' mod=mpwebservice}</label>
									</div>
								</div>
							</div>
						{/if}
						<div class="form-group">
							<label for="api" class="control-label">
								{l s='Permissions' mod='mpwebservice'}
							</label>
							<div class="col-md-12">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>{l s='Resources' mod='mpwebservice'}</th>
											<th><input type="checkbox" id="wk_select_all_mpapi"></th>
										</tr>
									</thead>
									<tbody>
										{foreach $mpresources as $mpapi}
											<tr>
												<td><span class="pull-left">{$mpapi}</span></td>
												<td>
													<input class="form-check-input" type="checkbox" value="{$mpapi}" name="mpapi[]"
													{if isset($selected_mpresources) && $selected_mpresources.mpresource}
														{foreach $selected_mpresources.mpresource as $mpresource}
															{if $mpapi == $mpresource}
																checked="checked"
															{/if}
														{/foreach}
													{/if}
													/>
												</td>
											</tr>
										{/foreach}
									</tbody>
								</table>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-xs-4 col-sm-4 col-md-6">
								<a href="{$link->getModuleLink('mpwebservice', 'webservice')}" class="btn wk_btn_cancel wk_btn_extra">{l s='Cancel' mod='mpwebservice'}</a>
							</div>
							<div class="col-xs-8 col-sm-8 col-md-6 wk_text_right" id="wk-product-submit" data-action="Save">
								<button type="submit" name="submitAPIKey" class="btn btn-success wk_btn_extra form-control-submit">
									<span>{l s='Save' mod='mpwebservice'}</span>
								</button>
							</div>
						</div>
					</div>
				</form>
			{else}
				<p class="wk_text_right">
					<a href="{$addwebservice_link}">
						<button class="btn btn-primary btn-sm" type="button">
							<i class="icon-plus"></i>
							{l s='Add New' mod='mpwebservice'}
						</button>
					</a>
				</p>
				<table class="table table-hover">
					<thead>
						<tr>
							<th>{l s='Authentication Key (auth_key)' mod='mpwebservice'}</th>
							<th>{l s='Key Description' mod='mpwebservice'}</th>
							<th><center>{l s='Status' mod='mpwebservice'}</center></th>
							<th><center>{l s='Action' mod='mpwebservice'}</center></th>
						</tr>
					</thead>
					<tbody>
						{if $mpwebservicekeys}
							{foreach $mpwebservicekeys as $mpwskey}
							<tr>
								<td><a title="{l s='View' mod='mpwebservice'}" href="{$link->getModuleLink('mpwebservice', 'webservice', ['id_mpwebservice' => $mpwskey.id_wk_mp_webservice_key, 'action' => 'edit'])}">{$mpwskey.key}</a></td>
								<td><a title="{l s='View' mod='mpwebservice'}" href="{$link->getModuleLink('mpwebservice', 'webservice', ['id_mpwebservice' => $mpwskey.id_wk_mp_webservice_key, 'action' => 'edit'])}">{$mpwskey.description}</a></td>
								<td>
									<center>
										{if $WK_WS_KEY_SELLER_STATUS}
										    <a href="{$link->getModuleLink('mpwebservice', 'webservice', ['id_mpwebservice' => $mpwskey.id_wk_mp_webservice_key, 'action' => status])}">
										        {if $mpwskey.active == 1}
										            <img alt="{l s='Enabled' mod='mpwebservice'}" title="{l s='Click to disable' mod='mpwebservice'}" class="mpwebservice_status" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/icon/icon-check.png" />
										        {else}
										            <img alt="{l s='Disabled' mod='mpwebservice'}" title="{l s='Click to enable' mod='mpwebservice'}" class="mp_product_status" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/icon/icon-close.png" />
										        {/if}
										    </a>
										{else if $WK_WS_KEY_ADMIN_APPROVE}
											{if $mpwskey.active == 1}
												<img alt="{l s='Enabled' mod='mpwebservice'}" title="{l s='Enabled' mod='mpwebservice'}" class="mpwebservice_status" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/icon/icon-check.png" />
											{else}
												<span title="{l s='Pending by admin end' mod='mpwebservice'}">{l s='Pending' mod='mpwebservice'}</span>
											{/if}
										{else}
											{if $mpwskey.active == 1}
												<img alt="{l s='Enabled' mod='mpwebservice'}" title="{l s='Click to disable' mod='mpwebservice'}" class="mpwebservice_status" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/icon/icon-check.png" />
											{else}
												<img alt="{l s='Disabled' mod='mpwebservice'}" title="{l s='Click to enable' mod='mpwebservice'}" class="mp_product_status" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/icon/icon-close.png" />
											{/if}
										{/if}
									</center>
								</td>
								<td>
									<center>
										<a title="{l s='Edit' mod='mpwebservice'}" href="{$link->getModuleLink('mpwebservice', 'webservice', ['id_mpwebservice' => $mpwskey.id_wk_mp_webservice_key, 'action' => 'edit'])}">
											<i class="material-icons">&#xE254;</i>
										</a>
										&nbsp;
										<a title="{l s='Delete' mod='mpwebservice'}" href="{$link->getModuleLink('mpwebservice', 'webservice', ['id_mpwebservice' => $mpwskey.id_wk_mp_webservice_key, 'action' => 'delete'])}" class="delete_img">
											<i class="material-icons">&#xE872;</i>
										</a>
									</center>
								</td>
							</tr>
						{/foreach}
						{else}
							<tr>
								<td colspan="4">{l s='No data found' mod='mpwebservice'}</td>
							</tr>
						{/if}
					</tbody>
				</table>
			{/if}
		</div>
    </div>
</div>
{/if}
{/block}
