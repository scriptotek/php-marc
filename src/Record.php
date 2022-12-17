<?php

namespace Scriptotek\Marc;

use File_MARC_Field;
use File_MARC_Record;
use File_MARC_Reference;
use JsonSerializable;
use Scriptotek\Marc\Exceptions\RecordNotFound;
use Scriptotek\Marc\Exceptions\UnknownRecordType;
use Scriptotek\Marc\Fields\ControlField;
use Scriptotek\Marc\Fields\Field;
use SimpleXMLElement;

/**
 * The MARC record wrapper.
 *
 * We wrap File_MARC_Record rather than extend it because we would otherwise
 * have to copy or rewrite the functionality in the `next()` and `_decode()`
 * methods of File_MARC and File_MARCXML, which are hard-wired to call
 * `new File_MARC_Record()`. The down-side of the wrapping approach is that we
 * impede static code analysis and IDE code hinting.
 *
 * Methods on the wrapped record that are not implemented here may be accessed
 * using magic method calls, or through `getRecord()`. Method tags are included
 * below to aid IDE code hinting, but using the getter will give you better code
 * hinting and documentation.
 *
 * @method string getLeader()
 * @method string setLeader(string $leader)
 * @method File_MARC_Field appendField(File_MARC_Field $new_field)
 * @method File_MARC_Field prependField(File_MARC_Field $new_field)
 * @method File_MARC_Field insertField(File_MARC_Field $new_field, File_MARC_Field $existing_field, bool $before = false)
 * @method bool setLeaderLengths(int $record_length, int $base_address)
 * @method int deleteFields(string $tag, bool $pcre = null)
 * @method addWarning(string $warning)
 * @method string toRaw()
 * @method string toJSON()
 * @method string toJSONHash
 * @method string toXML(string $encoding = "UTF-8", bool $indent = true, bool $single = true)
 *
 * @property string id
 * @property string type
 */
class Record implements JsonSerializable
{
    use MagicAccess;

    /**
     * The record that is being wrapped.
     *
     * @var \File_MARC_Record
     */
    protected $record;

    /**
     * @var string[] List of properties to be included when serializing the record using the `toArray()` method.
     */
    public array $properties = ['id'];

    /**
     * Record constructor.
     * @param File_MARC_Record $record
     */
    public function __construct(File_MARC_Record $record)
    {
        $this->record = $record;
    }

    /**
     * Get the wrapped record.
     *
     * @return \File_MARC_Record
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Find and wrap the specified MARC field.
     *
     * @param string $spec
     *   The tag name.
     * @param bool $pcre
     *   If true, match as a regular expression.
     *
     * @return \Scriptotek\Marc\Fields\Field|null
     *   A wrapped field, or NULL if not found.
     */
    public function getField($spec = null, $pcre = null)
    {
        $q = $this->record->getField($spec, $pcre);
        if ($q) {
            return new Field($q);
        }
        return null;
    }

    /**
     * Find and wrap the specified MARC fields.
     *
     * @param string $spec
     *   The tag name.
     * @param bool $pcre
     *   If true, match as a regular expression.
     *
     * @return \Scriptotek\Marc\Fields\Field[]
     *   An array of wrapped fields.
     */
    public function getFields($spec = null, $pcre = null)
    {
        return array_values(array_map(function (File_MARC_Field $field) {
            return new Field($field);
        }, $this->record->getFields($spec, $pcre)));
    }

    /*************************************************************************
     * Data loading
     *************************************************************************/

    /**
     * Returns the first record found in the file $filename.
     *
     * @param string $filename
     *   The name of the file containing the MARC records.
     * @return BibliographicRecord|HoldingsRecord|AuthorityRecord
     *   A wrapped MARC record.
     * @throws RecordNotFound
     *   When the file does not contain a MARC record.
     */
    public static function fromFile($filename)
    {
        return Collection::fromFile($filename)->first();
    }

    /**
     * Returns the first record found in the string $data.
     *
     * @param string $data
     *   The string in which to look for MARC records.
     * @return BibliographicRecord|HoldingsRecord|AuthorityRecord
     *   A wrapped MARC record.
     * @throws RecordNotFound
     *   When the string does not contain a MARC record.
     */
    public static function fromString($data)
    {
        return Collection::fromString($data)->first();
    }

    /**
     * Returns the first record found in the SimpleXMLElement object
     *
     * @param SimpleXMLElement $element
     *   The SimpleXMLElement object in which to look for MARC records.
     * @return BibliographicRecord|HoldingsRecord|AuthorityRecord
     *   A wrapped MARC record.
     * @throws RecordNotFound
     *   When the object does not contain a MARC record.
     */
    public static function fromSimpleXMLElement(SimpleXMLElement $element)
    {
        return Collection::fromSimpleXMLElement($element)->first();
    }

    /*************************************************************************
     * Query
     *************************************************************************/

    /**
     * @param string $spec
     *   The MARCspec string
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
     * Get the record type based on the value of LDR/6.
     *
     * @return string
     *   Any of the Marc21::BIBLIOGRAPHIC, Marc21::AUTHORITY or Marc21::HOLDINGS
     *   constants.
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

    /**
     * Convert the MARC record into an array structure fit for `json_encode`.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $o = [];
        foreach ($this->properties as $prop) {
            $value = $this->$prop;
            if (is_null($value)) {
                $o[$prop] = $value;
            } elseif (is_array($value)) {
                $t = [];
                foreach ($value as $k => $v) {
                    if (is_object($v)) {
                        $t[$k] = $v->jsonSerialize();
                    } else {
                        $t[$k] = (string) $v;
                    }
                }
                $o[$prop] = $t;
            } elseif (is_object($value)) {
                $o[$prop] = $value->jsonSerialize();
            } else {
                $o[$prop] = $value;
            }
        }
        return $o;
    }

    /**
     * Delegate all unknown method calls to the wrapped record.
     *
     * @param string $name
     *   The name of the method being called.
     * @param array $args
     *   The arguments being passed to the method.
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array([$this->record, $name], $args);
    }

    /**
     * Get a string representation of this record.
     *
     * @return string
     */
    public function __toString()
    {
        return strval($this->record);
    }
}
