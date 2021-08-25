{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="tab-pane active" id="wk-gdpr-data-access" role="tabpanel">
    <h4 class="tab-pane-header">
        <img src="{$smarty.const._MODULE_DIR_}wkgdpr/views/img/slices/gdpr-data-access.png"/>&nbsp;&nbsp;&nbsp;&nbsp;{l s='Personal Data Access' mod='wkgdpr'}
    </h4>
    <div class="tab-pane-content row">
        <div class="col-sm-12">
            <p>
                {l s='You can download your personal data in PDF format from here. You can also email your personal data at desired email address.' mod='wkgdpr'}
            </p>
            <div class="data-access-block row">
                <div class="col-sm-12 data-download-block">
                    <h4>
                        {l s='Download your personal data in PDF format' mod='wkgdpr'} :
                    </h4>
                    <div class="row">
                        <div class="col-sm-3 col-xs-6">
                            <a class="btn btn-default wk_personal_info_download_btn" href="{$link->getModuleLink('wkgdpr', 'wkcustomergdprcontrols', ['wkDownloadCustomerData' => 1])}" title="{l s='Download information in PDF format' mod='wkgdpr'}">
                                {l s='PDF' mod='wkgdpr'} <i class="material-icons">arrow_drop_down_circle</i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 data-email-block">
                    <hr>
                    <h4>
                        {l s='Recieve your personal data through an email' mod='wkgdpr'} :
                    </h4>
                    <div class="row">
                        <form id="wk_data_access_form" method="post" action="{$link->getModuleLink('wkgdpr', 'wkcustomergdprcontrols')}">
                            <div class="form-group row">
    	                        <div class="col-sm-12 col-xs-12">
                                    <input type="hidden" class="form-control active_tab" name="tab" value="{$active_tab}"/>
                                    <input type="hidden" name="data_access_submit" value="1">
                                    <div class="col-sm-12 col-xs-12">
                                        <label for="wk-gdpr-cusstomer-email">
                                            {l s='Email Id' mod='wkgdpr'}
                                        </label>
                                        <input type="text" name="gdpr_cusstomer_email" class="form-control" id="wk-gdpr-cusstomer-email" value="{$customerEmail}"/>
                                        <div class="help-block">
                                            <i class="material-icons">info</i> {l s='Enter your email address at which you want to send your personal data.' mod='wkgdpr'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <button name="wk-data-access-submit" type="submit" class="btn btn-default wk-gdpr-email-btn">
                                    <span>
                                        {l s='Send Email' mod='wkgdpr'}
                                    </span>
                                </button>
                            </div>
                        </form>
                        <p class="col-xs-12 wk-grpr-note">
                            <span class="note-head">{l s='Note' mod='wkgdpr'} : </span>{l s='Above email id is used to send your personal data only. it will not be saved anywhere.' mod='wkgdpr'}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>