<?php

namespace Scriptotek\Marc\Fields;

class Title extends Field {

    public function __toString()
    {
        $a = $this->field->getSubfield('a');
        if (!$a) {
            return null;
        }
        $a = $a->getData();

        // TODO:
        // foreach ($this->field->getSubfields('b') as $b) {

        // }

        return $a;
    }

}