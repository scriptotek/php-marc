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

    public function getShelvingLocation()
    {
        return $this->sf('c');
    }

    public function getCallCode()
    {
        return $this->toString([
            'k',    // Call number prefix
            'l',    // Shelving form of title
            'h',    // Classification portion of the call number
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
        return $this->toString(['a', 'b', 'c', 'k', 'l', 'h', 'm']);
    }

}
