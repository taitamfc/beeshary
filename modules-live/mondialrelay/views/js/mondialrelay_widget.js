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

$(document).ready(function () {

    $(document).on('mouseover','.PR-List-Item',function(){
        replace_class_mr = $(this).attr('onclick');
        replace_class_mr2 = replace_class_mr.replace('"#Zone_Widget"', '".fancybox-inner #Zone_Widget"');
        $(this).attr('onclick', replace_class_mr2);
    });
    // settimeout utiliser pour la connection de l'utilisateur en opc
    if(!$('.payment-options').is(':visible')) {
        showWidgetMr();
    }

    $(document).on('click','input[name^=delivery_option]',function(event) {
        if (typeof PS_MRData != 'undefined')
        {
            showWidgetMr();
        }

    });

    $(document).on('click','#checkout-delivery-step .step-title',function(event) {
        if (typeof PS_MRData != 'undefined')
        {
            showWidgetMr();
        }

    });

});

function showWidgetMr() {
    $("#link_zone_widget").fancybox(
        {
            width		:	705,
            height		:	620,
            autoSize	:	false,
            autoScale	:	false,
            autoDimensions : false,
            afterShow : function(){
                updateBox();
            },
            onUpdate : function() {
                $('.fancybox-wrap').css({
                    top:'10%',
                    bottom:'auto'
                })
            }
        });
    checkToDisplayRelayList();
}

function updateBox() {
    button = $('<br clear="all"><center><a href="javascript:$.fancybox.close(); return false;" id="close_fancybox" class="button_large" onclick="$.fancybox.close(); return false;">'+button_validate+'</a></center>');
    button.appendTo($('.fancybox-inner #Zone_Widget').parent());

    $("#fancybox-content #close_fancybox").click(
        function(){
            $.fancybox.close();
        }
    );
    // avoid gray area in google map
    try{
        google.maps.event.trigger(document.getElementById('MRW-Map'), 'resize');
    }
    catch(e) {
        ;
    }
}


function getNumeric(val) {
    var reg = new RegExp("[0-9]+", "g");
    var res = reg.exec(val);
    if (isNaN(res))
        return 0;
    else
        return res;
}

function checkToDisplayRelayList()
{
    if (typeof PS_MRData != 'undefined')
    {
        //============================================================
        // auto display fancybox if radio already check
        //============================================================
        PS_MRSelectedRelayPoint['relayPointNum'] = PS_MRData.pre_selected_relay;

        var carrier_selected = $('input[name^=delivery_option]:checked').val();
        $.each(PS_MRData.carrier_list, function (i, carrier) {
            PS_MRCarrierMethodList[carrier.id_carrier] = carrier.id_mr_method;
            if (carrier.id_carrier + ',' == carrier_selected || carrier.id_carrier == carrier_selected) {
                overrideUpdateExtraCarrier(carrier_selected, id_address);
                PS_MRSelectedRelayPoint['carrier_id'] = carrier.id_carrier;
                PS_MRDisplayWidget(carrier.id_carrier);
            }
        });

        //============================================================			
        // Handle input click of the other input to hide the previous relay point list displayed
        $('input[name=id_carrier], input[name^=delivery_option]').click(function (e) {
            displayPickupPlace(0);
        });
    }
    //return false;
}

function isMRCarrier(id_carrier) {
    var carrier_list = PS_MRData.carrier_list;
    for (i in carrier_list) {
        var MR_carrier = carrier_list[i];
        if (MR_carrier.id_carrier == id_carrier) {
            return MR_carrier;
        }
    }
    return false;
}

function hideRelaySelectedBox(_this) {
    // Hide MR input if one of them is not selected
    if (PS_MRCarrierMethodList[_this.val()] == undefined) {
        // 1.5 way
        var id = getNumeric(_this.val());
        if (PS_MRCarrierMethodList[id] == undefined) {
            displayPickupPlace(0);
            PS_MRSelectedRelayPoint['carrier_id'] = 0;
            PS_MRDisplayWidget(0);
            PS_MRSelectedRelayPoint['relayPointNum'] = 0;
        }
        else {
            PS_MRSelectedRelayPoint['carrier_id'] = id;
            PS_MRDisplayWidget(id);
        }
    }
    else {
        PS_MRSelectedRelayPoint['carrier_id'] = _this.val();
        PS_MRDisplayWidget(_this.val());
    }
    return false;
}

function PS_MRDisplayWidget(carrier_id) {
    var dlv_mode = '';
    $.each(PS_MRData.carrier_list, function (i, carrier) {
        if (carrier.id_carrier == carrier_id)
            dlv_mode = carrier.dlv_mode;
        PS_MRSelectedRelayPoint['relayPointNum'] = -1;
        PS_MRAddSelectedCarrierInDB(carrier_id);
    }
    );
    if (carrier_id) {
        if (dlv_mode != 'LD1' && dlv_mode != 'LDS' && dlv_mode != 'HOM') {
            loadMR_Map("#Zone_Widget", dlv_mode);
            $("#link_zone_widget").click();
            if (PS_MRSelectedRelayPoint['relayPointNum'] == -1)
                PS_MRSelectedRelayPoint['relayPointNum'] = 0;
        }
        else
            displayPickupPlace(0);
    }
    return false;
}


function overrideUpdateExtraCarrier(id_delivery_option, id_address)
{
    var url = "";
    var method = 'updateExtraCarrier';
    var params = '';
    if (typeof (orderOpcUrl) !== 'undefined') {
        method = 'updateCarrierAndGetPayments';
        params += '&recyclable=' + (($('#recyclable:checked').val() != undefined) ? 1 : 0);
        params += '&gift=' + (($('#gift:checked').val() != undefined) ? 1 : 0);
        params += '&gift_message=' + $('#gift_message').val();
        params += '&delivery_option[' + id_address + ']=' + id_delivery_option;
        url = orderOpcUrl;
    }
    else
        url = orderUrl;

    $.ajax({
        type: 'POST',
        headers: {"cache-control": "no-cache"},
        url: url + '?rand=' + new Date().getTime(),
        async: false,
        cache: false,
        dataType: "json",
        data: 'ajax=true'
                + '&method=' + method
                + params
                + '&id_address=' + id_address
                + '&id_delivery_option=' + id_delivery_option
                + '&token=' + static_token
                + '&allow_refresh=1',
        success: function (jsonData)
        {
            //$('#HOOK_EXTRACARRIER_'+id_address).html(jsonData['content']);
            setProtectRelaySelected();
            return false;
        }
    });
    return false;
}

function displayPickupPlace(info) {
    var id = "relay_point_selected_box";

    if (!info) {
        $('#' + id).hide();
        $('#' + id).remove();
        return false;
    }


    var block_carrier = $('.delivery-options');

    if ($('#' + id).length !== 0) {
        $('#' + id).html('<h3>' + relay_point_selected_box_label + '</h3>' + info);
        $('#' + id).show();
    }
    else {
        $('<div id="' + id + '"><h3>' + relay_point_selected_box_label + '</h3>' + info + '</div>').insertAfter(block_carrier);
    }
    return false;
}

function PS_MRAddSelectedCarrierInDB(id_carrier)
{
    // Make the request
    $.ajax({
        type: 'POST',
        url: MR_ajax_url,
        data: {'method': 'addSelectedCarrierToDB',
            'id_carrier': id_carrier,
            'id_mr_method': PS_MRCarrierMethodList[id_carrier],
            'mrtoken': mrtoken},
        success: function (json)
        {
            return false;
        }
    });
    return false;
}

