<?php

namespace Scriptotek\Marc;

use File_MARC_Record;
use File_MARC_Reference;
use Scriptotek\Marc\Exceptions\RecordNotFound;
use Scriptotek\Marc\Exceptions\UnknownRecordType;
use Scriptotek\Marc\Fields\ControlField;
use Scriptotek\Marc\Fields\Isbn;
use Scriptotek\Marc\Fields\Subject;
use Scriptotek\Marc\Fields\SubjectInterface;
use Scriptotek\Marc\Fields\Title;

/**
 * The MARC record wrapper.
 *
 * We wrap File_MARC_Record rather than extend it because we would otherwise
 * have to copy or rewrite the functionality in the `next()` and `_decode()`
 * methods of File_MARC and File_MARCXML, which are hard-wired to call
 * `new File_MARC_Record()`. The down-side of the wrapping approach is that we
 * break static code analysis and IDE code hinting.
 */
class Record
{
    protected $record;

    /**
     * Record constructor.
     * @param File_MARC_Record $record
     */
    public function __construct(File_MARC_Record $record)
    {
        $this->record = $record;
    }

    /*************************************************************************
     * Data loading
     *************************************************************************/

    /**
     * Returns the first record found in the file $filename, or null if no records found.
     *
     * @param $filename
     * @return Record
     * @throws RecordNotFound
     */
    public static function fromFile($filename)
    {
        $records = Collection::fromFile($filename)->toArray();

        if (!count($records)) {
            throw new RecordNotFound();
        }

        return $records[0];
    }

    /**
     * Returns the first record found in the string $data, or null if no records found.
     *
     * @param $data
     * @return Record
     * @throws RecordNotFound
     */
    public static function fromString($data)
    {
        $records = Collection::fromString($data)->toArray();

        if (!count($records)) {
            throw new RecordNotFound();
        }

        return $records[0];
    }

    /*************************************************************************
     * Query
     *************************************************************************/

    /**
     * @param string $spec  The MARCspec string
     * @return QueryResult
     */
    public function query($spec)
    {
        return new QueryResult(new File_MARC_Reference($spec, $this->record));
    }

    /*************************************************************************
     * Helper methods for LDR
     *************************************************************************/

    /**
     * Get the record type based on the value of LDR/6. Returns any of
     * the Marc21::BIBLIOGRAPHIC, Marc21::AUTHORITY or Marc21::HOLDINGS
     * constants.
     *
     * @return string
     * @throws UnknownRecordType
     */
    public function getType()
    {
        $leader = $this->record->getLeader();
        $recordType = substr($leader, 6, 1);

        switch ($recordType) {
            case 'a': // Language material
            case 'c': // Notated music
            case 'd': // Manuscript notated music
            case 'e': // Cartographic material
            case 'f': // Manuscript cartographic material
            case 'g': // Projected medium
            case 'i': // Nonmusical sound recording
            case 'j': // Musical sound recording
            case 'k': // Two-dimensional nonprojectable graphic
            case 'm': // Computer file
            case 'o': // Kit
            case 'p': // Mixed materials
            case 'r': // Three-dimensional artifact or naturally occurring object
            case 't': // Manuscript language material
                return Marc21::BIBLIOGRAPHIC;
            case 'z':
                return Marc21::AUTHORITY;
            case 'u': // Unknown
            case 'v': // Multipart item holdings
            case 'x': // Single-part item holdings
            case 'y': // Serial item holdings
                return Marc21::HOLDINGS;
            default:
                throw new UnknownRecordType();
        }
    }

    /**
     * Get the descriptive cataloging form value from LDR/18. Returns any of
     * the constants Marc21::NON_ISBD, Marc21::AACR2, Marc21::ISBD_PUNCTUATION_OMITTED,
     * Marc21::ISBD_PUNCTUATION_INCLUDED, Marc21::NON_ISBD_PUNCTUATION_OMITTED
     * or Marc21::UNKNOWN_CATALOGING_FORM.
     *
     * @return string
     * @throws UnknownRecordType
     */
    public function getCatalogingForm()
    {
        $leader = $this->record->getLeader();
        return substr($leader, 18, 1);
    }

    /*************************************************************************
     * Helper methods for specific fields. Each of these are supported by
     * a class in src/Fields/
     *************************************************************************/

    /**
     * Get the value of the 001 field as a `ControlField` object.
     *
     * @return ControlField
     */
    public function getId()
    {
        return ControlField::get($this, '001');
    }

    /**
     * Get an array of the 020 fields as `Isbn` objects.
     *
     * @return Isbn[]
     */
    public function getIsbns()
    {
        return Isbn::get($this);
    }

    /**
     * Get the 245 field as a `Title` object. Returns null if no such field was found.
     *
     * @return Title
     */
    public function getTitle()
    {
        return Title::get($this);
    }

    /**
     * Get an array of the 6XX fields as `SubjectInterface` objects, optionally
     * filtered by vocabulary and/or tag.
     *
     * @param string $vocabulary
     * @param string|string[] $tag
     * @return SubjectInterface[]
     */
    public function getSubjects($vocabulary = null, $tag = null)
    {
        $tag = is_null($tag) ? [] : (is_array($tag) ? $tag : [$tag]);

        return array_values(array_filter(Subject::get($this), function (SubjectInterface $subject) use ($vocabulary, $tag) {
            $a = is_null($vocabulary) || $vocabulary == $subject->getVocabulary();
            $b = empty($tag) || in_array($subject->getType(), $tag);

            return $a && $b;
        }));
    }

    /*************************************************************************
     * Support methods
     *************************************************************************/

    public function __call($name, $args)
    {
        return call_user_func_array([$this->record, $name], $args);
    }

    public function __get($key)
    {
        $method = 'get' . ucfirst($key);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
    }

    public function __toString()
    {
        return strval($this->record);
    }
}
