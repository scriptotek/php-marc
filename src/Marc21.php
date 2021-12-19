<?php

namespace Scriptotek\Marc;

class Marc21
{
    // MARC21 record types
    public const AUTHORITY = 'Authority';
    public const BIBLIOGRAPHIC = 'Bibliographic';
    public const HOLDINGS = 'Holdings';

    // Descriptive cataloging forms
    public const NON_ISBD = ' ';
    public const AACR2 = 'a';
    public const ISBD_PUNCTUATION_OMITTED = 'c';
    public const ISBD_PUNCTUATION_INCLUDED = 'i';
    public const NON_ISBD_PUNCTUATION_OMITTED = 'n';
    public const UNKNOWN_CATALOGING_FORM = 'u';
}
