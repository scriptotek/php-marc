<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class ControlField extends Field implements FieldInterface
{
    public static function get(Record $record, $tag)
    {
        return parent::makeFieldObject($record, $tag);
    }

    public function __toString()
    {
        return $this->field->getData();
    }
}
