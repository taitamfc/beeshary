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

var currentLocation = null;
var infoWindow, ajaxCheckVar = null;
var orderControllerStores = null;
var map = null;
$(document).ready(function() {
    $('#wk_store_loader').hide();
    if (autoLocate && typeof navigator.geolocation != 'undefined') {
        navigator.geolocation.getCurrentPosition(function (position) {
            currentLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            getPositionByLatLang(position.coords.latitude, position.coords.longitude)
            if (idProductLoad || idStoreLoad) {
                $('#wrapper_content').hide();
                $('#wk_store_loader').show();
                data = {
                    current_location: currentLocation,
                    action: 'getStoreDetails',
                    ajax: true
                };
                if (idProductLoad) {
                    data['id_product'] = idProductLoad;
                }
                if (idStoreLoad) {
                    data['id_store'] = idStoreLoad;
                }
				var pp_theme = $('#pp_theme').val();
                if (pp_theme) {
                    data['pp_theme'] = pp_theme;
                }

                $.ajax({
                    url: ajaxurlStoreByKey,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (result) {
                        $('#wk_store_loader').hide();
                        $('#wrapper_content').show();
                        if (result.hasError) {
                            //$('#map-canvas').empty();
                            $("#wrapper_content_left").html('<h2>' + no_store_found + '</h2>');
                            $('#wrapper_content_right').css({ height: '0px' });
                        } else {
                            $('#wrapper_content_right').css({ height: '500px' });
                            $("#wrapper_content_left").html(result.html);
                            $('#wk_store_direction').attr('href', $('#store_direction_1').attr('href'));
                            store = result.stores;
                            if (currentLocation != null && $('#wk_current_location').val() != undefined) {
                            } else {
                                $('#wk_store_radius').val('');
                                $('#wk_store_radius').attr('disabled', 'true');
                            }
                            googleStoreLocator(store, result.html);
                        }
                    }
                });
            } else {
                if (controller == 'order') {
                } else {
                    initialize();
                }
            }
        }, function () {
            $('#wk_store_radius').val('');
            $('#wk_store_radius').attr('disabled', 'true');
            if (controller == 'order') {
            } else {
                initialize();
            }
        });
    } else {
        $('#wk_store_radius').val('');
        $('#wk_store_radius').attr('disabled', 'true');
        if (controller == 'order') {
        } else {
            initialize();
        }
    }
    $(document).on('click', '.wkshow_hours', function () {
        if ($(this).html() == 'expand_more') {
            $(this).html('expand_less');
        } else {
            $(this).html('expand_more');
        }
        $(this).next().slideToggle();
    })

    if (typeof storeLocationsJson != 'undefined' && storeLocationsJson) {
        try {
            storeLocations = $.parseJSON(storeLocationsJson);
            googleStoreLocator(storeLocations);
        } catch(e) {
        }
    } else {
        var html_var = '<center><h2>' + no_store_msg + '</h2></center>';
        $('#wrapper_content_left').html(html_var);
    }

    $("#select_search_products").on("keyup", function () {
        var searchProductKey = $(this).val();
        if (searchProductKey == '') {
            $('#products_ul').html('');
            $('#divnoproduct').css('padding', '0px');
            $('#divnoproduct').html('');
        } else {
            abortRunningAjax();
            if (($(this).val()).length > 2) {
                ajaxCheckVar = $.ajax({
                    url: ajaxurlStoreByKey,
                    type: "POST",
                    data: {
                        search_key: searchProductKey,
                        action: 'searchProduct',
                        ajax: true
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#products_ul').html(result.html);
                        if (result.html != '') {
                            $('#divnoproduct').css('padding', '0px');
                            $('#divnoproduct').html('');
                            $('#products_ul').html(result.html);
                            $('#products_ul li:even').css('background-color', '#F1F9FF');
                            $('#products_ul li:odd').css('background-color', '#FFF');
                        } else {
                            $('#divnoproduct').css('padding', '3px');
                            $('#divnoproduct').html(result.html);
                            $('#products_ul').html('');
                        }
                    }
                });

            }
        }
    });

    $(document).on('click', '#products_ul li', function (e) {
        e.preventDefault();
        if (typeof $(this).attr('id_product') != 'undefined') {
            $('#select_search_products').val($(this).html());
            $('#select_search_products').attr('id_product', $(this).attr('id_product'));
            $('#products_ul').html('');
        }
    });
    $(document).on('click', function (e) {
        $('#products_ul').html('');
    });

    $("#wk_store_reset").on("click", function() {
        window.location.reload();
    });

    $('select[name="mpstorelocator-n"]').on('change', function (e) {
        e.preventDefault();
        $.ajax({
            url: ajaxurlStoreByKey,
            type: "POST",
            data: {
                ajax: true,
                action: 'getProducts',
                n: $(this).val(),
                id_store: $('input[name="id_store"]').val()
            },
            dataType: 'json',
            success: function (result) {
                $('#wk_store_products').html(result.html);
            }
        });
    });

    $("#wk_store_search").on("click", function (e) {
        e.preventDefault();

        var key = $("#search_city").val();
        var idProduct = $("#select_search_products").attr('id_product');
        if (key != '' || idProduct != '' || currentLocation != null || radius != undefined) {
            data = {
                search_key: key,
                id_product: idProduct,
                current_location: currentLocation,
                radius: $('#wk_store_radius').val(),
                action: 'getStoreDetails',
                ajax: true
            }
			
			var pp_theme = $('#pp_theme').val();
			if (pp_theme) {
				data.pp_theme = pp_theme;
			}
			
			if( $('#category_id').length > 0 ){
				data.category_id = $('#category_id').val();
			}
			if( $('#date_stay').length > 0 ){
				data.date_stay = $('#date_stay').val();
			}
			
            $.ajax({
                url: ajaxurlStoreByKey,
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (result) {
                    if (result.hasError) {
                        //$('#map-canvas').empty();
                        $("#wrapper_content_left").html('<h2>' + no_store_found + '</h2>');
                        $('#wrapper_content_right').css({ height: '0px' });
                    } else {
                        $('#wrapper_content_right').css({ height: '500px' });
                        $("#wrapper_content_left").html(result.html);
                        store = result.stores;
                        if (currentLocation != null && $('#wk_current_location').val() != undefined) {
                        } else {
                            $('#wk_store_radius').val('');
                            $('#wk_store_radius').attr('disabled', 'true');
                        }
                        googleStoreLocator(store, result.html);
                    }
                }
            });
        }
    });
});

