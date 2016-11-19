<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Subject extends Field implements FieldInterface
{
    public static $glue = ' : ';

    const PERSONAL_NAME = '600';
    const CORPORATION_NAME = '601';
    const MEETING_NAME = '611';
    const UNIFORM_TITLE = '630';
    const NAMED_EVENT = '647';
    const CHRONOLOGICAL_TERM = '648';
    const TOPICAL_TERM = '650';
    const GEOGRAPHIC_NAME = '651';
    const UNCONTROLLED_INDEX_TERM = '653';
    const FACETED_TOPICAL_TERM = '654';
    const GENRE_FORM = '655';
    const OCCUPATION = '656';
    const FUNCTION_TERM = '657';
    const CURRICULUM_OBJECTIVE = '658';
    const HIERARCHICAL_PLACE_NAME = '662';

    protected $vocabularies = array(
        '0' => 'lcsh',  // 0: Library of Congress Subject Headings
        '1' => 'lccsh', // 1: LC subject headings for children's literature
        '2' => 'mesh',  // 2: Medical Subject Headings
        '3' => 'atg',   // 3: National Agricultural Library subject authority file (?)
        // 4: Source not specified
        '5' => 'cash',  // 5: Canadian Subject Headings
        '6' => 'rvm',   // 6: Répertoire de vedettes-matière
        // 7: Source specified in subfield $2
    );

    public static function get(Record $record) {
        return parent::makeFieldObjects($record, '6..', true);
    }

    public function getType()
    {
        return $this->getTag();
    }

    public function getVocabulary()
    {
        $ind2 = $this->field->getIndicator(2);
        $sf2 = $this->field->getSubfield('2');
        if (isset($this->vocabularies[$ind2])) {
            return $this->vocabularies[$ind2];
        }
        if ($sf2) {
            return $sf2->getData();
        }

        return null;
    }

    /**
     * Return the Authority record control number
     */
    public function getControlNumber()
    {
        $value = $this->field->getSubfield('0');
        return $value ?: null;
    }

    public function getParts()
    {
        $parts = array();
        foreach ($this->field->getSubfields() as $c) {
            if (in_array($c->getCode(), array('a', 'b', 'x', 'y', 'z'))) {
                $parts[] = $c->getData();
            }
        }
        return $parts;
    }

    public function __toString()
    {
        return implode(self::$glue, $this->getParts());
    }
}
