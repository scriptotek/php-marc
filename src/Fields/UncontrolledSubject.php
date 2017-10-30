<?php

namespace Scriptotek\Marc\Fields;

class UncontrolledSubject extends Subfield implements SubjectInterface
{
    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */
    public $properties = ['type', 'vocabulary', 'term', 'id'];

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
    public function getId()
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
}
