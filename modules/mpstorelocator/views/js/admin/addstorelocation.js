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

$(document).ready(function() {
    $('.pick-time').timepicker({
        timeFormat: 'hh:mm:ss',
        stepHour: 1,
        stepMinute: 1,
    });

    $('.wk-tabs-panel .nav-link').on('click', function() {
        activeTab =$(this).attr('href');
        activeTab = activeTab.replace('#', "");
        $('#active_tab').val(activeTab);
    });

    $('#mp_store_products').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 200,
        width: 400,
        maxWidth: 400,
    });
    $('i.glyphicon.glyphicon-search').removeClass('glyphicon glyphicon-search').addClass('material-icons').html('search');
    $('i.glyphicon.glyphicon-remove-circle').removeClass('glyphicon glyphicon-remove-circle').addClass('material-icons').html('cancel');

    $('input[name="store_pickup_available"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('#wk_store_pickup_time_slot'));
    });

    $("#state").closest('.form-group').hide();

    toggleMarkerElement($('input[name="store_pickup_available"]:checked').val(), $('#wk_store_pickup_time_slot'));

    function toggleMarkerElement(value, element) {
        if(value == 1) {
            element.show();
        } else {
            element.hide();
        }
    }

    $(document).on("click", ".store_opening_days", function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    // form validation
    $('#submit_store, #submit_and_stay_store').on('click', function(e) {
        var seller_name = $('#seller_name').val();
        var shop_name = $('#shop_name').val();
        var street = $('#address1').val();
        var city_name = $('#city_name').val().trim();
        var countries = $('#countries').val();
        var zip_code = $('#zip_code').val();
        var latitude = $('#latitude').val();
        var special_char = /^[^<>;=#{}]*$/;
        if (seller_name == '' || seller_name == 0) {
            $.growl.error({ title: "", message: req_seller_name});
            $('#seller_name').focus();
            return false;
        } else if (shop_name == '') {
            $.growl.error({ title: "", message: req_shop_name});
            $('#shop_name').focus();
            return false;
        } else if (!isNaN(shop_name) || !special_char.test(shop_name)) {
            $.growl.error({ title: "", message: inv_shop_name});
            $('#shop_name').focus();
            return false;
        } else if (address1 == '') {
            $.growl.error({ title: "", message: req_street});
            $('#address1').focus();
            return false;
        } else if (city_name == '') {
            $.growl.error({ title: "", message: req_city_name});
            $('#city_name').focus();
            return false;
        } else if (!isNaN(city_name) || !special_char.test(city_name)) {
            $.growl.error({ title: "", message: inv_city_name});
            $('#city_name').focus();
            return false;
        } else if (countries == '') {
            $.growl.error({ title: "", message: req_countries});
            $('#countries').focus();
            return false;
        } else if (zip_code == '') {
            $.growl.error({ title: "", message: req_zip_code});
            $('#product_quantity').focus();
            return false;
        } else if (zip_code.length > 12) {
            $.growl.error({ title: "", message: inv_zip_code});
            $('#zip_code').focus();
            return false;
        } else if (latitude == '') {
            $.growl.error({ title: "", message: req_latitude});
            return false;
        }
        $("#submit_store").on("click", function() {
            $("#submit_store").prop("disabled", true);
        });
    });

    $("#btn_store_search").on("click", function() {
        codeAddress();
    });
});

$(document).ready(function() {
    $(".delete_store_logo").on("click", function() {
        if (!confirm("Are you sure?")) {
            return false;
        }
    });
});

//---------------------Code for google map searching ---------------------------------

var geocoder;
var map;
var infowindow = new google.maps.InfoWindow();
var marker;

// map initialization on add store page
function initialize() {
    geocoder = new google.maps.Geocoder();
    if (typeof id_store != 'undefined') //if edit store seller store
    {
        var mapOptions = {
            center: new google.maps.LatLng(lat, lng),
            zoom: 17
        };
    } else {
        var mapOptions = {
            center: new google.maps.LatLng(-33.8688, 151.2195),
            zoom: 13
        };
    }

    var element = document.getElementById('map-canvas');
    if (element != null) {
        map = new google.maps.Map(element, mapOptions);

        var input = document.getElementById('pac-input');
        var types = document.getElementById('type-selector');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        if (typeof id_store != 'undefined') //if edit store seller store
        {
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29),
                position: mapOptions.center
            });
            infowindow.setContent(map_address);
            infowindow.open(map, marker);
        } else {
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29)
            });
        }

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
    
            if (!place.geometry) {
                return;
            }
    
            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17); // Why 17? Because it looks good.
            }
            marker.setIcon(({
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
            }));
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
    
            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }
    
            //get lat, lng and address from map
            $("#latitude").val(place.geometry.location.lat());
            $("#longitude").val(place.geometry.location.lng());
            $("#map_address").val('<div><strong>' + place.name + '</strong><br>' + address + '</div>');
            $("#map_address_text").val($("#pac-input").val());
    
            infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            infowindow.open(map, marker);
        });
    }
}

function codeAddress() {
    var address = document.getElementById('pac-input').value;
    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });

    infowindow.close();
    marker.setVisible(false);

    geocoder.geocode({
        'address': address
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var place = results[0];

            map.setCenter(place.geometry.location);
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            $("#latitude").val(place.geometry.location.lat());
            $("#longitude").val(place.geometry.location.lng());
            $("#map_address").val('<div>' + place.formatted_address + '</div>');
            $("#map_address_text").val($("#pac-input").val());

            infowindow.setContent(place.formatted_address);
            infowindow.open(map, marker);
        } else {
            $.growl.error({ title: "", message: 'Geocode was not successful for the following reason: ' + status});
        }
    });
}

google.maps.event.addDomListener(window, 'load', initialize);