<?php

namespace Scriptotek\Marc\Fields;

class UncontrolledSubject extends Subfield implements SubjectInterface
{
    public function getType()
    {
        return Subject::UNCONTROLLED_INDEX_TERM;
    }

    public function getVocabulary()
    {
        return null;
    }

    /**
     * Return the Authority record control number
     */
    public function getControlNumber()
    {
        return null;
    }

    public function getTerm()
    {
        return $this->subfield->getData();
    }

    public function getParts()
    {
        return [$this->getTerm()];
    }

    public function __toString()
    {
        return $this->getTerm();
    }

    public function jsonSerialize()
    {
        return [
            'type' => $this->getType(),
            'vocabulary' => $this->getVocabulary(),
            'id' => $this->getControlNumber(),
            'term' => (string) $this,
        ];
    }
}
