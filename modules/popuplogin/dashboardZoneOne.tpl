<div class="panel" id="hookDashboardZoneOne_popuplogin">
    <div class="panel-heading">
        <i class="icon-puzzle-piece"></i> {l s='PopUp Login' mod='popuplogin'}
    </div>
    {if $update_availablility != NULL}
        <div class="alert alert-danger">
            {$update_availablility|replace:'http://MyPresta.eu':'<a href="https://mypresta.eu" target="blank">MyPresta.eu</a>' nofilter}
        </div>
    {else}
        <div class="alert alert-success">
            {l s='module is up to date!' mod='popuplogin'}
        </div>
    {/if}
</div>