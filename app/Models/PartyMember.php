<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property int $is_host
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post $post
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartyMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartyMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartyMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartyMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartyMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartyMember whereIsHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartyMember wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartyMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartyMember whereUserId($value)
 *
 * @mixin \Eloquent
 */
class PartyMember extends Model
{
    protected $fillable = ['post_id', 'user_id', 'is_host'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
