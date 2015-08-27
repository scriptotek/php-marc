<?php

use Scriptotek\Marc\Importers\XmlImporter;

class SubjectFieldTest extends \PHPUnit_Framework_TestCase
{
    protected function getNthrecord($n)
    {
        $response = new XmlImporter('tests/data/sru-alma.xml');
        $collection = $response->getCollection();

        $records = $collection->records;
        foreach (range(1, $n) as $i) {
            $records->next();
        }

        return $records->current();
    }

    public function testSubjectString()
    {
        $record = $this->getNthrecord(1);

        # Vocabulary from indicator2
        $sub = $record->subjects[0];
        $this->assertEquals('lcsh', $sub->vocabulary);
        $this->assertEquals('topic', $sub->type);
        $this->assertEquals('Eightfold way (Nuclear physics) : Addresses, essays, lectures', strval($sub));
    }

    public function testSubjects()
    {
        $record = $this->getNthrecord(3);

        # Vocabulary from subfield 2
        $subject = $record->subjects[1];
        $this->assertInstanceOf('Scriptotek\Marc\Fields\Subject', $subject);
        $this->assertEquals('noubomn', $subject->vocabulary);
        $this->assertEquals('Elementærpartikler', strval($subject));
        $this->assertEquals('650', $subject->getTag('650'));

        $subject = $record->subjects[3];
        $this->assertInstanceOf('Scriptotek\Marc\Fields\Subject', $subject);
        $this->assertNull($subject->vocabulary);
        $this->assertEquals('elementærpartikler', strval($subject));
        $this->assertEquals('653', $subject->getTag('650'));
    }

    public function testGetSubjects()
    {
        $record = $this->getNthrecord(3);

        $lcsh = $record->getSubjects('lcsh');
        $noubomn = $record->getSubjects('noubomn');
        $noubomn_topic = $record->getSubjects('noubomn', 'topic');
        $noubomn_place = $record->getSubjects('noubomn', 'place');

        $this->assertCount(1, $lcsh);
        $this->assertCount(2, $noubomn);
        $this->assertCount(2, $noubomn_topic);
        $this->assertCount(0, $noubomn_place);
    }
}
