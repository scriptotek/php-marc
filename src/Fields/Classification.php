<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Classification extends Subfield implements \JsonSerializable
{
    use SerializableField;

    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */

    public $properties = ['scheme', 'number', 'heading', 'edition', 'assigning_agency', 'id'];

    public static function get(Record $record)
    {
        $out = [];
        foreach ($record->getFields('08[0234]', true) as $field) {
            foreach ($field->getSubfields('a') as $sfa) {
                $out[] = new Classification($field, $sfa);
            }
        }
        return $out;
    }

    public function getScheme()
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

    public function getEdition()
    {
        if (in_array($this->field->getTag(), ['080', '082', '083'])) {
            return $this->field->sf('2');
        }
    }

    public function getNumber()
    {
        return $this->subfield->getData();
    }

    public function getAssigningVocabulary()
    {
        return $this->field->sf('q');
    }

    public function getId()
    {
        // NOTE: Both $a and $0 are repeatable, but there's no examples of how that would look like.
        //       I'm guessing that they would alternate: $a ... $0 ... $a ... $0 ... , but not sure.
        return $this->field->sf('0');
    }

    public function __toString()
    {
        return $this->getNumber();
    }
}
