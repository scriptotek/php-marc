<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class SeeAlso extends Field implements \JsonSerializable
{
    /**
     * @param Record $record
     * @return array
     */
    public static function get(Record $record): array
    {
        $seeAlsos = [];

        $classMap = [
            '500' => Person::class,
            '510' => Corporation::class,
            // TODO: Add more classes
            '550' => Subject::class,
        ];

        foreach ($record->getFields('5..', true) as $field) {
            $tag = $field->getTag();
            if (isset($classMap[$tag])) {
                $seeAlsos[] = new $classMap[$tag]($field->getField());
            }
        }

        return $seeAlsos;
    }
}
