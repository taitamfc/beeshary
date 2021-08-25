{*
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
  <h1>{l s='Unexpected payment error' mod='mpmangopaypayment'}</h1>
  <div class="error">
      <p><b>{l s='Unfortunately your order could not be processed at this time, please contact our Customer service.' mod='mpmangopaypayment'}</b></p>
      {if isset($mangopay_error_messages) && count($mangopay_error_messages)}
      	<ul style="margin-left: 30px;">
          {foreach from=$mangopay_error_messages item=mangopay_error_message}
              <li>{$mangopay_error_message|escape:'htmlall':'UTF-8'}</li>
          {/foreach}
          </ul>
      {/if}
  </div>
{/block}
