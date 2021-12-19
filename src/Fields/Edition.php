<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Edition extends Field implements FieldInterface
{
    public static function get(Record $record): ?static
    {
        foreach ($record->query('250{$a}') as $field) {
            return new static($field->getField());
        }
        return null;
    }

    public function __toString(): string
    {
        return $this->sf('a');
    }
}
