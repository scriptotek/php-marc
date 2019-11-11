<?php

namespace Tests;

use File_MARC;
use File_MARC_Record;
use Scriptotek\Marc\AuthorityRecord;
use Scriptotek\Marc\BibliographicRecord;
use Scriptotek\Marc\Fields\Field;
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
        $record = Record::fromFile($this->pathTo('binary-marc.mrc'));
        $this->assertInstanceOf(Record::class, $record);
    }

    public function testThatFieldObjectsAreReturned()
    {
        $record = Record::fromFile($this->pathTo('binary-marc.mrc'));
        $this->assertInstanceOf(Field::class, $record->getField('020'));
        $this->assertInstanceOf(Field::class, $record->getFields('020')[0]);
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

    public function testRecordTypeDescriptiveCatalogingForm()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?>
          <record>
            <leader>99999cam a2299999 c 4500</leader>
          </record>';

        $record = Record::fromString($source);
        $this->assertEquals(Marc21::ISBD_PUNCTUATION_OMITTED, $record->catalogingForm);
    }

    /**
     * Test the getRecord method.
     *
     * @throws \File_MARC_Exception
     */
    public function testGetRecord()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?>
          <record>
            <leader>99999cam a2299999 c 4500</leader>
          </record>';
        $wrapped_record = new File_MARC_Record(new File_MARC($source, File_MARC::SOURCE_STRING));
        $wrapper = new Record($wrapped_record);

        // Make sure that the exact same wrapped record object is returned
        // by the getter.
        $this->assertSame($wrapped_record, $wrapper->getRecord());
    }
}
