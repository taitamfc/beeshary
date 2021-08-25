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
<style>
	.cdc-info {
		background: #d9edf7;
		color: #1b809e;
		padding: 7px;
		/*border-left: solid 3px #1b809e;*/
		margin-top: 50px;
		font-weight: normal;
	}

	.cdc-warning-box {
		background: #FFF3D7;
		color: #D2A63C;
		padding: 16px;
		font-weight: bold;
		border: solid 2px #fcc94f;
		margin: 30px 0;
		text-align: center;
		font-size: 1.2em;
	}

	.hook_ok {
		color: #00aa00;
		font-weight: bold;
	}
	.hook_nok {
		color: #cc0000;
		font-weight: bold;
	}
	.hook_list {
		font-family: monospace;
		list-style-type: square;
	}
</style>
<div class="bootstrap">


<div class="panel text-center">
	<img src="{$module_dir|escape:'htmlall':'UTF-8'}/logo.png" >
	<h1>
		Google Tag Manager Enhanced E-commerce
		<br /><small>GTM integration + Enhanced E-commerce + Google Trusted Stores</small>
	</h1>
</div>

<div>


	<div class="panel">
		<div>
			<h2>INSTALLATION</h2>
			<p>{l s='In order to work properly, this module needs the installation of custom hooks :' mod='cdc_googletagmanager'}</p>

            <!-- Loading -->
            <div class="check-hooks-loading">
                <div class="alert alert-info">{l s='Checking if hooks are correctly installed, please wait a couple of seconds...' mod='cdc_googletagmanager'}</div>
            </div>

            <!-- General error -->
            <div class="check-hooks-result check-hooks-error" style="display: none;">
                <div class="alert alert-danger">{l s='Error while checking if hooks are correctly installed. Please contact the support, and add these informations.' mod='cdc_googletagmanager'}</div>

                <pre id="error-detail"></pre>

                <a href="https://addons.prestashop.com/contact-community.php?id_product=23806" target="_blank" class="btn btn-info btn-lg button">
                    <b>{l s='Contact the support' mod='cdc_googletagmanager'}</b>
                </a>

                <div style="margin-top: 15px;">
                    <em>
                        {l s='I am sure that I have correctly installed the hooks but the hook locator cannot find it.' mod='cdc_googletagmanager'}
                        <a href="{$form_action}&force_installed_hooks">{l s='I want to bypass this verification.' mod='cdc_googletagmanager'}</a>
                    </em>
                </div>
            </div>

            <!-- Success -->
            <div class="check-hooks-result check-hooks-success" style="display: none;">
                <div class="alert alert-success">{l s='All the hooks are correctly installed!' mod='cdc_googletagmanager'}</div>

                <a href="{$form_action}&force_installed_hooks" class="btn btn-success btn-lg button"><b>{l s='Go to the configuration screen' mod='cdc_googletagmanager'} &raquo;</b></a>
            </div>

            <!-- Missing hooks -->
            <div class="check-hooks-result check-hooks-missing" style="display: none;">
                <div class="alert alert-danger">{l s='Some hooks are missing. Please install these hooks before continuing:' mod='cdc_googletagmanager'}</div>
            
                <ul class="hook_list">
                </ul>

                {if empty($troubleshooting)}
                <div style="margin: 20px 0 30px 0;">
                    <a href="{$form_action}&install_hooks" class="btn btn-success btn-lg button"><b>{l s='Install missing hooks automatically' mod='cdc_googletagmanager'}</b></a>

                    <div style="margin: 10px 0 0 0;">
                        <em>{l s='I am sure that I have correctly installed the hooks but the hook locator cannot find it.' mod='cdc_googletagmanager'}
                        <a href="{$form_action}&force_installed_hooks">{l s='I want to bypass this verification.' mod='cdc_googletagmanager'}</a></em>
                    </div>
                </div>
                {/if}
            </div>
			
		</div>

        

        {if $multishop}
        <div>
            <h2>{l s='Multishops' mod='cdc_googletagmanager'}</h2>
            <p>{l s='Multishop feature is enabled and you have at least 2 shops. The automatic installation may fails if you have many themes. Please refer to the documentation and install the hooks manually.' mod='cdc_googletagmanager'}</p>

            <a href="https://comptoirducode.com/prestashop/modules/google-tag-manager/documentation-google-tag-manager-prestashop/" target="_blank">
                <b>Read the documentation to know how to install hooks</b>
            </a>
        </div>
        {/if}

	</div>


    <!-- Troubleshooting -->
    {if !empty($troubleshooting)}
    <div class="panel">
        <div>
            <h2>{l s='INSTALLATION TROUBLESHOOTING' mod='cdc_googletagmanager'}</h2>
            <h4 class="text-danger">{l s='The automatic installation has failed.' mod='cdc_googletagmanager'}</h4>
            <p>{l s='It is due to an incompatibility with your theme files.' mod='cdc_googletagmanager'}</p>
            <p><b>{l s='You will have to install the hooks manually. Don\'t worry, this is something very easy.' mod='cdc_googletagmanager'}</b></p>
            <p>{l s='Please read the documentation to install the missing hooks. When you are done, you can check if hooks are correctly installed by clicking on the button' mod='cdc_googletagmanager'} <b>[{l s='Check if hooks are correctly installed' mod='cdc_googletagmanager'}]</b>.</p>
        </div>


        <div style="margin: 20px 0;">
            <a href="https://comptoirducode.com/prestashop/modules/google-tag-manager/documentation-google-tag-manager-prestashop/" target="_blank" class="btn btn-info btn-lg button">
                <b>{l s='Read the documentation to know how to install hooks' mod='cdc_googletagmanager'}</b>
            </a>
            <a href="#" id="check-hooks-installation" class="btn btn-success btn-lg button"><b>{l s='Check if hooks are correctly installed' mod='cdc_googletagmanager'}</b></a>
        </div>
        <em>{l s='I am sure that I have correctly installed the hooks but the hook locator cannot find it.' mod='cdc_googletagmanager'}
        <a href="{$form_action}&force_installed_hooks">{l s='I want to bypass this verification.' mod='cdc_googletagmanager'}</a></em>


        <div>
            <h2>{l s='Contact us' mod='cdc_googletagmanager'}</h2>
            <p>{l s='f you still have problem installing the hooks, you can contact' mod='cdc_googletagmanager'} <a href="https://addons.prestashop.com/contact-community.php?id_product=23806" target="_blank">{l s='our support team on Prestashop' mod='cdc_googletagmanager'}</a>.</p>
        </div>

    </div>
    {/if}


