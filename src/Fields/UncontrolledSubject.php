<?php

namespace Scriptotek\Marc\Fields;

class UncontrolledSubject extends Subfield implements SubjectFieldInterface, \JsonSerializable
{
    public function getType(): string
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
    public function getId(): ?string
    {
        return null;
    }

    public function getTerm(): string
    {
        return $this->subfield->getData();
    }

    public function getParts(): array
    {
        return [$this->getTerm()];
    }

    public function __toString(): string
    {
        return $this->getTerm();
    }

    public function jsonSerialize(): string|array
    {
        return [
            'type' => $this->getType(),
            'term' => $this->getTerm(),
        ];
    }
}
