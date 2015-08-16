<?php

/**
 * @package  Support
 * @author   Ian Olson <me@ianolson.io>
 *
 * @property key
 * @property value
 * @property reference_id
 * @property reference_type
 * @property created_at
 * @property updated_at
 */

namespace IanOlson\Support\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'meta_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'method',
      'value',
      'reference_id',
      'reference_type',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get all of the owning models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reference()
    {
        return $this->morphTo();
    }
}
