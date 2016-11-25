<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Title extends Field implements FieldInterface
{
    public static function get(Record $record)
    {
        return parent::makeFieldObject($record, '245');
    }

    /**
     * Returns the string representation $a, $b, $n and $p).
     *  - Joins the subfields by colon if no ISBD marker present at the end of $a
     *  - Removes trailing '/'
     *  - See tests/TitleFieldTest.php for more info.
     */
    public function __toString()
    {
        // $a is not repeated
        $a = $this->field->getSubfield('a');
        $title = $a ? trim($a->getData()) : '';

        // $b is not repeated
        $b = $this->field->getSubfield('b');
        if ($b) {
            if (!in_array(substr($title, strlen($title) - 1), [':', ';', '=', '.'])) {
                // Add colon if no ISBD marker present ("British style")
                $title .= ' :';
            }
            $title .= ' ' . trim($b->getData());
        }

        // Part number and title can be repeated
        foreach ($this->field->getSubfields() as $sf) {
            if (in_array($sf->getCode(), ['n', 'p'])) {
                $title .= ' ' . $sf->getData();
            }
        }

        // Strip off 'Statement of responsibility' marker
        // I would like to strip of the final dot as well, but we can't really distinguish
        // between dot as an ISBD marker and dot as part of the actual title
        // (for instance when the title is an abbreviation)
        $title = rtrim($title, ' /');

        return $title;
    }
}
