<?php

namespace Scriptotek\Marc\Fields;

class Title extends Field implements FieldInterface
{
    /**
     * See tests/TitleFieldTest.php for more info.
     */
    public function __toString()
    {
        // $a is not repeated
        $a = $this->field->getSubfield('a');
        if (!$a) {
            return;
        }
        $title = trim($a->getData());

        // $b is not repeated
        $b = $this->field->getSubfield('b');
        if ($b) {
            if (!in_array(substr($title, strlen($title) - 1), array(':', ';', '=', '.'))) {
                // Add colon if no ISBD marker present ("British style")
                $title .= ' :';
            }
            $title .= ' ' . trim($b->getData());
        }

        // Part number and title can be repeated
        foreach ($this->field->getSubfields() as $sf) {
            if (in_array($sf->getCode(), array('n', 'p'))) {
                $title .= ' ' . $sf->getData();
            }
        }

        // Strip off 'Statement of responsibility' marker
        // I would like to strip of the final dot as well, but we can't really distinguish
        // between dot as an ISBD marker and dot as part of the actual title
        // (for instance when the title is an abbreviation)
        $title = rtrim($title, ' /');

        // TODO: Handle more subfields like $k, $f and $g ?? Probably should...

        return $title;
    }
}
