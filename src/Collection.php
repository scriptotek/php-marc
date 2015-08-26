<?php

namespace Scriptotek\Marc;

use Scriptotek\Marc\Importers\Importer;
use Scriptotek\Marc\Importers\OaiPmhResponse;
use Scriptotek\Marc\Importers\SruResponse;

class Collection
{
    protected $parser;
    protected $_records;

    public static function fromFile($filename)
    {
        $importer = new Importer($filename, true);

        return $importer->getCollection();
    }

    public static function fromString($data)
    {
        $importer = new Importer($data, false);

        return $importer->getCollection();
    }

    public static function fromOaiPmhResponse($data)
    {
        $importer = new OaiPmhResponse($data);

        return $importer->getCollection();
    }

    public static function fromSruResponse($data)
    {
        $importer = new SruResponse($data);

        return $importer->getCollection();
    }

    public function __construct(\Factory $factory = null)
    {
        $this->factory = $factory ?: new Factory();
    }

    public function parse($source, $isXml, $ns = '', $isPrefix = true)
    {
        if ($isXml) {
            $this->parser = $this->factory->make('File_MARCXML', $source, \File_MARCXML::SOURCE_STRING, $ns, $isPrefix);
        } else {
            $this->parser = $this->factory->make('File_MARC', $source, \File_MARC::SOURCE_STRING);
        }
    }

    public function __get($key = '')
    {
        if ($key == 'records') {
            // re-instantiaces..
            if (is_null($this->parser)) {
                return array();
            }
            if (is_null($this->_records)) {
                $this->_records = new Records($this->parser);
            }

            return $this->_records;
        }
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->parser, $name), $arguments);
    }
}
