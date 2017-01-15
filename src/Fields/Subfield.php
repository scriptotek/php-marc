<?php

namespace Scriptotek\Marc\Fields;

use Scriptotek\Marc\Record;

abstract class Subfield implements \JsonSerializable
{
    protected $subfield;
    protected $field;

    public function __construct(\File_MARC_Data_Field $field, \File_MARC_Subfield $subfield)
    {
        $this->field = $field;
        $this->subfield = $subfield;
    }

    public function __destruct()
    {
        $this->field = null;
        $this->subfield = null;
    }

    public function delete()
    {
        $this->subfield->delete();
        $this->field->deleteSubfield($this->subfield);
        $this->__destruct();
    }

    public function jsonSerialize()
    {
        return (string) $this;
    }

    public function __toString()
    {
        return $this->subfield->getData();
    }

    public function __call($name, $args)
    {
        return call_user_func_array([$this->subfield, $name], $args);
    }

    public function __get($key)
    {
        $method = 'get' . ucfirst($key);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
    }
}
