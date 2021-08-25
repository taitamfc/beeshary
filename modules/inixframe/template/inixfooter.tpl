<div class="clear">&nbsp;</div>
<div class="inixframe">
    {if isset($show_conscent) AND $show_conscent}

    <div class="modal fade" id="update_service_conscent">
    	<div class="modal-dialog ">
    		<div class="modal-content">
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">{l s='Welcome' mod='inixframe'}</h4>
    			</div>
                <div class="modal-body">
                    <div class="note note-success block">
                        <h4><strong>{l s='Thank you for purchasing our module! You are Great!' mod='inixframe'}</strong></h4>
                    </div>
                    <h5><strong>Now, please allow us to clear up few things.</strong></h5>
                    <p>To create those great modules wee need some help from our module framework called <strong>Inixframe</strong></p>
                    <p>It contains the common building blocks that help us to create and distribute our modules. <strong>Just That!</strong></p>
                    <p>It comes in a form of a module which is put in you modules folder.</p>
                    <p>The module is named <strong>Presta-Apps Dashboard</strong> and the folder is called <stgrong>inixframe</stgrong></p>
                    <p>To not worry that these files sit there and do nothing, we made it as installable module with a <strong>real value</strong>.</p>
                    <p>With it we provide a <strong>dashboard</strong>, from which you can manage our installed modules, fetch updates and get insights for our new releases and promotions!</p>
                    <p>And all that <strong>free of charge!</strong>. You just need to go and install <strong>Presta-Apps dashboard</strong> </p>
                    <p>You can click the button bellow, it will take you just there.</p>
                    <br />
                    <p>Regards</p>
                    <p>The Presta-Apps Team</p>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No, Thank you!</button>
                   {if version_compare(_PS_VERSION_ ,'1.7.0.0','<')}
                    <a href="{$link->getAdminLink('AdminModules')}&tab_module=other&module_name=inixframe"
                       class="btn btn-primary">Install Presta-Apps Dashboard</a>
                   {else}
                       <a href="{$link->getAdminLink('AdminModules')}"
                       class="btn btn-primary">Install Presta-Apps Dashboard</a>                       
                   {/if}
                </div>
    		</div><!-- /.modal-content -->
    	</div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script type="text/javascript">
        $("#update_service_conscent").modal('show').on('hide.bs.modal',function(){
            $.get("{$current}&token={$token}&ajax=1&action=hide_conscent");
        });
    </script>
    {/if}
    <div class=" moduleFooter">

    <div class="main left">
        <span>{$slogan|replace:'|':'<br />'}</span>

    </div>
    <span class="right-edge"> </span>
    <div class="visit right">
        <a href="http://www.{$author_domain}" target="_blank">
            Visit <img src="http://presta-apps.com/inixframe_assets/logo_wide_white.png" height="40px"/></a>

    </div>
    <div class="bug {if $got_feedback}disabled{/if} right">
        <a href="{$bugreport_link}" id="bugreport" data-fancybox-type="iframe">{l s='Bug report' mod='inixframe'}</a>
    </div>
    <div class="feedback right">
        <a href="{$feedback_link}" id="feedback" data-fancybox-type="iframe">Feedback</a>
    </div>
    <div class="faq right">
        <a href="http://www.presta-apps.com/faq/1-faq" target="_blank">{l s='FAQ' mod='inixframe'}</a>
    </div>
</div>
<script type="text/javascript">

    $("#feedback").fancybox({
        padding: "0px"
    });


    $("#bugreport").fancybox({
        padding: "0px"
    });
</script>
</div>
