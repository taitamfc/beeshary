{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@buy-addons.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Buy-addons <contact@buy-addons.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel cronjob-box">
    <h3><i class="icon-cogs"></i> {l s='Step 3: cronjob' mod='ba_importer'}</h3>
    <form class="form-horizontal col-lg-12"  method="post" enctype="multipart/form-data">
        <div class="form-wrapper col-lg-12">
            <div class="form-group">
                <label class="control-label col-lg-3">Task frequency</label>
                <div class="col-lg-9 ">
                    <select name="hour" class=" fixed-width-xl" id="hour">
                        <option value="-1">{l s='Every hour' mod='ba_importer'}</option>
                        {for $foo=0 to 23}
                            <option value="{$foo|escape:'htmlall':'UTF-8'}" {if $foo == $arr_config_cronjob.hour}selected{/if}>{$foo|escape:'htmlall':'UTF-8'}:00</option>
                        {/for}
                    </select>
                    <p class="help-block">{l s='At what time should this task be executed? Now is ' mod='ba_importer'}{$cronjob_date_now|escape:'htmlall':'UTF-8'}</p>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <select name="day" class=" fixed-width-xl" id="day">
                        <option value="-1">{l s='Every day of the month' mod='ba_importer'}</option>
                        {for $foo=1 to 31}
                            <option value="{$foo|escape:'htmlall':'UTF-8'}" {if $foo == $arr_config_cronjob.day}selected{/if}>{$foo|escape:'htmlall':'UTF-8'}</option>
                        {/for}
                    </select>
                    <p class="help-block">{l s='On which day of the month should this task be executed?' mod='ba_importer'}</p>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                                
                    <select name="month" class=" fixed-width-xl" id="month">
                        <option value="-1">{l s='Every month' mod='ba_importer'}</option>
                        
                        <option value="1" {if "1" == $arr_config_cronjob.month}selected{/if}>{l s='January' mod='ba_importer'}</option>

                        <option value="2" {if "2" == $arr_config_cronjob.month}selected{/if}>{l s='February' mod='ba_importer'}</option>

                        <option value="3" {if "3" == $arr_config_cronjob.month}selected{/if}>{l s='March' mod='ba_importer'}</option>

                        <option value="4" {if "4" == $arr_config_cronjob.month}selected{/if}>{l s='April' mod='ba_importer'}</option>

                        <option value="5" {if "5" == $arr_config_cronjob.month}selected{/if}>{l s='May' mod='ba_importer'}</option>

                        <option value="6" {if "6" == $arr_config_cronjob.month}selected{/if}>{l s='June' mod='ba_importer'}</option>

                        <option value="7" {if "7" == $arr_config_cronjob.month}selected{/if}>{l s='July' mod='ba_importer'}</option>

                        <option value="8" {if "8" == $arr_config_cronjob.month}selected{/if}>{l s='August' mod='ba_importer'}</option>

                        <option value="9" {if "9" == $arr_config_cronjob.month}selected{/if}>{l s='September' mod='ba_importer'}</option>

                        <option value="10" {if "10" == $arr_config_cronjob.month}selected{/if}>{l s='October' mod='ba_importer'}</option>

                        <option value="11" {if "11" == $arr_config_cronjob.month}selected{/if}>{l s='November' mod='ba_importer'}</option>

                        <option value="12" {if "12" == $arr_config_cronjob.month}selected{/if}>{l s='December' mod='ba_importer'}</option>

                    </select>
                    <p class="help-block">{l s='On what month should this task be executed?' mod='ba_importer'}</p>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                                
                    <select name="day_of_week" class=" fixed-width-xl" id="day_of_week">
                        <option value="-1">{l s='Every day of the week' mod='ba_importer'}</option>

                        <option value="1" {if "1" == $arr_config_cronjob.day_of_week}selected{/if}>{l s='Monday' mod='ba_importer'}</option>

                        <option value="2" {if "2" == $arr_config_cronjob.day_of_week}selected{/if}>{l s='Tuesday' mod='ba_importer'}</option>

                        <option value="3" {if "3" == $arr_config_cronjob.day_of_week}selected{/if}>{l s='Wednesday' mod='ba_importer'}</option>

                        <option value="4" {if "4" == $arr_config_cronjob.day_of_week}selected{/if}>{l s='Thursday' mod='ba_importer'}</option>

                        <option value="5" {if "5" == $arr_config_cronjob.day_of_week}selected{/if}>{l s='Friday' mod='ba_importer'}</option>

                        <option value="6" {if "6" == $arr_config_cronjob.day_of_week}selected{/if}>{l s='Saturday' mod='ba_importer'}</option>

                        <option value="7" {if "7" == $arr_config_cronjob.day_of_week}selected{/if}>{l s='Sunday' mod='ba_importer'}</option>

                    </select>                                                                            
                    <p class="help-block">{l s='On which day of the week should this task be executed?' mod='ba_importer'}</p>                            
                </div>        
            </div>    
            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <p class="help-block" style="display:none;">{$config_auto_cronjob|escape:'htmlall':'UTF-8'}</p>                            
                </div>        
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-default pull-right" name="submit_reminder" value="1">
                <i class="process-icon-save "></i> <span>{l s='Save' mod='ba_importer'}</span>
            </button>
            <button type="submit" class="btn btn-default" name="back_bc2" value="1">
                <i class="process-icon-back"></i> <span>{l s='Back to Step 2' mod='ba_importer'}</span>
            </button>
			<a type="submit" class="btn btn-default" href="{$list_setting_url|escape:'htmlall':'UTF-8'}">
                <i class="process-icon-cancel"></i> <span>{l s='Back to Settings List' mod='ba_importer'}</span>
            </a>    
        </div>
    </form>
</div>
