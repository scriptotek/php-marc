<?php

namespace Tests;

use Scriptotek\Marc\Collection;
use Scriptotek\Marc\Exceptions\XmlException;

class CollectionTest extends TestCase
{
    public function testEmptyCollection()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?><test></test>';

        $collection = Collection::fromString($source);
        $this->assertCount(0, $collection->toArray());
    }

    public function testBinaryMarc()
    {
        $records = $this->getTestCollection('sandburg.mrc')->toArray();

        $this->assertCount(1, $records);
        $this->assertEquals('Arithmetic', $records[0]->title);
    }

    public function testBibsysOaiPmhSample()
    {
        $collection = $this->getTestCollection('oaipmh-bibsys.xml');

        $this->assertCount(89, $collection->toArray());
    }

    /**
     * Test that it XmlException is thrown when the specified encoding (UTF-16)
     * differs from the actual encoding (UTF-8).
     */
    public function testExceptionOnInvalidEncoding()
    {
        $this->expectException(XmlException::class);
        $this->getTestCollection('alma-bibs-api-invalid.xml');
    }

    public function testLocSample()
    {
        $collection = $this->getTestCollection('sru-loc.xml');

        $this->assertCount(10, $collection->toArray());
    }

    public function testBibsysSample()
    {
        $collection = $this->getTestCollection('sru-bibsys.xml');

        $this->assertCount(117, $collection->toArray());
    }

    public function testZdbSample()
    {
        $collection = $this->getTestCollection('sru-zdb.xml');

        $this->assertCount(8, $collection->toArray());
    }

    public function testKthSample()
    {
        $collection = $this->getTestCollection('sru-kth.xml');

        $this->assertCount(10, $collection->toArray());
    }

    public function testAlmaSample()
    {
        $collection = $this->getTestCollection('sru-alma.xml');

        $this->assertCount(3, $collection->toArray());
    }
}
