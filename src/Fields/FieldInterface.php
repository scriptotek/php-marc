<?php

namespace Scriptotek\Marc\Fields;

interface FieldInterface
{
    public function __toString(): string;

    public function jsonSerialize();
}
