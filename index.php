<!--Testing website map-app-swe.herokuapp.com -->
<!DOCTYPE html>
<?php
try
{
	$optionArg = $_POST["option"];
	$priceArg = $_POST["price"];
	$optionCookie = $_COOKIE["optionCookie"];
	$priceCookie = $_COOKIE["priceCookie"];
	if(empty($optionArg) && empty($priceArg)) {
		if (empty($optionCookie) && empty($priceCookie)) {
			$option = "pizza";
			$price = "0";
		}
		else {
			$option = $optionCookie;
			$price = $priceCookie;
		}
	}
	else {
		setcookie("optionCookie", $optionArg, time() + 60*60*24*7);
		setcookie("priceCookie", $priceArg, time() + 60*60*24*7);
		$option = $optionArg;
		$price = $priceArg;
	}
?>
<html lang = "en">
	<head>
		<title>Restaurant Finder</title>
		<meta charset="utf-8">
		<!-- Icon from https://gauger.io/fonticon/ -->
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		<link rel="stylesheet" type="text/css" href="stylesheet.css">
		<script>
			var map;
			var service;
			var infowindow;

			function initMap() {
			var sydney = new google.maps.LatLng(-33.867, 151.195);

			infowindow = new google.maps.InfoWindow();

			map = new google.maps.Map(
				document.getElementById('map'), {center: sydney, zoom: 15});

			var request = {
			  query: 'Museum of Contemporary Art Australia',
			  fields: ['name', 'geometry'],
			};

			service = new google.maps.places.PlacesService(map);

			service.findPlaceFromQuery(request, function(results, status) {
			  if (status === google.maps.places.PlacesServiceStatus.OK) {
				for (var i = 0; i < results.length; i++) {
				  createMarker(results[i]);
				}

				map.setCenter(results[0].geometry.location);
			  }
			});
			}

			function createMarker(place) {
			var marker = new google.maps.Marker({
			  map: map,
			  position: place.geometry.location
			});

			google.maps.event.addListener(marker, 'click', function() {
			  infowindow.setContent(place.name);
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
			<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDm9MtndFo7OyIi8HvTx4NnUDq9xN5BKE&libraries=places&callback=initMap" async defer></script>
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
			<input type="range" id = "range" name ="price" min="0" max="4" step="1" value="<?php echo $price; ?>"><br>
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
echo "Log:\n";
echo "Arguments: " . $optionArg . ", " . $priceArg . ".\n";
echo "Cookie: " . $_COOKIE["optionCookie"] . ", " . $_COOKIE["priceCookie"] . ".\n";
echo "Element: " . $option . ", " . $price . ".\n";
}
catch(Exception $e) {
	echo $e->getMessage();
}
?>
!-->