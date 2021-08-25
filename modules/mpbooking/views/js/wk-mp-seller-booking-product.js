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
	
	$.timepicker.regional['fr'] = {
		timeOnlyTitle: 'Sélectionnez l\'heure',
		timeText: 'Heure',
		hourText: 'Heures',
		minuteText: 'Minutes',
		secondText: 'Secondes',
		millisecText: 'Millisecondes',
		timezoneText: 'Fuseau horaire',
		currentText: 'Maintenant',
		closeText: 'Fermer',
		amNames: ['AM', 'A'],
		pmNames: ['PM', 'P'],
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['fr']);
	
	console.log('test');
	
    //Tab active code
    if ($('#active_tab').val() != '') {
        var active_tab = $('#active_tab').val();
        changeTabStatus(active_tab);
    }

    //Add product and update product form validation
    $('#wk_mp_seller_booking_product_form').on("submit", function(e) {
        //get all checked category value in a input hidden type name 'product_category'
        var rawCheckedID = [];
        $('.jstree-clicked').each(function() {
            var rawIsChecked = $(this).parent('.jstree-node').attr('id');
            rawCheckedID.push(rawIsChecked);
        });

        $('#product_category').val(rawCheckedID.join(","));

        var checkbox_length = $('#product_category').val();
        if (checkbox_length == 0) {
            alert(req_catg);
            return false;
        }
        window.wkerror = false;
        // validate seller product form
        $(".submitBookingProduct").css('pointer-events', 'none');
        $('.wk_product_loader').show();
        $.ajax({
            url: path_sellerproduct,
            cache: false,
            type: 'POST',
            async: false,
            dataType: "json",
            data: {
                ajax: true,
                action: 'validateMpBookingProductForm',
                formData: $("form").serialize(),
                token: $('#wk-static-token').val(),
                id_mp_product: $('#mp_product_id').val(),
            },
            success: function(result) {
                $('.wk_product_loader').hide();
                $(":input").removeClass('border_warning');
                if (result.status == 'ko') {
                    $('#wk_mp_form_error').text(result.msg).show('slow');
                    changeTabStatus(result.tab);

                    $('html,body').animate({
                        scrollTop: $("#wk_mp_form_error").offset().top - 10
                    }, 'slow');

                    if (result.multilang == 1) {
                        $('.' + result.inputName).addClass('border_warning');
                    } else {
                        $('input[name="' + result.inputName + '"]').addClass('border_warning');
                    }
                    window.wkerror = true;
                    $("html, body").animate({ scrollTop: 300 }, "slow");
                }
            }
        });
        if (window.wkerror) {
            $(".submitBookingProduct").css('pointer-events', '');
            return false;
        }
    });

    $(document).on("click", '.submitBookingProduct, #availability-search-submit', function(e) {
        //put active tab in input hidden
        if (adminController) {
            var active_tab_id = $('.wk-tabs-panel .nav-tabs li.active>a').attr('href');
        } else {
            var active_tab_id = $('.wk-tabs-panel .nav-tabs li>a.active').attr('href');
        }
        if (typeof active_tab_id !== 'undefined') {
            var active_tab_name = active_tab_id.substring(1, active_tab_id.length);
            $('#active_tab').val(active_tab_name);
        }
    });

    $(document).on("click", '.submitBookingProduct', function(e) {
        //get all checked category value in a input hidden type name 'product_category'
        var rawCheckedID = [];
        $('.jstree-clicked').each(function() {
            var rawIsChecked = $(this).parent('.jstree-node').attr('id');
            rawCheckedID.push(rawIsChecked);
        });

        $('#product_category').val(rawCheckedID.join(","));
        var checkbox_length = $('#product_category').val();
        if (checkbox_length == 0) {
            alert('choose category');
            return false;
        }
    });

    // select which type of booking is
    $('#booking_type').on('change', function() {
        if ($(this).val() == booking_type_date_range) {
            $('.booking_price_period').text(day_text);
        } else if ($(this).val() == booking_type_time_slot) {
            $('.booking_price_period').text(slot_text);
        }
    });

    $('.delete_feature_plan').on('click', function(){
        if (confirm(confirm_delete_msg)) {
            return true;
        } else {
            return false;
        }
    });

    // enable/disable time slots
    $(document).on("click", '.slot_active_img', function(e) {
        $(this).hide();
        $(this).closest('.slot_status_div').find('.slot_deactive_img').show();
        $(this).closest('.slot_status_div').find('.time_slot_status').val(0);
    });
    $(document).on("click", '.slot_deactive_img', function(e) {
        $(this).hide();
        $(this).closest('.slot_status_div').find('.slot_active_img').show();
        $(this).closest('.slot_status_div').find('.time_slot_status').val(1);
    });

    //date range row append
    $(document).on('click', '#add_more_date_ranges', function() {
        var date_ranges_length = $('.booking_date_ranges').length;
        if (adminController) {
            html = '<div class="single_date_range_slots_container" date_range_slot_num="' + date_ranges_length + '">';
                html += '<div  class="form-group table-responsive-row col-sm-6">';
                    html += '<table class="table">';
                        html += '<thead>';
                            html += '<tr>';
                                html += '<th class="center">';
                                    html += '<span>' + 'Du' + '</span>';
                                html += '</th>';
                                html += '<th class="center">';
                                    html += '<span>' + 'Au' + '</span>';
                                html += '</th>';
                            html += '</tr>';
                        html += '</thead>';
                        html += '<tbody>';
                            html += '<tr>';
                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input autocomplete="off" class="form-control sloting_date_from" type="text" name="sloting_date_from[]" value="" readonly>';
                                        html += '<span class="input-group-addon">';
                                            html += '<i class="icon-calendar"></i>';
                                        html += '</span>';
                                    html += '</div>';
                                html += '</td>';

                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input autocomplete="off" class="form-control sloting_date_to" type="text" name="sloting_date_to[]" value="" readonly>';
                                        html += '<span class="input-group-addon">';
                                            html += '<i class="icon-calendar"></i>';
                                        html += '</span>';
                                    html += '</div>';
                                html += '</td>';
                            html += '</tr>';
                        html += '</tbody>';
                    html += '</table>';
                html += '</div>';
                html += '<div  class="form-group table-responsive-row col-sm-6 time_slots_prices_table_div">  ';
                    html += '<table class="table time_slots_prices_table">';
                        html += '<thead>';
                            html += '<tr>';
                                html += '<th class="center">';
                                    html += '<span>' + 'Plage horaire à partir de' + '</span>';
                                html += '</th>';
                                html += '<th class="center">';
                                    html += '<span>' + 'À' + '</span>';
                                html += '</th>';
                                html += '<th class="center">';
                                    html += '<span>' + 'Prix ​​(HT)' + '</span>';
                                html += '</th>';
                                html += '<th class="center">';
                                    html += '<span>' + 'Statut' + '</span>';
                                html += '</th>';
                            html += '</tr>';
                        html += '</thead>';
                        html += '<tbody>';
                            html += '<tr>';
                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input id="booking_time_from" autocomplete="off" class="booking_time_from" type="text" name="booking_time_from' + date_ranges_length + '[]" value="" readonly>';
                                        html += '<span class="input-group-addon">';
                                            html += '<i class="icon-clock-o"></i>';
                                        html += '</span>';
                                    html += '</div>';
                                html += '</td>';
                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to' + date_ranges_length + '[]" value="" readonly>';
                                        html += '<span class="input-group-addon">';
                                            html += '<i class="icon-clock-o"></i>';
                                        html += '</span>';
                                    html += '</div>';
                                html += '</td>';
                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input type="text" name="slot_range_price' + date_ranges_length + '[]" value="' + Math.round($('#price').val()) + '">';
                                        html += '<span class="input-group-addon">' + defaultCurrencySign + '</span>';
                                    html += '</div>';
                                html += '</td>';
                                html += '<td class="center">';
                                    html += '<div class="slot_status_div">';
                                        html += '<input type="hidden" value="1" name="slot_active' + date_ranges_length + '[]" class="time_slot_status">';
                                        html += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-check.png" class="slot_active_img">';
                                        html += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-close.png" style="display:none;" class="slot_deactive_img">';
                                    html += '</div>';
                                html += '</td>';
                                html += '<td class="center">';
                                    html += '<a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>';
                                html += '</td>';
                            html += '</tr>';
                        html += '</tbody>';
                    html += '</table>';
                    html += '<div class="form-group">';
                        html += '<div class="col-lg-12">';
                            html += '<button class="add_more_time_slot_price" class="btn btn-default" type="button" data-size="s" data-style="expand-right">';
                                html += '<i class="icon-calendar-empty"></i>' + '&nbsp;Add More Slots';
                            html += '</button>';
                        html += '</div>';
                    html += '</div>';
                html += '</div>';
            html += '</div>';
        } else {
            html = '<div class="single_date_range_slots_container col-sm-12" date_range_slot_num="'+date_ranges_length+'">';
                html += '<div  class="form-group table-responsive booking_date_ranges">';
                    html += '<table class="table">';
                        html += '<thead>';
                            html += '<tr>';
                                html += '<th class="center">';
                                    html += '<span>'+'Du'+'</span>';
                                html += '</th>';
                                html += '<th class="center">';
                                    html += '<span>'+'Au'+'</span>';
                                html += '</th>';
                            html += '</tr>';
                        html += '</thead>';
                        html += '<tbody>';
                            html += '<tr>';
                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input autocomplete="off" class="form-control sloting_date_from" type="text" name="sloting_date_from[]" value="" readonly>';
                                        html += '<span class="input-group-addon">';
                                            if (adminController) {
                                                html += '<i class="icon-calendar"></i>';
                                            } else {
                                                html += '<i class="material-icons">&#xE8A3;</i>';
                                            }
                                        html += '</span>';
                                    html += '</div>';
                                html += '</td>';
                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input autocomplete="off" class="form-control sloting_date_to" type="text" name="sloting_date_to[]" value="" readonly>';
                                        html += '<span class="input-group-addon">';
                                            if (adminController) {
                                                html += '<i class="icon-calendar"></i>';
                                            } else {
                                                html += '<i class="material-icons">&#xE8A3;</i>';
                                            }
                                        html += '</span>';
                                    html += '</div>';
                                html += '</td>';
                            html += '</tr>';
                        html += '</tbody>';
                    html += '</table>';
                html += '</div>';
                html += '<div  class="form-group table-responsive time_slots_prices_table_div">  ';
                    html += '<table class="table time_slots_prices_table">';
                        html += '<thead>';
                            html += '<tr>';
                                html += '<th class="center">';
                                    html += '<span>'+'Plage horaire à partir de'+'</span>';
                                html += '</th>';
                                html += '<th class="center">';
                                    html += '<span>'+'À'+'</span>';
                                html += '</th>';
                                html += '<th class="center">';
                                    html += '<span>'+'Prix ​​(HT)'+'</span>';
                                html += '</th>';
                                html += '<th class="center">';
                                    html += '<span>' + 'Statut' + '</span>';
                                html += '</th>';
                            html += '</tr>';
                        html += '</thead>';
                        html += '<tbody>';
                            html += '<tr>';
                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input id="booking_time_from" autocomplete="off" class="booking_time_from form-control" type="text" name="booking_time_from'+date_ranges_length+'[]" value="" readonly>';
                                        html += '<span class="input-group-addon">';
                                            if (adminController) {
                                                html += '<i class="icon-clock-o"></i>';
                                            } else {
                                                html += '<i class="material-icons">&#xE192;</i>';
                                            }
                                        html += '</span>';
                                    html += '</div>';
                                html += '</td>';
                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to'+date_ranges_length+'[]" value="" readonly>';
                                        html += '<span class="input-group-addon">';
                                            if (adminController) {
                                                html += '<i class="icon-clock-o"></i>';
                                            } else {
                                                html += '<i class="material-icons">&#xE192;</i>';
                                            }
                                        html += '</span>';
                                    html += '</div>';
                                html += '</td>';
                                html += '<td class="center">';
                                    html += '<div class="input-group">';
                                        html += '<input type="text" class="form-control" name="slot_range_price'+date_ranges_length+'[]" value="'+Math.round($('#price').val())+'">';
                                        html += '<span class="input-group-addon">'+defaultCurrencySign+'</span>';
                                        html += '</div>';
                                html += '</td>';
                                html += '<td class="center">';
                                    html += '<center>';
                                        html += '<a class="slot_status_div btn">';
                                            html += '<input type="hidden" value="1" name="slot_active' + date_ranges_length + '[]" class="time_slot_status">';
                                            html += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-check.png" class="slot_active_img">';
                                            html += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-close.png" style="display:none;" class="slot_deactive_img">';
                                        html += '</a>';
                                html += '</td>';
                                html += '<td class="center">';
                                    html += '<a href="#" class="remove_date_ranges btn btn-default">';
                                    if (adminController) {
                                        html += '<i class="icon-trash"></i>';
                                    } else {
                                        html += '<i class="material-icons">&#xE872;</i>';
                                    }
                                    html += '</a>';
                                html += '</td>';
                            html += '</tr>';
                        html += '</tbody>';
                    html += '</table>';
                    html += '<div class="form-group">';
                        html += '<div class="col-lg-12">';
                            html += '<button class="btn btn-success btn-sm add_more_time_slot_price pull-right" type="button">';
                            if (adminController) {
                                html += '<i class="icon-plus-circle"></i>';
                            } else {
                                html += '<i class="material-icons">add_circle_outline</i>';
                            }
                            html += '&nbsp;'+add_more_slots_txt;
                            html += '</button>';
                        html += '</div>';
                    html += '</div>';
                html += '</div>';
            html += '</div>';
        }
        $('.time_slots_prices_content').append(html);
    });

    //time slots row append
    $(document).on('click', '.add_more_time_slot_price', function() {
        var date_ranges_length = $(this).closest('.single_date_range_slots_container').attr('date_range_slot_num');
        html = '<tr>';
            html += '<td class="center">';
                html += '<div class="input-group">';
                    html += '<input autocomplete="off" class="form-control booking_time_from" type="text" name="booking_time_from'+date_ranges_length+'[]" readonly>';
                    html += '<span class="input-group-addon">';
                    if (adminController) {
                        html += '<i class="icon-clock-o"></i>';
                    } else {
                        html += '<i class="material-icons">&#xE192;</i>';
                    }
                    html += '</span>';
                html += '</div>';
            html += '</td>';
            html += '<td class="center">';
                html += '<div class="input-group">';
                    html += '<input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to'+date_ranges_length+'[]" readonly>';
                    html += '<span class="input-group-addon">';
                        if (adminController) {
                            html += '<i class="icon-clock-o"></i>';
                        } else {
                            html += '<i class="material-icons">&#xE192;</i>';
                        }
                    html += '</span>';
                html += '</div>';
            html += '</td>';
            html += '<td class="center">';
                html += '<div class="input-group">';
                    html += '<input type="text" class="form-control" name="slot_range_price'+date_ranges_length+'[]" value="'+Math.round($('#price').val())+'">';
                    html += '<span class="input-group-addon">'+defaultCurrencySign+'</span>';
                html += '</div>';
            html += '</td>';
            html += '<td class="center">';
                html += '<center>';
                    html += '<a class="slot_status_div btn">';
                        html += '<input type="hidden" value="1" name="slot_active' + date_ranges_length + '[]" class="time_slot_status">';
                        html += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-check.png" class="slot_active_img">';
                        html += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-close.png" style="display:none;" class="slot_deactive_img">';
                    html += '</a>';
            html += '</td>';
            html += '<td class="center">';
                html += '<a href="#" class="remove_time_slot btn btn-default">';
                if (adminController) {
                    html += '<i class="icon-trash"></i>';
                } else {
                    html += '<i class="material-icons">&#xE872;</i>';
                }
                html += '</a>';
            html += '</td>';
        html += '</tr>';

        $(this).closest('.time_slots_prices_table_div').find('.time_slots_prices_table').append(html);
    });

    //To remove a row created with add new time slots buttons
    $(document).on('click', '.remove_time_slot', function(e) {
        e.preventDefault();
        if ($(this).closest('.time_slots_prices_table').find('.remove_time_slot').length == 1) {
            $(this).closest('.single_date_range_slots_container').remove();
        } else {
            $(this).closest('tr').remove();
        }
    });

    //To remove a row created with add new date ranges buttons
    $(document).on('click','.remove_date_ranges',function(e) {
        e.preventDefault();
        $(this).closest('.single_date_range_slots_container').remove();
    });

    //date picker for time slot datepicker
    $(document).on("focus", ".sloting_date_from, .sloting_date_to", function() {
        $(".sloting_date_from").datepicker({
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            minDate: 0,
        });
        $(".sloting_date_to").datepicker({
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            minDate: 0,
        });
    });

    //time picker for time slots
    $(document).on("focus", ".booking_time_from, .booking_time_to", function() {
        $(".booking_time_from, .booking_time_to").timepicker({
            pickDate: false,
            datepicker: false,
            format: 'H:i',
        });
    });

    // Js for availability and rated info Tab

    // Stats calendar tab js starts from here

    $('#stats-calendar').datepicker({
        defaultDate: (typeof calendarDate != 'undefined') ? calendarDate : new Date(),
        dayNamesMin: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        numberOfMonths: 2,
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
            dateToWork = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
            var calendarCssClass = '';
            var flag = 0;

            if (typeof disabledDays != 'undefined' && disabledDays) {
                var currentDay = date.getDay();
                if ($.inArray(String(currentDay), disabledDays) != -1) {
                    calendarCssClass += 'calender-disabled-dates ';
                }
            }
            if (typeof disabledDates != 'undefined' && disabledDates) {
                if ($.inArray(dateToWork, disabledDates) !== -1) {
                    calendarCssClass += 'calender-disabled-dates ';
                }
            }
            if (typeof bookingCalendarData != 'undefined') {
                $.each(bookingCalendarData, function(key, value) {
                    if (key === dateToWork) {
                        if (typeof value.calendarCssClass != 'undefined') {
                            calendarCssClass += ' ' + value.calendarCssClass + ' ' + key + ' ' + 'ui-datepicker-unselectable';
                            flag = 1;
                        }
                        return 1;
                    }
                });
            }
            if (flag) {
                return [true, calendarCssClass];
            } else {
                return [true, 'ui-datepicker-unselectable'];
            }
        },
    });

    // add popover information and rates on the dates <td>
    if (typeof bookingCalendarData != 'undefined') {
        $.each(bookingCalendarData, function(key, dateInfo) {
            var dateBookingInfo = dateInfo.booking_info;
            if ((typeof dateInfo.booking_type != 'undefined') && dateInfo.booking_type == booking_type_date_range) {
                if (typeof dateBookingInfo.price.total_price_tax_incl_formatted != 'undefined') {
                    $('body td.' + key).append('</br><span class="ui-datepicker-day-price">' + dateBookingInfo.price.total_price_tax_incl_formatted + '</span>');
                    $('body td.' + key).addClass('date_ranges_info_td');
                    $('body td.' + key + ' .ui-state-default').attr('data-toggle', 'popover');
                    $('body td.' + key + ' .ui-state-default').attr('data-placement', 'top');
                    $('body td.' + key + ' .ui-state-default').attr('data-html', true);
                    if (typeof dateBookingInfo.price != 'undefined') {
                        toolTipMsg = 'Total Available Qty: ' + dateBookingInfo.available_qty + '</br>Total Booked Qty: ' + dateBookingInfo.booked_qty + '</br>Price : ' + dateBookingInfo.price.total_price_tax_incl_formatted;
                    } else {
                        toolTipMsg = no_info_found_txt;
                    }
                    $('body td.' + key + ' .ui-state-default').attr('data-content', toolTipMsg);
                }
            } else if ((typeof dateInfo.booking_type != 'undefined') && dateInfo.booking_type == booking_type_time_slot) {
                $('body td.' + key).addClass('time_slots_info_td');
                $('body td.' + key + ' .ui-state-default').attr('data-toggle', 'popover');
                $('body td.' + key + ' .ui-state-default').attr('data-placement', 'top');
                $('body td.' + key + ' .ui-state-default').attr('data-html', true);
                var slotHtml = '';
                slotHtml += '<div class="table-responsive">';
                    slotHtml += '<table class="table">';
                        slotHtml += '<thead>';
                            slotHtml += '<th>' + slot_text + '</th>';
                            slotHtml += '<th>' + avl_qty_txt + '</th>';
                            slotHtml += '<th>' + price_txt + '</th>';
                            slotHtml += '<th>' + booked_qty_txt + '</th>';
                            slotHtml += '<th>' + status_txt + '</th>';
                        slotHtml += '</thead>';
                        if (typeof dateBookingInfo != 'undefined') {
                            if (dateBookingInfo.length) {
                                slotHtml += '<tbody>';
                                    $.each(dateBookingInfo, function(keySlot, slotInfo) {
                                        slotHtml += '<tr>';
                                            slotHtml += '<td>' + slotInfo.time_slot_from + ' ' + to_txt + ' ' + slotInfo.time_slot_to + '</td>';
                                            slotHtml += '<td>' + slotInfo.available_qty + '</td>';
                                            slotHtml += '<td>' + slotInfo.price_formatted + '</td>';
                                            slotHtml += '<td>' + slotInfo.booked_qty + '</td>';
                                            slotHtml += '<td>';
                                            if (slotInfo.active == 1) {
                                                slotHtml += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-check.png">';
                                            } else {
                                                slotHtml += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-close.png">';
                                            }
                                            slotHtml += '</td>';
                                        slotHtml += '</tr>';
                                    });
                                slotHtml += '</tbody>';
                            } else {
                                slotHtml += '<tr>';
                                    slotHtml += '<td colspan="4">' + no_slots_avail_txt + '</td>';
                                slotHtml += '</tr>';
                            }
                        }
                    slotHtml += '</table>';
                slotHtml += '</div>';
                $('body td.' + key + ' .ui-state-default').attr('data-content', slotHtml);
            }
        });
    }
    // When page will be loaded changes on calendar td cell content
    if (adminController) {
        var circle_icon = '<i class="icon-circle"></i>';
    } else {
        var circle_icon = '<i class="material-icons icon-circle">&#xE061;</i>';
    }
    $('#stats-calendar .booking_available .ui-state-default, #stats-calendar .booking_unavailable .ui-state-default').append('&nbsp;'+circle_icon);

    //If seller will change the month in the booking information calendar
    $(document).on('click', '.calendar_change_month_link', function() {
        if (adminController) {
            var circle_icon = '<i class="icon-circle"></i>';
        } else {
            var circle_icon = '<i class="material-icons icon-circle">&#xE061;</i>';
        }
        $('#stats-calendar .booking_available, #stats-calendar .booking_unavailable').append('&nbsp;'+circle_icon);
        if (typeof bookingCalendarData != 'undefined') {
            $.each(bookingCalendarData, function(key, dateInfo) {
                var dateBookingInfo = dateInfo.booking_info;
                if (typeof dateInfo.booking_type != 'undefined' && dateInfo.booking_type == booking_type_date_range) {
                    if (typeof dateBookingInfo.price.total_price_tax_incl_formatted != 'undefined') {
                        $('body td.' + key).append('</br><span class="ui-datepicker-day-price">' + dateBookingInfo.price.total_price_tax_incl_formatted + '</span>');
                        $('body td.' + key + ' .ui-state-default').attr('data-toggle', 'popover');
                        $('body td.' + key + ' .ui-state-default').attr('data-placement', 'top');
                        $('body td.' + key + ' .ui-state-default').attr('data-html', true);
                        if (typeof dateBookingInfo.price != 'undefined') {
                            toolTipMsg = 'Total Available Qty: ' + dateBookingInfo.available_qty + '</br>Total Booked Qty: ' + dateBookingInfo.booked_qty + '</br>Price : ' + dateBookingInfo.price.total_price_tax_incl_formatted;
                        } else {
                            toolTipMsg = no_info_found_txt;
                        }
                        $('body td.' + key + ' .ui-state-default').attr('data-content', toolTipMsg);
                    }
                } else if ((typeof dateInfo.booking_type != 'undefined') && dateInfo.booking_type == booking_type_time_slot) {
                    $('body td.' + key).addClass('time_slots_info_td');
                    $('body td.' + key + ' .ui-state-default').attr('data-toggle', 'popover');
                    $('body td.' + key + ' .ui-state-default').attr('data-placement', 'top');
                    $('body td.' + key + ' .ui-state-default').attr('data-html', true);
                    if (typeof dateBookingInfo != 'undefined') {
                        if (dateBookingInfo.length) {
                            var slotHtml = '';
                            slotHtml += '<div class="table-responsive">';
                            slotHtml += '<table class="table">';
                            slotHtml += '<thead>';
                            slotHtml += '<th>' + slot_text + '</th>';
                            slotHtml += '<th>' + avl_qty_txt + '</th>';
                            slotHtml += '<th>' + price_txt + '</th>';
                            slotHtml += '<th>' + booked_qty_txt + '</th>';
                            slotHtml += '<th>' + status_txt + '</th>';
                            slotHtml += '</thead>';
                            slotHtml += '<tbody>';
                            $.each(dateBookingInfo, function(keySlot, slotInfo) {
                                slotHtml += '<tr>';
                                slotHtml += '<td>' + slotInfo.time_slot_from + ' ' + to_txt + ' ' + slotInfo.time_slot_to + '</td>';
                                slotHtml += '<td>' + slotInfo.available_qty + '</td>';
                                slotHtml += '<td>' + slotInfo.price_formatted + '</td>';
                                slotHtml += '<td>' + slotInfo.booked_qty + '</td>';
                                slotHtml += '<td>';
                                if (slotInfo.active == 1) {
                                    slotHtml += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-check.png">';
                                } else {
                                    slotHtml += '<img src="' + module_dir + 'mpbooking/views/img/icon/icon-close.png">';
                                }
                                slotHtml += '</td>';
                                slotHtml += '</tr>';
                            });
                            slotHtml += '</tbody>';
                            slotHtml += '</table>';
                            slotHtml += '</div>';
                        } else {
                            slotHtml = no_slots_avail_txt;
                        }
                        $('body td.' + key + ' .ui-state-default').attr('data-content', slotHtml);
                    }
                }
            });
        }
        // To add our class on changing links of months to seperate from other datepickers
        $('[data-toggle="popover"]').popover();
        $('#stats-calendar-info .ui-datepicker-next, #stats-calendar-info .ui-datepicker-prev').addClass('calendar_change_month_link');
    });

    // To remove the other popovers before opening the new popover
    $('body, .ui-state-default').on('click', function() {
        $('.popover').remove();
    });

    // datepicker on search date from
    $("#search_date_from").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0,
        beforeShowDay: function(date) {
            return highlightDateBorder($("#search_date_from").val(), date);
        },
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
            selectedDate.setDate(selectedDate.getDate());
            $("#search_date_to").datepicker("option", "minDate", selectedDate);
        },
    });

    // datepicker on search date to
    $("#search_date_to").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: getDatePickerMinDate($("#search_date_from").val()),
        beforeShowDay: function(date) {
            return highlightDateBorder($("#search_date_to").val(), date);
        },
    });

    $('#availability-search-submit').on('click', function(e) {
        var dateFrom = $("#search_date_from").val();
        var dateTo = $("#search_date_to").val();
        var dateFromSplit = dateFrom.split("-");
        var dateFromFormatted = new Date($.datepicker.formatDate('yy-mm-dd', new Date(dateFromSplit[2], dateFromSplit[1] - 1, dateFromSplit[0])));
        var dateToSplit = dateTo.split("-");
        var checkOutFormatted = new Date($.datepicker.formatDate('yy-mm-dd', new Date(dateToSplit[2], dateToSplit[1] - 1, dateToSplit[0])));
        var error = false;
        $("#search_date_from").removeClass("error_border");
        $("#search_date_to").removeClass("error_border");
        $('#date_erros').text('');
        if (dateFrom == '') {
            $("#search_date_from").addClass("error_border");
            error = true;
        } else if (dateFromFormatted < $.datepicker.formatDate('yy-mm-dd', new Date())) {
            $("#search_date_from").addClass("error_border");
            $('#date_erros').text(date_from_less_current_date_err);
            error = true;
        }
        if (dateTo == '') {
            $("#search_date_to").addClass("error_border");
            error = true;
        } else if (checkOutFormatted < dateFromFormatted) {
            $("#search_date_to").addClass("error_border");
            $('#date_erros').text(date_to_more_date_from_err);
            error = true;
        }
        if (error) {
            return false;
        } else {
            return true;
        }
    });

    // Disable dates java script

    $("#date-start").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0,
        beforeShowDay: function(date) {
            return highlightDateBorder($("#date-start").val(), date);
        },
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
            selectedDate.setDate(selectedDate.getDate());
            $("#date-end").datepicker("option", "minDate", selectedDate);
        },
    });

    // datepicker on search date to
    $("#date-end").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: getDatePickerMinDate($("#date-start").val()),
        beforeShowDay: function(date) {
            return highlightDateBorder($("#date-end").val(), date);
        },
    });

    $('.is_disabled_week_days_exists').on('change', function() {
        if ($(this).val() == 1) {
            $('.disabled_week_days').show();
        } else if ($(this).val() == 0) {
            $('.disabled_week_days').hide();
        }
    });

    $('.is_disabled_specific_dates_exists').on('change', function() {
        if ($(this).val() == 1) {
            $('.disabled_specific_dates').show();
        } else if ($(this).val() == 0) {
            $('.disabled_specific_dates').hide();
        }
    });

    // add the disabled date ranges in the disable dates json
    $("button[name='submitDateRange']").on('click', function(e) {
        e.preventDefault();
        var id_booking_product_info = $('#id_booking_product_info').val();
        var $dateFrom = $('#date-start').val();
        var $dateTo = $('#date-end').val();
        var disabledDates = $('#disabled_specific_dates_json').val();
        error = false;
        if ($dateFrom == '') {
            showErrorMessage(date_from_req);
            error = true;
        }
        if ($dateTo == '') {
            showErrorMessage(date_to_req);
            error = true;
        }

        if (error) {
            return false;
        }
        if (disabledDates == '') {
            disabledDates = {};
        } else {
            disabledDates = JSON.parse(disabledDates);
        }
        // If booking id slot tyle bookings
        if (booking_type == booking_type_time_slot) {
            if ($dateFrom.trim() && $dateTo.trim()) {
                if (disabledDates[$dateFrom + '_' + $dateTo] === undefined) {
                    $.ajax({
                        url: path_sellerproduct,
                        data: {
                            id_booking_product_info: id_booking_product_info,
                            date_from: $dateFrom,
                            date_to: $dateTo,
                            action: 'getDateRangeAvailableBookingSlots',
                            ajax: true,
                        },
                        method: 'POST',
                        dataType: 'JSON',
                        success: function(result) {
                            $('.booking-disable-slots-content').empty();
                            if (result.status == 'failed') {
                                $.each(result.errors, function(key, error) {
                                    showErrorMessage(error);
                                });
                            } else if (result.status == 'success') {
                                if (result.slots == 'no_slot') {
                                    $('.disableSlotsModalSubmit').hide();
                                    $('.booking-disable-slots-content').append('<div class="alert alert-danger">' + no_slots_avail_txt + '</div>');
                                } else if (result.slots == 'all') {
                                    $('.disableSlotsModalSubmit').show();
                                    $('.booking-disable-slots').hide();
                                    $('.booking-disable-slots-content').attr('date_from', $dateFrom);
                                    $('.booking-disable-slots-content').attr('date_to', $dateTo);
                                    $('.booking-disable-slots-content').attr('all_slots', 1);
                                    $('.booking-disable-slots-content').append('<div class="alert alert-warning">' + all_slots_disable_warning + '</div>');
                                } else {
                                    $('.disableSlotsModalSubmit').show();
                                    $('.booking-disable-slots-content').attr('date_from', $dateFrom);
                                    $('.booking-disable-slots-content').attr('date_to', $dateTo);
                                    $('.booking-disable-slots-content').attr('all_slots', 0);
                                    var html = '<div class="from-group table-responsive-row clearfix">';
                                        html += '<table class="table booking-disable-slots">';
                                            html += '<tbody>';
                                            $.each(result.slots, function(key, slot) {
                                                html += '<tr>';
                                                    html += '<td>' + slot.time_slot_from + ' &nbsp;' + to_txt + ' &nbsp;' + slot.time_slot_to + '</td>';
                                                    html += '<td><input time_from="' + slot.time_slot_from + '" time_to="' + slot.time_slot_to + '" id_slot="' + slot.id + '" type="checkbox" class="selected_disable_slots"></td>';
                                                html += '</tr>';
                                            });
                                            html += '</tbody>';
                                        html += '</table>';
                                    html += '</div>';
                                    $('.booking-disable-slots-content').append(html);
                                }
                                $('#disableTimeSlotsModal').modal('show');
                            }
                        }
                    });
                } else {
                    showErrorMessage(date_range_already_added);
                }
            }
        } else { // If booking id date range type bookings
            if ($dateFrom.trim() && $dateTo.trim()) {
                if (disabledDates[$dateFrom + '_' + $dateTo] === undefined) {
                    var dateRangeObj = {
                        'date_from': $dateFrom,
                        'date_to': $dateTo,
                    };
                    disabledDates[$dateFrom + '_' + $dateTo] = dateRangeObj;
                    if (disabledDates) {
                        $('#disabled_specific_dates_json').val(JSON.stringify(disabledDates));
                        if (adminController) {
                            var html = '<div class="col-sm-3">';
                        } else {
                            var html = '<div class="col-sm-4">';
                        }
                        html += '<div class="disabled_date_container">';
                        if (adminController) {
                            var cross_circle_icon = '<i class="icon-times-circle"></i>';
                        } else {
                            var cross_circle_icon = '<i class="material-icons">&#xE888;</i>';
                        }
                        html += '<span>' + $dateFrom + '&nbsp; To &nbsp;' + $dateTo + '</span><span class="remove_disable_date" remove-date-index="' + ($dateFrom + '_' + $dateTo) + '">'+cross_circle_icon+'</span>';
                        html += '</div>';
                        html += '</div>';
                        $('.selected_disabled_dates').append(html);
                    }
                } else {
                    showErrorMessage(date_range_already_added);
                }
            }
            return false;
        }
    });

    $(document).on('click', '.edit_disable_date_slots', function(e) {
        e.preventDefault();
        var id_booking_product_info = $('#id_booking_product_info').val();
        var $dateFrom = $(this).attr('date_start');
        var $dateTo = $(this).attr('date_end');
        error = false;
        if ($dateFrom == '') {
            showErrorMessage(date_from_req);
            error = true;
        }
        if ($dateTo == '') {
            showErrorMessage(date_to_req);
            error = true;
        }

        if (error) {
            return false;
        } else {
            if ($dateFrom.trim() && $dateTo.trim()) {
                $.ajax({
                    url: path_sellerproduct,
                    data: {
                        id_booking_product_info: id_booking_product_info,
                        date_from: $dateFrom,
                        date_to: $dateTo,
                        action: 'getDateRangeAvailableBookingSlots',
                        ajax: true,
                    },
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(result) {
                        $('.booking-disable-slots-content').empty();
                        if (result.status == 'failed') {
                            $.each(result.errors, function(key, error) {
                                showErrorMessage(error);
                            });
                        } else if (result.status == 'success') {
                            if (result.slots == 'all') {
                                $('.booking-disable-slots').hide();
                                $('.booking-disable-slots-content').attr('date_from', $dateFrom);
                                $('.booking-disable-slots-content').attr('date_to', $dateTo);
                                $('.booking-disable-slots-content').attr('all_slots', 1);
                                $('.booking-disable-slots-content').append('<div class="alert alert-warning">' + all_slots_disable_warning + '</div>');
                                $('#disableTimeSlotsModal').modal('show');
                            } else {
                                var disabledDates = $('#disabled_specific_dates_json').val();
                                if (disabledDates == '') {
                                    disabledDates = {};
                                } else {
                                    disabledDates = JSON.parse(disabledDates);
                                }
                                $('.booking-disable-slots-content').attr('date_from', $dateFrom);
                                $('.booking-disable-slots-content').attr('date_to', $dateTo);
                                $('.booking-disable-slots-content').attr('all_slots', 0);
                                var html = '<div class="from-group table-responsive-row clearfix">';
                                    html += '<table class="table booking-disable-slots">';
                                        html += '<tbody>';
                                        $.each(result.slots, function(key_ajax_slots, ajax_slot) {
                                            html += '<tr>';
                                                html += '<td>' + ajax_slot.time_slot_from + ' &nbsp;' + to_txt + ' &nbsp;' + ajax_slot.time_slot_to + '</td>';
                                                html += '<td><input time_from="' + ajax_slot.time_slot_from + '" time_to="' + ajax_slot.time_slot_to + '" id_slot="' + ajax_slot.id + '" type="checkbox" class="selected_disable_slots"';
                                                $.each(disabledDates, function(key_disables_dates, disableRange) {
                                                    if (key_disables_dates == $dateFrom + '_' + $dateTo) {
                                                        $.each(disableRange.slots_info, function(key_slot_info, slot_info) {
                                                            if (slot_info.time_from == ajax_slot.time_slot_from && slot_info.time_to == ajax_slot.time_slot_to) {
                                                                html += ' checked="checked"';
                                                            }
                                                        });
                                                    }
                                                });
                                                html += '></td>';
                                            html += '</tr>';
                                        });
                                        html += '</tbody>';
                                    html += '</table>';
                                html += '</div>';
                                $('.booking-disable-slots-content').append(html);
                                $('#disableTimeSlotsModal').modal('show');
                            }
                        }
                    }
                });
            }
        }
    });

    // Disable dates data save when model open
    $(document).on('click', '.disableSlotsModalSubmit', function() {
        var dateFrom = $('.booking-disable-slots-content').attr('date_from');
        var dateTo = $('.booking-disable-slots-content').attr('date_to');
        var allSlots = $('.booking-disable-slots-content').attr('all_slots');
        error = false;
        if (typeof dateFrom == 'undefined' || dateFrom == '') {
            showErrorMessage(date_from_req);
            error = true;
        }
        if (typeof dateTo == 'undefined' || dateTo == '') {
            showErrorMessage(date_to_req);
            error = true;
        }
        if (allSlots == 0) {
            if ($('.selected_disable_slots:checked').length == 0) {
                showErrorMessage(no_slot_selected_err);
                error = true;
            }
        }
        if (error) {
            return false;
        }
        var slotInfo = new Array();
        $('.selected_disable_slots:checked').each(function(key, slot) {
            slotInfo.push({
                'time_from': $(this).attr('time_from'),
                'time_to': $(this).attr('time_to')
            });
        });

        var disabledDates = $('#disabled_specific_dates_json').val();
        if (disabledDates == '') {
            disabledDates = {};
        } else {
            disabledDates = JSON.parse(disabledDates);
        }
        if (dateFrom.trim() && dateTo.trim()) {
            var dateRangeSlotsObj = {
                'date_from': dateFrom,
                'date_to': dateTo,
                'slots_info': slotInfo,
            };
            if (disabledDates[dateFrom + '_' + dateTo] === undefined) {
                disabledDates[dateFrom + '_' + dateTo] = dateRangeSlotsObj;
                if (disabledDates) {
                    $('#disabled_specific_dates_json').val(JSON.stringify(disabledDates));
                    if (adminController) {
                        var html = '<div class="col-sm-3">';
                    } else {
                        var html = '<div class="col-sm-4">';
                    }
                        html += '<div class="disabled_date_container">';
                            if (adminController) {
                                var cross_circle_icon = '<i class="icon-times-circle"></i>';
                                var edit_icon = '<i class="icon-pencil"></i>';
                            } else {
                                var cross_circle_icon = '<i class="material-icons">&#xE888;</i>';
                                var edit_icon = '<i class="material-icons">&#xE254;</i>';
                            }
                            html += '<span>' + dateFrom + '&nbsp; '+to_txt+' &nbsp;' + dateTo + '</span>';
                            html += '<span class="remove_disable_date" remove-date-index="' + (dateFrom + '_' + dateTo) + '">'+cross_circle_icon+'</span>';
                            html += '<span date_end="' + dateTo + '" date_start="' + dateFrom + '" class="edit_disable_date_slots">'+edit_icon+'</span>';
                        html += '</div>';
                    html += '</div>';
                    console.log(html);
                    $('.selected_disabled_dates').append(html);
                }
            } else {
                disabledDates[dateFrom + '_' + dateTo] = dateRangeSlotsObj;
                $('#disabled_specific_dates_json').val(JSON.stringify(disabledDates));
            }
        }
        $('#disableTimeSlotsModal').modal('hide');
    });

    //delete the dateranges from the disables date ranges json
    $(document).on('click', '.remove_disable_date', function() {
        var indexToRemove = $(this).attr('remove-date-index');
        var disableDatesArray = JSON.parse($('#disabled_specific_dates_json').val());
        delete disableDatesArray[indexToRemove];
        $('#disabled_specific_dates_json').val(JSON.stringify(disableDatesArray));
        $(this).closest('.disabled_date_container').parent('div').remove();
    });

    // To make calendar dates disabled
    $('#date-start').attr('readonly', true);
    $('#date-end').attr('readonly', true);

});

