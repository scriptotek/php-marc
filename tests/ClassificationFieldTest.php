<?php

namespace Tests;

use Scriptotek\Marc\Fields\Classification;

class ClassificationFieldTest extends TestCase
{
    public function testClassificationString()
    {
        $record = $this->getNthrecord('sru-alma.xml', 1);

        # Vocabulary from indicator2
        $cls = $record->classifications[0];
        $this->assertInstanceOf('Scriptotek\Marc\Fields\Classification', $cls);
        $this->assertEquals('msc', $cls->scheme);
        $this->assertEquals('81', strval($cls));
        $this->assertEquals(Classification::OTHER_SCHEME, $cls->type);
    }

    public function testJsonSerialization()
    {
        $record = $this->getNthrecord('sru-alma.xml', 3);
        $cls = $record->classifications[1];

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'scheme' => 'inspec',
                'number' => 'a1130',
            ]),
            json_encode($cls)
        );
    }

    public function testRepeatedA()
    {
        $record = $this->makeMinimalRecord('
            <datafield tag="084" ind1=" " ind2=" ">
              <subfield code="a">330</subfield>
              <subfield code="a">380</subfield>
              <subfield code="a">650</subfield>
              <subfield code="q">DE-101</subfield>
              <subfield code="2">sdnb</subfield>
            </datafield>
        ');

        $this->assertCount(3, $record->classifications);

        $this->assertEquals('DE-101', $record->classifications[2]->assigningVocabulary);
        $this->assertEquals('sdnb', $record->classifications[2]->scheme);
    }
}
