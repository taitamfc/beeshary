{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApGmap -->
{if ($page_name != 'stores' || $formAtts.stores == 1) && ($page_name != 'sitemap' || $formAtts.sitemap == 1)}
<div id="google-maps-{$formAtts.form_id|escape:'html':'UTF-8'}" class="block widget-gmap">
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
    {if isset($formAtts.title) && !empty($formAtts.title)}
    <h4 class="title_block">
    	{$formAtts.title|escape:'html':'UTF-8'}
    </h4>
    {/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
    <div class="gmap-cover {if $hasListStore}display-list-store{else}not-display-list-store{/if}" style="width: 100%; 
     height:{if isset($formAtts.height) && $formAtts.height}{$formAtts.height}{else}100%;{/if}; clear:both;">
    	{if $hasListStore}
    	<div class="gmap-content col-lg-9 col-md-8 col-sm-8 col-xs-6">
    	{else}
    	<div class="gmap-content">
    	{/if}
            <div id="map-canvas-{$formAtts.form_id|escape:'html':'UTF-8'}" class="gmap" style="min-width:100px; min-height:100px;
            	width:{if isset($formAtts.width) && $formAtts.width}{$formAtts.width|escape:'html':'UTF-8'}{else}100%;{/if}; 
            	height:{if isset($formAtts.height) && $formAtts.height}{$formAtts.height|escape:'html':'UTF-8'}{else}100%;{/if};"></div>
    	</div>
		{if $hasListStore}
    	<div class="gmap-stores-content col-lg-3 col-md-4 col-sm-4 col-xs-6" style="height: 100%">
    		<div id="gmap-stores-list-{$formAtts.form_id|escape:'html':'UTF-8'}">
        		<ul></ul>
        	</div>
    	</div>
    	{/if}
    </div>
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
    
<script type="text/javascript">
ap_list_functions.push(function(){
    $('<script>')
    .attr('type', 'text/javascript')
    .attr('src', 'https://maps.googleapis.com/maps/api/js?key={if isset($formAtts.gkey) && $formAtts.gkey}{$formAtts.gkey}{/if}&callback=initLeoMap')
    .appendTo('head');
});
</script>
    
    <script type="text/javascript">
        
        var apGMap = {$apGMap nofilter};
		var marker_list_{$formAtts.form_id} = {$marker_list nofilter};
		var marker_center = {$marker_center nofilter}
    	var markers_{$formAtts.form_id|escape:'html':'UTF-8'} = [];

    	function displayAMarker(data, obj, id) {
    		var m = markers_{$formAtts.form_id|escape:'html':'UTF-8'}[id];
    		google.maps.event.trigger(m, 'click');
    	}
    	function initializeListStore(data, name) {
    		var obj = $("#" + name + " ul");
    		synSize(name);
    		for(var i = 0; i < data.length; i++) {
    			var s = data[i];
    			obj.append("<li class='item-gmap-store' marker-id='" + i + "'" 
    					+ "onclick='return displayAMarker(marker_list_{$formAtts.form_id|escape:'html':'UTF-8'}, this, " + i + ");'>"
    					+ "<strong><b><span class='icon-map-marker'></span> "
    					+ s.name + "</b></strong><br/><text>" + s.address + "</text>");
    		}
    	}
        function initLeoMap(){
            initializeGmap('',
                    marker_list_{$formAtts.form_id|escape:'html':'UTF-8'}, 
                    markers_{$formAtts.form_id|escape:'html':'UTF-8'}, 
                    "map-canvas-{$formAtts.form_id|escape:'html':'UTF-8'}", 
                    {$formAtts.zoom|escape:'html':'UTF-8'});

            if("{$hasListStore|escape:'html':'UTF-8'}".length > 0) {
                initializeListStore(
                        marker_list_{$formAtts.form_id|escape:'html':'UTF-8'}, 
                        "gmap-stores-list-{$formAtts.form_id|escape:'html':'UTF-8'}");
            }

        }

    // CODE HERE not write in *.js, compatility with Chrome
    function initializeGmap(map, data, markers, nameGmap, zoom)
    {
        map = new google.maps.Map(document.getElementById(nameGmap), {
            center: new google.maps.LatLng(marker_center.latitude, marker_center.longitude),
            zoom: zoom,
            mapTypeId: 'roadmap'
        });

        if(data.length>0)
        {
            setTimeout(createMarkers(map, markers, data), 1500);
        }
        else
        {
            markers[0] = new google.maps.Marker({
                position: new google.maps.LatLng(marker_center.latitude, marker_center.longitude),
                animation: google.maps.Animation.DROP,
                map: map,
            });
        }
    };

    function createMarkers(map, markers, data) {
        // dataMarkers
        for (var i = 0; i < data.length; i++) {
            var obj = data[i];
            var lg = parseFloat(obj.longitude);
            var lt = parseFloat(obj.latitude);
            var name = obj.name;
            var address = obj.address;
            var other = obj.other;
            var id_store = obj.id_store;
            var has_store_picture = obj.has_store_picture;

            var latlng = new google.maps.LatLng(lt, lg);
            var html = "<div style='min-width:200px;'><b>" + name + "</b><br/>" + address;
            html += (has_store_picture ? "<br /><br /><p><img src='" + apGMap.img_store_dir + parseInt(id_store) + ".jpg' alt='' /></p>" : "");
            html += other + "<a href='http://maps.google.com/maps?saddr=&daddr=" + latlng + "' target='_blank'>" + apGMap.translation_5 +"<\/a>";
            html += "</div>";

            var infowindow = new google.maps.InfoWindow({
                content: "loading..."
            });

            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(lt, lg),
                animation: google.maps.Animation.DROP,
                map: map,
                icon: apGMap.img_ps_dir + apGMap.logo_store,
                title: obj.name,
                html: html
            });

            google.maps.event.addListener(marker, "click", function () {
                infowindow.setContent(this.html);
                infowindow.open(map, this);
            });
            markers[i] = marker;
        }
    }
	</script>
</div>
{/if}