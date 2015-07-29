<?php

namespace Scriptotek\Marc\Importers;

use Scriptotek\Marc\Collection;

class OaiPmhResponse extends XmlImporter
{

    public function __construct($data, $ns = 'http://www.openarchives.org/OAI/2.0/', $isPrefix = false, Collection $collection = null)
    {
        parent::__construct($data, $ns, $isPrefix, $collection);
    }

}
