<?php

namespace Scriptotek\Marc\Fields;

interface SubjectFieldInterface extends AuthorityFieldInterface
{
    public function getVocabulary();

    public function getParts();

    public function getTerm();
}
