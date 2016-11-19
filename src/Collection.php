<?php

namespace Scriptotek\Marc;

use Scriptotek\Marc\Importers\Importer;

class Collection implements \Iterator
{
    protected $parser;
    protected $_records;
    protected $useCache = false;
    protected $position = 0;
    protected $_current;

    /**
     * Collection constructor.
     *
     * @param \File_MARC|\File_MARCXML $parser
     */
    public function __construct($parser = null)
    {
        $this->parser = $parser;
    }

    /**
     * Load records from a file (Binary MARC or XML).
     *
     * @param string $filename
     * @return Collection
     */
    public static function fromFile($filename)
    {
        $importer = new Importer();

        return $importer->fromFile($filename);
    }

    /**
     * Load records from a string (Binary MARC or XML).
     *
     * @param string $data
     * @return Collection
     */
    public static function fromString($data)
    {
        $importer = new Importer();

        return $importer->fromString($data);
    }

    /**
     * Returns an array representation of the collection.
     *
     * @return Collection[]
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }

    /*********************************************************
     * Iterator
     *********************************************************/

    public function valid()
    {
        return !is_null($this->_current);
    }

    public function current()
    {
        return $this->_current;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
        if ($this->useCache) {
            $rec = isset($this->_records[$this->position]) ? $this->_records[$this->position] : false;
        } else {
            $rec = isset($this->parser) ? $this->parser->next() : null;
            if ($rec) {
                $rec = new Record($rec);
                $this->_records[] = $rec;
            }
        }
        $this->_current = $rec ?: null;
    }

    public function rewind()
    {
        $this->position = -1;
        if (is_null($this->_records)) {
            $this->_records = [];
        } else {
            $this->useCache = true;
        }
        $this->next();
    }

    // public function count()
    // {
    // }

    /*********************************************************
     * Magic
     *********************************************************/

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->parser, $name], $arguments);
    }
}
