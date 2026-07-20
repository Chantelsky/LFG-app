<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $game_id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property string|null $skill_rank
 * @property string|null $region
 * @property string|null $timezone
 * @property string|null $availability
 * @property string $comm_preference
 * @property string $join_mode
 * @property int $party_size
 * @property int $current_members
 * @property string $status
 * @property int $is_priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Game $game
 * @property-read Collection<int, JoinRequest> $joinRequests
 * @property-read int|null $join_requests_count
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCommPreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCurrentMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereIsPriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereJoinMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post wherePartySize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereSkillRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUserId($value)
 *
 * @property-read Collection<int, PartyMember> $partyMembers
 * @property-read int|null $party_members_count
 *
 * @mixin \Eloquent
 */
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

    public function partyMembers()
    {
        return $this->hasMany(PartyMember::class);
    }
}
