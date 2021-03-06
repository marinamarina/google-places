<?php
class GooglePlacesSearch {

    private $searched_areas_source_file = 'input/set_of_searched_areas.csv';
    private $output_file = 'output/this.txt';
    private $set_of_searched_areas = array();
    private $base_url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';
    private $lines_count = 0;
    private $ary;
    private $lng_step = 0.5;
    private $lat_step = 0.6;
    private $_radius = 50000;
    private $_query = null;
    private $_language = 'en-GB';
    private $api_key = null;

    /*
     * Construct the query
     */
	public function __construct($api_key, $query) {
	    ini_set('memory_limit', '512M');
        $this->ary[] = 'UTF-8';
        $this->ary[] = 'ASCII';
        $this->ary[] = 'EUC-JP';
        mb_detect_order($this->ary);

        $this->api_key = $api_key;
        $this->query = $query;
	}

    /**
    * Setters and getters
    */
    public function getRadius() {
        return $this->_radius;
    }

    public function setRadius($radius) {
        $this->_radius = $radius;
    }

    public function setQuery($query) {
        $this->_query = urlencode($query);
    }

    public function getLanguage() {
        return $this->_language;
    }

    public function setLanguage($language) {
        $this->_language = $language;
    }

    public function __get($var) {
        $func = 'get'.$var;
        if (method_exists($this, $func)) {
            return $this->$func();
        } else {
            throw new Exception("Inexistent property: $var");
        }
    }

    public function __set($var, $value) {
        $func = 'set'.$var;

        if (method_exists($this, $func)) {
            $this->$func($value);
        } else {
            if (method_exists($this, 'get'.$var)) {
                throw new Exception("property $var is read-only");
            } else {
                throw new Exception("Inexistent property: $var");
            }
        }
    }

    /*
     * Read a set of coordinates from the file holding locations coordinates
     * input/set_of_searched_areas.csv, save it into an array
     */
    private function getAreas() {
       $csvNumColumns = 5;
       $csvDelim = ",";

       $csvData = file_get_contents($this->searched_areas_source_file);
       $set_of_searched_areas  = array_chunk(str_getcsv($csvData, $csvDelim), $csvNumColumns);

       return $set_of_searched_areas;
    }

    /**
     * Sends a request via curl and returns the response
     */
    private function query_api($url) {
        $curl = curl_init($url);

        $options = array (
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false
        );

        curl_setopt_array($curl, $options);
        $curl_result = curl_exec($curl);

        if ($error = curl_error($curl)) {
            echo $error;
        }

        curl_close($curl);

        return $curl_result;
	}

    private function extract_data_from_request($output, $currentArea) {
        $types = array();

        for ($i = 0; $i < count($output['results']); $i++) {
            $this->lines_count++;

            //creating a line
            $line = " " . $this->lines_count. ","; //add the count column
            $line = " " . $currentArea. ","; //add the area column

            //adding data from the cURL request to the line
            $line .= str_replace(",", "", $output['results'][$i]['name']) . ","; //eliminate commas in a name
            $line .= $output['results'][$i]['geometry']['location']['lat'] . ",";
            $line .= $output['results'][$i]['geometry']['location']['lng'] . ",";
            $line .= $output['results'][$i]['id'] . ",";
            $line .= str_replace(",", "", $output['results'][$i]['vicinity']) . ",";

            if(isset($output['results'][$i]['types'])) { //https://developers.google.com/places/documentation/supported_types
                $types = $output['results'][$i]['types'];

                for($j = 0; $j< sizeof($types); $j++) {
                    if ($j == sizeof($types)-1) {
                        $line .= $types[$j];
                    } else {
                        $line .= $types[$j] . " | ";
                    }
                }
            } else {
                $line .= " " . ",";
            }
            file_put_contents($this->output_file, $this->encode_line($line) . "\r\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Search by keyword
     * (within this pair of coordinates and using the radius)
     */
    private function handleResults($output, $currentArea) {
        if($output['status'] === "OK") {
            $this->extract_data_from_request($output, $currentArea);
            echo "I have results!" . "\n";
        }
        else if ($output['status'] === "ZERO_RESULTS") {
            echo "No results!" . "\n";
        } else {
            echo $output['status'];
        }
    }

    /**
     * Generate output file header
     */
    private function add_output_header() {
        file_put_contents($this->output_file, "area".","."name".","."lat".","."long".","."id".","."vicinity"."," ."type". "\r\n", FILE_APPEND | LOCK_EX);
    }

    private function encode_line($line) {
        $encoded_line = mb_convert_encoding($line, "UTF-8", "auto");
        return $encoded_line;
    }

    public function Search() {
        $count = 0;
    	$set_of_searched_areas = $this->getAreas();

        echo "Lat:" . " - " . "Long:" . "\n"; //add header to the console output
    	$this->add_output_header();           //add header to the xls output

    	foreach ($set_of_searched_areas as $area) {
            $currentArea = trim($area[0]);
            $lat = $area[1];
        	$lngStart = $area[2];
        	$lng = $lngStart; //$lat and $lng are the starting values of long (Coordinates for top Left corner)
        	$latEnd = $area[3];
        	$lngEnd = $area[4];

            while ($lat > $latEnd ) {
        	   	$lng = $lngStart; //reset longitude

               while($lng < $lngEnd ) {
                    $url = "{$this->base_url}?location={$lat},{$lng}&radius={$this->_radius}&name={$this->_query}&language={$this->_language}&sensor=true&key={$this->api_key}";

                    $output = json_decode($this->query_api($url), true);
                    echo ' area: ' . $currentArea . ' | ' . 'lat: ' . $lat . ' | long: ' . $lng . ' | ';

                    $this->handleResults($output, $currentArea);

                    $lng = $lng + $this->lng_step;
                }
                $lat = $lat - $this->lat_step;
            }
        }
    }
}