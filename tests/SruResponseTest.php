<?php

use Scriptotek\Marc\Importers\SruResponse;

class SruResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testLocSample()
    {
        $response = new SruResponse('tests/data/sru-loc.xml');

        $this->assertCount(10, $response->getRecords());
    }

    public function testBibsysSample()
    {
        $response = new SruResponse('tests/data/sru-bibsys.xml');

        $this->assertCount(117, $response->getRecords());
    }

    public function testZdbSample()
    {
        $response = new SruResponse('tests/data/sru-zdb.xml');

        $this->assertCount(8, $response->getRecords());
    }

    public function testKthSample()
    {
        $response = new SruResponse('tests/data/sru-kth.xml');

        $this->assertCount(10, $response->getRecords());
    }

    public function testAlmaSample()
    {
        $response = new SruResponse('tests/data/sru-alma.xml');

        $this->assertCount(3, $response->getRecords());
    }

}
