<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'code',
        'color',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'division_id', 'id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'division_id', 'id');
    }
}
