<div id="update_client_wrap">
{if $client_token == false}{include file="{$module_local_path}views/templates/wellcome.tpl"}{/if}
{if isset($banners) AND isset($banners.top)}
    <div class="row">
        <div class="col-lg-12">
                <div class="{if isset($banners.top.left) and isset($banners.top.left.width)}
                                col-lg-{$banners.top.left.width}
                            {else}
                                col-lg-4
                            {/if}">
                    {if isset($banners.top.left)}
                    <div class="panel bootstrap {if isset($banners.top.left.severity)} panel-{$banners.top.left.severity}{/if}">
                        <div class="panel-heading">
                            <h4>{$banners.top.left.title}</h4>
                        </div>
                        <div class="panel-body">
                            {$banners.top.left.body}
                        </div>
                    </div>
                    {/if}
                </div>

            <div class="{if isset($banners.top.middle) and isset($banners.top.middle.width)}
                                col-lg-{$banners.top.middle.width}
                            {else}
                                col-lg-4
                            {/if}">
                {if isset($banners.top.middle)}
                    <div class="panel bootstrap {if isset($banners.top.middle.severity)} panel-{$banners.top.middle.severity}{/if}">
                        <div class="panel-heading">
                            <h4>{$banners.top.middle.title}</h4>
                        </div>
                        <div class="panel-body">
                            {$banners.top.middle.body}
                        </div>
                    </div>
                {/if}
            </div>
            <div class="{if isset($banners.top.right) and isset($banners.top.right.width)}
                                col-lg-{$banners.top.right.width}
                            {else}
                                col-lg-4
                            {/if}">
                {if isset($banners.top.right)}
                    <div class="panel bootstrap {if isset($banners.top.right.severity)} panel-{$banners.top.right.severity}{/if}">
                        <div class="panel-heading">
                            <h4>{$banners.top.right.title}</h4>
                        </div>
                        <div class="panel-body">
                            {$banners.top.right.body}
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>

{/if}

