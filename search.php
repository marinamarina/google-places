<?php
	require_once('lib/GooglePlacesSearch.php');

	$apiKey = 'Your Google Places API Key';
	$person = 'Name';

	// Coordinates provided in the input file should be set as google.maps.LatLng objects
	$van_gogh_search = new GooglePlacesSearch($apiKey, $person);

	$van_gogh_search->Search();
