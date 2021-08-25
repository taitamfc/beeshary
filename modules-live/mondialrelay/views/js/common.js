/**
 * 2007-2018 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

function checkMrSelection() { 
    

    var selected_carrier_id = parseInt($('input[name^=delivery_option]:checked').val());


    if (PS_MRSelectedRelayPoint['relayPointNum'] == -1)
        return true;
    
    var MR_carrier_selected = (selected_carrier_id==PS_MRSelectedRelayPoint['carrier_id']); // is a mondial relay carrier ?
    var MR_relay_selected = (PS_MRSelectedRelayPoint['relayPointNum']>0);                   // a relay has been selected ?

    if (!MR_carrier_selected) 
        return true; 
    
    if (MR_relay_selected) {
        return true; 
    } else {
        if (!!$.prototype.fancybox && !(PS_MRData.PS_VERSION < '1.5'))
           $.fancybox.open([
            {
                type: 'inline',
                autoScale: true,
                minHeight: 30,
                content: '<p class="fancybox-error">' + PS_MRTranslationList['errorSelection'] + '</p>'
            }],
            {
                padding: 0
            });
        else
            alert(PS_MRTranslationList['errorSelection']);
        
        return false;
    }
}



function setProtectRelaySelected(){
       
       $(document).on('click','button[name=confirmDeliveryOption]',function(event){
            if(!checkMrSelection()){
                event.stopPropagation();
                return false;
            }
        }); 
        
}

$(document).ready(function() {
    
  
        setProtectRelaySelected(); 
 
});

