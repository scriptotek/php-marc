<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Classification extends Subfield implements \JsonSerializable
{
    use SerializableField;

    public const UDC = '080';
    public const DEWEY = '082';
    public const ADD_DEWEY = '082';
    public const OTHER_SCHEME = '084';

    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */

    public array $properties = ['scheme', 'number', 'heading', 'edition', 'assigning_agency', 'id'];

    public static function get(Record $record): array
    {
        $out = [];
        foreach ($record->getFields('08[0234]', true) as $field) {
            foreach ($field->getSubfields('a') as $sfa) {
                $out[] = new Classification($field, $sfa);
            }
        }
        return $out;
    }

    public function getType(): string
    {
        return $this->getTag();
    }

    public function getTag(): string
    {
        return $this->field->getTag();
    }

    public function getScheme(): string
    {
        $typeMap = [
            '080' => 'udc',
            '082' => 'ddc',
            '083' => 'ddc',
        ];

        $tag = $this->field->getTag();

        if ($tag == '084') {
            return $this->field->sf('2');
        }

        return $typeMap[$tag];
    }

    public function getEdition(): ?string
    {
        if (in_array($this->field->getTag(), ['080', '082', '083'])) {
            return $this->field->sf('2');
        }
        return null;
    }

    public function getNumber(): string
    {
        return $this->subfield->getData();
    }

    public function getAssigningVocabulary(): ?string
    {
        return $this->field->sf('q');
    }

    public function getId(): ?string
    {
        // NOTE: Both $a and $0 are repeatable, but there's no examples of how that would look like.
        //       I'm guessing that they would alternate: $a ... $0 ... $a ... $0 ... , but not sure.
        return $this->field->sf('0');
    }

    public function __toString(): string
    {
        return $this->getNumber();
    }
}