function initialize() {
    if (typeof storeLocate != 'undefined') {
        /*show the first array lat and lag on the page when page load*/
        storeLocationsJson = JSON.parse(storeLocationsJson);
        googleStoreLocator(storeLocationsJson);
    } else {
        var html_var = '<h2>' + no_store_found + '</h2>';
        $('#wrapper_content_right').css({ height: '0px' });
        $('#wrapper_content_left').html(html_var);
    }
}

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

    if (document.getElementById("map-canvas") != null) {

		
		// var mapOptions = {
		// 	zoom: 7,
		// 	center: { lat: 43.7032932, lng: 7.1827765 },
		// };
        if (currentLocation != null) {
            
            var mapOptions = {
                zoom: 11,
                
                center:{ lat: currentLocation.lat, lng: currentLocation.lng },         
                
            };
        }
        else {
            var mapOptions = {
            zoom: 7,
           
			center: { lat: 43.7032932, lng: 7.1827765 },
		};

        }


        map = new google.maps.Map( document.getElementById("map-canvas"),mapOptions );
		
        var storePage = document.getElementById('wk_store_details_page');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(storePage);

        var input = (document.getElementById('wk_current_location'));
        if (input != undefined) {
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);
        }
        if (input == undefined || input.value == '') {
            $('#wk_store_radius').val('');
            $('#wk_store_radius').attr('disabled', 'true');
        }
        google.maps.event.trigger(map, 'resize');
        map.setTilt(45);

        // Display multiple markers on a map
        infoWindow = new google.maps.InfoWindow();
        var marker;
        var i;
		var image = '/themes/beeshary/assets/img/ssIcon.png';
		var image = '/themes/beeshary/assets/img/ssIcon2.png';
        if (storeLocations != null || storeLocations != undefined) {
            markers = storeLocations.map(function (location, i) {
				if( location.latitude != '' && location.longitude != '' ){
					var position = new google.maps.LatLng(location.latitude, location.longitude);
					bounds.extend(position);
		
					markerData = {
						position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
						map: map,
						icon: image,
					}
					if (controller != 'order') {
						markerData['animation'] = google.maps.Animation.DROP;                
					}
					if (typeof storeConfiguration[location.id_seller] != 'undefined'
						&& storeConfiguration[location.id_seller]['enable_marker'] == 1
					) {
						iconCustomMarker = {
							url: location.markerIcon,
							scaledSize: new google.maps.Size(28.17, 40),
							size: new google.maps.Size(28.17, 40),
							origin: new google.maps.Point(0, 0),
							anchor: new google.maps.Point(14, 40)
						};
						//markerData['icon'] = iconCustomMarker;
					}
					marker = new google.maps.Marker(markerData);
					setInfoWindow(marker, i, location, infoWindow);
					return marker;
				}
                
            });
    
            // Automatically center the map fitting all markers on the screen
			//default city is nice, disable auto center
            //map.fitBounds(bounds);
    
            if (markers != undefined) {
                // Add a marker clusterer to manage the markers.
                if (displayCluster == 1 && controller != 'order') {
                    var markerCluster = new MarkerClusterer(
                        map,
                        markers,
                        { imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m' }
                    );
                }
            }
        }

        if (maxZoomLevelEnable != 0) {
            // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
            var zoomChangeBoundsListener = google.maps.event.addListener(map, 'bounds_changed', function (event) {
                if (map.getZoom() > maxZoomLevel) {
                    this.setZoom(parseInt(maxZoomLevel));
					console.log( parseInt(maxZoomLevel) );
                }
            });
            setTimeout(function () {
                google.maps.event.removeListener(zoomChangeBoundsListener)
            },
                500
            );
        }
    
        if (typeof autocomplete != 'undefined') {
            getUserLocation(autocomplete, map);
        } else {
            $('#wk_store_radius').val('');
            $('#wk_store_radius').attr('disabled', 'true');
        }

    }
}

