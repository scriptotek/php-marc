<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Isbn extends Field implements FieldInterface
{
    public function __toString()
    {
        return $this->sf('a', '');
    }

    public static function get(Record $record)
    {
        return parent::makeFieldObjects($record, '020');
    }
}
