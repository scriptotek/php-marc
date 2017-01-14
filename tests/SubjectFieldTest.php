<?php

use Scriptotek\Marc\Collection;
use Scriptotek\Marc\Fields\Subject;

class SubjectFieldTest extends \PHPUnit_Framework_TestCase
{
    protected function getNthrecord($n)
    {
        $records = Collection::fromFile('tests/data/sru-alma.xml')->toArray();

        return $records[$n - 1];
    }

    public function testSubjectString()
    {
        $record = $this->getNthrecord(1);

        # Vocabulary from indicator2
        $sub = $record->subjects[0];
        $this->assertEquals('lcsh', $sub->vocabulary);
        $this->assertEquals(Subject::TOPICAL_TERM, $sub->type);
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
        $this->assertEquals('650', $subject->getTag());
        $this->assertNull($subject->getControlNumber());

        $subject = $record->subjects[3];
        $this->assertInstanceOf('Scriptotek\Marc\Fields\Subject', $subject);
        $this->assertNull($subject->vocabulary);
        $this->assertEquals('elementærpartikler', strval($subject));
        $this->assertEquals('653', $subject->getTag());
    }

    public function testGetSubjectsFiltering()
    {
        $record = $this->getNthrecord(3);

        $lcsh = $record->getSubjects('lcsh');
        $noubomn = $record->getSubjects('noubomn');
        $noubomn_topic = $record->getSubjects('noubomn', Subject::TOPICAL_TERM);
        $noubomn_place = $record->getSubjects('noubomn', Subject::GEOGRAPHIC_NAME);

        $this->assertCount(1, $lcsh);
        $this->assertCount(2, $noubomn);
        $this->assertCount(2, $noubomn_topic);
        $this->assertCount(0, $noubomn_place);
    }

    public function testEdit()
    {
        $record = $this->getNthrecord(2);
        $this->assertCount(2, $record->subjects);

        $record->subjects[0]->delete();
        $this->assertCount(1, $record->subjects);

        $record->subjects[0]->delete();
        $this->assertCount(0, $record->subjects);
    }

    public function testJsonSerialization()
    {
        $record = $this->getNthrecord(3);
        $subject = $record->subjects[1];

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'vocabulary' => 'noubomn',
                'type' => Subject::TOPICAL_TERM,
                'id' => null,
                'term' => 'Elementærpartikler'
            ]),
            json_encode($subject)
        );
    }
}
