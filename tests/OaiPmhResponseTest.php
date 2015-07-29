<?php

use Scriptotek\Marc\Importers\OaiPmhResponse;

class OaiPmhResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testBibsysSample()
    {
        $response = new OaiPmhResponse('tests/data/oaipmh-bibsys.xml');

        $this->assertCount(89, $response->getRecords());
    }

}
