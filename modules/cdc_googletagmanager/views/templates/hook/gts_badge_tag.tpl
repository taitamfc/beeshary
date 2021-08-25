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

<!-- BEGIN: Google Trusted Stores -->
<script type="text/javascript">
  var gts = gts || [];

  gts.push(["id", "{$GTS_STORE_ID}"]);
  gts.push(["badge_position", "{$GTS_BADGE_POSITION}"]);
{if {$GTS_BADGE_POSITION} == "USER_DEFINED" && {$GTS_CONTAINER}}
  gts.push(["badge_container", "{$GTS_CONTAINER}"]);
{/if}
  gts.push(["locale", "{$GTS_LOCALE}"]);
{* /*gts.push(["google_base_offer_id", "ITEM_GOOGLE_SHOPPING_ID"]); */
*}{if {$GTS_GGSHOP_ACCOUNT_ID}}
  gts.push(["google_base_subaccount_id", "{$GTS_GGSHOP_ACCOUNT_ID}"]);
 {/if}
  (function() {
    var gts = document.createElement("script");
    gts.type = "text/javascript";
    gts.async = true;
    gts.src = "https://www.googlecommerce.com/trustedstores/api/js";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(gts, s);
  })();
</script>
<!-- END: Google Trusted Stores -->