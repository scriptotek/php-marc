<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Person extends Field implements FieldInterface, AuthorityInterface
{
    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */
    public array $properties = ['type', 'vocabulary', 'name', 'titulation', 'dates', 'id', 'relator_term', 'relationship'];

    public static string $formatWithDate = '{name} ({dates})';
    public static array $termComponentCodes = ['a', 'b', 'x', 'y', 'z'];

    public const MAIN_ENTRY= '100';
    public const ADDED_ENTRY = '700';

    /**
     * @param Record $record
     * @return static[]
     */
    public static function get(Record $record): array
    {
        $objs = [];

        foreach (static::makeFieldObjects($record, Person::MAIN_ENTRY) as $obj) {
            $objs[] = $obj;
        }

        foreach (static::makeFieldObjects($record, Person::ADDED_ENTRY) as $obj) {
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

    public function getTitulation(): ?string
    {
        return $this->sf('c');
    }

    public function getDates(): ?string
    {
        return $this->sf('d');
    }

    public function getRelatorTerm(): ?string
    {
        return $this->sf('e');
    }

    public function getRelationship(): ?string
    {
        return $this->sf('4');
    }

    public function __toString(): string
    {
        $tpl = $this->getDates() ? self::$formatWithDate : '{name}';

        return str_replace(
            ['{name}', '{dates}'],
            [$this->clean($this->getName()), $this->clean($this->getDates())],
            $tpl
        );
    }
}
