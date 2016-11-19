<?php

namespace Scriptotek\Marc\Record;

use PHPUnit_Framework_TestCase;
use Scriptotek\Marc\Collection;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyCollection()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?><test></test>';

        $collection = Collection::fromString($source);
        $this->assertCount(0, $collection->toArray());
    }
}
