<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Person extends Field implements FieldInterface
{
    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */
    public $properties = ['type', 'vocabulary', 'name', 'titulation', 'dates', 'id', 'relator_term', 'relationship'];

    public static $glue = ' : ';
    public static $termComponentCodes = ['a', 'b', 'x', 'y', 'z'];

    const MAIN_ENTRY= '100';
    const ADDED_ENTRY = '700';

    public static function get(Record $record)
    {
        $objs = [];

        foreach (parent::makeFieldObjects($record, Person::MAIN_ENTRY) as $obj) {
            $objs[] = $obj;
        }

        foreach (parent::makeFieldObjects($record, Person::ADDED_ENTRY) as $obj) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public function getType()
    {
        return $this->getTag();
    }

    /**
     * Return the Authority record control number
     */
    public function getId()
    {
        // preg_match('/^\((.+)\)(.+)$/', $sf0->getData(), $matches);
        return $this->sf('0');
    }

    public function getName()
    {
        return $this->sf('a');
    }

    public function getTitulation()
    {
        return $this->sf('c');
    }

    public function getDates()
    {
        return $this->sf('d');
    }

    public function getRelatorTerm()
    {
        return $this->sf('e');
    }

    public function getRelationship()
    {
        return $this->sf('4');
    }

    public function __toString()
    {
        if ($this->getDates()) {
            return sprintf('%s (%s)', $this->getName(), $this->getDates());
        }

        return $this->getName();
    }
}
