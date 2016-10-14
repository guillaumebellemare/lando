// JavaScript Document
//Plus c'est petit, plus le zoom est loin.
var LOCAL_ZOOM = 11;
var map;
var lat;
var lng;
var google;
var quebec = new google.maps.LatLng(48.902718, -64.249586);
var centeredCoords = null;
var customZip = getJSvars('google.map.js', 'client_zip');
var mc;
var map_bounds;
var init = false;
var info;
var over_query = false;
var geocoder = new google.maps.Geocoder();
//Step 1, on lance la demande. (suite à la ligne 289)
if(customZip) { findLatAndLngFromZip(geocoder, getJSvars('google.map.js', "client_zip")); }

$(document).ready(function() {
	//console.log(centeredCoords);
	
	 // Step 4. si on a setté centered coord, l'initilisation va se faire sur ce point.
	if (!init) { initializeMap(true, centeredCoords);  }
});

// the smooth zoom function
function smoothZoom (map, max, cnt) {
    if (cnt >= max) {
            return;
        }
    else {
        z = google.maps.event.addListener(map, 'zoom_changed', function(event){
            google.maps.event.removeListener(z);
            smoothZoom(map, max, cnt + 1);
        });
        setTimeout(function(){map.setZoom(cnt)}, 50); // 80ms is what I found to work well on my system -- it might not work well on all systems
    }
}

function getJSvars(script_name, var_name, if_empty) {
	var script_elements = document.getElementsByTagName('script');
	if (if_empty == null) {
		var if_empty = '';
	}
	for (a = 0; a < script_elements.length; a++) {
		var source_string = script_elements[a].src;
		if (source_string.indexOf(script_name) >= 0) {
			var_name = var_name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
			var regex_string = new RegExp("[\\?&]" + var_name + "=([^&#]*)");
			var parsed_vars = regex_string.exec(source_string);
			if (parsed_vars == null) {
				return if_empty;
			} else {
				return parsed_vars[1];
			}
		}
	}
}

