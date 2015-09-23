<?php

/**
 * @package     Support
 * @author      Ian Olson <me@ianolson.io>
 * @license     MIT
 * @copyright   2015, Ian Olson
 */

namespace IanOlson\Support\Traits;

trait UpdateTrait
{
    /**
     * Update model attributes from a request.
     *
     * @param       $model
     * @param array $data
     *
     * @return bool
     */
    protected function updateAttributes(&$model, array &$data)
    {
        if (empty($data)) {
            return false;
        }

        // Get mass assignment columns of the model.
        $massAssign = $model->getFillable();

        foreach ($data as $attribute => $value) {

            if (!in_array($attribute, $massAssign)) {
                continue;
            }

            $model->$attribute = $value;
        }

        return true;
    }
}