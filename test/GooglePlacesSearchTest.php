<?php
require_once '../src/GooglePlacesSearch.php';

class GooglePlacesSearchTest extends PHPUnit_Framework_TestCase {
      public function setUp() {
          $apiKey = '';
        $this->places = new GooglePlacesSearch($apiKey, 'mandela');
    }

    public function test_get_areas() {
        $this->markTestSkipped( 'PHPUnit will skip this test method' );

    }

    public function test_query_api() {
         $this->markTestSkipped( 'PHPUnit will skip this test method' );

    }

    public function test_set_radius() {
         $this->markTestSkipped( 'PHPUnit will skip this test method' );
    }

    public function test_set_language() {
        $this->assertEquals('en-GB', $this->places->_language);
        $this->places->__set($this->places->_language, 'cz');
    }
}