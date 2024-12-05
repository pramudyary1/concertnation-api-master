<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class event extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function images(): MorphMany
    {
        return $this->morphMany(image::class, 'imageable');
    }

    public function thumbnail()
    {
        return $this->hasMany(image::class)->where('a','a');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order() : HasMany{
        return $this->hasMany(order::class);
    }
}
