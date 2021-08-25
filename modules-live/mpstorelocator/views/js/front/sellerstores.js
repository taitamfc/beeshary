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
    function googleStoreLocator(stores) {
        if (!Array.isArray(stores)) {
            var storeLocations = [];
            var setZoom = 1;
            storeLocations.push(stores);
        } else {
            var storeLocations = stores;
            var setZoom = 0;
        }

        var map;
        var bounds = new google.maps.LatLngBounds();
        // Display a map on the page
        map = new google.maps.Map(document.getElementById("map-canvas"));
        google.maps.event.trigger(map, 'resize');
        map.setTilt(45);

        // Display multiple markers on a map
        var infoWindow = new google.maps.InfoWindow();
        var marker;
        var i;

        $.each(storeLocations, function(i, location) {
            var position = new google.maps.LatLng(location.latitude, location.longitude);
            bounds.extend(position);
            marker = new google.maps.Marker({
                position: position,
                map: map,
                animation: google.maps.Animation.DROP,
            });

            // Allow each marker to have an info window    
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infoWindow.setContent(location.map_address);
                    infoWindow.open(map, marker);
                }
            })(marker, i));

            // If Focus on particular store
            if (setZoom) {
                infoWindow.setContent(location.map_address);
                infoWindow.open(map, marker);
            }

            // Automatically center the map fitting all markers on the screen
            map.fitBounds(bounds);
        });

        // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
        var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
            // If Focus on particular store
            if (setZoom) {
                this.setZoom(17);
            }
            google.maps.event.removeListener(boundsListener);
        });
    }

    // On page load display all stores
    if (typeof storeLocationsJson != 'undefined') {
        storeLocationsJson = JSON.parse(storeLocationsJson);
        googleStoreLocator(storeLocationsJson);
    } else {
        var html_var = '<center><h2>' + no_store_msg + '</h2></center>';
        $('#wrapper_content_left').html(html_var);
    }

    /* when clicking on any store*/
    $(document).on("click", ".wk_store", function() {
        var id = $(this).attr('id');
        var address = $(this).attr('addr');
        var lat = $(this).attr('lat');
        var lng = $(this).attr('lng');

        var store = {
            latitude: lat,
            longitude: lng,
            map_address: address
        };
        googleStoreLocator(store);
    });

    // while filter by city
    $(document).on("click", "#go_btn", function() {
        var key = $("#search_city").val();
        if (key != '') {
            $.ajax({
                url: url_getstorebykey,
                data: {
                    search_key: key,
                    edit_store: "1"
                },
                success: function(result) {
                    if (result) {
                        result = JSON.parse(result);
                        $("#wrapper_content_left").html(result.allStoreTpl);
                        googleStoreLocator(result.allstore);
                    } else {
                        $('#map-canvas').empty();
                        $("#wrapper_content_left").html('<center><h2>' + no_store_msg + '</h2></center>');
                    }
                }
            });
        }
    });

    // while filter by product name
    $(document).on("change", "#select_search_products", function() {
        var id_product = $(this).val();
        $.ajax({
            url: url_getstorebyproduct,
            data: {
                id_product: id_product,
                id_seller: id_seller,
                edit_store: "1"
            },
            success: function(result) {
                if (result) {
                    result = JSON.parse(result);
                    $("#wrapper_content_left").html(result.allStoreTpl);
                    googleStoreLocator(result.allstore);
                } else {
                    $('#map-canvas').empty();
                    $("#wrapper_content_left").html('<center><h2>' + no_store_msg + '</h2></center>');
                }
            }
        });
    });

    $(document).on("click", ".delete_store", function() {
        if (!confirm(are_you_sure))
            return false;
    });

    $(document).on("click", "#reset_btn", function() {
        location.reload(true);
    });

    $('#icon_id i').show();
});