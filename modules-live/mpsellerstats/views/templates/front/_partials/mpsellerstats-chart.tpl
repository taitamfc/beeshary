{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
    <div class="panel-content">
        {block name='mp-view-graph'}
            <div class="row">
                <div class="col-sm-12">
                    <section class="panel wk-graph">
                        <div id="wk-dashboad-graph-chart">
                            <svg></svg>
                        </div>
                    </section>
                </div>
            </div>
            <div class="row">
                <div class='col-sm-6'>
                    <p>{l s='Total visits: ' mod='mpsellerstats'}<span id="visits_score"></span></p>
                </div>
                <div class='col-sm-6'>
                    <p>{l s='Total visitors: ' mod='mpsellerstats'}<span id="visitor_score"></span></p>
                </div>
            </div>
        {/block}
    </div>
</div>
<hr>
<div class="panel">
    <h4><i class="material-icons">people</i> {l s='Acquisition' mod='mpsellerstats'}</h4>
    <div class="panel-content">
        {block name='mp-pie-chart'}
        <div class="row">
            <div class="col-md-7">
                <section class="panel wk-graph">
                    <div id="wk-stats-donutchart">
                        <div id="donutchart" style="width: 100%; height: 400px;"></div>
                    </div>
                </section>
            </div>
            <div class="col-md-5" id='synthesis-table'>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{l s='Name: ' mod='mpsellerstats'}</th>
                                <th>{l s='Visits: ' mod='mpsellerstats'}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {/block}
    </div>
</div>