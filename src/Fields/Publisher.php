<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Publisher extends Field implements FieldInterface
{
    public static function get(Record $record)
    {
        foreach ($record->query('264{$b}') as $field) {
            return new static($field->getField());
        }
        foreach ($record->query('260{$b}') as $field) {
            return new static($field->getField());
        }
    }

    public function __toString()
    {
        return $this->sf('b');
    }
}
