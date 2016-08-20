<?php

use Scriptotek\Marc\Importers\XmlImporter;

class InvalidRecordTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException Scriptotek\Marc\Exceptions\XmlException
	 */
    public function testAlmaBibsApiExample()
    {
        // Expect failure because of invalid encoding in XML declaration:
        // Document labelled UTF-16 but has UTF-8 content
        $response = new XmlImporter('tests/data/alma-bibs-api-invalid.xml');
    }
}
