<?php

namespace Scriptotek\Marc\Importers;

use File_MARC;
use Scriptotek\Marc\Collection;
use Scriptotek\Marc\Factory;
use SimpleXMLElement;

class Importer
{
    private Factory $factory;

    public function __construct(Factory $factory = null)
    {
        $this->factory = $factory ?? new Factory();
    }

    public function fromFile(string $filename): Collection
    {
        $data = file_get_contents($filename);

        return $this->fromString($data);
    }

    public function fromString(string $data): Collection
    {
        $isXml = str_starts_with($data, '<');
        if ($isXml) {
            $importer = new XmlImporter($data);

            return $importer->getCollection();
        } else {
            $parser = $this->factory->make('File_MARC', $data, File_MARC::SOURCE_STRING);
            return new Collection($parser);
        }
    }
}
