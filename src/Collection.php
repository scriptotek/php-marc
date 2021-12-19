<?php

namespace Scriptotek\Marc;

use File_MARC;
use File_MARC_Record;
use File_MARCXML;
use Scriptotek\Marc\Exceptions\RecordNotFound;
use Scriptotek\Marc\Exceptions\UnknownRecordType;
use Scriptotek\Marc\Importers\Importer;
use Scriptotek\Marc\Importers\XmlImporter;
use SimpleXMLElement;

class Collection implements \Iterator
{
    protected File_MARCXML|File_MARC|null $parser;
    protected ?array $_records = null;
    protected bool $useCache = false;
    protected int $position = 0;
    protected Record|HoldingsRecord|BibliographicRecord|AuthorityRecord|null $_current = null;

    /**
     * Collection constructor.
     *
     * @param File_MARCXML|File_MARC|null $parser
     */
    public function __construct(File_MARCXML|File_MARC $parser = null)
    {
        $this->parser = $parser;
    }

    /**
     * Load records from a file (Binary MARC or XML).
     *
     * @param string $filename
     * @return Collection
     */
    public static function fromFile(string $filename): Collection
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
    public static function fromString(string $data): Collection
    {
        $importer = new Importer();

        return $importer->fromString($data);
    }

    /**
     * Load records from a SimpleXMLElement object.
     *
     * @param SimpleXMLElement $element
     * @return Collection
     */
    public static function fromSimpleXMLElement(SimpleXMLElement $element): Collection
    {
        $importer = new XmlImporter($element);

        return $importer->getCollection();
    }

    /**
     * Determines if a record is a bibliographic, holdings or authority record.
     *
     * @param File_MARC_Record $record
     * @return string
     */
    public static function getRecordType(File_MARC_Record $record): string
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
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    /**
     * Return the first record in the collection.
     *
     * @return Record|HoldingsRecord|BibliographicRecord|AuthorityRecord|null
     * @throws RecordNotFound if the collection is empty
     */
    public function first(): Record|HoldingsRecord|BibliographicRecord|AuthorityRecord|null
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
     * @return Record|HoldingsRecord|BibliographicRecord|AuthorityRecord|null
     */
    public function recordFactory(File_MARC_Record $record): Record|HoldingsRecord|BibliographicRecord|AuthorityRecord|null
    {
        try {
            $recordType = self::getRecordType($record);
        } catch (UnknownRecordType $e) {
            return new Record($record);
        }
        return match ($recordType) {
            Marc21::BIBLIOGRAPHIC => new BibliographicRecord($record),
            Marc21::HOLDINGS => new HoldingsRecord($record),
            Marc21::AUTHORITY => new AuthorityRecord($record),
            default => null,
        };
    }

    /*********************************************************
     * Iterator
     *********************************************************/

    public function valid(): bool
    {
        return !is_null($this->_current);
    }

    public function current(): Record|HoldingsRecord|BibliographicRecord|AuthorityRecord|null
    {
        return $this->_current;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
        if ($this->useCache) {
            $rec = $this->_records[$this->position] ?? false;
        } else {
            $rec = isset($this->parser) ? $this->parser->next() : null;
            if ($rec) {
                $rec = $this->recordFactory($rec);
                $this->_records[] = $rec;
            }
        }
        $this->_current = $rec ?: null;
    }

    public function rewind(): void
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
