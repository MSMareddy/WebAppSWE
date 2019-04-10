<!--Testing website map-app-swe.herokuapp.com -->
<!DOCTYPE html>
<?php
try
{
	#get Arguments
	$optionArg = isset($_POST["option"]) ? $_POST["option"] : "";
	$priceArg = isset($_POST["price"]) ? $_POST["price"] : "";
	
	#get Cookie values
	$optionCookie = isset($_COOKIE["optionCookie"]) ? $_COOKIE["optionCookie"] : "";
	$priceCookie = isset($_COOKIE["priceCookie"]) ? $_COOKIE["priceCookie"] : "";
	
	#check if arguments empty
	if(empty($optionArg) && empty($priceArg)) {
		#check if cookie empty
		if (empty($optionCookie) && empty($priceCookie)) {
			#set elements to default values
			$option = "pizza";
			$price = "1";
		}
		else {
			#set element to cookie values
			$option = $optionCookie;
			$price = $priceCookie;
		}
	}
	else {
		#set cookies to argument values
		setcookie("optionCookie", $optionArg, time() + 60*60*24*7);
		setcookie("priceCookie", $priceArg, time() + 60*60*24*7);
		$optionCookie = $optionArg;
		$priceCookie = $priceArg;
		#set element to argument values
		$option = $optionArg;
		$price = $priceArg;
	}
	
	$radius = 16000;
	if (isset($_COOKIE["radiusCookie"])) {
		$radius = $_COOKIE["radiusCookie"];
	}
	$address = "Peter Kiewit Institute";
	if (isset($_COOKIE["addressCookie"])) {
		$address = $_COOKIE["addressCookie"];
	}
	$latLong = "41.247389, -96.016763";
	if (isset($_COOKIE["latLongCookie"])) {
		$latLong = $_COOKIE["latLongCookie"];
	}
?>
<html lang = "en">
	<head>
		<title>Restaurant Finder</title>
		<meta charset="utf-8">
		<!-- Icon from https://gauger.io/fonticon/ -->
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		<link rel="stylesheet" type="text/css" href="stylesheet.css">
		
		<!-- Smart HTML resources-->
		<link rel="stylesheet" href="../../source/styles/smart.default.css" type="text/css" />
		<link rel="stylesheet" href="../../source/styles/smart.base.css" type="text/css" />
		<link rel="stylesheet" href="../../source/styles/images" type="text/css" />
		
		<script type="text/javascript" src="../../source/smart.core.js"></script>
		<script type="text/javascript" src="../../source/smart.element.js"></script>
		<script type="text/javascript" src="../../source/smart.button.js"></script>
		<script type="text/javascript" src="../../source/smart.scrollbar.js"></script>
		<script type="text/javascript" src="../../source/smart.listbox.js"></script>
		<script type="text/javascript" src="../../source/smart.dropdownlist.js"></script>
		<!-- <?php echo "Radius: ", $radius, " Address: ", $address; ?> -->
		
		<script>
			var map;
			var service;
			var infowindow;
			var count = 0;
			
			function initMap() {
			var PKI = new google.maps.LatLng(<?php echo $latLong; ?>);
			var OMAHA_BOUNDS = {
				north: 41.357508510088905,
				south: 41.13708358693462,
				west: -96.13591745327756,
				east: -95.89760854672238,
			};
			infowindow = new google.maps.InfoWindow();

			map = new google.maps.Map(document.getElementById('map'), {
				center: PKI,
				restriction: {
						latLngBounds: OMAHA_BOUNDS,
						strictBounds: false
					},
				zoom: 12,
                styles: [
                    {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
                    {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
                    {elementType: 'labels.text.fill', stylers: [{color: '#3d5574'}]},
                    {
                        featureType: 'administrative.locality',
                        elementType: 'labels.text.fill',
                        stylers: [{color: '#c8cfd5'}]
                    },
                    {
                        featureType: 'poi',
                        elementType: 'labels.text.fill',
                        stylers: [{color: '#c9d5d5'}]
                    },
                    {
                        featureType: 'poi.park',
                        elementType: 'geometry',
                        stylers: [{color: '#1c32b8'}]
                    },
                    {
                        featureType: 'poi.park',
                        elementType: 'labels.text.fill',
                        stylers: [{color: '#4fe2ee'}]
                    },
                    {
                        featureType: 'road',
                        elementType: 'geometry',
                        stylers: [{color: '#1dffdc'}]
                    },
                    {
                        featureType: 'road',
                        elementType: 'geometry.stroke',
                        stylers: [{color: '#1694f6'}]
                    },
                    {
                        featureType: 'road',
                        elementType: 'labels.text.fill',
                        stylers: [{color: '#9ca5b3'}]
                    },
                    {
                        featureType: 'road.highway',
                        elementType: 'geometry',
                        stylers: [{color: '#ba3ecd'}]
                    },
                    {
                        featureType: 'road.highway',
                        elementType: 'geometry.stroke',
                        stylers: [{color: '#3adfec'}]
                    },
                    {
                        featureType: 'road.highway',
                        elementType: 'labels.text.fill',
                        stylers: [{color: '#bbebf3'}]
                    },
                    {
                        featureType: 'transit',
                        elementType: 'geometry',
                        stylers: [{color: '#482534'}]
                    },
                    {
                        featureType: 'transit.station',
                        elementType: 'labels.text.fill',
                        stylers: [{color: '#d57cc1'}]
                    },
                    {
                        featureType: 'water',
                        elementType: 'geometry',
                        stylers: [{color: '#ebebf9'}]
                    },
                    {
                        featureType: 'water',
                        elementType: 'labels.text.fill',
                        stylers: [{color: '#9ff7e7'}]
                    },
                    {
                        featureType: 'water',
                        elementType: 'labels.text.stroke',
                        stylers: [{color: '#f7def7'}]
                    }
				]
			});

			var request = {
			  query: '<?php echo $option; ?>',
			  location: PKI,
			  radius: '<?php echo $radius; ?>',
			  minPriceLevel: '<?php echo $price; ?>',
			  maxPriceLevel: '<?php echo $price; ?>'
			};

			service = new google.maps.places.PlacesService(map);
			
			var PKI_Marker = new google.maps.Marker({
				position: PKI,
				map: map,
				title: 'Peter Kiewit Institute',
				label: {
					text: 'PKI',
					fontSize: '10px'
				}
			});
			service.textSearch(request, function(results, status) {
			  if (status === google.maps.places.PlacesServiceStatus.OK) {
				for (var i = 0; i < results.length; i++) {
				  if (map.getBounds().contains(results[i].geometry.location)) {
					  count++;
				  }
				  createMarker(results[i]);
				}
				console.log("Results within bounds: " + count);
				if (count === 0) {
					alert("No Places found for Restraunt type: <?php echo $option; ?> and price level: <?php echo $price; ?>\nPlease choose something else.");
				}
			  }
			});
			}
		
			function createMarker(place) {
			var PKI = new google.maps.LatLng(<?php echo $latLong; ?>);
			//get diff
			var diffInMiles = google.maps.geometry.spherical.computeDistanceBetween(PKI, place.geometry.location) * 0.000621371;
			//convert diff to number from float and truncate digits and to string
			var diffString = Number.parseFloat(diffInMiles).toFixed(1).toString();
			var marker = new google.maps.Marker({
			  map: map,
			  title: place.name,
			  position: place.geometry.location,
			  label: {
					text: diffString,
					fontSize: '10px'
			  }
			});

			google.maps.event.addListener(marker, 'click', function() {
			  infowindow.setContent(place.name + "<br>" + place.formatted_address + "<br>" 
			  + "<a target= '_blank' href = 'https://www.google.com/maps/search/?api=1&query=" + escape(place.name) +"&query_place_id=" + place.place_id + "'>View on Google Maps</a>");
			  infowindow.open(map, this);
			});
			}
		</script>
	</head>
<body>
	<ul>
	  <li><a class="active" href="/">Home</a></li>
	  <li><a href ="https://github.com/MSMareddy/WebAppSWE">About</a></li>
	</ul>
	<form method = "post">
	<div class="grid-container">
		<div class="title">
    </div>
		<div class="Map">
			<div id="map"></div>
			<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo getenv('API_KEY'); ?>&libraries=geometry,places&callback=initMap" async defer></script>
		</div>
		<div class="TypeLabel">
			Select restaurant option:
		</div>
		<div class="TypeListBox"; placeholder="enter">
			<smart-drop-down-list>
				<smart-list-item value="pizza" <?php if($option=="pizza" ){?> selected <?php }?>>Pizza</smart-list-item>
				<smart-list-item value="hamburger" <?php if($option=="hamburger" ){?> selected <?php }?>>Hamburger</smart-list-item>
				<smart-list-item value="taco" <?php if($option=="taco" ){?> selected <?php }?> >Taco</smart-list-item>
			</smart-drop-down-list>
		</div>
		<div class="OptionLabel">
			Select price range:
		</div>
		<div class="PriceSlider">
      <div>
        <smart-toggle-button class="primary raised exclusive-selection"> <i name="1" value="<?php echo $price[name]; ?>" class="material-icons">$</i></smart-toggle-button>
        <smart-toggle-button class="primary raised exclusive-selection"> <i name="2" value="<?php echo $price[name]; ?>" class="material-icons">$$</i></smart-toggle-button>
        <smart-toggle-button class="primary raised exclusive-selection"> <i name="3" value="<?php echo $price[name]; ?>" class="material-icons">$$$</i></smart-toggle-button>
        <smart-toggle-button class="primary  raised exclusive-selection"> <i name="4" value="<?php echo $price[name]; ?>" class="material-icons">$$$$</i></smart-toggle-button>
      </div>
		</div>
		<div class="SubmitButton">
        <smart-button class="raised" value="submit">Search</smart-button>
		</div>
		<div class="Empty2"></div>
	</div>
	</form>
</body>
</html>
<!--
<?php
#display all values for logging
echo "Log:\n";
echo "Arguments: " . $optionArg . ", " . $priceArg . ".\n";
echo "Cookie: " . $optionCookie . ", " . $priceCookie . ".\n";
echo "Element: " . $option . ", " . $price . ".\n";
}
catch(Exception $e) {
	#prevent exceptions from breaking code
	echo $e->getMessage();
	date_default_timezone_set(America/Chicago);
	$date = date('m-d-Y h:i:s a');
	error_log($date);
	error_log($e->getMessage());
	error_log($e->getTraceAsString());
}
?>
!-->