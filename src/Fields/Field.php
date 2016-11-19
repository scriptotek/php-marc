<?php

namespace Scriptotek\Marc\Fields;

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
}
