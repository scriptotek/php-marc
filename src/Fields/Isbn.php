<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Isbn extends Field implements FieldInterface
{
    public function __toString(): string
    {
        return $this->sf('a', '');
    }

    /**
     * @param Record $record
     * @return static[]
     */
    public static function get(Record $record): array
    {
        return parent::makeFieldObjects($record, '020');
    }
}
