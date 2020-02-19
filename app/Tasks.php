<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tasks extends Model
{
    /**
     * @return BelongsTo
     */
    public function users() {

        return $this->belongsTo('App\User');

    }
}
