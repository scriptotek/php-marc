<?php

namespace Scriptotek\Marc\Importers;

use Scriptotek\Marc\Collection;

class SruResponse extends XmlImporter
{

    public function __construct($data, $ns = 'http://www.loc.gov/zing/srw/', $isPrefix = false, Collection $collection = null)
    {
        parent::__construct($data, $ns, $isPrefix, $collection);
    }

}
