[![Build Status](https://img.shields.io/travis/scriptotek/marc.svg?style=flat-square)](https://travis-ci.org/scriptotek/marc)
[![StyleCI](https://styleci.io/repos/41363199/shield)](https://styleci.io/repos/41363199)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/scriptotek/marc.svg?style=flat-square)](https://scrutinizer-ci.com/g/scriptotek/marc/?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/scriptotek/marc.svg?style=flat-square)](https://codeclimate.com/github/scriptotek/marc)

# scriptotek/marc

This is a small package that provides a simple interface to parsing
MARC records using the [File_MARC package](https://github.com/pear/File_MARC).

The package has only been tested with XML encoded MARC 21.
It should likely support everything File_MARC supports, but that
remains to be tested.

## Installation using Composer:

```
composer require scriptotek/marc dev-master
```

## Usage examples

### Records from a file or string

```php
use Scriptotek\Marc\Collection;

$collection = Collection::fromFile($someFileName);
foreach ($collection->records as $record) {
  echo $record->getField('250')->getSubfield('a') . "\n";
}
```
It should detect if the data is Binary MARC or XML.
If you have the data as a string, use
`Collection::fromFile()` instead.

### Records from SRU/OAI-PMH response

The package makes it easy to handle records from an SRU or OAI/PMH response.

```php
$response = file_get_contents('http://lx2.loc.gov:210/NLSBPH?' . http_build_query(array(
    'operation' => 'searchRetrieve',
    'version' => '1.1',
    'query' => 'dc.publisher=CNIB%20AND%20dc.date=2005',
    'maximumRecords' => '10',
    'recordSchema' => 'marcxml'
));

$collection = Collection::fromSruResponse($response);
foreach ($collection->records as $record) {
  echo $record->getField('250')->getSubfield('a') . "\n";
}

```

### Using MARC spec

To easily look up a MARC (sub)field, you can use the MARC spec syntax provided
by the [php-marc-spec package](https://github.com/MARCspec/php-marc-spec):

```php
use Scriptotek\Marc\Collection;

$collection = Collection::from($someMarcDataOrFile);

foreach ($collection->records as $record) {
  echo $record->get('250$a');
}
```

### Convenience methods for handling common fields

The `Record` class has been extended with a few convenience methods to make
handling of everyday tasks easier, in the spirit of
[pymarc](https://github.com/edsu/pymarc). These generally make some
assumptions, for instance that a compound subject string should be joined using
a colon character.
These assumptions may or may not meet *your* expectations. You should inspect
the relevant field class before using it.

```php
use Scriptotek\Marc\Record;

$source = '<?xml version="1.0" encoding="UTF-8" ?>
  <record xmlns="info:lc/xmlns/marcxchange-v1">
    <leader>99999cam a2299999 u 4500</leader>
    <controlfield tag="001">98218834x</controlfield>
    <datafield tag="020" ind1=" " ind2=" ">
      <subfield code="a">8200424421</subfield>
      <subfield code="q">h.</subfield>
      <subfield code="c">Nkr 98.00</subfield>
    </datafield>
  </record>';

$record = Record::from($source);
echo $record->isbns[0];

```
