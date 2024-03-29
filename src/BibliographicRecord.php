<?php

namespace Scriptotek\Marc;

use Scriptotek\Marc\Exceptions\UnknownRecordType;
use Scriptotek\Marc\Fields\Classification;
use Scriptotek\Marc\Fields\Edition;
use Scriptotek\Marc\Fields\Isbn;
use Scriptotek\Marc\Fields\Person;
use Scriptotek\Marc\Fields\Publisher;
use Scriptotek\Marc\Fields\Subject;
use Scriptotek\Marc\Fields\SubjectInterface;
use Scriptotek\Marc\Fields\Title;

class BibliographicRecord extends Record
{
    /**
     * @var string[] List of properties to be included when serializing the record using the `toArray()` method.
     */
    public array $properties = [
        'id', 'isbns', 'title', 'publisher', 'pub_year', 'edition',  'creators',
        'subjects', 'classifications', 'toc', 'summary', 'part_of'
    ];

    /**
     * Get the descriptive cataloging form value from LDR/18. Returns any of
     * the constants Marc21::NON_ISBD, Marc21::AACR2, Marc21::ISBD_PUNCTUATION_OMITTED,
     * Marc21::ISBD_PUNCTUATION_INCLUDED, Marc21::NON_ISBD_PUNCTUATION_OMITTED
     * or Marc21::UNKNOWN_CATALOGING_FORM.
     *
     * @return string
     */
    public function getCatalogingForm(): string
    {
        $leader = $this->record->getLeader();
        return substr($leader, 18, 1);
    }

    /*************************************************************************
     * Helper methods for specific fields. Each of these are supported by
     * a class in src/Fields/
     *************************************************************************/

    /**
     * Get an array of the 020 fields as `Isbn` objects.
     *
     * @return Isbn[]
     */
    public function getIsbns(): array
    {
        return Isbn::get($this);
    }

    /**
     * Get the 245 field as a `Title` object. Returns null if no such field was found.
     *
     * @return Title|null
     */
    public function getTitle(): ?Title
    {
        return Title::get($this);
    }

    /**
     * Get 250 as an `Edition` object. Returns null if no such field was found.
     *
     * @return Edition|null
     */
    public function getEdition(): ?Edition
    {
        return Edition::get($this);
    }

    /**
     * Get 26[04]$b as a `Publisher` object. Returns null if no such field was found.
     *
     * @return Publisher|null
     */
    public function getPublisher(): ?Publisher
    {
        return Publisher::get($this);
    }

    /**
     * Get the publication year from 008
     *
     * @return string
     */
    public function getPubYear(): string
    {
        return substr($this->query('008')->text(), 7, 4);
    }

    /**
     * Get TOC
     *
     * @return array|null
     */
    public function getToc(): ?array
    {
        $field = $this->getField('505');
        if ($field) {
            if ($field->getIndicator(2) === '0') {
                // Enhanced
                $out = [
                    'text' => [],
                ];
                foreach ($field->getSubfields('t') as $sf) {
                    $out['text'][] = $sf->getData();
                }
                $out['text'] = implode("\n", $out['text']);

                return $out;
            } else {
                // Basic
                return $field->mapSubFields([
                   'a' => 'text',
                ]);
            }
        }
        return null;
    }

    /**
     * Get Summary
     *
     * @return array|null
     */
    public function getSummary(): array|null
    {
        $field = $this->getField('520');
        if ($field) {
            return $field->mapSubFields([
               'a' => 'text',
               'c' => 'assigning_source',
            ]);
        }
        return null;
    }

    /**
     * Get an array of the 6XX fields as `SubjectInterface` objects, optionally
     * filtered by vocabulary and/or tag.
     *
     * @param string|null $vocabulary
     * @param string|string[]|null $tag
     * @return SubjectInterface[]
     */
    public function getSubjects(string $vocabulary = null, array|string $tag = null): array
    {
        $tag = is_null($tag) ? [] : (is_array($tag) ? $tag : [$tag]);

        return array_values(array_filter(Subject::get($this), function (SubjectInterface $subject) use ($vocabulary, $tag) {
            $a = is_null($vocabulary) || $vocabulary == $subject->getVocabulary();
            $b = empty($tag) || in_array($subject->getType(), $tag);

            return $a && $b;
        }));
    }

    /**
     * Get an array of the 080, 082, 083, 084 fields as `Classification` objects, optionally
     * filtered by scheme and/or tag.
     *
     * @param string|null $scheme
     * @return Classification[]
     */
    public function getClassifications(string $scheme = null): array
    {
        return array_values(array_filter(Classification::get($this), function ($classifications) use ($scheme) {
            $a = is_null($scheme) || $scheme == $classifications->getScheme();

            return $a;
        }));
    }

    /**
     * Get an array of the 100 and 700 fields as `Person` objects, optionally
     * filtered by tag.
     *
     * @param string|string[]|null $tag
     * @return Person[]
     */
    public function getCreators(array|string $tag = null): array
    {
        $tag = is_null($tag) ? [] : (is_array($tag) ? $tag : [$tag]);

        return array_values(array_filter(Person::get($this), function (Person $person) use ($tag) {
            return empty($tag) || in_array($person->getType(), $tag);
        }));
    }

    /**
     * Get part of from 773.
     *
     * @return array|null
     */
    public function getPartOf(): ?array
    {
        $field = $this->getField('773');
        if ($field) {
            return $field->mapSubFields([
                'i' => 'relationship',
                't' => 'title',
                'x' => 'issn',
                'w' => 'id',
                'v' => 'volume',
            ]);
        }
        return null;
    }
}
