<?php

namespace Scriptotek\Marc;

use File_MARC_Record;
use File_MARC_Reference;
use Scriptotek\Marc\Exceptions\RecordNotFound;
use Scriptotek\Marc\Exceptions\UnknownRecordType;
use Scriptotek\Marc\Fields\ControlField;

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
     * @return BibliographicRecord|HoldingsRecord|AuthorityRecord
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
     * @return BibliographicRecord|HoldingsRecord|AuthorityRecord
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
        return Collection::getRecordType($this->record);
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
