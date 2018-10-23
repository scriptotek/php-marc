<?php

namespace Tests;

use Scriptotek\Marc\Collection;
use Scriptotek\Marc\Record;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function pathTo($filename)
    {
        return __DIR__ . '/data/' . $filename;
    }

    protected function getTestCollection($filename)
    {
        return Collection::fromFile($this->pathTo($filename));
    }

    protected function getNthrecord($filename, $n)
    {
        $records = $this->getTestCollection($filename)->toArray();

        return $records[$n - 1];
    }

    protected function makeMinimalRecord($value)
    {
        return Record::fromString('<?xml version="1.0" encoding="UTF-8" ?>
          <record>
            <leader>99999cam a2299999 u 4500</leader>
            <controlfield tag="001">98218834x</controlfield>
            ' . $value . '
          </record>');
    }
}
