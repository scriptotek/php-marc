<?php

namespace Scriptotek\Marc\Importers;

use Scriptotek\Marc\Collection;

class XmlImporter
{
    protected $source;
    protected $collection;

    public function __construct($data, $ns = '', $isPrefix = false, Collection $collection = null)
    {
        $this->collection = $collection ?: new Collection();

        if (strlen($data) < 256 && file_exists($data)) {
            $data = file_get_contents($data);
        }

        $this->source = simplexml_load_string($data, 'SimpleXMLElement', 0, $ns, $isPrefix);
    }

    public function getMarcNamespace($namespaces)
    {
        foreach ($namespaces as $prefix => $ns) {
            if ($ns == 'info:lc/xmlns/marcxchange-v1') {
                return array($prefix, $ns);
            } elseif ($ns == 'http://www.loc.gov/MARC21/slim') {
                return array($prefix, $ns);
            }
        }

        return array('', '');
    }

    public function getRecords()
    {
        $this->source->registerXPathNamespace('m', 'http://www.loc.gov/MARC21/slim');
        $this->source->registerXPathNamespace('x', 'info:lc/xmlns/marcxchange-v1');

        // If root node is record:
        if ($this->source->getName() == 'record') {
            return array($this->source);
        }

        $marcRecords = $this->source->xpath('.//x:record');
        if (count($marcRecords)) {
            return $marcRecords;
        }
        $marcRecords = $this->source->xpath('.//m:record');
        if (count($marcRecords)) {
            return $marcRecords;
        }
        $marcRecords = $this->source->xpath('.//record');
        if (count($marcRecords)) {
            return $marcRecords;
        }

        return array();
    }

    public function getCollection()
    {
        $records = $this->getRecords();
        if (!count($records)) {
            return $this->collection;
        }

        list($prefix, $ns) = $this->getMarcNamespace($records[0]->getNamespaces(true));
        $pprefix = empty($prefix) ? '' : "$prefix:";

        $records = array_map(function ($record) {
            $x = $record->asXML();

            // Strip away XML declaration.
            // Tried LIBXML_NOXMLDECL first, but didn't work,
            // https://bugs.php.net/bug.php?id=50989
            $x = trim(preg_replace('/^\<\?xml.*?\?\>/', '', $x));

            return $x;
        }, $records);

        $nsDef = '';
        if (!empty($ns)) {
            if (empty($prefix)) {
                $nsDef = " xmlns=\"$ns\"";
            } else {
                $nsDef = " xmlns:$prefix=\"$ns\"";
            }
        }
        $marcCollection = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<' . $pprefix . 'collection' . $nsDef . '>' .
            implode('', $records) .
            '</' . $pprefix . 'collection>';

        $this->collection->parse($marcCollection, true, $prefix);

        return $this->collection;
    }
}
