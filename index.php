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
	
	#set radius to 16000m(10 miles) and change to cookie value if set.
	$radius = 16000;
	if (isset($_COOKIE["radiusCookie"])) {
		$radius = $_COOKIE["radiusCookie"];
	}
	
	#set address, coordinates to PKI by default. If cookie exists set to home coordinates.
	$address = "PKI";
	$latLong = "41.247389, -96.016763";
	if (isset($_COOKIE["latCookie"]) && isset($_COOKIE["longCookie"])) {
		$address = "Home";
		$latLong = $_COOKIE["latCookie"] . ", " . $_COOKIE["longCookie"];
	}
	
	#backend frontend Mapping for optimal search results.
	$optionArray = [
		"buffet" => "Buffet",
		"chicken" => "Chicken",
		"chinese food"=> "Chinese" ,
		"cupcake"  => "Cupcake",
		"hamburger" => "Hamburger",
		"ice cream" => "Ice Cream",
		"pasta" => "Pasta",
		"pizza" => "Pizza",
		"sandwich" => "Sandwich",
		"seafood"=> "Seafood",
		"steak" => "Steak",
		"sushi" => "Sushi",
		"taco" => "Mexican"
	];
?>
<html lang = "en">
	<head>
		<title>Restaurant Locator</title>
		<meta charset="utf-8">
		<!-- Icon from https://gauger.io/fonticon/ -->
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
		
		<!-- <?php echo "Radius: ", $radius, " Address: ", $address; ?> -->
		<script src = "js/Elements.js"></script>
		<script>
		try {
			/**
			 * Executes function on window load.
			 */
			window.onload = function(){
				/**
				 * if href is clicked ask for confirmation and execute get currentPosition method.
				 * get current position transfers control to showPosition & Error method.
				 * If user declines/geolocation is not compatible don't redirect.
				 *
				 * @return boolean page redirects based on return value.
				 */
				document.getElementById("getLocation").onclick = function() {
					if (navigator.geolocation) {
						if (confirm("Do you want to change to current location?")) {
							navigator.geolocation.getCurrentPosition(showPosition, error);
						}
					} 
					else {
						alert("Geolocation is not supported by this browser.");
					}
					return false;
				}
				
				/**
				 * get all href elements based on class name catEl[Restraunt Category Element].
				 * set the option form argument to the id value of href that is selected by the user
				 *
				 * @return boolean page redirects based on return value.
				 */
				var elements = document.getElementsByClassName('catEl');
				console.log("Number of categories: " + elements.length);
				for (var i=0; i < elements.length; i++) {
					elements[i].onclick = function() {
						var optionArg = this.id;
						document.getElementById("selected").value = optionArg;
						console.log("Category Selected: " + optionArg);
						return false;
					}
				}
				/**
				 * showPosition
				 *
				 * Set cookie to current latitude and longitude.
				 * Cookie is valid for 1 week.
				 * Page refreshes after setting cookie value to reflect changes.
				 *
				 * @param position contains the current coordinates
				 */
				function showPosition(position) {
					var lat = String(position.coords.latitude).substring(0,8);
					var lng = String(position.coords.longitude).substring(0,8);
					var d = new Date();
					d.setTime(d.getTime() + (7*24*60*60*1000));
					var expires = "expires="+ d.toUTCString();
					document.cookie = "latCookie=" + lat + ";" + expires + ";path=/";
					document.cookie = "longCookie=" + lng + ";" + expires + ";path=/";
					location.reload(true);
				}
				/**
				 * error
				 * 
				 * Alerts the user with error message.
				 *
				 * @param err error object
				 */
				function error(err) {
					switch(error.code) {
						case error.PERMISSION_DENIED:
						  alert("Location Error: " + "User denied the request for Geolocation.")
						  break;
						case error.POSITION_UNAVAILABLE:
						  alert("Location Error: " + "Location information is unavailable.")
						  break;
						case error.TIMEOUT:
						  alert("Location Error: " + "The request to get user location timed out.")
						  break;
						case error.UNKNOWN_ERR:
						  alert("Location Error: " + "An unknown error occurred.")
						  break;
					}
				}
			}
			var map;
			var service;
			var infowindow;
			var count = 0;
			
			/**
			 * initMap
			 *
			 * Initialise Map.
			 * Set center of map get the coordinates from website arguments.
			 * Initialise circle with center and radius arguments.
			 * Restrict map to circular bounds.
			 * Set custom stylesheet for Map.
			 * Pass the request from user arguments.
			 * Initialise Home Marker.
			 */
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
				mapTypeControl: false,
				mapTypeControlOptions: {
				  style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
				  position: google.maps.ControlPosition.TOP_CENTER
				},
				zoomControl: true,
				zoomControlOptions: {
				  position: google.maps.ControlPosition.LEFT_CENTER
				},
				scaleControl: true,
				streetViewControl: false,
				streetViewControlOptions: {
				  position: google.maps.ControlPosition.LEFT_TOP
				},
				fullscreenControl: true,
				styles: [
					{
						featureType: "administrative",
						elementType: "labels.text",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "administrative.locality",
						elementType: "labels.text",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "administrative.neighborhood",
						elementType: "labels.text",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "landscape.man_made",
						elementType: "labels.text",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "landscape.natural",
						elementType: "geometry.fill",
						stylers: [
							{
								visibility: "on"
							},
							{
								color: "#e0efef"
							}
						]
					},
					{
						featureType: "landscape.natural",
						elementType: "labels.text",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "poi",
						elementType: "geometry.fill",
						stylers: [
							{
								visibility: "on"
							},
							{
								hue: "#1900ff"
							},
							{
								color: "#c0e8e8"
							}
						]
					},
					{
						featureType: "poi",
						elementType: "labels.text",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "poi",
						elementType: "labels.icon",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "road",
						elementType: "geometry",
						stylers: [
							{
								lightness: 100
							},
							{
								visibility: "simplified"
							}
						]
					},
					{
						featureType: "road",
						elementType: "labels",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "transit",
						elementType: "labels.text",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "transit",
						elementType: "labels.icon",
						stylers: [
							{
								visibility: "off"
							}
						]
					},
					{
						featureType: "transit.line",
						elementType: "geometry",
						stylers: [
							{
								visibility: "on"
							},
							{
								lightness: 700
							}
						]
					},
					{
						featureType: "water",
						elementType: "all",
						stylers: [
							{
								color: "#7dcdcd"
							}
						]
					}
				]
			});
			var legendControlDiv = document.createElement('div');
			var legendControl = new LegendControl(legendControlDiv, map);

			legendControlDiv.index = 1;
			map.controls[google.maps.ControlPosition.TOP_CENTER].push(legendControlDiv);

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
				animation: google.maps.Animation.DROP,
				title: '<?php echo $address; ?>',
				<?php if($address == "Home") {?> 
				icon: 'images/home.png',
				<?php } else {?>
				label: {
					text: '<?php echo $address; ?>',
					fontSize: '10px',
					fontWeight: 'bold'
				},
				<?php } ?>
			});
			
			// add legend elements			
			var legend = document.getElementById('legend');
			
			var div = document.createElement('div');
			div.innerHTML = '<img alt = "Open Red" width = "16" height = "16" src="' + 'images/blue.png'  + '"> ' + "Open<br>";
			div.id = "legendEntry";
			legend.appendChild(div);
			
			var div2 = document.createElement('div');
			div2.innerHTML = '<img alt = "Closed Red" width = "16" height = "16" src="' + 'images/red.png'  + '"> ' + "Closed";
			div2.id = "legendEntry";
			legend.appendChild(div2);
			
			var div3 = document.createElement('div');
			div3.innerHTML = "1.1 Distance";
			div3.id = "legendEntry";
			legend.appendChild(div3);
			
			<?php if($address == "Home") {?> 
			var div4 = document.createElement('div');
			div4.innerHTML = '<img alt = "Batcave Home" width = "16" height = "16" src="' + 'images/home.png'  + '"> ' + "<?php echo $address; ?>";
			div4.id = "legendEntry";
			legend.appendChild(div4);
			<?php } ?>
			// move legend div inside map.
			map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
			
			/**
			 * textSearch
			 *
			 * Get all the results, loop through them.
			 * Count all the results within circular bounds.
			 * If none within bounds alert user.
			 * Create Custom marker for each result.
			 * If there are any errors while adding marker log it to console.
			 *
			 * @param request request object containing all paramters for search.
			 * @param results contains all resulting objects from results.
			 * @param status ensures that the google places API is up and running.
			 */
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
			
			/**
			 * createMarker
			 *
			 * Add markers to map.
			 * Labeled with approx distance from home to restraunt in miles.
			 * Marker icon is blue and bold text if place open. uses red icon if closed.
			 * Added Listener which displays more infor like address and link to view on gmaps on click.
			 *
			 * @param place contains the place information.
			 */
			function createMarker(place) {
				var weightOfFont = "normal";
				var openNow = "Business Hours Info Unavailable";
				var color = "blue";
				try {
					var open = place.opening_hours.open_now;
					if (open != null) {
						weightOfFont = open? "bold":"lighter";
						openNow = open? "<strong>Open</strong>":"<i>Closed</i>";
						color = open?"blue":"red";
					}
				}
				catch(undefinedErr) {
					console.log("Open now warning: " + undefinedErr.message);
				}
				var place_icon = {
					url: 'images/'+ color +'.png', 
					labelOrigin: new google.maps.Point(15.25,10)
				};
				var home = new google.maps.LatLng(<?php echo $latLong; ?>);
				//get diff
				var diffInMiles = google.maps.geometry.spherical.computeDistanceBetween(home, place.geometry.location) * 0.000621371;
				//convert diff to number from float and truncate digits and to string
				var diffString = Number.parseFloat(diffInMiles).toFixed(1).toString();
				var marker = new google.maps.Marker({
				  map: map,
				  title: place.name,
				  position: place.geometry.location,
				  animation: google.maps.Animation.DROP,
				  label: {
					text: diffString,
					fontSize: '8px',
					fontWeight: weightOfFont,
				  },
				  icon: place_icon,
				});
				
				google.maps.event.addListener(marker, 'click', function() {
				  infowindow.setContent("<em>"+ place.name + "</em><br>" + place.formatted_address + "<br>" + "Approx Distance from <?php echo $address; ?>: " + diffString + "mi<br>"
				  + "Rating: " + place.rating + "/5<br>" + openNow + "<br>"
				  + "<a target= '_blank' href = 'https://www.google.com/maps/search/?api=1&query=" + escape(place.name) + "&query_place_id=" + place.place_id + "'>View on Google Maps</a>");
				  infowindow.open(map, this);
				});
			}
			/**
			* The LegendControl adds a control to the map that allows user to display legend.
			* 
			* This constructor takes the control DIV as an argument.
			* @constructor
			*/
			function LegendControl(controlDiv, map) {

				// Set CSS for the control border.
				var controlUI = document.createElement('div');
				controlUI.style.backgroundColor = '#fff';
				controlUI.style.border = '2px solid #fff';
				controlUI.style.borderRadius = '3px';
				controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
				controlUI.style.cursor = 'pointer';
				controlUI.style.marginBottom = '22px';
				controlUI.style.textAlign = 'center';
				controlUI.title = 'Show Legend';
				controlDiv.appendChild(controlUI);

				// Set CSS for the control interior.
				var controlText = document.createElement('div');
				controlText.id = "legendButton";
				controlText.style.color = 'rgb(25,25,25)';
				controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
				controlText.style.fontSize = '16px';
				controlText.style.lineHeight = '38px';
				controlText.style.paddingLeft = '5px';
				controlText.style.paddingRight = '5px';
				controlText.innerHTML = 'Show Legend';
				controlUI.appendChild(controlText);

				// Setup the click event listeners: simply set the map to Chicago.
				controlUI.addEventListener('click', function() {
				  var legendButton = document.getElementById('legendButton');
				  
				  if(legend.style.display == "none") {
				   legend.style.display = "block";
				   legendButton.innerHTML = "Hide Legend";
				  }
				  else {
					legend.style.display = "none";
					legendButton.innerHTML = "Show Legend";
				  }
				});
			}
		}
		catch(generalError) {
			//General error logging to console.
			console.log("General Error: " + generalError.message);
		}
		</script>
		
	</head>
