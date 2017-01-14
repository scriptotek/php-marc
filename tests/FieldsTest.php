<?php

use Scriptotek\Marc\Record;

class IsbnFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testIsbn()
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
        $this->assertEquals(['8200424421'], $record->isbns);
        $this->assertEquals('Nkr 98.00', $record->isbns[0]->sf('c'));
        $this->assertEquals(
            json_encode(['isbns' => ['8200424421']]),
            json_encode(['isbns' => $record->isbns])
        );
    }

    public function test020withoutA()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?>
          <record xmlns="http://www.loc.gov/MARC21/slim">
            <leader>99999cam a2299999 u 4500</leader>
            <controlfield tag="001">98218834x</controlfield>
            <datafield tag="020" ind1=" " ind2=" ">
              <subfield code="q">h.</subfield>
              <subfield code="c">Nkr 98.00</subfield>
            </datafield>
          </record>';

        $record = Record::fromString($source);
        $this->assertEquals([''], $record->isbns);
        $this->assertEquals(
            json_encode(['isbns' => ['']]),
            json_encode(['isbns' => $record->isbns])
        );
    }

    public function testId()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?>
          <record xmlns="http://www.loc.gov/MARC21/slim">
            <leader>99999cam a2299999 u 4500</leader>
            <controlfield tag="001">98218834x</controlfield>
          </record>';

        $record = Record::fromString($source);
        $this->assertEquals('98218834x', $record->id);
        $this->assertEquals(
            json_encode(['id' => '98218834x']),
            json_encode(['id' => $record->id])
        );
    }
}
