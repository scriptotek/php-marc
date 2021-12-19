<?php

namespace Scriptotek\Marc\Fields;

trait SerializableField
{
    public function jsonSerialize(): array|string
    {
        if (count($this->properties)) {
            $o = [];
            foreach ($this->properties as $prop) {
                $value = $this->$prop;
                if (is_object($value)) {
                    $o[$prop] = $value->jsonSerialize();
                } elseif ($value) {
                    $o[$prop] = $value;
                }
            }
            return $o;
        }
        return (string) $this;
    }
}
