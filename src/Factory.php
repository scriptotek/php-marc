<?php

namespace Scriptotek\Marc;

class Factory
{
    protected function genMake($className, $args)
    {
        $reflectionClass = new \ReflectionClass($className);
        $instance = $reflectionClass->newInstanceArgs($args);

        return $instance;
    }

    public function make()
    {
        $args = func_get_args();
        $className = array_shift($args);

        return $this->genMake($className, $args);
    }

    public function makeField()
    {
        $args = func_get_args();
        $className = 'Scriptotek\\Marc\\Fields\\' . array_shift($args);

        return $this->genMake($className, $args);
    }
}