function getUserLocation(autocomplete, map, store) {
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        var place = autocomplete.getPlace();

        if (!place.geometry) {
            return;
        }
        //get lat, lng and address from map
        currentLocation = { lat: place.geometry.location.lat(), lng: place.geometry.location.lng() };
        $('#wk_store_radius').removeAttr('disabled');

        if (controller == 'order') {
            $.each(orderControllerStores, function(index, store) {
                distance = -1;
                var directionsService = new google.maps.DirectionsService();
                
                var request = {
                    origin: new google.maps.LatLng(currentLocation.lat, currentLocation.lng),
                    destination: new google.maps.LatLng(store.latitude, store.longitude),
                    travelMode: google.maps.DirectionsTravelMode.DRIVING,
                };
                
                if (distanceType == 'METRIC') {
                    request['unitSystem'] = google.maps.UnitSystem.METRIC;
                } else {
                    request['unitSystem'] = google.maps.UnitSystem.IMPERIAL;
                }

                directionsService.route(request, function (response, status) {

                    if (status == google.maps.DirectionsStatus.OK) {
                        if (distanceType == 'METRIC') {
                            distance = ((response.routes[0].legs[0].distance.value)/1000).toFixed(2) + ' km'; // the distance in metres
                        } else {
                            distance = response.routes[0].legs[0].distance.text; // the distance in metres
                        }

                        // $(document).find('#wk_store_distance_'+store.id+' > span').html(distance);

                        orderControllerStores[index]['wk_store_distance'] = distance;
                        // $('#wk_store_distance_'+store.id).css({display: 'block'});;
                    }
                });
            });
            setTimeout(function () {
                googleStoreLocator(orderControllerStores);
            },
                500
            );
        } else {
            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
                map.setZoom(10);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(10); // Why 17? Because it looks good.
            }
        }
    });
}

