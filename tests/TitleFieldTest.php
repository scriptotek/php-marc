<?php

use Scriptotek\Marc\Fields\Title;

class TitleFieldTest extends \PHPUnit_Framework_TestCase
{
    /*
        In records formulated according to ISBD principles, subfield $b contains
        all the data following the first mark of ISBD punctuation and up to and
        including the mark of ISBD punctuation that introduces the first author
        statement (i.e., the first slash (/)) or precedes either the number
        (subfield $n) or the name (subfield $p) of a part/section of a work. Note
        that subfield $b is not repeated when more than one parallel title,
        subsequent title, and/or other title information are given in the field.

        1. Title and statement of responsibility area:

        = Parallel title R
        : Other title information R
        Statement of responsibility:
            / First statement MA
            ; Subsequent statement R
        ; Subsequent title by same author, etc. MA R
        . Subsequent title by different author, etc. MA R
    */

    public function testIsbdUsStyle()
    {
        // ISBD style: US
        // Title components: [Main title, Other title information, Other title information]
        $field = new File_MARC_Data_Field('245', array(
            new File_MARC_Subfield('a', 'Eternal darkness :'),
            new File_MARC_Subfield('b', 'sanity\'s requiem : Prima\'s official strategy guide /'),
            new File_MARC_Subfield('c', 'the Stratton Bros.'),
        ));
        $title = new Title($field);
        $this->assertEquals('Eternal darkness : sanity\'s requiem : Prima\'s official strategy guide', strval($title));
    }

    public function testIsbdUkStyle()
    {
        // ISBD style: UK
        // Title components: [Main title, Other title information, Other title information]
        $field = new File_MARC_Data_Field('245', array(
            new File_MARC_Subfield('a', 'Eternal darkness'),
            new File_MARC_Subfield('b', 'sanity\'s requiem : Prima\'s official strategy guide'),
            new File_MARC_Subfield('c', 'the Stratton Bros.'),
        ));
        $title = new Title($field);
        $this->assertEquals('Eternal darkness : sanity\'s requiem : Prima\'s official strategy guide', strval($title));
    }

    public function testParallelTitleUs()
    {
        // ISBD style: US
        // Title components: Main title, Parallel title
        $field = new File_MARC_Data_Field('245', array(
            new File_MARC_Subfield('a', 'Lageru ='),
            new File_MARC_Subfield('b', 'The land of eternal darkness /'),
            new File_MARC_Subfield('c', 'Kwak Pyŏng-gyu chŏ.'),
        ));
        $title = new Title($field);
        $this->assertEquals('Lageru = The land of eternal darkness', strval($title));
    }

    public function testParallelTitleUk()
    {
        // ISBD style: UK (note: = mark still included)
        // Components: Main title, Parallel title
        $field = new File_MARC_Data_Field('245', array(
            new File_MARC_Subfield('a', 'Byggekunst ='),
            new File_MARC_Subfield('b', 'The Norwegian review of architecture'),
            new File_MARC_Subfield('c', 'Norske arkitekters landsforbund'),
        ));
        $title = new Title($field);
        $this->assertEquals('Byggekunst = The Norwegian review of architecture', strval($title));
    }

    public function testIsbdSymbolsInTitle()
    {
        # http://lccn.loc.gov/2006589502
        # Here, the = does not indicate the start of a parallel title, but is part of the title.
        # How can we know?? Because it don't have a space in front?
        $field = new File_MARC_Data_Field('245', array(
            new File_MARC_Subfield('a', '2 + 2 = 5 :'),
            new File_MARC_Subfield('b', 'innovative ways of organising people in the Australian Public Service.'),
        ));
        $title = new Title($field);
        $this->assertEquals('2 + 2 = 5 : innovative ways of organising people in the Australian Public Service.', strval($title));
    }

    public function testMultipleTitles()
    {
        # http://lccn.loc.gov/2006589502
        # An example of where we really shouldn't strip of the final dot(s)
        $field = new File_MARC_Data_Field('245', array(
            new File_MARC_Subfield('a', 'Hamlet ;'),
            new File_MARC_Subfield('b', 'Romeo and Juliette ; Othello ...'),
        ));
        $title = new Title($field);
        $this->assertEquals('Hamlet ; Romeo and Juliette ; Othello ...', strval($title));
    }

    public function testTitleWithPart()
    {
        $field = new File_MARC_Data_Field('245', array(
            new File_MARC_Subfield('a', 'Love from Joy :'),
            new File_MARC_Subfield('b', 'letters from a farmer’s wife.'),
            new File_MARC_Subfield('n', 'Part III,'),
            new File_MARC_Subfield('p', '1987-1995, At the bungalow.'),
        ));
        $title = new Title($field);
        $this->assertEquals('Love from Joy : letters from a farmer’s wife. Part III, 1987-1995, At the bungalow.', strval($title));
    }

    public function testTitleWithMultiplePartSubfields()
    {
        $field = new File_MARC_Data_Field('245', array(
            new File_MARC_Subfield('a', 'Zentralblatt für Bakteriologie.'),
            new File_MARC_Subfield('n', '1. Abt. Originale.'),
            new File_MARC_Subfield('n', 'Reihe B,'),
            new File_MARC_Subfield('p', 'Hygiene, Krankenhaushygiene, Betriebshygiene, präventive Medizin.'),
        ));
        $title = new Title($field);
        $this->assertEquals('Zentralblatt für Bakteriologie. 1. Abt. Originale. Reihe B, Hygiene, Krankenhaushygiene, Betriebshygiene, präventive Medizin.', strval($title));
    }

    public function testJsonSerialization()
    {
        $field = new File_MARC_Data_Field('245', array(
            new File_MARC_Subfield('a', 'Zentralblatt für Bakteriologie.'),
            new File_MARC_Subfield('n', '1. Abt. Originale.'),
            new File_MARC_Subfield('n', 'Reihe B,'),
            new File_MARC_Subfield('p', 'Hygiene, Krankenhaushygiene, Betriebshygiene, präventive Medizin.'),
        ));
        $title = new Title($field);

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'title' => 'Zentralblatt für Bakteriologie. 1. Abt. Originale. Reihe B, Hygiene, Krankenhaushygiene, Betriebshygiene, präventive Medizin.',
            ]),
            json_encode(['title' => $title])
        );
    }
}
