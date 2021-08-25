/**
 * 2010-2019 Webkul.
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
 *  @copyright 2010-2019 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

$(document).ready(function() {
    $.datepicker.regional['fr'] = {clearText: 'Effacer', clearStatus: '',
        closeText: 'Fermer', closeStatus: 'Fermer sans modifier',
        prevText: '<Préc', prevStatus: 'Voir le mois précédent',
        nextText: 'Suiv>', nextStatus: 'Voir le mois suivant',
        currentText: 'Courant', currentStatus: 'Voir le mois courant',
        monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
            'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
        monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
            'Jul','Aoû','Sep','Oct','Nov','Déc'],
        monthStatus: 'Voir un autre mois', yearStatus: 'Voir un autre année',
        weekHeader: 'Sm', weekStatus: '',
        dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
        dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
        dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
        dayStatus: 'Utiliser DD comme premier jour de la semaine', dateStatus: 'Choisir le DD, MM d',
        dateFormat: 'dd/mm/yy', firstDay: 0,
        initStatus: 'Choisir la date', isRTL: false};
    $.datepicker.setDefaults($.datepicker.regional['fr']);

    //if prestashop non booking product removed from the cart in checkout page
    $(document).on('click', '.remove-from-cart', function() {
        $(document).ajaxStop(function() {
            location.reload();
        });
    });

    var To_txt = 'Au';

    $(document).on('click', '.product_blooking_time_slot', function() {
        var selectedSlots = new Array();
        var checkedSlots = parseInt($('.product_blooking_time_slot:checked').length);
        if (checkedSlots < 1) {
            alert('Au moins un créneau doit être sélectionné pour la réservation.')
            return false;
        }
        $('.product_blooking_time_slot:checked').each(function() {
            selectedSlots.push({
                id_slot: $(this).val(),
                quantity: $(this).closest('.time_slot_checkbox').find('.booking_time_slots_quantity_wanted').val(),
            });
        });
        $.ajax({
            url: wkBookingCartLink,
            data: {
                action: 'booking_product_time_slots_price_calc',
                selected_slots: selectedSlots,
                date: $('#booking_time_slot_date').val(),
                quantity: $('#booking_time_slots_quantity_wanted').val(),
                id_product: $('#product_page_product_id').val(),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    var qtyWanted = parseInt($('#booking_product_quantity_wanted').val());
                    $('.booking_total_price').text(result.productPrice);
                }
                if (result.errors != 'undefined') {
                    var errorHtml = '';
                    $(result.errors).each(function(key, error) {
                        errorHtml += error + '</br>';
                    });
                    if (errorHtml != '') {
                        $(".booking_product_errors").html(errorHtml);
                        $(".booking_product_errors").show();
                        $('.booking_product_errors').fadeOut(4000);
                    }
                }
            }
        });
    });

    $(document).on('click', '.remove-booking-product', function(e) {
        e.preventDefault();
        var $current = $(this);
        $.ajax({
            url: wkBookingCartLink,
            data: {
                action: 'remove_booking_product_from_cart',
                booking_type: booking_type_date_range,
                id_cart_booking: $(this).attr('id-cart-booking'),
                id_product: $(this).attr('id-product'),
                id_product_attribute: $(this).attr('id-product-attribute'),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    location.reload();
                } else {
                    alert(result.msg);
                }
            }
        });
    });

    $(document).on('keyup', '.booking_time_slots_quantity_wanted', function() {
        var selectedSlots = new Array();
        var slot_max_avail_qty = $(this).closest('.time_slot_checkbox').find('.slot_max_avail_qty').val();
        var qty_wanted = $(this).val();
        if (qty_wanted == '' || !$.isNumeric(qty_wanted)) {
            //PAUL
            $(this).val('');
            //PAUL
            qty_wanted = $(this).val();
        }

        //PAUL
        var Me = $(this);
        $(document).on("focusout",Me,function(){
            if(Me.val() == ''){
                Me.val(1);
            }
        });
        //PAUL

        $(this).val(parseInt(qty_wanted));
        if (parseInt(qty_wanted) < 1 || parseInt(qty_wanted) > slot_max_avail_qty) {
            $(this).val(slot_max_avail_qty);
        }
        $('.product_blooking_time_slot:checked').each(function() {
            selectedSlots.push({
                id_slot: $(this).val(),
                quantity: $(this).closest('.time_slot_checkbox').find('.booking_time_slots_quantity_wanted').val(),
            });
        });
        $.ajax({
            url: wkBookingCartLink,
            data: {
                action: 'booking_product_time_slots_price_calc',
                selected_slots: selectedSlots,
                date: $('#booking_time_slot_date').val(),
                quantity: $(this).val(),
                id_product: $('#product_page_product_id').val(),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    $('.booking_total_price').text(result.productPrice);
                }
                if (result.errors != 'undefined') {
                    var errorHtml = '';
                    $(result.errors).each(function(key, error) {
                        errorHtml += error + '</br>';
                    });
                    if (errorHtml != '') {
                        $(".booking_product_errors").html(errorHtml);
                        $(".booking_product_errors").show();
                        $('.booking_product_errors').fadeOut(4000);
                    }
                }
            }
        });
    });

    $(document).on('click', '#booking_button', function(e) {
        e.preventDefault();
        $(".booking_loading_img").show();
        $('#bookings_in_select_range').empty();
        var booking_type = $(this).attr('booking_type');
        if (booking_type == booking_type_date_range) {
            var quantity = $('#booking_product_quantity_wanted').val();
            if (quantity > 0) {
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        action: 'add_booking_product_to_cart',
                        booking_type: booking_type_date_range,
                        date_from: $('#booking_date_from').val(),
                        date_to: $('#booking_date_to').val(),
                        quantity: quantity,
                        id_product: $('#product_page_product_id').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        $(".booking_loading_img").hide();
                        if (result.status == 'ok') {
                            $('#quantity_wanted').val(result.product_qty_to_cart);
                            $(".add-to-cart").click();
                            if (result.available_qty <= 0) {
                                $('#booking_button').attr('disabled', 'disabled');
                                $('.unavailable_slot_err').show();
                            }
                            $('.product_max_avail_qty_display').text(result.available_qty);
                        }
                        if (result.errors != 'undefined') {
                            var errorHtml = '';
                            $(result.errors).each(function(key, error) {
                                errorHtml += error + '</br>';
                            });
                            if (errorHtml != '') {
                                $(".booking_product_errors").html(errorHtml).show().fadeOut(4000);
                            }
                        }
                    }
                });
            } else {
                $(".booking_product_errors").html(invalidQtyErr).show().fadeOut(4000);
            }
        } else if (booking_type == booking_type_time_slot) {
            var selectedSlots = new Array();
            $('.product_blooking_time_slot:checked').each(function() {
                selectedSlots.push({
                    id_slot: $(this).val(),
                    quantity: $(this).closest('.time_slot_checkbox').find('.booking_time_slots_quantity_wanted').val(),
                });
            });
            $.ajax({
                url: wkBookingCartLink,
                data: {
                    action: 'add_booking_product_to_cart',
                    selected_slots: selectedSlots,
                    booking_type: booking_type_time_slot,
                    date: $('#booking_time_slot_date').val(),
                    quantity: $('#booking_time_slots_quantity_wanted').val(),
                    id_product: $('#product_page_product_id').val(),
                },
                method: 'POST',
                dataType: 'json',
                success: function(result) {
                    $(".booking_loading_img").hide();
                    if (result.status == 'ok') {
                        var qtyWanted = parseInt(result.totalQty);
                        $('#quantity_wanted').val(qtyWanted);
                        $(".add-to-cart").click();
                        $('.product_max_avail_qty_display').text(result.available_qty);

                        // show this booking info on cart popup instead od normal product info
                        var popUpInfoHtml = '<div class="cart_pop_up_data range-period">';
                        popUpInfoHtml += '<span>' + dateText + ' - ' + $('#booking_time_slot_date').val() + '</span></br>';
                        $(result.timeSlotsInfo).each(function(key, slot) {
                            if (slot.quantity_avail == 0) {
                                $('#slot_checkbox_' + slot.slot_id).prop('checked', false);
                                $('#slot_quantity_container_' + slot.slot_id).empty();
                                $('#slot_quantity_container_' + slot.slot_id).html('<span class="booked_slot_text">' + slot_booked_text + '</span>');
                            }
                            $('#slot_max_avail_qty_' + slot.slot_id).val(slot.quantity_avail);
                            $('#qty_avail_' + slot.slot_id).html('/' + slot.quantity_avail);
                            popUpInfoHtml += '<span>' + slot.slot_from + ' - ' + slot.slot_to + ' , ' + qtyText + ' - ' + slot.quantity + '</span></br>';
                        });
                        popUpInfoHtml += '<span>' + total_price_text + ' - ' + result.totalPriceFormatted + '</span></br>';
                        popUpInfoHtml += '</div>';
                    }
                    if (result.errors != 'undefined') {
                        var errorHtml = '';
                        $(result.errors).each(function(key, error) {
                            errorHtml += error + '</br>';
                        });
                        if (errorHtml != '') {
                            $(".booking_product_errors").html(errorHtml).show().fadeOut(4000);
                        }
                    }
                }
            });
        }
    });

    $(document).on("focus", ".booking_date_from, .booking_date_to", function() {

        $(".booking_date_from").datepicker({
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            beforeShow: function(input, instance) {
                $(".booking_date_to").removeClass('hasDatepicker');
            },
            //for calender Css
            onSelect: function(selectedDate) {
                var date_format = selectedDate.split("-");
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                if (considerDateToConfiguration == 0) {
                    selectedDate.setDate(selectedDate.getDate() + 1);
                } else {
                    selectedDate.setDate(selectedDate.getDate());
                }
                $(".booking_date_to").datepicker("option", "minDate", selectedDate);
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        action: 'booking_product_price_calc',
                        date_from: $('#booking_date_from').val(),
                        date_to: $('#booking_date_to').val(),
                        quantity: $('#booking_product_quantity_wanted').val(),
                        id_product: $('#product_page_product_id').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == 'ok') {
                            if (result.max_avail_qty == 0) {
                                $('#booking_button').attr('disabled', true);
                                $('.unavailable_slot_err').show();
                            } else {
                                $('.unavailable_slot_err').hide();
                                $('#booking_button').attr('disabled', false);
                            }
                            $('.product_max_avail_qty_display').text(result.max_avail_qty);
                            $('#max_available_qty').val(result.max_avail_qty);
                            $('.booking_total_price').text(result.productPrice);
                            // to show date ranges added in the selected date range
                            $('#bookings_in_select_range').empty();
                            if (result.showBookings && result.dateRangesBookingInfo.length != 0) {
                                html = '<label>' + bookings_in_select_range_label + '</label>';
                                html += '<table class="table table-stripped">';
                                html += '<thead>';
                                html += '<tr>';
                                html += '<th>' + dateRangeText + '</th>';
                                html += '<th>' + priceText + '</th>';
                                html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                $(result.dateRangesBookingInfo).each(function(key, rangeInfo) {
                                    html += '<tr>';
                                    html += '<td>';
                                    html += rangeInfo.date_from + ' &nbsp;' + To_txt + ' &nbsp;' + rangeInfo.date_to;
                                    html += '</td>';
                                    html += '<td>';
                                    html += rangeInfo.price;
                                    html += '</td>';
                                    html += '</tr>';
                                });
                                html += '</tbody>';
                                html += '</table>';
                                $('#bookings_in_select_range').append(html);
                            }
                        }
                        if (result.errors != 'undefined') {
                            var errorHtml = '';
                            $(result.errors).each(function(key, error) {
                                errorHtml += error + '</br>';
                            });
                            if (errorHtml != '') {
                                $(".booking_product_errors").html(errorHtml);
                                $(".booking_product_errors").show();
                                $('.booking_product_errors').fadeOut(4000);
                            }
                        }
                    }
                });
            },
            beforeShowDay: function(date) {
                var currentMonth = date.getMonth() + 1;
                var currentDate = date.getDate();
                if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                }
                var dateClass = '';
                dateToWork = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
                if ($('.booking_date_from').val()) {
                    var dateFromVal = $('.booking_date_from').val().split("-");
                    var dateFromVal = (dateFromVal[2]) + '-' + (dateFromVal[1]) + '-' + (dateFromVal[0]);
                    if (dateToWork == dateFromVal) {
                        dateClass = 'selectedCheckedDate';
                    }
                }

                if (typeof disabledDays != 'undefined' && disabledDays) {
                    var currentDay = date.getDay();
                    if ($.inArray(String(currentDay), disabledDays) != -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                if (typeof disabledDates != 'undefined' && disabledDates) {
                    if ($.inArray(dateToWork, disabledDates) !== -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                return [true, dateClass];
            },
        });

        $(".booking_date_to").datepicker({
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            beforeShowDay: function(date) {
                var currentMonth = date.getMonth() + 1;
                var currentDate = date.getDate();
                if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                }
                var dateClass = '';
                dateToWork = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
                if ($('.booking_date_to').val()) {
                    var dateToVal = $('.booking_date_to').val().split("-");
                    var dateToVal = (dateToVal[2]) + '-' + (dateToVal[1]) + '-' + (dateToVal[0]);
                    if (dateToWork == dateToVal) {
                        dateClass = 'selectedCheckedDate';
                    }
                }
                if (typeof disabledDays != 'undefined' && disabledDays) {
                    var currentDay = date.getDay();
                    if ($.inArray(String(currentDay), disabledDays) != -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                if (typeof disabledDates != 'undefined' && disabledDates) {
                    if ($.inArray(dateToWork, disabledDates) !== -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                return [true, dateClass];
            },
            beforeShow: function(input, instance) {
                $(".booking_date_from").removeClass('hasDatepicker');
                var minDateTo = $('.booking_date_from').val();
                var date_format = minDateTo.split("-");
                var minDateTo = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                if (considerDateToConfiguration == 0) {
                    minDateTo.setDate(minDateTo.getDate() + 1);
                } else {
                    minDateTo.setDate(minDateTo.getDate());
                }
                $(".booking_date_to").datepicker("option", "minDate", minDateTo);
            },
            onSelect: function(selectedDate) {
                var selectedDate = new Date($.datepicker.formatDate('dd-mm-yy', new Date(selectedDate)));
                if (considerDateToConfiguration == 0) {
                    selectedDate.setDate(selectedDate.getDate() - 1);
                }
                $(".booking_date_from").datepicker("option", "maxDate", selectedDate);
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        action: 'booking_product_price_calc',
                        date_from: $('#booking_date_from').val(),
                        date_to: $('#booking_date_to').val(),
                        quantity: $('#booking_product_quantity_wanted').val(),
                        id_product: $('#product_page_product_id').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == 'ok') {
                            if (result.max_avail_qty == 0) {
                                $('.unavailable_slot_err').show();
                                $('#booking_button').attr('disabled', true);
                            } else {
                                $('.unavailable_slot_err').hide();
                                $('#booking_button').attr('disabled', false);
                            }
                            $('.product_max_avail_qty_display').text(result.max_avail_qty);
                            $('#max_available_qty').val(result.max_avail_qty);
                            $('.booking_total_price').text(result.productPrice);
                            // to show date ranges added in the selected date range
                            $('#bookings_in_select_range').empty();
                            if (result.showBookings && result.dateRangesBookingInfo.length != 0) {
                                html = '<label>' + bookings_in_select_range_label + '</label>';
                                html += '<table class="table table-stripped">';
                                html += '<thead>';
                                html += '<tr>';
                                html += '<th>' + dateRangeText + '</th>';
                                html += '<th>' + priceText + '</th>';
                                html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                $(result.dateRangesBookingInfo).each(function(key, rangeInfo) {
                                    html += '<tr>';
                                    html += '<td>';
                                    html += rangeInfo.date_from + ' &nbsp;' + To_txt + ' &nbsp;' + rangeInfo.date_to;
                                    html += '</td>';
                                    html += '<td>';
                                    html += rangeInfo.price;
                                    html += '</td>';
                                    html += '</tr>';
                                });
                                html += '</tbody>';
                                html += '</table>';
                                $('#bookings_in_select_range').append(html);
                            }
                        }
                        if (result.errors != 'undefined') {
                            var errorHtml = '';
                            $(result.errors).each(function(key, error) {
                                errorHtml += error + '</br>';
                            });
                            if (errorHtml != '') {
                                $(".booking_product_errors").html(errorHtml);
                                $(".booking_product_errors").show();
                                $('.booking_product_errors').fadeOut(4000);
                            }
                        }
                    }
                });
            }
        });
    });

    $(document).on("focus", ".booking_time_slot_date", function() {
        var selectedJsonDates = JSON.parse(selectedDatesJson);



        $(".booking_time_slot_date").datepicker({
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            beforeShowDay: function(date) {
                var currentMonth = date.getMonth() + 1;
                var currentDate = date.getDate();
                if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                }
                var dateClass = '';
                dateToWork = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
                if ($('.booking_time_slot_date').val()) {
                    var dateVal = $('.booking_time_slot_date').val().split("-");
                    var dateVal = (dateVal[2]) + '-' + (dateVal[1]) + '-' + (dateVal[0]);
                    if (dateToWork == dateVal) {
                        dateClass = 'selectedCheckedDate';
                    }
                }
                if (typeof disabledDays != 'undefined' && disabledDays) {
                    var currentDay = date.getDay();
                    if ($.inArray(String(currentDay), disabledDays) != -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                if (typeof disabledDates != 'undefined' && disabledDates) {
                    if ($.inArray(dateToWork, disabledDates) !== -1) {
                        return [false, dateClass, disable_date_title];
                    }
                }
                if ($.inArray(dateToWork, selectedJsonDates) == -1) {
                    return [false, dateClass];
                } else {
                    return [true, dateClass];
                }
            },
            //for calender Css
            onSelect: function(selectedDate) {
                $.ajax({
                    url: wkBookingCartLink,
                    data: {
                        action: 'booking_product_time_slots',
                        date: $('#booking_time_slot_date').val(),
                        quantity: $('#booking_time_slots_quantity_wanted').val(),
                        id_product: $('#product_page_product_id').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == 'ok') {
                            var bookingTimeSlots = result.bookingTimeSlots;
                            var html = '';
                            if (bookingTimeSlots != 'empty') {
                                $.each(bookingTimeSlots, function(index, slot) {
                                    html += '<div class="time_slot_checkbox row">';
                                    html += '<label class="col-sm-8 form-control-static">';
                                    html += '<div class="checkbox">';
                                    html += '<input type="checkbox" id="slot_checkbox_' + slot.id_time_slots_price + '"';
                                    if (!slot.available_qty) {
                                        html += ' disabled="disabled ';
                                    }
                                    if (slot.checked) {
                                        html += ' checked="checked" ';
                                    }
                                    html += ' value="' + slot.id_time_slots_price + '" class="product_blooking_time_slot">&nbsp;&nbsp;&nbsp';
                                    html += '<span class="time_slot_price">' + slot.formated_slot_price + '</span>&nbsp;&nbsp;Pour&nbsp;&nbsp;';
                                    html += '<span class="time_slot_range">' + slot.time_slot_from + '&nbsp;-&nbsp;' + slot.time_slot_to + '</span>';
                                    html += '</div>';
                                    html += '</label>';
                                    if (slot.available_qty) {
                                        html += '<label class="col-sm-4" id="slot_quantity_container_' + slot.id_time_slots_price + '">';
                                        html += '<div class="input-group col-sm-12">';
                                        html += '<input type="hidden" id="slot_max_avail_qty_' + slot.id_time_slots_price + '" class="slot_max_avail_qty" value="' + slot.available_qty + '">';
                                        html += '<input type="number" class="booking_time_slots_quantity_wanted  form-control" value="1" min="1">';
                                        html += '<div class="input-group-addon" id="qty_avail_' + slot.id_time_slots_price + '">/' + slot.available_qty + '</div>';
                                        html += '</div>';
                                        html += '<p class="personnes_p">personne(s)</p>';
                                    } else {
                                        html += '<label class="col-sm-4 form-control-static" id="slot_quantity_container_' + slot.id_time_slots_price + '">';
                                        html += '<span class="booked_slot_text">' + slot_booked_text + '</span>';
                                    }
                                    html += '</label>';
                                    html += '</div>';
                                });
                                $('#booking_button').attr('disabled', false);
                                $('#booking_product_time_slots').html(html);
                                $(".unavailable_slot_err").hide();
                            } else {
                                $('#booking_product_time_slots').html(no_slots_available_text);
                                $('.unavailable_slot_err').show();
                                $('#booking_button').attr('disabled', true);
                            }
                            $('.booking_total_price').text(result.productTotalFeaturePriceFormated);
                            if (result.totalSlotsQty !== 'undefined' && result.totalSlotsQty == 0) {
                                $('#booking_button').attr('disabled', true);
                            }

                            //PAUL : fix change quantity of Activity
                            if( $('input.booking_time_slots_quantity_wanted').length > 0 ){
                                var bookMulti = $('input.booking_time_slots_quantity_wanted');
                                var maxQty = bookMulti.parent().find('.slot_max_avail_qty').val();

                                bookMulti.on("keypress", function () {
                                    maxQty = $(this).parent().find('.slot_max_avail_qty').val();
                                    var strLgth = $(this).val().length;
                                    if((strLgth >= 2 && $(this).val() <= maxQty) || (maxQty.length == 1 || maxQty == 10)){
                                        $(this).select();
                                    }
                                });

                                $(document).on("focusout",bookMulti,function(){
                                    if(bookMulti.val() == ''){
                                        bookMulti.val(1);
                                    }
                                });
                                // bookMulti.spinner({min: 1, max: maxQty});
                            }

                            if( $('#booking_product_quantity_wanted').length > 0 ){
                                var bookSingle = $('#booking_product_quantity_wanted');
                                var maxQty = $('#booking_product_available_qty').find('.product_max_avail_qty_display').text();

                                bookSingle.on("keypress", function () {
                                    maxQty = $('#booking_product_available_qty').find('.product_max_avail_qty_display').text();
                                    var strLgth = $(this).val().length;
                                    if((strLgth >= 2 && $(this).val() <= maxQty) || (maxQty.length == 1 || maxQty == 10)){
                                        $(this).select();
                                    }
                                });

                                $(document).on("focusout",bookMulti,function(){
                                    if(bookSingle.val() == ''){
                                        bookSingle.val(1);
                                    }
                                });

                                // bookSingle.spinner({min: 1, max: maxQty});
                            }
                            //PAUL


                        }
                        if (result.errors != 'undefined') {
                            var errorHtml = '';
                            $(result.errors).each(function(key, error) {
                                errorHtml += error + '</br>';
                            });
                            if (errorHtml != '') {
                                $(".booking_product_errors").html(errorHtml);
                                $(".booking_product_errors").show();
                                $('.booking_product_errors').fadeOut(5000);
                            }
                        }
                    }
                });
            },
        });
    });

    $(document).on('keyup', '#booking_product_quantity_wanted', function() {
        var qty_wanted = $('#booking_product_quantity_wanted').val();
        if (qty_wanted == '' || !$.isNumeric(qty_wanted)) {
            //PAUL
            $('#booking_product_quantity_wanted').val('');
            //PAUL
            qty_wanted = $('#booking_product_quantity_wanted').val();
        }

        //PAUL
        $(document).on("focusout",$('#booking_product_quantity_wanted'),function(){
            if($('#booking_product_quantity_wanted').val() == ''){
                $('#booking_product_quantity_wanted').val(1);
            }
        });
        //PAUL
        
        
        $('#booking_product_quantity_wanted').val(parseInt(qty_wanted));
        if (parseInt(qty_wanted) < 1 || parseInt(qty_wanted) > $('#max_available_qty').val()) {
            $('#booking_product_quantity_wanted').val($('#max_available_qty').val());
        }
        $.ajax({
            url: wkBookingCartLink,
            data: {
                action: 'booking_product_price_calc',
                date_from: $('#booking_date_from').val(),
                date_to: $('#booking_date_to').val(),
                quantity: $('#booking_product_quantity_wanted').val(),
                id_product: $('#product_page_product_id').val(),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'ok') {
                    if (result.max_avail_qty == 0) {
                        $('.unavailable_slot_err').show();
                        $('#booking_button').attr('disabled', true);
                    } else {
                        $('.unavailable_slot_err').hide();
                        $('#booking_button').attr('disabled', false);
                    }
                    $('.product_max_avail_qty_display').text(result.max_avail_qty);
                    $('#max_available_qty').val(result.max_avail_qty);
                    $('.booking_total_price').text(result.productPrice);
                }
                if (result.errors != 'undefined') {
                    var errorHtml = '';
                    $(result.errors).each(function(key, error) {
                        errorHtml += error + '</br>';
                    });
                    if (errorHtml != '') {
                        $(".booking_product_errors").html(errorHtml);
                        $(".booking_product_errors").show();
                        $('.booking_product_errors').fadeOut(4000);
                    }
                }
            }
        });
    });
});