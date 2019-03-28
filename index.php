<!--testing website map-app-swe.herokuapp.com -->
<head>
<title>UI</title>
 <script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

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
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
</head>
<body>
<center>
<table align="left" width ="69%">
  <tr>
    <td width="200px" height="200px">
	<form method = "post">
		Select restaurant option:
		<select name ="option">
			<option value="taco">Taco</option>
			<option value="pizza">Pizza</option>
		</select>
		<br><br>
		Select price range:
		<div class="range">
			<input type="range" name ="price" min="0" max="4" steps="1" value="0">
		</div>
		Cheap&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Expensive<br><br><br>
		<input type = "submit">
	</form>
	</td>
	<td rowspan = "2" width="200px" height="200px">
		<div id="map" style="width:100%; height:100%"></div>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDm9MtndFo7OyIi8HvTx4NnUDq9xN5BKE&libraries=places&callback=initMap" async defer></script>
	</td>
  </tr>
  <!-- getting all the Post arguments -->
  Arguments:
  <?php echo $_POST["option"]; ?>
  <?php echo $_POST["price"]; ?>
</table>
</center>
</body>