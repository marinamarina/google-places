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
        $this->assertEquals(50000, $this->places->getRadius());
        $this->places->setRadius(3000);
        $this->assertEquals(3000, $this->places->getRadius());
    }

    public function test_set_language() {
        $this->assertEquals('en-GB', $this->places->getLanguage());
        $this->places->setLanguage('cz');
        $this->assertEquals('cz', $this->places->getLanguage());
    }

}