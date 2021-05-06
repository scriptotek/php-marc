<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Edition extends Field implements FieldInterface
{
    public static function get(Record $record)
    {
        foreach ($record->query('250{$a}') as $field) {
            return new static($field->getField());
        }
    }

    public function __toString()
    {
        return $this->sf('a');
    }
}
