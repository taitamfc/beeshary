{* 
* @Module Name: Leo Product Search
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
*}

<div class="panel form-horizontal">
    <div class="form-group">
        <div class="col-lg-1">
            <a class="megamenu-correct-module btn btn-success" href="{$url_admin}&success=correct&correctmodule=1">
                <i class="icon-AdminParentPreferences"></i>{l s='Correct module' mod='leoproductsearch'}
            </a>
        </div>
        <div class="control-label col-lg-11" style="text-align: left">* {l s='Make module work well with current Prestashop Version (fix database and old bug of module).' mod='leoproductsearch'}</div>
        <div class="control-label col-lg-11" style="text-align: left">* {l s='Please backup the database before run correct module to safe' mod='leoproductsearch'}</div>
    </div>
</div>

<div id="leoproductsearch-group">
    <div class="panel panel-default">
        <div class="panel-heading"><i class="icon-cogs"></i> {l s='Leo Product Search Global Config' mod='leoproductsearch'}</div>
        <div class="panel-content" id="leoquicklogin-setting">		
            <div class="tab-content">
                {$globalform}{* HTML form , no escape necessary *}
            </div>
        </div>	
    </div>
</div>