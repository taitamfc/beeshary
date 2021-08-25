/**
* 2010-2018 Webkul.
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
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

var storeLocations, pickUpStoreLocations, applyForAllEnable = 0;
$(document).ready(function() {
    storeLocations = $.parseJSON(storeLocationsJson);
    pickUpStoreLocations = storeLocations;
    days = null;
    var selectedHours = null;
    var todayDate = new Date().getDate();
    $(document).on('focus', '#wk_store_date_pickup', function() {
        idSeller = $('#wk_set_store').attr('id-seller');
        if (typeof idSeller != 'undefined') {
            mpMinimumDays = storeConfiguration[idSeller]['minimum_days'];
            mpMinimumHours = storeConfiguration[idSeller]['minimum_hours'];
            mpMaximumDays = storeConfiguration[idSeller]['maximum_days'];
            maxPickUp = storeConfiguration[idSeller]['max_pick_ups'];
        }
        if (mpMinimumDays == 0) {
            minDate = todayDate;
        } else{
            minDate = todayDate + parseInt(mpMinimumDays);
        }
        maxDate = todayDate + parseInt(mpMinimumDays) + parseInt(mpMaximumDays) - 1;
        startingDate = new Date();
        i = 0;
        if ($('#wk_store_date_pickup') != 'undefined') {
            $('#wk_store_date_pickup').datetimepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                useCurrent: false,
                startDate: new Date(new Date().setDate(minDate)),
                endDate: new Date(new Date().setDate(maxDate)),
                minuteStep: 0,
                initialDate: null,
                minView: 2,
                container: 'div#wk_store_date_pickup_container',
                pickerPosition: 'top-right',
                onRenderDay: function(date) {
                    today = new Date();
                    currentTime = today.getHours();
                    var currentMonth = date.getMonth() + 1;
                    var currentDate = date.getDate();

                    if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                    }
                    if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                    }
                    dateToWork = date.getFullYear() + "-" + currentMonth + "-" + currentDate;

                    if (maxPickUp != 0) {
                        disabledDates = Object.values(disabledDates);
                        if (typeof disabledDates != 'undefined' && disabledDates) {
                            if ($.inArray(dateToWork, disabledDates) !== -1) {
                                return ['disabled'];
                            }
                        }
                    }

                    if(date.getDate() == today.getDate()
                        && currentTime + parseInt(mpMinimumHours) > 23
                    ) {
                        return ['disabled'];
                        // Date equals today's date
                    } else if (days == null || !days[date.getDay()]) {
                        return ['disabled'];
                    } else{
                        return [true];
                    }
                },
            }).on('changeDate', function(ev){
                $('#wk_store_time_pickup').val('');
            });
            // <i class="material-icons">keyboard_arrow_left</i>
            $('#wk_store_date_pickup_container span.glyphicon.glyphicon-arrow-left').addClass('material-icons').html('keyboard_arrow_left');
            $('#wk_store_date_pickup_container span.glyphicon.glyphicon-arrow-right').addClass('material-icons').html('keyboard_arrow_right');
            $('#wk_store_date_pickup_container span.glyphicon.glyphicon-remove-circle').removeClass('glyphicon glyphicon-remove-circle').addClass('icon icon-remove-circle');
        }
    });

    $(document).on('click', '.wk-select-store', function() {
        idStore = $(this).attr('id-store');
        idSeller = $(this).attr('id-seller');
        if (typeof idSeller == 'undefined') {
            idSeller = 0;
        }
        index = $(this).attr('index');
        days = $.parseJSON(storeLocations[index]['store_open_days']);
        pickUpStartTime = storeLocations[index]['pickup_start_time'];
        pickUpEndTime = storeLocations[index]['pickup_end_time'];
        pickUpStartTime = pickUpStartTime.split(':');
        pickUpEndTime = pickUpEndTime.split(':');
        paymentOptions = $.parseJSON(storeLocations[index]['payment_option']);
        selectedHours = storeLocations[index]['hours'];
        html = '<div class="wkstore-pickup-name">' + storeLocations[index].name + '</div>';

        html += '<div>' + storeLocations[index].address1 + ' ' + storeLocations[index].address2 + '</div>';
        html += '<div>' + storeLocations[index].city_name;
        if (storeLocations[index].state_name != null) {
            html += ',' + storeLocations[index].state_name;
        }
        $('#wk_set_store').attr('id-seller', idSeller);
        html += ' ' + storeLocations[index].zip_code;
        html += '</div><div>' + storeLocations[index].country_name + '</div>';
        $('#wk_selected_store_address').html(html);

        hideStorePickup(idSeller);
        $('#selectedStoreId').val(idStore);

        mpMinimumDays = storeConfiguration[idSeller]['minimum_days'];
        mpMinimumHours = storeConfiguration[idSeller]['minimum_hours'];
        mpMaximumDays = storeConfiguration[idSeller]['maximum_days'];
        maxPickUp = storeConfiguration[idSeller]['max_pick_ups'];
        infoWindow.close();
        $.ajax({
            url: ajaxurlStoreByKey,
            type: "POST",
            data: {
                id_store: idStore,
                id_seller: idSeller,
                action: 'getStoreProductDetails',
                ajax: true
            },
            dataType: 'json',
            success: function(result) {
                $('#wk_set_store').show();
                applyForAllEnable = result.applyForAll;
                if (result.applyForAll == '1') {
                    $('#apply_for_all').show();
                } else {
                    $('#apply_for_all').hide();
                    $('#apply_for_all').removeAttr('checked');
                }
                if (result.html != 'undefined') {
                    $('#wk_store_payment_info').html(result.html);
                }
                if (result.disabledDates != 'undefined') {
                    disabledDates = result.disabledDates;
                }
                $('#wk_store_time_pickup').val('');
                $('#wk_store_date_pickup').val('');
            }
        });
    });

    $(document).find('#wk_store_pickup').hide();
    $('.wk_store_payment_details').hide();
    // $('.wk_pickup_datetime').hide();

    $(document).on('click', '#wk_set_store', function(e) {
        idProduct = $(this).attr('id-product');
        idProductAttr = $(this).attr('id-product-attr');
        idSeller = $(this).attr('id-seller');
        date = $('#wk_store_date_pickup').val();
        time = $('#wk_store_time_pickup').val();
        idSelected = $('#selectedStoreId').val();
        error = 0;

        if (typeof storeConfiguration != 'undefined' && idSeller != 0) {
            if (storeConfiguration[idSeller]['enable_date'] == 1) {
                if (date == '') {
                    error = 1;
                    $.growl.error({ title: "", message: dateError });
                }
                if (storeConfiguration[idSeller]['enable_time'] == 1) {
                    if (time == '') {
                        $.growl.error({ title: "", message: timeError });
                        error = 1;
                    }
                }
            }
        }
        if (idSelected == '') {
            $.growl.error({ title: "", message: idSelectError });
            error = 1;
        }
        if (error == 0) {
            data = {
                action: 'saveStoreDetails',
                ajax: true
            };
            if ($('#wk_apply_for_all').is(':checked')) {
                $('.wk_store_details').html($('#wk_selected_store_address').html());
                $('.wk_store_payment_details').html($('#wk_store_payment_info').html());
                // $('#wk_pickup_date').html(time);
                $('.wk_store_date_pickup').val(date);
                $('.wk_store_time_pickup').val(time);
                $('.wk_pickup_date').html(date);
                $('.wk_pickup_time').html(time);
                $('.wk_pickup_datetime').show();
                // $('#wk_pickup_time').html(date);
                $('.wk_store_id').val(idSelected);
                $('.wk_store_id').attr('apply_for_all', 1);
                data['apply_for_all'] = 1;
            } else {
                $('#wk_store_details_'+idProduct+'_'+idProductAttr).html($('#wk_selected_store_address').html());
                $('#wk_store_payment_details_'+idProduct+'_'+idProductAttr).html($('#wk_store_payment_info').html());
                // $('#wk_pickup_date_'+idProduct).html(time);

                $('#wk_pickup_date_'+idProduct+'_'+idProductAttr).html(date);
                $('#wk_pickup_time_'+idProduct+'_'+idProductAttr).html(time);
                $('#wk_pickup_datetime_'+idProduct+'_'+idProductAttr).show();
                hideStoreDateTimePick(idSeller);
                // $('#wk_pickup_time_'+idProduct).html(date);
                $('#wk_store_id_'+idProduct+'_'+idProductAttr).val(idSelected);
                $('#wk_store_id_'+idProduct+'_'+idProductAttr).attr('apply_for_all', applyForAllEnable);
                $('#wk_store_id_'+idProduct+'_'+idProductAttr).attr('id-seller', idSeller);
                data['apply_for_all'] = 0;
                data['id_product'] = idProduct;
                data['id_product_attr'] = idProductAttr;
            }
            data['wk_id_seller'] = idSeller;
            data['wk_pickup_time'] = time;
            data['wk_pickup_date'] = date;
            data['wk_id_store'] = idSelected;
            $.ajax({
                url: ajaxurlStoreByKey,
                type: "POST",
                data: data,
                dataType: 'json',
                success: function(result) {
                    if (typeof result != 'undefined') {
                    } else {
                    }
                }
            });
            $('#wkshowStore').modal('hide');
        }
    });

    $(document).on('focus', '#wk_store_time_pickup', function() {
        if ($('#wk_store_date_pickup').val() == 'undefined' || $('#wk_store_date_pickup').val() == '') {
            $('#wk_store_time_pickup').datetimepicker('remove');
            $.growl.error({ title: "", message: dateError });
        } else {
            if ($('#wk_store_time_pickup') != 'undefined') {
                $('#wk_store_time_pickup').datetimepicker({
                    format: "hh:ii:00",
                    autoclose: true,
                    useCurrent: false,
                    minuteStep: 5,
                    startView:1,
                    forceParse: false,
                    showMeridian: true,
                    maxView: 1,
                    container: 'div#wk_store_time_pickup_container',
                    pickerPosition: 'top-right',
                    onRenderDay: function(date) {
                        if (days == null || !days[date.getDay()]) {
                            return ['disabled'];
                        }
                    },
                    onRenderHour: function(date) {
                        //Disable any time between 12:00 and 13:59
                        if ($('#wk_store_date_pickup').val() != 'undefined' && $('#wk_store_date_pickup').val() != '') {
                            if (typeof $('#wk_store_date_pickup').val() != 'undefined') {
                                selectedDate = ($('#wk_store_date_pickup').val()).split('-');
                                selectedDate = new Date(selectedDate);
                                today = new Date();
                                currentTime = today.getHours();
                                if(selectedDate.getDate() == today.getDate()
                                    && date.getUTCHours() < currentTime + parseInt(mpMinimumHours)
                                ) {
                                    return ['disabled'];
                                    // Date equals today's date
                                }
                                if (days != null && days[selectedDate.getDay()] && selectedHours != null) {
                                    time = selectedHours[dayOfWeek[selectedDate.getDay()]].split('- ');
                                    if (date.getUTCHours()  < convertTime12to24(time[0])[0] || date.getUTCHours() > convertTime12to24(time[1])[0]) {
                                        return ['disabled'];
                                    }
                                    if (pickUpStartTime[0] > date.getUTCHours() || pickUpEndTime[0] < date.getUTCHours()) {
                                        return ['disabled'];
                                    }
                                } else {
                                }
                                return [true];
                            }
                        } else {
                        }
                        return ['disabled'];
                    },
                    onRenderMinute: function(date) {
                        today = new Date();
                        currentTime = today.getHours();
                        if (date.getDate() == today.getDate()
                            && (date.getUTCHours() == currentTime + parseInt(mpMinimumHours))
                            && today.getMinutes() > date.getUTCMinutes()
                        ) {
                            return ['disabled'];
                        } else if (parseInt(pickUpStartTime[0]) > date.getUTCHours()
                            || parseInt(pickUpEndTime[0]) < date.getUTCHours()
                        ) {
                            return ['disabled'];
                        } else if ((parseInt(pickUpStartTime[0]) == date.getUTCHours()
                            && parseInt(pickUpStartTime[1]) > date.getUTCMinutes())
                            || (parseInt(pickUpEndTime[0]) == date.getUTCHours()
                            && parseInt(pickUpEndTime[1]) < date.getUTCMinutes())
                        ) {
                            return ['disabled'];
                        }

                        if (typeof $('#wk_store_date_pickup').val() != 'undefined') {
                            selectedDate = ($('#wk_store_date_pickup').val()).split('-');
                            selectedDate = new Date(selectedDate);
                            time = selectedHours[dayOfWeek[selectedDate.getDay()]].split('- ');
                            var pickUpStartTimer = convertTime12to24(time[0]);
                            var pickUpEndTimer = convertTime12to24(time[1]);
                            if ((date.getUTCHours() == pickUpStartTimer[0] && date.getUTCMinutes() < pickUpStartTimer[1])
                                || (date.getUTCHours() == pickUpEndTimer[0] && date.getUTCMinutes() > pickUpEndTimer[1])
                            ) {
                                return ['disabled'];
                            }
                        }
                        return [true];
                    },
                });
                $('#wk_store_time_pickup').datetimepicker('setStartDate', new Date($('#wk_store_date_pickup').val()));
            }
        }

        $('#wk_store_time_pickup_container .datetimepicker .next').addClass('disabled');
        $('#wk_store_time_pickup_container .datetimepicker .prev').addClass('disabled');
        // $('#wk_store_time_pickup_container .datetimepicker .next').addClass('disabled');
        // $('#wk_store_time_pickup_container .datetimepicker .prev').addClass('disabled');
    });

    $("#wkshowStore").on("hidden.bs.modal", function () {
        $('#selectedStoreId').val('');
        $('#wk_store_date_pickup').val('');
        $('#wk_store_time_pickup').val('');
        $(document).find('#wk_store_pickup').hide();
        $('#wk_selected_store_address').html('');
    });

    function convertTime12to24(time12h) {
        const [time, modifier] = time12h.split(' ');

        let [hours, minutes] = time.split(':');

        if (hours === '12') {
          hours = '00';
        }

        if (modifier === 'pm') {
          hours = parseInt(hours, 10) + 12;
        }

        return [hours, minutes];
    }

    $(document).on('click', '.wk-store-select', function(e) {
        idProduct = $(this).attr('id-product');
        idProductAttr = $(this).attr('id-product-attr');
        idStore = $('#wk_store_id_'+idProduct+'_'+idProductAttr).val();
        idSeller = $('#wk_store_id_'+idProduct+'_'+idProductAttr).attr('id-seller');
        $('#wk_set_store').attr('id-product', idProduct);
        $('#wk_set_store').attr('id-product-attr', idProductAttr);

        if ($('#wk_store_id_'+idProduct+'_'+idProductAttr).val() != 'undefined' && $('#wk_store_id_'+idProduct+'_'+idProductAttr).val() != '') {
            $('#wk_selected_store_address').html($('#wk_store_details_'+idProduct+'_'+idProductAttr).html());
            // $('#wk_pickup_date_'+idProduct+'_'+idProductAttr).html($('#wk_store_time_pickup').val());
            var date = $('#wk_store_date_pickup_'+idProduct+'_'+idProductAttr).val();
            if (date != '0000-00-00' && date != ''){
                $('#wk_store_date_pickup').val(date);
            }
            // $('#wk_store_time_pickup').val($('#wk_store_time_pickup_'+idProduct+'_'+idProductAttr).val());
            // $('#wk_pickup_time_'+idProduct+'_'+idProductAttr).html($('#wk_store_date_pickup').val());
            $('#selectedStoreId').val($('#wk_store_id_'+idProduct+'_'+idProductAttr).val());
            $('#wk_store_details_'+idProduct+'_'+idProductAttr).html();
            $('#wk_store_pickup').show();
            $('#wk_set_store').show();
        }
        if (typeof idSeller == 'undefined') {
            idSeller = 0;
        } else {
            $('#wk_set_store').attr('id-seller', idSeller);
        }

        if (idStore != 'undefined' && idStore != '') {
            hideStorePickup(idSeller);
            idStoreIndex = getStoreDetails(idStore);
            if (idStoreIndex != -1) {
                days = $.parseJSON(pickUpStoreLocations[idStoreIndex]['store_open_days']);
                selectedHours = pickUpStoreLocations[idStoreIndex]['hours'];

                pickUpStartTime = pickUpStoreLocations[idStoreIndex]['pickup_start_time'];
                pickUpEndTime = pickUpStoreLocations[idStoreIndex]['pickup_end_time'];
                pickUpStartTime = pickUpStartTime.split(':');
                pickUpEndTime = pickUpEndTime.split(':');
                paymentOptions = $.parseJSON(pickUpStoreLocations[idStoreIndex]['payment_option']);
                selectedHours = pickUpStoreLocations[idStoreIndex]['hours'];
            }
        }

        $.ajax({
            url: ajaxurlStoreByKey,
            type: "POST",
            data: {
                id_product: idProduct,
                id_store: idStore,
                current_location: currentLocation,
                action: 'getStoreDetailsByIdProduct',
                ajax: true
            },
            dataType: 'json',
            success: function(result) {
                if (result.hasError) {
                    $('#map-canvas').empty();
                    $("#wrapper_content_left").html('<h2>' + no_store_found + '</h2>');
                    $('#wrapper_content_right').css({height: '0px'});
                } else {
                    $('#wk_apply_for_all').removeAttr('checked');
                    if ($('#wk_store_id_'+idProduct+'_'+idProductAttr).val() != 'undefined' && $('#wk_store_id_'+idProduct+'_'+idProductAttr).val() != '') {

                    } else {
                        $('#wk_set_store').hide();
                        $('#wkshowstore .modal-footer').hide();
                    }
                    if($('#wk_store_id_'+idProduct+'_'+idProductAttr).attr('apply_for_all') == 1) {
                        $('#apply_for_all').show();
                    } else {
                        $('#apply_for_all').hide();
                    }
                    $('#wk_store_payment_info').html($('#wk_store_payment_details_'+idProduct+'_'+idProductAttr).html());

                    $('#wrapper_content_right').css({height: '300px'});
                    storeLocations = result.stores;
                    orderControllerStores = result.stores;
                    disabledDates = result.disabledDates;
                    googleStoreLocator(storeLocations, result.html);
                }
            }
        });
    });

    $('button[name="confirmDeliveryOption"]').on('click', function(e) {
        var idAddress = $('input[name="id_address_delivery"]:checked').val();
        var idCarriers = $('input[name="delivery_option['+idAddress+']"]:checked').val();
        idCarriers = idCarriers.slice(0, -1);
        idCarriers = (idCarriers).split(',');
        shouldProceed = 1;
        if ($.inArray(storeCarrierId, idCarriers) != -1) {
            $.ajax({
                url: ajaxurlStoreByKey,
                type: "POST",
                data: {
                    action: 'checkAllProducts',
                    ajax: true
                },
                dataType: 'json',
                async: false,
                success: function(result) {
                    if (result.hasError) {
                        shouldProceed = 0;
                        $.growl.error({title: '', message: result.error});
                    }
                }
            });
        }
        if (shouldProceed == '1') {
            return true;
        } else {
            return false;
        }
    });
})

function getStoreDetails(idStore) {
    idStoreIndex = -1;
    storePickUpLocations = $.parseJSON(storeLocationsJson);
    $.each(storePickUpLocations, function (index, store) {
        if (store['id'] == idStore) {
            idStoreIndex = index;
            return index;
        }
    });
    return idStoreIndex;
}


function hideStorePickup(idSeller) {
    if (typeof storeConfiguration != 'undefined' && idSeller != 0) {
        if (storeConfiguration[idSeller]['enable_date'] == 1) {
            $('#wk_store_pickup').show();
            if (storeConfiguration[idSeller]['enable_time'] == 0) {
                $('#wk_store_time_pickup').closest('.form-group').hide();
            } else {
                $('#wk_store_time_pickup').closest('.form-group').show();
            }
        } else {
            $('#wk_store_pickup').hide();
        }
    }
}

function hideStoreDateTimePick(idSeller) {
    if (typeof storeConfiguration != 'undefined' && idSeller != 0) {
        if (storeConfiguration[idSeller]['enable_date'] == 1) {
            $('#wk_store_date_pickup_'+idProduct+'_'+idProductAttr).val(date);
            if (storeConfiguration[idSeller]['enable_time'] == 1) {
                $('#wk_store_time_pickup_'+idProduct+'_'+idProductAttr).val(time);
                $('#wk_store_time_pickup_'+idProduct+'_'+idProductAttr).closest('.wk-pick').show();
            } else {
                $('#wk_store_time_pickup_'+idProduct+'_'+idProductAttr).closest('.wk-pick').hide();
            }
            $('#wk_pickup_datetime_'+idProduct+'_'+idProductAttr).show();
        } else {
            $('#wk_pickup_datetime_'+idProduct+'_'+idProductAttr).hide();
        }
    }
}