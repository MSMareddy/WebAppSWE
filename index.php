<!--testing website map-app-swe.herokuapp.com -->
<!DOCTYPE html>
<html lang = "en">
	<head>
		<title>Restaurant Picker</title>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		<meta charset="utf-8">
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
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
	<?php
		$option = $_POST["option"] != "" ?$_POST["option"]:"pizza";
		$price = $_POST["price"] != ""?$_POST["price"]:"0";
	?>
	<h1 align="center">Find Cheap Restaurants!</h1>
	<table align="center" width ="81%">
		<tr>
			<td width="100px" height="200px">
				<form method = "post">
					<table>
						<tr>
							<td width="200px" height="50px">Select restaurant option:</td>
							<td>
								<select name ="option">
								<option value="taco" <?php if($option == "taco"){?> selected <?php }?>>Taco</option>
								<option value="pizza" <?php if($option == "pizza"){?> selected <?php }?>>Pizza</option>
								<option value="hamburger" <?php if($option == "hamburger"){?> selected <?php }?>>Hamburger</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="200px" height="50px">Select price range:</td>
							<td>
							<input type="range" id = "range" name ="price" min="0" max="4" steps="1" value="<?php echo $price; ?>"><br>
							Cheap&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Expensive
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td colspan = "2" align ="center"><input type = "submit"></td>
						</tr>
					</table>
				</form>
			</td>
			<td rowspan = "2" width="300px" height="400px">
				<div id="map" style="width:100%; height:100%"></div>
				<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDm9MtndFo7OyIi8HvTx4NnUDq9xN5BKE&libraries=places&callback=initMap" async defer></script>
			</td>
		</tr>
	</table>
</body>
</html>