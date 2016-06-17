## A PHP wrapper around Google Places API

This is a wrapper around Google Places API. The main purpose of the wrapper is to search places all over the world (eg., schools, restaurant, museums) having a keyword in the name, for example named after a certain person. It is possible to specify your own areas (or several of them) and perform a search just within those areas.
The wrapper saves a list of found places as a .txt file.


## Obtaining an API key ##

To be able to use this script, you will need a Google Places API key. To request an API key, point your browser to
https://code.google.com/apis/console and follow the instructions there. You can find your API key on the *API Access* tab under *Simple API Access*.

## How to use ##

### Preparation ###
Register a new GooglePlacesSearch object providing the following parameters:

* *Google Places API key*: string
* *query*: string


Specify the areas within which you want to perfom the search using the following format:
* area_name topLeftLong topLeftLat bottomRightLong bottomRightLat *

To search the whole world use the following set of areas:
 south_america,15,-95,-25,-40,
 africa,35,-22,-38,60,
 australia,-15,110,-48,156,
 north_america,49,-130,15,-60,
 asia,50,60,8,125,
 europe,65,-12,35,60

 You can add as many areas as you like, just remember to add a new area to a new line.

### Perform Search ###
```php

	require_once('lib/GooglePlacesSearch.php');

	$apiKey = 'Your Google Places API Key';
	$keyword = 'Mandela'; //For example, 'Mandela' will search by places named by Mandela

	// Coordinates provided in the input file should be set as google.maps.LatLng objects
	$mandela_search = new GooglePlacesSearch($apiKey, $keyword);

	$mandela_search->Search();

```

Run the search query with a following command in the terminal:

 `php search.php`

### Output ###
List of found places is saved in the file output/this.txt, each in a separate line containing information about the place in the following order:

* *area*: the name of the area specified in the input file
* *name*: the name of the place
* *lat*: the latitude of the place
* *lng*: the longitude of the place
* *lng*: unique identification number of the place
* *vicinity*: the street or neighborhood of the place
* *types*: array of feature types describing the place, see list of supported types[http://code.google.com/apis/maps/documentation/places/supported_types.html]