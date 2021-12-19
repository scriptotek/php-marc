<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Corporation extends Field implements AuthorityFieldInterface
{
    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */
    public array $properties = ['type', 'id', 'name', 'subordinate_unit', 'location', 'date', 'number', 'relationship'];

    public static array $headingComponentCodes = ['a', 'b', 'c', 'd', 'n'];

    const MAIN_ENTRY= '110';
    const ADDED_ENTRY = '710';

    /**
     * @param Record $record
     * @return static[]
     */
    public static function get(Record $record): array
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

    public function getType(): string
    {
        return $this->getTag();
    }

    /**
     * Return the Authority record control number
     */
    public function getId(): ?string
    {
        // preg_match('/^\((.+)\)(.+)$/', $sf0->getData(), $matches);
        return $this->sf('0');
    }

    public function getName(): ?string
    {
        return $this->sf('a');
    }

    public function getSubordinateUnit(): ?string
    {
        return $this->sf('b');
    }

    public function getLocation(): ?string
    {
        return $this->sf('c');
    }

    public function getDate(): ?string
    {
        return $this->sf('d');
    }

    public function getNumber(): ?string
    {
        return $this->sf('n');
    }

    public function getRelationship(): ?string
    {
        return $this->sf('4');
    }

    public function __toString(): string
    {
        $out = [];
        foreach ($this->getSubfields() as $sf) {
            if (in_array($sf->getCode(), static::$headingComponentCodes)) {
                $out[] = $sf->getData();
            }
        }
        return str_replace('/  /', ' ', implode(' ', $out));
    }
}
