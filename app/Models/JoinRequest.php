<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post $post
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereUserId($value)
 *
 * @mixin \Eloquent
 */
class JoinRequest extends Model
{
    protected $fillable = ['post_id', 'user_id', 'status'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
