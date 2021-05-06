<?php

namespace Tests;

use Scriptotek\Marc\Fields\Field;
use Scriptotek\Marc\Record;
use Scriptotek\Marc\QueryResult;

class EditionTest extends TestCase
{
    protected $record;

    public function testSimple250()
    {
        $record = Record::fromString('<?xml version="1.0" encoding="UTF-8" ?>
          <record xmlns="http://www.loc.gov/MARC21/slim">
            <leader>99999cam a2299999 u 4500</leader>
            <controlfield tag="001">98218834x</controlfield>
            <datafield tag="250" ind1=" " ind2=" ">
              <subfield code="a">2nd ed. </subfield>
            </datafield>
          </record>');

        $this->assertEquals('2nd ed.', $record->edition);
    }

    public function testMissingA()
    {
        $record = Record::fromString('<?xml version="1.0" encoding="UTF-8" ?>
          <record xmlns="http://www.loc.gov/MARC21/slim">
            <leader>99999cam a2299999 u 4500</leader>
            <controlfield tag="001">98218834x</controlfield>
            <datafield tag="250" ind1=" " ind2=" ">
              <subfield code="b">2nd ed. </subfield>
            </datafield>
          </record>');

        $this->assertNull($record->edition);
    }
}
