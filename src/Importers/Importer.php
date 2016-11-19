<?php

namespace Scriptotek\Marc\Importers;

use File_MARC;
use Scriptotek\Marc\Collection;
use Scriptotek\Marc\Factory;

class Importer
{
    public function __construct(Factory $factory = null)
    {
        $this->factory = isset($factory) ? $factory : new Factory();
    }

    public function fromFile($filename)
    {
        $data = file_get_contents($filename);

        return $this->fromString($data);
    }

    public function fromString($data)
    {
        $isXml = (substr($data, 0, 1) == '<');
        if ($isXml) {
            $importer = new XmlImporter($data);

            return $importer->getCollection();
        } else {
            $parser = $this->factory->make('File_MARC', $data, File_MARC::SOURCE_STRING);
            return new Collection($parser);
        }
    }
}