{if isset($own_modules) AND is_array($own_modules)}
    <div class="portlet">
        <div class="portlet-title">
           <div class="caption"> {l s='Installed modules' mod='inixframe'}</div>

            <div class="tools">
                <a class="btn btn-primary btn-sm" style="color: white" href="{$current}&token={$token}&refresh"><i class="icon process-icon-refresh"></i> {l s='Manual Check for update' mod='inixframe'}</a>
            </div>
        </div>

    <div class="portlet-body">
{foreach array_chunk($own_modules,3) as $chunk}
    <div class="row">
    {foreach $chunk as $module}
    <div class="col-lg-4">
        <div class="panel bootstrap  {if $module.status =='needupdate'}panel-warning{elseif $module.status =='error'}panel-danger{else}panel-success{/if}">
            {if $module.status =='needupdate'}
            <div class="panel-overlay hide" >
                <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                    <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                </svg>
            </div>
            {/if}
            <div class="panel-heading">
                {if $module.status =='needupdate'}
                    {l s='Need Update' mod='inixframe'}
                {elseif $module.status =='error'}
                    {l s='Error occured' mod='inixframe'}
                {else}

                    {l s='Up to date' mod='inixframe'}
                {/if}
            </div>
        	<div class="panel-body" >
        	   <img src="{$smarty.const.__PS_BASE_URI__}modules/{$module.name}/logo.png" width="36px" class="pull-left margin-right-15 margin-top-5">
                <h4>{$module.displayname} - <span class="label {if $module.status =='needupdate'}label-warning{else}label-info{/if}">v{$module.version}</span>
                    {if $module.status =='needupdate'}
                        <i class="fa fa-arrow-right margin-left-5"></i> <span class="label label-success">v{$remote_data[$module.name]['changelogs'][0]['version']}</span>
                {/if}
                    </h4>

                <p class="well well-sm margin-top-20">{$module.description}</p>
                {if $module.status =='error'}
                    {if $module.error =='invalid:module'}
                        <p class="text-danger"> {l s='Module is missing from our servers' mod='inixframe'}</p>
                    {elseif $module.error =='invalid:version'}
                        <p class="text-danger"> {l s='Module version is missing from our servers' mod='inixframe'}</p>
                    {elseif $module.error}
                        <p class="text-danger"> {$module.error}</p>
                    {/if}
                {/if}
        	</div>
            <div class="panel-footer">
                {if $module.status =='needupdate'}
                    <a class="btn btn-warning" data-toggle="modal" href="#changelog{$module.name}"><i class="fa fa-list"></i> {l s='Changelog' mod='inixframe'}</a>
                    <div class="modal fade" id="changelog{$module.name}">
                    	<div class="modal-dialog">
                    		<div class="modal-content">
                    			<div class="modal-header">
                    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    				<h4 class="modal-title">{$module.displayname} {l s='changelog' mod='inixframe'}</h4>
                    			</div>
                    			<div class="modal-body">
                    				{foreach $remote_data[$module.name]['changelogs'] as $changelog}
                                        <h3>v{$changelog.version} ( {$changelog.date} ) </h3>
                                        <div>
                                        {nl2br($changelog.summary) }
                                        </div>
                    				{/foreach}
                    			</div>
                    			<div class="modal-footer">
                    				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    			</div>
                    		</div><!-- /.modal-content -->
                    	</div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <a class="btn btn-primary update margin-left-15" data-module="{$module.name}"><i class="fa fa-arrow-up"></i>  {l s='Update' mod='inixframe'}</a>
                {/if}
                {if Module::isInstalled($module.name)}
                    <a class="btn btn-success margin-left-15" href="{$link->getAdminLink('AdminModules')}&configure={$module.name}"><i class="icon-wrench"></i> {l s='Configure' mod='inixframe'} </a>
                {else}
                    <a class="btn btn-success margin-left-15" href="{$link->getAdminLink('AdminModules')}&install={$module.name}&anchor={ucfirst($module.name)}"><i class="icon-plus-sign-alt"></i> {l s='Install' mod='inixframe'} </a>

                {/if}
            </div>
        </div>
    </div>
    {/foreach}
        </div>
{/foreach}
        </div>
    </div>
    <script type="text/javascript">
        var $up_to_date_msg = '{l s='Up to date' mod='inixframe'}';
    </script>
{/if}
    {if isset($banners) AND isset($banners.bottom)}
        <div class="row">
            <div class="col-lg-12">
                <div class="{if isset($banners.bottom.left) and isset($banners.bottom.left.width)}
                                col-lg-{$banners.bottom.left.width}
                            {else}
                                col-lg-4
                            {/if}">
                    {if isset($banners.bottom.left)}
                        <div class="portlet {if isset($banners.bottom.left.severity)} portlet-{$banners.bottom.left.severity}{/if}">
                            <div class="portlet-title">
                                <h4>{$banners.bottom.left.title}</h4>
                            </div>
                            <div class="portlet-body">
                                {$banners.bottom.left.body}
                            </div>
                        </div>
                    {/if}
                </div>

                <div class="{if isset($banners.bottom.middle) and isset($banners.bottom.middle.width)}
                                col-lg-{$banners.bottom.middle.width}
                            {else}
                                col-lg-4
                            {/if}">
                    {if isset($banners.bottom.middle)}
                        <div class="portlet {if isset($banners.bottom.middle.severity)} portlet-{$banners.bottom.middle.severity}{/if}">
                            <div class="portlet-title">
                                <h4>{$banners.bottom.middle.title}</h4>
                            </div>
                            <div class="portlet-body">
                                {$banners.bottom.middle.body}
                            </div>
                        </div>
                    {/if}
                </div>
                <div class="{if isset($banners.bottom.right) and isset($banners.bottom.right.width)}
                                col-lg-{$banners.bottom.right.width}
                            {else}
                                col-lg-4
                            {/if}">
                    {if isset($banners.bottom.right)}
                        <div class="portlet {if isset($banners.bottom.right.severity)} portlet-{$banners.bottom.right.severity}{/if}">
                            <div class="portlet-title">
                                <h4>{$banners.bottom.right.title}</h4>
                            </div>
                            <div class="portlet-body">
                                {$banners.bottom.right.body}
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    {/if}
</div>