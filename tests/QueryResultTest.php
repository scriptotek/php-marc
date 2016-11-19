<?php

use Scriptotek\Marc\Record;
use Scriptotek\Marc\QueryResult;

class QueryResultTest extends \PHPUnit_Framework_TestCase
{
    protected $record;

    public function setUp()
    {
        $this->record = Record::fromString('<?xml version="1.0" encoding="UTF-8" ?>
          <record xmlns="http://www.loc.gov/MARC21/slim">
            <leader>99999cam a2299999 u 4500</leader>
            <controlfield tag="001">98218834x</controlfield>
            <datafield tag="020" ind1=" " ind2=" ">
              <subfield code="a">8200424421</subfield>
              <subfield code="q">h.</subfield>
              <subfield code="c">Nkr 98.00</subfield>
            </datafield>
            <datafield tag="020" ind1=" " ind2=" ">
              <subfield code="a">9788200424420</subfield>
              <subfield code="q">ib.</subfield>
            </datafield>
          </record>');
    }

    public function testInitialization()
    {
        $result = $this->record->query('020');
        $this->assertInstanceOf(QueryResult::class, $result);
    }

    public function testFirstField()
    {
        $result = $this->record->query('020{$a}')->first();
        $this->assertInstanceOf('File_MARC_Field', $result);
    }

    public function testFirstSubfield()
    {
        $result = $this->record->query('020$a')->first();
        $this->assertInstanceOf('File_MARC_Subfield', $result);
    }

    public function testText()
    {
        $result = $this->record->query('020$a')->text();
        $this->assertEquals('8200424421', $result);
    }

    public function testTextPattern()
    {
        $result = $this->record->query('020$a{$q=\ib.}')->text();
        $this->assertEquals('9788200424420', $result);
    }
}
