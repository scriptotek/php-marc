<?php

namespace Scriptotek\Marc;

class Factory
{
    protected function _make($className, $args)
    {
        $reflectionClass = new \ReflectionClass($className);
        $instance = $reflectionClass->newInstanceArgs($args);
        return $instance;
    }

    public function make()
    {
        $args = func_get_args();
        $className = array_shift($args);
        return $this->_make($className, $args);
    }

    public function makeField()
    {
        $args = func_get_args();
        $className = 'Scriptotek\\Marc\\Fields\\' . array_shift($args);
        return $this->_make($className, $args);
    }
}
