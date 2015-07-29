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

    static function fromFile($filename)
    {
        $collection = Collection::fromFile($filename);
        return $collection->records->toArray()[0];
    }

    static function fromString($data)
    {
        $collection = Collection::fromString($data);
        return $collection->records->toArray()[0];
    }

    protected function makeField($model, \File_MARC_Field $field)
    {
        return $this->factory->makeField($model, $field);
    }

    public function getIsbns()
    {
        $fields = array();
        foreach ($this->record->getFields('020') as $field) {
            $fields[] = $this->makeField('Isbn', $field);
        }
        return $fields;
    }

    public function getSubjects($vocabulary=null, $type=null)
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
             // 656 - Index Term - Occupation (R) Full | Concise
             // 657 - Index Term - Function (R) Full | Concise
             // 658 - Index Term - Curriculum Objective (R) Full | Concise
             // 662 - Subject Added Entry - Hierarchical Place Name (R) Full | Concise
             // 69X - Local Subject Access Fields (R) Full | Concise
        );
        foreach ($saf as $k => $v) {
            foreach ($this->record->getFields($k) as $field) { // or 655, 648, etc.
                $f = $this->makeField('Subject', $field);
                $f->type = $v;
                $fields[] = $f;
            }
        }
        return array_filter($fields, function($s) use ($vocabulary, $type) {
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

    public function get($spec)
    {
        $reference = new \File_MARC_Reference($spec, $this->record);
        return $reference ?: [];
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
        // TODO: Throw something!
    }

}
