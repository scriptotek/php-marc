<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Publisher extends Field implements FieldInterface
{
    public static function get(Record $record)
    {
        foreach ($record->getFields('264') as $field) {
            return new static($field->getField());
        }
        foreach ($record->getFields('260') as $field) {
            return new static($field->getField());
        }
    }

    public function __toString()
    {
        return $this->sf('b');
    }
}
