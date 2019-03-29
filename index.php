<!--testing website map-app-swe.herokuapp.com -->
<!DOCTYPE html>
<html lang = "en">
	<head>
		<title>Restaurant Finder</title>
		<meta charset="utf-8">
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
	<?php
		$option = $_POST["option"] != "" ?$_POST["option"]:"pizza";
		$price = $_POST["price"] != ""?$_POST["price"]:"0";
	?>
	<form method = "post">
	<h1 id = "title">Find Cheap Restaurants!</h1>
	<table id = "layout">
		<tr>
			<td colspan = "2" class ="empty"></td>
			<td rowspan ="5" id="mapele">
				<div id="map"></div>
				<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDm9MtndFo7OyIi8HvTx4NnUDq9xN5BKE&libraries=places&callback=initMap" async defer></script>
			</td>
		</tr>
		<tr>
			<td class="label">Select restaurant option:</td>
			<td class ="formelement">
				<select name ="option">
					<option value="taco" <?php if($option == "taco"){?> selected <?php }?>>Taco</option>
					<option value="pizza" <?php if($option == "pizza"){?> selected <?php }?>>Pizza</option>
					<option value="hamburger" <?php if($option == "hamburger"){?> selected <?php }?>>Hamburger</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="label">Select price range:</td>
			<td class ="formelement">
			<input type="range" id = "range" name ="price" min="0" max="4" step="1" value="<?php echo $price; ?>"><br>
			Cheap&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Expensive
			</td>
		</tr>
		<tr>
			<td id="submitEle" colspan = "2"><input type = "submit" class = "submit" value = "Search"></td>
		</tr>
		<tr>
			<td colspan = "2" class ="empty"></td>
		</tr>
	</table>
	</form>
</body>
</html>