</div>
</div>


<script> 

    function findHooksInPage(page_content) {
        var hooks = {
            'displayAfterTitle': 0,
            'displayAfterBodyOpeningTag': 0,
            'displayBeforeBodyClosingTag': 0
        };

        var success = true;

        for (var hook in hooks) {
            var hook_key = "cdcgtm_" + hook;
            console.log("search hook " + hook + "(key: " + hook_key + ") ...");
            if (page_content.indexOf(hook_key) >= 0) {
                hooks[hook] = 1;
                console.log("hook " + hook + " found");
            } else {
                console.log("hook " + hook + " not found");
                success = false;
            }
        };


        // display result
        if(success) {
            $('.check-hooks-success').fadeIn(400);
        } else {
            $('.check-hooks-missing').fadeIn(400);

            console.log(hooks);
            for (var hook in hooks) {
                var hookOK = 0;
                if (hooks.hasOwnProperty(hook)) {
                    hookOK = hooks[hook];
                }

                var css_class = hookOK ? 'hook_ok' : 'hook_nok';
                var text = hookOK ? "{l s='Found' mod='cdc_googletagmanager'}" : "{l s='Not found' mod='cdc_googletagmanager'}";

                $('.hook_list').append('<li>' + hook + ': <span class="' + css_class + '">' + text + '</span></li>');
            }
        }

        return success;
    }


    function checkHooks() {

        // reset
        $('.check-hooks-result').hide();
        $('.check-hooks-loading').show();
        $('.hook_list').empty();

        // get homepage
        console.log("GET {$check_url}");
        $.get("{$check_url}", function(page_content) {

            $('.check-hooks-loading').hide();
            findHooksInPage(page_content);

        }).fail(function(xhr, status) {
            $('.check-hooks-loading').hide();

            // check hooks anyway
            let success = findHooksInPage(xhr.responseText);

            if(!success) {
                var errorDetail = {
                    status: xhr.status,
                    head: xhr.getAllResponseHeaders()
                }
                $('#error-detail').text(JSON.stringify(errorDetail));
                $('.check-hooks-error').fadeIn(400);
            }

        });
    }

    checkHooks();
    

    $('#check-hooks-installation').on('click', function(e) {
        e.preventDefault();
        checkHooks();
    });

</script>