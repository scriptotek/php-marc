<?php

namespace Scriptotek\Marc\Fields;

interface SubjectInterface
{
    public function getType();

    public function getVocabulary();

    public function getControlNumber();

    public function getParts();

    public function getTerm();

    public function __toString();

    public function jsonSerialize();
}
