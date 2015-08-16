<?php

/**
 * @package     Support
 * @author      Ian Olson <me@ianolson.io>
 * @license     MIT
 * @copyright   2015, Ian Olson
 */

namespace IanOlson\Support\Traits;

use Illuminate\Support\Facades\Validator;
use Laraflock\Dashboard\Exceptions\FormValidationException;

trait ValidateTrait
{
    /**
     * Global rules to use for validation.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Validate the form submission.
     *
     * @param array $data
     *
     * @throws FormValidationException
     */
    protected function validate(array $data)
    {
        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            throw new FormValidationException('Fix errors in the form below.', $validator);
        }
    }
}