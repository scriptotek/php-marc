<?php

namespace Scriptotek\Marc\Exceptions;

use LibXMLError;

class XmlException extends \RuntimeException
{
    public function __construct(array $errors)
    {
        $details = array_map(function (LibXMLError $error) {
            return $error->message;
        }, $errors);
        parent::__construct('Failed loading XML: \n' . implode('\n', $details));
    }
}
