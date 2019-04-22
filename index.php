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
		<script src = "js/script.js"></script>
		
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
				<span><?php echo ucfirst($option); ?></span>
				<ul class="dropdown">
					<li><a href="#" class="catEl" id = "pizza"><i class="fas fa-pizza-slice"></i>Pizza</a></li>
					<li><a href="#" class="catEl" id = "mexican"><i class="fas fa-pepper-hot"></i>Mexican</a></li>
					<li><a href="#" class="catEl" id = "burgers"><i class="fas fa-hamburger"></i>Burgers</a></li>	
					<li><a href="#" class="catEl" id = "abc"><i class="fas fa-hamburger"></i>Def</a></li>	
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