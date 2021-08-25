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

<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>

<script data-keepinline="true">
  window.renderOptIn = function() {
    window.gapi.load('surveyoptin', function() {
      window.gapi.surveyoptin.render(
        {
          "merchant_id": "{$greviews['merchant_id']}",
          "order_id": "{$greviews['order_id']}",
          "email": "{$greviews['customer_email']}",
          "delivery_country": "{$greviews['delivery_country']}",
          "estimated_delivery_date": "{$greviews['estimated_delivery_date']}"
        });
    });
  }
</script>


