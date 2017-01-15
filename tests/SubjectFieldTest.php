<?php

use Scriptotek\Marc\Collection;
use Scriptotek\Marc\Fields\Subject;
use Scriptotek\Marc\Fields\UncontrolledSubject;

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
        $this->assertEquals(Subject::TOPICAL_TERM, $subject->getType());
        $this->assertNull($subject->getControlNumber());
    }

    public function testRepeated653a()
    {
        $record = $this->getNthrecord(3);

        $subjects = $record->getSubjects(null, Subject::UNCONTROLLED_INDEX_TERM);
        $this->assertCount(2, $subjects);

        $this->assertInstanceOf(UncontrolledSubject::class, $subjects[0]);
        $this->assertEquals('elementærpartikler', (string) $subjects[0]);
        $this->assertEquals(Subject::UNCONTROLLED_INDEX_TERM, $subjects[0]->getType());
        $this->assertEquals('symmetri', (string) $subjects[1]);
    }

    public function testGetSubjectsFiltering()
    {
        $record = $this->getNthrecord(3);

        $lcsh = $record->getSubjects('lcsh');
        $noubomn = $record->getSubjects('noubomn');
        $noubomn_topic = $record->getSubjects('noubomn', Subject::TOPICAL_TERM);
        $noubomn_place = $record->getSubjects('noubomn', Subject::GEOGRAPHIC_NAME);
        $type_combo = $record->getSubjects(null, [Subject::TOPICAL_TERM, Subject::UNCONTROLLED_INDEX_TERM]);

        $this->assertCount(1, $lcsh);
        $this->assertCount(2, $noubomn);
        $this->assertCount(2, $noubomn_topic);
        $this->assertCount(0, $noubomn_place);
        $this->assertCount(5, $type_combo);
    }

    public function testEdit()
    {
        $record = $this->getNthrecord(3);
        $this->assertCount(5, $record->subjects);

        $this->assertInstanceOf(Subject::class, $record->subjects[0]);
        $record->subjects[0]->delete();

        $this->assertInstanceOf(Subject::class, $record->subjects[0]);
        $record->subjects[0]->delete();

        $this->assertInstanceOf(Subject::class, $record->subjects[0]);
        $record->subjects[0]->delete();
        $this->assertCount(2, $record->subjects);

        $this->assertInstanceOf(UncontrolledSubject::class, $record->subjects[0]);
        $record->subjects[0]->delete();
        $this->assertCount(1, $record->subjects);

        $this->assertInstanceOf(UncontrolledSubject::class, $record->subjects[0]);
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
