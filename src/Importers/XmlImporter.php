<?php

namespace Scriptotek\Marc\Importers;

use File_MARCXML;
use Scriptotek\Marc\Collection;
use Scriptotek\Marc\Exceptions\RecordNotFound;
use Scriptotek\Marc\Exceptions\XmlException;
use Scriptotek\Marc\Factory;
use SimpleXMLElement;

class XmlImporter
{
    protected $factory;

    /* var SimpleXMLElement */
    protected $source;

    /**
     * XmlImporter constructor.
     *
     * @param string|SimpleXMLElement $data  Filename, XML string or SimpleXMLElement object
     * @param string $ns  URI or prefix of the namespace
     * @param bool $isPrefix TRUE if $ns is a prefix, FALSE if it's a URI; defaults to FALSE
     * @param string $factory  (optional) Object factory, probably no need to set this outside testing.
     */
    public function __construct($data, $ns = '', $isPrefix = false, $factory = null)
    {
        $this->factory = isset($factory) ? $factory : new Factory();

        if (is_a($data, SimpleXMLElement::class)) {
            $this->source = $data;
            return;
        }

        if (strlen($data) < 256 && file_exists($data)) {
            $data = file_get_contents($data);
        }

        // Store errors internally so that we can fetch them with libxml_get_errors() later
        libxml_use_internal_errors(true);

        $this->source = simplexml_load_string($data, 'SimpleXMLElement', 0, $ns, $isPrefix);
        if (false === $this->source) {
            throw new XmlException(libxml_get_errors());
        }
    }

    public function getMarcNamespace($namespaces)
    {
        foreach ($namespaces as $prefix => $ns) {
            if ($ns == 'info:lc/xmlns/marcxchange-v1') {
                return [$prefix, $ns];
            } elseif ($ns == 'http://www.loc.gov/MARC21/slim') {
                return [$prefix, $ns];
            }
        }

        return ['', ''];
    }

    public function getRecords()
    {
        $this->source->registerXPathNamespace('m', 'http://www.loc.gov/MARC21/slim');
        $this->source->registerXPathNamespace('x', 'info:lc/xmlns/marcxchange-v1');

        // If root node is record:
        if ($this->source->getName() == 'record') {
            return [$this->source];
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

        return [];
    }

    public function getFirstRecord()
    {
        $records = $this->getRecords();
        if (!count($records)) {
            throw new RecordNotFound();
        }

        $record = $records[0];

        list($prefix, $ns) = $this->getMarcNamespace($record->getNamespaces(true));

        $parser = $this->factory->make('File_MARCXML', $record, File_MARCXML::SOURCE_SIMPLEXMLELEMENT, $ns);

        return (new Collection($parser))->$this->getFirstRecord();
    }

    public function getCollection()
    {
        $records = $this->getRecords();
        if (!count($records)) {
            return new Collection();
        }

        list($prefix, $ns) = $this->getMarcNamespace($records[0]->getNamespaces(true));

        $pprefix = empty($prefix) ? '' : "$prefix:";

        $records = array_map(function (SimpleXMLElement $record) {
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

        $parser = $this->factory->make('File_MARCXML', $marcCollection, File_MARCXML::SOURCE_STRING, $prefix, true);

        return new Collection($parser);
    }
}
