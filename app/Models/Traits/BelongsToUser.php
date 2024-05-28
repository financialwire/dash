<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;

trait BelongsToUser
{
    public static function bootBelongsToUser()
    {
        static::creating(function (Model $model) {
            if (auth()->hasUser()) {
                $model->user_id = auth()->user()->id;
            }
        });
    }
}
