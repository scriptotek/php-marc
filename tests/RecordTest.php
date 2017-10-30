<?php

use PHPUnit\Framework\TestCase;
use Scriptotek\Marc\AuthorityRecord;
use Scriptotek\Marc\BibliographicRecord;
use Scriptotek\Marc\Fields\Subject;
use Scriptotek\Marc\HoldingsRecord;
use Scriptotek\Marc\Marc21;
use Scriptotek\Marc\Record;

class RecordTest extends TestCase
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
        $this->assertInstanceOf(Record::class, $record);
        $this->assertInstanceOf(BibliographicRecord::class, $record);
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
        $this->assertInstanceOf(Record::class, $record);
        $this->assertInstanceOf(BibliographicRecord::class, $record);
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
        $this->assertInstanceOf(Record::class, $record);
        $this->assertInstanceOf(BibliographicRecord::class, $record);
    }

    public function testBinaryMarc()
    {
        $record = Record::fromFile(__DIR__ . '/data/binary-marc.mrc');
        $this->assertInstanceOf(Record::class, $record);
    }

    public function testRecordTypeBiblio()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?>
          <record>
            <leader>99999cam a2299999 u 4500</leader>
          </record>';

        $record = Record::fromString($source);
        $this->assertInstanceOf(Record::class, $record);
        $this->assertInstanceOf(BibliographicRecord::class, $record);
    }

    public function testRecordTypeAuthority()
    {
        $record = Record::fromFile(__DIR__ . '/data/authority-bibsys.xml');

        $this->assertInstanceOf(Record::class, $record);
        $this->assertInstanceOf(AuthorityRecord::class, $record);
    }

    public function testRecordTypeHoldings()
    {
        $record = Record::fromFile(__DIR__ . '/data/holdings.xml');

        $this->assertInstanceOf(Record::class, $record);
        $this->assertInstanceOf(HoldingsRecord::class, $record);

        $this->assertEquals('1030310', $record->location->sublocation);
        $this->assertEquals('k00473', $record->location->shelvinglocation);
        $this->assertEquals('Plv 157', $record->location->callcode);
    }

    public function testHoldingsToJson()
    {
        $record = Record::fromFile(__DIR__ . '/data/holdings.xml');

        $this->assertEquals([
            'type' => MARC21::HOLDINGS,
            'id' => 'h2051843-47bibsys_ubo',
            'location' => [
                'sublocation' => '1030310',
                'shelvinglocation' => 'k00473',
                'callcode' => 'Plv 157',
            ],
        ], $record->jsonSerialize());
    }

    public function testBibliographicToJson()
    {
        $record = Record::fromFile(__DIR__ . '/data/bibliographic.xml');

        $this->assertEquals([
            'type' => MARC21::BIBLIOGRAPHIC,
            'id' => '999401461934702201',
            'title' => 'The eightfold way',
            'subjects' => [
                [
                    'type' => Subject::TOPICAL_TERM,
                    'vocabulary' => 'lcsh',
                    'term' => 'Eightfold way (Nuclear physics) : Addresses, essays, lectures',
                ],
                [
                    'type' => Subject::TOPICAL_TERM,
                    'vocabulary' => 'lcsh',
                    'term' => 'Nuclear reactions : Addresses, essays, lectures',
                ],
            ],
            'isbns' => [],
        ], $record->jsonSerialize());
    }

    public function testRecordTypeDescriptiveCatalogingForm()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?>
          <record>
            <leader>99999cam a2299999 c 4500</leader>
          </record>';

        $record = Record::fromString($source);
        $this->assertEquals(Marc21::ISBD_PUNCTUATION_OMITTED, $record->catalogingForm);
    }
}
