[![Build Status](https://img.shields.io/travis/scriptotek/php-marc/master.svg?style=flat-square)](https://travis-ci.org/scriptotek/php-marc)
[![Coverage](https://img.shields.io/codecov/c/github/scriptotek/php-marc/master.svg?style=flat-square)](https://codecov.io/gh/scriptotek/php-marc)
[![StyleCI](https://styleci.io/repos/41363199/shield)](https://styleci.io/repos/41363199)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/scriptotek/php-marc.svg?style=flat-square)](https://scrutinizer-ci.com/g/scriptotek/php-marc/?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/scriptotek/marc.svg?style=flat-square)](https://codeclimate.com/github/scriptotek/marc)
[![Latest Stable Version](http://img.shields.io/packagist/v/scriptotek/marc.svg?style=flat-square)](https://packagist.org/packages/scriptotek/marc)
[![Total Downloads](http://img.shields.io/packagist/dt/scriptotek/marc.svg?style=flat-square)](https://packagist.org/packages/scriptotek/marc)

# scriptotek/marc

A small PHP package providing a simple interface to work with MARC21 records
on top of the excellent [File_MARC package](https://github.com/pear/File_MARC).

Works with both Binary MARC and MARCXML (namespaced or not), but not the various
Line mode MARC formats. Records can be edited using the editing capabilities of
File_MARC.

Note that version 0.3.0 introduced a few breaking changes. See
[releases](https://github.com/scriptotek/php-marc/releases) for more information.

## Installation using Composer:

If you have [Composer](https://getcomposer.org/) installed, the package can
be installed by running

```
composer require scriptotek/marc
```

## Reading records

Use `Collection::fromFile` or `Collection::fromString` to read one or more
MARC records from a file or string. The methods autodetect the data format
(Binary XML or MARCXML) and whether the XML is namespaced or not.

```php
use Scriptotek\Marc\Collection;

$collection = Collection::fromFile($someFileName);
foreach ($collection as $record) {
    echo $record->getField('250')->getSubfield('a')->getData() . "\n";
}
```

The `$collection` object is an iterator. If you rather want a normal array,
for instance in order to count the number of records, you can get that from
`$collection->toArray()`.

The loader can extract MARC records from any container XML, so you can pass
in an SRU or OAI-PMH response directly:

```php
$response = file_get_contents('http://lx2.loc.gov:210/lcdb?' . http_build_query([
    'operation'      => 'searchRetrieve',
    'recordSchema'   => 'marcxml',
    'version'        => '1.1',
    'maximumRecords' => '10',
    'query'          => 'bath.isbn=0761532692',
]));

$records = Collection::fromString($response);
foreach ($records as $record) {
    ...
}
```

If you only have a single record, you can also use `Record::fromFile` or
`Record::fromString`. These use the `Collection` methods under the hood,
but returns a single `Record` object.

```php
use Scriptotek\Marc\Record;

$record = Record::fromFile($someFileName);
```

## Querying with MARCspec

Use the `Record::query()` method to query a record using the
[MARCspec](http://marcspec.github.io/) language as implemented in the
[php-marc-spec package](https://github.com/MARCspec/php-marc-spec) package.
The method returns a `QueryResult` object, which is a small wrapper around
`File_MARC_Reference`.

Example: To loop over all `650` fields having `$2 noubomn`:

```php
foreach ($record->query('650{2=\noubomn}') as $field) {
   echo $field->getSubfield('a')->getData();
}
```

or we could reference the subfield directly, like so:

```php
foreach ($record->query('650$a{2=\noubomn}') as $subfield) {
   echo $subfield->getData();
}
```

You can retrieve single results using `first()`, which returns the first match,
or `null` if no matches were found:

```php
$record->query('250$a')->first();
```

In the same way, `text()` returns the data content of the first match, or `null`
if no matches were found:

```php
$record->query('250$a')->text();
```

## Convenience methods on the Record class

The `Record` class extends `File_MARC_Record` with a few convenience methods to
get data from commonly used fields. Each of these methods, except `getType()`,
returns an object or an array of objects of one of the field classes (located in
`src/Fields`). For instance `getIsbns()` returns an array of
`Scriptotek\Marc\Isbn` objects. All the field classes implements at minimum a
`__toString()` method so you easily can get a string representation of the field
for presentation purpose.

Note that all the get methods can also be accessed as attributes thanks to a
little PHP magic (`__get`). So instead of calling `$record->getId()`, you can
use the shorthand variant `$record->id`.

### type

`$record->getType()` or `$record->type` returns either 'Bibliographic', 'Authority'
or 'Holdings' based on the value of the sixth character in the leader.
See `Marc21.php` for supporting constants.

```php
if ($record->type == Marc21::BIBLIOGRAPHIC) {
    // ...
}
```

### catalogingForm

`$record->getCatalogingForm()` or `$record->catalogingForm` returns the value
of LDR/18. See `Marc21.php` for supporting constants.

### id

`$record->getId()` or `$record->id` returns the record id from 001 control field.

### isbns

`$record->getIsbns()` or `$record->isbns` returns an array of `Isbn` objects from
020 fields.

```php
use Scriptotek\Marc\Record;

$record = Record::fromString('<?xml version="1.0" encoding="UTF-8" ?>
  <record xmlns="""http://www.loc.gov/MARC21/slim">
    <leader>99999cam a2299999 u 4500</leader>
    <controlfield tag="001">98218834x</controlfield>
    <datafield tag="020" ind1=" " ind2=" ">
      <subfield code="a">8200424421</subfield>
      <subfield code="q">h.</subfield>
      <subfield code="c">Nkr 98.00</subfield>
    </datafield>
  </record>');
$isbn = $record->isbns[0];

// Get the string representation of the field:
echo $isbn . "\n";  // '8200424421'

// Get the value of $q using the standard FILE_MARC interface:
echo $isbn->getSubfield('q')->getData() . "\n";  // 'h.'

// or using the shorthand `sf()` method from the Field class:
echo $isbn->sf('q') . "\n";  // 'h.'
```

### title

`$record->getTitle()` or `$record->title` returns a `Title` objects from 245
field, or null if no such field is present.

Beware that the default string representation may or may not fit your needs.
It's currently a concatenation of `$a` (title), `$b` (remainder of title),
`$n`(part number) and `$p` (part title). For the remaining subfields like `$f`,
`$g` and `$k`, I haven't decided whether to handle them or not.

Parallel titles are unfortunately encoded in such a way that there's no way I'm
aware of to identify them in a secure manner, meaning there's also no secure way
to remove them if you don't want to include them.<sup id="a1">[1](#f1)</sup>

I'm trimming off any final '`/`' ISBD marker. I would have loved to be able to
also trim off final dots, but that's not trivial for the same reason identifying
parallel titles is not<sup id="a1">[1](#f1)</sup> – there's just no safe way to
tell if the final dot is an ISBD marker or part of the title.<sup
id="a2">[2](#f2)</sup> Since explicit ISBD markers are included in records
catalogued in the American tradition, but not in records catalogued in the
British tradition, a mix of records from both traditions will look silly.

### subjects

`$record->getSubjects($vocabulary, $tag)` or `$record->subjects` returns an array of `Subject`
objects from all [the 6XX fields](http://www.loc.gov/marc/bibliographic/bd6xx.html).
The `getSubjects()` method have two optional arguments you can use to limit by
vocabulary and/or tag.

```php
foreach ($record->getSubjects('mesh', Subject::TOPICAL_TERM) as $subject) {
    echo "{$subject->vocabulary} {$subject->type} {$subject}";
}
```

The string representation of this field makes use of the constant `Subject::glue`
to glue subject components together. The default value is a space-padded colon,
making `Physics : History : 20th century` the string representation of
`650 $aPhysics $xHistory $yHistory`. If you prefer the "LCSH-way" of
`Physics--History--20th century`, just set `Subject::glue = '--'`.

## Notes

It's unfortunately easy to err when trying to present data from MARC records in
end user applications. A developer learning by example might for instance assume
that `300 $a` is a subfield for "number of pages".<sup id="a3">[3](#f3)</sup> A
quick glance at e.g. [LC's MARC
documentation](https://www.loc.gov/marc/bibliographic/bd300.html) would be
enough to prove that wrong, but in other cases it's harder to avoid making false
assumptions without deep familiarity with cataloguing rules and practices.

<b id="f1">1</b> That might change in the future. But even if I decide to remove parallel titles,
I'm not really sure how to do it in a safe way. Parallel titles are identified by a leading `=`
ISBD marker. If the marker is at the end of subfield `$a`, we can be certain it's an ISBD marker,
but since the `$a` and `$c` subfields are not repeatable, multiple titles are just added to the
`$c` subfield. So if we encounter an `=` sign in the middle middle of `$c` somewhere, how can we
tell if it's an ISBD marker or just an equal sign part of the title (like in the fictive book
`"$aEating the right way : The 2 + 2 = 5 diet"`)? Some kind of escaping would have made that clear,
but the ISBD principles doesn't seem to call for that, leaving us completely in the dark.
*That* is seriously annoying :weary: [↩](#a1)

<b id="f2">2</b> [According to](http://www.loc.gov/marc/bibliographic/bd245.html)
ISBD principles "field 245 ends with a period, even when another mark of punctuation is present,
unless the last word in the field is an abbreviation, initial/letter, or data that ends with final
punctuation." Determining if something is "an abbreviation, initial/letter, or data that ends with
final punctuation" is certainly not an easy task for anything but humans and AI. [↩](#a2)

<b id="f3">3</b> Our old OPAC used to output something like
"Number of pages: One video disc (DVD)…" for DVDs – the developers had apparently just assumed that the
content of `300 $a` could be represented as "number of pages" in all cases. While that sounds silly, getting
the *number* of pages (for documents that actually have pages) from MARC records can be ridiculously hard;
you can safely extract the number from strings like `149 p.` (English), `149 s.` (Norwegian), etc., but you
must ignore the numbers in strings like `10 boxes`, `11 v.` (volumes) etc. So for a start you need a
list of valid abbreviations for "pages" in all relevant languages. Then there's the more complicated cases
like `1 score (16 p.)` – at first sight it looks like we can tokenize that into (number, unit) pairs, like
`("1 score", "16 p.")` and only accept the item(s) having an allowed unit (like `p.`). But then suddenly
comes a case like `"74 p. of ill., 15 p."`, which we would turn into `("74 p. of ill.", "15 p.")`, accepting
`15 p.`, not the correct `74 p.`. So we bite into the grass and start writing rules; if a valid match is found
as the start of the string, then accept it, else if …, else try tokenization, etc... it quickly becomes messy
and it will certainly fail in some cases. Sad to say, after a few years in the library, I still haven't
figured out a general way to extract the number of pages a document have using library data. [↩](#a3)
