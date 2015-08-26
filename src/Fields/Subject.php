<?php

namespace Scriptotek\Marc\Fields;

class Subject extends Field implements FieldInterface
{
    public static $glue = ' : ';

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

    public function __toString()
    {
        $parts = array();
        foreach ($this->field->getSubfields() as $c) {
            if (in_array($c->getCode(), array('a', 'b', 'x', 'y', 'z'))) {
                $parts[] = $c->getData();
            }
        }
        return implode(Subject::$glue, $parts);
    }
}
