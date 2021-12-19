<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class ControlField extends Field implements FieldInterface
{
    public static function get(Record $record, $tag): static
    {
        return static::makeFieldObject($record, $tag);
    }

    public function __toString(): string
    {
        return $this->field->getData();
    }
}
