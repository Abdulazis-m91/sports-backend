<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
        'team_name',
        'team_logo',
        'league_id',
        'league_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}