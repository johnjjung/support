<?php

/**
 * @package     Support
 * @author      Ian Olson <me@ianolson.io>
 * @license     MIT
 * @copyright   2015, Ian Olson
 */

namespace IanOlson\Support\Traits;

use Illuminate\Support\Facades\Cache;

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

    /**
     * Purge the cache of an array of cache keys.
     *
     * @param null $model
     * @param null $prefix
     */
    protected function purgeCache($model = null, $prefix = null)
    {
        if ($model && $prefix) {

            // Add $prefix.id to cache keys.
            $this->cacheKeys[] = $prefix . $model->id;

            // Get mass assignment columns of the model.
            $massAssign = $model->getFillable();

            // If slug as mass assignable add $prefix.slug to cache keys.
            if(in_array('slug', $massAssign)) {
                $this->cacheKeys[] = $prefix . $model->slug;
            }
        }

        // Clear cache keys.
        foreach ($this->cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}