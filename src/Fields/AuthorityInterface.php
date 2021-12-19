<?php

namespace Scriptotek\Marc\Fields;

interface AuthorityInterface
{
    /**
     * The control number of the authority record.
     *
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * The type of authority record.
     *
     * @return string
     */
    public function getType(): string;
}
