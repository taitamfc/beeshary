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

<div class="tab-pane" id="wk-gdpr-data-update" role="tabpanel">
    <h4 class="tab-pane-header">
        <img src="{$smarty.const._MODULE_DIR_}wkgdpr/views/img/slices/gdpr-data-update.png"/>&nbsp;&nbsp;&nbsp;&nbsp;{l s='Personal Data Rectification' mod='wkgdpr'}
    </h4>
    <div class="tab-pane-content row">
        <div class="col-sm-12">
            <p>
                {l s='You can update your personal data on the website through below links. For any other updation, You may send a request to the admin.' mod='wkgdpr'}
            </p>
            <div class="data-update-block row">
                 <div class="col-sm-12">
                    <h4>
                        {l s='Update personal data with below links' mod='wkgdpr'} :
                    </h4>
                    <ul>
                        <li>
                            <i class="material-icons">&#xE061;</i>
                            <a class="wk-gdpr-update-links" target="_blank" href="{$link->getPageLink('identity', true)}" title="{l s='Personal information update' mod='wkgdpr'}">
                                {l s='Update your Personal Information' mod='wkgdpr'}
                            </a>
                        </li>
                        <li>
                            <i class="material-icons">&#xE061;</i>
                            <a class="wk-gdpr-update-links" target="_blank" href="{$link->getPageLink('addresses', true)}" title="{l s='Update addresses' mod='wkgdpr'}">
                                {l s='Update your Address Information' mod='wkgdpr'}
                            </a>
                        </li>
                        <li>
                            <i class="material-icons">&#xE061;</i>
                            <a class="wk-gdpr-update-links wk-gdpr-other-updates" href="#" title="{l s='Update Other information' mod='wkgdpr'}">
                                {l s='For Other Updations Click Here' mod='wkgdpr'}
                            </a>
                        </li>
                    </ul>
                 </div>
                <form id="wk_data_update_form" method="post" action="{$link->getModuleLink('wkgdpr', 'wkcustomergdprcontrols')}">
                    <input type="hidden" class="form-control active_tab" name="tab" value="{$active_tab}"/>
                    <div class="form-group row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="col-sm-12 col-xs-12">
                                <label for="data_update_reason">
                                    {l s='Reason For update' mod='wkgdpr'}
                                </label>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <textarea class="form-control" rows="5" id="data_update_reason" name="data_update_reason"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12">
                        <button name="wk-data-update-submit" type="submit" class="btn btn-default wk-gdpr-submit-btn wk-gdpr-data-update-btn">
                            <span>
                                {l s='Request For Update' mod='wkgdpr'}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>