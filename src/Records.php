<?php

namespace Scriptotek\Marc;

class Records implements \Iterator //, \Countable
{
    protected $parser;
    protected $records;
    protected $useCache = false;

    public function __construct($parser)
    {
        $this->parser = $parser;
        $this->_current = null;
    }

    public function toArray()
    {
        $records = array();
        $this->next();
        while ($this->valid()) {
            $records[] = $this->current();
            $this->next();
        }
        return $records;
    }

    /*********************************************************
     * Iterator + Countable
     *********************************************************/

    protected $position = 0;
    protected $_current;

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
        $this->position++;
        if ($this->useCache) {
            $rec = isset($this->records[$this->position]) ? $this->records[$this->position] : false;
        } else {
            $rec = $this->parser->next();
            if ($rec) {
                $rec = new Record($rec);
                $this->records[] = $rec;
            }
        }
        $this->_current = $rec ?: null;
    }

    public function rewind()
    {
        $this->position = -1;
        if (is_null($this->records)) {
            $this->records = array();
        } else {
            $this->useCache = true;
        }
        $this->next();
    }

    public function valid()
    {
        return !is_null($this->_current);
    }

    // public function count()
    // {
    // }
}
