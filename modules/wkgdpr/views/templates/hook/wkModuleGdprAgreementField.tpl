{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="clearfix"></div>
<div class="wk-gdpr-block">
    <span class="custom-checkbox">
        <label>
            <input required="true" type="checkbox" name="gdpr_agreement" class="wk_gdpr_agreement" value="1" {if isset($smarty.post.gdpr_agreement) && $smarty.post.gdpr_agreement == '1'}checked="checked"{/if} />
            <span><i class="material-icons rtl-no-flip checkbox-checked"></i></span>
            {$gdprAgreementContent nofilter}
        </label>
    </span>
</div>