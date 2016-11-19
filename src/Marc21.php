<?php

namespace Scriptotek\Marc;

class Marc21
{
    // MARC21 record types
    const AUTHORITY = 'Authority';
    const BIBLIOGRAPHIC = 'Bibliographic';
    const HOLDINGS = 'Holdings';

    // Descriptive cataloging forms
    const NON_ISBD = ' ';
    const AACR2 = 'a';
    const ISBD_PUNCTUATION_OMITTED = 'c';
    const ISBD_PUNCTUATION_INCLUDED = 'i';
    const NON_ISBD_PUNCTUATION_OMITTED = 'n';
    const UNKNOWN_CATALOGING_FORM = 'u';
}