<body>
	<ul>
	  <li><a class="active" href="/">Home</a></li>
	  <li><a id="getLocation" href="/">Update Location</a></li>
	  <li><a href ="https://github.com/MSMareddy/WebAppSWE">About</a></li>
	</ul>
	<form method = "post">
	<div class="grid-container">
		<div class="Empty1">
			<h1 class="animated fadeIn" id = "title">Restaurant Locator</h1>
		</div>
		<div class="Map">
			<div id="map"></div>
			<div id="legend" style="display: none"><strong>Legend</strong></div>
			<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo getenv('API_KEY'); ?>&libraries=geometry,places&callback=initMap" async defer></script>
		</div>
		<div class="TypeLabel">
			Select restaurant option:
		</div>
		<div class="TypeListBox">
			<div id="dd" class="wrapper-dropdown-3" tabindex="1">
				<span><?php echo ucfirst($optionArray[$option]); ?></span>
				<ul class="dropdown">
					<li><a href="#" class="catEl" id = "pizza"><i class="fas fa-pizza-slice"></i>Pizza</a></li>
					<li><a href="#" class="catEl" id = "taco"><i class="fas fa-pepper-hot"></i>Mexican</a></li>
					<li><a href="#" class="catEl" id = "burgers"><i class="fas fa-hamburger"></i>Burgers</a></li>		
					<li><a href="#" class="catEl"><i class="fas fa-hamburger"></i>Burgers</a></li>	
					<li><a href="#" class="catEl"><i class="fas fa-hamburger"></i>Burgers</a></li>	
					<li><a href="#" class="catEl"><i class="fas fa-hamburger"></i>Burgers</a></li>
				</ul>
			</div>
		</div>
		<div  class="OptionLabel">
			Select price range:
		</div>

		<div class="PriceSlider">
			<input type="range" id = "range" name ="price" min="1" max="4" step="1" value="<?php echo $price; ?>"><br>
			<div id ="sliderlabel"><div id="leftlabel">Cheap</div><div id="rightlabel">&nbsp;Costly</div></div>
		</div>
		<div class="SubmitButton">
			<input type = "submit" class = "submit hvr-grow" value = "Search">
		</div>
		<div class="Empty2"></div>
	</div>
	
	<input type="hidden" name="option" id="selected" value="<?php echo ucfirst($option); ?>" />
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