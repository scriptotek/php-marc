<?php

namespace Tests;

use File_MARC_Data_Field;
use File_MARC_Subfield;
use Scriptotek\Marc\Fields\Location;

class LocationTest extends TestCase
{
    public function setUp()
    {
        $field = new File_MARC_Data_Field('852', array(
            new File_MARC_Subfield('b', '1030310'),
            new File_MARC_Subfield('c', 'k00481'),
            new File_MARC_Subfield('h', '793.24'),
            new File_MARC_Subfield('i', 'Cra'),
            new File_MARC_Subfield('x', 'A non-public note'),
            new File_MARC_Subfield('z', 'A public note'),

        ));
        $this->loc = new Location($field);
    }

    public function testCallcode()
    {
        $this->assertEquals('793.24 Cra', strval($this->loc->callcode));
    }

    public function testLocation()
    {
        $this->assertNull($this->loc->location);
    }

    public function testSublocation()
    {
        $this->assertEquals('1030310', strval($this->loc->sublocation));
    }

    public function testShelvinglocation()
    {
        $this->assertEquals('k00481', strval($this->loc->shelvinglocation));
    }

    public function testPublicNote()
    {
        $this->assertEquals('A public note', strval($this->loc->publicNote));
    }

    public function testNonPublicNote()
    {
        $this->assertEquals('A non-public note', strval($this->loc->nonPublicNote));
    }
}