function initializeMap(centering, customCoord) {
	/*var stylez = [{
		featureType: "all",
		stylers: [{
			saturation: -100
		}]
	}];*/
	var stylez = [{
		featureType: "all",
		/*stylers: [{
			saturation: -100
		}]*/
	}];
	var mapOptions = {
		zoom: customCoord ? LOCAL_ZOOM : 10,
		center: (customCoord ? customCoord : quebec),
		mapTypeControl: false,
		panControl: false,
		zoomControl: true,
		zoomControlOptions: {
			style: google.maps.ZoomControlStyle.LARGE,
			position: google.maps.ControlPosition.TOP_LEFT
		},
		scaleControl: false,
		streetViewControl: false
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	var styledMapOptions = {
		map: map,
		name: "tips4phpHip-Hop"
	}
	var testmap = new google.maps.StyledMapType(stylez, styledMapOptions);
	map.mapTypes.set('tips4php', testmap);
	map.setMapTypeId('tips4php');

	var clusterStyles = [{
		textColor: '#ffffff',
		anchor: [22, 0],
		anchorIcon: [33, 31],
		fontFamily: "Raleway,sans-serif",
		textSize: 20,
		fontWeight: '300',
		url: getJSvars('google.map.js', 'base') + 'public/images/marker-cluster.png',
		width: 66,
		height: 95,
	}];
	mcOptions = {
		styles: clusterStyles
	};

	mc = new MarkerClusterer(map, null, mcOptions);
	mc.onClick = function(clickedClusterIcon) { 
	  return multiChoice(clickedClusterIcon.cluster_); 
	}
	
	/****************************************/
	/* RÉACTIVER LORSQUE PLUS D'UN MARQUEUR */
	/****************************************/
	// Resize Map Bounds to see all Markers
	// map_bounds = new google.maps.LatLngBounds();
	map_bounds = new google.maps.LatLngBounds();
	var listener = google.maps.event.addListener(map, "idle", function() { 
	if (map.getZoom() > 16) map.setZoom(10); 
	  google.maps.event.removeListener(listener); 
	});

	//Load all markers
	loadMarkers();
	init = true;
}

function centerMap(coords){ // Step 3: si la carte est déjà initialisée, on se déplace sur la carte, sinon, on set la variable pour que l'initialisation se fasse comme il faut. 
	centeredCoords = coords;
	if(init){
		map.setCenter(centeredCoords);
		map.setZoom(LOCAL_ZOOM);
	}
}

function multiChoice(cluster) {
	// if more than 1 point shares the same lat/long
	// the size of the cluster array will be 1 AND
	// the number of markers in the cluster will be > 1
	// REMEMBER: maxZoom was already reached and we can't zoom in anymore
	if (cluster.markers_.length > 1) {
		info.close();
		var markers = cluster.markers_;

		var html = '';
		html += '<div id="infoWin">';
		var lang = getJSvars('google.map.js', 'lang', 'fr');
		if(lang == 'fr') html += '<p><em>Il y a ' + markers.length + ' détaillants à cette adresse:</em></p>';
		else html += '<p><em>There are ' + markers.length + ' retailers at this address:</em></p>';
		html += '<p class="store-address">' + markers[0].locationObj[4] + '<br />' + markers[0].locationObj[10] + ', ' + markers[0].locationObj[9] + ', ' + markers[0].locationObj[7] + '</p>'
		html += '<hr/>';
		for (var i = 0; i < markers.length; i++) {
			html += '<p class="store-name">'+ markers[i].locationObj[1] + '</p><p class="location">' + markers[i].locationObj[3] + '</p>';
			if(markers[i].locationObj[16]) html += '<a href="' + markers[i].locationObj[16] + '" class="btn">Voir la fiche</a></p>';
			if(i < markers.length -1) html += '<hr />';

		}
		html += '<br/>';
		html += '</div>';
		// I'm re-using the same global InfoWindow object here
		$('#infoWin').remove();
		$(html).appendTo('body');
		info.setContent($('#infoWin').get(0));
		pos = cluster.getCenter();
		info.setPosition(pos);
		info.open(map);

		// bind a click event to the list items to popup an InfoWindow
		$('ul.addrlist li').click(function() {
			var p = $(this).find("a").attr("rel");
			return infopop(markers[p]);
		});
		return false;
	}
	return true;
}

function showAddress(infowindow, locationObj, i) {
	var marker;
	var markerName;
	if(locationObj[2]===1){ markerName = 'marker'; }else if(locationObj[2]===2){ markerName = 'marker'; }else{ markerName = 'marker'; }
	if(locationObj[0]==="-1"){ markerName = 'marker'; }
	/*var markericon = new google.maps.MarkerImage(getJSvars('google.map.js', 'base') + 'images/common/' + markerName + '.png', new google.maps.Size(50, 76), new google.maps.Point(0, 0), new google.maps.Point(25, 76));
	var shadow = new google.maps.MarkerImage(getJSvars('google.map.js', 'base') + 'images/common/' + markerName + '.png', new google.maps.Size(50, 14), new google.maps.Point(0, 0), new google.maps.Point(25, 76));*/
	var markericon = new google.maps.MarkerImage(getJSvars('google.map.js', 'base') + 'public/images/' + markerName + '.png',
        new google.maps.Size(42.0, 51.0),
        new google.maps.Point(0, 0),
        new google.maps.Point(22.0, 51.0)
    );
    var shadow = new google.maps.MarkerImage(getJSvars('google.map.js', 'base') + 'public/images/shadow-marker.png',
        new google.maps.Size(60.0, 51.0),
        new google.maps.Point(0, 0),
        new google.maps.Point(17.0, 51.0)
    );
	
	var shape = {
		coord: [1, 1, 1, 60, 50, 60, 50, 1],
		type: 'poly'
	};
	//var markericon = 'marker';
	marker = new google.maps.Marker({
		"position": new google.maps.LatLng(locationObj[5], locationObj[6]),
		"icon": markericon,
		"map": map,
		"shape": shape,
		"shadow": shadow
	});
	marker.locationObj = locationObj;
	google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
			
			//  1 = Nom de l'endroit
			//  2 = 
			//  3 = Type de marqueur (pour différenciation visuelle)
			//  4 = Adresse
			//  5 = Latitude
			//  6 = Longitude
			//  7 = Code postal
			//  8 = Pays
			//  9 = Province
			//  10 = Ville
			//  11 = Sélectionné?
			//  12 = Marqueur 
			info_w = '<p class="location-name">' + locationObj[1] + '</p>' + '<p class="location-address">' + locationObj[4] + '<br />' + locationObj[9] + '<br />' + locationObj[10] + ', '+locationObj[8];
			if(locationObj[7]) { info_w += '<br>' + locationObj[7]; }
			if(locationObj[3]) { info_w +='<br><br><strong>' + locationObj[3] + '</strong>'; }
			if(locationObj[16]) { info_w += '<br><br><a href="' + locationObj[16] + '" class="btn">Voir la fiche</a></p>'; }
			infowindow.setContent(info_w);
			
			infowindow.setPosition(marker.getPosition());
			infowindow.open(map, this);
			marker.setZIndex(100);
		};
	})(marker, i));
	locationObj[12] = marker;
	if(getJSvars('google.map.js', 'nocluster') === 'true');
	else mc.addMarker(marker);
	if(!centeredCoords){
		map_bounds.extend(marker.getPosition());
		map.fitBounds(map_bounds);
	
		
		//Si on affiche la map pour seulement avoir les bureaux
		if (locationObj[0] === '-1') {
			map.setCenter(marker.getPosition());
			map.setZoom(11);
		}
		//if selected, center on it.
		if (locationObj[11] === true) {
			setTimeout(function() {
				console.log(marker);
				map.setCenter(marker.getPosition());
				//map.setZoom(9);
				smoothZoom (map,9, map.getZoom());
			}, 1000);
		}
	}
	
}

