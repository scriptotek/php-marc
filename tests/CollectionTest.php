<?php

namespace Tests;

use Scriptotek\Marc\BibliographicRecord;
use Scriptotek\Marc\Collection;
use Scriptotek\Marc\Exceptions\XmlException;

class CollectionTest extends TestCase
{
    /**
     * Test that an empty Collection is created if no MARC records were found in the input.
     */
    public function testEmptyCollection()
    {
        $source = '<?xml version="1.0" encoding="UTF-8" ?><test></test>';

        $collection = Collection::fromString($source);
        $this->assertCount(0, $collection->toArray());
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

    /**
     * Define a list of sample binary MARC files that we can test with,
     * and the expected number of records in each.
     *
     * @return array
     */
    public function mrcFiles()
    {
        return [
            ['sandburg.mrc', 1],        // Single binary MARC file
        ];
    }

    /**
     * Define a list of sample XML files from different sources that we can test with,
     * and the expected number of records in each.
     *
     * @return array
     */
    public function xmlFiles()
    {
        return [
            ['oaipmh-bibsys.xml', 89],  // Records encapsulated in OAI-PMH response
            ['sru-loc.xml', 10],        // Records encapsulated in SRU response
            ['sru-bibsys.xml', 117],    // (Another one)
            ['sru-zdb.xml', 8],         // (Another one)
            ['sru-kth.xml', 10],        // (Another one)
            ['sru-alma.xml', 3],        // (Another one)
        ];
    }

    /**
     * Test that the sample files can be loaded using Collection::fromFile
     *
     * @dataProvider mrcFiles
     * @dataProvider xmlFiles
     * @param string $filename
     * @param int $expected
     */
    public function testCollectionFromFile($filename, $expected)
    {
        $records = $this->getTestCollection($filename)->toArray();

        $this->assertCount($expected, $records);
        $this->assertInstanceOf(BibliographicRecord::class, $records[0]);
    }
}
