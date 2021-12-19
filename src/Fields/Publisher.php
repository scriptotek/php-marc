<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Publisher extends Field implements FieldInterface
{
    /**
     * @param Record $record
     * @return static|null
     */
    public static function get(Record $record): ?static
    {
        foreach ($record->query('264{$b}') as $field) {
            return new static($field->getField());
        }
        foreach ($record->query('260{$b}') as $field) {
            return new static($field->getField());
        }
        return null;
    }

    public function __toString(): string
    {
        return $this->sf('b');
    }
}
