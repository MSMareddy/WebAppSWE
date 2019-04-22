# Restaurant Locator
Webpage contains a form prompting the user to select a price level ranging from cheap to expensive and asked to choose from 13 restaurant types. On submit, the embedded Maps shows restaurant results that match the conditions within 10 miles from Peter Kiewit Institute. If no results are found within the 10mi radius, the webpage alerts the user to pick a different option/price level. User is also provided with an option to change the default location. Restraunts are marked blue when open and red if closed.

Web App is hosted on an Apache HTTPD web server on Heroku. This web app implements PHP on the server side and uses js on the client side for Places API. This website remembers the cookies for 1 week.  
  
[Web App Link](https://map-app-swe.herokuapp.com/)  

# Requirements
* PHP (> v7.0)
* HTTPD Web Server
* [Get Google Maps API KEY](https://developers.google.com/maps/documentation/javascript/get-api-key)
* [Enable Places API](https://developers.google.com/maps/documentation/javascript/places)
* Set `API_KEY` environment variable.
* [Enable Geometry API](https://developers.google.com/maps/documentation/javascript/geometry)