function setInfoWindow(marker, markerIndex, storeLocations, infoWindow, distance = -1) {
	console.log(storeLocations);
	// console.log(storeLocations.img_home_default.url);
	// console.log(storeLocations.img_home_default['url']);
	
    google.maps.event.addListener(marker, 'click', (function (marker, i) {
		var btnGetDirections = "Voir l'activité";
        var html = '';
        html += '<div class="wk-store-hour">';
        html += '<table cellpadding="0">';
        html += '<tr><td style="vertical-align: top" class="img_map_container">';
		if( typeof storeLocations.mp_seller_info.profile_image != 'undefined' ){
			var profile_image_url = storeLocations.mp_seller_info.profile_image;
		}else{
			var profile_image_url = '/modules/marketplace/views/img/seller_img/default-artisan-pic.png';
		}
		
		if( storeLocations.id_product && false ){
			html += '<img style="padding-right: 15px" src="'+profile_image_url+'"';
			if (controller != 'order') {
				
				html += 'height="100px">';
			} else {
				html += 'height="70px">';
			}
			html += '</img>';
		}
        html += '<div class="img_map_container_div"> <img class="artisan_img_map" src="'+profile_image_url+'"';
			if (controller != 'order') {
				
				html += 'height="100px">';
			} else {
				html += 'height="70px">';
			}
			html += '</img></div>';
		
        html += '<td>' + '<a ';
        if (displayStorePage != 'undefined' && displayStorePage == 1) {
            html += 'href="' + storeLocations.storeLink + '"';
        }
        html += 'class="wkstore-name">' + storeLocations.name + '</a>';

        if (controller != 'order') {
            html += '<div class="padding-2">' + storeLocations.address1 + ' ' + storeLocations.address2 + '</div>';
            html += '<div class="padding-2">' + storeLocations.zip_code ;
            html += ' ' + storeLocations.city_name+ '</div>';
            if (storeLocations.state_name != null) {
                html += ' ' + storeLocations.state_name;
            }
            
            html += '<div class="padding-2">' + storeLocations.country_name + '</div>';
        }
        if (displayStoreTiming == 1) {
            if (storeLocations['current_hours'] != '') {
                html += '<div class="padding-2">' + storeTiming + ': ' + storeLocations['current_hours'] + '</div>';
            } else {
                html += '<div class="padding-2">' + storeTiming + ': ' + closedMsg + '</div>';
            }
        }
        if (displayContactDetails == 1) {
            if (storeLocations['phone'] != '') {
                html += '<div class="padding-2">' + contactDetails + ': ' + storeLocations['phone'] + '</div>';
            }
        }
        if (controller == 'order' && storeLocations['wk_store_distance'] != undefined) {
            html += '<div class="padding-2" id="wk_store_distance_' + storeLocations.id + '"';
            html += '>' + distanceMsg + ': <span>' + storeLocations['wk_store_distance'] + '</span></div>';
        }
        if (displayEmail == 1 && controller != 'order') {
            if (storeLocations['email'] != '') {
                html += '<div class="padding-2">' + emailMsg + ': ' + storeLocations['email'] + '</div>';
            }
        }
        link = 'http://maps.google.com/maps?saddr=';
        if (currentLocation != null) {
            link += '(' + currentLocation.lat + ', ' + currentLocation.lng + ')';
        }
        link += '&amp;daddr=(' + storeLocations.latitude + ', ' + storeLocations.longitude + ')';
		
		var profile_link = storeLocations.storeLink;
		
        if (controller != 'order') {
            html += '<div class="padding-2"><a class="btn wkstore-btn"';
            html += ' href="' + profile_link + '" target="_blank">' + btnGetDirections + '</a></div>';
        }
        html += '</td></tr>';
        if (controller == 'order') {
            html += '<tr><td>';
            html += '<span class="wk-store-pickup-directions"><a ';
            html += ' href="' + profile_link + '" target="_blank">' + btnGetDirections + '</a></span></td><td>';
            html += '<span class="wk-store-pickup-select"><a index="' + markerIndex + '" id-seller="' + storeLocations.id_seller + '" id-store="' + storeLocations.id + '" class="btn wkstore-btn wk-select-store" target="">' + 'Select Store' + '</a>';
            html += '</span></td>';
            html += '<td>';
            html += '<span class="wk-store-pickup-directions"><a ';
            html += ' href="' + profile_link + '" target="_blank">' + btnGetDirections + '</a></span></td><td>';
            html += '<span class="wk-store-pickup-select"><a index="' + markerIndex + '" id-seller="' + storeLocations.id_seller + '" id-store="' + storeLocations.id + '" class="btn wkstore-btn wk-select-store" target="">' + 'Select Store' + '</a>';
            html += '</span></td></tr>';
        }
        html += '</div></table>';   
        return function () {
            infoWindow.setContent(html);
            infoWindow.open(map, marker);
        }
    })(marker));
}

function abortRunningAjax() {
    if (ajaxCheckVar) {
        ajaxCheckVar.abort();
    }
}
