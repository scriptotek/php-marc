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

    public function testBinaryMarc()
    {
        $records = Collection::fromFile('tests/data/sandburg.mrc')->toArray();

        $this->assertCount(1, $records);
        $this->assertEquals('Arithmetic', $records[0]->title);
    }

    public function testBibsysOaiPmhSample()
    {
        $collection = Collection::fromFile('tests/data/oaipmh-bibsys.xml');

        $this->assertCount(89, $collection->toArray());
    }

    /**
     * @expectedException Scriptotek\Marc\Exceptions\XmlException
     */
    public function testAlmaBibsApiExample()
    {
        // Expect failure because of invalid encoding in XML declaration:
        // Document labelled UTF-16 but has UTF-8 content
        Collection::fromFile('tests/data/alma-bibs-api-invalid.xml');
    }

    public function testLocSample()
    {
        $collection = Collection::fromFile('tests/data/sru-loc.xml');

        $this->assertCount(10, $collection->toArray());
    }

    public function testBibsysSample()
    {
        $collection = Collection::fromFile('tests/data/sru-bibsys.xml');

        $this->assertCount(117, $collection->toArray());
    }

    public function testZdbSample()
    {
        $collection = Collection::fromFile('tests/data/sru-zdb.xml');

        $this->assertCount(8, $collection->toArray());
    }

    public function testKthSample()
    {
        $collection = Collection::fromFile('tests/data/sru-kth.xml');

        $this->assertCount(10, $collection->toArray());
    }

    public function testAlmaSample()
    {
        $collection = Collection::fromFile('tests/data/sru-alma.xml');

        $this->assertCount(3, $collection->toArray());
    }
}
