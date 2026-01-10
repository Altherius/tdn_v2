<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /** @use HasFactory<\Database\Factories\CountryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Team, $this>
     */
    public function teams(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Team::class);
    }
}
