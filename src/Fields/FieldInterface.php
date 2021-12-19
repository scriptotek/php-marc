<?php

namespace Scriptotek\Marc\Fields;

use File_MARC_Field;

interface FieldInterface
{
    public function __construct(File_MARC_Field $field);

    public function __toString(): string;

    public function jsonSerialize();
}
