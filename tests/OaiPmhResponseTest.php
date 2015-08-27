<?php

use Scriptotek\Marc\Importers\XmlImporter;

class OaiPmhResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testBibsysSample()
    {
        $response = new XmlImporter('tests/data/oaipmh-bibsys.xml');

        $this->assertCount(89, $response->getRecords());
    }
}
