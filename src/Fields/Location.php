<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

/**
 * Location (852) field
 *
 * @property string location          $a : Institution or person holding the item or from which access is given.
 * @property string sublocation       $b : Sublocation or collection - Specific department, library, etc., within the holding organization in which the item is located or from which it is available.
 * @property string shelvingLocation  $c : Description of the shelving location of the item within the collection of the holding organization.
 * @property string callCode          Call number, including prefix and suffix.
 * @property string nonpublicNote     $x
 * @property string publicNote        $z
 *
 */
class Location extends Field implements FieldInterface
{
    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */
    public $properties = ['location', 'sublocation', 'shelvinglocation', 'callcode'];

    public static function get(Record $record)
    {
        return parent::makeFieldObjects($record, '852');
    }

    public function getLocation()
    {
        return $this->sf('a');
    }

    public function getSublocation()
    {
        return $this->sf('b');
    }

    public function getShelvinglocation()
    {
        return $this->sf('c');
    }

    public function getCallcode()
    {
        return $this->toString([
            'h',    // Classification part (NR)
            'i',    // Item part (R)
            'j',    // Shelving control number (NR)
            'k',    // Call number prefix
            'l',    // Shelving form of title
            'm',    // Call number suffix
        ]);
    }

    public function getNonpublicNote()
    {
        return $this->sf('x');
    }

    public function getPublicNote()
    {
        return $this->sf('x');
    }

    public function __toString()
    {
        return $this->toString(['a', 'b', 'c', 'h', 'i', 'j', 'k', 'l', 'm']);
    }
}
