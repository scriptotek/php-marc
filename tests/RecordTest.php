<?php

use Scriptotek\Marc\Record;
use Scriptotek\Marc\Collection;

class RecordTest extends \PHPUnit_Framework_TestCase
{
    public function testExampleWithNs()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?>
          <record xmlns="http://www.loc.gov/MARC21/slim">
            <leader>99999cam a2299999 u 4500</leader>
            <controlfield tag="001">98218834x</controlfield>
            <datafield tag="020" ind1=" " ind2=" ">
              <subfield code="a">8200424421</subfield>
              <subfield code="q">h.</subfield>
              <subfield code="c">Nkr 98.00</subfield>
            </datafield>
          </record>';

        $record = Record::fromString($source);
        $this->assertInstanceOf('Scriptotek\Marc\Record', $record);
    }

    public function testExampleWithoutNs()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?>
          <record>
            <leader>99999cam a2299999 u 4500</leader>
            <controlfield tag="001">98218834x</controlfield>
            <datafield tag="020" ind1=" " ind2=" ">
              <subfield code="a">8200424421</subfield>
              <subfield code="q">h.</subfield>
              <subfield code="c">Nkr 98.00</subfield>
            </datafield>
          </record>';

        $record = Record::fromString($source);
        $this->assertInstanceOf('Scriptotek\Marc\Record', $record);
    }

    public function testExampleWithCustomPrefix()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?>
          <mx:record xmlns:mx="http://www.loc.gov/MARC21/slim">
            <mx:leader>99999cam a2299999 u 4500</mx:leader>
            <mx:controlfield tag="001">98218834x</mx:controlfield>
            <mx:datafield tag="020" ind1=" " ind2=" ">
              <mx:subfield code="a">8200424421</mx:subfield>
              <mx:subfield code="q">h.</mx:subfield>
              <mx:subfield code="c">Nkr 98.00</mx:subfield>
            </mx:datafield>
          </mx:record>';

        $record = Record::fromString($source);
        $this->assertInstanceOf('Scriptotek\Marc\Record', $record);
    }

    public function testBinaryMarc()
    {
        $record = Record::fromFile('tests/data/binary-marc.mrc');
        $this->assertInstanceOf('Scriptotek\Marc\Record', $record);
    }

}
