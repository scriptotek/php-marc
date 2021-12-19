<?php

namespace Scriptotek\Marc\Fields;

interface SubjectInterface extends AuthorityInterface
{
    public function getVocabulary();

    public function getParts();

    public function getTerm();
}
