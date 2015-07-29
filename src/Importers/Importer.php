<?php

namespace Scriptotek\Marc\Importers;

use Scriptotek\Marc\Collection;

class Importer
{
    protected $data;

    public function __construct($data, $isFile)
    {
        if ($isFile) {
            $data = file_get_contents($data);
        }
        $this->data = trim($data);
    }

    public function getCollection()
    {
        $isXml = (substr($this->data, 0, 1) == '<');
        if ($isXml) {
            $importer = new XmlImporter($this->data);
            return $importer->getCollection();
        } else {
            $c = new Collection();
            $c->parse($this->data, false);
            return $c;
        }
    }

}
