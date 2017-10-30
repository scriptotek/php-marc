<?php

use PHPUnit\Framework\TestCase;
use Scriptotek\Marc\AuthorityRecord;
use Scriptotek\Marc\BibliographicRecord;
use Scriptotek\Marc\Fields\Subject;
use Scriptotek\Marc\HoldingsRecord;
use Scriptotek\Marc\Marc21;
use Scriptotek\Marc\Record;

class ExamplesTest extends TestCase
{
    /**
     * @dataProvider testExampleDataProvider
     */
    public function testExample($filename) {
        $record = Record::fromFile($filename);
        $jsonFilename = substr($filename, 0, strrpos($filename, ".")) . '.json';
        if (!file_exists($jsonFilename)) {
            file_put_contents($jsonFilename, json_encode($record, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } else {
            $jsonData = file_get_contents($jsonFilename);
            $this->assertJsonStringEqualsJsonString($jsonData, json_encode($record));
        }
    }

    public function testExampleDataProvider() {
        foreach (glob(__DIR__ . '/data/examples/*.xml') as $filename) {
            yield [$filename];
        }
    }
}
