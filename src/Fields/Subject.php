<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Subject extends Field implements SubjectInterface
{
    /**
     * @var array List of properties to be included when serializing the record using the `toArray()` method.
     */
    public array $properties = ['type', 'vocabulary', 'term', 'id'];

    public static string $glue = ' : ';
    public static bool $chopPunctuation = true;
    public static array $termComponentCodes = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
        'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z',
    ];

    public const PERSONAL_NAME = '600';
    public const CORPORATION_NAME = '601';
    public const MEETING_NAME = '611';
    public const UNIFORM_TITLE = '630';
    public const NAMED_EVENT = '647';
    public const CHRONOLOGICAL_TERM = '648';
    public const TOPICAL_TERM = '650';
    public const GEOGRAPHIC_NAME = '651';
    public const UNCONTROLLED_INDEX_TERM = '653';
    public const FACETED_TOPICAL_TERM = '654';
    public const GENRE_FORM = '655';
    public const OCCUPATION = '656';
    public const FUNCTION_TERM = '657';
    public const CURRICULUM_OBJECTIVE = '658';
    public const HIERARCHICAL_PLACE_NAME = '662';

    protected array $vocabularies = [
        '0' => 'lcsh',  // 0: Library of Congress Subject Headings
        '1' => 'lccsh', // 1: LC subject headings for children's literature
        '2' => 'mesh',  // 2: Medical Subject Headings
        '3' => 'atg',   // 3: National Agricultural Library subject authority file (?)
        // 4: Source not specified
        '5' => 'cash',  // 5: Canadian Subject Headings
        '6' => 'rvm',   // 6: Répertoire de vedettes-matière
        // 7: Source specified in subfield $2
    ];

    /**
     * @param Record $record
     * @return (UncontrolledSubject|Subject)[]
     */
    public static function get(Record $record): array
    {
        $subjects = [];

        foreach (static::makeFieldObjects($record, '6..', true) as $subject) {
            if ($subject->getTag() == '653') {
                foreach ($subject->getSubfields('a') as $sfa) {
                    $subjects[] = new UncontrolledSubject($subject, $sfa);
                }
            } else {
                $subjects[] = $subject;
            }
        }

        return $subjects;
    }

    public function getType(): string
    {
        return $this->getTag();
    }

    public function getVocabulary(): ?string
    {
        $ind2 = $this->field->getIndicator(2);
        $sf2 = $this->field->getSubfield('2');
        if (isset($this->vocabularies[$ind2])) {
            return $this->vocabularies[$ind2];
        }
        if ($sf2) {
            return $sf2->getData();
        }

        return null;
    }

    /**
     * Return the Authority record control number
     */
    public function getId(): ?string
    {
        return $this->sf('0');
    }

    public function getParts(): array
    {
        return $this->getSubfields('[' . implode('', self::$termComponentCodes) . ']', true);
    }

    public function getTerm(): ?string
    {
        return $this->toString(self::$termComponentCodes);
    }

    public function __toString(): string
    {
        return $this->getTerm();
    }
}
