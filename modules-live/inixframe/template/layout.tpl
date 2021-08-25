{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
    var $module_name = '{$module_name}';
    var $frame_path_uri  = '{$frame_path_uri}';

    var token = '{$token}';
    var update_success_msg = '{l s='Update successful' mod='inixframe' js=1}';

</script>
<div class="inixframe">
{if isset($conf)}
		<div class="note note-success">
			<button type="button" class="close" data-dismiss="note">&times;</button>
			<p>{$conf}</p>
		</div>

{/if}
{if count($errors) && (!isset($disableDefaultErrorOutPut) || $disableDefaultErrorOutPut == false)}

		<div class="note note-danger fade-in">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		{if count($errors) == 1}
			{reset($errors)}
		{else}
			<h4 class="block"> {l s='%d errors' mod='inixframe' sprintf=$errors|count}</h4>

			<ol>
				{foreach $errors as $error}
					<li>{$error}</li>
				{/foreach}
			</ol>
		{/if}
		</div>

{/if}
{if isset($informations) && count($informations) && $informations}
	<div class="note note-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<ul id="infos_block" class="list-unstyled">
			{foreach $informations as $info}
				<li>{$info}</li>
			{/foreach}
		</ul>
	</div>
{/if}
{if isset($confirmations) && count($confirmations) && $confirmations}
	<div class="note note-success" style="display:block;">
		{foreach $confirmations as $conf}
			{$conf}
		{/foreach}
	</div>
{/if}
{if count($warnings)}
	<div class="note note-warning">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{if count($warnings) > 1}
			<h4 class="block"><strong>{l s='There are %d warnings:' mod='inixframe' sprintf=count($warnings)}</strong></h4>
		{/if}
		<ul class="list-unstyled">
			{foreach $warnings as $warning}
				<li>{$warning}</li>
			{/foreach}
		</ul>
	</div>
{/if}
{if $show_wellcome}
    <div class="portlet inixwellcome">
        <div class="portlet-title">
            <div class="caption">
                Presta-Apps Development
                <small>Prestashop Solutions that empower your shop</small>
            </div>
            <div class="tools">
                <a class="btn btn-danger btn-sm" href="#" id="inixclose" style="color: white"><i
                            class="icon-sm process-icon-cancel"></i> Close</a>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-lg-2  margin-top-20">

                    <a class="thumbnail">
                        <img src="http://presta-apps.com/inixframe_assets/logo_vert.png" width="200px"/>
                    </a>

                </div>
                <div class="col-lg-10">

                    <p> We provide quality, convenient, and experience-driven solutions for Prestashop owners and
                        agencies to help them maximize their webshop’s sales conversion and profitability.</p>

                    <p>
                        We provide full-service Prestashop consulting and B2C / B2B Prestashop solutions.
                        We offer Prestashop store packages and provide retainer based hourly consulting and development
                        services.
                        Presta-Apps Consulting offers a range of services to suit the needs of our customers, including:
                        Prestashop Design and Development, Consulting and Analytics.
                    </p>

                    <p>For the latest modules & feature updates, please visit: <a href="http://www.presta-apps.com">www.presta-apps.com</a>
                    </p>

                    <p>Prestashop your module <a
                                href="http://www.presta-apps.com/content/6-prestahop-your-module">here</a></p>

                    <p>For any questions: <a href="mailto:{$author_email}">{$author_email}</a></p>

                    <p>Greeting</p>

                    <p>Presta-Apps TEAM</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="panel bootstrap panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Secure Online HelpDesk
                            </h3>
                        </div>
                        <div class="panel-body">
                            <p>As our customer you receive 100% free access to our Secure Online HelpDesk.</p>

                            <p>Please kindly direct all support-related inquiries to our HelpDesk so that our dedicated
                                support team can gladly assist you in the most timely and efficient manner.</p>

                            <p>Support for all of our distributions includes:</p>
                            <ul>
                                <li>* Responding to questions or problems regarding the item and its features</li>
                                <li>* Fixing bugs and reported issues</li>
                                <li>* Providing updates to ensure compatibility with new software versions</li>
                            </ul>
                            <p>Just contact us!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
    $("#inixclose").on('click',function(e){
        $.get("{$current}&token={$token}&ajax=1&action=hide_wellcome");
        $(".inixwellcome").slideUp();
        e.preventDefault();
    });
</script>
{/if}
{include file="{$frame_local_path}template/show_update.tpl"}
{$page}

 {if file_exists("{$module_local_path}views/templates/admin/inixframe/inixfooter.tpl")}
     {include file="{$module_local_path}views/templates/admin/inixframe/inixfooter.tpl"}
 {elseif file_exists("{$module_local_path}views/templates/inixframe/inixfooter.tpl")}
     {include file="{$module_local_path}views/templates/inixframe/inixfooter.tpl"}
 {else}
{include file="{$frame_local_path}template/inixfooter.tpl"}
    {/if}
</div>
