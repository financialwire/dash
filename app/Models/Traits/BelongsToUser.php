<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;

trait BelongsToUser
{
    public static function bootBelongsToUser()
    {
        static::creating(function (Model $model) {
            $model->user_id = auth()->user()->id;
        });
    }
}
