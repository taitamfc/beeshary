{*
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @package   cdc_googletagmanager
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2016 SAS Comptoir du Code
 * @license   Commercial license
 *}

<script data-keepinline="true">
    var ajaxGetProductUrl = '{if !empty($ajaxGetProductUrl)}{$ajaxGetProductUrl}{/if}';

/* datalayer */
dataLayer = [];
{if !empty($dataLayer)}dataLayer.push({$dataLayer nofilter});{/if}

/* call to GTM Tag */
{literal}(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
}){/literal}(window,document,'script','dataLayer','{$gtm_id nofilter}');

/* async call to avoid cache system for dynamic data */
{if $async_user_info}
var cdcgtmreq = new XMLHttpRequest();
cdcgtmreq.onreadystatechange = function() {
    if (cdcgtmreq.readyState == XMLHttpRequest.DONE ) {
        if (cdcgtmreq.status == 200) {
          	var datalayerJs = cdcgtmreq.responseText;
            try {
                var datalayerObj = JSON.parse(datalayerJs);
                dataLayer = dataLayer || [];
                dataLayer.push(datalayerObj);
            } catch(e) {
               console.log("[CDCGTM] error while parsing json");
            }

            {if $gtm_debug}
            // display debug
            console.log('[CDCGTM] DEBUG ENABLED');
            console.log(datalayerObj);
            document.addEventListener('DOMContentLoaded', function() {
              if(document.getElementById("cdcgtm_debug_asynccall")) {
                  document.getElementById("cdcgtm_debug_asynccall").innerHTML = datalayerJs;
              }
            }, false);
            {/if}
        }
        dataLayer.push({
          'event': '{$event_datalayer_ready nofilter}'
        });
    }
};
cdcgtmreq.open("GET", "{$async_url}" /*+ "?" + new Date().getTime()*/, true);
cdcgtmreq.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
cdcgtmreq.send();
{else}
dataLayer.push({
  'event': '{$event_datalayer_ready nofilter}'
});
{/if}
</script>
