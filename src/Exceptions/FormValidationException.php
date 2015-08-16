<?php

/**
 * @package     Support
 * @author      Ian Olson <me@ianolson.io>
 * @license     MIT
 * @copyright   2015, Ian Olson
 */

namespace IanOlson\Support\Exceptions;

use Exception;

class FormValidationException extends Exception
{
    protected $errors;

    public function __construct($message = '', $errors = [])
    {
        $this->errors = $errors;

        parent::__construct($message);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}