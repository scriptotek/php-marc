<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Corporation extends Field implements FieldInterface
{
    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */
    public $properties = ['type', 'id', 'name', 'subordinate_unit', 'location', 'date', 'number', 'relationship'];

    public static $headingComponentCodes = ['a', 'b', 'c', 'd', 'n'];

    const MAIN_ENTRY= '110';
    const ADDED_ENTRY = '710';

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

    public function getSubordinateUnit()
    {
        return $this->sf('b');
    }

    public function getLocation()
    {
        return $this->sf('c');
    }

    public function getDate()
    {
        return $this->sf('d');
    }

    public function getNumber()
    {
        return $this->sf('n');
    }

    public function getRelationship()
    {
        return $this->sf('4');
    }

    public function __toString()
    {
        $out = [];
        foreach ($this->getSubfields() as $sf) {
            if (in_array($sf->getCode(), $this->headingComponentCodes)) {
                $out[] = $sf->getData();
            }
        }
        return str_replace('/  /', ' ', implode(' ', $out));
    }
}
