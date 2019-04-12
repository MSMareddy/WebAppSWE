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
	$address = "PKI";
	$latLong = "41.247389, -96.016763";
	if (isset($_COOKIE["latLongCookie"])) {
		$address = "HOME";
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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
		  
		<!-- <?php echo "Radius: ", $radius, " Address: ", $address; ?> -->
		
		<script>
		try {
			window.onload = function(){
				document.getElementById("getLocation").onclick = function() {
					if (navigator.geolocation) {
						if (confirm("Do you want to change to current location?")) {
							navigator.geolocation.getCurrentPosition(showPosition);
						}
					} 
					else {
						alert("Geolocation is not supported by this browser.");
					}
					return false;
				}
				function showPosition(position) {
					var currLocation = String(position.coords.latitude).substring(0,8);
					currLocation += ", ";
					currLocation += String(position.coords.longitude).substring(0,8);
					var d = new Date();
					d.setTime(d.getTime() + (7*24*60*60*1000));
					var expires = "expires="+ d.toUTCString();
					document.cookie = "latLongCookie=" + currLocation + ";" + expires + ";path=/";
					var div = document.createElement('div');
					div.innerHTML = currLocation;
					document.body.appendChild(div);
					location.reload(true);
				}
			}
			var map;
			var service;
			var infowindow;
			var count = 0;
			
			function initMap() {
			var home = new google.maps.LatLng(<?php echo $latLong; ?>);
			
			var OMAHA_CIRCLE = new google.maps.Circle({
				center: home,
				radius: parseInt('<?php echo $radius; ?>', 10)
			});
			var OMAHA_BOUNDS = OMAHA_CIRCLE.getBounds();
			
			infowindow = new google.maps.InfoWindow();

			map = new google.maps.Map(document.getElementById('map'), {
				center: home,
				restriction: {
						latLngBounds: OMAHA_BOUNDS,
						strictBounds: false
					},
				zoom: 11,
                styles: [
                    {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
                    {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
                    {elementType: 'labels.text.fill', stylers: [{color: '#3d5574'}]},
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
                    }
				]
			});

			var request = {
			  query: '<?php echo $option; ?>',
			  location: home,
			  radius: '<?php echo $radius; ?>',
			  minPriceLevel: '<?php echo $price; ?>',
			  maxPriceLevel: '<?php echo $price; ?>'
			};

			service = new google.maps.places.PlacesService(map);
			
			var HOME_MARKER = new google.maps.Marker({
				position: home,
				map: map,
				title: '<?php echo $address; ?>',
				label: {
					text: '<?php echo $address; ?>',
					fontSize: '10px'
				}
				//,icon: 'http://maps.google.com/mapfiles/kml/shapes/homegardenbusiness.png'
			});
			service.textSearch(request, function(results, status) {
			  if (status === google.maps.places.PlacesServiceStatus.OK) {
				for (var i = 0; i < results.length; i++) {
				  if (map.getBounds().contains(results[i].geometry.location)) {
					  count++;
				  }
				  try {
					createMarker(results[i]);
				  }
				  catch(addMarkerErr) {
					console.log("Add Marker Error: " + addMarkerErr.message + " for result no: " + i + results[i].name)
				  }
				}
				console.log("Results within bounds: " + count);
				if (count === 0) {
					alert("No Places found for Restraunt type: <?php echo $option; ?> and price level: <?php echo $price; ?>\nPlease choose something else.");
				}
			  }
			});
			}
		
			function createMarker(place) {
				var home = new google.maps.LatLng(<?php echo $latLong; ?>);
				//get diff
				var diffInMiles = google.maps.geometry.spherical.computeDistanceBetween(home, place.geometry.location) * 0.000621371;
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
				  //,icon: 'http://maps.google.com/mapfiles/kml/pal2/icon45.png'
				});

				google.maps.event.addListener(marker, 'click', function() {
				  infowindow.setContent(place.name + "<br>" + place.formatted_address + "<br>" + "Distance from <?php echo $address; ?>: " + diffString + "mi<br>"
				  + "<a target= '_blank' href = 'https://www.google.com/maps/search/?api=1&query=" + escape(place.name) + "&query_place_id=" + place.place_id + "'>View on Google Maps</a>");
				  infowindow.open(map, this);
				});
			}
		}
		catch(generalError) {
			console.log("General Error: " + generalError.message);
		}
		</script>
	</head>
<body>
	<ul>
	  <li><a class="active" href="/">Home</a></li>
	  <li><a id="getLocation" href="/">Change Location</a></li>
	  <li><a href ="https://github.com/MSMareddy/WebAppSWE">About</a></li>
	</ul>
	<form method = "post">
	<div class="grid-container">
		<div class="Empty1">
			<h1 class="animated fadeIn" id = "title">Find Cheap Restaurants!</h1>
		</div>
		<div class="Map">
			<div id="map"></div>
			<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo getenv('API_KEY'); ?>&libraries=geometry,places&callback=initMap" async defer></script>
		</div>
		<div class="TypeLabel">
			Select restaurant option:
		</div>
		<div class="TypeListBox">
			<select name ="option">
				<option value="taco" <?php if($option == "taco"){?> selected <?php }?>>Taco</option>
				<option value="pizza" <?php if($option == "pizza"){?> selected <?php }?>>Pizza</option>
				<option value="hamburger" <?php if($option == "hamburger"){?> selected <?php }?>>Hamburger</option>
				<option value="pasta" <?php if($option == "pasta"){?> selected <?php }?>>Pasta</option>
				<option value="steak" <?php if($option == "steak"){?> selected <?php }?>>Steak</option>
				<option value="seafood" <?php if($option == "seafood"){?> selected <?php }?>>Seafood</option>
				<option value="sushi" <?php if($option == "sushi"){?> selected <?php }?>>Sushi</option>
				<option value="chinese food" <?php if($option == "chinese food"){?> selected <?php }?>>Chinese</option>
				<option value="cupcakes" <?php if($option == "cupcakes"){?> selected <?php }?>>Cupcakes</option>
				<option value="ice cream" <?php if($option == "ice cream"){?> selected <?php }?>>Ice Cream</option>
				<option value="chicken" <?php if($option == "chicken"){?> selected <?php }?>>Chicken</option>
				<option value="sandwich" <?php if($option == "sandwich"){?> selected <?php }?>>Sandwich</option>
				<option value="buffet" <?php if($option == "buffet"){?> selected <?php }?>>Buffet</option>
			</select>
		</div>
		<div  class="OptionLabel">
			Select price range:
		</div>

		<div class="PriceSlider">
			<input type="range" id = "range" name ="price" min="1" max="4" step="1" value="<?php echo $price; ?>"><br>
			<div id ="sliderlabel"><div id="leftlabel">Cheap</div><div id="rightlabel">&nbsp;Expensive</div></div>
		</div>
		<div class="SubmitButton">
			<input type = "submit" class = "submit hvr-grow" value = "Search">
		</div>
		<div class="Empty2"></div>
	</div>
	</form>
	<?php echo $latLong; ?>
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