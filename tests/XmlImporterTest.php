<?php

use Scriptotek\Marc\Importers\XmlImporter;

class XmlImporterTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptySet()
    {
        $response = new XmlImporter('<records></records>');

        $this->assertCount(0, $response->getCollection()->toArray());
    }
}
