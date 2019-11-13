<?php

namespace Scriptotek\Marc;

use File_MARC_Record;
use Scriptotek\Marc\Exceptions\RecordNotFound;
use Scriptotek\Marc\Exceptions\UnknownRecordType;
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
     * Determines if a record is a bibliographic, holdings or authority record.
     *
     * @param File_MARC_Record $record
     * @return string
     */
    public static function getRecordType(File_MARC_Record $record)
    {
        $leader = $record->getLeader();
        $recordType = substr($leader, 6, 1);

        switch ($recordType) {
            case 'a': // Language material
            case 'c': // Notated music
            case 'd': // Manuscript notated music
            case 'e': // Cartographic material
            case 'f': // Manuscript cartographic material
            case 'g': // Projected medium
            case 'i': // Nonmusical sound recording
            case 'j': // Musical sound recording
            case 'k': // Two-dimensional nonprojectable graphic
            case 'm': // Computer file
            case 'o': // Kit
            case 'p': // Mixed materials
            case 'r': // Three-dimensional artifact or naturally occurring object
            case 't': // Manuscript language material
                return Marc21::BIBLIOGRAPHIC;
            case 'z':
                return Marc21::AUTHORITY;
            case 'u': // Unknown
            case 'v': // Multipart item holdings
            case 'x': // Single-part item holdings
            case 'y': // Serial item holdings
                return Marc21::HOLDINGS;
            default:
                throw new UnknownRecordType();
        }
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

    /**
     * Return the first record in the collection.
     *
     * @return BibliographicRecord|HoldingsRecord|AuthorityRecord
     * @throws RecordNotFound if the collection is empty
     */
    public function first()
    {
        $this->rewind();
        if (is_null($this->current())) {
            throw new RecordNotFound();
        }
        return $this->current();
    }

    /**
     * Creates a Record object from a File_MARC_Record object.
     *
     * @param File_MARC_Record $record
     * @return AuthorityRecord|BibliographicRecord|HoldingsRecord
     */
    public function recordFactory(File_MARC_Record $record)
    {
        try {
            $recordType = self::getRecordType($record);
        } catch (UnknownRecordType $e) {
            return new Record($record);
        }
        switch ($recordType) {
            case Marc21::BIBLIOGRAPHIC:
                return new BibliographicRecord($record);

            case Marc21::HOLDINGS:
                return new HoldingsRecord($record);

            case Marc21::AUTHORITY:
                return new AuthorityRecord($record);
        }
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
                $rec = $this->recordFactory($rec);
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
