{if isset($update_available) AND $update_available}
  <div class="clearfix"></div>
  <div class="inixframe">
      <div class="note note-warning">
        <h3>{l s='Update available' mod='inixframe'}</h3>
        <p>
            <a href="{$link->getAdminLink('AdminModules')}&configure=inixframe" class="btn btn-default btn-sm">  {l s='Click here to update' mod='inixframe'}</a>
        </p>
    </div>
  </div>
{/if}