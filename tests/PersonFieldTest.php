<?php

use PHPUnit\Framework\TestCase;
use Scriptotek\Marc\Collection;
use Scriptotek\Marc\Fields\Person;

class PersonFieldTest extends TestCase
{
    protected function getNthrecord($filename, $n)
    {
        $records = Collection::fromFile('tests/data/' . $filename)->toArray();

        return $records[$n - 1];
    }

    public function testSimple()
    {
        $record = $this->getNthrecord('sru-alma.xml', 1);

        # Vocabulary from indicator2
        $person = $record->creators[0];
        $this->assertEquals('Gell-Mann, Murray', strval($person));
    }

    public function testWithDatesAndIsbd()
    {
        $record = $this->getNthrecord('sru-loc2.xml', 1);

        # Vocabulary from indicator2
        $person = $record->creators[0];
        $this->assertEquals('Einstein, Albert (1879-1955)', strval($person));
    }
}
