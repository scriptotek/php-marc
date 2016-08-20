<?php

namespace Scriptotek\Marc\Exceptions;

class XmlException extends \RuntimeException
{
	public function __construct($errors)
	{
		$details = array_map(function($error) {
			return $error->message;
		}, $errors);
		parent::__construct('Failed loading XML: \n' . implode('\n', $details));
	}
}