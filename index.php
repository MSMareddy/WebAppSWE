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
		
		<!-- <?php echo "Radius: ", $radius, " Address: ", $address; ?> -->
		
		<script>
			var map;
			var service;
			var infowindow;

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
						strictBounds: false,
					},
				zoom: 11});

			var request = {
			  query: '<?php echo $option; ?>',
			  location: PKI,
			  radius: '<?php echo $radius; ?>',
			  minPriceLevel: '<?php echo $price; ?>',
			  maxPriceLevel: '<?php echo $price; ?>'
			};

			service = new google.maps.places.PlacesService(map);

			service.textSearch(request, function(results, status) {
			  if (status === google.maps.places.PlacesServiceStatus.OK) {
				var count = 0;
				for (var i = 0; i < results.length; i++) {
				  createMarker(results[i]);
				  if (map.getBounds().contains(results[i].geometry.location)) {
					  count++;
				  }
				}
				console.log("Results within bounds: " + count);
				if (count == 0) {
					alert("No Places found for Restraunt type: <?php echo $option; ?> and price level: <?php echo $price; ?>\nPlease choose something else.");
				}
			  }
			});
			}
		
			function createMarker(place) {
			var marker = new google.maps.Marker({
			  map: map,
			  title: place.name,
			  position: place.geometry.location
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
	<h1 id = "title">Find Cheap Restaurants!</h1>
	<div class="grid-container">
		<div class="Empty1"></div>
		<div class="Map">
			<div id="map"></div>
			<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo getenv('API_KEY'); ?>&libraries=places&callback=initMap" async defer></script>
		</div>
		<div class="TypeLabel">
			Select restaurant option:
		</div>
		<div class="TypeListBox">
			<select name ="option">
				<option value="taco" <?php if($option == "taco"){?> selected <?php }?>>Taco</option>
				<option value="pizza" <?php if($option == "pizza"){?> selected <?php }?>>Pizza</option>
				<option value="hamburger" <?php if($option == "hamburger"){?> selected <?php }?>>Hamburger</option>
			</select>
		</div>
		<div class="OptionLabel">
			Select price range:
		</div>

		<div class="PriceSlider">
			<input type="range" id = "range" name ="price" min="1" max="4" step="1" value="<?php echo $price; ?>"><br>
			<div id ="sliderlabel"><div id="leftlabel">Cheap</div><div id="rightlabel">Expensive</div></div>
		</div>
		<div class="SubmitButton">
			<input type = "submit" class = "submit" value = "Search">
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