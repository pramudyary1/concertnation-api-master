<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function image(): MorphOne
    {
        return $this->morphOne(image::class, 'imageable');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
