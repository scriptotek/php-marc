[![Build Status](https://img.shields.io/travis/scriptotek/php-marc/master.svg?style=flat-square)](https://travis-ci.org/scriptotek/php-marc)
[![Coverage](https://img.shields.io/codecov/c/github/scriptotek/php-marc/master.svg?style=flat-square)](https://codecov.io/gh/scriptotek/php-marc)
[![StyleCI](https://styleci.io/repos/41363199/shield)](https://styleci.io/repos/41363199)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/scriptotek/php-marc.svg?style=flat-square)](https://scrutinizer-ci.com/g/scriptotek/php-marc/?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/scriptotek/marc.svg?style=flat-square)](https://codeclimate.com/github/scriptotek/marc)
[![Latest Stable Version](http://img.shields.io/packagist/v/scriptotek/marc.svg?style=flat-square)](https://packagist.org/packages/scriptotek/marc)
[![Total Downloads](http://img.shields.io/packagist/dt/scriptotek/marc.svg?style=flat-square)](https://packagist.org/packages/scriptotek/marc)

# scriptotek/marc

This is a small package that provides a simple interface for working with
MARC records using the [File_MARC package](https://github.com/pear/File_MARC).
It should work with both Binary MARC and MARCXML (with or without namespaces),
but not the various Line mode MARC formats. Records can be edited using the
editing capabilities of File_MARC.

## Installation using Composer:

```
composer require scriptotek/marc dev-master
```

## Reading records

Records are loaded into a `Collection` object using
`Collection::fromFile` or `Collection::fromStringString`,
which autodetects if the data is Binary MARC or XML:

```php
use Scriptotek\Marc\Collection;

$collection = Collection::fromFile($someFileName);
foreach ($collection->records as $record) {
  echo $record->getField('250')->getSubfield('a') . "\n";
}
```

The package will extract MARC records from any container XML,
so you can load an SRU or OAI-PMH response directly:

```php
$response = file_get_contents('http://lx2.loc.gov:210/lcdb?' . http_build_query(array(
    'operation' => 'searchRetrieve',
    'recordSchema' => 'marcxml',
    'version' => '1.1',
    'maximumRecords' => '10',
    'query' => 'bath.isbn=0761532692',
)));

$collection = Collection::fromString($response);
foreach ($collection->records as $record) {
  echo $record->getField('245')->getSubfield('a') . "\n";
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

The `Record` class of File_MARC has been extended with a few
convenience methods to make handling of some everyday tasks easier.

### getType()

Returns either 'Bibliographic', 'Authority' or 'Holdings' based on the
value of the sixth character in the leader.

### Handlers for specific fields

Hopefully this list will grow larger over time:

* `getIsbns()`
* `getSubjects()`
* `getTitle()`

Each of these methods returns an array of one of the corresponding field classes (located in `src/Fields`).
For instance `getIsbns()` returns an array of `Scriptotek\Marc\Isbn` objects. All the field classes
implements at minimum a `__toString()` method so you can easily get a string representation of the field
for presentation purpose, like so:

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
echo $record->isbns[0];
```

Notice that we used `isbns` instead of `getIsbns()`. In the same way, you can request `$record->subjects` instead of `$record->getSubjects()`, etc. This is made possible using [a little bit of PHP magic](https://github.com/scriptotek/php-marc/blob/master/src/Fields/Field.php#L19).

*But* providing a single, *general* string representation that makes sense in all cases
can sometimes be quite a challenge. The general string representation might not fit your
specific need.

Take the `Title` class based on `245`. The string representation doesn't include data
from `$h` (medium) or `$c` (statement of responsibility, etc.), since that's probably
not the kind of info most non-librarians would expect to see in a "title". But it currently
does include everything contained in `$a` and `$b` (except any final `/` ISBD marker),
which means it doesn't make any attempt of removing parallel titles.<sup id="a1">[1](#f1)</sup>
It also includes text from `$n` (part number) and `$p` (part title), but yet some other
subfields like `$f`, `$g` and `$k` are currently ignored since I haven't really decided
whether to include them or not.

I would love to remove the ending dot that is present
in records with explicit ISBD markers, but that's not trivial for the same reason
identifying parallel titles is not<sup id="a1">[1](#f1)</sup> – there's just no safe
way to tell if the final dot is an ISBD marker or part of the title.<sup id="a2">[2](#f2)</sup>
Since explicit ISBD markers are included in records catalogued in the American tradition,
but not in records catalogued in the British tradition, a mix of records from both traditions
will look silly.

I hope this makes clear that you need to check if the assumptions and simplifications made
in the string representation methods makes sense to *your* project or not. It's also not
unlikely that some methods make false assumptions based on (my) incomplete knowledge of
cataloguing rules/practice. A developer given just a few MARC records might for instance assume
that `300 $a` is a subfield for "number of pages".<sup id="a3">[3](#f3)</sup> A quick glance
at e.g. [LC's MARC documentation](https://www.loc.gov/marc/bibliographic/bd300.html) would
be enough to prove that wrong, but in other cases it's harder to avoid making false assumptions
without deep familiarity with cataloguing rules and practices.

There's also cases where different traditions conflict, and you just have to make a choice.
Subject subfields, for instance, have to be joined using some kind of glue.
[LCSHs](https://en.wikipedia.org/wiki/Library_of_Congress_Subject_Headings) are
ordinarily presented as strings glued together with em-dashes or double en-dashes
(`650 $aPhysics $xHistory $yHistory` is presented as `Physics--History--20th century`).
But in other subject heading systems colons are used as the glue (`Physics : History : 20th century`).
This package defaults to colon, but you change that by setting `Subject::glue = '--'` or whatever.

## Notes

<b id="f1">1</b> That might change in the future. But even if I decide to remove parallel titles,
I'm not really sure how to do it in a safe way. Parallel titles are identified by a leading `=`
ISBD marker. If the marker is at the end of subfield `$a`, we can be certain it's an ISBD marker,
but since the `$a` and `$c` subfields are not repeatable, multiple titles are just added to the
`$c` subfield. So if we encounter an `=` sign in the middle middle of `$c` somewhere, how can we
tell if it's an ISBD marker or just an equal sign part of the title (like in the fictive book
`"$aEating the right way : The 2 + 2 = 5 diet"`)? Some kind of escaping would have made that clear,
but the ISBD principles doesn't seem to call for that, leaving us completely in the dark!
*That* is seriously annoying :weary: [↩](#a1)

<b id="f2">2</b> [According to](http://www.loc.gov/marc/bibliographic/bd245.html)
ISBD principles "field 245 ends with a period, even when another mark of punctuation is present,
unless the last word in the field is an abbreviation, initial/letter, or data that ends with final
punctuation." Determining if something is "an abbreviation, initial/letter, or data that ends with
final punctuation" is certainly not trivial, I would guess that machine learning would be needed
for a highly successful implementation [↩](#a2)

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
