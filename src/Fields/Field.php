<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

class Field
{
    protected $field;

    public function __construct(\File_MARC_Field $field)
    {
        $this->field = $field;
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->field, $name), $args);
    }

    public function __get($key)
    {
        $method = 'get' . ucfirst($key);
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        }
    }

    public function sf($code)
    {
        $x = $this->getSubfield($code);
        return $x->getData();
    }

    public static function makeFieldObject(Record $record, $tag, $pcre=false)
    {
        $field = $record->getField($tag, $pcre);

        // Note: `new static()` is a way of creating a new instance of the
        // called class using late static binding.
        return isset($field) ? new static($field) : $field;
    }

    public static function makeFieldObjects(Record $record, $tag, $pcre=false)
    {
        return array_map(function($field) {
            return new self($field);
        }, $record->getFields($tag, $pcre));
    }
}
