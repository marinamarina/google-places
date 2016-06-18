<?php
	require_once('src/GooglePlacesSearch.php');

	$apiKey = 'AIzaSyCcnjLmWQYr0eP3_Er1DwyBOlKo6gGjDng'; //'Your Google Places API Key';
	$keyword = 'Mandela'; //For example, 'Mandela' will search by places named by Mandela

	// Coordinates provided in the input file should be set as google.maps.LatLng objects
	$mandela_search = new GooglePlacesSearch($apiKey, $keyword);

	$mandela_search->Search();
