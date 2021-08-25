{*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*}

<h2>{l s='Personal Information' mod='wkgdpr'}</h2>
<table id="personalInfo-tab" width="100%" class="common-table-style">
    <tr>
        <td width="48%">
            <table id="personalInfo-tab1" width="100%">
                <tr>
                    <td width="50%">
                        {l s='Social Title' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {$personalInfo['gender']|escape:'htmlall':'UTF-8'}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Name' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {$personalInfo['firstname']|escape:'htmlall':'UTF-8'} {$personalInfo['lastname']|escape:'htmlall':'UTF-8'}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Email' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {$personalInfo['email']|escape:'htmlall':'UTF-8'}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Age' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {if $personalInfo['birthday'] != '0000-00-00'}
                            {$personalInfo['stats']['age']|escape:'htmlall':'UTF-8'} {l s='years old' mod='wkgdpr'} {l s='(birth date: %s)' sprintf=[$personalInfo['birthday']|escape:'htmlall':'UTF-8'] mod='wkgdpr'}
                        {else}
                            {l s='Unknown' mod='wkgdpr'}
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Registration Date' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {dateFormat date=$personalInfo['date_add']|escape:'htmlall':'UTF-8' full=1}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Latest Update' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {dateFormat date=$personalInfo['date_upd']|escape:'htmlall':'UTF-8' full=1}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Language' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {$personalInfo['language']['name']|escape:'htmlall':'UTF-8'}
                    </td>
                </tr>
            </table>
        </td>
        <td width="3%"></td>
        <td width="48%">
            <table id="personalInfo-tab2" width="100%">
                <tr>
                    <td width="50%">
                        {l s='Last Visit' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {dateFormat date=$personalInfo['stats']['last_visit']|escape:'htmlall':'UTF-8' full=1}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Newsletter' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {if $personalInfo['newsletter']}
                            {l s='Subscribed' mod='wkgdpr'}
                        {else}
                            {l s='Unsubscribed' mod='wkgdpr'}
                        {/if}
                    </td>
                </tr>
                {* <tr>
                    <td width="50%">
                        {l s='Newsletter Subscription Date' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {if $personalInfo['newsletter_date_add'] != '0000-00-00 00:00:00'}
                            {dateFormat date=$personalInfo['newsletter_date_add']|escape:'htmlall':'UTF-8' full=1}
                        {/if}
                    </td>
                </tr> *}
                <tr>
                    <td width="50%">
                        {l s='Company' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {$personalInfo['company']|escape:'htmlall':'UTF-8'}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Siret' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {$personalInfo['siret']|escape:'htmlall':'UTF-8'}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Ape' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {$personalInfo['ape']|escape:'htmlall':'UTF-8'}
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        {l s='Website' mod='wkgdpr'}
                    </td>
                    <td width="50%">
                        {$personalInfo['website']|escape:'htmlall':'UTF-8'}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>