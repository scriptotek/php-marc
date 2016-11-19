<?php

namespace Scriptotek\Marc;

use File_MARC_Record;
use File_MARC_Reference;
use Scriptotek\Marc\Exceptions\RecordNotFound;

class Record
{
    protected $record;
    protected $factory;

    /**
     * Record constructor.
     * @param File_MARC_Record $record
     * @param Factory|null $factory
     */
    public function __construct(File_MARC_Record $record, Factory $factory = null)
    {
        $this->record = $record;
        $this->factory = $factory ?: new Factory();
    }

    /**
     * Returns the first record found in the file $filename, or null if no records found.
     *
     * @param $filename
     * @return null|Collection
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
     * @return null|Collection
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
     * Determine if record is a bibliographic, authority or holdings record
     *************************************************************************/

    /**
     * Get the record type based on the value of LDR/6. Returns any of
     * the Marc21::BIBLIOGRAPHIC, Marc21::AUTHORITY or Marc21::HOLDINGS
     * constants.
     *
     * @return string
     * @throws ErrorException
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
                throw new \ErrorException('Unknown record type.');
        }
    }

    /*************************************************************************
     * Helper methods for specific fields. Each of these are supported by
     * a class in src/Fields/
     *************************************************************************/

    public function getIsbns()
    {
        $fields = array();
        foreach ($this->record->getFields('020') as $field) {
            $fields[] = $this->makeField('Isbn', $field);
        }

        return $fields;
    }

    public function getSubjects($vocabulary = null, $type = null)
    {
        $fields = array();
        $saf = array(
            '600' => 'person',         # Subject Added Entry - Personal name
            '610' => 'corporation',    # Subject Added Entry - Corporate name
            '611' => 'meeting',        # Subject Added Entry - Meeting name
            '630' => 'uniform-title',  # Subject Added Entry - Uniform title
            '648' => 'time',           # Subject Added Entry - Chronological Term
            '650' => 'topic',          # Subject Added Entry - Topical Term
            '651' => 'place',          # Subject Added Entry - Geographic Name
            '653' => 'uncontrolled',   # Index Term - Uncontrolled
             // 654 :  Subject Added Entry - Faceted Topical Terms
            '655' => 'form',           # Index Term - Genre/Form
            '656' => 'occupation',     # Index Term - Occupation
             // 657 - Index Term - Function
             // 658 - Index Term - Curriculum Objective
             // 662 - Subject Added Entry - Hierarchical Place Name
             // 69X - Local Subject Access Fields
        );
        foreach ($saf as $k => $v) {
            foreach ($this->record->getFields($k) as $field) { // or 655, 648, etc.
                $f = $this->makeField('Subject', $field);
                $f->type = $v;
                $fields[] = $f;
            }
        }

        return array_filter($fields, function ($s) use ($vocabulary, $type) {
            $a = is_null($vocabulary) || $vocabulary == $s->vocabulary;
            $b = is_null($type) || $type == $s->type;

            return $a && $b;
        });
    }

    public function getTitle()
    {
        $field = $this->record->getField('245');

        return $field ? $this->makeField('Title', $field) : null;
    }

    public function getId()
    {
        $field = $this->record->getField('001');

        return $field ? $this->makeField('ControlField', $field) : null;
    }

    /*************************************************************************
     * Support methods
     *************************************************************************/

    protected function makeField($model, \File_MARC_Field $field)
    {
        return $this->factory->makeField($model, $field);
    }

    /**
     * @param string $spec  The MARCspec string
     * @return QueryResult
     */
    public function query($spec)
    {
        return new QueryResult(new File_MARC_Reference($spec, $this->record));
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->record, $name), $args);
    }

    public function __get($key)
    {
        $method = 'get' . ucfirst($key);
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        }
    }

    public function __toString()
    {
        return strval($this->record);
    }
}
