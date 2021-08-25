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

<div class="tab-pane" id="wk-gdpr-data-erasure" role="tabpanel">
    <h4 class="tab-pane-header">
        <img src="{$smarty.const._MODULE_DIR_}wkgdpr/views/img/slices/gdpr-data-erasure.png"/>&nbsp;&nbsp;{l s='Personal Data Erasure' mod='wkgdpr'}
    </h4>
    <div class="tab-pane-content row">
        <div class="col-sm-12">
            <p>
                {if $customer_data_delete_approve}
                    {l s='You can request to erase your personal data. To request, Write erasure reason below and submit the request. Your request will be processed as soon as possible.' mod='wkgdpr'}
                {else}
                    {l s='You can erase your personal data. To erase your personal data click on the below button and confirm. All your personal data will be deleted from the website.' mod='wkgdpr'}
                {/if}
            </p>

            <hr>
            {if isset($deleteDataPending) && $deleteDataPending}
                <div class="alert alert-info">
                    <i class="icon icon-info-circle"></i> &nbsp;{l s='Your request for personal data erasure has been successfully submitted. You will recieve an email as soon as your personal data will be deleted.' mod='wkgdpr'}
                </div>
            {else}
                <div class="data-erasure-block row">
                    <form id="data_erasure_form" method="post" action="{$link->getModuleLink('wkgdpr', 'wkcustomergdprcontrols')}">
                        <input type="hidden" class="form-control active_tab" name="tab" value="{$active_tab}"/>
                        <input type="hidden" class="data_erasure_confirmed" name="data_erasure_confirmed" value="0">
                        <input type="hidden" name="data_erasure_submit" value="1">
                        {if $customer_data_delete_approve}
                            <div class="form-group row">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="col-sm-12 col-xs-12">
                                        <label for="data_erasure_reason">
                                            {l s='Reason For Erasure' mod='wkgdpr'}
                                        </label>
                                        <textarea class="form-control" rows="5" id="data_erasure_reason" name="data_erasure_reason"></textarea>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        <div class="col-sm-12 col-xs-12">
                            <button name="wk-data-erasure-submit" type="submit" id="wk-data-erasure-submit" class="btn btn-default wk-gdpr-submit-btn wk-gdpr-data-erasure-btn">
                                <span>
                                    {if $customer_data_delete_approve}
                                        {l s='Request For Delete' mod='wkgdpr'}
                                    {else}
                                        {l s='Delete Personal Data' mod='wkgdpr'}
                                    {/if}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            {/if}
        </div>
    </div>
</div>

{* Fancybox for confirmation of customer personal data delete *}
{* <div style="display:none;" id="data_erasure_confirm_popup">
    <div class="wk-fancyb-content col-sm-12">
        <div class="wk-fancyb-body row">
            <div class="col-sm-12">
                <b><p>{l s='Are you sure you want to delete all your data?' mod='wkgdpr'}</p></b>
                <p>{l s='Once your data gets deleted, it could not be recovered in any circumstances?' mod='wkgdpr'}</p>
            </div>
        </div>
        <div class="wk-fancyb-footer row">
            <div class="col-sm-6 col-xs-8">
                <button type="submit" class="btn btn-default wk-gdpr-submit-btn wk-data-erasure-confirm wk-gdpr-data-erasure-btn">
                    <span>{l s='Delete Data' mod='wkgdpr'}</span>
                </button>
            </div>
            <div class="col-sm-6 col-xs-4">
                <span class="pull-right form-control-static fancybox-cancel-btn">{l s='CANCEL' mod='wkgdpr'}</span>
            </div>
        </div>
    </div>
</div> *}

<div class="modal fade" id="data_erasure_confirm_popup" tabindex="-1" role="dialog" aria-labelledby="data_erasure_confirm_popupLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row eraseModal-primaryBlock">
                    <div class="col-sm-12">
                        <p class="eraseModal-primaryContent">{l s='Are you sure you want to delete all your data?' mod='wkgdpr'}</p>
                        <p>{l s='Once your data gets deleted, it could not be recovered in any circumstances?' mod='wkgdpr'}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-default wk-gdpr-submit-btn wk-data-erasure-confirm wk-gdpr-data-erasure-btn">
                            <span>{l s='Delete Data' mod='wkgdpr'}</span>
                        </button>

                        <button type="button" class="btn btn-default eraseModal-cancel-btn" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>