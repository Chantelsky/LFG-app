<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'game_id', 'user_id', 'title', 'description', 'skill_rank',
        'region', 'timezone', 'availability', 'comm_preference',
        'join_mode', 'party_size', 'current_members', 'status', 'is_priority',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function joinRequests()
    {
        return $this->hasMany(JoinRequest::class);
    }
}