// To initialize the popover
$(function() {
    $('[data-toggle="popover"]').popover();
    // To add our class on changing links of months to seperate from other datepickers
    $('#stats-calendar-info .ui-datepicker-next, #stats-calendar-info .ui-datepicker-prev').addClass('calendar_change_month_link');
});

function changeTabStatus(active_tab) {
    //Remove all tabs from active (make normal)
    if (adminController) {
        $('.wk-tabs-panel .nav-tabs li').removeClass('active');
        $('[href*="#' + active_tab + '"]').parent('li').addClass('active');
    } else {
        $('.wk-tabs-panel .nav-tabs li>a').removeClass('active');
        $('[href*="#' + active_tab + '"]').addClass('active');
    }
    $('.wk-tabs-panel .tab-content .tab-pane').removeClass('active');
    //Add active class in selected tab
    $('#' + active_tab).addClass('active in');
}
function changeMultilangFormLanguage(id_lang, select_lang_name)
{
    $('#lang_select_btn').html(select_lang_name + ' <span class="caret"></span>');
    $('.wk_booking_text_field_all').hide();
    $('.wk_booking_text_field_' + id_lang).show();
    $('#choosedLangId').val(id_lang);
    $('.all_lang_icon').attr('src', img_dir_l+id_lang+'.jpg');
}
