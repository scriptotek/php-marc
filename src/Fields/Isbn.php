<?php

namespace Scriptotek\Marc\Fields;

class Isbn extends Field implements FieldInterface
{
    public function __toString()
    {
        $a = $this->field->getSubfield('a');
        if (!$a) {
            return '';
        }

        return $a->getData();
    }
}
