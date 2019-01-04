<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InstagramProfiles
 *
 * @property int $id
 * @property string $name
 * @property string $instagram_id
 * @property string $last_check
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereInstagramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereLastCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $followers
 * @property string|null $profile_pic
 * @property int $is_private
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereProfilePic($value)
 * @property string|null $full_name
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InstagramProfiles onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereFullName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InstagramProfiles withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InstagramProfiles withoutTrashed()
 * @property mixed|null $last_error
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InstagramProfiles whereLastError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Todos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Todos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Todos query()
 * @property-read \App\Models\TodoTypes $type
 * @property string $data
 * @property int $type_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Todos whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Todos whereTypeId($value)
 */
class Todos extends Model
{
    use SoftDeletes;

    protected $table = 'todo';

    protected $dates = [];

    protected $guarded = ['id'];

    public function type()
    {
        return $this->belongsTo(TodoTypes::class, 'type_id');
    }
}
