<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'is_finalist' => 'boolean',
    ];

    public function votes() {
        return $this->hasMany(Vote::class);
    }
}
