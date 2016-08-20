<?php

namespace Scriptotek\Marc;

class Record
{
    protected $record;
    protected $factory;

    public function __construct(\File_MARC_Record $record, Factory $factory = null)
    {
        $this->record = $record;
        $this->factory = $factory ?: new Factory();
    }

    public static function fromFile($filename)
    {
        $collection = Collection::fromFile($filename);

        return $collection->records->toArray()[0];
    }

    public static function fromString($data)
    {
        $collection = Collection::fromString($data);

        $recs = $collection->records->toArray();
        if (!count($recs)) {
            throw new \ErrorException('Record not found');
        }

        return $recs[0];
    }

    /*************************************************************************
     * Determine if record is a bibliographic, authority or holdings record
     *************************************************************************/

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
                return 'Bibliographic';
            case 'z':
                return 'Authority';
            case 'u': // Unknown
            case 'v': // Multipart item holdings
            case 'x': // Single-part item holdings
            case 'y': // Serial item holdings
                return 'Holdings';
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

    /*************************************************************************
     * Support methods
     *************************************************************************/

    protected function makeField($model, \File_MARC_Field $field)
    {
        return $this->factory->makeField($model, $field);
    }

    public function get($spec)
    {
        $reference = new \File_MARC_Reference($spec, $this->record);

        return $reference->content ?: array();
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