function findLatAndLng(geocoder, infowindow, locationObj, i, retry) {
	
	var toGeocode = locationObj[4] + ', ' + locationObj[10] + ', ' + locationObj[8] +  ', ' + locationObj[7];
	if (!toGeocode) toGeocode = locationObj[2];
	geocoder.geocode({
		"address": toGeocode
	}, function(results, status) {
		
		if (status == 'OK') {
			if (getAddressComponent(results[0].address_components, 'street_number', 'long_name')) locationObj[2] = getAddressComponent(results[0].address_components, 'street_number', 'long_name') + ', ' + getAddressComponent(results[0].address_components, 'route', 'long_name') // Address
			locationObj[5] = results[0].geometry.location.lat(); //Lat
			locationObj[6] = results[0].geometry.location.lng(); //Long
			if (locationObj[7] == '') locationObj[7] = getAddressComponent(results[0].address_components, 'postal_code', 'long_name');
			locationObj[8] = getAddressComponent(results[0].address_components, 'administrative_area_level_1')
			locationObj[9] = getAddressComponent(results[0].address_components, 'country')
			if (locationObj[10] == '') locationObj[10] = getAddressComponent(results[0].address_components, 'locality', 'long_name')
			else $.post(getJSvars('google.map.js', 'base') + "fr/ou-trouver-nos-fromages-savegeo", {
				id: locationObj[0],
				lat: locationObj[5],
				lng: locationObj[6],
				location: locationObj[4],
				location_city: locationObj[10],
				postal_code: locationObj[7],
				country: locationObj[8],
				state: locationObj[9],
				table: getJSvars('google.map.js', 'table')
			}, function(results) {
				//console.log(results);
			});
			showAddress(infowindow, locationObj, i);
		} else {
			if (status != 'ZERO_RESULTS' && retry <= 100) {
				console.log(status, locationObj);
				findLatAndLng(geocoder, infowindow, locationObj, i, retry++);
			}
		}
	})
}


function findLatAndLngFromZip(geocoder, zip) {
	geocoder.geocode({address: zip},
    function(results_array, status) { 
	   // console.log("result ...", results_array)
        // Check status and do whatever you want with what you get back
        // in the results_array variable if it is OK.
        var lat = results_array[0].geometry.location.lat();
        var lng = results_array[0].geometry.location.lng();
        //Step 2 : quand on a la réponse, on appelle une fonction qui va gérer la réponse.  ligne 126
		centerMap(new google.maps.LatLng(lat, lng));
	});
	
}

function getAddressComponent(addr, searchedType, length) {
	if (!length) length = 'short_name';
	for (var i in addr) {
		if (addr[i].types[0] == searchedType) return addr[i][length];
	}
}

function loadMarkers() {
	var infowindow = new google.maps.InfoWindow({
		maxWidth: 425,
		pixelOffset: new google.maps.Size(0, 0, 'px', 'px')
	});
	info = infowindow;
/*
	google.maps.event.addListener(infowindow, "domready", function() {   
	
	  setTimeout(function(){$("img[src$='iws3.png']").hide();},10);
	
	});
*/
	var geocoder = new google.maps.Geocoder();
	var geocodeRequest = 1;
	for (var i = locations.length - 1; i >= 0; i--) {
		if(locations[i]){
		
			if (!locations[i][5] || !locations[i][6]) {
				
				if(geocodeRequest < 12) findLatAndLng(geocoder, infowindow, locations[i], i);
				else break;
				geocodeRequest++;
	
			} else {
				showAddress(infowindow, locations[i], i);
			}
			
		}
	}
}