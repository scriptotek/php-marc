<?php

namespace Scriptotek\Marc\Fields;

class ControlField extends Field implements FieldInterface
{
    public function __toString()
    {
        return $this->field->getData();
    }
}
